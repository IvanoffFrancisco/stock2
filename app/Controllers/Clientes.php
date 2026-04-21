<?php

namespace App\Controllers;

use App\Models\ClienteModel;

class Clientes extends BaseController
{
    public function index()
    {
        $clienteModel = new ClienteModel();

        $clientes = $clienteModel
            ->orderBy('id', 'DESC')
            ->findAll();

        return view('clientes/index', [
            'clientes' => $clientes,
        ]);
    }

    public function create()
    {
        return view('clientes/create', [
            'cliente' => null,
            'modo'    => 'crear',
        ]);
    }

    public function store()
    {
        $rules = [
            'nombre'      => 'required|min_length[3]|max_length[150]',
            'telefono'    => 'permit_empty|max_length[50]',
            'direccion'   => 'permit_empty|max_length[255]',
            'localidad'   => 'permit_empty|max_length[120]',
            'observacion' => 'permit_empty|max_length[2000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $clienteModel = new ClienteModel();

        $clienteModel->save([
            'nombre'      => trim($this->request->getPost('nombre')),
            'telefono'    => trim((string) $this->request->getPost('telefono')),
            'direccion'   => trim((string) $this->request->getPost('direccion')),
            'localidad'   => trim((string) $this->request->getPost('localidad')),
            'observacion' => trim((string) $this->request->getPost('observacion')),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/clientes')->with('success', 'Cliente creado correctamente.');
    }

    public function edit($id)
    {
        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->find($id);

        if (!$cliente) {
            return redirect()->to('/clientes')->with('error', 'Cliente no encontrado.');
        }

        return view('clientes/create', [
            'cliente' => $cliente,
            'modo'    => 'editar',
        ]);
    }

    public function update($id)
    {
        $rules = [
            'nombre'      => 'required|min_length[3]|max_length[150]',
            'telefono'    => 'permit_empty|max_length[50]',
            'direccion'   => 'permit_empty|max_length[255]',
            'localidad'   => 'permit_empty|max_length[120]',
            'observacion' => 'permit_empty|max_length[2000]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $clienteModel = new ClienteModel();
        $cliente = $clienteModel->find($id);

        if (!$cliente) {
            return redirect()->to('/clientes')->with('error', 'Cliente no encontrado.');
        }

        $clienteModel->update($id, [
            'nombre'      => trim($this->request->getPost('nombre')),
            'telefono'    => trim((string) $this->request->getPost('telefono')),
            'direccion'   => trim((string) $this->request->getPost('direccion')),
            'localidad'   => trim((string) $this->request->getPost('localidad')),
            'observacion' => trim((string) $this->request->getPost('observacion')),
        ]);

        return redirect()->to('/clientes')->with('success', 'Cliente actualizado correctamente.');
    }
}