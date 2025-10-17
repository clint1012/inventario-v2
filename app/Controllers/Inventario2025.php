<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\BienesModel;
use App\Models\DepartamentosModel;
use App\Models\Inventario2025Model;
use App\Models\LocalesModel;
use App\Models\PersonasModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Inventario2025 extends BaseController
{
    protected $helpers = ['form'];

    public function index()
    {
        $inventario2025Model = new Inventario2025Model();
        $data['inventario2025'] = $inventario2025Model->obtenerInventario();
        return view('inventario2025/index', $data);
    }

    public function show($id = null)
    {

        $inventario2025Model = new Inventario2025Model();
        $data['inventario'] = $inventario2025Model->obtenerInventarioPorId($id);

        if (empty($data['inventario'])) {
            return redirect()->to('/inventario2025')->with('error', 'Inventario no encontrado');
        }

        // Obtener datos relacionados (si los necesitas)
        $personasModel = new PersonasModel();
        $departamentosModel = new DepartamentosModel();
        $localesModel = new LocalesModel();

        // Obtener datos adicionales de personas, departamentos, locales
        $data['personas'] = $personasModel->findAll();
        $data['departamentos'] = $departamentosModel->findAll();
        $data['locales'] = $localesModel->findAll();



        return view('inventario2025/ver', $data);
    }

    public function new()
    {
        // Crear instancias de los modelos
        $personasModel = new PersonasModel();
        $departamentosModel = new DepartamentosModel();
        $localesModel = new LocalesModel();
        $bienesModel = new BienesModel();

        // Obtener los datos de departamentos, personas, locales y bienes
        $data['personas'] = $personasModel->findAll();
        $data['departamentos'] = $departamentosModel->findAll();
        $data['locales'] = $localesModel->findAll();
        $data['bienes'] = $bienesModel->findAll();

        // Pasar los datos a la vista
        return view('inventario2025/nuevo', $data);
    }

    public function create()
    {
        $reglas = [
            'id_personas' => 'required',
            'id_departamentos' => 'required',
            'id_locales' => 'required',
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        $post = $this->request->getPost([
            'id_personas',
            'id_departamentos',
            'id_locales',
            'pc_escritorio',
            'teclado',
            'monitor',
            'impresora',
            'scanner',
            'otro'
        ]);

        $inventario2025Model = new Inventario2025Model();
        $inventario2025Model->insert([
            'id_personas' => trim($post['id_personas']),
            'id_departamentos' => trim($post['id_departamentos']),
            'id_locales' => trim($post['id_locales']),
            'pc_escritorio' => trim($post['pc_escritorio']),
            'teclado' => trim($post['teclado']),
            'monitor' => trim($post['monitor']),
            'impresora' => trim($post['impresora']),
            'scanner' => trim($post['scanner']),
            'otro' => trim($post['otro']),
        ]);

        return redirect()->to('/inventario2025')->with('success', 'Inventario creado correctamente.');
    }

    public function edit($id = null)
    {
        $inventario2025Model = new Inventario2025Model();
        $personasModel = new PersonasModel();
        $departamentosModel = new DepartamentosModel();
        $localesModel = new LocalesModel();
        $bienesModel = new BienesModel();

        // Obtener datos del inventario
        $data['inventario'] = $inventario2025Model->obtenerInventarioPorId($id);

        if (empty($data['inventario'])) {
            return redirect()->to('/inventario2025')->with('error', 'Inventario no encontrado');
        }

        // Obtener listas de personas, departamentos, locales y bienes para los selects
        $data['personas'] = $personasModel->findAll();
        $data['departamentos'] = $departamentosModel->findAll();
        $data['locales'] = $localesModel->findAll();
        $data['bienes'] = $bienesModel->findAll();

        return view('inventario2025/editar', $data);
    }

    public function update($id = null)
    {
        $inventario2025Model = new Inventario2025Model();

        // Validación
        $reglas = [
            'id_personas' => 'required',
            'id_departamentos' => 'required',
            'id_locales' => 'required',
        ];

        if (!$this->validate($reglas)) {
            return redirect()->back()->withInput()->with('error', $this->validator->listErrors());
        }

        // Obtener los datos de la solicitud PUT
        $post = $this->request->getRawInput();  // Usar getRawInput() para solicitudes PUT

        $data = [
            'id_personas' => trim($post['id_personas']),
            'id_departamentos' => trim($post['id_departamentos']),
            'id_locales' => trim($post['id_locales']),
            'pc_escritorio' => trim($post['pc_escritorio']),
            'teclado' => trim($post['teclado']),
            'monitor' => trim($post['monitor']),
            'impresora' => trim($post['impresora']),
            'scanner' => trim($post['scanner']),
            'otro' => trim($post['otro']),
        ];

        // Realizar la actualización
        if ($inventario2025Model->update($id, $data)) {
            return redirect()->to('/inventario2025')->with('success', 'Inventario actualizado correctamente.');
        } else {
            // En caso de que no se haya actualizado correctamente
            return redirect()->back()->with('error', 'No se pudo actualizar el inventario.');
        }
    }

    public function delete($id = null)
    {
        if ($id) {
            $inventario2025Model = new Inventario2025Model();
            if ($inventario2025Model->delete($id)) {
                return redirect()->to('/inventario2025')->with('success', 'Registro eliminado');
            } else {
                return redirect()->to('/inventario2025')->with('error', 'No se pudo eliminar');
            }
        }
        return redirect()->to('/inventario2025')->with('error', 'ID no válido');
    }

    public function getBienDescripcion()
    {
        $bienesModel = new BienesModel();
        $codPatrimonial = $this->request->getPost('codPatrimonial');

        $bien = $bienesModel->where('cod_patrimonial', $codPatrimonial)->first();

        if ($bien) {
            $descripcionCompleta = $bien['descripcion'] . ' - ' .
                ($bien['marca'] ?? 'Sin marca') . ' - ' .
                ($bien['modelo'] ?? 'Sin modelo');

            return $this->response->setJSON(['descripcion' => $descripcionCompleta]);
        } else {
            return $this->response->setJSON(['descripcion' => 'No encontrado']);
        }
    }

    public function getUsuariosSugeridos()
    {
        $term = $this->request->getGet('usuario'); // Obtener el término de búsqueda
        $personasModel = new \App\Models\PersonasModel();

        $personas = $personasModel
            ->like('nombre_completo', $term) // Busca nombres que comiencen con las letras ingresadas
            ->findAll(10); // Limitar a 10 resultados

        return $this->response->setJSON($personas);
    }

    public function reporte_asignacion()
    {
        $model = new \App\Models\Inventario2025Model();
        $inventario = $model->obtenerInventario();

        $data = [
            'inventario' => $inventario
        ];

        // Cargar vista del PDF
        $html = view('reportes/reporte_asignacion', $data);

        // Configurar DomPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        // Descargar el archivo
        $dompdf->stream("Reporte_Asignacion.pdf", ["Attachment" => false]);
    }
}
