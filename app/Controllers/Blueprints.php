<?php

namespace App\Controllers;
use App\Models\SedeModel;
use App\Models\PlanoModel;
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
        
        // Obtener todos los planos
        $planos = $planoModel->findAll();
        
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

        // Obtener planos de la sede
        $planos = $planoModel->where('sede_id', $id)->findAll();

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
                'fecha_creacion' => Time::now('America/Mexico_City')->format('Y-m-d H:i:s')
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
        // IMPORTANTE: Solo mostrar incidencias donde la trampa pertenece a la misma sede del plano
        // y donde la incidencia y la trampa tienen el mismo sede_id (mismo criterio que el dashboard)
        $incidenciaModel = new \App\Models\IncidenciaModel();
        $incidencias = [];
        
        if (!empty($trampas)) {
            $trampaIds = array_column($trampas, 'id');
            $db = \Config\Database::connect();
            
            // Obtener incidencias con información de la trampa asociada
            // Filtrar por: trampa pertenece al plano Y trampa tiene sede_id del plano Y incidencia tiene mismo sede_id que trampa
            // IMPORTANTE: Usar el mismo criterio que el dashboard (i.sede_id = t.sede_id)
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
            
            $incidencias = $query->getResultArray();
        }

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
        $idTrampa = $this->request->getPost('id_trampa'); // Obtener id_trampa si existe
        $comentario = $this->request->getPost('comentario'); // Obtener el comentario del movimiento
        
        if (!$sedeId || !$planoId || !$tipo || !$ubicacion || !$coordenadaX || !$coordenadaY) {
            return $this->response->setJSON(['success' => false, 'message' => 'Datos incompletos']);
        }
        
        try {
            // Cargar los modelos necesarios
            $trampaModel = new \App\Models\TrampaModel();
            $movimientoModel = new \App\Models\MovimientoTrampaModel();
            
            // Si se proporcionó un id_trampa, verificar si la trampa existe y obtener sus datos anteriores
            $trampaAnterior = null;
            if ($idTrampa) {
                $trampaAnterior = $trampaModel->where('id_trampa', $idTrampa)->first();
                
                // Si existe una trampa anterior, es un movimiento
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
                } else {
                    // Si no existe una trampa anterior pero se proporcionó un ID, 
                    // verificar si el ID ya está en uso por otra trampa
                    $trampaExistente = $trampaModel->where('id_trampa', $idTrampa)->first();
                    if ($trampaExistente) {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'El ID de trampa ' . $idTrampa . ' ya está en uso'
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
                $movimientoModel->insert([
                    'id_trampa' => $idTrampa,
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
        $trampaIdActual = $this->request->getPost('trampa_id_actual');
        $nuevoIdTrampa = $this->request->getPost('nuevo_id_trampa');
        
        if (!$trampaIdActual || !$nuevoIdTrampa) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Datos incompletos para actualizar el ID'
            ]);
        }
        
        try {
            // Cargar el modelo de trampas
            $trampaModel = new \App\Models\TrampaModel();
            
            // Buscar la trampa por su ID actual (puede ser id_trampa o id)
            $trampa = $trampaModel->where('id_trampa', $trampaIdActual)->first();
            
            // Si no se encuentra por id_trampa, buscar por id
            if (!$trampa) {
                $trampa = $trampaModel->find($trampaIdActual);
            }
            
            // Si es un ID temporal, buscar trampas recientes sin id_trampa o con ID temporal
            if (!$trampa && strpos($trampaIdActual, 'TEMP-') === 0) {
                // Para IDs temporales, buscar la trampa más reciente de este plano
                $planoId = $this->request->getPost('plano_id');
                if ($planoId) {
                    $trampa = $trampaModel->where('plano_id', $planoId)
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
            
            // IMPORTANTE: Este método actualiza el NOMBRE de la trampa, NO el id_trampa
            // El id_trampa es único y permanente, el nombre es lo que los inspectores editan
            
            // Verificar que el nuevo nombre no esté ya en uso por otra trampa (opcional)
            // Comentado porque el nombre podría repetirse entre trampas
            // $trampaExistente = $trampaModel->where('nombre', $nuevoIdTrampa)
            //                               ->where('id !=', $trampa['id'])
            //                               ->first();
            // 
            // if ($trampaExistente) {
            //     return $this->response->setJSON([
            //         'success' => false,
            //         'message' => 'El nombre "' . $nuevoIdTrampa . '" ya está en uso por otra trampa'
            //     ]);
            // }
            
            // Actualizar el NOMBRE de la trampa (no el id_trampa que permanece constante)
            $actualizado = $trampaModel->update($trampa['id'], ['nombre' => $nuevoIdTrampa]);
            
            if ($actualizado) {
                // Obtener la trampa actualizada con todos sus datos
                $trampaActualizada = $trampaModel->find($trampa['id']);
                
                // Debug temporal: Verificar que el tipo se mantiene
                log_message('info', 'Trampa actualizada - Tipo: ' . ($trampaActualizada['tipo'] ?? 'NULL') . ', Nombre: ' . $trampaActualizada['nombre']);
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Nombre de trampa actualizado correctamente',
                    'trampa' => [
                        'id' => $trampaActualizada['id'],
                        'id_trampa' => $trampaActualizada['id_trampa'], // Permanece igual
                        'nombre' => $trampaActualizada['nombre'], // Nuevo nombre
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
            
            // Si es un hallazgo, no necesitamos buscar la trampa
            if ($tipoIncidencia === 'Hallazgo') {
                // Para hallazgos, obtener la sede desde el plano_id o sede_id enviado
                $planoId = $this->request->getPost('plano_id');
                $sedeIdEnviado = $this->request->getPost('sede_id');
                
                if ($sedeIdEnviado) {
                    $idSede = $sedeIdEnviado;
                } elseif ($planoId) {
                    $planoModel = new \App\Models\PlanoModel();
                    $plano = $planoModel->find($planoId);
                    if ($plano && isset($plano['sede_id'])) {
                        $idSede = $plano['sede_id'];
                    } else {
                        return $this->response->setJSON(['success' => false, 'message' => 'No se pudo obtener la sede del plano']);
                    }
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
                $idSede = $trampa['sede_id'];
                log_message('info', 'ID de trampa encontrado: ' . $idTrampaReciente . ', ID de sede: ' . $idSede);
            }
            
            // Cargar el modelo de incidencias
            $incidenciaModel = new \App\Models\IncidenciaModel();
            
            // Formatear la fecha de incidencia para MySQL (YYYY-MM-DD HH:MM:SS)
            $fechaFormateada = date('Y-m-d H:i:s', strtotime($fechaIncidencia));
            
            // Verificar que los valores de tipo_insecto y tipo_incidencia sean correctos
            log_message('info', 'Valores antes de guardar: tipo_insecto=' . $tipoInsecto . ', tipo_incidencia=' . $tipoIncidencia);
            
            // Preparar los datos para guardar - Asegurarse de que los campos estén correctamente asignados
            $data = [
                'id_trampa' => $idTrampaReciente, // Para hallazgos será null, para capturas será el ID de la trampa
                'sede_id' => $idSede, // Agregamos el ID de la sede
                'fecha' => $fechaFormateada, // Usamos la fecha proporcionada por el usuario
                'tipo_plaga' => $tipoPlaga,
                'tipo_insecto' => $tipoInsecto, // Asegurarse de que este valor sea correcto
                'cantidad_organismos' => $cantidadOrganismos,
                'tipo_incidencia' => $tipoIncidencia, // Asegurarse de que este valor sea correcto
                'notas' => $notas,
                'inspector' => $inspector ?? 'Sistema'
            ];
            
            // Guardar la incidencia
            $incidenciaId = $incidenciaModel->insert($data);
            log_message('info', 'Incidencia guardada con ID: ' . $incidenciaId);
            
            // Registrar en auditoría
            log_create('incidencias', $incidenciaId, $data, "Se creó una incidencia: {$tipoPlaga} ({$tipoInsecto}) - Cantidad: {$cantidadOrganismos} - Inspector: {$inspector}");
            
            // Verificar que se haya guardado correctamente
            $incidenciaGuardada = $incidenciaModel->find($incidenciaId);
            log_message('info', 'Incidencia guardada: ' . json_encode($incidenciaGuardada));
            
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
        $incidencias = [];
        if (!empty($trampas)) {
            $trampaIds = array_column($trampas, 'id');
            $incidencias = $incidenciaModel->whereIn('id_trampa', $trampaIds)->findAll();
            
            // Asociar cada incidencia con su trampa correspondiente
            foreach ($incidencias as &$incidencia) {
                foreach ($trampas as $trampa) {
                    if ($trampa['id'] == $incidencia['id_trampa']) {
                        $incidencia['trampa'] = $trampa;
                        break;
                    }
                }
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
        $db = \Config\Database::connect();
        $query = $db->table('incidencias i')
            ->select('DISTINCT(i.tipo_plaga) as plaga')
            ->join('trampas t', 'i.id_trampa = t.id')
            ->where('t.plano_id', $id)
            ->where('i.tipo_plaga IS NOT NULL')
            ->where('i.tipo_plaga !=', '')
            ->orderBy('i.tipo_plaga', 'ASC')
            ->get();
        
        $data['listaPlagas'] = $query->getResultArray();

        return view('blueprints/ver_imagen', $data);
    }

} 