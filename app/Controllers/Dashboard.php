<?php

namespace App\Controllers;

use App\Models\BienesModel;
use App\Models\AsignacionModel;
use App\Models\PersonasModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $bienesModel = new BienesModel();
        $movimientosModel = new AsignacionModel();

        // 📊 Estadísticas generales
        $data['total_bienes'] = $bienesModel->countAll();
        $data['bienes_activos'] = $bienesModel->where('estado', 'activo')->countAllResults();
        $data['bienes_mantenimiento'] = $bienesModel->where('estado', 'mantenimiento')->countAllResults();
        $data['bienes_asignados'] = $bienesModel->where('estado', 'asignado')->countAllResults();
        $data['bienes_baja'] = $bienesModel->where('estado', 'baja')->countAllResults();
        $data['bienes_prestamo'] = $bienesModel->where('estado', 'prestamo')->countAllResults();
        $data['bienes_disponible'] = $bienesModel->where('estado', 'disponible')->countAllResults();

        // 📈 Movimientos por tipo (últimos 6 meses)
        $data['movimientos_por_mes'] = $this->getMovimientosPorMes();

        // 🥧 Distribución por estado (solo estados con datos)
        $data['distribucion_estados'] = $this->getDistribucionEstados($data);

        // 📋 Últimos movimientos
        $data['ultimos_movimientos'] = $movimientosModel
            ->select('movimientos.*, personas.nombre, personas.ape_paterno, personas.ape_materno')
            ->join('personas', 'personas.id = movimientos.id_personas', 'left')
            ->where('movimientos.anulado', 0)
            ->orderBy('movimientos.fecha_movimiento', 'DESC')
            ->limit(5)
            ->findAll();

        // 🏆 Top 5 usuarios con más equipos
        $data['top_usuarios'] = $this->getTopUsuarios();

        return view('index', $data);
    }

    private function getMovimientosPorMes()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('movimientos');

        // Generar últimos 6 meses
        $meses = [];
        for ($i = 5; $i >= 0; $i--) {
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
            ->where('fecha_movimiento >=', date('Y-m-d', strtotime('-6 months')))
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

    private function getNombreMes($fecha)
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

    private function getDistribucionEstados($data)
    {
        $estados = [
            'activos' => ['valor' => $data['bienes_activos'], 'label' => 'Activos'],
            'asignados' => ['valor' => $data['bienes_asignados'], 'label' => 'Asignados'],
            'mantenimiento' => ['valor' => $data['bienes_mantenimiento'], 'label' => 'Mantenimiento'],
            'prestamo' => ['valor' => $data['bienes_prestamo'], 'label' => 'Préstamo'],
            'disponible' => ['valor' => $data['bienes_disponible'], 'label' => 'Disponible'],
            'baja' => ['valor' => $data['bienes_baja'], 'label' => 'Baja']
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

    private function getTopUsuarios()
    {
        $db = \Config\Database::connect();

        $resultados = $db->table('bienes b')
            ->select('p.nombre, p.ape_paterno, p.ape_materno, COUNT(*) as total_equipos')
            ->join('personas p', 'p.id = b.id_personas', 'left')
            ->where('b.estado !=', 'baja')
            ->whereNotIn('b.id_personas', [254, 255]) // Excluir OTI
            ->where('b.id_personas IS NOT NULL')  // ← Cambio aquí
            ->groupBy('b.id_personas, p.nombre, p.ape_paterno, p.ape_materno')
            ->orderBy('total_equipos', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        return $resultados;
    }
}