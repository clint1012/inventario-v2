<?php namespace App\Controllers;

use App\Models\PermisosModel;
use App\Models\AuditoriaModel;

class Permisos extends BaseController
{
    protected $model;
    public function __construct()
    {
        $this->model = new \App\Models\PermisosModel();
    }

    public function index()
    {
        $permisos = $this->model->findAll();
        return view('permisos/index', compact('permisos'));
    }

    public function store()
    {
        $p = $this->request->getPost();
        $permiso_id = $this->model->insert(['clave'=>$p['clave'],'descripcion'=>$p['descripcion']]);
        
        // Registrar auditoría
        AuditoriaModel::registrar('CREAR', 'Permisos', $permiso_id, [
            'clave' => $p['clave'],
            'descripcion' => $p['descripcion']
        ]);
        
        return redirect()->to('/permisos')->with('success','Permiso creado');
    }

    public function update($id)
    {
        $p = $this->request->getPost();
        $this->model->update($id, ['clave'=>$p['clave'],'descripcion'=>$p['descripcion']]);
        
        // Registrar auditoría
        AuditoriaModel::registrar('EDITAR', 'Permisos', $id, [
            'clave' => $p['clave'],
            'descripcion' => $p['descripcion']
        ]);
        
        return redirect()->to('/permisos')->with('success','Permiso actualizado');
    }

    public function delete($id)
    {
        $permiso = $this->model->find($id);
        $this->model->delete($id);
        
        // Registrar auditoría
        AuditoriaModel::registrar('ELIMINAR', 'Permisos', $id, [
            'clave' => $permiso['clave'] ?? ''
        ]);
        
        return redirect()->to('/permisos')->with('success','Permiso eliminado');
    }
}
