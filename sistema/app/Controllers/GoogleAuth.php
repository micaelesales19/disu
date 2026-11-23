<?php

namespace App\Controllers;

use Google\Client as GoogleClient;

class GoogleAuth extends BaseController
{
    private function googleClient(): GoogleClient
    {
        $client = new GoogleClient();

        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));

        $client->setScopes([
            'openid',
            'email',
            'profile',
        ]);

        return $client;
    }


    public function redirect()
    {
        $client = $this->googleClient();

        $state = bin2hex(random_bytes(32));

        session()->set('google_oauth_state', $state);

        // Se já estiver autenticado, inicia a vinculação.
        // Caso contrário, inicia o login com Google.
        session()->set(
            'google_oauth_mode',
            auth()->loggedIn() ? 'link' : 'login'
        );

        $client->setState($state);

        // Permite selecionar a conta Google.
        $client->setPrompt('select_account');

        return redirect()->to($client->createAuthUrl());
    }

    public function link()
    {
        if (!auth()->loggedIn()) {
            return redirect()
                ->to(url_to('login'))
                ->with('error', 'Entre no DISU para vincular sua conta Google.');
        }

        $client = $this->googleClient();

        $state = bin2hex(random_bytes(32));

        session()->set('google_oauth_state', $state);
        session()->set('google_oauth_mode', 'link');

        $client->setState($state);
        $client->setPrompt('select_account');

        return redirect()->to($client->createAuthUrl());
    }

    public function callback()
    {
        $session = session();

        $mode = $session->get('google_oauth_mode');
        $expectedState = $session->get('google_oauth_state');

        // O state deve ser usado apenas uma vez.
        $session->remove('google_oauth_mode');
        $session->remove('google_oauth_state');

        if ($this->request->getGet('error')) {
            return redirect()->to(url_to('login'))
                ->with('error', 'Autenticação Google cancelada.');
        }

        $receivedState = $this->request->getGet('state');

        if (
            !is_string($expectedState) ||
            !is_string($receivedState) ||
            !hash_equals($expectedState, $receivedState)
        ) {
            return redirect()->to(url_to('login'))
                ->with('error', 'Sessão Google inválida. Tente novamente.');
        }

        $code = $this->request->getGet('code');

        if (!is_string($code) || $code === '') {
            return redirect()->to(url_to('login'))
                ->with('error', 'Código de autorização não recebido.');
        }

        try {
            $client = $this->googleClient();
            $token = $client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error']) || empty($token['id_token'])) {
                throw new \RuntimeException('Token Google inválido.');
            }

            $payload = $client->verifyIdToken($token['id_token']);

            if (!$payload || ($payload['email_verified'] ?? false) !== true) {
                throw new \RuntimeException('Identidade Google não verificada.');
            }

            $googleId = $payload['sub'] ?? null;
            $googleEmail = $payload['email'] ?? null;

            if (
                !is_string($googleId) || $googleId === '' ||
                !is_string($googleEmail) || $googleEmail === ''
            ) {
                throw new \RuntimeException('Dados Google incompletos.');
            }

            $db = db_connect();

            // VINCULAÇÃO: usuário já autenticado no Shield
            if ($mode === 'link') {
                if (!auth()->loggedIn()) {
                    return redirect()->to(url_to('login'))
                        ->with('error', 'Entre no DISU antes de vincular o Google.');
                }

                $userId = auth()->id();

                $existing = $db->table('google_accounts')
                    ->groupStart()
                    ->where('user_id', $userId)
                    ->orWhere('google_id', $googleId)
                    ->groupEnd()
                    ->get()
                    ->getResultArray();

                if ($existing !== []) {
                    return redirect()->to(site_url('dashboard'))
                        ->with('error', 'Já existe uma vinculação para este usuário ou conta Google.');
                }


                $dados = [
                    'user_id'      => $userId,
                    'google_id'    => $googleId,
                    'google_email' => $googleEmail,
                    'created_at'   => date('Y-m-d H:i:s'),
                ];

                $salvou = $db->table('google_accounts')->insert($dados);

                if (!$salvou) {
                    log_message(
                        'error',
                        'Falha ao inserir vínculo Google: {erro}',
                        ['erro' => json_encode($db->error())]
                    );

                    return redirect()
                        ->to(site_url('dashboard'))
                        ->with(
                            'error',
                            'Não foi possível salvar a vinculação Google.'
                        );
                }

                return redirect()
                    ->to(site_url('dashboard'))
                    ->with(
                        'message',
                        'Conta Google vinculada com sucesso!'
                    );
            }


            // LOGIN: localizar a conta Google vinculada
            if ($mode !== 'login') {
                return redirect()->to(url_to('login'))
                    ->with('error', 'Operação de autenticação inválida.');
            }

            $googleAccount = $db->table('google_accounts')
                ->where('google_id', $googleId)
                ->get()
                ->getRowArray();

            if (!$googleAccount) {
                return redirect()->to(url_to('login'))
                    ->with(
                        'error',
                        'Esta conta Google ainda não está vinculada a um usuário do DISU.'
                    );
            }

            // Buscar o usuário correspondente no Shield
            $users = auth()->getProvider();

            $user = $users->find($googleAccount['user_id']);

            if (!$user || !$user->isActivated()) {
                return redirect()->to(url_to('login'))
                    ->with('error', 'Usuário não encontrado ou desativado.');
            }

            // Iniciar sessão utilizando o Shield
            auth('session')->login($user);

            // Renovar o identificador da sessão
            session()->regenerate();

            return redirect()->to(site_url('dashboard'));
        } catch (\Throwable $e) {
            log_message('error', 'Erro na autenticação Google: {message}', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->to(
                auth()->loggedIn() ? site_url('dashboard') : url_to('login')
            )->with('error', 'Não foi possível concluir a autenticação Google.');
        }
    }
}
