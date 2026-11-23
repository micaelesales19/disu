<?php

namespace App\Controllers\Financeiro;

use App\Controllers\BaseController;
use App\Models\CobrancaModel;
use App\Models\ClienteModel;
use App\Models\ContratoModel;
use CodeIgniter\HTTP\RedirectResponse;

class Cobrancas extends BaseController
{
    public function index()
    {
        $cobrancaModel = new CobrancaModel();
        $clienteModel  = new ClienteModel();

        // FILTROS
        $tipoPeriodo = $this->request->getGet('periodo') ?? 'mes';
        $mes         = (int) ($this->request->getGet('mes') ?? date('m'));
        $ano         = (int) ($this->request->getGet('ano') ?? date('Y'));
        $dataInicio  = $this->request->getGet('data_inicio');
        $dataFim     = $this->request->getGet('data_fim');
        $status      = $this->request->getGet('status') ?? '';
        $clienteId   = (int) ($this->request->getGet('cliente_id') ?? 0);
        $busca       = trim($this->request->getGet('busca') ?? '');

        if (!in_array($tipoPeriodo, ['todos', 'mes', 'periodo'], true)) {
            $tipoPeriodo = 'mes';
        }

        $mes = max(1, min(12, $mes));
        $ano = max(2000, min(2100, $ano));

        $statusPermitidos = [
            'em_aberto',
            'pago',
            'recebido',
            'vencido',
            'a_receber',
            'cancelado',
            'expirado',
            'processando',
            'erro'
        ];

        if (!in_array($status, $statusPermitidos, true)) {
            $status = '';
        }

        // BASE DA CONSULTA
        $builder = $cobrancaModel
            ->select('cobrancas.*')
            ->select('clientes.nome AS empresa_nome')
            ->select('contratos.titulo AS contrato_titulo')
            ->join(
                'clientes',
                'clientes.id = cobrancas.cliente_id',
                'left'
            )
            ->join(
                'contratos',
                'contratos.id = cobrancas.contrato_id',
                'left'
            );

        // PERÍODO
        if ($tipoPeriodo === 'mes') {
            $inicio = sprintf('%04d-%02d-01', $ano, $mes);
            $fim = date('Y-m-t', strtotime($inicio));

            $builder->where('cobrancas.vencimento >=', $inicio);
            $builder->where('cobrancas.vencimento <=', $fim);
        }

        if ($tipoPeriodo === 'periodo') {
            if ($dataInicio && $dataFim && $dataInicio > $dataFim) {
                [$dataInicio, $dataFim] = [$dataFim, $dataInicio];
            }

            if ($dataInicio) {
                $builder->where('cobrancas.vencimento >=', $dataInicio);
            }

            if ($dataFim) {
                $builder->where('cobrancas.vencimento <=', $dataFim);
            }
        }

        // CLIENTE
        if ($clienteId > 0) {
            $builder->where('cobrancas.cliente_id', $clienteId);
        }

        // STATUS
        if ($status !== '') {
            $builder->where('cobrancas.status', $status);
        }

        // PESQUISA
        if ($busca !== '') {
            $builder->groupStart()
                ->like('cobrancas.descricao', $busca)
                ->orLike('clientes.nome', $busca)
                ->orLike('contratos.titulo', $busca)
                ->groupEnd();
        }

        // Obtém os dados do resumo sem reiniciar o Builder original.
        $resumoBuilder = clone $builder;

        $registrosResumo = $resumoBuilder
            ->get(null, 0, false)
            ->getResultArray();

        $indicadores = [
            'quantidade' => count($registrosResumo),
            'total'      => 0,
            'aberto'     => 0,
            'recebido'   => 0,
        ];

        foreach ($registrosResumo as $item) {
            $valor = (float) $item['valor'];

            $indicadores['total'] += $valor;

            if (in_array($item['status'], [
                'em_aberto',
                'a_receber',
                'vencido',
                'processando'
            ], true)) {
                $indicadores['aberto'] += $valor;
            }

            if (in_array($item['status'], [
                'pago',
                'recebido'
            ], true)) {
                $indicadores['recebido'] += $valor;
            }
        }

        // LISTAGEM PAGINADA
        $cobrancas = $builder
            ->orderBy('cobrancas.vencimento', 'DESC')
            ->paginate(10, 'cobrancas');

        return view('financeiro/cobrancas/index', [
            'cobrancas'  => $cobrancas,
            'pager'      => $cobrancaModel->pager,
            'indicadores' => $indicadores,
            'clientes'   => $clienteModel
                ->orderBy('nome', 'ASC')
                ->findAll(),

            'filtros' => [
                'periodo'     => $tipoPeriodo,
                'mes'         => $mes,
                'ano'         => $ano,
                'data_inicio' => $dataInicio,
                'data_fim'    => $dataFim,
                'status'      => $status,
                'cliente_id'  => $clienteId,
                'busca'       => $busca,
            ],
        ]);
    }
    public function novo()
    {
        $clienteModel = new ClienteModel();

        $clientes = $clienteModel
            ->where('ativo', 1)
            ->orderBy('nome', 'ASC')
            ->findAll();

        return view('financeiro/cobrancas/form', [
            'titulo'   => 'Nova Cobrança',
            'clientes' => $clientes,
        ]);
    }


    public function contratosPorEmpresa(int $clienteId)
    {
        if ($clienteId < 1) {
            return $this->response->setJSON([]);
        }

        $cliente = (new ClienteModel())->find($clienteId);

        if (!$cliente || (int) $cliente['ativo'] !== 1) {
            return $this->response->setJSON([]);
        }

        $contratos = (new ContratoModel())
            ->select('id, titulo, valor, tipo')
            ->where('cliente_id', $clienteId)
            ->where('situacao', 'ativo')
            ->orderBy('titulo', 'ASC')
            ->findAll();

        return $this->response->setJSON($contratos);
    }


    public function editar(int $id)
    {
        $cobranca = (new CobrancaModel())
            ->select('cobrancas.*')
            ->select('clientes.nome AS empresa_nome')
            ->select('contratos.titulo AS contrato_titulo')
            ->join(
                'clientes',
                'clientes.id = cobrancas.cliente_id',
                'left'
            )
            ->join(
                'contratos',
                'contratos.id = cobrancas.contrato_id',
                'left'
            )
            ->where('cobrancas.id', $id)
            ->first();

        if (!$cobranca) {
            return redirect()
                ->to(site_url('financeiro/cobrancas'))
                ->with('error', 'Cobrança não encontrada.');
        }

        return view('financeiro/cobrancas/detalhes', [
            'titulo'   => 'Detalhes da Cobrança',
            'cobranca' => $cobranca,
        ]);
    }

    public function salvar(): RedirectResponse
    {
        $clienteId = filter_var(
            $this->request->getPost('cliente_id'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $contratoPost = trim(
            (string) $this->request->getPost('contrato_id')
        );

        $descricao = trim(
            (string) $this->request->getPost('descricao')
        );

        $valorInformado = trim(
            (string) $this->request->getPost('valor')
        );

        $vencimento = trim(
            (string) $this->request->getPost('vencimento')
        );

        // Validar empresa
        if (!$clienteId) {
            return $this->erroSalvar('Selecione uma empresa válida.');
        }

        $cliente = (new ClienteModel())
            ->where('ativo', 1)
            ->find($clienteId);

        if (!$cliente) {
            return $this->erroSalvar('Empresa não encontrada ou inativa.');
        }

        // Validar contrato opcional
        $contratoId = null;

        if ($contratoPost !== '') {

            $contratoId = filter_var(
                $contratoPost,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if (!$contratoId) {
                return $this->erroSalvar('Contrato inválido.');
            }

            $contrato = (new ContratoModel())->find($contratoId);

            if (
                !$contrato ||
                (int) $contrato['cliente_id'] !== (int) $clienteId ||
                $contrato['situacao'] !== 'ativo'
            ) {
                return $this->erroSalvar(
                    'O contrato selecionado não pertence à empresa ou não está ativo.'
                );
            }
        }

        // Converter valor brasileiro para decimal
        if (preg_match('/^(?:0|[1-9]\d{0,2}(?:\.\d{3})*),\d{2}$/', $valorInformado)) {
            $valorInformado = str_replace(
                ['.', ','],
                ['', '.'],
                $valorInformado
            );
        }

        if (
            !preg_match('/^(?:0|[1-9]\d{0,9})(?:\.\d{1,2})?$/', $valorInformado) ||
            (float) $valorInformado <= 0
        ) {
            return $this->erroSalvar(
                'Informe um valor válido e maior que zero.'
            );
        }

        // Validar vencimento
        $data = \DateTimeImmutable::createFromFormat(
            '!Y-m-d',
            $vencimento
        );

        if (
            $data === false ||
            $data->format('Y-m-d') !== $vencimento
        ) {
            return $this->erroSalvar(
                'Informe uma data de vencimento válida.'
            );
        }

        // Não permitir vencimento anterior à data atual
        $hoje = new \DateTimeImmutable('today');

        if ($data < $hoje) {
            return $this->erroSalvar(
                'A data de vencimento não pode ser anterior à data atual.'
            );
        }

        if ($descricao === '') {
            $descricao = $contratoId !== null
                ? $contrato['titulo']
                : 'Cobrança avulsa';
        }

        if (mb_strlen($descricao) > 500) {
            return $this->erroSalvar(
                'A descrição deve ter no máximo 500 caracteres.'
            );
        }

        $dados = [
            'cliente_id' => (int) $clienteId,
            'contrato_id' => $contratoId,
            'descricao' => $descricao,
            'valor' => $valorInformado,
            'vencimento' => $vencimento,
            'status' => 'em_aberto',
            'origem' => 'avulsa',
        ];

        try {
            $model = new CobrancaModel();

            if (!$model->insert($dados)) {
                return $this->erroSalvar(
                    implode(' ', $model->errors())
                        ?: 'Não foi possível cadastrar a cobrança.'
                );
            }

            return redirect()
                ->to(site_url('financeiro/cobrancas'))
                ->with(
                    'sucesso',
                    'Cobrança cadastrada com sucesso!'
                );
        } catch (\Throwable $e) {

            log_message('error', 'Erro ao salvar cobrança: {erro}', [
                'erro' => $e->getMessage()
            ]);

            return $this->erroSalvar(
                'Não foi possível salvar a cobrança. Tente novamente.'
            );
        }
    }


    /*public function atualizar(int $id): RedirectResponse
    {
        $model = new CobrancaModel();

        // Buscar cobrança existente
        $cobranca = $model->find($id);

        if (!$cobranca) {
            return redirect()
                ->to(site_url('financeiro/cobrancas'))
                ->with('error', 'Cobrança não encontrada.');
        }

        // Somente cobranças em aberto podem ser editadas
        if ($cobranca['status'] !== 'em_aberto') {
            return redirect()
                ->to(site_url('financeiro/cobrancas'))
                ->with('error', 'Esta cobrança não pode ser editada.');
        }

        // Dados recebidos
        $clienteId = filter_var(
            $this->request->getPost('cliente_id'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        $contratoPost = trim((string) $this->request->getPost('contrato_id'));
        $descricao = trim((string) $this->request->getPost('descricao'));
        $valor = trim((string) $this->request->getPost('valor'));
        $vencimento = trim((string) $this->request->getPost('vencimento'));

        // Validar empresa
        if (!$clienteId) {
            return $this->erroSalvar('Selecione uma empresa válida.');
        }

        $cliente = (new ClienteModel())->find($clienteId);

        if (!$cliente || (int) $cliente['ativo'] !== 1) {
            return $this->erroSalvar('Empresa não encontrada ou inativa.');
        }

        // Validar contrato (opcional)
        $contratoId = null;
        $contrato = null;

        if ($contratoPost !== '') {

            $contratoId = filter_var(
                $contratoPost,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1]]
            );

            if (!$contratoId) {
                return $this->erroSalvar('Contrato inválido.');
            }

            $contrato = (new ContratoModel())->find($contratoId);

            if (
                !$contrato ||
                (int) $contrato['cliente_id'] !== (int) $clienteId ||
                $contrato['situacao'] !== 'ativo'
            ) {
                return $this->erroSalvar(
                    'O contrato selecionado não pertence à empresa ou não está ativo.'
                );
            }
        }

        // Converter valor brasileiro
        if (preg_match('/^(?:0|[1-9]\d{0,2}(?:\.\d{3})*),\d{2}$/', $valor)) {
            $valor = str_replace(['.', ','], ['', '.'], $valor);
        }

        if (
            !preg_match('/^(?:0|[1-9]\d{0,9})(?:\.\d{1,2})?$/', $valor) ||
            (float) $valor <= 0
        ) {
            return $this->erroSalvar(
                'Informe um valor válido e maior que zero.'
            );
        }

        // Validar vencimento
        $data = \DateTimeImmutable::createFromFormat('!Y-m-d', $vencimento);

        if (!$data || $data->format('Y-m-d') !== $vencimento) {
            return $this->erroSalvar(
                'Informe uma data de vencimento válida.'
            );
        }

        // Descrição padrão
        if ($descricao === '') {
            $descricao = $contrato !== null
                ? $contrato['titulo']
                : 'Cobrança avulsa';
        }

        if (mb_strlen($descricao) > 500) {
            return $this->erroSalvar(
                'A descrição deve ter no máximo 500 caracteres.'
            );
        }

        // Atualizar somente os campos editáveis
        $dados = [
            'cliente_id' => (int) $clienteId,
            'contrato_id' => $contratoId,
            'descricao' => $descricao,
            'valor' => $valor,
            'vencimento' => $vencimento,
        ];

        try {

            if (!$model->update($id, $dados)) {
                return $this->erroSalvar(
                    implode(' ', $model->errors())
                        ?: 'Não foi possível atualizar a cobrança.'
                );
            }

            return redirect()
                ->to(site_url('financeiro/cobrancas/editar/' . $id))
                ->with('sucesso', 'Cobrança atualizada com sucesso!');
        } catch (\Throwable $e) {

            log_message('error', 'Erro ao atualizar cobrança: {erro}', [
                'erro' => $e->getMessage()
            ]);

            return $this->erroSalvar(
                'Não foi possível atualizar a cobrança.'
            );
        }
    }*/


    public function alterarVencimento(int $id): RedirectResponse
    {
        $model = new CobrancaModel();
        $cobranca = $model->find($id);

        if (!$cobranca) {
            return redirect()
                ->to(site_url('financeiro/cobrancas'))
                ->with('error', 'Cobrança não encontrada.');
        }

        $urlDetalhes = site_url('financeiro/cobrancas/editar/' . $id);

        // Cobranças finalizadas não podem ter vencimento alterado.
        if (!in_array($cobranca['status'], ['em_aberto', 'a_receber', 'vencido'], true)) {
            return redirect()
                ->to($urlDetalhes)
                ->with('error', 'Não é possível alterar o vencimento desta cobrança.');
        }

        // Não alterar localmente uma cobrança já vinculada ao banco.
        if (
            !empty($cobranca['boleto_id']) ||
            !empty($cobranca['boleto_linha_digitavel']) ||
            !empty($cobranca['pix_copia_cola'])
        ) {
            return redirect()
                ->to($urlDetalhes)
                ->with('error', 'Esta cobrança já possui dados bancários. O vencimento deverá ser atualizado também no Banco Inter.');
        }

        $vencimento = trim((string) $this->request->getPost('vencimento'));

        $data = \DateTimeImmutable::createFromFormat('!Y-m-d', $vencimento);

        if (
            !$data ||
            $data->format('Y-m-d') !== $vencimento
        ) {
            return redirect()
                ->to($urlDetalhes)
                ->with('error', 'Informe uma data de vencimento válida.');
        }

        if ($vencimento === $cobranca['vencimento']) {
            return redirect()
                ->to($urlDetalhes)
                ->with('sucesso', 'O vencimento não foi alterado.');
        }

        try {
            if (!$model->update($id, ['vencimento' => $vencimento])) {
                return redirect()
                    ->to($urlDetalhes)
                    ->with('error', implode(' ', $model->errors()) ?: 'Não foi possível alterar o vencimento.');
            }

            return redirect()
                ->to($urlDetalhes)
                ->with('sucesso', 'Vencimento atualizado com sucesso!');
        } catch (\Throwable $e) {
            log_message('error', 'Erro ao alterar vencimento da cobrança {id}: {erro}', [
                'id' => $id,
                'erro' => $e->getMessage(),
            ]);

            return redirect()
                ->to($urlDetalhes)
                ->with('error', 'Não foi possível alterar o vencimento.');
        }
    }

    public function alterarStatus(int $id)
    {
        $model = new CobrancaModel();
        $cobranca = $model->find($id);

        $url = site_url('financeiro/cobrancas/editar/' . $id);

        if (!$cobranca) {
            return redirect()
                ->to(site_url('financeiro/cobrancas'))
                ->with('error', 'Cobrança não encontrada.');
        }

        $status = (string) $this->request->getPost('status');

        $permitidos = ['em_aberto', 'recebido', 'vencido', 'cancelado'];

        if (!in_array($status, $permitidos, true)) {
            return redirect()->to($url)
                ->with('error', 'Status inválido.');
        }

        // Impede alterações em cobranças já pagas.
        if (
            in_array($cobranca['status'], ['pago', 'recebido'], true)
            || !empty($cobranca['pago_em'])
        ) {
            return redirect()->to($url)
                ->with('error', 'Esta cobrança já possui pagamento registrado.');
        }

        // Impede alteração manual de cobranças vinculadas ao banco.
        if (
            !empty($cobranca['boleto_id'])
            || !empty($cobranca['boleto_linha_digitavel'])
            || !empty($cobranca['pix_copia_cola'])
        ) {
            return redirect()->to($url)
                ->with('error', 'Esta cobrança possui dados bancários. A alteração deverá respeitar o Banco Inter.');
        }

        $dados = [
            'status' => $status,
        ];

        if ($status === 'recebido') {

            $dataPagamento = (string) $this->request->getPost('pago_em');

            $data = \DateTimeImmutable::createFromFormat(
                '!Y-m-d',
                $dataPagamento
            );

            if (!$data || $data->format('Y-m-d') !== $dataPagamento) {
                return redirect()->to($url)
                    ->with('error', 'Informe uma data de pagamento válida.');
            }

            $formaPagamento = (string) $this->request->getPost('forma_pagamento');

            if (!in_array(
                $formaPagamento,
                ['pix', 'transferencia', 'dinheiro', 'outro'],
                true
            )) {
                return redirect()->to($url)
                    ->with('error', 'Selecione uma forma de pagamento válida.');
            }

            $observacao = trim(
                (string) $this->request->getPost('observacoes')
            );

            // Baixa integral da cobrança.
            $dados['pago_em'] = $dataPagamento . ' 12:00:00';
            $dados['valor_pago'] = (float) $cobranca['valor'];

            // Novos campos.
            $dados['forma_pagamento'] = $formaPagamento;
            $dados['origem_pagamento'] = 'manual';

            // Agora guarda somente a observação.
            $dados['observacoes'] = $observacao;
        } else {

            // Cobrança sem pagamento confirmado.
            $dados['pago_em'] = null;
            $dados['valor_pago'] = null;
            $dados['forma_pagamento'] = null;
            $dados['origem_pagamento'] = null;
        }

        try {

            if (!$model->update($id, $dados)) {
                return redirect()->to($url)
                    ->with(
                        'error',
                        implode(' ', $model->errors())
                            ?: 'Não foi possível atualizar o status.'
                    );
            }

            return redirect()->to($url)
                ->with('sucesso', 'Status atualizado com sucesso!');
        } catch (\Throwable $e) {

            log_message(
                'error',
                'Erro ao alterar status da cobrança {id}: {erro}',
                [
                    'id' => $id,
                    'erro' => $e->getMessage(),
                ]
            );

            return redirect()->to($url)
                ->with('error', 'Erro ao atualizar o status.');
        }
    }

    private function erroSalvar(string $mensagem): RedirectResponse
    {
        return redirect()
            ->back()
            ->withInput()
            ->with('error', $mensagem);
    }
}
