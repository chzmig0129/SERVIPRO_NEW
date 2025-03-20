<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\PlanoModel;
use App\Models\SedeModel;
use App\Models\TrampaModel;
use App\Models\IncidenciaModel;

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
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
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
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
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
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }
} 