<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Si no está logueado, redirige a login
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

         // 2. Si no se requiere permiso específico → permitir
        if (empty($arguments)) {
            return;
        }

        // 3. Permiso necesario (viene desde la ruta)
        $permisoRequerido = $arguments[0];

        // 4. Permisos que tiene el usuario (cargados al hacer login)
        $permisosUsuario = session()->get('permisos') ?? [];

        // 5. Validar si el usuario NO tiene el permiso requerido
        if (!in_array($permisoRequerido, $permisosUsuario)) {
            // Redirige a página sin acceso
            return redirect()->to('/sin-acceso');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nothing
    }
}
