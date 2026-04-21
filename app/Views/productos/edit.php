<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar producto - Sistema Stock</title>
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
                            <h2 class="fw-bold mb-1">Editar producto</h2>
                            <p class="text-muted mb-0">Solo se pueden modificar categoría, nombre, tipo y kilogramos</p>
                        </div>
                        <a href="<?= base_url('productos') ?>" class="btn btn-outline-secondary">Volver</a>
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

                    <form action="<?= base_url('productos/update/' . $producto['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select" id="categoria_id" name="categoria_id" required>
                                <option value="">Seleccionar categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option
                                        value="<?= esc($categoria['id']) ?>"
                                        <?= old('categoria_id', $producto['categoria_id']) == $categoria['id'] ? 'selected' : '' ?>
                                    >
                                        <?= esc($categoria['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del producto</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nombre"
                                name="nombre"
                                value="<?= old('nombre', $producto['nombre']) ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="tipo" class="form-label">Tipo</label>
                            <input
                                type="text"
                                class="form-control"
                                id="tipo"
                                name="tipo"
                                value="<?= old('tipo', $producto['tipo']) ?>"
                            >
                        </div>

                        <div class="mb-3">
                            <label for="kilogramos" class="form-label">Kilogramos</label>
                            <input
                                type="number"
                                step="0.01"
                                class="form-control"
                                id="kilogramos"
                                name="kilogramos"
                                value="<?= old('kilogramos', $producto['kilogramos']) ?>"
                                required
                            >
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Molino</label>
                                <input type="text" class="form-control" value="<?= esc($producto['molino'] ?? '-') ?>" disabled>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bolsas por pallet</label>
                                <input type="text" class="form-control" value="<?= esc($producto['bolsas_por_pallet']) ?>" disabled>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stock unidades</label>
                                <input type="text" class="form-control" value="<?= esc($producto['stock_unidades']) ?>" disabled>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha alta</label>
                                <input type="text" class="form-control" value="<?= esc($producto['created_at']) ?>" disabled>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 py-2 mt-2">
                            Guardar cambios
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>