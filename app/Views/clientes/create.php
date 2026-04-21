<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($modo === 'editar') ? 'Editar cliente' : 'Nuevo cliente' ?> - Sistema Stock</title>
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
        <div class="col-lg-7">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="fw-bold mb-1">
                                <?= ($modo === 'editar') ? 'Editar cliente' : 'Nuevo cliente' ?>
                            </h2>
                            <p class="text-muted mb-0">
                                <?= ($modo === 'editar') ? 'Modificar datos del cliente seleccionado' : 'Registrar un cliente para futuros pedidos' ?>
                            </p>
                        </div>
                        <a href="<?= base_url('clientes') ?>" class="btn btn-outline-secondary">Volver</a>
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

                    <?php
                        $esEditar = ($modo === 'editar' && !empty($cliente));
                        $action = $esEditar
                            ? base_url('clientes/update/' . $cliente['id'])
                            : base_url('clientes/store');
                    ?>

                    <form action="<?= $action ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del cliente</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nombre"
                                name="nombre"
                                value="<?= old('nombre', $cliente['nombre'] ?? '') ?>"
                                placeholder="Ej: Juan Pérez / Pet Shop Central"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input
                                type="text"
                                class="form-control"
                                id="telefono"
                                name="telefono"
                                value="<?= old('telefono', $cliente['telefono'] ?? '') ?>"
                                placeholder="Ej: 3624-123456"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <input
                                type="text"
                                class="form-control"
                                id="direccion"
                                name="direccion"
                                value="<?= old('direccion', $cliente['direccion'] ?? '') ?>"
                                placeholder="Ej: Av. Principal 123"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="localidad" class="form-label">Localidad</label>
                            <input
                                type="text"
                                class="form-control"
                                id="localidad"
                                name="localidad"
                                value="<?= old('localidad', $cliente['localidad'] ?? '') ?>"
                                placeholder="Ej: Resistencia"
                            >
                        </div>

                        <div class="mb-4">
                            <label for="observacion" class="form-label">Observación</label>
                            <textarea
                                class="form-control"
                                id="observacion"
                                name="observacion"
                                rows="4"
                                placeholder="Datos adicionales del cliente"
                            ><?= old('observacion', $cliente['observacion'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            <?= $esEditar ? 'Actualizar cliente' : 'Guardar cliente' ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>