<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estadísticas de Quejas</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
            margin: 20px;
            font-size: 12px;
        }
        h1 {
            color: #1a56db;
            font-size: 20px;
            text-align: center;
            margin-bottom: 10px;
        }
        h2 {
            color: #1a56db;
            font-size: 16px;
            border-bottom: 1px solid #1a56db;
            margin-top: 20px;
            padding-bottom: 5px;
        }
        .subtitle {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 14px;
        }
        .date {
            text-align: right;
            font-size: 10px;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th {
            background-color: #e0e7ff;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #a5b4fc;
        }
        td {
            padding: 6px 8px;
            border: 1px solid #e5e7eb;
        }
        .summary-box {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 10px;
            margin-bottom: 20px;
        }
        .summary-item {
            margin-bottom: 10px;
        }
        .summary-label {
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .critico {
            color: #e02d1b;
            font-weight: bold;
        }
        .alto {
            color: #e67c00;
            font-weight: bold;
        }
        .medio {
            color: #f0b429;
        }
        .bajo {
            color: #2f9e44;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .chart-container {
            text-align: center;
            margin: 15px 0;
        }
        .chart-image {
            max-width: 100%;
            height: auto;
        }
        .chart-section {
            page-break-inside: avoid;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <h1>Estadísticas de Quejas</h1>
    <p class="subtitle">
        <?php if (!empty($nombre_sede_seleccionada)): ?>
            Estadísticas para la planta: <?= esc($nombre_sede_seleccionada) ?>
        <?php else: ?>
            Estadísticas globales de todas las plantas
        <?php endif; ?>
    </p>
    <p class="date">Generado el: <?= date('d/m/Y H:i:s') ?></p>

    <!-- Resumen General -->
    <h2>Resumen General</h2>
    <div class="summary-box">
        <div class="summary-item">
            <span class="summary-label">Total de Quejas:</span> 
            <?= number_format($totalQuejas) ?>
        </div>
        <div class="summary-item">
            <span class="summary-label">Quejas Críticas:</span> 
            <span class="critico"><?= number_format($totalCriticos) ?></span>
            (<?= $totalQuejas > 0 ? round(($totalCriticos / $totalQuejas) * 100, 1) : 0 ?>%)
        </div>
        <?php 
        $mesActual = date('Y-m');
        $quejasMesActual = 0;
        
        if (!empty($estadisticasMensuales)) {
            foreach ($estadisticasMensuales as $estadistica) {
                if ($estadistica['mes'] == $mesActual) {
                    $quejasMesActual = $estadistica['total'];
                    break;
                }
            }
        }
        ?>
        <div class="summary-item">
            <span class="summary-label">Quejas este Mes:</span> 
            <?= number_format($quejasMesActual) ?>
        </div>
    </div>
    
    <?php if (!empty($chart_images)): ?>
    <!-- Sección de Gráficos -->
    <div class="chart-section">
        <h2>Gráficos de Análisis</h2>
        
        <?php if (!empty($chart_images['semanal'])): ?>
        <div class="chart-container">
            <p><strong>Quejas por Semana</strong></p>
            <img src="<?= $chart_images['semanal'] ?>" alt="Gráfico de quejas por semana" class="chart-image">
        </div>
        <?php endif; ?>

        <?php if (!empty($chart_images['anual'])): ?>
        <div class="chart-container">
            <p><strong>Quejas por Año</strong></p>
            <img src="<?= $chart_images['anual'] ?>" alt="Gráfico de quejas por año" class="chart-image">
        </div>
        <?php endif; ?>
        
        <?php if (!empty($chart_images['lineas'])): ?>
        <div class="chart-container">
            <p><strong>Frecuencia de Líneas Afectadas</strong></p>
            <img src="<?= $chart_images['lineas'] ?>" alt="Gráfico de líneas afectadas" class="chart-image">
        </div>
        <?php endif; ?>
        
        <?php if (!empty($chart_images['insectos'])): ?>
        <div class="chart-container">
            <p><strong>Distribución por Tipo de Insecto</strong></p>
            <img src="<?= $chart_images['insectos'] ?>" alt="Gráfico de tipos de insectos" class="chart-image">
        </div>
        <?php endif; ?>
        
        <?php if (!empty($chart_images['clasificacion'])): ?>
        <div class="chart-container">
            <p><strong>Quejas por Clasificación</strong></p>
            <img src="<?= $chart_images['clasificacion'] ?>" alt="Gráfico de clasificación" class="chart-image">
        </div>
        <?php endif; ?>
        
        <?php if (!empty($chart_images['estado'])): ?>
        <div class="chart-container">
            <p><strong>Distribución por Estado</strong></p>
            <img src="<?= $chart_images['estado'] ?>" alt="Gráfico de estado" class="chart-image">
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Distribución por Clasificación -->
    <h2>Distribución por Clasificación</h2>
    <?php if (!empty($estadisticasClasificacion)): ?>
        <table>
            <tr>
                <th>Clasificación</th>
                <th>Cantidad</th>
                <th>Porcentaje</th>
            </tr>
            <?php foreach ($estadisticasClasificacion as $estadistica): ?>
                <tr>
                    <td>
                        <span class="<?= strtolower($estadistica['clasificacion']) ?>">
                            <?= esc($estadistica['clasificacion']) ?>
                        </span>
                    </td>
                    <td><?= number_format($estadistica['total']) ?></td>
                    <td><?= $totalQuejas > 0 ? round(($estadistica['total'] / $totalQuejas) * 100, 1) : 0 ?>%</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay datos disponibles.</p>
    <?php endif; ?>

    <!-- Tipos de Insectos más Frecuentes -->
    <h2>Tipos de Insectos más Frecuentes</h2>
    <?php if (!empty($estadisticasInsectos)): ?>
        <table>
            <tr>
                <th>Tipo de Insecto</th>
                <th>Ocurrencias</th>
                <th>Porcentaje</th>
            </tr>
            <?php foreach ($estadisticasInsectos as $estadistica): ?>
                <tr>
                    <td><?= esc($estadistica['insecto']) ?></td>
                    <td><?= number_format($estadistica['frecuencia']) ?></td>
                    <td><?= $totalQuejas > 0 ? round(($estadistica['frecuencia'] / $totalQuejas) * 100, 1) : 0 ?>%</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay datos disponibles.</p>
    <?php endif; ?>

    <!-- Ubicaciones más Frecuentes -->
    <h2>Ubicaciones más Frecuentes</h2>
    <?php if (!empty($estadisticasUbicacion)): ?>
        <table>
            <tr>
                <th>Ubicación</th>
                <th>Ocurrencias</th>
                <th>Porcentaje</th>
            </tr>
            <?php foreach ($estadisticasUbicacion as $estadistica): ?>
                <tr>
                    <td><?= esc($estadistica['ubicacion']) ?></td>
                    <td><?= number_format($estadistica['total']) ?></td>
                    <td><?= $totalQuejas > 0 ? round(($estadistica['total'] / $totalQuejas) * 100, 1) : 0 ?>%</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay datos disponibles.</p>
    <?php endif; ?>

    <!-- Quejas por Sede -->
    <?php if (empty($sede_seleccionada) && !empty($estadisticasPorSede)): ?>
        <h2>Detalle de Quejas por planta</h2>
        <table>
            <tr>
                <th>Planta</th>
                <th>Total Quejas</th>
                <th>Porcentaje</th>
            </tr>
            <?php foreach ($estadisticasPorSede as $estadistica): ?>
                <tr>
                    <td><?= esc($estadistica['sede']) ?></td>
                    <td><?= number_format($estadistica['total_quejas']) ?></td>
                    <td><?= $totalQuejas > 0 ? round(($estadistica['total_quejas'] / $totalQuejas) * 100, 1) : 0 ?>%</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <!-- Tendencia Mensual -->
    <h2>Tendencia Mensual</h2>
    <?php if (!empty($estadisticasMensuales)): ?>
        <table>
            <tr>
                <th>Mes</th>
                <th>Total Quejas</th>
            </tr>
            <?php 
            // Mostrar solo los últimos 12 meses como máximo
            $mesesAMostrar = min(12, count($estadisticasMensuales));
            for ($i = 0; $i < $mesesAMostrar; $i++): 
                $mesFormateado = date('F Y', strtotime($estadisticasMensuales[$i]['mes'] . '-01'));
            ?>
                <tr>
                    <td><?= $mesFormateado ?></td>
                    <td><?= number_format($estadisticasMensuales[$i]['total']) ?></td>
                </tr>
            <?php endfor; ?>
        </table>
    <?php else: ?>
        <p>No hay datos disponibles.</p>
    <?php endif; ?>

    <div class="footer">
        Este reporte fue generado desde el sistema ServiPro - © <?= date('Y') ?> Todos los derechos reservados
    </div>
</body>
</html> 