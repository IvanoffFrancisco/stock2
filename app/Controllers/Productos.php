<?php

namespace App\Controllers;

use App\Models\ProductoModel;
use App\Models\CategoriaModel;

class Productos extends BaseController
{
    public function index()
    {
        $productoModel = new ProductoModel();

        $buscarNombre = trim((string) $this->request->getGet('nombre'));
        $molino       = trim((string) $this->request->getGet('molino'));

        $builder = $productoModel
            ->select('productos.*, categorias.nombre AS categoria_nombre')
            ->join('categorias', 'categorias.id = productos.categoria_id');

        if ($buscarNombre !== '') {
            $builder->like('productos.nombre', $buscarNombre);
        }

        if ($molino !== '') {
            $builder->where('productos.molino', $molino);
        }

        $productos = $builder
            ->orderBy('productos.id', 'DESC')
            ->findAll();

        foreach ($productos as &$producto) {
            $bolsasPorPallet = (int) ($producto['bolsas_por_pallet'] ?? 0);
            $stockUnidades   = (int) ($producto['stock_unidades'] ?? 0);
            $stockMinimo     = (int) ($producto['stock_minimo'] ?? 0);

            if ($bolsasPorPallet > 0) {
                $producto['pallets_actuales'] = round($stockUnidades / $bolsasPorPallet, 2);
            } else {
                $producto['pallets_actuales'] = 0;
            }

            if ($stockUnidades <= 0) {
                $producto['estado_stock'] = 'sin_stock';
            } elseif ($stockUnidades <= $stockMinimo) {
                $producto['estado_stock'] = 'stock_bajo';
            } elseif ($stockUnidades <= ($stockMinimo + 5)) {
                $producto['estado_stock'] = 'stock_justo';
            } else {
                $producto['estado_stock'] = 'stock_normal';
            }
        }
        unset($producto);

        $molinos = $productoModel
            ->select('molino')
            ->where('molino IS NOT NULL')
            ->where('molino !=', '')
            ->groupBy('molino')
            ->orderBy('molino', 'ASC')
            ->findAll();

        $data = [
            'productos' => $productos,
            'molinos'   => $molinos,
            'filtros'   => [
                'nombre' => $buscarNombre,
                'molino' => $molino,
            ],
        ];

        return view('productos/index', $data);
    }

    public function create()
    {
        if (session('rol') !== 'admin') {
            return redirect()->to('/productos')->with('error', 'No tenés permiso para crear productos.');
        }

        $categoriaModel = new CategoriaModel();

        $data = [
            'categorias' => $categoriaModel->orderBy('nombre', 'ASC')->findAll(),
        ];

        return view('productos/create', $data);
    }

    public function store()
    {
        if (session('rol') !== 'admin') {
            return redirect()->to('/productos')->with('error', 'No tenés permiso para crear productos.');
        }

        $rules = [
            'categoria_id'       => 'required|is_not_unique[categorias.id]',
            'nombre'             => 'required|min_length[2]|max_length[150]',
            'molino'             => 'permit_empty|max_length[120]',
            'tipo'               => 'permit_empty|max_length[120]',
            'kilogramos'         => 'required|decimal',
            'bolsas_por_pallet'  => 'required|integer|greater_than_equal_to[0]',
            'stock_unidades'     => 'required|integer|greater_than_equal_to[0]',
            'stock_minimo'       => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $productoModel = new ProductoModel();

        $productoModel->save([
            'categoria_id'       => $this->request->getPost('categoria_id'),
            'nombre'             => trim($this->request->getPost('nombre')),
            'molino'             => trim((string) $this->request->getPost('molino')),
            'tipo'               => trim((string) $this->request->getPost('tipo')),
            'kilogramos'         => $this->request->getPost('kilogramos'),
            'bolsas_por_pallet'  => $this->request->getPost('bolsas_por_pallet'),
            'stock_unidades'     => $this->request->getPost('stock_unidades'),
            'stock_minimo'       => $this->request->getPost('stock_minimo'),
            'created_at'         => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/productos')->with('success', 'Producto creado correctamente.');
    }

    public function edit($id = null)
    {
        if (session('rol') !== 'admin') {
            return redirect()->to('/productos')->with('error', 'No tenés permiso para editar productos.');
        }

        $productoModel = new ProductoModel();
        $categoriaModel = new CategoriaModel();

        $producto = $productoModel->find($id);

        if (!$producto) {
            return redirect()->to('/productos')->with('error', 'Producto no encontrado.');
        }

        $data = [
            'producto'   => $producto,
            'categorias' => $categoriaModel->orderBy('nombre', 'ASC')->findAll(),
        ];

        return view('productos/edit', $data);
    }

    public function update($id = null)
    {
        if (session('rol') !== 'admin') {
            return redirect()->to('/productos')->with('error', 'No tenés permiso para editar productos.');
        }

        $productoModel = new ProductoModel();
        $producto = $productoModel->find($id);

        if (!$producto) {
            return redirect()->to('/productos')->with('error', 'Producto no encontrado.');
        }

        $rules = [
            'categoria_id'      => 'required|is_not_unique[categorias.id]',
            'nombre'            => 'required|min_length[2]|max_length[150]',
            'molino'            => 'permit_empty|max_length[120]',
            'tipo'              => 'permit_empty|max_length[120]',
            'kilogramos'        => 'required|decimal',
            'bolsas_por_pallet' => 'permit_empty|integer|greater_than_equal_to[0]',
            'stock_unidades'    => 'permit_empty|integer|greater_than_equal_to[0]',
            'stock_minimo'      => 'required|integer|greater_than_equal_to[0]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $dataUpdate = [
            'categoria_id' => $this->request->getPost('categoria_id'),
            'nombre'       => trim($this->request->getPost('nombre')),
            'molino'       => trim((string) $this->request->getPost('molino')),
            'tipo'         => trim((string) $this->request->getPost('tipo')),
            'kilogramos'   => $this->request->getPost('kilogramos'),
            'stock_minimo' => $this->request->getPost('stock_minimo'),
        ];

        if ($this->request->getPost('bolsas_por_pallet') !== null && $this->request->getPost('bolsas_por_pallet') !== '') {
            $dataUpdate['bolsas_por_pallet'] = $this->request->getPost('bolsas_por_pallet');
        }

        if ($this->request->getPost('stock_unidades') !== null && $this->request->getPost('stock_unidades') !== '') {
            $dataUpdate['stock_unidades'] = $this->request->getPost('stock_unidades');
        }

        $productoModel->update($id, $dataUpdate);

        return redirect()->to('/productos')->with('success', 'Producto actualizado correctamente.');
    }
}
