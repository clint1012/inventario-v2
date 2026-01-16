<?php

namespace App\Controllers;

use App\Config\AppConstants;
use App\Models\AuditoriaModel;
use App\Models\UsuariosModel;
use App\Models\UsuariosRolesModel;
use App\Models\RolesPermisosModel;
use App\Models\SesionesActivasModel;
use App\Models\HistorialLoginsModel;
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

        // ==============================
        // REGISTRAR SESIÓN ACTIVA
        // ==============================
        $sesionesModel = new SesionesActivasModel();
        $userAgent = $this->request->getUserAgent();
        $parsed = SesionesActivasModel::parseUserAgent($userAgent->getAgentString());

        // Obtener session_id - regenerar para asegurar que existe
        session()->regenerate(false);
        $sessionId = session_id();

            // Guardar el session_id en la sesión para referencia futura
            session()->set('session_id_real', $sessionId);
            $sesionesModel->registrarSesion([
                'session_id' => $sessionId,
                'usuario_id' => $user['id'],
                'usuario' => $user['usuario'],
                'nombre' => $user['nombre'] ?? '',
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $userAgent->getAgentString(),
                'navegador' => $parsed['navegador'],
                'sistema_operativo' => $parsed['sistema_operativo']
            ]);

        // Registrar en historial de logins
        HistorialLoginsModel::registrar(
            $user['id'],
            $user['usuario'],
            $user['nombre'] ?? '',
            'LOGIN'
        );

        // Registrar en auditoría
        AuditoriaModel::registrar('LOGIN', 'Sistema', $user['id'], [
            'usuario' => $user['usuario'],
            'nombre' => $user['nombre'] ?? '',
            'ip' => $this->request->getIPAddress(),
            'navegador' => $parsed['navegador']
        ]);

        return redirect()->to('/home');
    }

    public function logout(): RedirectResponse
    {
        // ...existing code...
        try {
            // IMPORTANTE: Capturar session_id ANTES de cualquier operación
                // Usar el session_id guardado en la sesión
                $session_id = session()->get('session_id_real');
            $usuario_id = session()->get('usuario_id');
            $usuario = session()->get('usuario');
            $nombre = session()->get('nombre');

            // ...existing code...

            // Cerrar sesión activa y calcular duración
            if ($session_id && $usuario_id) {
                $sesionesModel = new SesionesActivasModel();
                $sesion = $sesionesModel->existeSesion($session_id);

                // ...existing code...

                if ($sesion) {
                    $inicio = strtotime($sesion['fecha_inicio']);
                    $duracion = time() - $inicio;

                    // Cerrar sesión
                    $resultado = $sesionesModel->cerrarSesion($session_id);
                    // ...existing code...

                    // Registrar en historial con duración
                    HistorialLoginsModel::registrar(
                        $usuario_id,
                        $usuario,
                        $nombre,
                        'LOGOUT',
                        $duracion
                    );
                } else {
                    log_message('warning', "LOGOUT - No se encontró sesión activa para session_id: {$session_id}");
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'LOGOUT - Excepción: ' . $e->getMessage());
        }
        
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
