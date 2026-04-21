<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo movimiento - Sistema Stock</title>
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
                            <h2 class="fw-bold mb-1">Nuevo movimiento</h2>
                            <p class="text-muted mb-0">Registrar ingreso, egreso o ajuste de stock</p>
                        </div>
                        <a href="<?= base_url('movimientos-stock') ?>" class="btn btn-outline-secondary">Volver</a>
                    </div>

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

                    <form action="<?= base_url('movimientos-stock/store') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="producto_id" class="form-label">Producto</label>
                            <select class="form-select" id="producto_id" name="producto_id" required>
                                <option value="">Seleccionar producto</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?= esc($producto['id']) ?>" <?= old('producto_id') == $producto['id'] ? 'selected' : '' ?>>
                                        <?= esc($producto['nombre']) ?> - <?= esc($producto['kilogramos']) ?> kg (<?= esc($producto['categoria_nombre']) ?>) | Stock actual: <?= esc($producto['stock_unidades']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="tipo_movimiento" class="form-label">Tipo de movimiento</label>
                                <select class="form-select" id="tipo_movimiento" name="tipo_movimiento" required>
                                    <option value="">Seleccionar tipo</option>
                                    <option value="ingreso" <?= old('tipo_movimiento') === 'ingreso' ? 'selected' : '' ?>>Ingreso</option>
                                    <option value="egreso" <?= old('tipo_movimiento') === 'egreso' ? 'selected' : '' ?>>Egreso</option>
                                    <option value="ajuste" <?= old('tipo_movimiento') === 'ajuste' ? 'selected' : '' ?>>Ajuste</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="cantidad" class="form-label">Cantidad</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="cantidad"
                                    name="cantidad"
                                    value="<?= old('cantidad') ?>"
                                    placeholder="Ej: 50"
                                    required
                                >
                                <div class="form-text">
                                    En ajuste, este valor será el nuevo stock total.
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="motivo" class="form-label">Motivo</label>
                            <input
                                type="text"
                                class="form-control"
                                id="motivo"
                                name="motivo"
                                value="<?= old('motivo') ?>"
                                placeholder="Ej: Reposición de proveedor"
                            >
                        </div>

                        <div class="mb-4">
                            <label for="observacion" class="form-label">Observación</label>
                            <textarea
                                class="form-control"
                                id="observacion"
                                name="observacion"
                                rows="4"
                                placeholder="Detalle adicional del movimiento"
                            ><?= old('observacion') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-2">
                            Guardar movimiento
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>