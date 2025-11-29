<?php

namespace App\Models;

use CodeIgniter\Model;

class EvidenciaModel extends Model
{
    protected $table      = 'evidencia';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'id_plano',
        'ubicacion',
        'descripcion',
        'fecha_registro',
        'imagen_evidencia',
        'imagen_resuelta',
        'estado',
        'fecha_resolucion',
        'coordenada_x',
        'coordenada_y',
        'visto_bueno_supervisor'
    ];
    
    // Validation rules
    protected $validationRules      = [
        'id_plano'     => 'required|integer',
        'ubicacion'    => 'required',
        'coordenada_x' => 'required|decimal',
        'coordenada_y' => 'required|decimal'
    ];
    
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
    
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
} 