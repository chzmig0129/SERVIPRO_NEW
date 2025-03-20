<?php

namespace App\Models;

use CodeIgniter\Model;

class NotaModel extends Model
{
    protected $table      = 'notas';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = ['sede_id', 'elemento_id', 'contenido', 'fecha_creacion'];
    
    protected $useTimestamps = true;
    protected $createdField  = 'fecha_creacion';
    protected $updatedField  = 'fecha_actualizacion';
    
    protected $validationRules    = [
        'sede_id'     => 'required|numeric',
        'elemento_id' => 'required',
        'contenido'   => 'required'
    ];
    
    protected $validationMessages = [
        'sede_id' => [
            'required' => 'Se requiere un ID de sede válido',
            'numeric'  => 'El ID de sede debe ser un número'
        ],
        'elemento_id' => [
            'required' => 'Se requiere un ID de elemento'
        ],
        'contenido' => [
            'required' => 'El contenido de la nota no puede estar vacío'
        ]
    ];
    
    protected $skipValidation = false;
} 