<?php

namespace App\Models;

use CodeIgniter\Model;

class LocalesModel extends Model
{
    protected $table            = 'locales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [];


    // Dates
    protected $useTimestamps = false;

}
