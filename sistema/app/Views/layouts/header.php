<?php
$usuario = auth()->user();
$email = $usuario->email ?? '';
$nome = $usuario->username ?? $email;
?>

<header class="app-header" id="appHeader">
    <div class="container-fluid w-100">
        <div class="d-flex justify-content-between align-items-center">

            <!-- LADO ESQUERDO -->
            <div class="d-inline-flex align-items-center gap-2">

                <!-- Botão recolher sidebar -->
                <button
                    type="button"
                    class="vertical-toggle btn header-btn"
                    id="toggleSidebar"
                    aria-label="Recolher menu">

                    <i class="bi bi-arrow-bar-left header-icon"></i>
                </button>

                <span class="fw-semibold text-body-emphasis d-none d-md-inline">
                    Sistema DISU
                </span>

            </div>

            <!-- LADO DIREITO -->
            <div class="flex-shrink-0 d-flex align-items-center gap-3">

                <!-- Notificações -->
                <div class="dropdown pe-dropdown-mega">
                    <button
                        class="btn header-btn"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        aria-label="Notificações">

                        <i class="bi bi-bell"></i>
                    </button>

                    <div class="dropdown-menu dropdown-menu-end header-dropdown-menu p-0">
                        <div class="p-3 border-bottom">
                            <h6 class="mb-0">Notificações</h6>
                        </div>

                        <div class="p-4 text-center text-muted">
                            Nenhuma notificação no momento.
                        </div>
                    </div>
                </div>

                <div class="dark-mode-btn" id="toggleMode">
                    <button class="btn header-btn active" id="lightModeBtn" type="button" aria-label="Switch to light mode">
                        <i class="bi bi-brightness-high"></i>
                    </button>
                    <button class="btn header-btn" id="darkModeBtn" type="button" aria-label="Switch to Dark mode">
                        <i class="bi bi-moon-stars"></i>
                    </button>
                </div>

                <!-- Perfil do usuário -->
                <div class="dropdown pe-dropdown-mega">

                    <button
                        class="header-profile-btn btn gap-2 text-start"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">

                        <div class="d-none d-md-block pe-2">
                            <span class="d-block mb-0 fs-12 fw-semibold">
                                <?= esc($nome) ?>
                            </span>

                            <span class="d-block mb-0 fs-10 text-muted">
                                <?= esc($email) ?>
                            </span>
                        </div>

                        <span class="header-btn btn">
                            <i class="bi bi-person-circle fs-22"></i>
                        </span>
                    </button>

                    <!-- Menu do perfil -->
                    <div class="dropdown-menu dropdown-menu-end header-dropdown-menu p-3">

                        <div class="border-bottom pb-3 mb-2">
                            <h6 class="mb-1">
                                <?= esc($nome) ?>
                            </h6>

                            <p class="mb-0 fs-12 text-muted">
                                <?= esc($email) ?>
                            </p>
                        </div>

                        <a
                            class="dropdown-item"
                            href="<?= site_url('auth/google/link') ?>">

                            <i class="bi bi-google me-2"></i>
                            Vincular conta Google
                        </a>

                        <div class="dropdown-divider"></div>

                        <!-- Logout: mantém a rota atual do Shield -->
                        <form
                            action="<?= url_to('logout') ?>"
                            method="GET">

                            <?= csrf_field() ?>

                            <button
                                type="submit"
                                class="dropdown-item text-danger">

                                <i class="bi bi-box-arrow-right me-2"></i>
                                Sair do sistema
                            </button>
                        </form>

                    </div>

                </div>

            </div>
        </div>
    </div>
</header>
