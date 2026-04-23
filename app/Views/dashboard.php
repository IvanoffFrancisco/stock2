<?php
$db = \Config\Database::connect();

$productosStockBajo = $db->table('productos')
    ->where('stock_unidades <= stock_minimo')
    ->where('stock_unidades >', 0)
    ->countAllResults();

$productosSinStock = $db->table('productos')
    ->where('stock_unidades <=', 0)
    ->countAllResults();
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --bg-main: #f4f7fb;
            --card-border: rgba(15, 23, 42, 0.08);
            --text-main: #1e293b;
            --text-soft: #64748b;
            --shadow-soft: 0 10px 30px rgba(15, 23, 42, 0.08);
            --shadow-hover: 0 18px 40px rgba(15, 23, 42, 0.14);
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

        .hero-card {
            background: linear-gradient(135deg, #ffffff, #f8fbff);
            border: 1px solid rgba(255, 255, 255, 0.75);
            border-radius: 28px;
            box-shadow: var(--shadow-soft);
            overflow: hidden;
            position: relative;
        }

        .hero-card::after {
            content: "";
            position: absolute;
            inset: auto -60px -60px auto;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(13, 110, 253, 0.10), transparent 68%);
        }

        .welcome-pill {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .55rem .9rem;
            border-radius: 999px;
            background: rgba(13, 110, 253, 0.08);
            color: #0d6efd;
            font-weight: 600;
            font-size: .92rem;
        }

        .section-title {
            font-weight: 700;
            letter-spacing: -.02em;
        }

        .dashboard-card {
            background: #fff;
            border: 1px solid var(--card-border);
            border-radius: 22px;
            box-shadow: var(--shadow-soft);
            transition: transform .22s ease, box-shadow .22s ease, border-color .22s ease;
            overflow: hidden;
            height: 100%;
        }

        .dashboard-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
            border-color: rgba(13, 110, 253, 0.18);
        }

        .dashboard-card .card-body {
            padding: 1.35rem;
        }

        .card-icon {
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

        .icon-danger { background: linear-gradient(135deg, #dc3545, #ff6b6b); }
        .icon-primary { background: linear-gradient(135deg, #0d6efd, #4f8cff); }
        .icon-success { background: linear-gradient(135deg, #198754, #33c27f); }
        .icon-warning { background: linear-gradient(135deg, #f59e0b, #fbbf24); }
        .icon-info { background: linear-gradient(135deg, #0dcaf0, #38bdf8); }
        .icon-dark { background: linear-gradient(135deg, #111827, #374151); }
        .icon-secondary { background: linear-gradient(135deg, #64748b, #94a3b8); }

        .card-title {
            font-weight: 700;
            margin-bottom: .55rem;
        }

        .card-text {
            color: var(--text-soft);
            min-height: 48px;
        }

        .btn-soft {
            border-radius: 12px;
            font-weight: 600;
            padding: .7rem 1rem;
        }

        .stat-box {
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(255, 255, 255, 0.72);
            border-radius: 18px;
            padding: 1rem;
            height: 100%;
        }

        .stat-box small {
            color: var(--text-soft);
        }

        .alert {
            border: 0;
            border-radius: 16px;
            box-shadow: var(--shadow-soft);
        }

        @media (max-width: 768px) {
            .hero-card {
                border-radius: 22px;
            }

            .dashboard-card {
                border-radius: 18px;
            }

            .card-text {
                min-height: auto;
            }
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-stock">
    <div class="container py-2">
        <a class="navbar-brand d-flex align-items-center gap-3 fw-semibold" href="#">
            <span class="brand-badge">
                <i class="bi bi-box-seam"></i>
            </span>
            <span>Sistema Stock</span>
        </a>

        <div class="ms-auto d-flex align-items-center gap-3 text-white flex-wrap justify-content-end">
            <div class="text-end">
                <div class="fw-semibold"><?= esc(session('nombre')) ?></div>
                <small class="text-white-50 text-uppercase"><?= esc(session('rol')) ?></small>
            </div>
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3">
                <i class="bi bi-box-arrow-right me-1"></i>Salir
            </a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session('rol') === 'admin' && ($productosStockBajo > 0 || $productosSinStock > 0)): ?>
        <div class="alert alert-warning mb-4">
            <i class="bi bi-exclamation-circle-fill me-2"></i>
            <?php if ($productosSinStock > 0): ?>
                Hay <strong><?= esc($productosSinStock) ?></strong> producto(s) sin stock.
            <?php endif; ?>
            <?php if ($productosStockBajo > 0): ?>
                <?= $productosSinStock > 0 ? ' Además,' : 'Hay' ?>
                <strong><?= esc($productosStockBajo) ?></strong> producto(s) con stock bajo.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="hero-card p-4 p-lg-5 mb-4">
        <div class="row g-4 align-items-center position-relative" style="z-index: 1;">
            <div class="col-lg-8">
                <span class="welcome-pill mb-3">
                    <i class="bi bi-speedometer2"></i>
                    Panel principal
                </span>

                <h1 class="display-6 fw-bold mb-2">Bienvenido, <?= esc(session('nombre')) ?></h1>
                <p class="text-secondary fs-5 mb-2">
                    Gestioná tu sistema de stock de forma más clara, rápida y ordenada.
                </p>
                <p class="text-muted mb-0">
                    Has iniciado sesión correctamente. Tu rol actual es <strong><?= esc(session('rol')) ?></strong>.
                </p>
            </div>

            <div class="col-lg-4">
                <div class="row g-3">
                    <div class="col-6 col-lg-12">
                        <div class="stat-box">
                            <small>Usuario activo</small>
                            <div class="fw-bold fs-5 mt-1"><?= esc(session('nombre')) ?></div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-12">
                        <div class="stat-box">
                            <small>Rol del sistema</small>
                            <div class="fw-bold fs-5 mt-1 text-capitalize"><?= esc(session('rol')) ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
        <div>
            <h2 class="h4 section-title mb-1">Accesos rápidos</h2>
            <p class="text-muted mb-0">Elegí una sección para comenzar.</p>
        </div>
    </div>

    <div class="row g-4">

        <?php if (session('rol') === 'admin'): ?>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-danger"><i class="bi bi-people-fill"></i></div>
                        <h5 class="card-title">Usuarios</h5>
                        <p class="card-text">Gestiona usuarios, accesos y permisos del sistema.</p>
                        <a href="<?= base_url('usuarios') ?>" class="btn btn-danger btn-soft w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a usuarios
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-primary"><i class="bi bi-tags-fill"></i></div>
                        <h5 class="card-title">Categorías</h5>
                        <p class="card-text">Administra las categorías disponibles para los productos.</p>
                        <a href="<?= base_url('categorias') ?>" class="btn btn-primary btn-soft w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a categorías
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-success"><i class="bi bi-box2-heart-fill"></i></div>
                        <h5 class="card-title">Productos</h5>
                        <p class="card-text">Administra productos, detalles y stock disponible.</p>
                        <a href="<?= base_url('productos') ?>" class="btn btn-success btn-soft w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a productos
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-warning"><i class="bi bi-cash-coin"></i></div>
                        <h5 class="card-title">Precios</h5>
                        <p class="card-text">Configura valores, ajustes y precios por cantidad.</p>
                        <a href="<?= base_url('precio-productos') ?>" class="btn btn-warning btn-soft w-100 text-dark">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a precios
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-info"><i class="bi bi-arrow-left-right"></i></div>
                        <h5 class="card-title">Movimientos</h5>
                        <p class="card-text">Controla ingresos y egresos de stock de forma ordenada.</p>
                        <a href="<?= base_url('movimientos-stock') ?>" class="btn btn-info btn-soft w-100 text-white">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a movimientos
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-dark"><i class="bi bi-person-vcard-fill"></i></div>
                        <h5 class="card-title">Clientes</h5>
                        <p class="card-text">Administra y consulta la cartera de clientes.</p>
                        <a href="<?= base_url('clientes') ?>" class="btn btn-dark btn-soft w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a clientes
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-secondary"><i class="bi bi-clipboard-check-fill"></i></div>
                        <h5 class="card-title">Pedidos</h5>
                        <p class="card-text">Consulta y administra los pedidos de todos los vendedores.</p>
                        <a href="<?= base_url('pedidos') ?>" class="btn btn-secondary btn-soft w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a pedidos
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-primary"><i class="bi bi-receipt-cutoff"></i></div>
                        <h5 class="card-title">Ventas</h5>
                        <p class="card-text">Consulta las ventas generadas a partir de pedidos entregados.</p>
                        <a href="<?= base_url('ventas') ?>" class="btn btn-primary btn-soft w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a ventas
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-warning"><i class="bi bi-exclamation-triangle-fill"></i></div>
                        <h5 class="card-title">Stock bajo</h5>
                        <p class="card-text">Productos que están en el límite o por debajo del stock mínimo.</p>
                        <a href="<?= base_url('productos') ?>" class="btn btn-warning btn-soft w-100 text-dark">
                            <i class="bi bi-box2-heart me-1"></i><?= esc($productosStockBajo) ?> producto(s)
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-danger"><i class="bi bi-x-octagon-fill"></i></div>
                        <h5 class="card-title">Sin stock</h5>
                        <p class="card-text">Productos agotados que requieren reposición urgente.</p>
                        <a href="<?= base_url('productos') ?>" class="btn btn-danger btn-soft w-100">
                            <i class="bi bi-box-seam me-1"></i><?= esc($productosSinStock) ?> producto(s)
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-primary"><i class="bi bi-bar-chart-line-fill"></i></div>
                        <h5 class="card-title">Métricas</h5>
                        <p class="card-text">Accedé al dashboard analítico de ventas, vendedores y evolución comercial.</p>
                        <a href="<?= base_url('metricas') ?>" class="btn btn-primary btn-soft w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a métricas
                        </a>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <?php if (session('rol') === 'vendedor'): ?>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-success"><i class="bi bi-people-fill"></i></div>
                        <h5 class="card-title">Clientes</h5>
                        <p class="card-text">Consulta, registra y organiza tus clientes.</p>
                        <a href="<?= base_url('clientes') ?>" class="btn btn-success btn-soft w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a clientes
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-success"><i class="bi bi-box2-heart-fill"></i></div>
                        <h5 class="card-title">Productos</h5>
                        <p class="card-text">Consulta los productos y el stock disponible.</p>
                        <a href="<?= base_url('productos') ?>" class="btn btn-success btn-soft w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a productos
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-warning"><i class="bi bi-cash-coin"></i></div>
                        <h5 class="card-title">Precios</h5>
                        <p class="card-text">Consulta los precios cargados de los productos.</p>
                        <a href="<?= base_url('precio-productos') ?>" class="btn btn-warning btn-soft w-100 text-dark">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a precios
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-primary"><i class="bi bi-bag-check-fill"></i></div>
                        <h5 class="card-title">Pedidos</h5>
                        <p class="card-text">Carga, consulta y gestiona tus pedidos diarios.</p>
                        <a href="<?= base_url('pedidos') ?>" class="btn btn-primary btn-soft w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a pedidos
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-success"><i class="bi bi-currency-dollar"></i></div>
                        <h5 class="card-title">Ventas</h5>
                        <p class="card-text">Consulta tus ventas generadas desde pedidos entregados.</p>
                        <a href="<?= base_url('ventas') ?>" class="btn btn-success btn-soft w-100">
                            <i class="bi bi-arrow-right-circle me-1"></i>Ir a ventas
                        </a>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <?php if (session('rol') === 'consultor'): ?>

            <div class="col-md-6 col-xl-4">
                <div class="dashboard-card">
                    <div class="card-body">
                        <div class="card-icon icon-secondary"><i class="bi bi-search"></i></div>
                        <h5 class="card-title">Consultas</h5>
                        <p class="card-text">Consulta productos, stock y reportes del sistema.</p>
                        <button type="button" class="btn btn-secondary btn-soft w-100" disabled>
                            <i class="bi bi-eye me-1"></i>Solo lectura
                        </button>
                    </div>
                </div>
            </div>

        <?php endif; ?>

    </div>
</div>

</body>
</html>