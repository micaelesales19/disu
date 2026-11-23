<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>DISU Contabilidade | Sistema</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <meta content="DISU CONTABILIDADE" name="description" />
    <meta content="Pixeleyez" name="author" />

    <!-- layout setup -->
    <script type="module" src="<?php echo base_url('assets/js/layout-setup.js'); ?>"></script>

    <!-- App favicon -->
    <link rel="shortcut icon" href="<?php echo base_url('assets/images/favicon.png'); ?>"> <!-- Simplebar Css -->
    <link rel="stylesheet" href="<?php echo base_url('assets/libs/simplebar/simplebar.min.css'); ?>">
    <!-- Swiper Css -->
    <link href="<?php echo base_url('assets/libs/swiper/swiper-bundle.min.css'); ?>" rel="stylesheet">
    <!-- Nouislider Css -->
    <link href="<?php echo base_url('assets/libs/nouislider/nouislider.min.css'); ?>" rel="stylesheet">
    <!-- Bootstrap Css -->
    <link href="<?php echo base_url('assets/css/bootstrap.min.css'); ?>" id="bootstrap-style" rel="stylesheet" type="text/css">
    <!--icons css-->
    <link href="<?php echo base_url('assets/css/icons.min.css'); ?>" rel="stylesheet" type="text/css">
    <!-- App Css-->
    <link href="<?php echo base_url('assets/css/app.min.css'); ?>" id="app-style" rel="stylesheet" type="text/css">
</head>

<body>
    <!-- START -->
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
                        <h3 class="fw-medium text-center">Bem-vindo de volta!</h3>
                        <p class="mb-8 text-muted text-center">Crie sua conta em minutos</p>

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

                        <form action="<?= url_to('login') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email')) ?>" placeholder="Digite seu E-mail" autocomplete="username" required>
                            </div>
                            <div class="mb-4">
                                <label for="password" class="form-label">Senha <span class="text-danger">*</span></label>
                                <div class="position-relative">
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Digite sua senha" autocomplete="current-password" required>

                                    <button type="button" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted toggle-password" id="toggle-password" data-target="password">
                                        <i class="ri-eye-off-line align-middle"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="my-6">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                                        <label class="form-check-label" for="remember">Lembrar-me</label>
                                    </div>
                                    <div class="form-text" style="display: none;">
                                        <?php if (setting('Auth.allowMagicLinkLogins')): ?>
                                            <div class="text-center mt-3">
                                                <a href="<?= url_to('magic-link') ?>"
                                                    class="text-decoration-none">
                                                    Esqueceu sua senha?
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary w-100 mb-4">Entrar</button>
                                <a href="<?= site_url('auth/google') ?>"
                                    class="btn btn-outline-light w-100 d-flex align-items-center gap-2 justify-content-center text-muted">
                                    <img
                                        src="<?= base_url('assets/images/google.png') ?>"
                                        alt="Google Logo"
                                        class="h-20px w-20px">
                                    Entrar com Google
                                </a>
                            </div>
                        </form>
                        <!-- <p class="text-center mt-6 mb-0 text-muted fs-13">Não tem uma conta? <a href="auth-signup.html" class="link fw-semibold">Cadastre-se aqui</a></p> -->
                    </div>
                </div>
                <p class="position-relative text-center fs-13 mb-0">©
                    <script>
                        document.write(new Date().getFullYear())
                    </script> DISU | CONTABILIDADE
                </p>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script src="<?php echo base_url('assets/libs/swiper/swiper-bundle.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/libs/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/libs/simplebar/simplebar.min.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/scroll-top.init.js'); ?>"></script>
    <script src="<?php echo base_url('assets/js/auth/auth.init.js'); ?>"></script>
</body>

</html>
