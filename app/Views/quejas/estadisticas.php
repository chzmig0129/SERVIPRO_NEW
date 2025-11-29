<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Incluir Chart.js y html2pdf -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<div class="space-y-6" id="contenido-pdf">
    <!-- Encabezado con gradiente -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl p-6 text-white shadow-lg text-center mb-6">
        <div class="flex flex-col items-center justify-center">
            <h1 class="text-3xl font-bold text-white mb-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                    <path d="M3 3v18h18"></path>
                    <path d="M18 17V9"></path>
                    <path d="M13 17V5"></path>
                    <path d="M8 17v-3"></path>
                </svg>
                Estadísticas de Quejas
                <?php if (isset($nombre_sede_seleccionada)): ?>
                    <span class="ml-2 text-xl">- <?= esc($nombre_sede_seleccionada) ?></span>
                <?php endif; ?>
            </h1>
            <p class="text-blue-100">Análisis estadístico de las quejas registradas</p>
        </div>
    </div>

    <!-- Barra de herramientas -->
    <div class="bg-white rounded-lg shadow-sm p-4 flex justify-between">
        <a href="<?= site_url('quejas') ?>" 
           class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Volver a Quejas
        </a>
        
        <div class="flex items-center gap-4">
            <a href="<?= site_url('reportes/pdf_optimizado/quejas') . (isset($_GET['sede_id']) ? '?sede_id=' . $_GET['sede_id'] : '') ?>" 
               class="bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <line x1="10" y1="9" x2="8" y2="9"></line>
                </svg>
                PDF Optimizado
            </a>
            
           
            
            <a href="<?= site_url('quejas/estadisticas/pdf_con_graficas') . (isset($_GET['sede_id']) ? '?sede_id=' . $_GET['sede_id'] : '') ?>" 
               class="bg-purple-600 hover:bg-purple-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <rect x="8" y="12" width="8" height="6" rx="1"></rect>
                    <line x1="10" y1="9" x2="8" y2="9"></line>
                </svg>
                PDF con Gráficas
            </a>
            
            <form action="<?= site_url('quejas/estadisticas') ?>" method="get" class="flex items-center gap-2">
                <label for="sede_id" class="text-sm font-medium text-gray-700">Filtrar por Sede:</label>
                <select name="sede_id" 
                        id="sede_id" 
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        onchange="this.form.submit()">
                    <option value="">Todas las sedes</option>
                    <?php foreach ($sedes as $sede): ?>
                        <option value="<?= $sede['id'] ?>" <?= ($sede_seleccionada == $sede['id']) ? 'selected' : '' ?>>
                            <?= esc($sede['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </div>

    <!-- Filtros -->
    <div class="mb-6">
        <form action="<?= site_url('quejas/estadisticas') ?>" method="get" class="flex items-center gap-4">
            <div class="flex items-center gap-2">
                <label for="sede_id" class="text-sm font-medium text-gray-700">Filtrar por Plantas:</label>
                <select name="sede_id" 
                        id="sede_id" 
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Todas las plantas</option>
                    <?php foreach ($sedes as $sede): ?>
                        <option value="<?= $sede['id'] ?>" <?= ($sede_seleccionada == $sede['id']) ? 'selected' : '' ?>>
                            <?= esc($sede['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <label for="fecha_inicio" class="text-sm font-medium text-gray-700">Fecha Inicio:</label>
                <input type="date" 
                       name="fecha_inicio" 
                       id="fecha_inicio"
                       value="<?= $fecha_inicio ?? '' ?>"
                       class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="flex items-center gap-2">
                <label for="fecha_fin" class="text-sm font-medium text-gray-700">Fecha Fin:</label>
                <input type="date" 
                       name="fecha_fin" 
                       id="fecha_fin"
                       value="<?= $fecha_fin ?? '' ?>"
                       class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                Filtrar
            </button>

            <?php if(isset($sede_seleccionada) || isset($fecha_inicio) || isset($fecha_fin)): ?>
                <a href="<?= site_url('quejas/estadisticas') ?>" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                    Limpiar
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Gráfica de quejas por semana -->
    <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
        <h2 class="text-lg font-semibold mb-4">Quejas por Semana</h2>
        <canvas id="graficaSemanal" height="100"></canvas>
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Notas sobre quejas semanales:</label>
            <textarea class="notas-grafica w-full p-2 border rounded-lg" rows="3" placeholder="Escriba sus observaciones aquí..."></textarea>
        </div>
    </div>

    <!-- Contenedor de gráficos -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Gráfico de barras - Quejas por año -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Quejas por Año</h2>
            <canvas id="graficaAnual"></canvas>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notas sobre quejas anuales:</label>
                <textarea class="notas-grafica w-full p-2 border rounded-lg" rows="3" placeholder="Escriba sus observaciones aquí..."></textarea>
            </div>
        </div>

        <!-- Gráfica de líneas afectadas -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Frecuencia de Líneas Afectadas</h2>
            <canvas id="graficaLines"></canvas>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notas sobre líneas afectadas:</label>
                <textarea class="notas-grafica w-full p-2 border rounded-lg" rows="3" placeholder="Escriba sus observaciones aquí..."></textarea>
            </div>
        </div>

        <!-- Gráfico circular - Tipos de insectos -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Distribución por Tipo de Insecto</h2>
            <canvas id="graficaInsectos"></canvas>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notas sobre tipos de insectos:</label>
                <textarea class="notas-grafica w-full p-2 border rounded-lg" rows="3" placeholder="Escriba sus observaciones aquí..."></textarea>
            </div>
        </div>

        <!-- Gráfico de barras - Clasificación -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Quejas por Clasificación</h2>
            <canvas id="graficaClasificacion"></canvas>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notas sobre clasificación:</label>
                <textarea class="notas-grafica w-full p-2 border rounded-lg" rows="3" placeholder="Escriba sus observaciones aquí..."></textarea>
            </div>
        </div>

        <!-- Gráfico circular - Estado -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Distribución por Estado</h2>
            <canvas id="graficaEstado"></canvas>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notas sobre estado:</label>
                <textarea class="notas-grafica w-full p-2 border rounded-lg" rows="3" placeholder="Escriba sus observaciones aquí..."></textarea>
            </div>
        </div>

        <!-- Gráfico circular - Estado de la Queja -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Estado de las Quejas</h2>
            <canvas id="graficaEstadoQueja"></canvas>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notas sobre estado de quejas:</label>
                <textarea class="notas-grafica w-full p-2 border rounded-lg" rows="3" placeholder="Escriba sus observaciones aquí..."></textarea>
            </div>
        </div>

        <!-- Tabla de resumen -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-lg font-semibold mb-4">Resumen General</h2>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoría</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php foreach ($estadisticasInsectos as $insecto): ?>
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900"><?= esc($insecto['insecto']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-900"><?= $insecto['total'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notas adicionales:</label>
                <textarea class="notas-grafica w-full p-2 border rounded-lg" rows="3" placeholder="Escriba sus observaciones aquí..."></textarea>
            </div>
        </div>
    </div>
</div>

<script>
// Datos para las gráficas
const datosAnuales = <?= json_encode($estadisticasAnuales) ?>;
const datosInsectos = <?= json_encode($estadisticasInsectos) ?>;
const datosClasificacion = <?= json_encode($estadisticasClasificacion) ?>;
const datosEstado = <?= json_encode($estadisticasEstado) ?>;
const datosSemanales = <?= json_encode($estadisticasSemanales) ?>;
const datosLineas = <?= json_encode($estadisticasLineas) ?>;
const datosEstadoQueja = <?= json_encode($estadisticasEstadoQueja) ?>;

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

// Gráfica de líneas - Quejas por semana
new Chart(document.getElementById('graficaSemanal'), {
    type: 'line',
    data: {
        labels: Array.from({length: 52}, (_, i) => 'Semana ' + (i + 1)),
        datasets: [
            {
                label: añoActual.toString(),
                data: datosAñoActual,
                borderColor: '#7C3AED', // Púrpura
                backgroundColor: '#C4B5FD',
                fill: true,
                tension: 0.4
            },
            {
                label: (añoActual - 1).toString(),
                data: datosAñoAnterior,
                borderColor: '#059669', // Verde
                backgroundColor: '#6EE7B7',
                fill: true,
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            },
            title: {
                display: true,
                text: '#QUEJAS VS SEMANA',
                font: {
                    size: 16,
                    weight: 'bold'
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                min: 0,
                max: function(context) {
                    // Get the maximum value from the dataset
                    let max = 0;
                    for (let i = 0; i < context.chart.data.datasets.length; i++) {
                        const dataset = context.chart.data.datasets[i];
                        for (let j = 0; j < dataset.data.length; j++) {
                            if (dataset.data[j] > max) {
                                max = dataset.data[j];
                            }
                        }
                    }
                    // Return a reasonable maximum value (max + buffer)
                    return Math.max(5, Math.ceil(max * 1.45));
                },
                title: {
                    display: true,
                    text: 'Número de Quejas',
                    font: {
                        size: 13,
                        weight: 'bold'
                    }
                },
                ticks: {
                    precision: 0,
                    stepSize: 1
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    }
});

// Gráfica de pastel - Frecuencia de líneas afectadas
new Chart(document.getElementById('graficaLines'), {
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
        plugins: {
            legend: {
                position: 'right'
            },
            title: {
                display: true,
                text: 'FRECUENCIA DE LÍNEAS AFECTADAS',
                font: {
                    size: 16,
                    weight: 'bold'
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
            label: 'Número de quejas',
            data: datosAnuales.map(d => d.total),
            backgroundColor: '#3B82F6',
            borderColor: '#2563EB',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
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
            data: datosInsectos.map(d => d.total),
            backgroundColor: colores
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right'
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
            label: 'Número de quejas',
            data: datosClasificacion.map(d => d.total),
            backgroundColor: datosClasificacion.map(d => coloresClasificacion[d.clasificacion] || '#6B7280')
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
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
        plugins: {
            legend: {
                position: 'right',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Gráfico circular - Estado de la Queja
new Chart(document.getElementById('graficaEstadoQueja'), {
    type: 'doughnut',
    data: {
        labels: datosEstadoQueja.map(item => item.estado_queja || 'No especificado'),
        datasets: [{
            data: datosEstadoQueja.map(item => item.total),
            backgroundColor: [
                '#EAB308', // Amarillo para Pendiente
                '#22C55E', // Verde para Resuelta
                '#94A3B8'  // Gris para No especificado
            ],
            borderColor: '#ffffff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'right',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = Math.round((value / total) * 100);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// Esperar a que el DOM esté completamente cargado
document.addEventListener('DOMContentLoaded', function() {
    // Encontrar el botón de generar PDF
    const btnGenerarPDF = document.getElementById('btnGenerarPDF');

    // Evento para generar el PDF
    btnGenerarPDF.addEventListener('click', function() {
        // Mostrar indicador de carga
        mostrarCargando('Generando PDF...');

        // Esperar a que las imágenes y fuentes se carguen
        setTimeout(function() {
            generarPDFConDOM2PDF();
        }, 500);
    });

    // Función para mostrar indicador de carga
    function mostrarCargando(mensaje) {
        const overlay = document.createElement('div');
        overlay.id = 'overlay-cargando';
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        overlay.style.display = 'flex';
        overlay.style.justifyContent = 'center';
        overlay.style.alignItems = 'center';
        overlay.style.zIndex = '9999';

        const contenido = document.createElement('div');
        contenido.style.backgroundColor = 'white';
        contenido.style.padding = '20px';
        contenido.style.borderRadius = '8px';
        contenido.style.textAlign = 'center';
        contenido.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';

        contenido.innerHTML = `
            <p style="margin-bottom: 15px;">${mensaje}</p>
            <div style="width: 40px; height: 40px; border: 4px solid #f3f3f3; 
                 border-top: 4px solid #3498db; border-radius: 50%; 
                 margin: 0 auto; animation: girar 1s linear infinite;"></div>
        `;

        const estilo = document.createElement('style');
        estilo.innerHTML = `
            @keyframes girar {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;

        document.head.appendChild(estilo);
        overlay.appendChild(contenido);
        document.body.appendChild(overlay);
    }

    // Función para ocultar indicador de carga
    function ocultarCargando() {
        const overlay = document.getElementById('overlay-cargando');
        if (overlay) {
            document.body.removeChild(overlay);
        }
    }

    // Función para generar el PDF usando la biblioteca html2pdf.js
    function generarPDFConDOM2PDF() {
        try {
            // Mostrar mensaje de procesamiento
            mostrarCargando('Capturando gráficos...');
            
            // Primero capturamos todas las imágenes de los gráficos
            const chartImagesObj = {};
            
            // Función para convertir Canvas a imagen base64
            function getChartImageData(chartId) {
                const canvas = document.getElementById(chartId);
                if (canvas) {
                    return canvas.toDataURL('image/png');
                }
                return null;
            }
            
            // Capturar todas las imágenes de los gráficos
            chartImagesObj.semanal = getChartImageData('graficaSemanal');
            chartImagesObj.anual = getChartImageData('graficaAnual');
            chartImagesObj.lineas = getChartImageData('graficaLines');
            chartImagesObj.insectos = getChartImageData('graficaInsectos');
            chartImagesObj.clasificacion = getChartImageData('graficaClasificacion');
            chartImagesObj.estado = getChartImageData('graficaEstado');
            
            // Obtener sede seleccionada para la URL
            const sedeId = document.getElementById('sede_id').value;
            
            // Crear un formulario para enviar los datos al servidor
            mostrarCargando('Generando PDF en el servidor...');
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?= site_url('quejas/generarPDF') ?>?sede_id=' + sedeId;
            form.target = '_blank'; // Abrir en nueva pestaña
            
            // Crear campo oculto para las imágenes de los gráficos
            const chartImagesInput = document.createElement('input');
            chartImagesInput.type = 'hidden';
            chartImagesInput.name = 'chart_images';
            chartImagesInput.value = JSON.stringify(chartImagesObj);
            
            // Agregar campos al formulario
            form.appendChild(chartImagesInput);
            
            // Agregar formulario al documento, enviarlo y luego eliminarlo
            document.body.appendChild(form);
            form.submit();
            
            // Limpiar
            setTimeout(() => {
                document.body.removeChild(form);
                ocultarCargando();
            }, 1000);
        } catch (error) {
            console.error('Error en el proceso de generación de PDF:', error);
            alert('Error en el proceso: ' + error.message);
            ocultarCargando();
        }
    }
});
</script>
<?= $this->endSection() ?> 