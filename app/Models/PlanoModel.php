<?php

namespace App\Models;

use CodeIgniter\Model;

class PlanoModel extends Model
{
    protected $table      = 'planos'; // Nombre de la tabla
    protected $primaryKey = 'id';     // Clave primaria
    protected $useAutoIncrement = true;

    protected $allowedFields = ['nombre', 'descripcion', 'sede_id', 'fecha_creacion', 'archivo', 'habilitado']; // Campos permitidos

    protected $useTimestamps = true;
    protected $createdField = 'fecha_creacion';
    protected $updatedField = '';
    protected $deletedField = '';
}