<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear usuario - Sistema Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-secondary">

<nav class="navbar navbar-expand-lg bg-dark navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('dashboard') ?>">Sistema Stock</a>
        <div class="ms-auto d-flex align-items-center gap-3 text-white">
            <span><?= esc(session('nombre')) ?> (<?= esc(session('rol')) ?>)</span>
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm">Salir</a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="fw-bold mb-1">Nuevo usuario</h2>
                            <p class="text-muted mb-0">Crear admin, vendedor o consultor</p>
                        </div>
                        <a href="<?= base_url('usuarios') ?>" class="btn btn-outline-secondary">Volver</a>
                    </div>

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

                    <form action="<?= base_url('usuarios/store') ?>" method="post">
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

                        <div class="mb-4">
                            <label for="rol" class="form-label">Rol</label>
                            <select class="form-select" id="rol" name="rol" required>
                                <option value="">Seleccionar rol</option>
                                <option value="admin" <?= old('rol') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                <option value="vendedor" <?= old('rol') === 'vendedor' ? 'selected' : '' ?>>Vendedor</option>
                                <option value="consultor" <?= old('rol') === 'consultor' ? 'selected' : '' ?>>Consultor</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            Guardar usuario
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>