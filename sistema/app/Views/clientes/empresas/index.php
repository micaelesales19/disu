<?php

/**
 * @var array $empresas
 * @var array $indicadores
 * @var array $filtros
 * @var \CodeIgniter\Pager\Pager $pager
 * @var int $totalFiltrado
 */
?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<!-- CABEÇALHO -->
<div class="main-breadcrumb d-flex align-items-center justify-content-between my-3">

    <h2 class="breadcrumb-title mb-0 fs-18">
        Empresas
    </h2>

    <div class="d-flex align-items-center gap-2">

        <a href="<?= site_url('clientes/empresas/lixeira') ?>"
            class="btn btn-light border">

            <i class="ri-delete-bin-line me-1"></i>
            Lixeira
        </a>

        <a href="<?= site_url('clientes/empresas/novo') ?>"
            class="btn btn-primary">

            <i class="ri-add-line me-1"></i>
            Nova Empresa
        </a>

    </div>

</div>

<!-- INDICADORES -->
<div class="row g-3 mb-4">

    <?php
    $cards = [
        [
            'titulo' => 'Total de clientes',
            'valor'  => $indicadores['total'],
            'icone'  => 'ri-group-line',
            'cor'    => 'primary',
        ],
        [
            'titulo' => 'Ativos',
            'valor'  => $indicadores['ativos'],
            'icone'  => 'ri-checkbox-circle-line',
            'cor'    => 'success',
        ],
        [
            'titulo' => 'Inativos',
            'valor'  => $indicadores['inativos'],
            'icone'  => 'ri-forbid-line',
            'cor'    => 'danger',
        ],
        [
            'titulo' => 'VIP',
            'valor'  => $indicadores['vip'],
            'icone'  => 'ri-star-line',
            'cor'    => 'warning',
        ],
    ];
    ?>

    <?php foreach ($cards as $card): ?>

        <div class="col-xl-3 col-md-6">

            <div class="card h-100">
                <div class="card-body">

                    <div class="d-flex align-items-center gap-3">

                        <div class="avatar-sm rounded-3 bg-<?= $card['cor'] ?>-subtle text-<?= $card['cor'] ?> d-flex align-items-center justify-content-center">
                            <i class="<?= esc($card['icone']) ?> fs-22"></i>
                        </div>

                        <div>
                            <p class="text-muted mb-1">
                                <?= esc($card['titulo']) ?>
                            </p>

                            <h3 class="fw-bold mb-0">
                                <?= (int) $card['valor'] ?>
                            </h3>
                        </div>

                    </div>

                </div>
            </div>

        </div>

    <?php endforeach; ?>

</div>

<form method="GET" action="<?= site_url('clientes/empresas') ?>" id="formFiltrosEmpresas">

    <div class="row g-3 mb-3">

        <div class="col-md-3">
            <select class="form-select" name="regime" id="filtroRegime">
                <option value="">Todos os regimes</option>

                <?php foreach (
                    [
                        'simples_nacional' => 'Simples Nacional',
                        'lucro_presumido' => 'Lucro Presumido',
                        'lucro_real' => 'Lucro Real',
                        'simei' => 'SIMEI / MEI',
                        'pessoa_fisica' => 'Pessoa Física'
                    ] as $valor => $rotulo
                ): ?>
                    <option value="<?= esc($valor, 'attr') ?>"
                        <?= ($filtros['regime'] ?? '') === $valor ? 'selected' : '' ?>>
                        <?= esc($rotulo) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3">
            <select class="form-select" name="status" id="filtroStatus">
                <option value="">Todos os status</option>
                <option value="Ativo" <?= ($filtros['status'] ?? '') === 'Ativo' ? 'selected' : '' ?>>Ativos</option>
                <option value="Inativo" <?= ($filtros['status'] ?? '') === 'Inativo' ? 'selected' : '' ?>>Inativos</option>
            </select>
        </div>

        <div class="col-md-3">
            <select class="form-select" name="perfil" id="filtroPerfil">
                <option value="">Todos os perfis</option>
                <option value="VIP" <?= ($filtros['perfil'] ?? '') === 'VIP' ? 'selected' : '' ?>>VIP</option>
                <option value="Normal" <?= ($filtros['perfil'] ?? '') === 'Normal' ? 'selected' : '' ?>>Normal</option>
            </select>
        </div>

        <div class="col-md-3">
            <a href="<?= site_url('clientes/empresas') ?>" class="btn btn-light border w-100">
                <i class="ri-refresh-line me-1"></i>
                Limpar filtros
            </a>
        </div>

    </div>

    <div class="input-group mb-3">
        <span class="input-group-text">
            <i class="ri-search-line"></i>
        </span>

        <input
            type="search"
            name="busca"
            id="buscarEmpresa"
            class="form-control"
            value="<?= esc($filtros['busca'] ?? '', 'attr') ?>"
            placeholder="Pesquisar por nome, razão social, CNPJ, e-mail...">

        <button type="submit" class="btn btn-primary">
            Pesquisar
        </button>
    </div>
</form>


<!-- TABELA DE EMPRESAS -->
<div class="card">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-hover align-middle"
                id="tabelaEmpresas">

                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Empresa</th>
                        <th>E-mail</th>
                        <th>Cidade</th>
                        <th>Regime</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($empresas as $empresa): ?>

                        <tr
                            data-regime="<?= esc($empresa['regime_tributario'] ?? '', 'attr') ?>"
                            data-status="<?= !empty($empresa['ativo']) ? 'Ativo' : 'Inativo' ?>"
                            data-perfil="<?= !empty($empresa['vip']) ? 'VIP' : 'Normal' ?>">

                            <td>
                                <?= esc($empresa['codigo_interno'] ?? '') ?>
                            </td>

                            <td>
                                <strong>
                                    <?= esc($empresa['nome']) ?>
                                </strong>

                                <div class="text-muted fs-12">
                                    <?= esc($empresa['cpf_cnpj'] ?? '') ?>
                                </div>
                            </td>

                            <td>
                                <?= esc($empresa['email'] ?? '') ?>
                            </td>

                            <td>
                                <?= esc(
                                    trim(
                                        ($empresa['cidade'] ?? '') .
                                            '/' .
                                            ($empresa['uf'] ?? ''),
                                        '/'
                                    )
                                ) ?>
                            </td>

                            <td>
                                <?php
                                $regimes = [
                                    'simples_nacional' => 'Simples Nacional',
                                    'lucro_presumido'  => 'Lucro Presumido',
                                    'lucro_real'       => 'Lucro Real',
                                    'simei'            => 'SIMEI / MEI',
                                    'pessoa_fisica'    => 'Pessoa Física',
                                ];

                                $regimeAtual = $empresa['regime_tributario'] ?? '';
                                ?>

                                <?= esc($regimes[$regimeAtual] ?? ($regimeAtual ?: 'Não informado')) ?>
                            </td>


                            <td>
                                <?php if ($empresa['ativo']): ?>
                                    <span class="badge bg-success-subtle text-success">
                                        Ativo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-danger-subtle text-danger">
                                        Inativo
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <div class="dropdown">
                                    <div class="dropdown position-static">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-light border"
                                            data-bs-toggle="dropdown"
                                            data-bs-boundary="viewport"
                                            data-bs-display="dynamic"
                                            aria-expanded="false"
                                            title="Ações da empresa">

                                            <i class="ri-more-2-fill fs-18"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">

                                            <li>
                                                <a class="dropdown-item"
                                                    href="<?= site_url('clientes/empresas/editar/' . $empresa['id']) ?>">

                                                    <i class="ri-edit-line me-2 text-primary"></i>
                                                    Editar empresa
                                                </a>
                                            </li>

                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>

                                            <li>
                                                <form
                                                    action="<?= site_url('clientes/empresas/alterar-status/' . $empresa['id']) ?>"
                                                    method="POST"
                                                    onsubmit="return confirm('Deseja realmente <?= !empty($empresa['ativo']) ? 'desativar' : 'reativar' ?> esta empresa?');">

                                                    <?= csrf_field() ?>

                                                    <button
                                                        type="submit"
                                                        class="dropdown-item">

                                                        <?php if (!empty($empresa['ativo'])): ?>

                                                            <i class="ri-forbid-line me-2 text-warning"></i>
                                                            Desativar empresa

                                                        <?php else: ?>

                                                            <i class="ri-checkbox-circle-line me-2 text-success"></i>
                                                            Reativar empresa

                                                        <?php endif; ?>

                                                    </button>
                                                </form>
                                            </li>


                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>

                                            <li>
                                                <form
                                                    action="<?= site_url('clientes/empresas/excluir/' . $empresa['id']) ?>"
                                                    method="POST"
                                                    onsubmit="return confirm('ATENÇÃO!\n\nDeseja realmente excluir a empresa <?= esc(addslashes($empresa['nome']), 'js') ?>?\n\nEla será removida da listagem, mas poderá ser restaurada futuramente.');">

                                                    <?= csrf_field() ?>

                                                    <button
                                                        type="submit"
                                                        class="dropdown-item text-danger">

                                                        <i class="ri-delete-bin-line me-2"></i>
                                                        Excluir empresa

                                                    </button>
                                                </form>
                                            </li>

                                        </ul>
                                    </div>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>
        </div>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mt-3">
            <small class="text-muted">
                <?= (int) $totalFiltrado ?> empresa(s) encontrada(s)
            </small>
            <?php if ($pager->getPageCount('empresas') > 1): ?>
                <?= $pager->only(['busca', 'regime', 'status', 'perfil'])
                    ->links('empresas', 'default_full') ?>
            <?php endif; ?>
        </div>


        <?php if (empty($empresas)): ?>
            <div class="text-center py-5">
                <i class="ri-building-2-line fs-32 text-muted"></i>

                <h6 class="mt-3">
                    Nenhuma empresa cadastrada
                </h6>

                <p class="text-muted mb-3">
                    Comece cadastrando a primeira empresa do DISU.
                </p>

                <a href="<?= site_url('clientes/empresas/novo') ?>"
                    class="btn btn-primary">
                    <i class="ri-add-line me-1"></i>
                    Nova Empresa
                </a>
            </div>
        <?php endif; ?>

        <div id="semResultados"
            class="text-center text-muted py-4 d-none">
            Nenhuma empresa encontrada com os filtros selecionados.
        </div>

    </div>
</div>

<style>
    /* Permite que o menu de ações fique sobre a tabela */
    #tabelaEmpresas .dropdown-menu {
        z-index: 1080;
        min-width: 190px;
    }

    #tabelaEmpresas .dropdown-item {
        padding: 10px 15px;
        font-size: 13px;
    }

    #tabelaEmpresas .dropdown-item:hover {
        background-color: #f5f6fa;
    }

    #tabelaEmpresas td:last-child {
        white-space: nowrap;
    }
</style>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const busca = document.getElementById('buscarEmpresa');
        const regime = document.getElementById('filtroRegime');
        const status = document.getElementById('filtroStatus');
        const perfil = document.getElementById('filtroPerfil');

        const linhas = document.querySelectorAll('#tabelaEmpresas tbody tr');
        const semResultados = document.getElementById('semResultados');

        function filtrarEmpresas() {

            const texto = busca.value.toLocaleLowerCase('pt-BR').trim();
            let visiveis = 0;

            linhas.forEach(function(linha) {

                const correspondeBusca =
                    linha.textContent.toLocaleLowerCase('pt-BR').includes(texto);

                const correspondeRegime = !regime.value || linha.dataset.regime === regime.value;

                const correspondeStatus = !status.value || linha.dataset.status === status.value;

                const correspondePerfil = !perfil.value || linha.dataset.perfil === perfil.value;

                const mostrar =
                    correspondeBusca &&
                    correspondeRegime &&
                    correspondeStatus &&
                    correspondePerfil;

                linha.classList.toggle('d-none', !mostrar);

                if (mostrar) {
                    visiveis++;
                }
            });

            semResultados.classList.toggle(
                'd-none',
                visiveis > 0 || linhas.length === 0
            );
        }

        [busca, regime, status, perfil].forEach(function(campo) {
            campo.addEventListener('input', filtrarEmpresas);
            campo.addEventListener('change', filtrarEmpresas);
        });

    });


    document.addEventListener('DOMContentLoaded', function() {

        document.querySelectorAll('#tabelaEmpresas .dropdown').forEach(function(dropdown) {

            const botao = dropdown.querySelector('[data-bs-toggle="dropdown"]');
            const menu = dropdown.querySelector('.dropdown-menu');

            if (!botao || !menu) return;

            let placeholder = null;

            function posicionarMenu() {
                const rect = botao.getBoundingClientRect();
                const largura = menu.offsetWidth;
                const altura = menu.offsetHeight;

                let esquerda = rect.right - largura;
                let topo = rect.bottom + 5;

                esquerda = Math.max(8, Math.min(esquerda, window.innerWidth - largura - 8));

                if (topo + altura > window.innerHeight - 8) {
                    topo = rect.top - altura - 5;
                }

                menu.style.position = 'fixed';
                menu.style.left = esquerda + 'px';
                menu.style.top = Math.max(8, topo) + 'px';
                menu.style.transform = 'none';
                menu.style.inset = 'auto';
                menu.style.zIndex = '2000';
            }

            botao.addEventListener('show.bs.dropdown', function() {
                placeholder = document.createComment('dropdown-placeholder');
                menu.parentNode.insertBefore(placeholder, menu);
                document.body.appendChild(menu);
            });

            botao.addEventListener('shown.bs.dropdown', function() {
                posicionarMenu();
            });

            botao.addEventListener('hide.bs.dropdown', function() {
                if (placeholder && placeholder.parentNode) {
                    placeholder.parentNode.insertBefore(menu, placeholder);
                    placeholder.remove();
                    placeholder = null;
                }

                menu.removeAttribute('style');
            });

        });

    });
</script>

<?= $this->endSection() ?>
