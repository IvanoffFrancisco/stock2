<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas - Sistema Stock</title>
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
            <h1 class="h3 mb-1">Ventas</h1>
            <p class="text-muted mb-0">
                <?php if (session('rol') === 'admin'): ?>
                    Visualización de todas las ventas del sistema
                <?php else: ?>
                    Visualización de tus ventas generadas
                <?php endif; ?>
            </p>
        </div>
        <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">
            Volver
        </a>
    </div>

    <div class="card border-0 shadow rounded-4 mb-4">
        <div class="card-body">
            <form method="get" action="<?= base_url('ventas') ?>">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label class="form-label">Fecha desde</label>
                        <input type="date" name="fecha_desde" class="form-control" value="<?= esc($filtros['fecha_desde'] ?? '') ?>">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Fecha hasta</label>
                        <input type="date" name="fecha_hasta" class="form-control" value="<?= esc($filtros['fecha_hasta'] ?? '') ?>">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Cliente</label>
                        <input type="text" name="cliente" class="form-control" placeholder="Buscar por cliente" value="<?= esc($filtros['cliente'] ?? '') ?>">
                    </div>

                    <?php if (session('rol') === 'admin'): ?>
                        <div class="col-md-2">
                            <label class="form-label">Vendedor</label>
                            <select name="vendedor" class="form-select">
                                <option value="">Todos</option>
                                <?php foreach ($vendedores as $vend): ?>
                                    <option value="<?= esc($vend['id']) ?>" <?= (($filtros['vendedor'] ?? '') == $vend['id']) ? 'selected' : '' ?>>
                                        <?= esc($vend['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>

                    <div class="col-md-2">
                        <label class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">Todos</option>
                            <option value="entregado" <?= (($filtros['estado'] ?? '') === 'entregado') ? 'selected' : '' ?>>Entregado</option>
                        </select>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-dark w-100">Filtrar</button>
                    </div>

                    <div class="col-md-12">
                        <a href="<?= base_url('ventas') ?>" class="btn btn-outline-secondary btn-sm">Limpiar filtros</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>Importante:</strong> las ventas se generan desde pedidos ya descontados. Esta pantalla no realiza movimientos de stock; solo refleja pedidos entregados transformados en venta.
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
                            <th class="py-3">Cliente</th>
                            <th class="py-3">Vendedor</th>
                            <th class="py-3">Fecha venta</th>
                            <th class="py-3">Fecha entrega</th>
                            <th class="py-3">Forma de pago</th>
                            <th class="py-3">Estado entrega</th>
                            <th class="py-3">Subtotal</th>
                            <th class="py-3">Descuento</th>
                            <th class="py-3">Total</th>
                            <th class="py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($ventas)): ?>
                            <?php foreach ($ventas as $venta): ?>
                                <tr>
                                    <td class="px-4"><?= esc($venta['id']) ?></td>
                                    <td><?= esc($venta['cliente_nombre']) ?></td>
                                    <td><?= esc($venta['vendedor_nombre']) ?></td>
                                    <td><?= esc($venta['fecha_venta'] ?: '-') ?></td>
                                    <td><?= esc($venta['fecha_entrega'] ?: '-') ?></td>
                                    <td><?= esc($venta['forma_pago'] ?: '-') ?></td>
                                    <td>
                                        <?php
                                            $estado = $venta['estado_entrega'];
                                            $badgeClass = match ($estado) {
                                                'entregado' => 'bg-success',
                                                default => 'bg-secondary',
                                            };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= esc(ucfirst($estado)) ?>
                                        </span>
                                    </td>
                                    <td>$ <?= number_format((float) $venta['subtotal'], 2, ',', '.') ?></td>
                                    <td>$ <?= number_format((float) $venta['descuento'], 2, ',', '.') ?></td>
                                    <td>$ <?= number_format((float) $venta['total'], 2, ',', '.') ?></td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            <a href="<?= base_url('ventas/show/' . $venta['id']) ?>" class="btn btn-sm btn-outline-dark">
                                                Ver
                                            </a>
                                            <a href="<?= base_url('ventas/pdf/' . $venta['id']) ?>" target="_blank" class="btn btn-sm btn-outline-danger">
                                                PDF
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    No hay ventas registradas.
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