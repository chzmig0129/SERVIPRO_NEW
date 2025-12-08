<?php

namespace App\Models;

use CodeIgniter\Model;

class SedeModel extends Model
{
    protected $table = 'sedes';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $allowedFields = ['nombre', 'direccion', 'ciudad', 'pais', 'fecha_creacion', 'estatus'];

    // Validation rules
    protected $validationRules = [
        'nombre' => 'required|min_length[3]|max_length[255]',
        'direccion' => 'required|min_length[5]|max_length[255]',
        'ciudad' => 'required|min_length[3]|max_length[100]',
        'pais' => 'required|min_length[3]|max_length[100]',
        'fecha_creacion' => 'required|valid_date',
        'estatus' => 'permit_empty|in_list[0,1,Activo,Inactivo]'  // Acepta valores numéricos (0,1) y strings (Activo,Inactivo)
    ];
}