<?php

namespace App\Controllers;

use App\Config\AppConstants;
use App\Models\BienesModel;
use App\Models\AsignacionModel;
use App\Models\PersonasModel;
use CodeIgniter\HTTP\ResponseInterface;

class Dashboard extends BaseController
{
    private BienesModel $bienesModel;
    private AsignacionModel $movimientosModel;
    private PersonasModel $personasModel;

    public function __construct()
    {
        $this->bienesModel = new BienesModel();
        $this->movimientosModel = new AsignacionModel();
        $this->personasModel = new PersonasModel();
    }

    public function index(): string
    {
        // Estadísticas generales
        $data['total_bienes'] = $this->bienesModel->countAll();
        $data['bienes_activos'] = $this->bienesModel->where('estado', AppConstants::ESTADO_ACTIVO)->countAllResults();
        $data['bienes_mantenimiento'] = $this->bienesModel->where('estado', AppConstants::ESTADO_MANTENIMIENTO)->countAllResults();
        $data['bienes_asignados'] = $this->bienesModel->where('estado', AppConstants::ESTADO_ASIGNADO)->countAllResults();
        $data['bienes_baja'] = $this->bienesModel->where('estado', AppConstants::ESTADO_BAJA)->countAllResults();
        $data['bienes_prestamo'] = $this->bienesModel->where('estado', AppConstants::ESTADO_PRESTAMO)->countAllResults();
        $data['bienes_disponible'] = $this->bienesModel->where('estado', AppConstants::ESTADO_DISPONIBLE)->countAllResults();

        // Estadísticas por tipo de bien
        $data['bienes_por_tipo'] = $this->getBienesPorTipo();

        // Movimientos por tipo (últimos 6 meses)
        $data['movimientos_por_mes'] = $this->getMovimientosPorMes();

        // Distribución por estado (solo estados con datos)
        $data['distribucion_estados'] = $this->getDistribucionEstados($data);

        // Últimos movimientos
        $data['ultimos_movimientos'] = $this->movimientosModel
            ->select('movimientos.*, personas.nombre, personas.ape_paterno, personas.ape_materno')
            ->join('personas', 'personas.id = movimientos.id_personas', 'left')
            ->where('movimientos.anulado', 0)
            ->orderBy('movimientos.fecha_movimiento', 'DESC')
            ->limit(AppConstants::MAX_ULTIMOS_MOVIMIENTOS)
            ->findAll();

        // Top usuarios con más equipos
        $data['top_usuarios'] = $this->getTopUsuarios();

        return view('index', $data);
    }

    private function getBienesPorTipo(): array
    {
        $db = \Config\Database::connect();
        
        return $db->table('bienes')
            ->select('tipo_bien, COUNT(*) as total')
            ->where('estado !=', AppConstants::ESTADO_BAJA)
            ->groupBy('tipo_bien')
            ->orderBy('total', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function exportarTipoBien(?string $tipo = null): void
    {
        $bienes = $this->obtenerBienesParaExportar($tipo);
        $nombreArchivo = $this->generarNombreArchivoExcel($tipo);

        $spreadsheet = $this->crearExcelBienes($bienes);

        // Descargar
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $nombreArchivo . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }

    private function getMovimientosPorMes(): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('movimientos');

        // Generar últimos 6 meses
        $meses = [];
        for ($i = AppConstants::MESES_ESTADISTICAS - 1; $i >= 0; $i--) {
            $fecha = date('Y-m', strtotime("-$i months"));
            $meses[$fecha] = [
                'label' => $this->getNombreMes($fecha),
                'asignacion' => 0,
                'prestamo' => 0,
                'retiro' => 0,
                'devolucion' => 0
            ];
        }

        // Obtener datos reales
        $resultados = $builder
            ->select("DATE_FORMAT(fecha_movimiento, '%Y-%m') as mes, tipo_movimiento, COUNT(*) as total")
            ->where('anulado', 0)
            ->where('fecha_movimiento >=', date('Y-m-d', strtotime('-' . AppConstants::MESES_ESTADISTICAS . ' months')))
            ->groupBy('mes, tipo_movimiento')
            ->orderBy('mes', 'ASC')
            ->get()
            ->getResultArray();

        // Rellenar con datos reales
        foreach ($resultados as $row) {
            $mes = $row['mes'];
            $tipo = $row['tipo_movimiento'];
            $total = (int) $row['total'];

            if (isset($meses[$mes])) {
                $meses[$mes][$tipo] = $total;
            }
        }

        return $meses;
    }

    private function getNombreMes(string $fecha): string
    {
        $meses = [
            '01' => 'Ene',
            '02' => 'Feb',
            '03' => 'Mar',
            '04' => 'Abr',
            '05' => 'May',
            '06' => 'Jun',
            '07' => 'Jul',
            '08' => 'Ago',
            '09' => 'Sep',
            '10' => 'Oct',
            '11' => 'Nov',
            '12' => 'Dic'
        ];

        list($anio, $mes) = explode('-', $fecha);
        return $meses[$mes] . ' ' . $anio;
    }

    private function getDistribucionEstados(array $data): array
    {
        $labels = AppConstants::getEstadosLabels();
        $estados = [
            'activos' => ['valor' => $data['bienes_activos'], 'label' => $labels[AppConstants::ESTADO_ACTIVO]],
            'asignados' => ['valor' => $data['bienes_asignados'], 'label' => $labels[AppConstants::ESTADO_ASIGNADO]],
            'mantenimiento' => ['valor' => $data['bienes_mantenimiento'], 'label' => $labels[AppConstants::ESTADO_MANTENIMIENTO]],
            'prestamo' => ['valor' => $data['bienes_prestamo'], 'label' => $labels[AppConstants::ESTADO_PRESTAMO]],
            'disponible' => ['valor' => $data['bienes_disponible'], 'label' => $labels[AppConstants::ESTADO_DISPONIBLE]],
            'baja' => ['valor' => $data['bienes_baja'], 'label' => $labels[AppConstants::ESTADO_BAJA]]
        ];

        // Filtrar solo estados con valores > 0
        $estadosFiltrados = [];
        foreach ($estados as $key => $estado) {
            if ($estado['valor'] > 0) {
                $estadosFiltrados[$key] = $estado;
            }
        }

        return $estadosFiltrados;
    }

    private function getTopUsuarios(): array
    {
        $db = \Config\Database::connect();

        return $db->table('bienes b')
            ->select('p.nombre, p.ape_paterno, p.ape_materno, COUNT(*) as total_equipos')
            ->join('personas p', 'p.id = b.id_personas', 'left')
            ->where('b.estado !=', AppConstants::ESTADO_BAJA)
            ->whereNotIn('b.id_personas', [254, 255])
            ->where('b.id_personas IS NOT NULL')
            ->groupBy('b.id_personas, p.nombre, p.ape_paterno, p.ape_materno')
            ->orderBy('total_equipos', 'DESC')
            ->limit(AppConstants::MAX_RESULTADOS_TOP_USUARIOS)
            ->get()
            ->getResultArray();
    }

    private function obtenerBienesParaExportar(?string $tipo): array
    {
        $query = $this->bienesModel
            ->select('bienes.*, 
                locales.nombre as local,
                departamentos.nombre as departamento')
            ->join('locales', 'locales.id = bienes.id_locales', 'left')
            ->join('departamentos', 'departamentos.id = bienes.id_departamento', 'left')
            ->where('bienes.estado !=', AppConstants::ESTADO_BAJA);
        
        if ($tipo && $tipo !== 'todos') {
            $query->where('bienes.tipo_bien', $tipo);
        }
        
        return $query->findAll();
    }

    private function generarNombreArchivoExcel(?string $tipo): string
    {
        $prefijo = ($tipo && $tipo !== 'todos') ? 'bienes_' . $tipo : 'todos_los_bienes';
        return $prefijo . '_' . date('Y-m-d') . '.xlsx';
    }

    private function crearExcelBienes(array $bienes): \PhpOffice\PhpSpreadsheet\Spreadsheet
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $this->configurarEncabezadosExcel($sheet);
        $this->llenarDatosExcel($sheet, $bienes);
        $this->ajustarColumnasExcel($sheet);

        return $spreadsheet;
    }

    private function configurarEncabezadosExcel($sheet): void
    {
        $encabezados = [
            'A1' => 'Código Patrimonial',
            'B1' => 'Tipo de Bien',
            'C1' => 'Marca',
            'D1' => 'Modelo',
            'E1' => 'Serie',
            'F1' => 'Estado',
            'G1' => 'Local',
            'H1' => 'Departamento',
            'I1' => 'Usuario Asignado'
        ];

        foreach ($encabezados as $celda => $valor) {
            $sheet->setCellValue($celda, $valor);
        }

        $sheet->getStyle('A1:I1')->getFont()->setBold(true);
        $sheet->getStyle('A1:I1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFC41E3A');
        $sheet->getStyle('A1:I1')->getFont()->getColor()
            ->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
    }

    private function llenarDatosExcel($sheet, array $bienes): void
    {
        $row = 2;
        foreach ($bienes as $bien) {
            $sheet->setCellValue('A' . $row, $bien['cod_patrimonial'] ?? '-');
            $sheet->setCellValue('B' . $row, $bien['tipo_bien'] ?? '-');
            $sheet->setCellValue('C' . $row, $bien['marca'] ?? '-');
            $sheet->setCellValue('D' . $row, $bien['modelo'] ?? '-');
            $sheet->setCellValue('E' . $row, $bien['serie'] ?? '-');
            $sheet->setCellValue('F' . $row, $bien['estado'] ?? '-');
            $sheet->setCellValue('G' . $row, $bien['local'] ?? '-');
            $sheet->setCellValue('H' . $row, $bien['departamento'] ?? '-');
            $sheet->setCellValue('I' . $row, $this->obtenerNombrePersona($bien['id_personas'] ?? null));
            
            $row++;
        }
    }

    private function obtenerNombrePersona(?int $idPersona): string
    {
        if (!$idPersona) {
            return '-';
        }

        $persona = $this->personasModel->find($idPersona);
        
        if (!$persona) {
            return '-';
        }

        return trim($persona['nombre'] . ' ' . $persona['ape_paterno'] . ' ' . $persona['ape_materno']);
    }

    private function ajustarColumnasExcel($sheet): void
    {
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
    }
}