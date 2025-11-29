<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanosModel extends Model
{
    protected $table = 'planos';
    protected $primaryKey = 'id';
    protected $allowedFields = ['sede_id', 'nombre', 'archivo', 'fecha_creacion', 'descripcion'];
    
    protected $useTimestamps = false;
} 