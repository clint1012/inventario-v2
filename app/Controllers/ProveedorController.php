<?php

namespace App\Controllers;

use App\Models\ProveedorModel;
use App\Models\AuditoriaModel;
use Mpdf\Mpdf;

class ProveedorController extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new ProveedorModel();
        helper(['form', 'url']);
    }

    // GET /proveedores → lista todos los proveedores
    public function index()
    {
        if ($this->request->isAJAX()) {
            $proveedores = $this->model->findAll();
            return $this->response->setJSON(['data' => $proveedores]); // <-- aquí
        }

        return view('proveedores/index');
    }


    // GET /proveedores/:id → mostrar un proveedor
    public function show($id = null)
    {
        $proveedor = $this->model->find($id);
        if (!$proveedor) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Proveedor no encontrado']);
        }
        return $this->response->setJSON($proveedor);
    }

    // POST /proveedores → crear
    public function create()
    {
        $data = $this->request->getPost();

        // Archivos
        $rnp = $this->request->getFile('rnp');
        $ficha = $this->request->getFile('ficha_ruc');

        if ($rnp && $rnp->isValid()) {
            $data['rnp'] = $rnp->getRandomName();
            $rnp->move('uploads/proveedores', $data['rnp']);
        }
        if ($ficha && $ficha->isValid()) {
            $data['ficha_ruc'] = $ficha->getRandomName();
            $ficha->move('uploads/proveedores', $data['ficha_ruc']);
        }

        $proveedor_id = $this->model->insert($data);
        
        // Registrar auditoría
        AuditoriaModel::registrar('CREAR', 'Proveedores', $proveedor_id, [
            'razon_social' => $data['razon_social'] ?? '',
            'ruc' => $data['ruc'] ?? ''
        ]);
        
        return redirect()->to(base_url('proveedor'))->with('success', 'Proveedor creado correctamente');
    }

    // PUT /proveedores/:id → actualizar
    public function update($id = null)
    {
        $data = $this->request->getRawInput();
        $this->model->update($id, $data);
        
        // Registrar auditoría
        AuditoriaModel::registrar('EDITAR', 'Proveedores', $id, [
            'razon_social' => $data['razon_social'] ?? '',
            'ruc' => $data['ruc'] ?? ''
        ]);
        
        return $this->response->setJSON(['status' => 'ok', 'message' => 'Proveedor actualizado']);
    }

    // DELETE /proveedores/:id → eliminar
    public function delete($id = null)
    {
        $proveedor = $this->model->find($id);
        $this->model->delete($id);
        
        // Registrar auditoría
        AuditoriaModel::registrar('ELIMINAR', 'Proveedores', $id, [
            'razon_social' => $proveedor['razon_social'] ?? '',
            'ruc' => $proveedor['ruc'] ?? ''
        ]);
        
        return $this->response->setJSON(['status' => 'ok', 'message' => 'Proveedor eliminado']);
    }

    // GET /proveedores/pdf/:id → ruta extra para PDF
    public function pdf($id)
    {
        $proveedor = $this->model->find($id);
        if (!$proveedor)
            return redirect()->back();

        $mpdf = new Mpdf();
        $html = view('proveedores/pdf', ['proveedor' => $proveedor]);
        $mpdf->WriteHTML($html);
        $mpdf->Output("proveedor_{$id}.pdf", 'I');
        exit;
    }
}
