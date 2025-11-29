<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Completo - <?= $plano['nombre'] ?></title>
    <style>
        /* Estilos globales y tipografía */
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }
        .container {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 10px;
        }
        
        /* Cabecera del documento */
        .header {
            text-align: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #2c3e50;
            position: relative;
        }
        .header h1 {
            margin: 5px 0;
            color: #2c3e50;
            font-size: 28px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0;
            color: #555;
        }
        .header::before {
            content: "";
            position: absolute;
            bottom: 0;
            left: 25%;
            width: 50%;
            height: 3px;
            background-color: #7f8c8d;
        }
        
        /* Secciones de información */
        .info-section {
            margin-bottom: 25px;
            background-color: #f9f9f9;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .info-section h2 {
            color: #2c3e50;
            border-bottom: 2px solid #eee;
            padding-bottom: 8px;
            margin-top: 0;
            font-size: 20px;
        }
        .info-item {
            margin-bottom: 8px;
            display: flex;
        }
        .info-item strong {
            font-weight: bold;
            min-width: 180px;
            display: inline-block;
            color: #555;
        }
        
        /* Imagen del plano */
        .image-container {
            width: 100%;
            text-align: center;
            margin: 20px 0;
            background-color: #f5f5f5;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .blueprint-image {
            max-width: 100%;
            max-height: 400px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        /* Tablas de datos */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            border-radius: 5px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        table, th, td {
            border: 1px solid #e0e0e0;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 13px;
        }
        tr:nth-child(even) {
            background-color: #f5f5f5;
        }
        tr:hover {
            background-color: #f0f0f0;
        }
        
        /* Pie de página */
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
            padding-top: 15px;
            border-top: 1px solid #ddd;
        }
        
        /* Filtros aplicados */
        .filter-tag {
            display: inline-block;
            padding: 5px 10px;
            background-color: #eee;
            border: 1px solid #ddd;
            border-radius: 20px;
            margin-right: 8px;
            font-size: 13px;
            margin-bottom: 8px;
            color: #333;
        }
        .filter-section {
            margin-bottom: 20px;
            padding: 12px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .filter-section-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        /* Resumen estadístico */
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
        }
        .summary-box h3 {
            margin-top: 0;
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 18px;
            border-bottom: 1px solid #eaecef;
            padding-bottom: 8px;
        }
        
        /* Filas de estadísticas */
        .stats-row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -8px;
        }
        .stat-column {
            flex: 1;
            padding: 0 8px;
            margin-bottom: 15px;
            min-width: 200px;
        }
        .stat-card {
            background-color: white;
            border-radius: 6px;
            padding: 15px;
            height: 100%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 4px solid #2c3e50;
        }
        .stat-card.trap {
            border-color: #3498db;
        }
        .stat-card.incident {
            border-color: #e74c3c;
        }
        .stat-card h4 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 16px;
            margin-bottom: 10px;
            border-bottom: 1px solid #f5f5f5;
            padding-bottom: 8px;
        }
        .stat-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .stat-list li {
            padding: 5px 0;
            display: flex;
            justify-content: space-between;
            border-bottom: 1px dashed #eee;
        }
        .stat-list li:last-child {
            border-bottom: none;
        }
        .stat-list-label {
            font-weight: bold;
        }
        .stat-list-value {
            color: #2c3e50;
        }
        
        /* Tarjetas de datos */
        .data-card {
            background-color: white;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .data-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .data-card-title {
            margin: 0;
            font-size: 16px;
            color: #2c3e50;
        }
        .data-card-value {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
        }
        
        /* Gráficos de barras */
        .bar-chart {
            margin-top: 15px;
        }
        .bar-item {
            margin-bottom: 10px;
        }
        .bar-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 13px;
        }
        .bar-name {
            font-weight: bold;
        }
        .bar-value {
            color: #7f8c8d;
        }
        .bar-container {
            height: 12px;
            background-color: #ecf0f1;
            border-radius: 6px;
            overflow: hidden;
        }
        .bar-fill {
            height: 100%;
            background-color: #3498db;
            border-radius: 6px;
        }
        .bar-fill.incident {
            background-color: #e74c3c;
        }
        
        /* Mensajes vacíos */
        .empty-message {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            color: #6c757d;
            font-style: italic;
        }
        
        /* Estilos para indicadores */
        .indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 5px;
        }
        .indicator-blue {
            background-color: #3498db;
        }
        .indicator-red {
            background-color: #e74c3c;
        }
        .indicator-green {
            background-color: #2ecc71;
        }
        .indicator-yellow {
            background-color: #f1c40f;
        }
        
        /* Etiquetas de estado */
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .status-active {
            background-color: #2ecc71;
        }
        .status-warning {
            background-color: #f39c12;
        }
        .status-danger {
            background-color: #e74c3c;
        }
        
        /* Divisor de página para impresión */
        .page-break {
            page-break-before: always;
        }
        
        /* Estilos responsive para impresión */
        @media print {
            body {
                font-size: 12px;
            }
            .header h1 {
                font-size: 22px;
            }
            .container {
                width: 100%;
                max-width: none;
                padding: 0;
            }
            .page-break {
                page-break-before: always;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reporte Completo</h1>
            <p><?= $plano['nombre'] ?> - <?= $sede['nombre'] ?? 'Sede no especificada' ?></p>
            <p>Fecha de generación: <?= date('d/m/Y H:i', strtotime($fecha_generacion)) ?></p>
        </div>
        
        <!-- Resumen Ejecutivo -->
        <div class="executive-summary" style="margin-bottom: 30px; padding: 20px; background-color: #f8f9fa; border-radius: 8px; border-left: 5px solid #2c3e50;">
            <h2 style="color: #2c3e50; margin-top: 0; font-size: 22px; margin-bottom: 15px;">Resumen Ejecutivo</h2>
            <?php
            // Calcular estadísticas clave
            $totalTrampas = count($trampas);
            $totalIncidencias = count($incidencias);
            $trampasSinIncidencias = 0;
            $trampasConIncidencias = [];
            $totalOrganismos = 0;
            $tiposPlaga = [];
            $tiposIncidencia = [];
            
            foreach ($trampas as $trampa) {
                $conIncidencias = false;
                foreach ($incidencias as $incidencia) {
                    if ($incidencia['id_trampa'] == $trampa['id']) {
                        $conIncidencias = true;
                        if (!isset($trampasConIncidencias[$trampa['id']])) {
                            $trampasConIncidencias[$trampa['id']] = 0;
                        }
                        $trampasConIncidencias[$trampa['id']]++;
                        
                        // Contar organismos
                        $totalOrganismos += (int)($incidencia['cantidad_organismos'] ?? 0);
                        
                        // Contar tipos de plaga
                        $tipoPlaga = $incidencia['tipo_plaga'] ?? 'Desconocido';
                        if (!isset($tiposPlaga[$tipoPlaga])) {
                            $tiposPlaga[$tipoPlaga] = 0;
                        }
                        $tiposPlaga[$tipoPlaga]++;
                        
                        // Contar tipos de incidencia
                        $tipoInc = $incidencia['tipo_incidencia'] ?? 'Desconocido';
                        if (!isset($tiposIncidencia[$tipoInc])) {
                            $tiposIncidencia[$tipoInc] = 0;
                        }
                        $tiposIncidencia[$tipoInc]++;
                    }
                }
                if (!$conIncidencias) {
                    $trampasSinIncidencias++;
                }
            }
            
            // Ordenar tipos de plaga por frecuencia
            arsort($tiposPlaga);
            $plagaMasComun = key($tiposPlaga);
            
            // Ordenar tipos de incidencia por frecuencia
            arsort($tiposIncidencia);
            $incidenciaMasComun = key($tiposIncidencia);
            
            // Calcular porcentajes
            $porcentajeTrampasAfectadas = $totalTrampas > 0 ? (count($trampasConIncidencias) / $totalTrampas) * 100 : 0;
            ?>
            
            <p style="font-size: 14px; line-height: 1.6; margin-bottom: 15px;">
                Este reporte presenta un análisis detallado del sistema de control de plagas en <strong><?= $sede['nombre'] ?? 'la sede' ?></strong>, 
                específicamente para el plano "<?= $plano['nombre'] ?>". El análisis abarca un total de <strong><?= $totalTrampas ?> trampas</strong> 
                instaladas y monitoreadas.
            </p>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 15px;">
                <div style="background: #fff; padding: 15px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <h3 style="color: #2c3e50; margin-top: 0; font-size: 16px; margin-bottom: 10px;">Hallazgos Principales</h3>
                    <ul style="margin: 0; padding-left: 20px; list-style-type: disc;">
                        <li>Se detectaron <strong><?= $totalIncidencias ?> incidencias</strong> en total</li>
                        <li><strong><?= round($porcentajeTrampasAfectadas, 1) ?>%</strong> de las trampas presentaron actividad</li>
                        <li>Se capturaron <strong><?= $totalOrganismos ?> organismos</strong> en total</li>
                        <li>Plaga más común: <strong><?= $plagaMasComun ?></strong></li>
                    </ul>
                </div>
                
                <div style="background: #fff; padding: 15px; border-radius: 6px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <h3 style="color: #2c3e50; margin-top: 0; font-size: 16px; margin-bottom: 10px;">Estado del Control</h3>
                    <ul style="margin: 0; padding-left: 20px; list-style-type: disc;">
                        <li><strong><?= $trampasSinIncidencias ?></strong> trampas sin actividad</li>
                        <li><strong><?= count($trampasConIncidencias) ?></strong> trampas con incidencias</li>
                        <li>Tipo de incidencia más común: <strong><?= $incidenciaMasComun ?></strong></li>
                        <li>Se identificaron <strong><?= count($tiposPlaga) ?></strong> tipos diferentes de plagas</li>
                    </ul>
                </div>
            </div>
            
            <?php if ($totalIncidencias > 0): ?>
            <div style="font-size: 14px; color: #666; font-style: italic;">
                Recomendación: Prestar especial atención a las zonas con mayor concentración de <?= $plagaMasComun ?>, 
                ya que representa la plaga más frecuente en este período.
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Sección de filtros aplicados -->
        <div class="filter-section">
            <div class="filter-section-title">Filtros aplicados:</div>
            <span class="filter-tag">
                <?php if ($filtroPlaga === 'todos'): ?>
                    <i>Todas las plagas</i>
                <?php else: ?>
                    <i>Plaga:</i> <?= ucfirst($filtroPlaga) ?>
                <?php endif; ?>
            </span>
            <span class="filter-tag">
                <?php if ($filtroIncidencia === 'todos'): ?>
                    <i>Todas las incidencias</i>
                <?php else: ?>
                    <i>Incidencia:</i> <?= $filtroIncidencia ?>
                <?php endif; ?>
            </span>
        </div>
        
        <div class="info-section">
            <h2>Información del Plano</h2>
            <div class="info-item"><strong>Nombre:</strong> <?= $plano['nombre'] ?></div>
            <div class="info-item"><strong>Descripción:</strong> <?= $plano['descripcion'] ?></div>
            <div class="info-item"><strong>Fecha de creación:</strong> <?= date('d/m/Y', strtotime($plano['fecha_creacion'])) ?></div>
            <div class="info-item"><strong>Sede:</strong> <?= $sede['nombre'] ?? 'No especificada' ?></div>
            <div class="info-item"><strong>Dirección:</strong> <?= $sede['direccion'] ?? 'No especificada' ?></div>
            <div class="info-item"><strong>Ciudad:</strong> <?= $sede['ciudad'] ?? 'No especificada' ?></div>
            <div class="info-item"><strong>País:</strong> <?= $sede['pais'] ?? 'No especificado' ?></div>
        </div>
        
        <?php if (!empty($imagen_url)): ?>
        <div class="image-container">
            <img src="<?= $imagen_url ?>" alt="Plano <?= $plano['nombre'] ?>" class="blueprint-image">
            <p style="margin-top: 10px; color: #666; font-style: italic;">Vista del plano de ubicación de trampas</p>
        </div>
        <?php endif; ?>
        
        <div class="summary-box">
            <h3>Resumen de Trampas e Incidencias</h3>
            
            <?php
            // Preparar datos para las estadísticas
            $totalTrampas = count($trampas);
            $totalIncidencias = count($incidencias);
            $totalOrganismos = 0;
            
            // Contar tipos de trampas
            $tiposTrampa = [];
            foreach ($trampas as $trampa) {
                $tipo = $trampa['tipo'] ?? 'Otro';
                if (!isset($tiposTrampa[$tipo])) {
                    $tiposTrampa[$tipo] = 0;
                }
                $tiposTrampa[$tipo]++;
            }
            
            // Calcular trampas con y sin incidencias
            $trampasSinIncidencias = 0;
            $trampasConIncidencias = [];
            foreach ($trampas as $trampa) {
                $conIncidencias = false;
                foreach ($incidencias as $incidencia) {
                    if ($incidencia['id_trampa'] == $trampa['id']) {
                        $conIncidencias = true;
                        if (!isset($trampasConIncidencias[$trampa['id']])) {
                            $trampasConIncidencias[$trampa['id']] = 0;
                        }
                        $trampasConIncidencias[$trampa['id']]++;
                        
                        // Sumar organismos
                        $totalOrganismos += (int)($incidencia['cantidad_organismos'] ?? 0);
                        break;
                    }
                }
                if (!$conIncidencias) {
                    $trampasSinIncidencias++;
                }
            }
            
            // Contar tipos de incidencias y plagas
            $tiposIncidencia = [];
            $tiposPlaga = [];
            foreach ($incidencias as $incidencia) {
                // Contar por tipo de incidencia
                $tipoInc = $incidencia['tipo_incidencia'] ?? 'Desconocido';
                if (!isset($tiposIncidencia[$tipoInc])) {
                    $tiposIncidencia[$tipoInc] = 0;
                }
                $tiposIncidencia[$tipoInc]++;
                
                // Contar por tipo de plaga
                $tipoPlaga = $incidencia['tipo_plaga'] ?? 'Desconocido';
                if (!isset($tiposPlaga[$tipoPlaga])) {
                    $tiposPlaga[$tipoPlaga] = 0;
                }
                $tiposPlaga[$tipoPlaga]++;
            }
            ?>
            
            <!-- Tarjetas de resumen -->
            <div class="stats-row">
                <div class="stat-column">
                    <div class="stat-card trap">
                        <h4>Trampas</h4>
                        <ul class="stat-list">
                            <li>
                                <span class="stat-list-label">Total trampas:</span>
                                <span class="stat-list-value"><?= $totalTrampas ?></span>
                            </li>
                            <li>
                                <span class="stat-list-label">Sin incidencias:</span>
                                <span class="stat-list-value"><?= $trampasSinIncidencias ?></span>
                            </li>
                            <li>
                                <span class="stat-list-label">Con incidencias:</span>
                                <span class="stat-list-value"><?= count($trampasConIncidencias) ?></span>
                            </li>
                            <li>
                                <span class="stat-list-label">% con problemas:</span>
                                <span class="stat-list-value">
                                    <?= $totalTrampas > 0 ? round((count($trampasConIncidencias) / $totalTrampas) * 100) : 0 ?>%
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="stat-column">
                    <div class="stat-card incident">
                        <h4>Incidencias</h4>
                        <ul class="stat-list">
                            <li>
                                <span class="stat-list-label">Total incidencias:</span>
                                <span class="stat-list-value"><?= $totalIncidencias ?></span>
                            </li>
                            <li>
                                <span class="stat-list-label">Tipos de plaga:</span>
                                <span class="stat-list-value"><?= count($tiposPlaga) ?></span>
                            </li>
                            <li>
                                <span class="stat-list-label">Tipos de incidencia:</span>
                                <span class="stat-list-value"><?= count($tiposIncidencia) ?></span>
                            </li>
                            <li>
                                <span class="stat-list-label">Total organismos:</span>
                                <span class="stat-list-value"><?= $totalOrganismos ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Gráficos de barras -->
            <div class="stats-row">
                <?php if (count($tiposTrampa) > 0): ?>
                <div class="stat-column">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h4 class="data-card-title">Distribución de Trampas</h4>
                        </div>
                        <div class="bar-chart">
                            <?php 
                            arsort($tiposTrampa);
                            $maxValue = max($tiposTrampa);
                            foreach ($tiposTrampa as $tipo => $cantidad): 
                                $porcentaje = ($cantidad / $maxValue) * 100;
                            ?>
                            <div class="bar-item">
                                <div class="bar-label">
                                    <span class="bar-name">
                                        <span class="indicator indicator-blue"></span>
                                        <?= $tipo ?>
                                    </span>
                                    <span class="bar-value"><?= $cantidad ?> (<?= round(($cantidad / $totalTrampas) * 100) ?>%)</span>
                                </div>
                                <div class="bar-container">
                                    <div class="bar-fill" style="width: <?= $porcentaje ?>%;"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (count($tiposPlaga) > 0): ?>
                <div class="stat-column">
                    <div class="data-card">
                        <div class="data-card-header">
                            <h4 class="data-card-title">Distribución de Plagas</h4>
                        </div>
                        <div class="bar-chart">
                            <?php 
                            arsort($tiposPlaga);
                            $maxValue = max($tiposPlaga);
                            foreach ($tiposPlaga as $tipo => $cantidad): 
                                $porcentaje = ($cantidad / $maxValue) * 100;
                            ?>
                            <div class="bar-item">
                                <div class="bar-label">
                                    <span class="bar-name">
                                        <span class="indicator indicator-red"></span>
                                        <?= $tipo ?>
                                    </span>
                                    <span class="bar-value"><?= $cantidad ?> (<?= round(($cantidad / $totalIncidencias) * 100) ?>%)</span>
                                </div>
                                <div class="bar-container">
                                    <div class="bar-fill incident" style="width: <?= $porcentaje ?>%;"></div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Lista de Trampas -->
        <?php if (count($trampas) > 0): ?>
        <div class="info-section">
            <h2>Listado de Trampas</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 7%">ID</th>
                        <th style="width: 15%">Tipo</th>
                        <th style="width: 25%">Ubicación</th>
                        <th style="width: 15%">Instalación</th>
                        <th style="width: 18%">Coordenadas</th>
                        <th style="width: 20%">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trampas as $trampa): 
                        // Contar incidencias para esta trampa (solo las filtradas)
                        $incidenciasTrampa = 0;
                        foreach ($incidencias as $incidencia) {
                            if ($incidencia['id_trampa'] == $trampa['id']) {
                                $incidenciasTrampa++;
                            }
                        }
                        
                        // Determinar el estado según si tiene incidencias
                        $estadoClase = '';
                        $estadoTexto = '';
                        
                        if ($incidenciasTrampa > 10) {
                            $estadoClase = 'status-danger';
                            $estadoTexto = 'Crítico';
                        } elseif ($incidenciasTrampa > 0) {
                            $estadoClase = 'status-warning';
                            $estadoTexto = 'Alerta';
                        } else {
                            $estadoClase = 'status-active';
                            $estadoTexto = 'Normal';
                        }
                        
                        // Determinar el color de fondo según si tiene incidencias
                        $rowColor = $incidenciasTrampa > 0 ? 'background-color: #fff8e1;' : '';
                    ?>
                    <tr style="<?= $rowColor ?>">
                        <td><?= $trampa['id_trampa'] ?? ('T'.$trampa['id']) ?></td>
                        <td><?= $trampa['tipo'] ?></td>
                        <td><?= $trampa['ubicacion'] ?></td>
                        <td><?= date('d/m/Y', strtotime($trampa['fecha_instalacion'])) ?></td>
                        <td><?= round($trampa['coordenada_x'], 2) ?>, <?= round($trampa['coordenada_y'], 2) ?></td>
                        <td>
                            <span class="status-badge <?= $estadoClase ?>"><?= $estadoTexto ?></span>
                            <?php if ($incidenciasTrampa > 0): ?>
                                <span style="margin-left: 5px;"><?= $incidenciasTrampa ?> incidencia(s)</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-message">
            <p>No hay trampas registradas para este plano.</p>
        </div>
        <?php endif; ?>
        
        <!-- Salto de página para iniciar la sección de incidencias -->
        <div class="page-break"></div>
        
        <!-- Lista de Incidencias -->
        <?php if (count($incidencias) > 0): ?>
        <div class="info-section">
            <h2>Listado de Incidencias (Filtradas)</h2>
            <table>
                <thead>
                    <tr>
                        <th style="width: 7%">ID</th>
                        <th style="width: 18%">Trampa</th>
                        <th style="width: 15%">Tipo Incidencia</th>
                        <th style="width: 15%">Tipo Plaga</th>
                        <th style="width: 10%">Cantidad</th>
                        <th style="width: 15%">Fecha</th>
                        <th style="width: 20%">Inspector</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incidencias as $incidencia): 
                        // Buscar la trampa correspondiente
                        $trampaInfo = 'No especificada';
                        foreach ($trampas as $trampa) {
                            if ($trampa['id'] == $incidencia['id_trampa']) {
                                $trampaInfo = $trampa['tipo'] . ' (' . $trampa['ubicacion'] . ')';
                                break;
                            }
                        }
                        
                        // Formatear fecha
                        $fecha = 'No disponible';
                        if (!empty($incidencia['fecha'])) {
                            $fecha = date('d/m/Y', strtotime($incidencia['fecha']));
                        }
                        
                        // Determinar severidad basada en cantidad
                        $cantidad = (int)($incidencia['cantidad_organismos'] ?? 0);
                        $severidad = '';
                        $severidadClass = '';
                        if ($cantidad > 10) {
                            $severidad = 'Alta';
                            $severidadClass = 'status-danger';
                        } elseif ($cantidad > 5) {
                            $severidad = 'Media';
                            $severidadClass = 'status-warning';
                        } else {
                            $severidad = 'Baja';
                            $severidadClass = 'status-active';
                        }
                    ?>
                    <tr>
                        <td><?= $incidencia['id'] ?></td>
                        <td><?= $trampaInfo ?></td>
                        <td><?= $incidencia['tipo_incidencia'] ?? 'No especificado' ?></td>
                        <td><?= $incidencia['tipo_plaga'] ?? 'No especificado' ?></td>
                        <td>
                            <?= $cantidad ?>
                            <span class="status-badge <?= $severidadClass ?>"><?= $severidad ?></span>
                        </td>
                        <td><?= $fecha ?></td>
                        <td><?= $incidencia['inspector'] ?? 'No especificado' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-message">
            <p>No hay incidencias registradas para este plano con los filtros aplicados.</p>
        </div>
        <?php endif; ?>
        
        <div class="footer">
            <p>Servipro - Sistema de Gestión de Control de Plagas</p>
            <p>Reporte generado el <?= date('d/m/Y H:i:s', strtotime($fecha_generacion)) ?></p>
            <p><strong>Filtros aplicados:</strong> 
                <?php echo ($filtroPlaga === 'todos') ? 'Todas las plagas' : 'Plaga: ' . ucfirst($filtroPlaga); ?>, 
                <?php echo ($filtroIncidencia === 'todos') ? 'Todas las incidencias' : 'Incidencia: ' . $filtroIncidencia; ?>
            </p>
        </div>
    </div>
</body>
</html> 