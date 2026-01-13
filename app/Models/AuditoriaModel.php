<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditoriaModel extends Model
{
    protected $table = 'auditoria';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'usuario_id',
        'usuario_nombre',
        'accion',
        'modulo',
        'registro_id',
        'detalles',
        'ip_address',
        'user_agent'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = '';
    protected $dateFormat = 'datetime';

    /**
     * Obtener auditoría con paginación y filtros
     */
    public function getAuditoriaConFiltros($filtros = [])
    {
        $builder = $this->select('auditoria.*, usuarios.nombre as usuario_sistema')
            ->join('usuarios', 'usuarios.id = auditoria.usuario_id', 'left')
            ->orderBy('auditoria.created_at', 'DESC');

        if (!empty($filtros['usuario'])) {
            $builder->like('auditoria.usuario_nombre', $filtros['usuario']);
        }

        if (!empty($filtros['modulo'])) {
            $builder->where('auditoria.modulo', $filtros['modulo']);
        }

        if (!empty($filtros['accion'])) {
            $builder->where('auditoria.accion', $filtros['accion']);
        }

        if (!empty($filtros['fecha_desde'])) {
            $builder->where('DATE(auditoria.created_at) >=', $filtros['fecha_desde']);
        }

        if (!empty($filtros['fecha_hasta'])) {
            $builder->where('DATE(auditoria.created_at) <=', $filtros['fecha_hasta']);
        }

        return $builder;
    }

    /**
     * Obtener estadísticas de auditoría
     */
    public function getEstadisticas()
    {
        return [
            'total_eventos' => $this->countAll(),
            'eventos_hoy' => $this->where('DATE(created_at)', date('Y-m-d'))->countAllResults(false),
            'por_modulo' => $this->select('modulo, COUNT(*) as total')
                ->groupBy('modulo')
                ->orderBy('total', 'DESC')
                ->findAll(10),
            'por_accion' => $this->select('accion, COUNT(*) as total')
                ->groupBy('accion')
                ->orderBy('total', 'DESC')
                ->findAll(10),
            'usuarios_activos' => $this->select('usuario_nombre, COUNT(*) as total')
                ->where('DATE(created_at)', date('Y-m-d'))
                ->groupBy('usuario_nombre')
                ->orderBy('total', 'DESC')
                ->findAll(10)
        ];
    }

    /**
     * Registrar evento de auditoría
     */
    public static function registrar($accion, $modulo, $registro_id = null, $detalles = null)
    {
        $session = session();
        $request = service('request');
        
        // Obtener datos de sesión con valores por defecto
        $usuario_id = $session->get('usuario_id') ?? $session->get('id');
        $usuario_nombre = $session->get('usuario') ?? $session->get('nombre') ?? 'Sistema';
        
        $data = [
            'usuario_id' => $usuario_id,
            'usuario_nombre' => (string)$usuario_nombre,
            'accion' => (string)$accion,
            'modulo' => (string)$modulo,
            'registro_id' => $registro_id,
            'detalles' => is_array($detalles) ? json_encode($detalles) : $detalles,
            'ip_address' => $request->getIPAddress() ?? '0.0.0.0',
            'user_agent' => $request->getUserAgent()->getAgentString() ?? 'Unknown'
        ];
        
        try {
            $model = new self();
            $model->insert($data);
        } catch (\Exception $e) {
            // Log error pero no interrumpir la ejecución
            log_message('error', 'Error al registrar auditoría: ' . $e->getMessage());
        }
    }
}
