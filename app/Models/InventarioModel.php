<?php

// filepath: c:\xampp\htdocs\inventariov2\app\Models\InventarioModel.php
namespace App\Models;

use CodeIgniter\Model;

class InventarioModel extends Model
{
    protected $table      = 'inventarios';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['anio','mes','usuario_id','jefe_id','observacion'];
    protected $useTimestamps = true;
}