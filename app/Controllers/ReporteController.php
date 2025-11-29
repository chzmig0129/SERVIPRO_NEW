<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\PlanoModel;
use App\Models\SedeModel;
use App\Models\TrampaModel;
use App\Models\IncidenciaModel;
use App\Models\VentaModel;
use App\Models\QuejaModel;
use App\Models\UsuarioModel;

class ReporteController extends BaseController
{
    /**
     * Genera un reporte PDF de un plano con sus trampas e incidencias
     * 
     * @param int $id ID del plano
     * @return \CodeIgniter\HTTP\Response
     */
    public function generarReportePlano($id = null)
    {
        if (!$id) {
            return redirect()->to('/blueprints')->with('error', 'Plano no especificado');
        }

        // Cargar modelos
        $planoModel = new PlanoModel();
        $sedeModel = new SedeModel();
        $trampaModel = new TrampaModel();
        $incidenciaModel = new IncidenciaModel();

        // Obtener información del plano
        $plano = $planoModel->find($id);
        if (!$plano) {
            return redirect()->to('/blueprints')->with('error', 'Plano no encontrado');
        }

        // Obtener la imagen del plano
        $imagenUrl = '';
        if (!empty($plano['archivo'])) {
            try {
                $archivoData = json_decode($plano['archivo'], true);
                if (isset($archivoData['imagen']) && !empty($archivoData['imagen'])) {
                    $imagenUrl = $archivoData['imagen'];
                }
            } catch (\Exception $e) {
                log_message('error', 'Error al procesar la imagen del plano: ' . $e->getMessage());
            }
        }
        
        // Obtener información de la sede asociada
        $sede = $sedeModel->find($plano['sede_id']);
        
        // Obtener las trampas asociadas al plano
        $trampas = $trampaModel->where('plano_id', $id)->findAll();
        
        // Obtener las incidencias asociadas a las trampas de este plano
        $incidencias = [];
        if (!empty($trampas)) {
            $trampaIds = array_column($trampas, 'id');
            $incidencias = $incidenciaModel->whereIn('id_trampa', $trampaIds)->findAll();
        }

        // Preparar datos para la vista
        $data = [
            'plano' => $plano,
            'sede' => $sede,
            'imagen_url' => $imagenUrl,
            'trampas' => $trampas,
            'incidencias' => $incidencias,
            'fecha_generacion' => date('Y-m-d H:i:s')
        ];

        // Generar la vista HTML para el reporte
        $html = view('reportes/reporte_plano', $data);

        // Configurar opciones de DOMPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true); // Permitir cargar imágenes remotas
        $options->set('isPhpEnabled', true);
        $options->set('isFontSubsettingEnabled', true);
        
        // Crear instancia de DOMPDF
        $dompdf = new Dompdf($options);
        
        // Cargar el HTML
        $dompdf->loadHtml($html);
        
        // Configurar papel y orientación
        $dompdf->setPaper('A4', 'landscape');
        
        // Renderizar el PDF
        $dompdf->render();
        
        // Nombre del archivo
        $filename = 'Reporte_Plano_' . $id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Enviar el archivo al navegador
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    /**
     * Genera un reporte PDF de todas las incidencias de una sede
     * 
     * @param int $id ID de la sede
     * @return \CodeIgniter\HTTP\Response
     */
    public function generarReporteSede($id = null)
    {
        if (!$id) {
            return redirect()->to('/locations')->with('error', 'Sede no especificada');
        }

        // Cargar modelos
        $sedeModel = new SedeModel();
        $planoModel = new PlanoModel();
        $trampaModel = new TrampaModel();
        $incidenciaModel = new IncidenciaModel();

        // Obtener información de la sede
        $sede = $sedeModel->find($id);
        if (!$sede) {
            return redirect()->to('/locations')->with('error', 'Sede no encontrada');
        }

        // Obtener los planos de la sede
        $planos = $planoModel->where('sede_id', $id)->findAll();
        
        // Obtener todas las trampas de la sede
        $trampas = $trampaModel->where('sede_id', $id)->findAll();
        
        // Obtener todas las incidencias de la sede
        $incidencias = $incidenciaModel->where('sede_id', $id)->findAll();
        
        // Preparar estadísticas para gráficos
        $estadisticas = [
            'total_trampas' => count($trampas),
            'total_incidencias' => count($incidencias),
            'tipos_trampas' => [],
            'tipos_incidencias' => [],
            'incidencias_por_mes' => []
        ];
        
        // Contar tipos de trampas
        foreach ($trampas as $trampa) {
            $tipo = $trampa['tipo'] ?? 'Desconocido';
            if (!isset($estadisticas['tipos_trampas'][$tipo])) {
                $estadisticas['tipos_trampas'][$tipo] = 0;
            }
            $estadisticas['tipos_trampas'][$tipo]++;
        }
        
        // Contar tipos de incidencias y organizar por mes
        foreach ($incidencias as $incidencia) {
            // Contar por tipo
            $tipo = $incidencia['tipo_incidencia'] ?? 'Desconocido';
            if (!isset($estadisticas['tipos_incidencias'][$tipo])) {
                $estadisticas['tipos_incidencias'][$tipo] = 0;
            }
            $estadisticas['tipos_incidencias'][$tipo]++;
            
            // Organizar por mes
            $mes = date('Y-m', strtotime($incidencia['fecha']));
            if (!isset($estadisticas['incidencias_por_mes'][$mes])) {
                $estadisticas['incidencias_por_mes'][$mes] = 0;
            }
            $estadisticas['incidencias_por_mes'][$mes]++;
        }
        
        // Preparar datos para la vista
        $data = [
            'sede' => $sede,
            'planos' => $planos,
            'trampas' => $trampas,
            'incidencias' => $incidencias,
            'estadisticas' => $estadisticas,
            'fecha_generacion' => date('Y-m-d H:i:s')
        ];

        // Generar la vista HTML para el reporte
        $html = view('reportes/reporte_sede', $data);

        // Configurar opciones de DOMPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isFontSubsettingEnabled', true);
        
        // Crear instancia de DOMPDF
        $dompdf = new Dompdf($options);
        
        // Cargar el HTML
        $dompdf->loadHtml($html);
        
        // Configurar papel y orientación
        $dompdf->setPaper('A4', 'portrait');
        
        // Renderizar el PDF
        $dompdf->render();
        
        // Nombre del archivo
        $filename = 'Reporte_Sede_' . $id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Enviar el archivo al navegador
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    /**
     * Genera un reporte PDF que captura la vista actual con trampas e incidencias
     * 
     * @param int $id ID del plano
     * @return \CodeIgniter\HTTP\Response
     */
    public function capturarVista($id = null)
    {
        if (!$id) {
            return redirect()->to('/blueprints')->with('error', 'Plano no especificado');
        }

        // Cargar modelos
        $planoModel = new PlanoModel();
        $sedeModel = new SedeModel();
        $trampaModel = new TrampaModel();
        $incidenciaModel = new IncidenciaModel();

        // Obtener información del plano
        $plano = $planoModel->find($id);
        if (!$plano) {
            return redirect()->to('/blueprints')->with('error', 'Plano no encontrado');
        }

        // Obtener la imagen del plano y otros datos como lo hace el método verImagen
        $imagenUrl = '';
        if (!empty($plano['archivo'])) {
            try {
                $archivoData = json_decode($plano['archivo'], true);
                if (isset($archivoData['imagen']) && !empty($archivoData['imagen'])) {
                    $imagenUrl = $archivoData['imagen'];
                }
            } catch (\Exception $e) {
                log_message('error', 'Error al procesar la imagen del plano: ' . $e->getMessage());
            }
        }
        
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
            'estadoPlano' => $estadoPlano,
            'es_pdf' => true, // Indicador para la vista de que se está generando un PDF
            'fecha_generacion' => date('Y-m-d H:i:s')
        ];

        // Generar la vista HTML para el reporte (usar la misma vista que para verImagen pero con modificaciones para PDF)
        $html = view('reportes/captura_vista', $data);

        // Configurar opciones de DOMPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isFontSubsettingEnabled', true);
        
        // Crear instancia de DOMPDF
        $dompdf = new Dompdf($options);
        
        // Cargar el HTML
        $dompdf->loadHtml($html);
        
        // Configurar papel y orientación
        $dompdf->setPaper('A4', 'landscape');
        
        // Renderizar el PDF
        $dompdf->render();
        
        // Nombre del archivo
        $filename = 'Captura_Plano_' . $id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Enviar el archivo al navegador
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    /**
     * Genera un reporte PDF combinado de ventas y quejas
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function pdfVentasQuejas()
    {
        // Obtener los filtros (si existen)
        $sedeId = $this->request->getGet('sede_id');
        $fechaInicio = $this->request->getGet('fecha_inicio') ?? date('Y-m-d', strtotime('-3 months'));
        $fechaFin = $this->request->getGet('fecha_fin') ?? date('Y-m-d');
        
        // Cargar modelos
        $ventaModel = new VentaModel();
        $quejaModel = new QuejaModel();
        $sedeModel = new SedeModel();
        $usuarioModel = new UsuarioModel();
        
        // Obtener todas las sedes para el filtro
        $data['sedes'] = $sedeModel->findAll();
        $data['sede_seleccionada'] = $sedeId;
        
        // Si hay una sede seleccionada, obtener su nombre
        if ($sedeId) {
            $sede = $sedeModel->find($sedeId);
            $data['nombre_sede_seleccionada'] = $sede ? $sede['nombre'] : '';
        }
        
        // Condición WHERE para filtros
        $db = \Config\Database::connect();
        $whereSedeVentas = $sedeId ? "AND v.sede_id = $sedeId" : "";
        $whereSedeQuejas = $sedeId ? "AND q.sede_id = $sedeId" : "";
        $whereFechaVentas = " AND v.fecha BETWEEN '$fechaInicio' AND '$fechaFin'";
        $whereFechaQuejas = " AND q.fecha BETWEEN '$fechaInicio' AND '$fechaFin'";
        
        // === DATOS DE VENTAS ===
        
        // Obtener resumen de ventas
        $queryResumenVentas = $db->query("
            SELECT 
                COUNT(*) as total_ventas,
                SUM(monto) as importe_total
            FROM ventas v
            WHERE 1=1 $whereSedeVentas $whereFechaVentas
        ");
        $data['resumenVentas'] = $queryResumenVentas->getRowArray();
        
        // Conceptos más frecuentes
        $queryConceptos = $db->query("
            SELECT 
                concepto,
                COUNT(*) as frecuencia,
                SUM(monto) as importe_total
            FROM ventas v
            WHERE 1=1 $whereSedeVentas $whereFechaVentas
            GROUP BY concepto
            ORDER BY frecuencia DESC
            LIMIT 10
        ");
        $data['estadisticasConceptos'] = $queryConceptos->getResultArray();
        
        // Ventas por mes
        $queryVentasMensual = $db->query("
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as mes,
                COUNT(*) as total,
                SUM(monto) as importe_total
            FROM ventas v
            WHERE 1=1 $whereSedeVentas $whereFechaVentas
            GROUP BY DATE_FORMAT(fecha, '%Y-%m')
            ORDER BY mes
        ");
        $data['ventasMensuales'] = $queryVentasMensual->getResultArray();
        
        // === DATOS DE QUEJAS ===
        
        // Obtener resumen de quejas
        $queryResumenQuejas = $db->query("
            SELECT 
                COUNT(*) as total_quejas,
                SUM(CASE WHEN clasificacion = 'Crítico' THEN 1 ELSE 0 END) as quejas_criticas
            FROM quejas q
            WHERE 1=1 $whereSedeQuejas $whereFechaQuejas
        ");
        $data['resumenQuejas'] = $queryResumenQuejas->getRowArray();
        
        // Distribución por clasificación
        $queryClasificacion = $db->query("
            SELECT 
                clasificacion, 
                COUNT(*) as total
            FROM quejas q
            WHERE 1=1 $whereSedeQuejas $whereFechaQuejas
            GROUP BY clasificacion
            ORDER BY FIELD(clasificacion, 'Crítico', 'Alto', 'Medio', 'Bajo')
        ");
        $data['estadisticasClasificacion'] = $queryClasificacion->getResultArray();
        
        // Tipos de insectos más frecuentes
        $queryInsectos = $db->query("
            SELECT 
                insecto,
                COUNT(*) as frecuencia
            FROM quejas q
            WHERE 1=1 $whereSedeQuejas $whereFechaQuejas
            GROUP BY insecto
            ORDER BY frecuencia DESC
            LIMIT 10
        ");
        $data['estadisticasInsectos'] = $queryInsectos->getResultArray();
        
        // Quejas por mes
        $queryQuejasMensual = $db->query("
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as mes,
                COUNT(*) as total
            FROM quejas q
            WHERE 1=1 $whereSedeQuejas $whereFechaQuejas
            GROUP BY DATE_FORMAT(fecha, '%Y-%m')
            ORDER BY mes
        ");
        $data['quejasMensuales'] = $queryQuejasMensual->getResultArray();
        
        // === DATOS COMBINADOS ===
        
        // Estadísticas por sede (solo si no hay filtro)
        if (!$sedeId) {
            $queryVentasPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_ventas,
                    SUM(v.monto) as importe_total
                FROM ventas v
                JOIN sedes s ON s.id = v.sede_id
                WHERE v.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
                GROUP BY v.sede_id, s.nombre
                ORDER BY total_ventas DESC
            ");
            $data['ventasPorSede'] = $queryVentasPorSede->getResultArray();
            
            $queryQuejasPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_quejas,
                    SUM(CASE WHEN clasificacion = 'Crítico' THEN 1 ELSE 0 END) as quejas_criticas
                FROM quejas q
                JOIN sedes s ON s.id = q.sede_id
                WHERE q.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
                GROUP BY q.sede_id, s.nombre
                ORDER BY total_quejas DESC
            ");
            $data['quejasPorSede'] = $queryQuejasPorSede->getResultArray();
            
            // Combinar estadísticas por sede
            $sedesEstadisticas = [];
            
            foreach ($data['ventasPorSede'] as $ventasPorSede) {
                $sedesEstadisticas[$ventasPorSede['sede']] = [
                    'sede' => $ventasPorSede['sede'],
                    'total_ventas' => $ventasPorSede['total_ventas'],
                    'importe_total' => $ventasPorSede['importe_total'],
                    'total_quejas' => 0,
                    'quejas_criticas' => 0
                ];
            }
            
            foreach ($data['quejasPorSede'] as $quejasPorSede) {
                if (isset($sedesEstadisticas[$quejasPorSede['sede']])) {
                    $sedesEstadisticas[$quejasPorSede['sede']]['total_quejas'] = $quejasPorSede['total_quejas'];
                    $sedesEstadisticas[$quejasPorSede['sede']]['quejas_criticas'] = $quejasPorSede['quejas_criticas'];
                } else {
                    $sedesEstadisticas[$quejasPorSede['sede']] = [
                        'sede' => $quejasPorSede['sede'],
                        'total_ventas' => 0,
                        'importe_total' => 0,
                        'total_quejas' => $quejasPorSede['total_quejas'],
                        'quejas_criticas' => $quejasPorSede['quejas_criticas']
                    ];
                }
            }
            
            $data['sedesEstadisticas'] = array_values($sedesEstadisticas);
        }
        
        // Información de periodo
        $data['fechaInicio'] = $fechaInicio;
        $data['fechaFin'] = $fechaFin;
        
        // Cargar la vista especial para PDF
        $html = view('reportes/pdf_ventas_quejas', $data);

        // Configurar opciones de DOMPDF
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isFontSubsettingEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        
        // Crear instancia de DOMPDF
        $dompdf = new Dompdf($options);
        
        // Cargar el HTML
        $dompdf->loadHtml($html);
        
        // Configurar papel y orientación
        $dompdf->setPaper('A4', 'portrait');
        
        // Renderizar el PDF
        $dompdf->render();
        
        // Nombre del archivo
        $filename = 'Reporte_Ventas_Quejas_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Enviar el archivo al navegador
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    /**
     * Genera un PDF optimizado que se enfoca en la correcta visualización
     * 
     * @param string $tipoReporte Tipo de reporte a generar (ventas, quejas, etc)
     * @return \CodeIgniter\HTTP\Response
     */
    public function generarPDFOptimizado($tipoReporte = 'ventas_quejas')
    {
        // Inicializar respuesta de error
        $errorResponse = function($mensaje) {
            return redirect()->back()->with('error', $mensaje);
        };
        
        // Cargar modelos necesarios según el tipo de reporte
        $db = \Config\Database::connect();
        $sedeModel = new SedeModel();
        
        // Obtener los filtros (si existen)
        $sedeId = $this->request->getGet('sede_id');
        $fechaInicio = $this->request->getGet('fecha_inicio') ?? date('Y-m-d', strtotime('-3 months'));
        $fechaFin = $this->request->getGet('fecha_fin') ?? date('Y-m-d');
        
        // Datos comunes para todos los reportes
        $data = [
            'sedes' => $sedeModel->findAll(),
            'sede_seleccionada' => $sedeId,
            'fechaInicio' => $fechaInicio,
            'fechaFin' => $fechaFin,
            'fecha_generacion' => date('Y-m-d H:i:s')
        ];
        
        // Si hay una sede seleccionada, obtener su nombre
        if ($sedeId) {
            $sede = $sedeModel->find($sedeId);
            $data['nombre_sede_seleccionada'] = $sede ? $sede['nombre'] : '';
        }
        
        // Determinar qué datos cargar según el tipo de reporte
        switch ($tipoReporte) {
            case 'ventas_quejas':
                $data = $this->prepararDatosVentasQuejas($data, $db, $sedeId, $fechaInicio, $fechaFin);
                $vista = 'reportes/pdf_optim_ventas_quejas';
                $nombreArchivo = 'Reporte_Ventas_Quejas_';
                break;
                
            case 'ventas':
                $data = $this->prepararDatosVentas($data, $db, $sedeId, $fechaInicio, $fechaFin);
                $vista = 'reportes/pdf_optim_ventas';
                $nombreArchivo = 'Reporte_Ventas_';
                break;
                
            case 'quejas':
                $data = $this->prepararDatosQuejas($data, $db, $sedeId, $fechaInicio, $fechaFin);
                $vista = 'reportes/pdf_optim_quejas';
                $nombreArchivo = 'Reporte_Quejas_';
                break;
                
            default:
                return $errorResponse('Tipo de reporte no válido');
        }
        
        // Generar la vista HTML para el reporte con un diseño optimizado para PDF
        try {
            $html = view($vista, $data);
        } catch (\Exception $e) {
            log_message('error', 'Error al cargar la vista para PDF: ' . $e->getMessage());
            return $errorResponse('Error al generar el PDF: ' . $e->getMessage());
        }
        
        // Configurar opciones de DOMPDF para máxima compatibilidad
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', false); // Desactivar PHP para evitar problemas
        $options->set('isFontSubsettingEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('defaultMediaType', 'print');
        $options->set('debugKeepTemp', false);
        $options->set('debugCss', false);
        
        // Crear instancia de DOMPDF con opciones optimizadas
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Establecer tamaño de papel y orientación
        $dompdf->setPaper('A4', 'portrait');
        
        // Cargar el HTML y renderizar
        try {
            $dompdf->loadHtml($html);
            $dompdf->render();
        } catch (\Exception $e) {
            log_message('error', 'Error al renderizar PDF: ' . $e->getMessage());
            return $errorResponse('Error al renderizar el PDF: ' . $e->getMessage());
        }
        
        // Nombre del archivo con fecha y hora
        $filename = $nombreArchivo . date('Y-m-d_H-i-s') . '.pdf';
        
        // Forzar la descarga del archivo en lugar de mostrar en el navegador
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }
    
    /**
     * Prepara los datos para el reporte combinado de ventas y quejas
     */
    private function prepararDatosVentasQuejas($data, $db, $sedeId, $fechaInicio, $fechaFin)
    {
        // Condición WHERE para filtros
        $whereSedeVentas = $sedeId ? "AND v.sede_id = $sedeId" : "";
        $whereSedeQuejas = $sedeId ? "AND q.sede_id = $sedeId" : "";
        $whereFechaVentas = " AND v.fecha BETWEEN '$fechaInicio' AND '$fechaFin'";
        $whereFechaQuejas = " AND q.fecha BETWEEN '$fechaInicio' AND '$fechaFin'";
        
        // === DATOS DE VENTAS ===
        
        // Obtener resumen de ventas
        $queryResumenVentas = $db->query("
            SELECT 
                COUNT(*) as total_ventas,
                SUM(monto) as importe_total
            FROM ventas v
            WHERE 1=1 $whereSedeVentas $whereFechaVentas
        ");
        $data['resumenVentas'] = $queryResumenVentas->getRowArray();
        
        // Conceptos más frecuentes
        $queryConceptos = $db->query("
            SELECT 
                concepto,
                COUNT(*) as frecuencia,
                SUM(monto) as importe_total
            FROM ventas v
            WHERE 1=1 $whereSedeVentas $whereFechaVentas
            GROUP BY concepto
            ORDER BY frecuencia DESC
            LIMIT 10
        ");
        $data['estadisticasConceptos'] = $queryConceptos->getResultArray();
        
        // Ventas por mes
        $queryVentasMensual = $db->query("
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as mes,
                COUNT(*) as total,
                SUM(monto) as importe_total
            FROM ventas v
            WHERE 1=1 $whereSedeVentas $whereFechaVentas
            GROUP BY DATE_FORMAT(fecha, '%Y-%m')
            ORDER BY mes
        ");
        $data['ventasMensuales'] = $queryVentasMensual->getResultArray();
        
        // === DATOS DE QUEJAS ===
        
        // Obtener resumen de quejas
        $queryResumenQuejas = $db->query("
            SELECT 
                COUNT(*) as total_quejas,
                SUM(CASE WHEN clasificacion = 'Crítico' THEN 1 ELSE 0 END) as quejas_criticas
            FROM quejas q
            WHERE 1=1 $whereSedeQuejas $whereFechaQuejas
        ");
        $data['resumenQuejas'] = $queryResumenQuejas->getRowArray();
        
        // Distribución por clasificación
        $queryClasificacion = $db->query("
            SELECT 
                clasificacion, 
                COUNT(*) as total
            FROM quejas q
            WHERE 1=1 $whereSedeQuejas $whereFechaQuejas
            GROUP BY clasificacion
            ORDER BY FIELD(clasificacion, 'Crítico', 'Alto', 'Medio', 'Bajo')
        ");
        $data['estadisticasClasificacion'] = $queryClasificacion->getResultArray();
        
        // Tipos de insectos más frecuentes
        $queryInsectos = $db->query("
            SELECT 
                insecto,
                COUNT(*) as frecuencia
            FROM quejas q
            WHERE 1=1 $whereSedeQuejas $whereFechaQuejas
            GROUP BY insecto
            ORDER BY frecuencia DESC
            LIMIT 10
        ");
        $data['estadisticasInsectos'] = $queryInsectos->getResultArray();
        
        // Quejas por mes
        $queryQuejasMensual = $db->query("
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as mes,
                COUNT(*) as total
            FROM quejas q
            WHERE 1=1 $whereSedeQuejas $whereFechaQuejas
            GROUP BY DATE_FORMAT(fecha, '%Y-%m')
            ORDER BY mes
        ");
        $data['quejasMensuales'] = $queryQuejasMensual->getResultArray();
        
        // === DATOS COMBINADOS ===
        
        // Estadísticas por sede (solo si no hay filtro)
        if (!$sedeId) {
            $queryVentasPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_ventas,
                    SUM(v.monto) as importe_total
                FROM ventas v
                JOIN sedes s ON s.id = v.sede_id
                WHERE v.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
                GROUP BY v.sede_id, s.nombre
                ORDER BY total_ventas DESC
            ");
            $data['ventasPorSede'] = $queryVentasPorSede->getResultArray();
            
            $queryQuejasPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_quejas,
                    SUM(CASE WHEN clasificacion = 'Crítico' THEN 1 ELSE 0 END) as quejas_criticas
                FROM quejas q
                JOIN sedes s ON s.id = q.sede_id
                WHERE q.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
                GROUP BY q.sede_id, s.nombre
                ORDER BY total_quejas DESC
            ");
            $data['quejasPorSede'] = $queryQuejasPorSede->getResultArray();
            
            // Combinar estadísticas por sede
            $sedesEstadisticas = [];
            
            foreach ($data['ventasPorSede'] as $ventasPorSede) {
                $sedesEstadisticas[$ventasPorSede['sede']] = [
                    'sede' => $ventasPorSede['sede'],
                    'total_ventas' => $ventasPorSede['total_ventas'],
                    'importe_total' => $ventasPorSede['importe_total'],
                    'total_quejas' => 0,
                    'quejas_criticas' => 0
                ];
            }
            
            foreach ($data['quejasPorSede'] as $quejasPorSede) {
                if (isset($sedesEstadisticas[$quejasPorSede['sede']])) {
                    $sedesEstadisticas[$quejasPorSede['sede']]['total_quejas'] = $quejasPorSede['total_quejas'];
                    $sedesEstadisticas[$quejasPorSede['sede']]['quejas_criticas'] = $quejasPorSede['quejas_criticas'];
                } else {
                    $sedesEstadisticas[$quejasPorSede['sede']] = [
                        'sede' => $quejasPorSede['sede'],
                        'total_ventas' => 0,
                        'importe_total' => 0,
                        'total_quejas' => $quejasPorSede['total_quejas'],
                        'quejas_criticas' => $quejasPorSede['quejas_criticas']
                    ];
                }
            }
            
            $data['sedesEstadisticas'] = array_values($sedesEstadisticas);
        }
        
        return $data;
    }
    
    /**
     * Prepara los datos para el reporte de ventas
     */
    private function prepararDatosVentas($data, $db, $sedeId, $fechaInicio, $fechaFin)
    {
        // Condición WHERE para filtros
        $whereSede = $sedeId ? "AND v.sede_id = $sedeId" : "";
        $whereFecha = " AND v.fecha BETWEEN '$fechaInicio' AND '$fechaFin'";
        
        // Obtener resumen de ventas
        $queryResumen = $db->query("
            SELECT 
                COUNT(*) as total_ventas,
                SUM(monto) as importe_total,
                AVG(monto) as promedio_venta,
                MIN(monto) as venta_minima,
                MAX(monto) as venta_maxima
            FROM ventas v
            WHERE 1=1 $whereSede $whereFecha
        ");
        $data['resumenVentas'] = $queryResumen->getRowArray();
        
        // Conceptos más frecuentes
        $queryConceptos = $db->query("
            SELECT 
                concepto,
                COUNT(*) as frecuencia,
                SUM(monto) as importe_total
            FROM ventas v
            WHERE 1=1 $whereSede $whereFecha
            GROUP BY concepto
            ORDER BY frecuencia DESC
            LIMIT 10
        ");
        $data['estadisticasConceptos'] = $queryConceptos->getResultArray();
        
        // Ventas por mes
        $queryMensual = $db->query("
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as mes,
                COUNT(*) as total,
                SUM(monto) as importe_total
            FROM ventas v
            WHERE 1=1 $whereSede $whereFecha
            GROUP BY DATE_FORMAT(fecha, '%Y-%m')
            ORDER BY mes
        ");
        $data['ventasMensuales'] = $queryMensual->getResultArray();
        
        // Ventas por sede (solo si no hay filtro)
        if (!$sedeId) {
            $queryPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_ventas,
                    SUM(v.monto) as importe_total
                FROM ventas v
                JOIN sedes s ON s.id = v.sede_id
                WHERE v.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
                GROUP BY v.sede_id, s.nombre
                ORDER BY total_ventas DESC
            ");
            $data['ventasPorSede'] = $queryPorSede->getResultArray();
        }
        
        return $data;
    }
    
    /**
     * Prepara los datos para el reporte de quejas
     */
    private function prepararDatosQuejas($data, $db, $sedeId, $fechaInicio, $fechaFin)
    {
        // Condición WHERE para filtros
        $whereSede = $sedeId ? "AND q.sede_id = $sedeId" : "";
        $whereFecha = " AND q.fecha BETWEEN '$fechaInicio' AND '$fechaFin'";
        
        // Obtener resumen de quejas
        $queryResumen = $db->query("
            SELECT 
                COUNT(*) as total_quejas,
                SUM(CASE WHEN clasificacion = 'Crítico' THEN 1 ELSE 0 END) as quejas_criticas,
                SUM(CASE WHEN clasificacion = 'Alto' THEN 1 ELSE 0 END) as quejas_altas,
                SUM(CASE WHEN clasificacion = 'Medio' THEN 1 ELSE 0 END) as quejas_medias,
                SUM(CASE WHEN clasificacion = 'Bajo' THEN 1 ELSE 0 END) as quejas_bajas
            FROM quejas q
            WHERE 1=1 $whereSede $whereFecha
        ");
        $data['resumenQuejas'] = $queryResumen->getRowArray();
        
        // Distribución por clasificación
        $queryClasificacion = $db->query("
            SELECT 
                clasificacion, 
                COUNT(*) as total
            FROM quejas q
            WHERE 1=1 $whereSede $whereFecha
            GROUP BY clasificacion
            ORDER BY FIELD(clasificacion, 'Crítico', 'Alto', 'Medio', 'Bajo')
        ");
        $data['estadisticasClasificacion'] = $queryClasificacion->getResultArray();
        
        // Tipos de insectos más frecuentes
        $queryInsectos = $db->query("
            SELECT 
                insecto,
                COUNT(*) as frecuencia
            FROM quejas q
            WHERE 1=1 $whereSede $whereFecha
            GROUP BY insecto
            ORDER BY frecuencia DESC
            LIMIT 10
        ");
        $data['estadisticasInsectos'] = $queryInsectos->getResultArray();
        
        // Quejas por mes
        $queryMensual = $db->query("
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as mes,
                COUNT(*) as total
            FROM quejas q
            WHERE 1=1 $whereSede $whereFecha
            GROUP BY DATE_FORMAT(fecha, '%Y-%m')
            ORDER BY mes
        ");
        $data['quejasMensuales'] = $queryMensual->getResultArray();
        
        // Quejas por sede (solo si no hay filtro)
        if (!$sedeId) {
            $queryPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_quejas,
                    SUM(CASE WHEN clasificacion = 'Crítico' THEN 1 ELSE 0 END) as quejas_criticas
                FROM quejas q
                JOIN sedes s ON s.id = q.sede_id
                WHERE q.fecha BETWEEN '$fechaInicio' AND '$fechaFin'
                GROUP BY q.sede_id, s.nombre
                ORDER BY total_quejas DESC
            ");
            $data['quejasPorSede'] = $queryPorSede->getResultArray();
        }
        
        return $data;
    }
} 