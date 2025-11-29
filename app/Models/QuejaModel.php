<?php

namespace App\Models;

use CodeIgniter\Model;

class QuejaModel extends Model
{
    protected $table      = 'quejas';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'fecha',
        'insecto',
        'ubicacion',
        'lineas',
        'archivo',
        'clasificacion',
        'sede_id',
        'estado',
        'estado_queja',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $validationRules = [
        'fecha' => 'required|valid_date',
        'insecto' => 'required|min_length[3]|max_length[100]',
        'ubicacion' => 'required|max_length[255]',
        'lineas' => 'required|max_length[100]',
        'clasificacion' => 'required|in_list[Crítico,Alto,Medio,Bajo]',
        'sede_id' => 'required|integer|is_not_unique[sedes.id]',
        'estado' => 'required|in_list[Vivo,Muerto]',
        'estado_queja' => 'required|in_list[Pendiente,Resuelta]'
    ];
    
    protected $validationMessages = [
        'fecha' => [
            'required' => 'La fecha es requerida',
            'valid_date' => 'La fecha debe ser válida'
        ],
        'insecto' => [
            'required' => 'El tipo de insecto es requerido',
            'min_length' => 'El tipo de insecto debe tener al menos 3 caracteres',
            'max_length' => 'El tipo de insecto no puede exceder los 100 caracteres'
        ],
        'ubicacion' => [
            'required' => 'La ubicación es requerida',
            'max_length' => 'La ubicación no puede exceder los 255 caracteres'
        ],
        'lineas' => [
            'required' => 'Las líneas afectadas son requeridas',
            'max_length' => 'Las líneas no pueden exceder los 100 caracteres'
        ],
        'clasificacion' => [
            'required' => 'La clasificación es requerida',
            'in_list' => 'La clasificación debe ser Crítico, Alto, Medio o Bajo'
        ],
        'sede_id' => [
            'required' => 'La sede es requerida',
            'integer' => 'La sede debe ser un número válido',
            'is_not_unique' => 'La sede seleccionada no existe'
        ],
        'estado' => [
            'required' => 'El estado del insecto es requerido',
            'in_list' => 'El estado debe ser Vivo o Muerto'
        ],
        'estado_queja' => [
            'required' => 'El estado de la queja es requerido',
            'in_list' => 'El estado de la queja debe ser Pendiente o Resuelta'
        ]
    ];
    
    protected $skipValidation = false;

    protected $dateFormat = 'datetime';
} 