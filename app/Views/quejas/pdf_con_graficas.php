<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Estadísticas de Quejas con Gráficas</title>
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
            font-size: 14px;
            border-bottom: 1px solid #1a56db;
            margin-top: 10px;
            margin-bottom: 8px;
            padding-bottom: 3px;
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
            margin-bottom: 15px;
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
            margin-bottom: 15px;
            page-break-inside: avoid;
            overflow: hidden;
        }
        .chart-canvas {
            width: 100%;
            display: block;
            margin: 0 auto;
        }
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 15px;
        }
        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }
        #content-to-capture {
            background-color: white;
            padding: 20px;
            max-width: 900px;
            margin: 0 auto;
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
            display: flex;
            gap: 10px;
        }
        .btn {
            padding: 8px 16px;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-primary {
            background-color: #1a56db;
        }
        .btn-primary:hover {
            background-color: #1e429f;
        }
        .btn-success {
            background-color: #047857;
        }
        .btn-success:hover {
            background-color: #065f46;
        }
        .fullwidth-chart {
            height: 200px;
            max-height: 200px;
            margin-bottom: 15px;
        }
        .chart-container {
            position: relative;
            height: 350px;
            width: 100%;
        }
        .compact-chart {
            height: 200px;
            max-height: 200px;
        }
        .single-page-container {
            max-height: 970px; /* Altura aproximada de una página A4 */
            overflow: hidden;
        }
        .chart-page {
            page-break-after: always;
        }
        .chart-page:last-child {
            page-break-after: auto;
        }
        .page-title {
            color: #1a56db;
            font-size: 16px;
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 10px;
        }
        @media print {
            #pdf-controls {
                display: none;
            }
            .chart-box {
                break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div id="pdf-controls">
        <a href="<?= site_url('reportes/pdf_optimizado/quejas') . (isset($_GET['sede_id']) ? '?sede_id=' . $_GET['sede_id'] : '') ?>" 
           class="btn btn-success">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <line x1="10" y1="9" x2="8" y2="9"></line>
            </svg>
            PDF Optimizado
        </a>
        <button id="generate-pdf" class="btn btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                <polyline points="7 10 12 15 17 10"></polyline>
                <line x1="12" y1="15" x2="12" y2="3"></line>
            </svg>
            Descargar PDF
        </button>
    </div>

    <div id="content-to-capture">
        <!-- Cabecera y resumen -->
        <div class="chart-page">
            <h1>Estadísticas de Quejas con Gráficas</h1>
            <p class="subtitle">
                <?php if (!empty($nombre_sede_seleccionada)): ?>
                    Estadísticas para la planta: <?= esc($nombre_sede_seleccionada) ?>
                <?php else: ?>
                    Estadísticas globales de todas las plantas
                <?php endif; ?>
            </p>
            <p class="date">Generado el: <?= date('d/m/Y H:i:s') ?></p>

            <!-- Resumen General -->
            <div class="summary-box">
                <div style="display: flex; justify-content: space-between;">
                    <div class="summary-item" style="margin-right: 10px;">
                        <span class="summary-label">Total de Quejas:</span> 
                        <?= number_format($totalQuejas) ?>
                    </div>
                    <div class="summary-item" style="margin-right: 10px;">
                        <span class="summary-label">Quejas Críticas:</span> 
                        <?= number_format($totalCriticos) ?>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Porcentaje Críticas:</span> 
                        <?= $totalQuejas > 0 ? number_format(($totalCriticos / $totalQuejas) * 100, 2) : '0.00' ?>%
                    </div>
                </div>
            </div>

            <!-- Página 1: Primeras 4 gráficas (2x2) -->
            <div class="page-title">Gráficas por Año y Clasificación</div>
            
            <div class="grid">
                <!-- Gráfica de Quejas por Año -->
                <div class="chart-box">
                    <h2>Quejas por Año</h2>
                    <div class="chart-container">
                        <canvas id="graficaAnual"></canvas>
                    </div>
                </div>

                <!-- Gráfica de Tipos de Insectos -->
                <div class="chart-box">
                    <h2>Tipos de Insecto</h2>
                    <div class="chart-container">
                        <canvas id="graficaInsectos"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid">
                <!-- Gráfica de Clasificación de Quejas -->
                <div class="chart-box">
                    <h2>Por Clasificación</h2>
                    <div class="chart-container">
                        <canvas id="graficaClasificacion"></canvas>
                    </div>
                </div>
                
                <!-- Gráfica de Estado (Vivo/Muerto) -->
                <div class="chart-box">
                    <h2>Por Estado</h2>
                    <div class="chart-container">
                        <canvas id="graficaEstado"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Página 2: Las 2 gráficas restantes y tablas -->
        <div class="chart-page">
            <h1>Estadísticas de Quejas con Gráficas</h1>
            <div class="page-title">Líneas Afectadas y Distribución Semanal</div>
            
            <div class="grid">
                <!-- Gráfica de Líneas Afectadas -->
                <div class="chart-box">
                    <h2>Líneas Afectadas</h2>
                    <div class="chart-container">
                        <canvas id="graficaLineas"></canvas>
                    </div>
                </div>
                
                <!-- Gráfica de Quejas Semanales -->
                <div class="chart-box">
                    <h2>Quejas por Semana</h2>
                    <div class="chart-container">
                        <canvas id="graficaSemanal"></canvas>
                    </div>
                </div>
            </div>

            <!-- Tablas de resumen -->
            <div class="page-title">Tablas de Resumen</div>
            <div style="display: flex; gap: 20px;">
                <!-- Tabla de Resumen - Tipos de Insectos -->
                <div style="width: 50%;">
                    <h2>Detalle por Tipos de Insectos</h2>
                    <?php if (!empty($estadisticasInsectos)): ?>
                        <table>
                            <tr>
                                <th>Tipo de Insecto</th>
                                <th>Ocurrencias</th>
                            </tr>
                            <?php foreach ($estadisticasInsectos as $insecto): ?>
                                <tr>
                                    <td><?= esc($insecto['insecto']) ?></td>
                                    <td><?= number_format($insecto['frecuencia']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <p>No hay datos disponibles.</p>
                    <?php endif; ?>
                </div>

                <!-- Tabla de Resumen - Clasificación -->
                <div style="width: 50%;">
                    <h2>Detalle por Clasificación</h2>
                    <?php if (!empty($estadisticasClasificacion)): ?>
                        <table>
                            <tr>
                                <th>Clasificación</th>
                                <th>Total</th>
                            </tr>
                            <?php foreach ($estadisticasClasificacion as $clasificacion): ?>
                                <tr>
                                    <td><?= esc($clasificacion['clasificacion']) ?></td>
                                    <td><?= number_format($clasificacion['total']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    <?php else: ?>
                        <p>No hay datos disponibles.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="footer">
                Este reporte fue generado desde el sistema ServiPro - © <?= date('Y') ?> Todos los derechos reservados
            </div>
        </div>
    </div>

    <!-- Script para generar los gráficos y el PDF -->
    <script>
        // Define el espacio de nombres jsPDF para usarlo más tarde
        window.jsPDF = window.jspdf.jsPDF;
        
        document.addEventListener('DOMContentLoaded', function() {
            // Datos para las gráficas
            const datosAnuales = <?= json_encode($estadisticasAnuales) ?>;
            const datosInsectos = <?= json_encode($estadisticasInsectos) ?>;
            const datosClasificacion = <?= json_encode($estadisticasClasificacion) ?>;
            const datosEstado = <?= json_encode($estadisticasEstado) ?>;
            const datosSemanales = <?= json_encode($estadisticasSemanales) ?>;
            const datosLineas = <?= json_encode($estadisticasLineas) ?>;

            // Separar datos por año
            const añoActual = new Date().getFullYear();
            const datosAñoActual = Array(52).fill(0);
            const datosAñoAnterior = Array(52).fill(0);

            datosSemanales.forEach(dato => {
                const semana = parseInt(dato.semana) - 1;
                const total = parseInt(dato.total);
                if (dato.año == añoActual) {
                    datosAñoActual[semana] = total;
                } else {
                    datosAñoAnterior[semana] = total;
                }
            });

            // Colores para las gráficas
            const colores = [
                '#4F46E5', '#2563EB', '#3B82F6', '#60A5FA', '#93C5FD',
                '#38BDF8', '#7DD3FC', '#BAE6FD', '#0EA5E9', '#0284C7'
            ];

            const coloresClasificacion = {
                'Crítico': '#DC2626',
                'Alto': '#F97316',
                'Medio': '#EAB308',
                'Bajo': '#22C55E'
            };

            // Configuración común para todas las gráficas
            Chart.defaults.plugins.legend.display = true;
            Chart.defaults.animation = false;
            Chart.defaults.responsive = true;
            Chart.defaults.maintainAspectRatio = false;

            // Gráfica de líneas - Quejas por semana
            new Chart(document.getElementById('graficaSemanal'), {
                type: 'line',
                data: {
                    labels: Array.from({length: 52}, (_, i) => 'S' + (i + 1)),
                    datasets: [
                        {
                            label: añoActual.toString(),
                            data: datosAñoActual,
                            borderColor: '#7C3AED', // Púrpura
                            backgroundColor: 'rgba(124, 58, 237, 0.2)',
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: (añoActual - 1).toString(),
                            data: datosAñoAnterior,
                            borderColor: '#059669', // Verde
                            backgroundColor: 'rgba(5, 150, 105, 0.2)',
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                boxWidth: 10,
                                font: { size: 10 }
                            }
                        },
                        title: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 9 }
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 0,
                                autoSkip: true,
                                font: { size: 8 }
                            }
                        }
                    }
                }
            });

            // Gráfica de pastel - Frecuencia de líneas afectadas
            new Chart(document.getElementById('graficaLineas'), {
                type: 'pie',
                data: {
                    labels: datosLineas.map(d => d.lineas),
                    datasets: [{
                        data: datosLineas.map(d => d.frecuencia),
                        backgroundColor: colores
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            display: true,
                            labels: {
                                boxWidth: 10,
                                font: { size: 9 }
                            }
                        }
                    }
                }
            });

            // Gráfica de barras - Quejas por año
            new Chart(document.getElementById('graficaAnual'), {
                type: 'bar',
                data: {
                    labels: datosAnuales.map(d => d.año),
                    datasets: [{
                        label: 'Quejas',
                        data: datosAnuales.map(d => d.total),
                        backgroundColor: '#3B82F6',
                        borderColor: '#2563EB',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 9 }
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 9 }
                            }
                        }
                    }
                }
            });

            // Gráfica circular - Tipos de insectos
            new Chart(document.getElementById('graficaInsectos'), {
                type: 'pie',
                data: {
                    labels: datosInsectos.map(d => d.insecto),
                    datasets: [{
                        data: datosInsectos.map(d => d.frecuencia),
                        backgroundColor: colores
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            display: true,
                            labels: {
                                boxWidth: 10,
                                font: { size: 9 }
                            }
                        }
                    }
                }
            });

            // Gráfica de barras - Clasificación
            new Chart(document.getElementById('graficaClasificacion'), {
                type: 'bar',
                data: {
                    labels: datosClasificacion.map(d => d.clasificacion),
                    datasets: [{
                        label: 'Quejas',
                        data: datosClasificacion.map(d => d.total),
                        backgroundColor: datosClasificacion.map(d => coloresClasificacion[d.clasificacion] || '#6B7280')
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1,
                                font: { size: 9 }
                            }
                        },
                        x: {
                            ticks: {
                                font: { size: 9 }
                            }
                        }
                    }
                }
            });

            // Gráfico circular - Estado
            new Chart(document.getElementById('graficaEstado'), {
                type: 'doughnut',
                data: {
                    labels: datosEstado.map(item => item.estado || 'No especificado'),
                    datasets: [{
                        data: datosEstado.map(item => item.total),
                        backgroundColor: [
                            '#22C55E', // Verde para Vivo
                            '#DC2626', // Rojo para Muerto
                            '#94A3B8'  // Gris para No especificado
                        ],
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            display: true,
                            labels: {
                                boxWidth: 10,
                                font: { size: 9 }
                            }
                        }
                    }
                }
            });

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
                        scale: 2, // Mayor calidad para capturar los detalles
                        useCORS: true, // Permitir imágenes de otros dominios
                        allowTaint: true, // Permitir elementos con diferentes orígenes
                        backgroundColor: '#ffffff', // Fondo blanco
                    };
                    
                    // Capturar cada página por separado
                    const pages = document.querySelectorAll('.chart-page');
                    
                    // Crear un nuevo PDF
                    const pdf = new jsPDF('p', 'mm', 'a4');
                    const pdfWidth = 210; // Ancho de página A4 en mm
                    
                    // Función para capturar cada página de manera secuencial
                    const capturePages = async (index) => {
                        if (index >= pages.length) {
                            // Todas las páginas han sido capturadas, guardar el PDF
                            pdf.save('estadisticas_quejas_con_graficas.pdf');
                            document.body.removeChild(loadingMessage);
                            return;
                        }
                        
                        try {
                            const canvas = await html2canvas(pages[index], options);
                            
                            // Si no es la primera página, agregar una nueva
                            if (index > 0) {
                                pdf.addPage();
                            }
                            
                            // Calcular altura proporcional
                            const imgWidth = pdfWidth;
                            const imgHeight = canvas.height * imgWidth / canvas.width;
                            
                            // Añadir la imagen al PDF
                            pdf.addImage(
                                canvas.toDataURL('image/jpeg', 1.0),
                                'JPEG',
                                0, // X
                                0, // Y
                                imgWidth,
                                imgHeight
                            );
                            
                            // Procesar la siguiente página
                            capturePages(index + 1);
                        } catch (error) {
                            console.error("Error al capturar la página", index, error);
                            alert("Error al generar el PDF: " + error.message);
                            document.body.removeChild(loadingMessage);
                        }
                    };
                    
                    // Iniciar la captura desde la primera página
                    capturePages(0);
                    
                }, 2500); // Tiempo suficiente para que las gráficas se carguen
            }
            
            // Asignar evento al botón de generar PDF
            document.getElementById('generate-pdf').addEventListener('click', generatePDF);
            
            // Si se carga la página con un parámetro para generar automáticamente
            <?php if (isset($_GET['auto_pdf']) && $_GET['auto_pdf'] == '1'): ?>
                // Esperar a que los gráficos se carguen y luego generar el PDF
                setTimeout(generatePDF, 2000);
            <?php endif; ?>
        });
    </script>
</body>
</html> 