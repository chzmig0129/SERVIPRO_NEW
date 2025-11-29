<?php

namespace App\Controllers;

use App\Models\EvidenciaModel;
use App\Models\PlanoModel;
use App\Models\SedeModel;
use CodeIgniter\API\ResponseTrait;

class Incidencias extends BaseController
{
    use ResponseTrait;
    
    /**
     * Muestra la página principal de incidencias
     */
    public function index()
    {
        $sedeModel = new SedeModel();
        
        $data = [
            'title' => 'Gestión de Incidencias',
            'sedes' => $sedeModel->findAll()
        ];
        
        return view('incidents/index', $data);
    }
    
    /**
     * Obtiene los planos asociados a una sede
     */
    public function getPlanosBySede($sedeId = null)
    {
        if (!$sedeId) {
            return $this->response->setJSON([]);
        }
        
        $planoModel = new PlanoModel();
        $planos = $planoModel->where('sede_id', $sedeId)->findAll();
        
        // Devolvemos los datos como JSON directamente
        return $this->response->setJSON($planos);
    }
    
    /**
     * Guarda una nueva incidencia/evidencia en la base de datos
     */
    public function guardarIncidencia()
    {
        // Verificar si es una solicitud AJAX
        if (!$this->request->isAJAX()) {
            return $this->fail('Método de acceso no permitido', 403);
        }
        
        try {
            // Obtener datos del formulario
            $idPlano = $this->request->getPost('plano_id');
            $ubicacion = $this->request->getPost('zona_ubicacion');
            $descripcion = $this->request->getPost('description');
            $coordenadaX = (int)$this->request->getPost('coordenada_x');
            $coordenadaY = (int)$this->request->getPost('coordenada_y');
            
            // Validar datos obligatorios
            if (empty($idPlano) || empty($ubicacion) || !isset($coordenadaX) || !isset($coordenadaY)) {
                return $this->fail('Todos los campos obligatorios deben ser proporcionados', 400);
            }
            
            // Crear instancia del modelo
            $evidenciaModel = new EvidenciaModel();
            
            // Preparar datos para inserción
            $data = [
                'id_plano'     => $idPlano,
                'ubicacion'    => $ubicacion,
                'descripcion'  => $descripcion,
                'coordenada_x' => $coordenadaX,
                'coordenada_y' => $coordenadaY,
                'fecha_registro' => date('Y-m-d H:i:s')
            ];
            
            // Procesar imagen si se ha subido
            if ($imagen = $this->request->getFile('evidencia_imagen')) {
                if ($imagen->isValid() && !$imagen->hasMoved()) {
                    // Generar nombre aleatorio para la imagen
                    $nombreArchivo = $imagen->getRandomName();
                    
                    // Mover la imagen al directorio de uploads
                    if ($imagen->move(ROOTPATH . 'public/uploads/evidencias', $nombreArchivo)) {
                        $data['imagen_evidencia'] = 'uploads/evidencias/' . $nombreArchivo;
                    } else {
                        return $this->fail('No se pudo guardar la imagen', 500);
                    }
                }
            }
            
            // Procesar múltiples imágenes si es necesario
            $imagenes = $this->request->getFiles();
            if (!empty($imagenes) && isset($imagenes['evidencia_imagenes'])) {
                $rutasImagenes = [];
                
                foreach ($imagenes['evidencia_imagenes'] as $img) {
                    if ($img->isValid() && !$img->hasMoved()) {
                        $nombreArchivo = $img->getRandomName();
                        
                        if ($img->move(ROOTPATH . 'public/uploads/evidencias', $nombreArchivo)) {
                            $rutasImagenes[] = 'uploads/evidencias/' . $nombreArchivo;
                        }
                    }
                }
                
                if (!empty($rutasImagenes)) {
                    $data['imagen_evidencia'] = json_encode($rutasImagenes);
                }
            }
            
            // Guardar en la base de datos
            if (!$evidenciaModel->insert($data)) {
                return $this->fail($evidenciaModel->errors(), 400);
            }
            
            // Obtener el ID de la inserción
            $incidenciaId = $evidenciaModel->getInsertID();
            
            // Retornar respuesta exitosa
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Incidencia guardada correctamente',
                'evidencia_id' => $incidenciaId
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[Incidencias::guardarIncidencia] ' . $e->getMessage());
            return $this->fail('Error al guardar la incidencia: ' . $e->getMessage(), 500);
        }
    }
    
    /**
     * Obtiene las incidencias por plano
     */
    public function getIncidenciasPorPlano($idPlano = null)
    {
        if (!$idPlano) {
            return $this->response->setJSON([]);
        }
        
        $evidenciaModel = new EvidenciaModel();
        
        // Filtro por fecha si se proporciona
        $fecha = $this->request->getGet('fecha');
        
        if ($fecha) {
            $incidencias = $evidenciaModel->where('id_plano', $idPlano)
                                         ->where('DATE(fecha_registro)', $fecha)
                                         ->findAll();
        } else {
            $incidencias = $evidenciaModel->where('id_plano', $idPlano)
                                         ->findAll();
        }
        
        return $this->response->setJSON($incidencias);
    }
} 