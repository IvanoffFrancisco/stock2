<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Sistema Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

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
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Productos</h1>
            <p class="text-muted mb-0">Administración de productos de stock</p>
        </div>
        <div class="d-flex gap-2">
    <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Volver</a>

    <?php if (session('rol') === 'admin'): ?>
        <a href="<?= base_url('productos/create') ?>" class="btn btn-primary">Nuevo producto</a>
    <?php endif; ?>
</div>
    </div>

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

    <div class="card border-0 shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="py-3">Categoría</th>
                            <th class="py-3">Nombre</th>
                            <th class="py-3">Molino</th>
                            <th class="py-3">Tipo</th>
                            <th class="py-3">Kg</th>
                            <th class="py-3">Bolsas por pallet</th>
                            <th class="py-3">Stock unidades</th>
                            <th class="py-3">Pallets actuales</th>
                            <th class="py-3">Fecha alta</th>
                            <?php if (session('rol') === 'admin'): ?>
                                <th class="py-3 text-center">Acciones</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($productos)): ?>
                            <?php foreach ($productos as $producto): ?>
                                <tr>
                                    <td class="px-4"><?= esc($producto['id']) ?></td>
                                    <td><?= esc($producto['categoria_nombre']) ?></td>
                                    <td><?= esc($producto['nombre']) ?></td>
                                    <td><?= esc($producto['molino'] ?? '-') ?></td>
                                    <td><?= esc($producto['tipo'] ?: '-') ?></td>
                                    <td><?= esc($producto['kilogramos']) ?></td>
                                    <td><?= esc($producto['bolsas_por_pallet']) ?></td>
                                    <td><?= esc($producto['stock_unidades']) ?></td>
                                    <td><?= esc($producto['pallets_actuales']) ?></td>
                                    <td><?= esc($producto['created_at']) ?></td>
                                    <?php if (session('rol') === 'admin'): ?>
                                        <td class="text-center">
                                            <a href="<?= base_url('productos/edit/' . $producto['id']) ?>" class="btn btn-sm btn-warning">
                                                Editar
                                            </a>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= session('rol') === 'admin' ? '11' : '10' ?>" class="text-center py-4">
                                    No hay productos registrados.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>