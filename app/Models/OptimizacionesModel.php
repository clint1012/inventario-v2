<?php

namespace App\Models;

use CodeIgniter\Model;

class OptimizacionesModel extends Model
{
    protected $table = 'optimizaciones';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'bien_id',
        'optimizacion',
        'motivo',
        'fecha_modificacion',
        'id_locales',
        'tipo_mantenimiento',
        'tecnico_responsable',
        'empresa_externa',
        'tecnico_externo'
    ];

    protected $useTimestamps = false; // ya existe fecha de modificacion manual
}