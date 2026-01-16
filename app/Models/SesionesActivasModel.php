<?php

namespace App\Models;

use CodeIgniter\Model;

class SesionesActivasModel extends Model
{
    protected $table = 'sesiones_activas';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'session_id',
        'usuario_id',
        'usuario',
        'nombre',
        'ip_address',
        'user_agent',
        'navegador',
        'sistema_operativo',
        'ultima_actividad',
        'fecha_inicio',
        'activa'
    ];

    protected $useTimestamps = false;
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Obtener todas las sesiones activas
     */
    public function getSesionesActivas()
    {
        return $this->where('activa', 1)
            ->orderBy('ultima_actividad', 'DESC')
            ->findAll();
    }

    /**
     * Obtener sesiones por usuario
     */
    public function getSesionesByUsuario($usuario_id)
    {
        return $this->where('usuario_id', $usuario_id)
            ->where('activa', 1)
            ->orderBy('ultima_actividad', 'DESC')
            ->findAll();
    }

    /**
     * Verificar si existe una sesión activa
     */
    public function existeSesion($session_id)
    {
        return $this->where('session_id', $session_id)
            ->where('activa', 1)
            ->first();
    }

    /**
     * Registrar o actualizar sesión
     */
    public function registrarSesion($data)
    {
        $existing = $this->where('session_id', $data['session_id'])->first();

        if ($existing) {
            // Actualizar última actividad
            return $this->update($existing['id'], [
                'ultima_actividad' => date('Y-m-d H:i:s'),
                'activa' => 1
            ]);
        } else {
            // Crear nueva sesión
            $data['fecha_inicio'] = date('Y-m-d H:i:s');
            $data['ultima_actividad'] = date('Y-m-d H:i:s');
            $data['activa'] = 1;
            return $this->insert($data);
        }
    }

    /**
     * Cerrar sesión
     */
    public function cerrarSesion($session_id)
    {
        return $this->where('session_id', $session_id)
            ->set(['activa' => 0])
            ->update();
    }

    /**
     * Cerrar sesión por ID
     */
    public function cerrarSesionById($id)
    {
        return $this->update($id, ['activa' => 0]);
    }

    /**
     * Cerrar todas las sesiones de un usuario
     */
    public function cerrarSesionesUsuario($usuario_id, $except_session_id = null)
    {
        $builder = $this->where('usuario_id', $usuario_id);
        
        if ($except_session_id) {
            $builder->where('session_id !=', $except_session_id);
        }

        return $builder->set(['activa' => 0])->update();
    }

    /**
     * Limpiar sesiones expiradas (más de X horas sin actividad)
     */
    public function limpiarSesionesExpiradas($horas = 2)
    {
        $fecha_limite = date('Y-m-d H:i:s', strtotime("-{$horas} hours"));
        
        return $this->where('activa', 1)
            ->where('ultima_actividad <', $fecha_limite)
            ->set(['activa' => 0])
            ->update();
    }

    /**
     * Detectar usuarios con múltiples sesiones
     */
    public function getUsuariosMultiplesSesiones()
    {
        return $this->select('usuario_id, usuario, nombre, COUNT(*) as num_sesiones')
            ->where('activa', 1)
            ->groupBy('usuario_id, usuario, nombre')
            ->having('COUNT(*) >', 1)
            ->findAll();
    }

    /**
     * Obtener estadísticas de sesiones
     */
    public function getEstadisticas()
    {
        $db = \Config\Database::connect();
        
        // Contar usuarios conectados únicos
        $usuariosConectados = $db->query(
            "SELECT COUNT(DISTINCT usuario_id) as total FROM sesiones_activas WHERE activa = 1"
        )->getRow()->total;
        
        // Contar usuarios con múltiples sesiones
        $multiplesSesiones = $db->query(
            "SELECT COUNT(*) as total FROM (
                SELECT usuario_id FROM sesiones_activas 
                WHERE activa = 1 
                GROUP BY usuario_id 
                HAVING COUNT(*) > 1
            ) as subquery"
        )->getRow()->total;
        
        return [
            'activas_ahora' => $this->where('activa', 1)->countAllResults(),
            'usuarios_conectados' => $usuariosConectados,
            'sesiones_hoy' => $db->table('historial_logins')
                ->where('DATE(fecha)', date('Y-m-d'))
                ->where('accion', 'LOGIN')
                ->countAllResults(),
            'multiples_sesiones' => $multiplesSesiones
        ];
    }

    /**
     * Parsear User Agent para obtener navegador y SO
     */
    public static function parseUserAgent($user_agent)
    {
        $navegador = 'Desconocido';
        $sistema = 'Desconocido';

        // Detectar navegador
        if (preg_match('/Edge/i', $user_agent)) {
            $navegador = 'Edge';
        } elseif (preg_match('/Chrome/i', $user_agent) && !preg_match('/Edg/i', $user_agent)) {
            $navegador = 'Chrome';
        } elseif (preg_match('/Firefox/i', $user_agent)) {
            $navegador = 'Firefox';
        } elseif (preg_match('/Safari/i', $user_agent) && !preg_match('/Chrome/i', $user_agent)) {
            $navegador = 'Safari';
        } elseif (preg_match('/Opera|OPR/i', $user_agent)) {
            $navegador = 'Opera';
        } elseif (preg_match('/MSIE|Trident/i', $user_agent)) {
            $navegador = 'Internet Explorer';
        }

        // Detectar sistema operativo
        if (preg_match('/Windows NT 10/i', $user_agent)) {
            $sistema = 'Windows 10/11';
        } elseif (preg_match('/Windows NT 6.3/i', $user_agent)) {
            $sistema = 'Windows 8.1';
        } elseif (preg_match('/Windows NT 6.2/i', $user_agent)) {
            $sistema = 'Windows 8';
        } elseif (preg_match('/Windows NT 6.1/i', $user_agent)) {
            $sistema = 'Windows 7';
        } elseif (preg_match('/Windows/i', $user_agent)) {
            $sistema = 'Windows';
        } elseif (preg_match('/Mac OS X/i', $user_agent)) {
            $sistema = 'macOS';
        } elseif (preg_match('/Linux/i', $user_agent)) {
            $sistema = 'Linux';
        } elseif (preg_match('/Android/i', $user_agent)) {
            $sistema = 'Android';
        } elseif (preg_match('/iOS|iPhone|iPad/i', $user_agent)) {
            $sistema = 'iOS';
        }

        return [
            'navegador' => $navegador,
            'sistema_operativo' => $sistema
        ];
    }
}
