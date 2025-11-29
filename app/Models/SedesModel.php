<?php

namespace App\Models;

use CodeIgniter\Model;

class SedesModel extends Model
{
    protected $table = 'sedes';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nombre', 'direccion', 'ciudad', 'estado', 'pais'];
} 