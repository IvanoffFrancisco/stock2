<?php

namespace App\Controllers;

class Metricas extends BaseController
{
    public function index()
    {
        if (!session()->get('isLoggedIn') || session('rol') !== 'admin') {
            return redirect()->to('/dashboard')->with('error', 'No tenés permisos para ver métricas.');
        }

        $db = \Config\Database::connect();

        $desde = $this->request->getGet('desde');
        $hasta = $this->request->getGet('hasta');
        $filtroRapido = $this->request->getGet('filtro_rapido');

        switch ($filtroRapido) {
            case 'hoy':
                $desde = date('Y-m-d');
                $hasta = date('Y-m-d');
                break;

            case '7dias':
                $desde = date('Y-m-d', strtotime('-6 days'));
                $hasta = date('Y-m-d');
                break;

            case '30dias':
                $desde = date('Y-m-d', strtotime('-29 days'));
                $hasta = date('Y-m-d');
                break;

            case 'mes':
                $desde = date('Y-m-01');
                $hasta = date('Y-m-t');
                break;

            case 'mes_anterior':
                $desde = date('Y-m-01', strtotime('-1 month'));
                $hasta = date('Y-m-t', strtotime('-1 month'));
                break;

            case 'anio':
                $desde = date('Y-01-01');
                $hasta = date('Y-12-31');
                break;
        }

        if (!$desde || !$hasta) {
            $desde = date('Y-m-01');
            $hasta = date('Y-m-t');
            $filtroRapido = 'mes';
        }

        // KPIs del período filtrado
        $kpi = $db->table('ventas')
            ->select('COUNT(*) AS cantidad_ventas, COALESCE(SUM(total),0) AS total_facturado, COALESCE(AVG(total),0) AS ticket_promedio')
            ->where('fecha_venta >=', $desde)
            ->where('fecha_venta <=', $hasta)
            ->get()
            ->getRowArray();

        // Ventas por vendedor
        $ventasPorVendedor = $db->table('ventas')
            ->select('usuarios.id, usuarios.nombre, COUNT(ventas.id) AS cantidad_ventas, COALESCE(SUM(ventas.total),0) AS total_vendido, COALESCE(AVG(ventas.total),0) AS ticket_promedio')
            ->join('usuarios', 'usuarios.id = ventas.usuario_id')
            ->where('ventas.fecha_venta >=', $desde)
            ->where('ventas.fecha_venta <=', $hasta)
            ->groupBy('usuarios.id, usuarios.nombre')
            ->orderBy('total_vendido', 'DESC')
            ->get()
            ->getResultArray();

        $topVendedor = !empty($ventasPorVendedor) ? $ventasPorVendedor[0] : null;

        // Ventas por molino
        $ventasPorMolino = $db->table('venta_detalles')
            ->select('productos.molino, 
                      COALESCE(SUM(venta_detalles.cantidad),0) AS bolsas_vendidas,
                      COALESCE(SUM(venta_detalles.subtotal),0) AS total_vendido,
                      COUNT(DISTINCT venta_detalles.venta_id) AS cantidad_ventas')
            ->join('productos', 'productos.id = venta_detalles.producto_id')
            ->join('ventas', 'ventas.id = venta_detalles.venta_id')
            ->where('productos.molino IS NOT NULL')
            ->where('productos.molino !=', '')
            ->where('ventas.fecha_venta >=', $desde)
            ->where('ventas.fecha_venta <=', $hasta)
            ->groupBy('productos.molino')
            ->orderBy('total_vendido', 'DESC')
            ->get()
            ->getResultArray();

        $topMolino = !empty($ventasPorMolino) ? $ventasPorMolino[0] : null;

        // Histórico por vendedor
        $ventasPorVendedorHistorico = $db->table('ventas')
            ->select('usuarios.id, usuarios.nombre, COUNT(ventas.id) AS cantidad_ventas, COALESCE(SUM(ventas.total),0) AS total_vendido')
            ->join('usuarios', 'usuarios.id = ventas.usuario_id')
            ->groupBy('usuarios.id, usuarios.nombre')
            ->orderBy('total_vendido', 'DESC')
            ->get()
            ->getResultArray();

        // Top productos por cantidad
        $topProductosCantidad = $db->table('venta_detalles')
            ->select('productos.id,
                      productos.nombre,
                      productos.molino,
                      productos.stock_unidades,
                      productos.stock_minimo,
                      categorias.nombre AS categoria_nombre,
                      COALESCE(SUM(venta_detalles.cantidad),0) AS bolsas_vendidas,
                      COUNT(DISTINCT venta_detalles.venta_id) AS cantidad_ventas,
                      COALESCE(SUM(venta_detalles.subtotal),0) AS total_vendido')
            ->join('productos', 'productos.id = venta_detalles.producto_id')
            ->join('categorias', 'categorias.id = productos.categoria_id', 'left')
            ->join('ventas', 'ventas.id = venta_detalles.venta_id')
            ->where('ventas.fecha_venta >=', $desde)
            ->where('ventas.fecha_venta <=', $hasta)
            ->groupBy('productos.id, productos.nombre, productos.molino, productos.stock_unidades, productos.stock_minimo, categorias.nombre')
            ->orderBy('bolsas_vendidas', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Top productos por facturación
        $topProductosFacturacion = $db->table('venta_detalles')
            ->select('productos.id,
                      productos.nombre,
                      productos.molino,
                      productos.stock_unidades,
                      productos.stock_minimo,
                      categorias.nombre AS categoria_nombre,
                      COALESCE(SUM(venta_detalles.subtotal),0) AS total_vendido,
                      COALESCE(SUM(venta_detalles.cantidad),0) AS bolsas_vendidas,
                      COUNT(DISTINCT venta_detalles.venta_id) AS cantidad_ventas')
            ->join('productos', 'productos.id = venta_detalles.producto_id')
            ->join('categorias', 'categorias.id = productos.categoria_id', 'left')
            ->join('ventas', 'ventas.id = venta_detalles.venta_id')
            ->where('ventas.fecha_venta >=', $desde)
            ->where('ventas.fecha_venta <=', $hasta)
            ->groupBy('productos.id, productos.nombre, productos.molino, productos.stock_unidades, productos.stock_minimo, categorias.nombre')
            ->orderBy('total_vendido', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        // Estado visual de stock para top productos
        foreach ($topProductosCantidad as &$producto) {
            $stock = (int) ($producto['stock_unidades'] ?? 0);
            $minimo = (int) ($producto['stock_minimo'] ?? 0);

            if ($stock <= 0) {
                $producto['estado_stock'] = 'sin_stock';
            } elseif ($stock <= $minimo) {
                $producto['estado_stock'] = 'stock_bajo';
            } elseif ($stock <= ($minimo + 5)) {
                $producto['estado_stock'] = 'stock_justo';
            } else {
                $producto['estado_stock'] = 'stock_normal';
            }
        }
        unset($producto);

        foreach ($topProductosFacturacion as &$producto) {
            $stock = (int) ($producto['stock_unidades'] ?? 0);
            $minimo = (int) ($producto['stock_minimo'] ?? 0);

            if ($stock <= 0) {
                $producto['estado_stock'] = 'sin_stock';
            } elseif ($stock <= $minimo) {
                $producto['estado_stock'] = 'stock_bajo';
            } elseif ($stock <= ($minimo + 5)) {
                $producto['estado_stock'] = 'stock_justo';
            } else {
                $producto['estado_stock'] = 'stock_normal';
            }
        }
        unset($producto);

        return view('metricas/index', [
            'kpi'                        => $kpi,
            'ventasPorVendedor'          => $ventasPorVendedor,
            'topVendedor'                => $topVendedor,
            'ventasPorMolino'            => $ventasPorMolino,
            'topMolino'                  => $topMolino,
            'ventasPorVendedorHistorico' => $ventasPorVendedorHistorico,
            'topProductosCantidad'       => $topProductosCantidad,
            'topProductosFacturacion'    => $topProductosFacturacion,
            'desde'                      => $desde,
            'hasta'                      => $hasta,
            'filtroRapido'               => $filtroRapido,
        ]);
    }
}