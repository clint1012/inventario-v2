<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BienesModel;
use App\Models\DepartamentosModel;
use App\Models\LocalesModel;
use App\Models\PersonasModel;


class Bienes extends BaseController
{
    protected $bienesModel;

    public function __construct()
    {
        // Instancia el modelo BienesModel
        $this->bienesModel = new BienesModel();
        helper('form');
    }

    protected $helpers = ['form'];
    /**
     * Return an array of resource objects, themselves in array format.
     *
     * @return ResponseInterface
     */
    public function index()
    {
        //Obtener los bienes
        $bienesModel = new BienesModel();
        $departamentosModel = new DepartamentosModel();
        $personasModel = new PersonasModel();
        $localesModel = new LocalesModel();

        // Obtener todos los bienes
        $bienes = $bienesModel->where('estado !=', 'retirado')->findAll();

        // Obtener todos los locales
        $locales = $localesModel->findAll();
        $localesArray = array_column($locales, 'nombre', 'id');

        // Obtener los departamentos
        $departamentos = $departamentosModel->findAll();
        $departamentosArray = array_column($departamentos, 'nombre', 'id'); // Crea un array con el ID como clave y el nombre como valor

        // Mapear personas
        $personas = $personasModel->findAll();
        $personasArray = array_column($personas, 'nombre_completo', 'id');


        // Asignar los nombres de los departamentos a los bienes
        foreach ($bienes as &$bien) {
            $bien['nombre_departamento'] = $departamentosArray[$bien['id_departamento']] ?? 'Desconocido';
            $bien['nombre_persona'] = $personasArray[$bien['id_personas']] ?? 'No asignado';
            $bien['nombre_local'] = $localesArray[$bien['id_locales']] ?? 'Desconocido';
        }

        // Pasar los datos a la vista
        $data['bienes'] = $bienes;

        return view('bienes/index', $data);
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
        if ($id === null) {
            return redirect()->route('bienes');
        }

        $bienesModel = new BienesModel();
        $departamentosModel = new DepartamentosModel();
        $personasModel = new PersonasModel(); // Instancia del modelo Personas

        // Obtener los detalles del bien
        $bien = $bienesModel->find($id);

        if (!$bien) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Bien no encontrado.");
        }

        // Obtener la persona asociada al bien
        if ($bien['id_personas']) {
            $persona = $personasModel->find($bien['id_personas']);
            $bien['persona_nombre'] = $persona ? $persona['nombre_completo'] : 'No asignado';
        } else {
            $bien['persona_nombre'] = 'No asignado';
        }

        // Obtener todos los departamentos
        $departamentos = $departamentosModel->findAll();
        $departamentosArray = array_column($departamentos, 'nombre', 'id'); // Crear un array con el ID como clave y el nombre como valor

        // Asignar los nombres de los departamentos a los bienes
        $bien['nombre_departamento'] = $departamentosArray[$bien['id_departamento']] ?? 'Desconocido';

        // Pasar los datos a la vista
        $data['bien'] = $bien;
        $data['departamentos'] = $departamentos;

        return view('bienes/ver', $data);
    }

    /**
     * Return a new resource object, with default properties.
     *
     * @return ResponseInterface
     */
    public function new()
    {
        // Crear instancias de los modelos
        $departamentosModel = new DepartamentosModel();
        $personasModel = new PersonasModel();
        $localesModel = new LocalesModel();

        // Obtener los datos de departamentos y personas
        $data['departamentos'] = $departamentosModel->findAll();
        $data['personas'] = $personasModel->findAll();
        $data['locales'] = $localesModel->findAll();

        // Pasar los datos a la vista
        return view('bienes/nuevo', $data);
    }

    /**
     * Create a new resource object, from "posted" parameters.
     *
     * @return ResponseInterface
     */
    public function create()
    {
        $reglas = [
            'cod_patrimonial' => 'required|min_length[10]|max_length[12]|is_unique[bienes.cod_patrimonial]',
            'descripcion' => 'required',
            'marca' => 'required',
            'modelo' => 'required',
            'serie' => 'required',
            'estado' => 'required',
            'fecha_adquisicion' => 'required',
            'departamento' => 'required|is_not_unique[departamentos.id]',
            'id_personas' => 'required|is_not_unique[personas.id]',
            'id_locales' => 'required|is_not_unique[locales.id]',
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('error', message: $this->validator->listErrors());
        }

        $post = $this->request->getPost([
            'cod_patrimonial',
            'descripcion',
            'marca',
            'modelo',
            'serie',
            'procesador',
            'memoria',
            'sistema_operativo',
            'estado',
            'fecha_adquisicion',
            'años_garantia',
            'proveedor',
            'departamento',
            'id_personas',
            'id_locales'
        ]);

        $marca = $post['marca'] === 'otro' ? $this->request->getPost('otraMarca') : $post['marca'];
        $procesador = $post['procesador'] === 'otro' ? $this->request->getPost('otroProcesador') : $post['procesador'];
        $memoria = $post['memoria'] === 'otro' ? $this->request->getPost('otraMemoria') : $post['memoria'];
        $sistema_operativo = $post['sistema_operativo'] === 'otro' ? ($this->request->getPost('otroSO') ?? '') : $post['sistema_operativo'];

        $bienesModel = new BienesModel();
        $bienesModel->insert([
            'cod_patrimonial' => trim($post['cod_patrimonial']),
            'descripcion' => trim($post['descripcion']),
            'marca' => trim($marca),
            'modelo' => trim($post['modelo']),
            'serie' => trim($post['serie']),
            'procesador' => trim($procesador),
            'memoria' => trim($memoria),
            'sistema_operativo' => trim($sistema_operativo),
            'años_garantia' => trim($post['años_garantia']),
            'proveedor' => trim($post['proveedor']),
            'estado' => trim($post['estado']),
            'fecha_adquisicion' => trim($post['fecha_adquisicion']),
            'id_departamento' => trim($post['departamento']),
            'id_personas' => trim($post['id_personas']),
            'id_locales' => trim($post['id_locales']),
        ]);

        // Mensaje de confirmación
        session()->setFlashdata('success', 'Bien registrado exitosamente');

        return redirect()->to('bienes');
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
            return redirect()->route('bienes');
        }

        $bienesModel = new BienesModel();
        $departamentosModel = new DepartamentosModel();
        $personasModel = new PersonasModel(); // Instancia del modelo Personas
        $localesModel = new LocalesModel(); // Instancia del modelo Locales

        // Obtener datos necesarios para la vista
        $data['departamentos'] = $departamentosModel->findAll();
        $data['personas'] = $personasModel->findAll(); // Obtener todas las personas
        $data['locales'] = $localesModel->findAll(); // Obtener todos los locales
        $data['bien'] = $bienesModel->find($id);

        if (!$data['bien']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Bien no encontrado.");
        }

        // Obtener el nombre de la persona asociada al bien
        $persona = $personasModel->find($data['bien']['id_personas']);
        $data['persona_nombre'] = $persona ? $persona['nombre_completo'] : '';

        return view('bienes/editar', $data);
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
        if (!$this->request->is('put') || $id == null) {
            return redirect()->route('bienes');
        }
        //
        //
        $reglas = [
            'cod_patrimonial' => "required|min_length[10]|max_length[12]",
            'descripcion' => 'required',
            'marca' => 'required',
            'modelo' => 'required',
            'serie' => 'required',

            'estado' => 'required',
            'fecha_adquisicion' => 'required',
            'departamento' => 'required|is_not_unique[departamentos.id]'

        ];
        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }
        ;

        $post = $this->request->getPost([
            'cod_patrimonial',
            'descripcion',
            'marca',
            'modelo',
            'serie',
            'procesador',
            'memoria',
            'sistema_operativo',
            'estado',
            'fecha_adquisicion',
            'años_garantia',
            'proveedor',
            'id_personas',
            'departamento'
        ]);

        $marca = $post['marca'] === 'otro' ? $this->request->getPost('otraMarca') : $post['marca'];
        $procesador = $post['procesador'] === 'otro' ? $this->request->getPost('otroProcesador') : $post['procesador'];
        $memoria = $post['memoria'] === 'otro' ? $this->request->getPost('otraMemoria') : $post['memoria'];
        $sistema_operativo = $post['sistema_operativo'] === 'otro' ? ($this->request->getPost('otroSO') ?? '') : $post['sistema_operativo'];


        $bienesModel = new BienesModel();
        $bienesModel->update($id, [
            'cod_patrimonial' => trim($post['cod_patrimonial']),
            'descripcion' => trim($post['descripcion']),
            'marca' => trim($marca),
            'modelo' => trim($post['modelo']),
            'serie' => trim($post['serie']),
            'procesador' => trim($procesador),
            'memoria' => trim($memoria),
            'sistema_operativo' => trim($sistema_operativo),
            'años_garantia' => trim($post['años_garantia']),
            'proveedor' => trim($post['proveedor']),
            'estado' => trim($post['estado']),
            'fecha_adquisicion' => trim($post['fecha_adquisicion']),
            'id_personas' => trim($post['id_personas']),
            'id_departamento' => trim($post['departamento']),

        ]);
        return redirect()->to('bienes');
    }

    /**
     * Delete the designated resource object from the model.
     *
     * @param int|string|null $id
     *
     * @return ResponseInterface
     */
    public function desactivar()
    {
        // Obtener datos del formulario
        $bien_id = $this->request->getPost('bien_id');
        $motivo_baja = $this->request->getPost('motivo_baja');
        $usuario_baja = $this->request->getPost('usuario_baja');

        // Validar que los datos sean correctos
        if (!$bien_id || !$motivo_baja || !$usuario_baja) {
            return redirect()->to('bienes')->with('error', 'Todos los campos son obligatorios.');
        }

        // Verificar archivos
        $foto_frente = $this->request->getFile('foto_frente');
        $foto_lateral = $this->request->getFile('foto_lateral');

        if (!$foto_frente->isValid() || $foto_frente->hasMoved() || !$foto_lateral->isValid() || $foto_lateral->hasMoved()) {
            return redirect()->to('bienes')->with('error', 'Error al subir las imágenes.');
        }

        // Definir ruta de almacenamiento
        $ruta = 'uploads/bajas/';

        // Crear carpeta si no existe
        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }

        // Obtener nombres aleatorios y mover archivos
        $nombreFrente = $foto_frente->getRandomName();
        $nombreLateral = $foto_lateral->getRandomName();

        $foto_frente->move($ruta, $nombreFrente);
        $foto_lateral->move($ruta, $nombreLateral);

        // Guardar en la base de datos
        $data = [
            'estado' => 'retirado',
            'motivo_baja' => $motivo_baja,
            'usuario_baja' => $usuario_baja,
            'foto_frente' => $ruta . $nombreFrente,
            'foto_lateral' => $ruta . $nombreLateral,
        ];

        $this->bienesModel->update($bien_id, $data);

        return redirect()->to('bienes')->with('success', 'El bien ha sido dado de baja exitosamente.');
    }

    public function subida_masiva()
    {
        $archivo = $this->request->getFile('archivo');

        if ($archivo && $archivo->isValid() && !$archivo->hasMoved()) {
            $extension = $archivo->getClientExtension();

            if (!in_array($extension, ['csv', 'xls', 'xlsx'])) {
                return redirect()->back()->with('error', 'Formato de archivo no permitido.');
            }

            // Mover archivo al directorio temporal
            $nombreArchivo = $archivo->getRandomName();
            $archivo->move(WRITEPATH . 'uploads', $nombreArchivo);
            $rutaArchivo = WRITEPATH . 'uploads/' . $nombreArchivo;

            try {
                // Cargar el archivo con PhpSpreadsheet
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($rutaArchivo);
                $hoja = $spreadsheet->getActiveSheet();
                $datos = $hoja->toArray(null, true, true, true);

                // Procesar cada fila, omitiendo la cabecera
                foreach ($datos as $indice => $fila) {
                    if ($indice === 1) {
                        // Saltar la fila de encabezados
                        continue;
                    }

                    // Extraer datos de las columnas (ajusta según tu archivo)
                    $cod_patrimonial = $fila['A']; // Código patrimonial
                    $descripcion = $fila['B'];    // Descripción
                    $marca = $fila['C'];          // Marca
                    $id_departamento = $fila['D']; // ID del departamento
                    $estado = $fila['E'];         // Estado

                    // Validar y guardar en la base de datos
                    $data = [
                        'cod_patrimonial' => $cod_patrimonial,
                        'descripcion' => $descripcion,
                        'marca' => $marca,
                        'id_departamento' => $id_departamento,
                        'estado' => $estado,
                    ];

                    // Llama al modelo para guardar los datos
                    $this->bienesModel->insert($data); // Asegúrate de que `$bienesModel` esté configurado
                }

                return redirect()->to('bienes')->with('success', 'Archivo procesado correctamente.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Error al subir el archivo.');
    }

    public function getUsuariosSugeridos()
    {
        $term = $this->request->getGet('usuario'); // Obtener el término de búsqueda
        $personasModel = new \App\Models\PersonasModel();

        $personas = $personasModel
            ->like('nombre_completo', $term) // Busca nombres que comiencen con las letras ingresadas
            ->findAll(10); // Limitar a 10 resultados

        return $this->response->setJSON($personas);
    }


    public function verificarCodigo()
    {
        $cod_patrimonial = $this->request->getPost('cod_patrimonial');

        $bienesModel = new \App\Models\BienesModel(); // Asegúrate de que el modelo esté correctamente configurado
        $existe = $bienesModel->where('cod_patrimonial', $cod_patrimonial)->first();

        return $this->response->setJSON(['existe' => $existe ? true : false]);
    }

    public function getLocales()
    {
        $localesModel = new \App\Models\LocalesModel();
        $locales = $localesModel
            ->select('id,nombre')
            ->orderBy('nombre', 'ASC')
            ->findAll();
        return $this->response->setJSON($locales);
    }

    public function getDepartamentos()
    {
        $departamentosModel = new \App\Models\DepartamentosModel();
        $departamentos = $departamentosModel
            ->select('id,nombre')
            ->orderBy('nombre', 'ASC')
            ->findAll();
        return $this->response->setJSON($departamentos);
    }

    public function getMarcas()
    {
        $bienesModel = new \App\Models\BienesModel();
        $marcas = $bienesModel->distinct()
            ->select('marca')
            ->where('marca IS NOT NULL')
            ->orderBy('marca', 'ASC')
            ->findAll();

        return $this->response->setJSON($marcas);
    }

    // Obtener modelos únicos desde la tabla bienes
    public function getModelos()
    {
        $bienesModel = new \App\Models\BienesModel();
        $modelos = $bienesModel->distinct()
            ->select('modelo')
            ->where('modelo IS NOT NULL')
            ->orderBy('modelo', 'ASC')
            ->findAll();

        return $this->response->setJSON($modelos);
    }

    public function buscarDescripcion()
    {
        $term = $this->request->getGet('term');//palabra buscada
        $bienesModel = new \App\Models\BienesModel();

        $resultados = [];

        if ($term && strlen($term) >= 3) {
            $data = $bienesModel
                ->select('descripcion')
                ->like('descripcion', $term, 'both')
                ->distinct()
                ->limit(10)
                ->findAll();

            foreach ($data as $row) {
                $resultados[] = ['label' => $row['descripcion'], 'value' => $row['descripcion']];
            }
        }
        return $this->response->setJSON($resultados);
    }

    public function getMantenimiento()
    {

        $bien_id = $this->request->getPost('bien_id');
        $motivo_mantenimiento = $this->request->getPost('motivo_mantenimiento');
        $usuario_mantenimiento = $this->request->getPost('usuario_mantenimiento');
        $tipo_mantenimiento = $this->request->getPost('tipo_mantenimiento');

        // Validar que los datos sean correctos
        if (!$bien_id || !$motivo_mantenimiento || !$usuario_mantenimiento || !$tipo_mantenimiento) {
            return redirect()->to('bienes')->with('error', 'Todos los campos son obligatorios.');
        }

        $data = [
            'estado' => 'mantenimiento',
            'motivo_mantenimiento' => $motivo_mantenimiento,
            'usuario_mantenimiento' => $usuario_mantenimiento,
            'tipo_mantenimiento' => $tipo_mantenimiento,
        ];

        $this->bienesModel->update($bien_id, $data);

        return redirect()->to('bienes')->with('success', 'El bien ha sido enviado a mantenimiento exitosamente.');
    }
}
