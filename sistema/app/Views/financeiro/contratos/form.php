<?php

/**
 * @var array $contrato
 */
?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger">
        <i class="ri-error-warning-line me-2"></i>
        <?= esc(session('error')) ?>
    </div>
<?php endif; ?>

<?php if (session('sucesso')): ?>
    <div class="alert alert-success alert-dismissible fade show"
        role="alert">

        <i class="ri-checkbox-circle-line me-2"></i>

        <?= esc(session('sucesso')) ?>

        <button type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Fechar">
        </button>
    </div>
<?php endif; ?>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<style>
    /* Campo principal */
    #formContrato .ts-wrapper .ts-control {
        background-color: var(--contrato-select-bg) !important;
        color: var(--contrato-select-text) !important;
        border: 1px solid var(--contrato-select-border) !important;
        border-radius: 6px;
        min-height: 38px;
    }

    #formContrato .ts-control input,
    #formContrato .ts-control .item {
        color: var(--contrato-select-text) !important;
    }

    #formContrato .ts-control input::placeholder {
        color: var(--bs-secondary-color, #6c757d) !important;
    }

    /* Lista suspensa, inclusive quando anexada ao body */
    .ts-dropdown {
        background-color: var(--bs-body-bg, #fff) !important;
        color: var(--bs-body-color, #212529) !important;
        border: 1px solid var(--bs-border-color, #dee2e6) !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, .15) !important;
        border-radius: 6px !important;
        z-index: 2050 !important;
        overflow: hidden;
    }

    .ts-dropdown .option,
    .ts-dropdown .no-results {
        background-color: var(--bs-body-bg, #fff) !important;
        color: var(--bs-body-color, #212529) !important;
        padding: 10px 12px !important;
    }

    .ts-dropdown .option.active,
    .ts-dropdown .option:hover {
        background-color: var(--bs-tertiary-bg, #eef4ff) !important;
        color: var(--bs-body-color, #212529) !important;
    }

    /* Validação */
    #formContrato .campo-invalido {
        border-color: var(--bs-danger, #dc3545) !important;
        box-shadow: 0 0 0 .2rem rgba(220, 53, 69, .12);
    }

    #formContrato .ts-wrapper.campo-invalido .ts-control {
        border-color: var(--bs-danger, #dc3545) !important;
    }
</style>

<?php
$clientes = $clientes ?? [];
$servicos = $servicos ?? [];
$contrato = $contrato ?? null;
$itensContrato = $itensContrato ?? [];

$editando = !empty($contrato['id']);
?>

<div class="main-breadcrumb d-flex align-items-center justify-content-between my-3">
    <div>
        <h2 class="breadcrumb-title mb-1 fs-18">
            <?= $editando ? 'Editar Contrato' : 'Novo Contrato' ?>
        </h2>
        <p class="text-muted mb-0 fs-12">Cadastre os dados e as condições do contrato recorrente</p>
    </div>
    <a href="<?= site_url('financeiro/contratos') ?>" class="btn btn-light border">
        <i class="ri-arrow-left-line me-1"></i>Voltar
    </a>
</div>

<div class="alert border rounded-3 mb-4" style="background-color:#eef4ff;border-color:#d8e4f5!important;color:#183b70" role="note">
    <h6 class="fw-bold mb-2"><i class="ri-information-line me-1"></i>Como preencher o contrato</h6>
    <p class="mb-0">Selecione uma empresa já cadastrada, escolha um ou mais serviços e confira o valor total calculado. Nesta etapa, o formulário é apenas uma prévia: não salva contratos nem gera cobranças.</p>
</div>

<form id="formContrato"
    action="<?= $editando
                ? site_url('financeiro/contratos/atualizar/' . $contrato['id'])
                : site_url('financeiro/contratos/salvar') ?>"
    method="POST"
    autocomplete="off">

    <?= csrf_field() ?>

    <ul class="nav nav-tabs mb-4" id="abasContrato" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="tab-dados" data-bs-toggle="tab" data-bs-target="#dadosContrato" type="button" role="tab" aria-controls="dadosContrato" aria-selected="true">
                <i class="ri-file-list-3-line me-1"></i>Dados do Contrato
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-vigencia" data-bs-toggle="tab" data-bs-target="#vigenciaContrato" type="button" role="tab" aria-controls="vigenciaContrato" aria-selected="false">
                <i class="ri-calendar-line me-1"></i>Vigência e Cobranças
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="tab-adicionais" data-bs-toggle="tab" data-bs-target="#adicionaisContrato" type="button" role="tab" aria-controls="adicionaisContrato" aria-selected="false">
                <i class="ri-settings-3-line me-1"></i>Configurações Adicionais
            </button>
        </li>
    </ul>

    <div class="tab-content" id="conteudoContrato">
        <div class="tab-pane fade show active" id="dadosContrato" role="tabpanel" aria-labelledby="tab-dados">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-2">Identificação</h5>
                    <p class="text-muted mb-3">Informações principais do contrato recorrente.</p>
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="titulo" class="form-label">Título do contrato *</label>
                            <input type="text" class="form-control" id="titulo" name="titulo" maxlength="180" placeholder="Ex.: Honorários contábeis mensais" value="<?= esc(old('titulo', $contrato['titulo'] ?? ''), 'attr') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label for="cliente_id" class="form-label fw-medium">
                                Empresa *
                            </label>

                            <select
                                name="cliente_id"
                                id="cliente_id"
                                class="form-select"
                                required>

                                <option value="">Selecione ou pesquise uma empresa...</option>

                                <?php foreach ($clientes as $cliente): ?>
                                    <option
                                        value="<?= (int) $cliente['id'] ?>"
                                        <?= (string) old('cliente_id', $contrato['cliente_id'] ?? '') === (string) $cliente['id'] ? 'selected' : '' ?>>

                                        <?= esc($cliente['nome']) ?>
                                        <?= !empty($cliente['cpf_cnpj']) ? ' - ' . esc($cliente['cpf_cnpj']) : '' ?>

                                    </option>
                                <?php endforeach; ?>

                            </select>

                            <small class="text-muted">
                                Digite o nome ou CNPJ para localizar a empresa.
                            </small>
                        </div>
                        <div class="col-md-4">
                            <label for="tipo" class="form-label">Tipo de contrato *</label>

                            <select class="form-select" id="tipo" name="tipo" required>
                                <?php $tipoSelecionado = old('tipo', $contrato['tipo'] ?? 'mensal'); ?>

                                <option value="mensal"
                                    <?= $tipoSelecionado === 'mensal' ? 'selected' : '' ?>>
                                    Mensal
                                </option>

                                <option value="pontual"
                                    <?= $tipoSelecionado === 'pontual' ? 'selected' : '' ?>>
                                    Valor único
                                </option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="dia_vencimento" class="form-label">Dia do vencimento *</label>
                            <input type="number" class="form-control" id="dia_vencimento" name="dia_vencimento" min="1" max="31" value="<?= esc(old('dia_vencimento', $contrato['dia_vencimento'] ?? 10), 'attr') ?>" required>
                            <div class="form-text">Dias 29, 30 e 31 exigirão regra para meses mais curtos.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="forma_pagamento" class="form-label">Forma de pagamento *</label>
                            <select class="form-select"
                                id="forma_pagamento"
                                name="forma_pagamento"
                                required>

                                <?php
                                $formaSelecionada = old(
                                    'forma_pagamento',
                                    $contrato['forma_pagamento'] ?? 'boleto'
                                );

                                $formas = [
                                    'boleto' => 'Boleto',
                                    'pix' => 'Pix',
                                    'transferencia' => 'Transferência',
                                    'outro' => 'Outro',
                                ];
                                ?>

                                <?php foreach ($formas as $valor => $descricao): ?>
                                    <option value="<?= esc($valor, 'attr') ?>"
                                        <?= $formaSelecionada === $valor ? 'selected' : '' ?>>
                                        <?= esc($descricao) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                        <h5 class="fw-semibold mb-0">Serviços contratados</h5>
                        <button type="button" class="btn btn-light border" id="adicionarServico"><i class="ri-add-line me-1"></i>Adicionar serviço</button>
                    </div>
                    <p class="text-muted mb-3">Os valores são sugeridos pelo cadastro de Serviços e podem ser alterados neste contrato.</p>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="tabelaItensContrato">
                            <thead>
                                <tr>
                                    <th style="min-width:240px">Serviço</th>
                                    <th style="min-width:100px">Quantidade</th>
                                    <th style="min-width:140px">Valor unitário (R$)</th>
                                    <th style="min-width:130px">Subtotal</th>
                                    <th class="text-end">Ações</th>
                                </tr>
                            </thead>
                            <tbody id="itensContrato"></tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end align-items-center gap-3 mt-3">
                        <span class="text-muted">Valor por Cobrança</span>
                        <strong class="fs-22" id="totalContrato">R$ 0,00</strong>
                    </div>
                    <div class="form-text text-end">O total será calculado automaticamente pela quantidade × valor unitário.</div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="vigenciaContrato" role="tabpanel" aria-labelledby="tab-vigencia">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-2">Período do contrato</h5>
                    <p class="text-muted mb-3">Defina quando o contrato começa e se possui data de encerramento.</p>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="data_inicio" class="form-label">Data de início *</label>
                            <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?= esc(old('data_inicio', $contrato['data_inicio'] ?? ''), 'attr') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label for="data_termino" class="form-label">Data de término</label>
                            <input type="date" class="form-control" id="data_termino" name="data_termino" value="<?= esc(old('data_termino', $contrato['data_termino'] ?? ''), 'attr') ?>">
                        </div>

                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="prazo_indeterminado" name="prazo_indeterminado" value="1" <?= (int) ($contrato['prazo_indeterminado'] ?? 1) === 1 ? 'checked' : '' ?>>
                                <label class="form-check-label" for="prazo_indeterminado">Contrato por tempo indeterminado</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-2">Cobranças recorrentes</h5>
                    <p class="text-muted mb-3">Configurações que serão utilizadas na futura geração das mensalidades.</p>

                    <div id="secaoDecimoTerceiro" class="mb-4">

                        <div class="form-check form-switch mb-2">
                            <input type="checkbox"
                                class="form-check-input"
                                id="decimo_terceiro"
                                name="decimo_terceiro"
                                value="1"
                                <?= (int) ($contrato['decimo_terceiro'] ?? 0) === 1
                                    ? 'checked' : '' ?>>

                            <label class="form-check-label fw-semibold"
                                for="decimo_terceiro">
                                13º honorário
                            </label>
                        </div>

                        <p class="text-muted fs-12 ms-4 mb-3">
                            Uma mensalidade extra por ano, cobrada nos meses que você escolher.
                        </p>

                        <div id="configuracaoDecimoTerceiro" class="d-none">

                            <label class="form-label fw-medium">
                                Em quais meses cobrar o 13º?
                            </label>

                            <div class="d-flex flex-wrap gap-2 mb-3">
                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    data-meses-preset="dezembro">
                                    Só dezembro
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    data-meses-preset="novdez">
                                    Nov + Dez
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    data-meses-preset="trimestral">
                                    Trimestral
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-primary"
                                    data-meses-preset="todos">
                                    12× (todo mês)
                                </button>
                            </div>

                            <div class="d-flex flex-wrap gap-2 mb-3" id="mesesDecimoTerceiro">
                                <?php
                                $meses = [
                                    1 => 'Jan',
                                    2 => 'Fev',
                                    3 => 'Mar',
                                    4 => 'Abr',
                                    5 => 'Mai',
                                    6 => 'Jun',
                                    7 => 'Jul',
                                    8 => 'Ago',
                                    9 => 'Set',
                                    10 => 'Out',
                                    11 => 'Nov',
                                    12 => 'Dez'
                                ];
                                ?>

                                <?php foreach ($meses as $numero => $nome): ?>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary mes-decimo"
                                        data-mes="<?= $numero ?>">
                                        <?= $nome ?>
                                    </button>
                                <?php endforeach; ?>
                            </div>

                            <input type="hidden"
                                id="meses_decimo_terceiro"
                                name="meses_decimo_terceiro"
                                value="">

                            <small class="text-muted" id="resumoDecimoTerceiro">
                                Selecione os meses para calcular o 13º.
                            </small>

                        </div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" class="form-check-input" id="mover_fim_semana" name="mover_fim_semana" value="1" <?= (int) ($contrato['mover_fim_semana'] ?? 0) === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="mover_fim_semana">Mover vencimentos de sábado ou domingo para a segunda-feira</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade" id="adicionaisContrato" role="tabpanel" aria-labelledby="tab-adicionais">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-2">Nota fiscal de serviço</h5>
                    <p class="text-muted mb-3">Preparação para futura integração de NFS-e.</p>
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" class="form-check-input" id="emitir_nfse" name="emitir_nfse" value="1" <?= (int) ($contrato['emitir_nfse'] ?? 0) === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="emitir_nfse">Habilitar emissão de NFS-e para este contrato</label>
                    </div>

                    <div id="configuracaoNfse" class="mt-3">
                        <label for="momento_nfse" class="form-label">
                            Quando gerar a nota?
                        </label>

                        <select
                            class="form-select"
                            id="momento_nfse"
                            name="momento_nfse">
                            <?php
                            $momentoNfse = old(
                                'momento_nfse',
                                $contrato['momento_nfse'] ?? 'pagamento'
                            );
                            ?>

                            <option value="pagamento"
                                <?= $momentoNfse === 'pagamento' ? 'selected' : '' ?>>
                                Ao receber o pagamento
                            </option>

                            <option value="manual"
                                <?= $momentoNfse === 'manual' ? 'selected' : '' ?>>
                                Manualmente
                            </option>
                        </select>

                        <small class="text-muted d-block mt-2">
                            A nota fiscal será emitida pela contabilidade para a empresa
                            vinculada ao contrato, utilizando os dados fiscais do serviço.
                        </small>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-2">Reajustes e descontos</h5>
                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" class="form-check-input" id="reajuste_automatico" name="reajuste_automatico" value="1" <?= (int) ($contrato['reajuste_automatico'] ?? 0) === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="reajuste_automatico">Habilitar reajuste periódico</label>
                    </div>

                    <div id="camposReajuste" class="row g-3 mb-3 d-none">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label" for="tipo_reajuste">
                                    Tipo de reajuste
                                </label>

                                <?php
                                $tipoReajuste = old(
                                    'tipo_reajuste',
                                    $contrato['tipo_reajuste'] ?? 'fixo'
                                );
                                ?>

                                <select class="form-select"
                                    name="tipo_reajuste"
                                    id="tipo_reajuste">

                                    <option value="fixo" <?= $tipoReajuste === 'fixo' ? 'selected' : '' ?>>
                                        Percentual fixo
                                    </option>

                                    <option value="ipca" <?= $tipoReajuste === 'ipca' ? 'selected' : '' ?>>
                                        IPCA (IBGE)
                                    </option>

                                    <option value="igpm" <?= $tipoReajuste === 'igpm' ? 'selected' : '' ?>>
                                        IGP-M (FGV)
                                    </option>

                                    <option value="inpc" <?= $tipoReajuste === 'inpc' ? 'selected' : '' ?>>
                                        INPC (IBGE)
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-6" id="campoPercentualFixo">
                                <label class="form-label" for="percentual_reajuste">
                                    Percentual (%)
                                </label>

                                <input type="number"
                                    class="form-control"
                                    id="percentual_reajuste"
                                    name="percentual_reajuste"
                                    min="0"
                                    max="100"
                                    step="0.01"
                                    value="<?= esc(old(
                                                'percentual_reajuste',
                                                $contrato['percentual_reajuste'] ?? ''
                                            )) ?>"
                                    placeholder="Ex.: 5.00">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="periodicidade_reajuste">
                                    A cada (meses)
                                </label>

                                <input type="number"
                                    class="form-control"
                                    id="periodicidade_reajuste"
                                    name="periodicidade_reajuste"
                                    min="1"
                                    max="120"
                                    step="1"
                                    value="<?= esc(old(
                                                'periodicidade_reajuste',
                                                $contrato['periodicidade_reajuste'] ?? 12
                                            )) ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" for="aplicacao_reajuste">
                                    Como aplicar
                                </label>

                                <?php
                                $aplicacaoReajuste = old(
                                    'aplicacao_reajuste',
                                    $contrato['aplicacao_reajuste'] ?? 'manual'
                                );
                                ?>

                                <select class="form-select"
                                    id="aplicacao_reajuste"
                                    name="aplicacao_reajuste">

                                    <option value="manual"
                                        <?= $aplicacaoReajuste === 'manual' ? 'selected' : '' ?>>
                                        Manual (só avisar)
                                    </option>

                                    <option value="automatico"
                                        <?= $aplicacaoReajuste === 'automatico' ? 'selected' : '' ?>>
                                        Automático
                                    </option>
                                </select>
                            </div>

                            <div class="col-12">
                                <small class="text-muted" id="descricaoReajuste">
                                    O sistema avisará quando chegar a data do reajuste.
                                </small>
                            </div>

                            <div class="col-md-6">
                                <label for="data_proximo_reajuste" class="form-label">
                                    Próximo reajuste
                                </label>

                                <input type="date"
                                    class="form-control"
                                    id="data_proximo_reajuste"
                                    name="data_proximo_reajuste"
                                    value="<?= esc(old(
                                                'data_proximo_reajuste',
                                                $contrato['data_proximo_reajuste'] ?? ''
                                            ), 'attr') ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input type="checkbox" class="form-check-input" id="desconto_antecipacao" name="desconto_antecipacao" value="1" <?= (int) ($contrato['desconto_antecipacao'] ?? 0) === 1 ? 'checked' : '' ?>>
                        <label class="form-check-label" for="desconto_antecipacao">Habilitar desconto por antecipação</label>
                    </div>


                    <div class="row g-3 d-none" id="camposDesconto">

                        <div class="col-md-6">
                            <label for="tipo_desconto" class="form-label">
                                Tipo de desconto
                            </label>

                            <?php
                            $tipoDesconto = old(
                                'tipo_desconto',
                                $contrato['tipo_desconto'] ?? 'percentual'
                            );
                            ?>

                            <select class="form-select"
                                id="tipo_desconto"
                                name="tipo_desconto">

                                <option value="percentual"
                                    <?= $tipoDesconto === 'percentual' ? 'selected' : '' ?>>
                                    Percentual (%)
                                </option>

                                <option value="fixo"
                                    <?= $tipoDesconto === 'fixo' ? 'selected' : '' ?>>
                                    Valor fixo (R$)
                                </option>

                            </select>
                        </div>

                        <div class="col-md-6" id="campoDescontoPercentual">
                            <label for="percentual_desconto" class="form-label">
                                Desconto (%)
                            </label>

                            <input type="number"
                                class="form-control"
                                id="percentual_desconto"
                                name="percentual_desconto"
                                min="0.01"
                                max="100"
                                step="0.01"
                                placeholder="Ex.: 5.00"
                                value="<?= esc(old(
                                            'percentual_desconto',
                                            $contrato['percentual_desconto'] ?? ''
                                        ), 'attr') ?>">
                        </div>

                        <div class="col-md-6 d-none" id="campoDescontoFixo">
                            <label for="valor_desconto_fixo" class="form-label">
                                Desconto (R$)
                            </label>

                            <input type="number"
                                class="form-control"
                                id="valor_desconto_fixo"
                                name="valor_desconto_fixo"
                                min="0.01"
                                step="0.01"
                                placeholder="Ex.: 30.00"
                                value="<?= esc(old(
                                            'valor_desconto_fixo',
                                            $contrato['valor_desconto_fixo'] ?? ''
                                        ), 'attr') ?>">
                        </div>

                        <div class="col-md-6">
                            <label for="dias_antecipacao" class="form-label">
                                Dias mínimos de antecipação
                            </label>

                            <input type="number"
                                class="form-control"
                                id="dias_antecipacao"
                                name="dias_antecipacao"
                                min="1"
                                max="365"
                                step="1"
                                placeholder="Ex.: 5"
                                value="<?= esc(old(
                                            'dias_antecipacao',
                                            $contrato['dias_antecipacao'] ?? ''
                                        ), 'attr') ?>">
                        </div>

                        <div class="col-12">
                            <small class="text-muted">
                                O desconto será aplicado quando o pagamento ocorrer
                                com a antecedência mínima informada em relação
                                ao vencimento da cobrança.
                            </small>
                        </div>

                    </div>

                </div>
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="fw-semibold mb-2">Observações</h5>
                    <label for="observacoes" class="form-label">Descrição e informações complementares</label>
                    <textarea class="form-control" id="observacoes" name="observacoes" rows="4" placeholder="Condições particulares, observações internas, etc."><?= $contrato['observacoes'] ?? '' ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="<?= site_url('financeiro/contratos') ?>" class="btn btn-light border">Cancelar</a>
        <button type="submit"
            class="btn btn-primary"
            id="btnSalvarContrato">
            <i class="ri-save-line me-1"></i>
            Salvar Contrato
        </button>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const catalogo = <?= json_encode(array_map(static function ($servico) {
                                return [
                                    'id' => (int) $servico['id'],
                                    'nome' => (string) $servico['nome'],
                                    'valor' => (float) $servico['valor_padrao'],
                                ];
                            }, $servicos), JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

        const itensExistentes = <?= json_encode(
                                    $itensContrato,
                                    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
                                ) ?>;

        const tbody = document.getElementById('itensContrato');


        function atualizarTotal() {
            let total = 0;

            tbody.querySelectorAll('tr').forEach(function(linha) {
                const qtd = Number(
                    linha.querySelector('.quantidade').value
                ) || 0;

                const preco = disuValorNumerico(
                    linha.querySelector('.preco').value
                );

                const subtotal = Math.round(
                    (qtd * preco + Number.EPSILON) * 100
                ) / 100;

                linha.querySelector('.subtotal').textContent =
                    disuFormatarMoeda(subtotal);

                total += subtotal;
            });

            document.getElementById('totalContrato').textContent =
                disuFormatarMoeda(total);

            atualizarDecimoTerceiro();
        }



        const switchDecimo = document.getElementById('decimo_terceiro');
        const configuracaoDecimo = document.getElementById('configuracaoDecimoTerceiro');
        const secaoDecimo = document.getElementById('secaoDecimoTerceiro');
        const tipoContrato = document.getElementById('tipo');
        const campoMeses = document.getElementById('meses_decimo_terceiro');
        const resumoDecimo = document.getElementById('resumoDecimoTerceiro');

        const botoesMeses = Array.from(
            document.querySelectorAll('.mes-decimo')
        );

        const mesesSalvos = <?= json_encode(
                                old(
                                    'meses_decimo_terceiro',
                                    $contrato['meses_decimo_terceiro'] ?? ''
                                )
                            ) ?>;

        let mesesSelecionados = String(mesesSalvos || '')
            .split(',')
            .filter(mes => /^(?:[1-9]|1[0-2])$/.test(mes))
            .map(Number);

        const nomesMeses = [
            'Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun',
            'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'
        ];

        function atualizarDecimoTerceiro() {
            const mensal = tipoContrato.value === 'mensal';

            secaoDecimo.classList.toggle('d-none', !mensal);

            if (!mensal) {
                switchDecimo.checked = false;
            }

            configuracaoDecimo.classList.toggle(
                'd-none',
                !mensal || !switchDecimo.checked
            );

            botoesMeses.forEach(botao => {
                const mes = Number(botao.dataset.mes);
                const selecionado = mesesSelecionados.includes(mes);

                botao.classList.toggle('btn-primary', selecionado);
                botao.classList.toggle('btn-outline-primary', !selecionado);
                botao.setAttribute('aria-pressed', String(selecionado));
            });

            campoMeses.value = mensal && switchDecimo.checked ?
                mesesSelecionados.join(',') :
                '';

            const linhas = document.querySelectorAll('#itensContrato tr');

            let valorMensal = 0;

            linhas.forEach(linha => {
                const qtd = Number(linha.querySelector('.quantidade')?.value) || 0;
                const preco = disuValorNumerico(
                    linha.querySelector('.preco')?.value
                );

                valorMensal += qtd * preco;
            });

            const quantidade = mesesSelecionados.length;

            if (!quantidade) {
                resumoDecimo.textContent = 'Selecione os meses para calcular o 13º.';
                return;
            }

            const parcela = valorMensal / quantidade;

            const mesesTexto = mesesSelecionados
                .map(mes => nomesMeses[mes - 1])
                .join(', ');

            resumoDecimo.textContent =
                quantidade + ' parcela(s) de ' +
                disuFormatarMoeda(parcela) +
                ' (' + mesesTexto + '). Total do 13º: ' +
                disuFormatarMoeda(valorMensal) + '.';
        }

        document.querySelectorAll('[data-meses-preset]').forEach(botao => {
            botao.addEventListener('click', function() {
                const preset = this.dataset.mesesPreset;

                if (preset === 'dezembro') {
                    mesesSelecionados = [12];
                } else if (preset === 'novdez') {
                    mesesSelecionados = [11, 12];
                } else if (preset === 'trimestral') {
                    mesesSelecionados = [3, 6, 9, 12];
                } else if (preset === 'todos') {
                    mesesSelecionados = Array.from({
                            length: 12
                        },
                        (_, i) => i + 1
                    );
                }

                atualizarDecimoTerceiro();
            });
        });

        botoesMeses.forEach(botao => {
            botao.addEventListener('click', function() {
                const mes = Number(this.dataset.mes);

                if (mesesSelecionados.includes(mes)) {
                    mesesSelecionados = mesesSelecionados.filter(m => m !== mes);
                } else {
                    mesesSelecionados.push(mes);
                    mesesSelecionados.sort((a, b) => a - b);
                }

                atualizarDecimoTerceiro();
            });
        });

        switchDecimo.addEventListener('change', atualizarDecimoTerceiro);
        tipoContrato.addEventListener('change', atualizarDecimoTerceiro);

        document.getElementById('itensContrato').addEventListener(
            'input',
            atualizarDecimoTerceiro
        );

        document.getElementById('itensContrato').addEventListener(
            'change',
            atualizarDecimoTerceiro
        );

        document.getElementById('adicionarServico').addEventListener(
            'click',
            atualizarDecimoTerceiro
        );

        atualizarDecimoTerceiro();

        function adicionarLinha(item = null) {
            const linha = document.createElement('tr');
            const tdServico = document.createElement('td');
            const tdQtd = document.createElement('td');
            const tdPreco = document.createElement('td');
            const tdSubtotal = document.createElement('td');
            const tdAcoes = document.createElement('td');

            const select = document.createElement('select');
            select.className = 'form-select servico-id';
            select.name = 'servico_id[]';
            select.required = true;
            select.add(new Option('Selecione um serviço', ''));
            catalogo.forEach(s => select.add(new Option(s.nome, String(s.id))));
            tdServico.appendChild(select);

            const qtd = document.createElement('input');
            qtd.type = 'number';
            qtd.min = '1';
            qtd.step = '1';
            qtd.value = '1';
            qtd.name = 'quantidade[]';
            qtd.className = 'form-control quantidade';
            qtd.required = true;
            tdQtd.appendChild(qtd);


            const preco = document.createElement('input');
            preco.type = 'text';
            preco.inputMode = 'numeric';
            preco.value = '0,00';
            preco.name = 'valor_unitario[]';
            preco.className = 'form-control preco moeda-br';
            preco.required = true;
            tdPreco.appendChild(preco);


            tdSubtotal.className = 'subtotal text-nowrap fw-semibold';
            tdSubtotal.textContent = disuFormatarMoeda(0);

            const remover = document.createElement('button');
            remover.type = 'button';
            remover.className = 'btn btn-sm btn-light border text-danger';
            remover.title = 'Remover serviço';
            const icone = document.createElement('i');
            icone.className = 'ri-delete-bin-line';
            remover.appendChild(icone);
            tdAcoes.className = 'text-end';
            tdAcoes.appendChild(remover);

            [tdServico, tdQtd, tdPreco, tdSubtotal, tdAcoes].forEach(td => linha.appendChild(td));
            tbody.appendChild(linha);

            if (item) {
                select.value = String(item.servico_id);
                qtd.value = item.quantidade;
                preco.value = disuFormatarValor(item.valor_unitario);
            }

            select.addEventListener('change', function() {
                const escolhido = catalogo.find(s => s.id === Number(select.value));
                preco.value = escolhido ?
                    disuFormatarValor(escolhido.valor) :
                    '0,00';
                atualizarTotal();
            });
            [qtd, preco].forEach(campo => campo.addEventListener('input', atualizarTotal));
            remover.addEventListener('click', function() {
                linha.remove();
                atualizarTotal();
            });
            atualizarTotal();
            disuIniciarMascarasMoeda();
        }

        document.getElementById('adicionarServico').addEventListener('click', function() {
            adicionarLinha();
        });

        if (itensExistentes.length > 0) {
            itensExistentes.forEach(function(item) {
                adicionarLinha(item);
            });
        } else {
            adicionarLinha();
        }

        function vincularSwitch(idSwitch, idContainer) {
            const controle = document.getElementById(idSwitch);
            const container = document.getElementById(idContainer);

            function atualizar() {
                container.classList.toggle('d-none', !controle.checked);
                container.querySelectorAll('input, select').forEach(campo => {
                    campo.disabled = !controle.checked;
                });
            }
            controle.addEventListener('change', atualizar);
            atualizar();
        }


        const switchDesconto = document.getElementById('desconto_antecipacao');
        const tipoDesconto = document.getElementById('tipo_desconto');

        const campoDescontoPercentual = document.getElementById('campoDescontoPercentual');
        const campoDescontoFixo = document.getElementById('campoDescontoFixo');

        const percentualDesconto = document.getElementById('percentual_desconto');
        const valorDescontoFixo = document.getElementById('valor_desconto_fixo');
        const diasAntecipacao = document.getElementById('dias_antecipacao');

        function atualizarCamposDesconto() {
            const ativo = switchDesconto.checked;
            const percentual = tipoDesconto.value === 'percentual';

            campoDescontoPercentual.classList.toggle(
                'd-none',
                !percentual
            );

            campoDescontoFixo.classList.toggle(
                'd-none',
                percentual
            );

            percentualDesconto.disabled = !ativo || !percentual;
            percentualDesconto.required = ativo && percentual;

            valorDescontoFixo.disabled = !ativo || percentual;
            valorDescontoFixo.required = ativo && !percentual;

            diasAntecipacao.disabled = !ativo;
            diasAntecipacao.required = ativo;
        }

        tipoDesconto.addEventListener('change', atualizarCamposDesconto);
        switchDesconto.addEventListener('change', atualizarCamposDesconto);

        atualizarCamposDesconto();

        vincularSwitch('emitir_nfse', 'configuracaoNfse');
        vincularSwitch('reajuste_automatico', 'camposReajuste');
        vincularSwitch('desconto_antecipacao', 'camposDesconto');

        const indeterminado = document.getElementById('prazo_indeterminado');
        const termino = document.getElementById('data_termino');

        function atualizarTermino() {
            termino.disabled = indeterminado.checked;
            termino.required = !indeterminado.checked;
            if (indeterminado.checked) termino.value = '';
        }
        indeterminado.addEventListener('change', atualizarTermino);
        atualizarTermino();

        const formContrato = document.getElementById('formContrato');
        const btnSalvarContrato = document.getElementById('btnSalvarContrato');

        function localizarCampoInvalido() {
            return Array.from(formContrato.elements).find(function(campo) {
                return campo.willValidate && !campo.validity.valid;
            });
        }

        function focarCampo(campo) {
            const tomSelect = campo.tomselect;

            if (tomSelect) {
                tomSelect.focus();
                tomSelect.wrapper.classList.add('campo-invalido');
            } else {
                campo.classList.add('campo-invalido');
                campo.focus({
                    preventScroll: true
                });
            }

            const elementoVisual = tomSelect ?
                tomSelect.wrapper :
                campo;

            elementoVisual.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });

            // Exibe a mensagem nativa de validação.
            if (!tomSelect) {
                campo.reportValidity();
            }
        }

        function mostrarCampoInvalido() {
            const campo = localizarCampoInvalido();

            if (!campo) return false;

            const aba = campo.closest('.tab-pane');

            if (!aba || aba.classList.contains('active')) {
                focarCampo(campo);
                return true;
            }

            const botaoAba = document.querySelector(
                '[data-bs-toggle="tab"][data-bs-target="#' + aba.id + '"]'
            );

            if (!botaoAba) {
                focarCampo(campo);
                return true;
            }

            botaoAba.addEventListener('shown.bs.tab', function() {
                focarCampo(campo);
            }, {
                once: true
            });

            bootstrap.Tab.getOrCreateInstance(botaoAba).show();

            return true;
        }

        // Verifica antes de o navegador tentar enviar.
        btnSalvarContrato.addEventListener('click', function(evento) {
            if (mostrarCampoInvalido()) {
                evento.preventDefault();
            }
        });

        // Impede que a validação nativa tente focar
        // um campo ainda escondido em outra aba.
        formContrato.addEventListener('invalid', function(evento) {
            evento.preventDefault();
        }, true);

        // Remove o destaque quando o campo é corrigido.
        formContrato.addEventListener('input', function(evento) {
            const campo = evento.target;

            if (campo.validity && campo.validity.valid) {
                campo.classList.remove('campo-invalido');

                if (campo.tomselect) {
                    campo.tomselect.wrapper.classList.remove('campo-invalido');
                }
            }
        });


        const tipoReajuste = document.getElementById('tipo_reajuste');
        const campoPercentualFixo = document.getElementById('campoPercentualFixo');
        const percentualReajuste = document.getElementById('percentual_reajuste');
        const aplicacaoReajuste = document.getElementById('aplicacao_reajuste');
        const descricaoReajuste = document.getElementById('descricaoReajuste');
        const switchReajuste = document.getElementById('reajuste_automatico');

        function atualizarCamposReajuste() {
            const ativo = switchReajuste.checked;
            const fixo = tipoReajuste.value === 'fixo';

            campoPercentualFixo.classList.toggle('d-none', !fixo);
            percentualReajuste.disabled = !ativo || !fixo;
            percentualReajuste.required = ativo && fixo;

            const dataProximoReajuste = document.getElementById('data_proximo_reajuste');
            dataProximoReajuste.required = ativo;

            if (aplicacaoReajuste.value === 'manual') {
                descricaoReajuste.textContent =
                    'O sistema avisará quando chegar a data do reajuste. ' +
                    'A aplicação dependerá da confirmação do usuário.';
            } else {
                descricaoReajuste.textContent =
                    'O reajuste automático será disponibilizado ' +
                    'quando implementarmos o processamento das cobranças.';
            }
        }

        tipoReajuste.addEventListener('change', atualizarCamposReajuste);
        aplicacaoReajuste.addEventListener('change', atualizarCamposReajuste);
        switchReajuste.addEventListener('change', atualizarCamposReajuste);

        atualizarCamposReajuste();

    });
</script>
<?= $this->endSection() ?>
