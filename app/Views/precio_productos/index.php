<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Precios de productos - Sistema Stock</title>
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
            <h1 class="h3 mb-1">Precios de productos</h1>
            <p class="text-muted mb-0">Precios por rango de cantidad de bolsas</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Volver</a>
            <a href="<?= base_url('precio-productos/create') ?>" class="btn btn-primary">Nuevo precio</a>
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
                            <th class="py-3">Producto</th>
                            <th class="py-3">Categoría</th>
                            <th class="py-3">Kg</th>
                            <th class="py-3">Cantidad desde</th>
                            <th class="py-3">Cantidad hasta</th>
                            <th class="py-3">Precio unitario</th>
                            <th class="py-3">Fecha alta</th>
                            <th class="py-3 text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($precios)): ?>
                            <?php foreach ($precios as $precio): ?>
                                <tr>
                                    <td class="px-4"><?= esc($precio['id']) ?></td>
                                    <td><?= esc($precio['producto_nombre']) ?></td>
                                    <td><?= esc($precio['categoria_nombre']) ?></td>
                                    <td><?= esc($precio['kilogramos']) ?></td>
                                    <td><?= esc($precio['cantidad_desde']) ?></td>
                                    <td><?= esc($precio['cantidad_hasta'] ?? 'Sin límite') ?></td>
                                    <td>$ <?= number_format((float) $precio['precio_unitario'], 2, ',', '.') ?></td>
                                    <td><?= esc($precio['created_at']) ?></td>
                                    <td class="text-center">
                                        <a href="<?= base_url('precio-productos/edit/' . $precio['id']) ?>" class="btn btn-sm btn-warning">
                                            Editar
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    No hay precios cargados.
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