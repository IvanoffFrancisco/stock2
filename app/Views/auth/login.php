<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-secondary">

<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow border-0 rounded-4" style="max-width: 420px; width: 100%;">
        <div class="card-body p-4 p-md-5">
            <h2 class="text-center fw-bold mb-2">Iniciar sesión</h2>
            <p class="text-center text-muted mb-4">Ingresá a tu sistema de stock</p>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php $errors = session()->getFlashdata('errors'); ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php foreach ($errors as $error): ?>
                            <li><?= esc($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/autenticar') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        value="<?= old('email') ?>"
                        required
                    >
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input
                        type="password"
                        class="form-control"
                        id="password"
                        name="password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2">
                    Ingresar
                </button>
            </form>

            <div class="text-center mt-4">
                <span class="text-muted">¿No tenés cuenta?</span>
                <a href="<?= base_url('register') ?>" class="text-decoration-none fw-semibold">Registrate</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>