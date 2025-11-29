<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Visita - <?= $plano['nombre'] ?> - <?= date('d/m/Y', strtotime($fecha_visita)) ?></title>
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
            position: relative;
        }
        .logo {
            max-width: 150px;
            height: auto;
            margin-bottom: 15px;
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
        .incidencia-note {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin-bottom: 10px;
        }
        .signature-section {
            margin-top: 50px;
            page-break-inside: avoid;
        }
        .signature-box {
            margin-top: 20px;
            border-top: 1px solid #000;
            width: 250px;
            padding-top: 5px;
            text-align: center;
            display: inline-block;
            margin-right: 50px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="<?= base_url('Servipro_logo.jpg') ?>" alt="Servipro Logo" class="logo">
            <h1>Reporte de Visita Técnica</h1>
            <p><?= $plano['nombre'] ?> - <?= $sede['nombre'] ?? 'Sede no especificada' ?></p>
            <p>Fecha de Visita: <?= date('d/m/Y', strtotime($fecha_visita)) ?></p>
        </div>
        
        <div class="info-section">
            <h2>Información General</h2>
            <div class="info-item"><strong>Nombre del Plano:</strong> <?= $plano['nombre'] ?></div>
            <div class="info-item"><strong>Sede:</strong> <?= $sede['nombre'] ?? 'No especificada' ?></div>
            <div class="info-item"><strong>Dirección:</strong> <?= $sede['direccion'] ?? 'No especificada' ?></div>
            <div class="info-item"><strong>Fecha de Visita:</strong> <?= date('d/m/Y', strtotime($fecha_visita)) ?></div>
            <div class="info-item"><strong>Total de Trampas:</strong> <?= count($trampas_limpias) + count($incidencias) ?></div>
            <div class="info-item"><strong>Trampas sin Incidencias:</strong> <?= count($trampas_limpias) ?></div>
            <div class="info-item"><strong>Incidencias Detectadas:</strong> <?= count($incidencias) ?></div>
        </div>
        
        <div class="summary-box">
            <h3>Resumen de la Visita</h3>
            <p>Durante la visita técnica realizada el día <?= date('d/m/Y', strtotime($fecha_visita)) ?> a las instalaciones de <?= $sede['nombre'] ?? 'la sede' ?>, se realizó la revisión de <?= count($trampas_limpias) + count($incidencias) ?> trampas instaladas en el plano "<?= $plano['nombre'] ?>".</p>
            
            <p>Se encontraron <?= count($incidencias) ?> incidencias en las trampas, las cuales fueron debidamente registradas y se detallan en este informe. Las <?= count($trampas_limpias) ?> trampas restantes se encontraron en buen estado y sin presencia de plagas.</p>
        </div>
        
        <?php if (count($incidencias) > 0): ?>
        <div class="info-section">
            <h2>Incidencias Detectadas</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Trampa</th>
                        <th>Ubicación</th>
                        <th>Tipo de Plaga</th>
                        <th>Tipo de Incidencia</th>
                        <th>Cantidad</th>
                        <th>Inspector</th>
                        <th>Notas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incidencias as $incidencia): 
                        // Buscar la información de la trampa
                        $trampaInfo = null;
                        foreach ($todasTrampas as $trampa) {
                            if ($trampa['id'] == $incidencia['id_trampa']) {
                                $trampaInfo = $trampa;
                                break;
                            }
                        }
                    ?>
                    <tr>
                        <td><?= $trampaInfo ? ($trampaInfo['id_trampa'] ?? ('T'.$trampaInfo['id'])) : 'Desconocido' ?></td>
                        <td><?= $trampaInfo ? ($trampaInfo['ubicacion'] ?? 'No especificada') : 'No especificada' ?></td>
                        <td><?= $incidencia['tipo_plaga'] ?? 'No especificado' ?></td>
                        <td><?= $incidencia['tipo_incidencia'] ?? 'No especificada' ?></td>
                        <td><?= $incidencia['cantidad_organismos'] ?? '1' ?></td>
                        <td><?= $incidencia['inspector'] ?? 'No especificado' ?></td>
                        <td><?= $incidencia['notas'] ?? '-' ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
        <div class="info-section">
            <h2>Trampas sin Incidencias</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID Trampa</th>
                        <th>Tipo</th>
                        <th>Ubicación</th>
                        <th>Fecha Instalación</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($trampas_limpias) > 0): ?>
                        <?php foreach ($trampas_limpias as $trampa): ?>
                        <tr>
                            <td><?= $trampa['id_trampa'] ?? ('T'.$trampa['id']) ?></td>
                            <td><?= $trampa['tipo'] ?? 'No especificado' ?></td>
                            <td><?= $trampa['ubicacion'] ?? 'No especificada' ?></td>
                            <td><?= isset($trampa['fecha_instalacion']) ? date('d/m/Y', strtotime($trampa['fecha_instalacion'])) : 'No especificada' ?></td>
                            <td>Sin incidencias</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No hay trampas sin incidencias</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div class="signature-section">
            <h2>Firmas de Conformidad</h2>
            <div style="margin-top: 30px;">
                <div class="signature-box" style="margin-top: 50px;">
                    <p>Técnico</p>
                </div>
                <div class="signature-box" style="margin-top: 50px;">
                    <p>Cliente</p>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Reporte generado el: <?= date('d/m/Y H:i:s', strtotime($fecha_generacion)) ?></p>
            <p>Este documento es parte del programa de control de plagas implementado en la planta.</p>
        </div>
    </div>
</body>
</html> 