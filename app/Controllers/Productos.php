<?php

namespace App\Controllers;

use App\Models\ProductoModel;
use App\Models\CategoriaModel;

class Productos extends BaseController
{
    public function index()
    {
        $productoModel = new ProductoModel();

        $productos = $productoModel
            ->select('productos.*, categorias.nombre AS categoria_nombre')
            ->join('categorias', 'categorias.id = productos.categoria_id')
            ->orderBy('productos.id', 'DESC')
            ->findAll();

        foreach ($productos as &$producto) {
            $bolsasPorPallet = (int) ($producto['bolsas_por_pallet'] ?? 0);
            $stockUnidades   = (int) ($producto['stock_unidades'] ?? 0);

            if ($bolsasPorPallet > 0) {
                $producto['pallets_actuales'] = round($stockUnidades / $bolsasPorPallet, 2);
            } else {
                $producto['pallets_actuales'] = 0;
            }
        }

        $data = [
            'productos' => $productos,
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
            'categoria_id' => 'required|is_not_unique[categorias.id]',
            'nombre'       => 'required|min_length[2]|max_length[150]',
            'tipo'         => 'permit_empty|max_length[120]',
            'kilogramos'   => 'required|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $productoModel->update($id, [
            'categoria_id' => $this->request->getPost('categoria_id'),
            'nombre'       => trim($this->request->getPost('nombre')),
            'tipo'         => trim((string) $this->request->getPost('tipo')),
            'kilogramos'   => $this->request->getPost('kilogramos'),
        ]);

        return redirect()->to('/productos')->with('success', 'Producto actualizado correctamente.');
    }
}