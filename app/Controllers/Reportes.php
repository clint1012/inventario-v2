<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\BienesModel;
use CodeIgniter\HTTP\ResponseInterface;

class Inventario extends BaseController
{
    protected $bienesModel;

    public function __construct()
    {
        $this->bienesModel = new BienesModel();
    }

    /**
     * Muestra la vista principal del módulo de reportes
     */
    public function index()
    {
        return view('reportes/index');
    }

    /**
     * Genera un reporte de bienes por local
     */
    public function bienesPorLocal($id_local)
    {
        $bienes = $this->bienesModel
            ->where('id_locales', $id_local)
            ->where('estado !=', 'retirado')
            ->findAll();

        return $this->response->setJSON($bienes);
    }

    /**
     * Genera un reporte de bienes asignados a personal
     */
    public function bienesPorPersonal($id_persona)
    {
        $bienes = $this->bienesModel
            ->where('id_personas', $id_persona)
            ->where('estado !=', 'retirado')
            ->findAll();

        return $this->response->setJSON($bienes);
    }

    /**
     * Genera un reporte de bienes dados de baja
     */
    public function bienesDadosDeBaja()
    {
        $bienes = $this->bienesModel
            ->where('estado', 'retirado')
            ->findAll();

        return $this->response->setJSON($bienes);
    }

    /**
     * Método público para generar el PDF según el criterio seleccionado
     */
    public function generarPDF($criterio)
    {
        $params = ['criterio' => $criterio];
        return $this->generarReporte($params);
    }

    /**
     * Método privado para generar reportes en PDF
     */
    private function generarReporte($params)
    {
        $criterio = $params['criterio'] ?? null;

        switch ($criterio) {
            case 'local':
                $bienes = $this->bienesModel
                    ->where('nombre_sede IS NOT NULL', null, false)
                    ->findAll();
                $titulo = 'Reporte de Bienes por Local';
                break;
            case 'personal':
                $bienes = $this->bienesModel
                    ->where('asignado_a IS NOT NULL', null, false)
                    ->findAll();
                $titulo = 'Reporte de Bienes Asignados a Personal';
                break;
            case 'baja':
                $bienes = $this->bienesModel
                    ->where('estado', 'retirado')
                    ->findAll();
                $titulo = 'Reporte de Bienes Dados de Baja';
                break;
            default:
                return redirect()->back()->with('error', 'Criterio inválido para el reporte.');
        }

        if (empty($bienes)) {
            return redirect()->back()->with('error', 'No se encontraron bienes para el reporte seleccionado.');
        }

        // Configurar opciones de DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        // Generar HTML para el PDF
        $fecha = date('d/m/Y');
        $data = ['bienes' => $bienes, 'titulo' => $titulo, 'fecha' => $fecha];
        $html = view('reportes/reporte_bienes', $data);

        // Cargar HTML en DomPDF
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Descargar PDF
        $dompdf->stream("{$titulo}.pdf", ['Attachment' => false]);
    }
}
