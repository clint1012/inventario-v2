<?php

namespace App\Models;

use CodeIgniter\Model;

class BienesModel extends Model
{
    protected $table            = 'bienes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id','cod_patrimonial', 'descripcion', 'marca',
    'modelo', 'serie', 'procesador', 'memoria','tipo_disco','espacio_disco', 'sistema_operativo','ver_office','Ip', 'estado',
    'id_departamento','id_personas', 'fecha_adquisicion','años_garantia','estado_garantia',
    'proveedor', 'id_locales','motivo_baja','usuario_baja','foto_frente','foto_lateral'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function bienesDepartamento(){
        return $this->select('bienes.*, departamentos.nombre AS departamento')
        ->join('departamentos', 'bienes.id_departamento = departamentos.id')
        ->findAll();
    }
    public function bienesPersonas(){
        return $this->select('bienes.*, personas.nombre_completo AS persona')
        ->join('personas', 'bienes.id_personas = personas.id')
        ->findAll();
    }

    // 🔹 Relación completa: persona + departamento + local
    public function bienesConRelaciones(){
        return $this->select("
            bienes.*,
            personas.nombre_completo AS persona,
            departamentos.nombre AS departamento,
            locales.nombre AS local
        ")
        ->join('personas', 'bienes.id_personas = personas.id', 'left')
        ->join('departamentos', 'bienes.id_departamento = departamentos.id', 'left')
        ->join('locales', 'bienes.id_locales = locales.id', 'left')
        ->findAll();
    }

}
