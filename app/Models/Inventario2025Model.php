<?php

namespace App\Models;

use CodeIgniter\Model;

class Inventario2025Model extends Model
{
    protected $table = 'inventario2025';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true; // Habilita el manejo automático de timestamps
    protected $createdField = 'created_at'; // Indica el campo para la fecha de creación
    protected $updatedField = ''; // Si no usas updated_at, déjalo vacío
    protected $allowedFields = [
        'id_personas',
        'id_departamentos',
        'id_locales',
        'pc_escritorio',
        'teclado',
        'monitor',
        'impresora',
        'scanner',
        'otro'
    ];


    public function obtenerInventario()
    {
        return $this->select('
            inventario2025.id,
            CONCAT(personas.nombre, " ", personas.ape_paterno, " ", personas.ape_materno) AS nombre_completo,
            departamentos.nombre AS departamento,
            locales.nombre AS sede,
            inventario2025.pc_escritorio,
            inventario2025.teclado,
            inventario2025.monitor,
            inventario2025.impresora,
            inventario2025.scanner,
            inventario2025.otro,
            inventario2025.created_at,
            inventario2025.updated_at
        ')
            ->join('personas', 'personas.id = inventario2025.id_personas', 'left')
            ->join('departamentos', 'departamentos.id = inventario2025.id_departamentos', 'left')
            ->join('locales', 'locales.id = inventario2025.id_locales', 'left')
            ->findAll();
    }

    public function obtenerInventarioPorId($id)
    {
        $query = $this->select('
            inventario2025.id,
            inventario2025.id_personas,
            inventario2025.id_departamentos,
            inventario2025.id_locales,
            CONCAT(personas.nombre, " ", personas.ape_paterno, " ", personas.ape_materno) AS nombre_completo,
            departamentos.nombre AS departamento,
            locales.nombre AS sede,
            inventario2025.pc_escritorio,
            inventario2025.teclado,
            inventario2025.monitor,
            inventario2025.impresora,
            inventario2025.scanner,
            inventario2025.otro
        ')
            ->join('personas', 'personas.id = inventario2025.id_personas', 'left')
            ->join('departamentos', 'departamentos.id = inventario2025.id_departamentos', 'left')
            ->join('locales', 'locales.id = inventario2025.id_locales', 'left')
            ->where('inventario2025.id', $id);

        // No hacer JOIN con bienes si el campo está vacío
        $row = $query->first();
        if ($row) {
            $bienesModel = new \App\Models\BienesModel();
            foreach ([
                'pc_escritorio', 'teclado', 'monitor', 'impresora', 'scanner', 'otro'
            ] as $campo) {
                $cod = $row[$campo];
                if ($cod) {
                    $bien = $bienesModel->where('cod_patrimonial', $cod)->first();
                    $row[$campo.'_desc'] = $bien ? $bien['descripcion'] : null;
                } else {
                    $row[$campo.'_desc'] = null;
                }
            }
        }
        return $row;
    }
}
