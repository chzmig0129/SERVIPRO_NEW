<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Detallado de Plano - <?= $plano['nombre'] ?></title>
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
        .image-container {
            width: 100%;
            text-align: center;
            margin: 20px 0;
        }
        .blueprint-image {
            max-width: 100%;
            max-height: 400px;
            border: 1px solid #ddd;
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
        }
        .chart-container img {
            max-width: 100%;
            border: 1px solid #ddd;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reporte Detallado de Plano</h1>
            <p><?= $plano['nombre'] ?> - <?= $sede['nombre'] ?? 'Sede no especificada' ?></p>
        </div>
        
        <div class="info-section">
            <h2>Información del Plano</h2>
            <div class="info-item"><strong>Nombre:</strong> <?= $plano['nombre'] ?></div>
            <div class="info-item"><strong>Descripción:</strong> <?= $plano['descripcion'] ?></div>
            <div class="info-item"><strong>Fecha de creación:</strong> <?= date('d/m/Y', strtotime($plano['fecha_creacion'])) ?></div>
            <div class="info-item"><strong>Sede:</strong> <?= $sede['nombre'] ?? 'No especificada' ?></div>
            <div class="info-item"><strong>Dirección de la sede:</strong> <?= $sede['direccion'] ?? 'No especificada' ?></div>
            <div class="info-item"><strong>Ciudad:</strong> <?= $sede['ciudad'] ?? 'No especificada' ?></div>
            <div class="info-item"><strong>País:</strong> <?= $sede['pais'] ?? 'No especificado' ?></div>
        </div>
        
        <div class="image-container">
            <img src="<?= $imagen_url ?>" alt="Plano <?= $plano['nombre'] ?>" class="blueprint-image">
        </div>
        
        <div class="summary-box">
            <h3>Resumen de Trampas e Incidencias</h3>
            <ul class="list-unstyled">
                <li><strong>Total de trampas:</strong> <?= count($trampas) ?></li>
                <li><strong>Total de incidencias:</strong> <?= count($incidencias) ?></li>
                <?php
                $tiposTrampa = [];
                $tiposIncidencia = [];
                $tiposPlaga = [];
                
                // Contar tipos de trampas
                foreach ($trampas as $trampa) {
                    $tipo = $trampa['tipo'] ?? 'Desconocido';
                    if (!isset($tiposTrampa[$tipo])) {
                        $tiposTrampa[$tipo] = 0;
                    }
                    $tiposTrampa[$tipo]++;
                }
                
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
                }
                ?>
                
                <li><strong>Trampas por tipo:</strong>
                    <ul>
                        <?php foreach ($tiposTrampa as $tipo => $cantidad): ?>
                            <li><?= $tipo ?>: <?= $cantidad ?></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php if (count($tiposIncidencia) > 0): ?>
                <li><strong>Incidencias por tipo:</strong>
                    <ul>
                        <?php foreach ($tiposIncidencia as $tipo => $cantidad): ?>
                            <li><?= $tipo ?>: <?= $cantidad ?></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php endif; ?>
                <?php if (count($tiposPlaga) > 0): ?>
                <li><strong>Incidencias por tipo de plaga:</strong>
                    <ul>
                        <?php foreach ($tiposPlaga as $tipo => $cantidad): ?>
                            <li><?= $tipo ?>: <?= $cantidad ?></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </div>
        
        <?php if (count($trampas) > 0): ?>
        <div class="info-section">
            <h2>Listado de Trampas</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tipo</th>
                        <th>Ubicación</th>
                        <th>Fecha Instalación</th>
                        <th>Coordenadas (X,Y)</th>
                        <th>Incidencias</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trampas as $trampa): 
                        // Contar incidencias para esta trampa
                        $incidenciasTrampa = 0;
                        foreach ($incidencias as $incidencia) {
                            if ($incidencia['id_trampa'] == $trampa['id']) {
                                $incidenciasTrampa++;
                            }
                        }
                    ?>
                    <tr>
                        <td><?= $trampa['id_trampa'] ?? ('T'.$trampa['id']) ?></td>
                        <td><?= $trampa['tipo'] ?></td>
                        <td><?= $trampa['ubicacion'] ?></td>
                        <td><?= date('d/m/Y', strtotime($trampa['fecha_instalacion'])) ?></td>
                        <td><?= round($trampa['coordenada_x'], 2) ?>%, <?= round($trampa['coordenada_y'], 2) ?>%</td>
                        <td><?= $incidenciasTrampa ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <?php if (count($incidencias) > 0): ?>
        <div class="info-section">
            <h2>Listado de Incidencias</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Trampa</th>
                        <th>Fecha</th>
                        <th>Tipo Plaga</th>
                        <th>Tipo Insecto</th>
                        <th>Cantidad</th>
                        <th>Tipo Incidencia</th>
                        <th>Inspector</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incidencias as $incidencia): ?>
                    <tr>
                        <td><?= $incidencia['id'] ?></td>
                        <td><?= $incidencia['id_trampa'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($incidencia['fecha'])) ?></td>
                        <td><?= $incidencia['tipo_plaga'] ?? 'No especificado' ?></td>
                        <td><?= $incidencia['tipo_insecto'] ?? 'No especificado' ?></td>
                        <td><?= $incidencia['cantidad_organismos'] ?? 'No especificado' ?></td>
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