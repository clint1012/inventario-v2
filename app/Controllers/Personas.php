<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PersonasModel;
use App\Models\RegimenLaboralModel;
use App\Models\LocalesModel;

class Personas extends BaseController
{
    protected $helpers = ['form'];

    public function index()
    {
        $personasModel = new PersonasModel();

        // JOIN con locales y regimen_laboral
        $data['personas'] = $personasModel
            ->select('personas.*, locales.nombre AS nombre_local, regimen_laboral.regimen_laboral AS nombre_regimen')
            ->join('locales', 'locales.id = personas.id_locales', 'left')
            ->join('regimen_laboral', 'regimen_laboral.id = personas.id_regimen_laboral', 'left')
            ->findAll();

        return view('personas/index', $data);
    }


    public function show($id)
    {
        $personasModel = new PersonasModel();
        $regimenModel = new RegimenLaboralModel();
        $localesModel = new LocalesModel();

        $persona = $personasModel
            ->select('personas.*, regimen_laboral.regimen_laboral, locales.nombre as nombre_local')
            ->join('regimen_laboral', 'personas.id_regimen_laboral = regimen_laboral.id', 'left')
            ->join('locales', 'personas.id_locales = locales.id', 'left')
            ->find($id);

        if (!$persona) {
            return redirect()->to('personas')->with('error', 'Persona no encontrada');
        }

        $data = [
            'titulo' => 'Detalles de la Persona',
            'persona' => $persona,
        ];

        return view('personas/ver', $data);
    }


    public function new()
    {
        // Crear instancias de los modelos

        $localesModel = new LocalesModel();
        $regimenModel = new RegimenLaboralModel();

        // Obtener los datos de departamentos y personas

        $data = [
            'locales' => $localesModel->findAll(),
            'regimenes' => $regimenModel->findAll(),
        ];

        // Pasar los datos a la vista
        return view('personas/nuevo', $data);
    }


    public function create()
    {
        //
        $reglas = [
            'dni' => 'required|min_length[8]|max_length[8]|is_unique[personas.dni]',
            'nombre' => 'required',
            'ape_paterno' => 'required',
            'ape_materno' => 'required',
            'id_regimen_laboral' => 'required|integer',
            'id_locales' => 'required|integer',
        ];
        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }
        ;

        $post = $this->request->getPost([
            'dni',
            'nombre',
            'ape_paterno',
            'ape_materno',
            'id_regimen_laboral',
            'fecha_inicio',
            'fecha_fin',
            'correo',
            'telefono',
            'direccion_domiciliaria',
            'modalidad',
            'id_locales'
        ]);

        $personasModel = new PersonasModel();
        $personasModel->insert([
            'dni' => trim($post['dni']),
            'nombre' => trim($post['nombre']),
            'ape_paterno' => trim($post['ape_paterno']),
            'ape_materno' => trim($post['ape_materno']),
            'id_regimen' => $post['id_regimen_laboral'],
            'fecha_inicio' => $post['fecha_inicio'],
            'fecha_fin' => $post['fecha_fin'],
            'correo' => $post['correo'],
            'telefono' => $post['telefono'],
            'direccion_domiciliaria' => $post['direccion domiciliaria'],
            'modalidad' => $post['modalidad'],
            'id_locales' => $post['id_locales']

        ]);
        return redirect()->to(base_url('personas'))->with('success', 'Persona creada correctamente');

    }


    public function edit($id)
    {
        $personasModel = new PersonasModel();
        $regimenModel = new RegimenLaboralModel();
        $localesModel = new LocalesModel();

        $persona = $personasModel->find($id);

        if (!$persona) {
            return redirect()->to('personas')->with('error', 'Persona no encontrada');
        }

        $data = [
            'persona' => $persona,
            'regimenes' => $regimenModel->findAll(),
            'locales' => $localesModel->findAll(),
        ];

        return view('personas/editar', $data);
    }


    public function update($id)
    {
        $personasModel = new PersonasModel();

        $reglas = [
            'dni' => "required|min_length[8]|max_length[8]",
            'nombre' => 'required',
            'ape_paterno' => 'required',
            'ape_materno' => 'required',
            'id_regimen_laboral' => 'required',
            'id_locales' => 'required',
            'correo' => 'required|valid_email',
            'telefono' => 'required',
            'direccion_domiciliaria' => 'required',
            'fecha_inicio' => 'required|valid_date',
            'fecha_fin' => 'required|valid_date'
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $post = $this->request->getPost([
            'dni',
            'nombre',
            'ape_paterno',
            'ape_materno',
            'id_regimen_laboral',
            'id_locales',
            'correo',
            'telefono',
            'direccion_domiciliaria',
            'modalidad',
            'fecha_inicio',
            'fecha_fin'
        ]);

        $personasModel->update($id, $post);

        return redirect()->to('personas')->with('success', 'Datos actualizados correctamente');
    }


    public function delete($id = null)
    {
        //
    }

    public function desactivar($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Solicitud inválida.']);
        }

        if (empty($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID no especificado.']);
        }

        $motivo = trim($this->request->getPost('motivo_cese') ?? '');
        if ($motivo === '') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Ingrese un motivo de cese.']);
        }

        $personasModel = new \App\Models\PersonasModel();
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('personas');

        $data = [
            'estado' => 'inactivo'
        ];

        if (in_array('motivo_cese', $fields)) {
            $data['motivo_cese'] = $motivo;
        } elseif (in_array('observaciones', $fields)) {
            $current = $personasModel->find($id);
            $obs = trim($current['observaciones'] ?? '');
            $data['observaciones'] = ($obs === '' ? '' : $obs . "\n") . 'Motivo cese: ' . $motivo;
        }

        if (in_array('fecha_cese', $fields)) {
            $data['fecha_cese'] = date('Y-m-d H:i:s');
        }

        // Evitar llamar update con array vacío
        if (empty($data)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No hay datos para actualizar.']);
        }

        try {
            $personasModel->update($id, $data);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error al actualizar: ' . $e->getMessage()]);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario desactivado correctamente.', 'id' => $id]);
    }


    public function recuperar($id = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Solicitud inválida.']);
        }

        if (empty($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID no especificado.']);
        }

        $personasModel = new \App\Models\PersonasModel();
        $person = $personasModel->find($id);

        if (!$person) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Usuario no encontrado.']);
        }

        // Preparar datos a actualizar
        $data = ['estado' => 'activo'];

        // Solo intentar resetear si las columnas existen
        $db = \Config\Database::connect();
        $fields = $db->getFieldNames('personas');
        if (in_array('motivo_cese', $fields)) {
            $data['motivo_cese'] = null;
        }
        if (in_array('fecha_cese', $fields)) {
            $data['fecha_cese'] = null;
        }

        // Evitar update vacío
        if (empty($data)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No hay datos para actualizar.']);
        }

        try {
            $personasModel->update($id, $data);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Error al actualizar: ' . $e->getMessage()]);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => 'Usuario recuperado y motivo/fecha de cese reseteados.', 'id' => $id]);
    }
}
