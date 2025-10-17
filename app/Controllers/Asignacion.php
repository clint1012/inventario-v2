<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsignacionModel;
use App\Models\BienesModel;
use App\Models\PersonasModel;
use App\Models\DepartamentosModel;
use App\Models\LocalesModel;
use Dompdf\Dompdf;
use Dompdf\Options;


class Asignacion extends BaseController
{
    protected $asignacionModel;
    protected $bienesModel;

    public function __construct()
    {
        $this->asignacionModel = new AsignacionModel();
        $this->bienesModel     = new BienesModel();

        // ✅ Forzar hora local
        date_default_timezone_set('America/Lima');
    }

    // 📌 Listado de movimientos
    public function index()
    {
        $data['usuarios'] = $this->asignacionModel->getResumenUsuarios();
        return view('movimientos/index', $data);
    }

    // 📌 Formulario
    public function new()
    {
        $data = [
            'personas'      => (new PersonasModel())->findAll(),
            'departamentos' => (new DepartamentosModel())->findAll(),
            'locales'       => (new LocalesModel())->findAll(),
        ];
        return view('movimientos/create', $data);
    }

    // 📌 Guardar movimientos
    public function create()
    {
        $idPersona      = $this->request->getPost('id_personas');
        $idDepartamento = $this->request->getPost('id_departamentos');
        $idLocal        = $this->request->getPost('id_locales');
        $tipo           = $this->request->getPost('tipo_movimiento');
        $fecha          = $this->request->getPost('fecha_movimiento') 
                          ? $this->request->getPost('fecha_movimiento') . ' ' . date('H:i:s')
                          : date('Y-m-d H:i:s');
        $observaciones  = $this->request->getPost('observaciones') ?? '';

        // 🔑 Generar lote único
        $lote = uniqid('mov_');

        if ($tipo === 'asignacion') {
            $bienes = $this->request->getPost('bienes_asignar');
            $this->procesarAsignacion($bienes, $idPersona, $idDepartamento, $idLocal, $fecha, $observaciones, $lote);

        } elseif ($tipo === 'retiro') {
            $bienes = $this->request->getPost('bienes_retirar');
            $this->procesarRetiro($bienes, $fecha, $observaciones, $lote);

        } elseif ($tipo === 'cambio') {
            $bienesAsignar = $this->request->getPost('bienes_asignar');
            $bienesRetirar = $this->request->getPost('bienes_retirar');

            $this->procesarRetiro($bienesRetirar, $fecha, $observaciones, $lote);
            $this->procesarAsignacion($bienesAsignar, $idPersona, $idDepartamento, $idLocal, $fecha, $observaciones, $lote);
        }

        return redirect()->to(base_url('movimientos/descargarCargoLote/' . $lote));
    }

    // 📌 Procesar asignaciones
    private function procesarAsignacion($bienes, $idPersona, $idDepartamento, $idLocal, $fecha, $observaciones, $lote)
    {
        if (empty($bienes) || !is_array($bienes)) return;

        foreach ($bienes as $idBien) {
            // Guardar dueño anterior
            $bien = $this->bienesModel->find($idBien);
            $dueñoAnterior = $bien['id_personas'] ?? null;

            $this->asignacionModel->insert([
                'id_bienes'        => $idBien,
                'id_personas'      => $idPersona,
                'id_departamentos' => $idDepartamento,
                'id_locales'       => $idLocal,
                'tipo_movimiento'  => 'asignacion',
                'fecha_movimiento' => $fecha,
                'observaciones'    => $observaciones,
                'lote'             => $lote,
                'id_persona_anterior' => $dueñoAnterior // 📌 historial
            ]);

            $this->bienesModel->update($idBien, [
                'estado'          => 'asignado',
                'id_personas'     => $idPersona,
                'id_departamentos'=> $idDepartamento,
                'id_locales'      => $idLocal,
            ]);
        }
    }

    // 📌 Procesar retiros
    private function procesarRetiro($bienes, $fecha, $observaciones, $lote)
    {
        if (empty($bienes) || !is_array($bienes)) return;

        foreach ($bienes as $idBien) {
            $bien = $this->bienesModel->find($idBien);
            $dueñoAnterior = $bien['id_personas'] ?? null;

            $this->asignacionModel->insert([
                'id_bienes'        => $idBien,
                'id_personas'      => 254,
                'id_departamentos' => 1,
                'id_locales'       => 5,
                'tipo_movimiento'  => 'retiro',
                'fecha_movimiento' => $fecha,
                'observaciones'    => $observaciones,
                'lote'             => $lote,
                'id_persona_anterior' => $dueñoAnterior
            ]);

            $this->bienesModel->update($idBien, [
                'estado'          => 'activo',
                'id_personas'     => 254,
                'id_departamentos'=> 1,
                'id_locales'      => 5,
            ]);
        }
    }

    // 📌 Buscar bienes
    public function buscarBienes()
    {
        $term     = $this->request->getGet('q');
        $tipo     = $this->request->getGet('tipo');
        $idPersona= $this->request->getGet('persona');

        $builder = $this->bienesModel;

        if (!empty($term)) {
            $builder = $builder
                ->like('cod_patrimonial', $term)
                ->orLike('descripcion', $term);
        }

        // 📌 Si es retiro/cambio, mostrar solo los bienes del usuario
        if (($tipo === 'retiro' || $tipo === 'cambio') && $idPersona) {
            $builder->where('id_personas', $idPersona);
        }

        $bienes = $builder->findAll(10);
        $results = [];

        foreach ($bienes as $b) {
            $disable = false;
            if ($tipo === 'asignacion' && $b['estado'] === 'asignado') {
                $disable = true;
            }

            $results[] = [
                'id'       => $b['id'],
                'text'     => "{$b['cod_patrimonial']} - {$b['descripcion']}",
                'estado'   => $b['estado'],
                'disabled' => $disable
            ];
        }

        return $this->response->setJSON(['results' => $results]);
    }

    // 📌 PDF individual
    public function descargarCargo($id)
    {
        $movimiento = $this->asignacionModel->getMovimientoById($id);
        if (!$movimiento) {
            return redirect()->to('/movimientos')->with('error', 'Movimiento no encontrado.');
        }

        $html = view('movimientos/pdf', ['movimiento' => $movimiento]);

        $options = new Options();
        $options->set('isRemoteEnabled', true); // Permitir imágenes externas (URLs) o base_url
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("cargo_{$movimiento['id']}.pdf", ["Attachment" => false]);
    }

    // 📌 PDF lote
    public function descargarCargoLote($lote)
    {
        $movimientos = $this->asignacionModel
            ->select('movimientos.*, bienes.cod_patrimonial, bienes.descripcion, bienes.marca, bienes.modelo, bienes.serie,
                      personas.nombre, personas.ape_paterno, personas.ape_materno,
                      p2.nombre as nombre_anterior, p2.ape_paterno as apep_anterior, p2.ape_materno as apem_anterior,
                      departamentos.nombre AS departamento, locales.nombre AS local')
            ->where('lote', $lote)
            ->join('bienes', 'bienes.id = movimientos.id_bienes', 'left')
            ->join('personas', 'personas.id = movimientos.id_personas', 'left')
            ->join('personas as p2', 'p2.id = movimientos.id_persona_anterior', 'left')
            ->join('departamentos', 'departamentos.id = movimientos.id_departamentos', 'left')
            ->join('locales', 'locales.id = movimientos.id_locales', 'left')
            ->findAll();

        if (!$movimientos) {
            return redirect()->to('/movimientos')->with('error', 'No se encontraron movimientos para este lote.');
        }

        $html = view('movimientos/pdf_lote', ['movimientos' => $movimientos]);

        $options = new Options();
        $options->set('isRemoteEnabled', true); // Permitir imágenes externas (URLs) o base_url
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("cargo_lote_$lote.pdf", ["Attachment" => false]);
    }


    // 📄 Generar Acta consolidada por usuario
     // 📄 Generar Acta consolidada por usuario
 
public function descargarActa($idPersona)
{
    $personaModel = new \App\Models\PersonasModel();
    $movModel = new \App\Models\AsignacionModel();

    // Buscar persona
    $persona = $personaModel->find($idPersona);
    if (!$persona) {
        return redirect()->back()->with('error', 'Persona no encontrada');
    }

    // Buscar el último movimiento del usuario
    $ultimo = $movModel
        ->select('lote, tipo_movimiento, fecha_movimiento')
        ->where('id_personas', $idPersona)
        ->orderBy('fecha_movimiento', 'DESC')
        ->first();

    if (!$ultimo) {
        return redirect()->back()->with('error', 'No se encontraron movimientos');
    }

    // Obtener los bienes de ese lote
    $bienes = $movModel
    ->select('bienes.cod_patrimonial, bienes.descripcion, bienes.marca, bienes.modelo, bienes.serie,
              departamentos.nombre AS departamento, locales.nombre AS local')
    ->join('bienes', 'bienes.id = movimientos.id_bienes')
    ->join('departamentos', 'departamentos.id = movimientos.id_departamentos', 'left')
    ->join('locales', 'locales.id = movimientos.id_locales', 'left')
    ->where('movimientos.lote', $ultimo['lote'])
    ->findAll();


    $logoPath = 'C:/xampp/htdocs/inventariov2/public/sb2/img/tc_logo_superior.png';
    // Enviar los datos a la vista
    $data = [
        'persona' => $persona,
        'bienes' => $bienes,
        'tipo' => strtoupper($ultimo['tipo_movimiento']),
        'fecha_emision' => date('d/m/Y H:i:s'),
        'logo_path' => $logoPath
    ];

    // ✅ Configurar DomPDF (permite imágenes locales)
    $options = new \Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new \Dompdf\Dompdf($options);

    // Cargar el HTML
    $html = view('movimientos/pdf_acta', $data);

    // Generar el PDF
    $dompdf = new \Dompdf\Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
   $dompdf->stream("Acta_{$persona['nombre']}.pdf", ["Attachment" => false]);
}



}
