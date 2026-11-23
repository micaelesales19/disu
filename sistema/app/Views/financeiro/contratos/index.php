<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$contratos = $contratos ?? [];

$indicadores = $indicadores ?? [
    'total'       => 0,
    'ativos'      => 0,
    'encerrados'  => 0,
    'vencidos'    => 0,
    'previsto'    => 0,
];

$filtros = $filtros ?? [];
?>

<!-- CABEÇALHO -->
<div class="main-breadcrumb d-flex align-items-center justify-content-between my-3">

    <h2 class="breadcrumb-title mb-0 fs-18">
        Contratos
    </h2>

    <div class="d-flex align-items-center gap-2">

        <a href="#secaoReajustes"
            class="btn btn-light border"
            title="Ir para reajustes de contratos">
            <i class="ri-line-chart-line me-1"></i>
            Reajustes
        </a>

        <a href="<?= site_url('financeiro/contratos/novo') ?>"
            class="btn btn-primary">
            <i class="ri-add-line me-1"></i>
            Novo Contrato
        </a>

    </div>
</div>


<?php if (session('sucesso')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-checkbox-circle-line me-2"></i>
        <?= esc(session('sucesso')) ?>

        <button type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Fechar"></button>
    </div>
<?php endif; ?>

<?php if (session('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line me-2"></i>
        <?= esc(session('error')) ?>

        <button type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Fechar"></button>
    </div>
<?php endif; ?>


<!-- INDICADORES -->
<div class="row g-3 mb-4">

    <?php
    $cards = [
        [
            'titulo' => 'Total de contratos',
            'valor'  => (int) $indicadores['total'],
            'icone'  => 'ri-file-list-3-line',
            'cor'    => 'primary',
            'filtro' => 'todos',
        ],
        [
            'titulo' => 'Ativos',
            'valor'  => (int) $indicadores['ativos'],
            'icone'  => 'ri-checkbox-circle-line',
            'cor'    => 'success',
            'filtro' => 'ativo',
        ],
        [
            'titulo' => 'Encerrados',
            'valor'  => (int) $indicadores['encerrados'],
            'icone'  => 'ri-forbid-line',
            'cor'    => 'secondary',
            'filtro' => 'encerrado',
        ],
        [
            'titulo' => 'Com cobranças vencidas',
            'valor'  => (int) $indicadores['vencidos'],
            'icone'  => 'ri-error-warning-line',
            'cor'    => 'danger',
            'filtro' => 'vencidos',
        ],
        [
            'titulo' => 'Valor previsto',
            'valor'  => 'R$ ' . number_format(
                (float) $indicadores['previsto'],
                2,
                ',',
                '.'
            ),
            'icone'  => 'ri-money-dollar-circle-line',
            'cor'    => 'info',
        ],
    ];
    ?>

    <?php foreach ($cards as $card): ?>
        <div class="col-xl col-md-6">
            <div class="card h-100 <?= isset($card['filtro']) ? 'card-filtro' : '' ?>"
                <?= isset($card['filtro']) ? 'role="button" tabindex="0" data-filtro="' . esc($card['filtro'], 'attr') . '"' : '' ?>>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3">

                        <div class="avatar-sm rounded-3 bg-<?= esc($card['cor'], 'attr') ?>-subtle text-<?= esc($card['cor'], 'attr') ?> d-flex align-items-center justify-content-center">
                            <i class="<?= esc($card['icone'], 'attr') ?> fs-22"></i>
                        </div>

                        <div>
                            <p class="text-muted mb-1">
                                <?= esc($card['titulo']) ?>
                            </p>

                            <h3 class="fw-bold mb-0 fs-20">
                                <?= esc((string) $card['valor']) ?>
                            </h3>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>

</div>

<!-- PESQUISA -->
<div class="input-group mb-3">
    <span class="input-group-text"><i class="ri-search-line"></i></span>
    <input type="search" class="form-control" id="buscarContrato"
        placeholder="Pesquisar por título ou nome da empresa..." aria-label="Pesquisar contratos">
</div>

<!-- TABELA -->
<div class="card">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table table-hover align-middle"
                id="tabelaContratos">

                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Empresa</th>
                        <th>Valor</th>
                        <th>Tipo</th>
                        <th>Forma</th>
                        <th>Início</th>
                        <th>Término</th>
                        <th>Reajuste</th>
                        <th>Situação</th>
                        <th>Ações</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($contratos as $contrato): ?>
                        <?php
                        $situacao = $contrato['situacao'] ?? '';
                        $tipo = $contrato['tipo'] ?? '';

                        $inicio = !empty($contrato['data_inicio'])
                            ? date('d/m/Y', strtotime($contrato['data_inicio']))
                            : '-';

                        $termino = !empty($contrato['data_termino'])
                            ? date('d/m/Y', strtotime($contrato['data_termino']))
                            : 'Indeterminado';
                        ?>

                        <tr class="linha-contrato"
                            data-situacao="<?= esc($situacao, 'attr') ?>"
                            data-tipo="<?= esc($tipo, 'attr') ?>"
                            data-vencido="0"
                            data-url="<?= site_url('financeiro/contratos/editar/' . (int) $contrato['id']) ?>"
                            tabindex="0" role="link"
                            aria-label="Editar contrato <?= esc($contrato['titulo'] ?? '', 'attr') ?>">

                            <td>
                                <strong>
                                    <?= esc($contrato['titulo'] ?? '') ?>
                                </strong>
                            </td>

                            <td>
                                <?= esc($contrato['empresa_nome'] ?? '') ?>
                            </td>

                            <td class="text-nowrap">
                                R$ <?= number_format(
                                        (float) ($contrato['valor'] ?? 0),
                                        2,
                                        ',',
                                        '.'
                                    ) ?>
                            </td>

                            <td>
                                <?= esc(match ($tipo) {
                                    'mensal' => 'Mensal',
                                    'pontual' => 'Valor único',
                                    default => ucfirst($tipo),
                                }) ?>
                            </td>

                            <td>
                                <?= esc(match ($contrato['forma_pagamento']) {
                                    'boleto' => 'Boleto',
                                    'pix' => 'Pix',
                                    'transferencia' => 'Transferência',
                                    'outro' => 'Outro',
                                    default => ucfirst($contrato['forma_pagamento'] ?? '-'),
                                }) ?>
                            </td>

                            <td class="text-nowrap">
                                <?= esc($inicio) ?>
                            </td>

                            <td class="text-nowrap">
                                <?= esc($termino) ?>
                            </td>

                            <td>
                                <?= esc($contrato['reajuste_resumo'] ?? '-') ?>
                            </td>

                            <td>
                                <?php if ($situacao === 'ativo'): ?>
                                    <span class="badge bg-success-subtle text-success">
                                        Ativo
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary">
                                        Encerrado
                                    </span>
                                <?php endif; ?>
                            </td>

                            <td class="text-nowrap">
                                <div class="d-flex align-items-center gap-2">

                                    <!-- GERAR COBRANÇAS -->
                                    <button type="button"
                                        class="btn btn-sm btn-outline-primary"
                                        title="Gerar cobranças (em desenvolvimento)"
                                        aria-label="Gerar cobranças"
                                        disabled>
                                        <i class="ri-calendar-schedule-line fs-16"></i>
                                    </button>

                                    <!-- EDITAR -->
                                    <a href="<?= site_url('financeiro/contratos/editar/' . (int) $contrato['id']) ?>"
                                        class="btn btn-sm btn-outline-secondary"
                                        title="Editar contrato"
                                        aria-label="Editar contrato">
                                        <i class="ri-pencil-line fs-16"></i>
                                    </a>

                                    <!-- EXCLUIR -->
                                    <form action="<?= site_url('financeiro/contratos/excluir/' . (int) $contrato['id']) ?>"
                                        method="post"
                                        class="m-0 form-excluir-contrato"
                                        data-titulo="<?= esc($contrato['titulo'], 'attr') ?>">

                                        <?= csrf_field() ?>

                                        <button type="submit"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Excluir contrato"
                                            aria-label="Excluir contrato">
                                            <i class="ri-delete-bin-line fs-16"></i>
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>

                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

        <?php if (empty($contratos)): ?>
            <div class="text-center py-5">
                <i class="ri-file-list-3-line fs-32 text-muted"></i>

                <h6 class="mt-3">
                    Nenhum contrato cadastrado
                </h6>

                <p class="text-muted mb-0">
                    Clique em Novo Contrato para cadastrar o primeiro.
                </p>
            </div>
        <?php endif; ?>

        <div id="semResultados"
            class="text-center text-muted py-4 d-none">
            Nenhum contrato encontrado para esta pesquisa ou filtro.
        </div>


        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3">

            <small class="text-muted" id="totalEncontrado">
                <?= count($contratos) ?> contrato(s) nesta listagem
            </small>

            <nav aria-label="Paginação dos contratos">
                <ul class="pagination pagination-sm mb-0"
                    id="paginacaoContratos">
                </ul>
            </nav>

        </div>


    </div>
</div>

<?= $this->include('financeiro/contratos/_reajustes') ?>

<style>
    html {
        scroll-behavior: smooth;
    }

    #secaoReajustes {
        scroll-margin-top: 90px;
    }

    #tabelaContratos th,
    #tabelaContratos td {
        white-space: nowrap;
        vertical-align: middle;
    }

    #tabelaContratos .linha-contrato {
        cursor: pointer;
    }

    #tabelaContratos .linha-contrato:focus-visible,
    .card-filtro:focus-visible {
        outline: 2px solid var(--bs-primary);
        outline-offset: 2px;
    }

    .card-filtro {
        cursor: pointer;
        transition: border-color .15s, transform .15s;
    }

    .card-filtro:hover {
        transform: translateY(-2px);
    }

    .card-filtro.filtro-ativo {
        border: 2px solid var(--bs-primary);
    }

    #tabelaContratos .dropdown-menu {
        z-index: 1080;
        min-width: 190px;
    }

    #tabelaContratos .dropdown-item {
        padding: 10px 15px;
        font-size: 13px;
    }

    /* Linhas clicáveis dos reajustes */
    .linha-reajuste {
        cursor: pointer;
    }

    .linha-reajuste:hover {
        background-color: var(--bs-tertiary-bg);
    }

    .linha-reajuste:focus-visible {
        outline: 2px solid var(--bs-primary);
        outline-offset: -2px;
    }
</style>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const busca = document.getElementById('buscarContrato');
        const linhas = [...document.querySelectorAll('.linha-contrato')];
        const cards = [...document.querySelectorAll('.card-filtro')];
        const semResultados = document.getElementById('semResultados');
        const totalEncontrado = document.getElementById('totalEncontrado');
        let filtroAtual = 'todos';
        const paginacao = document.getElementById('paginacaoContratos');

        const itensPorPagina = 10;
        let paginaAtual = 1;

        function normalizar(texto) {
            return (texto || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase().trim();
        }


        function filtrar() {
            paginaAtual = 1;
            atualizarTabela();
        }

        function atualizarTabela() {
            const texto = normalizar(busca.value);

            const filtradas = linhas.filter(linha => {
                const nome = normalizar(
                    linha.cells[0]?.textContent + ' ' +
                    linha.cells[1]?.textContent
                );

                const atendeBusca = nome.includes(texto);

                const atendeFiltro =
                    filtroAtual === 'todos' ||
                    (
                        filtroAtual === 'vencidos' ?
                        linha.dataset.vencido === '1' :
                        linha.dataset.situacao === filtroAtual
                    );

                return atendeBusca && atendeFiltro;
            });

            const total = filtradas.length;
            const totalPaginas = Math.ceil(total / itensPorPagina);

            if (paginaAtual > totalPaginas) {
                paginaAtual = Math.max(1, totalPaginas);
            }

            const inicio = (paginaAtual - 1) * itensPorPagina;
            const fim = inicio + itensPorPagina;

            linhas.forEach(linha => {
                linha.classList.add('d-none');
            });

            filtradas.slice(inicio, fim).forEach(linha => {
                linha.classList.remove('d-none');
            });

            semResultados.classList.toggle(
                'd-none',
                total !== 0 || linhas.length === 0
            );

            totalEncontrado.textContent =
                total + ' contrato(s) encontrado(s)';

            renderizarPaginacao(totalPaginas);
        }

        function renderizarPaginacao(totalPaginas) {
            paginacao.innerHTML = '';

            if (totalPaginas <= 1) {
                return;
            }

            function criarBotao(texto, pagina, desabilitado, ativo = false) {
                const li = document.createElement('li');

                li.className = 'page-item';

                if (desabilitado) li.classList.add('disabled');
                if (ativo) li.classList.add('active');

                const botao = document.createElement('button');

                botao.type = 'button';
                botao.className = 'page-link';
                botao.textContent = texto;
                botao.disabled = desabilitado;

                botao.addEventListener('click', function() {
                    paginaAtual = pagina;
                    atualizarTabela();
                });

                li.appendChild(botao);
                paginacao.appendChild(li);
            }

            criarBotao(
                '«',
                paginaAtual - 1,
                paginaAtual === 1
            );

            for (let i = 1; i <= totalPaginas; i++) {
                criarBotao(
                    String(i),
                    i,
                    false,
                    i === paginaAtual
                );
            }

            criarBotao(
                '»',
                paginaAtual + 1,
                paginaAtual === totalPaginas
            );
        }


        function selecionarCard(card) {
            filtroAtual = card.dataset.filtro;
            cards.forEach(c => {
                c.classList.toggle('filtro-ativo', c === card);
                c.setAttribute('aria-pressed', String(c === card));
            });
            filtrar();
        }

        cards.forEach(card => {
            card.setAttribute('aria-pressed', String(card.dataset.filtro === 'todos'));
            if (card.dataset.filtro === 'todos') card.classList.add('filtro-ativo');
            card.addEventListener('click', () => selecionarCard(card));
            card.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    selecionarCard(card);
                }
            });
        });

        busca.addEventListener('input', filtrar);

        linhas.forEach(linha => {
            const abrir = () => {
                if (linha.dataset.url) window.location.assign(linha.dataset.url);
            };
            linha.addEventListener('click', e => {
                if (e.target.closest('button, a, input, select, .dropdown, [data-bs-toggle]')) return;
                abrir();
            });
            linha.addEventListener('keydown', e => {
                if (e.target !== linha) return;
                if (e.key === 'Enter') {
                    e.preventDefault();
                    abrir();
                }
            });
        });

        document.querySelectorAll('.form-excluir-contrato').forEach(form => {

            form.addEventListener('click', function(e) {
                e.stopPropagation();
            });

            form.addEventListener('submit', function(e) {
                e.stopPropagation();

                const titulo = this.dataset.titulo || 'este contrato';

                const confirmado = confirm(
                    'Deseja realmente excluir o contrato "' + titulo + '"?\n\n' +
                    'Esta ação removerá o contrato da listagem.'
                );

                if (!confirmado) {
                    e.preventDefault();
                }
            });
        });

        // ========================================
        // FILTROS E PAGINAÇÃO DOS REAJUSTES
        // ========================================

        const botoesReajuste = document.querySelectorAll('.filtro-reajuste');
        const linhasReajuste = [...document.querySelectorAll('.linha-reajuste')];

        const semReajustes = document.getElementById('semReajustes');
        const paginacaoReajustes = document.getElementById('paginacaoReajustes');
        const totalReajustesEncontrados = document.getElementById('totalReajustesEncontrados');

        const reajustesPorPagina = 10;

        let paginaReajusteAtual = 1;
        let filtroReajusteAtual = 'todos';

        function atualizarTabelaReajustes() {

            const filtradas = linhasReajuste.filter(linha => {
                return filtroReajusteAtual === 'todos' ||
                    linha.dataset.status === filtroReajusteAtual;
            });

            const total = filtradas.length;

            const totalPaginas = Math.ceil(total / reajustesPorPagina);

            // Garante uma página válida
            paginaReajusteAtual = Math.min(
                paginaReajusteAtual,
                Math.max(1, totalPaginas)
            );

            const inicio = (paginaReajusteAtual - 1) * reajustesPorPagina;
            const fim = inicio + reajustesPorPagina;

            // Oculta todas as linhas
            linhasReajuste.forEach(linha => {
                linha.classList.add('d-none');
            });

            // Mostra somente as linhas da página atual
            filtradas.slice(inicio, fim).forEach(linha => {
                linha.classList.remove('d-none');
            });

            // Mensagem de nenhum resultado
            semReajustes.classList.toggle('d-none', total > 0);

            // Contador
            if (total === 0) {
                totalReajustesEncontrados.textContent =
                    'Nenhum contrato encontrado';
            } else {
                const ultimo = Math.min(fim, total);

                totalReajustesEncontrados.textContent =
                    `Mostrando ${inicio + 1} a ${ultimo} de ${total} contrato(s)`;
            }

            renderizarPaginacaoReajustes(totalPaginas);
        }

        function renderizarPaginacaoReajustes(totalPaginas) {

            paginacaoReajustes.innerHTML = '';

            // Não exibe paginação quando há até 10 contratos
            if (totalPaginas <= 1) {
                return;
            }

            function criarBotao(texto, pagina, desabilitado, ativo = false) {

                const li = document.createElement('li');
                li.className = 'page-item';

                if (desabilitado) li.classList.add('disabled');
                if (ativo) li.classList.add('active');

                const botao = document.createElement('button');

                botao.type = 'button';
                botao.className = 'page-link';
                botao.textContent = texto;
                botao.disabled = desabilitado;

                if (ativo) {
                    botao.setAttribute('aria-current', 'page');
                }

                botao.addEventListener('click', function() {
                    paginaReajusteAtual = pagina;
                    atualizarTabelaReajustes();
                });

                li.appendChild(botao);
                paginacaoReajustes.appendChild(li);
            }

            // Anterior
            criarBotao(
                '«',
                paginaReajusteAtual - 1,
                paginaReajusteAtual === 1
            );

            // Limita os botões numéricos a 5 páginas
            const primeiraPagina = Math.max(
                1,
                Math.min(paginaReajusteAtual - 2, totalPaginas - 4)
            );

            const ultimaPagina = Math.min(
                totalPaginas,
                primeiraPagina + 4
            );

            for (let i = primeiraPagina; i <= ultimaPagina; i++) {
                criarBotao(
                    String(i),
                    i,
                    false,
                    i === paginaReajusteAtual
                );
            }

            // Próxima
            criarBotao(
                '»',
                paginaReajusteAtual + 1,
                paginaReajusteAtual === totalPaginas
            );
        }

        // ========================================
        // CLIQUE NOS FILTROS
        // ========================================

        botoesReajuste.forEach(botao => {

            botao.addEventListener('click', function() {

                filtroReajusteAtual = this.dataset.filtro;

                // Ao trocar o filtro, volta para a primeira página
                paginaReajusteAtual = 1;

                botoesReajuste.forEach(b => {
                    b.classList.remove('btn-primary');
                    b.classList.add('btn-outline-primary');
                    b.setAttribute('aria-pressed', 'false');
                });

                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                this.setAttribute('aria-pressed', 'true');

                atualizarTabelaReajustes();
            });

        });

        // Inicializa a tabela
        atualizarTabelaReajustes();

        // ========================================
        // LINHAS CLICÁVEIS DOS REAJUSTES
        // ========================================
        linhasReajuste.forEach(linha => {

            const abrirContrato = () => {
                const url = linha.dataset.url;

                if (url) {
                    window.location.assign(url);
                }
            };

            linha.addEventListener('click', function(e) {

                // Evita conflito com links e botões internos
                if (e.target.closest('a, button, input, select, form')) {
                    return;
                }

                abrirContrato();
            });

            linha.addEventListener('keydown', function(e) {

                if (e.target !== linha) return;

                if (e.key === 'Enter') {
                    e.preventDefault();
                    abrirContrato();
                }
            });
        });

        atualizarTabela();
    });
</script>

<?= $this->endSection() ?>
