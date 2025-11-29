<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table      = 'usuarios';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    
    protected $allowedFields = [
        'correo', 
        'nombre',
        'password'
    ];
    
    // Validation rules
    protected $validationRules = [
        'correo' => 'required|valid_email|max_length[100]',
        'nombre' => 'required|min_length[3]|max_length[100]',
        'password' => 'required|min_length[6]'
    ];
    
    protected $validationMessages = [
        'correo' => [
            'required' => 'El correo es obligatorio',
            'valid_email' => 'Debe ingresar un correo electrónico válido',
            'max_length' => 'El correo no puede exceder los 100 caracteres'
        ],
        'nombre' => [
            'required' => 'El nombre es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder los 100 caracteres'
        ],
        'password' => [
            'required' => 'La contraseña es obligatoria',
            'min_length' => 'La contraseña debe tener al menos 6 caracteres'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    // No timestamps are needed
    protected $useTimestamps = false;
} 