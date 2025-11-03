<?php

namespace App\Models;

use CodeIgniter\Model;

class ProveedorModel extends Model
{
    protected $table = 'proveedores';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nombre',
        'ruc',
        'telefono',
        'correo',
        'direccion',
        'estado',
        'giro',
        'rnp',
        'ficha_ruc'
    ];
    protected $useTimestamps = true;
}
