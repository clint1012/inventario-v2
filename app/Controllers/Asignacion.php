<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\AsignacionModel;
use App\Models\AuditoriaModel;
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
        $this->bienesModel = new BienesModel();

        // ✅ Forzar hora local
        date_default_timezone_set('America/Lima');
    }

    // 📌 Listado de movimientos
    public function index()
    {
        $data['usuarios'] = $this->asignacionModel->getResumenUsuariosAgrupado();
        return view('movimientos/index', $data);
    }

    // 📌 Formulario
    public function new()
    {
        $data = [
            'personas' => (new PersonasModel())->findAll(),
            'departamentos' => (new DepartamentosModel())->findAll(),
            'locales' => (new LocalesModel())->findAll(),
        ];
        return view('movimientos/create', $data);
    }

    // 📌 Guardar movimientos
    public function create()
    {
        $idPersona = $this->request->getPost('id_personas');
        $idDepartamento = $this->request->getPost('id_departamentos');
        $idLocal = $this->request->getPost('id_locales');
        $tipo = $this->request->getPost('tipo_movimiento');
        $fecha = $this->request->getPost('fecha_movimiento')
            ? $this->request->getPost('fecha_movimiento') . ' ' . date('H:i:s')
            : date('Y-m-d H:i:s');
        $fechaLimite = $this->request->getPost('fecha_limite');
        $observaciones = $this->request->getPost('observaciones') ?? '';

        // 🔑 Generar lote único
        $lote = uniqid('mov_');

        if ($tipo === 'asignacion') {
            $bienes = $this->request->getPost('bienes_asignar');
            $this->procesarAsignacion($bienes, $idPersona, $idDepartamento, $idLocal, $fecha, $observaciones, $lote);

        } elseif ($tipo === 'prestamo') {
            $bienes = $this->request->getPost('bienes_prestar');
            $fechaLimite = $this->request->getPost('fecha_limite'); // ✅ nuevo
            $this->procesarPrestamo($bienes, $idPersona, $idDepartamento, $idLocal, $fecha, $observaciones, $lote, $fechaLimite);

        } elseif ($tipo === 'retiro') {
            $bienes = $this->request->getPost('bienes_retirar');
            $this->procesarRetiro($bienes, $fecha, $observaciones, $lote);

        } elseif ($tipo === 'cambio') {
            $bienesAsignar = $this->request->getPost('bienes_asignar');
            $bienesRetirar = $this->request->getPost('bienes_retirar');

            $this->procesarRetiro($bienesRetirar, $fecha, $observaciones, $lote);
            $this->procesarAsignacion($bienesAsignar, $idPersona, $idDepartamento, $idLocal, $fecha, $observaciones, $lote);
        }

        // Registrar en auditoría
        AuditoriaModel::registrar('CREAR', 'Movimientos', null, [
            'lote' => $lote,
            'tipo' => $tipo,
            'fecha_movimiento' => $fecha
        ]);

        return redirect()->to(base_url('movimientos'))
            ->with('pdf_lote', $lote)
            ->with('success', 'Movimiento registrado correctamente');
    }

    // 📌 Procesar asignaciones
    private function procesarAsignacion($bienes, $idPersona, $idDepartamento, $idLocal, $fecha, $observaciones, $lote)
    {
        if (empty($bienes) || !is_array($bienes))
            return;

        foreach ($bienes as $idBien) {
            // Guardar dueño anterior
            $bien = $this->bienesModel->find($idBien);
            $dueñoAnterior = $bien['id_personas'] ?? null;

            $this->asignacionModel->insert([
                'id_bienes' => $idBien,
                'id_personas' => $idPersona,
                'id_departamentos' => $idDepartamento,
                'id_locales' => $idLocal,
                'tipo_movimiento' => 'asignacion',
                'fecha_movimiento' => $fecha,
                'observaciones' => $observaciones,
                'lote' => $lote,
                'id_persona_anterior' => $dueñoAnterior, // 📌 historial
                'id_departamento_anterior' => $bien['id_departamento'], // ← corregido: usar id_departamentos del bien
                'id_local_anterior' => $bien['id_locales'],
                'estado_anterior' => $bien['estado'], // 🔄 Guardar estado anterior
            ]);

            $this->bienesModel->update($idBien, [
                'estado' => 'asignado',
                'id_personas' => $idPersona,
                'id_departamentos' => $idDepartamento,
                'id_locales' => $idLocal,
            ]);
        }
    }

    // 📌 Procesar prestamos
    private function procesarPrestamo($bienes, $idPersona, $idDepartamento, $idLocal, $fecha, $observaciones, $lote, $fechaLimite)
    {
        if (empty($bienes) || !is_array($bienes))
            return;

        foreach ($bienes as $idBien) {
            // Guardar dueño anterior
            $bien = $this->bienesModel->find($idBien);
            $dueñoAnterior = $bien['id_personas'] ?? null;

            $this->asignacionModel->insert([
                'id_bienes' => $idBien,
                'id_personas' => $idPersona,
                'id_departamentos' => $idDepartamento,
                'id_locales' => $idLocal,
                'tipo_movimiento' => 'prestamo',
                'fecha_movimiento' => $fecha,
                'observaciones' => $observaciones,
                'lote' => $lote,
                'id_persona_anterior' => $dueñoAnterior, // 📌 historial
                'fecha_limite_prestamo' => $fechaLimite,
                'id_departamento_anterior' => $bien['id_departamento'], // ← corregido
                'id_local_anterior' => $bien['id_locales'],
                'estado_anterior' => $bien['estado'], // 🔄 Guardar estado anterior
            ]);

            $this->bienesModel->update($idBien, [
                'estado' => 'prestamo',
                'id_personas' => $idPersona,
                'id_departamentos' => $idDepartamento,
                'id_locales' => $idLocal,
            ]);
        }
    }

    // 📌 Procesar retiros
    private function procesarRetiro($bienes, $fecha, $observaciones, $lote)
    {
        if (empty($bienes) || !is_array($bienes))
            return;

        foreach ($bienes as $idBien) {
            $bien = $this->bienesModel->find($idBien);
            $dueñoAnterior = $bien['id_personas'] ?? null;

            $this->asignacionModel->insert([
                'id_bienes' => $idBien,
                'id_personas' => 254,
                'id_departamentos' => 1,
                'id_locales' => 5,
                'tipo_movimiento' => 'retiro',
                'fecha_movimiento' => $fecha,
                'observaciones' => $observaciones,
                'lote' => $lote,
                'id_persona_anterior' => $dueñoAnterior,
                'id_departamento_anterior' => $bien['id_departamento'], // ← corregido
                'id_local_anterior' => $bien['id_locales'],
                'estado_anterior' => $bien['estado'], // 🔄 Guardar estado anterior
            ]);

            $this->bienesModel->update($idBien, [
                'estado' => 'activo',
                'id_personas' => 254,
                'id_departamento' => 1, // ← corregido: usar 'id_departamentos'
                'id_locales' => 5,
            ]);
        }
    }

    // 📌 Buscar bienes
    public function buscarBienes()
    {
        $term = $this->request->getGet('q');
        $tipo = $this->request->getGet('tipo');
        $idPersona = $this->request->getGet('persona');

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
                'id' => $b['id'],
                'text' => "{$b['cod_patrimonial']} - {$b['descripcion']}",
                'estado' => $b['estado'],
                'disabled' => $disable
            ];
        }

        return $this->response->setJSON(['results' => $results]);
    }


    public function anular($lote = null)
    {
        $movModel = new \App\Models\AsignacionModel();
        $bienesModel = new \App\Models\BienesModel();

        if (!$lote) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Lote no especificado.']);
            }
            return redirect()->to('movimientos')->with('error', 'Lote no especificado.');
        }

        $movimientos = $movModel->where('lote', $lote)->findAll();
        if (empty($movimientos)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Lote no encontrado.']);
            }
            return redirect()->to('movimientos')->with('error', 'Lote no encontrado.');
        }

        if ((int) $movimientos[0]['anulado'] === 1) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'El lote ya está anulado.']);
            }
            return redirect()->to('movimientos')->with('warning', 'El lote ya está anulado.');
        }

        $motivo = $this->request->getPost('motivo_anulacion') ?? 'Anulación manual';

        // 🔄 REVERTIR cada bien a su estado ANTERIOR
        foreach ($movimientos as $mov) {
            $idBien = $mov['id_bienes'];

            // Restaurar al dueño anterior, departamento anterior y local anterior
            $dataRevertir = [
                'id_personas' => $mov['id_persona_anterior'],
                'id_departamento' => $mov['id_departamento_anterior'],
                'id_locales' => $mov['id_local_anterior'],
            ];

            // ✅ Restaurar el estado anterior guardado
            if (!empty($mov['estado_anterior'])) {
                $dataRevertir['estado'] = $mov['estado_anterior'];
            } else {
                // Fallback para movimientos antiguos sin estado_anterior
                if ($mov['tipo_movimiento'] === 'retiro') {
                    $dataRevertir['estado'] = 'asignado';
                } elseif ($mov['tipo_movimiento'] === 'prestamo') {
                    $dataRevertir['estado'] = 'asignado';
                } elseif ($mov['tipo_movimiento'] === 'asignacion') {
                    $dataRevertir['estado'] = $mov['id_persona_anterior'] ? 'asignado' : 'disponible';
                }
            }

            $bienesModel->update($idBien, $dataRevertir);
        }

        // Marcar lote como anulado
        $movModel->where('lote', $lote)->set([
            'motivo_anulacion' => $motivo,
            'anulado' => 1,
            'fecha_anulacion' => date('Y-m-d H:i:s')
        ])->update();

        // Registrar en auditoría
        AuditoriaModel::registrar('ANULAR', 'Movimientos', null, [
            'lote' => $lote,
            'motivo' => $motivo,
            'cantidad_bienes' => count($movimientos)
        ]);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Lote anulado y bienes revertidos correctamente.']);
        }

        return redirect()->to('movimientos')->with('success', 'El lote fue anulado correctamente.');
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
        $builder = $this->asignacionModel
            ->select('
            movimientos.*,
            movimientos.fecha_limite_prestamo,
            bienes.cod_patrimonial, bienes.descripcion, bienes.marca, bienes.modelo, bienes.serie,
            personas.nombre, personas.ape_paterno, personas.ape_materno,
            p2.nombre AS nombre_anterior, p2.ape_paterno AS apep_anterior, p2.ape_materno AS apem_anterior,
            departamentos.nombre AS departamento,
            locales.nombre AS local,
            dep2.nombre AS departamento_anterior,
            loc2.nombre AS local_anterior
        ')
            ->where('movimientos.lote', $lote)
            ->join('bienes', 'bienes.id = movimientos.id_bienes', 'left')
            ->join('personas', 'personas.id = movimientos.id_personas', 'left')
            ->join('personas AS p2', 'p2.id = movimientos.id_persona_anterior', 'left')
            ->join('departamentos', 'departamentos.id = movimientos.id_departamentos', 'left')
            ->join('locales', 'locales.id = movimientos.id_locales', 'left')
            ->join('departamentos AS dep2', 'dep2.id = movimientos.id_departamento_anterior', 'left')
            ->join('locales AS loc2', 'loc2.id = movimientos.id_local_anterior', 'left');

        $movimientos = $builder->get()->getResultArray();

        if (!$movimientos) {
            return redirect()->to('/movimientos')
                ->with('error', 'No se encontraron movimientos para este lote.');
        }

        // Buscar la primera fecha_limite_prestamo entre los movimientos de tipo 'prestamo'
        $fechaLimite = null;
        foreach ($movimientos as $m) {
            if (!empty($m['tipo_movimiento']) && $m['tipo_movimiento'] === 'prestamo' && !empty($m['fecha_limite_prestamo'])) {
                $fechaLimite = $m['fecha_limite_prestamo'];
                break;
            }
        }


        $html = view('movimientos/pdf_lote', [
            'movimientos' => $movimientos,
            'fechaLimite' => $fechaLimite
        ]);

        $snappy = new \Knp\Snappy\Pdf('C:\ARCHIV~1\wkhtmltopdf\bin\wkhtmltopdf.exe');

        $snappy->setOption('encoding', 'UTF-8');
        $snappy->setOption('enable-local-file-access', true);
        $snappy->setOption('page-size', 'A4');

        $pdfContent = $snappy->getOutputFromHtml($html);

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="Movimiento_' . $lote . '.pdf"')
            ->setBody($pdfContent);
    }


    public function descargarActa($idPersona)
    {
        $personaModel = new \App\Models\PersonasModel();
        $movModel = new \App\Models\AsignacionModel();

        // Buscar persona
        $persona = $personaModel->find($idPersona);
        if (!$persona) {
            return redirect()->back()->with('error', 'Persona no encontrada');
        }

        // Buscar el último movimiento del usuario con responsable anterior
        $ultimo = $movModel
            ->select('movimientos.lote, movimientos.tipo_movimiento, movimientos.fecha_movimiento, 
                  movimientos.observaciones,
                  p2.nombre as nombre_anterior, p2.ape_paterno as apep_anterior, p2.ape_materno as apem_anterior')
            ->join('personas as p2', 'p2.id = movimientos.id_persona_anterior', 'left')
            ->where('movimientos.id_personas', $idPersona)
            ->orderBy('movimientos.fecha_movimiento', 'DESC')
            ->first();

        if (!$ultimo) {
            return redirect()->back()->with('error', 'No se encontraron movimientos');
        }

        // Obtener los bienes del lote (ahora incluye la fecha de adquisición)
        $bienes = $movModel
            ->select('bienes.cod_patrimonial, bienes.descripcion, bienes.marca, bienes.modelo, bienes.serie,
                  bienes.fecha_adquisicion,
                  departamentos.nombre AS departamento, locales.nombre AS local')
            ->join('bienes', 'bienes.id = movimientos.id_bienes')
            ->join('departamentos', 'departamentos.id = movimientos.id_departamentos', 'left')
            ->join('locales', 'locales.id = movimientos.id_locales', 'left')
            ->where('movimientos.lote', $ultimo['lote'])
            ->findAll();

        // 🧮 Calcular estado según antigüedad
        $hoy = new \DateTime();
        foreach ($bienes as &$bien) {
            if (!empty($bien['fecha_adquisicion'])) {
                $fechaAdq = new \DateTime($bien['fecha_adquisicion']);
                $anios = $fechaAdq->diff($hoy)->y; // diferencia en años

                if ($anios <= 5) {
                    $bien['estado'] = 'BUENO';
                } elseif ($anios <= 9) {
                    $bien['estado'] = 'REGULAR';
                } else {
                    $bien['estado'] = 'MALO';
                }
            } else {
                $bien['estado'] = 'SIN FECHA';
            }
        }

        // Ruta del logo
        $logoPath = 'C:/xampp/htdocs/inventariov2/public/sb2/img/tc_logo_superior.png';

        // Determinar último responsable anterior
        $ultimo_responsable = 'No registrado';
        if (!empty($ultimo['nombre_anterior'])) {
            $ultimo_responsable = trim($ultimo['nombre_anterior'] . ' ' . $ultimo['apep_anterior'] . ' ' . $ultimo['apem_anterior']);
        }

        // Enviar datos a la vista
        $data = [
            'persona' => $persona,
            'bienes' => $bienes,
            'tipo' => strtoupper($ultimo['tipo_movimiento']),
            'fecha_mov' => $ultimo['fecha_movimiento'],
            'observaciones' => $ultimo['observaciones'],
            'logo_path' => $logoPath,
            'ultimo_responsable' => $ultimo_responsable,
        ];

        // Configurar DomPDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);

        // Cargar el HTML
        $html = view('movimientos/pdf_acta', $data);

        // Generar el PDF
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("Acta_{$persona['nombre']}.pdf", ["Attachment" => false]);
    }

    public function prestamosPorVencer(int $dias = 7)
    {
        $model = $this->asignacionModel; // ya instanciado en __construct
        $rows = $model->getPrestamosPorVencer($dias); // debe devolver lote, fecha_limite_prestamo, nombre, ape_paterno, ape_materno, total_bienes

        $prestamos = array_map(function ($r) {
            $usuario = trim(($r['nombre'] ?? '') . ' ' . ($r['ape_paterno'] ?? '') . ' ' . ($r['ape_materno'] ?? ''));
            return [
                'lote' => $r['lote'],
                'fecha_limite' => date('d/m/Y', strtotime($r['fecha_limite_prestamo'])),
                'usuario' => $usuario ?: 'Sin usuario',
                'total_bienes' => (int) ($r['total_bienes'] ?? 0)
            ];
        }, $rows);

        return $this->response->setJSON([
            'cantidad' => count($prestamos),
            'prestamos' => $prestamos
        ]);
    }


    public function devolverPrestamo($lote = null)
    {
        // Requiere autenticación (ruta ya tiene filter 'auth')
        if (!$lote) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Lote no especificado.']);
        }

        // Buscar préstamos activos del lote
        $prestamos = $this->asignacionModel
            ->where('lote', $lote)
            ->where('tipo_movimiento', 'prestamo')
            ->where('anulado', 0)
            ->findAll();

        if (empty($prestamos)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No se encontraron préstamos activos para ese lote.']);
        }

        // IDs/valores de OTI: AJUSTA según tu sistema
        $otiPersonaId = 254;      // persona OTI (quien queda "dueño" después de la devolución)
        $otiDepartamentoId = 1;   // departamento OTI
        $otiLocalId = 5;          // local OTI

        // Datos del usuario que realiza la devolución (desde session)
        $userId = session('id') ?? null;
        $userName = trim((session('nombre') ?? '') . ' ' . (session('ape_paterno') ?? '') . ' ' . (session('ape_materno') ?? ''));
        $userDeptId = session('id_departamentos') ?? session('id_departamento') ?? null;
        $userLocalId = session('id_locales') ?? session('id_local') ?? null;

        // Obtener nombres de departamento/local del que devuelve (si existen)
        $deptName = null;
        $localName = null;
        if ($userDeptId) {
            $depModel = new \App\Models\DepartamentosModel();
            $d = $depModel->find($userDeptId);
            $deptName = $d['nombre'] ?? null;
        }
        if ($userLocalId) {
            $locModel = new \App\Models\LocalesModel();
            $l = $locModel->find($userLocalId);
            $localName = $l['nombre'] ?? null;
        }

        $fecha = date('Y-m-d H:i:s');
        $nuevoLote = 'dev_' . $lote . '_' . time();

        foreach ($prestamos as $p) {
            $idBien = $p['id_bienes'] ?? null;
            if (empty($idBien))
                continue;

            // obtener datos actuales del bien para campos "anterior"
            $bien = $this->bienesModel->find($idBien);

            // Observaciones que indican quién devolvió + área + local + fecha
            $observ = sprintf(
                "Devuelto por: %s | Área: %s | Local: %s | Fecha devolución: %s",
                $userName ?: 'Desconocido',
                $deptName ?: '-',
                $localName ?: '-',
                date('d-m-Y H:i', strtotime($fecha))
            );

            // Insertar movimiento de devolución (destino: OTI), registrando en observaciones quien devolvió
            $this->asignacionModel->insert([
                'id_bienes' => $idBien,
                'id_personas' => $otiPersonaId,
                'id_departamentos' => $otiDepartamentoId,
                'id_locales' => $otiLocalId,
                'tipo_movimiento' => 'devolucion',
                'fecha_movimiento' => $fecha,
                'observaciones' => $observ,
                'lote' => $nuevoLote,
                // datos anteriores para historial
                'id_persona_anterior' => $p['id_personas'] ?? null,
                'id_departamento_anterior' => $p['id_departamentos'] ?? ($bien['id_departamento'] ?? null),
                'id_local_anterior' => $p['id_locales'] ?? ($bien['id_locales'] ?? null),
                'anulado' => 0,
            ]);

            // Actualizar el bien para que vuelva a OTI
            $this->bienesModel->update($idBien, [
                'id_personas' => $otiPersonaId,
                'id_departamentos' => $otiDepartamentoId,
                'id_locales' => $otiLocalId,
                'estado' => 'disponible'
            ]);
        }

        // Registrar en auditoría
        AuditoriaModel::registrar('DEVOLVER', 'Movimientos', null, [
            'lote_original' => $lote,
            'nuevo_lote' => $nuevoLote,
            'usuario_devolucion' => $userName,
            'cantidad_bienes' => count($prestamos)
        ]);

        // Devolver info útil para el frontend (para actualizar fila)
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Préstamo devuelto y bienes regresados a OTI.',
            'lote' => $lote,
            'oti_departamento_id' => $otiDepartamentoId,
            'oti_local_id' => $otiLocalId,
            'oti_departamento_nombre' => (new \App\Models\DepartamentosModel())->find($otiDepartamentoId)['nombre'] ?? 'OTI',
            'oti_local_nombre' => (new \App\Models\LocalesModel())->find($otiLocalId)['nombre'] ?? 'OTI',
            'returner_name' => $userName
        ]);
    }

    // 📌 Obtener personas con búsqueda
    public function getPersonas()
    {
        $query = $this->request->getGet('q');
        
        if (empty($query) || strlen($query) < 3) {
            return $this->response->setJSON([]);
        }

        $personasModel = new PersonasModel();
        $personas = $personasModel
            ->like('nombre', $query)
            ->orLike('ape_paterno', $query)
            ->orLike('ape_materno', $query)
            ->orderBy('nombre', 'ASC')
            ->findAll(20);

        $resultado = [];
        foreach ($personas as $persona) {
            $resultado[] = [
                'id' => $persona['id'],
                'nombre_completo' => trim($persona['nombre'] . ' ' . $persona['ape_paterno'] . ' ' . $persona['ape_materno'])
            ];
        }

        return $this->response->setJSON($resultado);
    }

    // 📌 Obtener departamentos con búsqueda
    public function getDepartamentos()
    {
        $query = $this->request->getGet('q');
        
        if (empty($query) || strlen($query) < 3) {
            return $this->response->setJSON([]);
        }

        $departamentosModel = new DepartamentosModel();
        $departamentos = $departamentosModel
            ->like('nombre', $query)
            ->orderBy('nombre', 'ASC')
            ->findAll(20);

        return $this->response->setJSON($departamentos);
    }

}
