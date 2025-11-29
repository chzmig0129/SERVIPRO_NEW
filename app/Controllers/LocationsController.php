<?php
namespace App\Controllers;

use App\Models\SedeModel;
use App\Models\TrampaModel;
use App\Models\IncidenciaModel;
use App\Models\PlanoModel;

class LocationsController extends BaseController
{
    /**
     * Genera un PDF con las estadísticas de locations
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function generarPDF()
    {
        $db = \Config\Database::connect();
        $sedeModel = new SedeModel();
        $trampaModel = new TrampaModel();
        $incidenciaModel = new IncidenciaModel();
        $planoModel = new PlanoModel();

        // Obtener todas las sedes para el filtro
        $data['sedes'] = $sedeModel->findAll();
        $sedeSeleccionada = $this->request->getGet('sede_id');
        if (empty($sedeSeleccionada) && !empty($data['sedes'])) {
            $sedeSeleccionada = $data['sedes'][0]['id'];
        }
        $data['sedeSeleccionada'] = $sedeSeleccionada;

        // Inicializar datos
        $data['totalTrampasSede'] = 0;
        $data['trampasDetalle'] = [];
        $data['totalIncidenciasPorTipo'] = [];
        $data['totalCapturas'] = 0;
        $data['efectividad'] = 0;
        $data['planos'] = [];
        $data['trampasPorUbicacion'] = [];
        $data['listaMeses'] = [];
        $data['sedeSeleccionadaNombre'] = '';
        $data['mensaje_error'] = '';

        try {
            // Obtener información de la sede seleccionada
            if ($sedeSeleccionada) {
                $sede = $sedeModel->find($sedeSeleccionada);
                $data['sedeSeleccionadaNombre'] = $sede ? $sede['nombre'] : '';
            }

            // Obtener total de trampas en la sede
            $builder = $db->table('trampas')->where('sede_id', $sedeSeleccionada);
            $data['totalTrampasSede'] = $builder->countAllResults(false);

            // Obtener detalle de trampas
            $query = $db->table('trampas t')
                ->select('t.id, t.nombre, t.tipo, t.ubicacion, t.fecha_instalacion, t.plano_id')
                ->where('t.sede_id', $sedeSeleccionada)
                ->get();
            $data['trampasDetalle'] = $query->getResultArray();

            // Obtener total de incidencias por tipo
            $query = $db->table('incidencias i')
                ->select('i.tipo_incidencia, COUNT(*) as total')
                ->join('trampas t', 'i.id_trampa = t.id')
                ->where('t.sede_id', $sedeSeleccionada)
                ->groupBy('i.tipo_incidencia')
                ->get();
            $data['totalIncidenciasPorTipo'] = $query->getResultArray();

            // Obtener todas las incidencias para la tabla detallada
            $query = $db->table('incidencias i')
                ->select('i.tipo_incidencia, i.tipo_plaga, i.cantidad_organismos, i.tipo_insecto, i.fecha')
                ->join('trampas t', 'i.id_trampa = t.id')
                ->where('t.sede_id', $sedeSeleccionada)
                ->orderBy('i.fecha', 'DESC')
                ->get();
            $data['todasLasIncidencias'] = $query->getResultArray();

            // Obtener todas las trampas para la tabla detallada
            $query = $db->table('trampas t')
                ->select('t.id, t.nombre, t.tipo, t.ubicacion, t.fecha_instalacion')
                ->where('t.sede_id', $sedeSeleccionada)
                ->orderBy('t.id')
                ->get();
            $data['todasLasTrampas'] = $query->getResultArray();

            // Obtener trampas por ubicación
            $query = $db->table('trampas')
                ->select('ubicacion, COUNT(*) as total_trampas')
                ->where('sede_id', $sedeSeleccionada)
                ->groupBy('ubicacion')
                ->orderBy('total_trampas', 'DESC')
                ->get();
            $data['trampasPorUbicacion'] = $query->getResultArray();

            // Obtener incidencias por tipo para estadísticas
            $query = $db->table('incidencias i')
                ->select('i.tipo_incidencia, COUNT(*) as total')
                ->join('trampas t', 'i.id_trampa = t.id')
                ->where('t.sede_id', $sedeSeleccionada)
                ->groupBy('i.tipo_incidencia')
                ->get();
            $data['incidenciasPorTipo'] = $query->getResultArray();

            // Obtener todos los planos de la sede
            $query = $db->table('planos p')
                ->select('p.*, s.nombre as sede_nombre')
                ->join('sedes s', 's.id = p.sede_id', 'left')
                ->where('p.sede_id', $sedeSeleccionada)
                ->orderBy('p.nombre')
                ->get();
            $data['todosLosPlanos'] = $query->getResultArray();

            // Lista de meses disponibles para filtro
            $query = $db->table('incidencias i')
                ->select("DISTINCT(DATE_FORMAT(i.fecha, '%Y-%m')) as mes_valor, DATE_FORMAT(i.fecha, '%Y-%m') as mes_fecha")
                ->join('trampas t', 'i.id_trampa = t.id')
                ->where('t.sede_id', $sedeSeleccionada)
                ->orderBy('i.fecha', 'DESC')
                ->get();
            $listaMeses = $query->getResultArray();
            $mesesEspanol = [
                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
                '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
                '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
            ];
            foreach ($listaMeses as &$mes) {
                $partesFecha = explode('-', $mes['mes_fecha']);
                $numeroMes = $partesFecha[1];
                $anio = $partesFecha[0];
                $mes['mes_nombre'] = $mesesEspanol[$numeroMes] . ' ' . $anio;
            }
            $data['listaMeses'] = $listaMeses;

            // Calcular totales para el resumen
            $data['totalIncidencias'] = count($data['todasLasIncidencias']);
            $data['totalPlanos'] = count($data['todosLosPlanos']);

        } catch (\Exception $e) {
            $data['mensaje_error'] = "Error al procesar datos de la sede: " . $e->getMessage();
        }

        // Procesar imágenes de gráficos si se recibieron
        $chartImages = $this->request->getPost('chart_images');
        $usarChartImages = !empty($chartImages);
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
        if (!empty($accionesSeguimiento)) {
            $data['acciones_seguimiento'] = $accionesSeguimiento;
        }

        // Cargar la vista especial para PDF
        $html = view('locations/pdf_estadisticas', $data);

        // Configurar opciones de DOMPDF
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isFontSubsettingEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $filename = 'Reporte_sede_' . date('Y-m-d_H-i-s') . '.pdf';
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