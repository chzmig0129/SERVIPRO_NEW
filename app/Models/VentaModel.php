<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaModel extends Model
{
    protected $table      = 'ventas';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    
    protected $allowedFields = [
        'concepto', 
        'descripcion', 
        'monto', 
        'fecha', 
        'sede_id',
        'usuario_id'
    ];
    
    // Validation rules
    protected $validationRules = [
        'concepto'        => 'required|min_length[3]|max_length[100]',
        'descripcion'     => 'permit_empty',
        'monto'           => 'required|numeric|greater_than[0]',
        'fecha'           => 'required|valid_date',
        'sede_id'         => 'required|integer',
        'usuario_id'      => 'required|integer'
    ];
    
    protected $validationMessages = [
        'concepto' => [
            'required' => 'El concepto es obligatorio',
            'min_length' => 'El concepto debe tener al menos 3 caracteres',
            'max_length' => 'El concepto no puede exceder los 100 caracteres'
        ],
        'monto' => [
            'required' => 'El monto es obligatorio',
            'numeric' => 'El monto debe ser un número',
            'greater_than' => 'El monto debe ser mayor que cero'
        ],
        'fecha' => [
            'required' => 'La fecha es obligatoria',
            'valid_date' => 'La fecha debe ser válida'
        ],
        'sede_id' => [
            'required' => 'La sede es obligatoria',
            'integer' => 'El ID de sede debe ser un número entero'
        ],
        'usuario_id' => [
            'required' => 'El usuario es obligatorio',
            'integer' => 'El ID de usuario debe ser un número entero'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    // No dates are needed since we're mapping to an existing table
    protected $useTimestamps = false;
} 