<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title>Recuperar acesso | DISU Contabilidade</title>

    <meta name="viewport"
        content="width=device-width, initial-scale=1.0">

    <meta name="description" content="DISU Contabilidade">

    <script type="module"
        src="<?= base_url('assets/js/layout-setup.js') ?>"></script>

    <link rel="shortcut icon"
        href="<?= base_url('assets/images/favicon.png') ?>">

    <link rel="stylesheet"
        href="<?= base_url('assets/libs/simplebar/simplebar.min.css') ?>">

    <link rel="stylesheet"
        href="<?= base_url('assets/libs/swiper/swiper-bundle.min.css') ?>">

    <link rel="stylesheet"
        href="<?= base_url('assets/libs/nouislider/nouislider.min.css') ?>">

    <link rel="stylesheet"
        href="<?= base_url('assets/css/bootstrap.min.css') ?>">

    <link rel="stylesheet"
        href="<?= base_url('assets/css/icons.min.css') ?>">

    <link rel="stylesheet"
        href="<?= base_url('assets/css/app.min.css') ?>">
</head>

<body>

    <!-- FUNDO DO LOGIN -->
    <div class="position-fixed top-0 bottom-0 end-0 start-0 z-0 bg-pattern"></div>
    <div class="auth-pattern-shapes d-none d-lg-block"></div>
    <div class="auth-pattern-outline d-none d-lg-block"></div>
    <div class="auth-pattern-shape extra d-none d-lg-block"></div>
    <div class="auth-pattern-extra d-none d-lg-block"></div>

    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 pt-20 pb-10">

            <div class="col-12 col-md-8 col-lg-6 col-xl-5">

                <div class="card mx-xxl-8 shadow-none">
                    <div class="card-body p-8">

                        <h3 class="fw-medium text-center">
                            Esqueceu sua senha?
                        </h3>

                        <p class="mb-8 text-muted text-center">
                            Informe seu e-mail cadastrado no DISU.
                            Enviaremos um link seguro para você
                            recuperar o acesso à sua conta.
                        </p>

                        <?php if (session('error')): ?>
                            <div class="alert alert-danger" role="alert">
                                <?= esc(session('error')) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session('message')): ?>
                            <div class="alert alert-success" role="status">
                                <?= esc(session('message')) ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session('errors')): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php foreach ((array) session('errors') as $error): ?>
                                    <div><?= esc($error) ?></div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= url_to('magic-link') ?>"
                            method="POST">

                            <?= csrf_field() ?>

                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    E-mail
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    value="<?= esc(old('email')) ?>"
                                    placeholder="Digite seu e-mail"
                                    autocomplete="email"
                                    required>
                            </div>

                            <button type="submit"
                                class="btn btn-primary w-100 my-6">
                                <i class="ri-mail-send-line me-1"></i>
                                Enviar link de acesso
                            </button>

                        </form>

                        <p class="text-center mb-0 text-muted fs-13">
                            Lembrou sua senha?
                            <a href="<?= url_to('login') ?>"
                                class="link fw-semibold">
                                Voltar para o login
                            </a>
                        </p>

                    </div>
                </div>

                <p class="position-relative text-center fs-13 mb-0">
                    © <?= date('Y') ?> DISU | CONTABILIDADE
                </p>

            </div>
        </div>
    </div>

    <script src="<?= base_url('assets/libs/swiper/swiper-bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('assets/libs/simplebar/simplebar.min.js') ?>"></script>
    <script src="<?= base_url('assets/js/scroll-top.init.js') ?>"></script>
    <script src="<?= base_url('assets/js/auth/auth.init.js') ?>"></script>

</body>

</html>
