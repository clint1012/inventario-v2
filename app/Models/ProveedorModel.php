<?php

namespace App\Models;

use CodeIgniter\Model;

class ProveedorModel extends Model
{
    protected $table = 'proveedores';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nombre',
        'rep_legal',
        'ruc',
        'telefono',
        'tel_fijo',
        'correo',
        'direccion',
        'estado',
        'giro',
        'rnp',
        'ficha_ruc'
    ];
    protected $useTimestamps = true;
}
