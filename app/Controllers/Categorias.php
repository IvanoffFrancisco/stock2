<?php

namespace App\Controllers;

use App\Models\CategoriaModel;

class Categorias extends BaseController
{
    private function esAdmin()
    {
        return session('rol') === 'admin';
    }

    private function validarAdmin()
    {
        if (!$this->esAdmin()) {
            return redirect()->to('/categorias')->with('error', 'No tenés permisos para realizar esta acción.');
        }

        return null;
    }

    public function index()
    {
        $categoriaModel = new CategoriaModel();

        $data = [
            'categorias' => $categoriaModel->orderBy('id', 'DESC')->findAll()
        ];

        return view('categorias/index', $data);
    }

    public function create()
    {
        if ($redirect = $this->validarAdmin()) {
            return $redirect;
        }

        return view('categorias/create');
    }

    public function store()
    {
        if ($redirect = $this->validarAdmin()) {
            return $redirect;
        }

        $rules = [
            'nombre' => 'required|min_length[3]|max_length[120]|is_unique[categorias.nombre]',
            'descripcion' => 'permit_empty|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $categoriaModel = new CategoriaModel();

        $categoriaModel->save([
            'nombre'      => trim($this->request->getPost('nombre')),
            'descripcion' => trim((string) $this->request->getPost('descripcion')),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/categorias')->with('success', 'Categoría creada correctamente.');
    }

    public function edit($id = null)
    {
        if ($redirect = $this->validarAdmin()) {
            return $redirect;
        }

        $categoriaModel = new CategoriaModel();
        $categoria = $categoriaModel->find($id);

        if (!$categoria) {
            return redirect()->to('/categorias')->with('error', 'La categoría no existe.');
        }

        return view('categorias/edit', ['categoria' => $categoria]);
    }

    public function update($id = null)
    {
        if ($redirect = $this->validarAdmin()) {
            return $redirect;
        }

        $categoriaModel = new CategoriaModel();
        $categoria = $categoriaModel->find($id);

        if (!$categoria) {
            return redirect()->to('/categorias')->with('error', 'La categoría no existe.');
        }

        $nombre = trim((string) $this->request->getPost('nombre'));

        $rules = [
            'nombre' => "required|min_length[3]|max_length[120]|is_unique[categorias.nombre,id,{$id}]",
            'descripcion' => 'permit_empty|max_length[1000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $categoriaModel->update($id, [
            'nombre'      => $nombre,
            'descripcion' => trim((string) $this->request->getPost('descripcion')),
        ]);

        return redirect()->to('/categorias')->with('success', 'Categoría actualizada correctamente.');
    }

    public function delete($id = null)
    {
        if ($redirect = $this->validarAdmin()) {
            return $redirect;
        }

        $categoriaModel = new CategoriaModel();
        $categoria = $categoriaModel->find($id);

        if (!$categoria) {
            return redirect()->to('/categorias')->with('error', 'La categoría no existe.');
        }

        $categoriaModel->delete($id);

        return redirect()->to('/categorias')->with('success', 'Categoría eliminada correctamente.');
    }
}