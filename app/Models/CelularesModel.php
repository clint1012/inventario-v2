<?php

namespace App\Models;

use CodeIgniter\Model;

class CelularesModel extends Model
{
    protected $table = 'celulares';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';

    protected $allowedFields = [
        'numero_serie',
        'imei',
        'modelo',
        'descripcion',
        'estado'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    protected $validationRules = [
        'imei' => 'required|is_unique[celulares.imei,id,{id}]',
        'modelo' => 'required',
    ];

    protected $validationMessages = [
        'imei' => [
            'required' => 'El IMEI es obligatorio',
            'is_unique' => 'Este IMEI ya está registrado'
        ],
        'modelo' => [
            'required' => 'El modelo es obligatorio'
        ]
    ];

    /**
     * Obtener celulares disponibles
     */
    public function getCelularesDisponibles()
    {
        return $this->where('estado', 'disponible')
                    ->orderBy('modelo', 'ASC')
                    ->findAll();
    }

    /**
     * Obtener celulares asignados
     */
    public function getCelularesAsignados()
    {
        return $this->where('estado', 'asignado')
                    ->orderBy('modelo', 'ASC')
                    ->findAll();
    }

    /**
     * Buscar celular por IMEI
     */
    public function buscarPorImei($imei)
    {
        return $this->where('imei', $imei)->first();
    }
}
