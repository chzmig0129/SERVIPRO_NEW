<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Incidencias - <?= $plano['nombre'] ?></title>
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
            border-bottom: 3px solid #e74c3c;
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
            background-color: #2c3e50;
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
            color: #e74c3c;
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
            background-color: #e74c3c;
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
            background-color: #ffebee;
            border: 1px solid #ffcdd2;
            border-radius: 20px;
            margin-right: 8px;
            font-size: 13px;
            margin-bottom: 8px;
            color: #c62828;
        }
        .filter-section {
            margin-bottom: 20px;
            padding: 12px;
            border: 1px solid #ffcdd2;
            background-color: #fff5f5;
            border-radius: 8px;
        }
        .filter-section-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #c62828;
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
        .stat-container {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
            gap: 15px;
            margin-bottom: 20px;
        }
        .stat-box {
            flex: 1;
            min-width: 140px;
            background-color: white;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 4px solid #e74c3c;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            margin: 5px 0;
            color: #2c3e50;
        }
        .stat-label {
            font-size: 12px;
            color: #7f8c8d;
            text-transform: uppercase;
        }
        
        /* Gráficos de datos */
        .chart-section {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        .chart-container {
            flex: 1;
            min-width: 250px;
            background-color: white;
            border-radius: 6px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .chart-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
            text-align: center;
        }
        .data-bars {
            margin-top: 10px;
        }
        .data-bar-container {
            margin-bottom: 8px;
        }
        .data-bar-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 3px;
            font-size: 13px;
        }
        .data-bar-name {
            font-weight: bold;
        }
        .data-bar-value {
            color: #7f8c8d;
        }
        .data-bar {
            height: 12px;
            background-color: #f5f5f5;
            border-radius: 6px;
            overflow: hidden;
        }
        .data-bar-fill {
            height: 100%;
            background-color: #e74c3c;
        }
        
        /* Listas sin estilo */
        .list-unstyled {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }
        .list-unstyled li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        .list-unstyled li:last-child {
            border-bottom: none;
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
        
        /* Códigos de severidad */
        .severity-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            color: white;
        }
        .severity-high {
            background-color: #e53935;
        }
        .severity-medium {
            background-color: #fb8c00;
        }
        .severity-low {
            background-color: #43a047;
        }
        
        /* Estilos responsive para impresión */
        @media print {
            .page-break {
                page-break-before: always;
            }
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
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reporte de Incidencias</h1>
            <p><?= $plano['nombre'] ?> - <?= $sede['nombre'] ?? 'Sede no especificada' ?></p>
            <p>Fecha de generación: <?= date('d/m/Y H:i', strtotime($fecha_generacion)) ?></p>
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
            <div class="info-item"><strong>Sede:</strong> <?= $sede['nombre'] ?? 'No especificada' ?></div>
            <div class="info-item"><strong>Ubicación:</strong> <?= $sede['direccion'] ?? 'No especificada' ?>, <?= $sede['ciudad'] ?? '' ?></div>
            <div class="info-item"><strong>Total incidencias (filtradas):</strong> <?= count($incidencias) ?></div>
        </div>
        
        <div class="summary-box">
            <h3>Resumen de Incidencias (Filtradas)</h3>
            <?php
            $tiposPlaga = [];
            $tiposIncidencia = [];
            $resumenPorTrampa = [];
            $totalOrganismos = 0;
            
            // Contar tipos de incidencias y plagas
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
                
                // Contar por trampa
                $trampaId = $incidencia['id_trampa'] ?? 'Sin trampa';
                if (!isset($resumenPorTrampa[$trampaId])) {
                    $resumenPorTrampa[$trampaId] = 0;
                }
                $resumenPorTrampa[$trampaId]++;
                
                // Sumar organismos
                $totalOrganismos += (int)($incidencia['cantidad_organismos'] ?? 0);
            }
            ?>
            
            <div class="stat-container">
                <div class="stat-box">
                    <div class="stat-label">Total Incidencias</div>
                    <div class="stat-value"><?= count($incidencias) ?></div>
                </div>
                
                <div class="stat-box">
                    <div class="stat-label">Tipos de Plaga</div>
                    <div class="stat-value"><?= count($tiposPlaga) ?></div>
                </div>
                
                <div class="stat-box">
                    <div class="stat-label">Trampas Afectadas</div>
                    <div class="stat-value"><?= count($resumenPorTrampa) ?></div>
                </div>
                
                <div class="stat-box">
                    <div class="stat-label">Total Organismos</div>
                    <div class="stat-value"><?= $totalOrganismos ?></div>
                </div>
            </div>
            
            <div class="chart-section">
                <?php if (count($tiposPlaga) > 0): ?>
                <div class="chart-container">
                    <div class="chart-title">Incidencias por tipo de plaga</div>
                    <div class="data-bars">
                        <?php 
                        $maxValue = max($tiposPlaga);
                        foreach ($tiposPlaga as $tipo => $cantidad): 
                        $porcentaje = ($cantidad / $maxValue) * 100;
                        ?>
                        <div class="data-bar-container">
                            <div class="data-bar-label">
                                <span class="data-bar-name"><?= $tipo ?></span>
                                <span class="data-bar-value"><?= $cantidad ?> (<?= round(($cantidad / count($incidencias)) * 100) ?>%)</span>
                            </div>
                            <div class="data-bar">
                                <div class="data-bar-fill" style="width: <?= $porcentaje ?>%;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (count($tiposIncidencia) > 0): ?>
                <div class="chart-container">
                    <div class="chart-title">Incidencias por tipo</div>
                    <div class="data-bars">
                        <?php 
                        $maxValue = max($tiposIncidencia);
                        foreach ($tiposIncidencia as $tipo => $cantidad): 
                        $porcentaje = ($cantidad / $maxValue) * 100;
                        ?>
                        <div class="data-bar-container">
                            <div class="data-bar-label">
                                <span class="data-bar-name"><?= $tipo ?></span>
                                <span class="data-bar-value"><?= $cantidad ?> (<?= round(($cantidad / count($incidencias)) * 100) ?>%)</span>
                            </div>
                            <div class="data-bar">
                                <div class="data-bar-fill" style="width: <?= $porcentaje ?>%;"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if (count($resumenPorTrampa) > 5): ?>
            <div style="margin-top: 15px;">
                <h4 style="margin: 10px 0; color: #2c3e50; font-size: 16px;">Trampas más afectadas</h4>
                <table style="margin-bottom: 0;">
                    <thead>
                        <tr>
                            <th style="width: 15%">ID Trampa</th>
                            <th style="width: 60%">Ubicación</th>
                            <th style="width: 25%">Incidencias</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Ordenar trampas por cantidad de incidencias (descendente)
                        arsort($resumenPorTrampa);
                        $contador = 0;
                        foreach ($resumenPorTrampa as $trampaId => $cantidad): 
                            if ($contador++ >= 5) break; // Mostrar solo las 5 más afectadas
                            
                            // Buscar información de la trampa
                            $ubicacion = 'No disponible';
                            foreach ($trampas as $trampa) {
                                if ($trampa['id'] == $trampaId) {
                                    $ubicacion = $trampa['tipo'] . ' - ' . $trampa['ubicacion'];
                                    break;
                                }
                            }
                        ?>
                        <tr>
                            <td><?= $trampaId ?></td>
                            <td><?= $ubicacion ?></td>
                            <td>
                                <strong><?= $cantidad ?></strong> 
                                (<?= round(($cantidad / count($incidencias)) * 100) ?>%)
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if (count($incidencias) > 0): ?>
        <div class="info-section">
            <h2>Listado de Incidencias</h2>
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
                            $severidadClass = 'severity-high';
                        } elseif ($cantidad > 5) {
                            $severidad = 'Media';
                            $severidadClass = 'severity-medium';
                        } else {
                            $severidad = 'Baja';
                            $severidadClass = 'severity-low';
                        }
                    ?>
                    <tr>
                        <td><?= $incidencia['id'] ?></td>
                        <td><?= $trampaInfo ?></td>
                        <td><?= $incidencia['tipo_incidencia'] ?? 'No especificado' ?></td>
                        <td><?= $incidencia['tipo_plaga'] ?? 'No especificado' ?></td>
                        <td>
                            <?= $cantidad ?>
                            <span class="severity-badge <?= $severidadClass ?>"><?= $severidad ?></span>
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