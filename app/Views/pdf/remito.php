<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= esc($tituloDocumento) ?></title>
    <style>
        @page {
            margin: 18px 22px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #111;
        }

        .borde {
            border: 1px solid #222;
            border-radius: 12px;
        }

        .mb-10 { margin-bottom: 10px; }
        .mb-14 { margin-bottom: 14px; }
        .p-10 { padding: 10px; }
        .p-12 { padding: 12px; }

        .header-table,
        .info-table,
        .detalle-table,
        .totales-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .logo-box {
            width: 95px;
            text-align: center;
        }

        .logo-box img {
            display: block;
            margin: 0 auto;
            max-width: 85px;
            max-height: 85px;
            width: auto;
            height: auto;
        }

        .empresa-box {
            width: 50%;
            padding-right: 10px;
        }

        .documento-box {
            width: 50%;
            text-align: right;
        }

        .titulo-doc {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .subtitulo {
            font-size: 9px;
            color: #444;
        }

        .numero-doc {
            font-size: 20px;
            font-weight: bold;
            margin: 12px 0 8px;
        }

        .label {
            font-weight: bold;
        }

        .info-table td {
            padding: 3px 0;
            vertical-align: top;
        }

        .detalle-table th,
        .detalle-table td {
            border: 1px solid #333;
            padding: 6px 7px;
        }

        .detalle-table th {
            background: #f2f2f2;
            font-size: 10px;
            text-transform: uppercase;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .totales-table td {
            padding: 6px 8px;
            border: 1px solid #333;
        }

        .totales-table .label-total {
            width: 70%;
            font-weight: bold;
            text-align: right;
            background: #f7f7f7;
        }

        .totales-table .valor-total {
            width: 30%;
            text-align: right;
            font-weight: bold;
        }

        .small {
            font-size: 10px;
        }

        .firma-area {
            margin-top: 34px;
        }

        .firma-linea {
            border-top: 1px solid #333;
            width: 220px;
            margin-top: 26px;
            padding-top: 4px;
            font-size: 10px;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="borde p-12 mb-10">
        <table class="header-table">
            <tr>
                <td class="empresa-box">
                    <table style="width:100%;">
                        <tr>
                            <td class="logo-box">
                                <?php if (!empty($logoPath)): ?>
                                    <img src="<?= esc($logoPath, 'attr') ?>" alt=" ">
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="font-size:18px; font-weight:bold;"><?= esc($empresa['nombre']) ?></div>
                                <div class="small"><?= esc($empresa['direccion']) ?></div>
                                <div class="small">CUIT: <?= esc($empresa['cuit']) ?></div>
                                <div class="small">Tel: <?= esc($empresa['telefono']) ?></div>
                                <div class="small">Email: <?= esc($empresa['email']) ?></div>
                            </td>
                        </tr>
                    </table>
                </td>
                <td class="documento-box">
                    <div class="titulo-doc"><?= esc($tituloDocumento) ?></div>
                    <div class="subtitulo">Comprobante interno</div>
                    <div class="numero-doc">N° <?= esc($numeroDocumento) ?></div>
                    <div><span class="label">Fecha:</span> <?= esc($fechaDocumento) ?></div>
                </td>
            </tr>
        </table>
    </div>

    <div class="borde p-10 mb-10">
        <table class="info-table">
            <tr>
                <td style="width:60%;">
                    <span class="label">Cliente:</span> <?= esc($cliente['nombre'] ?? '-') ?>
                </td>
                <td style="width:40%;">
                    <span class="label">Teléfono:</span> <?= esc($cliente['telefono'] ?? '-') ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Dirección:</span> <?= esc($cliente['direccion'] ?? '-') ?>
                </td>
                <td>
                    <span class="label">Localidad:</span> <?= esc($cliente['localidad'] ?? '-') ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Vendedor:</span> <?= esc($vendedorNombre ?? '-') ?>
                </td>
                <td>
                    <span class="label">Forma de pago:</span> <?= esc($formaPago ?? '-') ?>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="label">Fecha entrega:</span> <?= esc($fechaEntrega ?? '-') ?>
                </td>
                <td>
                    <span class="label">Estado:</span> <?= esc($estado ?? '-') ?>
                </td>
            </tr>
        </table>
    </div>

    <table class="detalle-table mb-14">
        <thead>
            <tr>
                <th style="width:32%;">Detalle</th>
                <th style="width:16%;">Categoría</th>
                <th style="width:8%;">Kg</th>
                <th style="width:10%;">Cantidad</th>
                <th style="width:14%;">P. Unitario</th>
                <th style="width:8%;">Bonif.</th>
                <th style="width:12%;">Importe</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($detalles)): ?>
                <?php foreach ($detalles as $detalle): ?>
                    <tr>
                        <td><?= esc($detalle['producto_nombre'] ?? '-') ?></td>
                        <td><?= esc($detalle['categoria_nombre'] ?? '-') ?></td>
                        <td class="text-center"><?= esc($detalle['kilogramos'] ?? '-') ?></td>
                        <td class="text-center"><?= esc($detalle['cantidad'] ?? 0) ?></td>
                        <td class="text-right">$ <?= number_format((float) ($detalle['precio_unitario'] ?? 0), 2, ',', '.') ?></td>
                        <td class="text-center"><?= ((int) ($detalle['bonificado'] ?? 0) === 1) ? 'Sí' : 'No' ?></td>
                        <td class="text-right">$ <?= number_format((float) ($detalle['subtotal'] ?? 0), 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Sin detalle</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <table class="totales-table">
        <tr>
            <td class="label-total">Subtotal</td>
            <td class="valor-total">$ <?= number_format((float) ($subtotal ?? 0), 2, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label-total">Descuento</td>
            <td class="valor-total">$ <?= number_format((float) ($descuento ?? 0), 2, ',', '.') ?></td>
        </tr>
        <tr>
            <td class="label-total">Total</td>
            <td class="valor-total">$ <?= number_format((float) ($total ?? 0), 2, ',', '.') ?></td>
        </tr>
    </table>

    <div class="firma-area">
        <div class="firma-linea">Firma / Aclaración</div>
    </div>

</body>
</html>