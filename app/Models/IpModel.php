<?php namespace App\Models;

use CodeIgniter\Model;

class IpModel extends Model
{
    protected $table = 'ips';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Lista de campos permitidos, usando las Claves Foráneas
    protected $allowedFields = [
        'direccion_ip', 
        'estado', 
        'id_persona',       // <-- FK a personas
        'id_departamento',  // <-- FK a departamentos
        'piso',             // <-- Campo Piso
        'bien_id',          // <-- FK a bienes
        'observaciones', 
        'fecha_asignacion'
    ];

    /**
     * Obtiene todas las IPs con el nombre de la Persona, el Área y los detalles del Bien.
     * @return \CodeIgniter\Database\BaseBuilder
     */
    public function obtenerIpsConRelaciones()
    {
        return $this->select('
                ips.id, 
                ips.direccion_ip, 
                ips.estado, 
                ips.piso, 
                ips.observaciones,
                
                personas.nombre_completo AS nombre_persona,        
                departamentos.nombre AS nombre_area,               
                bienes.cod_patrimonial, 
                bienes.descripcion AS descripcion_bien,
                
                ips.id_persona,
                ips.id_departamento,
                ips.bien_id
            ')
            // Se asume que tu tabla de personas usa 'nombre_completo'
            ->join('personas', 'personas.id = ips.id_persona', 'left')          
            // Se asume que tu tabla de departamentos usa 'nombre'
            ->join('departamentos', 'departamentos.id = ips.id_departamento', 'left') 
            ->join('bienes', 'bienes.id = ips.bien_id', 'left')
            
            // Ordena las IPs por su valor numérico (útil para 192.168.1.x)
            ->orderBy('INET_ATON(ips.direccion_ip)', 'ASC');
    }
}