<?php namespace App\Models;

use CodeIgniter\Model;

class UsuariosRolesModel extends Model
{
    protected $table = 'usuarios_roles';
    protected $primaryKey = null;
    protected $allowedFields = ['usuario_id','rol_id'];
    protected $returnType = 'array';
    public $incrementing = false;


    public function getRolesByUsuario($usuarioId)
{
    return $this->select('roles.id, roles.nombre')
        ->join('roles', 'roles.id = usuarios_roles.rol_id')
        ->where('usuarios_roles.usuario_id', $usuarioId)
        ->findAll();
}
}


