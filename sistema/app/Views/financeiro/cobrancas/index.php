<?php

/**
 * @var array $clientes
 * @var array $cobrancas
 * @var \CodeIgniter\Pager\Pager $pager
 */

use Config\Pager;

?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$f = $filtros ?? [];

$periodo = $f['periodo'] ?? 'mes';
$mes = (int) ($f['mes'] ?? date('m'));
$ano = (int) ($f['ano'] ?? date('Y'));
$status = $f['status'] ?? '';
$clienteId = (int) ($f['cliente_id'] ?? 0);
$busca = $f['busca'] ?? '';

$meses = [
    1 => 'Janeiro',
    2 => 'Fevereiro',
    3 => 'Março',
    4 => 'Abril',
    5 => 'Maio',
    6 => 'Junho',
    7 => 'Julho',
    8 => 'Agosto',
    9 => 'Setembro',
    10 => 'Outubro',
    11 => 'Novembro',
    12 => 'Dezembro'
];

$anterior = new DateTimeImmutable(sprintf('%04d-%02d-01', $ano, $mes));
$proximo = $anterior->modify('+1 month');
$anterior = $anterior->modify('-1 month');

$statuses = [
    'em_aberto' => 'Em aberto',
    'pago' => 'Pago',
    'recebido' => 'Recebido',
    'vencido' => 'Vencido',
    'a_receber' => 'A receber',
    'cancelado' => 'Cancelado',
    'expirado' => 'Expirado',
    'processando' => 'Processando',
    'erro' => 'Erro',
];

$cores = [
    'em_aberto' => 'warning',
    'pago' => 'success',
    'recebido' => 'success',
    'vencido' => 'danger',
    'a_receber' => 'info',
    'cancelado' => 'secondary',
    'expirado' => 'secondary',
    'processando' => 'primary',
    'erro' => 'danger',
];

$baseUrl = site_url('financeiro/cobrancas');

$query = static function (array $alteracoes = []) use ($f) {
    return http_build_query(array_merge($f, $alteracoes));
};
?>

<div class="container-fluid">

    <!-- CABEÇALHO -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="mb-1">Contas a Receber</h4>
            <p class="text-muted mb-0">
                Acompanhe cobranças, vencimentos e recebimentos.
            </p>
        </div>

        <a href="<?= site_url('financeiro/cobrancas/novo') ?>"
            class="btn btn-primary">
            <i class="ri-add-line me-1"></i>
            Nova Cobrança
        </a>
    </div>

    <!-- INDICADORES -->
    <div class="row g-3 mb-4">

        <?php
        $cards = [
            [
                'titulo' => 'Cobranças no período',
                'valor' => number_format($indicadores['quantidade'] ?? 0, 0, ',', '.'),
                'icone' => 'ri-file-list-3-line',
                'cor' => 'primary',
                'status' => ''
            ],
            [
                'titulo' => 'Total em valor',
                'valor' => 'R$ ' . number_format($indicadores['total'] ?? 0, 2, ',', '.'),
                'icone' => 'ri-money-dollar-circle-line',
                'cor' => 'primary',
                'status' => ''
            ],
            [
                'titulo' => 'Em aberto / a receber',
                'valor' => 'R$ ' . number_format($indicadores['aberto'] ?? 0, 2, ',', '.'),
                'icone' => 'ri-time-line',
                'cor' => 'warning',
                'status' => 'em_aberto'
            ],
            [
                'titulo' => 'Recebidos',
                'valor' => 'R$ ' . number_format($indicadores['recebido'] ?? 0, 2, ',', '.'),
                'icone' => 'ri-checkbox-circle-line',
                'cor' => 'success',
                'status' => 'recebido'
            ],
        ];
        ?>

        <?php foreach ($cards as $card): ?>
            <div class="col-xl-3 col-md-6">
                <a href="<?= esc($baseUrl . '?' . $query(['status' => $card['status']]), 'attr') ?>"
                    class="text-decoration-none">

                    <div class="card h-100 mb-0">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted mb-2">
                                        <?= esc($card['titulo']) ?>
                                    </p>

                                    <h4 class="mb-0 text-body-emphasis">
                                        <?= esc($card['valor']) ?>
                                    </h4>
                                </div>

                                <span class="text-<?= esc($card['cor']) ?> fs-3">
                                    <i class="<?= esc($card['icone']) ?>"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>

    </div>

    <!-- FILTROS -->
    <div class="card mb-4">
        <div class="card-body">

            <form method="get" action="<?= $baseUrl ?>" id="formFiltros">

                <div class="row g-3 align-items-end">

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Período</label>

                        <select name="periodo"
                            id="tipoPeriodo"
                            class="form-select">
                            <option value="todos" <?= $periodo === 'todos' ? 'selected' : '' ?>>
                                Todos
                            </option>
                            <option value="mes" <?= $periodo === 'mes' ? 'selected' : '' ?>>
                                Mês
                            </option>
                            <option value="periodo" <?= $periodo === 'periodo' ? 'selected' : '' ?>>
                                Período personalizado
                            </option>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6" id="filtroMes">
                        <label class="form-label">Mês de referência</label>

                        <div class="input-group">
                            <a class="btn btn-outline-secondary"
                                href="<?= esc($baseUrl . '?' . $query([
                                            'periodo' => 'mes',
                                            'mes' => $anterior->format('n'),
                                            'ano' => $anterior->format('Y')
                                        ]), 'attr') ?>">
                                <i class="ri-arrow-left-s-line"></i>
                            </a>

                            <input type="text"
                                class="form-control text-center"
                                value="<?= esc($meses[$mes] . ' / ' . $ano) ?>"
                                readonly>

                            <a class="btn btn-outline-secondary"
                                href="<?= esc($baseUrl . '?' . $query([
                                            'periodo' => 'mes',
                                            'mes' => $proximo->format('n'),
                                            'ano' => $proximo->format('Y')
                                        ]), 'attr') ?>">
                                <i class="ri-arrow-right-s-line"></i>
                            </a>
                        </div>

                        <input type="hidden" name="mes" value="<?= $mes ?>">
                        <input type="hidden" name="ano" value="<?= $ano ?>">
                    </div>

                    <div class="col-lg-3 col-md-6 filtroDatas">
                        <label class="form-label">Data inicial</label>
                        <input type="date"
                            name="data_inicio"
                            class="form-control"
                            value="<?= esc($f['data_inicio'] ?? '') ?>">
                    </div>

                    <div class="col-lg-3 col-md-6 filtroDatas">
                        <label class="form-label">Data final</label>
                        <input type="date"
                            name="data_fim"
                            class="form-control"
                            value="<?= esc($f['data_fim'] ?? '') ?>">
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Status do boleto</label>

                        <select name="status" class="form-select">
                            <option value="">Todos</option>

                            <?php foreach ($statuses as $chave => $nome): ?>
                                <option value="<?= esc($chave) ?>"
                                    <?= $status === $chave ? 'selected' : '' ?>>
                                    <?= esc($nome) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <label class="form-label">Cliente</label>

                        <select name="cliente_id"
                            id="filtroCliente"
                            class="form-select">
                            <option value="">Todos os clientes</option>

                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= (int) $cliente['id'] ?>"
                                    <?= $clienteId === (int) $cliente['id'] ? 'selected' : '' ?>>
                                    <?= esc($cliente['nome']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <label class="form-label">Pesquisar</label>
                        <input type="search"
                            name="busca"
                            class="form-control"
                            placeholder="Descrição, empresa ou contrato..."
                            value="<?= esc($busca) ?>">
                    </div>

                    <div class="col-lg-2 col-md-6">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-search-line"></i>
                                Filtrar
                            </button>

                            <a href="<?= $baseUrl ?>"
                                class="btn btn-light border"
                                title="Limpar filtros">
                                <i class="ri-refresh-line"></i>
                            </a>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- LISTAGEM -->
    <div class="card">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Cobranças</h5>
                <span class="text-muted small">
                    <?= count($cobrancas) ?> registros nesta página
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Empresa</th>
                            <th>Origem</th>
                            <th>Valor</th>
                            <th>Vencimento</th>
                            <th>Status</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (empty($cobrancas)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="ri-inbox-line fs-1 d-block mb-2"></i>
                                    Nenhuma cobrança encontrada.
                                </td>
                            </tr>
                        <?php else: ?>

                            <?php foreach ($cobrancas as $cobranca): ?>
                                <?php
                                $situacao = $cobranca['status'] ?? 'em_aberto';
                                $cor = $cores[$situacao] ?? 'secondary';
                                ?>

                                <tr>
                                    <td>
                                        <strong>
                                            <?= esc($cobranca['descricao'] ?: 'Cobrança #' . $cobranca['id']) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <?= esc($cobranca['empresa_nome'] ?? '-') ?>
                                    </td>

                                    <td>
                                        <?= ($cobranca['origem'] ?? '') === 'contrato'
                                            ? 'Contrato'
                                            : 'Avulsa' ?>
                                    </td>

                                    <td>
                                        R$ <?= number_format((float) $cobranca['valor'], 2, ',', '.') ?>
                                    </td>

                                    <td>
                                        <?= date('d/m/Y', strtotime($cobranca['vencimento'])) ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-<?= esc($cor) ?>-subtle text-<?= esc($cor) ?>">
                                            <?= esc($statuses[$situacao] ?? ucfirst($situacao)) ?>
                                        </span>
                                    </td>

                                    <td class="text-end">

                                        <!-- VISUALIZAR COBRANÇA -->
                                        <a href="<?= site_url('financeiro/cobrancas/editar/' . $cobranca['id']) ?>"
                                            class="btn btn-sm btn-light border"
                                            title="Visualizar cobrança">
                                            <i class="ri-eye-line"></i>
                                        </a>

                                        <!-- EXCLUIR (FUTURAMENTE) -->
                                        <button type="button"
                                            class="btn btn-sm btn-light border"
                                            title="Exclusão disponível na próxima etapa"
                                            disabled>
                                            <i class="ri-delete-bin-line"></i>
                                        </button>

                                    </td>
                                </tr>
                            <?php endforeach; ?>

                        <?php endif; ?>

                    </tbody>
                </table>
            </div>

            <?php if ($pager): ?>
                <div class="mt-3">
                    <?= $pager->links('cobrancas', 'default_full') ?>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const tipoPeriodo = document.getElementById('tipoPeriodo');
        const filtroMes = document.getElementById('filtroMes');
        const filtroDatas = document.querySelectorAll('.filtroDatas');

        function atualizarPeriodo() {
            const tipo = tipoPeriodo.value;

            filtroMes.style.display = tipo === 'mes' ? '' : 'none';

            filtroDatas.forEach(function(elemento) {
                elemento.style.display = tipo === 'periodo' ? '' : 'none';
            });
        }

        tipoPeriodo.addEventListener('change', atualizarPeriodo);
        atualizarPeriodo();

        // Usa Select2 se já estiver carregado no template.
        if (window.jQuery && jQuery.fn.select2) {
            jQuery('#filtroCliente').select2({
                width: '100%',
                placeholder: 'Pesquisar cliente',
                allowClear: true
            });
        }

    });
</script>

<?= $this->endSection() ?>
