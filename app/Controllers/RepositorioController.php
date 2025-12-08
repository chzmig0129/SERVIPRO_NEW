<?php

namespace App\Controllers;

use App\Models\SedeModel;
use App\Models\RepositorioDocumentoModel;

class RepositorioController extends BaseController
{
    protected $documentoModel;
    protected $sedeModel;

    public function __construct()
    {
        $this->documentoModel = new RepositorioDocumentoModel();
        $this->sedeModel = new SedeModel();
    }

    public function index()
    {
        // Verificar si el usuario ha iniciado sesión
        $this->verificarSesion();
        
        $data['sedes'] = $this->sedeModel->findAll();
        
        // Obtener la sede seleccionada
        $sedeSeleccionada = $this->request->getGet('sede_id');
        if (empty($sedeSeleccionada) && !empty($data['sedes'])) {
            $sedeSeleccionada = $data['sedes'][0]['id'];
        }
        
        $data['sedeSeleccionada'] = $sedeSeleccionada;
        
        // Obtener el nombre de la sede seleccionada
        $sedeSeleccionadaNombre = "";
        if (!empty($sedeSeleccionada)) {
            foreach ($data['sedes'] as $sede) {
                if ($sede['id'] == $sedeSeleccionada) {
                    $sedeSeleccionadaNombre = $sede['nombre'];
                    break;
                }
            }
        }
        $data['sedeSeleccionadaNombre'] = $sedeSeleccionadaNombre;
        
        // Obtener filtros
        $tipoFiltro = $this->request->getGet('tipo');
        $busqueda = $this->request->getGet('busqueda');
        
        // Obtener documentos de la sede
        if (!empty($sedeSeleccionada)) {
            $data['documentos'] = $this->documentoModel->obtenerDocumentosPorSede(
                $sedeSeleccionada, 
                $tipoFiltro, 
                $busqueda
            );
        } else {
            $data['documentos'] = [];
        }
        
        return view('repositorio/index', $data);
    }

    /**
     * Sube un documento al repositorio
     */
    public function subir()
    {
        // Verificar si el usuario ha iniciado sesión
        $this->verificarSesion();
        
        // Validar datos del formulario
        $rules = [
            'sede_id' => 'required|integer|is_not_unique[sedes.id]',
            'titulo' => 'required|min_length[3]|max_length[255]',
            'tipo' => 'required|in_list[plan_accion,documento,reporte,otro]',
            'descripcion' => 'permit_empty|max_length[5000]',
            'archivo' => 'uploaded[archivo]|max_size[archivo,51200]|ext_in[archivo,pdf,doc,docx,ppt,pptx]',
            'fecha_documento' => 'permit_empty|valid_date'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $this->validator->getErrors()
            ]);
        }

        try {
            $archivo = $this->request->getFile('archivo');
            
            if (!$archivo || !$archivo->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se proporcionó un archivo válido'
                ]);
            }

            // Obtener información del archivo ANTES de moverlo
            $tamañoArchivo = $archivo->getSize();
            
            // Obtener tipo MIME del cliente (navegador) o determinar por extensión
            $tipoMime = $archivo->getClientMimeType();
            
            // Si no se puede obtener del cliente, determinar por extensión
            if (empty($tipoMime)) {
                $extension = $archivo->getClientExtension();
                $tiposMime = [
                    'pdf' => 'application/pdf',
                    'doc' => 'application/msword',
                    'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'ppt' => 'application/vnd.ms-powerpoint',
                    'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation'
                ];
                $tipoMime = $tiposMime[strtolower($extension)] ?? 'application/octet-stream';
            }

            // Crear directorio si no existe
            $uploadPath = FCPATH . 'uploads/repositorio/';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Generar nombre único para el archivo
            $nombreArchivo = $archivo->getRandomName();
            
            // Mover el archivo
            if (!$archivo->move($uploadPath, $nombreArchivo)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al subir el archivo: ' . $archivo->getErrorString()
                ]);
            }

            // Preparar datos para guardar en la base de datos
            $data = [
                'sede_id' => $this->request->getPost('sede_id'),
                'titulo' => $this->request->getPost('titulo'),
                'tipo' => $this->request->getPost('tipo'),
                'descripcion' => $this->request->getPost('descripcion'),
                'nombre_archivo' => $nombreArchivo,
                'ruta_archivo' => 'uploads/repositorio/' . $nombreArchivo,
                'tamaño_archivo' => $tamañoArchivo,
                'tipo_mime' => $tipoMime,
                'fecha_documento' => $this->request->getPost('fecha_documento') 
                    ? date('Y-m-d H:i:s', strtotime($this->request->getPost('fecha_documento'))) 
                    : null
            ];

            // Guardar en la base de datos
            if ($this->documentoModel->insert($data)) {
                $documentoId = $this->documentoModel->getInsertID();
                
                // Registrar en auditoría
                log_create('repositorio_documentos', $documentoId, $data, "Se subió un documento: {$data['titulo']} (tipo: {$data['tipo']})");
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Documento subido correctamente',
                    'documento_id' => $documentoId
                ]);
            } else {
                // Si falla la inserción, eliminar el archivo subido
                $archivoCompleto = $uploadPath . $nombreArchivo;
                if (file_exists($archivoCompleto)) {
                    unlink($archivoCompleto);
                }
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al guardar el documento en la base de datos',
                    'errors' => $this->documentoModel->errors()
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al subir documento: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Descarga un documento
     */
    public function descargar($id)
    {
        // Verificar si el usuario ha iniciado sesión
        $this->verificarSesion();
        
        $documento = $this->documentoModel->find($id);
        
        if (!$documento) {
            return redirect()->back()->with('error', 'Documento no encontrado');
        }

        $archivoPath = FCPATH . $documento['ruta_archivo'];
        
        if (!file_exists($archivoPath)) {
            return redirect()->back()->with('error', 'El archivo no existe en el servidor');
        }

        return $this->response->download($archivoPath, null);
    }

    /**
     * Elimina un documento
     */
    public function eliminar($id)
    {
        // Verificar si el usuario ha iniciado sesión
        $this->verificarSesion();
        
        $documento = $this->documentoModel->find($id);
        
        if (!$documento) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Documento no encontrado'
            ]);
        }

        try {
            // Eliminar archivo físico
            $archivoPath = FCPATH . $documento['ruta_archivo'];
            if (file_exists($archivoPath)) {
                unlink($archivoPath);
            }

            // Eliminar registro de la base de datos
            if ($this->documentoModel->delete($id)) {
                // Registrar en auditoría
                log_delete('repositorio_documentos', $id, $documento, "Se eliminó el documento: {$documento['titulo']}");
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Documento eliminado correctamente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al eliminar el documento de la base de datos'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al eliminar documento: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al procesar la solicitud: ' . $e->getMessage()
            ]);
        }
    }
}

