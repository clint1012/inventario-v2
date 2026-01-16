<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SesionesActivasModel;
use App\Models\HistorialLoginsModel;
use App\Models\AuditoriaModel;

class Sesiones extends BaseController
{
    protected $sesionesModel;
    protected $historialModel;

    public function __construct()
    {
        $this->sesionesModel = new SesionesActivasModel();
        $this->historialModel = new HistorialLoginsModel();
    }

    /**
     * Vista principal de gestión de sesiones
     */
    public function index()
    {
        // Verificar permisos
        $permisos = session()->get('permisos') ?? [];
        $tienePermiso = false;
        
        foreach ($permisos as $permiso) {
            if (stripos($permiso, 'sesiones') !== false || 
                stripos($permiso, 'administrador') !== false) {
                $tienePermiso = true;
                break;
            }
        }

        if (!$tienePermiso) {
            return redirect()->to('/home')->with('error', 'No tienes permisos para acceder a esta sección');
        }

        // Limpiar sesiones expiradas automáticamente
        $this->sesionesModel->limpiarSesionesExpiradas();

        $data = [
            'titulo' => 'Gestión de Sesiones',
            'estadisticas' => $this->sesionesModel->getEstadisticas()
        ];

        return view('sesiones/index', $data);
    }

    /**
     * API: Obtener sesiones activas
     */
    public function listarSesiones()
    {
        $sesiones = $this->sesionesModel->getSesionesActivas();
        
        // ...existing code...
        
        // Agregar información adicional
        $session_id_actual = session_id();
        
        foreach ($sesiones as &$sesion) {
            $sesion['es_mi_sesion'] = ($sesion['session_id'] === $session_id_actual);
            $sesion['tiempo_inactivo'] = $this->calcularTiempoInactivo($sesion['ultima_actividad']);
            $sesion['duracion_sesion'] = $this->calcularDuracion($sesion['fecha_inicio']);
        }

        // ...existing code...

        return $this->response->setJSON([
            'success' => true,
            'data' => $sesiones,
            'total' => count($sesiones)
        ]);
    }

    /**
     * API: Obtener historial de logins
     */
    public function listarHistorial()
    {
        $limit = $this->request->getGet('limit') ?? 50;
        $offset = $this->request->getGet('offset') ?? 0;
        
        $filtros = [
            'usuario_id' => $this->request->getGet('usuario_id'),
            'accion' => $this->request->getGet('accion'),
            'fecha_inicio' => $this->request->getGet('fecha_inicio'),
            'fecha_fin' => $this->request->getGet('fecha_fin')
        ];

        $historial = $this->historialModel->getHistorialPaginado($limit, $offset, $filtros);
        $total = $this->historialModel->countAllResults(false);

        return $this->response->setJSON([
            'success' => true,
            'data' => $historial,
            'total' => $total
        ]);
    }

    /**
     * Cerrar sesión remota
     */
    public function cerrarSesion()
    {
        // Verificar permisos
        $permisos = session()->get('permisos') ?? [];
        $tienePermiso = false;
        
        foreach ($permisos as $permiso) {
            if (stripos($permiso, 'sesiones.cerrar') !== false || 
                stripos($permiso, 'administrador') !== false) {
                $tienePermiso = true;
                break;
            }
        }

        if (!$tienePermiso) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tienes permisos para cerrar sesiones'
            ]);
        }

        $sesion_id = $this->request->getPost('sesion_id');
        
        if (!$sesion_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de sesión no proporcionado'
            ]);
        }

        // Obtener información de la sesión antes de cerrarla
        $sesion = $this->sesionesModel->find($sesion_id);
        
        if (!$sesion) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sesión no encontrada'
            ]);
        }

        // No permitir cerrar la propia sesión
        $session_id_actual = session()->session_id ?? session_id();
        if ($sesion['session_id'] === $session_id_actual) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No puedes cerrar tu propia sesión desde aquí'
            ]);
        }

        // Cerrar sesión
        $this->sesionesModel->cerrarSesionById($sesion_id);

        // Calcular duración
        $inicio = strtotime($sesion['fecha_inicio']);
        $duracion = time() - $inicio;

        // Registrar en historial
        HistorialLoginsModel::registrar(
            $sesion['usuario_id'],
            $sesion['usuario'],
            $sesion['nombre'],
            'SESION_CERRADA',
            $duracion
        );

        // Registrar en auditoría
        AuditoriaModel::registrar(
            'SESION_CERRADA_REMOTAMENTE',
            'Sistema',
            $sesion['usuario_id'],
            [
                'cerrada_por' => session()->get('usuario'),
                'usuario_afectado' => $sesion['usuario'],
                'ip' => $sesion['ip_address'],
                'duracion_segundos' => $duracion
            ]
        );

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Sesión cerrada exitosamente'
        ]);
    }

    /**
     * Cerrar todas las sesiones de un usuario
     */
    public function cerrarSesionesUsuario()
    {
        // Verificar permisos
        $permisos = session()->get('permisos') ?? [];
        $tienePermiso = false;
        
        foreach ($permisos as $permiso) {
            if (stripos($permiso, 'sesiones.cerrar') !== false || 
                stripos($permiso, 'administrador') !== false) {
                $tienePermiso = true;
                break;
            }
        }

        if (!$tienePermiso) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tienes permisos para cerrar sesiones'
            ]);
        }

        $usuario_id = $this->request->getPost('usuario_id');
        
        if (!$usuario_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de usuario no proporcionado'
            ]);
        }

        // No permitir cerrar las propias sesiones
        if ($usuario_id == session()->get('usuario_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No puedes cerrar tus propias sesiones desde aquí'
            ]);
        }

        // Obtener sesiones del usuario
        $sesiones = $this->sesionesModel->getSesionesByUsuario($usuario_id);
        $count = count($sesiones);

        if ($count === 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No hay sesiones activas para este usuario'
            ]);
        }

        // Cerrar todas las sesiones
        $this->sesionesModel->cerrarSesionesUsuario($usuario_id);

        // Registrar en auditoría
        AuditoriaModel::registrar(
            'SESIONES_CERRADAS_MASIVAMENTE',
            'Sistema',
            $usuario_id,
            [
                'cerradas_por' => session()->get('usuario'),
                'cantidad_sesiones' => $count
            ]
        );

        return $this->response->setJSON([
            'success' => true,
            'message' => "Se cerraron {$count} sesión(es) del usuario"
        ]);
    }

    /**
     * Obtener estadísticas para el dashboard
     */
    public function estadisticas()
    {
        $stats = $this->sesionesModel->getEstadisticas();
        $multiplesUsuarios = $this->sesionesModel->getUsuariosMultiplesSesiones();
        $ultimosLogins = $this->historialModel->getUltimosLogins(5);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'estadisticas' => $stats,
                'usuarios_multiples_sesiones' => $multiplesUsuarios,
                'ultimos_logins' => $ultimosLogins
            ]
        ]);
    }

    /**
     * Calcular tiempo de inactividad
     */
    private function calcularTiempoInactivo($ultima_actividad)
    {
        $tiempo = time() - strtotime($ultima_actividad);
        
        if ($tiempo < 60) {
            return $tiempo . 's';
        } elseif ($tiempo < 3600) {
            return floor($tiempo / 60) . 'm';
        } else {
            return floor($tiempo / 3600) . 'h ' . floor(($tiempo % 3600) / 60) . 'm';
        }
    }

    /**
     * Calcular duración de sesión
     */
    private function calcularDuracion($fecha_inicio)
    {
        $tiempo = time() - strtotime($fecha_inicio);
        
        if ($tiempo < 60) {
            return $tiempo . 's';
        } elseif ($tiempo < 3600) {
            return floor($tiempo / 60) . ' min';
        } else {
            $horas = floor($tiempo / 3600);
            $minutos = floor(($tiempo % 3600) / 60);
            return $horas . 'h ' . $minutos . 'm';
        }
    }

    /**
     * Vista de historial de logins
     */
    public function historial()
    {
        // Verificar permisos
        $permisos = session()->get('permisos') ?? [];
        $tienePermiso = false;
        
        foreach ($permisos as $permiso) {
            if (stripos($permiso, 'sesiones') !== false || 
                stripos($permiso, 'administrador') !== false) {
                $tienePermiso = true;
                break;
            }
        }

        if (!$tienePermiso) {
            return redirect()->to('/home')->with('error', 'No tienes permisos para acceder a esta sección');
        }

        $data = [
            'titulo' => 'Historial de Logins'
        ];

        return view('sesiones/historial', $data);
    }
}
