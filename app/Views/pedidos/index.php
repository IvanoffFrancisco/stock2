<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - Sistema Stock</title>
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
            <h1 class="h3 mb-1">Pedidos</h1>
            <p class="text-muted mb-0">
                <?php if (session('rol') === 'admin'): ?>
                    Visualización de todos los pedidos del sistema
                <?php else: ?>
                    Visualización de tus pedidos cargados
                <?php endif; ?>
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Volver</a>
            <a href="<?= base_url('pedidos/create') ?>" class="btn btn-primary">Nuevo pedido</a>
        </div>
    </div>

    <div class="card border-0 shadow rounded-4 mb-4">
        <div class="card-body">
            <form method="get" action="<?= base_url('pedidos') ?>">
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
                            <option value="pendiente" <?= (($filtros['estado'] ?? '') === 'pendiente') ? 'selected' : '' ?>>Pendiente</option>
                            <option value="cancelado" <?= (($filtros['estado'] ?? '') === 'cancelado') ? 'selected' : '' ?>>Cancelado</option>
                            <option value="entregado" <?= (($filtros['estado'] ?? '') === 'entregado') ? 'selected' : '' ?>>Entregado</option>
                        </select>
                    </div>

                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-dark w-100">Filtrar</button>
                    </div>

                    <div class="col-md-12">
                        <a href="<?= base_url('pedidos') ?>" class="btn btn-outline-secondary btn-sm">Limpiar filtros</a>
                    </div>
                </div>
            </form>
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

    <div class="alert alert-info">
        <strong>Importante:</strong> los pedidos en estado <strong>pendiente</strong> o <strong>entregado</strong> descuentan stock inmediatamente. Los pedidos <strong>cancelados</strong> devuelven stock.
    </div>

    <div class="card border-0 shadow rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="py-3">Cliente</th>
                            <th class="py-3">Vendedor</th>
                            <th class="py-3">Fecha pedido</th>
                            <th class="py-3">Fecha entrega</th>
                            <th class="py-3">Forma de pago</th>
                            <th class="py-3">Estado</th>
                            <th class="py-3">Subtotal</th>
                            <th class="py-3">Descuento</th>
                            <th class="py-3">Total</th>
                            <th class="py-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($pedidos)): ?>
                            <?php foreach ($pedidos as $pedido): ?>
                                <tr>
                                    <td class="px-4"><?= esc($pedido['id']) ?></td>
                                    <td><?= esc($pedido['cliente_nombre']) ?></td>
                                    <td><?= esc($pedido['vendedor_nombre']) ?></td>
                                    <td><?= esc($pedido['fecha_pedido'] ?? '-') ?></td>
                                    <td><?= esc($pedido['fecha_entrega'] ?? '-') ?></td>
                                    <td><?= esc($pedido['forma_pago'] ?? '-') ?></td>
                                    <td>
                                        <?php
                                            $estado = $pedido['estado'];
                                            $badgeClass = match ($estado) {
                                                'entregado' => 'bg-success',
                                                'cancelado' => 'bg-danger',
                                                default => 'bg-warning text-dark',
                                            };
                                        ?>
                                        <span class="badge <?= $badgeClass ?>">
                                            <?= esc(ucfirst($estado)) ?>
                                        </span>
                                    </td>
                                    <td>$ <?= number_format((float) $pedido['subtotal'], 2, ',', '.') ?></td>
                                    <td>$ <?= number_format((float) $pedido['descuento'], 2, ',', '.') ?></td>
                                    <td>$ <?= number_format((float) $pedido['total'], 2, ',', '.') ?></td>
                                    <td>
                                        <div class="d-flex flex-column gap-2">
                                            <a href="<?= base_url('pedidos/show/' . $pedido['id']) ?>" class="btn btn-sm btn-outline-dark">
                                                Ver
                                            </a>

                                            <?php if ($pedido['estado'] !== 'entregado'): ?>
                                                <a href="<?= base_url('pedidos/edit/' . $pedido['id']) ?>" class="btn btn-sm btn-outline-primary">
                                                    Editar
                                                </a>

                                                <form action="<?= base_url('pedidos/cambiar-estado/' . $pedido['id']) ?>" method="post">
                                                    <?= csrf_field() ?>
                                                    <input type="hidden" name="estado" value="entregado">
                                                    <button type="submit" class="btn btn-sm btn-success w-100">
                                                        Marcar entregado
                                                    </button>
                                                </form>

                                                <?php if ($pedido['estado'] !== 'cancelado'): ?>
                                                    <form action="<?= base_url('pedidos/cambiar-estado/' . $pedido['id']) ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="estado" value="cancelado">
                                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                            Cancelar
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <?php if ($pedido['estado'] === 'cancelado'): ?>
                                                    <form action="<?= base_url('pedidos/cambiar-estado/' . $pedido['id']) ?>" method="post">
                                                        <?= csrf_field() ?>
                                                        <input type="hidden" name="estado" value="pendiente">
                                                        <button type="submit" class="btn btn-sm btn-outline-warning w-100">
                                                            Volver a pendiente
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted small">Pedido entregado</span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="text-center py-4">
                                    No hay pedidos registrados.
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