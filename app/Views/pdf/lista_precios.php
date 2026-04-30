<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de precios</title>
    <style>
        @page {
            margin: 20px 22px 24px;
        }

        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #172033;
        }

        .header-table,
        .filters-table,
        .prices-table,
        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table {
            margin-bottom: 12px;
        }

        .brand-cell {
            width: 34%;
            vertical-align: middle;
        }

        .title-cell {
            width: 32%;
            text-align: center;
            vertical-align: middle;
        }

        .date-cell {
            width: 34%;
            text-align: right;
            vertical-align: middle;
            font-size: 10px;
            color: #4b5563;
        }

        .logo {
            max-width: 70px;
            max-height: 55px;
            vertical-align: middle;
        }

        .brand-name {
            display: inline-block;
            margin-left: 8px;
            font-size: 15px;
            font-weight: bold;
            vertical-align: middle;
        }

        .title {
            font-size: 19px;
            font-weight: bold;
            letter-spacing: 0;
            color: #0b2545;
        }

        .filters-box {
            border: 1px solid #cfd7e3;
            background: #f7f9fc;
            padding: 8px 10px;
            margin-bottom: 12px;
        }

        .filters-table td {
            width: 33.33%;
            padding: 2px 4px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            color: #0b2545;
        }

        .prices-table th,
        .prices-table td {
            border: 1px solid #b8c2d1;
            padding: 6px 7px;
        }

        .prices-table th {
            background: #0b2545;
            color: #fff;
            font-size: 9px;
            text-transform: uppercase;
        }

        .mill-row td {
            background: #dce6f3;
            color: #0b2545;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            padding: 7px;
        }

        .product-name {
            font-weight: bold;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .empty {
            color: #6b7280;
            text-align: center;
        }

        .observations {
            border: 1px solid #cfd7e3;
            background: #f7f9fc;
            margin-top: 12px;
            padding: 8px 10px;
            font-size: 10px;
        }

        .footer-table {
            margin-top: 14px;
            color: #6b7280;
            font-size: 9px;
        }

        .footer-table td {
            border-top: 1px solid #d7dde6;
            padding-top: 7px;
        }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="brand-cell">
                <?php if (!empty($logoPath)): ?>
                    <img class="logo" src="<?= esc($logoPath, 'attr') ?>" alt="Logo">
                    <span class="brand-name"><?= esc($empresaNombre ?? 'GP') ?></span>
                <?php else: ?>
                    <span class="brand-name"><?= esc($empresaNombre ?? 'GP') ?></span>
                <?php endif; ?>
            </td>
            <td class="title-cell">
                <div class="title">LISTA DE PRECIOS</div>
            </td>
            <td class="date-cell">
                <span class="label">Fecha de emision:</span> <?= esc($fechaDocumento ?? date('d/m/Y')) ?>
            </td>
        </tr>
    </table>

    <div class="filters-box">
        <table class="filters-table">
            <tr>
                <td><span class="label">Molino:</span> <?= esc($filtros['molino'] ?: 'Todos') ?></td>
                <td><span class="label">Categoria:</span> <?= esc($filtros['categoria'] ?: 'Todas') ?></td>
                <td><span class="label">Producto:</span> <?= esc($filtros['buscar'] ?: 'Todos') ?></td>
            </tr>
        </table>
    </div>

    <table class="prices-table">
        <thead>
            <tr>
                <th style="width:43%;">Producto</th>
                <th style="width:19%;">+10 bolsas</th>
                <th style="width:19%;">Hasta 50 bolsas</th>
                <th style="width:19%;">+50 bolsas</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($preciosAgrupados)): ?>
                <?php foreach ($preciosAgrupados as $grupo): ?>
                    <tr class="mill-row">
                        <td colspan="4"><?= esc($grupo['molino'] ?? 'Sin molino') ?></td>
                    </tr>

                    <?php foreach (($grupo['productos'] ?? []) as $producto): ?>
                        <tr>
                            <td class="product-name"><?= esc($producto['producto_nombre'] ?? '-') ?></td>
                            <td class="text-right">
                                <?= $producto['mas_10'] !== null ? '$ ' . number_format((float) $producto['mas_10'], 2, ',', '.') : '-' ?>
                            </td>
                            <td class="text-right">
                                <?= $producto['hasta_50'] !== null ? '$ ' . number_format((float) $producto['hasta_50'], 2, ',', '.') : '-' ?>
                            </td>
                            <td class="text-right">
                                <?= $producto['mas_50'] !== null ? '$ ' . number_format((float) $producto['mas_50'], 2, ',', '.') : '-' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" class="empty">No se encontraron precios con los filtros aplicados.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="observations">
        <span class="label">Observaciones:</span> Precios expresados en pesos. Sujetos a cambios sin previo aviso.
    </div>

    <table class="footer-table">
        <tr>
            <td>Lista de precios generada por Sistema Stock</td>
            <td class="text-right"><?= esc($empresaNombre ?? 'GP') ?></td>
        </tr>
    </table>

</body>
</html>
