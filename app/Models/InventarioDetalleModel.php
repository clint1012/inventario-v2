<?php

// filepath: c:\xampp\htdocs\inventariov2\app\Models\InventarioDetalleModel.php
namespace App\Models;

use CodeIgniter\Model;

class InventarioDetalleModel extends Model
{
    protected $table      = 'inventario_detalles';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $allowedFields = ['inventario_id','bien_id','verificado','comentario','condicion'];
    public $timestamps = false;
}