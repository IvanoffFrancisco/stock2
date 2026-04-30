<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar pedido - Sistema Stock</title>
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
    <div class="card shadow border-0 rounded-4">
        <div class="card-body p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1">Editar pedido #<?= esc($pedido['id']) ?></h2>
                    <p class="text-muted mb-0">Modificar cabecera y detalle del pedido</p>
                </div>
                <a href="<?= base_url('pedidos') ?>" class="btn btn-outline-secondary">Volver</a>
            </div>

            <div class="alert alert-warning">
                <strong>Importante:</strong>
                al actualizar este pedido, el sistema devuelve el stock del detalle anterior y vuelve a aplicar el stock del nuevo detalle, salvo que el pedido quede <strong>cancelado</strong>.
            </div>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

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

            <form action="<?= base_url('pedidos/update/' . $pedido['id']) ?>" method="post" id="formPedido">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cliente_id" class="form-label">Cliente</label>
                        <select class="form-select" id="cliente_id" name="cliente_id" required>
                            <option value="">Seleccionar cliente</option>
                            <?php foreach ($clientes as $cliente): ?>
                                <option value="<?= esc($cliente['id']) ?>" <?= (old('cliente_id', $pedido['cliente_id']) == $cliente['id']) ? 'selected' : '' ?>>
                                    <?= esc($cliente['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="fecha_entrega" class="form-label">Fecha de entrega</label>
                        <input
                            type="date"
                            class="form-control"
                            id="fecha_entrega"
                            name="fecha_entrega"
                            value="<?= old('fecha_entrega', $pedido['fecha_entrega']) ?>"
                        >
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="forma_pago" class="form-label">Forma de pago</label>
                        <select class="form-select" id="forma_pago" name="forma_pago">
                            <option value="">Seleccionar</option>
                            <option value="efectivo" <?= old('forma_pago', $pedido['forma_pago']) === 'efectivo' ? 'selected' : '' ?>>Efectivo</option>
                            <option value="plazo" <?= old('forma_pago', $pedido['forma_pago']) === 'plazo' ? 'selected' : '' ?>>Plazo</option>
                            <option value="cheque" <?= old('forma_pago', $pedido['forma_pago']) === 'cheque' ? 'selected' : '' ?>>Cheque</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-4">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="pendiente" <?= old('estado', $pedido['estado']) === 'pendiente' ? 'selected' : '' ?>>Pendiente</option>
                            <option value="entregado" <?= old('estado', $pedido['estado']) === 'entregado' ? 'selected' : '' ?>>Entregado</option>
                            <option value="cancelado" <?= old('estado', $pedido['estado']) === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>

                    <div class="col-md-3 mb-4">
                        <label for="descuento" class="form-label">Descuento total</label>
                        <input
                            type="number"
                            step="0.01"
                            class="form-control"
                            id="descuento"
                            name="descuento"
                            value="<?= old('descuento', $pedido['descuento']) ?>"
                        >
                    </div>

                    <div class="col-md-3 mb-4">
                        <label for="lista_precio" class="form-label">Lista</label>
                        <select class="form-select" id="lista_precio" name="lista_precio">
                            <?php foreach (($listasPrecios ?? ['General']) as $lista): ?>
                                <option value="<?= esc($lista) ?>" <?= (old('lista_precio', $pedido['lista_precio'] ?? 'General') === $lista) ? 'selected' : '' ?>>
                                    <?= esc($lista) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Se usa para sugerir precios cuando el precio unitario queda en 0.</div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Detalle del pedido</h4>
                    <button type="button" class="btn btn-primary btn-sm" id="agregarFila">
                        Agregar producto
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="tablaDetalle">
                        <thead class="table-light">
                            <tr>
                                <th style="min-width: 260px;">Producto</th>
                                <th style="width: 120px;">Cantidad</th>
                                <th style="width: 160px;">Precio unitario</th>
                                <th style="width: 120px;">Bonificado</th>
                                <th style="width: 160px;">Subtotal</th>
                                <th style="width: 80px;">Quitar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($detalles as $index => $detalle): ?>
                                <tr>
                                    <td>
                                        <select class="form-select producto-select" name="producto_id[]" required>
                                            <option value="">Seleccionar producto</option>
                                            <?php foreach ($productos as $producto): ?>
                                                <option value="<?= esc($producto['id']) ?>" <?= ((int) $detalle['producto_id'] === (int) $producto['id']) ? 'selected' : '' ?>>
                                                    <?= esc($producto['nombre']) ?> - <?= esc($producto['kilogramos']) ?> kg (<?= esc($producto['categoria_nombre']) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="cantidad[]" class="form-control cantidad-input" min="1" value="<?= esc($detalle['cantidad']) ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="precio_unitario[]" class="form-control precio-input" value="<?= esc($detalle['precio_unitario']) ?>">
                                        <div class="form-text">0 = usar precio de la lista seleccionada</div>
                                    </td>
                                    <td class="text-center">
                                        <input type="checkbox" name="bonificado[<?= $index ?>]" class="form-check-input bonificado-input" <?= ((int) $detalle['bonificado'] === 1) ? 'checked' : '' ?>>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control subtotal-linea" value="<?= esc(number_format((float) $detalle['subtotal'], 2, '.', '')) ?>" readonly>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm quitarFila">X</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="row mt-4">
                    <div class="col-md-8 mb-3">
                        <label for="observacion" class="form-label">Observación</label>
                        <textarea
                            class="form-control"
                            id="observacion"
                            name="observacion"
                            rows="4"
                            placeholder="Observaciones generales del pedido"
                        ><?= old('observacion', $pedido['observacion']) ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h5 class="card-title">Resumen</h5>
                                <p class="mb-2">Subtotal estimado: <strong id="subtotalGeneral">$ 0,00</strong></p>
                                <p class="mb-0">Total estimado: <strong id="totalGeneral">$ 0,00</strong></p>
                                <small class="text-muted">Si dejás precio en 0, se usa el precio de la lista seleccionada. La cantidad no define la lista.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-success w-100 py-2 mt-4">
                    Actualizar pedido
                </button>
            </form>
        </div>
    </div>
</div>

<script>
const precioSugeridoUrl = "<?= base_url('pedidos/precio-sugerido') ?>";

const productosHtml = `
<option value="">Seleccionar producto</option>
<?php foreach ($productos as $producto): ?>
<option value="<?= esc($producto['id']) ?>">
    <?= esc($producto['nombre']) ?> - <?= esc($producto['kilogramos']) ?> kg (<?= esc($producto['categoria_nombre']) ?>)
</option>
<?php endforeach; ?>
`;

let bonificadoIndex = <?= count($detalles) ?>;

function formatearMoneda(valor) {
    return '$ ' + valor.toLocaleString('es-AR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

async function obtenerPrecioSugeridoBackend(productoId, cantidad, listaPrecio) {
    if (!productoId) {
        return 0;
    }

    const params = new URLSearchParams({
        producto_id: productoId,
        cantidad: cantidad,
        lista_precio: listaPrecio || 'General'
    });

    try {
        const respuesta = await fetch(`${precioSugeridoUrl}?${params.toString()}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        if (!respuesta.ok) {
            return 0;
        }

        const data = await respuesta.json();
        return data && data.ok ? parseFloat(data.precio || 0) : 0;
    } catch (error) {
        console.error('Error buscando precio sugerido:', error);
        return 0;
    }
}

async function autocompletarPrecioFila(fila, forzar = false) {
    const listaSelect = document.getElementById('lista_precio');
    const productoSelect = fila.querySelector('.producto-select');
    const cantidadInput = fila.querySelector('.cantidad-input');
    const precioInput = fila.querySelector('.precio-input');
    const bonificadoInput = fila.querySelector('.bonificado-input');

    if (!productoSelect || !cantidadInput || !precioInput) {
        return;
    }

    if (bonificadoInput && bonificadoInput.checked) {
        precioInput.value = '0.00';
        precioInput.dataset.auto = '1';
        recalcularTotales();
        return;
    }

    const precioActual = parseFloat(precioInput.value || 0);
    const usaPrecioAutomatico = forzar || precioInput.dataset.auto === '1' || precioActual <= 0;

    if (!usaPrecioAutomatico) {
        recalcularTotales();
        return;
    }

    const productoId = productoSelect.value;
    const cantidad = parseInt(cantidadInput.value || 0);
    const listaPrecio = listaSelect ? listaSelect.value : 'General';

    // La cantidad se envía solo por compatibilidad con la ruta,
    // pero el precio sugerido se busca por producto + lista.
    const precio = await obtenerPrecioSugeridoBackend(productoId, cantidad, listaPrecio);
    precioInput.value = precio > 0 ? precio.toFixed(2) : '0.00';
    precioInput.dataset.auto = '1';

    recalcularTotales();
}

async function autocompletarTodasLasFilas(forzar = false) {
    const filas = document.querySelectorAll('#tablaDetalle tbody tr');
    for (const fila of filas) {
        await autocompletarPrecioFila(fila, forzar);
    }
}

function recalcularTotales() {
    let subtotalGeneral = 0;
    const filas = document.querySelectorAll('#tablaDetalle tbody tr');

    filas.forEach((fila) => {
        const cantidadInput = fila.querySelector('.cantidad-input');
        const precioInput = fila.querySelector('.precio-input');
        const bonificadoInput = fila.querySelector('.bonificado-input');
        const subtotalLineaInput = fila.querySelector('.subtotal-linea');

        const cantidad = parseFloat(cantidadInput.value || 0);
        const precio = parseFloat(precioInput.value || 0);
        const bonificado = bonificadoInput.checked;

        const subtotal = bonificado ? 0 : (cantidad * precio);

        subtotalLineaInput.value = subtotal.toFixed(2);
        subtotalGeneral += subtotal;
    });

    const descuento = parseFloat(document.getElementById('descuento').value || 0);
    const totalGeneral = Math.max(0, subtotalGeneral - descuento);

    document.getElementById('subtotalGeneral').textContent = formatearMoneda(subtotalGeneral);
    document.getElementById('totalGeneral').textContent = formatearMoneda(totalGeneral);
}

document.getElementById('agregarFila').addEventListener('click', function () {
    const tbody = document.querySelector('#tablaDetalle tbody');
    const tr = document.createElement('tr');

    tr.innerHTML = `
        <td>
            <select class="form-select producto-select" name="producto_id[]" required>
                ${productosHtml}
            </select>
        </td>
        <td>
            <input type="number" name="cantidad[]" class="form-control cantidad-input" min="1" value="1" required>
        </td>
        <td>
            <input type="number" step="0.01" name="precio_unitario[]" class="form-control precio-input" value="0.00" data-auto="1">
            <div class="form-text">0 = usar precio de la lista seleccionada</div>
        </td>
        <td class="text-center">
            <input type="checkbox" name="bonificado[${bonificadoIndex}]" class="form-check-input bonificado-input">
        </td>
        <td>
            <input type="text" class="form-control subtotal-linea" value="0.00" readonly>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm quitarFila">X</button>
        </td>
    `;

    bonificadoIndex++;
    tbody.appendChild(tr);
    recalcularTotales();
});

document.addEventListener('input', function (e) {
    if (e.target.classList.contains('precio-input')) {
        const valor = parseFloat(e.target.value || 0);
        e.target.dataset.auto = valor > 0 ? '0' : '1';

        if (valor <= 0) {
            autocompletarPrecioFila(e.target.closest('tr'), true);
            return;
        }

        recalcularTotales();
        return;
    }

    if (e.target.classList.contains('cantidad-input')) {
        autocompletarPrecioFila(e.target.closest('tr'));
        return;
    }

    if (e.target.id === 'descuento') {
        recalcularTotales();
    }
});

document.addEventListener('change', function (e) {
    if (e.target.classList.contains('producto-select')) {
        autocompletarPrecioFila(e.target.closest('tr'), true);
        return;
    }

    if (e.target.id === 'lista_precio') {
        autocompletarTodasLasFilas(false);
        return;
    }

    if (e.target.classList.contains('bonificado-input')) {
        autocompletarPrecioFila(e.target.closest('tr'), true);
    }
});

document.addEventListener('click', function (e) {
    if (e.target.classList.contains('quitarFila')) {
        const filas = document.querySelectorAll('#tablaDetalle tbody tr');
        if (filas.length > 1) {
            e.target.closest('tr').remove();
            recalcularTotales();
        }
    }
});

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.precio-input').forEach((input) => {
        const valor = parseFloat(input.value || 0);
        input.dataset.auto = valor > 0 ? '0' : '1';
    });

    recalcularTotales();
});
</script>

</body>
</html>