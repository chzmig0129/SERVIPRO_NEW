<?php

namespace App\Models;

use CodeIgniter\Model;

class RepositorioDocumentoModel extends Model
{
    protected $table      = 'repositorio_documentos';
    protected $primaryKey = 'id';
    
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    
    protected $allowedFields = [
        'sede_id',
        'titulo',
        'tipo',
        'descripcion',
        'nombre_archivo',
        'ruta_archivo',
        'tamaño_archivo',
        'tipo_mime',
        'fecha_documento',
        'created_at',
        'updated_at'
    ];
    
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    
    protected $validationRules = [
        'sede_id' => 'required|integer|is_not_unique[sedes.id]',
        'titulo' => 'required|min_length[3]|max_length[255]',
        'tipo' => 'required|in_list[plan_accion,documento,reporte,otro]',
        'nombre_archivo' => 'required|max_length[255]',
        'ruta_archivo' => 'required|max_length[500]'
    ];
    
    protected $validationMessages = [
        'sede_id' => [
            'required' => 'La sede es requerida',
            'integer' => 'La sede debe ser un número válido',
            'is_not_unique' => 'La sede seleccionada no existe'
        ],
        'titulo' => [
            'required' => 'El título es requerido',
            'min_length' => 'El título debe tener al menos 3 caracteres',
            'max_length' => 'El título no puede exceder los 255 caracteres'
        ],
        'tipo' => [
            'required' => 'El tipo de documento es requerido',
            'in_list' => 'El tipo debe ser: plan_accion, documento, reporte u otro'
        ],
        'nombre_archivo' => [
            'required' => 'El nombre del archivo es requerido',
            'max_length' => 'El nombre del archivo no puede exceder los 255 caracteres'
        ],
        'ruta_archivo' => [
            'required' => 'La ruta del archivo es requerida',
            'max_length' => 'La ruta del archivo no puede exceder los 500 caracteres'
        ]
    ];
    
    protected $skipValidation = false;
    protected $dateFormat = 'datetime';
    
    /**
     * Obtiene los últimos N documentos de una sede
     * 
     * @param int $sedeId ID de la sede
     * @param int $limite Número de documentos a obtener
     * @return array
     */
    public function obtenerUltimosDocumentos($sedeId, $limite = 3)
    {
        return $this->where('sede_id', $sedeId)
            ->orderBy('created_at', 'DESC')
            ->limit($limite)
            ->findAll();
    }
    
    /**
     * Obtiene todos los documentos de una sede con filtros opcionales
     * 
     * @param int $sedeId ID de la sede
     * @param string|null $tipo Tipo de documento a filtrar
     * @param string|null $busqueda Texto para buscar en título y descripción
     * @return array
     */
    public function obtenerDocumentosPorSede($sedeId, $tipo = null, $busqueda = null)
    {
        $builder = $this->where('sede_id', $sedeId);
        
        if ($tipo && !empty($tipo)) {
            $builder->where('tipo', $tipo);
        }
        
        if ($busqueda && !empty($busqueda)) {
            $builder->groupStart()
                ->like('titulo', $busqueda)
                ->orLike('descripcion', $busqueda)
                ->groupEnd();
        }
        
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }
}

