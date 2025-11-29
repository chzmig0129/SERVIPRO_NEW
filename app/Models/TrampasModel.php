<?php

namespace App\Models;

use CodeIgniter\Model;

class TrampasModel extends Model
{
    protected $table = 'trampas';
    protected $primaryKey = 'id';
    protected $allowedFields = ['plano_id', 'tipo', 'posicion_x', 'posicion_y', 'estado'];
} 