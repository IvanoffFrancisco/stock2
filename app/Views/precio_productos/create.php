<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= ($modo ?? 'crear') === 'editar' ? 'Editar precio' : 'Nuevo precio' ?> - Sistema Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-secondary">

<?php
    $esEdicion = ($modo ?? 'crear') === 'editar';
    $precioData = $precio ?? null;

    $productoSeleccionado = old('producto_id', $precioData['producto_id'] ?? '');
    $cantidadDesde = old('cantidad_desde', $precioData['cantidad_desde'] ?? '');
    $cantidadHasta = old('cantidad_hasta', $precioData['cantidad_hasta'] ?? '');
    $precioUnitarioActual = $precioData['precio_unitario'] ?? null;

    $tipoEdicion = old('tipo_edicion', 'final');
    $precioFinal = old('precio_final', $precioUnitarioActual);
    $ajusteMonto = old('ajuste_monto', '');
    $ajustePorcentaje = old('ajuste_porcentaje', '');
?>

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
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="fw-bold mb-1"><?= $esEdicion ? 'Editar precio' : 'Nuevo precio' ?></h2>
                            <p class="text-muted mb-0">
                                <?= $esEdicion ? 'Modificar precio por valor final, monto o porcentaje' : 'Crear precio por rango de cantidad' ?>
                            </p>
                        </div>
                        <a href="<?= base_url('precio-productos') ?>" class="btn btn-outline-secondary">Volver</a>
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

                    <form action="<?= $esEdicion ? base_url('precio-productos/update/' . $precioData['id']) : base_url('precio-productos/store') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="producto_id" class="form-label">Producto</label>
                            <select class="form-select" id="producto_id" name="producto_id" required>
                                <option value="">Seleccionar producto</option>
                                <?php foreach ($productos as $producto): ?>
                                    <option value="<?= esc($producto['id']) ?>" <?= (string)$productoSeleccionado === (string)$producto['id'] ? 'selected' : '' ?>>
                                        <?= esc($producto['nombre']) ?> - <?= esc($producto['kilogramos']) ?> kg - Molino: <?= esc($producto['molino'] ?? '-') ?> (<?= esc($producto['categoria_nombre']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="cantidad_desde" class="form-label">Cantidad desde</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="cantidad_desde"
                                    name="cantidad_desde"
                                    value="<?= esc($cantidadDesde) ?>"
                                    placeholder="Ej: 1"
                                    required
                                >
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="cantidad_hasta" class="form-label">Cantidad hasta</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="cantidad_hasta"
                                    name="cantidad_hasta"
                                    value="<?= esc($cantidadHasta) ?>"
                                    placeholder="Ej: 10"
                                >
                                <div class="form-text">Dejalo vacío si es sin límite.</div>
                            </div>

                            <?php if (!$esEdicion): ?>
                                <div class="col-md-4 mb-3">
                                    <label for="precio_unitario" class="form-label">Precio unitario</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        class="form-control"
                                        id="precio_unitario"
                                        name="precio_unitario"
                                        value="<?= old('precio_unitario') ?>"
                                        placeholder="Ej: 13000"
                                        required
                                    >
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($esEdicion): ?>
                            <hr class="my-4">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Precio actual</label>
                                <div class="form-control bg-light">
                                    $ <?= number_format((float) $precioUnitarioActual, 2, ',', '.') ?>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="tipo_edicion" class="form-label">Forma de actualización</label>
                                <select class="form-select" id="tipo_edicion" name="tipo_edicion" required>
                                    <option value="final" <?= $tipoEdicion === 'final' ? 'selected' : '' ?>>Definir precio final</option>
                                    <option value="monto" <?= $tipoEdicion === 'monto' ? 'selected' : '' ?>>Sumar / restar monto fijo</option>
                                    <option value="porcentaje" <?= $tipoEdicion === 'porcentaje' ? 'selected' : '' ?>>Aumentar / disminuir por porcentaje</option>
                                </select>
                            </div>

                            <div id="bloque_final" class="mb-3">
                                <label for="precio_final" class="form-label">Nuevo precio final</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control"
                                    id="precio_final"
                                    name="precio_final"
                                    value="<?= esc($precioFinal) ?>"
                                    placeholder="Ej: 14500"
                                >
                                <div class="form-text">Ingresá directamente el precio que querés dejar.</div>
                            </div>

                            <div id="bloque_monto" class="mb-3">
                                <label for="ajuste_monto" class="form-label">Ajuste por monto</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control"
                                    id="ajuste_monto"
                                    name="ajuste_monto"
                                    value="<?= esc($ajusteMonto) ?>"
                                    placeholder="Ej: 1200 o -500"
                                >
                                <div class="form-text">Usá valores positivos o negativos. Ej: +1200 o -500.</div>
                            </div>

                            <div id="bloque_porcentaje" class="mb-3">
                                <label for="ajuste_porcentaje" class="form-label">Ajuste por porcentaje</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    class="form-control"
                                    id="ajuste_porcentaje"
                                    name="ajuste_porcentaje"
                                    value="<?= esc($ajustePorcentaje) ?>"
                                    placeholder="Ej: 10 o -5"
                                >
                                <div class="form-text">Usá porcentaje positivo o negativo. Ej: 10 para subir 10%, -5 para bajar 5%.</div>
                            </div>
                        <?php endif; ?>

                        <button type="submit" class="btn btn-primary w-100 py-2 mt-2">
                            <?= $esEdicion ? 'Guardar cambios' : 'Guardar precio' ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($esEdicion): ?>
<script>
    function actualizarCamposEdicion() {
        const tipo = document.getElementById('tipo_edicion').value;

        const bloqueFinal = document.getElementById('bloque_final');
        const bloqueMonto = document.getElementById('bloque_monto');
        const bloquePorcentaje = document.getElementById('bloque_porcentaje');

        bloqueFinal.style.display = (tipo === 'final') ? 'block' : 'none';
        bloqueMonto.style.display = (tipo === 'monto') ? 'block' : 'none';
        bloquePorcentaje.style.display = (tipo === 'porcentaje') ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        const selectTipo = document.getElementById('tipo_edicion');
        if (selectTipo) {
            actualizarCamposEdicion();
            selectTipo.addEventListener('change', actualizarCamposEdicion);
        }
    });
</script>
<?php endif; ?>

</body>
</html>