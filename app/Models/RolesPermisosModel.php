<?php namespace App\Models;

use CodeIgniter\Model;

class RolesPermisosModel extends Model
{
    protected $table = 'roles_permisos';
    protected $primaryKey = null;
    protected $allowedFields = ['rol_id','permiso_id'];
    protected $returnType = 'array';
    public $incrementing = false;


    public function getPermisosByRoles($rolesIds)
{
    if (empty($rolesIds)) return [];

    return $this->select('permisos.clave')
        ->join('permisos', 'permisos.id = roles_permisos.permiso_id')
        ->whereIn('roles_permisos.rol_id', $rolesIds)
        ->findAll();
}
}
