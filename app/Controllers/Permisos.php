<?php namespace App\Controllers;

use App\Models\PermisosModel;

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
        $this->model->insert(['clave'=>$p['clave'],'descripcion'=>$p['descripcion']]);
        return redirect()->to('/permisos')->with('success','Permiso creado');
    }

    public function update($id)
    {
        $p = $this->request->getPost();
        $this->model->update($id, ['clave'=>$p['clave'],'descripcion'=>$p['descripcion']]);
        return redirect()->to('/permisos')->with('success','Permiso actualizado');
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect()->to('/permisos')->with('success','Permiso eliminado');
    }
}
