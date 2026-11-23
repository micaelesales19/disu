<?php

/**
 * @var array $empresas
 * @var int $total
 * @var \CodeIgniter\Pager\Pager $pager
 */
?>

<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="main-breadcrumb d-flex align-items-center justify-content-between my-3">

    <h2 class="breadcrumb-title mb-0 fs-18">
        <i class="ri-delete-bin-line me-2"></i>
        Lixeira de EmpresasF
    </h2>

    <a href="<?= site_url('clientes/empresas') ?>"
        class="btn btn-light border">
        <i class="ri-arrow-left-line me-1"></i>
        Voltar
    </a>

</div>

<div class="card">
    <div class="card-body">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h5 class="mb-1">Empresas excluídas</h5>
                <p class="text-muted mb-0">
                    Restaure empresas removidas da listagem.
                </p>
            </div>

            <span class="badge bg-danger-subtle text-danger fs-13">
                <?= (int) $total ?> na lixeira
            </span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">

                <thead>
                    <tr>
                        <th>Empresa</th>
                        <th>CPF/CNPJ</th>
                        <th>E-mail</th>
                        <th>Excluída em</th>
                        <th class="text-end">Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($empresas as $empresa): ?>
                        <tr>
                            <td>
                                <strong><?= esc($empresa['nome']) ?></strong>
                            </td>

                            <td>
                                <?= esc($empresa['cpf_cnpj'] ?? '-') ?>
                            </td>

                            <td>
                                <?= esc($empresa['email'] ?? '-') ?>
                            </td>

                            <td>
                                <?= !empty($empresa['deleted_at'])
                                    ? esc(date('d/m/Y H:i', strtotime($empresa['deleted_at'])))
                                    : '-' ?>
                            </td>

                            <td class="text-end">
                                <form
                                    action="<?= site_url('clientes/empresas/restaurar/' . $empresa['id']) ?>"
                                    method="POST"
                                    onsubmit="return confirm('Deseja restaurar esta empresa?');">

                                    <?= csrf_field() ?>

                                    <button type="submit"
                                        class="btn btn-sm btn-outline-success">

                                        <i class="ri-refresh-line me-1"></i>
                                        Restaurar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

        <?php if (empty($empresas)): ?>
            <div class="text-center py-5">
                <i class="ri-delete-bin-line fs-32 text-muted"></i>

                <h6 class="mt-3">A lixeira está vazia</h6>

                <p class="text-muted">
                    Nenhuma empresa excluída.
                </p>
            </div>
        <?php endif; ?>

        <?php if ($pager->getPageCount('lixeira') > 1): ?>
            <div class="mt-3 d-flex justify-content-end">
                <?= $pager->links('lixeira', 'default_full') ?>
            </div>
        <?php endif; ?>

    </div>
</div>

<?= $this->endSection() ?>
