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
        return $this->select("
        inventario2025.id,
        CONCAT(personas.nombre, ' ', personas.ape_paterno, ' ', personas.ape_materno) AS nombre_completo,
        departamentos.nombre AS departamento,
        locales.nombre AS sede,
        CONCAT(
            IF(pc_escritorio > 0, 'PC Escritorio, ', ''),
            IF(teclado > 0, 'Teclado, ', ''),
            IF(monitor > 0, 'Monitor, ', ''),
            IF(impresora > 0, 'Impresora, ', ''),
            IF(scanner > 0, 'Scanner, ', ''),
            IF(otro > 0, 'Otros, ', '')
        ) AS maquinas_asignadas,
         inventario2025.created_at,
         inventario2025.updated_at
    ")
            ->join('personas', 'personas.id = inventario2025.id_personas')
            ->join('departamentos', 'departamentos.id = inventario2025.id_departamentos')
            ->join('locales', 'locales.id = inventario2025.id_locales')
            
            
            ->findAll();
    }

    public function obtenerInventarioPorId($id)
    {
        return $this->select("
            inventario2025.id,
            inventario2025.id_personas,
            inventario2025.id_departamentos,
            inventario2025.id_locales,
            CONCAT(personas.nombre, ' ', personas.ape_paterno, ' ', personas.ape_materno) AS nombre_completo,
            departamentos.nombre AS departamento,
            locales.nombre AS sede,
            bienes.cod_patrimonial AS pc_escritorio_cod,
            bienes.descripcion AS pc_escritorio_desc,
            bienes2.cod_patrimonial AS teclado_cod,
            bienes2.descripcion AS teclado_desc,
            bienes3.cod_patrimonial AS monitor_cod,
            bienes3.descripcion AS monitor_desc,
            bienes4.cod_patrimonial AS impresora_cod,
            bienes4.descripcion AS impresora_desc,
            bienes5.cod_patrimonial AS scanner_cod,
            bienes5.descripcion AS scanner_desc,
            bienes6.cod_patrimonial AS otro_cod,
            bienes6.descripcion AS otro_desc,
            inventario2025.pc_escritorio,
            inventario2025.teclado,
            inventario2025.monitor,
            inventario2025.impresora,
            inventario2025.scanner,
            inventario2025.otro
        ")
            ->join('personas', 'personas.id = inventario2025.id_personas', 'left')
            ->join('departamentos', 'departamentos.id = inventario2025.id_departamentos', 'left')
            ->join('locales', 'locales.id = inventario2025.id_locales', 'left')
            ->join('bienes AS bienes', 'bienes.cod_patrimonial = NULLIF(inventario2025.pc_escritorio, "")', 'left')
            ->join('bienes AS bienes2', 'bienes2.cod_patrimonial = NULLIF(inventario2025.teclado, "")', 'left')
            ->join('bienes AS bienes3', 'bienes3.cod_patrimonial = NULLIF(inventario2025.monitor, "")', 'left')
            ->join('bienes AS bienes4', 'bienes4.cod_patrimonial = NULLIF(inventario2025.impresora, "")', 'left')
            ->join('bienes AS bienes5', 'bienes5.cod_patrimonial = NULLIF(inventario2025.scanner, "")', 'left')
            ->join('bienes AS bienes6', 'bienes6.cod_patrimonial = NULLIF(inventario2025.otro, "")', 'left')
            ->where('inventario2025.id', $id)
            ->first();
    }
}
