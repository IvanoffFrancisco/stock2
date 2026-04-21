<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle pedido #<?= esc($pedido['id']) ?></title>
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
            <h1 class="h3 mb-1">Pedido #<?= esc($pedido['id']) ?></h1>
            <p class="text-muted mb-0">Detalle completo del pedido</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('pedidos') ?>" class="btn btn-outline-secondary">Volver</a>

            <a href="<?= base_url('pedidos/pdf/' . $pedido['id']) ?>" target="_blank" class="btn btn-danger">
                PDF
            </a>

            <?php if ($pedido['estado'] !== 'entregado'): ?>
                <a href="<?= base_url('pedidos/edit/' . $pedido['id']) ?>" class="btn btn-primary">Editar</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card border-0 shadow rounded-4">
                <div class="card-body">
                    <h5 class="mb-3">Datos generales</h5>

                    <p class="mb-2"><strong>Cliente:</strong> <?= esc($pedido['cliente_nombre']) ?></p>
                    <p class="mb-2"><strong>Teléfono:</strong> <?= esc($pedido['telefono'] ?? '-') ?></p>
                    <p class="mb-2"><strong>Dirección:</strong> <?= esc($pedido['direccion'] ?? '-') ?></p>
                    <p class="mb-2"><strong>Localidad:</strong> <?= esc($pedido['localidad'] ?? '-') ?></p>
                    <p class="mb-2"><strong>Vendedor:</strong> <?= esc($pedido['vendedor_nombre']) ?></p>
                    <p class="mb-2"><strong>Fecha pedido:</strong> <?= esc($pedido['fecha_pedido'] ?? '-') ?></p>
                    <p class="mb-2"><strong>Fecha entrega:</strong> <?= esc($pedido['fecha_entrega'] ?? '-') ?></p>
                    <p class="mb-2"><strong>Forma de pago:</strong> <?= esc($pedido['forma_pago'] ?? '-') ?></p>

                    <p class="mb-2">
                        <strong>Estado:</strong>
                        <?php
                            $estado = $pedido['estado'];
                            $badgeClass = match ($estado) {
                                'entregado' => 'bg-success',
                                'cancelado' => 'bg-danger',
                                default => 'bg-warning text-dark',
                            };
                        ?>
                        <span class="badge <?= $badgeClass ?>"><?= esc(ucfirst($estado)) ?></span>
                    </p>

                    <p class="mb-2"><strong>Observación:</strong> <?= esc($pedido['observacion'] ?? '-') ?></p>
                </div>
            </div>

            <div class="card border-0 shadow rounded-4 mt-4">
                <div class="card-body">
                    <h5 class="mb-3">Resumen económico</h5>
                    <p class="mb-2"><strong>Subtotal:</strong> $ <?= number_format((float) ($pedido['subtotal'] ?? 0), 2, ',', '.') ?></p>
                    <p class="mb-2"><strong>Descuento:</strong> $ <?= number_format((float) ($pedido['descuento'] ?? 0), 2, ',', '.') ?></p>
                    <p class="mb-0"><strong>Total:</strong> $ <?= number_format((float) ($pedido['total'] ?? 0), 2, ',', '.') ?></p>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card border-0 shadow rounded-4">
                <div class="card-body p-0">
                    <div class="p-4 pb-0">
                        <h5>Detalle de productos</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="px-4 py-3">Producto</th>
                                    <th class="py-3">Categoría</th>
                                    <th class="py-3">Kg</th>
                                    <th class="py-3">Cantidad</th>
                                    <th class="py-3">P. unitario</th>
                                    <th class="py-3">Bonificado</th>
                                    <th class="py-3">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($detalles as $detalle): ?>
                                    <tr>
                                        <td class="px-4"><?= esc($detalle['producto_nombre'] ?? '-') ?></td>
                                        <td><?= esc($detalle['categoria_nombre'] ?? '-') ?></td>
                                        <td><?= esc($detalle['kilogramos'] ?? '-') ?></td>
                                        <td><?= esc($detalle['cantidad'] ?? 0) ?></td>
                                        <td>$ <?= number_format((float) ($detalle['precio_unitario'] ?? 0), 2, ',', '.') ?></td>
                                        <td>
                                            <?php if ((int) ($detalle['bonificado'] ?? 0) === 1): ?>
                                                <span class="badge bg-success">Sí</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>$ <?= number_format((float) ($detalle['subtotal'] ?? 0), 2, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>

                                <?php if (empty($detalles)): ?>
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Este pedido no tiene detalle.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>