<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos - Sistema Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .badge-stock {
            font-size: 11px;
            font-weight: 700;
            padding: 4px 8px;
            border-radius: 999px;
            display: inline-block;
            line-height: 1;
        }
        .badge-sin-stock { background: #dc3545; color: #fff; }
        .badge-stock-bajo { background: #ffc107; color: #212529; }
        .badge-stock-justo { background: #0dcaf0; color: #212529; }
        .badge-stock-normal { background: #198754; color: #fff; }

        .fila-sin-stock { background-color: #ead0d2; }
        .fila-stock-bajo { background-color: #eadfbb; }
        .fila-stock-justo { background-color: #d7eff5; }

        .tabla-productos thead th {
            vertical-align: middle;
            white-space: nowrap;
        }

        .tabla-productos tbody td {
            vertical-align: middle;
        }

        .card-filtros {
            border: 1px solid #d8e9ef;
            background: #f8fcfd;
        }
    </style>
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
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h2 mb-1">Productos</h1>
            <p class="text-muted mb-0">Administración de productos de stock</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-secondary">Volver</a>
            <a href="<?= base_url('productos/create') ?>" class="btn btn-primary">Nuevo producto</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="alert alert-info border mb-3">
        <strong>Referencia de stock:</strong>
        <span class="badge-stock badge-sin-stock">Sin stock</span>
        <span class="badge-stock badge-stock-bajo">Stock bajo</span>
        <span class="badge-stock badge-stock-justo">Stock justo</span>
        <span class="badge-stock badge-stock-normal">Stock normal</span>
    </div>

    <div class="card card-filtros shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="get" action="<?= base_url('productos') ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="nombre" class="form-label fw-semibold">Buscar por nombre</label>
                        <input
                            type="text"
                            name="nombre"
                            id="nombre"
                            class="form-control"
                            placeholder="Ej: Crianza Perro Ad"
                            value="<?= esc($filtros['nombre'] ?? '') ?>"
                        >
                    </div>

                    <div class="col-md-4">
                        <label for="molino" class="form-label fw-semibold">Filtrar por molino</label>
                        <select name="molino" id="molino" class="form-select">
                            <option value="">Todos los molinos</option>
                            <?php foreach (($molinos ?? []) as $item): ?>
                                <option value="<?= esc($item['molino']) ?>" <?= (($filtros['molino'] ?? '') === ($item['molino'] ?? '')) ? 'selected' : '' ?>>
                                    <?= esc($item['molino']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                            <a href="<?= base_url('productos') ?>" class="btn btn-outline-secondary w-100">Limpiar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table tabla-productos table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="px-3 py-3">ID</th>
                        <th class="py-3">Categoría</th>
                        <th class="py-3">Nombre</th>
                        <th class="py-3">Molino</th>
                        <th class="py-3">Tipo</th>
                        <th class="py-3">Kg</th>
                        <th class="py-3">Bolsas<br>por pallet</th>
                        <th class="py-3">Stock<br>actual</th>
                        <th class="py-3">Stock<br>mínimo</th>
                        <th class="py-3">Estado<br>stock</th>
                        <th class="py-3">Pallets<br>actuales</th>
                        <th class="py-3">Fecha alta</th>
                        <th class="py-3 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($productos)): ?>
                        <?php foreach ($productos as $producto): ?>
                            <?php
                                $estado = $producto['estado_stock'] ?? 'stock_normal';
                                $filaClass = match ($estado) {
                                    'sin_stock'   => 'fila-sin-stock',
                                    'stock_bajo'  => 'fila-stock-bajo',
                                    'stock_justo' => 'fila-stock-justo',
                                    default       => '',
                                };

                                $badgeClass = match ($estado) {
                                    'sin_stock'   => 'badge-sin-stock',
                                    'stock_bajo'  => 'badge-stock-bajo',
                                    'stock_justo' => 'badge-stock-justo',
                                    default       => 'badge-stock-normal',
                                };

                                $estadoTexto = match ($estado) {
                                    'sin_stock'   => 'Sin stock',
                                    'stock_bajo'  => 'Stock bajo',
                                    'stock_justo' => 'Stock justo',
                                    default       => 'Stock normal',
                                };
                            ?>
                            <tr class="<?= $filaClass ?>">
                                <td class="px-3"><?= esc($producto['id']) ?></td>
                                <td><?= esc($producto['categoria_nombre'] ?? '-') ?></td>
                                <td><?= esc($producto['nombre'] ?? '-') ?></td>
                                <td><?= esc($producto['molino'] ?? '-') ?></td>
                                <td><?= esc($producto['tipo'] ?? '-') ?></td>
                                <td><?= number_format((float) ($producto['kilogramos'] ?? 0), 2, ',', '.') ?></td>
                                <td><?= esc($producto['bolsas_por_pallet'] ?? 0) ?></td>
                                <td class="fw-semibold"><?= esc($producto['stock_unidades'] ?? 0) ?></td>
                                <td><?= esc($producto['stock_minimo'] ?? 0) ?></td>
                                <td>
                                    <span class="badge-stock <?= $badgeClass ?>"><?= esc($estadoTexto) ?></span>
                                </td>
                                <td><?= number_format((float) ($producto['pallets_actuales'] ?? 0), 2, ',', '.') ?></td>
                                <td>
                                    <?php if (!empty($producto['created_at'])): ?>
                                        <?= esc(date('Y-m-d H:i:s', strtotime($producto['created_at']))) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if (session('rol') === 'admin'): ?>
                                        <a href="<?= base_url('productos/edit/' . $producto['id']) ?>" class="btn btn-outline-primary btn-sm">
                                            Editar
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="13" class="text-center py-4">No se encontraron productos con los filtros aplicados.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
