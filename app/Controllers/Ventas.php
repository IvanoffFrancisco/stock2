<?php

namespace App\Controllers;

use App\Models\VentaModel;
use App\Models\VentaDetalleModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Ventas extends BaseController
{
    public function index()
{
    $ventaModel = new VentaModel();
    $rol = session('rol');
    $usuarioId = session('id_usuario');

    $fechaDesde = $this->request->getGet('fecha_desde');
    $fechaHasta = $this->request->getGet('fecha_hasta');
    $cliente    = trim((string) $this->request->getGet('cliente'));
    $vendedor   = $this->request->getGet('vendedor');
    $estado     = $this->request->getGet('estado');

    $builder = $ventaModel
        ->select('ventas.*, clientes.nombre AS cliente_nombre, usuarios.nombre AS vendedor_nombre')
        ->join('clientes', 'clientes.id = ventas.cliente_id')
        ->join('usuarios', 'usuarios.id = ventas.usuario_id');

    if ($rol === 'vendedor') {
        $builder->where('ventas.usuario_id', $usuarioId);
    }

    if (!empty($fechaDesde)) {
        $builder->where('ventas.fecha_venta >=', $fechaDesde);
    }

    if (!empty($fechaHasta)) {
        $builder->where('ventas.fecha_venta <=', $fechaHasta);
    }

    if (!empty($cliente)) {
        $builder->like('clientes.nombre', $cliente);
    }

    if ($rol === 'admin' && !empty($vendedor)) {
        $builder->where('ventas.usuario_id', $vendedor);
    }

    if (!empty($estado)) {
        $builder->where('ventas.estado_entrega', $estado);
    }

    $ventas = $builder
        ->orderBy('ventas.id', 'DESC')
        ->findAll();

    $vendedores = [];
    if ($rol === 'admin') {
        $db = \Config\Database::connect();
        $vendedores = $db->table('usuarios')
            ->select('id, nombre')
            ->where('rol', 'vendedor')
            ->orderBy('nombre', 'ASC')
            ->get()
            ->getResultArray();
    }

    return view('ventas/index', [
        'ventas'     => $ventas,
        'vendedores' => $vendedores,
        'filtros'    => [
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
            'cliente'     => $cliente,
            'vendedor'    => $vendedor,
            'estado'      => $estado,
        ],
    ]);
}

    public function show($id = null)
    {
        $ventaModel = new VentaModel();
        $ventaDetalleModel = new VentaDetalleModel();

        $venta = $ventaModel
            ->select('ventas.*, 
                      clientes.nombre AS cliente_nombre,
                      clientes.telefono,
                      clientes.direccion,
                      clientes.localidad,
                      usuarios.nombre AS vendedor_nombre')
            ->join('clientes', 'clientes.id = ventas.cliente_id')
            ->join('usuarios', 'usuarios.id = ventas.usuario_id')
            ->where('ventas.id', $id)
            ->first();

        if (!$venta) {
            return redirect()->to('/ventas')->with('error', 'La venta no existe.');
        }

        if (session('rol') === 'vendedor' && (int) $venta['usuario_id'] !== (int) session('id_usuario')) {
            return redirect()->to('/ventas')->with('error', 'No tienes permisos para ver esta venta.');
        }

        $detalles = $ventaDetalleModel
            ->select('venta_detalles.*, productos.nombre AS producto_nombre, productos.kilogramos, categorias.nombre AS categoria_nombre')
            ->join('productos', 'productos.id = venta_detalles.producto_id')
            ->join('categorias', 'categorias.id = productos.categoria_id')
            ->where('venta_detalles.venta_id', $id)
            ->findAll();

        return view('ventas/show', [
            'venta'    => $venta,
            'detalles' => $detalles,
        ]);
    }
    public function pdf($id = null)
{
    $ventaModel = new VentaModel();
    $ventaDetalleModel = new VentaDetalleModel();

    $venta = $ventaModel
        ->select('ventas.*, clientes.nombre AS cliente_nombre, clientes.telefono, clientes.direccion, clientes.localidad, usuarios.nombre AS vendedor_nombre')
        ->join('clientes', 'clientes.id = ventas.cliente_id')
        ->join('usuarios', 'usuarios.id = ventas.usuario_id')
        ->where('ventas.id', $id)
        ->first();

    if (!$venta) {
        return redirect()->to('/ventas')->with('error', 'La venta no existe.');
    }

    if (session('rol') === 'vendedor' && (int) $venta['usuario_id'] !== (int) session('id_usuario')) {
        return redirect()->to('/ventas')->with('error', 'No tienes permisos para ver esta venta.');
    }

    $detalles = $ventaDetalleModel
        ->select('venta_detalles.*, productos.nombre AS producto_nombre, productos.kilogramos, categorias.nombre AS categoria_nombre')
        ->join('productos', 'productos.id = venta_detalles.producto_id')
        ->join('categorias', 'categorias.id = productos.categoria_id')
        ->where('venta_detalles.venta_id', $id)
        ->findAll();

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Helvetica');
    $options->setChroot(FCPATH);

    $tempDir = WRITEPATH . 'dompdf';
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0777, true);
    }

    $options->setTempDir($tempDir);

    $dompdf = new Dompdf($options);

    $html = view('pdf/remito', [
        'tituloDocumento' => 'REMITO / VENTA',
        'numeroDocumento' => str_pad((string) $venta['id'], 6, '0', STR_PAD_LEFT),
        'fechaDocumento'  => $venta['fecha_venta'] ?? date('Y-m-d'),
        'fechaEntrega'    => $venta['fecha_entrega'] ?? '-',
        'formaPago'       => $venta['forma_pago'] ?? '-',
        'estado'          => ucfirst($venta['estado_entrega'] ?? '-'),
        'vendedorNombre'  => $venta['vendedor_nombre'] ?? '-',
        'cliente' => [
            'nombre'    => $venta['cliente_nombre'] ?? '-',
            'telefono'  => $venta['telefono'] ?? '-',
            'direccion' => $venta['direccion'] ?? '-',
            'localidad' => $venta['localidad'] ?? '-',
        ],
        'detalles'  => $detalles,
        'subtotal'  => $venta['subtotal'] ?? 0,
        'descuento' => $venta['descuento'] ?? 0,
        'total'     => $venta['total'] ?? 0,
        'empresa'   => [
            'nombre'    => 'GP',
            'direccion' => 'Tu dirección',
            'cuit'      => 'Tu CUIT',
            'telefono'  => 'Tu teléfono',
            'email'     => 'Tu email',
        ],
        'logoPath' => $this->obtenerLogoPdf(),
    ]);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    return $this->response
        ->setHeader('Content-Type', 'application/pdf')
        ->setBody($dompdf->output());
}

    private function obtenerLogoPdf(): string
    {
        $rutaLogo = FCPATH . 'img/logo-gp.png';

        if (!is_file($rutaLogo)) {
            return '';
        }

        $contenido = @file_get_contents($rutaLogo);

        if ($contenido === false) {
            return 'file://' . str_replace('\\', '/', realpath($rutaLogo) ?: $rutaLogo);
        }

        return 'data:image/png;base64,' . base64_encode($contenido);
    }

}
