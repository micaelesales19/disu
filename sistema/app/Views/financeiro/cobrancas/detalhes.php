<?php

/**
 * @var array $cobranca
 */
?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$status = $cobranca['status'] ?? 'em_aberto';

$pagamentoConfirmado = in_array(
    $status,
    ['pago', 'recebido'],
    true
) || !empty($cobranca['pago_em']);

$cobrancaFinalizada = $pagamentoConfirmado
    || $status === 'cancelado';

$possuiBoleto = !empty($cobranca['boleto_id'])
    || !empty($cobranca['boleto_linha_digitavel']);

$possuiPix = !empty($cobranca['pix_copia_cola']);

$podeGerarBoleto = !$cobrancaFinalizada && !$possuiBoleto;
$podeGerarPix = !$cobrancaFinalizada && !$possuiPix;

$statusLabels = [
    'em_aberto'   => 'Em aberto',
    'a_receber'   => 'A receber',
    'vencido'     => 'Vencido',
    'pago'        => 'Pago',
    'recebido'    => 'Recebido',
    'cancelado'   => 'Cancelado',
    'expirado'    => 'Expirado',
    'processando' => 'Processando',
    'erro'        => 'Erro',
];

$statusCores = [
    'em_aberto'   => 'warning',
    'a_receber'   => 'info',
    'vencido'     => 'danger',
    'pago'        => 'success',
    'recebido'    => 'success',
    'cancelado'   => 'secondary',
    'expirado'    => 'secondary',
    'processando' => 'primary',
    'erro'        => 'danger',
];

$corStatus = $statusCores[$status] ?? 'secondary';
$nomeStatus = $statusLabels[$status] ?? ucfirst($status);

$vencimento = !empty($cobranca['vencimento'])
    ? date('d/m/Y', strtotime($cobranca['vencimento']))
    : '-';

$valor = number_format(
    (float) ($cobranca['valor'] ?? 0),
    2,
    ',',
    '.'
);

$empresa = $cobranca['empresa_nome'] ?? 'Empresa não encontrada';
$contrato = $cobranca['contrato_titulo'] ?? '';
?>

<div class="container-fluid">

    <!-- CABEÇALHO -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mt-2 mb-1">
                Cobrança #<?= (int) $cobranca['id'] ?>
            </h4>

            <p class="text-muted mb-0">
                Detalhes e gerenciamento da cobrança.
            </p>
        </div>

        <div>
            <span class="badge bg-<?= esc($corStatus) ?>-subtle text-<?= esc($corStatus) ?> fs-6">
                <?= esc($nomeStatus) ?>
            </span>

            <a href="<?= site_url('financeiro/cobrancas') ?>"
                class="btn btn-light border">
                <i class="ri-arrow-left-line me-1"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- RESUMO DA COBRANÇA -->
    <div class="card mb-4">
        <div class="card-body">

            <div class="row g-4 align-items-start">

                <div class="col-lg-8">

                    <p class="text-muted small text-uppercase mb-2">
                        Cobrança
                    </p>

                    <h4 class="mb-3">
                        <?= esc($cobranca['descricao'] ?? 'Cobrança') ?>
                    </h4>

                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="badge bg-primary-subtle text-primary border">
                            <i class="ri-building-line me-1"></i>
                            <?= esc($empresa) ?>
                        </span>

                        <?php if ($contrato !== ''): ?>
                            <span class="badge bg-info-subtle text-info border">
                                <i class="ri-file-text-line me-1"></i>
                                <?= esc($contrato) ?>
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-secondary border">
                                Cobrança avulsa
                            </span>
                        <?php endif; ?>
                    </div>

                </div>

                <div class="col-lg-4 text-lg-end">

                    <p class="text-muted small text-uppercase mb-2">
                        Valor
                    </p>

                    <h2 class="text-primary mb-0">
                        R$ <?= $valor ?>
                    </h2>

                </div>

            </div>

            <hr class="my-4">

            <div class="row g-3">

                <div class="col-md-4">
                    <p class="text-muted small mb-1">
                        Descrição / Observações
                    </p>

                    <div class="fw-medium">
                        <?= nl2br(esc($cobranca['descricao'] ?? '-')) ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <p class="text-muted small mb-1">
                        Vencimento
                    </p>

                    <div class="d-flex align-items-center gap-2">
                        <span class="fw-medium">
                            <?= esc($vencimento) ?>
                        </span>

                        <?php
                        $podeAlterarVencimento =
                            !$cobrancaFinalizada
                            && in_array($status, ['em_aberto', 'a_receber', 'vencido'], true)
                            && !$possuiBoleto
                            && !$possuiPix;
                        ?>

                        <button type="button"
                            class="btn btn-sm btn-light border"
                            title="<?= $podeAlterarVencimento ? 'Alterar vencimento' : 'Vencimento bloqueado' ?>"
                            data-bs-toggle="modal"
                            data-bs-target="#modalAlterarVencimento"
                            <?= $podeAlterarVencimento ? '' : 'disabled' ?>>
                            <i class="ri-pencil-line"></i>
                        </button>
                    </div>
                </div>

                <div class="col-md-4">
                    <p class="text-muted small mb-1">
                        Status
                    </p>

                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-<?= esc($corStatus) ?>-subtle text-<?= esc($corStatus) ?>">
                            <?= esc($nomeStatus) ?>
                        </span>
                        <?php
                        $podeAlterarStatus = !in_array(
                            $status,
                            ['pago', 'recebido'],
                            true
                        ) && empty($cobranca['pago_em'])
                            && empty($cobranca['boleto_id'])
                            && empty($cobranca['boleto_linha_digitavel'])
                            && empty($cobranca['pix_copia_cola']);
                        ?>

                        <button type="button"
                            class="btn btn-sm btn-light border"
                            title="Alterar status da cobrança"
                            data-bs-toggle="modal"
                            data-bs-target="#modalAlterarStatus"
                            <?= $podeAlterarStatus ? '' : 'disabled' ?>>
                            <i class="ri-pencil-line"></i>
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>


    <!-- INFORMAÇÕES DO PAGAMENTO -->
    <?php if (in_array($status, ['pago', 'recebido'], true)): ?>

        <?php
        $dataPagamento = !empty($cobranca['pago_em'])
            ? date('d/m/Y', strtotime($cobranca['pago_em']))
            : '-';

        $valorPago = number_format(
            (float) ($cobranca['valor_pago'] ?? 0),
            2,
            ',',
            '.'
        );

        $observacoes = (string) ($cobranca['observacoes'] ?? '');

        $formas = [
            'pix'           => 'PIX',
            'transferencia' => 'Transferência bancária',
            'dinheiro'      => 'Dinheiro',
            'outro'         => 'Outro',
        ];

        $formaCodigo = $cobranca['forma_pagamento'] ?? null;
        $origemCodigo = $cobranca['origem_pagamento'] ?? null;

        $formaPagamento = $formas[$formaCodigo ?? ''] ?? '-';

        $origens = [
            'manual'      => 'Baixa manual',
            'banco_inter' => 'Banco Inter',
        ];

        $origemPagamento = $origens[$origemCodigo ?? '']
            ?? 'Não informada';

        $observacaoAdicional = $observacoes;

        // Compatibilidade com baixas antigas,
        if (
            empty($formaCodigo)
            && empty($origemCodigo)
            && str_starts_with($observacoes, 'Baixa manual | Forma: ')
        ) {
            $partes = explode(' | ', $observacoes, 3);

            $formaAntiga = str_replace(
                'Forma: ',
                '',
                $partes[1] ?? ''
            );

            $formaPagamento = $formas[$formaAntiga] ?? '-';
            $origemPagamento = 'Baixa manual';
            $observacaoAdicional = $partes[2] ?? '';
        }
        ?>

        <div class="card mb-4">

            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="ri-checkbox-circle-line me-2 text-success"></i>
                    Informações do pagamento
                </h5>

                <span class="badge bg-success-subtle text-success">
                    Recebido
                </span>
            </div>

            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-4">
                        <p class="text-muted small mb-1">
                            Data do pagamento
                        </p>

                        <div class="fw-medium">
                            <?= esc($dataPagamento) ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <p class="text-muted small mb-1">
                            Valor recebido
                        </p>

                        <div class="fw-semibold text-success fs-5">
                            R$ <?= $valorPago ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <p class="text-muted small mb-1">
                            Forma de pagamento
                        </p>

                        <div class="fw-medium">
                            <?= esc($formaPagamento) ?>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <p class="text-muted small mb-1">
                            Origem do pagamento
                        </p>

                        <span class="badge bg-info-subtle text-info">
                            <?= esc($origemPagamento) ?>
                        </span>
                    </div>

                    <div class="col-md-8">
                        <p class="text-muted small mb-1">
                            Observações
                        </p>

                        <div class="fw-medium">
                            <?= $observacaoAdicional !== ''
                                ? nl2br(esc($observacaoAdicional))
                                : '<span class="text-muted">Nenhuma observação</span>' ?>
                        </div>
                    </div>

                </div>

            </div>
        </div>

    <?php endif; ?>

    <!-- DADOS DO BOLETO -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="ri-file-list-3-line me-2"></i>
                Dados do boleto / link
            </h5>
        </div>

        <div class="card-body">

            <?php
            $camposBoleto = [
                'boleto_linha_digitavel' => 'Linha digitável',
                'boleto_codigo_barras'   => 'Código de barras',
                'link_pagamento'         => 'Link de pagamento',
            ];
            ?>

            <?php foreach ($camposBoleto as $campo => $label): ?>
                <div class="mb-3">
                    <label class="form-label">
                        <?= esc($label) ?>
                    </label>

                    <div class="input-group">
                        <input type="text"
                            class="form-control"
                            value="<?= esc($cobranca[$campo] ?? '', 'attr') ?>"
                            readonly
                            placeholder="Ainda não disponível">

                        <button type="button"
                            class="btn btn-light border"
                            title="Copiar"
                            onclick="navigator.clipboard.writeText(this.previousElementSibling.value)"
                            <?= empty($cobranca[$campo]) ? 'disabled' : '' ?>>
                            <i class="ri-file-copy-line"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="alert alert-info mb-0">
                <i class="ri-information-line me-1"></i>
                Os dados bancários aparecerão após a integração com o Banco Inter.
            </div>

        </div>
    </div>

    <!-- PIX -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="ri-qr-code-line me-2"></i>
                PIX
            </h5>
        </div>

        <div class="card-body">

            <label class="form-label">
                Código PIX copia e cola
            </label>

            <div class="input-group">
                <input type="text"
                    class="form-control"
                    value="<?= esc($cobranca['pix_copia_cola'] ?? '', 'attr') ?>"
                    readonly
                    placeholder="PIX ainda não gerado">

                <button type="button"
                    class="btn btn-light border"
                    title="Copiar PIX"
                    onclick="navigator.clipboard.writeText(this.previousElementSibling.value)"
                    <?= empty($cobranca['pix_copia_cola']) ? 'disabled' : '' ?>>
                    <i class="ri-file-copy-line"></i>
                </button>
            </div>

        </div>
    </div>

    <!-- AÇÕES DE PAGAMENTO -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="ri-bank-card-line me-2"></i>
                Ações de pagamento
            </h5>
        </div>

        <div class="card-body">

            <p class="text-muted small">
                Integração bancária: Banco Inter
            </p>

            <div class="row g-2">

                <div class="col-md-6">
                    <button type="button"
                        class="btn btn-primary w-100"
                        title="<?= $pagamentoConfirmado
                                    ? 'Cobrança já recebida'
                                    : 'Disponível após integração com Banco Inter' ?>"
                        disabled>
                        <i class="ri-file-add-line me-1"></i>
                        Gerar boleto
                    </button>
                </div>

                <div class="col-md-6">
                    <button type="button"
                        class="btn btn-primary w-100"
                        title="<?= $pagamentoConfirmado
                                    ? 'Cobrança já recebida'
                                    : 'Disponível após integração com Banco Inter' ?>"
                        disabled>
                        <i class="ri-qr-code-line me-1"></i>
                        Gerar PIX
                    </button>
                </div>

                <div class="col-md-6">
                    <button class="btn btn-outline-primary w-100" disabled>
                        <i class="ri-file-pdf-line me-1"></i>
                        Baixar boleto PDF
                    </button>
                </div>

                <div class="col-md-6">
                    <button class="btn btn-outline-primary w-100" disabled>
                        <i class="ri-refresh-line me-1"></i>
                        Consultar pagamento
                    </button>
                </div>

                <div class="col-md-6">
                    <button class="btn btn-outline-primary w-100" disabled>
                        <i class="ri-file-text-line me-1"></i>
                        Gerar NFS-e
                    </button>
                </div>

                <div class="col-md-6">
                    <button class="btn btn-success w-100" disabled>
                        <i class="ri-whatsapp-line me-1"></i>
                        Enviar pelo WhatsApp
                    </button>
                </div>

            </div>

        </div>
    </div>

</div>


<!-- MODAL ALTERAR VENCIMENTO -->
<div class="modal fade"
    id="modalAlterarVencimento"
    tabindex="-1"
    aria-labelledby="tituloModalVencimento"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form method="POST"
                action="<?= site_url('financeiro/cobrancas/alterar-vencimento/' . $cobranca['id']) ?>">

                <?= csrf_field() ?>

                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalVencimento">
                        Alterar vencimento
                    </h5>

                    <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>

                <div class="modal-body">
                    <p class="text-muted small">
                        Informe a nova data de vencimento da cobrança.
                    </p>

                    <label for="novoVencimento" class="form-label">
                        Novo vencimento *
                    </label>

                    <input type="date"
                        id="novoVencimento"
                        name="vencimento"
                        class="form-control"
                        value="<?= esc($cobranca['vencimento'], 'attr') ?>"
                        required>
                </div>

                <div class="modal-footer">
                    <button type="button"
                        class="btn btn-light border"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>
                        Salvar vencimento
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<!-- MODAL ALTERAR STATUS -->
<div class="modal fade" id="modalAlterarStatus" tabindex="-1"
    aria-labelledby="tituloModalStatus" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form method="POST"
                action="<?= site_url('financeiro/cobrancas/alterar-status/' . $cobranca['id']) ?>">

                <?= csrf_field() ?>

                <div class="modal-header">
                    <h5 class="modal-title" id="tituloModalStatus">
                        Alterar status da cobrança
                    </h5>

                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Fechar"></button>
                </div>

                <div class="modal-body">

                    <p class="text-muted small">
                        Baixa manual para pagamentos recebidos por fora,
                        sem retorno automático do Banco Inter.
                    </p>

                    <div class="mb-3">
                        <label for="novoStatus" class="form-label">
                            Novo status *
                        </label>

                        <select name="status" id="novoStatus"
                            class="form-select" required>
                            <option value="em_aberto" <?= $status === 'em_aberto' ? 'selected' : '' ?>>
                                Em aberto
                            </option>
                            <option value="recebido" <?= in_array($status, ['pago', 'recebido'], true) ? 'selected' : '' ?>>
                                Pago / Recebido
                            </option>
                            <option value="vencido" <?= $status === 'vencido' ? 'selected' : '' ?>>
                                Vencido
                            </option>
                            <option value="cancelado" <?= $status === 'cancelado' ? 'selected' : '' ?>>
                                Cancelado
                            </option>
                        </select>
                    </div>

                    <div id="dadosPagamentoManual" class="d-none">

                        <hr>

                        <h6>Dados do recebimento</h6>

                        <div class="mb-3">
                            <label class="form-label">
                                Data do pagamento *
                            </label>

                            <input type="date"
                                name="pago_em"
                                id="dataPagamentoManual"
                                class="form-control"
                                value="<?= date('Y-m-d') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Valor recebido
                            </label>

                            <input type="text"
                                class="form-control"
                                value="R$ <?= $valor ?>"
                                readonly>

                            <small class="text-muted">
                                Somente baixa integral.
                            </small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Forma de pagamento *
                            </label>

                            <select name="forma_pagamento"
                                id="formaPagamentoManual"
                                class="form-select">
                                <option value="">Selecione...</option>
                                <option value="pix">PIX</option>
                                <option value="transferencia">Transferência bancária</option>
                                <option value="dinheiro">Dinheiro</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">
                                Observações
                            </label>

                            <textarea name="observacoes"
                                class="form-control"
                                rows="2"
                                maxlength="1000"
                                placeholder="Ex.: Pagamento confirmado pelo escritório"></textarea>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>
                        Salvar alteração
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const campoStatus = document.getElementById('novoStatus');
        const dadosPagamento = document.getElementById('dadosPagamentoManual');
        const campoData = document.getElementById('dataPagamentoManual');
        const campoForma = document.getElementById('formaPagamentoManual');

        function atualizarCampos() {
            const recebido = campoStatus.value === 'recebido';

            dadosPagamento.classList.toggle('d-none', !recebido);
            campoData.required = recebido;
            campoForma.required = recebido;
        }

        campoStatus.addEventListener('change', atualizarCampos);
        atualizarCampos();
    });
</script>

<?= $this->endSection() ?>
