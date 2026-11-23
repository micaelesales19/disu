<?php

namespace App\Controllers\Clientes;

use App\Controllers\BaseController;
use App\Models\ClienteModel;

class Empresas extends BaseController
{
    protected ClienteModel $clienteModel;

    public function __construct()
    {
        $this->clienteModel = new ClienteModel();
    }

    public function index()
    {
        $busca  = trim((string) $this->request->getGet('busca'));
        $regime = trim((string) $this->request->getGet('regime'));
        $status = trim((string) $this->request->getGet('status'));
        $perfil = trim((string) $this->request->getGet('perfil'));

        $porPagina = 10;

        // Nova consulta para não alterar os demais métodos do Model.
        $model = new \App\Models\ClienteModel();

        if ($busca !== '') {
            $model->groupStart()
                ->like('nome', $busca)
                ->orLike('razao_social', $busca)
                ->orLike('nome_fantasia', $busca)
                ->orLike('cpf_cnpj', preg_replace('/\D/', '', $busca))
                ->orLike('codigo_interno', $busca)
                ->orLike('email', $busca)
                ->orLike('cidade', $busca)
                ->groupEnd();
        }

        if ($regime !== '') {
            $model->where('regime_tributario', $regime);
        }

        if ($status === 'Ativo') {
            $model->where('ativo', 1);
        } elseif ($status === 'Inativo') {
            $model->where('ativo', 0);
        }

        if ($perfil === 'VIP') {
            $model->where('vip', 1);
        } elseif ($perfil === 'Normal') {
            $model->where('vip', 0);
        }

        $empresas = $model
            ->orderBy('nome', 'ASC')
            ->paginate($porPagina, 'empresas');

        $dados = [
            'title'       => 'Empresas | DISU',
            'empresas'    => $empresas,
            'indicadores' => $this->clienteModel->indicadores(),
            'pager'       => $model->pager,
            'filtros'     => compact('busca', 'regime', 'status', 'perfil'),
            'totalFiltrado' => $model->pager->getTotal('empresas'),
        ];

        return view('clientes/empresas/index', $dados);
    }

    // FORMULÁRIO DE CADASTRO
    public function novo()
    {
        return view('clientes/empresas/form', [
            'title'   => 'Nova Empresa | DISU',
            'empresa' => null,
            'edicao'  => false,
        ]);
    }

    // FORMULÁRIO DE EDIÇÃO
    public function editar(int $id)
    {
        $empresa = $this->clienteModel->find($id);

        if (!$empresa) {
            return redirect()
                ->to(site_url('clientes/empresas'))
                ->with('error', 'Empresa não encontrada.');
        }

        return view('clientes/empresas/form', [
            'title'   => 'Editar Empresa | DISU',
            'empresa' => $empresa,
            'edicao'  => true,
        ]);
    }


    public function salvar()
    {
        $request = $this->request;

        // Apenas campos permitidos no cadastro
        $campos = [
            'codigo_interno',
            'cpf_cnpj',
            'nome',
            'nome_fantasia',
            'razao_social',
            'contador_responsavel',
            'data_entrada',
            'data_saida',
            'email',
            'email_cobranca',
            'telefone',
            'whatsapp',
            'cep',
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'cidade',
            'uf',
            'observacoes_internas',
            'regime_tributario',
            'inscricao_municipal',
            'inscricao_estadual',
            'capital_social',
            'plano_empresa',
            'informacoes_fiscais',
            'dados_contabeis',
            'dados_dp',
            'url_contrato_social',
            'url_cartao_cnpj',
        ];

        $dados = [];

        foreach ($campos as $campo) {
            $valor = $request->getPost($campo);

            $dados[$campo] = is_string($valor)
                ? trim($valor)
                : null;
        }

        // Remove formatação dos campos numéricos
        foreach (['cpf_cnpj', 'telefone', 'whatsapp', 'cep'] as $campo) {
            $dados[$campo] = preg_replace(
                '/\D/',
                '',
                $dados[$campo] ?? ''
            ) ?: null;
        }

        // Código e documento vazios devem ser NULL
        // para não causar conflito nos índices UNIQUE.
        foreach (
            [
                'codigo_interno',
                'data_entrada',
                'data_saida',
                'capital_social',
            ] as $campo
        ) {
            if ($dados[$campo] === '') {
                $dados[$campo] = null;
            }
        }

        // Campos opcionais em branco
        foreach ($dados as $campo => $valor) {
            if ($valor === '') {
                $dados[$campo] = null;
            }
        }

        // Normalização
        $dados['uf'] = strtoupper($dados['uf'] ?? '');

        if ($dados['uf'] === '') {
            $dados['uf'] = null;
        }

        // Checkboxes: ausente = 0
        foreach (
            [
                'ativo',
                'vip',
                'possui_funcionarios',
                'possui_extrato_bancario',
                'importar_notas_sieg',
            ] as $campo
        ) {
            $dados[$campo] = $request->getPost($campo) === '1'
                ? 1
                : 0;
        }

        // Validação dos dados antes da inserção
        $regras = [
            'nome' => 'required|max_length[255]',

            'cpf_cnpj' =>
            'permit_empty|regex_match[/^([0-9]{11}|[0-9]{14})$/]|is_unique[clientes.cpf_cnpj]',

            'codigo_interno' =>
            'permit_empty|max_length[30]|is_unique[clientes.codigo_interno]',

            'email' =>
            'permit_empty|valid_email|max_length[255]',

            'email_cobranca' =>
            'permit_empty|valid_email|max_length[255]',

            'telefone' =>
            'permit_empty|regex_match[/^[0-9]{10,11}$/]',

            'whatsapp' =>
            'permit_empty|regex_match[/^[0-9]{10,11}$/]',

            'cep' =>
            'permit_empty|regex_match[/^[0-9]{8}$/]',

            'uf' =>
            'permit_empty|exact_length[2]|alpha',

            'capital_social' =>
            'permit_empty|decimal',

            'data_entrada' =>
            'permit_empty|valid_date[Y-m-d]',

            'data_saida' =>
            'permit_empty|valid_date[Y-m-d]',

            'url_contrato_social' =>
            'permit_empty|valid_url|max_length[2048]',

            'url_cartao_cnpj' =>
            'permit_empty|valid_url|max_length[2048]',
        ];

        $validation = service('validation');
        $validation->setRules($regras);

        if (!$validation->run($dados)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        // O Model também possui regras de validação.
        // Nesta operação de criação, utilizamos as regras
        // específicas acima para evitar divergência na
        // verificação de unicidade.

        try {
            $clienteModel = new \App\Models\ClienteModel();

            $clienteModel->skipValidation(true);

            $id = $clienteModel->insert($dados);

            if ($id === false) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with(
                        'error',
                        'Não foi possível cadastrar a empresa.'
                    );
            }

            return redirect()
                ->to(site_url('clientes/empresas'))
                ->with(
                    'message',
                    'Empresa cadastrada com sucesso!'
                );
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Erro ao cadastrar empresa: {erro}',
                ['erro' => $e->getMessage()]
            );

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Ocorreu um erro ao salvar a empresa.'
                );
        }
    }


    public function atualizar(int $id)
    {
        $empresa = $this->clienteModel->find($id);

        if (!$empresa) {
            return redirect()
                ->to(site_url('clientes/empresas'))
                ->with('error', 'Empresa não encontrada.');
        }

        $campos = [
            'codigo_interno',
            'cpf_cnpj',
            'nome',
            'nome_fantasia',
            'razao_social',
            'contador_responsavel',
            'data_entrada',
            'data_saida',
            'email',
            'email_cobranca',
            'telefone',
            'whatsapp',
            'cep',
            'logradouro',
            'numero',
            'complemento',
            'bairro',
            'cidade',
            'uf',
            'observacoes_internas',
            'regime_tributario',
            'inscricao_municipal',
            'inscricao_estadual',
            'capital_social',
            'plano_empresa',
            'informacoes_fiscais',
            'dados_contabeis',
            'dados_dp',
            'url_contrato_social',
            'url_cartao_cnpj',
        ];

        $dados = [];

        foreach ($campos as $campo) {
            $valor = $this->request->getPost($campo);
            $dados[$campo] = is_string($valor)
                ? (trim($valor) ?: null)
                : null;
        }

        // Remove máscaras
        foreach (['cpf_cnpj', 'telefone', 'whatsapp', 'cep'] as $campo) {
            $dados[$campo] = preg_replace(
                '/\D/',
                '',
                $dados[$campo] ?? ''
            ) ?: null;
        }

        $dados['uf'] = strtoupper($dados['uf'] ?? '') ?: null;

        // Checkboxes
        foreach (
            [
                'ativo',
                'vip',
                'possui_funcionarios',
                'possui_extrato_bancario',
                'importar_notas_sieg',
            ] as $campo
        ) {
            $dados[$campo] = $this->request->getPost($campo) === '1' ? 1 : 0;
        }

        // Validações
        $regras = [
            'nome' => 'required|max_length[255]',
            'cpf_cnpj' => 'permit_empty|regex_match[/^([0-9]{11}|[0-9]{14})$/]',
            'codigo_interno' => 'permit_empty|max_length[30]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'email_cobranca' => 'permit_empty|valid_email|max_length[255]',
            'telefone' => 'permit_empty|regex_match[/^[0-9]{10,11}$/]',
            'whatsapp' => 'permit_empty|regex_match[/^[0-9]{10,11}$/]',
            'cep' => 'permit_empty|regex_match[/^[0-9]{8}$/]',
            'uf' => 'permit_empty|exact_length[2]|alpha',
            'capital_social' => 'permit_empty|decimal',
            'data_entrada' => 'permit_empty|valid_date[Y-m-d]',
            'data_saida' => 'permit_empty|valid_date[Y-m-d]',
            'url_contrato_social' => 'permit_empty|valid_url|max_length[2048]',
            'url_cartao_cnpj' => 'permit_empty|valid_url|max_length[2048]',
        ];

        $validation = service('validation');
        $validation->setRules($regras);

        if (!$validation->run($dados)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $validation->getErrors());
        }

        // Verifica duplicidade, ignorando a própria empresa
        foreach (['cpf_cnpj', 'codigo_interno'] as $campo) {
            if ($dados[$campo] === null) {
                continue;
            }

            $duplicado = $this->clienteModel
                ->where($campo, $dados[$campo])
                ->where('id !=', $id)
                ->first();

            if ($duplicado) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', "O campo {$campo} já pertence a outra empresa.");
            }
        }

        try {
            // Evita conflito com as regras de criação do Model.
            $model = new ClienteModel();
            $model->skipValidation(true);

            if (!$model->update($id, $dados)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Não foi possível atualizar a empresa.');
            }

            return redirect()
                ->to(site_url('clientes/empresas/editar/' . $id))
                ->with('message', 'Empresa atualizada com sucesso!');
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Erro ao atualizar empresa {id}: {erro}',
                ['id' => $id, 'erro' => $e->getMessage()]
            );

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ocorreu um erro ao atualizar a empresa.');
        }
    }


    public function consultarCnpj(string $cnpj)
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        // Valida formato e dígitos verificadores
        if (!$this->cnpjValido($cnpj)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'sucesso' => false,
                    'mensagem' => 'Informe um CNPJ válido com 14 dígitos.',
                ]);
        }

        try {
            $client = service('curlrequest', [
                'timeout'         => 15,
                'connect_timeout' => 5,
                'http_errors'     => false,
            ]);

            $resposta = $client->get(
                'https://brasilapi.com.br/api/cnpj/v1/' . $cnpj,
                [
                    'headers' => [
                        'Accept'     => 'application/json',
                        'User-Agent' => 'DISU/1.0 (Sistema de Gestao Contabil)',
                    ],
                ]
            );

            $status = $resposta->getStatusCode();

            if ($status === 404) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'sucesso' => false,
                        'mensagem' => 'CNPJ não encontrado.',
                    ]);
            }

            if ($status !== 200) {

                log_message(
                    'warning',
                    'BrasilAPI HTTP {status}: {resposta}',
                    [
                        'status'   => $status,
                        'resposta' => substr($resposta->getBody(), 0, 500),
                    ]
                );

                return $this->response
                    ->setStatusCode(502)
                    ->setJSON([
                        'sucesso' => false,
                        'mensagem' => 'Falha na consulta do CNPJ.',
                        'status_api' => $status,
                    ]);
            }

            $resultado = json_decode(
                $resposta->getBody(),
                true
            );

            if (!is_array($resultado) || empty($resultado['cnpj'])) {
                return $this->response
                    ->setStatusCode(502)
                    ->setJSON([
                        'sucesso' => false,
                        'mensagem' => 'A consulta retornou dados inválidos.',
                    ]);
            }

            // Retornamos apenas os campos necessários
            // para o formulário de empresas.
            $dados = [
                'cpf_cnpj'     => $cnpj,
                'nome'         => $resultado['razao_social'] ?? '',
                'razao_social' => $resultado['razao_social'] ?? '',
                'nome_fantasia' => $resultado['nome_fantasia'] ?? '',
                'email'        => $resultado['email'] ?? '',
                'telefone'     => $resultado['ddd_telefone_1'] ?? '',
                'cep'          => preg_replace(
                    '/\D/',
                    '',
                    $resultado['cep'] ?? ''
                ),
                'logradouro'   => trim(
                    ($resultado['descricao_tipo_de_logradouro'] ?? '') . ' ' .
                        ($resultado['logradouro'] ?? '')
                ),
                'numero'       => $resultado['numero'] ?? '',
                'complemento'  => $resultado['complemento'] ?? '',
                'bairro'       => $resultado['bairro'] ?? '',
                'cidade'       => $resultado['municipio'] ?? '',
                'uf'           => $resultado['uf'] ?? '',
                'capital_social' => $resultado['capital_social'] ?? null,
            ];

            return $this->response->setJSON([
                'sucesso' => true,
                'mensagem' => 'Empresa encontrada.',
                'dados' => $dados,
            ]);
        } catch (\Throwable $e) {
            log_message(
                'error',
                'Erro na consulta de CNPJ: {erro}',
                ['erro' => $e->getMessage()]
            );

            return $this->response
                ->setStatusCode(502)
                ->setJSON([
                    'sucesso' => false,
                    'mensagem' => 'Não foi possível consultar o CNPJ neste momento.',
                ]);
        }
    }

    /**
     * Verifica os dígitos verificadores do CNPJ.
     */
    private function cnpjValido(string $cnpj): bool
    {
        if (!preg_match('/^\d{14}$/', $cnpj)) {
            return false;
        }

        if (preg_match('/^(\d)\1{13}$/', $cnpj)) {
            return false;
        }

        $calcularDigito = static function (
            string $base,
            array $pesos
        ): int {
            $soma = 0;

            foreach ($pesos as $indice => $peso) {
                $soma += (int) $base[$indice] * $peso;
            }

            $resto = $soma % 11;

            return $resto < 2 ? 0 : 11 - $resto;
        };

        $primeiro = $calcularDigito(
            substr($cnpj, 0, 12),
            [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]
        );

        if ($primeiro !== (int) $cnpj[12]) {
            return false;
        }

        $segundo = $calcularDigito(
            substr($cnpj, 0, 13),
            [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]
        );

        return $segundo === (int) $cnpj[13];
    }


    public function alterarStatus(int $id)
    {
        $empresa = $this->clienteModel->find($id);

        if (!$empresa) {
            return redirect()
                ->to(site_url('clientes/empresas'))
                ->with('error', 'Empresa não encontrada.');
        }

        $novoStatus = (int) $empresa['ativo'] === 1 ? 0 : 1;

        if (!$this->clienteModel
            ->skipValidation(true)
            ->update($id, ['ativo' => $novoStatus])) {
            return redirect()
                ->to(site_url('clientes/empresas'))
                ->with('error', 'Não foi possível alterar o status da empresa.');
        }

        $mensagem = $novoStatus === 1
            ? 'Empresa reativada com sucesso!'
            : 'Empresa desativada com sucesso!';

        return redirect()
            ->to(site_url('clientes/empresas'))
            ->with('message', $mensagem);
    }


    public function excluir(int $id)
    {
        $empresa = $this->clienteModel->find($id);

        if (!$empresa) {
            return redirect()
                ->to(site_url('clientes/empresas'))
                ->with('error', 'Empresa não encontrada.');
        }

        // Exclusão lógica: preenche deleted_at
        // sem remover o registro do banco.
        if (!$this->clienteModel->delete($id)) {
            return redirect()
                ->to(site_url('clientes/empresas'))
                ->with('error', 'Não foi possível excluir a empresa.');
        }

        return redirect()
            ->to(site_url('clientes/empresas'))
            ->with('message', 'Empresa excluída com sucesso!');
    }


    public function lixeira()
    {
        $empresas = $this->clienteModel->listarExcluidas(10);

        return view('clientes/empresas/lixeira', [
            'title'    => 'Lixeira de Empresas | DISU',
            'empresas' => $empresas,
            'pager'    => $this->clienteModel->pager,
            'total'    => $this->clienteModel->contarExcluidas(),
        ]);
    }

    public function restaurar(int $id)
    {
        // Localiza a empresa, incluindo registros excluídos.
        $empresa = $this->clienteModel
            ->onlyDeleted()
            ->find($id);

        if (!$empresa) {
            return redirect()
                ->to(site_url('clientes/empresas/lixeira'))
                ->with('error', 'Empresa não encontrada na lixeira.');
        }

        // Limpa o campo deleted_at para restaurar a empresa.
        $restaurado = $this->clienteModel
            ->builder()
            ->where('id', $id)
            ->update([
                'deleted_at' => null,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if (!$restaurado) {
            return redirect()
                ->to(site_url('clientes/empresas/lixeira'))
                ->with('error', 'Não foi possível restaurar a empresa.');
        }

        return redirect()
            ->to(site_url('clientes/empresas/lixeira'))
            ->with('message', 'Empresa restaurada com sucesso!');
    }
}
