<?php

namespace App\Controllers;

use App\Models\LicenciasModel;
use App\Models\AuditoriaModel;
use CodeIgniter\RESTful\ResourceController;

class Licencias extends ResourceController
{
    protected $modelName = 'App\Models\LicenciasModel';
    protected $format = 'json';

    public function index()
    {
        // Detectar si la petición viene por AJAX (DataTables)
        if ($this->request->isAJAX()) {
            $licencias = $this->model->findAll();
            return $this->respond(['data' => $licencias]);
        }

        // Si no es AJAX, carga la vista normal
        return view('licencias/index');
    }

    public function show($id = null)
    {
        $licencia = $this->model->find($id);
        if (!$licencia) {
            return $this->failNotFound('Licencia no encontrada');
        }
        return $this->respond($licencia);
    }

    public function create()
    {
        $data = $this->request->getPost();
        if ($licencia_id = $this->model->insert($data)) {
            // Registrar auditoría
            AuditoriaModel::registrar('CREAR', 'Licencias', $licencia_id, [
                'software' => $data['software'] ?? '',
                'tipo_licencia' => $data['tipo_licencia'] ?? ''
            ]);
            
            return $this->respondCreated(['status' => 'ok', 'message' => 'Licencia creada correctamente']);
        }
        return $this->failValidationErrors($this->model->errors());
    }

    public function update($id = null)
    {
        $data = $this->request->getRawInput();
        if ($this->model->update($id, $data)) {
            // Registrar auditoría
            AuditoriaModel::registrar('EDITAR', 'Licencias', $id, [
                'software' => $data['software'] ?? '',
                'tipo_licencia' => $data['tipo_licencia'] ?? ''
            ]);
            
            return $this->respond(['status' => 'ok', 'message' => 'Licencia actualizada']);
        }
        return $this->failValidationErrors($this->model->errors());
    }

    public function delete($id = null)
    {
        $licencia = $this->model->find($id);
        if ($this->model->delete($id)) {
            // Registrar auditoría
            AuditoriaModel::registrar('ELIMINAR', 'Licencias', $id, [
                'software' => $licencia['software'] ?? '',
                'tipo_licencia' => $licencia['tipo_licencia'] ?? ''
            ]);
            
            return $this->respondDeleted(['status' => 'ok', 'message' => 'Licencia eliminada']);
        }
        return $this->failNotFound('No se pudo eliminar la licencia');
    }

    public function proximasAVencer()
    {
        $diasAviso = 30;

        $licencias = $this->model
            ->where('fecha_expiracion IS NOT NULL')
            ->where('fecha_expiracion <=', date('Y-m-d', strtotime("+$diasAviso days")))
            ->findAll();

        return $this->respond([
            "cantidad" => count($licencias),
            "licencias" => $licencias
        ]);
    }
}
