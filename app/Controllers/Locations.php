<?php
namespace App\Controllers;

use App\Models\SedeModel;
use App\Models\TrampaModel;
use App\Models\IncidenciaModel;
use App\Models\UmbralModel;
use App\Models\RepositorioDocumentoModel;
use CodeIgniter\I18n\Time;

class Locations extends BaseController
{
    public function index(): string
    {
        // Verificar si el usuario ha iniciado sesión
        $this->verificarSesion();
        
        // Agregar logs para depuración
        log_message('debug', 'Iniciando carga de sedes');
        
        // Cargar el modelo de sedes
        try {
            $sedeModel = new SedeModel();
            
            // Verificar si la clase SedeModel está completa
            // Obtener solo las sedes activas (estatus = 1)
            $data['sedes'] = $sedeModel->where('estatus', 1)->findAll();
            log_message('debug', 'Sedes cargadas: ' . json_encode($data['sedes']));
        } catch (\Exception $e) {
            log_message('error', 'Error al cargar sedes: ' . $e->getMessage());
            $data['sedes'] = [];
            $data['mensaje_error'] = "Error al cargar las sedes: " . $e->getMessage();
            return view('locations/index', $data);
        }

        // Cargar el modelo de trampas
        $trampaModel = new TrampaModel();

        // Si hay una sede seleccionada (por defecto la primera)
        $sedeSeleccionada = $this->request->getGet('sede_id');
        if (empty($sedeSeleccionada) && !empty($data['sedes'])) {
            $sedeSeleccionada = $data['sedes'][0]['id'];
        }

        // Verificar si la sede seleccionada es válida
        if (empty($sedeSeleccionada)) {
            $data['mensaje_error'] = "No hay sede seleccionada.";
            return view('locations/index', $data);
        }

        $data['sedeSeleccionada'] = $sedeSeleccionada;
        $db = \Config\Database::connect();

        // Inicializar datos para evitar errores en la vista
        $data['totalTrampasSede'] = 0;
        $data['trampasDetalle'] = [];
        $data['totalIncidenciasPorTipo'] = [];
        $data['totalCapturas'] = 0;
        $data['efectividad'] = 0;
        $data['capturasPorMes'] = [];
        
        // Obtener filtros de fecha para gráficas
        $fechaInicio = $this->request->getGet('fecha_inicio');
        $fechaFin = $this->request->getGet('fecha_fin');
        
        // Agregar las fechas a los datos para la vista
        $data['fechaInicio'] = $fechaInicio;
        $data['fechaFin'] = $fechaFin;
        
        // Preparar condición de fecha para las consultas
        $condicionFecha = '';
        if (!empty($fechaInicio) && !empty($fechaFin)) {
            // El formato de fechaInicio y fechaFin debe ser YYYY-MM-DD
            // Creamos la condición SQL para filtrar por fecha
            $condicionFecha = " AND i.fecha BETWEEN '{$fechaInicio} 00:00:00' AND '{$fechaFin} 23:59:59'";
            log_message('debug', 'Filtro de fecha aplicado: ' . $condicionFecha);
        }

        try {
            // Obtener el total de trampas para la sede
            $builder = $db->table('trampas')->where('sede_id', $sedeSeleccionada);
            $data['totalTrampasSede'] = $builder->countAllResults(false);

            // Obtener el detalle de las trampas (nombre, tipo y ubicación)
            $query = $db->table('trampas')
                ->select('id, id_trampa, nombre, tipo, ubicacion, fecha_instalacion, plano_id')
                ->where('sede_id', $sedeSeleccionada)
                ->get();
            $data['trampasDetalle'] = $query->getResultArray();

            // Base query para incidencias con filtro de fecha
            $baseQuery = "sede_id = {$sedeSeleccionada}";
            if (!empty($condicionFecha)) {
                $baseQuery .= $condicionFecha;
            }
            
            // Obtener el total de incidencias agrupadas por tipo_incidencia y tipo_plaga
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            if (empty($condicionFecha)) {
                // Sin filtro de fecha
                $query = $db->table('incidencias i')
                    ->select('i.tipo_incidencia, i.tipo_insecto, i.tipo_plaga, SUM(i.cantidad_organismos) as cantidad_organismos, COUNT(*) as total')
                    ->join('trampas t', 'i.id_trampa = t.id', 'inner')
                    ->where('i.sede_id', $sedeSeleccionada)
                    ->where('t.sede_id', $sedeSeleccionada) // Asegurar que la trampa pertenece a la sede seleccionada
                    ->groupBy(['i.tipo_incidencia', 'i.tipo_plaga'])
                    ->get();
            } else {
                // Con filtro de fecha
                $query = $db->query("
                    SELECT i.tipo_incidencia, i.tipo_insecto, i.tipo_plaga, 
                           SUM(i.cantidad_organismos) as cantidad_organismos, 
                           COUNT(*) as total
                    FROM incidencias i
                    INNER JOIN trampas t ON i.id_trampa = t.id
                    WHERE i.sede_id = {$sedeSeleccionada} 
                    AND t.sede_id = {$sedeSeleccionada}
                    {$condicionFecha}
                    GROUP BY i.tipo_incidencia, i.tipo_plaga
                ");
            }
            $data['totalIncidenciasPorTipo'] = $query->getResultArray();

            // Obtener todas las incidencias sin agrupar para la tabla "Detalle de Incidencias"
            // Incluir JOIN con trampas para obtener ID de trampa, tipo de trampa y plano_id
            // Usar INNER JOIN para asegurar que solo se muestren incidencias con trampas válidas de la sede
            if (empty($condicionFecha)) {
                // Sin filtro de fecha
                $query = $db->table('incidencias i')
                    ->select('i.id, i.tipo_incidencia, i.tipo_insecto, i.tipo_plaga, i.cantidad_organismos, i.fecha, t.id_trampa, t.tipo as tipo_trampa, t.plano_id')
                    ->join('trampas t', 'i.id_trampa = t.id', 'inner')
                    ->where('i.sede_id', $sedeSeleccionada)
                    ->where('t.sede_id', $sedeSeleccionada) // Asegurar que la trampa pertenece a la sede seleccionada
                    ->orderBy('i.fecha', 'DESC')
                    ->get();
            } else {
                // Con filtro de fecha
                $query = $db->query("
                    SELECT i.id, i.tipo_incidencia, i.tipo_insecto, i.tipo_plaga, i.cantidad_organismos, i.fecha, 
                           t.id_trampa, t.tipo as tipo_trampa, t.plano_id
                    FROM incidencias i
                    INNER JOIN trampas t ON i.id_trampa = t.id
                    WHERE i.sede_id = {$sedeSeleccionada} 
                    AND t.sede_id = {$sedeSeleccionada}
                    {$condicionFecha}
                    ORDER BY i.fecha DESC
                ");
            }
            $data['todasLasIncidencias'] = $query->getResultArray();

            // Obtener conteo de incidencias por tipo (Hallazgo y Captura)
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            if (empty($condicionFecha)) {
                // Sin filtro de fecha
                $query = $db->table('incidencias i')
                    ->select('i.tipo_incidencia, COUNT(*) as total')
                    ->join('trampas t', 'i.id_trampa = t.id', 'inner')
                    ->where('i.sede_id', $sedeSeleccionada)
                    ->where('t.sede_id', $sedeSeleccionada)
                    ->groupBy('i.tipo_incidencia')
                    ->get();
            } else {
                // Con filtro de fecha
                $query = $db->query("
                    SELECT i.tipo_incidencia, COUNT(*) as total
                    FROM incidencias i
                    INNER JOIN trampas t ON i.id_trampa = t.id
                    WHERE i.sede_id = {$sedeSeleccionada} 
                    AND t.sede_id = {$sedeSeleccionada}
                    {$condicionFecha}
                    GROUP BY i.tipo_incidencia
                ");
            }
            $conteoPorTipo = $query->getResultArray();
            
            // Inicializar contadores
            $data['totalHallazgos'] = 0;
            $data['totalCapturas'] = 0;
            
            // Procesar resultados
            foreach ($conteoPorTipo as $item) {
                if (strtolower($item['tipo_incidencia']) === 'hallazgo') {
                    $data['totalHallazgos'] = (int)$item['total'];
                } elseif (strtolower($item['tipo_incidencia']) === 'captura') {
                    $data['totalCapturas'] = (int)$item['total'];
                }
            }

            // Obtener el total de capturas (solo incidencias de tipo "Captura")
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            $capturaQuery = "
                SELECT COUNT(*) as totalCapturas
                FROM incidencias i
                INNER JOIN trampas t ON i.id_trampa = t.id
                WHERE i.sede_id = {$sedeSeleccionada}
                AND t.sede_id = {$sedeSeleccionada}
                AND i.tipo_incidencia = 'Captura'
            ";
            
            // Agregar filtro de fecha si existe
            if (!empty($condicionFecha)) {
                $capturaQuery .= $condicionFecha;
            }
            
            $query = $db->query($capturaQuery);
            $result = $query->getRow();
            $data['totalCapturas'] = $result->totalCapturas ?? 0;

            // Calcular la efectividad evitando división por cero
            if ($data['totalCapturas'] > 0) {
                $data['efectividad'] = round(($data['totalTrampasSede'] / $data['totalCapturas']) * 100, 2);
            }

            // Obtener las capturas por mes
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            $incidenciasPorTipoPlagaQuery = "
                SELECT DATE_FORMAT(i.fecha, '%Y-%m') as mes, i.tipo_plaga, COUNT(*) as total
                FROM incidencias i
                INNER JOIN trampas t ON i.id_trampa = t.id
                WHERE i.sede_id = {$sedeSeleccionada}
                AND t.sede_id = {$sedeSeleccionada}
            ";
            
            // Agregar filtro de fecha si existe
            if (!empty($condicionFecha)) {
                $incidenciasPorTipoPlagaQuery .= $condicionFecha;
            }
            
            $incidenciasPorTipoPlagaQuery .= "
                GROUP BY mes, i.tipo_plaga
                ORDER BY mes ASC
            ";
            
            $query = $db->query($incidenciasPorTipoPlagaQuery);
            $data['incidenciasPorTipoPlaga'] = $query->getResultArray();
            
            $incidenciasPorTipoIncidenciaQuery = "
                SELECT DATE_FORMAT(i.fecha, '%Y-%m') as mes, i.tipo_incidencia, COUNT(*) as total
                FROM incidencias i
                INNER JOIN trampas t ON i.id_trampa = t.id
                WHERE i.sede_id = {$sedeSeleccionada}
                AND t.sede_id = {$sedeSeleccionada}
            ";
            
            // Agregar filtro de fecha si existe
            if (!empty($condicionFecha)) {
                $incidenciasPorTipoIncidenciaQuery .= $condicionFecha;
            }
            
            $incidenciasPorTipoIncidenciaQuery .= "
                GROUP BY mes, i.tipo_incidencia
                ORDER BY mes ASC
            ";
            
            $query = $db->query($incidenciasPorTipoIncidenciaQuery);
            $data['incidenciasPorTipoIncidencia'] = $query->getResultArray();

            // Obtener lista de plagas para el selector de filtro
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            $listaPlagasQuery = "
                SELECT DISTINCT(i.tipo_plaga) as plaga
                FROM incidencias i
                INNER JOIN trampas t ON i.id_trampa = t.id
                WHERE i.sede_id = {$sedeSeleccionada}
                AND t.sede_id = {$sedeSeleccionada}
                AND i.tipo_plaga IS NOT NULL
                AND i.tipo_plaga != ''
            ";
            
            // Agregar filtro de fecha si existe
            if (!empty($condicionFecha)) {
                $listaPlagasQuery .= $condicionFecha;
            }
            
            $listaPlagasQuery .= "
                ORDER BY i.tipo_plaga ASC
            ";
            
            $query = $db->query($listaPlagasQuery);
            $data['listaPlagas'] = $query->getResultArray();
            
            // Obtener el nombre de la sede seleccionada
            $sedeSeleccionadaNombre = "";
            foreach ($data['sedes'] as $sede) {
                if ($sede['id'] == $sedeSeleccionada) {
                    $sedeSeleccionadaNombre = $sede['nombre'];
                    break;
                }
            }

            // Pasar el nombre de la sede a la vista
            $data['sedeSeleccionadaNombre'] = $sedeSeleccionadaNombre;

            // Obtener el conteo de trampas por ubicación
            $query = $db->table('trampas')
                ->select('ubicacion, COUNT(*) as total')
                ->where('sede_id', $sedeSeleccionada)
                ->groupBy('ubicacion')
                ->get();
            $data['trampasPorUbicacion'] = $query->getResultArray();

            // Obtener los planos de la sede seleccionada
            $planoModel = new \App\Models\PlanoModel();
            $planos = [];

            if ($sedeSeleccionada) {
                $planos = $planoModel->where('sede_id', $sedeSeleccionada)->findAll();
                
                // Procesar las previsualizaciones de los planos
                foreach ($planos as &$plano) {
                    $plano['preview_image'] = $this->getPreviewImage($plano);
                }
            }

            $data['planos'] = $planos;
            
            // Obtener lista de meses disponibles para filtro
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            $query = $db->table('incidencias i')
                ->select("DISTINCT(DATE_FORMAT(i.fecha, '%Y-%m')) as mes_valor, DATE_FORMAT(i.fecha, '%Y-%m') as mes_fecha")
                ->join('trampas t', 'i.id_trampa = t.id', 'inner')
                ->where('i.sede_id', $sedeSeleccionada)
                ->where('t.sede_id', $sedeSeleccionada) // Asegurar que la trampa pertenece a la sede seleccionada
                ->orderBy('i.fecha', 'DESC')
                ->get();
            
            $listaMeses = $query->getResultArray();
            
            // Convertir nombres de meses a español
            $mesesEspanol = [
                '01' => 'Enero',
                '02' => 'Febrero',
                '03' => 'Marzo',
                '04' => 'Abril',
                '05' => 'Mayo',
                '06' => 'Junio',
                '07' => 'Julio',
                '08' => 'Agosto',
                '09' => 'Septiembre',
                '10' => 'Octubre',
                '11' => 'Noviembre',
                '12' => 'Diciembre'
            ];
            
            foreach ($listaMeses as &$mes) {
                $partesFecha = explode('-', $mes['mes_fecha']);
                $numeroMes = $partesFecha[1];
                $anio = $partesFecha[0];
                $mes['mes_nombre'] = $mesesEspanol[$numeroMes] . ' ' . $anio;
            }
            
            $data['listaMeses'] = $listaMeses;
            
            // Obtener el mes seleccionado para el filtro (por defecto el más reciente)
            $mesSeleccionado = $this->request->getGet('mes');
            if (empty($mesSeleccionado) && !empty($data['listaMeses'])) {
                $mesSeleccionado = $data['listaMeses'][0]['mes_valor'];
            }
            
            $data['mesSeleccionado'] = $mesSeleccionado;
            
            // Obtener la plaga seleccionada para el filtro (por defecto la primera)
            $plagaSeleccionada = $this->request->getGet('plaga');
            if (empty($plagaSeleccionada) && !empty($data['listaPlagas'])) {
                $plagaSeleccionada = $data['listaPlagas'][0]['plaga'];
            }
            
            $data['plagaSeleccionada'] = $plagaSeleccionada;
            
            // Consulta para obtener las plagas con mayor presencia en la sede (filtrada por mes y fecha si está seleccionado)
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            $plagasMayorPresenciaQuery = "
                SELECT i.tipo_plaga, SUM(i.cantidad_organismos) as total_organismos
                FROM incidencias i
                INNER JOIN trampas t ON i.id_trampa = t.id
                WHERE i.sede_id = {$sedeSeleccionada}
                AND t.sede_id = {$sedeSeleccionada}
            ";
            
            // Aplicar filtro de mes si está seleccionado
            if (!empty($mesSeleccionado)) {
                $plagasMayorPresenciaQuery .= " AND DATE_FORMAT(i.fecha, '%Y-%m') = '{$mesSeleccionado}'";
            }
            
            // Aplicar filtro de fecha si existe
            if (!empty($condicionFecha)) {
                $plagasMayorPresenciaQuery .= $condicionFecha;
            }
            
            $plagasMayorPresenciaQuery .= "
                GROUP BY i.tipo_plaga
                ORDER BY total_organismos DESC
            ";
            
            $query = $db->query($plagasMayorPresenciaQuery);
            $data['plagasMayorPresencia'] = $query->getResultArray();
            
            // Consulta para obtener las áreas con mayor incidencia de la plaga seleccionada
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            if (!empty($plagaSeleccionada)) {
                $areasMayorIncidenciaQuery = "
                    SELECT t.ubicacion, SUM(i.cantidad_organismos) as total_organismos
                    FROM incidencias i
                    INNER JOIN trampas t ON i.id_trampa = t.id
                    WHERE i.sede_id = {$sedeSeleccionada}
                    AND t.sede_id = {$sedeSeleccionada}
                    AND i.tipo_plaga = '{$plagaSeleccionada}'
                ";
                
                // Aplicar filtro de fecha si existe
                if (!empty($condicionFecha)) {
                    $areasMayorIncidenciaQuery .= $condicionFecha;
                }
                
                $areasMayorIncidenciaQuery .= "
                    GROUP BY t.ubicacion
                    ORDER BY total_organismos DESC
                ";
                
                $query = $db->query($areasMayorIncidenciaQuery);
                $data['areasMayorIncidencia'] = $query->getResultArray();
            } else {
                $data['areasMayorIncidencia'] = [];
            }
            
            // Consulta para obtener todas las capturas de trampas (para el filtro dinámico)
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            $todasTrampasCapturaQuery = "
                SELECT t.id as id_trampa, t.nombre as trampa_nombre, t.ubicacion, i.tipo_plaga, COUNT(*) as total_capturas, SUM(i.cantidad_organismos) as cantidad_total
                FROM incidencias i
                INNER JOIN trampas t ON i.id_trampa = t.id
                WHERE i.sede_id = {$sedeSeleccionada}
                AND t.sede_id = {$sedeSeleccionada}
                AND i.tipo_incidencia = 'Captura'
                AND i.tipo_plaga IS NOT NULL
                AND i.tipo_plaga != ''
            ";
            
            // Aplicar filtro de fecha si existe
            if (!empty($condicionFecha)) {
                $todasTrampasCapturaQuery .= $condicionFecha;
            }
            
            $todasTrampasCapturaQuery .= "
                GROUP BY t.id, t.nombre, t.ubicacion, i.tipo_plaga
                ORDER BY cantidad_total DESC, total_capturas DESC
            ";
            
            $query = $db->query($todasTrampasCapturaQuery);
            $data['todasTrampasCaptura'] = $query->getResultArray();

            // Consulta para obtener trampas con mayor captura por plaga seleccionada
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            if (!empty($plagaSeleccionada)) {
                $trampasMayorCapturaQuery = "
                    SELECT t.id as id_trampa, t.nombre as trampa_nombre, t.ubicacion, COUNT(*) as total_capturas, SUM(i.cantidad_organismos) as cantidad_total
                    FROM incidencias i
                    INNER JOIN trampas t ON i.id_trampa = t.id
                    WHERE i.sede_id = {$sedeSeleccionada}
                    AND t.sede_id = {$sedeSeleccionada}
                    AND i.tipo_plaga = '{$plagaSeleccionada}'
                ";
                
                // Aplicar filtro de fecha si existe
                if (!empty($condicionFecha)) {
                    $trampasMayorCapturaQuery .= $condicionFecha;
                }
                
                $trampasMayorCapturaQuery .= "
                    GROUP BY t.id, t.nombre, t.ubicacion
                    ORDER BY cantidad_total DESC, total_capturas DESC
                    LIMIT 10
                ";
                
                $query = $db->query($trampasMayorCapturaQuery);
                $data['trampasMayorCaptura'] = $query->getResultArray();
            } else {
                $data['trampasMayorCaptura'] = [];
            }
            
            // Consulta para obtener áreas con capturas por plaga
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            $areasCapturasPorPlagaQuery = "
                SELECT t.ubicacion, i.tipo_plaga, COUNT(*) as total, SUM(i.cantidad_organismos) as cantidad_total
                FROM incidencias i
                INNER JOIN trampas t ON i.id_trampa = t.id
                WHERE i.sede_id = {$sedeSeleccionada}
                AND t.sede_id = {$sedeSeleccionada}
                AND i.tipo_incidencia = 'Captura'
                AND i.tipo_plaga IS NOT NULL
                AND i.tipo_plaga != ''
            ";
            
            // Aplicar filtro de fecha si existe
            if (!empty($condicionFecha)) {
                $areasCapturasPorPlagaQuery .= $condicionFecha;
            }
            
            $areasCapturasPorPlagaQuery .= "
                GROUP BY t.ubicacion, i.tipo_plaga
                ORDER BY cantidad_total DESC, total DESC
            ";
            
            $query = $db->query($areasCapturasPorPlagaQuery);
            $data['areasCapturasPorPlaga'] = $query->getResultArray();
            
            // Agregar información sobre el filtro de fecha aplicado a la vista
            $data['filtroFechaAplicado'] = !empty($condicionFecha);
            $data['mensajeFiltroFecha'] = '';
            
            if (!empty($fechaInicio) && !empty($fechaFin)) {
                $fechaInicioFormateada = date('d/m/Y', strtotime($fechaInicio));
                $fechaFinFormateada = date('d/m/Y', strtotime($fechaFin));
                $data['mensajeFiltroFecha'] = "Mostrando datos del {$fechaInicioFormateada} al {$fechaFinFormateada}";
            }
            
        } catch (\Exception $e) {
            log_message('error', 'Error al procesar datos de sede: ' . $e->getMessage());
            $data['mensaje_error'] = "Error al procesar datos de la sede: " . $e->getMessage();
        }

        // Obtener los últimos 3 documentos del repositorio para la sede seleccionada
        if (!empty($sedeSeleccionada)) {
            try {
                $documentoModel = new RepositorioDocumentoModel();
                $data['ultimosDocumentos'] = $documentoModel->obtenerUltimosDocumentos($sedeSeleccionada, 3);
            } catch (\Exception $e) {
                log_message('error', 'Error al obtener documentos del repositorio: ' . $e->getMessage());
                $data['ultimosDocumentos'] = [];
            }
        } else {
            $data['ultimosDocumentos'] = [];
        }

        return view('locations/index', $data);
    }

    // Método para obtener la imagen de previsualización
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

    // Función para crear una imagen de gráfico de barras
    private function createBarChartImage($labels, $data, $title, $color = 'rgba(54, 162, 235, 0.8)', $width = 750, $height = 400) {
        // Crear imagen
        $img = imagecreatetruecolor($width, $height);
        
        // Definir colores
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        $gray = imagecolorallocate($img, 220, 220, 220);
        
        // Extraer valores RGB del color (asumiendo formato rgba(r,g,b,a))
        preg_match('/rgba?\((\d+),\s*(\d+),\s*(\d+)/', $color, $matches);
        $r = isset($matches[1]) ? $matches[1] : 54;
        $g = isset($matches[2]) ? $matches[2] : 162;
        $b = isset($matches[3]) ? $matches[3] : 235;
        $barColor = imagecolorallocate($img, $r, $g, $b);
        
        // Rellenar el fondo
        imagefill($img, 0, 0, $white);
        
        // Dibujar título
        $titleFont = 3;
        $titleWidth = imagefontwidth($titleFont) * strlen($title);
        imagestring($img, $titleFont, ($width - $titleWidth) / 2, 10, $title, $black);
        
        // Ajustes para el gráfico
        $chartMargin = 50;
        $chartWidth = $width - ($chartMargin * 2);
        $chartHeight = $height - ($chartMargin * 2);
        $chartBottom = $height - $chartMargin;
        
        // Encontrar el valor máximo para escalar el gráfico
        $maxValue = count($data) > 0 ? max($data) : 0;
        $maxValue = ($maxValue > 0) ? $maxValue : 1; // Evitar división por cero
        
        // Calcular ancho de barras
        $barCount = count($data);
        $barWidth = $barCount > 0 ? $chartWidth / $barCount : 0;
        $barSpacing = $barWidth * 0.2;
        $barRealWidth = $barWidth - $barSpacing;
        
        // Dibujar eje Y
        imageline($img, $chartMargin, $chartMargin, $chartMargin, $chartBottom, $black);
        
        // Dibujar eje X
        imageline($img, $chartMargin, $chartBottom, $width - $chartMargin, $chartBottom, $black);
        
        // Dibujar líneas de cuadrícula horizontales
        $gridCount = 5;
        for ($i = 0; $i <= $gridCount; $i++) {
            $y = $chartBottom - ($i * ($chartHeight / $gridCount));
            imageline($img, $chartMargin, $y, $width - $chartMargin, $y, $gray);
            $label = round($maxValue * ($i / $gridCount));
            imagestring($img, 2, $chartMargin - 30, $y - 7, $label, $black);
        }
        
        // Dibujar barras
        for ($i = 0; $i < $barCount; $i++) {
            $value = $data[$i];
            $barHeight = ($value / $maxValue) * $chartHeight;
            $x1 = $chartMargin + ($i * $barWidth) + ($barSpacing / 2);
            $y1 = $chartBottom - $barHeight;
            $x2 = $x1 + $barRealWidth;
            $y2 = $chartBottom;
            
            // Dibujar barra
            imagefilledrectangle($img, $x1, $y1, $x2, $y2, $barColor);
            imagerectangle($img, $x1, $y1, $x2, $y2, $black);
            
            // Dibujar valor encima de la barra
            $valueStr = $value;
            $strWidth = imagefontwidth(2) * strlen($valueStr);
            imagestring($img, 2, $x1 + ($barRealWidth - $strWidth) / 2, $y1 - 15, $valueStr, $black);
            
            // Dibujar etiqueta
            if ($i % max(1, ceil($barCount / 15)) === 0) { // Mostrar solo algunas etiquetas si hay muchas
                $label = isset($labels[$i]) ? $labels[$i] : '';
                $labelFont = 2;
                // Rotar texto verticalmente para que quepan más etiquetas
                $this->imageStringRotated($img, $labelFont, $x1 + ($barRealWidth / 2), $chartBottom + 5, $label, $black, 90);
            }
        }
        
        return $img;
    }
    
    // Función auxiliar para dibujar texto rotado
    private function imageStringRotated($image, $font, $x, $y, $string, $color, $angle = 0) {
        if ($angle === 0) {
            imagestring($image, $font, $x, $y, $string, $color);
            return;
        }
        
        // Para ángulos de 90 grados, giramos manualmente el texto
        if ($angle === 90) {
            $len = strlen($string);
            $height = imagefontwidth($font);
            
            for ($i = 0; $i < $len; $i++) {
                $char = $string[$i];
                imagestring($image, $font, $x - 5, $y + ($i * $height), $char, $color);
            }
        }
    }

    // Función para crear un gráfico de barras apiladas
    private function createStackedBarChartImage($labels, $categories, $data, $title, $width = 750, $height = 400) {
        // Crear imagen
        $img = imagecreatetruecolor($width, $height);
        
        // Definir colores
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        $gray = imagecolorallocate($img, 220, 220, 220);
        
        // Generar colores para categorías
        $categoryColors = [];
        $baseColors = [
            [54, 162, 235],   // Azul
            [255, 99, 132],   // Rojo
            [255, 206, 86],   // Amarillo
            [75, 192, 192],   // Verde
            [153, 102, 255],  // Púrpura
            [255, 159, 64],   // Naranja
            [199, 199, 199],  // Gris
            [83, 102, 255],   // Azul claro
            [255, 99, 255],   // Rosa
            [165, 42, 42],    // Marrón
            [0, 128, 128],    // Verde azulado
            [128, 0, 128],    // Púrpura oscuro
            [255, 215, 0],    // Dorado
            [192, 192, 192],  // Plata
            [139, 69, 19],    // Marrón oscuro
            [46, 139, 87]     // Verde mar
        ];
        
        foreach ($categories as $index => $category) {
            $colorIndex = $index % count($baseColors);
            $colorValues = $baseColors[$colorIndex];
            $categoryColors[$category] = imagecolorallocate($img, $colorValues[0], $colorValues[1], $colorValues[2]);
        }
        
        // Rellenar el fondo
        imagefill($img, 0, 0, $white);
        
        // Dibujar título
        $titleFont = 3;
        $titleWidth = imagefontwidth($titleFont) * strlen($title);
        imagestring($img, $titleFont, ($width - $titleWidth) / 2, 10, $title, $black);
        
        // Ajustes para el gráfico
        $chartMargin = 50;
        $legendHeight = 120; // Espacio para la leyenda
        $chartWidth = $width - ($chartMargin * 2);
        $chartHeight = $height - ($chartMargin * 2) - $legendHeight;
        $chartBottom = $height - $chartMargin - $legendHeight;
        
        // Calcular el valor máximo apilado
        $maxValue = 0;
        foreach ($labels as $label) {
            $total = 0;
            foreach ($categories as $category) {
                $total += isset($data[$label][$category]) ? $data[$label][$category] : 0;
            }
            $maxValue = max($maxValue, $total);
        }
        $maxValue = ($maxValue > 0) ? $maxValue : 1; // Evitar división por cero
        
        // Redondear maxValue al siguiente entero para tener números más limpios
        $maxValue = ceil($maxValue);
        
        // Calcular ancho de barras
        $barCount = count($labels);
        $barWidth = $barCount > 0 ? $chartWidth / $barCount : 0;
        $barSpacing = $barWidth * 0.2;
        $barRealWidth = $barWidth - $barSpacing;
        
        // Dibujar eje Y
        imageline($img, $chartMargin, $chartMargin, $chartMargin, $chartBottom, $black);
        
        // Dibujar eje X
        imageline($img, $chartMargin, $chartBottom, $width - $chartMargin, $chartBottom, $black);
        
        // Dibujar líneas de cuadrícula horizontales y etiquetas del eje Y
        $gridCount = $maxValue;
        for ($i = 0; $i <= $gridCount; $i++) {
            $y = $chartBottom - ($i * ($chartHeight / $gridCount));
            imageline($img, $chartMargin, $y, $width - $chartMargin, $y, $gray);
            $label = $i;
            imagestring($img, 2, $chartMargin - 30, $y - 7, $label, $black);
        }
        
        // Dibujar barras apiladas
        for ($i = 0; $i < $barCount; $i++) {
            $label = $labels[$i];
            $x1 = $chartMargin + ($i * $barWidth) + ($barSpacing / 2);
            $y2 = $chartBottom;
            $x2 = $x1 + $barRealWidth;
            
            $yOffset = 0;
            
            // Dibujar cada segmento de la barra para cada categoría
            foreach ($categories as $category) {
                $value = isset($data[$label][$category]) ? $data[$label][$category] : 0;
                if ($value > 0) { // Solo dibujar si hay un valor
                    $barHeight = ($value / $maxValue) * $chartHeight;
                    $y1 = $chartBottom - $yOffset - $barHeight;
                    
                    // Dibujar segmento de la barra
                    imagefilledrectangle($img, $x1, $y1, $x2, $chartBottom - $yOffset, $categoryColors[$category]);
                    imagerectangle($img, $x1, $y1, $x2, $chartBottom - $yOffset, $black);
                    
                    $yOffset += $barHeight;
                }
            }
            
            // Dibujar etiqueta del eje X
            $labelText = $label;
            if (strlen($labelText) > 10) {
                $labelText = substr($labelText, 0, 10) . '...';
            }
            $this->imageStringRotated($img, 2, $x1 + ($barRealWidth / 2), $chartBottom + 5, $labelText, $black, 90);
        }
        
        // Dibujar leyenda
        $legendY = $height - $legendHeight + 20;
        $legendX = $chartMargin;
        $legendItemWidth = 20;
        $legendItemSpacing = 10;
        $legendItemsPerRow = 4;
        $legendItemHeight = 15;
        
        imagestring($img, 3, $legendX, $legendY - 20, "Leyenda:", $black);
        
        $col = 0;
        $row = 0;
        foreach ($categories as $category) {
            $x = $legendX + ($col * ($width - $chartMargin * 2) / $legendItemsPerRow);
            $y = $legendY + ($row * ($legendItemHeight + $legendItemSpacing));
            
            // Si sobrepasa el ancho, pasar a la siguiente fila
            if ($col >= $legendItemsPerRow) {
                $col = 0;
                $row++;
                $x = $legendX;
                $y = $legendY + ($row * ($legendItemHeight + $legendItemSpacing));
            }
            
            // Dibujar cuadrado de color
            imagefilledrectangle($img, $x, $y, $x + $legendItemWidth, $y + $legendItemHeight, $categoryColors[$category]);
            imagerectangle($img, $x, $y, $x + $legendItemWidth, $y + $legendItemHeight, $black);
            
            // Dibujar nombre de la categoría
            imagestring($img, 2, $x + $legendItemWidth + 5, $y + 2, $category, $black);
            
            $col++;
        }
        
        return $img;
    }

    public function generatePDF() {
        // Obtener los parámetros de la solicitud
        $sedeId = $this->request->getGet('sede_id');
        $plagaSeleccionada = $this->request->getGet('plaga');
        $mesSeleccionado = $this->request->getGet('mes');
        
        // Obtener datos necesarios
        $sedeModel = new SedeModel();
        $db = \Config\Database::connect();
        
        try {
            // Recopilar datos
            $sede = $sedeModel->find($sedeId);
            if (!$sede) {
                throw new \Exception("Sede no encontrada");
            }
            
            $data = [
                'sedeSeleccionada' => $sedeId,
                'sedeSeleccionadaNombre' => $sede['nombre'],
                'plagaSeleccionada' => $plagaSeleccionada,
                'mesSeleccionado' => $mesSeleccionado
            ];
            
            // Obtener total de trampas
            $builder = $db->table('trampas')->where('sede_id', $sedeId);
            $data['totalTrampasSede'] = $builder->countAllResults(false);
            
            // Obtener las plagas con mayor presencia (filtrada por mes si está seleccionado)
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            $builder = $db->table('incidencias i')
                ->select('i.tipo_plaga, SUM(i.cantidad_organismos) as total_organismos')
                ->join('trampas t', 'i.id_trampa = t.id', 'inner')
                ->where('i.sede_id', $sedeId)
                ->where('t.sede_id', $sedeId);
                
            // Aplicar filtro de mes si está seleccionado
            if (!empty($mesSeleccionado)) {
                $builder->where("DATE_FORMAT(i.fecha, '%Y-%m')", $mesSeleccionado);
            }
            
            $query = $builder->groupBy('i.tipo_plaga')
                ->orderBy('total_organismos', 'DESC')
                ->get();
            
            $data['plagasMayorPresencia'] = $query->getResultArray();
            
            // Consulta para obtener las áreas con mayor incidencia de la plaga seleccionada
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            if (!empty($plagaSeleccionada)) {
                $areasMayorIncidenciaQuery = "
                    SELECT t.ubicacion, SUM(i.cantidad_organismos) as total_organismos
                    FROM incidencias i
                    INNER JOIN trampas t ON i.id_trampa = t.id
                    WHERE i.sede_id = {$sedeId}
                    AND t.sede_id = {$sedeId}
                    AND i.tipo_plaga = '{$plagaSeleccionada}'
                ";
                
                // Aplicar filtro de fecha si existe
                if (!empty($condicionFecha)) {
                    $areasMayorIncidenciaQuery .= $condicionFecha;
                }
                
                $areasMayorIncidenciaQuery .= "
                    GROUP BY t.ubicacion
                    ORDER BY total_organismos DESC
                ";
                
                $query = $db->query($areasMayorIncidenciaQuery);
                $data['areasMayorIncidencia'] = $query->getResultArray();
            } else {
                $data['areasMayorIncidencia'] = [];
            }
            
            // Consulta para obtener todas las capturas de trampas (para el filtro dinámico)
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            $todasTrampasCapturaQuery = "
                SELECT t.id as id_trampa, t.nombre as trampa_nombre, t.ubicacion, i.tipo_plaga, COUNT(*) as total_capturas, SUM(i.cantidad_organismos) as cantidad_total
                FROM incidencias i
                INNER JOIN trampas t ON i.id_trampa = t.id
                WHERE i.sede_id = {$sedeId}
                AND t.sede_id = {$sedeId}
                AND i.tipo_incidencia = 'Captura'
                AND i.tipo_plaga IS NOT NULL
                AND i.tipo_plaga != ''
            ";
            
            // Aplicar filtro de fecha si existe
            if (!empty($condicionFecha)) {
                $todasTrampasCapturaQuery .= $condicionFecha;
            }
            
            $todasTrampasCapturaQuery .= "
                GROUP BY t.id, t.nombre, t.ubicacion, i.tipo_plaga
                ORDER BY cantidad_total DESC, total_capturas DESC
            ";
            
            $query = $db->query($todasTrampasCapturaQuery);
            $data['todasTrampasCaptura'] = $query->getResultArray();

            // Consulta para obtener trampas con mayor captura por plaga seleccionada
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            if (!empty($plagaSeleccionada)) {
                $trampasMayorCapturaQuery = "
                    SELECT t.id as id_trampa, t.nombre as trampa_nombre, t.ubicacion, COUNT(*) as total_capturas, SUM(i.cantidad_organismos) as cantidad_total
                    FROM incidencias i
                    INNER JOIN trampas t ON i.id_trampa = t.id
                    WHERE i.sede_id = {$sedeId}
                    AND t.sede_id = {$sedeId}
                    AND i.tipo_plaga = '{$plagaSeleccionada}'
                ";
                
                // Aplicar filtro de fecha si existe
                if (!empty($condicionFecha)) {
                    $trampasMayorCapturaQuery .= $condicionFecha;
                }
                
                $trampasMayorCapturaQuery .= "
                    GROUP BY t.id, t.nombre, t.ubicacion
                    ORDER BY cantidad_total DESC, total_capturas DESC
                    LIMIT 10
                ";
                
                $query = $db->query($trampasMayorCapturaQuery);
                $data['trampasMayorCaptura'] = $query->getResultArray();
            } else {
                $data['trampasMayorCaptura'] = [];
            }
            
            // Consulta para obtener áreas con capturas por plaga
            // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
            $areasCapturasPorPlagaQuery = "
                SELECT t.ubicacion, i.tipo_plaga, COUNT(*) as total, SUM(i.cantidad_organismos) as cantidad_total
                FROM incidencias i
                INNER JOIN trampas t ON i.id_trampa = t.id
                WHERE i.sede_id = {$sedeId}
                AND t.sede_id = {$sedeId}
                AND i.tipo_incidencia = 'Captura'
                AND i.tipo_plaga IS NOT NULL
                AND i.tipo_plaga != ''
            ";
            
            // Aplicar filtro de fecha si existe
            if (!empty($condicionFecha)) {
                $areasCapturasPorPlagaQuery .= $condicionFecha;
            }
            
            $areasCapturasPorPlagaQuery .= "
                GROUP BY t.ubicacion, i.tipo_plaga
                ORDER BY cantidad_total DESC, total DESC
            ";
            
            $query = $db->query($areasCapturasPorPlagaQuery);
            $data['areasCapturasPorPlaga'] = $query->getResultArray();
            
            // Agregar información sobre el filtro de fecha aplicado a la vista
            $data['filtroFechaAplicado'] = !empty($condicionFecha);
            $data['mensajeFiltroFecha'] = '';
            
            if (!empty($fechaInicio) && !empty($fechaFin)) {
                $fechaInicioFormateada = date('d/m/Y', strtotime($fechaInicio));
                $fechaFinFormateada = date('d/m/Y', strtotime($fechaFin));
                $data['mensajeFiltroFecha'] = "Mostrando datos del {$fechaInicioFormateada} al {$fechaFinFormateada}";
            }
            
            // Crear gráfico de Plagas con Mayor Presencia
            if (!empty($data['plagasMayorPresencia'])) {
                $width = 750;
                $height = 400;
                
                // Preparar datos para el gráfico
                $labels = [];
                $valores = [];
                
                foreach ($data['plagasMayorPresencia'] as $item) {
                    $labels[] = $item['tipo_plaga'] ?? 'No especificado';
                    $valores[] = (int)$item['total_organismos'];
                }
                
                // Título del gráfico con el mes seleccionado
                $tituloMes = '';
                if (!empty($mesSeleccionado)) {
                    // Formatear el mes para mostrarlo en el título
                    $partesFecha = explode('-', $mesSeleccionado);
                    if (count($partesFecha) == 2) {
                        $numeroMes = $partesFecha[1];
                        $anio = $partesFecha[0];
                        
                        $mesesEspanol = [
                            '01' => 'ENERO',
                            '02' => 'FEBRERO',
                            '03' => 'MARZO',
                            '04' => 'ABRIL',
                            '05' => 'MAYO',
                            '06' => 'JUNIO',
                            '07' => 'JULIO',
                            '08' => 'AGOSTO',
                            '09' => 'SEPTIEMBRE',
                            '10' => 'OCTUBRE',
                            '11' => 'NOVIEMBRE',
                            '12' => 'DICIEMBRE'
                        ];
                        
                        $tituloMes = ' DURANTE ' . $mesesEspanol[$numeroMes] . ' ' . $anio;
                    } else {
                        $tituloMes = ' DURANTE ' . strtoupper($mesSeleccionado);
                    }
                }
                
                $img = $this->createBarChartImage(
                    $labels,
                    $valores,
                    'PLAGA CON MAYOR PRESENCIA' . $tituloMes,
                    'rgba(255, 99, 132, 0.8)',
                    $width,
                    $height
                );
                
                // Convertir a base64 para incluir en el PDF
                ob_start();
                imagepng($img);
                $imageData = ob_get_clean();
                $data['plagasMayorPresenciaImagen'] = base64_encode($imageData);
                
                // Liberar memoria
                imagedestroy($img);
            }
            
            // Crear gráfico de Áreas con Mayor Incidencia
            if (!empty($data['areasMayorIncidencia']) && !empty($data['plagaSeleccionada'])) {
                $width = 750;
                $height = 400;
                
                // Preparar datos para el gráfico
                $labels = [];
                $valores = [];
                
                foreach ($data['areasMayorIncidencia'] as $item) {
                    $labels[] = $item['ubicacion'] ?? 'No especificado';
                    $valores[] = (int)$item['total_organismos'];
                }
                
                $img = $this->createBarChartImage(
                    $labels,
                    $valores,
                    'ÁREAS CON MAYOR INCIDENCIA DE ' . strtoupper($data['plagaSeleccionada']),
                    'rgba(75, 192, 192, 0.8)',
                    $width,
                    $height
                );
                
                // Convertir a base64 para incluir en el PDF
                ob_start();
                imagepng($img);
                $imageData = ob_get_clean();
                $data['areasMayorIncidenciaImagen'] = base64_encode($imageData);
                
                // Liberar memoria
                imagedestroy($img);
            }
            
            // Crear gráfico de Trampas con Mayor Captura
            if (!empty($data['trampasMayorCaptura']) && !empty($data['plagaSeleccionada'])) {
                $width = 750;
                $height = 400;
                
                // Ordenar las trampas por mayor captura
                usort($data['trampasMayorCaptura'], function($a, $b) {
                    return $b['total_capturas'] - $a['total_capturas'];
                });
                
                // Preparar datos para el gráfico
                $labels = [];
                $valores = [];
                
                foreach ($data['trampasMayorCaptura'] as $item) {
                    $labels[] = $item['id_trampa'] . ' (' . $item['ubicacion'] . ')';
                    $valores[] = (int)$item['total_capturas'];
                }
                
                $img = $this->createBarChartImage(
                    $labels,
                    $valores,
                    'TRAMPAS QUE PRESENTAN MAYOR CAPTURA DE ' . strtoupper($data['plagaSeleccionada']),
                    'rgba(54, 162, 235, 0.8)',
                    $width,
                    $height
                );
                
                // Convertir a base64 para incluir en el PDF
                ob_start();
                imagepng($img);
                $imageData = ob_get_clean();
                $data['trampasMayorCapturaImagen'] = base64_encode($imageData);
                
                // Liberar memoria
                imagedestroy($img);
            }
            
            // Crear gráfico de Áreas que Presentaron Capturas
            if (!empty($data['areasCapturasPorPlaga'])) {
                $width = 750;
                $height = 400;
                
                // Procesar datos para el gráfico de barras apiladas
                $ubicacionesSet = [];
                $plagasSet = [];
                
                // Extraer ubicaciones y plagas únicas
                foreach ($data['areasCapturasPorPlaga'] as $item) {
                    $ubicacionesSet[$item['ubicacion']] = true;
                    $plagasSet[$item['tipo_plaga']] = true;
                }
                
                $ubicaciones = array_keys($ubicacionesSet);
                $plagas = array_keys($plagasSet);
                
                // Crear una matriz para los datos de captura
                $capturasPorUbicacionYPlaga = [];
                foreach ($ubicaciones as $ubicacion) {
                    $capturasPorUbicacionYPlaga[$ubicacion] = [];
                    foreach ($plagas as $plaga) {
                        $capturasPorUbicacionYPlaga[$ubicacion][$plaga] = 0;
                    }
                }
                
                // Llenar la matriz con los datos de capturas
                foreach ($data['areasCapturasPorPlaga'] as $item) {
                    $capturasPorUbicacionYPlaga[$item['ubicacion']][$item['tipo_plaga']] = (int)$item['cantidad_total'];
                }
                
                // Crear gráfico
                $imagenAreasCapturasPlaga = $this->createStackedBarChartImage(
                    array_keys($capturasPorUbicacionYPlaga),  // Ubicaciones
                    array_values(array_unique(array_reduce(array_keys($capturasPorUbicacionYPlaga), function ($carry, $ubicacion) use ($capturasPorUbicacionYPlaga) {
                        return array_merge($carry, array_keys($capturasPorUbicacionYPlaga[$ubicacion]));
                    }, []))),  // Tipos de plagas
                    $capturasPorUbicacionYPlaga,
                    'ÁREAS QUE PRESENTARON CAPTURAS'
                );
                
                // Convertir imagen a dataURL base64
                ob_start();
                imagepng($imagenAreasCapturasPlaga);
                $imageData = ob_get_clean();
                $dataURL = 'data:image/png;base64,' . base64_encode($imageData);
                $data['imagenAreasCapturasPlaga'] = $dataURL;
                
                // Gráfico: Trampas con Mayor Captura (si hay plaga seleccionada)
                $data['imagenTrampasMayorCaptura'] = '';
                if (!empty($data['trampasMayorCaptura'])) {
                    // Preparar datos para el gráfico
                    $nombreTrampas = [];
                    $totalCapturasPorTrampa = [];
                    
                    foreach ($data['trampasMayorCaptura'] as $trampa) {
                        $nombreTrampas[] = $trampa['id_trampa'] . ' (' . $trampa['ubicacion'] . ')';
                        $totalCapturasPorTrampa[] = (int)$trampa['total_capturas'];
                    }
                    
                    // Limitar a 10 trampas para mejor visualización
                    if (count($nombreTrampas) > 10) {
                        $nombreTrampas = array_slice($nombreTrampas, 0, 10);
                        $totalCapturasPorTrampa = array_slice($totalCapturasPorTrampa, 0, 10);
                    }
                    
                    // Crear gráfico
                    $imagenTrampasMayorCaptura = $this->createBarChartImage(
                        $nombreTrampas,
                        $totalCapturasPorTrampa,
                        'TRAMPAS QUE PRESENTAN MAYOR CAPTURA DE ' . strtoupper($data['plagaSeleccionada']),
                        'rgba(54, 162, 235, 0.8)',
                        750,
                        400
                    );
                    
                    // Convertir imagen a dataURL base64
                    ob_start();
                    imagepng($imagenTrampasMayorCaptura);
                    $imageData = ob_get_clean();
                    $dataURL = 'data:image/png;base64,' . base64_encode($imageData);
                    $data['imagenTrampasMayorCaptura'] = $dataURL;
                    
                    // Liberar memoria
                    imagedestroy($imagenTrampasMayorCaptura);
                }
            }
            
            // Obtener notas de los gráficos desde la BD
            $queryNotas = $db->table('notas')
                ->select('elemento_id, contenido')
                ->where('sede_id', $sedeId)
                ->get();
            
            $resultNotas = $queryNotas->getResultArray();
            $notas = [];
            foreach ($resultNotas as $nota) {
                $notas[$nota['elemento_id']] = $nota['contenido'];
            }
            
            $data['notas'] = $notas;
            
            // Generar el PDF
            return view('locations/pdf_report', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error al generar PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }
    
    public function getDatosComparacionMeses()
    {
        try {
            $sedeId = $this->request->getPost('sede_id');
            $mesesJson = $this->request->getPost('meses');
            $plagasFiltroJson = $this->request->getPost('plagas_filtro');
            
            $meses = json_decode($mesesJson, true);
            $plagasFiltro = json_decode($plagasFiltroJson, true);
            
            if (!$sedeId || !$meses || !is_array($meses)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Parámetros inválidos'
                ]);
            }
            
            $db = \Config\Database::connect();
            $datos = [];
            
            foreach ($meses as $mes) {
                // Obtener plagas con mayor presencia para este mes específico
                // IMPORTANTE: Filtrar por ambas condiciones: i.sede_id = X AND t.sede_id = X
                $builder = $db->table('incidencias i')
                    ->select('i.tipo_plaga, SUM(i.cantidad_organismos) as total_organismos')
                    ->join('trampas t', 'i.id_trampa = t.id', 'inner')
                    ->where('i.sede_id', $sedeId)
                    ->where('t.sede_id', $sedeId)
                    ->where("DATE_FORMAT(i.fecha, '%Y-%m')", $mes)
                    ->where('i.tipo_plaga IS NOT NULL')
                    ->where('i.tipo_plaga !=', '');
                
                // Agregar filtro de plagas si se especifica
                if (!empty($plagasFiltro) && is_array($plagasFiltro)) {
                    $builder->whereIn('i.tipo_plaga', $plagasFiltro);
                }
                
                $builder->groupBy('i.tipo_plaga')
                    ->orderBy('total_organismos', 'DESC');
                
                $query = $builder->get();
                $plagasDelMes = $query->getResultArray();
                
                $datos[] = [
                    'mes' => $mes,
                    'plagas' => $plagasDelMes
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'datos' => $datos,
                'plagas_filtro' => $plagasFiltro
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en getDatosComparacionMeses: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener datos: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Obtener las trampas de un plano específico para filtrar
     */
    public function getTrampasPorPlano()
    {
        try {
            $planoId = $this->request->getPost('plano_id');
            $sedeId = $this->request->getPost('sede_id');
            
            if (empty($sedeId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Sede ID es requerido'
                ]);
            }
            
            $db = \Config\Database::connect();
            
            // Si no hay plano seleccionado, obtener todas las trampas de la sede que tienen incidencias
            if (empty($planoId)) {
                $query = $db->query("
                    SELECT DISTINCT t.id_trampa
                    FROM trampas t
                    INNER JOIN incidencias i ON i.id_trampa = t.id
                    WHERE t.sede_id = ?
                    AND i.sede_id = ?
                    AND t.id_trampa IS NOT NULL
                    AND t.id_trampa != ''
                    ORDER BY t.id_trampa ASC
                ", [$sedeId, $sedeId]);
            } else {
                // Obtener solo las trampas del plano seleccionado que tienen incidencias
                $query = $db->query("
                    SELECT DISTINCT t.id_trampa
                    FROM trampas t
                    INNER JOIN incidencias i ON i.id_trampa = t.id
                    WHERE t.sede_id = ?
                    AND i.sede_id = ?
                    AND t.plano_id = ?
                    AND t.id_trampa IS NOT NULL
                    AND t.id_trampa != ''
                    ORDER BY t.id_trampa ASC
                ", [$sedeId, $sedeId, $planoId]);
            }
            
            $trampas = $query->getResultArray();
            $idsTrampas = array_filter(array_column($trampas, 'id_trampa'));
            
            // Si no hay trampas, retornar array vacío en lugar de error
            return $this->response->setJSON([
                'success' => true,
                'trampas' => array_values($idsTrampas) // Reindexar el array
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Error en getTrampasPorPlano: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al obtener trampas: ' . $e->getMessage()
            ]);
        }
    }
}
