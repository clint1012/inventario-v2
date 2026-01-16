<?php

// filepath: c:\xampp\htdocs\inventariov2\app\Controllers\Inventario.php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PersonasModel;
use App\Models\BienesModel;
use App\Models\DepartamentosModel;
use App\Models\InventarioModel;
use App\Models\InventarioDetalleModel;
use App\Models\AuditoriaModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class Inventario extends BaseController
{
    protected $personas;
    protected $bienes;
    protected $departamentos;
    protected $inventarios;
    protected $detalles;

    public function __construct()
    {
        $this->personas = new PersonasModel();
        $this->bienes = new BienesModel();
        $this->inventarios = new InventarioModel();
        $this->detalles = new InventarioDetalleModel();
        $this->departamentos = new DepartamentosModel();
        helper(['form', 'text']);
    }

    public function index()
    {
        return view('inventario/index', [
            'anioActual' => date('Y')
        ]);
    }

    public function buscarUsuarios()
    {
        $term = trim($this->request->getGet('term') ?? '');
        if (strlen($term) < 3) {
            return $this->response->setJSON([]);
        }

        $usuarios = $this->personas
            ->select('personas.id, personas.nombre_completo, regimen_laboral.regimen_laboral')
            ->join('regimen_laboral', 'regimen_laboral.id = personas.id_regimen_laboral', 'left')
            ->like('personas.nombre_completo', $term, 'both')
            ->limit(10)
            ->findAll();

        $suggestions = array_map(fn($u) => [
            'id' => $u['id'],
            'label' => $u['nombre_completo'],
            'value' => $u['nombre_completo'],
            'regimen' => $u['regimen_laboral'] ?? 'Sin régimen',
        ], $usuarios);

        return $this->response->setJSON($suggestions);
    }

    public function equiposPorUsuario($usuarioId)
    {
        $equipos = $this->bienes
            ->select('bienes.id, bienes.cod_patrimonial, bienes.descripcion, bienes.marca, bienes.estado, locales.nombre AS local, departamentos.nombre AS departamento')
            ->join('locales', 'locales.id = bienes.id_locales', 'left')
            ->join('departamentos', 'departamentos.id = bienes.id_departamento', 'left')
            ->where('bienes.id_personas', $usuarioId)
            ->findAll();

        return $this->response->setJSON($equipos);
    }

    public function registrar()
    {
        $anio = (int) $this->request->getPost('anio');
        $mes = $this->request->getPost('mes');
        $usuarioId = $this->request->getPost('usuario_id');
        $jefeId = $this->request->getPost('jefe_id') ?: null;
        $observacion = $this->request->getPost('observacion');
        $equipos = $this->request->getPost('equipos') ?? [];

        if (!$usuarioId || !$anio || !$mes) {
            return redirect()->back()->with('error', 'Debe indicar año y usuario.');
        }

        $inventarioId = $this->inventarios->insert([
            'anio' => $anio,
            'mes' => $mes,
            'usuario_id' => $usuarioId,
            'jefe_id' => $jefeId,
            'observacion' => $observacion,
        ]);

        $detalleRows = [];
        foreach ($equipos as $bienId => $data) {
            // Solo guardar si el bien fue verificado (marcado "Sí")
            if (isset($data['verificado']) && $data['verificado'] == '1') {
                $detalleRows[] = [
                    'inventario_id' => $inventarioId,
                    'bien_id' => $bienId,
                    'verificado' => 1,
                    'comentario' => $data['comentario'] ?? null,
                    'condicion' => $data['condicion'] ?? null,
                ];
            }
        }

        if ($detalleRows) {
            $this->detalles->insertBatch($detalleRows);
        }
        
        // Registrar auditoría
        $usuario = $this->personas->find($usuarioId);
        AuditoriaModel::registrar('CREAR', 'Inventario', $inventarioId, [
            'anio' => $anio,
            'mes' => $mes,
            'usuario' => $usuario['nombre_completo'] ?? '',
            'total_equipos' => count($equipos)
        ]);

        return redirect()->back()->with('success', 'Inventario registrado.');
    }


    public function liberarBien()
    {
        $bienId = (int) $this->request->getPost('bien_id');
        $usuarioId = (int) $this->request->getPost('usuario_id');

        if ($bienId <= 0 || $usuarioId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Datos inválidos.']);
        }

        $bien = $this->bienes->find($bienId);
        if (!$bien) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Bien no encontrado.']);
        }

        if ((int) $bien['id_personas'] !== $usuarioId) {
            return $this->response->setStatusCode(409)->setJSON(['message' => 'El bien no pertenece al usuario.']);
        }

        $this->bienes->update($bienId, ['id_personas' => 255]);
        
        // Registrar auditoría
        AuditoriaModel::registrar('LIBERAR', 'Inventario', $bienId, [
            'cod_patrimonial' => $bien['cod_patrimonial'] ?? '',
            'usuario_id' => $usuarioId
        ]);

        return $this->response->setJSON(['message' => 'Bien liberado correctamente.']);
    }

    public function asignarBien()
    {
        $usuarioId = (int) $this->request->getPost('usuario_id');
        $bienId = (int) $this->request->getPost('bien_id');
        $sbn = trim((string) $this->request->getPost('sbn'));

        if ($usuarioId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Usuario inválido.']);
        }

        if ($bienId <= 0 && $sbn === '') {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Debe indicar bien o código SBN.']);
        }

        $bien = $bienId > 0
            ? $this->bienes->find($bienId)
            : $this->bienes->where('cod_patrimonial', $sbn)->first();

        if (!$bien) {
            return $this->response->setStatusCode(404)->setJSON(['message' => 'Bien no encontrado.']);
        }

        $advertencia = null;
        $anteriorId = (int) $bien['id_personas'];
        if ($anteriorId > 0 && $anteriorId !== $usuarioId && $anteriorId !== 255) {
            // El bien estaba asignado a otra persona
            $personaAnterior = $this->personas->find($anteriorId);
            $advertencia = 'El bien estaba asignado a ' . ($personaAnterior['nombre_completo'] ?? 'otro usuario') . '. Se ha reasignado.';
            // Registrar auditoría de liberación
            AuditoriaModel::registrar('LIBERAR', 'Inventario', $bien['id'], [
                'cod_patrimonial' => $bien['cod_patrimonial'] ?? '',
                'usuario_id' => $anteriorId
            ]);
        }

        $this->bienes->update($bien['id'], ['id_personas' => $usuarioId]);

        // Registrar auditoría de asignación
        AuditoriaModel::registrar('ASIGNAR', 'Inventario', $bien['id'], [
            'cod_patrimonial' => $bien['cod_patrimonial'] ?? '',
            'usuario_id' => $usuarioId
        ]);

        $msg = 'Bien asignado correctamente.';
        if ($advertencia) {
            $msg .= ' ' . $advertencia;
        }
        return $this->response->setJSON(['message' => $msg]);
    }

    public function listado()
    {
        $inventarios = $this->inventarios
            ->select('inventarios.*, personas.nombre_completo AS usuario, regimen_laboral.regimen_laboral AS regimen, jefe.nombre_completo AS jefe')
            ->join('personas', 'personas.id = inventarios.usuario_id', 'left')
            ->join('regimen_laboral', 'regimen_laboral.id = personas.id_regimen_laboral', 'left')
            ->join('personas AS jefe', 'jefe.id = inventarios.jefe_id', 'left')
            ->orderBy('anio', 'DESC')
            ->findAll();

        $detalles = $this->detalles
            ->select('inventario_detalles.*, bienes.cod_patrimonial, bienes.descripcion, bienes.marca, bienes.serie, locales.nombre AS local, departamentos.nombre AS departamento')
            ->join('bienes', 'bienes.id = inventario_detalles.bien_id', 'left')
            ->join('locales', 'locales.id = bienes.id_locales', 'left')
            ->join('departamentos', 'departamentos.id = bienes.id_departamento', 'left')
            ->findAll();

        $detallesPorInventario = [];
        foreach ($detalles as $detalle) {
            $detallesPorInventario[$detalle['inventario_id']][] = $detalle;
        }

        return view('inventario/listado', [
            'inventarios' => $inventarios,
            'detalles' => $detallesPorInventario,
        ]);
    }


    public function exportarExcel()
    {
        $busqueda = trim($this->request->getGet('busqueda') ?? '');
        $anio = $this->request->getGet('anio');
        $mes = $this->request->getGet('mes');

        $query = $this->inventarios
            ->select('inventarios.*, personas.nombre_completo AS usuario, regimen_laboral.regimen_laboral AS regimen, jefe.nombre_completo AS jefe')
            ->join('personas', 'personas.id = inventarios.usuario_id', 'left')
            ->join('regimen_laboral', 'regimen_laboral.id = personas.id_regimen_laboral', 'left')
            ->join('personas AS jefe', 'jefe.id = inventarios.jefe_id', 'left');

        if ($anio) {
            $query->where('inventarios.anio', $anio);
        }
        if ($mes) {
            $query->where('inventarios.mes', $mes);
        }
        if ($busqueda) {
            $query->like('personas.nombre_completo', $busqueda, 'both');
        }

        $inventarios = $query->orderBy('anio', 'DESC')->findAll();

        if (empty($inventarios)) {
            return redirect()->back()->with('error', 'No hay inventarios que coincidan con los filtros aplicados.');
        }

        $inventarioIds = array_column($inventarios, 'id');

        $detalles = $this->detalles
            ->select('inventario_detalles.*, bienes.cod_patrimonial, bienes.descripcion, bienes.marca, bienes.serie, locales.nombre AS local, departamentos.nombre AS departamento')
            ->join('bienes', 'bienes.id = inventario_detalles.bien_id', 'left')
            ->join('locales', 'locales.id = bienes.id_locales', 'left')
            ->join('departamentos', 'departamentos.id = bienes.id_departamento', 'left')
            ->whereIn('inventario_detalles.inventario_id', $inventarioIds)
            ->findAll();

        $detallesPorInventario = [];
        foreach ($detalles as $detalle) {
            $detallesPorInventario[$detalle['inventario_id']][] = $detalle;
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Inventarios');

        // Encabezado
        $sheet->setCellValue('A1', 'INVENTARIO DE BIENES PATRIMONIALES');
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $row = 3;
        foreach ($inventarios as $inv) {
            // Datos del inventario
            $periodo = ($inv['mes'] ?? '—') . ' / ' . $inv['anio'];
            $sheet->setCellValue("A{$row}", "Usuario: {$inv['usuario']} | Periodo: {$periodo} | Total bienes: " . count($detallesPorInventario[$inv['id']] ?? []));
            $sheet->mergeCells("A{$row}:G{$row}");
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("A{$row}")->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setARGB('FFD1D3D4');
            $row++;

            // Información adicional (régimen y jefe)
            if (!empty($inv['regimen']) || !empty($inv['jefe'])) {
                $infoAdicional = '';
                if (!empty($inv['regimen'])) {
                    $infoAdicional .= "Régimen Laboral: {$inv['regimen']}";
                }
                if (!empty($inv['jefe'])) {
                    if (!empty($infoAdicional))
                        $infoAdicional .= ' | ';
                    $infoAdicional .= "Jefe Responsable: {$inv['jefe']}";
                }
                $sheet->setCellValue("A{$row}", $infoAdicional);
                $sheet->mergeCells("A{$row}:G{$row}");
                $sheet->getStyle("A{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFF8F9FA');
                $row++;
            }

            // Encabezados de columnas
            $headers = ['SBN', 'Descripción', 'Marca', 'Serie', 'Local', 'Departamento', 'Verificado'];
            $col = 'A';
            foreach ($headers as $header) {
                $sheet->setCellValue("{$col}{$row}", $header);
                $sheet->getStyle("{$col}{$row}")->getFont()->setBold(true);
                $sheet->getStyle("{$col}{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE9ECEF');
                $col++;
            }
            $row++;

            // Datos de bienes
            if (!empty($detallesPorInventario[$inv['id']])) {
                foreach ($detallesPorInventario[$inv['id']] as $detalle) {
                    $sheet->setCellValueExplicit(
                        "A{$row}",
                        $detalle['cod_patrimonial'] ?? '—',
                        \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING
                    );
                    $sheet->setCellValue("B{$row}", $detalle['descripcion'] ?? '—');
                    $sheet->setCellValue("C{$row}", $detalle['marca'] ?? '—');
                    $sheet->setCellValue("D{$row}", $detalle['serie'] ?? '—');
                    $sheet->setCellValue("E{$row}", $detalle['local'] ?? '—');
                    $sheet->setCellValue("F{$row}", $detalle['departamento'] ?? '—');
                    $sheet->setCellValue("G{$row}", $detalle['verificado'] ? 'SÍ' : 'NO');
                    $row++;
                }
            }
            $row += 2;
        }

        // Ajustar anchos de columna
        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'inventario_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
    public function buscarJefes()
    {
        $term = trim($this->request->getGet('term') ?? '');
        if (strlen($term) < 3) {
            return $this->response->setJSON([]);
        }

        $jefes = $this->personas
            ->select('id, nombre_completo')
            ->like('nombre_completo', $term, 'both')
            ->limit(10)
            ->findAll();

        $suggestions = array_map(fn($j) => [
            'id' => $j['id'],
            'label' => $j['nombre_completo'],
            'value' => $j['nombre_completo'],
        ], $jefes);

        return $this->response->setJSON($suggestions);
    }

    public function editar($id)
    {
        $inventario = $this->inventarios
            ->select('inventarios.*, personas.nombre_completo AS usuario, personas.id AS usuario_id, regimen_laboral.regimen_laboral AS regimen, jefe.nombre_completo AS jefe')
            ->join('personas', 'personas.id = inventarios.usuario_id', 'left')
            ->join('regimen_laboral', 'regimen_laboral.id = personas.id_regimen_laboral', 'left')
            ->join('personas AS jefe', 'jefe.id = inventarios.jefe_id', 'left')
            ->find($id);

        if (!$inventario) {
            return redirect()->to('inventario/listado')->with('error', 'Inventario no encontrado.');
        }

        $detalles = $this->detalles
            ->select('inventario_detalles.*, bienes.cod_patrimonial, bienes.descripcion, bienes.marca')
            ->join('bienes', 'bienes.id = inventario_detalles.bien_id', 'left')
            ->where('inventario_detalles.inventario_id', $id)
            ->findAll();

        return view('inventario/editar', [
            'inventario' => $inventario,
            'detalles' => $detalles,
            'anioActual' => date('Y')
        ]);
    }

    public function actualizar($id)
    {
        $anio = (int) $this->request->getPost('anio');
        $mes = $this->request->getPost('mes');
        $jefeId = $this->request->getPost('jefe_id') ?: null;
        $observacion = $this->request->getPost('observacion');
        $equipos = $this->request->getPost('equipos') ?? [];

        if (!$anio || !$mes) {
            return redirect()->back()->with('error', 'Debe indicar año y mes.');
        }

        $this->inventarios->update($id, [
            'anio' => $anio,
            'mes' => $mes,
            'jefe_id' => $jefeId,
            'observacion' => $observacion,
        ]);

        // Eliminar detalles existentes
        $this->detalles->where('inventario_id', $id)->delete();

        // Insertar nuevos detalles SOLO si están verificados
        $detalleRows = [];
        foreach ($equipos as $bienId => $data) {
            if (isset($data['verificado']) && $data['verificado'] == '1') {
                $detalleRows[] = [
                    'inventario_id' => $id,
                    'bien_id' => $bienId,
                    'verificado' => 1,
                    'comentario' => $data['comentario'] ?? null,
                    'condicion' => $data['condicion'] ?? null,
                ];
            }
        }

        if ($detalleRows) {
            $this->detalles->insertBatch($detalleRows);
        }

        return redirect()->to('inventario/listado')->with('success', 'Inventario actualizado correctamente.');
    }

    public function eliminar()
    {
        $inventarioId = (int) $this->request->getPost('inventario_id');

        if ($inventarioId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'ID inválido.']);
        }

        // Eliminar detalles primero (por FK)
        $this->detalles->where('inventario_id', $inventarioId)->delete();

        // Eliminar inventario
        $this->inventarios->delete($inventarioId);

        return $this->response->setJSON(['message' => 'Inventario eliminado correctamente.']);
    }

}