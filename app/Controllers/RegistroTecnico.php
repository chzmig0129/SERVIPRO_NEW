<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SedesModel;
use App\Models\PlanoModel;
use App\Models\TrampaModel;

class RegistroTecnico extends BaseController
{
    protected $sedesModel;
    protected $planoModel;
    protected $trampaModel;
    protected $quejaModel;

    public function __construct()
    {
        $this->sedesModel = new SedesModel();
        $this->planoModel = new PlanoModel();
        $this->trampaModel = new TrampaModel();
        $this->quejaModel = new \App\Models\QuejaModel();
    }

    /**
     * Obtiene las quejas pendientes para una sede específica
     * 
     * @param int $sedeId ID de la sede
     * @return array Array con las quejas pendientes
     */
    private function getQuejasPendientes($sedeId)
    {
        if (!$sedeId) {
            return [];
        }
        
        // Consulta para obtener las quejas pendientes de la sede
        $quejasPendientes = $this->quejaModel
            ->where('sede_id', $sedeId)
            ->where('estado_queja', 'Pendiente')
            ->orderBy('fecha', 'DESC')
            ->findAll();
            
        return $quejasPendientes;
    }

    public function index()
    {
        // Obtener la sede seleccionada o la primera por defecto
        $sedeSeleccionada = $this->request->getGet('sede_id');
        $sedes = $this->sedesModel->where('estatus', 1)->findAll();
        
        if (empty($sedeSeleccionada) && !empty($sedes)) {
            $sedeSeleccionada = $sedes[0]['id'];
        }

        // Obtener los planos de la sede seleccionada
        $planos = [];
        if ($sedeSeleccionada) {
            $planos = $this->planoModel->where('sede_id', $sedeSeleccionada)->findAll();
            
            // Procesar las previsualizaciones de los planos (similar a Blueprints)
            foreach ($planos as &$plano) {
                $plano['preview_image'] = $this->getPreviewImage($plano);
            }
        }

        // Obtener quejas pendientes de la sede seleccionada
        $quejasPendientes = $this->getQuejasPendientes($sedeSeleccionada);

        $data = [
            'sedes' => $sedes,
            'sedeSeleccionada' => $sedeSeleccionada,
            'planos' => $planos,
            'quejasPendientes' => $quejasPendientes
        ];

        return view('registro_tecnico/index', $data);
    }

    /**
     * Obtiene una imagen de previsualización del archivo JSON del plano
     * 
     * @param array $plano Datos del plano
     * @return string|null URL de la imagen de previsualización o null si no hay imagen
     */
    private function getPreviewImage($plano)
    {
        if (empty($plano['archivo'])) {
            return null;
        }

        try {
            $archivoData = json_decode($plano['archivo'], true);
            if (isset($archivoData['imagen']) && !empty($archivoData['imagen'])) {
                return $archivoData['imagen'];
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al procesar la imagen del plano: ' . $e->getMessage());
        }

        return null;
    }

    public function getPlanosBySede($sedeId)
    {
        try {
            $planos = $this->planoModel->where('sede_id', $sedeId)->findAll();
            
            // Procesar las previsualizaciones de los planos
            foreach ($planos as &$plano) {
                $plano['preview_image'] = $this->getPreviewImage($plano);
            }
            
            if (empty($planos)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se encontraron planos para esta sede'
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $planos
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener los planos: ' . $e->getMessage()
            ]);
        }
    }

    public function verTrampasPorPlano($planoId = null)
    {
        if (!$planoId) {
            return redirect()->to('/registro_tecnico')->with('error', 'Plano no especificado');
        }

        // Cargar modelos necesarios
        $planoModel = $this->planoModel;
        $sedeModel = $this->sedesModel;
        $trampaModel = $this->trampaModel;

        // Obtener información del plano
        $plano = $planoModel->find($planoId);
        if (!$plano) {
            return redirect()->to('/registro_tecnico')->with('error', 'Plano no encontrado');
        }

        // Obtener la sede
        $sede = $sedeModel->find($plano['sede_id']);

        // Obtener todas las trampas del plano
        $trampas = $trampaModel->where('plano_id', $planoId)->findAll();

        $data = [
            'plano' => $plano,
            'sede' => $sede,
            'trampas' => $trampas,
            'planoId' => $planoId
        ];

        return view('registro_tecnico/trampas_plano', $data);
    }

    /**
     * Obtiene el historial completo de una trampa específica
     */
    public function verHistorialTrampa($trampaId = null)
    {
        if (!$trampaId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de trampa no especificado'
            ]);
        }

        $db = \Config\Database::connect();
        
        try {
            // Obtener información básica de la trampa
            $trampa = $this->trampaModel->find($trampaId);
            if (!$trampa) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Trampa no encontrada'
                ]);
            }

            // Obtener todas las incidencias de esta trampa
            $queryIncidencias = $db->query("
                SELECT 
                    i.*,
                    DATE_FORMAT(i.fecha, '%d/%m/%Y %H:%i') as fecha_formateada
                FROM incidencias i
                WHERE i.id_trampa = ?
                ORDER BY i.fecha DESC
            ", [$trampaId]);
            $incidencias = $queryIncidencias->getResultArray();

            // Obtener historial de movimientos de la trampa
            $queryMovimientos = $db->query("
                SELECT 
                    m.*,
                    DATE_FORMAT(m.fecha_movimiento, '%d/%m/%Y %H:%i') as fecha_formateada
                FROM historial_movimientos m
                WHERE m.id_trampa = ?
                ORDER BY m.fecha_movimiento DESC
            ", [$trampaId]);
            $movimientos = $queryMovimientos->getResultArray();

            // Estadísticas de la trampa
            $queryEstadisticas = $db->query("
                SELECT 
                    COUNT(*) as total_incidencias,
                    COUNT(DISTINCT tipo_plaga) as tipos_plaga_diferentes,
                    SUM(cantidad_organismos) as total_organismos,
                    MAX(fecha) as ultima_incidencia
                FROM incidencias
                WHERE id_trampa = ?
            ", [$trampaId]);
            $estadisticas = $queryEstadisticas->getRowArray();

            // Organismos por tipo de plaga
            $queryTiposPlagas = $db->query("
                SELECT 
                    tipo_plaga,
                    COUNT(*) as frecuencia,
                    SUM(cantidad_organismos) as total_organismos
                FROM incidencias
                WHERE id_trampa = ? AND tipo_plaga IS NOT NULL
                GROUP BY tipo_plaga
                ORDER BY total_organismos DESC
            ", [$trampaId]);
            $tiposPlagas = $queryTiposPlagas->getResultArray();

            return $this->response->setJSON([
                'success' => true,
                'data' => [
                    'trampa' => $trampa,
                    'incidencias' => $incidencias,
                    'movimientos' => $movimientos,
                    'estadisticas' => $estadisticas,
                    'tipos_plagas' => $tiposPlagas
                ]
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al obtener historial de trampa: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener el historial: ' . $e->getMessage()
            ]);
        }
    }

    public function getTrampasPlano($planoId)
    {
        try {
            // Depuración - guardar en log
            log_message('info', 'Consultando trampas para el plano ID: ' . $planoId);
            
            // Obtener todas las trampas del plano
            $trampas = $this->trampaModel->where('plano_id', $planoId)->findAll();
            
            // Depuración - guardar en log el resultado
            log_message('info', 'Resultado de la consulta: ' . (empty($trampas) ? 'Sin trampas' : count($trampas) . ' trampas encontradas'));
            
            if (empty($trampas)) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => [],
                    'message' => 'No se encontraron trampas para este plano'
                ]);
            }
            
            // Procesar la información de cada trampa para la vista
            foreach ($trampas as &$trampa) {
                // Agregar campos adicionales si se necesitan
                $trampa['posicion_x'] = $trampa['coordenada_x'] ?? '';
                $trampa['posicion_y'] = $trampa['coordenada_y'] ?? '';
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $trampas
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al obtener trampas: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener las trampas: ' . $e->getMessage()
            ]);
        }
    }

    public function guardarIncidencia()
    {
        // Verificar si es una solicitud POST
        if (!$this->request->isAJAX() && !$this->request->getMethod() === 'post') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
        }
        
        // Obtener y formatear la fecha de incidencia
        $fechaIncidencia = $this->request->getPost('fecha_incidencia');
        $fechaFormateada = null;
        
        log_message('info', 'Fecha recibida del formulario: ' . $fechaIncidencia);
        
        // Verificar si la fecha tiene un formato válido
        if (!empty($fechaIncidencia)) {
            try {
                // Intentar convertir la fecha recibida al formato MySQL
                if (strpos($fechaIncidencia, 'T') !== false) {
                    // Es un formato datetime-local (YYYY-MM-DDTHH:MM)
                    $fecha = new \DateTime($fechaIncidencia);
                } else if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $fechaIncidencia)) {
                    // Ya está en formato MySQL (YYYY-MM-DD HH:MM:SS)
                    $fecha = new \DateTime($fechaIncidencia);
                } else {
                    // Intentar con cualquier otro formato reconocible
                    $fecha = new \DateTime($fechaIncidencia);
                }
                
                // Formatear siempre al formato estándar MySQL
                $fechaFormateada = $fecha->format('Y-m-d H:i:s');
                log_message('info', 'Fecha formateada: ' . $fechaFormateada);
            } catch (\Exception $e) {
                log_message('error', 'Error al formatear la fecha: ' . $e->getMessage() . ' - Fecha recibida: ' . $fechaIncidencia);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Formato de fecha incorrecto: ' . $e->getMessage()
                ]);
            }
        } else {
            // Si no se proporciona fecha, usar la fecha actual
            $fechaFormateada = date('Y-m-d H:i:s');
            log_message('info', 'Usando fecha actual: ' . $fechaFormateada);
        }
        
        // Obtener los datos del formulario
        $data = [
            'id_trampa' => $this->request->getPost('trampa_id'),
            'plano_id' => $this->request->getPost('plano_id'),
            'tipo_plaga' => $this->request->getPost('tipo_plaga'),
            'tipo_incidencia' => $this->request->getPost('tipo_incidencia'),
            'tipo_insecto' => $this->request->getPost('tipo_insecto'),
            'cantidad_organismos' => $this->request->getPost('cantidad_organismos'),
            'fecha' => $fechaFormateada,
            'inspector' => $this->request->getPost('inspector'),
            'notas' => $this->request->getPost('notas'),
            'fecha_registro' => date('Y-m-d H:i:s')
        ];
        
        try {
            // Cargar el modelo de incidencias
            $incidenciaModel = new \App\Models\IncidenciaModel();
            
            // Obtener el ID de la sede a partir del plano
            $planoId = $data['plano_id'];
            if (!empty($planoId)) {
                // Obtener información del plano
                $plano = $this->planoModel->find($planoId);
                if ($plano && isset($plano['sede_id'])) {
                    $data['sede_id'] = $plano['sede_id'];
                    log_message('info', 'Sede ID obtenido del plano: ' . $plano['sede_id']);
                } else {
                    log_message('error', 'No se pudo obtener la sede_id del plano: ' . $planoId);
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'No se pudo obtener la sede asociada al plano.'
                    ]);
                }
            } else {
                log_message('error', 'No se proporcionó un plano_id válido.');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se proporcionó un plano_id válido.'
                ]);
            }
            
            // Guardar la incidencia
            log_message('info', 'Intentando guardar incidencia con datos: ' . json_encode($data));
            $resultado = $incidenciaModel->insert($data);
            
            if ($resultado) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Incidencia guardada correctamente'
                ]);
            } else {
                log_message('error', 'Error al guardar incidencia: ' . json_encode($incidenciaModel->errors()));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al guardar la incidencia: ' . json_encode($incidenciaModel->errors())
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Excepción al guardar incidencia: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al guardar la incidencia: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Actualiza el estado de una trampa
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function actualizarEstadoTrampa()
    {
        // Verificar si es una solicitud AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
        }
        
        try {
            // Obtener datos del request
            $trampaId = $this->request->getPost('trampa_id');
            $nuevoEstado = $this->request->getPost('estado');
            
            // Validar datos
            if (empty($trampaId) || empty($nuevoEstado)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Datos incompletos para actualizar el estado'
                ]);
            }
            
            // Validar que el estado sea válido
            $estadosValidos = ['Activo', 'Inactivo', 'En mantenimiento', 'Reemplazada'];
            if (!in_array($nuevoEstado, $estadosValidos)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Estado no válido'
                ]);
            }
            
            // Actualizar el estado en la base de datos
            $actualizado = $this->trampaModel->update($trampaId, ['estado' => $nuevoEstado]);
            
            if ($actualizado) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Estado de la trampa actualizado correctamente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al actualizar el estado de la trampa: ' . 
                                json_encode($this->trampaModel->errors())
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar estado de trampa: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Actualiza el estado de una queja
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function actualizarEstadoQueja()
    {
        // Verificar si es una solicitud AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
        }
        
        try {
            // Obtener datos del request
            $quejaId = $this->request->getPost('queja_id');
            $nuevoEstado = $this->request->getPost('estado_queja');
            
            // Validar datos
            if (empty($quejaId) || empty($nuevoEstado)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Datos incompletos para actualizar el estado'
                ]);
            }
            
            // Validar que el estado sea válido
            $estadosValidos = ['Pendiente', 'Resuelta'];
            if (!in_array($nuevoEstado, $estadosValidos)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Estado no válido'
                ]);
            }
            
            // Actualizar el estado en la base de datos
            $actualizado = $this->quejaModel->update($quejaId, [
                'estado_queja' => $nuevoEstado,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($actualizado) {
                $mensaje = ($nuevoEstado === 'Resuelta') 
                    ? 'La queja ha sido marcada como resuelta correctamente' 
                    : 'La queja ha sido marcada como pendiente correctamente';
                    
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $mensaje
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al actualizar el estado de la queja: ' . 
                                json_encode($this->quejaModel->errors())
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar estado de queja: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ]);
        }
    }

    // Método para actualizar el ID de una trampa
    public function actualizar_id_trampa()
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }
        
        // Obtener los datos del POST
        $trampaIdActual = $this->request->getPost('trampa_id_actual');
        $nuevoIdTrampa = $this->request->getPost('nuevo_id_trampa');
        
        if (!$trampaIdActual || !$nuevoIdTrampa) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Datos incompletos para actualizar el ID'
            ]);
        }
        
        try {
            // Buscar la trampa por su ID actual (puede ser id_trampa o id)
            $trampa = $this->trampaModel->where('id_trampa', $trampaIdActual)->first();
            
            // Si no se encuentra por id_trampa, buscar por id
            if (!$trampa) {
                $trampa = $this->trampaModel->find($trampaIdActual);
            }
            
            // Si es un ID temporal, buscar trampas recientes sin id_trampa o con ID temporal
            if (!$trampa && strpos($trampaIdActual, 'TEMP-') === 0) {
                // Para IDs temporales, buscar la trampa más reciente de este plano
                $planoId = $this->request->getPost('plano_id');
                if ($planoId) {
                    $trampa = $this->trampaModel->where('plano_id', $planoId)
                                                ->where('id_trampa IS NULL OR id_trampa = ""')
                                                ->orderBy('id', 'DESC')
                                                ->first();
                }
            }
            
            if (!$trampa) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se encontró la trampa especificada'
                ]);
            }
            
            // Verificar que el nuevo ID no esté ya en uso por otra trampa
            $trampaExistente = $this->trampaModel->where('id_trampa', $nuevoIdTrampa)
                                                 ->where('id !=', $trampa['id'])
                                                 ->first();
            
            if ($trampaExistente) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'El ID "' . $nuevoIdTrampa . '" ya está en uso por otra trampa'
                ]);
            }
            
            // Actualizar el ID de la trampa
            $actualizado = $this->trampaModel->update($trampa['id'], ['id_trampa' => $nuevoIdTrampa]);
            
            if ($actualizado) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'ID de trampa actualizado correctamente',
                    'trampa' => [
                        'id' => $trampa['id'],
                        'id_trampa_anterior' => $trampaIdActual,
                        'id_trampa_nuevo' => $nuevoIdTrampa
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al actualizar el ID en la base de datos'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar el ID: ' . $e->getMessage()
            ]);
        }
    }
} 