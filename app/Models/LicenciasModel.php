<?php

namespace App\Models;

use CodeIgniter\Model;

class LicenciasModel extends Model
{
    protected $table = 'licencias';
    protected $primaryKey = 'id';
    protected $useTimestamps = true;

    protected $allowedFields = [
        'nombre_software',
        'tipo_licencia',
        'categoria',
        'clave_licencia',
        'version',
        'fabricante',
        'fecha_adquisicion',
        'fecha_expiracion',
        'cantidad_total',
        'cantidad_disponible',
        'proveedor',
        'estado',
        'observaciones'
    ];

    /**
     * Obtener todas las licencias ordenadas alfabéticamente
     */
    public function obtenerLicencias()
    {
        return $this->orderBy('nombre_software', 'ASC')->findAll();
    }

    /**
     * Crear una nueva licencia
     */
    public function crearLicencia(array $data)
    {
        return $this->insert($data);
    }

    /**
     * Actualizar una licencia existente
     */
    public function actualizarLicencia(int $id, array $data)
    {
        return $this->update($id, $data);
    }

    /**
     * Eliminar una licencia
     */
    public function eliminarLicencia(int $id)
    {
        return $this->delete($id);
    }

    /**
     * Buscar una licencia por su ID
     */
    public function obtenerPorId(int $id)
    {
        return $this->find($id);
    }
}
