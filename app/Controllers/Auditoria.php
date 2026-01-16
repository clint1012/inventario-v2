<?php

namespace App\Controllers;

use App\Models\AuditoriaModel;

class Auditoria extends BaseController
{
    protected $auditoriaModel;

    public function __construct()
    {
        $this->auditoriaModel = new AuditoriaModel();
    }

    /**
     * Listar auditoría con filtros
     */
    public function index()
    {
        $filtros = [
            'usuario' => $this->request->getGet('usuario'),
            'modulo' => $this->request->getGet('modulo'),
            'accion' => $this->request->getGet('accion'),
            'fecha_desde' => $this->request->getGet('fecha_desde'),
            'fecha_hasta' => $this->request->getGet('fecha_hasta')
        ];

        $builder = $this->auditoriaModel->getAuditoriaConFiltros($filtros);
        
        $data = [
            'auditoria' => $builder->paginate(20),
            'pager' => $this->auditoriaModel->pager,
            'filtros' => $filtros,
            'estadisticas' => $this->auditoriaModel->getEstadisticas()
        ];

        return view('auditoria/index', $data);
    }

    /**
     * Ver detalle de un evento
     */
    public function show($id)
    {
        $evento = $this->auditoriaModel->find($id);

        if (!$evento) {
            return redirect()->to(base_url('auditoria'))
                ->with('error', 'Evento de auditoría no encontrado');
        }

        // Decodificar detalles si es JSON
        if (!empty($evento['detalles'])) {
            $decoded = json_decode($evento['detalles'], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $evento['detalles_decoded'] = $decoded;
            }
        }

        $data['evento'] = $evento;
        return view('auditoria/detalle', $data);
    }

    /**
     * Exportar auditoría a Excel
     */
    public function exportar()
    {
        $filtros = [
            'usuario' => $this->request->getGet('usuario'),
            'modulo' => $this->request->getGet('modulo'),
            'accion' => $this->request->getGet('accion'),
            'fecha_desde' => $this->request->getGet('fecha_desde'),
            'fecha_hasta' => $this->request->getGet('fecha_hasta')
        ];

        $builder = $this->auditoriaModel->getAuditoriaConFiltros($filtros);
        $registros = $builder->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Fecha/Hora');
        $sheet->setCellValue('C1', 'Usuario');
        $sheet->setCellValue('D1', 'Módulo');
        $sheet->setCellValue('E1', 'Acción');
        $sheet->setCellValue('F1', 'Registro ID');
        $sheet->setCellValue('G1', 'IP');
        $sheet->setCellValue('H1', 'Detalles');

        // Estilo encabezado
        $sheet->getStyle('A1:H1')->getFont()->setBold(true);
        $sheet->getStyle('A1:H1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1:H1')->getFont()->getColor()->setRGB('FFFFFF');

        // Datos
        $row = 2;
        foreach ($registros as $reg) {
            $sheet->setCellValue('A' . $row, $reg['id']);
            $sheet->setCellValue('B' . $row, $reg['created_at']);
            $sheet->setCellValue('C' . $row, $reg['usuario_nombre']);
            $sheet->setCellValue('D' . $row, $reg['modulo']);
            $sheet->setCellValue('E' . $row, $reg['accion']);
            $sheet->setCellValue('F' . $row, $reg['registro_id'] ?? '-');
            $sheet->setCellValue('G' . $row, $reg['ip_address']);
            $sheet->setCellValue('H' . $row, $reg['detalles'] ?? '-');
            $row++;
        }

        // Autoajustar columnas
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Descargar
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'auditoria_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    /**
     * Limpiar auditoría antigua
     */
    public function limpiar()
    {
        if (!session()->get('is_admin')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'No autorizado']);
        }

        $dias = $this->request->getPost('dias') ?? 90;
        $fecha_limite = date('Y-m-d', strtotime("-{$dias} days"));

        $deleted = $this->auditoriaModel
            ->where('created_at <', $fecha_limite)
            ->delete();

        AuditoriaModel::registrar('LIMPIAR', 'Auditoría', null, "Eliminados registros anteriores a {$fecha_limite}");

        return $this->response->setJSON([
            'status' => 'success',
            'message' => "Se eliminaron {$deleted} registros antiguos"
        ]);
    }

    /**
     * Ver historial de cambios de un registro específico
     */
    public function historial($modulo, $registro_id)
    {
        $historial = $this->auditoriaModel
            ->where('modulo', $modulo)
            ->where('registro_id', $registro_id)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Obtener información del registro según el módulo
        $registro = $this->obtenerRegistro($modulo, $registro_id);

        $data = [
            'historial' => $historial,
            'modulo' => $modulo,
            'registro_id' => $registro_id,
            'registro' => $registro
        ];

        return view('auditoria/historial', $data);
    }

    /**
     * Obtener información del registro según el módulo
     */
    private function obtenerRegistro($modulo, $registro_id)
    {
        $modelMap = [
            'Bienes' => 'App\Models\BienesModel',
            'Personas' => 'App\Models\PersonasModel',
            'Usuarios' => 'App\Models\UsuariosModel',
            'Licencias' => 'App\Models\LicenciasModel',
            'Proveedores' => 'App\Models\ProveedorModel',
            'Celulares' => 'App\Models\CelularesModel',
        ];

        if (!isset($modelMap[$modulo])) {
            return null;
        }

        $modelClass = $modelMap[$modulo];
        $model = new $modelClass();

        return $model->find($registro_id);
    }
}
