<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Models\PlanoModel;
use App\Models\SedeModel;
use App\Models\TrampaModel;
use App\Models\IncidenciaModel;

class Reports extends BaseController
{
    /**
     * Genera un reporte PDF de las trampas de un plano con filtros aplicados
     * 
     * @param int $id ID del plano
     * @return \CodeIgniter\HTTP\Response
     */
    public function pdf_trampas($id = null)
    {
        if (!$id) {
            return redirect()->to('/blueprints')->with('error', 'Plano no especificado');
        }

        // Obtener filtros de la URL
        $filtroPlaga = $this->request->getGet('filtro_plaga') ?? 'todos';
        $filtroIncidencia = $this->request->getGet('filtro_incidencia') ?? 'todos';

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

        // Obtener información de la sede asociada
        $sede = $sedeModel->find($plano['sede_id']);
        
        // Obtener las trampas asociadas al plano
        $trampas = $trampaModel->where('plano_id', $id)->findAll();
        
        // Obtener las incidencias asociadas a las trampas de este plano
        $incidencias = [];
        $incidenciasFiltradas = [];
        if (!empty($trampas)) {
            $trampaIds = array_column($trampas, 'id');
            $incidencias = $incidenciaModel->whereIn('id_trampa', $trampaIds)->findAll();
            
            // Aplicar filtros
            foreach ($incidencias as $incidencia) {
                $tipoPlaga = strtolower($incidencia['tipo_plaga'] ?? 'otro');
                $tipoIncidencia = $incidencia['tipo_incidencia'] ?? 'Captura';
                
                $pasaFiltroPlaga = $filtroPlaga === 'todos' || $tipoPlaga === $filtroPlaga;
                $pasaFiltroIncidencia = $filtroIncidencia === 'todos' || $tipoIncidencia === $filtroIncidencia;
                
                if ($pasaFiltroPlaga && $pasaFiltroIncidencia) {
                    $incidenciasFiltradas[] = $incidencia;
                }
            }
        }

        // Preparar datos para la vista
        $data = [
            'plano' => $plano,
            'sede' => $sede,
            'trampas' => $trampas,
            'incidencias' => $incidenciasFiltradas, // Usamos las incidencias filtradas
            'filtroPlaga' => $filtroPlaga,
            'filtroIncidencia' => $filtroIncidencia,
            'fecha_generacion' => date('Y-m-d H:i:s')
        ];

        // Generar la vista HTML para el reporte
        $html = view('reportes/pdf_trampas', $data);

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
        $filename = 'Reporte_Trampas_' . $id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Enviar el archivo al navegador
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    /**
     * Genera un reporte PDF de incidencias de un plano con filtros aplicados
     * 
     * @param int $id ID del plano
     * @return \CodeIgniter\HTTP\Response
     */
    public function pdf_incidencias($id = null)
    {
        if (!$id) {
            return redirect()->to('/blueprints')->with('error', 'Plano no especificado');
        }

        // Obtener filtros de la URL
        $filtroPlaga = $this->request->getGet('filtro_plaga') ?? 'todos';
        $filtroIncidencia = $this->request->getGet('filtro_incidencia') ?? 'todos';

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

        // Obtener información de la sede asociada
        $sede = $sedeModel->find($plano['sede_id']);
        
        // Obtener las trampas asociadas al plano
        $trampas = $trampaModel->where('plano_id', $id)->findAll();
        
        // Obtener las incidencias asociadas a las trampas de este plano
        $incidencias = [];
        $incidenciasFiltradas = [];
        if (!empty($trampas)) {
            $trampaIds = array_column($trampas, 'id');
            $incidencias = $incidenciaModel->whereIn('id_trampa', $trampaIds)->findAll();
            
            // Aplicar filtros
            foreach ($incidencias as $incidencia) {
                $tipoPlaga = strtolower($incidencia['tipo_plaga'] ?? 'otro');
                $tipoIncidencia = $incidencia['tipo_incidencia'] ?? 'Captura';
                
                $pasaFiltroPlaga = $filtroPlaga === 'todos' || $tipoPlaga === $filtroPlaga;
                $pasaFiltroIncidencia = $filtroIncidencia === 'todos' || $tipoIncidencia === $filtroIncidencia;
                
                if ($pasaFiltroPlaga && $pasaFiltroIncidencia) {
                    $incidenciasFiltradas[] = $incidencia;
                }
            }
        }

        // Preparar datos para la vista
        $data = [
            'plano' => $plano,
            'sede' => $sede,
            'trampas' => $trampas,
            'incidencias' => $incidenciasFiltradas, // Usamos las incidencias filtradas
            'filtroPlaga' => $filtroPlaga,
            'filtroIncidencia' => $filtroIncidencia,
            'fecha_generacion' => date('Y-m-d H:i:s')
        ];

        // Generar la vista HTML para el reporte
        $html = view('reportes/pdf_incidencias', $data);

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
        $filename = 'Reporte_Incidencias_' . $id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Enviar el archivo al navegador
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    /**
     * Genera un reporte PDF completo de un plano con filtros aplicados
     * 
     * @param int $id ID del plano
     * @return \CodeIgniter\HTTP\Response
     */
    public function pdf_completo($id = null)
    {
        if (!$id) {
            return redirect()->to('/blueprints')->with('error', 'Plano no especificado');
        }

        // Obtener filtros de la URL
        $filtroPlaga = $this->request->getGet('filtro_plaga') ?? 'todos';
        $filtroIncidencia = $this->request->getGet('filtro_incidencia') ?? 'todos';

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
        $incidenciasFiltradas = [];
        if (!empty($trampas)) {
            $trampaIds = array_column($trampas, 'id');
            $incidencias = $incidenciaModel->whereIn('id_trampa', $trampaIds)->findAll();
            
            // Aplicar filtros
            foreach ($incidencias as $incidencia) {
                $tipoPlaga = strtolower($incidencia['tipo_plaga'] ?? 'otro');
                $tipoIncidencia = $incidencia['tipo_incidencia'] ?? 'Captura';
                
                $pasaFiltroPlaga = $filtroPlaga === 'todos' || $tipoPlaga === $filtroPlaga;
                $pasaFiltroIncidencia = $filtroIncidencia === 'todos' || $tipoIncidencia === $filtroIncidencia;
                
                if ($pasaFiltroPlaga && $pasaFiltroIncidencia) {
                    $incidenciasFiltradas[] = $incidencia;
                }
            }
        }

        // Preparar datos para la vista
        $data = [
            'plano' => $plano,
            'sede' => $sede,
            'imagen_url' => $imagenUrl,
            'trampas' => $trampas,
            'incidencias' => $incidenciasFiltradas, // Usamos las incidencias filtradas
            'filtroPlaga' => $filtroPlaga,
            'filtroIncidencia' => $filtroIncidencia,
            'fecha_generacion' => date('Y-m-d H:i:s')
        ];

        // Generar la vista HTML para el reporte
        $html = view('reportes/pdf_completo', $data);

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
        $filename = 'Reporte_Completo_' . $id . '_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Enviar el archivo al navegador
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }

    /**
     * Genera un reporte PDF de una visita específica, mostrando trampas limpias e incidencias
     * 
     * @param int $planoId ID del plano (opcional)
     * @return \CodeIgniter\HTTP\Response
     */
    public function reporte_visita()
    {
        // Obtener parámetros de filtro
        $planoId = $this->request->getGet('plano_id');
        $fecha = $this->request->getGet('fecha') ?? date('Y-m-d');
        
        // Validar que se haya proporcionado un plano
        if (!$planoId) {
            return redirect()->to('/blueprints')->with('error', 'Debe especificar un plano para generar el reporte de visita');
        }
        
        // Cargar modelos necesarios
        $planoModel = new PlanoModel();
        $sedeModel = new SedeModel();
        $trampaModel = new TrampaModel();
        $incidenciaModel = new IncidenciaModel();
        
        // Obtener información del plano
        $plano = $planoModel->find($planoId);
        if (!$plano) {
            return redirect()->to('/blueprints')->with('error', 'Plano no encontrado');
        }
        
        // Obtener información de la sede
        $sede = $sedeModel->find($plano['sede_id']);
        
        // Obtener todas las trampas del plano
        $todasTrampas = $trampaModel->where('plano_id', $planoId)->findAll();
        
        // Obtener incidencias del día especificado
        $incidenciasDia = $incidenciaModel
            ->where('DATE(fecha)', $fecha)
            ->whereIn('id_trampa', array_column($todasTrampas, 'id'))
            ->findAll();
        
        // Identificar las trampas que tuvieron incidencias
        $trampaConIncidencia = [];
        foreach ($incidenciasDia as $incidencia) {
            $trampaConIncidencia[$incidencia['id_trampa']] = true;
        }
        
        // Separar las trampas limpias (sin incidencias) y las que tuvieron incidencias
        $trampasLimpias = [];
        $trampasConIncidencias = [];
        
        foreach ($todasTrampas as $trampa) {
            if (isset($trampaConIncidencia[$trampa['id']])) {
                $trampasConIncidencias[] = $trampa;
            } else {
                $trampasLimpias[] = $trampa;
            }
        }
        
        // Preparar los datos para la vista
        $data = [
            'plano' => $plano,
            'sede' => $sede,
            'fecha_visita' => $fecha,
            'trampas_limpias' => $trampasLimpias,
            'trampas_con_incidencias' => $trampasConIncidencias,
            'incidencias' => $incidenciasDia,
            'todasTrampas' => $todasTrampas,
            'fecha_generacion' => date('Y-m-d H:i:s')
        ];
        
        // Generar la vista HTML para el reporte
        $html = view('reportes/reporte_visita', $data);
        
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
        $fechaFormateada = date('Y-m-d', strtotime($fecha));
        $filename = 'Reporte_Visita_' . $plano['nombre'] . '_' . $fechaFormateada . '.pdf';
        
        // Enviar el archivo al navegador
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }
} 