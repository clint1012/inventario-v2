<?php

namespace App\Models;

use CodeIgniter\Model;

class IpModel extends Model
{
    protected $table = 'ips';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'direccion_ip',
        'estado',
        'bien_id',
        'observaciones',
        'fecha_asignacion',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $returnType    = 'array';

    // Para obtener datos con JOIN a la tabla bienes
    public function obtenerConBienes()
    {
        return $this->select('ips.*, bienes.cod_patrimonial, bienes.descripcion')
                    ->join('bienes', 'bienes.id = ips.bien_id', 'left')
                    ->findAll();
    }

    // IPs disponibles (no asignadas)
    public function obtenerDisponibles()
    {
        return $this->where('estado', 'disponible')->findAll();
    }
}