<?php namespace App\Models;

use CodeIgniter\Model;

class PermisosModel extends Model
{
    protected $table = 'permisos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['clave','nombre','descripcion','creado_en'];
    protected $returnType = 'array';
}
