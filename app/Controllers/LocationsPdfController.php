<?php
namespace App\Controllers;

use App\Models\SedeModel;
use App\Models\TrampaModel;
use App\Models\IncidenciaModel;
use App\Models\PlanoModel;

class LocationsPdfController extends BaseController
{
    /**
     * Genera un PDF con las estadísticas de locations
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function generarPDF()
    {
        // Obtener los mismos datos que para la vista de estadísticas
        $db = \Config\Database::connect();
        $sedeId = $this->request->getGet('sede_id');
        
        // Verificar si se están recibiendo datos de gráficos por POST
        $chartImages = $this->request->getPost('chart_images');
        $usarChartImages = !empty($chartImages);
        
        // Inicializar modelos
        $sedeModel = new SedeModel();
        $trampaModel = new TrampaModel();
        $incidenciaModel = new IncidenciaModel();
        $planoModel = new PlanoModel();
        
        // Obtener todas las sedes para el filtro
        $data['sedes'] = $sedeModel->findAll();
        $data['sede_seleccionada'] = $sedeId;

        // Condición WHERE base para el filtro de sede
        $whereSedeCondition = $sedeId ? "AND t.sede_id = $sedeId" : "";
        $whereSedeConditionIncidencias = $sedeId ? "AND i.sede_id = $sedeId" : "";
        
        // Obtener estadísticas de trampas por ubicación
        $queryTrampasUbicacion = $db->query("
            SELECT 
                ubicacion,
                COUNT(*) as total_trampas
            FROM trampas t
            WHERE 1=1 $whereSedeCondition
            GROUP BY ubicacion
            ORDER BY total_trampas DESC
        ");
        $data['trampasPorUbicacion'] = $queryTrampasUbicacion->getResultArray();

        // Obtener estadísticas de incidencias por tipo
        $queryIncidenciasTipo = $db->query("
            SELECT 
                tipo_incidencia,
                COUNT(*) as total
            FROM incidencias i
            WHERE 1=1 $whereSedeConditionIncidencias
            GROUP BY tipo_incidencia
            ORDER BY total DESC
        ");
        $data['incidenciasPorTipo'] = $queryIncidenciasTipo->getResultArray();
        
        // Obtener estadísticas de incidencias por mes
        $queryIncidenciasMensual = $db->query("
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as mes,
                COUNT(*) as total
            FROM incidencias i
            WHERE 1=1 $whereSedeConditionIncidencias
            GROUP BY DATE_FORMAT(fecha, '%Y-%m')
            ORDER BY mes DESC
        ");
        $data['incidenciasMensuales'] = $queryIncidenciasMensual->getResultArray();

        // Obtener todas las trampas con detalles
        $queryTodasTrampas = $db->query("
            SELECT 
                t.*,
                s.nombre as sede_nombre
            FROM trampas t
            LEFT JOIN sedes s ON s.id = t.sede_id
            WHERE 1=1 $whereSedeCondition
            ORDER BY t.ubicacion, t.id
        ");
        $data['todasLasTrampas'] = $queryTodasTrampas->getResultArray();

        // Obtener todas las incidencias con detalles
        $queryTodasIncidencias = $db->query("
            SELECT 
                i.*,
                s.nombre as sede_nombre
            FROM incidencias i
            LEFT JOIN sedes s ON s.id = i.sede_id
            WHERE 1=1 $whereSedeConditionIncidencias
            ORDER BY i.fecha DESC
        ");
        $data['todasLasIncidencias'] = $queryTodasIncidencias->getResultArray();

        // Obtener todos los planos
        $queryTodosPlanos = $db->query("
            SELECT 
                p.*,
                s.nombre as sede_nombre
            FROM planos p
            LEFT JOIN sedes s ON s.id = p.sede_id
            WHERE 1=1 " . ($sedeId ? "AND p.sede_id = $sedeId" : "") . "
            ORDER BY p.nombre
        ");
        $data['todosLosPlanos'] = $queryTodosPlanos->getResultArray();
        
        // Calcular estadísticas generales
        $totalTrampas = 0;
        $totalIncidencias = 0;
        $totalPlanos = 0;
        
        if (!empty($data['todasLasTrampas'])) {
            $totalTrampas = count($data['todasLasTrampas']);
        }
        
        if (!empty($data['todasLasIncidencias'])) {
            $totalIncidencias = count($data['todasLasIncidencias']);
        }
        
        if (!empty($data['todosLosPlanos'])) {
            $totalPlanos = count($data['todosLosPlanos']);
        }
        
        $data['totalTrampasSede'] = $totalTrampas;
        $data['totalIncidencias'] = $totalIncidencias;
        $data['totalPlanos'] = $totalPlanos;
        
        // Si hay una sede seleccionada, obtener su nombre
        if ($sedeId) {
            $sede = $sedeModel->find($sedeId);
            $data['nombre_sede_seleccionada'] = $sede ? $sede['nombre'] : '';
        }
        
        // Pasar las imágenes de gráficos si se recibieron
        if ($usarChartImages) {
            $data['chart_images'] = json_decode($chartImages, true);
        }
        
        // Procesar notas de gráficas si se recibieron
        $notasGraficas = $this->request->getPost('notas_graficas');
        if (!empty($notasGraficas)) {
            $data['notas_graficas'] = json_decode($notasGraficas, true);
        }

        // Procesar acciones de seguimiento si se recibieron
        $accionesSeguimiento = $this->request->getPost('acciones_seguimiento');
        
        // Debug: Log para verificar qué se está recibiendo
        log_message('info', 'Debug PDF - Acciones de seguimiento recibidas: ' . var_export($accionesSeguimiento, true));
        
        if (!empty($accionesSeguimiento)) {
            $data['acciones_seguimiento'] = $accionesSeguimiento;
            log_message('info', 'Debug PDF - Acciones de seguimiento asignadas a data');
        } else {
            log_message('info', 'Debug PDF - Acciones de seguimiento están vacías o no se recibieron');
        }
        
        // Cargar la vista especial para PDF
        $html = view('locations/pdf_estadisticas', $data);

        // Configurar opciones de DOMPDF (exactamente igual que quejas)
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isFontSubsettingEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        
        // Crear instancia de DOMPDF
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Cargar el HTML
        $dompdf->loadHtml($html);
        
        // Configurar papel y orientación
        $dompdf->setPaper('A4', 'portrait');
        
        // Renderizar el PDF
        $dompdf->render();
        
        // Nombre del archivo
        $filename = 'Estadisticas_Locations_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Enviar el archivo al navegador (exactamente igual que quejas)
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    // Método para obtener la imagen de previsualización (igual que en index)
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
            // log_message('error', 'Error al procesar la imagen del plano: ' . $e->getMessage());
        }
        return null;
    }
} 