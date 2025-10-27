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

    public function buscarAsignacionPorCodigo(string $codigo)
    {
        return $this->select('
                bienes.id, 
                bienes.id_personas AS id_persona,
                bienes.id_departamento AS id_departamento,
                personas.nombre_completo AS nombre_persona_actual, 
                departamentos.nombre AS nombre_departamento_actual
            ')
            // 🚨 CORRECCIÓN: Usar bienes.id_personas en lugar de bienes.id_persona_asignada 
            ->join('personas', 'personas.id = bienes.id_personas', 'left') 
            // 🚨 CORRECCIÓN: Usar bienes.id_departamento en lugar de bienes.id_departamento_asignado
            ->join('departamentos', 'departamentos.id = bienes.id_departamento', 'left') 
            ->where('bienes.cod_patrimonial', $codigo)
            ->first();
    }

     /**
     * Construye el QueryBuilder aplicando filtros.
     *
     * @param array $filters - filtros simples: clave => valor
     *        Soporta:
     *          - fecha_adquisicion_from, fecha_adquisicion_to
     *          - búsqueda global: $search (string)
     */

      public function buildFilteredQuery(array $filters = [], string $search = null)
    {
        $builder = $this->db->table($this->table);
        // joins para obtener nombres legibles si quieres (departamento, persona, local)
        $builder->select('bienes.*,
            departamentos.nombre AS nombre_departamento,
            personas.nombre_completo AS nombre_persona,
            locales.nombre AS nombre_local
        ')
        ->join('departamentos', 'bienes.id_departamento = departamentos.id', 'left')
        ->join('personas', 'bienes.id_personas = personas.id', 'left')
        ->join('locales', 'bienes.id_locales = locales.id', 'left');

        // Aplicar filtros exactos / like según presencia
        foreach ($filters as $key => $value) {
            if ($value === '' || $value === null) continue;

            // Rango de fechas
            if ($key === 'fecha_adquisicion_from') {
                $builder->where('fecha_adquisicion >=', $value);
                continue;
            }
            if ($key === 'fecha_adquisicion_to') {
                $builder->where('fecha_adquisicion <=', $value);
                continue;
            }

            // Si es id_departamento / id_personas / id_locales aplicamos igualdad
            if (in_array($key, ['id_departamento','id_personas','id_locales','espacio_disco','años_garantia'])) {
                $builder->where("bienes.{$key}", $value);
                continue;
            }

            // Para algunos campos haremos match exacto (estado), para otros LIKE
            if ($key === 'estado' || $key === 'estado_garantia' || $key === 'tipo_disco' || $key === 'ver_office') {
                $builder->where("bienes.{$key}", $value);
                continue;
            }

            // Para el resto usamos LIKE (búsqueda parcial)
            // Protegemos el nombre de la columna usando escapeIdentifiers
            $col = "bienes.{$key}";
            $builder->groupStart();
            $builder->like($col, $value);
            $builder->groupEnd();
        }

        // Búsqueda global (DataTables search box) -> buscar en varias columnas
        if ($search && $search !== '') {
            $builder->groupStart();
            $cols = [
                'cod_patrimonial','descripcion','marca','modelo','serie',
                'procesador','memoria','sistema_operativo','ver_office','Ip',
                'proveedor','estado_garantia'
            ];
            foreach ($cols as $i => $c) {
                if ($i === 0) $builder->like("bienes.{$c}", $search);
                else $builder->orLike("bienes.{$c}", $search);
            }
            $builder->groupEnd();
        }

        return $builder;
    }

    /**
     * Devuelve el total de registros (sin filtros).
     */
    public function getTotalCount()
    {
        return (int) $this->db->table($this->table)->countAllResults(false);
    }

    /**
     * Devuelve el total filtrado y los datos paginados según DataTables.
     *
     * @param array $filters
     * @param string|null $search
     * @param int $start
     * @param int $length
     * @param array $order (['column' => 'colname', 'dir' => 'asc|desc'])
     * @return array [ 'recordsFiltered' => int, 'data' => array ]
     */
    public function getFilteredData(array $filters = [], ?string $search = null, int $start = 0, int $length = 10, array $order = null)
    {
        // 1) Query base con filtros
        $builder = $this->buildFilteredQuery($filters, $search);

        // 2) Obtener recordsFiltered: clonamos el builder para contar
        $cloneForCount = clone $builder;
        $recordsFiltered = (int) $cloneForCount->countAllResults(false);

        // 3) Orden y paginación
        if ($order && isset($order['column']) && isset($order['dir'])) {
            // Para seguridad, permitimos ordenar solo por columnas conocidas
            $allowedOrderCols = [
                'id','cod_patrimonial','descripcion','marca','modelo','serie',
                'nombre_departamento','estado','fecha_adquisicion','estado_garantia','nombre_persona'
            ];
            $col = in_array($order['column'], $allowedOrderCols) ? $order['column'] : 'id';
            $dir = ($order['dir'] === 'asc') ? 'ASC' : 'DESC';
            $builder->orderBy($col, $dir);
        } else {
            $builder->orderBy('id', 'DESC');
        }

        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $rows = $builder->get()->getResultArray();

        return [
            'recordsFiltered' => $recordsFiltered,
            'data' => $rows
        ];
    }
}

