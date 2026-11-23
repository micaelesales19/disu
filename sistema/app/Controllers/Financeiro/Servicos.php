<?php

namespace App\Controllers\Financeiro;

use App\Controllers\BaseController;
use App\Models\ServicoModel;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Servicos extends BaseController
{
    protected ServicoModel $servicoModel;

    public function __construct()
    {
        $this->servicoModel = new ServicoModel();
    }

    public function index(): string
    {
        $servicos = $this->servicoModel
            ->orderBy('nome', 'ASC')
            ->findAll();

        return view('financeiro/servicos/index', [
            'servicos' => $servicos,
            'total' => count($servicos),
            'ativos' => count(array_filter(
                $servicos,
                static fn($s) => (int) $s['ativo'] === 1
            )),
            'inativos' => count(array_filter(
                $servicos,
                static fn($s) => (int) $s['ativo'] === 0
            )),
        ]);
    }

    public function salvar(): RedirectResponse
    {
        $idPost = $this->request->getPost('id');
        $id = null;

        if ($idPost !== null && $idPost !== '') {
            if (!ctype_digit((string) $idPost) || (int) $idPost < 1) {
                return $this->erro('Identificador do serviço inválido.');
            }

            $id = (int) $idPost;

            if (!$this->servicoModel->find($id)) {
                return $this->erro('Serviço não encontrado.');
            }
        }

        $nome = trim((string) $this->request->getPost('nome'));
        $codigo = trim((string) $this->request->getPost('codigo_interno'));
        $descricao = trim((string) $this->request->getPost('descricao'));
        $codigoFiscal = trim((string) $this->request->getPost('codigo_fiscal'));

        $valor = trim((string) $this->request->getPost('valor_padrao'));

        if ($valor === '') {
            $valor = '0.00';
        }

        // Converte valores no formato brasileiro para decimal.
        // Exemplo: 1.250,75 -> 1250.75
        if (preg_match('/^\d{1,3}(?:\.\d{3})*,\d{2}$/', $valor)) {
            $valor = str_replace(
                ['.', ','],
                ['', '.'],
                $valor
            );
        }

        // Valida o valor após a conversão.
        if (!preg_match('/^\d{1,10}(?:\.\d{1,2})?$/', $valor)) {
            return $this->erro(
                'Informe um valor válido, com até duas casas decimais.'
            );
        }

        $unidade = (string) $this->request->getPost('unidade');
        $permiteNfse = (string) $this->request->getPost('permite_nfse');
        $ativo = (string) $this->request->getPost('ativo');

        if ($nome === '' || mb_strlen($nome) > 150) {
            return $this->erro('Informe um nome de serviço válido.');
        }

        if (mb_strlen($codigo) > 30) {
            return $this->erro('O código interno deve ter no máximo 30 caracteres.');
        }

        if (mb_strlen($codigoFiscal) > 50) {
            return $this->erro('O código fiscal deve ter no máximo 50 caracteres.');
        }

        if (!in_array($unidade, ['servico', 'mensalidade', 'hora', 'unidade'], true)) {
            return $this->erro('Unidade de cobrança inválida.');
        }

        if (!in_array($permiteNfse, ['0', '1'], true)) {
            return $this->erro('Configuração de NFS-e inválida.');
        }

        if (!in_array($ativo, ['0', '1'], true)) {
            return $this->erro('Situação do serviço inválida.');
        }

        // Verifica duplicidade, incluindo registros excluídos logicamente.
        if ($codigo !== '') {
            $existente = $this->servicoModel
                ->withDeleted()
                ->where('codigo_interno', $codigo)
                ->first();

            if ($existente && (int) $existente['id'] !== $id) {
                return $this->erro(
                    'Este código interno já está cadastrado ou pertence a um registro arquivado.'
                );
            }
        }

        $dados = [
            'nome' => $nome,
            'codigo_interno' => $codigo !== '' ? $codigo : null,
            'descricao' => $descricao !== '' ? $descricao : null,
            'valor_padrao' => $valor,
            'unidade' => $unidade,
            'permite_nfse' => (int) $permiteNfse,
            'codigo_fiscal' => $codigoFiscal !== '' ? $codigoFiscal : null,
            'ativo' => (int) $ativo,
        ];

        try {
            $salvo = $id === null
                ? $this->servicoModel->insert($dados)
                : $this->servicoModel->update($id, $dados);

            if ($salvo === false) {
                return $this->erro(
                    implode(' ', $this->servicoModel->errors())
                        ?: 'Não foi possível salvar o serviço.'
                );
            }

            return redirect()
                ->to(site_url('financeiro/servicos'))
                ->with(
                    'sucesso',
                    $id === null
                        ? 'Serviço cadastrado com sucesso!'
                        : 'Serviço atualizado com sucesso!'
                );
        } catch (DatabaseException $e) {
            log_message('error', 'Erro ao salvar serviço: {erro}', [
                'erro' => $e->getMessage(),
            ]);

            return $this->erro(
                'Não foi possível salvar o serviço. Verifique os dados e tente novamente.'
            );
        }
    }

    public function desativar(int $id): RedirectResponse
    {
        $servico = $this->servicoModel->find($id);

        if (!$servico) {
            return $this->erro('Serviço não encontrado.');
        }

        if ((int) $servico['ativo'] === 0) {
            return $this->erro('Este serviço já está inativo.');
        }

        try {
            $atualizado = $this->servicoModel->update($id, [
                'ativo' => 0,
            ]);

            if ($atualizado === false) {
                return $this->erro('Não foi possível desativar o serviço.');
            }

            return redirect()
                ->to(site_url('financeiro/servicos'))
                ->with('sucesso', 'Serviço desativado com sucesso!');
        } catch (DatabaseException $e) {
            log_message('error', 'Erro ao desativar serviço: {erro}', [
                'erro' => $e->getMessage(),
            ]);

            return $this->erro(
                'Não foi possível desativar o serviço.'
            );
        }
    }

    private function erro(string $mensagem): RedirectResponse
    {
        return redirect()
            ->to(site_url('financeiro/servicos'))
            ->withInput()
            ->with('erro', $mensagem);
    }
}
