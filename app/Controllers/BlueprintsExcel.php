<?php

namespace App\Controllers;

use App\Models\PlanoModel;
use App\Models\SedeModel;
use App\Models\TrampaModel;
use CodeIgniter\HTTP\ResponseInterface;

class BlueprintsExcel extends BaseController
{
    /**
     * Muestra el formulario para descargar la plantilla
     */
    public function mostrarFormularioDescarga($planoId = null)
    {
        if (!$planoId) {
            return redirect()->to('/blueprints')->with('error', 'Plano no especificado');
        }

        $planoModel = new PlanoModel();
        $sedeModel = new SedeModel();

        $plano = $planoModel->find($planoId);
        if (!$plano) {
            return redirect()->to('/blueprints')->with('error', 'Plano no encontrado');
        }

        $sede = $sedeModel->find($plano['sede_id']);

        $data = [
            'plano' => $plano,
            'sede' => $sede,
            'title' => 'Descargar Plantilla de Incidencias'
        ];

        return view('blueprints/formulario_descarga_plantilla', $data);
    }

    /**
     * Genera y descarga la plantilla Excel
     */
    public function descargarPlantilla($planoId = null)
    {
        if (!$planoId) {
            return redirect()->to('/blueprints')->with('error', 'Plano no especificado');
        }

        // Verificar si PhpSpreadsheet está disponible
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            return redirect()->back()
                ->with('error', 'PhpSpreadsheet no está instalado. Por favor instálelo manualmente con: composer require phpoffice/phpspreadsheet');
        }

        // Obtener parámetros
        $semanas = $this->request->getGet('semanas') ?? 4;
        $fechaInicio = $this->request->getGet('fecha_inicio') ?? date('Y-m-d');
        $incluirHallazgos = $this->request->getGet('incluir_hallazgos') === 'on' || $this->request->getGet('incluir_hallazgos') === '1';
        $zonasHallazgos = $this->request->getGet('zonas_hallazgos');
        
        // Si zonas_hallazgos viene como array, procesarlo
        if (is_array($zonasHallazgos)) {
            $zonasHallazgos = array_filter($zonasHallazgos, function($zona) {
                return !empty(trim($zona));
            });
        } else {
            $zonasHallazgos = [];
        }

        // Validar parámetros
        $semanas = (int)$semanas;
        if ($semanas < 1 || $semanas > 52) {
            return redirect()->back()->with('error', 'El número de semanas debe estar entre 1 y 52');
        }
        
        // Si se incluyen hallazgos pero no hay zonas, obtener zonas de las trampas
        if ($incluirHallazgos && empty($zonasHallazgos)) {
            $trampaModel = new TrampaModel();
            $trampas = $trampaModel->where('plano_id', $planoId)->findAll();
            $zonasHallazgos = array_unique(array_filter(array_column($trampas, 'ubicacion')));
        }

        try {
            // Cargar modelos
            $planoModel = new PlanoModel();
            $sedeModel = new SedeModel();
            $trampaModel = new TrampaModel();

            // Obtener información del plano
            $plano = $planoModel->find($planoId);
            if (!$plano) {
                return redirect()->to('/blueprints')->with('error', 'Plano no encontrado');
            }

            $sede = $sedeModel->find($plano['sede_id']);
            
            // Obtener todas las trampas del plano
            $trampas = $trampaModel->where('plano_id', $planoId)->findAll();
            
            if (empty($trampas)) {
                return redirect()->back()->with('error', 'No hay equipos/trampas registrados en este plano. Por favor, agregue equipos al plano primero.');
            }

            // Crear el archivo Excel
            $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

            // Tipos de insectos/plagas (basado en el modal de registrar incidencia)
            $tiposInsectos = [
                'Mosca',
                'Mosca doméstica',
                'Mosca de la fruta',
                'Mosca de drenaje',
                'Moscas metálicas',
                'Mosca forida',
                'Palomillas de almacén',
                'Otras palomillas',
                'Gorgojos',
                'Otros escarabajos',
                'Abejas',
                'Avispas',
                'Mosquitos',
                'Cucaracha',
                'Cucaracha Americana',
                'Cucaracha Alemana',
                'Hormiga',
                'Roedor',
                'Arañas',
                'Escarabajo',
                'Tijerilla',
                'Lagartijas',
                'Insectos de áreas verdes',
                'Otros',
                'Total de insectos'
            ];
            
            // Mapeo de tipos de trampa a tipos de plaga permitidos
            $tiposPlagaPorTrampa = [
                'edc_quimicas' => ['Roedor'],
                'edc_adhesivas' => ['Roedor', 'Hormiga', 'Cucaracha Americana', 'Cucaracha Alemana', 'Escarabajo', 'Tijerilla', 'Arañas'],
                'luz_uv' => ['Mosca doméstica', 'Mosca de la fruta', 'Mosca de drenaje', 'Moscas metálicas', 'Mosca forida', 'Palomillas de almacén', 'Otras palomillas', 'Gorgojos', 'Otros escarabajos', 'Abejas', 'Avispas', 'Mosquitos', 'Insectos de áreas verdes', 'Otros']
            ];

            // Generar una hoja por cada semana
            for ($semana = 1; $semana <= $semanas; $semana++) {
                $fechaSemana = date('Y-m-d', strtotime($fechaInicio . ' + ' . (($semana - 1) * 7) . ' days'));
                $fechaFinSemana = date('Y-m-d', strtotime($fechaSemana . ' + 6 days'));
                
                $nombreMes = $this->obtenerNombreMes($fechaSemana);
                $diaInicio = date('d', strtotime($fechaSemana));
                $nombreHoja = "Semana $semana";
                
                // Para la primera semana, usar la hoja por defecto, para las demás crear nuevas
                if ($semana == 1) {
                    $sheet = $spreadsheet->getActiveSheet();
                    $sheet->setTitle($nombreHoja);
                } else {
                    $sheet = new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, $nombreHoja);
                    $spreadsheet->addSheet($sheet);
                }
                
                // Generar la tabla para esta semana
                $this->generarTablaSemana($sheet, $trampas, $tiposInsectos, $fechaSemana, $plano, $sede, $tiposPlagaPorTrampa, $incluirHallazgos, $zonasHallazgos);
            }

            // Establecer la primera hoja como activa
            $spreadsheet->setActiveSheetIndex(0);

            // Generar el nombre del archivo
            $nombreArchivo = 'Plantilla_Incidencias_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $plano['nombre']) . '_' . date('Y-m-d') . '.xlsx';

            // Crear un archivo temporal
            $tempFile = tempnam(sys_get_temp_dir(), 'excel_');
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save($tempFile);

            // Leer el archivo y enviarlo
            $fileContent = file_get_contents($tempFile);
            unlink($tempFile); // Eliminar archivo temporal

            // Configurar respuesta para descarga
            return $this->response
                ->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
                ->setHeader('Content-Disposition', 'attachment; filename="' . $nombreArchivo . '"')
                ->setHeader('Cache-Control', 'max-age=0')
                ->setBody($fileContent);

        } catch (\Exception $e) {
            log_message('error', 'Error al generar plantilla Excel: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error al generar la plantilla: ' . $e->getMessage());
        }
    }

    /**
     * Genera la tabla para una semana específica
     */
    private function generarTablaSemana($sheet, $trampas, $tiposInsectos, $fechaInicio, $plano, $sede, $tiposPlagaPorTrampa, $incluirHallazgos = false, $zonasHallazgos = [])
    {
        $fila = 1;

        // Logo y título (simulado)
        $sheet->setCellValue('A' . $fila, 'SERVIPRO');
        $sheet->mergeCells('A' . $fila . ':Z' . $fila);
        $sheet->getStyle('A' . $fila)->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A' . $fila)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $fila += 2;

        // Título principal
        $nombrePlano = strtoupper($plano['nombre']);
        $titulo = 'REGISTRO DE ACTIVIDAD DE INSECTOS VOLADORES EN ' . strtoupper($sede['nombre']) . ' ' . $nombrePlano . ' ' . date('Y');
        $sheet->setCellValue('A' . $fila, $titulo);
        $sheet->mergeCells('A' . $fila . ':Z' . $fila);
        $sheet->getStyle('A' . $fila)->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A' . $fila)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $fila += 1;

        // Mes
        $nombreMes = $this->obtenerNombreMes($fechaInicio);
        $sheet->setCellValue('A' . $fila, strtoupper($nombreMes));
        $sheet->mergeCells('A' . $fila . ':Z' . $fila);
        $sheet->getStyle('A' . $fila)->getFont()->setBold(true)->setSize(11);
        $sheet->getStyle('A' . $fila)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $fila += 1;
        
        // Fecha
        $sheet->setCellValue('A' . $fila, 'Fecha en la que se registrarán las incidencias');
        $sheet->getStyle('A' . $fila)->getFont()->setBold(true)->setSize(10);
        $fechaExcel = \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(strtotime($fechaInicio));
        $sheet->setCellValue('B' . $fila, $fechaExcel);
        $sheet->getStyle('B' . $fila)
            ->getNumberFormat()
            ->setFormatCode('dd/mm/yyyy');
        
        $fila += 2;

        // Generar tabla para EDC Químicas
        $fila = $this->generarTablaPorTipoTrampa($sheet, $trampas, $tiposPlagaPorTrampa['edc_quimicas'] ?? [], 'EDC QUÍMICAS', $fila);
        
        // Generar tabla para EDC Adhesivas
        $fila = $this->generarTablaPorTipoTrampa($sheet, $trampas, $tiposPlagaPorTrampa['edc_adhesivas'] ?? [], 'EDC ADHESIVAS', $fila);
        
        // Generar tabla para Equipo de Luz UV
        $fila = $this->generarTablaPorTipoTrampa($sheet, $trampas, $tiposPlagaPorTrampa['luz_uv'] ?? [], 'EQUIPO DE LUZ UV', $fila);
        
        // Generar tabla para otras trampas
        $fila = $this->generarTablaOtrasTrampas($sheet, $trampas, $tiposInsectos, $fila);
        
        // Generar tabla para hallazgos si está habilitado
        if ($incluirHallazgos && !empty($zonasHallazgos)) {
            $fila = $this->generarTablaHallazgos($sheet, $zonasHallazgos, $tiposInsectos, $fila);
        }
    }
    
    /**
     * Normaliza un tipo de trampa para comparación
     */
    private function normalizarTipoTrampa($tipo) {
        if (empty($tipo)) return '';
        return strtolower(str_replace([' ', 'á', 'é', 'í', 'ó', 'ú', 'ñ'], ['_', 'a', 'e', 'i', 'o', 'u', 'n'], $tipo));
    }
    
    /**
     * Genera una tabla para un tipo específico de trampa
     */
    private function generarTablaPorTipoTrampa($sheet, $trampas, $tiposPlagaPermitidos, $nombreTipoTrampa, $filaInicio)
    {
        // Palabras clave para identificar cada tipo de trampa
        $palabrasClave = [
            'EDC QUÍMICAS' => ['edc', 'quimica', 'quimicas'],
            'EDC ADHESIVAS' => ['edc', 'adhesiva', 'adhesivas'],
            'EQUIPO DE LUZ UV' => ['luz', 'uv', 'equipo']
        ];
        
        $claves = $palabrasClave[$nombreTipoTrampa] ?? [];
        
        // Filtrar trampas por tipo usando palabras clave
        $trampasFiltradas = array_filter($trampas, function($trampa) use ($claves, $nombreTipoTrampa) {
            $tipoTrampaNormalizado = $this->normalizarTipoTrampa($trampa['tipo'] ?? '');
            
            // Verificar que todas las palabras clave estén presentes
            $todasLasClavesPresentes = true;
            foreach ($claves as $clave) {
                if (strpos($tipoTrampaNormalizado, $clave) === false) {
                    $todasLasClavesPresentes = false;
                    break;
                }
            }
            
            if (!$todasLasClavesPresentes) {
                return false;
            }
            
            // Para EDC, también verificar que no sea del otro tipo
            if ($nombreTipoTrampa === 'EDC QUÍMICAS') {
                // Asegurarse de que no sea adhesiva
                return strpos($tipoTrampaNormalizado, 'adhesiva') === false;
            }
            if ($nombreTipoTrampa === 'EDC ADHESIVAS') {
                // Asegurarse de que no sea química
                return strpos($tipoTrampaNormalizado, 'quimica') === false;
            }
            
            return true;
        });
        
        // Si no hay trampas de este tipo, no generar tabla
        if (empty($trampasFiltradas)) {
            return $filaInicio;
        }
        
        // Título de la sección
        $sheet->setCellValue('A' . $filaInicio, $nombreTipoTrampa);
        $sheet->getStyle('A' . $filaInicio)->getFont()->setBold(true)->setSize(12);
        $filaInicio += 1;
        
        // Calcular número de columnas
        $numColumnas = 2 + count($tiposPlagaPermitidos) + 1; // ÁREA + EQUIPO + tipos de plaga + Total
        $ultimaColumnaLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($numColumnas);
        
        // Establecer ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(15);
        foreach (range('C', $ultimaColumnaLetra) as $col) {
            $sheet->getColumnDimension($col)->setWidth(15);
        }
        
        $fila = $filaInicio;
        
        // Encabezados
        $columna = 'A';
        $sheet->setCellValue($columna . $fila, 'ÁREA');
        $sheet->getStyle($columna . $fila)->getFont()->setBold(true);
        $sheet->getStyle($columna . $fila)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle($columna . $fila)->getFont()->getColor()->setRGB('FFFFFF');
        $columnaIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columna);
        $columnaIndex++;
        $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
        
        $sheet->setCellValue($columna . $fila, 'EQUIPO');
        $sheet->getStyle($columna . $fila)->getFont()->setBold(true);
        $sheet->getStyle($columna . $fila)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle($columna . $fila)->getFont()->getColor()->setRGB('FFFFFF');
        $columnaIndex++;
        $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
        
        foreach ($tiposPlagaPermitidos as $tipo) {
            $sheet->setCellValue($columna . $fila, $tipo);
            $sheet->getStyle($columna . $fila)->getFont()->setBold(true);
            $sheet->getStyle($columna . $fila)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle($columna . $fila)->getFont()->getColor()->setRGB('FFFFFF');
            if (strlen($tipo) > 12) {
                $sheet->getColumnDimension($columna)->setWidth(18);
            }
            $columnaIndex++;
            $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
        }
        
        $sheet->setCellValue($columna . $fila, 'Total de insectos');
        $sheet->getStyle($columna . $fila)->getFont()->setBold(true);
        $sheet->getStyle($columna . $fila)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle($columna . $fila)->getFont()->getColor()->setRGB('FFFFFF');
        
        $fila++;
        $filaInicioTabla = $fila;
        
        // Filas de datos
        foreach ($trampasFiltradas as $trampa) {
            $columna = 'A';
            
            $ubicacion = !empty($trampa['ubicacion']) ? $trampa['ubicacion'] : 'Sin ubicación';
            $sheet->setCellValue($columna . $fila, $ubicacion);
            $columnaIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columna);
            $columnaIndex++;
            $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
            
            $nombreTrampa = !empty($trampa['nombre']) ? $trampa['nombre'] : (!empty($trampa['id_trampa']) ? $trampa['id_trampa'] : 'T' . $trampa['id']);
            $sheet->setCellValue($columna . $fila, $nombreTrampa);
            $columnaIndex++;
            $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
            
            $columnaInicio = $columna;
            foreach ($tiposPlagaPermitidos as $tipo) {
                $sheet->setCellValue($columna . $fila, '0');
                $columnaIndex++;
                $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
            }
            
            $columnaFinDatos = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnaInicio) + count($tiposPlagaPermitidos) - 1
            );
            $sheet->setCellValue($columna . $fila, '=SUM(' . $columnaInicio . $fila . ':' . $columnaFinDatos . $fila . ')');
            
            $fila++;
        }
        
        // Aplicar bordes
        $range = 'A' . $filaInicioTabla . ':' . $ultimaColumnaLetra . ($fila - 1);
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle($range)->applyFromArray($styleArray);
        
        return $fila + 2; // Dejar espacio antes de la siguiente tabla
    }
    
    /**
     * Genera una tabla para hallazgos (sin trampa asociada, solo zona)
     */
    private function generarTablaHallazgos($sheet, $zonasHallazgos, $tiposInsectos, $filaInicio)
    {
        if (empty($zonasHallazgos)) {
            return $filaInicio;
        }
        
        // Filtrar tipos de insectos (excluir "Total de insectos")
        $tiposSinTotal = array_filter($tiposInsectos, function($tipo) {
            return $tipo !== 'Total de insectos';
        });
        
        // Título de la sección
        $sheet->setCellValue('A' . $filaInicio, 'HALLAZGOS');
        $sheet->getStyle('A' . $filaInicio)->getFont()->setBold(true)->setSize(12);
        $filaInicio += 1;
        
        // Calcular número de columnas (ZONA + todos los tipos de plaga + Total)
        $numColumnas = 1 + count($tiposSinTotal) + 1; // ZONA + tipos de plaga + Total
        $ultimaColumnaLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($numColumnas);
        
        // Establecer ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(25);
        foreach (range('B', $ultimaColumnaLetra) as $col) {
            $sheet->getColumnDimension($col)->setWidth(15);
        }
        
        $fila = $filaInicio;
        
        // Encabezados
        $columna = 'A';
        $sheet->setCellValue($columna . $fila, 'ZONA');
        $sheet->getStyle($columna . $fila)->getFont()->setBold(true);
        $sheet->getStyle($columna . $fila)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle($columna . $fila)->getFont()->getColor()->setRGB('FFFFFF');
        $columnaIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columna);
        $columnaIndex++;
        $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
        
        foreach ($tiposSinTotal as $tipo) {
            $sheet->setCellValue($columna . $fila, $tipo);
            $sheet->getStyle($columna . $fila)->getFont()->setBold(true);
            $sheet->getStyle($columna . $fila)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle($columna . $fila)->getFont()->getColor()->setRGB('FFFFFF');
            if (strlen($tipo) > 12) {
                $sheet->getColumnDimension($columna)->setWidth(18);
            }
            $columnaIndex++;
            $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
        }
        
        $sheet->setCellValue($columna . $fila, 'Total de insectos');
        $sheet->getStyle($columna . $fila)->getFont()->setBold(true);
        $sheet->getStyle($columna . $fila)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle($columna . $fila)->getFont()->getColor()->setRGB('FFFFFF');
        
        $fila++;
        $filaInicioTabla = $fila;
        
        // Filas de datos (una por cada zona)
        foreach ($zonasHallazgos as $zona) {
            $columna = 'A';
            
            $sheet->setCellValue($columna . $fila, $zona);
            $columnaIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columna);
            $columnaIndex++;
            $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
            
            $columnaInicio = $columna;
            foreach ($tiposSinTotal as $tipo) {
                $sheet->setCellValue($columna . $fila, '0');
                $columnaIndex++;
                $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
            }
            
            $columnaFinDatos = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnaInicio) + count($tiposSinTotal) - 1
            );
            $sheet->setCellValue($columna . $fila, '=SUM(' . $columnaInicio . $fila . ':' . $columnaFinDatos . $fila . ')');
            
            $fila++;
        }
        
        // Aplicar bordes
        $range = 'A' . $filaInicioTabla . ':' . $ultimaColumnaLetra . ($fila - 1);
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle($range)->applyFromArray($styleArray);
        
        return $fila + 2; // Dejar espacio antes de la siguiente tabla
    }
    
    /**
     * Genera una tabla para otras trampas (no EDC Químicas, EDC Adhesivas, ni Equipo de Luz UV)
     */
    private function generarTablaOtrasTrampas($sheet, $trampas, $tiposInsectos, $filaInicio)
    {
        // Filtrar trampas que NO sean de los 3 tipos principales
        $tiposPrincipales = ['edc_quimicas', 'edc_adhesivas', 'luz_uv'];
        $trampasFiltradas = array_filter($trampas, function($trampa) use ($tiposPrincipales) {
            $tipoTrampaNormalizado = $this->normalizarTipoTrampa($trampa['tipo'] ?? '');
            
            // Verificar si NO es de los tipos principales
            $esPrincipal = false;
            foreach ($tiposPrincipales as $tipoPrincipal) {
                $palabrasClave = [
                    'edc_quimicas' => ['edc', 'quimica', 'quimicas'],
                    'edc_adhesivas' => ['edc', 'adhesiva', 'adhesivas'],
                    'luz_uv' => ['luz', 'uv', 'equipo']
                ];
                
                $claves = $palabrasClave[$tipoPrincipal] ?? [];
                $todasLasClavesPresentes = true;
                foreach ($claves as $clave) {
                    if (strpos($tipoTrampaNormalizado, $clave) === false) {
                        $todasLasClavesPresentes = false;
                        break;
                    }
                }
                
                if ($todasLasClavesPresentes) {
                    // Verificar que no sea del otro tipo de EDC
                    if ($tipoPrincipal === 'edc_quimicas' && strpos($tipoTrampaNormalizado, 'adhesiva') === false) {
                        $esPrincipal = true;
                        break;
                    }
                    if ($tipoPrincipal === 'edc_adhesivas' && strpos($tipoTrampaNormalizado, 'quimica') === false) {
                        $esPrincipal = true;
                        break;
                    }
                    if ($tipoPrincipal === 'luz_uv') {
                        $esPrincipal = true;
                        break;
                    }
                }
            }
            
            return !$esPrincipal;
        });
        
        // Si no hay trampas de otros tipos, no generar tabla
        if (empty($trampasFiltradas)) {
            return $filaInicio;
        }
        
        // Función auxiliar para obtener el nombre legible del tipo de trampa
        $getNombreTipoTrampa = function($tipo) {
            if (empty($tipo)) return 'Sin tipo';
            
            $tipos = [
                'feromona_gorgojo' => 'Trampa de Feromona Gorgojo',
                'equipo_sonico' => 'Equipo Sónico',
                'globo_terror' => 'Globo terror',
                'atrayente_chinches' => 'Trampa atrayente chinches',
                'atrayente_pulgas' => 'Trampa atrayente pulgas',
                'feromona_picudo' => 'Trampa feromonas picudo rojo'
            ];
            
            $tipoNormalizado = $this->normalizarTipoTrampa($tipo);
            foreach ($tipos as $key => $value) {
                if (strpos($tipoNormalizado, $this->normalizarTipoTrampa($key)) !== false) {
                    return $value;
                }
            }
            
            return $tipo;
        };
        
        // Título de la sección
        $sheet->setCellValue('A' . $filaInicio, 'OTRAS TRAMPAS');
        $sheet->getStyle('A' . $filaInicio)->getFont()->setBold(true)->setSize(12);
        $filaInicio += 1;
        
        // Calcular número de columnas (ÁREA + EQUIPO + Tipo de Trampa + todos los tipos de plaga + Total)
        $tiposSinTotal = array_filter($tiposInsectos, function($tipo) {
            return $tipo !== 'Total de insectos';
        });
        $numColumnas = 3 + count($tiposSinTotal) + 1; // ÁREA + EQUIPO + Tipo de Trampa + tipos de plaga + Total
        $ultimaColumnaLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($numColumnas);
        
        // Establecer ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(15);
        $sheet->getColumnDimension('C')->setWidth(20); // Tipo de Trampa
        foreach (range('D', $ultimaColumnaLetra) as $col) {
            $sheet->getColumnDimension($col)->setWidth(15);
        }
        
        $fila = $filaInicio;
        
        // Agregar todos los tipos de insectos (excepto "Total de insectos")
        $tiposSinTotal = array_filter($tiposInsectos, function($tipo) {
            return $tipo !== 'Total de insectos';
        });
        
        // Encabezados
        $columna = 'A';
        $sheet->setCellValue($columna . $fila, 'ÁREA');
        $sheet->getStyle($columna . $fila)->getFont()->setBold(true);
        $sheet->getStyle($columna . $fila)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle($columna . $fila)->getFont()->getColor()->setRGB('FFFFFF');
        $columnaIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columna);
        $columnaIndex++;
        $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
        
        $sheet->setCellValue($columna . $fila, 'EQUIPO');
        $sheet->getStyle($columna . $fila)->getFont()->setBold(true);
        $sheet->getStyle($columna . $fila)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle($columna . $fila)->getFont()->getColor()->setRGB('FFFFFF');
        $columnaIndex++;
        $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
        
        $sheet->setCellValue($columna . $fila, 'Tipo de Trampa');
        $sheet->getStyle($columna . $fila)->getFont()->setBold(true);
        $sheet->getStyle($columna . $fila)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle($columna . $fila)->getFont()->getColor()->setRGB('FFFFFF');
        $columnaIndex++;
        $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
        
        foreach ($tiposSinTotal as $tipo) {
            $sheet->setCellValue($columna . $fila, $tipo);
            $sheet->getStyle($columna . $fila)->getFont()->setBold(true);
            $sheet->getStyle($columna . $fila)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('4472C4');
            $sheet->getStyle($columna . $fila)->getFont()->getColor()->setRGB('FFFFFF');
            if (strlen($tipo) > 12) {
                $sheet->getColumnDimension($columna)->setWidth(18);
            }
            $columnaIndex++;
            $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
        }
        
        $sheet->setCellValue($columna . $fila, 'Total de insectos');
        $sheet->getStyle($columna . $fila)->getFont()->setBold(true);
        $sheet->getStyle($columna . $fila)->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('4472C4');
        $sheet->getStyle($columna . $fila)->getFont()->getColor()->setRGB('FFFFFF');
        
        $fila++;
        $filaInicioTabla = $fila;
        
        // Filas de datos
        foreach ($trampasFiltradas as $trampa) {
            $columna = 'A';
            
            $ubicacion = !empty($trampa['ubicacion']) ? $trampa['ubicacion'] : 'Sin ubicación';
            $sheet->setCellValue($columna . $fila, $ubicacion);
            $columnaIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columna);
            $columnaIndex++;
            $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
            
            $nombreTrampa = !empty($trampa['nombre']) ? $trampa['nombre'] : (!empty($trampa['id_trampa']) ? $trampa['id_trampa'] : 'T' . $trampa['id']);
            $sheet->setCellValue($columna . $fila, $nombreTrampa);
            $columnaIndex++;
            $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
            
            $tipoTrampaNombre = $getNombreTipoTrampa($trampa['tipo'] ?? '');
            $sheet->setCellValue($columna . $fila, $tipoTrampaNombre);
            $columnaIndex++;
            $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
            
            $columnaInicio = $columna;
            foreach ($tiposSinTotal as $tipo) {
                $sheet->setCellValue($columna . $fila, '0');
                $columnaIndex++;
                $columna = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnaIndex);
            }
            
            $columnaFinDatos = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
                \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($columnaInicio) + count($tiposSinTotal) - 1
            );
            $sheet->setCellValue($columna . $fila, '=SUM(' . $columnaInicio . $fila . ':' . $columnaFinDatos . $fila . ')');
            
            $fila++;
        }
        
        // Aplicar bordes
        $range = 'A' . $filaInicioTabla . ':' . $ultimaColumnaLetra . ($fila - 1);
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ];
        $sheet->getStyle($range)->applyFromArray($styleArray);
        
        return $fila + 2; // Dejar espacio antes de la siguiente tabla
    }

    /**
     * Obtiene el nombre del mes en español
     */
    private function obtenerNombreMes($fecha)
    {
        $meses = [
            '01' => 'ENERO', '02' => 'FEBRERO', '03' => 'MARZO', '04' => 'ABRIL',
            '05' => 'MAYO', '06' => 'JUNIO', '07' => 'JULIO', '08' => 'AGOSTO',
            '09' => 'SEPTIEMBRE', '10' => 'OCTUBRE', '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE'
        ];
        
        $mes = date('m', strtotime($fecha));
        return $meses[$mes] ?? 'ENERO';
    }

    /**
     * Procesa la subida de un archivo Excel y devuelve el preview de incidencias
     */
    public function procesarExcel($planoId = null)
    {
        if (!$planoId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Plano no especificado'
            ]);
        }

        // Verificar si PhpSpreadsheet está disponible
        if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'PhpSpreadsheet no está instalado'
            ]);
        }

        // Obtener el archivo
        $archivo = $this->request->getFile('archivo_excel');
        
        if (!$archivo || !$archivo->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No se ha proporcionado un archivo válido'
            ]);
        }

        // Validar extensión del archivo
        $extension = $archivo->getClientExtension();
        $extensionesPermitidas = ['xlsx', 'xls'];
        
        if (!in_array(strtolower($extension), $extensionesPermitidas)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Formato de archivo no soportado. Solo se permiten archivos Excel (.xlsx, .xls)'
            ]);
        }

        // Validar que el archivo no esté vacío
        if ($archivo->getSize() == 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El archivo está vacío o corrupto'
            ]);
        }

        try {
            // Cargar modelos
            $planoModel = new PlanoModel();
            $trampaModel = new TrampaModel();

            // Verificar que el plano existe
            $plano = $planoModel->find($planoId);
            if (!$plano) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Plano no encontrado'
                ]);
            }

            // Obtener todas las trampas del plano para hacer match
            $trampas = $trampaModel->where('plano_id', $planoId)->findAll();
            
            if (empty($trampas)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No hay equipos/trampas registrados en este plano'
                ]);
            }

            // Crear un índice de trampas por ID/nombre para búsqueda rápida
            $trampasIndex = [];
            foreach ($trampas as $trampa) {
                // Priorizar el nombre si está disponible, sino usar id_trampa
                $nombreTrampa = !empty($trampa['nombre']) ? $trampa['nombre'] : (!empty($trampa['id_trampa']) ? $trampa['id_trampa'] : 'T' . $trampa['id']);
                $idTrampa = !empty($trampa['id_trampa']) ? $trampa['id_trampa'] : ($trampa['nombre'] ?? 'T' . $trampa['id']);
                
                // Indexar por nombre (prioridad)
                $nombreNormalizado = strtoupper(trim((string)$nombreTrampa));
                $trampasIndex[$nombreNormalizado] = $trampa;
                
                // También indexar por id_trampa si es diferente del nombre
                $idTrampaNormalizado = strtoupper(trim((string)$idTrampa));
                if ($idTrampaNormalizado !== $nombreNormalizado && !isset($trampasIndex[$idTrampaNormalizado])) {
                    $trampasIndex[$idTrampaNormalizado] = $trampa;
                }
                
                // Si el nombre tiene formato numérico (ej: "0084"), también indexar sin ceros a la izquierda
                if (preg_match('/^0+(\d+)$/', $nombreNormalizado, $matches)) {
                    $nombreSinCeros = $matches[1];
                    if (!isset($trampasIndex[$nombreSinCeros])) {
                        $trampasIndex[$nombreSinCeros] = $trampa;
                    }
                }
                
                // También indexar con ceros a la izquierda si es numérico
                if (is_numeric($nombreNormalizado)) {
                    $nombreConCeros = str_pad($nombreNormalizado, 4, '0', STR_PAD_LEFT);
                    if (!isset($trampasIndex[$nombreConCeros])) {
                        $trampasIndex[$nombreConCeros] = $trampa;
                    }
                }
                
                // Si el id_trampa tiene formato numérico y es diferente, también indexarlo
                if ($idTrampaNormalizado !== $nombreNormalizado && preg_match('/^0+(\d+)$/', $idTrampaNormalizado, $matches)) {
                    $idSinCeros = $matches[1];
                    if (!isset($trampasIndex[$idSinCeros])) {
                        $trampasIndex[$idSinCeros] = $trampa;
                    }
                }
            }
            
            // Log para debug
            log_message('info', 'Total de trampas indexadas: ' . count($trampasIndex));
            log_message('info', 'IDs de trampas en índice: ' . implode(', ', array_keys($trampasIndex)));

            // Leer el archivo Excel usando IOFactory para detectar automáticamente el tipo
            $rutaTemporal = $archivo->getTempName();
            
            // Verificar que el archivo temporal existe y es legible
            if (!file_exists($rutaTemporal) || !is_readable($rutaTemporal)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No se pudo acceder al archivo temporal. Por favor, intente nuevamente.'
                ]);
            }

            // Usar IOFactory para detectar automáticamente el tipo de archivo
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($rutaTemporal);
            } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                // Error específico de lectura
                log_message('error', 'Error al leer archivo Excel: ' . $e->getMessage());
                
                // Mensaje más amigable según el tipo de error
                $mensajeError = 'El archivo Excel no es válido o está corrupto. ';
                
                if (strpos($e->getMessage(), 'zip') !== false || strpos($e->getMessage(), '_rels') !== false) {
                    $mensajeError .= 'El archivo no parece ser un archivo Excel válido. Por favor, verifique que el archivo no esté dañado y descargue la plantilla oficial usando el botón "Descargar Excel" de esta página.';
                } else {
                    $mensajeError .= 'Por favor, descargue la plantilla oficial usando el botón "Descargar Excel" de esta página y vuelva a intentar.';
                }
                
                return $this->response->setJSON([
                    'success' => false,
                    'error_type' => 'archivo_invalido',
                    'message' => $mensajeError
                ]);
            } catch (\Exception $e) {
                // Otros errores
                log_message('error', 'Error inesperado al leer archivo Excel: ' . $e->getMessage());
                
                return $this->response->setJSON([
                    'success' => false,
                    'error_type' => 'archivo_invalido',
                    'message' => 'El archivo Excel no pudo ser procesado. Por favor, verifique que el archivo sea válido y descargue la plantilla oficial usando el botón "Descargar Excel" de esta página.'
                ]);
            }
            
            // Tipos de insectos esperados (debe coincidir con los del Excel)
            $tiposInsectos = [
                'Mosca',
                'Mosca doméstica',
                'Mosca de la fruta',
                'Mosca de drenaje',
                'Moscas metálicas',
                'Mosca forida',
                'Palomillas de almacén',
                'Otras palomillas',
                'Gorgojos',
                'Otros escarabajos',
                'Abejas',
                'Avispas',
                'Mosquitos',
                'Cucaracha',
                'Cucaracha Americana',
                'Cucaracha Alemana',
                'Hormiga',
                'Roedor',
                'Arañas',
                'Escarabajo',
                'Tijerilla',
                'Lagartijas',
                'Insectos de áreas verdes',
                'Otros',
                'Total de insectos'
            ];

            // Mapeo de tipos de insectos a tipos de plaga y tipo de insecto
            $mapeoTiposInsectos = [
                'Mosca' => ['tipo_plaga' => 'mosca', 'tipo_insecto' => 'Volador'],
                'Mosca doméstica' => ['tipo_plaga' => 'mosca_domestica', 'tipo_insecto' => 'Volador'],
                'Mosca de la fruta' => ['tipo_plaga' => 'mosca_fruta', 'tipo_insecto' => 'Volador'],
                'Mosca de drenaje' => ['tipo_plaga' => 'mosca_drenaje', 'tipo_insecto' => 'Volador'],
                'Moscas metálicas' => ['tipo_plaga' => 'mosca_metalica', 'tipo_insecto' => 'Volador'],
                'Mosca forida' => ['tipo_plaga' => 'mosca_forida', 'tipo_insecto' => 'Volador'],
                'Palomillas de almacén' => ['tipo_plaga' => 'palomilla_almacen', 'tipo_insecto' => 'Volador'],
                'Otras palomillas' => ['tipo_plaga' => 'otras_palomillas', 'tipo_insecto' => 'Volador'],
                'Gorgojos' => ['tipo_plaga' => 'gorgojo', 'tipo_insecto' => 'Rastrero'],
                'Otros escarabajos' => ['tipo_plaga' => 'otros_escarabajos', 'tipo_insecto' => 'Rastrero'],
                'Abejas' => ['tipo_plaga' => 'abeja', 'tipo_insecto' => 'Volador'],
                'Avispas' => ['tipo_plaga' => 'avispa', 'tipo_insecto' => 'Volador'],
                'Mosquitos' => ['tipo_plaga' => 'mosquito', 'tipo_insecto' => 'Volador'],
                'Cucaracha' => ['tipo_plaga' => 'cucaracha', 'tipo_insecto' => 'Rastrero'],
                'Cucaracha Americana' => ['tipo_plaga' => 'cucaracha_americana', 'tipo_insecto' => 'Rastrero'],
                'Cucaracha Alemana' => ['tipo_plaga' => 'cucaracha_alemana', 'tipo_insecto' => 'Rastrero'],
                'Hormiga' => ['tipo_plaga' => 'hormiga', 'tipo_insecto' => 'Rastrero'],
                'Roedor' => ['tipo_plaga' => 'roedor', 'tipo_insecto' => 'Rastrero'],
                'Arañas' => ['tipo_plaga' => 'Arañas', 'tipo_insecto' => 'Rastrero'],
                'Escarabajo' => ['tipo_plaga' => 'escarabajo', 'tipo_insecto' => 'Rastrero'],
                'Tijerilla' => ['tipo_plaga' => 'tijerilla', 'tipo_insecto' => 'Rastrero'],
                'Lagartijas' => ['tipo_plaga' => 'Lagartija', 'tipo_insecto' => 'Rastrero'],
                'Insectos de áreas verdes' => ['tipo_plaga' => 'insectos_areas_verdes', 'tipo_insecto' => 'Volador'],
                'Otros' => ['tipo_plaga' => 'otro', 'tipo_insecto' => 'Volador']
            ];

            // Obtener el inspector por defecto de la sesión
            $inspectorPorDefecto = session()->get('nombre') ?? 'Sistema';

            // Procesar cada hoja (semana)
            $incidenciasAgregadas = [];
            $hojas = $spreadsheet->getAllSheets();
            $hojasSinFormato = [];
            $hojasVacias = [];
            $hojasProcesadas = [];
            
            foreach ($hojas as $index => $sheet) {
                $nombreHoja = $sheet->getTitle();
                
                // Extraer el número de semana del nombre de la hoja
                preg_match('/Semana\s+(\d+)/i', $nombreHoja, $matches);
                $numeroSemana = isset($matches[1]) ? (int)$matches[1] : ($index + 1);
                
                // Procesar esta hoja
                $resultado = $this->procesarHojaExcel($sheet, $trampasIndex, $tiposInsectos, $mapeoTiposInsectos, $inspectorPorDefecto, $numeroSemana, $plano);
                
                if (isset($resultado['error'])) {
                    // La hoja tiene formato incorrecto
                    $hojasSinFormato[] = [
                        'nombre' => $nombreHoja,
                        'mensaje' => $resultado['error']
                    ];
                } elseif (isset($resultado['vacia']) && $resultado['vacia']) {
                    // La hoja tiene formato correcto pero está vacía
                    $hojasVacias[] = $nombreHoja;
                } elseif (!empty($resultado['incidencias'])) {
                    // La hoja tiene incidencias
                    $incidenciasAgregadas = array_merge($incidenciasAgregadas, $resultado['incidencias']);
                    $hojasProcesadas[] = $nombreHoja;
                } elseif (!empty($resultado) && is_array($resultado)) {
                    // Si es un array de incidencias directamente (compatibilidad)
                    $incidenciasAgregadas = array_merge($incidenciasAgregadas, $resultado);
                    $hojasProcesadas[] = $nombreHoja;
                } else {
                    // Hoja sin datos pero con formato correcto (por compatibilidad)
                    $hojasVacias[] = $nombreHoja;
                }
            }

            // Si no se procesó ninguna hoja y hay hojas sin formato, es un error de formato
            if (empty($incidenciasAgregadas) && !empty($hojasSinFormato) && empty($hojasVacias)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error_type' => 'formato_incorrecto',
                    'message' => 'No se pudo detectar el formato correcto del archivo Excel. El sistema no encontró los encabezados esperados ("ÁREA" y "EQUIPO") o las columnas de tipos de insectos.',
                    'hojas_sin_formato' => array_column($hojasSinFormato, 'nombre'),
                    'total' => 0
                ]);
            }

            // Si hay incidencias procesadas, retornar éxito (aunque algunas hojas puedan tener errores o estar vacías)
            return $this->response->setJSON([
                'success' => true,
                'incidencias' => $incidenciasAgregadas,
                'total' => count($incidenciasAgregadas),
                'message' => 'Archivo procesado correctamente',
                'hojas_procesadas' => count($hojasProcesadas),
                'hojas_vacias' => $hojasVacias,
                'hojas_con_error' => count($hojasSinFormato),
                'hojas_sin_formato' => !empty($hojasSinFormato) ? array_column($hojasSinFormato, 'nombre') : []
            ]);

        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            // Error específico de PhpSpreadsheet
            log_message('error', 'Error de PhpSpreadsheet: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            $mensajeError = 'El archivo Excel no es válido o está corrupto. ';
            
            if (strpos($e->getMessage(), 'zip') !== false || strpos($e->getMessage(), '_rels') !== false) {
                $mensajeError .= 'El archivo no parece ser un archivo Excel válido. Por favor, verifique que el archivo no esté dañado y descargue la plantilla oficial usando el botón "Descargar Excel" de esta página.';
            } else {
                $mensajeError .= 'Por favor, descargue la plantilla oficial usando el botón "Descargar Excel" de esta página y vuelva a intentar.';
            }
            
            return $this->response->setJSON([
                'success' => false,
                'error_type' => 'archivo_invalido',
                'message' => $mensajeError
            ]);
            
        } catch (\Exception $e) {
            // Otros errores generales
            log_message('error', 'Error al procesar Excel: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'error_type' => 'error_general',
                'message' => 'Error al procesar el archivo: ' . $e->getMessage() . '. Por favor, verifique que el archivo sea válido y descargue la plantilla oficial si es necesario.'
            ]);
        }
    }

    /**
     * Procesa una hoja específica del Excel y extrae las incidencias
     * Ahora soporta múltiples tablas en la misma hoja (EDC Químicas, EDC Adhesivas, Equipo de Luz UV)
     */
    private function procesarHojaExcel($sheet, $trampasIndex, $tiposInsectos, $mapeoTiposInsectos, $inspectorPorDefecto, $numeroSemana, $plano)
    {
        $incidencias = [];
        
        // Buscar la fecha de la semana (buscar "Fecha en la que se registrarán las incidencias")
        $fechaIncidencia = null;
        for ($fila = 1; $fila <= 20; $fila++) {
            $valor = $sheet->getCell('A' . $fila)->getValue();
            if (is_string($valor) && stripos($valor, 'Fecha en la que se registrarán') !== false) {
                $valorFecha = $sheet->getCell('B' . $fila)->getValue();
                if ($valorFecha) {
                    try {
                        if (is_numeric($valorFecha)) {
                            $fechaIncidencia = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($valorFecha)->format('Y-m-d');
                        } else {
                            $fechaIncidencia = date('Y-m-d', strtotime($valorFecha));
                        }
                    } catch (\Exception $e) {
                        $fechaIncidencia = date('Y-m-d');
                    }
                }
                break;
            }
        }
        
        if (!$fechaIncidencia) {
            $fechaIncidencia = date('Y-m-d');
        }
        
        // Títulos de las tablas que buscamos
        $titulosTablas = ['EDC QUÍMICAS', 'EDC ADHESIVAS', 'EQUIPO DE LUZ UV', 'OTRAS TRAMPAS', 'HALLAZGOS', 'EDC QUIMICAS', 'EDC ADHESIVAS', 'EQUIPO DE LUZ UV'];
        
        $ultimaFila = $sheet->getHighestRow();
        
        // Buscar todas las tablas en la hoja
        for ($fila = 1; $fila <= $ultimaFila; $fila++) {
            $valorCelda = trim((string)$sheet->getCell('A' . $fila)->getValue());
            $valorNormalizado = strtoupper($this->normalizarTexto($valorCelda));
            
            // Verificar si es un título de tabla
            $esTituloTabla = false;
            foreach ($titulosTablas as $titulo) {
                $tituloNormalizado = strtoupper($this->normalizarTexto($titulo));
                if ($valorNormalizado === $tituloNormalizado || strpos($valorNormalizado, $tituloNormalizado) !== false) {
                    $esTituloTabla = true;
                    break;
                }
            }
            
            if ($esTituloTabla) {
                // Verificar si es tabla de hallazgos
                $esTablaHallazgos = (stripos($valorNormalizado, 'HALLAZGOS') !== false || stripos($valorNormalizado, 'HALLAZGO') !== false);
                
                // Buscar los encabezados de esta tabla (deben estar en la siguiente fila)
                $filaEncabezados = null;
                $columnasTipos = [];
                
                // Buscar encabezados desde la fila siguiente hasta 5 filas después
                for ($filaBuscar = $fila + 1; $filaBuscar <= min($fila + 5, $ultimaFila); $filaBuscar++) {
                    $valorArea = trim((string)$sheet->getCell('A' . $filaBuscar)->getValue());
                    $valorEquipo = trim((string)$sheet->getCell('B' . $filaBuscar)->getValue());
                    
                    // Para tabla de hallazgos, buscar "ZONA" en lugar de "ÁREA" y "EQUIPO"
                    if ($esTablaHallazgos) {
                        if (strtoupper($valorArea) === 'ZONA') {
                            $filaEncabezados = $filaBuscar;
                            
                            // Buscar las columnas de tipos de insectos (empiezan en B para hallazgos)
                            $columnaActual = 'B';
                            $maxColumnas = 30;
                            $columnaIndex = 1;
                            
                            while ($columnaIndex < $maxColumnas) {
                                $valorColumna = trim((string)$sheet->getCell($columnaActual . $filaEncabezados)->getValue());
                                
                                if (stripos($valorColumna, 'Total') !== false && stripos($valorColumna, 'insectos') !== false) {
                                    break;
                                }
                                
                                if (empty($valorColumna)) {
                                    $columnaActual++;
                                    $columnaIndex++;
                                    continue;
                                }
                                
                                foreach ($tiposInsectos as $tipo) {
                                    if ($tipo === 'Total de insectos') {
                                        continue;
                                    }
                                    
                                    $tipoNormalizado = $this->normalizarTexto($tipo);
                                    $valorNormalizado = $this->normalizarTexto($valorColumna);
                                    
                                    if ($tipoNormalizado === $valorNormalizado) {
                                        if (!isset($columnasTipos[$tipo])) {
                                            $columnasTipos[$tipo] = $columnaActual;
                                        }
                                        break;
                                    }
                                }
                                
                                $columnaActual++;
                                $columnaIndex++;
                            }
                            
                            break;
                        }
                    } else {
                        // Para tablas normales (EDC, Equipo de Luz, Otras Trampas)
                        if (strtoupper($valorArea) === 'ÁREA' || strtoupper($valorArea) === 'AREA') {
                            if (strtoupper($valorEquipo) === 'EQUIPO') {
                                $filaEncabezados = $filaBuscar;
                                
                                // Buscar las columnas de tipos de insectos
                                $columnaActual = 'C';
                                $maxColumnas = 30;
                                $columnaIndex = 2;
                                
                                while ($columnaIndex < $maxColumnas) {
                                    $valorColumna = trim((string)$sheet->getCell($columnaActual . $filaEncabezados)->getValue());
                                    
                                    if (stripos($valorColumna, 'Total') !== false && stripos($valorColumna, 'insectos') !== false) {
                                        break;
                                    }
                                    
                                    if (empty($valorColumna)) {
                                        $columnaActual++;
                                        $columnaIndex++;
                                        continue;
                                    }
                                    
                                    foreach ($tiposInsectos as $tipo) {
                                        if ($tipo === 'Total de insectos') {
                                            continue;
                                        }
                                        
                                        $tipoNormalizado = $this->normalizarTexto($tipo);
                                        $valorNormalizado = $this->normalizarTexto($valorColumna);
                                        
                                        if ($tipoNormalizado === $valorNormalizado) {
                                            if (!isset($columnasTipos[$tipo])) {
                                                $columnasTipos[$tipo] = $columnaActual;
                                            }
                                            break;
                                        }
                                    }
                                    
                                    $columnaActual++;
                                    $columnaIndex++;
                                }
                                
                                break;
                            }
                        }
                    }
                }
                
                if ($filaEncabezados && !empty($columnasTipos)) {
                    // Procesar las filas de esta tabla
                    $filaInicioDatos = $filaEncabezados + 1;
                    $filaFinDatos = $ultimaFila;
                    
                    // Buscar dónde termina esta tabla (siguiente título o fin de hoja)
                    for ($filaFin = $filaInicioDatos; $filaFin <= $ultimaFila; $filaFin++) {
                        $valorSiguiente = trim((string)$sheet->getCell('A' . $filaFin)->getValue());
                        $valorSiguienteNormalizado = strtoupper($this->normalizarTexto($valorSiguiente));
                        
                        // Si encontramos otro título de tabla o una fila vacía después de varias filas con datos, terminar
                        $esOtroTitulo = false;
                        foreach ($titulosTablas as $titulo) {
                            $tituloNormalizado = strtoupper($this->normalizarTexto($titulo));
                            if ($valorSiguienteNormalizado === $tituloNormalizado || strpos($valorSiguienteNormalizado, $tituloNormalizado) !== false) {
                                $esOtroTitulo = true;
                                break;
                            }
                        }
                        
                        if ($esOtroTitulo) {
                            $filaFinDatos = $filaFin - 1;
                            break;
                        }
                    }
                    
                    // Determinar si es tabla de otras trampas (tiene columna "Tipo de Trampa")
                    $esTablaOtrasTrampas = false;
                    if ($filaEncabezados) {
                        $valorTipoTrampa = trim((string)$sheet->getCell('C' . $filaEncabezados)->getValue());
                        $esTablaOtrasTrampas = (stripos($valorTipoTrampa, 'tipo') !== false && stripos($valorTipoTrampa, 'trampa') !== false);
                    }
                    
                    // Procesar las filas de esta tabla
                    for ($filaDatos = $filaInicioDatos; $filaDatos <= $filaFinDatos; $filaDatos++) {
                        // Para tabla de hallazgos, procesar de manera diferente
                        if ($esTablaHallazgos) {
                            // Obtener la zona (columna A)
                            $zona = trim((string)$sheet->getCell('A' . $filaDatos)->getValue());
                            
                            if (empty($zona)) {
                                continue;
                            }
                            
                            // Procesar cada tipo de insecto para esta zona
                            foreach ($columnasTipos as $tipoInsecto => $columna) {
                                $cantidad = $sheet->getCell($columna . $filaDatos)->getValue();
                                
                                // Convertir a número
                                if (is_numeric($cantidad)) {
                                    $cantidad = (float)$cantidad;
                                } else {
                                    $cantidad = 0;
                                }
                                
                                if ($cantidad > 0 && isset($mapeoTiposInsectos[$tipoInsecto])) {
                                    $mapeo = $mapeoTiposInsectos[$tipoInsecto];
                                    
                                    $incidencias[] = [
                                        'id_trampa' => null, // Hallazgos no tienen trampa
                                        'trampa_id' => null,
                                        'zona' => $zona,
                                        'tipo_plaga' => $mapeo['tipo_plaga'],
                                        'tipo_insecto' => $mapeo['tipo_insecto'],
                                        'tipo_incidencia' => 'Hallazgo',
                                        'cantidad_organismos' => (int)$cantidad,
                                        'fecha_incidencia' => $fechaIncidencia,
                                        'fecha' => $fechaIncidencia,
                                        'inspector' => $inspectorPorDefecto,
                                        'notas' => 'Zona: ' . $zona,
                                        'sede_id' => $plano['sede_id'],
                                        'semana' => $numeroSemana
                                    ];
                                }
                            }
                            
                            continue; // Continuar con la siguiente fila
                        }
                        
                        // Para tablas normales (EDC, Equipo de Luz, Otras Trampas)
                        // Obtener el ID del equipo (siempre está en la columna B)
                        $idEquipoExcel = $sheet->getCell('B' . $filaDatos)->getValue();
                        
                        // Convertir a string si es numérico
                        if (is_numeric($idEquipoExcel)) {
                            $idEquipoExcel = (string)$idEquipoExcel;
                            if (strlen($idEquipoExcel) < 4 && is_numeric($idEquipoExcel)) {
                                $idEquipoExcel = str_pad($idEquipoExcel, 4, '0', STR_PAD_LEFT);
                            }
                        }
                        
                        if (empty($idEquipoExcel)) {
                            continue;
                        }

                        // Normalizar el ID del equipo para búsqueda
                        $idEquipoNormalizado = strtoupper(trim((string)$idEquipoExcel));
                        
                        $idsParaBuscar = [$idEquipoNormalizado];
                        if (is_numeric($idEquipoNormalizado)) {
                            $idConCeros = str_pad($idEquipoNormalizado, 4, '0', STR_PAD_LEFT);
                            if ($idConCeros !== $idEquipoNormalizado) {
                                $idsParaBuscar[] = $idConCeros;
                            }
                            $idSinCeros = ltrim($idEquipoNormalizado, '0');
                            if ($idSinCeros !== $idEquipoNormalizado && !empty($idSinCeros)) {
                                $idsParaBuscar[] = $idSinCeros;
                            }
                        }
                        
                        // Buscar la trampa correspondiente
                        $trampa = null;
                        foreach ($idsParaBuscar as $idBuscar) {
                            if (isset($trampasIndex[$idBuscar])) {
                                $trampa = $trampasIndex[$idBuscar];
                                break;
                            }
                        }
                        
                        if (!$trampa) {
                            continue;
                        }
                        
                        // Procesar cada tipo de insecto
                        foreach ($columnasTipos as $tipoInsecto => $columna) {
                            $cell = $sheet->getCell($columna . $filaDatos);
                            $cantidad = null;
                            
                            try {
                                $cantidad = $cell->getCalculatedValue();
                            } catch (\Exception $e) {
                                $cantidad = $cell->getValue();
                            }
                            
                            if ($cantidad === null) {
                                $cantidad = $cell->getFormattedValue();
                            }
                            
                            if (is_numeric($cantidad)) {
                                $cantidad = (float)$cantidad;
                            } elseif (is_string($cantidad)) {
                                $cantidad = trim($cantidad);
                                $cantidad = preg_replace('/[^0-9.]/', '', $cantidad);
                                $cantidad = !empty($cantidad) ? (float)$cantidad : 0;
                            } else {
                                $cantidad = 0;
                            }
                            
                            if ($cantidad > 0 && isset($mapeoTiposInsectos[$tipoInsecto])) {
                                $mapeo = $mapeoTiposInsectos[$tipoInsecto];
                                
                                $incidencias[] = [
                                    'id_trampa' => !empty($trampa['id_trampa']) ? $trampa['id_trampa'] : ('T' . $trampa['id']),
                                    'trampa_id' => $trampa['id'],
                                    'tipo_plaga' => $mapeo['tipo_plaga'],
                                    'tipo_insecto' => $mapeo['tipo_insecto'],
                                    'tipo_incidencia' => 'Captura',
                                    'cantidad_organismos' => (int)$cantidad,
                                    'fecha_incidencia' => $fechaIncidencia,
                                    'fecha' => $fechaIncidencia, // Agregar campo fecha para consistencia
                                    'inspector' => $inspectorPorDefecto,
                                    'notas' => '',
                                    'semana' => $numeroSemana
                                ];
                            }
                        }
                    }
                }
            }
        }
        
        // Si no se encontraron incidencias, verificar si se encontraron tablas (formato correcto pero vacío)
        if (empty($incidencias)) {
            // Verificar si se encontró al menos una tabla con encabezados (formato correcto)
            $formatoCorrecto = false;
            for ($fila = 1; $fila <= $ultimaFila; $fila++) {
                $valorCelda = trim((string)$sheet->getCell('A' . $fila)->getValue());
                $valorNormalizado = strtoupper($this->normalizarTexto($valorCelda));
                
                foreach ($titulosTablas as $titulo) {
                    $tituloNormalizado = strtoupper($this->normalizarTexto($titulo));
                    if ($valorNormalizado === $tituloNormalizado || strpos($valorNormalizado, $tituloNormalizado) !== false) {
                        // Buscar si hay encabezados después de este título
                        for ($filaBuscar = $fila + 1; $filaBuscar <= min($fila + 5, $ultimaFila); $filaBuscar++) {
                            $valorArea = trim((string)$sheet->getCell('A' . $filaBuscar)->getValue());
                            $valorEquipo = trim((string)$sheet->getCell('B' . $filaBuscar)->getValue());
                            
                            if ((strtoupper($valorArea) === 'ÁREA' || strtoupper($valorArea) === 'AREA') && 
                                strtoupper($valorEquipo) === 'EQUIPO') {
                                $formatoCorrecto = true;
                                break 2;
                            }
                        }
                    }
                }
            }
            
            if ($formatoCorrecto) {
                // Formato correcto pero sin datos (hoja vacía)
                return [
                    'incidencias' => [],
                    'error' => null,
                    'vacia' => true
                ];
            } else {
                // Formato incorrecto (no se encontraron las tablas esperadas)
                return [
                    'error' => 'No se encontraron tablas con el formato esperado en esta hoja',
                    'incidencias' => [],
                    'vacia' => false
                ];
            }
        }
        
        return [
            'incidencias' => $incidencias,
            'error' => null,
            'vacia' => false
        ];
    }

    /**
     * Normaliza texto eliminando acentos y caracteres especiales para comparación
     */
    private function normalizarTexto($texto)
    {
        $texto = strtoupper(trim($texto));
        // Reemplazar acentos
        $texto = str_replace(['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'], ['A', 'E', 'I', 'O', 'U', 'N'], $texto);
        $texto = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ'], ['A', 'E', 'I', 'O', 'U', 'N'], $texto);
        // Eliminar espacios múltiples
        $texto = preg_replace('/\s+/', ' ', $texto);
        return trim($texto);
    }

}

