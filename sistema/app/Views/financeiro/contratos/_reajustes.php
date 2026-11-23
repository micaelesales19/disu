<?php

/**
 * @var array $resumoReajustes
 * @var array $reajustes
 */
?>
<div class="card mt-4" id="secaoReajustes">
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-1">
                    <i class="ri-line-chart-line text-primary me-2"></i>
                    Reajustes de contrato
                </h5>

                <small class="text-muted">
                    <?= (int) $resumoReajustes['vencidos'] ?> vencido(s) ·
                    <?= (int) $resumoReajustes['a_vencer'] ?> a vencer ·
                    <?= (int) $resumoReajustes['em_dia'] ?> em dia ·
                    <?= (int) $resumoReajustes['sem_reajuste'] ?> sem reajuste
                </small>
            </div>

            <div class="d-flex gap-2 flex-wrap" id="filtrosReajuste">
                <?php
                $opcoes = [
                    'todos' => 'Todos',
                    'vencidos' => 'Vencidos',
                    'a_vencer' => 'A vencer (30 dias)',
                    'em_dia' => 'Em dia',
                    'sem_reajuste' => 'Sem reajuste',
                ];
                ?>

                <?php foreach ($opcoes as $chave => $nome): ?>
                    <button type="button"
                        class="btn btn-sm <?= $chave === 'todos' ? 'btn-primary' : 'btn-outline-primary' ?> filtro-reajuste"
                        data-filtro="<?= esc($chave, 'attr') ?>">
                        <?= esc($nome) ?>
                        (<?= (int) $resumoReajustes[$chave] ?>)
                    </button>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Contrato</th>
                        <th>Empresa</th>
                        <th>Valor atual</th>
                        <th>%</th>
                        <th>Próximo</th>
                        <th>Modo</th>
                        <th>Situação</th>
                        <th>Se aplicar</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($reajustes as $item): ?>
                        <?php
                        $status = $item['situacao_reajuste'];

                        $labels = [
                            'vencidos' => ['Vencido', 'danger'],
                            'a_vencer' => ['A vencer', 'warning'],
                            'em_dia' => ['Em dia', 'success'],
                            'sem_reajuste' => ['Sem reajuste', 'secondary'],
                        ];

                        [$label, $cor] = $labels[$status];

                        $habilitado = $status !== 'sem_reajuste';
                        ?>

                        <tr class="linha-reajuste"
                            data-status="<?= esc($status, 'attr') ?>"
                            data-url="<?= site_url('financeiro/contratos/editar/' . (int) $item['id']) ?>"
                            tabindex="0"
                            role="link"
                            aria-label="Editar contrato <?= esc($item['titulo'], 'attr') ?>">


                            <td>
                                <?= esc($item['titulo']) ?>
                            </td>

                            <td><?= esc($item['empresa_nome']) ?></td>

                            <td class="text-nowrap">
                                R$ <?= number_format((float) $item['valor'], 2, ',', '.') ?>
                            </td>

                            <td>
                                <?= $habilitado && $item['tipo_reajuste'] === 'fixo'
                                    ? esc((string) $item['percentual_reajuste']) . '%'
                                    : '—' ?>
                            </td>

                            <td class="text-nowrap">
                                <?= $habilitado
                                    ? date('d/m/Y', strtotime($item['data_proximo_reajuste']))
                                    : '—' ?>
                            </td>

                            <td>
                                <?= $habilitado
                                    ? ($item['aplicacao_reajuste'] === 'automatico' ? 'Automático' : 'Manual')
                                    : '—' ?>
                            </td>

                            <td>
                                <span class="badge bg-<?= $cor ?>-subtle text-<?= $cor ?>">
                                    <?= esc($label) ?>
                                </span>
                            </td>

                            <td class="text-nowrap">
                                <?= $item['valor_reajustado'] !== null
                                    ? 'R$ ' . number_format($item['valor_reajustado'], 2, ',', '.')
                                    : '—' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div id="semReajustes" class="text-center text-muted py-4 d-none">
            Nenhum contrato encontrado para este filtro.
        </div>

        <!-- PAGINAÇÃO DOS REAJUSTES -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3">

            <small class="text-muted" id="totalReajustesEncontrados">
                0 contrato(s) encontrado(s)
            </small>

            <nav aria-label="Paginação dos reajustes">
                <ul class="pagination pagination-sm mb-0"
                    id="paginacaoReajustes">
                </ul>
            </nav>

        </div>

    </div>
</div>
