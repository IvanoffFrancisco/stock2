<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar categoría - Sistema Stock</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-body-secondary">

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
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="fw-bold mb-1">Editar categoría</h2>
                            <p class="text-muted mb-0">Modificar datos de la categoría</p>
                        </div>
                        <a href="<?= base_url('categorias') ?>" class="btn btn-outline-secondary">Volver</a>
                    </div>

                    <?php $errors = session()->getFlashdata('errors'); ?>
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('categorias/update/' . $categoria['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de la categoría</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nombre"
                                name="nombre"
                                value="<?= old('nombre', $categoria['nombre']) ?>"
                                required
                            >
                        </div>

                        <div class="mb-4">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea
                                class="form-control"
                                id="descripcion"
                                name="descripcion"
                                rows="4"
                            ><?= old('descripcion', $categoria['descripcion']) ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-warning w-100 py-2">
                            Actualizar categoría
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>