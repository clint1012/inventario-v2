<?php namespace App\Models;

use CodeIgniter\Model;

class UsuariosRolesModel extends Model
{
    protected $table = 'usuarios_roles';
    protected $primaryKey = null;
    protected $allowedFields = ['usuario_id','rol_id'];
    protected $returnType = 'array';
    public $incrementing = false;
}
