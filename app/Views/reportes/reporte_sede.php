<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Sede - <?= $sede['nombre'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 10px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }
        .header h1 {
            margin: 5px 0;
            color: #2c3e50;
        }
        .info-section {
            margin-bottom: 20px;
        }
        .info-section h2 {
            color: #3498db;
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-top: 0;
        }
        .info-item {
            margin-bottom: 5px;
        }
        .info-item strong {
            font-weight: bold;
            min-width: 150px;
            display: inline-block;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #777;
        }
        .statistics {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .statistics-item {
            width: 48%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .statistics-item h3 {
            margin-top: 0;
            color: #3498db;
        }
        .chart-container {
            width: 100%;
            text-align: center;
            margin: 20px 0;
            page-break-inside: avoid;
        }
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-box h3 {
            margin-top: 0;
            color: #2c3e50;
        }
        .list-unstyled {
            list-style: none;
            padding-left: 0;
            margin: 0;
        }
        .list-unstyled li {
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .list-unstyled li:last-child {
            border-bottom: none;
        }
        .page-break {
            page-break-before: always;
        }
        .chart-bar {
            height: 20px;
            background-color: #3498db;
            margin-bottom: 5px;
        }
        .chart-label {
            display: inline-block;
            width: 30%;
            text-align: right;
            padding-right: 10px;
        }
        .chart-value {
            display: inline-block;
            width: 10%;
            text-align: right;
            padding-right: 5px;
        }
        .chart-bar-container {
            display: inline-block;
            width: 50%;
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reporte Detallado de Sede</h1>
            <p><?= $sede['nombre'] ?></p>
        </div>
        
        <div class="info-section">
            <h2>Información de la Sede</h2>
            <div class="info-item"><strong>Nombre:</strong> <?= $sede['nombre'] ?></div>
            <div class="info-item"><strong>Dirección:</strong> <?= $sede['direccion'] ?? 'No especificada' ?></div>
            <div class="info-item"><strong>Ciudad:</strong> <?= $sede['ciudad'] ?? 'No especificada' ?></div>
            <div class="info-item"><strong>País:</strong> <?= $sede['pais'] ?? 'No especificado' ?></div>
            <div class="info-item"><strong>Fecha de creación:</strong> <?= isset($sede['fecha_creacion']) ? date('d/m/Y', strtotime($sede['fecha_creacion'])) : 'No especificada' ?></div>
        </div>
        
        <div class="summary-box">
            <h3>Resumen General</h3>
            <ul class="list-unstyled">
                <li><strong>Total de planos:</strong> <?= count($planos) ?></li>
                <li><strong>Total de trampas:</strong> <?= $estadisticas['total_trampas'] ?></li>
                <li><strong>Total de incidencias:</strong> <?= $estadisticas['total_incidencias'] ?></li>
            </ul>
        </div>
        
        <!-- Gráfico de Tipos de Trampas -->
        <div class="statistics-item">
            <h3>Distribución de Tipos de Trampas</h3>
            <?php
            // Ordenar por cantidad (de mayor a menor)
            arsort($estadisticas['tipos_trampas']);
            $maxValue = max($estadisticas['tipos_trampas']) > 0 ? max($estadisticas['tipos_trampas']) : 1;
            
            foreach ($estadisticas['tipos_trampas'] as $tipo => $cantidad):
                $porcentaje = ($cantidad / $maxValue) * 100;
            ?>
                <div>
                    <span class="chart-label"><?= $tipo ?>:</span>
                    <span class="chart-value"><?= $cantidad ?></span>
                    <span class="chart-bar-container">
                        <span class="chart-bar" style="width: <?= $porcentaje ?>%;"></span>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Gráfico de Tipos de Incidencias -->
        <?php if (!empty($estadisticas['tipos_incidencias'])): ?>
        <div class="statistics-item">
            <h3>Distribución de Tipos de Incidencias</h3>
            <?php
            // Ordenar por cantidad (de mayor a menor)
            arsort($estadisticas['tipos_incidencias']);
            $maxValue = max($estadisticas['tipos_incidencias']) > 0 ? max($estadisticas['tipos_incidencias']) : 1;
            
            foreach ($estadisticas['tipos_incidencias'] as $tipo => $cantidad):
                $porcentaje = ($cantidad / $maxValue) * 100;
            ?>
                <div>
                    <span class="chart-label"><?= $tipo ?>:</span>
                    <span class="chart-value"><?= $cantidad ?></span>
                    <span class="chart-bar-container">
                        <span class="chart-bar" style="width: <?= $porcentaje ?>%;"></span>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Gráfico de Incidencias por Mes -->
        <?php if (!empty($estadisticas['incidencias_por_mes'])): ?>
        <div class="statistics-item" style="width: 100%;">
            <h3>Incidencias por Mes</h3>
            <?php
            // Ordenar cronológicamente
            ksort($estadisticas['incidencias_por_mes']);
            $maxValue = max($estadisticas['incidencias_por_mes']) > 0 ? max($estadisticas['incidencias_por_mes']) : 1;
            
            foreach ($estadisticas['incidencias_por_mes'] as $mes => $cantidad):
                $porcentaje = ($cantidad / $maxValue) * 100;
                $nombreMes = date("F Y", strtotime($mes."-01"));
            ?>
                <div>
                    <span class="chart-label"><?= $nombreMes ?>:</span>
                    <span class="chart-value"><?= $cantidad ?></span>
                    <span class="chart-bar-container">
                        <span class="chart-bar" style="width: <?= $porcentaje ?>%;"></span>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <!-- Nueva página para los listados -->
        <div class="page-break"></div>
        
        <?php if (count($planos) > 0): ?>
        <div class="info-section">
            <h2>Listado de Planos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Fecha Creación</th>
                        <th>Trampas</th>
                        <th>Incidencias</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($planos as $plano): 
                        // Contar trampas para este plano
                        $trampasPorPlano = 0;
                        foreach ($trampas as $trampa) {
                            if ($trampa['plano_id'] == $plano['id']) {
                                $trampasPorPlano++;
                            }
                        }
                        
                        // Contar incidencias para este plano
                        $incidenciasPorPlano = 0;
                        $trampaIdsEnPlano = [];
                        foreach ($trampas as $trampa) {
                            if ($trampa['plano_id'] == $plano['id']) {
                                $trampaIdsEnPlano[] = $trampa['id'];
                            }
                        }
                        
                        foreach ($incidencias as $incidencia) {
                            if (in_array($incidencia['id_trampa'], $trampaIdsEnPlano)) {
                                $incidenciasPorPlano++;
                            }
                        }
                    ?>
                    <tr>
                        <td><?= $plano['id'] ?></td>
                        <td><?= $plano['nombre'] ?></td>
                        <td><?= $plano['descripcion'] ?></td>
                        <td><?= date('d/m/Y', strtotime($plano['fecha_creacion'])) ?></td>
                        <td><?= $trampasPorPlano ?></td>
                        <td><?= $incidenciasPorPlano ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <?php if (count($incidencias) > 0): ?>
        <div class="info-section">
            <h2>Incidencias Recientes (Últimas 20)</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trampa</th>
                        <th>Fecha</th>
                        <th>Tipo Plaga</th>
                        <th>Tipo Incidencia</th>
                        <th>Inspector</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Ordenar incidencias por fecha (más recientes primero)
                    usort($incidencias, function($a, $b) {
                        return strtotime($b['fecha']) - strtotime($a['fecha']);
                    });
                    
                    // Mostrar sólo las últimas 20
                    $recentIncidencias = array_slice($incidencias, 0, 20);
                    
                    foreach ($recentIncidencias as $incidencia): 
                    ?>
                    <tr>
                        <td><?= $incidencia['id'] ?></td>
                        <td><?= $incidencia['id_trampa'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($incidencia['fecha'])) ?></td>
                        <td><?= $incidencia['tipo_plaga'] ?? 'No especificado' ?></td>
                        <td><?= $incidencia['tipo_incidencia'] ?? 'No especificado' ?></td>
                        <td><?= $incidencia['inspector'] ?? 'No especificado' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <div class="footer">
            <p>Reporte generado el <?= date('d/m/Y H:i:s', strtotime($fecha_generacion)) ?></p>
            <p>Servipro - Sistema de Gestión de Control de Plagas</p>
        </div>
    </div>
</body>
</html> 