<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CelularesModel;
use App\Models\MovimientosCelularesModel;
use App\Models\PersonasModel;
use App\Models\DepartamentosModel;
use App\Models\LocalesModel;
use App\Models\AuditoriaModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Celulares extends BaseController
{
    protected $celularesModel;
    protected $movimientosModel;

    public function __construct()
    {
        $this->celularesModel = new CelularesModel();
        $this->movimientosModel = new MovimientosCelularesModel();
        date_default_timezone_set('America/Lima');
    }

    // 📱 Listar todos los celulares registrados
    public function index()
    {
        $data['celulares'] = $this->celularesModel->orderBy('id', 'DESC')->findAll();
        return view('celulares/index', $data);
    }

    // 📱 Vista para registrar nuevo celular
    public function nuevoCelular()
    {
        return view('celulares/nuevo');
    }

    // 📱 Guardar nuevo celular
    public function guardarCelular()
    {
        $data = [
            'numero_serie' => $this->request->getPost('numero_serie'),
            'imei' => $this->request->getPost('imei'),
            'modelo' => $this->request->getPost('modelo'),
            'descripcion' => $this->request->getPost('descripcion'),
            'estado' => 'disponible'
        ];

        if (!$celular_id = $this->celularesModel->insert($data)) {
            return redirect()->back()->with('error', 'Error al registrar celular')->withInput();
        }
        
        // Registrar auditoría
        AuditoriaModel::registrar('CREAR', 'Celulares', $celular_id, [
            'imei' => $data['imei'],
            'modelo' => $data['modelo']
        ]);

        return redirect()->to(base_url('celulares'))->with('success', 'Celular registrado correctamente');
    }

    // 📱 Editar celular
    public function editarCelular($id)
    {
        $data['celular'] = $this->celularesModel->find($id);
        if (!$data['celular']) {
            return redirect()->to(base_url('celulares'))->with('error', 'Celular no encontrado');
        }
        return view('celulares/editar', $data);
    }

    // 📱 Actualizar celular
    public function actualizarCelular($id)
    {
        $data = [
            'numero_serie' => $this->request->getPost('numero_serie'),
            'imei' => $this->request->getPost('imei'),
            'modelo' => $this->request->getPost('modelo'),
            'descripcion' => $this->request->getPost('descripcion'),
        ];

        if (!$this->celularesModel->update($id, $data)) {
            return redirect()->back()->with('error', 'Error al actualizar celular')->withInput();
        }
        
        // Registrar auditoría
        AuditoriaModel::registrar('EDITAR', 'Celulares', $id, [
            'imei' => $data['imei'],
            'modelo' => $data['modelo']
        ]);

        return redirect()->to(base_url('celulares'))->with('success', 'Celular actualizado correctamente');
    }

    // 📱 Dar de baja un celular
    public function bajaCelular($id)
    {
        $celular = $this->celularesModel->find($id);
        if (!$this->celularesModel->update($id, ['estado' => 'baja'])) {
            return redirect()->back()->with('error', 'Error al dar de baja el celular');
        }
        
        // Registrar auditoría
        AuditoriaModel::registrar('BAJA', 'Celulares', $id, [
            'imei' => $celular['imei'] ?? '',
            'modelo' => $celular['modelo'] ?? ''
        ]);
        
        return redirect()->to(base_url('celulares'))->with('success', 'Celular dado de baja');
    }

    // 📋 MOVIMIENTOS - Listar movimientos de celulares
    public function movimientos()
    {
        $data['movimientos'] = $this->movimientosModel->getResumenMovimientosAgrupado();
        return view('celulares/movimientos/index', $data);
    }

    // 📋 Vista para nuevo movimiento (entrega o devolución)
    public function nuevoMovimiento()
    {
        $data = [
            'celulares_disponibles' => $this->celularesModel->getCelularesDisponibles(),
            'celulares_asignados' => $this->celularesModel->getCelularesAsignados(),
            'personas' => (new PersonasModel())->findAll(),
            'departamentos' => (new DepartamentosModel())->findAll(),
            'locales' => (new LocalesModel())->findAll(),
        ];
        return view('celulares/movimientos/create', $data);
    }

    // 📋 Guardar movimiento
    public function guardarMovimiento()
    {
        $tipo = $this->request->getPost('tipo_movimiento');
        $fecha = $this->request->getPost('fecha_movimiento')
            ? $this->request->getPost('fecha_movimiento') . ' ' . date('H:i:s')
            : date('Y-m-d H:i:s');
        
        $lote = uniqid('cel_');

        if ($tipo === 'entrega') {
            $celulares = $this->request->getPost('celulares_entrega');
            $idPersona = $this->request->getPost('id_personas');
            $idDepartamento = $this->request->getPost('id_departamentos');
            $idLocal = $this->request->getPost('id_locales');
            $observaciones = $this->request->getPost('observaciones') ?? '';
            $responsable = $this->request->getPost('responsable_nombre') ?? '';

            if (empty($celulares) || !is_array($celulares)) {
                return redirect()->back()->with('error', 'Seleccione al menos un celular');
            }

            foreach ($celulares as $idCelular) {
                $this->movimientosModel->insert([
                    'id_celular' => $idCelular,
                    'id_personas' => $idPersona,
                    'id_departamentos' => $idDepartamento,
                    'id_locales' => $idLocal,
                    'tipo_movimiento' => 'entrega',
                    'fecha_movimiento' => $fecha,
                    'observaciones' => $observaciones,
                    'responsable_nombre' => $responsable,
                    'lote' => $lote,
                    'anulado' => 0
                ]);

                // Actualizar estado del celular a 'asignado'
                $this->celularesModel->update($idCelular, ['estado' => 'asignado']);
            }

        } elseif ($tipo === 'devolucion') {
            $celulares = $this->request->getPost('celulares_devolucion');
            $observaciones = $this->request->getPost('observaciones') ?? '';
            $responsable = $this->request->getPost('responsable_nombre') ?? '';

            if (empty($celulares) || !is_array($celulares)) {
                return redirect()->back()->with('error', 'Seleccione al menos un celular');
            }

            foreach ($celulares as $idCelular) {
                // Obtener último movimiento para saber de quién es
                $ultimoMov = $this->movimientosModel->getUltimoMovimientoCelular($idCelular);
                
                $this->movimientosModel->insert([
                    'id_celular' => $idCelular,
                    'id_personas' => $ultimoMov['id_personas'] ?? null,
                    'id_departamentos' => $ultimoMov['id_departamentos'] ?? null,
                    'id_locales' => $ultimoMov['id_locales'] ?? null,
                    'tipo_movimiento' => 'devolucion',
                    'fecha_movimiento' => $fecha,
                    'observaciones' => $observaciones,
                    'responsable_nombre' => $responsable,
                    'lote' => $lote,
                    'anulado' => 0
                ]);

                // Actualizar estado del celular a 'disponible'
                $this->celularesModel->update($idCelular, ['estado' => 'disponible']);
            }
        }
        
        // Registrar auditoría
        AuditoriaModel::registrar(
            $tipo === 'entrega' ? 'ENTREGA' : 'DEVOLUCION',
            'Celulares',
            null,
            [
                'lote' => $lote,
                'tipo' => $tipo,
                'cantidad_celulares' => count($celulares ?? [])
            ]
        );

        return redirect()->to(base_url('celulares/movimientos'))
            ->with('success', 'Movimiento registrado correctamente')
            ->with('pdf_lote', $lote);
    }

    // 📋 Anular movimiento
    public function anularMovimiento()
    {
        $lote = $this->request->getPost('lote');
        $motivo = $this->request->getPost('motivo');

        $movimientos = $this->movimientosModel->where('lote', $lote)->findAll();
        
        if (empty($movimientos)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Lote no encontrado']);
        }

        foreach ($movimientos as $mov) {
            // Anular el movimiento
            $this->movimientosModel->update($mov['id'], [
                'anulado' => 1,
                'motivo_anulacion' => $motivo
            ]);

            // Revertir estado del celular
            if ($mov['tipo_movimiento'] === 'entrega') {
                $this->celularesModel->update($mov['id_celular'], ['estado' => 'disponible']);
            } elseif ($mov['tipo_movimiento'] === 'devolucion') {
                $this->celularesModel->update($mov['id_celular'], ['estado' => 'asignado']);
            }
        }
        
        // Registrar auditoría
        AuditoriaModel::registrar('ANULAR', 'Celulares', null, [
            'lote' => $lote,
            'motivo' => $motivo,
            'cantidad_movimientos' => count($movimientos)
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Movimiento anulado correctamente']);
    }

    // 📄 Descargar PDF de movimiento
    public function descargarPDF($lote)
    {
        if (empty($lote)) {
            return redirect()->to('celulares/movimientos')->with('error', 'Lote no especificado.');
        }

        $movimientos = $this->movimientosModel->getMovimientosPorLote($lote);
        
        if (empty($movimientos)) {
            return redirect()->to('celulares/movimientos')->with('error', 'Lote no encontrado.');
        }

        $primerMov = $movimientos[0];
        $tipo = ucfirst($primerMov['tipo_movimiento']);
        $fecha = date('d/m/Y H:i', strtotime($primerMov['fecha_movimiento']));
        $usuario = strtoupper(trim($primerMov['nombre'] . ' ' . $primerMov['ape_paterno'] . ' ' . $primerMov['ape_materno']));
        $departamento = $primerMov['departamento'] ?? 'N/A';
        $local = $primerMov['local'] ?? 'N/A';
        $responsable = $primerMov['responsable_nombre'] ?? 'N/A';

        $html = view('celulares/movimientos/pdf', [
            'movimientos' => $movimientos,
            'tipo' => $tipo,
            'fecha' => $fecha,
            'usuario' => $usuario,
            'departamento' => $departamento,
            'local' => $local,
            'responsable' => $responsable,
            'lote' => $lote
        ]);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'Arial');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setBody($dompdf->output());
    }
}
