<?php namespace App\Models;

use CodeIgniter\Model;

class RolesPermisosModel extends Model
{
    protected $table = 'roles_permisos';
    protected $primaryKey = null;
    protected $allowedFields = ['rol_id','permiso_id'];
    protected $returnType = 'array';
    public $incrementing = false;
}
