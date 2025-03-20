<?php
namespace App\Controllers;

use App\Models\SedeModel;
use App\Models\TrampaModel;
use App\Models\IncidenciaModel;
use CodeIgniter\I18n\Time;

class Locations extends BaseController
{
    public function index(): string
    {
        // Agregar logs para depuración
        log_message('debug', 'Iniciando carga de sedes');
        
        // Cargar el modelo de sedes
        try {
            $sedeModel = new SedeModel();
            
            // Verificar si la clase SedeModel está completa
            // Obtener todas las sedes
            $data['sedes'] = $sedeModel->findAll();
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

        try {
            // Obtener el total de trampas para la sede
            $builder = $db->table('trampas')->where('sede_id', $sedeSeleccionada);
            $data['totalTrampasSede'] = $builder->countAllResults(false);

            // Obtener el detalle de las trampas (nombre, tipo y ubicación)
            $query = $db->table('trampas')
                ->select('id, nombre, tipo, ubicacion')
                ->where('sede_id', $sedeSeleccionada)
                ->get();
            $data['trampasDetalle'] = $query->getResultArray();

            // Obtener el total de incidencias agrupadas por tipo_incidencia y tipo_plaga
            $query = $db->table('incidencias')
                ->select('tipo_incidencia,tipo_insecto, tipo_plaga, SUM(cantidad_organismos) as cantidad_organismos, COUNT(*) as total')
                ->where('sede_id', $sedeSeleccionada)
                ->groupBy(['tipo_incidencia', 'tipo_plaga'])
                ->get();
            $data['totalIncidenciasPorTipo'] = $query->getResultArray();

            // Obtener el total de capturas (solo incidencias de tipo "Captura")
            $query = $db->table('incidencias i')
                ->select('COUNT(*) as totalCapturas')
                ->join('trampas t', 'i.id_trampa = t.id')
                ->where('t.sede_id', $sedeSeleccionada)
                ->where('i.tipo_incidencia', 'Captura')
                ->get();
            $result = $query->getRow();
            $data['totalCapturas'] = $result->totalCapturas ?? 0;

            // Calcular la efectividad evitando división por cero
            if ($data['totalCapturas'] > 0) {
                $data['efectividad'] = round(($data['totalTrampasSede'] / $data['totalCapturas']) * 100, 2);
            }

            // Obtener las capturas por mes
            $query = $db->table('incidencias i')
                ->select("DATE_FORMAT(i.fecha, '%Y-%m') as mes, i.tipo_plaga, COUNT(*) as total")
                ->join('trampas t', 'i.id_trampa = t.id')
                ->where('t.sede_id', $sedeSeleccionada)
                ->groupBy(["mes", "i.tipo_plaga"])
                ->orderBy("mes", "ASC")
                ->get();
            
            $data['incidenciasPorTipoPlaga'] = $query->getResultArray();
            
            $query = $db->table('incidencias i')
                ->select("DATE_FORMAT(i.fecha, '%Y-%m') as mes, i.tipo_incidencia, COUNT(*) as total")
                ->join('trampas t', 'i.id_trampa = t.id')
                ->where('t.sede_id', $sedeSeleccionada)
                ->groupBy(["mes", "i.tipo_incidencia"])
                ->orderBy("mes", "ASC")
                ->get();

            $data['incidenciasPorTipoIncidencia'] = $query->getResultArray();
            
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
            
            // Obtener lista de plagas para el selector de filtro
            $query = $db->table('incidencias i')
                ->select('DISTINCT(i.tipo_plaga) as plaga')
                ->join('trampas t', 'i.id_trampa = t.id')
                ->where('t.sede_id', $sedeSeleccionada)
                ->where('i.tipo_plaga IS NOT NULL')
                ->where('i.tipo_plaga !=', '')
                ->orderBy('i.tipo_plaga', 'ASC')
                ->get();
            
            $data['listaPlagas'] = $query->getResultArray();
            
            // Obtener lista de meses disponibles para filtro
            $query = $db->table('incidencias i')
                ->select("DISTINCT(DATE_FORMAT(i.fecha, '%Y-%m')) as mes_valor, DATE_FORMAT(i.fecha, '%Y-%m') as mes_fecha")
                ->join('trampas t', 'i.id_trampa = t.id')
                ->where('t.sede_id', $sedeSeleccionada)
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
            
            // Consulta para obtener las plagas con mayor presencia en la sede (filtrada por mes si está seleccionado)
            $builder = $db->table('incidencias i')
                ->select('i.tipo_plaga, SUM(i.cantidad_organismos) as total_organismos')
                ->join('trampas t', 'i.id_trampa = t.id')
                ->where('t.sede_id', $sedeSeleccionada);
                
            // Aplicar filtro de mes si está seleccionado
            if (!empty($mesSeleccionado)) {
                $builder->where("DATE_FORMAT(i.fecha, '%Y-%m')", $mesSeleccionado);
            }
            
            $query = $builder->groupBy('i.tipo_plaga')
                ->orderBy('total_organismos', 'DESC')
                ->get();
            
            $data['plagasMayorPresencia'] = $query->getResultArray();
            
            // Consulta para obtener las áreas con mayor incidencia de la plaga seleccionada
            if (!empty($plagaSeleccionada)) {
                $query = $db->table('incidencias i')
                    ->select('t.ubicacion, SUM(i.cantidad_organismos) as total_organismos')
                    ->join('trampas t', 'i.id_trampa = t.id')
                    ->where('t.sede_id', $sedeSeleccionada)
                    ->where('i.tipo_plaga', $plagaSeleccionada)
                    ->groupBy('t.ubicacion')
                    ->orderBy('total_organismos', 'DESC')
                    ->get();
                
                $data['areasMayorIncidencia'] = $query->getResultArray();
            } else {
                $data['areasMayorIncidencia'] = [];
            }
            
            // Consulta para obtener las trampas con mayor captura por tipo de plaga (con ID de trampa)
            if (!empty($plagaSeleccionada)) {
                $query = $db->table('incidencias i')
                    ->select('t.id as trampa_id, CONCAT("Trampa #", t.id) as trampa_nombre, SUM(i.cantidad_organismos) as total_capturas')
                    ->join('trampas t', 'i.id_trampa = t.id')
                    ->where('t.sede_id', $sedeSeleccionada)
                    ->where('i.tipo_plaga', $plagaSeleccionada)
                    ->where('i.tipo_incidencia', 'Captura')
                    ->groupBy('t.id')
                    ->orderBy('total_capturas', 'DESC')
                    ->limit(30) // Limitar a 30 trampas para mejor visualización
                    ->get();
                
                $data['trampasMayorCaptura'] = $query->getResultArray();
            } else {
                $data['trampasMayorCaptura'] = [];
            }
            
            // Consulta para obtener áreas que presentaron capturas con diferentes plagas
            $query = $db->table('incidencias i')
                ->select('t.ubicacion, i.tipo_plaga, COUNT(*) as total_capturas')
                ->join('trampas t', 'i.id_trampa = t.id')
                ->where('t.sede_id', $sedeSeleccionada)
                ->where('i.tipo_incidencia', 'Captura')
                ->where('i.tipo_plaga IS NOT NULL')
                ->where('i.tipo_plaga !=', '')
                ->groupBy(['t.ubicacion', 'i.tipo_plaga'])
                ->orderBy('t.ubicacion', 'ASC')
                ->orderBy('i.tipo_plaga', 'ASC')
                ->get();
            
            $data['areasCapturasPorPlaga'] = $query->getResultArray();
            
        } catch (\Exception $e) {
            log_message('error', 'Error al procesar datos de sede: ' . $e->getMessage());
            $data['mensaje_error'] = "Error al procesar datos de la sede: " . $e->getMessage();
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
            $builder = $db->table('incidencias i')
                ->select('i.tipo_plaga, SUM(i.cantidad_organismos) as total_organismos')
                ->join('trampas t', 'i.id_trampa = t.id')
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
            if (!empty($plagaSeleccionada)) {
                $query = $db->table('incidencias i')
                    ->select('t.ubicacion, SUM(i.cantidad_organismos) as total_organismos')
                    ->join('trampas t', 'i.id_trampa = t.id')
                    ->where('t.sede_id', $sedeId)
                    ->where('i.tipo_plaga', $plagaSeleccionada)
                    ->groupBy('t.ubicacion')
                    ->orderBy('total_organismos', 'DESC')
                    ->get();
                
                $data['areasMayorIncidencia'] = $query->getResultArray();
            } else {
                $data['areasMayorIncidencia'] = [];
            }
            
            // Consulta para obtener las trampas con mayor captura por tipo de plaga
            if (!empty($plagaSeleccionada)) {
                $query = $db->table('incidencias i')
                    ->select('t.id as trampa_id, CONCAT("Trampa #", t.id) as trampa_nombre, SUM(i.cantidad_organismos) as total_capturas')
                    ->join('trampas t', 'i.id_trampa = t.id')
                    ->where('t.sede_id', $sedeId)
                    ->where('i.tipo_plaga', $plagaSeleccionada)
                    ->where('i.tipo_incidencia', 'Captura')
                    ->groupBy('t.id')
                    ->orderBy('total_capturas', 'DESC')
                    ->limit(30) // Limitar a 30 trampas para mejor visualización
                    ->get();
                
                $data['trampasMayorCaptura'] = $query->getResultArray();
            } else {
                $data['trampasMayorCaptura'] = [];
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
                    $labels[] = $item['trampa_nombre'];
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
                    $capturasPorUbicacionYPlaga[$item['ubicacion']][$item['tipo_plaga']] = (int)$item['total_capturas'];
                }
                
                // Crear la imagen para el gráfico de barras apiladas
                $img = $this->createStackedBarChartImage(
                    $ubicaciones,
                    $plagas,
                    $capturasPorUbicacionYPlaga,
                    'ÁREAS QUE PRESENTARON CAPTURAS',
                    $width,
                    $height
                );
                
                // Convertir a base64 para incluir en el PDF
                ob_start();
                imagepng($img);
                $imageData = ob_get_clean();
                $data['areasCapturasPorPlagaImagen'] = base64_encode($imageData);
                
                // Liberar memoria
                imagedestroy($img);
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
            
            // Obtener áreas que presentaron capturas con diferentes plagas
            $query = $db->table('incidencias i')
                ->select('t.ubicacion, i.tipo_plaga, COUNT(*) as total_capturas')
                ->join('trampas t', 'i.id_trampa = t.id')
                ->where('t.sede_id', $sedeId)
                ->where('i.tipo_incidencia', 'Captura')
                ->where('i.tipo_plaga IS NOT NULL')
                ->where('i.tipo_plaga !=', '')
                ->groupBy(['t.ubicacion', 'i.tipo_plaga'])
                ->orderBy('t.ubicacion', 'ASC')
                ->orderBy('i.tipo_plaga', 'ASC')
                ->get();
            
            $data['areasCapturasPorPlaga'] = $query->getResultArray();
            
            // Generar el PDF
            return view('locations/pdf_report', $data);
            
        } catch (\Exception $e) {
            log_message('error', 'Error al generar PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al generar PDF: ' . $e->getMessage());
        }
    }
}
