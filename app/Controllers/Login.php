<?php

namespace App\Controllers;

use App\Config\AppConstants;
use App\Models\AuditoriaModel;
use App\Models\UsuariosModel;
use App\Models\UsuariosRolesModel;
use App\Models\RolesPermisosModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\RedirectResponse;

class Login extends Controller
{
    public function index(): string|RedirectResponse
    {
        // Si ya está logueado, redirige al home
        if (session()->get('logged_in')) {
            return redirect()->to('/home');
        }

        return view('login');
    }

    public function doLogin(): RedirectResponse
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
        if (isset($user['estado']) && $user['estado'] !== AppConstants::USUARIO_ACTIVO) {
            return redirect()->back()->with('error', 'Usuario inactivo')->withInput();
        }

        // Verificar contraseña (sha256)
        if (hash('sha256', $password) !== $user['password']) {
            return redirect()->back()->with('error', 'Contraseña incorrecta')->withInput();
        }

        // ==============================
        // CARGAR ROLES Y PERMISOS
        // ==============================
        $usuariosRolesModel = new UsuariosRolesModel();
        $rolesPermisosModel = new RolesPermisosModel();

        // Obtener roles del usuario
        $roles = $usuariosRolesModel->getRolesByUsuario($user['id']);
        $rolesIds = array_column($roles, 'id');

        // Obtener permisos (solo claves)
        $permisos = $rolesPermisosModel->getPermisosByRoles($rolesIds);
        $permisosClaves = array_column($permisos, 'clave');

        // Guardar sesión completa
        $sessionData = [
            'usuario_id' => $user['id'],
            'usuario' => $user['usuario'],
            'nombre' => $user['nombre'] ?? '',
            'correo' => $user['correo'] ?? '',
            'logged_in' => true,
            'roles' => $rolesIds,
            'permisos' => $permisosClaves,
        ];

        session()->set($sessionData);

        // Registrar en auditoría
        AuditoriaModel::registrar('LOGIN', 'Sistema', $user['id'], [
            'usuario' => $user['usuario'],
            'nombre' => $user['nombre'] ?? ''
        ]);

        return redirect()->to('/home');
    }

    public function logout(): RedirectResponse
    {
        $usuario_id = session()->get('usuario_id');
        $usuario = session()->get('usuario');
        
        // Registrar en auditoría antes de destruir la sesión
        if ($usuario_id) {
            AuditoriaModel::registrar('LOGOUT', 'Sistema', $usuario_id, [
                'usuario' => $usuario
            ]);
        }
        
        session()->destroy();
        return redirect()->to('/login');
    }

    public function verificar(): RedirectResponse
    {
        // Si ya hay sesión activa, va al Home
        if (session()->get('logged_in')) {
            return redirect()->to('/home');
        }

        // Si no hay sesión, muestra el login
        return redirect()->to('/login');
    }
}
