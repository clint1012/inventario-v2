<?php

namespace App\Models;

use CodeIgniter\Model;

class AsignacionModel extends Model
{
    protected $table            = 'movimientos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields = [
        'id_bienes',
        'id_personas',
        'id_departamentos',
        'id_locales',
        'id_persona_anterior',
        'tipo_movimiento',
        'fecha_movimiento',
        'observaciones',
        'lote'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getMovimientosConDetalles()
    {
        return $this->select('movimientos.*, bienes.cod_patrimonial, bienes.descripcion, 
                personas.nombre, personas.ape_paterno, personas.ape_materno,
                p2.nombre as nombre_anterior, p2.ape_paterno as apep_anterior, p2.ape_materno as apem_anterior,
                departamentos.nombre as departamento, locales.nombre as local, movimientos.lote')
            ->join('bienes', 'bienes.id = movimientos.id_bienes')
            ->join('personas', 'personas.id = movimientos.id_personas', 'left')
            ->join('personas as p2', 'p2.id = movimientos.id_persona_anterior', 'left')
            ->join('departamentos', 'departamentos.id = movimientos.id_departamentos', 'left')
            ->join('locales', 'locales.id = movimientos.id_locales', 'left')
            ->orderBy('movimientos.fecha_movimiento', 'DESC')
            ->findAll();
    }

    public function getMovimientoById($id)
    {
        return $this->select('movimientos.*, bienes.cod_patrimonial, bienes.descripcion, 
                bienes.marca, bienes.modelo, bienes.serie,
                personas.nombre, personas.ape_paterno, personas.ape_materno,
                p2.nombre as nombre_anterior, p2.ape_paterno as apep_anterior, p2.ape_materno as apem_anterior,
                departamentos.nombre as departamento, locales.nombre as local')
            ->join('bienes', 'bienes.id = movimientos.id_bienes')
            ->join('personas', 'personas.id = movimientos.id_personas', 'left')
            ->join('personas as p2', 'p2.id = movimientos.id_persona_anterior', 'left')
            ->join('departamentos', 'departamentos.id = movimientos.id_departamentos', 'left')
            ->join('locales', 'locales.id = movimientos.id_locales', 'left')
            ->where('movimientos.id', $id)
            ->first();
    }

   public function getResumenUsuarios()
{
    return $this->db->table('movimientos m')
        ->select('m.lote, m.tipo_movimiento, m.fecha_movimiento, 
                  m.id_personas, p.nombre, p.ape_paterno, p.ape_materno, 
                  d.nombre as departamento, l.nombre as local')
        ->join('personas p', 'p.id = m.id_personas', 'left')
        ->join('departamentos d', 'd.id = m.id_departamentos', 'left')
        ->join('locales l', 'l.id = m.id_locales', 'left')
        ->orderBy('m.fecha_movimiento', 'DESC')
        ->get()
        ->getResultArray();
}




public function getBienesCambio($idPersona)
{
    // Bienes retirados
    $retirados = $this->db->table('movimientos m')
        ->select('b.cod_patrimonial, b.descripcion, b.marca, b.modelo, b.serie,
                  d.nombre AS departamento, l.nombre AS local')
        ->join('bienes b', 'b.id = m.id_bienes')
        ->join('departamentos d', 'd.id = m.id_departamentos', 'left')
        ->join('locales l', 'l.id = m.id_locales', 'left')
        ->where('m.id_personas', $idPersona)
        ->where('m.tipo_movimiento', 'retiro')
        ->get()
        ->getResultArray();

    // Bienes asignados
    $asignados = $this->db->table('movimientos m')
        ->select('b.cod_patrimonial, b.descripcion, b.marca, b.modelo, b.serie,
                  d.nombre AS departamento, l.nombre AS local')
        ->join('bienes b', 'b.id = m.id_bienes')
        ->join('departamentos d', 'd.id = m.id_departamentos', 'left')
        ->join('locales l', 'l.id = m.id_locales', 'left')
        ->where('m.id_personas', $idPersona)
        ->where('m.tipo_movimiento', 'asignacion')
        ->get()
        ->getResultArray();

    return [
        'retirados' => $retirados,
        'asignados' => $asignados
    ];
}

public function getBienesPorLoteYTipo($idPersona, $tipo, $lote)
{
    return $this->db->table('movimientos m')
        ->select('b.cod_patrimonial, b.descripcion, b.marca, b.modelo, b.serie,
                  d.nombre AS departamento, l.nombre AS local')
        ->join('bienes b', 'b.id = m.id_bienes')
        ->join('departamentos d', 'd.id = m.id_departamentos', 'left')
        ->join('locales l', 'l.id = m.id_locales', 'left')
        ->where('m.id_personas', $idPersona)
        ->where('m.tipo_movimiento', $tipo)
        ->where('m.lote', $lote)
        ->orderBy('m.fecha_movimiento', 'DESC')
        ->get()
        ->getResultArray();
}


}
