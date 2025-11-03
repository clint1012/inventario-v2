<?php
namespace App\Models;

use CodeIgniter\Model;

class BienesModel extends Model
{

    protected $table = 'bienes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'id',
        'cod_patrimonial',
        'descripcion',
        'marca',
        'modelo',
        'serie',
        'procesador',
        'memoria',
        'tipo_disco',
        'espacio_disco',
        'sistema_operativo',
        'ver_office',
        'Ip',
        'estado',
        'id_departamento',
        'id_personas',
        'fecha_adquisicion',
        'años_garantia',
        'estado_garantia',
        'proveedor',
        'id_locales',
        'motivo_baja',
        'usuario_baja',
        'foto_frente',
        'foto_lateral'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;
    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates 
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';


    public function bienesDepartamento()
    {
        return $this->select('bienes.*, departamentos.nombre AS departamento')
            ->join('departamentos', 'bienes.id_departamento = departamentos.id')
            ->findAll();
    }
    public function bienesPersonas()
    {
        return $this->select('bienes.*, personas.nombre_completo AS persona')
            ->join('personas', 'bienes.id_personas = personas.id')->findAll();
    }



    public function bienesConRelaciones()
    {
        return $this->select(
            "bienes.*, 
         personas.nombre_completo AS nombre_persona,
         departamentos.nombre AS nombre_departamento, 
         locales.nombre AS nombre_local"
        )
            ->join('personas', 'bienes.id_personas = personas.id', 'left')
            ->join('departamentos', 'bienes.id_departamento = departamentos.id', 'left')
            ->join('locales', 'bienes.id_locales = locales.id', 'left');
    }

    public function buscarAsignacionPorCodigo(string $codigo)
    {
        return $this->select(
            ' bienes.id, bienes.id_personas AS id_persona, 
            bienes.id_departamento AS id_departamento,
             personas.nombre_completo AS nombre_persona_actual, 
             departamentos.nombre AS nombre_departamento_actual '
        )

            // Usar bienes.id_personas en lugar de bienes.id_persona_asignada 
            ->join('personas', 'personas.id = bienes.id_personas', 'left')

            //Usar bienes.id_departamento en lugar de bienes.id_departamento_asignado 
            ->join('departamentos', 'departamentos.id = bienes.id_departamento', 'left')
            ->where('bienes.cod_patrimonial', $codigo)
            ->first();
    }
}