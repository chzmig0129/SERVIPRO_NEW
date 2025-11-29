<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte Combinado: Ventas y Quejas</title>
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
        <h1>Reporte Combinado: Ventas y Quejas</h1>
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

    <!-- ===================== SECCIÓN VENTAS ===================== -->
    <h2>RESUMEN DE VENTAS</h2>
    
    <div class="stats-box">
        <div class="stats-row">
            <span class="stats-label">Total de ventas:</span> 
            <?= isset($resumenVentas['total_ventas']) ? number_format($resumenVentas['total_ventas']) : '0' ?>
        </div>
        <div class="stats-row">
            <span class="stats-label">Importe total:</span> 
            $<?= isset($resumenVentas['importe_total']) ? number_format($resumenVentas['importe_total'], 2) : '0.00' ?>
        </div>
        <div class="stats-row">
            <span class="stats-label">Promedio por venta:</span> 
            $<?= isset($resumenVentas['total_ventas']) && $resumenVentas['total_ventas'] > 0 
                ? number_format($resumenVentas['importe_total'] / $resumenVentas['total_ventas'], 2) 
                : '0.00' ?>
        </div>
    </div>

    <!-- Conceptos más Frecuentes -->
    <h2>Conceptos de Ventas más Frecuentes</h2>
    <?php if (!empty($estadisticasConceptos)): ?>
        <table>
            <tr>
                <th width="60%">Concepto</th>
                <th width="20%">Ocurrencias</th>
                <th width="20%">Importe Total</th>
            </tr>
            <?php foreach ($estadisticasConceptos as $estadistica): ?>
                <tr>
                    <td><?= esc($estadistica['concepto']) ?></td>
                    <td class="text-center"><?= number_format($estadistica['frecuencia']) ?></td>
                    <td class="text-right">$<?= number_format($estadistica['importe_total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay datos disponibles.</p>
    <?php endif; ?>

    <!-- Ventas por Mes -->
    <h2>Tendencia de Ventas por Mes</h2>
    <?php if (!empty($ventasMensuales)): ?>
        <table>
            <tr>
                <th width="40%">Mes</th>
                <th width="25%">Total Ventas</th>
                <th width="35%">Importe Total</th>
            </tr>
            <?php foreach ($ventasMensuales as $estadistica): ?>
                <tr>
                    <td><?= date('F Y', strtotime($estadistica['mes'] . '-01')) ?></td>
                    <td class="text-center"><?= number_format($estadistica['total']) ?></td>
                    <td class="text-right">$<?= number_format($estadistica['importe_total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p>No hay datos disponibles.</p>
    <?php endif; ?>

    <!-- Salto de página antes de la sección de Quejas -->
    <div class="page-break"></div>

    <!-- ===================== SECCIÓN QUEJAS ===================== -->
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

    <?php if (empty($sede_seleccionada) && !empty($sedesEstadisticas)): ?>
        <!-- Salto de página antes de la sección de Datos por Sede -->
        <div class="page-break"></div>
        
        <h2>DATOS COMBINADOS POR SEDE</h2>
        <table>
            <tr>
                <th width="40%">Sede</th>
                <th width="15%">Total Ventas</th>
                <th width="20%">Importe Total</th>
                <th width="15%">Total Quejas</th>
                <th width="10%">Críticas</th>
            </tr>
            <?php foreach ($sedesEstadisticas as $estadistica): ?>
                <tr>
                    <td><?= esc($estadistica['sede']) ?></td>
                    <td class="text-center"><?= number_format($estadistica['total_ventas']) ?></td>
                    <td class="text-right">$<?= number_format($estadistica['importe_total'], 2) ?></td>
                    <td class="text-center"><?= number_format($estadistica['total_quejas']) ?></td>
                    <td class="text-center"><?= number_format($estadistica['quejas_criticas']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <div class="footer">
        Sistema ServiPro &copy; <?= date('Y') ?> Todos los derechos reservados
    </div>
</body>
</html> 