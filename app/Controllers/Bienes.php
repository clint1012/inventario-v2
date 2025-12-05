<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BienesModel;
use App\Models\DepartamentosModel;
use App\Models\LocalesModel;
use App\Models\PersonasModel;
use App\Models\ProveedorModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


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


    public function show($id = null)
    {
        if ($id === null) {
            return redirect()->route('bienes');
        }

        $bienesModel = new BienesModel();
        $departamentosModel = new DepartamentosModel();
        $personasModel = new PersonasModel();
        $proveedorModel = new ProveedorModel(); // ← AGREGAR ESTO

        // Obtener los detalles del bien
        $bien = $bienesModel->find($id);

        if (!$bien) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Bien no encontrado.");
        }

        // Obtener la persona asociada (si existe)
        if ($bien['id_personas']) {
            $persona = $personasModel->find($bien['id_personas']);
            $bien['persona_nombre'] = $persona ? $persona['nombre_completo'] : 'No asignado';
        } else {
            $bien['persona_nombre'] = 'No asignado';
        }

        // Obtener nombre del proveedor
        if (!empty($bien['proveedor_id'])) {
            $proveedor = $proveedorModel->find($bien['proveedor_id']);
            $bien['proveedor_nombre'] = $proveedor ? $proveedor['nombre'] : 'No definido';
        } else {
            $bien['proveedor_nombre'] = 'No definido';
        }

        // Departamentos
        $departamentos = $departamentosModel->findAll();

        $data['bien'] = $bien;
        $data['departamentos'] = $departamentos;

        return view('bienes/ver', $data);
    }



    public function new()
    {
        // Crear instancias de los modelos
        $departamentosModel = new DepartamentosModel();
        $personasModel = new PersonasModel();
        $localesModel = new LocalesModel();
        $proveedorModel = new ProveedorModel();

        // Obtener los datos de departamentos y personas
        $data['departamentos'] = $departamentosModel->findAll();
        $data['personas'] = $personasModel->findAll();
        $data['locales'] = $localesModel->findAll();
        $data['proveedores'] = $proveedorModel->findAll();

        // Pasar los datos a la vista
        return view('bienes/nuevo', $data);
    }


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
            'tipo_disco',
            'espacio_disco',
            'sistema_operativo',
            'ver_office',
            'Ip',
            'estado',
            'fecha_adquisicion',
            'años_garantia',
            'proveedor_id',
            'num_doc_compra',
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
            'tipo_disco' => trim($post['tipo_disco']),
            'espacio_disco' => trim($post['espacio_disco']),
            'sistema_operativo' => trim($sistema_operativo),
            'ver_office' => trim($post['ver_office']),
            'Ip' => trim($post['Ip']),
            'años_garantia' => trim($post['años_garantia']),
            'proveedor_id' => trim($post['proveedor_id']),
            'num_doc_compra' => trim($post['num_doc_compra']),
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


    public function edit($id = null)
    {
        if ($id == null) {
            return redirect()->route('bienes');
        }

        $bienesModel = new BienesModel();
        $departamentosModel = new DepartamentosModel();
        $personasModel = new PersonasModel(); // Instancia del modelo Personas
        $localesModel = new LocalesModel(); // Instancia del modelo Locales
        $proveedorModel = new ProveedorModel();

        // Obtener datos necesarios para la vista
        $data['departamentos'] = $departamentosModel->findAll();
        $data['personas'] = $personasModel->findAll(); // Obtener todas las personas
        $data['locales'] = $localesModel->findAll(); // Obtener todos los locales
        $data['bien'] = $bienesModel->find($id);
        $data['proveedores'] = $proveedorModel->findAll();


        if (!$data['bien']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Bien no encontrado.");
        }

        // Obtener el nombre de la persona asociada al bien
        $persona = $personasModel->find($data['bien']['id_personas']);
        $data['persona_nombre'] = $persona ? $persona['nombre_completo'] : '';

        return view('bienes/editar', $data);
    }


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
            'tipo_disco',
            'espacio_disco',
            'sistema_operativo',
            'ver_office',
            'Ip',
            'estado',
            'fecha_adquisicion',
            'años_garantia',
            'proveedor_id',
            'num_doc_compra',
            'departamento',
            'id_personas',
            'id_locales'
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
            'tipo_disco' => trim($post['tipo_disco']),
            'espacio_disco' => trim($post['espacio_disco']),
            'sistema_operativo' => trim($sistema_operativo),
            'ver_office' => trim($post['ver_office']),
            'Ip' => trim($post['Ip']),
            'años_garantia' => trim($post['años_garantia']),
            'proveedor_id' => trim($post['proveedor_id']),
            'num_doc_compra' => trim($post['num_doc_compra']),
            'estado' => trim($post['estado']),
            'fecha_adquisicion' => trim($post['fecha_adquisicion']),
            'id_departamento' => trim($post['departamento']),
            'id_personas' => trim($post['id_personas']),
            'id_locales' => trim($post['id_locales']),

        ]);
        return redirect()->to('bienes');
    }


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

    public function detallePorCodigo()
    {
        $codigo = $this->request->getGet('codigo');
        if (!$codigo) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Código requerido']);
        }

        $bien = $this->bienesModel->where('cod_patrimonial', $codigo)->first();
        if (!$bien) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Bien no encontrado']);
        }

        return $this->response->setJSON([
            'id' => $bien['id'],
            'descripcion' => $bien['descripcion'],
            'marca' => $bien['marca'],
        ]);
    }


     public function buscarPorSbn()
    {
        $sbn = trim((string) $this->request->getGet('sbn'));
        if ($sbn === '') {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'SBN requerido']);
        }

        $bien = $this->bienesModel
            ->select('id, cod_patrimonial, descripcion, marca')
            ->where('cod_patrimonial', $sbn)
            ->first();

        if (!$bien) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Bien no encontrado']);
        }

        return $this->response->setJSON($bien);
    }

    public function autocompletarDescripcion()
    {
        $term = trim((string) $this->request->getGet('term'));
        if (mb_strlen($term) < 2) {
            return $this->response->setJSON([]);
        }

        $rows = $this->bienesModel
            ->select('descripcion')
            ->like('descripcion', $term, 'both')
            ->distinct()
            ->limit(10)
            ->findAll();

        $suggestions = array_map(
            fn($row) => ['label' => $row['descripcion'], 'value' => $row['descripcion']],
            $rows
        );

        return $this->response->setJSON($suggestions);
    }
    public function getMantenimiento()
    {
        $codigo = trim((string) $this->request->getPost('cod_patrimonial'));
        $bienId = $this->request->getPost('bien_id');

        if ($codigo !== '') {
            $bien = $this->bienesModel->where('cod_patrimonial', $codigo)->first();
        } elseif (!empty($bienId)) {
            $bien = $this->bienesModel->find($bienId);
        } else {
            return redirect()->back()->with('error', 'Debe indicar un bien.');
        }

        if (!$bien) {
            return redirect()->back()->with('error', 'El bien no existe.');
        }

        if ($bien['estado'] === 'mantenimiento') {
            return redirect()->back()->with('error', 'El bien ya se encuentra en mantenimiento.');
        }

        $motivo = trim($this->request->getPost('motivo_mantenimiento'));
        $usuario = trim($this->request->getPost('usuario_mantenimiento'));
        $tipo = trim($this->request->getPost('tipo_mantenimiento'));

        $this->bienesModel->update($bien['id'], [
            'motivo_mantenimiento' => $motivo,
            'usuario_mantenimiento' => $usuario,
            'tipo_mantenimiento' => $tipo,
            'estado' => 'mantenimiento',
        ]);

        return redirect()->back()->with('success', 'Bien enviado a mantenimiento.');
    }


    public function recuperarMantenimiento()
    {
        $bienId = $this->request->getPost('bien_id');

        if (!$bienId) {
            return redirect()->back()->with('error', 'Bien no especificado.');
        }

        $bien = $this->bienesModel->find($bienId);

        if (!$bien) {
            return redirect()->back()->with('error', 'El bien no existe.');
        }

        if ($bien['estado'] !== 'mantenimiento') {
            return redirect()->back()->with('info', 'El bien no está en mantenimiento.');
        }

        $this->bienesModel->update($bienId, [
            'estado' => 'bueno',
            'motivo_mantenimiento' => null,
            'usuario_mantenimiento' => null,
            'tipo_mantenimiento' => null,
        ]);

        return redirect()->back()->with('success', 'Bien recuperado de mantenimiento.');
    }
    public function exportarFiltrado()
    {
        $db = \Config\Database::connect();

        $builder = $db->table('bienes b')
            ->select("
                b.id, b.cod_patrimonial, b.descripcion, b.marca, b.modelo, b.serie,
                b.procesador, b.memoria, b.tipo_disco, b.espacio_disco,
                b.sistema_operativo, b.ver_office, b.Ip, b.estado,
                b.fecha_adquisicion, b.años_garantia, b.estado_garantia,
                b.proveedor_id, b.num_doc_compra,
                COALESCE(d.nombre, 'Sin asignar') AS departamento_nombre,
                COALESCE(p.nombre_completo, 'No asignado') AS persona_nombre,
                COALESCE(l.nombre, 'Sin asignar') AS local_nombre
            ")
            ->join('departamentos d', 'd.id = b.id_departamento', 'left')
            ->join('personas p', 'p.id = b.id_personas', 'left')
            ->join('locales l', 'l.id = b.id_locales', 'left')
            ->where('b.estado !=', 'retirado');

        $get = $this->request->getGet();
        log_message('debug', 'Params: ' . json_encode($get));

        $this->aplicarFiltrosBienes($builder, $get);

        $builder->orderBy('b.id', 'ASC');

        log_message('debug', 'SQL: ' . $builder->getCompiledSelect(false));

        $rows = $builder->get()->getResultArray();

        log_message('debug', 'Total rows: ' . count($rows));

        return $this->generarExcelBienes($rows, 'filtrados');
    }

    public function exportarGeneral()
    {
        $db = \Config\Database::connect();

        $rows = $db->table('bienes b')
            ->select("
                b.id, b.cod_patrimonial, b.descripcion, b.marca, b.modelo, b.serie,
                b.procesador, b.memoria, b.tipo_disco, b.espacio_disco,
                b.sistema_operativo, b.ver_office, b.Ip, b.estado,
                b.fecha_adquisicion, b.años_garantia, b.estado_garantia,
                b.proveedor_id, b.num_doc_compra,
                COALESCE(d.nombre, 'Sin asignar') AS departamento_nombre,
                COALESCE(p.nombre_completo, 'No asignado') AS persona_nombre,
                COALESCE(l.nombre, 'Sin asignar') AS local_nombre
            ")
            ->join('departamentos d', 'd.id = b.id_departamento', 'left')
            ->join('personas p', 'p.id = b.id_personas', 'left')
            ->join('locales l', 'l.id = b.id_locales', 'left')
            ->where('b.estado !=', 'retirado')
            ->orderBy('b.id', 'ASC')
            ->get()
            ->getResultArray();

        log_message('debug', 'Export general rows: ' . count($rows));

        return $this->generarExcelBienes($rows, 'general');
    }

    private function generarExcelBienes(array $rows, string $sufijoNombre)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Bienes');

        $headers = [
            'ID',
            'Código Patrimonial',
            'Descripción',
            'Marca',
            'Modelo',
            'Serie',
            'Procesador',
            'Memoria',
            'Tipo Disco',
            'Espacio Disco',
            'S.O.',
            'Office',
            'IP',
            'Estado',
            'Fecha Adquisición',
            'Años Garantía',
            'Estado Garantía',
            'Proveedor ID',
            'Documento Compra',
            'Departamento',
            'Persona',
            'Local'
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:U1')->getFont()->setBold(true);
        $sheet->getStyle('A1:U1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFD3D3D3');

        if (!empty($rows)) {
            $rowNum = 2;
            foreach ($rows as $r) {
                $sheet->fromArray([
                    $r['id'] ?? '',
                    $r['cod_patrimonial'] ?? '',
                    $r['descripcion'] ?? '',
                    $r['marca'] ?? '',
                    $r['modelo'] ?? '',
                    $r['serie'] ?? '',
                    $r['procesador'] ?? '',
                    $r['memoria'] ?? '',
                    $r['tipo_disco'] ?? '',
                    $r['espacio_disco'] ?? '',
                    $r['sistema_operativo'] ?? '',
                    $r['ver_office'] ?? '',
                    $r['Ip'] ?? '',
                    $r['estado'] ?? '',
                    $r['fecha_adquisicion'] ?? '',
                    $r['años_garantia'] ?? '',
                    $r['estado_garantia'] ?? '',
                    $r['proveedor_id'] ?? '',
                    $r['num_doc_compra'] ?? '',
                    $r['departamento_nombre'] ?? 'Sin asignar',
                    $r['persona_nombre'] ?? 'No asignado',
                    $r['local_nombre'] ?? 'Sin asignar'
                ], null, 'A' . $rowNum);
                $rowNum++;
            }
        } else {
            $sheet->setCellValue('A2', 'Sin datos que mostrar');
        }

        $widths = [8, 15, 25, 12, 12, 12, 12, 12, 10, 12, 12, 12, 15, 12, 15, 12, 15, 12, 15, 15, 15, 15];
        $col = 'A';
        foreach ($widths as $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
            $col++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'bienes_' . $sufijoNombre . '_' . date('YmdHis') . '.xlsx';

        ob_start();
        $writer->save('php://output');
        $excelOutput = ob_get_clean();

        return $this->response
            ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setHeader('Cache-Control', 'max-age=0')
            ->setHeader('Pragma', 'public')
            ->setBody($excelOutput);
    }

    private function aplicarFiltrosBienes($builder, array $filters)
    {
        if (empty($filters)) {
            return;
        }

        if (!empty($filters['search'])) {
            $s = trim($filters['search']);
            $builder->groupStart()
                ->like('b.cod_patrimonial', $s)
                ->orLike('b.descripcion', $s)
                ->orLike('b.marca', $s)
                ->orLike('b.modelo', $s)
                ->orLike('b.serie', $s)
                ->groupEnd();
        }

        if (!empty($filters['cod_patrimonial'])) {
            $builder->like('b.cod_patrimonial', trim($filters['cod_patrimonial']));
        }
        if (!empty($filters['descripcion'])) {
            $builder->like('b.descripcion', trim($filters['descripcion']));
        }
        if (!empty($filters['marca'])) {
            $builder->like('b.marca', trim($filters['marca']));
        }
        if (!empty($filters['modelo'])) {
            $builder->like('b.modelo', trim($filters['modelo']));
        }
        if (!empty($filters['serie'])) {
            $builder->like('b.serie', trim($filters['serie']));
        }
        if (!empty($filters['local'])) {
            $builder->like('l.nombre', trim($filters['local']));
        }
        if (!empty($filters['departamento'])) {
            $builder->like('d.nombre', trim($filters['departamento']));
        }
        if (!empty($filters['estado'])) {
            $builder->where('b.estado', trim($filters['estado']));
        }
        if (!empty($filters['fecha_adquisicion'])) {
            $builder->where('b.fecha_adquisicion', trim($filters['fecha_adquisicion']));
        }
        if (!empty($filters['estado_garantia'])) {
            $builder->where('b.estado_garantia', trim($filters['estado_garantia']));
        }
        if (!empty($filters['usuario'])) {
            $builder->like('p.nombre_completo', trim($filters['usuario']));
        }
    }

     public function actualizarUbicacion()
    {
        $bienId        = (int) $this->request->getPost('bien_id');
        $localId       = (int) $this->request->getPost('id_locales');
        $departamentoId = (int) $this->request->getPost('id_departamento');

        if ($bienId <= 0 || $localId <= 0 || $departamentoId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Datos inválidos.']);
        }

        $bien = $this->bienesModel->find($bienId);
        if (!$bien) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Bien no encontrado.']);
        }

        $this->bienesModel->update($bienId, [
            'id_locales'      => $localId,
            'id_departamento' => $departamentoId,
        ]);

        return $this->response->setJSON(['message' => 'Ubicación actualizada.']);
    }

}
