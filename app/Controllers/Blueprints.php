<?php

namespace App\Controllers;
use App\Models\SedeModel;
use App\Models\PlanoModel;
use App\Models\IncidenciaModel;
use App\Models\EstadoTrampaModel;
use App\Models\MovimientoTrampaModel;
use CodeIgniter\I18n\Time;

class Blueprints extends BaseController
{
    public function index()
    {
        // Verificar si el usuario ha iniciado sesión
        $this->verificarSesion();
        
        // Cargar los modelos necesarios
        $sedeModel = new SedeModel();
        $planoModel = new PlanoModel();
        
        // Obtener solo las sedes activas (estatus = 1)
        $data['sedes'] = $sedeModel->where('estatus', 1)->findAll();
        
        // Obtener solo los planos habilitados (habilitado = 1)
        $planos = $planoModel->where('habilitado', 1)->findAll();
        
        // Procesar las previsualizaciones de los planos
        foreach ($planos as &$plano) {
            $plano['preview_image'] = $this->getPreviewImage($plano);
            // Obtener el nombre de la sede para cada plano
            $sede = $sedeModel->find($plano['sede_id']);
            $plano['sede_nombre'] = $sede ? $sede['nombre'] : 'Sede desconocida';
        }
        
        $data['planos'] = $planos;
        
        // Cargar la vista con los datos
        return view('blueprints/index', $data);
    }

    public function view($id = null)
    {
        if (!$id) {
            return redirect()->to('/blueprints')->with('error', 'Sede no especificada');
        }

        // Cargar modelos
        $sedeModel = new SedeModel();
        $planoModel = new PlanoModel();

        // Obtener información de la sede (solo activas)
        $sede = $sedeModel->where('estatus', 1)->find($id);
        if (!$sede) {
            return redirect()->to('/blueprints')->with('error', 'Sede no encontrada o inactiva');
        }

        // Obtener solo los planos habilitados de la sede
        $planos = $planoModel->where('sede_id', $id)->where('habilitado', 1)->findAll();

        // Obtener el conteo de incidencias por plano
        $db = \Config\Database::connect();
        $trampaModel = new \App\Models\TrampaModel();
        $incidenciaModel = new \App\Models\IncidenciaModel();
        
        // Procesar las previsualizaciones de los planos y agregar conteo de incidencias
        foreach ($planos as &$plano) {
            $plano['preview_image'] = $this->getPreviewImage($plano);
            
            // Obtener las trampas del plano
            $trampas = $trampaModel->where('plano_id', $plano['id'])->findAll();
            
            // Contar las incidencias de las trampas de este plano
            // IMPORTANTE: Usar EXACTAMENTE la misma lógica que en viewplano() para mantener consistencia
            // Filtrar por: trampa pertenece al plano Y trampa tiene sede_id del plano Y incidencia tiene mismo sede_id que trampa
            $conteoIncidencias = 0;
            if (!empty($trampas)) {
                $trampaIds = array_column($trampas, 'id');
                // Usar la misma consulta que en viewplano() pero para contar
                // Esta es la consulta exacta de viewplano() pero con COUNT(*)
                $query = $db->query("
                    SELECT COUNT(*) as total
                    FROM incidencias i
                    INNER JOIN trampas t ON i.id_trampa = t.id
                    WHERE i.id_trampa IN (" . implode(',', $trampaIds) . ")
                    AND t.sede_id = ?
                    AND i.sede_id = t.sede_id
                ", [$plano['sede_id']]);
                $result = $query->getRow();
                $conteoIncidencias = $result ? (int)$result->total : 0;
            }
            
            $plano['conteo_incidencias'] = $conteoIncidencias;
        }

        // Calcular totales para la sede
        $totalPlanos = count($planos);
        $totalIncidencias = array_sum(array_column($planos, 'conteo_incidencias'));

        $data = [
            'sede' => $sede,
            'planos' => $planos,
            'total_planos' => $totalPlanos,
            'total_incidencias' => $totalIncidencias
        ];

        return view('blueprints/view', $data);
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

    public function guardar_plano()
    {
        // Validar los datos del formulario
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombre' => 'required|max_length[255]',
            'descripcion' => 'required',
            'sede_id' => 'required|numeric'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Por favor, complete todos los campos correctamente.');
        }

        try {
            // Obtener los datos del formulario
            $data = [
                'nombre' => $this->request->getPost('nombre'),
                'descripcion' => $this->request->getPost('descripcion'),
                'sede_id' => $this->request->getPost('sede_id'),
                'fecha_creacion' => Time::now('America/Mexico_City')->format('Y-m-d H:i:s'),
                'habilitado' => 1 // Los nuevos planos se crean habilitados por defecto
            ];

            // Guardar los datos en la base de datos
            $planoModel = new PlanoModel();
            $planoId = $planoModel->insert($data, true); // El segundo parámetro true hace que retorne el ID insertado

            // Registrar en auditoría
            log_create('planos', $planoId, $data, "Se creó un nuevo plano: {$data['nombre']} para la sede ID: {$data['sede_id']}");

            // Redirigir a la vista del plano con mensaje de éxito
            return redirect()->to('blueprints/viewplano/' . $planoId)
                            ->with('message', 'Plano guardado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Error al guardar el plano. Por favor, intente nuevamente.');
        }
    }

    // Agregar el método para ver un plano específico
    public function viewplano($id = null)
    {
        if (!$id) {
            return redirect()->to('/blueprints')->with('error', 'Plano no especificado');
        }

        // Cargar modelos
        $planoModel = new PlanoModel();
        $sedeModel = new SedeModel();
        $trampaModel = new \App\Models\TrampaModel();

        // Obtener información del plano
        $plano = $planoModel->find($id);
        if (!$plano) {
            return redirect()->to('/blueprints')->with('error', 'Plano no encontrado');
        }

        // Obtener información de la sede asociada
        $sede = $sedeModel->find($plano['sede_id']);
        
        // Obtener las trampas desde la base de datos (fuente de verdad)
        $trampas = $trampaModel->where('plano_id', $id)->findAll();

        // Obtener las incidencias de las trampas de este plano
        // IMPORTANTE: Incluir tanto incidencias con trampa (Captura) como sin trampa (Hallazgo)
        // Solo mostrar incidencias donde la trampa pertenece a la misma sede del plano
        // y donde la incidencia y la trampa tienen el mismo sede_id (mismo criterio que el dashboard)
        $incidenciaModel = new \App\Models\IncidenciaModel();
        $incidencias = [];
        
        $db = \Config\Database::connect();
        
        // Obtener incidencias con trampa asociada (Capturas)
        $incidenciasConTrampa = [];
        if (!empty($trampas)) {
            $trampaIds = array_column($trampas, 'id');
            
            // Obtener incidencias con información de la trampa asociada
            // Filtrar por: trampa pertenece al plano Y trampa tiene sede_id del plano Y incidencia tiene mismo sede_id que trampa
            $query = $db->query("
                SELECT i.id, i.fecha, i.tipo_plaga, i.tipo_insecto, i.cantidad_organismos, 
                       i.tipo_incidencia, i.notas, i.inspector, i.sede_id, i.id_trampa as incidencia_trampa_id,
                       COALESCE(NULLIF(t.id_trampa, ''), CAST(t.id AS CHAR)) as id_trampa, 
                       t.nombre as trampa_nombre, 
                       t.ubicacion as trampa_ubicacion
                FROM incidencias i
                INNER JOIN trampas t ON i.id_trampa = t.id
                WHERE i.id_trampa IN (" . implode(',', $trampaIds) . ")
                AND t.sede_id = ?
                AND i.sede_id = t.sede_id
                ORDER BY i.fecha DESC
            ", [$plano['sede_id']]);
            
            $incidenciasConTrampa = $query->getResultArray();
        }
        
        // Obtener incidencias sin trampa asociada (Hallazgos) que pertenecen a este plano específico
        $queryHallazgos = $db->query("
            SELECT i.id, i.fecha, i.tipo_plaga, i.tipo_insecto, i.cantidad_organismos, 
                   i.tipo_incidencia, i.notas, i.inspector, i.sede_id, i.id_trampa as incidencia_trampa_id,
                   NULL as id_trampa,
                   NULL as trampa_nombre,
                   CASE 
                       WHEN i.notas LIKE '%Zona: %' THEN 
                           TRIM(SUBSTRING_INDEX(
                               SUBSTRING_INDEX(i.notas, 'Zona: ', -1), 
                               ' |', 
                               1
                           ))
                       WHEN i.notas LIKE '%| Zona: %' THEN 
                           TRIM(SUBSTRING_INDEX(
                               SUBSTRING_INDEX(i.notas, 'Zona: ', -1), 
                               ' |', 
                               1
                           ))
                       ELSE NULL
                   END as trampa_ubicacion
            FROM incidencias i
            WHERE i.id_trampa IS NULL
            AND i.plano_id = ?
            AND i.sede_id = ?
            AND i.tipo_incidencia = 'Hallazgo'
            ORDER BY i.fecha DESC
        ", [$id, $plano['sede_id']]);
        
        $incidenciasHallazgos = $queryHallazgos->getResultArray();
        
        // Combinar ambas listas de incidencias
        $incidencias = array_merge($incidenciasConTrampa, $incidenciasHallazgos);
        
        // Ordenar por fecha descendente
        usort($incidencias, function($a, $b) {
            $fechaA = strtotime($a['fecha'] ?? '1970-01-01');
            $fechaB = strtotime($b['fecha'] ?? '1970-01-01');
            return $fechaB - $fechaA;
        });

        $data = [
            'plano' => $plano,
            'sede' => $sede,
            'trampas' => $trampas, // Agregar trampas desde BD
            'incidencias' => $incidencias // Agregar incidencias del plano
        ];

        return view('blueprints/viewplano', $data);
    }

    /**
     * Muestra la página para subir incidencias por Excel
     */
    public function uploadIncidenciasExcel($id = null)
    {
        if (!$id) {
            return redirect()->to('/blueprints')->with('error', 'Plano no especificado');
        }

        // Cargar modelos
        $planoModel = new PlanoModel();
        $sedeModel = new SedeModel();

        // Obtener información del plano
        $plano = $planoModel->find($id);
        if (!$plano) {
            return redirect()->to('/blueprints')->with('error', 'Plano no encontrado');
        }

        // Obtener información de la sede asociada
        $sede = $sedeModel->find($plano['sede_id']);
        
        // Obtener las trampas del plano
        $trampaModel = new \App\Models\TrampaModel();
        $trampas = $trampaModel->where('plano_id', $id)->findAll();

        $data = [
            'plano' => $plano,
            'sede' => $sede,
            'trampas' => $trampas,
            'title' => 'Subida de incidencias por Excel'
        ];

        return view('blueprints/upload_incidencias_excel', $data);
    }

    // Método para guardar el estado del plano (JSON)
    public function guardar_estado()
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }

        // Obtener los datos JSON y el ID del plano
        $planoId = $this->request->getPost('plano_id');
        $jsonData = $this->request->getPost('json_data');

        if (!$planoId || !$jsonData) {
            return $this->response->setJSON(['success' => false, 'message' => 'Datos incompletos']);
        }

        try {
            // Cargar el modelo de planos
            $planoModel = new PlanoModel();
            
            // Verificar que el plano existe
            $plano = $planoModel->find($planoId);
            if (!$plano) {
                return $this->response->setJSON(['success' => false, 'message' => 'Plano no encontrado']);
            }
            
            // Decodificar los datos JSON
            $estadoData = json_decode($jsonData, true);
            
            // Verificar si hay una imagen en los datos
            if (isset($estadoData['imagen']) && !empty($estadoData['imagen'])) {
                // Verificar si es una imagen base64
                if (strpos($estadoData['imagen'], 'data:image') === 0 && strpos($estadoData['imagen'], 'base64,') !== false) {
                    // Extraer el tipo de imagen y los datos base64
                    $partes = explode('base64,', $estadoData['imagen']);
                    if (count($partes) === 2) {
                        $cabecera = $partes[0];
                        $datos = $partes[1];
                        
                        // Determinar la extensión del archivo basado en el tipo MIME
                        $extension = 'png'; // Por defecto
                        if (strpos($cabecera, 'image/jpeg') !== false) {
                            $extension = 'jpg';
                        } elseif (strpos($cabecera, 'image/gif') !== false) {
                            $extension = 'gif';
                        }
                        
                        // Generar un nombre de archivo único
                        $nombreArchivo = 'plano_' . $planoId . '_' . time() . '.' . $extension;
                        $rutaArchivo = FCPATH . 'uploads/planos/' . $nombreArchivo;
                        
                        // Guardar la imagen en el sistema de archivos
                        if (file_put_contents($rutaArchivo, base64_decode($datos))) {
                            // Actualizar el JSON para que contenga la ruta de la imagen en lugar de los datos base64
                            $rutaRelativa = base_url('uploads/planos/' . $nombreArchivo);
                            $estadoData['imagen'] = $rutaRelativa;
                            
                            // Actualizar el JSON
                            $jsonData = json_encode($estadoData);
                        } else {
                            return $this->response->setJSON(['success' => false, 'message' => 'Error al guardar la imagen en el servidor']);
                        }
                    }
                } elseif (strpos($estadoData['imagen'], base_url('uploads/planos/')) === 0) {
                    // La imagen ya es una URL, no necesitamos hacer nada
                }
            }
            
            // Actualizar el campo 'archivo' con los datos JSON
            $planoModel->update($planoId, ['archivo' => $jsonData]);
            
            return $this->response->setJSON(['success' => true, 'message' => 'Estado del plano guardado correctamente']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error al guardar el estado: ' . $e->getMessage()]);
        }
    }
    
    // Método para obtener el estado actual del plano
    public function obtener_estado($id = null)
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }
        
        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID de plano no especificado']);
        }
        
        try {
            // Cargar los modelos
            $planoModel = new PlanoModel();
            $trampaModel = new \App\Models\TrampaModel();
            
            // Obtener el plano
            $plano = $planoModel->find($id);
            if (!$plano) {
                return $this->response->setJSON(['success' => false, 'message' => 'Plano no encontrado']);
            }
            
            // Obtener las trampas desde la base de datos (fuente de verdad)
            $trampas = $trampaModel->where('plano_id', $id)->findAll();
            
            // Devolver el plano con su estado y las trampas de la BD
            return $this->response->setJSON([
                'success' => true, 
                'plano' => $plano,
                'trampas' => $trampas // Agregar trampas desde BD
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error al obtener el estado: ' . $e->getMessage()]);
        }
    }
    
    // Método para guardar una trampa en la base de datos
    public function guardar_trampa()
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }
        
        // Obtener los datos de la trampa
        $sedeId = $this->request->getPost('sede_id');
        $planoId = $this->request->getPost('plano_id');
        $tipo = $this->request->getPost('tipo');
        $ubicacion = $this->request->getPost('ubicacion');
        $coordenadaX = $this->request->getPost('coordenada_x');
        $coordenadaY = $this->request->getPost('coordenada_y');
        $idTrampa = $this->request->getPost('id_trampa'); // Obtener id_trampa (etiqueta) si existe
        $dbId = $this->request->getPost('db_id'); // ID único de la BD (PRIMARY KEY)
        $comentario = $this->request->getPost('comentario'); // Obtener el comentario del movimiento
        
        if (!$sedeId || !$planoId || !$tipo || !$ubicacion || !$coordenadaX || !$coordenadaY) {
            return $this->response->setJSON(['success' => false, 'message' => 'Datos incompletos']);
        }
        
        try {
            // Cargar los modelos necesarios
            $trampaModel = new \App\Models\TrampaModel();
            $movimientoModel = new \App\Models\MovimientoTrampaModel();
            
            // Determinar si es un movimiento usando el db_id (ID único de la BD)
            // Solo es movimiento si se proporciona el db_id de una trampa existente
            $trampaAnterior = null;
            if ($dbId) {
                // Buscar por el ID único de la BD (PRIMARY KEY) - identificador irrepetible
                $trampaAnterior = $trampaModel->find($dbId);
                
                // Si existe la trampa, es un movimiento
                if ($trampaAnterior) {
                    // Verificar si realmente hay cambios en la posición o ubicación
                    if ($trampaAnterior['coordenada_x'] == $coordenadaX &&
                        $trampaAnterior['coordenada_y'] == $coordenadaY &&
                        $trampaAnterior['ubicacion'] == $ubicacion) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'No se detectaron cambios en la trampa'
                        ]);
                    }
                }
            }
            
            // Si es un movimiento, actualizar la trampa existente
            if ($trampaAnterior) {
                // Al mover una trampa, NO actualizar ni id_trampa ni nombre (deben permanecer constantes)
                // Solo actualizar ubicación, coordenadas y otros datos
                $dataActualizacion = [
                    'sede_id' => $sedeId,
                    'plano_id' => $planoId,
                    'tipo' => $tipo,
                    'ubicacion' => $ubicacion,
                    'coordenada_x' => $coordenadaX,
                    'coordenada_y' => $coordenadaY
                    // nombre permanece igual - solo se cambia desde el modal "Editar ID"
                ];
                
                $trampaModel->update($trampaAnterior['id'], $dataActualizacion);
                $trampaId = $trampaAnterior['id'];
            } else {
                // Es una trampa nueva - preparar los datos para guardar
                $data = [
                    'sede_id' => $sedeId,
                    'plano_id' => $planoId,
                    'tipo' => $tipo,
                    'ubicacion' => $ubicacion,
                    'coordenada_x' => $coordenadaX,
                    'coordenada_y' => $coordenadaY,
                    'fecha_instalacion' => date('Y-m-d H:i:s')
                ];
                
                // Si se proporcionó un id_trampa, usarlo (requerido para trampas nuevas)
                if ($idTrampa) {
                    $data['id_trampa'] = $idTrampa;
                    // El campo 'nombre' se inicializará automáticamente con el valor de id_trampa
                }
                
                // Guardar la trampa nueva y obtener el ID insertado
                $trampaId = $trampaModel->insert($data);
            }
            
            // Obtener el registro completo para recuperar el id_trampa generado
            $trampa = $trampaModel->find($trampaId);
            
            // Si es una trampa existente y sus coordenadas o ubicación han cambiado
            if ($trampaAnterior && (
                $trampaAnterior['coordenada_x'] != $coordenadaX ||
                $trampaAnterior['coordenada_y'] != $coordenadaY ||
                $trampaAnterior['ubicacion'] != $ubicacion
            )) {
                // Registrar el movimiento en el historial
                // Usar el id_trampa de la trampa anterior para consistencia
                $movimientoModel->insert([
                    'id_trampa' => $trampaAnterior['id_trampa'] ?? $idTrampa,
                    'tipo' => $tipo,
                    'zona_anterior' => $trampaAnterior['ubicacion'],
                    'zona_nueva' => $ubicacion,
                    'x_anterior' => $trampaAnterior['coordenada_x'],
                    'y_anterior' => $trampaAnterior['coordenada_y'],
                    'x_nueva' => $coordenadaX,
                    'y_nueva' => $coordenadaY,
                    'plano_id' => $planoId,
                    'comentario' => $comentario ?: 'Sin comentario' // Guardar el comentario o un valor por defecto
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true, 
                'message' => $trampaAnterior ? 'Trampa movida correctamente' : 'Trampa guardada correctamente',
                'trampa' => [
                    'id' => $trampaId,
                    'db_id' => $trampaId, // ID único de la BD (PRIMARY KEY)
                    'id_trampa' => $trampa['id_trampa'] ?? '',
                    'nombre' => $trampa['nombre'] ?? '',
                    'es_movida' => (bool)$trampaAnterior
                ]
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error al guardar la trampa: ' . $e->getMessage()
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
        $dbId = $this->request->getPost('db_id');
        $trampaIdActual = $this->request->getPost('trampa_id_actual');
        $nuevoIdTrampa = $this->request->getPost('nuevo_id_trampa');
        
        if (!$nuevoIdTrampa) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Datos incompletos para actualizar el ID'
            ]);
        }
        
        try {
            // Cargar el modelo de trampas
            $trampaModel = new \App\Models\TrampaModel();
            
            $trampa = null;
            
            // PRIORIDAD 1: Buscar por db_id (PK único, siempre correcto)
            if ($dbId && is_numeric($dbId)) {
                $trampa = $trampaModel->find($dbId);
            }
            
            // PRIORIDAD 2: Fallback para trampas temporales o sin db_id
            if (!$trampa && $trampaIdActual) {
                if (strpos($trampaIdActual, 'TEMP-') === 0) {
                    $planoId = $this->request->getPost('plano_id');
                    if ($planoId) {
                        $trampa = $trampaModel->where('plano_id', $planoId)
                                             ->where('id_trampa IS NULL OR id_trampa = ""')
                                             ->orderBy('id', 'DESC')
                                             ->first();
                    }
                } else {
                    // Solo como último recurso, buscar por id_trampa
                    $trampa = $trampaModel->where('id_trampa', $trampaIdActual)->first();
                }
            }
            
            if (!$trampa) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se encontró la trampa especificada'
                ]);
            }
            
            // Actualizar tanto el nombre como el id_trampa para mantener consistencia
            // El nombre se usa en la vista del plano, el id_trampa se usa en las estadísticas
            // NOTA: id_trampa puede tener duplicados (es como un nombre), solo el campo 'id' es único
            
            // Actualizar tanto el nombre como el id_trampa
            $actualizado = $trampaModel->update($trampa['id'], [
                'nombre' => $nuevoIdTrampa,
                'id_trampa' => $nuevoIdTrampa
            ]);
            
            if ($actualizado) {
                // Obtener la trampa actualizada con todos sus datos
                $trampaActualizada = $trampaModel->find($trampa['id']);
                
                // Debug temporal: Verificar que el tipo se mantiene
                log_message('info', 'Trampa actualizada - Tipo: ' . ($trampaActualizada['tipo'] ?? 'NULL') . ', Nombre: ' . $trampaActualizada['nombre']);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'ID de trampa actualizado correctamente',
                    'trampa' => [
                        'id' => $trampaActualizada['id'],
                        'id_trampa' => $trampaActualizada['id_trampa'], // Actualizado
                        'nombre' => $trampaActualizada['nombre'], // Actualizado
                        'tipo' => $trampaActualizada['tipo'], // Mantener tipo
                        'ubicacion' => $trampaActualizada['ubicacion'], // Mantener ubicación
                        'coordenada_x' => $trampaActualizada['coordenada_x'], // Mantener coordenadas
                        'coordenada_y' => $trampaActualizada['coordenada_y'],
                        'nombre_anterior' => $trampa['nombre'],
                        'nombre_nuevo' => $nuevoIdTrampa
                    ]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al actualizar el nombre en la base de datos'
                ]);
            }
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar el ID: ' . $e->getMessage()
            ]);
        }
    }

    // Método para guardar una incidencia en la base de datos
    public function guardar_incidencia()
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }
        
        // Obtener los datos del POST
        $trampaId = $this->request->getPost('trampa_id');
        $tipoPlaga = $this->request->getPost('tipo_plaga');
        $tipoIncidencia = $this->request->getPost('tipo_incidencia');
        $zona = $this->request->getPost('zona');
        // Contexto del plano/sede (en viewplano ahora se envía siempre)
        $planoIdPost = $this->request->getPost('plano_id');
        $sedeIdPost = $this->request->getPost('sede_id');
        
        // Agregar logs para debug
        log_message('info', 'trampa_id recibido: ' . $trampaId);
        log_message('info', 'tipo_plaga recibido: ' . $tipoPlaga);
        log_message('info', 'tipo_incidencia recibido: ' . $tipoIncidencia);

        // Validar datos básicos
        if (!$tipoPlaga) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Datos incompletos: falta tipo_plaga',
                'debug' => [
                    'trampa_id' => $trampaId,
                    'tipo_plaga' => $tipoPlaga,
                    'tipo_incidencia' => $tipoIncidencia
                ]
            ]);
        }
        
        // Para hallazgos, trampa_id puede estar vacío
        if ($tipoIncidencia !== 'Hallazgo' && !$trampaId) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Datos incompletos: falta trampa_id',
                'debug' => [
                    'trampa_id' => $trampaId,
                    'tipo_plaga' => $tipoPlaga
                ]
            ]);
        }
        
        // Obtener directamente los valores de los campos del formulario
        $tipoInsecto = $this->request->getPost('tipo_insecto');
        
        $cantidadOrganismos = $this->request->getPost('cantidad_organismos');
        $notas = $this->request->getPost('notas');
        $inspector = $this->request->getPost('inspector');
        
        // Registrar los datos recibidos para depuración
        log_message('info', 'Datos de incidencia recibidos: ' . json_encode([
            'id_trampa' => $trampaId,
            'tipo_plaga' => $tipoPlaga,
            'tipo_insecto' => $tipoInsecto,
            'cantidad_organismos' => $cantidadOrganismos,
            'tipo_incidencia' => $tipoIncidencia,
            'zona' => $zona,
            'notas' => $notas,
            'inspector' => $inspector
        ]));
        
        // Verificar que se haya proporcionado una fecha
        $fechaIncidencia = $this->request->getPost('fecha_incidencia');
        if (!$fechaIncidencia) {
            return $this->response->setJSON(['success' => false, 'message' => 'Debe proporcionar una fecha para la incidencia']);
        }
        
        try {
            $idTrampaReciente = null;
            $idSede = null;
            $idPlano = null;
            
            // Si es un hallazgo, no necesitamos buscar la trampa
            if ($tipoIncidencia === 'Hallazgo') {
                // Para hallazgos, obtener la sede desde el plano_id o sede_id enviado
                $planoId = $this->request->getPost('plano_id');
                $sedeIdEnviado = $this->request->getPost('sede_id');
                
                if ($planoId) {
                    $idPlano = $planoId;
                    $planoModel = new \App\Models\PlanoModel();
                    $plano = $planoModel->find($planoId);
                    if ($plano && isset($plano['sede_id'])) {
                        $idSede = $plano['sede_id'];
                    } else {
                        return $this->response->setJSON(['success' => false, 'message' => 'No se pudo obtener la sede del plano']);
                    }
                } elseif ($sedeIdEnviado) {
                    $idSede = $sedeIdEnviado;
                } else {
                    return $this->response->setJSON(['success' => false, 'message' => 'No se proporcionó plano_id ni sede_id para el hallazgo']);
                }
                
                // Agregar la zona a las notas si está disponible
                if ($zona && !empty($zona)) {
                    $notas = ($notas ? $notas . ' | ' : '') . 'Zona: ' . $zona;
                }
            } else {
                // Para capturas, buscar la trampa normalmente
                $trampaModel = new \App\Models\TrampaModel();
                
                // Intentar buscar la trampa por id_trampa primero
                $trampa = $trampaModel->where('id_trampa', $trampaId)->orderBy('id', 'DESC')->first();
                log_message('info', 'Búsqueda por id_trampa: ' . ($trampa ? 'Encontrada' : 'No encontrada'));
                
                // Si no se encuentra por id_trampa, intentar buscar por id
                if (!$trampa) {
                    $trampa = $trampaModel->find($trampaId);
                    log_message('info', 'Búsqueda por id: ' . ($trampa ? 'Encontrada' : 'No encontrada'));
                }
                
                // Si aún no se encuentra, intentar buscar por id numérico
                if (!$trampa && is_numeric($trampaId)) {
                    $trampa = $trampaModel->find((int)$trampaId);
                    log_message('info', 'Búsqueda por id numérico: ' . ($trampa ? 'Encontrada' : 'No encontrada'));
                }
                
                if (!$trampa) {
                    log_message('error', 'No se encontró la trampa con ID: ' . $trampaId);
                    return $this->response->setJSON(['success' => false, 'message' => 'No se encontró la trampa especificada (ID: ' . $trampaId . ')']);
                }
                
                // Usar el ID de la trampa encontrada
                $idTrampaReciente = $trampa['id'];
                // Obtener el ID de la sede asociada a la trampa
                $idSede = $trampa['sede_id'] ?? null;
                // Obtener el plano_id de la trampa
                $idPlano = $trampa['plano_id'] ?? null;
                
                // Fallback: si por datos viejos la trampa no tiene plano_id/sede_id, usar lo enviado desde la vista del plano
                if (empty($idPlano) && !empty($planoIdPost)) {
                    $idPlano = $planoIdPost;
                }
                if (empty($idSede) && !empty($sedeIdPost)) {
                    $idSede = $sedeIdPost;
                }
                log_message('info', 'ID de trampa encontrado: ' . $idTrampaReciente . ', ID de sede: ' . $idSede . ', ID de plano: ' . $idPlano);
            }
            
            // Validación final: plano_id debe existir para asociar la incidencia al plano actual
            if (empty($idPlano)) {
                log_message('error', 'No se pudo determinar plano_id para la incidencia. trampa_id=' . ($trampaId ?? 'NULL') . ', tipo_incidencia=' . ($tipoIncidencia ?? 'NULL') . ', plano_id_post=' . ($planoIdPost ?? 'NULL'));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se pudo determinar el plano para esta incidencia. Recargue el plano e intente nuevamente.'
                ]);
            }
            
            // Cargar el modelo de incidencias
            $incidenciaModel = new \App\Models\IncidenciaModel();
            
            // Formatear la fecha de incidencia para MySQL (YYYY-MM-DD HH:MM:SS)
            $fechaFormateada = date('Y-m-d H:i:s', strtotime($fechaIncidencia));
            
            // Verificar que los valores de tipo_insecto y tipo_incidencia sean correctos
            log_message('info', 'Valores antes de guardar: tipo_insecto=' . $tipoInsecto . ', tipo_incidencia=' . $tipoIncidencia);
            
            // Preparar los datos para guardar - Asegurarse de que los campos estén correctamente asignados
            $data = [
                'sede_id' => $idSede, // Agregamos el ID de la sede
                'plano_id' => $idPlano, // Agregamos el ID del plano (para hallazgos y capturas)
                'fecha' => $fechaFormateada, // Usamos la fecha proporcionada por el usuario
                'tipo_plaga' => $tipoPlaga,
                'tipo_insecto' => $tipoInsecto, // Asegurarse de que este valor sea correcto
                'cantidad_organismos' => $cantidadOrganismos,
                'tipo_incidencia' => $tipoIncidencia, // Asegurarse de que este valor sea correcto
                'notas' => $notas,
                'inspector' => $inspector ?? 'Sistema'
            ];
            
            // Para hallazgos, id_trampa debe ser null explícitamente
            // Para capturas y consumo, incluir el id_trampa
            if ($tipoIncidencia === 'Hallazgo') {
                $data['id_trampa'] = null; // Explícitamente null para hallazgos
            } else {
                $data['id_trampa'] = $idTrampaReciente; // ID de la trampa para capturas y consumo
            }
            
            // Log de los datos antes de insertar
            log_message('info', 'Datos a insertar: ' . json_encode($data));
            
            // Guardar la incidencia
            $incidenciaId = $incidenciaModel->insert($data);
            
            // Verificar si la inserción fue exitosa
            if ($incidenciaId === false) {
                // Obtener los errores del modelo
                $errores = $incidenciaModel->errors();
                log_message('error', 'Error al insertar incidencia: ' . json_encode($errores));
                log_message('error', 'Datos que fallaron: ' . json_encode($data));
                
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Error al guardar la incidencia en la base de datos',
                    'errors' => $errores,
                    'debug' => [
                        'tipo_incidencia' => $tipoIncidencia,
                        'id_trampa' => $data['id_trampa'],
                        'sede_id' => $idSede
                    ]
                ]);
            }
            
            log_message('info', 'Incidencia guardada con ID: ' . $incidenciaId);
            
            // Registrar en auditoría
            log_create('incidencias', $incidenciaId, $data, "Se creó una incidencia: {$tipoPlaga} ({$tipoInsecto}) - Cantidad: {$cantidadOrganismos} - Inspector: {$inspector}");
            
            // Verificar que se haya guardado correctamente
            $incidenciaGuardada = $incidenciaModel->find($incidenciaId);
            if (!$incidenciaGuardada) {
                log_message('error', 'La incidencia se insertó pero no se pudo recuperar. ID: ' . $incidenciaId);
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'La incidencia se guardó pero no se pudo verificar'
                ]);
            }
            
            log_message('info', 'Incidencia guardada y verificada: ' . json_encode($incidenciaGuardada));
            
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Incidencia registrada correctamente',
                'incidencia_id' => $incidenciaId
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al guardar incidencia: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error al guardar la incidencia: ' . $e->getMessage()
            ]);
        }
    }

    // Método para guardar múltiples incidencias en lote (batch)
    public function guardar_incidencias_batch()
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }

        // Leer el cuerpo JSON
        $body = $this->request->getJSON(true);
        $incidencias = $body['incidencias'] ?? [];

        // Validar que el array no esté vacío
        if (empty($incidencias) || !is_array($incidencias)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El array de incidencias está vacío o no es válido'
            ]);
        }

        // Límite de seguridad: máximo 500 incidencias por lote
        if (count($incidencias) > 500) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Se superó el límite de seguridad: máximo 500 incidencias por lote'
            ]);
        }

        $db = \Config\Database::connect();
        $incidenciaModel = new \App\Models\IncidenciaModel();
        $trampaModel = new \App\Models\TrampaModel();
        $planoModel = new \App\Models\PlanoModel();

        $guardadas = 0;
        $detalleErrores = [];

        $db->transStart();

        foreach ($incidencias as $index => $inc) {
            try {
                $trampaId        = $inc['trampa_id'] ?? null;
                $tipoPlaga       = $inc['tipo_plaga'] ?? null;
                $tipoIncidencia  = $inc['tipo_incidencia'] ?? null;
                $tipoInsecto     = $inc['tipo_insecto'] ?? null;
                $cantidadOrganismos = $inc['cantidad_organismos'] ?? null;
                $notas           = $inc['notas'] ?? null;
                $inspector       = $inc['inspector'] ?? 'Sistema';
                $fechaIncidencia = $inc['fecha_incidencia'] ?? null;
                $zona            = $inc['zona'] ?? null;
                $planoIdPost     = $inc['plano_id'] ?? null;
                $sedeIdPost      = $inc['sede_id'] ?? null;

                // Validar tipo_plaga requerido
                if (!$tipoPlaga) {
                    $detalleErrores[] = [
                        'index'   => $index,
                        'message' => 'Datos incompletos: falta tipo_plaga'
                    ];
                    continue;
                }

                // Para no-Hallazgo, trampa_id es requerido
                if ($tipoIncidencia !== 'Hallazgo' && !$trampaId) {
                    $detalleErrores[] = [
                        'index'   => $index,
                        'message' => 'Datos incompletos: falta trampa_id'
                    ];
                    continue;
                }

                $idTrampaReciente = null;
                $idSede = null;
                $idPlano = null;

                if ($tipoIncidencia === 'Hallazgo') {
                    // Para hallazgos, obtener la sede desde el plano_id o sede_id enviado
                    if ($planoIdPost) {
                        $idPlano = $planoIdPost;
                        $plano = $planoModel->find($planoIdPost);
                        if ($plano && isset($plano['sede_id'])) {
                            $idSede = $plano['sede_id'];
                        } else {
                            $detalleErrores[] = [
                                'index'   => $index,
                                'message' => 'No se pudo obtener la sede del plano'
                            ];
                            continue;
                        }
                    } elseif ($sedeIdPost) {
                        $idSede = $sedeIdPost;
                    } else {
                        $detalleErrores[] = [
                            'index'   => $index,
                            'message' => 'No se proporcionó plano_id ni sede_id para el hallazgo'
                        ];
                        continue;
                    }

                    // Agregar la zona a las notas si está disponible
                    if ($zona && !empty($zona)) {
                        $notas = ($notas ? $notas . ' | ' : '') . 'Zona: ' . $zona;
                    }
                } else {
                    // Para capturas, buscar la trampa normalmente
                    // Intentar buscar la trampa por id_trampa primero
                    $trampa = $trampaModel->where('id_trampa', $trampaId)->orderBy('id', 'DESC')->first();

                    // Si no se encuentra por id_trampa, intentar buscar por id
                    if (!$trampa) {
                        $trampa = $trampaModel->find($trampaId);
                    }

                    // Si aún no se encuentra, intentar buscar por id numérico
                    if (!$trampa && is_numeric($trampaId)) {
                        $trampa = $trampaModel->find((int)$trampaId);
                    }

                    if (!$trampa) {
                        $detalleErrores[] = [
                            'index'   => $index,
                            'message' => 'No se encontró la trampa especificada (ID: ' . $trampaId . ')'
                        ];
                        continue;
                    }

                    // Usar el ID de la trampa encontrada
                    $idTrampaReciente = $trampa['id'];
                    $idSede = $trampa['sede_id'] ?? null;
                    $idPlano = $trampa['plano_id'] ?? null;

                    // Fallback: si la trampa no tiene plano_id/sede_id, usar lo enviado
                    if (empty($idPlano) && !empty($planoIdPost)) {
                        $idPlano = $planoIdPost;
                    }
                    if (empty($idSede) && !empty($sedeIdPost)) {
                        $idSede = $sedeIdPost;
                    }
                }

                // Validación final: plano_id debe existir
                if (empty($idPlano)) {
                    $detalleErrores[] = [
                        'index'   => $index,
                        'message' => 'No se pudo determinar el plano para esta incidencia. Recargue el plano e intente nuevamente.'
                    ];
                    continue;
                }

                // Formatear la fecha de incidencia para MySQL (YYYY-MM-DD HH:MM:SS)
                $fechaFormateada = date('Y-m-d H:i:s', strtotime($fechaIncidencia));

                // Preparar los datos para guardar
                $data = [
                    'sede_id'             => $idSede,
                    'plano_id'            => $idPlano,
                    'fecha'               => $fechaFormateada,
                    'tipo_plaga'          => $tipoPlaga,
                    'tipo_insecto'        => $tipoInsecto,
                    'cantidad_organismos' => $cantidadOrganismos,
                    'tipo_incidencia'     => $tipoIncidencia,
                    'notas'               => $notas,
                    'inspector'           => $inspector
                ];

                // Para hallazgos, id_trampa debe ser null explícitamente
                if ($tipoIncidencia === 'Hallazgo') {
                    $data['id_trampa'] = null;
                } else {
                    $data['id_trampa'] = $idTrampaReciente;
                }

                // Insertar la incidencia
                $incidenciaId = $incidenciaModel->insert($data);

                if ($incidenciaId === false) {
                    $errores = $incidenciaModel->errors();
                    $detalleErrores[] = [
                        'index'   => $index,
                        'message' => 'Error al guardar la incidencia en la base de datos: ' . json_encode($errores)
                    ];
                    continue;
                }

                // Registrar en auditoría
                log_create('incidencias', $incidenciaId, $data, "Se creó una incidencia (batch): {$tipoPlaga} ({$tipoInsecto}) - Cantidad: {$cantidadOrganismos} - Inspector: {$inspector}");

                $guardadas++;

            } catch (\Exception $e) {
                $detalleErrores[] = [
                    'index'   => $index,
                    'message' => 'Excepción al procesar incidencia: ' . $e->getMessage()
                ];
            }
        }

        $db->transComplete();

        // Si la transacción falló, reportar todo como error
        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'success'        => false,
                'message'        => 'La transacción falló. No se guardó ninguna incidencia.',
                'total'          => count($incidencias),
                'guardadas'      => 0,
                'errores'        => count($incidencias),
                'detalle_errores' => $detalleErrores
            ]);
        }

        return $this->response->setJSON([
            'success'        => true,
            'total'          => count($incidencias),
            'guardadas'      => $guardadas,
            'errores'        => count($detalleErrores),
            'detalle_errores' => $detalleErrores
        ]);
    }

    public function actualizar_incidencia()
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }
        
        // Obtener los datos del POST
        $incidenciaId = $this->request->getPost('incidencia_id');
        $tipoPlaga = $this->request->getPost('tipo_plaga_editar');
        
        if (!$incidenciaId || !$tipoPlaga) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Datos incompletos'
            ]);
        }
        
        // Obtener los valores de los campos del formulario
        $tipoInsecto = $this->request->getPost('tipo_insecto_editar');
        $tipoIncidencia = $this->request->getPost('tipo_incidencia_editar');
        $cantidadOrganismos = $this->request->getPost('cantidad_organismos_editar') ?: null;
        $notas = $this->request->getPost('notas_editar');
        $inspector = $this->request->getPost('inspector_editar');
        
        // Verificar que se haya proporcionado una fecha
        $fechaIncidencia = $this->request->getPost('fecha_incidencia_editar');
        if (!$fechaIncidencia) {
            return $this->response->setJSON(['success' => false, 'message' => 'Debe proporcionar una fecha para la incidencia']);
        }
        
        try {
            // Cargar el modelo de incidencias
            $incidenciaModel = new \App\Models\IncidenciaModel();
            
            // Verificar que la incidencia existe
            $incidencia = $incidenciaModel->find($incidenciaId);
            if (!$incidencia) {
                return $this->response->setJSON(['success' => false, 'message' => 'No se encontró la incidencia especificada']);
            }
            
            // Guardar datos anteriores para el log
            $datosAnteriores = $incidencia;
            
            // Formatear la fecha de incidencia para MySQL (YYYY-MM-DD HH:MM:SS)
            $fechaFormateada = date('Y-m-d H:i:s', strtotime($fechaIncidencia));
            
            // Preparar los datos para actualizar
            $data = [
                'fecha' => $fechaFormateada,
                'tipo_plaga' => $tipoPlaga,
                'tipo_insecto' => $tipoInsecto,
                'cantidad_organismos' => $cantidadOrganismos,
                'tipo_incidencia' => $tipoIncidencia,
                'notas' => $notas,
                'inspector' => $inspector ?? 'Sistema'
            ];
            
            // Actualizar la incidencia
            $incidenciaModel->update($incidenciaId, $data);
            log_message('info', 'Incidencia actualizada con ID: ' . $incidenciaId);
            
            // Registrar en auditoría
            log_update('incidencias', $incidenciaId, $datosAnteriores, $data, "Se actualizó la incidencia ID: {$incidenciaId} - {$tipoPlaga} ({$tipoInsecto})");
            
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Incidencia actualizada correctamente',
                'incidencia_id' => $incidenciaId
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar incidencia: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Error al actualizar la incidencia: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene todas las zonas únicas para un plano específico
     * 
     * @param int|null $plano_id ID del plano
     * @return \CodeIgniter\HTTP\Response
     */
    public function obtener_zonas($plano_id = null)
    {
        // Verificar si es una solicitud AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No es una solicitud AJAX válida'
            ]);
        }

        // Verificar que se proporcionó un ID de plano
        if (!$plano_id) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID de plano no proporcionado'
            ]);
        }

        // Cargar el modelo de trampas (asumiendo que tienes un modelo llamado TrampaModel)
        $db = \Config\Database::connect();
        
        // Consultar zonas únicas desde la tabla 'trampas'
        $query = $db->table('trampas')
                    ->select('DISTINCT(ubicacion) as zona')
                    ->where('plano_id', $plano_id)
                    ->where('ubicacion IS NOT NULL')
                    ->where('ubicacion !=', '')
                    ->get();
        
        $zonas = [];
        foreach ($query->getResult() as $row) {
            $zonas[] = $row->zona;
        }

        return $this->response->setJSON([
            'success' => true,
            'zonas' => $zonas
        ]);
    }

    /**
     * Muestra la imagen del plano con las incidencias marcadas
     * 
     * @param int $id ID del plano
     * @return \CodeIgniter\HTTP\Response
     */
    public function verImagen($id = null)
    {
        if (!$id) {
            return redirect()->to('/blueprints')->with('error', 'Plano no especificado');
        }

        // Cargar modelos
        $planoModel = new PlanoModel();
        $sedeModel = new SedeModel();
        $trampaModel = new \App\Models\TrampaModel();
        $incidenciaModel = new \App\Models\IncidenciaModel();

        // Obtener información del plano
        $plano = $planoModel->find($id);
        if (!$plano) {
            return redirect()->to('/blueprints')->with('error', 'Plano no encontrado');
        }

        // Obtener la imagen de previsualización
        $imagenUrl = $this->getPreviewImage($plano);
        
        // Obtener información de la sede asociada
        $sede = $sedeModel->find($plano['sede_id']);
        
        // Obtener las trampas asociadas al plano
        $trampas = $trampaModel->where('plano_id', $id)->findAll();
        
        // Obtener las incidencias asociadas a las trampas de este plano
        // IMPORTANTE: Validar que la sede_id de la incidencia coincida con la sede_id del plano
        // para evitar mostrar incidencias de planos anteriores que reutilizaron el mismo ID
        $incidencias = [];
        $db = \Config\Database::connect();
        
        if (!empty($trampas)) {
            $trampaIds = array_column($trampas, 'id');
            
            // Consulta con validación de sede_id (mismo criterio que viewplano)
            $query = $db->query("
                SELECT i.*, t.id as trampa_db_id, t.tipo as trampa_tipo, 
                       t.ubicacion as trampa_ubicacion, t.nombre as trampa_nombre,
                       t.id_trampa as trampa_id_trampa,
                       t.coordenada_x as trampa_coordenada_x,
                       t.coordenada_y as trampa_coordenada_y
                FROM incidencias i
                INNER JOIN trampas t ON i.id_trampa = t.id
                WHERE i.id_trampa IN (" . implode(',', $trampaIds) . ")
                AND t.sede_id = ?
                AND i.sede_id = t.sede_id
                ORDER BY i.fecha DESC
            ", [$plano['sede_id']]);
            
            $incidenciasRaw = $query->getResultArray();
            
            // Asociar cada incidencia con su trampa correspondiente
            foreach ($incidenciasRaw as $incidencia) {
                $incidencia['trampa'] = [
                    'id' => $incidencia['trampa_db_id'],
                    'tipo' => $incidencia['trampa_tipo'],
                    'ubicacion' => $incidencia['trampa_ubicacion'],
                    'nombre' => $incidencia['trampa_nombre'],
                    'id_trampa' => $incidencia['trampa_id_trampa'],
                    'coordenada_x' => $incidencia['trampa_coordenada_x'],
                    'coordenada_y' => $incidencia['trampa_coordenada_y']
                ];
                $incidencias[] = $incidencia;
            }
        }
        
        // Obtener el estado del plano (JSON)
        $estadoPlano = null;
        if (!empty($plano['archivo'])) {
            try {
                $estadoPlano = json_decode($plano['archivo'], true);
            } catch (\Exception $e) {
                log_message('error', 'Error al decodificar el archivo del plano: ' . $e->getMessage());
            }
        }

        $data = [
            'plano' => $plano,
            'sede' => $sede,
            'imagen_url' => $imagenUrl,
            'trampas' => $trampas,
            'incidencias' => $incidencias,
            'estadoPlano' => $estadoPlano
        ];

        // Obtener lista de plagas únicas para filtros
        // IMPORTANTE: Aplicar la misma validación de sede_id para consistencia
        $queryPlagas = $db->query("
            SELECT DISTINCT(i.tipo_plaga) as plaga
            FROM incidencias i
            INNER JOIN trampas t ON i.id_trampa = t.id
            WHERE t.plano_id = ?
            AND t.sede_id = ?
            AND i.sede_id = t.sede_id
            AND i.tipo_plaga IS NOT NULL
            AND i.tipo_plaga != ''
            ORDER BY i.tipo_plaga ASC
        ", [$id, $plano['sede_id']]);
        
        $data['listaPlagas'] = $queryPlagas->getResultArray();

        return view('blueprints/ver_imagen', $data);
    }

    /**
     * Muestra la página para registrar el estado de funcionamiento de las trampas
     */
    public function estadoTrampas($id = null)
    {
        if (!$id) {
            return redirect()->to('/blueprints')->with('error', 'Plano no especificado');
        }

        // Cargar modelos
        $planoModel = new PlanoModel();
        $sedeModel = new SedeModel();
        $trampaModel = new \App\Models\TrampaModel();
        $estadoTrampaModel = new \App\Models\EstadoTrampaModel();

        // Obtener información del plano
        $plano = $planoModel->find($id);
        if (!$plano) {
            return redirect()->to('/blueprints')->with('error', 'Plano no encontrado');
        }

        // Obtener información de la sede asociada
        $sede = $sedeModel->find($plano['sede_id']);
        
        // Obtener todas las trampas del plano con sus estados más recientes
        $trampas = $estadoTrampaModel->obtenerEstadosCompletosPorPlano($id);

        $data = [
            'plano' => $plano,
            'sede' => $sede,
            'trampas' => $trampas,
            'title' => 'Registro de Estado de Trampas'
        ];

        return view('blueprints/estado_trampas', $data);
    }

    /**
     * Guarda los estados de funcionamiento de las trampas
     */
    public function guardarEstadosTrampas($id = null)
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }

        if (!$id) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID de plano no especificado']);
        }

        // Obtener los datos JSON
        $jsonData = $this->request->getJSON(true);
        
        if (!$jsonData || !isset($jsonData['estados']) || !is_array($jsonData['estados'])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Datos incompletos']);
        }

        $planoId = $jsonData['plano_id'] ?? $id;
        $sedeId = $jsonData['sede_id'] ?? null;
        $estados = $jsonData['estados'];

        try {
            // Cargar modelos
            $planoModel = new PlanoModel();
            $estadoTrampaModel = new \App\Models\EstadoTrampaModel();
            
            // Verificar que el plano existe
            $plano = $planoModel->find($planoId);
            if (!$plano) {
                return $this->response->setJSON(['success' => false, 'message' => 'Plano no encontrado']);
            }

            // Si no se proporcionó sede_id, obtenerlo del plano
            if (!$sedeId) {
                $sedeId = $plano['sede_id'];
            }

            // Obtener información del usuario actual (si está disponible)
            $usuarioRegistro = session()->get('usuario') ?? session()->get('username') ?? 'Sistema';

            $estadosGuardados = 0;
            $errores = [];

            // Guardar cada estado
            foreach ($estados as $estadoData) {
                if (!isset($estadoData['trampa_id']) || !isset($estadoData['estado'])) {
                    $errores[] = 'Datos incompletos para una trampa';
                    continue;
                }

                // Validar que el estado sea válido
                $estadosValidos = ['funciona', 'en_reparacion', 'no_funciona', 'sin_registro'];
                if (!in_array($estadoData['estado'], $estadosValidos)) {
                    $errores[] = 'Estado inválido para la trampa ID: ' . $estadoData['trampa_id'];
                    continue;
                }

                // Preparar datos para insertar
                $data = [
                    'trampa_id' => $estadoData['trampa_id'],
                    'plano_id' => $planoId,
                    'sede_id' => $sedeId,
                    'estado' => $estadoData['estado'],
                    'observaciones' => $estadoData['observaciones'] ?? '',
                    'usuario_registro' => $usuarioRegistro
                ];

                // Insertar el nuevo estado (siempre se crea un nuevo registro para mantener historial)
                $insertado = $estadoTrampaModel->insert($data);
                
                if ($insertado) {
                    $estadosGuardados++;
                } else {
                    $errores[] = 'Error al guardar estado para trampa ID: ' . $estadoData['trampa_id'];
                }
            }

            if (count($errores) > 0 && $estadosGuardados === 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se pudo guardar ningún estado. Errores: ' . implode(', ', $errores)
                ]);
            }

            $mensaje = "Se guardaron {$estadosGuardados} estado(s) correctamente.";
            if (count($errores) > 0) {
                $mensaje .= " Algunos errores: " . implode(', ', array_slice($errores, 0, 3));
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $mensaje,
                'estados_guardados' => $estadosGuardados,
                'errores' => $errores
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error al guardar estados de trampas: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al guardar los estados: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Deshabilita un plano cambiando su habilitado a 0 (soft delete)
     * No elimina físicamente el registro para mantener la información sensible
     */
    public function deshabilitar($id = null)
    {
        // Verificar si se proporcionó un ID
        if ($id === null) {
            return redirect()->back()->with('error', 'ID de plano no proporcionado.');
        }

        try {
            // Cargar el modelo
            $planoModel = new PlanoModel();

            // Verificar que el plano existe
            $plano = $planoModel->find($id);
            if (!$plano) {
                return redirect()->back()->with('error', 'Plano no encontrado.');
            }

            // Guardar estado anterior para el log
            $estadoAnterior = $plano['habilitado'] ?? 'N/A';

            // === CASCADE DELETE: Eliminar trampas y datos relacionados de ESTE plano específico ===
            $trampaModel = new \App\Models\TrampaModel();
            $incidenciaModel = new \App\Models\IncidenciaModel();
            $estadoTrampaModel = new \App\Models\EstadoTrampaModel();
            $movimientoModel = new \App\Models\MovimientoTrampaModel();

            // 1. Obtener SOLO las trampas de ESTE plano (filtro estricto por plano_id)
            $trampasDelPlano = $trampaModel->where('plano_id', $id)->findAll();

            if (!empty($trampasDelPlano)) {
                foreach ($trampasDelPlano as $trampa) {
                    // 2a. Eliminar incidencias por trampa.id (columna id_trampa en incidencias guarda el PK numérico)
                    $incidenciaModel->where('id_trampa', $trampa['id'])->delete();

                    // 2b. Eliminar estados por trampa.id (columna trampa_id en estado_trampas guarda el PK numérico)
                    $estadoTrampaModel->where('trampa_id', $trampa['id'])->delete();

                    // 2c. Eliminar historial de movimientos por trampa.id_trampa (columna id_trampa en historial_movimientos guarda el STRING label)
                    // Y TAMBIÉN filtrar por plano_id para no borrar movimientos de trampas con el mismo label en otros planos
                    $movimientoModel->where('id_trampa', $trampa['id_trampa'])->where('plano_id', $id)->delete();
                }

                // 3. Eliminar las trampas de ESTE plano
                $trampaModel->where('plano_id', $id)->delete();

                log_message('info', 'Cascade delete: eliminadas ' . count($trampasDelPlano) . ' trampas y datos relacionados del plano ID: ' . $id);
            }
            // === FIN CASCADE DELETE ===

            // Cambiar el habilitado a 0 (deshabilitado)
            // Usar skipValidation para omitir las reglas de validación que esperan strings
            $resultado = $planoModel->skipValidation(true)->update($id, ['habilitado' => 0]);
            
            // Si el update falla, usar consulta directa como fallback
            if (!$resultado) {
                $db = \Config\Database::connect();
                $db->table('planos')->where('id', $id)->update(['habilitado' => 0]);
                
                // Verificar que se actualizó
                $planoActualizado = $planoModel->find($id);
                if (!$planoActualizado || $planoActualizado['habilitado'] != 0) {
                    log_message('error', 'No se pudo actualizar el habilitado del plano ID: ' . $id);
                    throw new \Exception('No se pudo actualizar el habilitado del plano');
                }
            }
            
            log_message('info', 'Plano deshabilitado correctamente. ID: ' . $id . ', Habilitado anterior: ' . $estadoAnterior);
            
            // Registrar en auditoría
            log_status_change('planos', $id, (string)$estadoAnterior, '0', "Se deshabilitó el plano: {$plano['nombre']}");

            // Redirigir según desde dónde se llamó
            $referer = $this->request->getHeaderLine('Referer');
            if (strpos($referer, 'blueprints/view') !== false) {
                // Si viene de la vista de una sede, redirigir a esa vista
                preg_match('/blueprints\/view\/(\d+)/', $referer, $matches);
                if (!empty($matches[1])) {
                    return redirect()->to('/blueprints/view/' . $matches[1])->with('message', 'Plano deshabilitado correctamente. Ya no aparecerá en las vistas.');
                }
            }

            return redirect()->to('/blueprints')->with('message', 'Plano deshabilitado correctamente. Ya no aparecerá en las vistas.');
        } catch (\Exception $e) {
            log_message('error', 'Error al deshabilitar plano ID ' . $id . ': ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error al deshabilitar el plano: ' . $e->getMessage());
        }
    }

    // Método para eliminar una trampa y sus registros relacionados
    public function eliminar_trampa()
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no valida']);
        }

        // Obtener el db_id del POST (PRIMARY KEY int de la tabla trampas)
        $dbId = $this->request->getPost('db_id');

        // Validar que db_id existe y es numérico
        if (!$dbId || !is_numeric($dbId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID de trampa no proporcionado']);
        }

        try {
            // Cargar los modelos necesarios
            $trampaModel = new \App\Models\TrampaModel();
            $incidenciaModel = new IncidenciaModel();
            $estadoTrampaModel = new EstadoTrampaModel();
            $movimientoModel = new MovimientoTrampaModel();

            // Buscar la trampa
            $trampa = $trampaModel->find($dbId);
            if (!$trampa) {
                return $this->response->setJSON(['success' => false, 'message' => 'Trampa no encontrada']);
            }

            // Guardar los datos completos de la trampa ANTES de borrar
            $trampaData = $trampa;

            // Borrar registros relacionados en orden:
            // a. DELETE FROM incidencias WHERE id_trampa = $dbId
            $incidenciaModel->where('id_trampa', $dbId)->delete();

            // b. DELETE FROM estado_trampas WHERE trampa_id = $dbId
            $estadoTrampaModel->where('trampa_id', $dbId)->delete();

            // c. DELETE FROM historial_movimientos WHERE id_trampa = STRING label AND plano_id
            // historial_movimientos.id_trampa guarda el STRING label (ej: 'LV-01'), no el PK numérico
            // Filtrar también por plano_id para no borrar movimientos de trampas con el mismo label en otros planos
            if ($trampaData) {
                $movimientoModel->where('id_trampa', $trampaData['id_trampa'])
                                 ->where('plano_id', $trampaData['plano_id'])
                                 ->delete();
            }

            // Borrar la trampa
            $trampaModel->delete($dbId);

            // Registrar en auditoría
            $idTrampaStr = $trampaData['id_trampa'] ?? $dbId;
            $nombreTrampa = $trampaData['nombre'] ?? $idTrampaStr;
            $planoId = $trampaData['plano_id'] ?? '';
            log_delete('trampas', (int)$dbId, $trampaData, "Se elimino la trampa ID: {$idTrampaStr} - {$nombreTrampa} del plano {$planoId}");

            return $this->response->setJSON(['success' => true, 'message' => 'Trampa eliminada correctamente']);

        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Error al eliminar la trampa: ' . $e->getMessage()]);
        }
    }

    /**
     * Eliminar TODAS las trampas de un plano específico y sus datos relacionados.
     * Usado por 'Limpiar Todo' en el frontend.
     * IMPORTANTE: Solo elimina trampas WHERE plano_id = $planoId recibido.
     */
    public function eliminar_trampas_plano()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }

        $planoId = $this->request->getPost('plano_id');

        if (!$planoId) {
            return $this->response->setJSON(['success' => false, 'message' => 'plano_id es requerido']);
        }

        // Verificar que el plano existe
        $planoModel = new \App\Models\PlanoModel();
        $plano = $planoModel->find($planoId);
        if (!$plano) {
            return $this->response->setJSON(['success' => false, 'message' => 'Plano no encontrado']);
        }

        try {
            $trampaModel = new \App\Models\TrampaModel();
            $incidenciaModel = new \App\Models\IncidenciaModel();
            $estadoTrampaModel = new \App\Models\EstadoTrampaModel();
            $movimientoModel = new \App\Models\MovimientoTrampaModel();

            // Obtener SOLO las trampas de ESTE plano
            $trampasDelPlano = $trampaModel->where('plano_id', $planoId)->findAll();
            $cantidadEliminadas = count($trampasDelPlano);

            if (!empty($trampasDelPlano)) {
                foreach ($trampasDelPlano as $trampa) {
                    // Eliminar incidencias (id_trampa guarda PK numérico de trampas)
                    $incidenciaModel->where('id_trampa', $trampa['id'])->delete();

                    // Eliminar estados (trampa_id guarda PK numérico de trampas)
                    $estadoTrampaModel->where('trampa_id', $trampa['id'])->delete();

                    // Eliminar movimientos (id_trampa guarda STRING label, filtrar también por plano_id)
                    $movimientoModel->where('id_trampa', $trampa['id_trampa'])->where('plano_id', $planoId)->delete();
                }

                // Eliminar las trampas de ESTE plano
                $trampaModel->where('plano_id', $planoId)->delete();

                log_message('info', 'Limpiar Todo: eliminadas ' . $cantidadEliminadas . ' trampas del plano ID: ' . $planoId);
            }

            // Limpiar el array de trampas dentro del campo JSON `archivo` del plano
            // para que al hacer refresh no reaparezcan desde el fallback del frontend
            if (!empty($plano['archivo'])) {
                $archivoData = json_decode($plano['archivo'], true);
                if (is_array($archivoData)) {
                    $archivoData['trampas'] = [];
                    $planoModel->update($planoId, ['archivo' => json_encode($archivoData)]);
                    log_message('info', 'Limpiar Todo: campo archivo.trampas vaciado para plano ID: ' . $planoId);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Se eliminaron ' . $cantidadEliminadas . ' trampas del plano'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en eliminar_trampas_plano: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar trampas: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar todas las incidencias tipo Hallazgo de un plano específico.
     * Hallazgos = incidencias con id_trampa IS NULL y plano_id = $planoId.
     * IMPORTANTE: Solo elimina registros de ESTE plano.
     */
    public function eliminar_hallazgos_plano()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }

        $planoId = $this->request->getPost('plano_id');

        if (!$planoId) {
            return $this->response->setJSON(['success' => false, 'message' => 'plano_id es requerido']);
        }

        $planoModel = new \App\Models\PlanoModel();
        $plano = $planoModel->find($planoId);
        if (!$plano) {
            return $this->response->setJSON(['success' => false, 'message' => 'Plano no encontrado']);
        }

        try {
            $incidenciaModel = new \App\Models\IncidenciaModel();

            $cantidad = $incidenciaModel
                ->where('plano_id', $planoId)
                ->where('id_trampa IS NULL', null, false)
                ->countAllResults();

            $incidenciaModel
                ->where('plano_id', $planoId)
                ->where('id_trampa IS NULL', null, false)
                ->delete();

            log_message('info', 'Eliminar hallazgos: eliminados ' . $cantidad . ' hallazgos del plano ID: ' . $planoId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Se eliminaron ' . $cantidad . ' hallazgos del plano'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en eliminar_hallazgos_plano: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar hallazgos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Eliminar todas las evidencias fotográficas de un plano específico,
     * incluyendo los archivos físicos del filesystem.
     * IMPORTANTE: Solo elimina registros con id_plano = $planoId.
     */
    public function eliminar_evidencias_plano()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Solicitud no válida']);
        }

        $planoId = $this->request->getPost('plano_id');

        if (!$planoId) {
            return $this->response->setJSON(['success' => false, 'message' => 'plano_id es requerido']);
        }

        $planoModel = new \App\Models\PlanoModel();
        $plano = $planoModel->find($planoId);
        if (!$plano) {
            return $this->response->setJSON(['success' => false, 'message' => 'Plano no encontrado']);
        }

        try {
            $evidenciaModel = new \App\Models\EvidenciaModel();

            $evidencias = $evidenciaModel->where('id_plano', $planoId)->findAll();

            foreach ($evidencias as $evidencia) {
                // Eliminar imagen_evidencia del filesystem
                if (!empty($evidencia['imagen_evidencia'])) {
                    $path = $evidencia['imagen_evidencia'];
                    // Manejar rutas relativas y URLs completas
                    if (filter_var($path, FILTER_VALIDATE_URL)) {
                        // Extraer la parte relativa de la URL
                        $parsed = parse_url($path, PHP_URL_PATH);
                        $path = ltrim($parsed, '/');
                    }
                    $fullPath = FCPATH . $path;
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }

                // Eliminar imagen_resuelta del filesystem
                if (!empty($evidencia['imagen_resuelta'])) {
                    $path = $evidencia['imagen_resuelta'];
                    if (filter_var($path, FILTER_VALIDATE_URL)) {
                        $parsed = parse_url($path, PHP_URL_PATH);
                        $path = ltrim($parsed, '/');
                    }
                    $fullPath = FCPATH . $path;
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }

            $cantidad = count($evidencias);
            $evidenciaModel->where('id_plano', $planoId)->delete();

            log_message('info', 'Eliminar evidencias: eliminadas ' . $cantidad . ' evidencias del plano ID: ' . $planoId);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Se eliminaron ' . $cantidad . ' evidencias del plano'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en eliminar_evidencias_plano: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al eliminar evidencias: ' . $e->getMessage()
            ]);
        }
    }

}