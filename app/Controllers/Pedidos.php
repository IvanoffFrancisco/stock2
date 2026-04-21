<?php

namespace App\Controllers;

use App\Models\PedidoModel;
use App\Models\PedidoDetalleModel;
use App\Models\ClienteModel;
use App\Models\ProductoModel;
use App\Models\PrecioProductoModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Pedidos extends BaseController
{
    public function index()
{
    $pedidoModel = new PedidoModel();
    $rol = session('rol');
    $usuarioId = session('id_usuario');

    $fechaDesde = $this->request->getGet('fecha_desde');
    $fechaHasta = $this->request->getGet('fecha_hasta');
    $cliente    = trim((string) $this->request->getGet('cliente'));
    $vendedor   = $this->request->getGet('vendedor');
    $estado     = $this->request->getGet('estado');

    $builder = $pedidoModel
        ->select('pedidos.*, clientes.nombre AS cliente_nombre, usuarios.nombre AS vendedor_nombre')
        ->join('clientes', 'clientes.id = pedidos.cliente_id')
        ->join('usuarios', 'usuarios.id = pedidos.usuario_id');

    if ($rol === 'vendedor') {
        $builder->where('pedidos.usuario_id', $usuarioId);
    }

    if (!empty($fechaDesde)) {
        $builder->where('pedidos.fecha_pedido >=', $fechaDesde);
    }

    if (!empty($fechaHasta)) {
        $builder->where('pedidos.fecha_pedido <=', $fechaHasta);
    }

    if (!empty($cliente)) {
        $builder->like('clientes.nombre', $cliente);
    }

    if ($rol === 'admin' && !empty($vendedor)) {
        $builder->where('pedidos.usuario_id', $vendedor);
    }

    if (!empty($estado)) {
        $builder->where('pedidos.estado', $estado);
    }

    $pedidos = $builder
        ->orderBy('pedidos.id', 'DESC')
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

    return view('pedidos/index', [
        'pedidos'     => $pedidos,
        'vendedores'  => $vendedores,
        'filtros'     => [
            'fecha_desde' => $fechaDesde,
            'fecha_hasta' => $fechaHasta,
            'cliente'     => $cliente,
            'vendedor'    => $vendedor,
            'estado'      => $estado,
        ],
    ]);
}

    public function create()
    {
        $clienteModel = new ClienteModel();
        $productoModel = new ProductoModel();

        $clientes = $clienteModel
            ->orderBy('nombre', 'ASC')
            ->findAll();

        $productos = $productoModel
            ->select('productos.*, categorias.nombre AS categoria_nombre')
            ->join('categorias', 'categorias.id = productos.categoria_id')
            ->orderBy('productos.nombre', 'ASC')
            ->findAll();

        return view('pedidos/create', [
            'clientes'  => $clientes,
            'productos' => $productos,
        ]);
    }

    public function store()
    {
        $request = $this->request;

        $clienteId     = $request->getPost('cliente_id');
        $fechaEntrega  = $request->getPost('fecha_entrega');
        $formaPago     = $request->getPost('forma_pago');
        $estado        = $request->getPost('estado') ?: 'pendiente';
        $descuento     = (float) ($request->getPost('descuento') ?: 0);
        $observacion   = trim((string) $request->getPost('observacion'));

        $productoIds      = $request->getPost('producto_id') ?? [];
        $cantidades       = $request->getPost('cantidad') ?? [];
        $preciosUnitarios = $request->getPost('precio_unitario') ?? [];
        $bonificados      = $request->getPost('bonificado') ?? [];

        $rules = [
            'cliente_id'    => 'required|is_not_unique[clientes.id]',
            'fecha_entrega' => 'permit_empty|valid_date[Y-m-d]',
            'forma_pago'    => 'permit_empty|max_length[50]',
            'estado'        => 'required|in_list[pendiente,entregado,cancelado]',
            'descuento'     => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (empty($productoIds) || !is_array($productoIds)) {
            return redirect()->back()->withInput()->with('error', 'Debes agregar al menos un producto al pedido.');
        }

        $resultado = $this->procesarDetallePedido($productoIds, $cantidades, $preciosUnitarios, $bonificados);

        if (!$resultado['ok']) {
            return redirect()->back()->withInput()->with('error', $resultado['error']);
        }

        $pedidoModel        = new PedidoModel();
        $pedidoDetalleModel = new PedidoDetalleModel();

        $db = \Config\Database::connect();
        $db->transStart();

        $pedidoModel->insert([
            'cliente_id'     => $clienteId,
            'usuario_id'     => session('id_usuario'),
            'fecha_pedido'   => date('Y-m-d'),
            'fecha_entrega'  => $fechaEntrega ?: null,
            'forma_pago'     => $formaPago ?: null,
            'estado'         => $estado,
            'subtotal'       => $resultado['subtotal'],
            'descuento'      => $descuento,
            'total'          => max(0, $resultado['subtotal'] - $descuento),
            'observacion'    => $observacion !== '' ? $observacion : null,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);

        $pedidoId = $pedidoModel->getInsertID();

        foreach ($resultado['detalles'] as $detalle) {
            $detalle['pedido_id'] = $pedidoId;
            $pedidoDetalleModel->insert($detalle);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error al guardar el pedido.');
        }

        return redirect()->to('/pedidos')->with('success', 'Pedido creado correctamente.');
    }

    public function edit($id = null)
    {
        $pedidoModel = new PedidoModel();
        $pedidoDetalleModel = new PedidoDetalleModel();
        $clienteModel = new ClienteModel();
        $productoModel = new ProductoModel();

        $pedido = $pedidoModel->find($id);

        if (!$pedido) {
            return redirect()->to('/pedidos')->with('error', 'El pedido no existe.');
        }

        if (session('rol') === 'vendedor' && (int) $pedido['usuario_id'] !== (int) session('id_usuario')) {
            return redirect()->to('/pedidos')->with('error', 'No tienes permisos para editar este pedido.');
        }

        if ($pedido['estado'] === 'entregado') {
            return redirect()->to('/pedidos')->with('error', 'No se puede editar un pedido entregado.');
        }

        $detalles = $pedidoDetalleModel
            ->select('pedido_detalles.*, productos.nombre AS producto_nombre, productos.kilogramos, categorias.nombre AS categoria_nombre')
            ->join('productos', 'productos.id = pedido_detalles.producto_id')
            ->join('categorias', 'categorias.id = productos.categoria_id')
            ->where('pedido_detalles.pedido_id', $id)
            ->findAll();

        $clientes = $clienteModel
            ->orderBy('nombre', 'ASC')
            ->findAll();

        $productos = $productoModel
            ->select('productos.*, categorias.nombre AS categoria_nombre')
            ->join('categorias', 'categorias.id = productos.categoria_id')
            ->orderBy('productos.nombre', 'ASC')
            ->findAll();

        return view('pedidos/edit', [
            'pedido'    => $pedido,
            'detalles'  => $detalles,
            'clientes'  => $clientes,
            'productos' => $productos,
        ]);
    }

    public function update($id = null)
    {
        $pedidoModel = new PedidoModel();
        $pedidoDetalleModel = new PedidoDetalleModel();

        $pedido = $pedidoModel->find($id);

        if (!$pedido) {
            return redirect()->to('/pedidos')->with('error', 'El pedido no existe.');
        }

        if (session('rol') === 'vendedor' && (int) $pedido['usuario_id'] !== (int) session('id_usuario')) {
            return redirect()->to('/pedidos')->with('error', 'No tienes permisos para editar este pedido.');
        }

        if ($pedido['estado'] === 'entregado') {
            return redirect()->to('/pedidos')->with('error', 'No se puede editar un pedido entregado.');
        }

        $request = $this->request;

        $clienteId     = $request->getPost('cliente_id');
        $fechaEntrega  = $request->getPost('fecha_entrega');
        $formaPago     = $request->getPost('forma_pago');
        $estado        = $request->getPost('estado') ?: 'pendiente';
        $descuento     = (float) ($request->getPost('descuento') ?: 0);
        $observacion   = trim((string) $request->getPost('observacion'));

        $productoIds      = $request->getPost('producto_id') ?? [];
        $cantidades       = $request->getPost('cantidad') ?? [];
        $preciosUnitarios = $request->getPost('precio_unitario') ?? [];
        $bonificados      = $request->getPost('bonificado') ?? [];

        $rules = [
            'cliente_id'    => 'required|is_not_unique[clientes.id]',
            'fecha_entrega' => 'permit_empty|valid_date[Y-m-d]',
            'forma_pago'    => 'permit_empty|max_length[50]',
            'estado'        => 'required|in_list[pendiente,entregado,cancelado]',
            'descuento'     => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        if (empty($productoIds) || !is_array($productoIds)) {
            return redirect()->back()->withInput()->with('error', 'Debes agregar al menos un producto al pedido.');
        }

        $resultado = $this->procesarDetallePedido($productoIds, $cantidades, $preciosUnitarios, $bonificados);

        if (!$resultado['ok']) {
            return redirect()->back()->withInput()->with('error', $resultado['error']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $pedidoModel->update($id, [
            'cliente_id'     => $clienteId,
            'fecha_entrega'  => $fechaEntrega ?: null,
            'forma_pago'     => $formaPago ?: null,
            'estado'         => $estado,
            'subtotal'       => $resultado['subtotal'],
            'descuento'      => $descuento,
            'total'          => max(0, $resultado['subtotal'] - $descuento),
            'observacion'    => $observacion !== '' ? $observacion : null,
        ]);

        $pedidoDetalleModel->where('pedido_id', $id)->delete();

        foreach ($resultado['detalles'] as $detalle) {
            $detalle['pedido_id'] = $id;
            $pedidoDetalleModel->insert($detalle);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error al actualizar el pedido.');
        }

        return redirect()->to('/pedidos')->with('success', 'Pedido actualizado correctamente.');
    }

    public function cambiarEstado($id = null)
    {
        $pedidoModel = new PedidoModel();
        $pedido = $pedidoModel->find($id);

        if (!$pedido) {
            return redirect()->to('/pedidos')->with('error', 'El pedido no existe.');
        }

        if (session('rol') === 'vendedor' && (int) $pedido['usuario_id'] !== (int) session('id_usuario')) {
            return redirect()->to('/pedidos')->with('error', 'No tienes permisos para cambiar el estado de este pedido.');
        }

        $nuevoEstado = $this->request->getPost('estado');

        if (!in_array($nuevoEstado, ['pendiente', 'cancelado', 'entregado'])) {
            return redirect()->to('/pedidos')->with('error', 'Estado inválido.');
        }

        if ($pedido['estado'] === 'entregado') {
            return redirect()->to('/pedidos')->with('error', 'El pedido ya fue entregado y no puede cambiarse.');
        }

        if ($nuevoEstado === 'entregado') {
            $resultado = $this->generarVentaDesdePedido((int) $id);

            if (!$resultado['ok']) {
                return redirect()->to('/pedidos')->with('error', $resultado['error']);
            }

            return redirect()->to('/pedidos')->with('success', 'Pedido entregado, venta generada y stock descontado correctamente.');
        }

        $pedidoModel->update($id, [
            'estado' => $nuevoEstado,
        ]);

        return redirect()->to('/pedidos')->with('success', 'Estado del pedido actualizado correctamente.');
    }

    private function procesarDetallePedido(array $productoIds, array $cantidades, array $preciosUnitarios, array $bonificados): array
    {
        $precioProductoModel = new PrecioProductoModel();
        $productoModel       = new ProductoModel();

        $detalles = [];
        $subtotalGeneral = 0;

        for ($i = 0; $i < count($productoIds); $i++) {
            $productoId = isset($productoIds[$i]) ? (int) $productoIds[$i] : 0;
            $cantidad   = isset($cantidades[$i]) ? (int) $cantidades[$i] : 0;
            $precioPost = isset($preciosUnitarios[$i]) ? (float) $preciosUnitarios[$i] : 0;
            $bonificado = isset($bonificados[$i]) ? 1 : 0;

            if ($productoId <= 0 || $cantidad <= 0) {
                continue;
            }

            $producto = $productoModel->find($productoId);

            if (!$producto) {
                continue;
            }

            $precioSugerido = $this->buscarPrecioPorCantidad($precioProductoModel, $productoId, $cantidad);
            $precioUnitarioFinal = $precioPost > 0 ? $precioPost : $precioSugerido;

            if ($bonificado === 1) {
                $precioUnitarioFinal = 0;
            }

            $subtotalLinea = $cantidad * $precioUnitarioFinal;
            $subtotalGeneral += $subtotalLinea;

            $detalles[] = [
                'producto_id'       => $productoId,
                'cantidad'          => $cantidad,
                'precio_unitario'   => $precioUnitarioFinal,
                'subtotal'          => $subtotalLinea,
                'bonificado'        => $bonificado,
                'descripcion_extra' => null,
            ];
        }

        if (empty($detalles)) {
            return [
                'ok' => false,
                'error' => 'No se pudo generar el detalle del pedido.'
            ];
        }

        return [
            'ok' => true,
            'detalles' => $detalles,
            'subtotal' => $subtotalGeneral,
        ];
    }

    private function generarVentaDesdePedido(int $pedidoId): array
    {
        $pedidoModel          = new PedidoModel();
        $pedidoDetalleModel   = new PedidoDetalleModel();
        $ventaModel           = new \App\Models\VentaModel();
        $ventaDetalleModel    = new \App\Models\VentaDetalleModel();
        $productoModel        = new ProductoModel();
        $movimientoStockModel = new \App\Models\MovimientoStockModel();

        $pedido = $pedidoModel->find($pedidoId);

        if (!$pedido) {
            return [
                'ok' => false,
                'error' => 'El pedido no existe.'
            ];
        }

        $ventaExistente = $ventaModel->where('pedido_id', $pedidoId)->first();

        if ($ventaExistente) {
            return [
                'ok' => false,
                'error' => 'Este pedido ya generó una venta anteriormente.'
            ];
        }

        $detalles = $pedidoDetalleModel->where('pedido_id', $pedidoId)->findAll();

        if (empty($detalles)) {
            return [
                'ok' => false,
                'error' => 'El pedido no tiene detalle para generar la venta.'
            ];
        }

        foreach ($detalles as $detalle) {
            $producto = $productoModel->find($detalle['producto_id']);

            if (!$producto) {
                return [
                    'ok' => false,
                    'error' => 'Uno de los productos del pedido ya no existe.'
                ];
            }

            $cantidad = (int) $detalle['cantidad'];
            $bonificado = (int) $detalle['bonificado'];

            if ($bonificado === 1) {
                continue;
            }

            if ((int) $producto['stock_unidades'] < $cantidad) {
                return [
                    'ok' => false,
                    'error' => 'No hay stock suficiente para entregar el producto: ' . $producto['nombre']
                ];
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $ventaModel->insert([
            'pedido_id'       => $pedido['id'],
            'cliente_id'      => $pedido['cliente_id'],
            'usuario_id'      => $pedido['usuario_id'],
            'fecha_venta'     => date('Y-m-d'),
            'fecha_entrega'   => $pedido['fecha_entrega'],
            'forma_pago'      => $pedido['forma_pago'],
            'estado_entrega'  => 'entregado',
            'subtotal'        => $pedido['subtotal'],
            'descuento'       => $pedido['descuento'],
            'total'           => $pedido['total'],
            'observacion'     => $pedido['observacion'],
            'created_at'      => date('Y-m-d H:i:s'),
        ]);

        $ventaId = $ventaModel->getInsertID();

        foreach ($detalles as $detalle) {
            $ventaDetalleModel->insert([
                'venta_id'          => $ventaId,
                'producto_id'       => $detalle['producto_id'],
                'cantidad'          => $detalle['cantidad'],
                'precio_unitario'   => $detalle['precio_unitario'],
                'subtotal'          => $detalle['subtotal'],
                'bonificado'        => $detalle['bonificado'],
                'descripcion_extra' => $detalle['descripcion_extra'],
            ]);

            $producto = $productoModel->find($detalle['producto_id']);
            $cantidad = (int) $detalle['cantidad'];

            if ((int) $detalle['bonificado'] !== 1) {
                $nuevoStock = (int) $producto['stock_unidades'] - $cantidad;

                $productoModel->update($detalle['producto_id'], [
                    'stock_unidades' => $nuevoStock,
                ]);

                $movimientoStockModel->insert([
                    'producto_id'     => $detalle['producto_id'],
                    'usuario_id'      => session('id_usuario'),
                    'tipo_movimiento' => 'egreso',
                    'cantidad'        => $cantidad,
                    'motivo'          => 'Venta generada desde pedido #' . $pedido['id'],
                    'observacion'     => 'Venta #' . $ventaId,
                    'created_at'      => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $pedidoModel->update($pedidoId, [
            'estado' => 'entregado',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return [
                'ok' => false,
                'error' => 'Ocurrió un error al generar la venta.'
            ];
        }

        return [
            'ok' => true
        ];
    }

    private function buscarPrecioPorCantidad(PrecioProductoModel $precioProductoModel, int $productoId, int $cantidad): float
    {
        $precios = $precioProductoModel
            ->where('producto_id', $productoId)
            ->orderBy('cantidad_desde', 'ASC')
            ->findAll();

        foreach ($precios as $precio) {
            $desde = (int) $precio['cantidad_desde'];
            $hasta = $precio['cantidad_hasta'] !== null ? (int) $precio['cantidad_hasta'] : null;

            if ($cantidad >= $desde && ($hasta === null || $cantidad <= $hasta)) {
                return (float) $precio['precio_unitario'];
            }
        }

        return 0;
    }
    public function show($id = null)
{
    $pedidoModel = new PedidoModel();
    $pedidoDetalleModel = new PedidoDetalleModel();

    $pedido = $pedidoModel
        ->select('pedidos.*, clientes.nombre AS cliente_nombre, clientes.telefono, clientes.direccion, clientes.localidad, usuarios.nombre AS vendedor_nombre')
        ->join('clientes', 'clientes.id = pedidos.cliente_id')
        ->join('usuarios', 'usuarios.id = pedidos.usuario_id')
        ->where('pedidos.id', $id)
        ->first();

    if (!$pedido) {
        return redirect()->to('/pedidos')->with('error', 'El pedido no existe.');
    }

    if (session('rol') === 'vendedor' && (int) $pedido['usuario_id'] !== (int) session('id_usuario')) {
        return redirect()->to('/pedidos')->with('error', 'No tienes permisos para ver este pedido.');
    }

    $detalles = $pedidoDetalleModel
        ->select('pedido_detalles.*, productos.nombre AS producto_nombre, productos.kilogramos, categorias.nombre AS categoria_nombre')
        ->join('productos', 'productos.id = pedido_detalles.producto_id')
        ->join('categorias', 'categorias.id = productos.categoria_id')
        ->where('pedido_detalles.pedido_id', $id)
        ->findAll();

    return view('pedidos/show', [
        'pedido'   => $pedido,
        'detalles' => $detalles,
    ]);
}
public function pdf($id = null)
{
    $pedidoModel = new PedidoModel();
    $pedidoDetalleModel = new PedidoDetalleModel();

    $pedido = $pedidoModel
        ->select('pedidos.*, clientes.nombre AS cliente_nombre, clientes.telefono, clientes.direccion, clientes.localidad, usuarios.nombre AS vendedor_nombre')
        ->join('clientes', 'clientes.id = pedidos.cliente_id')
        ->join('usuarios', 'usuarios.id = pedidos.usuario_id')
        ->where('pedidos.id', $id)
        ->first();

    if (!$pedido) {
        return redirect()->to('/pedidos')->with('error', 'El pedido no existe.');
    }

    if (session('rol') === 'vendedor' && (int) $pedido['usuario_id'] !== (int) session('id_usuario')) {
        return redirect()->to('/pedidos')->with('error', 'No tienes permisos para ver este pedido.');
    }

    $detalles = $pedidoDetalleModel
        ->select('pedido_detalles.*, productos.nombre AS producto_nombre, productos.kilogramos, categorias.nombre AS categoria_nombre')
        ->join('productos', 'productos.id = pedido_detalles.producto_id')
        ->join('categorias', 'categorias.id = productos.categoria_id')
        ->where('pedido_detalles.pedido_id', $id)
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
        'tituloDocumento' => 'REMITO / PEDIDO',
        'numeroDocumento' => str_pad((string) $pedido['id'], 6, '0', STR_PAD_LEFT),
        'fechaDocumento'  => $pedido['fecha_pedido'] ?? date('Y-m-d'),
        'fechaEntrega'    => $pedido['fecha_entrega'] ?? '-',
        'formaPago'       => $pedido['forma_pago'] ?? '-',
        'estado'          => ucfirst($pedido['estado'] ?? '-'),
        'vendedorNombre'  => $pedido['vendedor_nombre'] ?? '-',
        'cliente' => [
            'nombre'    => $pedido['cliente_nombre'] ?? '-',
            'telefono'  => $pedido['telefono'] ?? '-',
            'direccion' => $pedido['direccion'] ?? '-',
            'localidad' => $pedido['localidad'] ?? '-',
        ],
        'detalles'  => $detalles,
        'subtotal'  => $pedido['subtotal'] ?? 0,
        'descuento' => $pedido['descuento'] ?? 0,
        'total'     => $pedido['total'] ?? 0,
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
