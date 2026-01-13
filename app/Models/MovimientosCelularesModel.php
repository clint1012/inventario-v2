<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientosCelularesModel extends Model
{
    protected $table = 'movimientos_celulares';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_celular',
        'id_personas',
        'id_departamentos',
        'id_locales',
        'tipo_movimiento',
        'fecha_movimiento',
        'observaciones',
        'responsable_nombre',
        'lote',
        'anulado',
        'motivo_anulacion'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Obtener movimientos con detalles completos
     */
    public function getMovimientosConDetalles()
    {
        return $this->select('movimientos_celulares.*, 
                celulares.numero_serie, celulares.imei, celulares.modelo, celulares.descripcion as celular_desc,
                personas.nombre, personas.ape_paterno, personas.ape_materno,
                departamentos.nombre as departamento, 
                locales.nombre as local')
            ->join('celulares', 'celulares.id = movimientos_celulares.id_celular')
            ->join('personas', 'personas.id = movimientos_celulares.id_personas', 'left')
            ->join('departamentos', 'departamentos.id = movimientos_celulares.id_departamentos', 'left')
            ->join('locales', 'locales.id = movimientos_celulares.id_locales', 'left')
            ->where('movimientos_celulares.anulado', 0)
            ->orderBy('movimientos_celulares.fecha_movimiento', 'DESC')
            ->findAll();
    }

    /**
     * Obtener movimientos por lote
     */
    public function getMovimientosPorLote($lote)
    {
        return $this->select('movimientos_celulares.*, 
                celulares.numero_serie, celulares.imei, celulares.modelo, celulares.descripcion as celular_desc,
                personas.nombre, personas.ape_paterno, personas.ape_materno,
                departamentos.nombre as departamento, 
                locales.nombre as local')
            ->join('celulares', 'celulares.id = movimientos_celulares.id_celular')
            ->join('personas', 'personas.id = movimientos_celulares.id_personas', 'left')
            ->join('departamentos', 'departamentos.id = movimientos_celulares.id_departamentos', 'left')
            ->join('locales', 'locales.id = movimientos_celulares.id_locales', 'left')
            ->where('movimientos_celulares.lote', $lote)
            ->findAll();
    }

    /**
     * Obtener resumen de movimientos agrupados por lote
     */
    public function getResumenMovimientosAgrupado()
    {
        return $this->db->table('movimientos_celulares mc')
            ->select("
                mc.lote,
                MAX(mc.fecha_movimiento) AS fecha_movimiento,
                mc.anulado,
                MAX(mc.tipo_movimiento) AS tipo_movimiento,
                MAX(p.nombre) AS nombre,
                MAX(p.ape_paterno) AS ape_paterno,
                MAX(p.ape_materno) AS ape_materno,
                COUNT(mc.id) as cantidad_celulares,
                GROUP_CONCAT(CONCAT(c.modelo, ' (', COALESCE(c.numero_serie, 'S/N'), ')') SEPARATOR ', ') as celulares_detalle
            ")
            ->join('personas p', 'p.id = mc.id_personas', 'left')
            ->join('celulares c', 'c.id = mc.id_celular', 'left')
            ->groupBy('mc.lote')
            ->orderBy('MAX(mc.fecha_movimiento)', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Obtener último movimiento de un celular
     */
    public function getUltimoMovimientoCelular($idCelular)
    {
        return $this->where('id_celular', $idCelular)
                    ->where('anulado', 0)
                    ->orderBy('fecha_movimiento', 'DESC')
                    ->first();
    }
}
