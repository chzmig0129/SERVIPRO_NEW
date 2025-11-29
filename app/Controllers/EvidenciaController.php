<?php

namespace App\Controllers;

use App\Models\EvidenciaModel;
use CodeIgniter\HTTP\ResponseInterface;

class EvidenciaController extends BaseController
{
    public function guardarEvidencia()
    {
        // Inicializar la respuesta
        $response = [
            'success' => false,
            'message' => '',
            'evidencia_id' => null
        ];
        
        try {
            // Validar los datos recibidos
            $rules = [
                'zona_ubicacion' => 'required',
                'description' => 'required',
                'coordenada_x' => 'required',
                'coordenada_y' => 'required',
                'plano_id' => 'required|numeric'
            ];
            
            if (!$this->validate($rules)) {
                $response['message'] = 'Datos incompletos o inválidos: ' . implode(', ', $this->validator->getErrors());
                return $this->response->setJSON($response);
            }
            
            // Obtener datos del formulario
            $planoId = $this->request->getPost('plano_id');
            $zonaUbicacion = $this->request->getPost('zona_ubicacion');
            $descripcion = $this->request->getPost('description');
            
            // Convertir coordenadas a flotantes
            $coordenadaX = floatval($this->request->getPost('coordenada_x'));
            $coordenadaY = floatval($this->request->getPost('coordenada_y'));
            
            // Procesar la imagen si existe
            $imagenPath = null;
            $files = $this->request->getFiles();
            
            if (isset($files['evidencia_imagenes']) && !empty($files['evidencia_imagenes'])) {
                $imagenes = $files['evidencia_imagenes'];
                
                // Por ahora guardamos solo la primera imagen
                if (isset($imagenes[0]) && $imagenes[0]->isValid() && !$imagenes[0]->hasMoved()) {
                    // Crear directorio si no existe
                    $uploadPath = FCPATH . 'uploads/evidencias/';
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    
                    // Generar nombre único y mover archivo
                    $newName = $imagenes[0]->getRandomName();
                    $imagenes[0]->move($uploadPath, $newName);
                    $imagenPath = 'uploads/evidencias/' . $newName;
                }
            }
            
            // Guardar en la base de datos
            $evidenciaModel = new EvidenciaModel();
            
            $data = [
                'id_plano' => $planoId,
                'ubicacion' => $zonaUbicacion,
                'descripcion' => $descripcion,
                'fecha_registro' => date('Y-m-d H:i:s'),
                'imagen_evidencia' => $imagenPath,
                'coordenada_x' => $coordenadaX,
                'coordenada_y' => $coordenadaY,
                'estado' => 'Pendiente'
            ];
            
            // Desactivar temporalmente las reglas de validación si hay problemas
            $evidenciaModel->skipValidation(true);
            
            $evidenciaId = $evidenciaModel->insert($data);
            
            if ($evidenciaId) {
                $response['success'] = true;
                $response['message'] = 'Evidencia guardada correctamente';
                $response['evidencia_id'] = $evidenciaId;
            } else {
                $response['message'] = 'Error al guardar la evidencia: ' . implode(', ', $evidenciaModel->errors());
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al guardar evidencia: ' . $e->getMessage());
            $response['message'] = 'Error: ' . $e->getMessage();
        }
        
        return $this->response->setJSON($response);
    }
    
    public function getEvidenciasPorPlano($planoId)
    {
        $evidenciaModel = new EvidenciaModel();
        $evidencias = $evidenciaModel->where('id_plano', $planoId)->findAll();
        
        return $this->response->setJSON($evidencias);
    }
    
    public function getEvidencia($id)
    {
        $evidenciaModel = new EvidenciaModel();
        $evidencia = $evidenciaModel->find($id);
        
        if (!$evidencia) {
            return $this->response->setStatusCode(404)->setJSON([
                'error' => true,
                'message' => 'Evidencia no encontrada'
            ]);
        }
        
        return $this->response->setJSON($evidencia);
    }
    
    public function actualizarEvidencia()
    {
        // Inicializar la respuesta
        $response = [
            'success' => false,
            'message' => ''
        ];
        
        try {
            // Validar los datos recibidos
            $rules = [
                'evidencia_id' => 'required|numeric',
                'description' => 'required'
            ];
            
            if (!$this->validate($rules)) {
                $response['message'] = 'Datos incompletos o inválidos: ' . implode(', ', $this->validator->getErrors());
                return $this->response->setJSON($response);
            }
            
            // Obtener datos del formulario
            $evidenciaId = $this->request->getPost('evidencia_id');
            $descripcion = $this->request->getPost('description');
            
            // Verificar si la evidencia existe
            $evidenciaModel = new EvidenciaModel();
            $evidencia = $evidenciaModel->find($evidenciaId);
            
            if (!$evidencia) {
                $response['message'] = 'La evidencia no existe';
                return $this->response->setStatusCode(404)->setJSON($response);
            }
            
            // Datos para actualizar
            $data = [
                'descripcion' => $descripcion
            ];
            
            // Procesar nuevas imágenes si existen
            $files = $this->request->getFiles();
            
            if (isset($files['evidencia_imagenes']) && !empty($files['evidencia_imagenes'])) {
                $imagenes = $files['evidencia_imagenes'];
                
                // Si hay al menos una imagen nueva
                if (isset($imagenes[0]) && $imagenes[0]->isValid() && !$imagenes[0]->hasMoved()) {
                    // Crear directorio si no existe
                    $uploadPath = FCPATH . 'uploads/evidencias/';
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    
                    // Generar nombre único y mover archivo
                    $newName = $imagenes[0]->getRandomName();
                    $imagenes[0]->move($uploadPath, $newName);
                    $data['imagen_evidencia'] = 'uploads/evidencias/' . $newName;
                    
                    // Opcionalmente, eliminar la imagen anterior
                    if (!empty($evidencia['imagen_evidencia']) && file_exists(FCPATH . $evidencia['imagen_evidencia'])) {
                        unlink(FCPATH . $evidencia['imagen_evidencia']);
                    }
                }
            }
            
            // Actualizar en la base de datos
            $evidenciaModel->skipValidation(true);
            
            $result = $evidenciaModel->update($evidenciaId, $data);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Evidencia actualizada correctamente';
            } else {
                $response['message'] = 'Error al actualizar la evidencia: ' . implode(', ', $evidenciaModel->errors());
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar evidencia: ' . $e->getMessage());
            $response['message'] = 'Error: ' . $e->getMessage();
        }
        
        return $this->response->setJSON($response);
    }
    
    public function eliminarEvidencia($id)
    {
        // Inicializar la respuesta
        $response = [
            'success' => false,
            'message' => ''
        ];
        
        try {
            // Log para depuración
            log_message('info', 'Intentando eliminar evidencia con ID: ' . $id);
            
            // Verificar si la evidencia existe
            $evidenciaModel = new EvidenciaModel();
            $evidencia = $evidenciaModel->find($id);
            
            if (!$evidencia) {
                $response['message'] = 'La evidencia no existe';
                log_message('warning', 'Error al eliminar: La evidencia con ID ' . $id . ' no existe');
                return $this->response->setStatusCode(404)->setJSON($response);
            }
            
            // Eliminar la imagen asociada si existe
            if (!empty($evidencia['imagen_evidencia']) && file_exists(FCPATH . $evidencia['imagen_evidencia'])) {
                unlink(FCPATH . $evidencia['imagen_evidencia']);
            }
            
            // Eliminar el registro de la base de datos
            $result = $evidenciaModel->delete($id);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Evidencia eliminada correctamente';
                log_message('info', 'Evidencia con ID ' . $id . ' eliminada correctamente');
            } else {
                $response['message'] = 'Error al eliminar la evidencia';
                log_message('error', 'Error al eliminar la evidencia con ID ' . $id . ': ' . implode(', ', $evidenciaModel->errors()));
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar evidencia: ' . $e->getMessage());
            $response['message'] = 'Error: ' . $e->getMessage();
        }
        
        return $this->response->setJSON($response);
    }
    
    public function subirImagenResuelta()
    {
        $response = [
            'success' => false,
            'message' => ''
        ];
        
        try {
            $evidenciaId = $this->request->getPost('evidencia_id');
            
            if (!$evidenciaId) {
                $response['message'] = 'ID de evidencia requerido';
                return $this->response->setJSON($response);
            }
            
            // Verificar si la evidencia existe
            $evidenciaModel = new EvidenciaModel();
            $evidencia = $evidenciaModel->find($evidenciaId);
            
            if (!$evidencia) {
                $response['message'] = 'La evidencia no existe';
                return $this->response->setStatusCode(404)->setJSON($response);
            }
            
            // Manejar la imagen resuelta
            $imagenPath = '';
            $files = $this->request->getFiles();
            
            if (isset($files['imagen_resuelta']) && $files['imagen_resuelta']->isValid()) {
                $imagen = $files['imagen_resuelta'];
                
                if (!$imagen->hasMoved()) {
                    // Crear directorio si no existe
                    $uploadPath = FCPATH . 'uploads/evidencias/';
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0777, true);
                    }
                    
                    // Generar nombre único y mover archivo
                    $newName = 'resuelta_' . $imagen->getRandomName();
                    $imagen->move($uploadPath, $newName);
                    $imagenPath = 'uploads/evidencias/' . $newName;
                }
            } else {
                $response['message'] = 'No se recibió una imagen válida';
                return $this->response->setJSON($response);
            }
            
            // Actualizar la evidencia con la imagen resuelta
            $data = [
                'imagen_resuelta' => $imagenPath
            ];
            
            $result = $evidenciaModel->update($evidenciaId, $data);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Imagen de resolución guardada correctamente';
                $response['imagen_path'] = $imagenPath;
            } else {
                $response['message'] = 'Error al guardar la imagen: ' . implode(', ', $evidenciaModel->errors());
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error al subir imagen resuelta: ' . $e->getMessage());
            $response['message'] = 'Error: ' . $e->getMessage();
        }
        
        return $this->response->setJSON($response);
    }
    
    public function cambiarEstado()
    {
        $response = [
            'success' => false,
            'message' => ''
        ];
        
        try {
            $evidenciaId = $this->request->getPost('evidencia_id');
            $estado = $this->request->getPost('estado');
            
            if (!$evidenciaId || !$estado) {
                $response['message'] = 'ID de evidencia y estado son requeridos';
                return $this->response->setJSON($response);
            }
            
            if (!in_array($estado, ['Pendiente', 'Resuelta'])) {
                $response['message'] = 'Estado no válido';
                return $this->response->setJSON($response);
            }
            
            // Verificar si la evidencia existe
            $evidenciaModel = new EvidenciaModel();
            $evidencia = $evidenciaModel->find($evidenciaId);
            
            if (!$evidencia) {
                $response['message'] = 'La evidencia no existe';
                return $this->response->setStatusCode(404)->setJSON($response);
            }
            
            // Preparar datos para actualizar
            $data = [
                'estado' => $estado
            ];
            
            // Si se marca como resuelta, agregar fecha de resolución
            if ($estado === 'Resuelta') {
                $data['fecha_resolucion'] = date('Y-m-d H:i:s');
            } else {
                // Si se reabre, limpiar fecha de resolución
                $data['fecha_resolucion'] = null;
            }
            
            $result = $evidenciaModel->update($evidenciaId, $data);
            
            if ($result) {
                $response['success'] = true;
                $response['message'] = $estado === 'Resuelta' ? 'Evidencia marcada como resuelta' : 'Evidencia reabierta';
            } else {
                $response['message'] = 'Error al cambiar el estado: ' . implode(', ', $evidenciaModel->errors());
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error al cambiar estado de evidencia: ' . $e->getMessage());
            $response['message'] = 'Error: ' . $e->getMessage();
        }
        
        return $this->response->setJSON($response);
    }

    public function vistoBuenoSupervisor()
    {
        $response = [
            'success' => false,
            'message' => '',
            'debug' => []
        ];

        try {
            // Log para depuración
            log_message('info', '=== INICIO vistoBuenoSupervisor ===');
            
            $evidenciaId = $this->request->getPost('evidencia_id');
            $vistoBueno = $this->request->getPost('visto_bueno');
            
            // Log de datos recibidos
            log_message('info', 'Datos recibidos - evidencia_id: ' . $evidenciaId . ', visto_bueno: ' . $vistoBueno);
            $response['debug']['received_data'] = [
                'evidencia_id' => $evidenciaId,
                'visto_bueno' => $vistoBueno
            ];

            if (!$evidenciaId) {
                $response['message'] = 'ID de evidencia requerido';
                log_message('error', 'Error: ID de evidencia requerido');
                return $this->response->setJSON($response);
            }

            $evidenciaModel = new \App\Models\EvidenciaModel();
            $evidencia = $evidenciaModel->find($evidenciaId);
            
            // Log de evidencia encontrada
            log_message('info', 'Evidencia encontrada: ' . json_encode($evidencia));

            if (!$evidencia) {
                $response['message'] = 'La evidencia no existe';
                log_message('error', 'Error: La evidencia con ID ' . $evidenciaId . ' no existe');
                return $this->response->setStatusCode(404)->setJSON($response);
            }

            // Convertir a entero para asegurar el tipo correcto
            $vistoBuenoValue = ($vistoBueno == '1' || $vistoBueno === true || $vistoBueno == 'true') ? 1 : 0;
            
            $data = [
                'visto_bueno_supervisor' => $vistoBuenoValue
            ];
            
            // Log de datos a actualizar
            log_message('info', 'Datos a actualizar: ' . json_encode($data));
            $response['debug']['update_data'] = $data;

            // Desactivar validación temporalmente si hay problemas
            $evidenciaModel->skipValidation(true);
            
            $result = $evidenciaModel->update($evidenciaId, $data);
            
            // Log del resultado
            log_message('info', 'Resultado de actualización: ' . ($result ? 'true' : 'false'));

            if ($result) {
                $response['success'] = true;
                $response['message'] = 'Visto Bueno actualizado correctamente';
                log_message('info', 'Visto Bueno actualizado exitosamente para evidencia ID: ' . $evidenciaId);
            } else {
                $response['message'] = 'Error al actualizar Visto Bueno: ' . implode(', ', $evidenciaModel->errors());
                log_message('error', 'Error al actualizar Visto Bueno: ' . implode(', ', $evidenciaModel->errors()));
            }
            
            log_message('info', '=== FIN vistoBuenoSupervisor ===');
            
        } catch (\Exception $e) {
            log_message('error', 'Excepción en vistoBuenoSupervisor: ' . $e->getMessage());
            $response['message'] = 'Error: ' . $e->getMessage();
        }

        return $this->response->setJSON($response);
    }
} 