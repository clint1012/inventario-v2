<?php namespace App\Models;

use CodeIgniter\Model;

class RolesModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre','descripcion','estado','creado_en'];
    protected $returnType = 'array';
}
