<?php

namespace App\Controllers;

use App\Models\MovimientoStockModel;
use App\Models\ProductoModel;

class MovimientosStock extends BaseController
{
    public function index()
    {
        $movimientoModel = new MovimientoStockModel();

        $movimientos = $movimientoModel
            ->select('movimientos_stock.*, productos.nombre AS producto_nombre, productos.kilogramos, categorias.nombre AS categoria_nombre, usuarios.nombre AS usuario_nombre')
            ->join('productos', 'productos.id = movimientos_stock.producto_id')
            ->join('categorias', 'categorias.id = productos.categoria_id')
            ->join('usuarios', 'usuarios.id = movimientos_stock.usuario_id')
            ->orderBy('movimientos_stock.id', 'DESC')
            ->findAll();

        return view('movimientos_stock/index', [
            'movimientos' => $movimientos,
        ]);
    }

    public function create()
    {
        $productoModel = new ProductoModel();

        $productos = $productoModel
            ->select('productos.*, categorias.nombre AS categoria_nombre')
            ->join('categorias', 'categorias.id = productos.categoria_id')
            ->orderBy('productos.nombre', 'ASC')
            ->findAll();

        return view('movimientos_stock/create', [
            'productos' => $productos,
        ]);
    }

    public function store()
    {
        $rules = [
            'producto_id'       => 'required|is_not_unique[productos.id]',
            'tipo_movimiento'   => 'required|in_list[ingreso,egreso,ajuste]',
            'cantidad'          => 'required|integer|greater_than[0]',
            'motivo'            => 'permit_empty|max_length[150]',
            'observacion'       => 'permit_empty|max_length[2000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $productoModel   = new ProductoModel();
        $movimientoModel = new MovimientoStockModel();

        $productoId      = (int) $this->request->getPost('producto_id');
        $tipoMovimiento  = $this->request->getPost('tipo_movimiento');
        $cantidad        = (int) $this->request->getPost('cantidad');
        $motivo          = trim((string) $this->request->getPost('motivo'));
        $observacion     = trim((string) $this->request->getPost('observacion'));

        $producto = $productoModel->find($productoId);

        if (!$producto) {
            return redirect()->back()->withInput()->with('error', 'El producto seleccionado no existe.');
        }

        $stockActual = (int) $producto['stock_unidades'];
        $nuevoStock  = $stockActual;

        if ($tipoMovimiento === 'ingreso') {
            $nuevoStock = $stockActual + $cantidad;
        }

        if ($tipoMovimiento === 'egreso') {
            if ($cantidad > $stockActual) {
                return redirect()->back()->withInput()->with('error', 'No puedes egresar más stock del disponible.');
            }

            $nuevoStock = $stockActual - $cantidad;
        }

        if ($tipoMovimiento === 'ajuste') {
            $nuevoStock = $cantidad;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $movimientoModel->save([
            'producto_id'      => $productoId,
            'usuario_id'       => session('id_usuario'),
            'tipo_movimiento'  => $tipoMovimiento,
            'cantidad'         => $cantidad,
            'motivo'           => $motivo !== '' ? $motivo : null,
            'observacion'      => $observacion !== '' ? $observacion : null,
            'created_at'       => date('Y-m-d H:i:s'),
        ]);

        $productoModel->update($productoId, [
            'stock_unidades' => $nuevoStock,
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Ocurrió un error al guardar el movimiento.');
        }

        return redirect()->to('/movimientos-stock')->with('success', 'Movimiento de stock registrado correctamente.');
    }
}