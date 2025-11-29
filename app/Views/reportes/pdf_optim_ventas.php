<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Ventas</title>
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
        <h1>Reporte de Ventas</h1>
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

    <!-- Resumen de Ventas -->
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
            $<?= isset($resumenVentas['promedio_venta']) ? number_format($resumenVentas['promedio_venta'], 2) : 
                (isset($resumenVentas['total_ventas']) && $resumenVentas['total_ventas'] > 0 
                ? number_format($resumenVentas['importe_total'] / $resumenVentas['total_ventas'], 2) 
                : '0.00') ?>
        </div>
        <div class="stats-row">
            <span class="stats-label">Venta mínima:</span> 
            $<?= isset($resumenVentas['venta_minima']) ? number_format($resumenVentas['venta_minima'], 2) : '0.00' ?>
        </div>
        <div class="stats-row">
            <span class="stats-label">Venta máxima:</span> 
            $<?= isset($resumenVentas['venta_maxima']) ? number_format($resumenVentas['venta_maxima'], 2) : '0.00' ?>
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

    <?php if (empty($sede_seleccionada) && !empty($ventasPorSede)): ?>
        <!-- Salto de página antes de la sección de Datos por Sede -->
        <div class="page-break"></div>
        
        <h2>VENTAS POR SEDE</h2>
        <table>
            <tr>
                <th width="50%">Sede</th>
                <th width="20%">Total Ventas</th>
                <th width="30%">Importe Total</th>
            </tr>
            <?php foreach ($ventasPorSede as $estadistica): ?>
                <tr>
                    <td><?= esc($estadistica['sede']) ?></td>
                    <td class="text-center"><?= number_format($estadistica['total_ventas']) ?></td>
                    <td class="text-right">$<?= number_format($estadistica['importe_total'], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <div class="footer">
        Sistema ServiPro &copy; <?= date('Y') ?> Todos los derechos reservados
    </div>
</body>
</html> 