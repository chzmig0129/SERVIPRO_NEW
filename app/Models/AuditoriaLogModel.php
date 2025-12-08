<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditoriaLogModel extends Model
{
    protected $table      = 'auditoria_logs';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $useTimestamps = false;
    
    protected $allowedFields = [
        'usuario_id',
        'usuario_nombre',
        'accion',
        'tabla_afectada',
        'registro_id',
        'descripcion',
        'datos_anteriores',
        'datos_nuevos',
        'ip_address',
        'user_agent',
        'fecha_hora'
    ];

    /**
     * Registra una actividad en el log de auditoría
     * 
     * @param array $data Datos de la actividad
     * @return int|false ID del log creado o false en caso de error
     */
    public function registrarActividad(array $data)
    {
        // Asegurar que fecha_hora esté presente
        if (!isset($data['fecha_hora'])) {
            $data['fecha_hora'] = date('Y-m-d H:i:s');
        }

        // Convertir arrays a JSON si es necesario
        if (isset($data['datos_anteriores']) && is_array($data['datos_anteriores'])) {
            $data['datos_anteriores'] = json_encode($data['datos_anteriores'], JSON_UNESCAPED_UNICODE);
        }
        
        if (isset($data['datos_nuevos']) && is_array($data['datos_nuevos'])) {
            $data['datos_nuevos'] = json_encode($data['datos_nuevos'], JSON_UNESCAPED_UNICODE);
        }

        return $this->insert($data);
    }

    /**
     * Obtiene los logs de un usuario específico
     * 
     * @param int $usuarioId ID del usuario
     * @param int $limit Límite de resultados
     * @return array
     */
    public function obtenerLogsPorUsuario($usuarioId, $limit = 50)
    {
        return $this->where('usuario_id', $usuarioId)
                    ->orderBy('fecha_hora', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Obtiene los logs de una tabla específica
     * 
     * @param string $tabla Nombre de la tabla
     * @param int $limit Límite de resultados
     * @return array
     */
    public function obtenerLogsPorTabla($tabla, $limit = 50)
    {
        return $this->where('tabla_afectada', $tabla)
                    ->orderBy('fecha_hora', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Obtiene los logs de un registro específico
     * 
     * @param string $tabla Nombre de la tabla
     * @param int $registroId ID del registro
     * @return array
     */
    public function obtenerLogsPorRegistro($tabla, $registroId)
    {
        return $this->where('tabla_afectada', $tabla)
                    ->where('registro_id', $registroId)
                    ->orderBy('fecha_hora', 'DESC')
                    ->findAll();
    }

    /**
     * Obtiene los logs de una acción específica
     * 
     * @param string $accion Tipo de acción
     * @param int $limit Límite de resultados
     * @return array
     */
    public function obtenerLogsPorAccion($accion, $limit = 50)
    {
        return $this->where('accion', $accion)
                    ->orderBy('fecha_hora', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Obtiene los logs en un rango de fechas
     * 
     * @param string $fechaInicio Fecha de inicio (Y-m-d)
     * @param string $fechaFin Fecha de fin (Y-m-d)
     * @param int $limit Límite de resultados
     * @return array
     */
    public function obtenerLogsPorRangoFechas($fechaInicio, $fechaFin, $limit = 100)
    {
        return $this->where('fecha_hora >=', $fechaInicio . ' 00:00:00')
                    ->where('fecha_hora <=', $fechaFin . ' 23:59:59')
                    ->orderBy('fecha_hora', 'DESC')
                    ->findAll($limit);
    }
}

