<?php

namespace App\Controllers\Financeiro;

use App\Controllers\BaseController;
use App\Models\ClienteModel;
use App\Models\ServicoModel;
use App\Models\ContratoModel;
use App\Models\ContratoServicoModel;
use CodeIgniter\HTTP\RedirectResponse;
use Throwable;

class Contratos extends BaseController
{
    public function index(): string
    {
        $model = new ContratoModel();

        // Todos os contratos para calcular os indicadores
        $todosContratos = $model->listarComEmpresa();

        $ativos = array_filter(
            $todosContratos,
            static fn($c) => $c['situacao'] === 'ativo'
        );


        $hoje = new \DateTimeImmutable('today');
        $limite = $hoje->modify('+30 days');

        $reajustes = [];
        $resumoReajustes = [
            'todos' => 0,
            'vencidos' => 0,
            'a_vencer' => 0,
            'em_dia' => 0,
            'sem_reajuste' => 0,
        ];

        foreach ($todosContratos as $contrato) {

            $habilitado = (int) ($contrato['reajuste_automatico'] ?? 0) === 1;
            $data = $contrato['data_proximo_reajuste'] ?? null;

            $situacaoReajuste = 'sem_reajuste';

            if ($habilitado && $data) {
                $proximo = \DateTimeImmutable::createFromFormat('!Y-m-d', $data);

                if ($proximo !== false) {
                    if ($proximo < $hoje) {
                        $situacaoReajuste = 'vencidos';
                    } elseif ($proximo <= $limite) {
                        $situacaoReajuste = 'a_vencer';
                    } else {
                        $situacaoReajuste = 'em_dia';
                    }
                }
            }

            $contrato['situacao_reajuste'] = $situacaoReajuste;

            // Somente reajustes fixos têm percentual conhecido.
            $contrato['valor_reajustado'] = null;

            if (
                $habilitado &&
                ($contrato['tipo_reajuste'] ?? '') === 'fixo' &&
                $contrato['percentual_reajuste'] !== null
            ) {
                $valorAtual = (float) $contrato['valor'];
                $percentual = (float) $contrato['percentual_reajuste'];

                $contrato['valor_reajustado'] = round(
                    $valorAtual * (1 + $percentual / 100),
                    2
                );
            }

            $reajustes[] = $contrato;

            $resumoReajustes['todos']++;
            $resumoReajustes[$situacaoReajuste]++;
        }

        return view('financeiro/contratos/index', [
            'contratos' => $todosContratos,
            'reajustes' => $reajustes,
            'resumoReajustes' => $resumoReajustes,
            'indicadores' => [
                'total' => count($todosContratos),
                'ativos' => count($ativos),
                'encerrados' => count(array_filter(
                    $todosContratos,
                    static fn($c) => $c['situacao'] === 'encerrado'
                )),
                'vencidos' => 0,
                'previsto' => array_sum(array_map(
                    static fn($c) => (float) $c['valor'],
                    $ativos
                )),
            ],
        ]);
    }


    public function novo(): string
    {
        return view('financeiro/contratos/form', [
            'clientes' => (new ClienteModel())->where('ativo', 1)->orderBy('nome', 'ASC')->findAll(),
            'servicos' => (new ServicoModel())->where('ativo', 1)->orderBy('nome', 'ASC')->findAll(),
        ]);
    }


    public function editar(int $id)
    {
        $contratoModel = new \App\Models\ContratoModel();
        $contratoServicoModel = new \App\Models\ContratoServicoModel();

        $contrato = $contratoModel->find($id);

        if (!$contrato) {
            return redirect()
                ->to(site_url('financeiro/contratos'))
                ->with('error', 'Contrato não encontrado.');
        }

        $clientes = (new \App\Models\ClienteModel())
            ->orderBy('nome', 'ASC')
            ->findAll();

        $servicos = (new \App\Models\ServicoModel())
            ->orderBy('nome', 'ASC')
            ->findAll();

        $itensContrato = $contratoServicoModel
            ->where('contrato_id', $id)
            ->findAll();

        return view('financeiro/contratos/form', [
            'contrato'      => $contrato,
            'clientes'      => $clientes,
            'servicos'      => $servicos,
            'itensContrato' => $itensContrato,
        ]);
    }


    public function salvar(): RedirectResponse
    {
        return $this->persistir(null);
    }

    public function atualizar(int $id): RedirectResponse
    {
        return $this->persistir($id);
    }

    private function persistir(?int $id): RedirectResponse
    {
        $existente = $id !== null ? (new ContratoModel())->find($id) : null;
        if ($id !== null && !$existente) {
            return redirect()->to(site_url('financeiro/contratos'))->with('error', 'Contrato não encontrado.');
        }
        $post = $this->request->getPost();
        $valor = static fn(string $chave): string => trim((string)($post[$chave] ?? ''));
        $flag = static fn(string $chave): int => isset($post[$chave]) && (string)$post[$chave] === '1' ? 1 : 0;
        $clienteId = filter_var($post['cliente_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $titulo = $valor('titulo');
        $tipo = $valor('tipo');
        $forma = $valor('forma_pagamento');
        $dia = filter_var($post['dia_vencimento'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 31]]);
        $inicio = $valor('data_inicio');
        $indeterminado = $flag('prazo_indeterminado');
        $termino = $indeterminado ? null : $valor('data_termino');
        $dataValida = static function (?string $data): bool {
            if (!$data) return false;
            $dt = \DateTimeImmutable::createFromFormat('!Y-m-d', $data);
            return $dt !== false && $dt->format('Y-m-d') === $data;
        };

        if (!$clienteId || !(new ClienteModel())->find($clienteId) || ($id === null && !(new ClienteModel())->where('ativo', 1)->find($clienteId))) {
            return $this->falha('Selecione uma empresa ativa e válida.');
        }
        if ($titulo === '' || mb_strlen($titulo) > 180) return $this->falha('Informe um título de até 180 caracteres.');
        if (!in_array($tipo, ['mensal', 'pontual'], true)) return $this->falha('Tipo de contrato inválido.');
        if (!in_array($forma, ['boleto', 'pix', 'transferencia', 'outro'], true)) return $this->falha('Forma de pagamento inválida.');
        if (!$dia || !$dataValida($inicio)) return $this->falha('Informe um dia de vencimento e uma data de início válidos.');
        if (!$indeterminado && (!$dataValida($termino) || $termino < $inicio)) return $this->falha('Informe uma data de término igual ou posterior ao início.');

        $ids = $post['servico_id'] ?? [];
        $qtds = $post['quantidade'] ?? [];
        $precos = $post['valor_unitario'] ?? [];
        if (!is_array($ids) || !is_array($qtds) || !is_array($precos) || count($ids) === 0 || count($ids) !== count($qtds) || count($ids) !== count($precos)) {
            return $this->falha('Adicione ao menos um serviço válido ao contrato.');
        }
        if (count($ids) > 100) return $this->falha('Limite de 100 serviços por contrato.');


        $tipo = (string) $this->request->getPost('tipo');

        $decimoTerceiro = $tipo === 'mensal'
            && $this->request->getPost('decimo_terceiro') === '1';

        $mesesDecimo = null;

        if ($decimoTerceiro) {
            $mesesRecebidos = (string) $this->request
                ->getPost('meses_decimo_terceiro');

            $partes = explode(',', $mesesRecebidos);

            $meses = [];

            foreach ($partes as $parte) {
                $parte = trim($parte);

                if (!preg_match('/^(?:[1-9]|1[0-2])$/', $parte)) {
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Selecione meses válidos para o 13º honorário.');
                }

                $meses[] = (int) $parte;
            }

            $meses = array_values(array_unique($meses));
            sort($meses, SORT_NUMERIC);

            if (empty($meses)) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Selecione pelo menos um mês para o 13º honorário.');
            }

            $mesesDecimo = implode(',', $meses);
        }


        $itens = [];
        $totalCentavos = 0;
        $servicoModel = new ServicoModel();
        foreach ($ids as $i => $idBruto) {
            $servicoId = filter_var($idBruto, FILTER_VALIDATE_INT, [
                'options' => ['min_range' => 1]
            ]);
            $qtd = filter_var($qtds[$i], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

            $preco = trim((string) $precos[$i]);

            // Aceita valores brasileiros formatados:
            // 1.250,75 -> 1250.75
            // 100,00   -> 100.00
            if (preg_match('/^(?:0|[1-9]\d{0,2}(?:\.\d{3})*),\d{2}$/', $preco)) {
                $preco = str_replace(['.', ','], ['', '.'], $preco);
            }


            if (!$servicoId || !$qtd || $qtd > 100000 || !preg_match('/^(?:0|[1-9]\d{0,9})(?:\.\d{1,2})?$/', $preco)) {
                return $this->falha('Confira os serviços, as quantidades e os valores unitários.');
            }
            if (!$servicoModel->find($servicoId) || ($existente === null && !$servicoModel->where('ativo', 1)->find($servicoId))) return $this->falha('Um dos serviços selecionados não está disponível.');
            [$reais, $centavos] = array_pad(explode('.', $preco, 2), 2, '');
            $unitarioCentavos = (int)$reais * 100 + (int)str_pad($centavos, 2, '0');
            $subtotalCentavos = $unitarioCentavos * $qtd;
            $totalCentavos += $subtotalCentavos;
            if ($totalCentavos > 999999999999) return $this->falha('O valor total excede o limite permitido.');
            $itens[] = [
                'servico_id' => $servicoId,
                'quantidade' => $qtd,
                'valor_unitario' => sprintf('%d.%02d', intdiv($unitarioCentavos, 100), $unitarioCentavos % 100),
                'subtotal' => sprintf('%d.%02d', intdiv($subtotalCentavos, 100), $subtotalCentavos % 100),
            ];
        }

        $percentual = static function (string $chave) use ($valor): ?string {
            $v = $valor($chave);
            return $v === '' ? null : $v;
        };

        $reajuste = $flag('reajuste_automatico');

        $desconto = $flag('desconto_antecipacao');

        $tipoDesconto = null;
        $valorDescontoFixo = null;
        $percDesconto = null;
        $diasAntecipacao = null;

        if ($desconto) {

            $tipoDesconto = $valor('tipo_desconto');

            if (!in_array($tipoDesconto, ['percentual', 'fixo'], true)) {
                return $this->falha('Selecione um tipo de desconto válido.');
            }

            // Dias mínimos para receber o desconto
            $diasAntecipacao = filter_var(
                $post['dias_antecipacao'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => [
                    'min_range' => 1,
                    'max_range' => 365,
                ]]
            );

            if ($diasAntecipacao === false) {
                return $this->falha(
                    'Informe uma quantidade de dias entre 1 e 365.'
                );
            }

            if ($tipoDesconto === 'percentual') {

                $percDesconto = $percentual('percentual_desconto');

                if (
                    $percDesconto === null ||
                    !preg_match('/^\d{1,3}(?:\.\d{1,2})?$/', $percDesconto) ||
                    $percDesconto <= 0 ||
                    $percDesconto > 100
                ) {

                    return $this->falha(
                        'Informe um percentual de desconto entre 0,01% e 100%.'
                    );
                }
            } else {

                $valorInformado = $valor('valor_desconto_fixo');

                if (
                    $valorInformado === '' ||
                    !is_numeric($valorInformado) ||
                    (float) $valorInformado <= 0
                ) {
                    return $this->falha(
                        'Informe um valor fixo de desconto válido.'
                    );
                }

                if (!preg_match('/^(?:0|[1-9]\d{0,9})(?:\.\d{1,2})?$/', $valorInformado)) {
                    return $this->falha(
                        'Informe um desconto fixo válido, com até duas casas decimais.'
                    );
                }

                [$reaisDesconto, $centavosDesconto] = array_pad(
                    explode('.', $valorInformado, 2),
                    2,
                    ''
                );

                $descontoCentavos = (int) $reaisDesconto * 100
                    + (int) str_pad($centavosDesconto, 2, '0');

                if ($descontoCentavos <= 0) {
                    return $this->falha('O desconto fixo deve ser maior que zero.');
                }

                if ($descontoCentavos > $totalCentavos) {
                    return $this->falha(
                        'O desconto fixo não pode ultrapassar o valor do contrato.'
                    );
                }

                $valorDescontoFixo = sprintf(
                    '%d.%02d',
                    intdiv($descontoCentavos, 100),
                    $descontoCentavos % 100
                );
            }
        }


        $nfse = $flag('emitir_nfse');

        $tipoReajuste = null;
        $periodicidadeReajuste = null;
        $aplicacaoReajuste = null;

        if ($reajuste) {

            $tipoReajuste = $valor('tipo_reajuste');
            $aplicacaoReajuste = $valor('aplicacao_reajuste');

            $periodicidadeReajuste = filter_var(
                $post['periodicidade_reajuste'] ?? null,
                FILTER_VALIDATE_INT,
                ['options' => ['min_range' => 1, 'max_range' => 120]]
            );

            if (!in_array($tipoReajuste, ['fixo', 'ipca', 'igpm', 'inpc'], true)) {
                return $this->falha('Selecione um tipo de reajuste válido.');
            }

            if (!in_array($aplicacaoReajuste, ['manual', 'automatico'], true)) {
                return $this->falha('Selecione uma forma de aplicação válida.');
            }

            if ($periodicidadeReajuste === false) {
                return $this->falha('Informe uma periodicidade entre 1 e 120 meses.');
            }
        }

        $percReajuste = ($reajuste && $tipoReajuste === 'fixo')
            ? $percentual('percentual_reajuste')
            : null;

        if ($reajuste && $tipoReajuste === 'fixo' && $percReajuste === null) {
            return $this->falha('Informe o percentual fixo do reajuste.');
        }

        foreach ([$percReajuste, $percDesconto] as $percent) {
            if ($percent !== null && (!preg_match('/^\d{1,3}(?:\.\d{1,2})?$/', $percent) || (float)$percent > 100)) {
                return $this->falha('Percentuais devem estar entre 0 e 100, com até duas casas decimais.');
            }
        }

        $proximoReajuste = $reajuste
            ? ($valor('data_proximo_reajuste') ?: null)
            : null;

        if ($reajuste) {
            if ($proximoReajuste === null) {
                return $this->falha(
                    'Informe a data do próximo reajuste.'
                );
            }

            if (!$dataValida($proximoReajuste)) {
                return $this->falha(
                    'A data do próximo reajuste é inválida.'
                );
            }

            if ($proximoReajuste < $inicio) {
                return $this->falha(
                    'O próximo reajuste não pode ser anterior ao início do contrato.'
                );
            }
        }

        $momentoNfse = $nfse ? ($valor('momento_nfse') ?: null) : null;
        if ($momentoNfse !== null && !in_array($momentoNfse, ['pagamento', 'manual'], true)) return $this->falha('Momento de emissão de NFS-e inválido.');
        $observacoes = $valor('observacoes');

        $dados = [
            'cliente_id' => $clienteId,
            'titulo' => $titulo,
            'tipo' => $tipo,
            'valor' => sprintf('%d.%02d', intdiv($totalCentavos, 100), $totalCentavos % 100),
            'dia_vencimento' => $dia,
            'forma_pagamento' => $forma,
            'data_inicio' => $inicio,
            'data_termino' => $termino,
            'prazo_indeterminado' => $indeterminado,
            'decimo_terceiro'       => $decimoTerceiro ? 1 : 0,
            'meses_decimo_terceiro' => $mesesDecimo,
            'mover_fim_semana' => $flag('mover_fim_semana'),
            'emitir_nfse' => $nfse,
            'momento_nfse' => $momentoNfse,
            'reajuste_automatico' => $reajuste,
            'tipo_reajuste' => $tipoReajuste,
            'periodicidade_reajuste' => $periodicidadeReajuste,
            'aplicacao_reajuste' => $aplicacaoReajuste,
            'percentual_reajuste' => $percReajuste,
            'data_proximo_reajuste' => $proximoReajuste,
            'desconto_antecipacao' => $desconto,
            'tipo_desconto' => $tipoDesconto,
            'percentual_desconto' => $percDesconto,
            'valor_desconto_fixo' => $valorDescontoFixo,
            'dias_antecipacao' => $diasAntecipacao,
            'observacoes' => $observacoes ?: null,
            'situacao' => $existente['situacao'] ?? 'ativo',
        ];

        $db = db_connect();
        $db->transBegin();
        try {
            $contratoModel = new ContratoModel();
            if ($id === null) {
                $contratoId = $contratoModel->insert($dados, true);
                if (!$contratoId) throw new \RuntimeException(implode('; ', $contratoModel->errors()) ?: 'Falha ao inserir contrato.');
            } else {
                $contratoId = $id;
                if (!$contratoModel->update($id, $dados)) throw new \RuntimeException(implode('; ', $contratoModel->errors()) ?: 'Falha ao atualizar contrato.');
            }
            $itensModel = new ContratoServicoModel();
            if ($id !== null) {
                $db->table('contrato_servicos')->where('contrato_id', $id)->delete();
            }
            foreach ($itens as $item) {
                $item['contrato_id'] = (int)$contratoId;
                if (!$itensModel->insert($item)) throw new \RuntimeException(implode('; ', $itensModel->errors()) ?: 'Falha ao inserir serviço.');
            }
            if ($db->transStatus() === false) throw new \RuntimeException('Falha na transação do banco de dados.');
            $db->transCommit();

            return redirect()
                ->to(site_url('financeiro/contratos/editar/' . $contratoId))
                ->with(
                    'sucesso',
                    ($id === null
                        ? 'Contrato cadastrado com sucesso!'
                        : 'Alterações salvas com sucesso!'
                    )
                );
        } catch (Throwable $e) {
            $db->transRollback();
            log_message('error', 'Erro ao salvar contrato: {erro}', ['erro' => $e->getMessage()]);
            return $this->falha('Não foi possível salvar o contrato. Confira os dados e tente novamente.');
        }
    }

    public function excluir(int $id): RedirectResponse
    {
        $contratoModel = new ContratoModel();

        $contrato = $contratoModel->find($id);

        if (!$contrato) {
            return redirect()
                ->to(site_url('financeiro/contratos'))
                ->with('error', 'Contrato não encontrado.');
        }

        try {
            if (!$contratoModel->delete($id)) {
                throw new \RuntimeException('Falha ao excluir contrato.');
            }

            return redirect()
                ->to(site_url('financeiro/contratos'))
                ->with('sucesso', 'Contrato excluído com sucesso!');
        } catch (Throwable $e) {

            log_message(
                'error',
                'Erro ao excluir contrato {id}: {erro}',
                [
                    'id' => $id,
                    'erro' => $e->getMessage()
                ]
            );

            return redirect()
                ->to(site_url('financeiro/contratos'))
                ->with('error', 'Não foi possível excluir o contrato.');
        }
    }

    private function falha(string $mensagem): RedirectResponse
    {
        return redirect()->back()->withInput()->with('error', $mensagem);
    }
}
