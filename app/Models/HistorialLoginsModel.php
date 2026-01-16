<?php

namespace App\Models;

use CodeIgniter\Model;

class HistorialLoginsModel extends Model
{
    protected $table = 'historial_logins';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'usuario_id',
        'usuario',
        'nombre',
        'accion',
        'ip_address',
        'user_agent',
        'navegador',
        'sistema_operativo',
        'fecha',
        'duracion_segundos'
    ];

    protected $useTimestamps = false;
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;

    /**
     * Registrar evento de login/logout
     */
    public static function registrar($usuario_id, $usuario, $nombre, $accion, $duracion_segundos = null)
    {
        $model = new self();
        $request = \Config\Services::request();
        
        $user_agent = $request->getUserAgent();
        $parsed = SesionesActivasModel::parseUserAgent($user_agent->getAgentString());

        $data = [
            'usuario_id' => $usuario_id,
            'usuario' => $usuario,
            'nombre' => $nombre,
            'accion' => $accion,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $user_agent->getAgentString(),
            'navegador' => $parsed['navegador'],
            'sistema_operativo' => $parsed['sistema_operativo'],
            'fecha' => date('Y-m-d H:i:s'),
            'duracion_segundos' => $duracion_segundos
        ];

        return $model->insert($data);
    }

    /**
     * Obtener historial con paginación
     */
    public function getHistorialPaginado($limit = 50, $offset = 0, $filtros = [])
    {
        $builder = $this->orderBy('fecha', 'DESC');

        if (!empty($filtros['usuario_id'])) {
            $builder->where('usuario_id', $filtros['usuario_id']);
        }

        if (!empty($filtros['accion'])) {
            $builder->where('accion', $filtros['accion']);
        }

        if (!empty($filtros['fecha_inicio'])) {
            $builder->where('fecha >=', $filtros['fecha_inicio']);
        }

        if (!empty($filtros['fecha_fin'])) {
            $builder->where('fecha <=', $filtros['fecha_fin']);
        }

        return $builder->findAll($limit, $offset);
    }

    /**
     * Obtener historial de un usuario específico
     */
    public function getHistorialUsuario($usuario_id, $limit = 20)
    {
        return $this->where('usuario_id', $usuario_id)
            ->orderBy('fecha', 'DESC')
            ->findAll($limit);
    }

    /**
     * Obtener últimos logins
     */
    public function getUltimosLogins($limit = 10)
    {
        return $this->where('accion', 'LOGIN')
            ->orderBy('fecha', 'DESC')
            ->findAll($limit);
    }

    /**
     * Estadísticas de logins por periodo
     */
    public function getEstadisticasPorPeriodo($dias = 7)
    {
        $fecha_inicio = date('Y-m-d', strtotime("-{$dias} days"));
        
        return $this->select('DATE(fecha) as fecha, COUNT(*) as total')
            ->where('accion', 'LOGIN')
            ->where('fecha >=', $fecha_inicio)
            ->groupBy('DATE(fecha)')
            ->orderBy('fecha', 'ASC')
            ->findAll();
    }

    /**
     * Detectar intentos de login fallidos (desde auditoría)
     */
    public function getIntentosFailidos($usuario = null, $horas = 24)
    {
        $db = \Config\Database::connect();
        $fecha_limite = date('Y-m-d H:i:s', strtotime("-{$horas} hours"));
        
        $builder = $db->table('auditoria')
            ->select('COUNT(*) as intentos, MAX(fecha) as ultimo_intento')
            ->where('accion', 'LOGIN_FALLIDO')
            ->where('fecha >=', $fecha_limite);

        if ($usuario) {
            $builder->where('usuario', $usuario);
        }

        return $builder->get()->getRowArray();
    }
}
