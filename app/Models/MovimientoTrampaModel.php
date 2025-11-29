<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientoTrampaModel extends Model
{
    protected $table      = 'historial_movimientos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_trampa',
        'tipo',
        'zona_anterior',
        'zona_nueva',
        'x_anterior',
        'y_anterior',
        'x_nueva',
        'y_nueva',
        'fecha_movimiento',
        'plano_id',
        'comentario'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'fecha_movimiento';
    protected $updatedField  = '';
    protected $deletedField  = '';

    protected $returnType = 'array';
} 