<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimientos de stock - Sistema Stock</title>
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
            <h1 class="h3 mb-1">Movimientos de stock</h1>
            <p class="text-muted mb-0">Historial de ingresos, egresos y ajustes</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Volver</a>
            <a href="<?= base_url('movimientos-stock/create') ?>" class="btn btn-primary">Nuevo movimiento</a>
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
                            <th class="py-3">Tipo</th>
                            <th class="py-3">Cantidad</th>
                            <th class="py-3">Motivo</th>
                            <th class="py-3">Observación</th>
                            <th class="py-3">Usuario</th>
                            <th class="py-3">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($movimientos)): ?>
                            <?php foreach ($movimientos as $movimiento): ?>
                                <tr>
                                    <td class="px-4"><?= esc($movimiento['id']) ?></td>
                                    <td><?= esc($movimiento['producto_nombre']) ?></td>
                                    <td><?= esc($movimiento['categoria_nombre']) ?></td>
                                    <td><?= esc($movimiento['kilogramos']) ?></td>
                                    <td>
                                        <?php
                                            $tipo = $movimiento['tipo_movimiento'];
                                            $badgeClass = match ($tipo) {
                                                'ingreso' => 'bg-success',
                                                'egreso'  => 'bg-danger',
                                                default   => 'bg-warning text-dark',
                                            };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= esc(ucfirst($tipo)) ?>
                                        </span>
                                    </td>
                                    <td><?= esc($movimiento['cantidad']) ?></td>
                                    <td><?= esc($movimiento['motivo'] ?: '-') ?></td>
                                    <td><?= esc($movimiento['observacion'] ?: '-') ?></td>
                                    <td><?= esc($movimiento['usuario_nombre']) ?></td>
                                    <td><?= esc($movimiento['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    No hay movimientos registrados.
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