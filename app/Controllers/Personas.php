<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PersonasModel;
use App\Models\RegimenLaboralModel;

class Personas extends BaseController
{
    protected $helpers = ['form'];
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        //
        $personasModel = new PersonasModel();
        $data['personas'] = $personasModel->findAll();
        return view('personas/index',$data);
    }

    /**
     * Return the properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function show($id = null)
    {
        //
        if ($id === null) {
            return redirect()->route('personas');
        }
        $personasModel = new PersonasModel();
        $regimenLaboralModel = new RegimenLaboralModel();

        $data ['persona'] = $personasModel->find($id);
        $data ['regimen_laboral'] = $regimenLaboralModel->findAll();

        if(!$data['persona']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("persona no encontrada");
        }

        return view('personas/ver',$data);
        }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        // Crear instancias de los modelos
        
        $regimenLaboralModel = new RegimenLaboralModel();

        // Obtener los datos de departamentos y personas
        
        $data['regimen_laboral'] = $regimenLaboralModel->findAll();
       
        // Pasar los datos a la vista
        return view('personas/nuevo', $data);
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        //
        $reglas = [
            'dni' => 'required|min_length[8]|max_length[8]|is_unique[bienes.cod_patrimonial]',
            'nombre' => 'required',
            'ape_paterno' => 'required',
            'ape_materno' => 'required',
            'id_regimen_laboral' => 'required',
        ];
        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        };

        $post = $this->request->getPost([
            'nombre',
            'ape_paterno',
            'ape_materno',
            'id_regimen_laboral',
            'dni'
        ]);

        $personasModel = new PersonasModel();
        $personasModel->insert([
            'dni' => trim($post['dni']),
            'nombre' => trim($post['nombre']),
            'ape_paterno' => trim($post['ape_paterno']),
            'ape_materno' => trim($post['ape_materno']),
            'id_regimen_laboral' => trim($post['estado']),

        ]);
        return redirect()->to('personas');

    }

    /**
     * Return the editable properties of a resource object.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function edit($id = null)
    {
        if ($id == null) {
            return redirect()->route('personas');
        }
        //

        $personasModel = new PersonasModel(); // Instancia del modelo Personas
        $regimenLaboralModel = new RegimenLaboralModel();
        
        // Obtener datos necesarios para la vista
       
        $data['persona'] = $personasModel->find($id); // Obtener todas las personas
        $data['regimen_laboral'] = $regimenLaboralModel->findAll();

        if (!$data['persona']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("persona no encontrado.");
        }

        return view('personas/editar', $data);
    }

    /**
     * Add or update a model resource, from "posted" properties.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function update($id = null)
    {
        //
        if (!$this->request->is('put') || $id == null) {
            return redirect()->route('personas');
        }

        $reglas = [
            'dni' => 'required|min_length[8]|max_length[8]|is_unique[bienes.cod_patrimonial]',
            'nombre' => 'required',
            'ape_paterno' => 'required',
            'ape_materno' => 'required',
            'id_regimen_laboral' => 'required',
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        };

        $post = $this->request->getPost([
            'nombre',
            'ape_paterno',
            'ape_materno',
            'id_regimen_laboral',
            'dni'
        ]);

        $personasModel = new PersonasModel();
        $personasModel->update($id,[
            'dni' => trim($post['dni']),
            'nombre' => trim($post['nombre']),
            'ape_paterno' => trim($post['ape_paterno']),
            'ape_materno' => trim($post['ape_materno']),
            'id_regimen_laboral' => trim($post['estado']),
        ]);

        return redirect()->to('personas');
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function delete($id = null)
    {
        //
    }
}
