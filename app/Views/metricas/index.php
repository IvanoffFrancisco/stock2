<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Métricas - Sistema Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #f4f7fb;
            --card-border: rgba(15, 23, 42, 0.08);
            --text-main: #1e293b;
            --text-soft: #64748b;
            --shadow-soft: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        body {
            background:
                radial-gradient(circle at top left, rgba(13, 110, 253, 0.10), transparent 28%),
                radial-gradient(circle at top right, rgba(25, 135, 84, 0.08), transparent 24%),
                var(--bg-main);
            color: var(--text-main);
        }

        .navbar-stock {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.18);
        }

        .brand-badge {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb, #0ea5e9);
            color: #fff;
            font-size: 1.1rem;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.35);
        }

        .page-card {
            background: linear-gradient(135deg, #ffffff, #f8fbff);
            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 28px;
            box-shadow: var(--shadow-soft);
        }

        .metric-card {
            background: #fff;
            border: 1px solid var(--card-border);
            border-radius: 22px;
            box-shadow: var(--shadow-soft);
            height: 100%;
        }

        .metric-card .card-body {
            padding: 1.35rem;
        }

        .metric-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.45rem;
            color: #fff;
            margin-bottom: 1rem;
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.14);
        }

        .icon-primary { background: linear-gradient(135deg, #0d6efd, #4f8cff); }
        .icon-success { background: linear-gradient(135deg, #198754, #33c27f); }
        .icon-warning { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .icon-dark { background: linear-gradient(135deg, #111827, #374151); }
        .icon-info { background: linear-gradient(135deg, #0dcaf0, #38bdf8); }
        .icon-secondary { background: linear-gradient(135deg, #64748b, #94a3b8); }
        .icon-danger { background: linear-gradient(135deg, #dc3545, #ff6b6b); }

        .table-card {
            background: #fff;
            border: 1px solid var(--card-border);
            border-radius: 22px;
            box-shadow: var(--shadow-soft);
            overflow: hidden;
        }

        .section-title {
            font-weight: 700;
            letter-spacing: -.02em;
        }

        .filter-card {
            background: #fff;
            border: 1px solid var(--card-border);
            border-radius: 22px;
            box-shadow: var(--shadow-soft);
        }

        .badge-stock {
            font-size: .78rem;
            padding: .45rem .65rem;
            border-radius: 999px;
            font-weight: 600;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-stock">
    <div class="container py-2">
        <a class="navbar-brand d-flex align-items-center gap-3 fw-semibold" href="<?= base_url('dashboard') ?>">
            <span class="brand-badge">
                <i class="bi bi-bar-chart-line-fill"></i>
            </span>
            <span>Sistema Stock</span>
        </a>

        <div class="ms-auto d-flex align-items-center gap-3 text-white flex-wrap justify-content-end">
            <div class="text-end">
                <div class="fw-semibold"><?= esc(session('nombre')) ?></div>
                <small class="text-white-50 text-uppercase"><?= esc(session('rol')) ?></small>
            </div>
            <a href="<?= base_url('dashboard') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-left me-1"></i>Volver
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="page-card p-4 p-lg-5 mb-4">
        <h1 class="h3 mb-2 fw-bold">Dashboard de métricas</h1>
        <p class="text-muted mb-0">
            Ventas por vendedor, ventas por molino y top productos del período seleccionado.
        </p>
    </div>

    <div class="filter-card p-4 mb-4">
        <h2 class="h5 section-title mb-3"><i class="bi bi-funnel-fill me-2"></i>Filtros</h2>
        <form method="get" action="<?= base_url('metricas') ?>">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Filtro rápido</label>
                    <select name="filtro_rapido" class="form-select">
                        <option value="">Seleccionar</option>
                        <option value="hoy" <?= ($filtroRapido ?? '') === 'hoy' ? 'selected' : '' ?>>Hoy</option>
                        <option value="7dias" <?= ($filtroRapido ?? '') === '7dias' ? 'selected' : '' ?>>Últimos 7 días</option>
                        <option value="30dias" <?= ($filtroRapido ?? '') === '30dias' ? 'selected' : '' ?>>Últimos 30 días</option>
                        <option value="mes" <?= ($filtroRapido ?? '') === 'mes' ? 'selected' : '' ?>>Mes actual</option>
                        <option value="mes_anterior" <?= ($filtroRapido ?? '') === 'mes_anterior' ? 'selected' : '' ?>>Mes anterior</option>
                        <option value="anio" <?= ($filtroRapido ?? '') === 'anio' ? 'selected' : '' ?>>Año actual</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Desde</label>
                    <input type="date" name="desde" class="form-control" value="<?= esc($desde ?? '') ?>">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Hasta</label>
                    <input type="date" name="hasta" class="form-control" value="<?= esc($hasta ?? '') ?>">
                </div>

                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-dark w-100">
                        <i class="bi bi-search me-1"></i>Filtrar
                    </button>
                    <a href="<?= base_url('metricas') ?>" class="btn btn-outline-secondary w-100">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="alert alert-info mb-4">
        <strong>Período analizado:</strong> <?= esc($desde ?? '-') ?> al <?= esc($hasta ?? '-') ?>
    </div>

    <div class="mb-4">
        <h2 class="h5 section-title mb-3">Resumen del período</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="metric-card">
                    <div class="card-body">
                        <div class="metric-icon icon-success"><i class="bi bi-cash-stack"></i></div>
                        <h5 class="mb-2 fw-bold">Facturación total</h5>
                        <div class="fs-3 fw-bold">$ <?= number_format((float) ($kpi['total_facturado'] ?? 0), 2, ',', '.') ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="metric-card">
                    <div class="card-body">
                        <div class="metric-icon icon-primary"><i class="bi bi-receipt"></i></div>
                        <h5 class="mb-2 fw-bold">Cantidad de ventas</h5>
                        <div class="fs-3 fw-bold"><?= esc($kpi['cantidad_ventas'] ?? 0) ?></div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="metric-card">
                    <div class="card-body">
                        <div class="metric-icon icon-warning"><i class="bi bi-graph-up-arrow"></i></div>
                        <h5 class="mb-2 fw-bold">Ticket promedio</h5>
                        <div class="fs-3 fw-bold">$ <?= number_format((float) ($kpi['ticket_promedio'] ?? 0), 2, ',', '.') ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if ($topVendedor): ?>
        <div class="metric-card mb-4">
            <div class="card-body">
                <div class="metric-icon icon-dark"><i class="bi bi-trophy-fill"></i></div>
                <h5 class="mb-2 fw-bold">Top vendedor del período</h5>
                <p class="mb-1"><strong><?= esc($topVendedor['nombre']) ?></strong></p>
                <p class="mb-1 text-muted">Ventas: <?= esc($topVendedor['cantidad_ventas']) ?></p>
                <p class="mb-0 text-muted">Total vendido: $ <?= number_format((float) $topVendedor['total_vendido'], 2, ',', '.') ?></p>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($topMolino): ?>
        <div class="metric-card mb-4">
            <div class="card-body">
                <div class="metric-icon icon-warning"><i class="bi bi-building-fill"></i></div>
                <h5 class="mb-2 fw-bold">Top molino del período</h5>
                <p class="mb-1"><strong><?= esc($topMolino['molino']) ?></strong></p>
                <p class="mb-1 text-muted">Bolsas vendidas: <?= esc($topMolino['bolsas_vendidas']) ?></p>
                <p class="mb-0 text-muted">Total vendido: $ <?= number_format((float) $topMolino['total_vendido'], 2, ',', '.') ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="table-card mb-4">
        <div class="p-4 pb-0">
            <h2 class="h5 section-title fw-bold"><i class="bi bi-people-fill me-2"></i>Ventas por vendedor</h2>
            <p class="text-muted mb-3">Resultado del período filtrado.</p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="py-3">Vendedor</th>
                        <th class="py-3">Cantidad de ventas</th>
                        <th class="py-3">Total vendido</th>
                        <th class="py-3">Ticket promedio</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ventasPorVendedor)): ?>
                        <?php foreach ($ventasPorVendedor as $index => $fila): ?>
                            <tr>
                                <td class="px-4"><?= $index + 1 ?></td>
                                <td><strong><?= esc($fila['nombre']) ?></strong></td>
                                <td><?= esc($fila['cantidad_ventas']) ?></td>
                                <td>$ <?= number_format((float) $fila['total_vendido'], 2, ',', '.') ?></td>
                                <td>$ <?= number_format((float) $fila['ticket_promedio'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">No hay ventas en este período.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-card mb-4">
        <div class="p-4 pb-0">
            <h2 class="h5 section-title fw-bold"><i class="bi bi-building-fill me-2"></i>Ventas por molino</h2>
            <p class="text-muted mb-3">Resultado del período filtrado.</p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="py-3">Molino</th>
                        <th class="py-3">Cantidad de ventas</th>
                        <th class="py-3">Bolsas vendidas</th>
                        <th class="py-3">Total vendido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ventasPorMolino)): ?>
                        <?php foreach ($ventasPorMolino as $index => $fila): ?>
                            <tr>
                                <td class="px-4"><?= $index + 1 ?></td>
                                <td><strong><?= esc($fila['molino']) ?></strong></td>
                                <td><?= esc($fila['cantidad_ventas']) ?></td>
                                <td><?= esc($fila['bolsas_vendidas']) ?></td>
                                <td>$ <?= number_format((float) $fila['total_vendido'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">No hay datos por molino en este período.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-card mb-4">
        <div class="p-4 pb-0">
            <h2 class="h5 section-title fw-bold"><i class="bi bi-box-seam-fill me-2"></i>Top 10 productos por cantidad</h2>
            <p class="text-muted mb-3">Productos con mayor rotación en el período.</p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="py-3">Producto</th>
                        <th class="py-3">Molino</th>
                        <th class="py-3">Categoría</th>
                        <th class="py-3">Bolsas vendidas</th>
                        <th class="py-3">Ventas</th>
                        <th class="py-3">Total vendido</th>
                        <th class="py-3">Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($topProductosCantidad)): ?>
                        <?php foreach ($topProductosCantidad as $index => $fila): ?>
                            <?php
                                $estadoStock = $fila['estado_stock'] ?? 'stock_normal';
                                $badgeClass = match ($estadoStock) {
                                    'sin_stock'   => 'bg-danger',
                                    'stock_bajo'  => 'bg-warning text-dark',
                                    'stock_justo' => 'bg-info text-dark',
                                    default       => 'bg-success',
                                };
                                $badgeText = match ($estadoStock) {
                                    'sin_stock'   => 'Sin stock',
                                    'stock_bajo'  => 'Stock bajo',
                                    'stock_justo' => 'Stock justo',
                                    default       => 'Normal',
                                };
                            ?>
                            <tr>
                                <td class="px-4"><?= $index + 1 ?></td>
                                <td><strong><?= esc($fila['nombre']) ?></strong></td>
                                <td><?= esc($fila['molino'] ?: '-') ?></td>
                                <td><?= esc($fila['categoria_nombre'] ?: '-') ?></td>
                                <td><?= esc($fila['bolsas_vendidas']) ?></td>
                                <td><?= esc($fila['cantidad_ventas']) ?></td>
                                <td>$ <?= number_format((float) $fila['total_vendido'], 2, ',', '.') ?></td>
                                <td>
                                    <span class="badge <?= $badgeClass ?> badge-stock">
                                        <?= esc($badgeText) ?> (<?= esc($fila['stock_unidades']) ?>)
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">No hay productos vendidos en este período.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-card mb-4">
        <div class="p-4 pb-0">
            <h2 class="h5 section-title fw-bold"><i class="bi bi-currency-dollar me-2"></i>Top 10 productos por facturación</h2>
            <p class="text-muted mb-3">Productos que más ingreso generaron en el período.</p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="py-3">Producto</th>
                        <th class="py-3">Molino</th>
                        <th class="py-3">Categoría</th>
                        <th class="py-3">Total vendido</th>
                        <th class="py-3">Bolsas vendidas</th>
                        <th class="py-3">Ventas</th>
                        <th class="py-3">Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($topProductosFacturacion)): ?>
                        <?php foreach ($topProductosFacturacion as $index => $fila): ?>
                            <?php
                                $estadoStock = $fila['estado_stock'] ?? 'stock_normal';
                                $badgeClass = match ($estadoStock) {
                                    'sin_stock'   => 'bg-danger',
                                    'stock_bajo'  => 'bg-warning text-dark',
                                    'stock_justo' => 'bg-info text-dark',
                                    default       => 'bg-success',
                                };
                                $badgeText = match ($estadoStock) {
                                    'sin_stock'   => 'Sin stock',
                                    'stock_bajo'  => 'Stock bajo',
                                    'stock_justo' => 'Stock justo',
                                    default       => 'Normal',
                                };
                            ?>
                            <tr>
                                <td class="px-4"><?= $index + 1 ?></td>
                                <td><strong><?= esc($fila['nombre']) ?></strong></td>
                                <td><?= esc($fila['molino'] ?: '-') ?></td>
                                <td><?= esc($fila['categoria_nombre'] ?: '-') ?></td>
                                <td>$ <?= number_format((float) $fila['total_vendido'], 2, ',', '.') ?></td>
                                <td><?= esc($fila['bolsas_vendidas']) ?></td>
                                <td><?= esc($fila['cantidad_ventas']) ?></td>
                                <td>
                                    <span class="badge <?= $badgeClass ?> badge-stock">
                                        <?= esc($badgeText) ?> (<?= esc($fila['stock_unidades']) ?>)
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">No hay productos vendidos en este período.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="table-card">
        <div class="p-4 pb-0">
            <h2 class="h5 section-title fw-bold"><i class="bi bi-clock-history me-2"></i>Histórico total por vendedor</h2>
            <p class="text-muted mb-3">Referencia acumulada general, sin filtros de fecha.</p>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="py-3">Vendedor</th>
                        <th class="py-3">Cantidad de ventas</th>
                        <th class="py-3">Total vendido</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($ventasPorVendedorHistorico)): ?>
                        <?php foreach ($ventasPorVendedorHistorico as $index => $fila): ?>
                            <tr>
                                <td class="px-4"><?= $index + 1 ?></td>
                                <td><strong><?= esc($fila['nombre']) ?></strong></td>
                                <td><?= esc($fila['cantidad_ventas']) ?></td>
                                <td>$ <?= number_format((float) $fila['total_vendido'], 2, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">Todavía no hay ventas registradas.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>