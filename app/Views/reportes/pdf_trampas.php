<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Trampas - <?= $plano['nombre'] ?></title>
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
            border-bottom: 3px solid #3498db;
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
            color: #3498db;
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
            background-color: #3498db;
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
            background-color: #e1f5fe;
            border: 1px solid #b3e5fc;
            border-radius: 20px;
            margin-right: 8px;
            font-size: 13px;
            margin-bottom: 8px;
            color: #0277bd;
        }
        .filter-section {
            margin-bottom: 20px;
            padding: 12px;
            border: 1px solid #e1f5fe;
            background-color: #f5fbff;
            border-radius: 8px;
        }
        .filter-section-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #0277bd;
        }
        
        /* Resumen estadístico */
        .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }
        .summary-box h3 {
            margin-top: 0;
            color: #2c3e50;
            width: 100%;
            margin-bottom: 15px;
            font-size: 18px;
            border-bottom: 1px solid #eaecef;
            padding-bottom: 8px;
        }
        .stat-container {
            display: flex;
            flex-wrap: wrap;
            width: 100%;
            gap: 10px;
        }
        .stat-box {
            flex: 1;
            min-width: 140px;
            background-color: white;
            border-radius: 6px;
            padding: 10px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-left: 4px solid #3498db;
        }
        .stat-box.trap {
            border-color: #3498db;
        }
        .stat-box.incident {
            border-color: #e74c3c;
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
        
        /* Mensajes vacíos */
        .empty-message {
            text-align: center;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            color: #6c757d;
            font-style: italic;
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
            <h1>Reporte de Trampas</h1>
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
            <div class="info-item"><strong>Fecha creación:</strong> <?= date('d/m/Y', strtotime($plano['fecha_creacion'])) ?></div>
            <div class="info-item"><strong>Sede:</strong> <?= $sede['nombre'] ?? 'No especificada' ?></div>
            <div class="info-item"><strong>Ubicación:</strong> <?= $sede['direccion'] ?? 'No especificada' ?>, <?= $sede['ciudad'] ?? '' ?></div>
            <div class="info-item"><strong>Total trampas:</strong> <?= count($trampas) ?></div>
        </div>
        
        <div class="summary-box">
            <h3>Resumen de Trampas e Incidencias</h3>
            
            <?php
            // Estadísticas sobre trampas e incidencias
            $totalTrampas = count($trampas);
            $totalIncidencias = count($incidencias);
            
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
                        break;
                    }
                }
                if (!$conIncidencias) {
                    $trampasSinIncidencias++;
                }
            }
            ?>
            
            <div class="stat-container">
                <div class="stat-box trap">
                    <div class="stat-label">Total Trampas</div>
                    <div class="stat-value"><?= $totalTrampas ?></div>
                </div>
                
                <div class="stat-box incident">
                    <div class="stat-label">Incidencias Filtradas</div>
                    <div class="stat-value"><?= $totalIncidencias ?></div>
                </div>
                
                <div class="stat-box trap">
                    <div class="stat-label">Trampas Sin Incidencias</div>
                    <div class="stat-value"><?= $trampasSinIncidencias ?></div>
                </div>
                
                <div class="stat-box trap">
                    <div class="stat-label">Trampas Con Incidencias</div>
                    <div class="stat-value"><?= count($trampasConIncidencias) ?></div>
                </div>
            </div>
            
            <?php if (count($tiposTrampa) > 0): ?>
            <div style="width: 100%; margin-top: 15px;">
                <h4 style="margin: 10px 0; color: #2c3e50; font-size: 16px;">Tipos de Trampas</h4>
                <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                    <?php foreach ($tiposTrampa as $tipo => $cantidad): ?>
                    <div style="background: #f5f5f5; border-radius: 4px; padding: 8px 12px; font-size: 13px;">
                        <span style="font-weight: bold;"><?= $tipo ?>:</span> <?= $cantidad ?>
                        (<?= round(($cantidad / $totalTrampas) * 100) ?>%)
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
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
                        <th style="width: 20%">Incidencias</th>
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
                            <?php if ($incidenciasTrampa > 0): ?>
                                <span style="color: #e74c3c; font-weight: bold;"><?= $incidenciasTrampa ?> incidencia(s)</span>
                            <?php else: ?>
                                <span style="color: #27ae60;">Sin incidencias</span>
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