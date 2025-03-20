<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Plano - <?= $plano['nombre'] ?></title>
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
            position: relative;
        }
        .blueprint-image {
            max-width: 100%;
            max-height: 500px;
            border: 1px solid #ddd;
        }
        .trap-marker {
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            transform: translate(-50%, -50%);
            z-index: 10;
        }
        .trap-label {
            position: absolute;
            background: rgba(255, 255, 255, 0.8);
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 11px;
            transform: translate(-50%, -50%);
            white-space: nowrap;
            z-index: 20;
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
        .trap-type-legend {
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }
        .trap-type-item {
            display: inline-block;
            margin-right: 15px;
        }
        .trap-type-color {
            display: inline-block;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            margin-right: 5px;
            vertical-align: middle;
        }
        .incident-marker {
            position: absolute;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            border: 2px solid red;
            background-color: rgba(255, 0, 0, 0.3);
            transform: translate(-50%, -50%);
            z-index: 15;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Reporte de Plano</h1>
            <p><?= $plano['nombre'] ?> - <?= $sede['nombre'] ?? 'Sede no especificada' ?></p>
        </div>
        
        <div class="info-section">
            <h2>Información del Plano</h2>
            <div class="info-item"><strong>Nombre:</strong> <?= $plano['nombre'] ?></div>
            <div class="info-item"><strong>Descripción:</strong> <?= $plano['descripcion'] ?></div>
            <div class="info-item"><strong>Fecha de creación:</strong> <?= date('d/m/Y', strtotime($plano['fecha_creacion'])) ?></div>
            <div class="info-item"><strong>Sede:</strong> <?= $sede['nombre'] ?? 'No especificada' ?></div>
            <div class="info-item"><strong>Total de trampas:</strong> <?= count($trampas) ?></div>
            <div class="info-item"><strong>Total de incidencias:</strong> <?= count($incidencias) ?></div>
        </div>
        
        <div class="image-container">
            <img src="<?= $imagen_url ?>" alt="Plano <?= $plano['nombre'] ?>" class="blueprint-image">
            
            <?php 
            // Colores según tipo de trampa
            $colorMap = [
                'Pegajosa' => 'blue',
                'Luz UV' => 'purple',
                'Cebos' => 'green',
                'Jaula' => 'brown',
                'Electrónica' => 'orange',
                'Feromonas' => 'pink'
            ];
            
            // Agregar marcadores de trampas
            foreach ($trampas as $trampa): 
                $color = isset($colorMap[$trampa['tipo']]) ? $colorMap[$trampa['tipo']] : 'gray';
            ?>
                <div class="trap-marker" style="
                    left: <?= $trampa['coordenada_x'] ?>%; 
                    top: <?= $trampa['coordenada_y'] ?>%; 
                    background-color: <?= $color ?>;"></div>
                <div class="trap-label" style="
                    left: <?= $trampa['coordenada_x'] ?>%; 
                    top: <?= ($trampa['coordenada_y'] + 3) ?>%;">
                    <?= $trampa['id_trampa'] ?? ('T'.$trampa['id']) ?> (<?= $trampa['tipo'] ?>)
                </div>
            <?php endforeach; ?>
            
            <?php 
            // Agregar marcadores de incidencias
            foreach ($incidencias as $incidencia): 
                if (isset($incidencia['trampa'])):
            ?>
                <div class="incident-marker" style="
                    left: <?= $incidencia['trampa']['coordenada_x'] ?>%; 
                    top: <?= $incidencia['trampa']['coordenada_y'] ?>%;"></div>
            <?php 
                endif;
            endforeach; 
            ?>
        </div>
        
        <div class="trap-type-legend">
            <h3>Leyenda de Tipos de Trampas</h3>
            <?php foreach ($colorMap as $tipo => $color): ?>
                <div class="trap-type-item">
                    <span class="trap-type-color" style="background-color: <?= $color ?>;"></span>
                    <?= $tipo ?>
                </div>
            <?php endforeach; ?>
            <div class="trap-type-item">
                <span class="trap-type-color" style="background-color: gray;"></span>
                Otro tipo
            </div>
            <div class="trap-type-item">
                <span class="trap-type-color" style="border: 2px solid red; background-color: rgba(255, 0, 0, 0.3);"></span>
                Incidencia
            </div>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($trampas as $trampa): ?>
                    <tr>
                        <td><?= $trampa['id_trampa'] ?? ('T'.$trampa['id']) ?></td>
                        <td><?= $trampa['tipo'] ?></td>
                        <td><?= $trampa['ubicacion'] ?></td>
                        <td><?= date('d/m/Y', strtotime($trampa['fecha_instalacion'])) ?></td>
                        <td><?= round($trampa['coordenada_x'], 2) ?>%, <?= round($trampa['coordenada_y'], 2) ?>%</td>
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
                        <th>Tipo Incidencia</th>
                        <th>Inspector</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incidencias as $incidencia): ?>
                    <tr>
                        <td><?= $incidencia['id'] ?></td>
                        <td><?= isset($incidencia['trampa']) ? ($incidencia['trampa']['id_trampa'] ?? ('T'.$incidencia['trampa']['id'])) : $incidencia['id_trampa'] ?></td>
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