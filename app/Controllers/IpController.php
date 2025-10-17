<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\IpModel;
use App\Models\BienesModel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;

class IpController extends BaseController
{
    protected $ipModel;
    protected $bienesModel;

    public function __construct()
    {
        $this->ipModel = new IpModel();
        $this->bienesModel = new BienesModel();
    }

    // Mostrar todas las IPs con datos del bien asignado (si lo hay)
    public function index()
    {
        $subred = $this->request->getGet('subred');

        $builder = $this->ipModel
            ->select('ips.*, bienes.cod_patrimonial, bienes.descripcion')
            ->join('bienes', 'bienes.id = ips.bien_id', 'left');

        if ($subred) {
            $builder->like('direccion_ip', $subred . '.', 'after');
        }

        $builder->orderBy('INET_ATON(direccion_ip)', 'ASC');

        $data['ips'] = $builder->findAll();
        $data['subred_actual'] = $subred;

        return view('Ips/index', $data);
    }

    // Formulario + proceso para asignar IP a un bien
    public function asignar()
    {
        if ($this->request->getMethod() === 'post') {
            $ip_id = $this->request->getPost('ip_id');
            $bien_id = $this->request->getPost('bien_id');
            $observaciones = $this->request->getPost('observaciones');

            $resultado = $this->ipModel->update($ip_id, [
                'bien_id' => $bien_id,
                'estado' => 'asignado',
                'fecha_asignacion' => date('Y-m-d'),
                'observaciones' => $observaciones,
            ]);

            // (Opcional) Debug si falla
            if (!$resultado) {
                dd('No se guardó', $this->ipModel->errors());
            }

            return redirect()->to('/ip')->with('mensaje', 'IP asignada correctamente.');
        }

        $data['ips'] = $this->ipModel->obtenerDisponibles();
        $data['bienes'] = $this->bienesModel->findAll();

        return view('Ips/asignar', $data);
    }

    // Liberar una IP (desvincular del bien y ponerla como disponible)
    public function liberar($id)
    {
        $this->ipModel->update($id, [
            'bien_id' => null,
            'estado' => 'disponible',
            'fecha_asignacion' => null,
            'observaciones' => null
        ]);

        return redirect()->to('/ip')->with('mensaje', 'IP liberada correctamente.');
    }

    // Eliminar IPs que empiezan por 192.
    public function eliminar192()
    {
        $this->ipModel->where('direccion_ip LIKE', '192.%')->delete();
        return redirect()->to('/ip')->with('mensaje', 'IPs con 192.*.*.* eliminadas.');
    }

    // Exportar a Excel
    public function exportarExcel()
    {
        $ips = $this->obtenerIPsFiltradas();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('IPs');

        $sheet->setCellValue('A1', 'Dirección IP');
        $sheet->setCellValue('B1', 'Estado');
        $sheet->setCellValue('C1', 'Cod. Patrimonial');
        $sheet->setCellValue('D1', 'Descripción');
        $sheet->setCellValue('E1', 'Fecha Asignación');

        $row = 2;
        foreach ($ips as $ip) {
            $sheet->setCellValue("A$row", $ip['direccion_ip']);
            $sheet->setCellValue("B$row", $ip['estado']);
            $sheet->setCellValue("C$row", $ip['cod_patrimonial'] ?? '-');
            $sheet->setCellValue("D$row", $ip['descripcion'] ?? '-');
            $sheet->setCellValue("E$row", $ip['fecha_asignacion'] ?? '-');
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'reporte_ips_' . date('Ymd_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $writer->save("php://output");
        exit;
    }

    // Exportar a PDF
    public function exportarPDF()
    {
        $data['ips'] = $this->obtenerIPsFiltradas();
        $html = view('Ips/reporte_pdf', $data);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream("reporte_ips_" . date('Ymd_His') . ".pdf", ["Attachment" => true]);
    }

    // Filtrado de IPs
    private function obtenerIPsFiltradas()
    {
        $subred = $this->request->getGet('subred');
        $ip = $this->request->getGet('ip');
        $patrimonial = $this->request->getGet('patrimonial');

        $builder = $this->ipModel->select('ips.*, bienes.cod_patrimonial, bienes.descripcion')
            ->join('bienes', 'bienes.id = ips.bien_id', 'left');

        if ($subred) {
            $builder->like('ips.direccion_ip', $subred . '.', 'after');
        }

        if ($ip) {
            $builder->like('ips.direccion_ip', $ip);
        }

        if ($patrimonial) {
            $builder->like('bienes.cod_patrimonial', $patrimonial);
        }

        return $builder->findAll();
    }

    public function buscarIpsDisponibles()
    {
        $term = $this->request->getGet('term');
        $ips = $this->ipModel
            ->select('id, direccion_ip')
            ->like('direccion_ip', $term)
            ->where('estado', 'disponible')
            ->findAll(10);

        $result = array_map(fn($ip) => [
            'id' => $ip['id'],
            'text' => $ip['direccion_ip']
        ], $ips);

        return $this->response->setJSON($result);
    }

    public function buscarBienes()
    {
        $term = $this->request->getGet('term');
        $bienes = $this->bienesModel
            ->select('id, cod_patrimonial, descripcion')
            ->groupStart()
                ->like('cod_patrimonial', $term)
                ->orLike('descripcion', $term)
            ->groupEnd()
            ->findAll(10);

        $result = array_map(fn($bien) => [
            'id' => $bien['id'],
            'text' => $bien['cod_patrimonial'] . ' - ' . $bien['descripcion']
        ], $bienes);

        return $this->response->setJSON($result);
    }
}
