<?php

namespace App\Controllers;

use App\Models\UsuariosModel;
use CodeIgniter\Controller;

class Login extends Controller
{
    public function index()
    {
        // Si ya está logueado, redirige al home
        if (session()->get('logged_in')) {
            return redirect()->to('/home');
        }

        return view('login');
    }

    public function doLogin()
    {
        helper('url');

        $usuario = $this->request->getPost('usuario');
        $password = $this->request->getPost('password');

        $model = new UsuariosModel();
        $user = $model->where('usuario', $usuario)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Usuario no encontrado')->withInput();
        }

        // Verificar estado
        if (isset($user['estado']) && $user['estado'] !== 'activo') {
            return redirect()->back()->with('error', 'Usuario inactivo')->withInput();
        }

        // Verificar contraseña (sha256)
        if (hash('sha256', $password) !== $user['password']) {
            return redirect()->back()->with('error', 'Contraseña incorrecta')->withInput();
        }

        // Crear sesión
        $sessionData = [
            'usuario_id' => $user['id'],
            'usuario'    => $user['usuario'],
            'nombre'     => $user['nombre'] ?? '',
            'correo'     => $user['correo'] ?? '',
            'logged_in'  => true,
        ];

        session()->set($sessionData);

        return redirect()->to('/home');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function verificar()
    {
        // Si ya hay sesión activa, va al Home
        if (session()->get('logged_in')) {
            return redirect()->to('/home');
        }

        // Si no hay sesión, muestra el login
        return redirect()->to('/login');
    }
}
