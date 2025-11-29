<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Quejas</title>
    <style>
        /* Estilos básicos optimizados para DOMPDF */
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h1 {
            font-size: 18px;
            font-weight: bold;
            color: #1a56db;
            text-align: center;
            margin-top: 0;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #1a56db;
        }
        h2 {
            font-size: 14px;
            font-weight: bold;
            color: #1a56db;
            margin-top: 20px;
            margin-bottom: 10px;
            padding-bottom: 3px;
            border-bottom: 1px solid #e5e7eb;
        }
        p {
            margin: 5px 0;
        }
        .header {
            margin-bottom: 20px;
        }
        .sede-info {
            text-align: center;
            font-size: 13px;
            margin-bottom: 5px;
        }
        .date-info {
            text-align: right;
            font-size: 10px;
            color: #6b7280;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            font-size: 11px;
        }
        th {
            background-color: #e0e7ff;
            text-align: left;
            padding: 6px;
            font-weight: bold;
            border: 1px solid #a5b4fc;
        }
        td {
            padding: 5px 6px;
            border: 1px solid #e5e7eb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .stats-box {
            background-color: #f3f4f6;
            border: 1px solid #d1d5db;
            padding: 10px;
            margin-bottom: 15px;
        }
        .stats-row {
            display: block;
            margin-bottom: 8px;
        }
        .stats-label {
            font-weight: bold;
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
        .page-break {
            page-break-before: always;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Quejas</h1>
        <div class="sede-info">
            <?php if (!empty($nombre_sede_seleccionada)): ?>
                Sede: <?= esc($nombre_sede_seleccionada) ?>
            <?php else: ?>
                Todas las sedes
            <?php endif; ?>
        </div>
        <div class="sede-info">
            Período: <?= date('d/m/Y', strtotime($fechaInicio)) ?> al <?= date('d/m/Y', strtotime($fechaFin)) ?>
        </div>
        <div class="date-info">
            Generado el: <?= date('d/m/Y H:i:s') ?>
        </div>
    </div>

    <!-- Resumen de Quejas -->
    <h2>RESUMEN DE QUEJAS</h2>
    
    <div class="stats-box">
        <div class="stats-row">
            <span class="stats-label">Total de quejas:</span> 
            <?= isset($resumenQuejas['total_quejas']) ? number_format($resumenQuejas['total_quejas']) : '0' ?>
        </div>
        <div class="stats-row">
            <span class="stats-label">Quejas críticas:</span> 
            <span class="critico"><?= isset($resumenQuejas['quejas_criticas']) ? number_format($resumenQuejas['quejas_criticas']) : '0' ?></span>
            <?php if (isset($resumenQuejas['total_quejas']) && $resumenQuejas['total_quejas'] > 0): ?>
                (<?= round(($resumenQuejas['quejas_criticas'] / $resumenQuejas['total_quejas']) * 100, 1) ?>%)
            <?php else: ?>
                (0%)
            <?php endif; ?>
        </div>
        <div class="stats-row">
            <span class="stats-label">Quejas altas:</span> 
            <span class="alto"><?= isset($resumenQuejas['quejas_altas']) ? number_format($resumenQuejas['quejas_altas']) : '0' ?></span>
            <?php if (isset($resumenQuejas['total_quejas']) && $resumenQuejas['total_quejas'] > 0): ?>
                (<?= round(($resumenQuejas['quejas_altas'] / $resumenQuejas['total_quejas']) * 100, 1) ?>%)
            <?php else: ?>
                (0%)
            <?php endif; ?>
        </div>
        <div class="stats-row">
            <span class="stats-label">Quejas medias:</span> 
            <span class="medio"><?= isset($resumenQuejas['quejas_medias']) ? number_format($resumenQuejas['quejas_medias']) : '0' ?></span>
            <?php if (isset($resumenQuejas['total_quejas']) && $resumenQuejas['total_quejas'] > 0): ?>
                (<?= round(($resumenQuejas['quejas_medias'] / $resumenQuejas['total_quejas']) * 100, 1) ?>%)
            <?php else: ?>
                (0%)
            <?php endif; ?>
        </div>
        <div class="stats-row">
            <span class="stats-label">Quejas bajas:</span> 
            <span class="bajo"><?= isset($resumenQuejas['quejas_bajas']) ? number_format($resumenQuejas['quejas_bajas']) : '0' ?></span>
            <?php if (isset($resumenQuejas['total_quejas']) && $resumenQuejas['total_quejas'] > 0): ?>
                (<?= round(($resumenQuejas['quejas_bajas'] / $resumenQuejas['total_quejas']) * 100, 1) ?>%)
            <?php else: ?>
                (0%)
            <?php endif; ?>
        </div>
    </div>

    <!-- Distribución por Clasificación -->
    <h2>Distribución de Quejas por Clasificación</h2>
    <?php if (!empty($estadisticasClasificacion)): ?>
        <table>
            <tr>
                <th width="50%">Clasificación</th>
                <th width="25%">Cantidad</th>
                <th width="25%">Porcentaje</th>
            </tr>
            <?php 
            $totalQuejas = isset($resumenQuejas['total_quejas']) ? $resumenQuejas['total_quejas'] : 0;
            foreach ($estadisticasClasificacion as $estadistica): 
                $porcentaje = $totalQuejas > 0 ? round(($estadistica['total'] / $totalQuejas) * 100, 1) : 0;
                $clase = strtolower($estadistica['clasificacion']);
            ?>
                <tr>
                    <td>
                        <span class="<?= $clase ?>"><?= esc($estadistica['clasificacion']) ?></span>
                    </td>
                    <td class="text-center"><?= number_format($estadistica['total']) ?></td>
                    <td class="text-center"><?= $porcentaje ?>%</td>
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
                <th width="60%">Tipo de Insecto</th>
                <th width="20%">Ocurrencias</th>
                <th width="20%">Porcentaje</th>
            </tr>
            <?php 
            $totalQuejas = isset($resumenQuejas['total_quejas']) ? $resumenQuejas['total_quejas'] : 0;
            foreach ($estadisticasInsectos as $estadistica): 
                $porcentaje = $totalQuejas > 0 ? round(($estadistica['frecuencia'] / $totalQuejas) * 100, 1) : 0;
            ?>
                <tr>
                    <td><?= esc($estadistica['insecto']) ?></td>
                    <td class="text-center"><?= number_format($estadistica['frecuencia']) ?></td>
                    <td class="text-center"><?= $porcentaje ?>%</td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay datos disponibles.</p>
    <?php endif; ?>

    <!-- Quejas por Mes -->
    <h2>Tendencia de Quejas por Mes</h2>
    <?php if (!empty($quejasMensuales)): ?>
        <table>
            <tr>
                <th width="50%">Mes</th>
                <th width="50%">Total Quejas</th>
            </tr>
            <?php foreach ($quejasMensuales as $estadistica): ?>
                <tr>
                    <td><?= date('F Y', strtotime($estadistica['mes'] . '-01')) ?></td>
                    <td class="text-center"><?= number_format($estadistica['total']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay datos disponibles.</p>
    <?php endif; ?>

    <?php if (empty($sede_seleccionada) && !empty($quejasPorSede)): ?>
        <!-- Salto de página antes de la sección de Datos por Sede -->
        <div class="page-break"></div>
        
        <h2>QUEJAS POR SEDE</h2>
        <table>
            <tr>
                <th width="60%">Sede</th>
                <th width="20%">Total Quejas</th>
                <th width="20%">Críticas</th>
            </tr>
            <?php foreach ($quejasPorSede as $estadistica): ?>
                <tr>
                    <td><?= esc($estadistica['sede']) ?></td>
                    <td class="text-center"><?= number_format($estadistica['total_quejas']) ?></td>
                    <td class="text-center">
                        <span class="critico"><?= number_format($estadistica['quejas_criticas']) ?></span>
                        <?php if ($estadistica['total_quejas'] > 0): ?>
                            (<?= round(($estadistica['quejas_criticas'] / $estadistica['total_quejas']) * 100, 1) ?>%)
                        <?php else: ?>
                            (0%)
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <div class="footer">
        Sistema ServiPro &copy; <?= date('Y') ?> Todos los derechos reservados
    </div>
</body>
</html> 