<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BienesModel;
use App\Models\DepartamentosModel;
use App\Models\localesModel;



class Mantenimiento extends BaseController
{

    public function index()
    {
        $bienesModel = new BienesModel();
        $departamentosModel = new DepartamentosModel();
        $localesModel = new LocalesModel();

        // Obtener solo bienes con estado "mantenimiento" y unir con la tabla departamentos y locales
        $bienes = $bienesModel
            ->select('bienes.*, departamentos.nombre AS nombre_departamento, locales.nombre AS nombre_local')
            ->join('departamentos', 'bienes.id_departamento = departamentos.id', 'left')
            ->join('locales', 'bienes.id_locales = locales.id', 'left')
            ->where('bienes.estado', 'mantenimiento')
            ->findAll();

        // 🔹 Agregar el botón "Recuperar" en una nueva clave llamada 'opcion'
        foreach ($bienes as &$bien) {
            $bien['opcion'] = '
            <form action="' . base_url('mantenimiento/recuperar/' . $bien['id']) . '" method="POST" style="display:inline;">
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fa fa-undo"></i> Recuperar
                </button>
            </form>
        ';
        }

        $data['bienes'] = $bienes;
        $data['departamentos'] = $departamentosModel->findAll();
        $data['locales'] = $localesModel->findAll();

        return view('mantenimiento/index', $data);
    }



    public function show($id = null)
    {
        if ($id === null) {
            return redirect()->route('bienes');
        }

        $bienesModel = new BienesModel();
        $departamentosModel = new DepartamentosModel();

        $data['bien'] = $bienesModel->where('estado =', 'mantenimiento')->findAll();
        $data['departamentos'] = $departamentosModel->findAll();

        if (!$data['bien']) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Bien no encontrado.");
        }

        return view('bienes/ver', $data);
    }


    public function new()
    {
        //
    }

    public function create()
    {
        //
    }


    public function edit($id = null)
    {
        //
    }


    public function update($id = null)
    {
        //
    }


    public function delete($id = null)
    {
        //
    }

    public function recuperar($id)
    {
        $bienesModel = new BienesModel();

        // Buscar el bien por ID
        $bien = $bienesModel->find($id);

        // Validar si el bien existe
        if (!$bien) {
            return redirect()->to('mantenimiento')->with('error', 'Bien no encontrado.');
        }

        // Actualizar el estado a "activo" o "asignado" (según tu lógica)
        $bienesModel->update($id, ['estado' => 'activo']);

        // Redireccionar con mensaje de éxito
        return redirect()->to('mantenimiento')->with('success', 'Bien activado correctamente.');
    }

    public function reportePDF()
    {
        $bienesModel = new BienesModel();

        // Obtener solo bienes retirados
        $data['bienes'] = $bienesModel
            ->select('bienes.*, departamentos.nombre AS nombre_departamento')
            ->join('departamentos', 'bienes.id_departamento = departamentos.id', 'left')
            ->where('bienes.estado', 'mantenimiento')
            ->findAll();

        // Cargar la vista HTML en una variable
        $html = view('reportes/reporte_mantenimiento', $data);

        // Cargar DomPDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);

        // Configurar tamaño y orientación
        $dompdf->setPaper('A4', 'landscape');

        // Renderizar el documento
        $dompdf->render();

        // Obtener la fecha actual en formato YYYY-MM-DD
        $fecha = date('Y-m-d');

        // Nombre del archivo con la fecha
        $fileName = "reporte_mantenimiento_{$fecha}.pdf";

        // Enviar el archivo al navegador con el nombre generado
        return $dompdf->stream($fileName, ['Attachment' => 0]);
    }

    public function exportarExcel()
    {
        $bienesModel = new \App\Models\BienesModel();

        $bienes = $bienesModel
            ->select('bienes.*, departamentos.nombre AS nombre_departamento')
            ->join('departamentos', 'bienes.id_departamento = departamentos.id', 'left')
            ->where('bienes.estado', 'mantenimiento')
            ->findAll();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Encabezados
        $sheet->setCellValue('A1', 'Código Patrimonial');
        $sheet->setCellValue('B1', 'Descripción');
        $sheet->setCellValue('C1', 'Marca');
        $sheet->setCellValue('D1', 'Modelo');
        $sheet->setCellValue('E1', 'Departamento');
        $sheet->setCellValue('F1', 'Estado');
        $sheet->setCellValue('G1', 'Fecha de Compra');
        $sheet->setCellValue('H1', 'Estado Garantía');
        $sheet->setCellValue('I1', 'Proveedor');
        $sheet->setCellValue('J1', 'Ultima Modificacion');
        $sheet->setCellValue('K1', 'Motivo del mantenimiento');

        // Ancho de columnas J y K para imágenes
        $sheet->getColumnDimension('L')->setWidth(20);
        $sheet->getColumnDimension('M')->setWidth(20);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $fila = 2;

        foreach ($bienes as $bien) {
            $sheet->setCellValue('A' . $fila, $bien['cod_patrimonial']);
            $sheet->setCellValue('B' . $fila, $bien['descripcion']);
            $sheet->setCellValue('C' . $fila, $bien['marca']);
            $sheet->setCellValue('D' . $fila, $bien['modelo']);
            $sheet->setCellValue('E' . $fila, $bien['nombre_departamento']);
            $sheet->setCellValue('F' . $fila, $bien['estado']);
            $sheet->setCellValue('G' . $fila, $bien['fecha_adquisicion']);
            $sheet->setCellValue('H' . $fila, $bien['estado_garantia']);
            $sheet->setCellValue('I' . $fila, $bien['proveedor']);
            $sheet->setCellValue('J' . $fila, $bien['updated_at']);
            $sheet->setCellValue('K' . $fila, $bien['motivo_mantenimiento']);


            $fila++;
        }

        // Configurar headers para descarga
        $fecha = date('Y-m-d');
        $fileName = "bienes_mantenimiento_{$fecha}.xlsx";

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$fileName\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
}
