<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
    /* Estilos para el contenedor del plano */
    .plano-container {
        position: relative;
        width: 100%;
        overflow: auto;
        margin: 0 auto;
        border: 1px solid #ddd;
        background-color: #f5f5f5;
    }
    
    /* Estilos para la imagen del plano - IMPORTANTE: no modificar el tamaño original */
    .plano-imagen {
        display: block;
        width: auto;
        height: auto;
        max-width: none; /* Permite que la imagen mantenga su tamaño original */
        max-height: none; /* Permite que la imagen mantenga su tamaño original */
        position: relative; /* Asegura que la imagen tenga posición relativa */
        z-index: 1; /* Establece un z-index bajo para la imagen */
    }
    
    /* Contenedor con scroll para la imagen */
    .plano-scroll-container {
        overflow: auto;
        max-height: 80vh;
        max-width: 100%;
    }
    
    /* Estilos para los marcadores de trampas */
    .trampa-marker {
        position: absolute;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: red;
        border: 2px solid white;
        z-index: 10;
        transform: translate(-50%, -50%);
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
    }
    
    /* Estilos para los marcadores de incidencias */
    .incidencia-marker {
        position: absolute;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        z-index: 20;
        transform: translate(-50%, -50%);
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    /* Estilos para diferentes tipos de plagas */
    .plaga-mosca { background-color: rgba(255, 0, 0, 0.7); }
    .plaga-cucaracha { background-color: rgba(0, 0, 255, 0.7); }
    .plaga-hormiga { background-color: rgba(0, 128, 0, 0.7); }
    .plaga-roedor { background-color: rgba(128, 0, 128, 0.7); }
    .plaga-otro { background-color: rgba(128, 128, 0, 0.7); }
    
    /* Estilos para diferentes tipos de incidencias */
    .incidencia-Captura { border: 3px solid #ff6600; }
    .incidencia-Hallazgo { border: 3px solid #00cc66; }
    
    /* Tooltip para mostrar información */
    .tooltip {
        position: fixed; /* Cambiado a fixed para evitar problemas con el scroll */
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        z-index: 100;
        display: none;
        font-size: 12px;
        max-width: 250px;
    }
    
    /* Controles de zoom */
    .zoom-controls {
        position: absolute;
        top: 10px;
        right: 10px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        z-index: 30;
    }
    
    .zoom-btn {
        width: 36px;
        height: 36px;
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    
    .zoom-btn:hover {
        background-color: #f0f0f0;
    }
    
    /* Estilos para el mapa de calor */
    #heatmapContainer {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        z-index: 5; /* Mayor que la imagen pero menor que los marcadores */
        pointer-events: none; /* Permite que los clics pasen a través del mapa de calor */
    }
    
    /* Asegurarse de que el contenedor del mapa de calor esté correctamente posicionado */
    #planoWrapper {
        position: relative;
    }

    /* Estilos para los botones de reportes */
    .reportes-btn-group {
        margin-bottom: 15px;
    }
    
    .reportes-btn-group .btn {
        margin-right: 5px;
    }
    
    /* Estilo para el tooltip */
    .tooltip-inner {
        max-width: 300px;
        background-color: rgba(0, 0, 0, 0.8);
    }
    
    /* Estilos para las trampas de diferentes tipos */
    .trampa-marker.tipo-Pegajosa { background-color: blue; }
    .trampa-marker.tipo-LuzUV { background-color: purple; }
    .trampa-marker.tipo-Cebos { background-color: green; }
    .trampa-marker.tipo-Jaula { background-color: brown; }
    .trampa-marker.tipo-Electronica { background-color: orange; }
    .trampa-marker.tipo-Feromonas { background-color: pink; }
    
    /* Leyenda de tipos de trampas */
    .leyenda-trampas {
        background: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 10px;
        margin-top: 10px;
    }
    
    .leyenda-item {
        display: inline-block;
        margin-right: 15px;
    }
    
    .leyenda-color {
        display: inline-block;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        margin-right: 5px;
        border: 1px solid #999;
    }
</style>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold"><?= esc($plano['nombre']) ?> - <?= esc($sede['nombre']) ?></h1>
        <div class="flex gap-2">
            <!-- Botones de reportes -->
            
            <button id="toggleTrampasBtn" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-map-marker-alt mr-1"></i> Mostrar Trampas
            </button>
            <button id="toggleHeatmapBtn" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="fas fa-fire mr-1"></i> Mapa de Calor
            </button>
            <a href="<?= base_url('blueprints/viewplano/' . $plano['id']) ?>" 
               class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Ver plano interactivo
            </a>
        </div>
    </div>
    
    <!-- Contenedor del plano con incidencias y trampas -->
    <div class="plano-container bg-white rounded-lg shadow-md">
        <?php if (!empty($imagen_url)): ?>
            <div class="zoom-controls">
                <button class="zoom-btn" id="zoomIn">+</button>
                <button class="zoom-btn" id="zoomOut">-</button>
                <button class="zoom-btn" id="zoomReset"><i class="fas fa-sync-alt"></i></button>
            </div>
            
            <div id="planoScrollContainer" class="plano-scroll-container">
                <div id="planoWrapper" class="relative">
                    <!-- Contenedor para el mapa de calor (ahora antes de la imagen) -->
                    <div id="heatmapContainer"></div>
                    
                    <img id="planoImagen" src="<?= $imagen_url ?>" alt="<?= esc($plano['nombre']) ?>" class="plano-imagen">
                    
                    <!-- Aquí se cargarán las trampas dinámicamente -->
                    <div id="trampasMarcadores"></div>
                    
                    <!-- Aquí se cargarán las incidencias dinámicamente -->
                    <div id="incidenciasMarcadores"></div>
                </div>
            </div>
            <div id="tooltip" class="tooltip"></div>
        <?php else: ?>
            <div class="w-full h-64 bg-gray-200 flex items-center justify-center">
                <p class="text-gray-500">No hay imagen disponible para este plano</p>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Leyenda de tipos de plagas e incidencias -->
    <div class="mt-6 bg-white rounded-lg shadow-md p-4">
        <h3 class="font-semibold mb-2">Leyenda</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <h4 class="font-medium mb-2">Tipos de Plagas</h4>
                <div class="grid grid-cols-2 gap-2">
                    <div class="flex items-center">
                        <span class="inline-block w-4 h-4 rounded-full bg-red-600 mr-2"></span>
                        <span>Mosca</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-block w-4 h-4 rounded-full bg-blue-600 mr-2"></span>
                        <span>Cucaracha</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-block w-4 h-4 rounded-full bg-green-600 mr-2"></span>
                        <span>Hormiga</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-block w-4 h-4 rounded-full bg-purple-600 mr-2"></span>
                        <span>Roedor</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-block w-4 h-4 rounded-full bg-yellow-600 mr-2"></span>
                        <span>Otro</span>
                    </div>
                </div>
            </div>
            
            <div>
                <h4 class="font-medium mb-2">Tipos de Incidencias</h4>
                <div class="grid grid-cols-2 gap-2">
                    <div class="flex items-center">
                        <span class="inline-block w-4 h-4 rounded-full bg-gray-300 border-2 border-orange-500 mr-2"></span>
                        <span>Captura</span>
                    </div>
                    <div class="flex items-center">
                        <span class="inline-block w-4 h-4 rounded-full bg-gray-300 border-2 border-green-500 mr-2"></span>
                        <span>Hallazgo</span>
                    </div>
                    <div class="flex items-center mt-2">
                        <span class="inline-block w-4 h-4 rounded-full bg-red-500 border-2 border-white mr-2"></span>
                        <span>Trampa</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Resumen de incidencias -->
        <div class="mt-4 border-t pt-4">
            <h4 class="font-medium mb-2">Resumen de Incidencias</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="px-4 py-2 border">Tipo de Plaga</th>
                            <th class="px-4 py-2 border">Tipo de Incidencia</th>
                            <th class="px-4 py-2 border">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody id="resumenIncidencias">
                        <tr>
                            <td colspan="3" class="px-4 py-2 text-center text-gray-500">Cargando resumen...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Cargar la biblioteca heatmap.js -->
<script src="https://cdn.jsdelivr.net/npm/heatmapjs@2.0.2/heatmap.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Datos de incidencias y trampas
    const incidencias = <?= !empty($incidencias) ? json_encode($incidencias) : '[]' ?>;
    const trampas = <?= !empty($trampas) ? json_encode($trampas) : '[]' ?>;
    const estadoPlano = <?= !empty($estadoPlano) ? json_encode($estadoPlano) : 'null' ?>;
    
    console.log("Incidencias:", incidencias);
    console.log("Trampas:", trampas);
    console.log("Estado del plano:", estadoPlano);
    
    // Elementos del DOM
    const planoImagen = document.getElementById('planoImagen');
    const trampasMarcadores = document.getElementById('trampasMarcadores');
    const incidenciasMarcadores = document.getElementById('incidenciasMarcadores');
    const tooltip = document.getElementById('tooltip');
    const resumenIncidencias = document.getElementById('resumenIncidencias');
    const toggleTrampasBtn = document.getElementById('toggleTrampasBtn');
    const toggleHeatmapBtn = document.getElementById('toggleHeatmapBtn');
    const planoWrapper = document.getElementById('planoWrapper');
    const heatmapContainer = document.getElementById('heatmapContainer');
    
    // Variables para el zoom
    let currentScale = 1;
    const zoomInBtn = document.getElementById('zoomIn');
    const zoomOutBtn = document.getElementById('zoomOut');
    const zoomResetBtn = document.getElementById('zoomReset');
    
    // Estado para mostrar/ocultar trampas y mapa de calor
    let mostrarTrampas = true;
    let mostrarHeatmap = false;
    
    // Inicializar el mapa de calor
    let heatmapInstance = h337.create({
        container: heatmapContainer,
        radius: 40,
        maxOpacity: 0.6, // Reducido para que se vea la imagen de fondo
        minOpacity: 0,
        blur: 0.8,
        gradient: {
            0.4: 'blue',
            0.6: 'cyan',
            0.7: 'lime',
            0.8: 'yellow',
            1.0: 'red'
        }
    });
    
    // Función para generar datos para el mapa de calor
    function generarDatosHeatmap() {
        const puntos = [];
        let maxValue = 0;
        
        // Agrupar incidencias por coordenadas
        const incidenciasPorCoordenada = {};
        
        incidencias.forEach(incidencia => {
            if (!incidencia.trampa || !incidencia.trampa.coordenada_x || !incidencia.trampa.coordenada_y) {
                return;
            }
            
            const x = Math.round(parseFloat(incidencia.trampa.coordenada_x));
            const y = Math.round(parseFloat(incidencia.trampa.coordenada_y));
            const key = `${x}-${y}`;
            
            if (!incidenciasPorCoordenada[key]) {
                incidenciasPorCoordenada[key] = {
                    x: x,
                    y: y,
                    value: 0
                };
            }
            
            // Incrementar el valor basado en la cantidad de organismos
            const cantidad = parseInt(incidencia.cantidad_organismos || 1);
            incidenciasPorCoordenada[key].value += cantidad;
            
            // Actualizar el valor máximo
            if (incidenciasPorCoordenada[key].value > maxValue) {
                maxValue = incidenciasPorCoordenada[key].value;
            }
        });
        
        // Convertir el objeto a un array de puntos
        for (const key in incidenciasPorCoordenada) {
            puntos.push(incidenciasPorCoordenada[key]);
        }
        
        return {
            max: maxValue || 10,
            data: puntos
        };
    }
    
    // Función para actualizar el mapa de calor
    function actualizarHeatmap() {
        if (mostrarHeatmap) {
            const datosHeatmap = generarDatosHeatmap();
            heatmapInstance.setData(datosHeatmap);
            heatmapContainer.style.display = 'block';
            
            // Asegurarse de que el contenedor del mapa de calor tenga el tamaño correcto
            heatmapContainer.style.width = `${planoImagen.width}px`;
            heatmapContainer.style.height = `${planoImagen.height}px`;
        } else {
            heatmapContainer.style.display = 'none';
        }
    }
    
    // Evento para mostrar/ocultar el mapa de calor
    toggleHeatmapBtn.addEventListener('click', () => {
        mostrarHeatmap = !mostrarHeatmap;
        actualizarHeatmap();
        
        // Actualizar el texto del botón
        toggleHeatmapBtn.innerHTML = mostrarHeatmap ? 
            '<i class="fas fa-fire mr-1"></i> Ocultar Mapa de Calor' : 
            '<i class="fas fa-fire mr-1"></i> Mostrar Mapa de Calor';
        
        // Si el mapa de calor está activo, ocultar los marcadores de incidencias
        if (mostrarHeatmap) {
            incidenciasMarcadores.style.display = 'none';
        } else {
            incidenciasMarcadores.style.display = 'block';
        }
    });
    
    // Función para aplicar zoom
    function applyZoom() {
        planoImagen.style.transform = `scale(${currentScale})`;
        planoImagen.style.transformOrigin = 'top left';
        
        // Ajustar la posición de los marcadores según el zoom
        document.querySelectorAll('.trampa-marker, .incidencia-marker').forEach(marker => {
            const x = parseFloat(marker.dataset.originalX);
            const y = parseFloat(marker.dataset.originalY);
            
            marker.style.left = `${x}px`;
            marker.style.top = `${y}px`;
        });
        
        // Actualizar el mapa de calor si está visible
        if (mostrarHeatmap) {
            actualizarHeatmap();
        }
    }
    
    // Eventos de zoom
    zoomInBtn.addEventListener('click', () => {
        currentScale += 0.1;
        applyZoom();
    });
    
    zoomOutBtn.addEventListener('click', () => {
        if (currentScale > 0.2) {
            currentScale -= 0.1;
            applyZoom();
        }
    });
    
    zoomResetBtn.addEventListener('click', () => {
        currentScale = 1;
        applyZoom();
    });
    
    // Evento para mostrar/ocultar trampas
    toggleTrampasBtn.addEventListener('click', () => {
        mostrarTrampas = !mostrarTrampas;
        trampasMarcadores.style.display = mostrarTrampas ? 'block' : 'none';
        toggleTrampasBtn.innerHTML = mostrarTrampas ? 
            '<i class="fas fa-map-marker-alt mr-1"></i> Ocultar Trampas' : 
            '<i class="fas fa-map-marker-alt mr-1"></i> Mostrar Trampas';
    });
    
    // Función para mostrar las trampas en el plano
    function mostrarTrampasEnPlano() {
        // Limpiar marcadores existentes
        trampasMarcadores.innerHTML = '';
        
        // Si no hay trampas, no hacer nada
        if (trampas.length === 0) {
            return;
        }
        
        // Crear marcadores para cada trampa
        trampas.forEach(trampa => {
            // Verificar si la trampa tiene coordenadas
            if (!trampa.coordenada_x || !trampa.coordenada_y) {
                return; // Saltar esta trampa si no tiene coordenadas
            }
            
            const marker = document.createElement('div');
            marker.className = 'trampa-marker';
            
            // Guardar las coordenadas originales como atributos de datos
            marker.dataset.originalX = trampa.coordenada_x;
            marker.dataset.originalY = trampa.coordenada_y;
            
            // Posicionar el marcador según las coordenadas exactas
            marker.style.left = `${trampa.coordenada_x}px`;
            marker.style.top = `${trampa.coordenada_y}px`;
            
            // Agregar datos para el tooltip
            marker.dataset.id = trampa.id || '';
            marker.dataset.tipo = trampa.tipo || '';
            marker.dataset.ubicacion = trampa.ubicacion || '';
            
            // Eventos para mostrar/ocultar tooltip
            marker.addEventListener('mouseenter', function(e) {
                const rect = this.getBoundingClientRect();
                
                tooltip.innerHTML = `
                    <div class="font-semibold">Trampa</div>
                    <div><strong>ID:</strong> ${this.dataset.id}</div>
                    <div><strong>Tipo:</strong> ${this.dataset.tipo}</div>
                    <div><strong>Ubicación:</strong> ${this.dataset.ubicacion}</div>
                `;
                
                tooltip.style.display = 'block';
                tooltip.style.left = `${rect.left + window.scrollX}px`;
                tooltip.style.top = `${rect.bottom + window.scrollY + 5}px`;
            });
            
            marker.addEventListener('mouseleave', function() {
                tooltip.style.display = 'none';
            });
            
            trampasMarcadores.appendChild(marker);
        });
    }
    
    // Función para mostrar las incidencias en el plano
    function mostrarIncidencias() {
        // Limpiar marcadores existentes
        incidenciasMarcadores.innerHTML = '';
        
        if (incidencias.length === 0) {
            // No hay incidencias para mostrar
            return;
        }
        
        // Crear marcadores para cada incidencia
        incidencias.forEach(incidencia => {
            // Verificar si la incidencia tiene una trampa asociada con coordenadas
            if (!incidencia.trampa || !incidencia.trampa.coordenada_x || !incidencia.trampa.coordenada_y) {
                return; // Saltar esta incidencia si no tiene coordenadas
            }
            
            const marker = document.createElement('div');
            const tipoPlaga = incidencia.tipo_plaga || 'otro';
            const tipoIncidencia = incidencia.tipo_incidencia || 'Captura';
            
            marker.className = `incidencia-marker plaga-${tipoPlaga.toLowerCase()} incidencia-${tipoIncidencia}`;
            
            // Guardar las coordenadas originales como atributos de datos
            marker.dataset.originalX = incidencia.trampa.coordenada_x;
            marker.dataset.originalY = incidencia.trampa.coordenada_y;
            
            // Posicionar el marcador según las coordenadas exactas
            marker.style.left = `${incidencia.trampa.coordenada_x}px`;
            marker.style.top = `${incidencia.trampa.coordenada_y}px`;
            
            // Agregar icono o texto según el tipo de plaga
            const icon = document.createElement('i');
            switch (tipoPlaga.toLowerCase()) {
                case 'mosca': icon.className = 'fas fa-bug'; break;
                case 'cucaracha': icon.className = 'fas fa-bug'; break;
                case 'hormiga': icon.className = 'fas fa-bug'; break;
                case 'roedor': icon.className = 'fas fa-mouse'; break;
                default: icon.className = 'fas fa-exclamation-circle';
            }
            marker.appendChild(icon);
            
            // Agregar datos para el tooltip
            marker.dataset.id = incidencia.id || '';
            marker.dataset.tipoPlaga = tipoPlaga;
            marker.dataset.tipoIncidencia = tipoIncidencia;
            marker.dataset.fecha = incidencia.fecha || '';
            marker.dataset.cantidad = incidencia.cantidad_organismos || '0';
            marker.dataset.tipoInsecto = incidencia.tipo_insecto || '';
            marker.dataset.inspector = incidencia.inspector || '';
            marker.dataset.notas = incidencia.notas || '';
            marker.dataset.trampaId = incidencia.trampa.id || '';
            marker.dataset.trampaUbicacion = incidencia.trampa.ubicacion || '';
            
            // Eventos para mostrar/ocultar tooltip
            marker.addEventListener('mouseenter', function(e) {
                const rect = this.getBoundingClientRect();
                
                // Formatear la fecha
                let fechaFormateada = 'No disponible';
                if (this.dataset.fecha) {
                    const fecha = new Date(this.dataset.fecha);
                    fechaFormateada = fecha.toLocaleDateString('es-ES', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
                
                tooltip.innerHTML = `
                    <div class="font-semibold text-lg mb-1">${this.dataset.tipoPlaga} - ${this.dataset.tipoIncidencia}</div>
                    <div><strong>Fecha:</strong> ${fechaFormateada}</div>
                    <div><strong>Cantidad:</strong> ${this.dataset.cantidad} organismos</div>
                    <div><strong>Tipo de Insecto:</strong> ${this.dataset.tipoInsecto || 'No especificado'}</div>
                    <div><strong>Inspector:</strong> ${this.dataset.inspector || 'No especificado'}</div>
                    <div><strong>Trampa:</strong> ID ${this.dataset.trampaId} (${this.dataset.trampaUbicacion})</div>
                    ${this.dataset.notas ? `<div class="mt-2"><strong>Notas:</strong> ${this.dataset.notas}</div>` : ''}
                `;
                
                tooltip.style.display = 'block';
                tooltip.style.left = `${rect.left + window.scrollX}px`;
                tooltip.style.top = `${rect.bottom + window.scrollY + 5}px`;
            });
            
            marker.addEventListener('mouseleave', function() {
                tooltip.style.display = 'none';
            });
            
            incidenciasMarcadores.appendChild(marker);
        });
        
        // Generar resumen de incidencias
        generarResumenIncidencias();
    }
    
    // Función para generar el resumen de incidencias
    function generarResumenIncidencias() {
        // Agrupar incidencias por tipo de plaga y tipo de incidencia
        const resumen = {};
        
        incidencias.forEach(incidencia => {
            const tipoPlaga = incidencia.tipo_plaga || 'Otro';
            const tipoIncidencia = incidencia.tipo_incidencia || 'Captura';
            const key = `${tipoPlaga}-${tipoIncidencia}`;
            
            if (!resumen[key]) {
                resumen[key] = {
                    tipoPlaga: tipoPlaga,
                    tipoIncidencia: tipoIncidencia,
                    cantidad: 0
                };
            }
            
            resumen[key].cantidad++;
        });
        
        // Generar filas de la tabla de resumen
        resumenIncidencias.innerHTML = '';
        
        if (Object.keys(resumen).length === 0) {
            resumenIncidencias.innerHTML = `
                <tr>
                    <td colspan="3" class="px-4 py-2 text-center text-gray-500">No hay incidencias registradas</td>
                </tr>
            `;
            return;
        }
        
        // Ordenar el resumen por cantidad (de mayor a menor)
        const resumenOrdenado = Object.values(resumen).sort((a, b) => b.cantidad - a.cantidad);
        
        resumenOrdenado.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-4 py-2 border">${item.tipoPlaga}</td>
                <td class="px-4 py-2 border">${item.tipoIncidencia}</td>
                <td class="px-4 py-2 border text-center">${item.cantidad}</td>
            `;
            resumenIncidencias.appendChild(tr);
        });
    }
    
    // Función para cerrar el tooltip al hacer clic fuera
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.trampa-marker') && !e.target.closest('.incidencia-marker') && !e.target.closest('.tooltip')) {
            tooltip.style.display = 'none';
        }
    });
    
    // Mostrar las trampas e incidencias cuando la imagen se cargue
    planoImagen.addEventListener('load', function() {
        mostrarTrampasEnPlano();
        mostrarIncidencias();
        
        // Inicializar el tamaño del contenedor del mapa de calor
        heatmapContainer.style.width = `${planoImagen.width}px`;
        heatmapContainer.style.height = `${planoImagen.height}px`;
        
        // Posicionar el contenedor del mapa de calor exactamente sobre la imagen
        heatmapContainer.style.position = 'absolute';
        heatmapContainer.style.top = '0';
        heatmapContainer.style.left = '0';
        
        // Generar el mapa de calor (inicialmente oculto)
        actualizarHeatmap();
    });
    
    // Si la imagen ya está cargada, mostrar las trampas e incidencias
    if (planoImagen.complete) {
        mostrarTrampasEnPlano();
        mostrarIncidencias();
        
        // Inicializar el tamaño del contenedor del mapa de calor
        heatmapContainer.style.width = `${planoImagen.width}px`;
        heatmapContainer.style.height = `${planoImagen.height}px`;
        
        // Posicionar el contenedor del mapa de calor exactamente sobre la imagen
        heatmapContainer.style.position = 'absolute';
        heatmapContainer.style.top = '0';
        heatmapContainer.style.left = '0';
        
        // Generar el mapa de calor (inicialmente oculto)
        actualizarHeatmap();
    }
});
</script>
<?= $this->endSection() ?> 