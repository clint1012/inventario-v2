<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OptimizacionesModel;
use App\Models\BienesModel;
use App\Models\LocalesModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class Optimizacion extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('optimizaciones o');
        $builder->select('
            b.cod_patrimonial,
            b.descripcion,
            o.optimizacion,
            o.motivo,
            o.fecha_modificacion,
            l.nombre AS local,
            o.id
        ');
        $builder->join('bienes b', 'b.id = o.bien_id');
        $builder->join('locales l', 'l.id = o.id_locales', 'left');
        $query = $builder->get();

        $data['optimizaciones'] = $query->getResultArray();

        return view('optimizacion/index', $data);
    }

    public function new()
    {
        $bienesModel = new BienesModel();
        $localesModel = new LocalesModel();

        $data = [
            'bienes' => $bienesModel->findAll(),
            'locales' => $localesModel->findAll()
        ];

        return view('optimizacion/form', $data);
    }

    public function show($id = null)
    {
        $optimizacionesModel = new OptimizacionesModel();
        $optimizacion = $optimizacionesModel->find($id);

        if (!$optimizacion) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Registro no encontrado']);
        }

        return $this->response->setJSON([
            'status' => 'ok',
            'optimizacion' => $optimizacion
        ]);
    }

    public function create()
    {
        log_message('debug', 'entro al metodo create de optimizacion');

        $optimizacionesModel = new OptimizacionesModel();

        try {
            $optimizacionesModel->insert([
                'bien_id' => $this->request->getPost('bien_id'),
                'optimizacion' => $this->request->getPost('optimizacion'),
                'motivo' => $this->request->getPost('motivo'),
                'fecha_modificacion' => date('Y-m-d'),
                'id_locales' => $this->request->getPost('id_locales'),
                'tipo_mantenimiento' => $this->request->getPost('tipo_mantenimiento'),
                'tecnico_responsable' => $this->request->getPost('tecnico_interno'),
                'empresa_externa' => $this->request->getPost('empresa_externa'),
                'tecnico_externo' => $this->request->getPost('tecnico_externo'),
            ]);

            return redirect()->to(base_url('optimizacion'))
                ->with('success', 'Optimización registrada correctamente.');
        } catch (DatabaseException $e) {
            return redirect()->back()->with('error', 'Error al guardar: ' . $e->getMessage());
        }
    }

    public function edit($id = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('optimizaciones o');
        $builder->select('o.*, COALESCE(l.nombre, "") AS local'); // COALESCE evita null
        $builder->join('locales l', 'l.id = o.id_locales', 'left');
        $builder->where('o.id', $id);
        $optimizacion = $builder->get()->getRowArray();

        if (!$optimizacion) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Registro no encontrado']);
        }

        return $this->response->setJSON([
            'status' => 'ok',
            'optimizacion' => $optimizacion
        ]);
    }


    public function update($id = null)
    {
        $optimizacionesModel = new \App\Models\OptimizacionesModel();

        $data = [
            'optimizacion' => $this->request->getPost('optimizacion'),
            'motivo' => $this->request->getPost('motivo'),
            'fecha_modificacion' => date('Y-m-d'),
        ];

        if ($optimizacionesModel->update($id, $data)) {
            // Obtener el registro actualizado con el nombre del local
            $db = \Config\Database::connect();
            $builder = $db->table('optimizaciones o');
            $builder->select('o.id, o.optimizacion, o.motivo, o.fecha_modificacion, l.nombre AS local');
            $builder->join('locales l', 'l.id = o.id_locales', 'left');
            $builder->where('o.id', $id);
            $registro = $builder->get()->getRowArray();

            return $this->response->setJSON([
                'status' => 'ok',
                'message' => 'Optimización actualizada correctamente.',
                'optimizacion' => $registro
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Error al actualizar los datos.'
            ]);
        }
    }


    public function delete($id = null)
    {
        $optimizacionesModel = new OptimizacionesModel();

        if ($optimizacionesModel->find($id)) {
            $optimizacionesModel->delete($id);
            return $this->response->setJSON(['status' => 'ok', 'message' => 'Registro eliminado correctamente']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Registro no encontrado']);
        }
    }

    public function buscarBien($codPatrimonial = null)
    {
        if (!$codPatrimonial) {
            return $this->response->setJSON(['error' => 'Código no proporcionado']);
        }

        $bienesModel = new \App\Models\BienesModel();
        $localesModel = new \App\Models\LocalesModel();

        // Buscar el bien por código patrimonial
        $bien = $bienesModel
            ->select('bienes.id, bienes.descripcion, bienes.id_locales, locales.nombre AS local')
            ->join('locales', 'locales.id = bienes.id_locales', 'left')
            ->where('bienes.cod_patrimonial', $codPatrimonial)
            ->first();

        if ($bien) {
            return $this->response->setJSON($bien);
        } else {
            return $this->response->setJSON(['error' => 'No se encontró el bien']);
        }
    }

    public function getPorCodigo($codigo)
    {
        $model = new \App\Models\BienesModel();
        $bien = $model->select('bienes.id, bienes.descripcion, bienes.id_locales, locales.nombre AS nombre_local')
            ->join('locales', 'locales.id = bienes.id_locales', 'left')
            ->where('bienes.cod_patrimonial', $codigo)
            ->first();

        return $this->response->setJSON($bien);
    }

}
