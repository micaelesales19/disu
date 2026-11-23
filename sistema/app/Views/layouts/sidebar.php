<aside class="pe-app-sidebar" id="sidebar">

    <!-- LOGO DISU -->
    <div class="pe-app-sidebar-logo px-3 d-flex align-items-center position-relative">

        <a href="<?= site_url('dashboard') ?>"
            class="d-flex align-items-center justify-content-center logo-main">

            <img
                src="<?= base_url('assets/images/disu-logo.png') ?>"
                alt="Logo DISU"
                class="disu-sidebar-logo">

        </a>

        <button type="button"
            id="sidebarDefaultArrow"
            class="btn btn-sm p-0 fs-16 text-body-emphasis ms-auto float-end d-none icon-hover-btn">
            <i class="ri-arrow-right-line fs-5"></i>
        </button>

    </div>
    <!-- MENU LATERAL -->
    <nav class="pe-app-sidebar-menu nav nav-pills"
        data-simplebar
        id="sidebar-simplebar">

        <div class="d-flex align-items-start flex-column w-100">

            <ul class="pe-main-menu list-unstyled">

                <li class="pe-menu-title">Principal</li>

                <!-- DASHBOARD -->
                <li class="pe-slide pe-has-sub">
                    <a href="<?= site_url('dashboard') ?>"
                        class="pe-nav-link">
                        <i class="ri-dashboard-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Dashboard</span>
                    </a>
                </li>

                <!-- CLIENTES -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseClientes"
                        class="pe-nav-link"
                        data-bs-toggle="collapse"
                        aria-expanded="false"
                        aria-controls="collapseClientes">

                        <i class="ri-group-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Clientes</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>

                    <ul class="pe-slide-menu collapse" id="collapseClientes">

                        <li class="pe-slide-item">
                            <a href="<?= site_url('clientes/empresas') ?>"
                                class="pe-nav-link">
                                <i class="ri-building-2-line me-2"></i>
                                Empresas
                            </a>
                        </li>

                        <li class="pe-slide-item">
                            <a href="javascript:void(0)"
                                class="pe-nav-link">
                                <i class="ri-bank-line me-2"></i>
                                Central do Cliente
                            </a>
                        </li>

                    </ul>
                </li>

                <!-- FINANCEIRO -->
                <li class="pe-slide pe-has-sub">
                    <a href="#collapseFinanceiro"
                        class="pe-nav-link"
                        data-bs-toggle="collapse"
                        aria-expanded="false"
                        aria-controls="collapseFinanceiro">

                        <i class="ri-money-dollar-circle-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Financeiro</span>
                        <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                    </a>

                    <ul class="pe-slide-menu collapse" id="collapseFinanceiro">
                        <li class="pe-slide-item">
                            <a href="<?= site_url('financeiro/contratos') ?>" class="pe-nav-link">
                                <i class="ri-file-list-3-line"></i>
                                Contrato recorrência
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="<?= site_url('financeiro/cobrancas') ?>" class="pe-nav-link">
                                <i class="ri-money-dollar-circle-line"></i>
                                Contas a receber
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="<?= site_url('financeiro/contratos') ?>" class="pe-nav-link">
                                <i class="ri-bank-card-line"></i>
                                Contas a pagar
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="<?= site_url('financeiro/servicos') ?>" class="pe-nav-link">
                                <i class="ri-shapes-line"></i>
                                Serviços
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="<?= site_url('financeiro/servicos') ?>" class="pe-nav-link">
                                <i class="ri-receipt-line"></i>
                                Notas fiscais emitidas
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="<?= site_url('financeiro/servicos') ?>" class="pe-nav-link">
                                <i class="ri-folder-chart-line"></i>
                                Categorias e centros de custo
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="<?= site_url('financeiro/servicos') ?>" class="pe-nav-link">
                                <i class="ri-bank-line"></i>
                                Contas bancárias
                            </a>
                        </li>
                        <li class="pe-slide-item">
                            <a href="<?= site_url('financeiro/servicos') ?>" class="pe-nav-link">
                                <i class="ri-bar-chart-box-line"></i>
                                Relatórios e análises
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- COMERCIAL -->
                <li class="pe-slide pe-has-sub">
                    <a href="javascript:void(0)" class="pe-nav-link">
                        <i class="ri-briefcase-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Comercial</span>
                    </a>
                </li>

                <!-- SOCIETÁRIO -->
                <li class="pe-slide pe-has-sub">
                    <a href="javascript:void(0)" class="pe-nav-link">
                        <i class="ri-building-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Societário</span>
                    </a>
                </li>

                <!-- REDE HUB -->
                <li class="pe-slide pe-has-sub">
                    <a href="javascript:void(0)" class="pe-nav-link">
                        <i class="ri-share-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Rede Hub</span>
                    </a>
                </li>

                <!-- CONTÁBIL -->
                <li class="pe-slide pe-has-sub">
                    <a href="javascript:void(0)" class="pe-nav-link">
                        <i class="ri-calculator-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Contábil</span>
                    </a>
                </li>

                <!-- FISCAL -->
                <li class="pe-slide pe-has-sub">
                    <a href="javascript:void(0)" class="pe-nav-link">
                        <i class="ri-file-list-3-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Fiscal</span>
                    </a>
                </li>

                <!-- TAREFAS -->
                <li class="pe-slide pe-has-sub">
                    <a href="javascript:void(0)" class="pe-nav-link">
                        <i class="ri-task-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Tarefas</span>
                    </a>
                </li>

                <!-- AUTOMAÇÕES -->
                <li class="pe-slide pe-has-sub">
                    <a href="javascript:void(0)" class="pe-nav-link">
                        <i class="ri-settings-3-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Automações</span>
                    </a>
                </li>

                <!-- HUB COMUNICA -->
                <li class="pe-slide pe-has-sub">
                    <a href="javascript:void(0)" class="pe-nav-link">
                        <i class="ri-chat-3-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Hub Comunica</span>
                    </a>
                </li>

                <!-- DOCUMENTOS -->
                <li class="pe-slide pe-has-sub">
                    <a href="javascript:void(0)" class="pe-nav-link">
                        <i class="ri-folder-open-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Documentos</span>
                    </a>
                </li>

                <!-- INTELIGÊNCIA ARTIFICIAL -->
                <li class="pe-slide pe-has-sub">
                    <a href="javascript:void(0)" class="pe-nav-link">
                        <i class="ri-robot-2-line pe-nav-icon"></i>
                        <span class="pe-nav-content">IA</span>
                    </a>
                </li>

                <li class="pe-menu-title">Sistema</li>

                <!-- CONFIGURAÇÕES -->
                <li class="pe-slide pe-has-sub">
                    <a href="javascript:void(0)" class="pe-nav-link">
                        <i class="ri-settings-line pe-nav-icon"></i>
                        <span class="pe-nav-content">Configurações</span>
                    </a>
                </li>

            </ul>

        </div>
    </nav>

</aside>
