<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
/**
 * Helper function to map plague types to color classes
 */
function getColorClass($tipo) {
    $colorMap = [
        'mosca' => 'red',
        'cucaracha' => 'blue',
        'hormiga' => 'green',
        'roedor' => 'purple',
        'otro' => 'yellow'
    ];
    
    // Default color for unknown types
    return isset($colorMap[$tipo]) ? $colorMap[$tipo] : 'yellow';
}
?>
<style>
    /* Estilos para el contenedor del plano */
    .plano-container {
        position: relative;
        width: 100%;
        height: 600px;
        overflow: hidden;
        margin: 0 auto;
        border: 1px solid #ddd;
        background-color: #f5f5f5;
    }
    
    /* Contenedor con scroll para la imagen */
    .plano-scroll-container {
        width: 100%;
        height: 100%;
        overflow: auto;
    }
    
    /* Estilos para la imagen del plano */
    .plano-imagen {
        max-width: none;
        max-height: none;
        display: block;
        z-index: 1;
    }
    
    /* Estilos para los marcadores de trampas */
    .trampa-marker {
        position: absolute;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #3B82F6;
        border: 2px solid white;
        z-index: 3;
        transform: translate(-50%, -50%);
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        pointer-events: auto;
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
        z-index: 4;
        transform: translate(-50%, -50%);
        box-shadow: 0 2px 4px rgba(0,0,0,0.3);
        pointer-events: auto;
    }
    
    /* Estilos para diferentes tipos de plagas */
    .plaga-mosca { background-color: rgba(255, 0, 0, 0.7); }
    .plaga-cucaracha { background-color: rgba(0, 0, 255, 0.7); }
    .plaga-hormiga { background-color: rgba(0, 128, 0, 0.7); }
    .plaga-roedor { background-color: rgba(128, 0, 128, 0.7); }
    .plaga-otro { background-color: rgba(128, 128, 0, 0.7); }
    
    /* Estilo por defecto para tipos de plagas dinámicos */
    [class^="plaga-"] { background-color: rgba(128, 128, 0, 0.7); }
    
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
        z-index: 10;
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
        z-index: 5;
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
        position: absolute !important;
        top: 0 !important;
        left: 0 !important;
        width: 100% !important;
        height: 100% !important;
        z-index: 2 !important;
        pointer-events: none !important;
        opacity: 1.0 !important;
    }
    
    /* Asegurarse de que el contenedor del mapa de calor esté correctamente posicionado */
    #planoWrapper {
        position: relative;
        display: inline-block;
        margin: 0 auto;
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
    .trampa-marker.tipo-Pegajosa { background-color: #3B82F6; }
    .trampa-marker.tipo-LuzUV { background-color: #1E3A8A; }
    .trampa-marker.tipo-Cebos { background-color: #1D4ED8; }
    .trampa-marker.tipo-Jaula { background-color: #2563EB; }
    .trampa-marker.tipo-Electronica { background-color: #1E40AF; }
    .trampa-marker.tipo-Feromonas { background-color: #1D4ED8; }
    
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
    
    /* Estilos para la leyenda del mapa de calor */
    .heatmap-legend {
        display: flex;
        align-items: center;
        margin-top: 10px;
        padding: 10px;
        border-radius: 4px;
        background: white;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    
    .heatmap-gradient {
        height: 20px;
        width: 200px;
        background: linear-gradient(to right, #22C55E, #EAB308, #EF4444);
        border-radius: 2px;
        margin-right: 15px;
    }
    
    .heatmap-labels {
        display: flex;
        justify-content: space-between;
        width: 200px;
        font-size: 12px;
        color: #666;
    }

    /* Estilos para el menú desplegable de PDF */
    .pdf-dropdown {
        position: relative;
        display: inline-block;
    }

    .pdf-dropdown .btn-pdf {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        background: linear-gradient(to right, #059669, #047857);
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .pdf-dropdown .btn-pdf:hover,
    .pdf-dropdown .btn-pdf:focus {
        background: linear-gradient(to right, #047857, #065f46);
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        color: white;
    }

    .pdf-dropdown .dropdown-menu {
        min-width: 220px;
        padding: 0.5rem 0;
        margin-top: 0.5rem;
        background: white;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(0, 0, 0, 0.1);
    }

    .pdf-dropdown .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: #374151;
        transition: all 0.2s ease;
    }

    .pdf-dropdown .dropdown-item:hover {
        background-color: #f3f4f6;
        color: #059669;
    }

    .pdf-dropdown .dropdown-item i {
        font-size: 1.1rem;
        color: #059669;
        width: 1.5rem;
        text-align: center;
    }

    .pdf-dropdown .dropdown-divider {
        margin: 0.5rem 0;
        border-top: 1px solid #e5e7eb;
    }
</style>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold"><?= esc($plano['nombre']) ?> - <?= esc($sede['nombre']) ?></h1>
        <div class="flex gap-2">
            <!-- Botones de reportes -->
            <div class="btn-group pdf-dropdown">
                <button type="button" class="btn btn-pdf dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-pdf"></i>
                    <span>Generar PDF</span>
                </button>
                <ul class="dropdown-menu">
                    
                    <li>
                        <a class="dropdown-item" href="#" id="pdfIncidenciasBtn">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Reporte de Incidencias</span>
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    
                </ul>
            </div>
            
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
    
    <!-- Filtros de incidencias y plagas -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <h4 class="font-medium mb-2">Filtrar por tipo de plaga</h4>
                <div class="flex flex-wrap gap-2">
                    <button class="filtro-plaga px-3 py-1 rounded-full bg-gray-200 hover:bg-gray-300 active" data-tipo="todos">Todos</button>
                    <?php foreach ($listaPlagas as $plaga): ?>
                        <?php $tipoClase = strtolower($plaga['plaga']); ?>
                        <button class="filtro-plaga px-3 py-1 rounded-full bg-<?= getColorClass($tipoClase) ?>-100 hover:bg-<?= getColorClass($tipoClase) ?>-200" data-tipo="<?= esc($tipoClase) ?>"><?= esc($plaga['plaga']) ?></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div>
                <h4 class="font-medium mb-2">Filtrar por tipo de incidencia</h4>
                <div class="flex flex-wrap gap-2">
                    <button class="filtro-incidencia px-3 py-1 rounded-full bg-gray-200 hover:bg-gray-300 active" data-tipo="todos">Todos</button>
                    <button class="filtro-incidencia px-3 py-1 rounded-full bg-orange-100 hover:bg-orange-200" data-tipo="Captura">Captura</button>
                    <button class="filtro-incidencia px-3 py-1 rounded-full bg-green-100 hover:bg-green-200" data-tipo="Hallazgo">Hallazgo</button>
                </div>
            </div>
        </div>
        
        <!-- Leyenda del mapa de calor -->
        <div id="heatmapLegend" class="heatmap-legend mt-4" style="display: none;">
            <div>
                <div class="font-medium mb-1">Densidad de incidencias:</div>
                <div class="heatmap-gradient"></div>
                <div class="heatmap-labels mt-1">
                    <span>Baja</span>
                    <span>Media</span>
                    <span>Alta</span>
                </div>
            </div>
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
                <div id="planoWrapper" style="position: relative; display: inline-block;">
                    <!-- La imagen del plano -->
                    <img id="planoImagen" src="<?= $imagen_url ?>" alt="<?= esc($plano['nombre']) ?>" class="plano-imagen">
                    
                    <!-- Contenedor para el mapa de calor - debe estar encima de la imagen pero debajo de los marcadores -->
                    <div id="heatmapContainer" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 2;"></div>
                    
                    <!-- Contenedor para las trampas -->
                    <div id="trampasMarcadores" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 3;"></div>
                    
                    <!-- Contenedor para las incidencias -->
                    <div id="incidenciasMarcadores" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 4;"></div>
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
                    <?php 
                    if (!empty($listaPlagas)): 
                        foreach ($listaPlagas as $plaga): 
                            $tipoClase = strtolower($plaga['plaga']);
                            $colorClass = getColorClass($tipoClase);
                    ?>
                    <div class="flex items-center">
                        <span class="inline-block w-4 h-4 rounded-full bg-<?= $colorClass ?>-600 mr-2"></span>
                        <span><?= esc($plaga['plaga']) ?></span>
                    </div>
                    <?php 
                        endforeach; 
                    else: 
                    ?>
                    <div class="flex items-center">
                        <span class="inline-block w-4 h-4 rounded-full bg-gray-600 mr-2"></span>
                        <span>No hay plagas registradas</span>
                    </div>
                    <?php endif; ?>
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
                            <th class="px-4 py-2 border">Organismos Totales</th>
                        </tr>
                    </thead>
                    <tbody id="resumenIncidencias">
                        <tr>
                            <td colspan="4" class="px-4 py-2 text-center text-gray-500">Cargando resumen...</td>
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
    const planoId = <?= $plano['id'] ?? 'null' ?>;
    const planoNombre = "<?= esc($plano['nombre']) ?>";
    const sedeNombre = "<?= esc($sede['nombre']) ?>";
    
    // Obtener el ancho renderizado guardado o usar 1600 como fallback
    const targetImageWidth = (estadoPlano && estadoPlano.renderedWidth) ? estadoPlano.renderedWidth : 1600;
    
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
    const heatmapLegend = document.getElementById('heatmapLegend');
    
    // Elementos para PDFs
    const pdfTrampasBtn = document.getElementById('pdfTrampasBtn');
    const pdfIncidenciasBtn = document.getElementById('pdfIncidenciasBtn');
    const pdfCompletoBtn = document.getElementById('pdfCompletoBtn');
    
    // Elementos de filtros
    const filtrosPlaga = document.querySelectorAll('.filtro-plaga');
    const filtrosIncidencia = document.querySelectorAll('.filtro-incidencia');
    
    // Variables para filtros
    let filtroPlaga = 'todos';
    let filtroIncidencia = 'todos';
    
    // Variables para el zoom
    let currentScale = 1;
    const zoomInBtn = document.getElementById('zoomIn');
    const zoomOutBtn = document.getElementById('zoomOut');
    const zoomResetBtn = document.getElementById('zoomReset');
    
    // Estado para mostrar/ocultar trampas y mapa de calor
    let mostrarTrampas = true;
    let mostrarHeatmap = false;
    
    // Variable para guardar la instancia del mapa de calor
    let heatmapInstance = null;
    
    // Eventos para los filtros de plagas
    filtrosPlaga.forEach(btn => {
        btn.addEventListener('click', function() {
            filtrosPlaga.forEach(b => b.classList.remove('active', 'bg-gray-400', 'text-white'));
            this.classList.add('active', 'bg-gray-400', 'text-white');
            filtroPlaga = this.dataset.tipo;
            aplicarFiltros();
            // Actualizar el mapa de calor si está visible
            if (mostrarHeatmap) {
                actualizarHeatmap();
            }
        });
    });
    
    // Eventos para los filtros de incidencias
    filtrosIncidencia.forEach(btn => {
        btn.addEventListener('click', function() {
            filtrosIncidencia.forEach(b => b.classList.remove('active', 'bg-gray-400', 'text-white'));
            this.classList.add('active', 'bg-gray-400', 'text-white');
            filtroIncidencia = this.dataset.tipo;
            aplicarFiltros();
            // Actualizar el mapa de calor si está visible
            if (mostrarHeatmap) {
                actualizarHeatmap();
            }
        });
    });
    
    // Función para aplicar filtros
    function aplicarFiltros() {
        // Mostrar/ocultar marcadores según los filtros
        document.querySelectorAll('.incidencia-marker').forEach(marker => {
            const tipoPlaga = marker.dataset.tipoPlaga?.toLowerCase();
            const tipoIncidencia = marker.dataset.tipoIncidencia;
            
            const mostrarPorPlaga = filtroPlaga === 'todos' || tipoPlaga === filtroPlaga;
            const mostrarPorIncidencia = filtroIncidencia === 'todos' || tipoIncidencia === filtroIncidencia;
            
            marker.style.display = (mostrarPorPlaga && mostrarPorIncidencia) ? 'flex' : 'none';
        });
        
        // Actualizar el mapa de calor con los filtros
        if (mostrarHeatmap) {
            actualizarHeatmap();
        }
        
        // Actualizar el resumen con los filtros
        generarResumenIncidencias();
    }
    
    // Función para generar datos para el mapa de calor
    function generarDatosHeatmap() {
        const puntos = [];
        let maxValue = 0;
        
        // Agrupar incidencias por coordenadas
        const incidenciasPorCoordenada = {};
        
        // Obtener las dimensiones actuales del plano
        const planoWidth = planoImagen.offsetWidth;
        const planoHeight = planoImagen.offsetHeight;
        
        incidencias.forEach(incidencia => {
            if (!incidencia.trampa || !incidencia.trampa.coordenada_x || !incidencia.trampa.coordenada_y) {
                return;
            }
            
            // Aplicar filtros
            const tipoPlaga = (incidencia.tipo_plaga || 'otro').toLowerCase();
            const tipoIncidencia = incidencia.tipo_incidencia || 'Captura';
            
            const pasaFiltroplaga = filtroPlaga === 'todos' || tipoPlaga === filtroPlaga;
            const pasaFiltroIncidencia = filtroIncidencia === 'todos' || tipoIncidencia === filtroIncidencia;
            
            if (!pasaFiltroplaga || !pasaFiltroIncidencia) {
                return;
            }
            
            // Usar las coordenadas exactas de las trampas
            const x = parseInt(incidencia.trampa.coordenada_x);
            const y = parseInt(incidencia.trampa.coordenada_y);
            const key = `${x}-${y}`;
            
            if (!incidenciasPorCoordenada[key]) {
                incidenciasPorCoordenada[key] = {
                    x: x,
                    y: y,
                    value: 0
                };
            }
            
            // Incrementar el valor basado en la cantidad de organismos
            // Usar un valor mínimo de 25 para mejor visualización con áreas pequeñas
            const cantidad = Math.max(25, parseInt(incidencia.cantidad_organismos || 25));
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
        
        // Ajustar el valor máximo para tener áreas rojas más pequeñas
        // Un valor máximo más alto hace que sea más difícil alcanzar el rojo
        maxValue = Math.max(maxValue, 35);
        
        // Siempre generar puntos adicionales para una visualización más circular
        const puntosAdicionales = [];
        
        puntos.forEach(punto => {
            // Menos puntos y más cercanos para áreas más pequeñas
            const numPuntos = 4; // 4 puntos por anillo
            
            // Primero, agregar punto central con valor reducido
            // Esto ayuda a evitar áreas rojas demasiado grandes
            puntosAdicionales.push({
                x: punto.x,
                y: punto.y,
                value: punto.value * 0.85 // Reducir ligeramente incluso el valor central
            });
            
            // Primer anillo muy cercano
            for (let i = 0; i < numPuntos; i++) {
                const distancia = 2; // Distancia muy reducida (antes 3)
                const angulo = i * (2 * Math.PI / numPuntos);
                
                const offsetX = Math.cos(angulo) * distancia;
                const offsetY = Math.sin(angulo) * distancia;
                
                puntosAdicionales.push({
                    x: punto.x + offsetX,
                    y: punto.y + offsetY,
                    value: punto.value * 0.7 // Valor más reducido (antes 0.8)
                });
            }
            
            // Segundo anillo ligeramente más grande
            for (let i = 0; i < numPuntos; i++) {
                const distancia = 5; // Distancia reducida (antes 8)
                const angulo = i * (2 * Math.PI / numPuntos);
                
                const offsetX = Math.cos(angulo) * distancia;
                const offsetY = Math.sin(angulo) * distancia;
                
                puntosAdicionales.push({
                    x: punto.x + offsetX,
                    y: punto.y + offsetY,
                    value: punto.value * 0.4 // Valor más reducido (antes 0.5)
                });
            }
            
            // Tercer anillo para crear gradiente
            for (let i = 0; i < numPuntos; i++) {
                const distancia = 9; // Distancia reducida (antes 15)
                const angulo = i * (2 * Math.PI / numPuntos);
                
                const offsetX = Math.cos(angulo) * distancia;
                const offsetY = Math.sin(angulo) * distancia;
                
                puntosAdicionales.push({
                    x: punto.x + offsetX,
                    y: punto.y + offsetY,
                    value: punto.value * 0.15 // Valor más reducido (antes 0.2)
                });
            }
        });
        
        puntos.push(...puntosAdicionales);
        
        console.log("Puntos del mapa de calor:", puntos.length);
        
        return {
            max: maxValue || 35,
            data: puntos
        };
    }
    
    // Función para actualizar el mapa de calor - NUEVA IMPLEMENTACIÓN
    function actualizarHeatmap() {
        // Limpiar el contenedor del mapa de calor
        heatmapContainer.innerHTML = '';
        
        if (!mostrarHeatmap) {
            heatmapContainer.style.display = 'none';
            return;
        }
        
        // Mostrar el contenedor del mapa de calor
        heatmapContainer.style.display = 'block';
        
        // Obtener datos del mapa de calor
        const datosHeatmap = generarDatosHeatmap();
        if (!datosHeatmap.data.length) {
            console.log('No hay datos para generar el mapa de calor');
            return;
        }
        
        console.log('Generando mapa de calor con', datosHeatmap.data.length, 'puntos');
        
        // Aplicar estilos específicos al contenedor del mapa de calor para asegurar que esté sobre la imagen
        heatmapContainer.style.position = 'absolute';
        heatmapContainer.style.top = '0';
        heatmapContainer.style.left = '0';
        heatmapContainer.style.width = `${planoImagen.offsetWidth}px`;
        heatmapContainer.style.height = `${planoImagen.offsetHeight}px`;
        heatmapContainer.style.zIndex = '2';
        heatmapContainer.style.pointerEvents = 'none';
        heatmapContainer.style.opacity = '1.0'; // Opacidad completa ya que no hay símbolos
        
        // Crear mapa de calor usando la biblioteca h337 con configuración mejorada para 3 colores
        const heatmapConfig = {
            container: heatmapContainer,
            radius: 20, // Radio más grande para mejor visibilidad de zonas
            maxOpacity: 0.9, // Mayor opacidad para colores más definidos
            minOpacity: 0.6, // Opacidad mínima más alta
            blur: 0.3, // Menor difuminado para zonas más definidas
            gradient: {
                0.0: '#22C55E',  // Verde (densidad baja) 
                0.5: '#EAB308',  // Amarillo (densidad media)
                1.0: '#EF4444'   // Rojo (densidad alta)
            }
        };
        
        // Crear nueva instancia
        heatmapInstance = h337.create(heatmapConfig);
        
        // Establecer los datos
        heatmapInstance.setData(datosHeatmap);
        
        console.log('Mapa de calor generado con éxito');
    }

    // Modificación en el evento para mostrar/ocultar el mapa de calor
    toggleHeatmapBtn.addEventListener('click', () => {
        mostrarHeatmap = !mostrarHeatmap;
        
        // Actualizar el mapa de calor
        actualizarHeatmap();
        
        // Actualizar el texto del botón
        toggleHeatmapBtn.innerHTML = mostrarHeatmap ? 
            '<i class="fas fa-fire mr-1"></i> Ocultar Mapa de Calor' : 
            '<i class="fas fa-fire mr-1"></i> Mostrar Mapa de Calor';
        
        // Mostrar/ocultar la leyenda del mapa de calor
        heatmapLegend.style.display = mostrarHeatmap ? 'flex' : 'none';
        
        // Ocultar/mostrar los marcadores cuando el mapa de calor está activo
        trampasMarcadores.style.display = mostrarHeatmap ? 'none' : 'block';
        incidenciasMarcadores.style.display = mostrarHeatmap ? 'none' : 'block';
    });
    
    // Función para aplicar zoom
    function applyZoom() {
        planoWrapper.style.transform = `scale(${currentScale})`;
        planoWrapper.style.transformOrigin = 'top left';
        
        // Actualizar el mapa de calor inmediatamente después del zoom
        if (mostrarHeatmap) {
            // Forzar una actualización completa del mapa de calor
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
                return;
            }
            
            const marker = document.createElement('div');
            marker.className = 'trampa-marker';
            
            // Usar las coordenadas exactas
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
            return;
        }
        
        // Crear marcadores para cada incidencia
        incidencias.forEach(incidencia => {
            // Verificar si la incidencia tiene una trampa asociada con coordenadas
            if (!incidencia.trampa || !incidencia.trampa.coordenada_x || !incidencia.trampa.coordenada_y) {
                return;
            }
            
            const marker = document.createElement('div');
            const tipoPlaga = incidencia.tipo_plaga || 'otro';
            const tipoIncidencia = incidencia.tipo_incidencia || 'Captura';
            
            marker.className = `incidencia-marker plaga-${tipoPlaga.toLowerCase()} incidencia-${tipoIncidencia}`;
            
            // Usar las coordenadas exactas
            marker.style.left = `${incidencia.trampa.coordenada_x}px`;
            marker.style.top = `${incidencia.trampa.coordenada_y}px`;
            
            // Agregar icono o texto según el tipo de plaga
            const icon = document.createElement('i');
            switch (tipoPlaga.toLowerCase()) {
                case 'mosca': 
                case 'cucaracha': 
                case 'hormiga': 
                    icon.className = 'fas fa-bug'; 
                    break;
                case 'roedor': 
                    icon.className = 'fas fa-mouse'; 
                    break;
                case 'araña':
                case 'alacrán':
                    icon.className = 'fas fa-spider';
                    break;
                default: 
                    icon.className = 'fas fa-exclamation-circle';
            }
            marker.appendChild(icon);
            
            // Agregar datos para el tooltip
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
        
        // Aplicar filtros iniciales
        aplicarFiltros();
        
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
            // Obtener cantidad de organismos (número o 0 si no existe)
            const cantidadOrganismos = parseInt(incidencia.cantidad_organismos || 0);
            
            // Aplicar filtros
            const pasaFiltroPlaga = filtroPlaga === 'todos' || tipoPlaga.toLowerCase() === filtroPlaga;
            const pasaFiltroIncidencia = filtroIncidencia === 'todos' || tipoIncidencia === filtroIncidencia;
            
            if (!pasaFiltroPlaga || !pasaFiltroIncidencia) {
                return; // Saltar esta incidencia si no pasa los filtros
            }
            
            const key = `${tipoPlaga}-${tipoIncidencia}`;
            
            if (!resumen[key]) {
                resumen[key] = {
                    tipoPlaga: tipoPlaga,
                    tipoIncidencia: tipoIncidencia,
                    cantidad: 0,
                    organismosTotales: 0
                };
            }
            
            resumen[key].cantidad++;
            resumen[key].organismosTotales += cantidadOrganismos;
        });
        
        // Generar filas de la tabla de resumen
        resumenIncidencias.innerHTML = '';
        
        if (Object.keys(resumen).length === 0) {
            resumenIncidencias.innerHTML = `
                <tr>
                    <td colspan="4" class="px-4 py-2 text-center text-gray-500">No hay incidencias registradas</td>
                </tr>
            `;
            return;
        }
        
        // Ordenar el resumen por cantidad de organismos (de mayor a menor)
        const resumenOrdenado = Object.values(resumen).sort((a, b) => b.organismosTotales - a.organismosTotales);
        
        resumenOrdenado.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-4 py-2 border">${item.tipoPlaga}</td>
                <td class="px-4 py-2 border">${item.tipoIncidencia}</td>
                <td class="px-4 py-2 border text-center">${item.cantidad}</td>
                <td class="px-4 py-2 border text-center font-semibold">${item.organismosTotales}</td>
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
    
    // Calcular y aplicar la escala correcta para la imagen cuando se cargue
    planoImagen.addEventListener('load', function() {
        // Obtener las dimensiones naturales de la imagen
        const naturalWidth = this.naturalWidth;
        const naturalHeight = this.naturalHeight;
        
        // Calcular la escala para ajustar la altura a 600px
        const scale = 600 / naturalHeight;
        
        // Aplicar la escala a la imagen
        this.style.width = `${naturalWidth * scale}px`;
        this.style.height = '600px';
        
        // Ajustar el wrapper al mismo tamaño
        planoWrapper.style.width = `${naturalWidth * scale}px`;
        planoWrapper.style.height = '600px';
        
        // Establecer la escala inicial
        currentScale = 1;
        
        // Mostrar las trampas e incidencias
        mostrarTrampasEnPlano();
        mostrarIncidencias();
        
        // Actualizar el mapa de calor si estaba visible
        if (mostrarHeatmap) {
            actualizarHeatmap();
        }
        
        console.log(`Imagen cargada y escalada a altura de 600px`);
    });

    // Verificar si la imagen ya está cargada
    if (planoImagen.complete) {
        // Disparar manualmente el evento load
        const event = new Event('load');
        planoImagen.dispatchEvent(event);
    }

    // Función para obtener texto del filtro activo
    function getTextoFiltroPlaga() {
        if (filtroPlaga === 'todos') return 'Todas las plagas';
        return 'Plaga: ' + filtroPlaga.charAt(0).toUpperCase() + filtroPlaga.slice(1);
    }
    
    function getTextoFiltroIncidencia() {
        if (filtroIncidencia === 'todos') return 'Todas las incidencias';
        return 'Incidencia: ' + filtroIncidencia;
    }
    
    // Función para generar informe PDF de trampas
    function generarPDFTrampas() {
        // URL base para el endpoint de generación de PDF
        const baseUrl = '<?= base_url('reports/pdf_trampas') ?>';
        
        // Construir URL con parámetros, incluyendo filtros activos
        const url = `${baseUrl}/${planoId}?filtro_plaga=${filtroPlaga}&filtro_incidencia=${filtroIncidencia}`;
        
        // Abrir en nueva ventana
        window.open(url, '_blank');
    }
    
    // Función para generar informe PDF de incidencias
    function generarPDFIncidencias() {
        // URL base para el endpoint de generación de PDF
        const baseUrl = '<?= base_url('reports/pdf_incidencias') ?>';
        
        // Construir URL con parámetros, incluyendo filtros activos
        const url = `${baseUrl}/${planoId}?filtro_plaga=${filtroPlaga}&filtro_incidencia=${filtroIncidencia}`;
        
        // Abrir en nueva ventana
        window.open(url, '_blank');
    }
    
    // Función para generar informe PDF completo
    function generarPDFCompleto() {
        // URL base para el endpoint de generación de PDF
        const baseUrl = '<?= base_url('reports/pdf_completo') ?>';
        
        // Construir URL con parámetros, incluyendo filtros activos
        const url = `${baseUrl}/${planoId}?filtro_plaga=${filtroPlaga}&filtro_incidencia=${filtroIncidencia}`;
        
        // Abrir en nueva ventana
        window.open(url, '_blank');
    }
    
    // Asignar eventos a los botones de PDF
    if (pdfTrampasBtn) pdfTrampasBtn.addEventListener('click', generarPDFTrampas);
    if (pdfIncidenciasBtn) pdfIncidenciasBtn.addEventListener('click', generarPDFIncidencias);
    if (pdfCompletoBtn) pdfCompletoBtn.addEventListener('click', generarPDFCompleto);
});
</script>
<?= $this->endSection() ?> 