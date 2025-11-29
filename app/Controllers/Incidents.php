<?php

namespace App\Controllers;

use App\Models\SedeModel;
use App\Models\PlanoModel;
use App\Models\EvidenciaModel;

class Incidents extends BaseController
{
    public function index(): string
    {
        // Verificar si el usuario ha iniciado sesión
        $this->verificarSesion();
        
        $sedeModel = new SedeModel();
        $planoModel = new PlanoModel();
        
        $data = [
            'sedes' => $sedeModel->findAll(),
            'title' => 'Evidencias'
        ];
        
        return view('incidents/index', $data);
    }
    
    public function getPlanosBySede($sedeId = null)
    {
        $planoModel = new PlanoModel();
        
        if ($sedeId) {
            $planos = $planoModel->where('sede_id', $sedeId)->findAll();
        } else {
            $planos = [];
        }
        
        return $this->response->setJSON($planos);
    }
    
    /**
     * Guarda una nueva evidencia en la base de datos
     */
    public function guardarEvidencia()
    {
        // Verificar si la solicitud es AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Acceso no permitido'
            ]);
        }
        
        // Crear una instancia del modelo de evidencias
        $evidenciaModel = new EvidenciaModel();
        
        // Obtener datos del formulario
        $zona = $this->request->getPost('zona_ubicacion');
        $tipo = $this->request->getPost('tipo_evidencia');
        $descripcion = $this->request->getPost('description');
        $coordenadaX = $this->request->getPost('coordenada_x');
        $coordenadaY = $this->request->getPost('coordenada_y');
        $planoId = $this->request->getPost('plano_id');
        
        // Validar que los campos obligatorios estén presentes
        if (empty($zona) || empty($tipo) || empty($descripcion) || empty($planoId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Todos los campos son obligatorios'
            ]);
        }
        
        // Preparar los datos para guardar
        $data = [
            'zona_ubicacion' => $zona,
            'tipo_evidencia' => $tipo,
            'description' => $descripcion,
            'coordenada_x' => $coordenadaX,
            'coordenada_y' => $coordenadaY,
            'plano_id' => $planoId,
            'fecha_registro' => date('Y-m-d H:i:s')
        ];
        
        // Obtener las imágenes
        $imagenes = $this->request->getFiles('evidencia_imagenes');
        
        // Variable para almacenar las rutas de las imágenes
        $rutasImagenes = [];
        
        // Si hay imágenes, procesarlas
        if (!empty($imagenes) && isset($imagenes[0]) && $imagenes[0]->isValid()) {
            foreach ($imagenes as $imagen) {
                // Verificar si es una imagen válida
                if ($imagen->isValid() && !$imagen->hasMoved()) {
                    // Obtener un nombre único para la imagen
                    $nombreArchivo = $imagen->getRandomName();
                    
                    // Mover la imagen a la carpeta de uploads
                    if ($imagen->move(ROOTPATH . 'public/uploads/evidencias', $nombreArchivo)) {
                        // Guardar la ruta relativa de la imagen
                        $rutasImagenes[] = 'uploads/evidencias/' . $nombreArchivo;
                    }
                }
            }
            
            // Guardar las rutas de las imágenes como JSON si hay alguna
            if (!empty($rutasImagenes)) {
                $data['imagenes_evidencia'] = json_encode($rutasImagenes);
            }
        }
        
        // Intentar guardar la evidencia
        try {
            $evidenciaModel->insert($data);
            $evidenciaId = $evidenciaModel->getInsertID();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Evidencia guardada correctamente',
                'evidencia_id' => $evidenciaId
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al guardar la evidencia: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Obtiene las evidencias asociadas a un plano específico
     */
    public function getEvidenciasByPlano($planoId = null)
    {
        if (!$planoId) {
            return $this->response->setJSON([]);
        }
        
        $evidenciaModel = new EvidenciaModel();
        
        // Obtener filtro de fecha si existe
        $fecha = $this->request->getGet('fecha');
        
        if ($fecha) {
            $evidencias = $evidenciaModel
                ->where('plano_id', $planoId)
                ->where('DATE(fecha_registro)', $fecha)
                ->findAll();
        } else {
            $evidencias = $evidenciaModel
                ->where('plano_id', $planoId)
                ->findAll();
        }
        
        return $this->response->setJSON($evidencias);
    }
} 