<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-secondary">

<div class="container min-vh-100 d-flex align-items-center justify-content-center">
    <div class="card shadow border-0 rounded-4" style="max-width: 460px; width: 100%;">
        <div class="card-body p-4 p-md-5">
            <h2 class="text-center fw-bold mb-2">Crear cuenta</h2>
            <p class="text-center text-muted mb-4">Registrá un nuevo usuario</p>

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

            <form action="<?= base_url('auth/guardarRegistro') ?>" method="post">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input
                        type="text"
                        class="form-control"
                        id="nombre"
                        name="nombre"
                        value="<?= old('nombre') ?>"
                        required
                    >
                </div>

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

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirmar contraseña</label>
                    <input
                        type="password"
                        class="form-control"
                        id="confirm_password"
                        name="confirm_password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-success w-100 py-2">
                    Registrarme
                </button>
            </form>

            <div class="text-center mt-4">
                <span class="text-muted">¿Ya tenés cuenta?</span>
                <a href="<?= base_url('login') ?>" class="text-decoration-none fw-semibold">Iniciar sesión</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>