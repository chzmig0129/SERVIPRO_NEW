<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estadísticas de Ventas con Gráficas</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.5;
            margin: 20px;
            font-size: 12px;
            background-color: white;
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
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #e5e7eb;
            padding-top: 10px;
        }
        .chart-box {
            background-color: white;
            border: 1px solid #e5e7eb;
            padding: 10px;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }
        .chart-canvas {
            width: 100%;
            height: 300px;
        }
        #content-to-capture {
            background-color: white;
            padding: 20px;
        }
        #pdf-controls {
            position: fixed;
            top: 10px;
            right: 10px;
            background-color: #fff;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        .btn {
            padding: 8px 16px;
            background-color: #1a56db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #1e429f;
        }
        @media print {
            #pdf-controls {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div id="pdf-controls">
        <button id="generate-pdf" class="btn">Descargar PDF</button>
    </div>

    <div id="content-to-capture">
        <h1>Estadísticas de Ventas con Gráficas</h1>
        <p class="subtitle">
            <?php if (!empty($nombre_sede_seleccionada)): ?>
                Estadísticas para la sede: <?= esc($nombre_sede_seleccionada) ?>
            <?php else: ?>
                Estadísticas globales de todas las sedes
            <?php endif; ?>
        </p>
        <p class="date">Generado el: <?= date('d/m/Y H:i:s') ?></p>

        <!-- Resumen General -->
        <h2>Resumen General</h2>
        <div class="summary-box">
            <div class="summary-item">
                <span class="summary-label">Total de Ventas:</span> 
                <?= number_format($totalVentas) ?>
            </div>
            <div class="summary-item">
                <span class="summary-label">Importe Total:</span> 
                $<?= number_format($importeTotal, 2) ?>
            </div>
            <div class="summary-item">
                <span class="summary-label">Promedio por Venta:</span> 
                $<?= $totalVentas > 0 ? number_format($importeTotal / $totalVentas, 2) : '0.00' ?>
            </div>
            <div class="summary-item">
                <span class="summary-label">Ventas este Año:</span> 
                <?php 
                $ventasAnioActual = 0;
                $anioActual = date('Y');
                if (!empty($estadisticasAnuales)) {
                    foreach ($estadisticasAnuales as $estadistica) {
                        if ($estadistica['año'] == $anioActual) {
                            $ventasAnioActual = $estadistica['total'];
                            break;
                        }
                    }
                }
                echo number_format($ventasAnioActual);
                ?>
            </div>
        </div>

        <!-- Gráficas de Ventas por Sede -->
        <?php if (empty($sede_seleccionada) && !empty($estadisticasPorSede)): ?>
            <h2>Gráfica de Ventas por Sede</h2>
            <div class="chart-box">
                <canvas id="ventasPorSede" class="chart-canvas"></canvas>
            </div>
        <?php endif; ?>

        <!-- Gráficas de Ventas por Usuario -->
        <?php if (!empty($estadisticasPorUsuario)): ?>
            <h2>Gráfica de Ventas por Usuario</h2>
            <div class="chart-box">
                <canvas id="ventasPorUsuario" class="chart-canvas"></canvas>
            </div>
        <?php endif; ?>

        <!-- Conceptos más Frecuentes -->
        <h2>Conceptos más Frecuentes</h2>
        <?php if (!empty($estadisticasConceptos)): ?>
            <table>
                <tr>
                    <th>Concepto</th>
                    <th>Ocurrencias</th>
                    <th>Importe Total</th>
                </tr>
                <?php foreach ($estadisticasConceptos as $estadistica): ?>
                    <tr>
                        <td><?= esc($estadistica['concepto']) ?></td>
                        <td><?= number_format($estadistica['frecuencia']) ?></td>
                        <td class="text-right">$<?= number_format($estadistica['importe_total'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No hay datos disponibles.</p>
        <?php endif; ?>

        <!-- Ventas por Usuario -->
        <h2>Ventas por Usuario</h2>
        <?php if (!empty($estadisticasPorUsuario)): ?>
            <table>
                <tr>
                    <th>Usuario</th>
                    <th>Total Ventas</th>
                    <th>Importe Total</th>
                    <th>Promedio</th>
                </tr>
                <?php foreach ($estadisticasPorUsuario as $estadistica): ?>
                    <tr>
                        <td><?= esc($estadistica['usuario']) ?></td>
                        <td><?= number_format($estadistica['total_ventas']) ?></td>
                        <td class="text-right">$<?= number_format($estadistica['importe_total'], 2) ?></td>
                        <td class="text-right">$<?= $estadistica['total_ventas'] > 0 ? number_format($estadistica['importe_total'] / $estadistica['total_ventas'], 2) : '0.00' ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No hay datos disponibles.</p>
        <?php endif; ?>

        <!-- Ventas por Sede -->
        <?php if (empty($sede_seleccionada) && !empty($estadisticasPorSede)): ?>
            <h2>Detalle de Ventas por Sede</h2>
            <table>
                <tr>
                    <th>Sede</th>
                    <th>Total Ventas</th>
                    <th>Importe Total</th>
                    <th>Promedio</th>
                </tr>
                <?php foreach ($estadisticasPorSede as $estadistica): ?>
                    <tr>
                        <td><?= esc($estadistica['sede']) ?></td>
                        <td><?= number_format($estadistica['total_ventas']) ?></td>
                        <td class="text-right">$<?= number_format($estadistica['importe_total'], 2) ?></td>
                        <td class="text-right">$<?= $estadistica['total_ventas'] > 0 ? number_format($estadistica['importe_total'] / $estadistica['total_ventas'], 2) : '0.00' ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <!-- Ventas por Año -->
        <h2>Ventas por Año</h2>
        <?php if (!empty($estadisticasAnuales)): ?>
            <table>
                <tr>
                    <th>Año</th>
                    <th>Total Ventas</th>
                    <th>Importe Total</th>
                    <th>Promedio</th>
                </tr>
                <?php foreach ($estadisticasAnuales as $estadistica): ?>
                    <tr>
                        <td><?= $estadistica['año'] ?></td>
                        <td><?= number_format($estadistica['total']) ?></td>
                        <td class="text-right">$<?= number_format($estadistica['importe_total'], 2) ?></td>
                        <td class="text-right">$<?= $estadistica['total'] > 0 ? number_format($estadistica['importe_total'] / $estadistica['total'], 2) : '0.00' ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No hay datos disponibles.</p>
        <?php endif; ?>

        <div class="footer">
            Este reporte fue generado desde el sistema ServiPro - © <?= date('Y') ?> Todos los derechos reservados
        </div>
    </div>

    <!-- Script para generar los gráficos y el PDF -->
    <script>
        // Define el espacio de nombres jsPDF para usarlo más tarde
        window.jsPDF = window.jspdf.jsPDF;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Colores para los gráficos
            const colores = [
                'rgba(59, 130, 246, 0.7)', 
                'rgba(16, 185, 129, 0.7)', 
                'rgba(139, 92, 246, 0.7)', 
                'rgba(245, 158, 11, 0.7)',
                'rgba(236, 72, 153, 0.7)',
                'rgba(239, 68, 68, 0.7)',
                'rgba(37, 99, 235, 0.7)'
            ];

            // Gráfico de ventas por sede (si está disponible)
            <?php if (empty($sede_seleccionada) && !empty($estadisticasPorSede)): ?>
                <?php 
                $sedes = [];
                $totalesSede = [];
                $importesSede = [];
                
                foreach ($estadisticasPorSede as $i => $estadistica) {
                    $sedes[] = $estadistica['sede'];
                    $totalesSede[] = $estadistica['total_ventas'];
                    $importesSede[] = $estadistica['importe_total'];
                }
                ?>
                
                const ctxSede = document.getElementById('ventasPorSede').getContext('2d');
                const ventasPorSede = new Chart(ctxSede, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($sedes) ?>,
                        datasets: [{
                            label: 'Total Ventas',
                            data: <?= json_encode($totalesSede) ?>,
                            backgroundColor: colores,
                            borderColor: 'rgba(255, 255, 255, 0.7)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        animation: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = 'Ventas: ' + context.raw;
                                        let importe = <?= json_encode($importesSede) ?>[context.dataIndex];
                                        return [label, 'Importe: $' + parseFloat(importe).toLocaleString('es-MX')];
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Número de Ventas'
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>

            // Gráfico de ventas por usuario (si está disponible)
            <?php if (!empty($estadisticasPorUsuario)): ?>
                <?php 
                $usuarios = [];
                $totalesUsuario = [];
                $importesUsuario = [];
                
                foreach ($estadisticasPorUsuario as $i => $estadistica) {
                    $usuarios[] = $estadistica['usuario'];
                    $totalesUsuario[] = $estadistica['total_ventas'];
                    $importesUsuario[] = $estadistica['importe_total'];
                }
                ?>
                
                const ctxUsuario = document.getElementById('ventasPorUsuario').getContext('2d');
                const ventasPorUsuario = new Chart(ctxUsuario, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($usuarios) ?>,
                        datasets: [{
                            label: 'Total Ventas',
                            data: <?= json_encode($totalesUsuario) ?>,
                            backgroundColor: colores,
                            borderColor: 'rgba(255, 255, 255, 0.7)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        animation: false,
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        let label = 'Ventas: ' + context.raw;
                                        let importe = <?= json_encode($importesUsuario) ?>[context.dataIndex];
                                        return [label, 'Importe: $' + parseFloat(importe).toLocaleString('es-MX')];
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Número de Ventas'
                                }
                            }
                        }
                    }
                });
            <?php endif; ?>

            // Función para generar el PDF
            function generatePDF() {
                const contentElement = document.getElementById('content-to-capture');
                
                // Mostrar mensaje de carga
                const loadingMessage = document.createElement('div');
                loadingMessage.innerHTML = '<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); display: flex; justify-content: center; align-items: center; z-index: 9999;"><div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.2);"><p style="font-size: 16px;">Generando PDF, por favor espere...</p></div></div>';
                document.body.appendChild(loadingMessage);
                
                // Permitir que el DOM se actualice antes de capturar
                setTimeout(() => {
                    // Configurar opciones para html2canvas
                    const options = {
                        scale: 2, // Mayor escala para mejor calidad
                        useCORS: true, // Permitir imágenes de otros dominios
                        allowTaint: true, // Permitir elementos con diferentes orígenes
                        backgroundColor: '#ffffff' // Fondo blanco
                    };
                    
                    html2canvas(contentElement, options).then(canvas => {
                        // Crear PDF
                        const imgData = canvas.toDataURL('image/jpeg', 1.0);
                        
                        // Calcular el tamaño del PDF (A4: 210 x 297 mm)
                        const pdfWidth = 210;
                        const pdfHeight = canvas.height * pdfWidth / canvas.width;
                        
                        const pdf = new jsPDF('p', 'mm', 'a4');
                        
                        // Si la imagen es más grande que una página A4, dividirla en varias páginas
                        let position = 0;
                        const pageHeight = 297; // Altura de una página A4 en mm
                        
                        // Primera página
                        pdf.addImage(imgData, 'JPEG', 0, position, pdfWidth, pdfHeight);
                        
                        // Si el contenido es más alto que una página A4, agregar más páginas
                        const totalPages = Math.ceil(pdfHeight / pageHeight);
                        
                        for (let i = 1; i < totalPages; i++) {
                            position = -i * pageHeight;
                            pdf.addPage();
                            pdf.addImage(imgData, 'JPEG', 0, position, pdfWidth, pdfHeight);
                        }
                        
                        // Descargar PDF
                        pdf.save('estadisticas_ventas_con_graficas.pdf');
                        
                        // Eliminar mensaje de carga
                        document.body.removeChild(loadingMessage);
                    });
                }, 500);
            }
            
            // Asignar evento al botón de generar PDF
            document.getElementById('generate-pdf').addEventListener('click', generatePDF);
            
            // Si se carga la página con un parámetro para generar automáticamente
            <?php if (isset($_GET['auto_pdf']) && $_GET['auto_pdf'] == '1'): ?>
                // Esperar a que los gráficos se carguen y luego generar el PDF
                setTimeout(generatePDF, 1000);
            <?php endif; ?>
        });
    </script>
</body>
</html> 