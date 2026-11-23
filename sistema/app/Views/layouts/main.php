<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <title><?= esc($title ?? 'Dashboard | DISU') ?></title>

    <link rel="icon" type="image/png" href="<?= base_url('assets/images/disu-logo.png') ?>">
    <!-- Configuração inicial do Urbix -->
    <script type="module"
        src="<?= base_url('assets/js/layout-setup.js') ?>">
    </script>

    <!-- CSS Urbix -->
    <link rel="stylesheet"
        href="<?= base_url('assets/libs/simplebar/simplebar.min.css') ?>">

    <link rel="stylesheet"
        href="<?= base_url('assets/css/bootstrap.min.css') ?>"
        id="bootstrap-style">

    <link rel="stylesheet"
        href="<?= base_url('assets/css/icons.min.css') ?>">

    <link rel="stylesheet"
        href="<?= base_url('assets/css/app.min.css') ?>"
        id="app-style">

    <?= $this->renderSection('styles') ?>
</head>

<body>

    <div id="layout-wrapper">

        <?= $this->include('layouts/header') ?>

        <?= $this->include('layouts/sidebar') ?>

        <div class="sidebar-backdrop"
            id="sidebar-backdrop"></div>

        <main class="app-wrapper">
            <div class="container-fluid">

                <?php if (session('message')): ?>
                    <div class="alert alert-success mt-3"
                        role="status">
                        <?= esc(session('message')) ?>
                    </div>
                <?php endif; ?>

                <?php if (session('error')): ?>
                    <div class="alert alert-danger mt-3"
                        role="alert">
                        <?= esc(session('error')) ?>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>

            </div>
        </main>

        <?= $this->include('layouts/footer') ?>

    </div>

    <!-- JavaScript Urbix -->
    <script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>

    <script src="<?= base_url('assets/libs/simplebar/simplebar.min.js') ?>"></script>

    <script src="<?= base_url('assets/js/scroll-top.init.js') ?>"></script>

    <script type="module"
        src="<?= base_url('assets/js/app.js') ?>">
    </script>

    <script src="<?= base_url('assets/js/disu.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>

</body>

</html>
