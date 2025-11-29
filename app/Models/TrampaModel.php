<?php

namespace App\Models;

use CodeIgniter\Model;

class TrampaModel extends Model
{
    protected $table      = 'trampas'; // Nombre de la tabla
    protected $primaryKey = 'id';     // Clave primaria
  
    protected $allowedFields = [
        'id_trampa', 'sede_id', 'plano_id','nombre', 'tipo', 'ubicacion', 
        'coordenada_x', 'coordenada_y', 'fecha_instalacion'
    ]; // Campos permitidos
    
    // Definir hooks para procesar datos antes de insertar
    protected $beforeInsert = ['inicializarNombre'];
    
    /**
     * Inicializa el campo nombre con el valor de id_trampa si no se proporciona
     * 
     * @param array $data Datos a insertar
     * @return array Datos modificados
     */
    protected function inicializarNombre(array $data)
    {
        // Si no se proporcionó un nombre, usar el id_trampa como nombre inicial
        if (empty($data['data']['nombre']) && !empty($data['data']['id_trampa'])) {
            $data['data']['nombre'] = $data['data']['id_trampa'];
        }
        
        return $data;
    }
    
    /**
     * Busca una trampa por su id_trampa (identificador único)
     * 
     * @param string $idTrampa ID de la trampa a buscar
     * @return array|null Datos de la trampa o null si no existe
     */
    public function buscarPorIdTrampa($idTrampa)
    {
        return $this->where('id_trampa', $idTrampa)->first();
    }
    
    /**
     * Actualiza el nombre de una trampa existente (cuando se mueve de lugar)
     * 
     * @param string $idTrampa ID de la trampa a actualizar
     * @param string $nuevoNombre Nuevo nombre para la trampa
     * @param array $otrosDatos Otros campos a actualizar (ubicacion, coordenadas, etc.)
     * @return bool True si se actualizó correctamente
     */
    public function actualizarNombreTrampa($idTrampa, $nuevoNombre, $otrosDatos = [])
    {
        $trampa = $this->buscarPorIdTrampa($idTrampa);
        
        if (!$trampa) {
            return false;
        }
        
        $datosActualizar = array_merge(['nombre' => $nuevoNombre], $otrosDatos);
        
        return $this->update($trampa['id'], $datosActualizar);
    }
}