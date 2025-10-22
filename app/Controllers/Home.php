<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        $session = session();

        // Verificar si el usuario ha iniciado sesión
        if (!$session->get('logged_in')) {
            return redirect()->to('/login');
        }

        return view('index');
    }
}