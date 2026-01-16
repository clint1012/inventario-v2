<?php namespace App\Controllers;

use App\Models\RolesModel;
use App\Models\PermisosModel;
use App\Models\RolesPermisosModel;
use App\Models\AuditoriaModel;

class Roles extends BaseController
{
    protected $rolesModel;
    protected $permisosModel;
    protected $rolesPermisos;

    public function __construct()
    {
        $this->rolesModel = new RolesModel();
        $this->permisosModel = new PermisosModel();
        $this->rolesPermisos = new RolesPermisosModel();
    }

    public function index()
    {
        $roles = $this->rolesModel->findAll();
        $permisos = $this->permisosModel->findAll();
        return view('roles/index', compact('roles','permisos'));
    }

    public function store()
    {
        $post = $this->request->getPost();
        $rol_id = $this->rolesModel->insert(['nombre'=>$post['nombre'],'descripcion'=>$post['descripcion']]);
        
        // Registrar auditoría
        AuditoriaModel::registrar('CREAR', 'Roles', $rol_id, [
            'nombre' => $post['nombre'],
            'descripcion' => $post['descripcion']
        ]);
        
        return redirect()->to('/roles')->with('success','Rol creado');
    }

    public function update($id)
    {
        $post = $this->request->getPost();
        $this->rolesModel->update($id, ['nombre'=>$post['nombre'],'descripcion'=>$post['descripcion']]);
        
        // Registrar auditoría
        AuditoriaModel::registrar('EDITAR', 'Roles', $id, [
            'nombre' => $post['nombre'],
            'descripcion' => $post['descripcion']
        ]);
        
        return redirect()->to('/roles')->with('success','Rol actualizado');
    }

    public function delete($id)
    {
        $rol = $this->rolesModel->find($id);
        $this->rolesModel->delete($id);
        
        // Registrar auditoría
        AuditoriaModel::registrar('ELIMINAR', 'Roles', $id, [
            'nombre' => $rol['nombre'] ?? ''
        ]);
        
        return redirect()->to('/roles')->with('success','Rol eliminado');
    }

    public function assignPermisos($id)
    {
        $permisos = $this->request->getPost('permisos') ?? [];
        $this->rolesPermisos->where('rol_id',$id)->delete();
        foreach ($permisos as $p) {
            $this->rolesPermisos->insert(['rol_id'=>$id, 'permiso_id'=>$p]);
        }
        
        // Registrar auditoría
        $rol = $this->rolesModel->find($id);
        AuditoriaModel::registrar('ASIGNAR_PERMISOS', 'Roles', $id, [
            'rol' => $rol['nombre'] ?? '',
            'cantidad_permisos' => count($permisos)
        ]);
        
        return redirect()->to('/roles')->with('success','Permisos asignados');
    }
}
