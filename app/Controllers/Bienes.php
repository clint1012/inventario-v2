<?php

namespace App\Controllers;

use App\Config\AppConstants;
use App\Controllers\BaseController;
use App\Models\AuditoriaModel;
use App\Models\BienesModel;
use App\Models\DepartamentosModel;
use App\Models\LocalesModel;
use App\Models\PersonasModel;
use App\Models\ProveedorModel;
use CodeIgniter\HTTP\RedirectResponse;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Bienes extends BaseController
{
    private BienesModel $bienesModel;
    private DepartamentosModel $departamentosModel;
    private PersonasModel $personasModel;
    private LocalesModel $localesModel;
    private ProveedorModel $proveedorModel;

    public function __construct()
    {
        $this->bienesModel = new BienesModel();
        $this->departamentosModel = new DepartamentosModel();
        $this->personasModel = new PersonasModel();
        $this->localesModel = new LocalesModel();
        $this->proveedorModel = new ProveedorModel();
        helper('form');
    }

    public function index(): string
    {
        // Obtener todos los bienes
        $bienes = $this->bienesModel->where('estado !=', AppConstants::ESTADO_RETIRADO)->findAll();

        // Obtener datos de catálogos
        $localesArray = array_column($this->localesModel->findAll(), 'nombre', 'id');
        $departamentosArray = array_column($this->departamentosModel->findAll(), 'nombre', 'id');
        $personasArray = array_column($this->personasModel->findAll(), 'nombre_completo', 'id');


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


    public function show(?int $id = null)
    {
        if ($id === null) {
            return redirect()->route('bienes');
        }

        // Obtener los detalles del bien
        $bien = $this->bienesModel->find($id);

        if (!$bien) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Bien no encontrado.");
        }

        // Obtener la persona asociada (si existe)
        if ($bien['id_personas']) {
            $persona = $this->personasModel->find($bien['id_personas']);
            $bien['persona_nombre'] = $persona ? $persona['nombre_completo'] : 'No asignado';
        } else {
            $bien['persona_nombre'] = 'No asignado';
        }

        // Obtener nombre del proveedor
        if (!empty($bien['proveedor_id'])) {
            $proveedor = $this->proveedorModel->find($bien['proveedor_id']);
            $bien['proveedor_nombre'] = $proveedor ? $proveedor['nombre'] : 'No definido';
        } else {
            $bien['proveedor_nombre'] = 'No definido';
        }

        // Departamentos
        $departamentos = $this->departamentosModel->findAll();

        $data['bien'] = $bien;
        $data['departamentos'] = $departamentos;

        return view('bienes/ver', $data);
    }



    public function new(): string
    {
        // Obtener los datos de catálogos
        $data['departamentos'] = $this->departamentosModel->findAll();
        $data['personas'] = $this->personasModel->findAll();
        $data['locales'] = $this->localesModel->findAll();
        $data['proveedores'] = $this->proveedorModel->findAll();

        // Pasar los datos a la vista
        return view('bienes/nuevo', $data);
    }


    public function create(): RedirectResponse
    {
        if (!$this->validate($this->getValidationRules())) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $post = $this->obtenerDatosFormulario();
        $datosLimpios = $this->procesarCamposPersonalizados($post);

        $bien_id = $this->bienesModel->insert($datosLimpios);
        
        // Registrar en auditoría
        AuditoriaModel::registrar('CREAR', 'Bienes', $bien_id, [
            'cod_patrimonial' => $datosLimpios['cod_patrimonial'] ?? null,
            'descripcion' => $datosLimpios['descripcion'] ?? null,
            'estado' => $datosLimpios['estado'] ?? null
        ]);
        
        session()->setFlashdata('success', 'Bien registrado exitosamente');

        return redirect()->to('bienes');
    }


    public function edit(?int $id = null)
    {
        if ($id === null) {
            return redirect()->route('bienes');
        }

        // Obtener datos necesarios para la vista
        $data['departamentos'] = $this->departamentosModel->findAll();
        $data['personas'] = $this->personasModel->findAll();
        $data['locales'] = $this->localesModel->findAll();
        $data['bien'] = $this->bienesModel->find($id);
        $data['proveedores'] = $this->proveedorModel->findAll();


        if (!$data['bien']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Bien no encontrado.");
        }

        // Obtener el nombre de la persona asociada al bien
        $persona = $this->personasModel->find($data['bien']['id_personas']);
        $data['persona_nombre'] = $persona ? $persona['nombre_completo'] : '';

        return view('bienes/editar', $data);
    }


    public function update(?int $id = null): RedirectResponse
    {
        if (!$this->request->is('put') || $id === null) {
            return redirect()->route('bienes');
        }

        if (!$this->validate($this->getValidationRulesUpdate())) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $post = $this->obtenerDatosFormulario();
        $datosLimpios = $this->procesarCamposPersonalizados($post);

        $this->bienesModel->update($id, $datosLimpios);
        
        // Registrar en auditoría
        AuditoriaModel::registrar('EDITAR', 'Bienes', $id, [
            'campos_modificados' => array_keys($datosLimpios)
        ]);
        
        return redirect()->to('bienes');
    }


    public function desactivar(): RedirectResponse
    {
        $bien_id = $this->request->getPost('bien_id');
        $motivo_baja = $this->request->getPost('motivo_baja');
        $usuario_baja = $this->request->getPost('usuario_baja');

        if (!$bien_id || !$motivo_baja || !$usuario_baja) {
            return redirect()->to('bienes')->with('error', 'Todos los campos son obligatorios.');
        }

        $foto_frente = $this->request->getFile('foto_frente');
        $foto_lateral = $this->request->getFile('foto_lateral');

        if (!$this->validarArchivos($foto_frente, $foto_lateral)) {
            return redirect()->to('bienes')->with('error', 'Error al subir las imágenes.');
        }

        $rutasImagenes = $this->guardarImagenesBaja($foto_frente, $foto_lateral);

        $data = [
            'estado' => AppConstants::ESTADO_RETIRADO,
            'motivo_baja' => $motivo_baja,
            'usuario_baja' => $usuario_baja,
            'foto_frente' => $rutasImagenes['foto_frente'],
            'foto_lateral' => $rutasImagenes['foto_lateral'],
        ];

        $this->bienesModel->update($bien_id, $data);
        
        // Registrar en auditoría
        $bien = $this->bienesModel->find($bien_id);
        AuditoriaModel::registrar('ELIMINAR', 'Bienes', $bien_id, [
            'cod_patrimonial' => $bien['cod_patrimonial'] ?? null,
            'motivo_baja' => $motivo_baja,
            'usuario_baja' => $usuario_baja
        ]);

        return redirect()->to('bienes')->with('success', 'El bien ha sido dado de baja exitosamente.');
    }

    /**
     * Descargar plantilla CSV con todos los bienes actuales para edición masiva
     */
    public function descargarPlantillaCSV()
    {
        // Obtener todos los bienes activos con sus relaciones
        $bienes = $this->bienesModel
            ->select('bienes.*, 
                locales.nombre as local_nombre,
                departamentos.nombre as departamento_nombre,
                personas.nombre as persona_nombre,
                personas.ape_paterno,
                personas.ape_materno')
            ->join('locales', 'locales.id = bienes.id_locales', 'left')
            ->join('departamentos', 'departamentos.id = bienes.id_departamento', 'left')
            ->join('personas', 'personas.id = bienes.id_personas', 'left')
            ->where('bienes.estado !=', AppConstants::ESTADO_RETIRADO)
            ->findAll();

        // Crear archivo CSV
        $filename = 'plantilla_bienes_' . date('Y-m-d_His') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $output = fopen('php://output', 'w');
        
        // BOM para UTF-8 en Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Encabezados
        $headers = [
            'id',
            'cod_patrimonial',
            'descripcion',
            'tipo_bien',
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
            'local_nombre',
            'departamento_nombre',
            'persona_nombre_completo',
            'fecha_adquisicion',
            'años_garantia',
            'num_doc_compra'
        ];
        
        // Usar punto y coma como delimitador (estándar Excel español)
        fputcsv($output, $headers, ';');
        
        // Datos
        foreach ($bienes as $bien) {
            $nombreCompleto = trim(
                ($bien['persona_nombre'] ?? '') . ' ' . 
                ($bien['ape_paterno'] ?? '') . ' ' . 
                ($bien['ape_materno'] ?? '')
            );
            
            $row = [
                $bien['id'],
                $bien['cod_patrimonial'] ?? '',
                $bien['descripcion'] ?? '',
                $bien['tipo_bien'] ?? '',
                $bien['marca'] ?? '',
                $bien['modelo'] ?? '',
                $bien['serie'] ?? '',
                $bien['procesador'] ?? '',
                $bien['memoria'] ?? '',
                $bien['tipo_disco'] ?? '',
                $bien['espacio_disco'] ?? '',
                $bien['sistema_operativo'] ?? '',
                $bien['ver_office'] ?? '',
                $bien['Ip'] ?? '',
                $bien['estado'] ?? '',
                $bien['local_nombre'] ?? '',
                $bien['departamento_nombre'] ?? '',
                $nombreCompleto ?: 'Sin asignar',
                $bien['fecha_adquisicion'] ?? '',
                $bien['años_garantia'] ?? '',
                $bien['num_doc_compra'] ?? ''
            ];
            
            // Usar punto y coma como delimitador
            fputcsv($output, $row, ';');
        }
        
        fclose($output);
        exit;
    }

    /**
     * Detectar automáticamente el delimitador del CSV (coma o punto y coma)
     */
    private function detectarDelimitadorCSV(string $filePath): string
    {
        $handle = fopen($filePath, 'r');
        $primeraLinea = fgets($handle);
        fclose($handle);
        
        // Contar apariciones de cada delimitador
        $comas = substr_count($primeraLinea, ',');
        $puntosComa = substr_count($primeraLinea, ';');
        
        // Retornar el delimitador más usado
        return $puntosComa > $comas ? ';' : ',';
    }

    /**
     * Procesar subida masiva de bienes desde CSV
     */
    public function subida_masiva()
    {
        $file = $this->request->getFile('archivo_csv');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'No se ha seleccionado un archivo válido.');
        }
        
        if ($file->getExtension() !== 'csv') {
            return redirect()->back()->with('error', 'El archivo debe ser formato CSV.');
        }
        
        // Detectar delimitador automáticamente
        $delimiter = $this->detectarDelimitadorCSV($file->getTempName());
        
        $handle = fopen($file->getTempName(), 'r');
        
        // Leer encabezados con el delimitador detectado
        $headers = fgetcsv($handle, 0, $delimiter);
        
        // Limpiar BOM UTF-8 del primer encabezado si existe
        if (!empty($headers[0])) {
            $headers[0] = str_replace("\xEF\xBB\xBF", '', $headers[0]);
        }
        
        // Convertir headers a UTF-8 si están en otra codificación
        $headers = array_map(function($header) {
            $header = trim($header);
            // Detectar y convertir a UTF-8 si es necesario
            if (!mb_check_encoding($header, 'UTF-8')) {
                // Intentar desde ISO-8859-1 (Latin1) o Windows-1252
                $header = mb_convert_encoding($header, 'UTF-8', 'ISO-8859-1,Windows-1252');
            }
            return $header;
        }, $headers);
        
        $validacion = $this->validarEncabezadosCSV($headers);
        if ($validacion !== true) {
            fclose($handle);
            return redirect()->back()->with('error', $validacion);
        }
        
        $actualizados = 0;
        $creados = 0;
        $omitidos = 0;
        $errores = [];
        $lineaActual = 1; // Empezando después del encabezado
        
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $lineaActual++;
            
            // Convertir cada campo a UTF-8 si es necesario
            $data = array_map(function($field) {
                if (!mb_check_encoding($field, 'UTF-8')) {
                    return mb_convert_encoding($field, 'UTF-8', 'ISO-8859-1,Windows-1252');
                }
                return $field;
            }, $data);
            
            try {
                $resultado = $this->procesarLineaCSV($data, $headers);
                if ($resultado === 'creado') {
                    $creados++;
                } elseif ($resultado === 'actualizado') {
                    $actualizados++;
                } elseif ($resultado === 'omitido') {
                    $omitidos++;
                }
            } catch (\Exception $e) {
                $errores[] = "Línea $lineaActual: " . $e->getMessage();
            }
        }
        
        fclose($handle);
        
        // Registrar en auditoría
        $detallesAuditoria = [
            'archivo' => $file->getName(),
            'creados' => $creados,
            'actualizados' => $actualizados,
            'omitidos' => $omitidos,
            'errores' => count($errores),
            'total_procesado' => $creados + $actualizados
        ];
        registrar_auditoria('importar_csv', 'bienes', null, $detallesAuditoria);
        
        $mensaje = "Procesamiento completado: ";
        if ($creados > 0) {
            $mensaje .= "$creados nuevo(s) bien(es) creado(s). ";
        }
        if ($actualizados > 0) {
            $mensaje .= "$actualizados bien(es) actualizado(s). ";
        }
        if ($omitidos > 0) {
            $mensaje .= "($omitidos líneas vacías omitidas). ";
        }
        if ($creados === 0 && $actualizados === 0) {
            $mensaje = "No se realizaron cambios.";
            if ($omitidos > 0) {
                $mensaje .= " ($omitidos líneas vacías omitidas)";
            }
        }
        
        if (count($errores) > 0) {
            // Guardar todos los errores en el log para revisión detallada
            log_message('error', '=== Errores importación CSV (' . count($errores) . ' errores) ===');
            foreach ($errores as $error) {
                log_message('error', '  - ' . $error);
            }
            log_message('error', '=== Fin errores CSV ===');
            
            // Mensaje resumido para el usuario
            $mensaje .= " Errores: " . implode('; ', array_slice($errores, 0, 5));
            if (count($errores) > 5) {
                $mensaje .= " y " . (count($errores) - 5) . " más... (Ver log en writable/logs/)";
            }
        }
        
        return redirect()->to('bienes')->with('success', $mensaje);
    }

    /**
     * Validar que los encabezados del CSV sean correctos
     * @return true si es válido, string con mensaje de error si no
     */
    private function validarEncabezadosCSV(array $headers)
    {
        $expectedHeaders = [
            'id',
            'cod_patrimonial',
            'descripcion',
            'tipo_bien',
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
            'local_nombre',
            'departamento_nombre',
            'persona_nombre_completo',
            'fecha_adquisicion',
            'años_garantia',
            'num_doc_compra'
        ];
        
        // Verificar que tenga el número correcto de columnas
        if (count($headers) !== count($expectedHeaders)) {
            return 'Se esperaban ' . count($expectedHeaders) . ' columnas, se encontraron ' . count($headers) . '.';
        }
        
        // Comparar encabezados y mostrar diferencias
        $errores = [];
        foreach ($expectedHeaders as $index => $expected) {
            if (!isset($headers[$index])) {
                $errores[] = "Falta columna en posición " . ($index + 1) . ": '$expected'";
                continue;
            }
            
            $headerValue = trim($headers[$index]);
            
            // Comparación case-insensitive
            if (strtolower($headerValue) !== strtolower($expected)) {
                $errores[] = "Columna " . ($index + 1) . ": esperado '$expected', encontrado '$headerValue'";
            }
        }
        
        if (!empty($errores)) {
            return 'Errores en encabezados: ' . implode(' | ', $errores);
        }
        
        return true;
    }

    /**
     * Procesar una línea del CSV: crear nuevo bien o actualizar existente
     * @return string 'creado', 'actualizado', 'sin_cambios' o 'omitido'
     */
    private function procesarLineaCSV(array $data, array $headers): string
    {
        // Crear array asociativo
        $row = array_combine($headers, $data);
        
        $id = trim($row['id'] ?? '');
        $codPatrimonial = trim($row['cod_patrimonial'] ?? '');
        
        // Ignorar líneas completamente vacías o sin código patrimonial
        if (empty($codPatrimonial)) {
            // Verificar si todos los campos importantes están vacíos
            $camposImportantes = ['descripcion', 'tipo_bien', 'marca', 'modelo', 'serie'];
            $tieneAlgunDato = false;
            foreach ($camposImportantes as $campo) {
                if (!empty(trim($row[$campo] ?? ''))) {
                    $tieneAlgunDato = true;
                    break;
                }
            }
            
            // Si no tiene código patrimonial ni otros datos, omitir silenciosamente
            if (!$tieneAlgunDato) {
                return 'omitido';
            }
            
            // Si tiene otros datos pero no código patrimonial, es un error
            throw new \Exception('Código patrimonial es obligatorio');
        }
        
        // Determinar si es creación o actualización
        $bienActual = null;
        $esNuevo = false;
        
        if (!empty($id) && is_numeric($id)) {
            // Buscar por ID
            $bienActual = $this->bienesModel->find($id);
        }
        
        if (!$bienActual) {
            // Buscar por código patrimonial
            $bienActual = $this->bienesModel->where('cod_patrimonial', $codPatrimonial)->first();
            
            if (!$bienActual) {
                // Es un bien nuevo
                $esNuevo = true;
            }
        }
        
        if ($esNuevo) {
            // Crear nuevo bien
            return $this->crearNuevoBienDesdeCSV($row);
        } else {
            // Actualizar bien existente
            return $this->actualizarBienDesdeCSV($row, $bienActual);
        }
    }
    
    /**
     * Crear un nuevo bien desde el CSV
     */
    private function crearNuevoBienDesdeCSV(array $row): string
    {
        $datos = [];
        
        // Campos directos
        $camposDirectos = [
            'cod_patrimonial',
            'descripcion',
            'tipo_bien',
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
            'num_doc_compra'
        ];
        
        foreach ($camposDirectos as $campo) {
            $valor = $row[$campo] ?? '';
            
            // Normalizar: eliminar espacios y detectar valores vacíos
            $valor = trim($valor);
            $esVacio = ($valor === '' || $valor === '-' || strtolower($valor) === 'null' || strtolower($valor) === 'n/a');
            
            if ($esVacio) {
                // Campos obligatorios con valores por defecto
                if ($campo === 'estado') {
                    $datos[$campo] = AppConstants::ESTADO_DISPONIBLE;
                } elseif ($campo === 'fecha_adquisicion') {
                    $datos[$campo] = date('Y-m-d'); // Fecha actual por defecto
                } else {
                    $datos[$campo] = null;
                }
            } else {
                $datos[$campo] = $valor;
            }
        }
        
        // Procesar relaciones
        $this->agregarRelacionesNuevoBien($row, $datos);
        
        // Validar y limpiar claves foráneas antes de insertar
        $this->validarClavesForaneas($datos);
        
        // Log de depuración (temporal)
        log_message('debug', 'Datos a insertar: ' . json_encode($datos));
        
        // Verificar que los campos obligatorios estén presentes (incluyendo id_personas si es NOT NULL)
        if (!isset($datos['id_locales']) || !isset($datos['id_departamento']) || !isset($datos['id_personas'])) {
            throw new \Exception('Faltan campos obligatorios: id_locales, id_departamento o id_personas no definidos');
        }
        
        // Insertar
        $this->bienesModel->insert($datos);
        
        return 'creado';
    }
    
    /**
     * Actualizar un bien existente desde el CSV
     */
    private function actualizarBienDesdeCSV(array $row, array $bienActual): string
    {
        $cambios = [];
        
        // Campos directos
        $camposDirectos = [
            'cod_patrimonial',
            'descripcion',
            'tipo_bien',
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
            'num_doc_compra'
        ];
        
        foreach ($camposDirectos as $campo) {
            $valorNuevo = trim($row[$campo] ?? '');
            $valorActual = $bienActual[$campo] ?? '';
            
            // Normalizar valores vacíos (detectar más variantes)
            if ($valorNuevo === '' || $valorNuevo === '-' || strtolower($valorNuevo) === 'null' || strtolower($valorNuevo) === 'n/a') {
                $valorNuevo = null;
            }
            if ($valorActual === '' || $valorActual === '-' || strtolower($valorActual) === 'null') {
                $valorActual = null;
            }
            
            // Comparar y agregar si hay cambio
            if ($valorNuevo != $valorActual) {
                $cambios[$campo] = $valorNuevo;
            }
        }
        
        // Procesar relaciones (local, departamento, persona) si cambiaron
        $this->procesarRelacionesCSV($row, $bienActual, $cambios);
        
        // Si hay cambios, actualizar
        if (count($cambios) > 0) {
            $this->bienesModel->update($bienActual['id'], $cambios);
            return 'actualizado';
        }
        
        return 'sin_cambios';
    }
    
    /**
     * Agregar relaciones al crear un nuevo bien
     */
    private function agregarRelacionesNuevoBien(array $row, array &$datos): void
    {
        // Local - Si no se encuentra, usar ID por defecto: 5
        $localNombre = trim($row['local_nombre'] ?? '');
        if (!empty($localNombre) && $localNombre !== '-') {
            $local = $this->localesModel->where('nombre', $localNombre)->first();
            if ($local && isset($local['id']) && is_numeric($local['id']) && $local['id'] > 0) {
                $datos['id_locales'] = (int)$local['id'];
            } else {
                // Si no existe el local, usar ID por defecto
                $datos['id_locales'] = 5;
                log_message('debug', "Local no encontrado. Usando ID por defecto: 5");
            }
        } else {
            // Si no hay nombre de local, usar ID por defecto
            $datos['id_locales'] = 5;
        }
        
        // Departamento - Si no se encuentra, usar ID por defecto: 1
        $deptoNombre = trim($row['departamento_nombre'] ?? '');
        if (!empty($deptoNombre) && $deptoNombre !== '-') {
            $depto = $this->departamentosModel->where('nombre', $deptoNombre)->first();
            if ($depto && isset($depto['id']) && is_numeric($depto['id']) && $depto['id'] > 0) {
                $datos['id_departamento'] = (int)$depto['id'];
            } else {
                // Si no existe el departamento, usar ID por defecto
                $datos['id_departamento'] = 1;
                log_message('debug', "Departamento no encontrado. Usando ID por defecto: 1");
            }
        } else {
            // Si no hay nombre de departamento, usar ID por defecto
            $datos['id_departamento'] = 1;
        }
        
        // Persona - Si id_personas es NOT NULL en BD, debe tener siempre un valor (ID por defecto: 254)
        $personaNombre = trim($row['persona_nombre_completo'] ?? '');
        // Normalizar valores que significan "sin persona"
        $valoresVacios = ['', '-', 'sin asignar', 'sin_asignar', 'n/a', 'null', 'ninguno', 'ninguna'];
        $esVacio = in_array(strtolower($personaNombre), $valoresVacios);
        
        if (!$esVacio && !empty($personaNombre)) {
            $persona = $this->buscarPersonaPorNombre($personaNombre);
            if ($persona && isset($persona['id']) && is_numeric($persona['id']) && $persona['id'] > 0) {
                $datos['id_personas'] = (int)$persona['id'];
                log_message('debug', "Persona encontrada: {$personaNombre} con ID: {$persona['id']}");
            } else {
                // Persona no encontrada - usar ID por defecto
                log_message('warning', "Persona NO encontrada: '{$personaNombre}'. Usando ID por defecto: 254");
                $datos['id_personas'] = 254;
            }
        } else {
            // Sin persona especificada - usar ID por defecto
            $datos['id_personas'] = 254;
            log_message('debug', "Sin persona especificada. Usando ID por defecto: 254");
        }
    }

    /**
     * Validar y limpiar claves foráneas antes de insertar
     * Elimina cualquier clave foránea con valor inválido
     */
    private function validarClavesForaneas(array &$datos): void
    {
        // Lista de claves foráneas que deben ser enteros positivos o null
        $clavesForaneas = ['id_locales', 'id_departamento', 'id_personas', 'proveedor_id'];
        
        foreach ($clavesForaneas as $clave) {
            if (isset($datos[$clave])) {
                $valor = $datos[$clave];
                
                // Eliminar si es cadena vacía, espacios, o valores no válidos
                if (is_string($valor)) {
                    $valor = trim($valor);
                    if ($valor === '' || $valor === '-' || strtolower($valor) === 'null') {
                        unset($datos[$clave]);
                        log_message('debug', "Clave foránea '{$clave}' eliminada: valor string vacío o inválido");
                        continue;
                    }
                }
                
                // Si el valor no es numérico, es 0, negativo, eliminarlo
                if (!is_numeric($valor) || $valor <= 0) {
                    unset($datos[$clave]);
                    log_message('debug', "Clave foránea '{$clave}' eliminada: no numérico o <= 0 (valor: {$valor})");
                } else {
                    // Convertir a entero para asegurar tipo correcto
                    $datos[$clave] = (int)$valor;
                }
            }
        }
    }

    /**
     * Procesar cambios en relaciones (local, departamento, persona)
     */
    private function procesarRelacionesCSV(array $row, array $bienActual, array &$cambios): void
    {
        // Local
        $localNombre = trim($row['local_nombre'] ?? '');
        if (!empty($localNombre) && $localNombre !== '-') {
            $local = $this->localesModel->where('nombre', $localNombre)->first();
            if ($local && $local['id'] != $bienActual['id_locales']) {
                $cambios['id_locales'] = $local['id'];
            }
        }
        
        // Departamento
        $deptoNombre = trim($row['departamento_nombre'] ?? '');
        if (!empty($deptoNombre) && $deptoNombre !== '-') {
            $depto = $this->departamentosModel->where('nombre', $deptoNombre)->first();
            if ($depto && $depto['id'] != $bienActual['id_departamento']) {
                $cambios['id_departamento'] = $depto['id'];
            }
        }
        
        // Persona (búsqueda aproximada por nombre completo)
        $personaNombre = trim($row['persona_nombre_completo'] ?? '');
        if (!empty($personaNombre) && $personaNombre !== '-' && $personaNombre !== 'Sin asignar') {
            $persona = $this->buscarPersonaPorNombre($personaNombre);
            if ($persona && $persona['id'] != $bienActual['id_personas']) {
                $cambios['id_personas'] = $persona['id'];
            }
        } elseif ($personaNombre === 'Sin asignar' || $personaNombre === '-') {
            // Desasignar persona
            if ($bienActual['id_personas'] !== null) {
                $cambios['id_personas'] = null;
            }
        }
    }

    /**
     * Buscar persona por nombre completo (aproximado)
     */
    private function buscarPersonaPorNombre(string $nombreCompleto): ?array
    {
        // Intentar búsqueda exacta primero
        $persona = $this->personasModel
            ->where('nombre_completo', $nombreCompleto)
            ->first();
        
        if ($persona) {
            return $persona;
        }
        
        // Búsqueda aproximada por partes del nombre
        $partes = explode(' ', $nombreCompleto);
        if (count($partes) >= 2) {
            $persona = $this->personasModel
                ->like('nombre', $partes[0])
                ->like('ape_paterno', $partes[1] ?? '')
                ->first();
            
            return $persona ?: null;
        }
        
        return null;
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
        
        // Obtener query de búsqueda
        $query = $this->request->getGet('q');
        
        $builder = $departamentosModel
            ->select('id, nombre')
            ->orderBy('nombre', 'ASC');
        
        // Si hay búsqueda y tiene al menos 3 caracteres, filtrar
        if (!empty($query) && strlen($query) >= 3) {
            $builder->like('nombre', $query);
        }
        
        $departamentos = $builder->findAll();
        
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

        // Búsqueda global
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

        // Información básica
        if (!empty($filters['cod_patrimonial'])) {
            $builder->like('b.cod_patrimonial', trim($filters['cod_patrimonial']));
        }
        if (!empty($filters['tipo_bien'])) {
            $builder->like('b.descripcion', trim($filters['tipo_bien']));
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

        // Filtros técnicos
        if (!empty($filters['procesador'])) {
            $builder->like('b.procesador', trim($filters['procesador']));
        }
        if (!empty($filters['memoria'])) {
            $builder->like('b.memoria', trim($filters['memoria']));
        }
        if (!empty($filters['tipo_disco'])) {
            $builder->like('b.tipo_disco', trim($filters['tipo_disco']));
        }
        if (!empty($filters['espacio_disco'])) {
            $builder->like('b.espacio_disco', trim($filters['espacio_disco']));
        }
        if (!empty($filters['sistema_operativo'])) {
            $builder->like('b.sistema_operativo', trim($filters['sistema_operativo']));
        }
        if (!empty($filters['office'])) {
            $builder->like('b.ver_office', trim($filters['office']));
        }

        // Ubicación y estado
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

    // Métodos auxiliares privados

    private function getValidationRules(): array
    {
        return [
            'cod_patrimonial' => 'required|min_length[10]|max_length[12]|is_unique[bienes.cod_patrimonial]',
            'descripcion' => 'required',
            'tipo_bien' => 'required',
            'marca' => 'required',
            'modelo' => 'required',
            'serie' => 'required',
            'estado' => 'required',
            'fecha_adquisicion' => 'required',
            'departamento' => 'required|is_not_unique[departamentos.id]',
            'id_personas' => 'required|is_not_unique[personas.id]',
            'id_locales' => 'required|is_not_unique[locales.id]',
        ];
    }

    private function getValidationRulesUpdate(): array
    {
        return [
            'cod_patrimonial' => 'required|min_length[10]|max_length[12]',
            'descripcion' => 'required',
            'marca' => 'required',
            'modelo' => 'required',
            'serie' => 'required',
            'estado' => 'required',
            'fecha_adquisicion' => 'required',
            'departamento' => 'required|is_not_unique[departamentos.id]'
        ];
    }

    private function obtenerDatosFormulario(): array
    {
        return $this->request->getPost([
            'cod_patrimonial',
            'descripcion',
            'tipo_bien',
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
    }

    private function procesarCamposPersonalizados(array $post): array
    {
        $marca = $post['marca'] === 'otro' ? $this->request->getPost('otraMarca') : $post['marca'];
        $procesador = $post['procesador'] === 'otro' ? $this->request->getPost('otroProcesador') : $post['procesador'];
        $memoria = $post['memoria'] === 'otro' ? $this->request->getPost('otraMemoria') : $post['memoria'];
        $sistema_operativo = $post['sistema_operativo'] === 'otro' ? ($this->request->getPost('otroSO') ?? '') : $post['sistema_operativo'];

        return [
            'cod_patrimonial' => trim($post['cod_patrimonial']),
            'descripcion' => trim($post['descripcion']),
            'tipo_bien' => trim($post['tipo_bien'] ?? ''),
            'marca' => trim($marca),
            'modelo' => trim($post['modelo']),
            'serie' => trim($post['serie']),
            'procesador' => $this->nullIfEmpty(trim($procesador)),
            'memoria' => $this->nullIfEmpty(trim($memoria)),
            'tipo_disco' => $this->nullIfEmpty(trim($post['tipo_disco'])),
            'espacio_disco' => $this->nullIfEmpty(trim($post['espacio_disco'])),
            'sistema_operativo' => $this->nullIfEmpty(trim($sistema_operativo)),
            'ver_office' => $this->nullIfEmpty(trim($post['ver_office'])),
            'Ip' => $this->nullIfEmpty(trim($post['Ip'])),
            'años_garantia' => $this->nullIfEmpty(trim($post['años_garantia'])),
            'proveedor_id' => $this->nullIfEmpty(trim($post['proveedor_id'])),
            'num_doc_compra' => $this->nullIfEmpty(trim($post['num_doc_compra'])),
            'estado' => trim($post['estado']),
            'fecha_adquisicion' => trim($post['fecha_adquisicion']),
            'id_departamento' => $this->nullIfEmpty(trim($post['departamento'])),
            'id_personas' => $this->nullIfEmpty(trim($post['id_personas'])),
            'id_locales' => $this->nullIfEmpty(trim($post['id_locales'])),
        ];
    }

    private function nullIfEmpty(?string $value): ?string
    {
        return ($value === null || $value === '') ? null : $value;
    }

    private function validarArchivos($foto_frente, $foto_lateral): bool
    {
        return $foto_frente->isValid() && 
               !$foto_frente->hasMoved() && 
               $foto_lateral->isValid() && 
               !$foto_lateral->hasMoved();
    }

    private function guardarImagenesBaja($foto_frente, $foto_lateral): array
    {
        $ruta = AppConstants::RUTA_BAJAS;

        if (!is_dir($ruta)) {
            mkdir($ruta, 0777, true);
        }

        $nombreFrente = $foto_frente->getRandomName();
        $nombreLateral = $foto_lateral->getRandomName();

        $foto_frente->move($ruta, $nombreFrente);
        $foto_lateral->move($ruta, $nombreLateral);

        return [
            'foto_frente' => $ruta . $nombreFrente,
            'foto_lateral' => $ruta . $nombreLateral
        ];
    }
}
