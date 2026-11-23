<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
// DADOS DEMONSTRATIVOS
// Posteriormente serão substituídos por dados do MySQL.

$mesAtual = 11;
$anoAtual = 2026;

$faturamento = 1810.00;
$contasPagar = 0.00;
$clientesAtivos = 201;

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
?>

<!-- CABEÇALHO DA PÁGINA -->
<div class="main-breadcrumb d-flex align-items-center my-3">

    <h2 class="breadcrumb-title mb-0 flex-grow-1 fs-14">
        Dashboard
    </h2>

    <div class="flex-shrink-0">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-end mb-0">
                <li class="breadcrumb-item active">
                    Visão geral
                </li>
            </ol>
        </nav>
    </div>

</div>

<!-- FILTROS -->
<div class="row g-3 mb-4">

    <div class="col-6 col-sm-auto">

        <label for="filtroMes"
            class="form-label fw-semibold">
            Mês
        </label>

        <select id="filtroMes"
            class="form-select">

            <?php foreach ($meses as $numero => $nome): ?>

                <option value="<?= $numero ?>"
                    <?= $numero === $mesAtual ? 'selected' : '' ?>>
                    <?= esc($nome) ?>
                </option>

            <?php endforeach; ?>

        </select>

    </div>

    <div class="col-6 col-sm-auto">

        <label for="filtroAno"
            class="form-label fw-semibold">
            Ano
        </label>

        <select id="filtroAno"
            class="form-select">

            <?php for ($ano = 2024; $ano <= 2026; $ano++): ?>

                <option value="<?= $ano ?>"
                    <?= $ano === $anoAtual ? 'selected' : '' ?>>
                    <?= $ano ?>
                </option>

            <?php endfor; ?>

        </select>

    </div>

</div>

<!-- CARDS PRINCIPAIS -->
<div class="row g-4 mb-4">

    <!-- FATURAMENTO MENSAL -->
    <div class="col-xxl-8">
        <div class="row h-100">

            <!-- FATURAMENTO MENSAL -->
            <div class="col-xl-4 col-sm-6">
                <div class="card card-h-100">
                    <div class="card-body d-flex align-items-center justify-content-around">
                        <div class="h-48px w-50px position-relative d-flex justify-content-center align-items-center text-primary fs-4 rounded-3 shadow-lg border">
                            <i class="ri-line-chart-line fs-22"></i>
                        </div>

                        <div>
                            <span class="badge bg-primary-subtle text-primary"
                                data-mes-card>
                                Novembro
                            </span>

                            <p class="text-muted mb-1">
                                Faturamento mensal
                            </p>

                            <h3 class="fw-bold mb-1">
                                R$ <?= number_format($faturamento, 2, ',', '.') ?>
                            </h3>

                            <span class="text-muted fs-12">
                                Total recebido no mês
                            </span>

                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTAS A PAGAR -->
            <div class="col-xl-4 col-sm-6">
                <div class="card card-h-100">
                    <div class="card-body d-flex align-items-center justify-content-around">
                        <div class="h-48px w-50px position-relative d-flex justify-content-center align-items-center text-danger fs-4 rounded-3 shadow-lg border">
                            <i class="ri-bank-card-line fs-22"></i>
                        </div>

                        <div>
                            <span class="badge bg-danger-subtle text-danger"
                                data-mes-card>
                                Novembro
                            </span>
                            <p class="text-muted mb-1">
                                Contas a pagar
                            </p>

                            <h3 class="fw-bold mb-1">
                                R$ <?= number_format($contasPagar, 2, ',', '.') ?>
                            </h3>

                            <span class="text-muted fs-12">
                                Total previsto para pagamento
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CLIENTES ATIVOS -->
            <div class="col-xl-4 col-sm-6">
                <div class="card card-h-100">
                    <div class="card-body d-flex align-items-center justify-content-around">
                        <div class="h-48px w-48px position-relative d-flex justify-content-center align-items-center text-success fs-4 rounded-3 shadow-lg border">
                            <i class="ri-group-line fs-22"></i>
                        </div>
                        <div>

                            <span class="badge bg-success-subtle text-success"
                                data-mes-card>
                                Novembro
                            </span>

                            <p class="text-muted mb-1">
                                Clientes ativos
                            </p>

                            <h3 class="fw-bold mb-1">
                                <?= number_format($clientesAtivos, 0, ',', '.') ?>
                            </h3>

                            <span class="text-muted fs-12">
                                Clientes na carteira
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- GRÁFICO: CONTAS A RECEBER X CONTAS A PAGAR -->
<div class="row g-4 mb-4">
    <div class="col-12">

        <div class="card">
            <div class="card-header border-0 bg-transparent">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                    <div>
                        <h5 class="card-title mb-1">
                            Contas a receber × Contas a pagar
                        </h5>

                        <p class="text-muted mb-0 fs-12">
                            Por vencimento — próximos 6 meses
                        </p>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <span class="fs-12">
                            <i class="ri-circle-fill text-success me-1"></i>
                            A receber
                        </span>

                        <span class="fs-12">
                            <i class="ri-circle-fill text-danger me-1"></i>
                            A pagar
                        </span>
                    </div>

                </div>
            </div>

            <div class="card-body">
                <div id="graficoContas"
                    style="min-height: 320px;">
                </div>
            </div>
        </div>

    </div>
</div>


<!-- FLUXO FINANCEIRO -->
<div class="row g-4 mb-4">

    <!-- FLUXO DE ENTRADAS -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-1">Fluxo de Entradas</h5>
                <p class="text-muted fs-12 mb-0">
                    Recebimentos do período
                </p>
            </div>

            <div class="card-body text-center">
                <div id="graficoEntradas"></div>

                <div class="d-flex justify-content-between border-top pt-3 mt-2">
                    <div class="text-start">
                        <span class="text-muted fs-12">Recebido</span>
                        <h6 class="text-success mb-0">R$ 1.810,00</h6>
                    </div>

                    <div class="text-end">
                        <span class="text-muted fs-12">Em aberto</span>
                        <h6 class="mb-0">R$ 78.190,00</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- COBRANÇAS E CONTAS A PAGAR -->
    <div class="col-xl-6">
        <div class="card h-100">

            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-0">
                    Cobranças e Contas a Pagar
                </h5>
            </div>

            <div class="card-body">

                <!-- ABAS -->
                <ul class="nav nav-pills mb-3"
                    id="abasFinanceiro"
                    role="tablist">

                    <li class="nav-item" role="presentation">
                        <button class="nav-link active"
                            id="abaEntradas"
                            data-bs-toggle="pill"
                            data-bs-target="#conteudoEntradas"
                            type="button"
                            role="tab"
                            aria-controls="conteudoEntradas"
                            aria-selected="true">
                            Entradas
                        </button>
                    </li>

                    <li class="nav-item" role="presentation">
                        <button class="nav-link"
                            id="abaPagar"
                            data-bs-toggle="pill"
                            data-bs-target="#conteudoPagar"
                            type="button"
                            role="tab"
                            aria-controls="conteudoPagar"
                            aria-selected="false">
                            Contas a Pagar
                        </button>
                    </li>

                </ul>

                <div class="tab-content" id="conteudoFinanceiro">

                    <!-- LISTA DE ENTRADAS -->
                    <div class="tab-pane fade show active"
                        id="conteudoEntradas"
                        role="tabpanel"
                        aria-labelledby="abaEntradas">

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Cliente</th>
                                        <th>Vencimento</th>
                                        <th>Valor</th>
                                        <th>Situação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Empresa Exemplo A</td>
                                        <td>05/11/2026</td>
                                        <td>R$ 850,00</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success">
                                                Recebido
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Empresa Exemplo B</td>
                                        <td>10/11/2026</td>
                                        <td>R$ 960,00</td>
                                        <td>
                                            <span class="badge bg-success-subtle text-success">
                                                Recebido
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>Empresa Exemplo C</td>
                                        <td>20/11/2026</td>
                                        <td>R$ 1.500,00</td>
                                        <td>
                                            <span class="badge bg-warning-subtle text-warning">
                                                Em aberto
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                    <!-- LISTA DE CONTAS A PAGAR -->
                    <div class="tab-pane fade"
                        id="conteudoPagar"
                        role="tabpanel"
                        aria-labelledby="abaPagar">

                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Descrição</th>
                                        <th>Vencimento</th>
                                        <th>Valor</th>
                                        <th>Situação</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="4"
                                            class="text-center text-muted py-4">
                                            Nenhuma conta a pagar no período.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- FLUXO DE SAÍDAS -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100">
            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-1">Fluxo de Saídas</h5>
                <p class="text-muted fs-12 mb-0">
                    Pagamentos do período
                </p>
            </div>

            <div class="card-body text-center">
                <div id="graficoSaidas"></div>

                <div class="d-flex justify-content-between border-top pt-3 mt-2">
                    <div class="text-start">
                        <span class="text-muted fs-12">Pago</span>
                        <h6 class="text-success mb-0">R$ 0,00</h6>
                    </div>

                    <div class="text-end">
                        <span class="text-muted fs-12">Em aberto</span>
                        <h6 class="mb-0">R$ 0,00</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- GESTÃO DO ESCRITÓRIO -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-transparent border-0">
                <h5 class="card-title mb-0">
                    Gestão do Escritório
                </h5>
            </div>

            <div class="card-body">

                <!-- INDICADORES -->
                <div class="row g-3 mb-4">

                    <div class="col-lg col-md-4 col-6">
                        <div class="border rounded-3 p-3 h-100">
                            <p class="text-muted mb-2 fs-12">
                                Novos Clientes
                            </p>
                            <h3 class="fw-bold text-success mb-0">0</h3>
                        </div>
                    </div>

                    <div class="col-lg col-md-4 col-6">
                        <div class="border rounded-3 p-3 h-100">
                            <p class="text-muted mb-2 fs-12">
                                Saídas
                            </p>
                            <h3 class="fw-bold text-danger mb-0">0</h3>
                        </div>
                    </div>

                    <div class="col-lg col-md-4 col-6">
                        <div class="border rounded-3 p-3 h-100">
                            <p class="text-muted mb-2 fs-12">
                                Saldo
                            </p>
                            <h3 class="fw-bold mb-0">+0</h3>
                        </div>
                    </div>

                    <div class="col-lg col-md-6 col-6">
                        <div class="border rounded-3 p-3 h-100">
                            <p class="text-muted mb-2 fs-12">
                                Clientes Ativos
                            </p>
                            <h3 class="fw-bold text-primary mb-0">201</h3>
                        </div>
                    </div>

                    <div class="col-lg col-md-6 col-6">
                        <div class="border rounded-3 p-3 h-100">
                            <p class="text-muted mb-2 fs-12">
                                Churn
                            </p>
                            <h3 class="fw-bold mb-0">0%</h3>
                        </div>
                    </div>

                </div>

                <!-- GRÁFICO -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="fw-semibold mb-1">
                            Entradas e Saídas de Clientes
                        </h6>
                        <p class="text-muted fs-12 mb-3">
                            Movimentação dos últimos 6 meses
                        </p>

                        <div id="graficoGestaoEscritorio"
                            style="min-height: 300px;"></div>
                    </div>
                </div>

                <!-- LISTAS -->
                <div class="row g-4 mt-2">

                    <div class="col-lg-6">
                        <h6 class="fw-semibold mb-3">
                            <i class="ri-user-add-line text-success me-2"></i>
                            Novos Clientes
                        </h6>

                        <div class="border rounded-3 p-4 text-center text-muted">
                            Nenhum cliente novo no período.
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <h6 class="fw-semibold mb-3">
                            <i class="ri-user-unfollow-line text-danger me-2"></i>
                            Saídas de Clientes
                        </h6>

                        <div class="border rounded-3 p-4 text-center text-muted">
                            Nenhuma saída de cliente no período.
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>


<!-- AVISO TEMPORÁRIO -->
<div class="alert alert-info d-flex align-items-center gap-2"
    role="status">

    <i class="ri-information-line fs-20"></i>

    <span>
        Dashboard em desenvolvimento.
        Os valores apresentados são demonstrativos.
    </span>

</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('assets/libs/apexcharts/apexcharts.min.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        const filtroMes = document.getElementById('filtroMes');
        const filtroAno = document.getElementById('filtroAno');

        const meses = [
            'Janeiro', 'Fevereiro', 'Março',
            'Abril', 'Maio', 'Junho',
            'Julho', 'Agosto', 'Setembro',
            'Outubro', 'Novembro', 'Dezembro'
        ];

        function atualizarFiltros() {

            const mes = Number(filtroMes.value);
            const ano = filtroAno.value;

            document.querySelectorAll('[data-mes-card]')
                .forEach(function(elemento) {
                    elemento.textContent = meses[mes - 1] + '/' + ano;
                });

            // Os valores permanecem demonstrativos nesta etapa.
            // Depois faremos a consulta real por mês e ano.
        }

        // GRÁFICO FINANCEIRO
        // Valores demonstrativos para testar o layout.
        const dadosReceber = [
            78910, 80000, 0, 0, 0, 0
        ];

        const dadosPagar = [
            0, 0, 0, 0, 0, 0
        ];

        function obterMesesGrafico() {
            const mesSelecionado = Number(filtroMes.value) - 1;
            const anoSelecionado = Number(filtroAno.value);

            const nomesMeses = [
                'Jan', 'Fev', 'Mar', 'Abr',
                'Mai', 'Jun', 'Jul', 'Ago',
                'Set', 'Out', 'Nov', 'Dez'
            ];

            const categorias = [];

            for (let i = 0; i < 6; i++) {
                const data = new Date(
                    anoSelecionado,
                    mesSelecionado + i,
                    1
                );

                categorias.push(
                    nomesMeses[data.getMonth()] + '/' +
                    data.getFullYear()
                );
            }

            return categorias;
        }

        const opcoesGrafico = {
            series: [{
                    name: 'Contas a receber',
                    data: dadosReceber
                },
                {
                    name: 'Contas a pagar',
                    data: dadosPagar
                }
            ],

            chart: {
                type: 'area',
                height: 340,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: false
                }
            },

            colors: ['#16a34a', '#ef4444'],

            stroke: {
                curve: 'smooth',
                width: 3
            },

            fill: {
                type: 'gradient',
                gradient: {
                    opacityFrom: 0.15,
                    opacityTo: 0.01
                }
            },

            dataLabels: {
                enabled: false
            },

            markers: {
                size: 0,
                hover: {
                    size: 5
                }
            },

            xaxis: {
                categories: obterMesesGrafico()
            },

            yaxis: {
                labels: {
                    formatter: function(valor) {
                        return 'R$ ' +
                            (valor / 1000).toFixed(0) + 'k';
                    }
                }
            },

            tooltip: {
                y: {
                    formatter: function(valor) {
                        return valor.toLocaleString('pt-BR', {
                            style: 'currency',
                            currency: 'BRL'
                        });
                    }
                }
            },

            legend: {
                show: false
            },

            grid: {
                borderColor: '#e9ecef',
                strokeDashArray: 4
            }
        };

        const graficoContas = new ApexCharts(
            document.querySelector('#graficoContas'),
            opcoesGrafico
        );

        graficoContas.render();

        // Atualiza os meses do gráfico quando
        // o usuário altera o período.

        function atualizarPeriodoGrafico() {
            graficoContas.updateOptions({
                xaxis: {
                    categories: obterMesesGrafico()
                }
            });
        }


        // =======================================
        // FLUXO DE ENTRADAS E SAÍDAS
        // Dados demonstrativos
        // =======================================

        function criarGraficoFluxo(seletor, percentual, cor) {

            const opcoes = {
                series: [percentual],

                chart: {
                    type: 'radialBar',
                    height: 240,
                    sparkline: {
                        enabled: true
                    }
                },

                colors: [cor],

                plotOptions: {
                    radialBar: {
                        hollow: {
                            size: '65%'
                        },

                        track: {
                            background: '#e9ecef'
                        },

                        dataLabels: {
                            name: {
                                show: false
                            },

                            value: {
                                fontSize: '26px',
                                fontWeight: 700,
                                offsetY: 8,

                                formatter: function(valor) {
                                    return Number(valor)
                                        .toLocaleString('pt-BR', {
                                            maximumFractionDigits: 1
                                        }) + '%';
                                }
                            }
                        }
                    }
                },

                stroke: {
                    lineCap: 'round'
                }
            };

            const grafico = new ApexCharts(
                document.querySelector(seletor),
                opcoes
            );

            grafico.render();

            return grafico;
        }

        // Entradas: R$ 1.810 de R$ 80.000
        const percentualEntradas = (1810 / 80000) * 100;

        // Saídas: sem pagamentos no período
        const percentualSaidas = 0;

        criarGraficoFluxo(
            '#graficoEntradas',
            percentualEntradas,
            '#16a34a'
        );

        criarGraficoFluxo(
            '#graficoSaidas',
            percentualSaidas,
            '#ef4444'
        );


        // =======================================
        // GESTÃO DO ESCRITÓRIO
        // Dados demonstrativos
        // =======================================

        function obterUltimosSeisMeses() {
            const mesSelecionado = Number(filtroMes.value) - 1;
            const anoSelecionado = Number(filtroAno.value);

            const nomesMeses = [
                'Jan', 'Fev', 'Mar', 'Abr',
                'Mai', 'Jun', 'Jul', 'Ago',
                'Set', 'Out', 'Nov', 'Dez'
            ];

            const categorias = [];

            for (let i = 5; i >= 0; i--) {
                const data = new Date(
                    anoSelecionado,
                    mesSelecionado - i,
                    1
                );

                categorias.push(
                    nomesMeses[data.getMonth()] + '/' +
                    data.getFullYear()
                );
            }

            return categorias;
        }

        const graficoGestao = new ApexCharts(
            document.querySelector('#graficoGestaoEscritorio'), {
                series: [{
                        name: 'Novos clientes',
                        data: [3, 2, 5, 1, 4, 0]
                    },
                    {
                        name: 'Saídas de clientes',
                        data: [1, 0, 2, 1, 1, 0]
                    }
                ],

                chart: {
                    type: 'bar',
                    height: 300,
                    toolbar: {
                        show: false
                    }
                },

                colors: ['#16a34a', '#ef4444'],

                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '35%',
                        borderRadius: 4
                    }
                },

                dataLabels: {
                    enabled: false
                },

                xaxis: {
                    categories: obterUltimosSeisMeses()
                },

                yaxis: {
                    min: 0,
                    forceNiceScale: true,
                    labels: {
                        formatter: function(valor) {
                            return Math.round(valor).toString();
                        }
                    }
                },

                legend: {
                    position: 'bottom'
                },

                grid: {
                    borderColor: '#e9ecef',
                    strokeDashArray: 4
                }
            }
        );

        graficoGestao.render();

        // Atualiza os rótulos quando o período muda.
        // Os valores ainda são demonstrativos.
        function atualizarPeriodoGestao() {
            graficoGestao.updateOptions({
                xaxis: {
                    categories: obterUltimosSeisMeses()
                }
            });
        }

        filtroMes.addEventListener('change', atualizarPeriodoGestao);
        filtroAno.addEventListener('change', atualizarPeriodoGestao);

        filtroMes.addEventListener(
            'change',
            atualizarPeriodoGrafico
        );

        filtroAno.addEventListener(
            'change',
            atualizarPeriodoGrafico
        );


        filtroMes.addEventListener('change', atualizarFiltros);
        filtroAno.addEventListener('change', atualizarFiltros);

        atualizarFiltros();

    });
</script>

<?= $this->endSection() ?>
