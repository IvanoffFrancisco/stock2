<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/register');
    }

    public function guardarRegistro()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'nombre' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[usuarios.email]',
            'password' => 'required|min_length[4]',
            'confirm_password' => 'required|matches[password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new UsuarioModel();

        $model->save([
            'nombre'     => $this->request->getPost('nombre'),
            'email'      => $this->request->getPost('email'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'rol'        => 'consultor',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/login')->with('success', 'Usuario registrado correctamente.');
    }

    public function autenticar()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $model = new UsuarioModel();

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $usuario = $model->where('email', $email)->first();

        if (!$usuario) {
            return redirect()->back()->withInput()->with('error', 'El usuario no existe.');
        }

        if (!password_verify($password, $usuario['password'])) {
            return redirect()->back()->withInput()->with('error', 'Contraseña incorrecta.');
        }

        session()->set([
            'id_usuario' => $usuario['id'],
            'nombre'     => $usuario['nombre'],
            'email'      => $usuario['email'],
            'rol'        => $usuario['rol'],
            'isLoggedIn' => true,
        ]);

        return redirect()->to('/dashboard');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Sesión cerrada correctamente.');
    }

    public function dashboard()
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión.');
        }

        return view('dashboard');
    }
}