<?php

/**
 * Helper para facilitar el registro de actividades en el sistema de auditoría
 * 
 * @package    App\Helpers
 * @category   Helper
 */

if (!function_exists('log_activity')) {
    /**
     * Registra una actividad en el log de auditoría
     * 
     * @param string $accion Tipo de acción (login, logout, create, update, delete, enable, disable, etc.)
     * @param string|null $tabla Nombre de la tabla afectada (opcional)
     * @param int|null $registroId ID del registro afectado (opcional)
     * @param string|null $descripcion Descripción detallada de la acción
     * @param array|null $datosAnteriores Datos anteriores (para updates)
     * @param array|null $datosNuevos Datos nuevos
     * @return int|false ID del log creado o false en caso de error
     */
    function log_activity(
        string $accion,
        ?string $tabla = null,
        ?int $registroId = null,
        ?string $descripcion = null,
        ?array $datosAnteriores = null,
        ?array $datosNuevos = null
    ) {
        try {
            $auditoriaModel = new \App\Models\AuditoriaLogModel();
            $session = session();
            $request = \Config\Services::request();

            // Obtener información del usuario de la sesión
            $usuarioId = $session->get('id') ?? null;
            $usuarioNombre = $session->get('nombre') ?? 'Sistema';

            // Obtener información de la petición
            $ipAddress = $request->getIPAddress();
            $userAgent = $request->getUserAgent()->getAgentString();

            // Preparar datos para el log
            $data = [
                'usuario_id' => $usuarioId,
                'usuario_nombre' => $usuarioNombre,
                'accion' => $accion,
                'tabla_afectada' => $tabla,
                'registro_id' => $registroId,
                'descripcion' => $descripcion,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => $datosNuevos,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'fecha_hora' => date('Y-m-d H:i:s')
            ];

            return $auditoriaModel->registrarActividad($data);
        } catch (\Exception $e) {
            // Si hay un error, registrarlo en el log del sistema pero no fallar
            log_message('error', 'Error al registrar actividad en auditoría: ' . $e->getMessage());
            return false;
        }
    }
}

if (!function_exists('log_login')) {
    /**
     * Registra un inicio de sesión
     * 
     * @param int $usuarioId ID del usuario
     * @param string $usuarioNombre Nombre del usuario
     * @param bool $exitoso Si el login fue exitoso
     * @return int|false
     */
    function log_login(int $usuarioId, string $usuarioNombre, bool $exitoso = true)
    {
        $accion = $exitoso ? 'login' : 'login_failed';
        $descripcion = $exitoso 
            ? "Usuario {$usuarioNombre} inició sesión exitosamente"
            : "Intento de inicio de sesión fallido para usuario ID: {$usuarioId}";
        
        return log_activity($accion, null, null, $descripcion);
    }
}

if (!function_exists('log_logout')) {
    /**
     * Registra un cierre de sesión
     * 
     * @param int $usuarioId ID del usuario
     * @param string $usuarioNombre Nombre del usuario
     * @return int|false
     */
    function log_logout(int $usuarioId, string $usuarioNombre)
    {
        $descripcion = "Usuario {$usuarioNombre} cerró sesión";
        return log_activity('logout', null, null, $descripcion);
    }
}

if (!function_exists('log_create')) {
    /**
     * Registra la creación de un registro
     * 
     * @param string $tabla Nombre de la tabla
     * @param int $registroId ID del registro creado
     * @param array $datosNuevos Datos del nuevo registro
     * @param string|null $descripcion Descripción adicional
     * @return int|false
     */
    function log_create(string $tabla, int $registroId, array $datosNuevos, ?string $descripcion = null)
    {
        if (!$descripcion) {
            $descripcion = "Se creó un nuevo registro en {$tabla} con ID: {$registroId}";
        }
        
        return log_activity('create', $tabla, $registroId, $descripcion, null, $datosNuevos);
    }
}

if (!function_exists('log_update')) {
    /**
     * Registra la actualización de un registro
     * 
     * @param string $tabla Nombre de la tabla
     * @param int $registroId ID del registro actualizado
     * @param array $datosAnteriores Datos anteriores
     * @param array $datosNuevos Datos nuevos
     * @param string|null $descripcion Descripción adicional
     * @return int|false
     */
    function log_update(
        string $tabla, 
        int $registroId, 
        array $datosAnteriores, 
        array $datosNuevos, 
        ?string $descripcion = null
    ) {
        if (!$descripcion) {
            $descripcion = "Se actualizó el registro ID: {$registroId} en {$tabla}";
        }
        
        return log_activity('update', $tabla, $registroId, $descripcion, $datosAnteriores, $datosNuevos);
    }
}

if (!function_exists('log_delete')) {
    /**
     * Registra la eliminación de un registro
     * 
     * @param string $tabla Nombre de la tabla
     * @param int $registroId ID del registro eliminado
     * @param array $datosAnteriores Datos del registro eliminado
     * @param string|null $descripcion Descripción adicional
     * @return int|false
     */
    function log_delete(string $tabla, int $registroId, array $datosAnteriores, ?string $descripcion = null)
    {
        if (!$descripcion) {
            $descripcion = "Se eliminó el registro ID: {$registroId} de {$tabla}";
        }
        
        return log_activity('delete', $tabla, $registroId, $descripcion, $datosAnteriores, null);
    }
}

if (!function_exists('log_status_change')) {
    /**
     * Registra un cambio de estatus (habilitar/deshabilitar)
     * 
     * @param string $tabla Nombre de la tabla
     * @param int $registroId ID del registro
     * @param string $estadoAnterior Estado anterior
     * @param string $estadoNuevo Estado nuevo
     * @param string|null $descripcion Descripción adicional
     * @return int|false
     */
    function log_status_change(
        string $tabla, 
        int $registroId, 
        string $estadoAnterior, 
        string $estadoNuevo, 
        ?string $descripcion = null
    ) {
        $accion = ($estadoNuevo == '0' || $estadoNuevo == 'Inactivo' || $estadoNuevo == 'deshabilitado') 
            ? 'disable' 
            : 'enable';
        
        if (!$descripcion) {
            $accionTexto = $accion == 'disable' ? 'deshabilitó' : 'habilitó';
            $descripcion = "Se {$accionTexto} el registro ID: {$registroId} en {$tabla}";
        }
        
        $datos = [
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $estadoNuevo
        ];
        
        return log_activity($accion, $tabla, $registroId, $descripcion, $datos, $datos);
    }
}

