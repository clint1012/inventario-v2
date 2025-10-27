<?php namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\IpModel;
use App\Models\BienesModel;
use App\Models\PersonasModel;    // Requerido para listas
use App\Models\DepartamentosModel; // Requerido para listas

class IpController extends BaseController
{
    protected $ipModel;
    protected $bienesModel;
    protected $personasModel;
    protected $departamentosModel;

    public function __construct()
    {
        // Instancia de los modelos
        $this->ipModel = new IpModel();
        $this->bienesModel = new BienesModel();
        $this->personasModel = new PersonasModel();        
        $this->departamentosModel = new DepartamentosModel(); 
    }

    // Muestra la vista principal con el DataTables
    public function index()
    {
        return view('Ips/index_datatable');
    }

    // Endpoint para DataTables (Carga la data vía AJAX)
    public function datatables()
    {
        $ips = $this->ipModel->obtenerIpsConRelaciones()->findAll();

        $data = [];
        foreach ($ips as $ip) {
            $row = [
                $ip['direccion_ip'],
                // Datos de la Persona (Nombre)
                $ip['nombre_persona'] ?? '<span class="badge badge-secondary">N/A</span>',      
                // Datos del Departamento (Área)
                $ip['nombre_area'] ?? '<span class="badge badge-secondary">N/A</span>',         
                $ip['piso'] ?? 'N/A',
                // Datos del Bien Patrimonial
                $ip['cod_patrimonial'] ?? 'N/A',
                $ip['descripcion_bien'] ?? 'N/A',
                // Columna Acciones
                '<a href="' . base_url('ip/editar/' . $ip['id']) . '" class="btn btn-sm btn-warning">Editar</a>'
            ];
            $data[] = $row;
        }

        // Responde con el formato JSON que DataTables espera
        return $this->response->setJSON(['data' => $data]);
    }

    // Muestra el formulario de edición (GET)
    public function editar($id)
    {
        $data['ip'] = $this->ipModel->obtenerIpsConRelaciones()
                                    ->where('ips.id', $id)
                                    ->first();

        if (empty($data['ip'])) {
            return redirect()->to('/ip')->with('error', 'IP no encontrada.');
        }
        
        // Cargar listas completas para los SELECTs en la vista
        $data['bienes'] = $this->bienesModel->select('id, cod_patrimonial, descripcion')->findAll();
        $data['personas'] = $this->personasModel->select('id, nombre_completo')->findAll();
        $data['departamentos'] = $this->departamentosModel->select('id, nombre')->findAll();

        return view('Ips/editar', $data);
    }

    // Procesa el formulario de edición (POST)
    public function actualizar($id)
    {
        // 1. Validar los datos (id_persona/id_departamento/bien_id pueden ser vacíos)
        if (!$this->validate([
            'id_persona' => 'permit_empty|integer',
            'id_departamento' => 'permit_empty|integer',
            'bien_id' => 'permit_empty|integer',
            'piso' => 'permit_empty|max_length[50]', 
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // 2. Obtener y Limpiar los IDs (si están vacíos, se guardan como NULL en la DB)
        $id_persona = $this->request->getPost('id_persona') ?: null;
        $id_departamento = $this->request->getPost('id_departamento') ?: null;
        $bien_id = $this->request->getPost('bien_id') ?: null;
        
        // La IP se considera asignada si tiene AL MENOS una relación
        $estaAsignado = $id_persona || $id_departamento || $bien_id;

        $datosActualizar = [
            'id_persona' => $id_persona,
            'id_departamento' => $id_departamento,
            'bien_id' => $bien_id,
            'piso' => $this->request->getPost('piso'),
            'observaciones' => $this->request->getPost('observaciones'),
            'estado' => $estaAsignado ? 'asignado' : 'disponible', 
            'fecha_asignacion' => $estaAsignado ? date('Y-m-d') : null,
        ];
        
        // 3. Actualizar
        $this->ipModel->update($id, $datosActualizar);

        return redirect()->to('/ip')->with('mensaje', 'Asignación de IP actualizada correctamente.');
    }

    public function buscarAsignacionBien()
    {
        $codigo = $this->request->getGet('cod_patrimonial');
        
        if (empty($codigo)) {
            return $this->response->setJSON(['error' => 'Código patrimonial requerido']);
        }

        // Usamos el método ajustado para obtener el ID, Persona y Departamento
        $asignacion = $this->bienesModel->buscarAsignacionPorCodigo($codigo);

        if (!$asignacion) {
            // Bien no encontrado, devolvemos un JSON para limpiar y alertar.
            return $this->response->setJSON([
                'id' => null, 
                'mensaje' => 'Bien no encontrado o sin asignación actual.'
            ]);
        }

        // Devolvemos el array con el ID del bien, el nombre y el área.
        return $this->response->setJSON($asignacion);
    }
}