<?php

namespace App\Models;

use CodeIgniter\Model;

class EstadoTrampaModel extends Model
{
    protected $table      = 'estado_trampas'; // Nombre de la tabla
    protected $primaryKey = 'id';     // Clave primaria
  
    protected $allowedFields = [
        'trampa_id', 'plano_id', 'sede_id', 'estado', 'observaciones', 
        'fecha_registro', 'usuario_registro'
    ]; // Campos permitidos
    
    protected $useTimestamps = true;
    protected $createdField  = 'fecha_registro';
    protected $updatedField  = 'fecha_actualizacion';
    protected $deletedField  = '';
    
    protected $returnType = 'array';
    
    /**
     * Obtiene el estado más reciente de una trampa
     * 
     * @param int $trampaId ID de la trampa
     * @return array|null Estado más reciente o null si no existe
     */
    public function obtenerEstadoReciente($trampaId)
    {
        return $this->where('trampa_id', $trampaId)
                    ->orderBy('fecha_registro', 'DESC')
                    ->first();
    }
    
    /**
     * Obtiene todos los estados de las trampas de un plano
     * 
     * @param int $planoId ID del plano
     * @return array Estados de las trampas
     */
    public function obtenerEstadosPorPlano($planoId)
    {
        // Obtener el estado más reciente de cada trampa del plano
        $db = \Config\Database::connect();
        
        $query = $db->query("
            SELECT et.*, t.id_trampa, t.tipo, t.nombre, t.ubicacion
            FROM estado_trampas et
            INNER JOIN trampas t ON et.trampa_id = t.id
            WHERE et.plano_id = ?
            AND et.id IN (
                SELECT MAX(id)
                FROM estado_trampas
                WHERE plano_id = ?
                GROUP BY trampa_id
            )
            ORDER BY t.id_trampa ASC
        ", [$planoId, $planoId]);
        
        return $query->getResultArray();
    }
    
    /**
     * Obtiene el estado actual de todas las trampas de un plano
     * Si una trampa no tiene registro, se considera como "Sin registro"
     * 
     * @param int $planoId ID del plano
     * @return array Estados de todas las trampas del plano
     */
    public function obtenerEstadosCompletosPorPlano($planoId)
    {
        $db = \Config\Database::connect();
        
        // Obtener todas las trampas del plano
        $trampaModel = new TrampaModel();
        $trampas = $trampaModel->where('plano_id', $planoId)->findAll();
        
        // Obtener los estados más recientes
        $estados = $this->obtenerEstadosPorPlano($planoId);
        
        // Crear un mapa de estados por trampa_id
        $estadosMap = [];
        foreach ($estados as $estado) {
            $estadosMap[$estado['trampa_id']] = $estado;
        }
        
        // Combinar trampas con sus estados
        $resultado = [];
        foreach ($trampas as $trampa) {
            $resultado[] = [
                'trampa_id' => $trampa['id'],
                'id_trampa' => $trampa['id_trampa'] ?? '',
                'tipo' => $trampa['tipo'] ?? '',
                'nombre' => $trampa['nombre'] ?? '',
                'ubicacion' => $trampa['ubicacion'] ?? '',
                'estado' => $estadosMap[$trampa['id']]['estado'] ?? 'sin_registro',
                'observaciones' => $estadosMap[$trampa['id']]['observaciones'] ?? '',
                'fecha_registro' => $estadosMap[$trampa['id']]['fecha_registro'] ?? null,
                'usuario_registro' => $estadosMap[$trampa['id']]['usuario_registro'] ?? null
            ];
        }
        
        return $resultado;
    }
}

