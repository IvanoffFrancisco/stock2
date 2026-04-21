<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg bg-dark navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="#">Sistema Stock</a>
        <div class="ms-auto d-flex align-items-center gap-3 text-white">
            <span><?= esc(session('nombre')) ?> (<?= esc(session('rol')) ?>)</span>
            <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm">Salir</a>
        </div>
    </div>
</nav>

<div class="container py-5">

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div class="card border-0 shadow rounded-4">
        <div class="card-body p-4">

            <h1 class="h3 mb-3">Bienvenido, <?= esc(session('nombre')) ?></h1>

            <p class="text-muted mb-0">
                Has iniciado sesión correctamente.
            </p>

            <p class="mt-2">
                Tu rol actual es:
                <strong><?= esc(session('rol')) ?></strong>
            </p>

            <div class="row g-3 mt-3">

                <?php if (session('rol') === 'admin'): ?>

                    <div class="col-md-4">
                        <div class="card border-danger h-100">
                            <div class="card-body">
                                <h5 class="card-title">Usuarios</h5>
                                <p class="card-text text-muted">
                                    Gestiona usuarios y permisos del sistema.
                                </p>
                                <a href="<?= base_url('usuarios') ?>" class="btn btn-danger">
                                    Ir a usuarios
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-primary h-100">
                            <div class="card-body">
                                <h5 class="card-title">Categorías</h5>
                                <p class="card-text text-muted">
                                    Administra categorías de productos.
                                </p>
                                <a href="<?= base_url('categorias') ?>" class="btn btn-primary">
                                    Ir a categorías
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <h5 class="card-title">Productos</h5>
                                <p class="card-text text-muted">
                                    Administra productos y stock.
                                </p>
                                <a href="<?= base_url('productos') ?>" class="btn btn-success">
                                    Ir a productos
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-warning h-100">
                            <div class="card-body">
                                <h5 class="card-title">Precios</h5>
                                <p class="card-text text-muted">
                                    Configura precios por cantidad.
                                </p>
                                <a href="<?= base_url('precio-productos') ?>" class="btn btn-warning">
                                    Ir a precios
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-info h-100">
                            <div class="card-body">
                                <h5 class="card-title">Movimientos</h5>
                                <p class="card-text text-muted">
                                    Control de ingresos y egresos de stock.
                                </p>
                                <a href="<?= base_url('movimientos-stock') ?>" class="btn btn-info text-white">
                                    Ir a movimientos
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-dark h-100">
                            <div class="card-body">
                                <h5 class="card-title">Clientes</h5>
                                <p class="card-text text-muted">
                                    Administración de clientes.
                                </p>
                                <a href="<?= base_url('clientes') ?>" class="btn btn-dark">
                                    Ir a clientes
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-secondary h-100">
                            <div class="card-body">
                                <h5 class="card-title">Pedidos</h5>
                                <p class="card-text text-muted">
                                    Consulta y administra los pedidos de todos los vendedores.
                                </p>
                                <a href="<?= base_url('pedidos') ?>" class="btn btn-secondary">
                                    Ir a pedidos
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-light-subtle h-100">
                            <div class="card-body">
                                <h5 class="card-title">Ventas</h5>
                                <p class="card-text text-muted">
                                    Consulta las ventas generadas a partir de pedidos entregados.
                                </p>
                                <a href="<?= base_url('ventas') ?>" class="btn btn-secondary">
                                    Ir a ventas
                                </a>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>

                <?php if (session('rol') === 'vendedor'): ?>

                    <div class="col-md-4">
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <h5 class="card-title">Clientes</h5>
                                <p class="card-text text-muted">
                                    Consulta y registra clientes.
                                </p>
                                <a href="<?= base_url('clientes') ?>" class="btn btn-success">
                                    Ir a clientes
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-primary h-100">
                            <div class="card-body">
                                <h5 class="card-title">Pedidos</h5>
                                <p class="card-text text-muted">
                                    Carga y gestiona tus pedidos.
                                </p>
                                <a href="<?= base_url('pedidos') ?>" class="btn btn-primary">
                                    Ir a pedidos
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border-success h-100">
                            <div class="card-body">
                                <h5 class="card-title">Ventas</h5>
                                <p class="card-text text-muted">
                                    Consulta tus ventas generadas desde pedidos entregados.
                                </p>
                                <a href="<?= base_url('ventas') ?>" class="btn btn-success">
                                    Ir a ventas
                                </a>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>

                <?php if (session('rol') === 'consultor'): ?>

                    <div class="col-md-4">
                        <div class="card border-secondary h-100">
                            <div class="card-body">
                                <h5 class="card-title">Consultas</h5>
                                <p class="card-text text-muted">
                                    Consulta productos, stock y reportes.
                                </p>
                            </div>
                        </div>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>

</div>

</body>
</html>