<?php namespace App\Models;

use CodeIgniter\Model;

class UsuariosModel extends Model
{
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $allowedFields = ['usuario','password','nombre','correo','estado','creado_en'];
    protected $returnType = 'array';
    public $useTimestamps = false;
}