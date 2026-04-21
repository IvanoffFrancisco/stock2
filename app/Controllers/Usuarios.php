<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Usuarios extends BaseController
{
    public function index()
    {
        $usuarioModel = new UsuarioModel();

        $data = [
            'usuarios' => $usuarioModel->orderBy('id', 'DESC')->findAll()
        ];

        return view('usuarios/index', $data);
    }

    public function create()
    {
        return view('usuarios/create');
    }

    public function store()
    {
        $rules = [
            'nombre'            => 'required|min_length[3]|max_length[100]',
            'email'             => 'required|valid_email|is_unique[usuarios.email]',
            'password'          => 'required|min_length[4]',
            'confirm_password'  => 'required|matches[password]',
            'rol'               => 'required|in_list[admin,vendedor,consultor]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $usuarioModel = new UsuarioModel();

        $usuarioModel->save([
            'nombre'     => $this->request->getPost('nombre'),
            'email'      => $this->request->getPost('email'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'rol'        => $this->request->getPost('rol'),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/usuarios')->with('success', 'Usuario creado correctamente.');
    }
}