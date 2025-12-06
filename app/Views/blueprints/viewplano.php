<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Agregar en el head -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    /* Estilos para el contenedor del plano */
    #planoContainer {
        position: relative;
        width: 100%;
        height: 600px;
        overflow: auto;
        border: 1px solid #ccc;
        background-color: #f5f5f5;
    }
    
    /* Estilos para la imagen del plano - Modificar para asegurar que se ajuste al contenedor */
    #planoImage {
        width: auto;
        height: auto;
        max-width: 100%; /* Cambiar para que se ajuste al contenedor */
        max-height: 100%; /* Cambiar para que se ajuste al contenedor */
        object-fit: contain; /* Asegurar que la imagen mantenga su proporción y se ajuste */
        display: none;
        position: relative; /* Necesario para posicionamiento correcto */
        margin: 0 auto; /* Centrar la imagen horizontalmente */
    }
    
    /* Estilos para el texto de placeholder */
    #placeholderText {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #999;
        text-align: center;
    }
    
    /* Estilos para los marcadores de trampas */
    .trap-marker {
        position: absolute;
        width: 24px;
        height: 24px;
        background-color: rgba(59, 130, 246, 0.7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
        cursor: pointer;
        z-index: 10;
        transition: all 0.2s ease;
    }
    
    .trap-marker:hover {
        transform: translate(-50%, -50%) scale(1.2);
        background-color: rgba(59, 130, 246, 0.9);
    }
    
    .trap-marker.highlighted {
        background-color: #1D4ED8;
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.5);
    }
    
    /* Estilos para el tooltip */
    .trap-tooltip {
        position: fixed; /* Cambiado a fixed para evitar problemas con el scroll */
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        z-index: 100;
        display: none;
    }
    
    .trap-tooltip button {
        background-color: #4a90e2;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        width: 100%;
        text-align: left;
        margin-bottom: 4px;
        transition: background-color 0.2s;
    }
    
    .trap-tooltip button:hover {
        background-color: #3a80d2;
    }
    
    .trap-tooltip .add-incidence-btn {
        background-color: #3B82F6;
    }
    
    .trap-tooltip .add-incidence-btn:hover {
        background-color: #2563EB;
    }
    
    .trap-tooltip .edit-id-btn {
        background-color: #10B981;
    }
    
    .trap-tooltip .edit-id-btn:hover {
        background-color: #059669;
    }
    
    /* Estilos para las zonas */
    .zone-polygon, .zona {
        position: absolute;
        background-color: rgba(0, 128, 255, 0.2);
        border: 2px dashed rgba(0, 128, 255, 0.5);
        pointer-events: none;
        cursor: move;
    }
    
    /* Estilos para el dropdown de trampas */
    .trap-menu {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background-color: white;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 1000;
        max-height: 300px;
        overflow-y: auto;
        margin-top: 2px;
    }
    
    .trap-menu a {
        display: block;
        padding: 10px 12px;
        color: #333;
        text-decoration: none;
        border-bottom: 1px solid #f0f0f0;
        transition: background-color 0.2s;
    }
    
    .trap-menu a:last-child {
        border-bottom: none;
    }
    
    .trap-menu a:hover {
        background-color: #f8f9fa;
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
        background-color: rgba(255, 255, 255, 0.8);
        padding: 5px;
        border-radius: 4px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
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
    }
    
    .zoom-btn:hover {
        background-color: #f0f0f0;
    }
    
    /* Animaciones */
    @keyframes highlight {
        0% { box-shadow: 0 0 0 0 rgba(255, 102, 0, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(255, 102, 0, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 102, 0, 0); }
    }
    
    @keyframes pulse {
        0% { transform: translate(-50%, -50%) scale(1); }
        50% { transform: translate(-50%, -50%) scale(1.3); background-color: #ff6600; }
        100% { transform: translate(-50%, -50%) scale(1); }
    }
    
    /* Estilos para las filas seleccionadas en la tabla */
    tr.selected {
        background-color: #e6f2ff !important;
    }

    /* Estilos adicionales para zonas específicas */
    .zona-circulo {
        border-radius: 50%;
    }

    .zona-poligono {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(100, 100, 100, 0.1);
        border: 2px dashed #666;
        pointer-events: auto;
        cursor: pointer;
    }

    .zona-poligono-temporal {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 0, 0.2);
        border: 2px dashed #ff0;
    }

    .punto-poligono {
        position: absolute;
        width: 8px;
        height: 8px;
        background: #ff0;
        border: 1px solid #000;
        border-radius: 50%;
        transform: translate(-50%, -50%);
        z-index: 20;
    }

    .zona-texto {
        position: absolute;
        background: rgba(255, 255, 255, 0.8);
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        transform: translate(-50%, -50%);
        z-index: 15;
        pointer-events: none;
        white-space: nowrap;
    }

    .resize-handle {
        position: absolute;
        width: 10px;
        height: 10px;
        background: white;
        border: 1px solid #666;
        cursor: se-resize;
    }

    @keyframes highlight {
        0% { background-color: rgba(255, 255, 0, 0.5); }
        100% { background-color: transparent; }
    }

    .trap-tooltip {
        position: fixed; /* Cambiado a fixed para evitar problemas con el scroll */
        background-color: white;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        z-index: 1000;
        display: none;
        min-width: 150px;
        animation: fadeIn 0.2s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .trap-tooltip button {
        background-color: #4CAF50;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background-color 0.3s;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .trap-tooltip button:hover {
        background-color: #45a049;
    }
    
    .trap-marker.highlighted {
        box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.7);
        transform: translate(-50%, -50%) scale(1.2);
        z-index: 1000;
    }

    /* Estilos para botones de reportes */
    .reportes-btn-group {
        margin-bottom: 15px;
    }
    
    .reportes-btn-group .btn {
        margin-right: 5px;
    }

    /* Estilos para la tabla de incidencias */
    .tabla-incidencias {
        width: 100%;
        border-collapse: collapse;
    }
    
    .tabla-incidencias th {
        position: sticky;
        top: 0;
        background-color: #f9fafb;
        z-index: 10;
    }
    
    .tabla-incidencias-container {
        max-height: 600px;
        overflow-y: auto;
        overflow-x: auto;
    }
    
    /* Estilos para el scroll en la tabla del modal */
    #listaIncidenciasModal > div {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f7fafc;
    }
    
    #listaIncidenciasModal > div::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    #listaIncidenciasModal > div::-webkit-scrollbar-track {
        background: #f7fafc;
        border-radius: 4px;
    }
    
    #listaIncidenciasModal > div::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 4px;
    }
    
    #listaIncidenciasModal > div::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }
</style>

<div class="space-y-6">
    <!-- Encabezado -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold">Sistema de Mapeo de Trampas</h1>
            <p class="text-gray-500"><?= $plano['nombre'] ?> - <?= $sede['nombre'] ?></p>
        </div>
        <div class="flex gap-3">
            <button id="btnSeleccionarImagen" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                    <polyline points="17 8 12 3 7 8"/>
                    <line x1="12" y1="3" x2="12" y2="15"/>
                </svg>
                Seleccionar Imagen
            </button>
            <button id="btnGuardar" class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                <i class="fas fa-save"></i>
                Guardar Estado
            </button>
            <button id="btnCargar" class="flex items-center gap-2 px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                <i class="fas fa-upload"></i>
                Cargar Estado
            </button>
            <button id="btnLimpiar" class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                <i class="fas fa-trash-alt"></i>
                Limpiar Todo
            </button>
            

        </div>
    </div>

    <!-- Área del Plano -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Plano -->
        <div class="lg:col-span-3 bg-white rounded-lg shadow-md p-4">
            <div id="planoContainer" class="w-full h-[600px] bg-gray-100 rounded-lg flex items-center justify-center relative">
                <!-- Controles de zoom -->
                <div class="zoom-controls">
                    <button class="zoom-btn" id="zoomIn">+</button>
                    <button class="zoom-btn" id="zoomOut">-</button>
                    <button class="zoom-btn" id="zoomReset"><i class="fas fa-sync-alt"></i></button>
                </div>
                <img id="planoImage" class="max-h-full hidden" style="object-fit: contain;" />
                <p id="placeholderText" class="text-gray-500">Seleccione una imagen para comenzar</p>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="space-y-6">
            <!-- Herramientas -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="font-semibold mb-4">Herramientas</h3>
                <div class="space-y-2">
                    <!-- Dropdown Agregar Trampa -->
                    <div class="dropdown relative">
                        <button id="btnAgregarTrampa" class="w-full flex items-center justify-between px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 mt-2">
                            <span class="flex items-center">
                                <i class="fas fa-plus-circle mr-2"></i>
                                Agregar Trampa
                            </span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </button>
                        <div class="trap-menu hidden absolute w-full bg-white border rounded-lg shadow-lg" style="z-index: 1000;">
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="edc_quimicas">
                                <i class="fas fa-flask mr-2"></i> EDC Químicas
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="edc_adhesivas">
                                <i class="fas fa-flask mr-2 text-blue-600"></i> EDC Adhesivas
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="luz_uv">
                                <i class="fas fa-lightbulb mr-2"></i> Equipo de Luz UV
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="feromona_gorgojo">
                                <i class="fas fa-bug mr-2"></i> Trampa de Feromona Gorgojo
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="equipo_sonico">
                                <i class="fas fa-volume-up mr-2"></i> Equipo Sónico
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="globo_terror">
                                <i class="fas fa-circle mr-2"></i> Globo terror
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="atrayente_chinches">
                                <i class="fas fa-spider mr-2"></i> Trampa atrayente chinches
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="atrayente_pulgas">
                                <i class="fas fa-paw mr-2"></i> Trampa atrayente pulgas
                            </a>
                            <a href="#" class="dropdown-item block px-4 py-2 hover:bg-gray-100" data-trap-type="feromona_picudo">
                                <i class="fas fa-seedling mr-2"></i> Trampa feromonas picudo rojo
                            </a>
                        </div>
                    </div>

                    <!-- Botón Mover Trampas -->
                    <button id="btnMoverTrampa" class="w-full px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700">
                        <i class="fas fa-arrows-alt mr-2"></i>
                        Mover Trampas
                    </button>

                    <!-- Botón Agregar Zona -->
                    <button id="btnAgregarZona" class="w-full flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 mt-2">
                        <i class="fas fa-draw-polygon mr-2"></i>
                        Agregar Zona
                    </button>

                    <!-- Botón Ver Historial -->
                    <a href="<?= base_url('historial/index/' . $plano['id']) ?>" class="w-full flex items-center justify-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 mt-2">
                        <i class="fas fa-history mr-2"></i>
                        Ver Historial
                    </a>
                </div>
            </div>

            <!-- Lista de Trampas -->
            <div class="bg-white rounded-lg shadow-md p-4">
                <h3 class="font-semibold mb-4">Lista de Trampas</h3>
                <!-- Filtro por tipo de trampa -->
                <div class="mb-4">
                    <label for="filtroTipoTrampa" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por tipo:</label>
                    <select id="filtroTipoTrampa" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                        <option value="">Todos los tipos</option>
                        <option value="edc_quimicas">EDC Químicas</option>
                        <option value="edc_adhesivas">EDC Adhesivas</option>
                        <option value="luz_uv">Equipo de Luz UV</option>
                        <option value="feromona_gorgojo">Trampa de Feromona Gorgojo</option>
                        <option value="equipo_sonico">Equipo Sónico</option>
                        <option value="globo_terror">Globo terror</option>
                        <option value="atrayente_chinches">Trampa atrayente chinches</option>
                        <option value="atrayente_pulgas">Trampa atrayente pulgas</option>
                        <option value="feromona_picudo">Trampa feromonas picudo rojo</option>
                        <!-- Mantener compatibilidad con tipos antiguos -->
                        <option value="rodent">Trampa para Roedores</option>
                        <option value="insect">Trampa para Insectos</option>
                        <option value="fly">Trampa para Moscas</option>
                        <option value="moth">Trampa para Polillas</option>
                    </select>
                </div>
                <div class="overflow-y-auto max-h-[400px]">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left">ID</th>
                                <th class="px-4 py-2 text-left">Tipo</th>
                                <th class="px-4 py-2 text-left">Ubicación</th>
                                <th class="px-4 py-2 text-left">Zona</th>
                                <th class="px-4 py-2 text-right">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="trampasTableBody">
                            <tr>
                                <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                                    No hay trampas registradas
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Incidencias del Plano -->
    <div class="bg-white rounded-lg shadow-md p-6 mt-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold">Incidencias del Plano</h3>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-500">Total: <span id="totalIncidencias"><?= isset($incidencias) ? count($incidencias) : 0 ?></span></span>
                <div id="botonesModoEdicion" class="hidden flex items-center gap-2">
                    <button id="btnGuardarTodos" onclick="guardarTodosLosCambios()" 
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Guardar Todos los Cambios
                    </button>
                    <button id="btnDesactivarModoEdicion" onclick="confirmarDesactivarModoEdicion()" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Desactivar Modo Edición
                    </button>
                </div>
                <button id="btnModoEdicion" onclick="toggleModoEdicion()" 
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    <span id="textoModoEdicion">Activar Modo Edición</span>
                </button>
            </div>
        </div>
        
        <!-- Filtros de la tabla -->
        <?php if (isset($incidencias) && !empty($incidencias)): ?>
        <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="flex items-center gap-2 mb-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span class="text-sm font-semibold text-gray-700">Filtros</span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Filtro por ID de Trampa -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">ID de Trampa</label>
                    <input type="text" id="filtroIdTrampa" 
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                           placeholder="Buscar por ID..."
                           onkeyup="aplicarFiltros()">
                </div>
                
                <!-- Filtro por Tipo de Plaga -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipo de Plaga</label>
                    <select id="filtroTipoPlaga" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                            onchange="aplicarFiltros()">
                        <option value="">Todos</option>
                        <?php
                        // Obtener tipos de plaga únicos de las incidencias
                        $tiposPlaga = [];
                        foreach ($incidencias as $inc) {
                            if (!empty($inc['tipo_plaga']) && !in_array($inc['tipo_plaga'], $tiposPlaga)) {
                                $tiposPlaga[] = $inc['tipo_plaga'];
                            }
                        }
                        sort($tiposPlaga);
                        foreach ($tiposPlaga as $tipo) {
                            $tipoDisplay = ucwords(str_replace('_', ' ', $tipo));
                            echo '<option value="' . esc($tipo) . '">' . esc($tipoDisplay) . '</option>';
                        }
                        ?>
                    </select>
                </div>
                
                <!-- Filtro por Tipo de Incidencia -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipo de Incidencia</label>
                    <select id="filtroTipoIncidencia" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                            onchange="aplicarFiltros()">
                        <option value="">Todos</option>
                        <option value="Captura">Captura</option>
                        <option value="Hallazgo">Hallazgo</option>
                    </select>
                </div>
                
                <!-- Filtro por Tipo de Insecto -->
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Tipo de Insecto</label>
                    <select id="filtroTipoInsecto" 
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                            onchange="aplicarFiltros()">
                        <option value="">Todos</option>
                        <option value="Volador">Volador</option>
                        <option value="Rastrero">Rastrero</option>
                    </select>
                </div>
            </div>
            <div class="mt-3 flex items-center justify-between">
                <button onclick="limpiarFiltros()" 
                        class="text-sm text-gray-600 hover:text-gray-800 flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Limpiar filtros
                </button>
                <span class="text-xs text-gray-500">
                    Mostrando: <span id="incidenciasVisibles"><?= isset($incidencias) ? count($incidencias) : 0 ?></span> de <?= isset($incidencias) ? count($incidencias) : 0 ?>
                </span>
            </div>
        </div>
        <?php endif; ?>
        
        <?php if (isset($incidencias) && !empty($incidencias)): ?>
            <div class="tabla-incidencias-container">
                <table class="tabla-incidencias w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">ID Trampa</th>
                            <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Tipo de Plaga</th>
                            <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Tipo de Incidencia</th>
                            <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Tipo de Insecto</th>
                            <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Cantidad de Organismos</th>
                            <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Fecha de Incidencia</th>
                            <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Inspector</th>
                            <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap min-w-[200px]">Notas Adicionales</th>
                            <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200" id="tbodyIncidencias">
                        <?php foreach ($incidencias as $incidencia): ?>
                            <?php 
                            // Preparar datos para JavaScript
                            $fechaFormateada = '';
                            if (!empty($incidencia['fecha'])) {
                                $fecha = new \DateTime($incidencia['fecha']);
                                $fechaFormateada = $fecha->format('Y-m-d\TH:i');
                            }
                            $idTrampa = !empty($incidencia['id_trampa']) ? $incidencia['id_trampa'] : ($incidencia['id_trampa'] ?? 'N/A');
                            ?>
                            <tr class="hover:bg-gray-50 transition-colors" data-incidencia-id="<?= $incidencia['id'] ?>" data-original-data='<?= json_encode([
                                'tipo_plaga' => $incidencia['tipo_plaga'] ?? '',
                                'tipo_incidencia' => $incidencia['tipo_incidencia'] ?? 'Captura',
                                'tipo_insecto' => $incidencia['tipo_insecto'] ?? 'Volador',
                                'cantidad_organismos' => $incidencia['cantidad_organismos'] ?? '',
                                'fecha' => $fechaFormateada,
                                'inspector' => $incidencia['inspector'] ?? '',
                                'notas' => $incidencia['notas'] ?? ''
                            ]) ?>'>
                                <!-- ID Trampa (solo lectura) -->
                                <td class="px-4 py-3 border-b border-gray-100 whitespace-nowrap">
                                    <span class="font-medium text-gray-900"><?= esc($idTrampa) ?></span>
                                </td>
                                
                                <!-- Tipo de Plaga (editable) -->
                                <td class="px-4 py-3 border-b border-gray-100 text-gray-700 whitespace-nowrap" data-tipo-plaga="<?= esc($incidencia['tipo_plaga'] ?? '') ?>">
                                    <span class="display-mode"><?php 
                                        $tipoPlagaDisplay = $incidencia['tipo_plaga'] ?? 'N/A';
                                        if ($tipoPlagaDisplay !== 'N/A') {
                                            // Formatear para mostrar: mayúscula inicial y reemplazar guiones bajos con espacios
                                            $tipoPlagaDisplay = ucwords(str_replace('_', ' ', $tipoPlagaDisplay));
                                        }
                                        echo esc($tipoPlagaDisplay);
                                    ?></span>
                                    <select class="edit-mode hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                            data-field="tipo_plaga">
                                        <option value="">Seleccione un tipo</option>
                                        <option value="mosca" <?= ($incidencia['tipo_plaga'] ?? '') === 'mosca' ? 'selected' : '' ?>>Mosca</option>
                                        <option value="mosca_domestica" <?= ($incidencia['tipo_plaga'] ?? '') === 'mosca_domestica' ? 'selected' : '' ?>>Mosca Doméstica</option>
                                        <option value="mosca_fruta" <?= ($incidencia['tipo_plaga'] ?? '') === 'mosca_fruta' ? 'selected' : '' ?>>Mosca De La Fruta</option>
                                        <option value="mosca_drenaje" <?= ($incidencia['tipo_plaga'] ?? '') === 'mosca_drenaje' ? 'selected' : '' ?>>Mosca De Drenaje</option>
                                        <option value="mosca_metalica" <?= ($incidencia['tipo_plaga'] ?? '') === 'mosca_metalica' ? 'selected' : '' ?>>Moscas Metálicas</option>
                                        <option value="mosca_forida" <?= ($incidencia['tipo_plaga'] ?? '') === 'mosca_forida' ? 'selected' : '' ?>>Mosca Forida</option>
                                        <option value="palomilla_almacen" <?= ($incidencia['tipo_plaga'] ?? '') === 'palomilla_almacen' ? 'selected' : '' ?>>Palomillas De Almacén</option>
                                        <option value="otras_palomillas" <?= ($incidencia['tipo_plaga'] ?? '') === 'otras_palomillas' ? 'selected' : '' ?>>Otras Palomillas</option>
                                        <option value="gorgojo" <?= ($incidencia['tipo_plaga'] ?? '') === 'gorgojo' ? 'selected' : '' ?>>Gorgojos</option>
                                        <option value="otros_escarabajos" <?= ($incidencia['tipo_plaga'] ?? '') === 'otros_escarabajos' ? 'selected' : '' ?>>Otros Escarabajos</option>
                                        <option value="abeja" <?= ($incidencia['tipo_plaga'] ?? '') === 'abeja' ? 'selected' : '' ?>>Abejas</option>
                                        <option value="avispa" <?= ($incidencia['tipo_plaga'] ?? '') === 'avispa' ? 'selected' : '' ?>>Avispas</option>
                                        <option value="mosquito" <?= ($incidencia['tipo_plaga'] ?? '') === 'mosquito' ? 'selected' : '' ?>>Mosquitos</option>
                                        <option value="cucaracha" <?= ($incidencia['tipo_plaga'] ?? '') === 'cucaracha' ? 'selected' : '' ?>>Cucaracha</option>
                                        <option value="hormiga" <?= ($incidencia['tipo_plaga'] ?? '') === 'hormiga' ? 'selected' : '' ?>>Hormiga</option>
                                        <option value="roedor" <?= ($incidencia['tipo_plaga'] ?? '') === 'roedor' ? 'selected' : '' ?>>Roedor</option>
                                        <option value="Arañas" <?= ($incidencia['tipo_plaga'] ?? '') === 'Arañas' ? 'selected' : '' ?>>Arañas</option>
                                        <option value="Lagartija" <?= ($incidencia['tipo_plaga'] ?? '') === 'Lagartija' ? 'selected' : '' ?>>Lagartijas</option>
                                        <option value="otro" <?= !in_array($incidencia['tipo_plaga'] ?? '', ['mosca', 'mosca_domestica', 'mosca_fruta', 'mosca_drenaje', 'mosca_metalica', 'mosca_forida', 'palomilla_almacen', 'otras_palomillas', 'gorgojo', 'otros_escarabajos', 'abeja', 'avispa', 'mosquito', 'cucaracha', 'hormiga', 'roedor', 'Arañas', 'Lagartija']) && !empty($incidencia['tipo_plaga']) ? 'selected' : '' ?>>Otro (especificar)</option>
                                    </select>
                                    <div class="edit-mode hidden w-full">
                                        <input type="text" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm tipo-plaga-personalizado" 
                                               data-field="tipo_plaga_personalizado" 
                                               placeholder="Especifique el tipo de plaga"
                                               value="<?= !in_array($incidencia['tipo_plaga'] ?? '', ['mosca', 'mosca_domestica', 'mosca_fruta', 'mosca_drenaje', 'mosca_metalica', 'mosca_forida', 'palomilla_almacen', 'otras_palomillas', 'gorgojo', 'otros_escarabajos', 'abeja', 'avispa', 'mosquito', 'cucaracha', 'hormiga', 'roedor', 'Arañas', 'Lagartija']) && !empty($incidencia['tipo_plaga']) ? esc($incidencia['tipo_plaga']) : '' ?>"
                                               style="display: none;">
                                    </div>
                                </td>
                                
                                <!-- Tipo de Incidencia (editable) -->
                                <td class="px-4 py-3 border-b border-gray-100 whitespace-nowrap" data-tipo-incidencia="<?= esc($incidencia['tipo_incidencia'] ?? '') ?>">
                                    <span class="display-mode inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= ($incidencia['tipo_incidencia'] ?? '') === 'Captura' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' ?>">
                                        <?= esc($incidencia['tipo_incidencia'] ?? 'N/A') ?>
                                    </span>
                                    <select class="edit-mode hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                            data-field="tipo_incidencia">
                                        <option value="Captura" <?= ($incidencia['tipo_incidencia'] ?? '') === 'Captura' ? 'selected' : '' ?>>Captura</option>
                                        <option value="Hallazgo" <?= ($incidencia['tipo_incidencia'] ?? '') === 'Hallazgo' ? 'selected' : '' ?>>Hallazgo</option>
                                    </select>
                                </td>
                                
                                <!-- Tipo de Insecto (editable) -->
                                <td class="px-4 py-3 border-b border-gray-100 text-gray-700 whitespace-nowrap" data-tipo-insecto="<?= esc($incidencia['tipo_insecto'] ?? '') ?>">
                                    <span class="display-mode"><?= esc($incidencia['tipo_insecto'] ?? 'N/A') ?></span>
                                    <select class="edit-mode hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                            data-field="tipo_insecto">
                                        <option value="Volador" <?= ($incidencia['tipo_insecto'] ?? '') === 'Volador' ? 'selected' : '' ?>>Volador</option>
                                        <option value="Rastrero" <?= ($incidencia['tipo_insecto'] ?? '') === 'Rastrero' ? 'selected' : '' ?>>Rastrero</option>
                                    </select>
                                </td>
                                
                                <!-- Cantidad de Organismos (editable) -->
                                <td class="px-4 py-3 border-b border-gray-100 text-gray-700 whitespace-nowrap text-center">
                                    <span class="display-mode">
                                        <?php 
                                        $cantidad = $incidencia['cantidad_organismos'] ?? null;
                                        if ($cantidad !== null && $cantidad !== '') {
                                            echo '<span class="font-semibold text-blue-600">' . esc($cantidad) . '</span>';
                                        } else {
                                            echo '<span class="text-gray-400">-</span>';
                                        }
                                        ?>
                                    </span>
                                    <input type="number" class="edit-mode hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm text-center" 
                                           data-field="cantidad_organismos" 
                                           min="1" 
                                           value="<?= esc($incidencia['cantidad_organismos'] ?? '') ?>">
                                </td>
                                
                                <!-- Fecha de Incidencia (editable) -->
                                <td class="px-4 py-3 border-b border-gray-100 text-gray-700 whitespace-nowrap">
                                    <span class="display-mode">
                                        <?php 
                                        if (!empty($incidencia['fecha'])) {
                                            $fecha = new \DateTime($incidencia['fecha']);
                                            // Formatear fecha con nombre del mes en español
                                            $meses = [
                                                1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
                                                5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
                                                9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
                                            ];
                                            $dia = $fecha->format('j'); // Día sin cero inicial
                                            $mes = (int)$fecha->format('n'); // Mes numérico sin cero inicial
                                            $anio = $fecha->format('Y');
                                            $hora = $fecha->format('H:i');
                                            echo $dia . ' de ' . ucfirst($meses[$mes]) . ' de ' . $anio . ', ' . $hora;
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </span>
                                    <input type="datetime-local" class="edit-mode hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                           data-field="fecha_incidencia" 
                                           value="<?= esc($fechaFormateada) ?>">
                                </td>
                                
                                <!-- Inspector (editable) -->
                                <td class="px-4 py-3 border-b border-gray-100 text-gray-700 whitespace-nowrap">
                                    <span class="display-mode"><?= esc($incidencia['inspector'] ?? 'N/A') ?></span>
                                    <input type="text" class="edit-mode hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                           data-field="inspector" 
                                           placeholder="Nombre del inspector"
                                           value="<?= esc($incidencia['inspector'] ?? '') ?>">
                                </td>
                                
                                <!-- Notas Adicionales (editable) -->
                                <td class="px-4 py-3 border-b border-gray-100 text-gray-700">
                                    <div class="display-mode max-w-xs" title="<?= esc($incidencia['notas'] ?? '') ?>">
                                        <?= esc($incidencia['notas'] ?? 'Sin notas') ?>
                                    </div>
                                    <textarea class="edit-mode hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                              data-field="notas" 
                                              rows="2"
                                              placeholder="Notas adicionales"><?= esc($incidencia['notas'] ?? '') ?></textarea>
                                </td>
                                
                                <!-- Acciones (solo mostrar en modo visualización) -->
                                <td class="px-4 py-3 border-b border-gray-100 whitespace-nowrap">
                                    <div class="display-mode">
                                        <button onclick="editarIncidencia(<?= $incidencia['id'] ?>)" 
                                                class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center gap-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Editar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-8 text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mt-2">No hay incidencias registradas para este plano.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Agregar los inputs ocultos -->
<input type="file" id="planoInput" class="form-control" accept="image/*" style="display: none;">
<input type="file" id="cargarEstadoInput" accept="application/json" style="display: none;">

<!-- Modal para Agregar Incidencia -->
<div id="modalIncidencia" class="fixed inset-0 bg-black bg-opacity-50 hidden" style="z-index: 10000;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-6xl max-h-[90vh] flex flex-col overflow-hidden" style="width: 90%; max-width: 1152px;">
            <div class="overflow-y-auto overflow-x-hidden" style="max-height: calc(90vh - 0px);">
                <div class="p-6">
                <h3 class="text-lg font-semibold mb-2">Registrar Incidencia</h3>
                <div id="trampaInfo" class="mb-4 p-3 bg-gray-100 rounded-md">
                    <p class="text-sm"><strong>ID de Trampa:</strong> <span id="trampaDbIdDisplay">-</span></p>
                    <p class="text-sm"><strong>Ubicación:</strong> <span id="trampaZonaDisplay">-</span></p>
                </div>
                <form id="formIncidencia">
                    <input type="hidden" name="trampa_id" id="trampa_id">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Plaga</label>
                            <select name="tipo_plaga_select" id="tipo_plaga_select"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione un tipo</option>
                                <option value="mosca">Mosca</option>
                                <option value="mosca_domestica">Mosca doméstica</option>
                                <option value="mosca_fruta">Mosca de la fruta</option>
                                <option value="mosca_drenaje">Mosca de drenaje</option>
                                <option value="mosca_metalica">Moscas metálicas</option>
                                <option value="mosca_forida">Mosca forida</option>
                                <option value="palomilla_almacen">Palomillas de almacén</option>
                                <option value="otras_palomillas">Otras palomillas</option>
                                <option value="gorgojo">Gorgojos</option>
                                <option value="otros_escarabajos">Otros escarabajos</option>
                                <option value="abeja">Abejas</option>
                                <option value="avispa">Avispas</option>
                                <option value="mosquito">Mosquitos</option>
                                <option value="cucaracha">Cucaracha</option>
                                <option value="hormiga">Hormiga</option>
                                <option value="roedor">Roedor</option>
                                <option value="Arañas">Arañas</option>
                                <option value="Lagartija">Lagartijas</option>
                                <option value="otro">Otro (especificar)</option>
                            </select>
                        </div>
                        <div id="tipo_plaga_personalizado_container" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700">Especifique el tipo de plaga</label>
                            <input type="text" name="tipo_plaga_personalizado" id="tipo_plaga_personalizado" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ingrese el tipo de plaga">
                        </div>
                        <input type="hidden" name="tipo_plaga" id="tipo_plaga" required>
                        <div id="cantidad_organismos_container">
                            <label class="block text-sm font-medium text-gray-700">Cantidad de Organismos</label>
                            <input type="number" name="cantidad_organismos" id="cantidad_organismos" min="1" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ingrese la cantidad">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Incidencia</label>
                            <select name="tipo_incidencia" id="tipo_incidencia"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Captura">Captura</option>
                                <option value="Hallazgo">Hallazgo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Insecto</label>
                            <select name="tipo_insecto" id="tipo_insecto"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Volador">Volador</option>
                                <option value="Rastrero">Rastrero</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de Incidencia</label>
                            <input type="datetime-local" name="fecha_incidencia" id="fecha_incidencia" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Inspector</label>
                            <input type="text" name="inspector" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Nombre del inspector">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Notas Adicionales</label>
                            <textarea name="notas" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeIncidenciaModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Cancelar
                        </button>
                        <button type="button" id="btnAgregarIncidenciaLista"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md">
                            Agregar a lista
                        </button>
                        <button type="button" id="btnGuardarTodasIncidencias"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            Guardar todas
                        </button>
                    </div>
                </form>
            </div>
            <!-- Tabla de incidencias agregadas -->
            <div id="listaIncidenciasModal" class="px-6 pb-6">
                <h4 class="font-semibold mb-3 text-lg">Incidencias agregadas: <span id="contadorIncidencias">0</span></h4>
                <div class="border border-gray-200 rounded-lg overflow-x-auto">
                    <table id="tablaIncidenciasAgregadas" class="w-full text-sm" style="min-width: 1000px;">
                        <thead class="bg-gray-50 sticky top-0">
                            <tr>
                                <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Tipo de Plaga</th>
                                <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Tipo de Incidencia</th>
                                <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Tipo de Insecto</th>
                                <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Cantidad de Organismos</th>
                                <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Fecha de Incidencia</th>
                                <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Inspector</th>
                                <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap min-w-[200px]">Notas Adicionales</th>
                                <th class="px-4 py-3 text-left border-b border-gray-200 font-semibold text-gray-700 whitespace-nowrap">Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="incidenciasAgregadas" class="bg-white divide-y divide-gray-200">
                            <tr id="filaVacia">
                                <td colspan="8" class="px-4 py-8 text-center text-gray-400">
                                    No hay incidencias agregadas.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar Incidencia -->
<div id="modalEditarIncidencia" class="fixed inset-0 bg-black bg-opacity-50 hidden" style="z-index: 10000;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-2">Editar Incidencia</h3>
                <div id="trampaInfoEditar" class="mb-4 p-3 bg-gray-100 rounded-md">
                    <p class="text-sm text-gray-600"></p>
                </div>
                <form id="formEditarIncidencia">
                    <input type="hidden" name="incidencia_id" id="incidencia_id_editar">
                    <input type="hidden" name="trampa_id_editar" id="trampa_id_editar">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Plaga</label>
                            <select name="tipo_plaga_select_editar" id="tipo_plaga_select_editar"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccione un tipo</option>
                                <option value="mosca">Mosca</option>
                                <option value="mosca_domestica">Mosca doméstica</option>
                                <option value="mosca_fruta">Mosca de la fruta</option>
                                <option value="mosca_drenaje">Mosca de drenaje</option>
                                <option value="mosca_metalica">Moscas metálicas</option>
                                <option value="mosca_forida">Mosca forida</option>
                                <option value="palomilla_almacen">Palomillas de almacén</option>
                                <option value="otras_palomillas">Otras palomillas</option>
                                <option value="gorgojo">Gorgojos</option>
                                <option value="otros_escarabajos">Otros escarabajos</option>
                                <option value="abeja">Abejas</option>
                                <option value="avispa">Avispas</option>
                                <option value="mosquito">Mosquitos</option>
                                <option value="cucaracha">Cucaracha</option>
                                <option value="hormiga">Hormiga</option>
                                <option value="roedor">Roedor</option>
                                <option value="Arañas">Arañas</option>
                                <option value="Lagartija">Lagartijas</option>
                                <option value="otro">Otro (especificar)</option>
                            </select>
                        </div>
                        <div id="tipo_plaga_personalizado_container_editar" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700">Especifique el tipo de plaga</label>
                            <input type="text" name="tipo_plaga_personalizado_editar" id="tipo_plaga_personalizado_editar" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ingrese el tipo de plaga">
                        </div>
                        <input type="hidden" name="tipo_plaga_editar" id="tipo_plaga_editar" required>
                        <div id="cantidad_organismos_container_editar" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700">Cantidad de Organismos</label>
                            <input type="number" name="cantidad_organismos_editar" id="cantidad_organismos_editar" min="1" 
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ingrese la cantidad">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Incidencia</label>
                            <select name="tipo_incidencia_editar" id="tipo_incidencia_editar"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Captura">Captura</option>
                                <option value="Hallazgo">Hallazgo</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tipo de Insecto</label>
                            <select name="tipo_insecto_editar" id="tipo_insecto_editar"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="Volador">Volador</option>
                                <option value="Rastrero">Rastrero</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de Incidencia</label>
                            <input type="datetime-local" name="fecha_incidencia_editar" id="fecha_incidencia_editar" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Inspector</label>
                            <input type="text" name="inspector_editar" id="inspector_editar"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Nombre del inspector">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Notas Adicionales</label>
                            <textarea name="notas_editar" id="notas_editar" rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" onclick="closeEditarIncidenciaModal()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Cancelar
                        </button>
                        <button type="button" id="btnGuardarIncidenciaEditar"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Desactivar Modo Edición -->
<div id="modalConfirmarDesactivar" class="fixed inset-0 bg-black bg-opacity-50 hidden" style="z-index: 10000;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4 text-gray-900">¿Desactivar Modo Edición?</h3>
                <p class="text-gray-600 mb-6">
                    Si desactivas el modo edición sin guardar, se perderán todos los cambios realizados.
                    ¿Estás seguro de que deseas continuar?
                </p>
                <div class="flex justify-end space-x-3">
                    <button onclick="cerrarModalConfirmarDesactivar()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                        Cancelar
                    </button>
                    <button onclick="desactivarModoEdicionConfirmado()" 
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-md">
                        Desactivar sin Guardar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar ID de Trampa -->
<div id="modalEditarId" class="fixed inset-0 bg-black bg-opacity-50 hidden" style="z-index: 10000;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Editar ID de Trampa</h3>
                    <button type="button" onclick="cerrarModalEditarId()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nuevo ID de Trampa:</label>
                    <input type="text" id="nuevoIdTrampa" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Ingrese el nuevo ID de la trampa">
                    <p class="mt-1 text-xs text-gray-500">Este ID se usará para identificar la trampa en la incidencia</p>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="cerrarModalEditarId()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md transition-colors">
                        Cancelar
                    </button>
                    <button type="button" onclick="guardarNuevoId()"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition-colors">
                        Guardar ID
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para ingresar la zona de la trampa -->
<div id="modalZonaTrampa" class="fixed inset-0 bg-black bg-opacity-50 hidden" style="z-index: 10000;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="p-6">
                <h3 class="text-lg font-semibold mb-4">Ingresar zona de ubicación</h3>
                <form id="formZonaTrampa">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">ID de trampa:</label>
                            <input type="text" id="idTrampaInput" name="idTrampa" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ingrese el ID de trampa (opcional)">
                            <p class="text-xs text-gray-500 mt-1">Si no se ingresa, se generará automáticamente</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Seleccione una zona existente:</label>
                            <select id="zonasExistentesSelect" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 mb-3">
                                <option value="">-- Seleccionar zona existente --</option>
                                <!-- Las opciones se cargarán dinámicamente -->
                            </select>
                        </div>
                        <div class="flex items-center py-2">
                            <div class="flex-grow border-t border-gray-300"></div>
                            <span class="mx-4 text-sm text-gray-500">O</span>
                            <div class="flex-grow border-t border-gray-300"></div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ingrese una nueva zona:</label>
                            <input type="text" id="zonaTrampaInput" name="zonaTrampa" 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ingrese la zona">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" id="cancelarZonaTrampa"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                            Aceptar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="<?= base_url('js/mapa.js') ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Referencias a elementos
        const planoContainer = document.getElementById('planoContainer');
        const planoImage = document.getElementById('planoImage');
        const planoInput = document.getElementById('planoInput');
        const btnSeleccionarImagen = document.getElementById('btnSeleccionarImagen');
        const btnGuardar = document.getElementById('btnGuardar');
        const btnCargar = document.getElementById('btnCargar');
        const btnLimpiar = document.getElementById('btnLimpiar');
        const btnMoverTrampa = document.getElementById('btnMoverTrampa');
        const btnAgregarZona = document.getElementById('btnAgregarZona');
        const btnAgregarTrampa = document.getElementById('btnAgregarTrampa');
        const dropdownTrampa = document.querySelector('.dropdown');
        const cargarEstadoInput = document.getElementById('cargarEstadoInput');

        // Variables para el manejo de trampas
        let modoEdicion = null;
        let tipoTrampaSeleccionado = null;
        let contadorTrampas = 1;
        let trampaSeleccionada = null;
        let offsetX = 0;
        let offsetY = 0;
        
        // Variables para el modal de zona de trampa
        let posicionTrampaX = 0;
        let posicionTrampaY = 0;

        // Agregar estas variables al inicio del script
        let dibujandoPoligono = false;
        let puntosPoligono = [];
        let poligonoTemporal = null;
        let tooltipElement = null;
        let confirmButton = null;

        // Función para ajustar la imagen al contenedor
        function adjustImageToContainer() {
            const planoContainer = document.getElementById('planoContainer');
            const planoImage = document.getElementById('planoImage');
            
            if (!planoImage.complete || !planoImage.naturalWidth) {
                // La imagen aún no se ha cargado completamente
                return;
            }
            
            // Ajustar el scrollTop y scrollLeft para centrar la imagen en el viewport
            setTimeout(() => {
                planoContainer.scrollTop = (planoImage.offsetHeight - planoContainer.clientHeight) / 2;
                planoContainer.scrollLeft = (planoImage.offsetWidth - planoContainer.clientWidth) / 2;
                
                // Después de ajustar la imagen, reposicionar las trampas
                reposicionarTrampas();
            }, 100);
        }

        // Evento para ajustar la imagen cuando cambia el tamaño de la ventana
        window.addEventListener('resize', adjustImageToContainer);

        // Crear el tooltip
        const tooltip = document.createElement('div');
        tooltip.className = 'trap-tooltip';
        tooltip.innerHTML = `
            <button type="button" class="add-incidence-btn">
                <i class="fas fa-plus-circle mr-2"></i>
                Agregar incidencia
            </button>
        `;
        document.body.appendChild(tooltip);

        // Variables para el manejo del tooltip y modal de incidencias
        let activeMarker = null;

        // Reposicionar las trampas después de que la página se haya cargado completamente
        setTimeout(reposicionarTrampas, 500);

        // Función para desactivar todos los botones excepto Seleccionar Imagen y Cargar
        function desactivarBotones() {
            btnGuardar.disabled = true;
            btnLimpiar.disabled = true;
            btnMoverTrampa.disabled = true;
            btnAgregarZona.disabled = true;
            btnAgregarTrampa.disabled = true;
            
            // Agregar clase para estilo desactivado
            [btnGuardar, btnLimpiar, btnMoverTrampa, btnAgregarZona, btnAgregarTrampa].forEach(btn => {
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            });
        }

        // Función para activar todos los botones
        function activarBotones() {
            btnGuardar.disabled = false;
            btnLimpiar.disabled = false;
            btnMoverTrampa.disabled = false;
            btnAgregarZona.disabled = false;
            btnAgregarTrampa.disabled = false;
            
            // Remover clase para estilo desactivado
            [btnGuardar, btnLimpiar, btnMoverTrampa, btnAgregarZona, btnAgregarTrampa].forEach(btn => {
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
            });
        }

        // Desactivar botones al inicio
        desactivarBotones();

        // CARGAR TRAMPAS DESDE LA BASE DE DATOS (fuente de verdad)
        <?php if (!empty($trampas)): ?>
        const trampasDesdeDB = <?= json_encode($trampas) ?>;
        <?php else: ?>
        const trampasDesdeDB = [];
        <?php endif; ?>

        // Cargar estado guardado en la base de datos si existe
        <?php if (!empty($plano['archivo'])): ?>
        try {
            const estadoGuardado = JSON.parse('<?= addslashes($plano['archivo']) ?>');
            if (estadoGuardado && estadoGuardado.imagen) {
                // Cargar la imagen
                planoImage.src = estadoGuardado.imagen;
                planoImage.style.display = 'block';
                document.getElementById('placeholderText').style.display = 'none';
                
                // Ajustar la imagen cuando se cargue
                planoImage.onload = function() {
                    adjustImageToContainer();
                    
                    // PRIORIDAD: Cargar trampas desde la BD si existen, sino desde el JSON
                    const trampasParaCargar = trampasDesdeDB.length > 0 ? trampasDesdeDB.map(t => ({
                        id: t.id_trampa || t.id,
                        id_trampa: t.id_trampa || t.id, // Asegurar que siempre haya un id_trampa
                        nombre: t.nombre || t.id_trampa || t.id, // Usar nombre o id_trampa como fallback
                        tipo: t.tipo,
                        x: parseFloat(t.coordenada_x),
                        y: parseFloat(t.coordenada_y),
                        zona: t.ubicacion,
                        ubicacion: t.ubicacion
                    })) : (estadoGuardado.trampas || []);
                    
                    if (trampasParaCargar.length > 0) {
                        window.puntos = trampasParaCargar;
                        
                        // Limpiar marcadores existentes para evitar duplicados
                        document.querySelectorAll('.trap-marker').forEach(marker => marker.remove());
                        
                        // Colocar las trampas
                        window.puntos.forEach(punto => {
                            marcarTrampa(punto);
                        });
                        
                        // Actualizar la tabla
                        actualizarTablaTrampas();
                        
                        // Asegurar que todos los marcadores tengan el evento de clic
                        document.querySelectorAll('.trap-marker').forEach(marker => {
                            const newMarker = addTrapClickEvent(marker);
                            if (marker !== newMarker && marker.parentNode) {
                                marker.parentNode.replaceChild(newMarker, marker);
                            }
                        });
                        
                        // Reposicionar las trampas para asegurar que estén en la posición correcta
                        setTimeout(reposicionarTrampas, 100);
                    }
                    
                    // Guardar las zonas en una variable global
                    if (estadoGuardado.zonas && Array.isArray(estadoGuardado.zonas)) {
                        window.zonas = estadoGuardado.zonas;
                        
                        // Limpiar zonas existentes para evitar duplicados
                        document.querySelectorAll('.zona, .zona-poligono, .zona-texto').forEach(zona => zona.remove());
                        
                        // Colocar las zonas
                        window.zonas.forEach(zona => {
                            crearZonaExistente(zona);
                        });
                    }
                };
                
                // Activar botones
                activarBotones();
            }
        } catch (error) {
            console.error('Error al cargar el estado guardado:', error);
        }
        <?php endif; ?>

        // Botón Seleccionar Imagen
        btnSeleccionarImagen.addEventListener('click', () => {
            planoInput.click();
        });

        planoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    planoImage.src = e.target.result;
                    planoImage.style.display = 'block';
                    document.getElementById('placeholderText').style.display = 'none';
                    
                    // Ajustar la imagen cuando se cargue
                    planoImage.onload = function() {
                        adjustImageToContainer();
                    };
                    
                    // Activar botones cuando se carga la imagen
                    activarBotones();
                    
                    // Guardar el estado automáticamente cuando se carga una nueva imagen
                    // Esto asegura que la imagen se guarde en el servidor inmediatamente
                    guardarEstadoPlano(false);
                };
                reader.readAsDataURL(file);
            }
        });

        // Botón Guardar Estado
        btnGuardar.addEventListener('click', function() {
            guardarEstadoPlano(true);
        });

        // Función para mostrar mensajes
        function mostrarMensaje(mensaje, tipo) {
            // Crear elemento de alerta
            const alerta = document.createElement('div');
            alerta.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg ${tipo === 'success' ? 'bg-green-500' : tipo === 'error' ? 'bg-red-500' : 'bg-blue-500'} text-white`;
            alerta.style.zIndex = '10001'; // Por encima del modal (z-index 10000)
            alerta.textContent = mensaje;
            
            // Agregar al DOM
            document.body.appendChild(alerta);
            
            // Eliminar después de 3 segundos
            setTimeout(() => {
                alerta.style.opacity = '0';
                alerta.style.transition = 'opacity 0.5s';
                setTimeout(() => alerta.remove(), 500);
            }, 3000);
        }

        // Función para crear una zona existente desde datos guardados
        function crearZonaExistente(zonaData) {
            const planoContainer = document.getElementById('planoContainer');
            const planoImage = document.getElementById('planoImage');
            
            // Obtener las dimensiones actuales
            const imagenRect = planoImage.getBoundingClientRect();
            const containerRect = planoContainer.getBoundingClientRect();
            
            // Calcular la posición relativa al contenedor
            const imagenLeft = imagenRect.left - containerRect.left;
            const imagenTop = imagenRect.top - containerRect.top;
            
            // Manejar zonas de tipo polígono
            if (zonaData.tipo === 'poligono' && zonaData.puntos && Array.isArray(zonaData.puntos)) {
                // Crear el elemento del polígono
                const poligono = document.createElement('div');
                poligono.className = 'zona-poligono';
                
                // Calcular el path del polígono - las coordenadas ya están relativas a la imagen
                // Necesitamos convertirlas a coordenadas absolutas para visualización
                const path = zonaData.puntos.map(p => `${p.x + imagenLeft}px ${p.y + imagenTop}px`).join(',');
                poligono.style.clipPath = `polygon(${path})`;
                
                // Crear el contenedor del texto
                const textoZona = document.createElement('div');
                textoZona.className = 'zona-texto';
                textoZona.textContent = zonaData.nombre || 'Sin nombre';
                
                // Posicionar el texto en el centro de la zona
                if (zonaData.centro) {
                    // Las coordenadas del centro ya están relativas a la imagen
                    // Necesitamos convertirlas a coordenadas absolutas para visualización
                    textoZona.style.left = `${imagenLeft + zonaData.centro.x}px`;
                    textoZona.style.top = `${imagenTop + zonaData.centro.y}px`;
                } else {
                    // Calcular el centro si no está definido
                    const centroX = zonaData.puntos.reduce((sum, p) => sum + p.x, 0) / zonaData.puntos.length;
                    const centroY = zonaData.puntos.reduce((sum, p) => sum + p.y, 0) / zonaData.puntos.length;
                    textoZona.style.left = `${imagenLeft + centroX}px`;
                    textoZona.style.top = `${imagenTop + centroY}px`;
                }
                
                // Agregar ID al elemento del DOM
                poligono.dataset.zonaId = zonaData.id || `Z${window.zonas.indexOf(zonaData) + 1}`;
                poligono.dataset.index = window.zonas.indexOf(zonaData);
                
                // Agregar manejador para eliminar con clic derecho
                poligono.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    if (confirm('¿Desea eliminar esta zona?')) {
                        const index = parseInt(this.dataset.index);
                        window.zonas.splice(index, 1);
                        this.remove();
                        textoZona.remove();
                        reindexarZonas();
                    }
                });
                
                // Agregar al contenedor
                planoContainer.appendChild(poligono);
                planoContainer.appendChild(textoZona);
                
                return poligono;
            }
            
            // Código existente para zonas rectangulares o circulares
            const zonaElement = document.createElement('div');
            zonaElement.className = 'zona';
            if (zonaData.tipo === 'circulo') {
                zonaElement.classList.add('zona-circulo');
            }
            
            // Posicionar la zona - las coordenadas ya están relativas a la imagen
            zonaElement.style.left = `${imagenLeft + zonaData.x}px`;
            zonaElement.style.top = `${imagenTop + zonaData.y}px`;
            zonaElement.style.width = `${zonaData.width}px`;
            zonaElement.style.height = `${zonaData.height}px`;
            zonaElement.dataset.index = window.zonas.indexOf(zonaData);
            
            // Agregar nombre si existe
            if (zonaData.nombre) {
                const nombreElement = document.createElement('div');
                nombreElement.className = 'absolute top-0 left-0 bg-white px-2 py-1 text-sm font-semibold';
                nombreElement.textContent = zonaData.nombre;
                zonaElement.appendChild(nombreElement);
            }
            
            // Agregar manejador para eliminar con clic derecho
            zonaElement.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                if (confirm('¿Desea eliminar esta zona?')) {
                    const index = parseInt(this.dataset.index);
                    window.zonas.splice(index, 1);
                    this.remove();
                    reindexarZonas();
                }
            });
            
            // Agregar al contenedor
            planoContainer.appendChild(zonaElement);
            
            // Agregar controlador de redimensionamiento
            const resizeHandle = document.createElement('div');
            resizeHandle.className = 'resize-handle';
            resizeHandle.style.bottom = '0';
            resizeHandle.style.right = '0';
            zonaElement.appendChild(resizeHandle);
            
            return zonaElement;
        }

        // Función para reindexar las zonas
        function reindexarZonas() {
            // Reindexar zonas rectangulares y circulares
            const zonaElements = document.querySelectorAll('.zona');
            zonaElements.forEach((zona, index) => {
                zona.dataset.index = index;
            });
            
            // Reindexar zonas de tipo polígono
            const poligonoElements = document.querySelectorAll('.zona-poligono');
            poligonoElements.forEach((poligono, index) => {
                const zonaIndex = window.zonas.findIndex(z => z.id === poligono.dataset.zonaId);
                if (zonaIndex !== -1) {
                    poligono.dataset.index = zonaIndex;
                }
            });
        }

        // Función para limpiar completamente el estado
        function limpiarEstadoCompleto() {
            // Limpiar imagen
            if (planoImage) {
                // Eliminar el evento onload para evitar ejecuciones inesperadas
                planoImage.onload = null;
                planoImage.src = '';
                planoImage.style.display = 'none';
            }

            const placeholderText = document.getElementById('placeholderText');
            if (placeholderText) {
                placeholderText.style.display = 'block';
            }
            
            // Limpiar inputs
            if (planoInput) planoInput.value = '';
            if (cargarEstadoInput) cargarEstadoInput.value = '';
            
            // Limpiar arrays
            window.puntos = [];
            window.zonas = [];
            
            // Limpiar marcadores y zonas del DOM
            if (planoContainer) {
                const marcasExistentes = planoContainer.querySelectorAll('.trap-marker, .zona, .zona-poligono, .zona-texto, .punto-poligono');
                marcasExistentes.forEach(marca => marca.remove());
            }
            
            // Limpiar tabla
            if (typeof actualizarTablaTrampas === 'function') {
                actualizarTablaTrampas();
            }
            
            // Resetear modos de edición
            if (planoContainer) {
                planoContainer.dataset.modoEdicion = '';
                planoContainer.dataset.tipoTrampa = '';
            }
            
            // Resetear clases activas de botones
            if (btnMoverTrampa) {
                btnMoverTrampa.classList.remove('active', 'bg-green-600', 'hover:bg-green-700');
                btnMoverTrampa.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
                btnMoverTrampa.innerHTML = `
                    <i class="fas fa-arrows-alt mr-2"></i>
                    Mover Trampas
                `;
            }

            // Verificar que el dropdown y su menú existan antes de manipularlos
            const trapMenu = dropdownTrampa?.querySelector('.trap-menu');
            if (trapMenu) {
                trapMenu.classList.add('hidden');
            }

            // Desactivar botones después de limpiar
            desactivarBotones();
        }

        // Botón Limpiar Todo
        btnLimpiar.addEventListener('click', () => {
            if (confirm('¿Está seguro de que desea limpiar todo?')) {
                limpiarEstadoCompleto();
            }
        });

        // Botón Cargar Estado
        btnCargar.addEventListener('click', () => {
            cargarEstadoInput.click();
        });

        // Evento para cargar estado desde archivo
        cargarEstadoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(e) {
                try {
                    const estado = JSON.parse(e.target.result);
                    
                    // Limpiar el estado actual
                    limpiarEstadoCompleto();
                    
                    // Verificar si la imagen es una URL o datos base64
                    if (estado.imagen) {
                        // Mostrar la imagen
                        planoImage.src = estado.imagen;
                        planoImage.style.display = 'block';
                        document.getElementById('placeholderText').style.display = 'none';
                        
                        // Ajustar la imagen cuando se cargue
                        planoImage.onload = function() {
                            adjustImageToContainer();
                            
                            // Cargar trampas después de que la imagen esté ajustada
                            if (estado.trampas && Array.isArray(estado.trampas)) {
                                window.puntos = estado.trampas;
                                
                                // Limpiar marcadores existentes para evitar duplicados
                                document.querySelectorAll('.trap-marker').forEach(marker => marker.remove());
                                
                                // Colocar las trampas
                                window.puntos.forEach(punto => {
                                    marcarTrampa(punto);
                                });
                                
                                // Actualizar la tabla
                                actualizarTablaTrampas();
                            }
                            
                            // Cargar zonas después de que la imagen esté ajustada
                            if (estado.zonas && Array.isArray(estado.zonas)) {
                                window.zonas = estado.zonas;
                                
                                // Limpiar zonas existentes para evitar duplicados
                                document.querySelectorAll('.zona, .zona-poligono, .zona-texto').forEach(zona => zona.remove());
                                
                                // Colocar las zonas
                                window.zonas.forEach(zona => {
                                    crearZonaExistente(zona);
                                });
                            }
                        };
                        
                        // Activar botones
                        activarBotones();
                        
                        // Guardar el estado automáticamente para asegurar que se guarde la imagen en el servidor
                        guardarEstadoPlano(false);
                    } else {
                        mostrarMensaje('El archivo JSON no contiene una imagen válida.', 'error');
                    }
                } catch (error) {
                    console.error('Error al cargar el estado:', error);
                    mostrarMensaje('Error al cargar el archivo. Asegúrate de que sea un archivo JSON válido.', 'error');
                }
            };
            reader.readAsText(file);
        });

        // Función para marcar una trampa en el plano
        function marcarTrampa(punto) {
            const planoContainer = document.getElementById('planoContainer');
            const planoImage = document.getElementById('planoImage');
            
            // Verificar que la imagen esté cargada
            if (!planoImage.complete || !planoImage.naturalWidth) {
                console.warn('La imagen no está completamente cargada. Reintentando en 100ms...');
                setTimeout(() => marcarTrampa(punto), 100);
                return;
            }
            
            // Obtener las dimensiones actuales
            const imagenRect = planoImage.getBoundingClientRect();
            const containerRect = planoContainer.getBoundingClientRect();

            // Crear el marcador visual
            const marcador = document.createElement('div');
            marcador.className = 'trap-marker';
            marcador.style.position = 'absolute';
            
            // Guardar las coordenadas originales como atributos de datos
            marcador.dataset.originalX = punto.x;
            marcador.dataset.originalY = punto.y;
            
            // Calcular la posición relativa al contenedor
            const imagenLeft = imagenRect.left - containerRect.left;
            const imagenTop = imagenRect.top - containerRect.top;
            
            // Posicionar el marcador
            marcador.style.left = `${imagenLeft + punto.x}px`;
            marcador.style.top = `${imagenTop + punto.y}px`;
            marcador.style.transform = 'translate(-50%, -50%)';
            marcador.dataset.index = window.puntos.indexOf(punto);

            // Agregar icono según el tipo
            const icon = document.createElement('i');
            switch (punto.tipo) {
                case 'edc_quimicas':
                    icon.className = 'fas fa-flask';
                    break;
                case 'edc_adhesivas':
                    icon.className = 'fas fa-flask';
                    icon.style.color = '#3B82F6'; // Color azul
                    break;
                case 'luz_uv':
                    icon.className = 'fas fa-lightbulb';
                    break;
                case 'feromona_gorgojo':
                    icon.className = 'fas fa-bug';
                    break;
                case 'equipo_sonico':
                    icon.className = 'fas fa-volume-up';
                    break;
                case 'globo_terror':
                    icon.className = 'fas fa-circle';
                    break;
                case 'atrayente_chinches':
                    icon.className = 'fas fa-spider';
                    break;
                case 'atrayente_pulgas':
                    icon.className = 'fas fa-paw';
                    break;
                case 'feromona_picudo':
                    icon.className = 'fas fa-seedling';
                    icon.style.color = '#3B82F6'; // Color azul
                    break;
                // Mantener compatibilidad con tipos antiguos
                case 'rodent':
                    icon.className = 'fas fa-mouse';
                    break;
                case 'insect':
                    icon.className = 'fas fa-bug';
                    break;
                case 'fly':
                    icon.className = 'fas fa-fly';
                    break;
                default:
                    icon.className = 'fas fa-trap';
                    break;
            }
            marcador.appendChild(icon);

            // Determinar el texto a mostrar: usar 'nombre' si existe, sino 'id'
            const displayText = punto.id.startsWith('TEMP-') ? 'Nueva' : (punto.nombre || punto.id);
            
            // Agregar tooltip con información
            marcador.title = `${getTipoTrampa(punto.tipo)} - ${punto.zona || 'Sin zona'} (ID: ${displayText})`;
            
            // Agregar ID visible en el marcador
            const idLabel = document.createElement('span');
            idLabel.className = 'trap-id-label';
            // No mostrar el ID temporal con prefijo TEMP-, mostrar "Nueva" en su lugar
            // Usar 'nombre' si existe, sino usar 'id'
            idLabel.textContent = displayText;
            idLabel.style.position = 'absolute';
            idLabel.style.top = '-20px';
            idLabel.style.left = '50%';
            idLabel.style.transform = 'translateX(-50%)';
            idLabel.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
            idLabel.style.color = 'white';
            idLabel.style.padding = '2px 5px';
            idLabel.style.borderRadius = '3px';
            idLabel.style.fontSize = '9px';
            idLabel.style.whiteSpace = 'nowrap';
            idLabel.style.maxWidth = '120px';
            idLabel.style.overflow = 'hidden';
            idLabel.style.textOverflow = 'ellipsis';
            marcador.appendChild(idLabel);

            // Agregar evento para eliminar
            marcador.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                if (confirm('¿Desea eliminar esta trampa?')) {
                    const index = parseInt(this.dataset.index);
                    window.puntos.splice(index, 1);
                    this.remove();
                    reindexarTrampas();
                    actualizarTablaTrampas();
                    
                    // Guardar automáticamente el estado del plano después de eliminar la trampa
                    guardarEstadoPlano(false);
                }
            });

            // Agregar evento de clic al marcador
            const marcadorConEvento = addTrapClickEvent(marcador);

            planoContainer.appendChild(marcadorConEvento);
            return marcadorConEvento;
        }

        // Modificar el evento de clic en las trampas
        function addTrapClickEvent(marker) {
            // Remove existing click event to avoid duplicates
            const newMarker = marker.cloneNode(true);
            if (marker.parentNode) {
                marker.parentNode.replaceChild(newMarker, marker);
            }
            
            // Add the click event to the new marker
            newMarker.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Si ya hay un tooltip visible, ocultarlo
                if (tooltip.style.display === 'block' && activeMarker === this) {
                    hideTooltip();
                    return;
                }

                // Ocultar cualquier otro tooltip visible
                hideTooltip();
                
                // Mostrar el tooltip para esta trampa
                showTooltip(this, e);
                
                // Resaltar la trampa seleccionada
                document.querySelectorAll('.trap-marker').forEach(m => {
                    m.classList.remove('highlighted');
                });
                this.classList.add('highlighted');
            });
            
            return newMarker;
        }

        // Función para mostrar el tooltip
        function showTooltip(marker, event) {
            const rect = marker.getBoundingClientRect();
            
            // Asegurarse de que el tooltip tenga el contenido correcto
            tooltip.innerHTML = `
                <div class="space-y-2">
                    <button type="button" class="add-incidence-btn w-full text-left">
                        <i class="fas fa-plus-circle mr-2"></i>
                        Agregar incidencia
                    </button>
                    <button type="button" class="edit-id-btn w-full text-left">
                        <i class="fas fa-edit mr-2"></i>
                        Editar ID
                    </button>
                </div>
            `;
            
            // Agregar evento al botón de incidencia
            tooltip.querySelector('.add-incidence-btn').addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (activeMarker) {
                    const trampaIndex = activeMarker.dataset.index;
                    const trampa = window.puntos[trampaIndex];
                    
                    if (!trampa) {
                        mostrarMensaje('Error: No se pudo identificar la trampa', 'error');
                        return;
                    }
                    
                    // Actualizar la información de la trampa en el modal
                    document.getElementById('trampa_id').value = trampa.id_trampa || trampa.id; // Usar id_trampa si existe, si no usar id
                    // Mostrar el 'nombre' (lo que los inspectores llaman ID), sino mostrar id_trampa o id
                    document.getElementById('trampaDbIdDisplay').textContent = trampa.nombre || trampa.id_trampa || (trampa.id.startsWith('TEMP-') ? 'Sin ID' : (trampa.id || 'Sin ID'));
                    document.getElementById('trampaZonaDisplay').textContent = trampa.zona || 'Sin zona';
                    
                    // Establecer la fecha y hora actual como valor predeterminado
                    const now = new Date();
                    const year = now.getFullYear();
                    const month = String(now.getMonth() + 1).padStart(2, '0');
                    const day = String(now.getDate()).padStart(2, '0');
                    const hours = String(now.getHours()).padStart(2, '0');
                    const minutes = String(now.getMinutes()).padStart(2, '0');
                    document.getElementById('fecha_incidencia').value = `${year}-${month}-${day}T${hours}:${minutes}`;
                    
                    // Mostrar el modal
                    document.getElementById('modalIncidencia').classList.remove('hidden');
                    
                    // Asegurar que los marcadores estén por debajo del modal
                    document.querySelectorAll('.trap-marker').forEach(marker => {
                        marker.style.zIndex = '5';
                    });
                    
                    hideTooltip();
                }
            });
            
            // Agregar evento al botón de editar ID
            tooltip.querySelector('.edit-id-btn').addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (activeMarker) {
                    const trampaIndex = activeMarker.dataset.index;
                    const trampa = window.puntos[trampaIndex];
                    
                    if (!trampa) {
                        mostrarMensaje('Error: No se pudo identificar la trampa', 'error');
                        return;
                    }
                    
                    // Establecer la trampa activa para edición (incluir índice)
                    window.trampaSeleccionadaParaEdicion = {
                        ...trampa,
                        index: trampaIndex,
                        marker: activeMarker
                    };
                    
                    // Prellenar el campo con el nombre actual (lo que los inspectores llaman "ID")
                    const idActual = trampa.nombre || trampa.id_trampa || (trampa.id.startsWith('TEMP-') ? '' : (trampa.id || ''));
                    document.getElementById('nuevoIdTrampa').value = idActual;
                    
                    // Mostrar el modal de edición
                    document.getElementById('modalEditarId').classList.remove('hidden');
                    
                    // Enfocar el campo de texto
                    document.getElementById('nuevoIdTrampa').focus();
                    
                                         hideTooltip();
                 }
             });
            
            // Mostrar el tooltip
            tooltip.style.display = 'block';
            tooltip.style.left = `${rect.left}px`;
            tooltip.style.top = `${rect.bottom + 5}px`;
            activeMarker = marker;
        }

        // Función para ocultar el tooltip
        function hideTooltip() {
            tooltip.style.display = 'none';
            activeMarker = null;
        }

        // Asignar la función marcarTrampa al objeto window
        window.marcarTrampa = marcarTrampa;

        // Agregar el evento a las trampas existentes
        document.querySelectorAll('.trap-marker').forEach(marker => {
            const newMarker = addTrapClickEvent(marker);
            if (marker !== newMarker && marker.parentNode) {
                marker.parentNode.replaceChild(newMarker, marker);
            }
        });

        // Cerrar tooltip al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.trap-marker') && !e.target.closest('.trap-tooltip')) {
                hideTooltip();
            }
        });

        // Reattach trap click events when page becomes visible again
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                // Reajustar la imagen cuando la página vuelve a ser visible
                adjustImageToContainer();
                
                // Recargar el estado del plano desde el servidor
                recargarEstadoPlano();
                
                // Page is now visible, reattach trap click events
                document.querySelectorAll('.trap-marker').forEach(marker => {
                    // Add the click event and get the new marker
                    const newMarker = addTrapClickEvent(marker);
                    // Replace the old marker with the new one
                    if (marker !== newMarker && marker.parentNode) {
                        marker.parentNode.replaceChild(newMarker, marker);
                    }
                });
            }
        });

        // Función para recargar el estado del plano desde el servidor
        function recargarEstadoPlano() {
            fetch(`<?= base_url('blueprints/obtener_estado/' . $plano['id']) ?>`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('Estado recargado desde servidor:', data);
                    
                    // PRIORIDAD: Usar trampas desde BD si existen, sino desde JSON
                    let trampasParaCargar = [];
                    
                    if (data.trampas && data.trampas.length > 0) {
                        // Cargar desde la BD (fuente de verdad)
                        trampasParaCargar = data.trampas.map(t => ({
                            id: t.id_trampa || t.id,
                            id_trampa: t.id_trampa || t.id, // Asegurar que siempre haya un id_trampa
                            nombre: t.nombre || t.id_trampa || t.id, // Usar nombre o id_trampa como fallback
                            tipo: t.tipo,
                            x: parseFloat(t.coordenada_x),
                            y: parseFloat(t.coordenada_y),
                            zona: t.ubicacion,
                            ubicacion: t.ubicacion
                        }));
                    } else if (data.plano && data.plano.archivo) {
                        // Fallback: cargar desde JSON si no hay trampas en BD
                        try {
                            const estadoGuardado = JSON.parse(data.plano.archivo);
                            if (estadoGuardado && estadoGuardado.trampas && Array.isArray(estadoGuardado.trampas)) {
                                trampasParaCargar = estadoGuardado.trampas;
                            }
                        } catch (error) {
                            console.error('Error al procesar el estado del plano:', error);
                        }
                    }
                    
                    if (trampasParaCargar.length > 0) {
                        // Actualizar el array de puntos con los datos del servidor
                        window.puntos = trampasParaCargar;
                        
                        // Limpiar marcadores existentes para evitar duplicados
                        document.querySelectorAll('.trap-marker').forEach(marker => marker.remove());
                        
                        // Colocar las trampas
                        window.puntos.forEach(punto => {
                            marcarTrampa(punto);
                        });
                        
                        // Actualizar la tabla
                        actualizarTablaTrampas();
                        
                        // Asegurar que todos los marcadores tengan el evento de clic
                        document.querySelectorAll('.trap-marker').forEach(marker => {
                            const newMarker = addTrapClickEvent(marker);
                            if (marker !== newMarker && marker.parentNode) {
                                marker.parentNode.replaceChild(newMarker, marker);
                            }
                        });
                        
                        // Reposicionar las trampas para asegurar que estén en la posición correcta
                        setTimeout(reposicionarTrampas, 100);
                    }
                }
            })
            .catch(error => {
                console.error('Error al recargar el estado del plano:', error);
            });
        }

        // Handle page loads from cache (when navigating back)
        window.addEventListener('pageshow', function(event) {
            // If the page is loaded from cache (navigating back)
            if (event.persisted) {
                // Reajustar la imagen
                adjustImageToContainer();
                
                // Recargar el estado del plano desde el servidor
                recargarEstadoPlano();
                
                // Reattach trap click events
                document.querySelectorAll('.trap-marker').forEach(marker => {
                    // Add the click event and get the new marker
                    const newMarker = addTrapClickEvent(marker);
                    // Replace the old marker with the new one
                    if (marker !== newMarker && marker.parentNode) {
                        marker.parentNode.replaceChild(newMarker, marker);
                    }
                });
            }
        });

        // Dropdown de Agregar Trampa
        const dropdownButton = dropdownTrampa.querySelector('button');
        const dropdownMenu = dropdownTrampa.querySelector('.trap-menu');

        dropdownButton.addEventListener('click', () => {
            dropdownMenu.classList.toggle('hidden');
        });

        // Eventos para tipos de trampas
        const trapLinks = dropdownMenu.querySelectorAll('a[data-trap-type]');
        trapLinks.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                tipoTrampaSeleccionado = e.target.closest('a').dataset.trapType;
                modoEdicion = 'agregarTrampa';
                dropdownMenu.classList.add('hidden');
                
                // Cambiar el cursor para indicar modo de agregar
                planoContainer.style.cursor = 'crosshair';
                
                // Agregar clase activa al botón
                dropdownButton.classList.add('active');
                dropdownButton.style.backgroundColor = '#2563eb'; // Azul más oscuro
            });
        });

        // Reemplazar el evento de clic en el plano
        planoContainer.addEventListener('click', function(e) {
            const rect = planoContainer.getBoundingClientRect();
            const imagen = planoImage.getBoundingClientRect();
            
            const clickX = e.clientX - rect.left;
            const clickY = e.clientY - rect.top;
            
            // Verificar si el clic está dentro de la imagen
            const imagenLeft = imagen.left - rect.left;
            const imagenTop = imagen.top - rect.top;
            const imagenRight = imagenLeft + imagen.width;
            const imagenBottom = imagenTop + imagen.height;
            
            if (clickX >= imagenLeft && clickX <= imagenRight && 
                clickY >= imagenTop && clickY <= imagenBottom) {
                
                // Manejar modo de dibujar zona
                if (planoContainer.dataset.modoEdicion === 'dibujarZona') {
                    // Agregar punto al polígono - guardar coordenadas relativas a la imagen
                    puntosPoligono.push({ 
                        x: clickX - imagenLeft, 
                        y: clickY - imagenTop 
                    });
                    
                    // Crear marcador de punto - mostrar en coordenadas absolutas
                    const punto = document.createElement('div');
                    punto.className = 'punto-poligono';
                    punto.style.left = `${clickX}px`;
                    punto.style.top = `${clickY}px`;
                    planoContainer.appendChild(punto);
                    
                    // Actualizar polígono temporal - usar coordenadas absolutas para visualización
                    if (puntosPoligono.length > 2) {
                        const path = puntosPoligono.map(p => `${p.x + imagenLeft}px ${p.y + imagenTop}px`).join(',');
                        poligonoTemporal.style.clipPath = `polygon(${path})`;
                        poligonoTemporal.style.display = 'block';
                    }

                    // Actualizar tooltip y mostrar botón de confirmar
                    if (puntosPoligono.length >= 3) {
                        tooltipElement.innerHTML = 'Haga clic en "Confirmar Zona" para finalizar';
                        confirmButton.classList.remove('hidden');
                    } else {
                        tooltipElement.innerHTML = `Haga clic para agregar puntos. Faltan ${3 - puntosPoligono.length} puntos mínimos.`;
                    }
                }
                
                // Manejar modo de agregar trampa
                else if (modoEdicion === 'agregarTrampa' && tipoTrampaSeleccionado) {
                    // Guardar la posición para usarla después de obtener la zona
                    posicionTrampaX = clickX - imagenLeft;
                    posicionTrampaY = clickY - imagenTop;
                    
                    // Mostrar el modal para ingresar la zona
                    document.getElementById('modalZonaTrampa').classList.remove('hidden');
                    document.getElementById('zonaTrampaInput').focus();
                }
            }
        });

        // Cerrar dropdown cuando se hace clic fuera
        document.addEventListener('click', (e) => {
            if (!dropdownTrampa.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });

        // Función para cerrar el modal de incidencia
        window.closeIncidenciaModal = function() {
            document.getElementById('modalIncidencia').classList.add('hidden');
            // Restaurar la visibilidad de los marcadores
            document.querySelectorAll('.trap-marker').forEach(marker => {
                marker.style.zIndex = '10';
            });
            
            // Reiniciar el formulario
            document.getElementById('formIncidencia').reset();
            
            // Ocultar campos adicionales
            document.getElementById('tipo_plaga_personalizado_container').style.display = 'none';
            document.getElementById('cantidad_organismos_container').style.display = 'none';
            
            // Quitar atributos required
            document.getElementById('tipo_plaga_personalizado').removeAttribute('required');
            document.getElementById('cantidad_organismos').removeAttribute('required');
        }

        // Funciones para el modal de editar ID
        window.abrirModalEditarId = function() {
            // Obtener el ID actual mostrado
            const idActual = document.getElementById('trampaDbIdDisplay').textContent;
            
            // Prellenar el campo con el ID actual (si no es "Sin ID")
            document.getElementById('nuevoIdTrampa').value = idActual !== 'Sin ID' ? idActual : '';
            
            // Mostrar el modal
            document.getElementById('modalEditarId').classList.remove('hidden');
            
            // Enfocar el campo de texto
            document.getElementById('nuevoIdTrampa').focus();
        }

        window.cerrarModalEditarId = function() {
            document.getElementById('modalEditarId').classList.add('hidden');
            document.getElementById('nuevoIdTrampa').value = '';
        }

                         window.guardarNuevoId = function() {
            const nuevoId = document.getElementById('nuevoIdTrampa').value.trim();
            
            if (!nuevoId) {
                alert('Por favor ingrese un ID válido');
                return;
            }
            
            if (!window.trampaSeleccionadaParaEdicion) {
                alert('Error: No hay trampa seleccionada para editar');
                return;
            }
            
            // Preparar datos para enviar al servidor
            const formData = new FormData();
            formData.append('trampa_id_actual', window.trampaSeleccionadaParaEdicion.id_trampa || window.trampaSeleccionadaParaEdicion.id);
            formData.append('nuevo_id_trampa', nuevoId);
            formData.append('plano_id', <?= $plano['id'] ?>);
            
            // Mostrar indicador de carga
            const btnGuardar = document.querySelector('#modalEditarId button[onclick="guardarNuevoId()"]');
            const textoOriginal = btnGuardar.textContent;
            btnGuardar.textContent = 'Guardando...';
            btnGuardar.disabled = true;
            
            // Enviar al servidor
            fetch('<?= base_url('blueprints/actualizar_id_trampa') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const trampaIndex = window.trampaSeleccionadaParaEdicion.index;
                    const marker = window.trampaSeleccionadaParaEdicion.marker;
                    
                    // Actualizar los datos en el array principal de puntos con TODOS los datos del servidor
                    if (trampaIndex !== undefined && window.puntos && window.puntos[trampaIndex] && data.trampa) {
                        // Preservar el tipo original si el servidor no lo devuelve o está vacío
                        const tipoOriginal = window.puntos[trampaIndex].tipo;
                        const tipoServidor = data.trampa.tipo;
                        
                        // Actualizar con los datos completos del servidor para mantener consistencia
                        window.puntos[trampaIndex].nombre = data.trampa.nombre;
                        window.puntos[trampaIndex].tipo = tipoServidor || tipoOriginal; // Usar tipo del servidor o mantener el original
                        window.puntos[trampaIndex].ubicacion = data.trampa.ubicacion;
                        window.puntos[trampaIndex].zona = data.trampa.ubicacion; // Sincronizar zona con ubicacion
                        
                        // Si es una trampa temporal que aún no tiene id_trampa, establecerlo ahora
                        if (window.puntos[trampaIndex].id && window.puntos[trampaIndex].id.startsWith('TEMP-')) {
                            window.puntos[trampaIndex].id_trampa = data.trampa.id_trampa;
                            window.puntos[trampaIndex].id = data.trampa.id_trampa;
                        }
                    }
                    
                    // Actualizar el marcador visual
                    if (marker) {
                        // Actualizar el label del nombre en el marcador
                        const idLabel = marker.querySelector('.trap-id-label');
                        if (idLabel) {
                            idLabel.textContent = nuevoId;
                        }
                        
                        // Actualizar el tooltip del marcador
                        const tipoTrampa = window.puntos[trampaIndex] ? getTipoTrampa(window.puntos[trampaIndex].tipo) : 'Trampa';
                        const zona = window.puntos[trampaIndex] ? window.puntos[trampaIndex].zona : 'Sin zona';
                        marker.title = `${tipoTrampa} - ${zona} (ID: ${nuevoId})`;
                    }
                    
                    // Actualizar la tabla de trampas si existe
                    if (typeof actualizarTablaTrampas === 'function') {
                        actualizarTablaTrampas();
                    }
                    
                    // Cerrar el modal
                    cerrarModalEditarId();
                    
                    // Mostrar mensaje de confirmación
                    mostrarMensaje(`ID de trampa actualizado correctamente a: ${nuevoId}`, 'success');
                    
                    // Limpiar la variable temporal
                    window.trampaSeleccionadaParaEdicion = null;
                } else {
                    // Error del servidor
                    mostrarMensaje(`Error: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                console.error('Error al actualizar ID de trampa:', error);
                mostrarMensaje('Error al comunicarse con el servidor', 'error');
            })
            .finally(() => {
                // Restaurar el botón
                btnGuardar.textContent = textoOriginal;
                btnGuardar.disabled = false;
            });
        }

         // Agregar soporte para cerrar modal con Escape y Enter para guardar
         document.addEventListener('keydown', function(event) {
             const modalEditarId = document.getElementById('modalEditarId');
             if (!modalEditarId.classList.contains('hidden')) {
                 if (event.key === 'Escape') {
                     cerrarModalEditarId();
                 } else if (event.key === 'Enter') {
                     event.preventDefault();
                     guardarNuevoId();
                 }
             }
         });

        // Manejar envío del formulario de incidencia
        document.getElementById('formIncidencia').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Obtener los datos del formulario
            const formData = new FormData(this);
            
            // Agregar el ID de trampa personalizado si existe
            if (window.trampaIdPersonalizado) {
                formData.set('trampa_codigo', window.trampaIdPersonalizado);
            }
            
            // Verificar que se haya seleccionado un tipo de plaga
            if (!formData.get('tipo_plaga')) {
                mostrarMensaje('Debe seleccionar un tipo de plaga', 'error');
                return;
            }
            
            // El trampa_id ya contiene el ID real de la trampa, no necesitamos convertirlo
            const trampaId = formData.get('trampa_id');
            
            // Mostrar información de depuración
            console.log('Todos los puntos disponibles:', window.puntos);
            console.log('Buscando trampa con ID:', trampaId);
            
            // Buscar la trampa por su ID para mostrar información
            const trampa = window.puntos.find(p => p.id_trampa === trampaId || p.id === trampaId);
            if (!trampa) {
                mostrarMensaje('Error: No se pudo identificar la trampa', 'error');
                return;
            }
            
            console.log('Enviando incidencia para trampa:', trampa);
            console.log('ID de trampa enviado:', trampaId);
            
            // Enviar los datos al servidor
            fetch('<?= base_url('blueprints/guardar_incidencia') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Obtener la trampa para la que se registró la incidencia
                    const trampaId = document.getElementById('trampa_id').value;
                    const trampa = window.puntos.find(p => p.id === trampaId);
                    
                    // Mostrar mensaje de éxito con detalles
                    const mensaje = `Incidencia registrada correctamente para la trampa ${trampaId} (${trampa ? trampa.nombre || 'Sin nombre' : 'Desconocida'})`;
                    mostrarMensaje(mensaje, 'success');
                    
                    // Cerrar el modal y limpiar el formulario
                    closeIncidenciaModal();
                    
                    // Resaltar la trampa para la que se registró la incidencia
                    const marker = document.querySelector(`.trap-marker[data-index="${window.puntos.findIndex(p => p.id === trampaId)}"]`);
                    if (marker) {
                        document.querySelectorAll('.trap-marker').forEach(m => {
                            m.classList.remove('highlighted');
                        });
                        marker.classList.add('highlighted');
                        
                        // Agregar una animación para indicar que se registró una incidencia
                        marker.style.animation = 'pulse 1s';
                        setTimeout(() => {
                            marker.style.animation = '';
                        }, 1000);
                    }
                } else {
                    mostrarMensaje(`Error: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                console.error('Error al guardar incidencia:', error);
                mostrarMensaje(`Error al guardar incidencia: ${error.message}`, 'error');
            });
        });

        // Mostrar/ocultar campo de cantidad de organismos cuando se selecciona un tipo de plaga
        document.getElementById('tipo_plaga_select').addEventListener('change', function() {
            const tipoPlagaPersonalizadoContainer = document.getElementById('tipo_plaga_personalizado_container');
            const cantidadContainer = document.getElementById('cantidad_organismos_container');
            const tipoPlagaHidden = document.getElementById('tipo_plaga');
            
            // Actualizar el campo oculto con el valor seleccionado
            tipoPlagaHidden.value = this.value;
            
            // Mostrar/ocultar el campo personalizado si se selecciona "otro"
            if (this.value === 'otro') {
                tipoPlagaPersonalizadoContainer.style.display = 'block';
                document.getElementById('tipo_plaga_personalizado').setAttribute('required', 'required');
                // Limpiar el campo oculto para que se actualice con el valor personalizado
                tipoPlagaHidden.value = '';
            } else {
                tipoPlagaPersonalizadoContainer.style.display = 'none';
                document.getElementById('tipo_plaga_personalizado').removeAttribute('required');
            }
            
            // Mostrar el campo de cantidad de organismos siempre
            cantidadContainer.style.display = 'block';
            if (this.value) {
                document.getElementById('cantidad_organismos').setAttribute('required', 'required');
            } else {
                document.getElementById('cantidad_organismos').removeAttribute('required');
            }
        });
        
        // Actualizar el campo oculto cuando se escribe en el campo personalizado
        document.getElementById('tipo_plaga_personalizado').addEventListener('input', function() {
            document.getElementById('tipo_plaga').value = this.value;
        });

        // Agregar esta función para reindexar las trampas
        function reindexarTrampas() {
            // Reindexar los marcadores en el DOM
            document.querySelectorAll('.trap-marker').forEach((marker, index) => {
                marker.dataset.index = index;
            });
        }

        // Modificar la función de eliminar en actualizarTablaTrampas
        function actualizarTablaTrampas() {
            const tbody = document.getElementById('trampasTableBody');
            if (!tbody) return;
            
            tbody.innerHTML = '';

            if (!window.puntos || window.puntos.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                            No hay trampas registradas
                        </td>
                    </tr>`;
                return;
            }

            // Obtener el valor del filtro
            const filtroTipo = document.getElementById('filtroTipoTrampa').value;

            // Filtrar puntos según el tipo seleccionado
            const puntosFiltrados = filtroTipo 
                ? window.puntos.filter(punto => punto.tipo === filtroTipo)
                : window.puntos;

            if (puntosFiltrados.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">
                            No hay trampas del tipo seleccionado
                        </td>
                    </tr>`;
                return;
            }

            // Modificar el encabezado de la tabla para eliminar la columna de nombre
            const thead = document.querySelector('table thead tr');
            if (thead) {
                thead.innerHTML = `
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Tipo</th>
                    <th class="px-4 py-2 text-left">Ubicación</th>
                    <th class="px-4 py-2 text-left">Zona</th>
                    <th class="px-4 py-2 text-right">Acciones</th>
                `;
            }

            puntosFiltrados.forEach((punto, index) => {
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 cursor-pointer';
                // Mostrar solo el nombre (lo que los inspectores llaman "ID")
                const displayId = punto.id && punto.id.startsWith('TEMP-') ? 'Nueva' : (punto.nombre || punto.id_trampa || punto.id || `T${index + 1}`);
                
                tr.innerHTML = `
                    <td class="px-4 py-2">${displayId}</td>
                    <td class="px-4 py-2">${getTipoTrampa(punto.tipo)}</td>
                    <td class="px-4 py-2">(${Math.round(punto.x)}, ${Math.round(punto.y)})</td>
                    <td class="px-4 py-2">${punto.zona || 'Sin zona'}</td>
                    <td class="px-4 py-2 text-right">
                        <button class="text-red-600 hover:text-red-800 delete-trap" title="Eliminar trampa">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                `;
                
                // Agregar evento de clic a la fila
                tr.addEventListener('click', (e) => {
                    if (e.target.closest('.delete-trap')) return;
                    
                    document.querySelectorAll('.trap-marker').forEach(marker => {
                        marker.classList.remove('highlighted');
                    });
                    
                    tbody.querySelectorAll('tr').forEach(row => {
                        row.classList.remove('selected');
                    });
                    
                    // Encontrar el índice original en el array de puntos
                    const originalIndex = window.puntos.findIndex(p => p === punto);
                    
                    const marker = document.querySelector(`.trap-marker[data-index="${originalIndex}"]`);
                    if (marker) {
                        marker.classList.add('highlighted');
                        marker.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    
                    tr.classList.add('selected');
                });

                // Modificar el evento del botón eliminar
                const deleteBtn = tr.querySelector('.delete-trap');
                deleteBtn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    if (confirm('¿Está seguro de que desea eliminar esta trampa?')) {
                        // Encontrar el índice original en el array de puntos
                        const originalIndex = window.puntos.findIndex(p => p === punto);
                        
                        // Eliminar el marcador del plano
                        const marker = document.querySelector(`.trap-marker[data-index="${originalIndex}"]`);
                        if (marker) marker.remove();
                        
                        // Eliminar del array de puntos
                        window.puntos.splice(originalIndex, 1);
                        
                        // Reindexar las trampas restantes
                        reindexarTrampas();
                        
                        // Actualizar la tabla
                        actualizarTablaTrampas();
                        
                        // Guardar automáticamente el estado del plano después de eliminar la trampa
                        guardarEstadoPlano(false);
                    }
                });
                




                tbody.appendChild(tr);
            });
        }

        // Función auxiliar para obtener el nombre del tipo de trampa
        function getTipoTrampa(tipo) {
            const tipos = {
                // Tipos que están en la base de datos (valores exactos)
                'Equipo de Luz UV': 'Equipo de Luz UV',
                'Equipo Sónico': 'Equipo Sónico',
                'Globo terror': 'Globo terror',
                // Tipos de códigos internos (para compatibilidad)
                'edc_quimicas': 'EDC Químicas',
                'edc_adhesivas': 'EDC Adhesivas',
                'luz_uv': 'Equipo de Luz UV',
                'feromona_gorgojo': 'Trampa de Feromona Gorgojo',
                'equipo_sonico': 'Equipo Sónico',
                'globo_terror': 'Globo terror',
                'atrayente_chinches': 'Trampa atrayente chinches',
                'atrayente_pulgas': 'Trampa atrayente pulgas',
                'feromona_picudo': 'Trampa feromonas picudo rojo',
                // Mantener compatibilidad con tipos antiguos
                'rodent': 'Trampa para Roedores',
                'insect': 'Trampa para Insectos',
                'fly': 'Trampa para Moscas',
                'moth': 'Trampa para Polillas'
            };
            
            return tipos[tipo] || 'Desconocido';
        }

        // Agregar estilos CSS para los marcadores
        const style = document.createElement('style');
        style.textContent = `
            .trap-marker {
                cursor: pointer;
                padding: 8px;
                background: rgba(255, 255, 255, 0.9);
                border-radius: 50%;
                box-shadow: 0 2px 4px rgba(0,0,0,0.2);
                z-index: 10;
                transition: transform 0.2s;
            }
            .trap-marker:hover {
                transform: scale(1.1);
                background: rgba(255, 255, 255, 1);
            }
            .trap-marker i {
                font-size: 16px;
                color: #444;
            }
            
            /* Cuando el modal está abierto, asegurarse de que los marcadores estén por debajo */
            #modalIncidencia:not(.hidden) ~ * .trap-marker {
                z-index: 5 !important;
            }
        `;
        document.head.appendChild(style);

        // Agregar estilos para el contenedor del plano
        const planoContainerStyle = document.createElement('style');
        planoContainerStyle.textContent = `
            #planoContainer {
                position: relative;
                overflow: hidden;
            }
            #planoImage {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
            }
        `;
        document.head.appendChild(planoContainerStyle);

        // Agregar estos estilos CSS
        const moveStyles = document.createElement('style');
        moveStyles.textContent = `
            .trap-marker.movable {
                cursor: move;
            }
            .trap-marker.moving {
                opacity: 0.8;
                transform: scale(1.1);
            }
        `;
        document.head.appendChild(moveStyles);

        // Agregar estos estilos CSS para el botón activo
        const dropdownStyles = document.createElement('style');
        dropdownStyles.textContent = `
            .dropdown button.active {
                background-color: #2563eb !important;
            }
            .dropdown button.active:hover {
                background-color: #1d4ed8 !important;
            }
        `;
        document.head.appendChild(dropdownStyles);

        // Agregar estos estilos CSS para el resaltado
        const highlightStyles = document.createElement('style');
        highlightStyles.textContent = `
            .trap-marker.highlighted {
                box-shadow: 0 0 0 3px rgba(29, 78, 216, 0.7);
                transform: scale(1.2);
                z-index: 1000;
            }
        `;
        document.head.appendChild(highlightStyles);

        // Agregar el evento para el botón de Agregar Zona
        btnAgregarZona.addEventListener('click', () => {
            const modoActual = planoContainer.dataset.modoEdicion || '';
            if (modoActual === 'dibujarZona') {
                // Desactivar modo dibujar
                finalizarDibujoPoligono();
                planoContainer.dataset.modoEdicion = '';
                btnAgregarZona.classList.remove('active');
                planoContainer.style.cursor = 'default';
            } else {
                // Activar modo dibujar
                planoContainer.dataset.modoEdicion = 'dibujarZona';
                btnAgregarZona.classList.add('active');
                planoContainer.style.cursor = 'crosshair';
                iniciarDibujoPoligono();
            }
        });

        // Función para iniciar el dibujo del polígono
        function iniciarDibujoPoligono() {
            dibujandoPoligono = true;
            puntosPoligono = [];
            
            // Crear el polígono temporal
            poligonoTemporal = document.createElement('div');
            poligonoTemporal.className = 'zona-poligono-temporal';
            planoContainer.appendChild(poligonoTemporal);

            // Crear tooltip flotante
            tooltipElement = document.createElement('div');
            tooltipElement.className = 'tooltip-flotante';
            tooltipElement.innerHTML = 'Haga clic para agregar puntos. Mínimo 3 puntos.';
            document.body.appendChild(tooltipElement);

            // Crear botón de confirmar
            confirmButton = document.createElement('button');
            confirmButton.className = 'confirm-polygon-button hidden';
            confirmButton.innerHTML = `
                <i class="fas fa-check mr-2"></i>
                Confirmar Zona
            `;
            planoContainer.appendChild(confirmButton);

            // Evento para el botón confirmar
            confirmButton.addEventListener('click', () => {
                if (puntosPoligono.length >= 3) {
                    finalizarDibujoPoligono();
                    planoContainer.dataset.modoEdicion = '';
                    btnAgregarZona.classList.remove('active');
                    planoContainer.style.cursor = 'default';
                }
            });

            // Actualizar posición del tooltip con el movimiento del mouse
            document.addEventListener('mousemove', actualizarPosicionTooltip);
        }

        // Función para actualizar la posición del tooltip
        function actualizarPosicionTooltip(e) {
            if (tooltipElement) {
                const offset = 5; // Distancia del cursor al tooltip
                tooltipElement.style.left = `${e.clientX + offset}px`;
                tooltipElement.style.top = `${e.clientY + offset}px`;
            }
        }

        // Función para finalizar el dibujo
        function finalizarDibujoPoligono() {
            if (puntosPoligono.length >= 3) {
                // Solicitar nombre de la zona
                const nombreZona = prompt('Ingrese un nombre para la zona:', '');
                if (nombreZona) {
                    // Obtener las dimensiones actuales
                    const imagenRect = planoImage.getBoundingClientRect();
                    const containerRect = planoContainer.getBoundingClientRect();
                    
                    // Calcular la posición relativa al contenedor
                    const imagenLeft = imagenRect.left - containerRect.left;
                    const imagenTop = imagenRect.top - containerRect.top;
                    
                    // Crear el polígono final
                    const poligono = document.createElement('div');
                    poligono.className = 'zona-poligono';
                    
                    // Calcular el path del polígono - usar coordenadas absolutas para visualización
                    const path = puntosPoligono.map(p => `${p.x + imagenLeft}px ${p.y + imagenTop}px`).join(',');
                    poligono.style.clipPath = `polygon(${path})`;
                    
                    // Crear el contenedor del texto
                    const textoZona = document.createElement('div');
                    textoZona.className = 'zona-texto';
                    textoZona.textContent = nombreZona;
                    
                    // Calcular el centro de la zona para posicionar el texto
                    const centroX = puntosPoligono.reduce((sum, p) => sum + p.x, 0) / puntosPoligono.length;
                    const centroY = puntosPoligono.reduce((sum, p) => sum + p.y, 0) / puntosPoligono.length;
                    
                    // Posicionar el texto - usar coordenadas absolutas para visualización
                    textoZona.style.left = `${centroX + imagenLeft}px`;
                    textoZona.style.top = `${centroY + imagenTop}px`;
                    
                    // Guardar la zona - guardar coordenadas relativas a la imagen
                    if (!window.zonas) window.zonas = [];
                    const zonaId = `Z${window.zonas.length + 1}`;
                    window.zonas.push({
                        tipo: 'poligono',
                        puntos: puntosPoligono, // Ya están en coordenadas relativas a la imagen
                        id: zonaId,
                        nombre: nombreZona,
                        centro: { x: centroX, y: centroY } // Guardar centro relativo a la imagen
                    });
                    
                    // Agregar ID al elemento del DOM
                    poligono.dataset.zonaId = zonaId;
                    poligono.dataset.index = window.zonas.length - 1;
                    planoContainer.appendChild(poligono);
                    planoContainer.appendChild(textoZona);
                }
            }
            
            // Limpiar
            dibujandoPoligono = false;
            puntosPoligono = [];
            
            if (poligonoTemporal) {
                poligonoTemporal.remove();
                poligonoTemporal = null;
            }
            if (tooltipElement) {
                tooltipElement.remove();
                tooltipElement = null;
                document.removeEventListener('mousemove', actualizarPosicionTooltip);
            }
            if (confirmButton) {
                confirmButton.remove();
                confirmButton = null;
            }
            document.querySelectorAll('.punto-poligono').forEach(punto => punto.remove());
        }

        // Agregar estos estilos para el texto de la zona
        const zonaStyles = document.createElement('style');
        zonaStyles.textContent = `
            .tooltip-flotante {
                position: fixed;
                background-color: rgba(0, 0, 0, 0.8);
                color: white;
                padding: 8px 12px;
                border-radius: 4px;
                font-size: 14px;
                pointer-events: none;
                z-index: 1000;
                transform: translate(0, -100%);
                white-space: nowrap;
            }

            .confirm-polygon-button {
                position: absolute;
                bottom: 20px;
                left: 50%;
                transform: translateX(-50%);
                background-color: #059669;
                color: white;
                padding: 8px 16px;
                border-radius: 6px;
                cursor: pointer;
                display: flex;
                align-items: center;
                transition: all 0.2s;
                z-index: 1000;
            }

            .confirm-polygon-button:hover {
                background-color: #047857;
            }

            .confirm-polygon-button.hidden {
                display: none;
            }

            .zona-poligono-temporal,
            .zona-poligono {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(255, 255, 0, 0.2);
                border: 2px dashed rgba(0, 0, 0, 0.5);
                pointer-events: none;
            }

            .punto-poligono {
                position: absolute;
                width: 8px;
                height: 8px;
                background-color: #ff0;
                border: 1px solid #000;
                border-radius: 50%;
                transform: translate(-50%, -50%);
                z-index: 20;
            }

            .zona-texto {
                position: absolute;
                background: rgba(255, 255, 255, 0.8);
                padding: 2px 6px;
                border-radius: 4px;
                font-size: 12px;
                font-weight: bold;
                transform: translate(-50%, -50%);
                z-index: 15;
                pointer-events: none;
                white-space: nowrap;
            }
        `;
        document.head.appendChild(zonaStyles);

        // Agregar estilos para botones desactivados
        const buttonStyles = document.createElement('style');
        buttonStyles.textContent = `
            button:disabled {
                pointer-events: none;
            }
            
            .cursor-not-allowed {
                cursor: not-allowed !important;
            }
            
            /* Estilos para todos los modales - asegurar que estén por encima de las trampas */
            #modalIncidencia,
            #modalEditarIncidencia,
            #modalEditarId,
            #modalZonaTrampa,
            #modalConfirmarDesactivar {
                z-index: 10000 !important; /* Asegurar que estén por encima de todo */
            }
            
            #modalIncidencia .bg-white,
            #modalEditarIncidencia .bg-white,
            #modalEditarId .bg-white,
            #modalZonaTrampa .bg-white,
            #modalConfirmarDesactivar .bg-white {
                position: relative;
                z-index: 10001; /* Asegurar que el contenido del modal esté por encima del fondo */
            }
            
            /* Asegurar que las trampas estén por debajo de los modales */
            .trap-marker {
                z-index: 10 !important;
            }
            
            .trap-tooltip {
                z-index: 100 !important;
            }
        `;
        document.head.appendChild(buttonStyles);

        // Botón Mover Trampas
        btnMoverTrampa.addEventListener('click', () => {
            const modoActual = planoContainer.dataset.modoEdicion || '';
            if (modoActual === 'mover') {
                // Desactivar modo mover
                planoContainer.dataset.modoEdicion = '';
                btnMoverTrampa.classList.remove('active', 'bg-green-600', 'hover:bg-green-700');
                btnMoverTrampa.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
                btnMoverTrampa.innerHTML = `
                    <i class="fas fa-arrows-alt mr-2"></i>
                    Mover Trampas
                `;
                planoContainer.style.cursor = 'default';
                
                // Remover clase de las trampas
                document.querySelectorAll('.trap-marker').forEach(trampa => {
                    trampa.classList.remove('movable');
                });
            } else {
                // Activar modo mover
                planoContainer.dataset.modoEdicion = 'mover';
                btnMoverTrampa.classList.add('active', 'bg-green-600', 'hover:bg-green-700');
                btnMoverTrampa.classList.remove('bg-yellow-600', 'hover:bg-yellow-700');
                btnMoverTrampa.innerHTML = `
                    <i class="fas fa-save mr-2"></i>
                    Guardar Cambios
                `;
                planoContainer.style.cursor = 'move';
                
                // Agregar clase a las trampas
                document.querySelectorAll('.trap-marker').forEach(trampa => {
                    trampa.classList.add('movable');
                });
            }
        });

        // Agregar eventos para mover trampas
        planoContainer.addEventListener('mousedown', function(e) {
            if (planoContainer.dataset.modoEdicion !== 'mover') return;
            
            const trampa = e.target.closest('.trap-marker');
            if (trampa) {
                e.preventDefault();
                trampaSeleccionada = trampa;
                
                // Calcular el offset del clic relativo a la trampa
                const trampaRect = trampa.getBoundingClientRect();
                offsetX = e.clientX - trampaRect.left - trampaRect.width / 2;
                offsetY = e.clientY - trampaRect.top - trampaRect.height / 2;
                
                trampa.style.zIndex = '1000';
                trampa.classList.add('moving');
            }
        });

        document.addEventListener('mousemove', function(e) {
            if (!trampaSeleccionada) return;
            
            const containerRect = planoContainer.getBoundingClientRect();
            const imagenRect = planoImage.getBoundingClientRect();
            
            // Calcular límites del área válida
            const minX = imagenRect.left - containerRect.left;
            const maxX = minX + imagenRect.width;
            const minY = imagenRect.top - containerRect.top;
            const maxY = minY + imagenRect.height;
            
            // Calcular nueva posición
            let newX = e.clientX - containerRect.left - offsetX;
            let newY = e.clientY - containerRect.top - offsetY;
            
            // Limitar al área de la imagen
            newX = Math.max(minX, Math.min(maxX, newX));
            newY = Math.max(minY, Math.min(maxY, newY));
            
            // Actualizar posición
            trampaSeleccionada.style.left = `${newX}px`;
            trampaSeleccionada.style.top = `${newY}px`;
        });

        // Función para guardar una trampa en la base de datos
        function guardarTrampaEnBD(trampa) {
            // Verificar que las coordenadas sean números válidos
            if (isNaN(trampa.x) || isNaN(trampa.y)) {
                console.error('Coordenadas inválidas:', trampa);
                mostrarMensaje('Error: Coordenadas inválidas', 'error');
                return;
            }
            
            // Redondear las coordenadas a 2 decimales para mayor precisión
            const x = parseFloat(trampa.x).toFixed(2);
            const y = parseFloat(trampa.y).toFixed(2);
            
            // Obtener el tipo de trampa en formato legible
            const tipoLegible = getTipoTrampa(trampa.tipo);
            
            // Preparar los datos para enviar
            const formData = new FormData();
            formData.append('sede_id', <?= $sede['id'] ?>);
            formData.append('plano_id', <?= $plano['id'] ?>);
            formData.append('tipo', tipoLegible);
            formData.append('ubicacion', trampa.zona);
            formData.append('coordenada_x', x);
            formData.append('coordenada_y', y);
            
            // Si la trampa ya tiene un id_trampa, enviarlo para mantenerlo
            if (trampa.id && !trampa.id.startsWith('TEMP-')) {
                formData.append('id_trampa', trampa.id);
            }
            
            // Enviar los datos al servidor
            fetch('<?= base_url('blueprints/guardar_trampa') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    console.log('Trampa guardada correctamente:', data);
                    
                    // Actualizar el ID de la trampa con el ID único generado
                    if (data.trampa && data.trampa.id_trampa) {
                        // Guardar el ID original como referencia interna
                        trampa.id_interno = trampa.id;
                        
                        // Actualizar los campos con los datos del servidor
                        trampa.id = data.trampa.id_trampa;
                        trampa.id_trampa = data.trampa.id_trampa;
                        trampa.nombre = data.trampa.nombre || data.trampa.id_trampa; // Nombre para mostrar
                        
                        // Actualizar la trampa en el array de puntos
                        const index = window.puntos.findIndex(p => p.id_interno === trampa.id_interno);
                        if (index !== -1) {
                            window.puntos[index] = trampa;
                        }
                        
                        // Actualizar el nombre visible en el marcador
                        const marcador = document.querySelector(`.trap-marker[data-index="${index}"]`);
                        if (marcador) {
                            const idLabel = marcador.querySelector('.trap-id-label');
                            if (idLabel) {
                                idLabel.textContent = trampa.nombre; // Mostrar el nombre
                            }
                            // Actualizar también el tooltip
                            marcador.title = `${getTipoTrampa(trampa.tipo)} - ${trampa.zona || 'Sin zona'} (ID: ${trampa.id})`;
                        }
                        
                        // Actualizar la tabla de trampas
                        actualizarTablaTrampas();
                    }
                    
                    // Mostrar mensaje apropiado
                    mostrarMensaje(`Trampa actualizada correctamente con ID: ${trampa.id}`, 'success');
                    
                    // Guardar automáticamente el estado del plano después de guardar la trampa
                    guardarEstadoPlano(false);
                } else {
                    console.error('Error al guardar la trampa:', data.message);
                    mostrarMensaje(`Error al guardar la trampa: ${data.message}`, 'error');
                }
            })
            .catch(error => {
                console.error('Error en la solicitud:', error);
                mostrarMensaje('Error en la comunicación con el servidor', 'error');
            });
        }

        // Función para guardar el estado del plano
        function guardarEstadoPlano(mostrarMensajeExito = true) {
            // Verificar que la imagen esté cargada completamente
            if (!planoImage.complete || !planoImage.naturalWidth) {
                mostrarMensaje('La imagen no está completamente cargada. Espere un momento y vuelva a intentarlo.', 'error');
                return;
            }
            
            // Obtener el ancho renderizado actual de la imagen
            const renderedWidth = planoImage.offsetWidth;
            
            // Verificar y limpiar los datos antes de guardar
            const trampasProcesadas = [];
            if (window.puntos && window.puntos.length > 0) {
                window.puntos.forEach(punto => {
                    // Crear una copia limpia del punto con coordenadas válidas
                    const puntoProcesado = {
                        id: punto.id,
                        tipo: punto.tipo,
                        x: parseFloat(parseFloat(punto.x).toFixed(2)),
                        y: parseFloat(parseFloat(punto.y).toFixed(2)),
                        zona: punto.zona
                    };
                    trampasProcesadas.push(puntoProcesado);
                });
            }
            
            const estado = {
                imagen: planoImage.src,
                trampas: trampasProcesadas,
                zonas: window.zonas || [],
                renderedWidth: renderedWidth // Guardar el ancho renderizado
            };
            
            // Convertir el estado a JSON
            const jsonData = JSON.stringify(estado);
            
            // Guardar en la base de datos mediante AJAX
            fetch('<?= base_url('blueprints/guardar_estado') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    'plano_id': <?= $plano['id'] ?>,
                    'json_data': jsonData
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Mostrar mensaje de éxito solo si se solicita
                    if (mostrarMensajeExito) {
                        mostrarMensaje(data.message, 'success');
                    }
                    
                    // Actualizar los puntos en memoria con los datos procesados
                    window.puntos = trampasProcesadas;
                } else {
                    // Mostrar mensaje de error
                    mostrarMensaje(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al guardar el estado', 'error');
            });
        }

        document.addEventListener('mouseup', function() {
            if (!trampaSeleccionada) return;
            
            // Obtener coordenadas de la nueva posición
            const containerRect = planoContainer.getBoundingClientRect();
            const imagenRect = planoImage.getBoundingClientRect();
            
            const trampaRect = trampaSeleccionada.getBoundingClientRect();
            const newX = trampaRect.left + trampaRect.width / 2 - imagenRect.left;
            const newY = trampaRect.top + trampaRect.height / 2 - imagenRect.top;
            
            // Obtener datos de la trampa original
            const index = parseInt(trampaSeleccionada.dataset.index);
            const trampaOriginal = window.puntos[index];
            
            // Solicitar la nueva zona y el comentario
            const nuevaZona = prompt('Ingrese la zona para la nueva ubicación de la trampa:', trampaOriginal.zona || '');
            
            if (nuevaZona) {
                // Solicitar el comentario del movimiento
                const comentario = prompt('Ingrese el motivo del movimiento de la trampa:', '');
                
                // Actualizar la posición y zona de la trampa
                trampaOriginal.x = newX;
                trampaOriginal.y = newY;
                trampaOriginal.zona = nuevaZona;
                
                // Actualizar la posición del marcador visual
                trampaSeleccionada.style.left = `${imagenRect.left - containerRect.left + newX}px`;
                trampaSeleccionada.style.top = `${imagenRect.top - containerRect.top + newY}px`;
                
                // Actualizar los atributos data-originalX y data-originalY del marcador
                trampaSeleccionada.dataset.originalX = newX;
                trampaSeleccionada.dataset.originalY = newY;
                
                // Guardar el movimiento en la base de datos incluyendo el comentario
                const formData = new FormData();
                formData.append('sede_id', <?= $sede['id'] ?>);
                formData.append('plano_id', <?= $plano['id'] ?>);
                formData.append('tipo', trampaOriginal.tipo);
                formData.append('ubicacion', nuevaZona);
                formData.append('coordenada_x', newX);
                formData.append('coordenada_y', newY);
                formData.append('id_trampa', trampaOriginal.id);
                formData.append('comentario', comentario || 'Sin comentario');
                
                fetch('<?= base_url('blueprints/guardar_trampa') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarMensaje('Trampa movida correctamente', 'success');
                    } else {
                        mostrarMensaje('Error al mover la trampa: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarMensaje('Error en la comunicación con el servidor', 'error');
                });
            } else {
                // Si se canceló, restaurar la posición original
                trampaSeleccionada.style.left = `${imagenRect.left - containerRect.left + trampaOriginal.x}px`;
                trampaSeleccionada.style.top = `${imagenRect.top - containerRect.top + trampaOriginal.y}px`;
            }
            
            // Limpiar estado
            trampaSeleccionada.style.zIndex = '10';
            trampaSeleccionada.classList.remove('moving');
            trampaSeleccionada = null;
            offsetX = 0;
            offsetY = 0;
            
            // Actualizar tabla
            actualizarTablaTrampas();
        });

        // Función para reposicionar trampas basado en las coordenadas originales
        function reposicionarTrampas() {
            const planoContainer = document.getElementById('planoContainer');
            const planoImage = document.getElementById('planoImage');
            
            if (!planoImage.complete || !planoImage.naturalWidth) {
                console.warn('La imagen no está completamente cargada. Reintentando en 100ms...');
                setTimeout(reposicionarTrampas, 100);
                return;
            }
            
            // Obtener las dimensiones actuales
            const imagenRect = planoImage.getBoundingClientRect();
            const containerRect = planoContainer.getBoundingClientRect();
            
            // Calcular la posición relativa al contenedor
            const imagenLeft = imagenRect.left - containerRect.left;
            const imagenTop = imagenRect.top - containerRect.top;
            
            // Reposicionar cada marcador
            document.querySelectorAll('.trap-marker').forEach(marker => {
                const originalX = parseFloat(marker.dataset.originalX);
                const originalY = parseFloat(marker.dataset.originalY);
                
                if (!isNaN(originalX) && !isNaN(originalY)) {
                    marker.style.left = `${imagenLeft + originalX}px`;
                    marker.style.top = `${imagenTop + originalY}px`;
                }
            });
            
            // Reposicionar zonas
            document.querySelectorAll('.zona-poligono').forEach(zona => {
                const index = parseInt(zona.dataset.index);
                if (isNaN(index) || !window.zonas || !window.zonas[index]) return;
                
                const zonaData = window.zonas[index];
                if (zonaData.tipo !== 'poligono' || !zonaData.puntos) return;
                
                // Calcular el nuevo path del polígono
                const path = zonaData.puntos.map(p => 
                    `${p.x + imagenLeft}px ${p.y + imagenTop}px`
                ).join(',');
                
                zona.style.clipPath = `polygon(${path})`;
            });
            
            // Reposicionar textos de zonas
            document.querySelectorAll('.zona-texto').forEach(texto => {
                const indice = Array.from(texto.parentNode.children)
                    .filter(el => el.matches('.zona-poligono'))
                    .findIndex(el => el.nextElementSibling === texto);
                    
                if (indice !== -1 && window.zonas && window.zonas[indice]) {
                    const zonaData = window.zonas[indice];
                    if (zonaData.centro) {
                        texto.style.left = `${imagenLeft + zonaData.centro.x}px`;
                        texto.style.top = `${imagenTop + zonaData.centro.y}px`;
                    }
                }
            });
        }

        // Llamar a reposicionarTrampas cuando la ventana cambia de tamaño
        window.addEventListener('resize', reposicionarTrampas);

        // Reposicionar trampas cuando la imagen se carga
        planoImage.addEventListener('load', function() {
            setTimeout(reposicionarTrampas, 100);
        });

        // Cerrar tooltip al hacer clic fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.trap-marker') && !e.target.closest('.trap-tooltip')) {
                hideTooltip();
            }
        });

        // Manejar el formulario de zona
        document.getElementById('formZonaTrampa').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Obtener el ID de trampa ingresado por el usuario
            const idTrampaManual = document.getElementById('idTrampaInput').value.trim();
            
            // Obtener la zona ingresada - priorizar el select si tiene un valor
            let zona = document.getElementById('zonasExistentesSelect').value;
            
            // Si no se seleccionó una zona existente, usar el input de texto
            if (!zona) {
                zona = document.getElementById('zonaTrampaInput').value;
            }
            
            // Usar el ID manual si se proporcionó, sino generar uno temporal
            const trampaId = idTrampaManual || `TEMP-${Date.now().toString().slice(-6)}`;
            
            // Crear nueva trampa con zona
            const nuevaTrampa = {
                id: trampaId, // ID manual o temporal que será reemplazado por el generado en el servidor
                tipo: tipoTrampaSeleccionado,
                x: posicionTrampaX,
                y: posicionTrampaY,
                zona: zona || 'Sin zona' // Usar 'Sin zona' si no se proporciona una
            };

            // Agregar al array de puntos
            if (!window.puntos) window.puntos = [];
            window.puntos.push(nuevaTrampa);

            // Crear el marcador visual
            const marcador = marcarTrampa(nuevaTrampa);
            actualizarTablaTrampas();
            
            // Guardar en la base de datos
            guardarTrampaEnBD(nuevaTrampa);
            
            // Desactivar modo de agregar trampa después de colocar una
            modoEdicion = null;
            tipoTrampaSeleccionado = null;
            planoContainer.style.cursor = 'default';
            
            // Resetear estado visual del botón
            dropdownButton.classList.remove('active');
            dropdownButton.style.backgroundColor = '';
            
            // Ocultar el modal y limpiar los campos
            document.getElementById('modalZonaTrampa').classList.add('hidden');
            document.getElementById('idTrampaInput').value = '';
            document.getElementById('zonaTrampaInput').value = '';
            document.getElementById('zonasExistentesSelect').value = '';
        });
        
        // Función para cargar las zonas existentes en el select
        function cargarZonasExistentes() {
            const select = document.getElementById('zonasExistentesSelect');
            
            // Limpiar opciones existentes excepto la primera
            while (select.options.length > 1) {
                select.remove(1);
            }
            
            // Obtener las zonas desde la base de datos mediante AJAX
            fetch('<?= base_url('blueprints/obtener_zonas/' . $plano['id']) ?>', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && Array.isArray(data.zonas)) {
                    // Ordenar alfabéticamente y añadir al select
                    data.zonas.sort().forEach(nombreZona => {
                        if (nombreZona && nombreZona !== 'Sin zona') {
                            const option = document.createElement('option');
                            option.value = nombreZona;
                            option.textContent = nombreZona;
                            select.appendChild(option);
                        }
                    });
                } else {
                    console.log('No se encontraron zonas en la base de datos');
                }
                
                // También agregar las zonas locales que puedan no estar en la BD
                agregarZonasLocales(select);
            })
            .catch(error => {
                console.error('Error al cargar zonas desde la BD:', error);
                // Si hay un error, intentar cargar solo las zonas locales
                agregarZonasLocales(select);
            });
        }
        
        // Función auxiliar para agregar zonas de los datos locales
        function agregarZonasLocales(select) {
            // Crear un conjunto para almacenar nombres únicos de zonas
            const zonasUnicas = new Set();
            
            // Comprobar si hay opciones existentes para no añadir duplicados
            const opcionesExistentes = new Set();
            for (let i = 0; i < select.options.length; i++) {
                opcionesExistentes.add(select.options[i].value);
            }
            
            // Añadir nombres de zonas al conjunto
            if (window.zonas && Array.isArray(window.zonas)) {
                window.zonas.forEach(zona => {
                    if (zona.nombre && !opcionesExistentes.has(zona.nombre)) {
                        zonasUnicas.add(zona.nombre);
                    }
                });
            }
            
            // Si hay puntos con zonas, también añadirlos
            if (window.puntos && Array.isArray(window.puntos)) {
                window.puntos.forEach(punto => {
                    if (punto.zona && punto.zona !== 'Sin zona' && !opcionesExistentes.has(punto.zona)) {
                        zonasUnicas.add(punto.zona);
                    }
                });
            }
            
            // Convertir a array, ordenar y añadir al select
            Array.from(zonasUnicas).sort().forEach(nombreZona => {
                const option = document.createElement('option');
                option.value = nombreZona;
                option.textContent = nombreZona;
                select.appendChild(option);
            });
        }
        
        // Cargar zonas cuando se abre el modal
        document.addEventListener('click', function(e) {
            // Si se hace clic en el plano en modo agregarTrampa, cargar las zonas
            if (e.target.closest('#planoContainer') && modoEdicion === 'agregarTrampa' && tipoTrampaSeleccionado) {
                cargarZonasExistentes();
            }
        });
        
        // Sincronizar el select con el input
        document.getElementById('zonasExistentesSelect').addEventListener('change', function() {
            // Si se selecciona una zona existente, limpiar el input de nueva zona
            if (this.value) {
                document.getElementById('zonaTrampaInput').value = '';
            }
        });
        
        document.getElementById('zonaTrampaInput').addEventListener('input', function() {
            // Si se escribe en el input, limpiar la selección del select
            if (this.value) {
                document.getElementById('zonasExistentesSelect').value = '';
            }
        });
        
        // Botón cancelar del modal de zona
        document.getElementById('cancelarZonaTrampa').addEventListener('click', function() {
            document.getElementById('modalZonaTrampa').classList.add('hidden');
            document.getElementById('idTrampaInput').value = '';
            document.getElementById('zonaTrampaInput').value = '';
            document.getElementById('zonasExistentesSelect').value = '';
        });
        
        // Cerrar el modal al hacer clic fuera del contenido
        document.getElementById('modalZonaTrampa').addEventListener('click', function(e) {
            if (e.target === this) {
                document.getElementById('modalZonaTrampa').classList.add('hidden');
                document.getElementById('idTrampaInput').value = '';
                document.getElementById('zonaTrampaInput').value = '';
                document.getElementById('zonasExistentesSelect').value = '';
            }
        });

        // Agregar el evento para el filtro de tipo de trampa
        document.getElementById('filtroTipoTrampa').addEventListener('change', function() {
            actualizarTablaTrampas();
            
            // Resaltar visualmente las trampas filtradas
            const filtroTipo = this.value;
            
            // Quitar resaltado de todas las trampas
            document.querySelectorAll('.trap-marker').forEach(marker => {
                if (filtroTipo === '') {
                    // Si no hay filtro, mostrar todas las trampas con opacidad normal
                    marker.style.opacity = '1';
                } else {
                    // Si hay filtro, verificar si la trampa es del tipo seleccionado
                    const index = marker.dataset.index;
                    const punto = window.puntos[index];
                    
                    if (punto && punto.tipo === filtroTipo) {
                        // Si coincide con el filtro, mostrar con opacidad normal
                        marker.style.opacity = '1';
                    } else {
                        // Si no coincide, mostrar con opacidad reducida
                        marker.style.opacity = '0.3';
                    }
                }
            });
        });

        // --- INICIO: Lógica para incidencias múltiples ---
        let incidenciasTemp = [];

        function formatearTipoPlaga(tipoPlaga) {
            if (!tipoPlaga) return 'N/A';
            return tipoPlaga.split('_').map(palabra => 
                palabra.charAt(0).toUpperCase() + palabra.slice(1)
            ).join(' ');
        }
        
        function formatearFecha(fecha) {
            if (!fecha) return 'N/A';
            try {
                // Normalizar el formato de fecha (puede venir como "YYYY-MM-DD HH:MM:SS" o "YYYY-MM-DDTHH:MM")
                let fechaStr = fecha.toString().replace(' ', 'T');
                if (!fechaStr.includes('T')) {
                    fechaStr = fechaStr + 'T00:00';
                }
                // Asegurar que tenga segundos si no los tiene
                if (fechaStr.split(':').length === 2) {
                    fechaStr = fechaStr + ':00';
                }
                
                const fechaObj = new Date(fechaStr);
                if (isNaN(fechaObj.getTime())) {
                    return fecha; // Si no se puede parsear, devolver el valor original
                }
                
                const meses = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 
                              'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];
                const dia = fechaObj.getDate();
                const mes = meses[fechaObj.getMonth()];
                const anio = fechaObj.getFullYear();
                const hora = fechaObj.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
                return `${dia} de ${mes} de ${anio}, ${hora}`;
            } catch (e) {
                return fecha;
            }
        }
        
        function renderizarListaIncidencias() {
            const tbody = document.getElementById('incidenciasAgregadas');
            const contador = document.getElementById('contadorIncidencias');
            const filaVacia = document.getElementById('filaVacia');
            
            tbody.innerHTML = '';
            
            if (incidenciasTemp.length === 0) {
                tbody.innerHTML = '<tr id="filaVacia"><td colspan="8" class="px-4 py-8 text-center text-gray-400">No hay incidencias agregadas.</td></tr>';
                contador.textContent = '0';
                return;
            }
            
            contador.textContent = incidenciasTemp.length;
            
            incidenciasTemp.forEach((inc, idx) => {
                const tipoPlaga = inc.tipo_plaga || inc.tipo_plaga_select || '';
                const tipoPlagaDisplay = formatearTipoPlaga(tipoPlaga);
                const fechaDisplay = formatearFecha(inc.fecha_incidencia);
                // Formatear fecha para input datetime-local (YYYY-MM-DDTHH:MM)
                let fechaFormateada = '';
                if (inc.fecha_incidencia) {
                    let fechaStr = inc.fecha_incidencia.toString().replace(' ', 'T');
                    // Si tiene formato completo, tomar solo los primeros 16 caracteres (YYYY-MM-DDTHH:MM)
                    if (fechaStr.length >= 16) {
                        fechaFormateada = fechaStr.substring(0, 16);
                    } else if (fechaStr.length === 10) {
                        // Si solo tiene la fecha, agregar hora por defecto
                        fechaFormateada = fechaStr + 'T00:00';
                    } else {
                        fechaFormateada = fechaStr;
                    }
                }
                
                const tr = document.createElement('tr');
                tr.className = 'hover:bg-gray-50 transition-colors';
                tr.setAttribute('data-indice', idx);
                tr.innerHTML = `
                    <!-- Tipo de Plaga -->
                    <td class="px-4 py-3 border-b border-gray-100 text-gray-700 whitespace-nowrap">
                        <span class="display-mode-${idx}">${tipoPlagaDisplay}</span>
                        <select class="edit-mode-${idx} hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                data-field="tipo_plaga" data-indice="${idx}">
                            <option value="">Seleccione un tipo</option>
                            <option value="mosca" ${tipoPlaga === 'mosca' ? 'selected' : ''}>Mosca</option>
                            <option value="mosca_domestica" ${tipoPlaga === 'mosca_domestica' ? 'selected' : ''}>Mosca Doméstica</option>
                            <option value="mosca_fruta" ${tipoPlaga === 'mosca_fruta' ? 'selected' : ''}>Mosca De La Fruta</option>
                            <option value="mosca_drenaje" ${tipoPlaga === 'mosca_drenaje' ? 'selected' : ''}>Mosca De Drenaje</option>
                            <option value="mosca_metalica" ${tipoPlaga === 'mosca_metalica' ? 'selected' : ''}>Moscas Metálicas</option>
                            <option value="mosca_forida" ${tipoPlaga === 'mosca_forida' ? 'selected' : ''}>Mosca Forida</option>
                            <option value="palomilla_almacen" ${tipoPlaga === 'palomilla_almacen' ? 'selected' : ''}>Palomillas De Almacén</option>
                            <option value="otras_palomillas" ${tipoPlaga === 'otras_palomillas' ? 'selected' : ''}>Otras Palomillas</option>
                            <option value="gorgojo" ${tipoPlaga === 'gorgojo' ? 'selected' : ''}>Gorgojos</option>
                            <option value="otros_escarabajos" ${tipoPlaga === 'otros_escarabajos' ? 'selected' : ''}>Otros Escarabajos</option>
                            <option value="abeja" ${tipoPlaga === 'abeja' ? 'selected' : ''}>Abejas</option>
                            <option value="avispa" ${tipoPlaga === 'avispa' ? 'selected' : ''}>Avispas</option>
                            <option value="mosquito" ${tipoPlaga === 'mosquito' ? 'selected' : ''}>Mosquitos</option>
                            <option value="cucaracha" ${tipoPlaga === 'cucaracha' ? 'selected' : ''}>Cucaracha</option>
                            <option value="hormiga" ${tipoPlaga === 'hormiga' ? 'selected' : ''}>Hormiga</option>
                            <option value="roedor" ${tipoPlaga === 'roedor' ? 'selected' : ''}>Roedor</option>
                            <option value="Arañas" ${tipoPlaga === 'Arañas' ? 'selected' : ''}>Arañas</option>
                            <option value="Lagartija" ${tipoPlaga === 'Lagartija' ? 'selected' : ''}>Lagartijas</option>
                            <option value="otro" ${!['mosca', 'mosca_domestica', 'mosca_fruta', 'mosca_drenaje', 'mosca_metalica', 'mosca_forida', 'palomilla_almacen', 'otras_palomillas', 'gorgojo', 'otros_escarabajos', 'abeja', 'avispa', 'mosquito', 'cucaracha', 'hormiga', 'roedor', 'Arañas', 'Lagartija'].includes(tipoPlaga) && tipoPlaga ? 'selected' : ''}>Otro (especificar)</option>
                        </select>
                        <input type="text" class="edit-mode-${idx} hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm tipo-plaga-personalizado-${idx}" 
                               data-field="tipo_plaga_personalizado" data-indice="${idx}"
                               placeholder="Especifique el tipo de plaga"
                               value="${!['mosca', 'mosca_domestica', 'mosca_fruta', 'mosca_drenaje', 'mosca_metalica', 'mosca_forida', 'palomilla_almacen', 'otras_palomillas', 'gorgojo', 'otros_escarabajos', 'abeja', 'avispa', 'mosquito', 'cucaracha', 'hormiga', 'roedor', 'Arañas', 'Lagartija'].includes(tipoPlaga) && tipoPlaga ? tipoPlaga : ''}"
                               style="display: none;">
                    </td>
                    
                    <!-- Tipo de Incidencia -->
                    <td class="px-4 py-3 border-b border-gray-100 whitespace-nowrap">
                        <span class="display-mode-${idx} inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${inc.tipo_incidencia === 'Captura' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${inc.tipo_incidencia || 'N/A'}</span>
                        <select class="edit-mode-${idx} hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                data-field="tipo_incidencia" data-indice="${idx}">
                            <option value="Captura" ${inc.tipo_incidencia === 'Captura' ? 'selected' : ''}>Captura</option>
                            <option value="Hallazgo" ${inc.tipo_incidencia === 'Hallazgo' ? 'selected' : ''}>Hallazgo</option>
                        </select>
                    </td>
                    
                    <!-- Tipo de Insecto -->
                    <td class="px-4 py-3 border-b border-gray-100 text-gray-700 whitespace-nowrap">
                        <span class="display-mode-${idx}">${inc.tipo_insecto || 'N/A'}</span>
                        <select class="edit-mode-${idx} hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                data-field="tipo_insecto" data-indice="${idx}">
                            <option value="Volador" ${inc.tipo_insecto === 'Volador' ? 'selected' : ''}>Volador</option>
                            <option value="Rastrero" ${inc.tipo_insecto === 'Rastrero' ? 'selected' : ''}>Rastrero</option>
                        </select>
                    </td>
                    
                    <!-- Cantidad de Organismos -->
                    <td class="px-4 py-3 border-b border-gray-100 text-gray-700 whitespace-nowrap text-center">
                        <span class="display-mode-${idx}">
                            ${inc.cantidad_organismos ? `<span class="font-semibold text-blue-600">${inc.cantidad_organismos}</span>` : '<span class="text-gray-400">-</span>'}
                        </span>
                        <input type="number" class="edit-mode-${idx} hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm text-center" 
                               data-field="cantidad_organismos" data-indice="${idx}"
                               min="1" 
                               value="${inc.cantidad_organismos || ''}">
                    </td>
                    
                    <!-- Fecha de Incidencia -->
                    <td class="px-4 py-3 border-b border-gray-100 text-gray-700 whitespace-nowrap">
                        <span class="display-mode-${idx}">${fechaDisplay}</span>
                        <input type="datetime-local" class="edit-mode-${idx} hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                               data-field="fecha_incidencia" data-indice="${idx}"
                               value="${fechaFormateada}">
                    </td>
                    
                    <!-- Inspector -->
                    <td class="px-4 py-3 border-b border-gray-100 text-gray-700 whitespace-nowrap">
                        <span class="display-mode-${idx}">${inc.inspector || 'N/A'}</span>
                        <input type="text" class="edit-mode-${idx} hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                               data-field="inspector" data-indice="${idx}"
                               placeholder="Nombre del inspector"
                               value="${inc.inspector || ''}">
                    </td>
                    
                    <!-- Notas Adicionales -->
                    <td class="px-4 py-3 border-b border-gray-100 text-gray-700">
                        <div class="display-mode-${idx} max-w-xs" title="${inc.notas || ''}">${inc.notas || 'Sin notas'}</div>
                        <textarea class="edit-mode-${idx} hidden w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm" 
                                  data-field="notas" data-indice="${idx}"
                                  rows="2"
                                  placeholder="Notas adicionales">${inc.notas || ''}</textarea>
                    </td>
                    
                    <!-- Acciones -->
                    <td class="px-4 py-3 border-b border-gray-100 whitespace-nowrap">
                        <div class="display-mode-${idx} flex items-center gap-2">
                            <button onclick="editarIncidenciaEnCola(${idx})" 
                                    class="text-blue-600 hover:text-blue-800 font-medium text-sm flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Editar
                            </button>
                            <button onclick="eliminarIncidenciaDeLista(${idx})" 
                                    class="text-red-600 hover:text-red-800 font-medium text-sm flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Eliminar
                            </button>
                        </div>
                        <div class="edit-mode-${idx} hidden flex items-center gap-2">
                            <button onclick="guardarIncidenciaEnCola(${idx})" 
                                    class="text-green-600 hover:text-green-800 font-medium text-sm flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Guardar
                            </button>
                            <button onclick="cancelarEdicionEnCola(${idx})" 
                                    class="text-red-600 hover:text-red-800 font-medium text-sm flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancelar
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
                
                // Configurar evento para el select de tipo de plaga
                setTimeout(() => {
                    const selectTipoPlaga = tr.querySelector(`select[data-field="tipo_plaga"][data-indice="${idx}"]`);
                    const inputPersonalizado = tr.querySelector(`.tipo-plaga-personalizado-${idx}`);
                    
                    if (selectTipoPlaga && inputPersonalizado) {
                        selectTipoPlaga.addEventListener('change', function() {
                            if (this.value === 'otro') {
                                inputPersonalizado.style.display = 'block';
                                inputPersonalizado.setAttribute('required', 'required');
                            } else {
                                inputPersonalizado.style.display = 'none';
                                inputPersonalizado.removeAttribute('required');
                            }
                        });
                    }
                }, 100);
            });
        }
        
        // Función para editar incidencia en la cola
        window.editarIncidenciaEnCola = function(idx) {
            const fila = document.querySelector(`tr[data-indice="${idx}"]`);
            if (!fila) return;
            
            // Ocultar elementos de visualización
            fila.querySelectorAll(`.display-mode-${idx}`).forEach(el => el.classList.add('hidden'));
            // Mostrar elementos de edición
            fila.querySelectorAll(`.edit-mode-${idx}`).forEach(el => el.classList.remove('hidden'));
            
            // Manejar tipo de plaga personalizado
            const selectTipoPlaga = fila.querySelector(`select[data-field="tipo_plaga"][data-indice="${idx}"]`);
            const inputPersonalizado = fila.querySelector(`.tipo-plaga-personalizado-${idx}`);
            
            if (selectTipoPlaga && inputPersonalizado) {
                const tipoPlaga = incidenciasTemp[idx].tipo_plaga || incidenciasTemp[idx].tipo_plaga_select || '';
                if (!['mosca', 'mosca_domestica', 'mosca_fruta', 'mosca_drenaje', 'mosca_metalica', 'mosca_forida', 'palomilla_almacen', 'otras_palomillas', 'gorgojo', 'otros_escarabajos', 'abeja', 'avispa', 'mosquito', 'cucaracha', 'hormiga', 'roedor', 'Arañas', 'Lagartija'].includes(tipoPlaga) && tipoPlaga) {
                    selectTipoPlaga.value = 'otro';
                    inputPersonalizado.style.display = 'block';
                }
            }
        };
        
        // Función para guardar cambios en incidencia de la cola
        window.guardarIncidenciaEnCola = function(idx) {
            const fila = document.querySelector(`tr[data-indice="${idx}"]`);
            if (!fila || !incidenciasTemp[idx]) return;
            
            const campos = fila.querySelectorAll(`[data-field][data-indice="${idx}"]`);
            const incidencia = incidenciasTemp[idx];
            
            campos.forEach(campo => {
                const fieldName = campo.getAttribute('data-field');
                if (fieldName === 'tipo_plaga') {
                    const selectTipoPlaga = fila.querySelector(`select[data-field="tipo_plaga"][data-indice="${idx}"]`);
                    const inputPersonalizado = fila.querySelector(`.tipo-plaga-personalizado-${idx}`);
                    
                    if (selectTipoPlaga.value === 'otro' && inputPersonalizado && inputPersonalizado.value) {
                        incidencia.tipo_plaga = inputPersonalizado.value;
                        incidencia.tipo_plaga_select = 'otro';
                    } else {
                        incidencia.tipo_plaga = selectTipoPlaga.value;
                        incidencia.tipo_plaga_select = selectTipoPlaga.value;
                    }
                } else if (fieldName === 'fecha_incidencia') {
                    // Convertir datetime-local a formato correcto
                    const fechaValue = campo.value;
                    if (fechaValue) {
                        incidencia.fecha_incidencia = fechaValue.replace('T', ' ') + ':00';
                    }
                } else {
                    incidencia[fieldName] = campo.value;
                }
            });
            
            // Ocultar elementos de edición
            fila.querySelectorAll(`.edit-mode-${idx}`).forEach(el => el.classList.add('hidden'));
            // Mostrar elementos de visualización
            fila.querySelectorAll(`.display-mode-${idx}`).forEach(el => el.classList.remove('hidden'));
            
            // Re-renderizar para actualizar los valores mostrados
            renderizarListaIncidencias();
        };
        
        // Función para cancelar edición en cola
        window.cancelarEdicionEnCola = function(idx) {
            const fila = document.querySelector(`tr[data-indice="${idx}"]`);
            if (!fila) return;
            
            // Ocultar elementos de edición
            fila.querySelectorAll(`.edit-mode-${idx}`).forEach(el => el.classList.add('hidden'));
            // Mostrar elementos de visualización
            fila.querySelectorAll(`.display-mode-${idx}`).forEach(el => el.classList.remove('hidden'));
            
            // Re-renderizar para restaurar valores originales
            renderizarListaIncidencias();
        };

        // Hacer global la función para que funcione el botón eliminar
        window.eliminarIncidenciaDeLista = function(idx) {
            incidenciasTemp.splice(idx, 1);
            renderizarListaIncidencias();
        };

        document.getElementById('btnAgregarIncidenciaLista').addEventListener('click', function() {
            const form = document.getElementById('formIncidencia');
            const formData = new FormData(form);
            // Validar campos mínimos
            if (!formData.get('tipo_plaga') && !formData.get('tipo_plaga_select')) {
                mostrarMensaje('Debe seleccionar un tipo de plaga', 'error');
                return;
            }
            if (!formData.get('fecha_incidencia')) {
                mostrarMensaje('Debe ingresar la fecha de incidencia', 'error');
                return;
            }
            // Convertir FormData a objeto
            const obj = {};
            formData.forEach((v, k) => obj[k] = v);
            incidenciasTemp.push(obj);
            renderizarListaIncidencias();
            form.reset();
            document.getElementById('tipo_plaga_personalizado_container').style.display = 'none';
            document.getElementById('cantidad_organismos_container').style.display = 'block';
            document.getElementById('tipo_plaga_personalizado').removeAttribute('required');
            document.getElementById('cantidad_organismos').removeAttribute('required');
        });

        document.getElementById('btnGuardarTodasIncidencias').addEventListener('click', function() {
            if (incidenciasTemp.length === 0) {
                mostrarMensaje('No hay incidencias para guardar', 'error');
                return;
            }
            let guardadas = 0;
            let errores = 0;
            incidenciasTemp.forEach((inc, idx) => {
                const formData = new FormData();
                Object.entries(inc).forEach(([k, v]) => formData.append(k, v));
                fetch('<?= base_url('blueprints/guardar_incidencia') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        guardadas++;
                    } else {
                        errores++;
                    }
                    if (guardadas + errores === incidenciasTemp.length) {
                        mostrarMensaje(`${guardadas} incidencias guardadas, ${errores} errores`, errores ? 'error' : 'success');
                        incidenciasTemp = [];
                        renderizarListaIncidencias();
                        closeIncidenciaModal();
                    }
                })
                .catch(() => {
                    errores++;
                    if (guardadas + errores === incidenciasTemp.length) {
                        mostrarMensaje(`${guardadas} incidencias guardadas, ${errores} errores`, 'error');
                        incidenciasTemp = [];
                        renderizarListaIncidencias();
                        closeIncidenciaModal();
                    }
                });
            });
        });

        // Limpiar lista al cerrar modal
        const oldCloseIncidenciaModal = window.closeIncidenciaModal;
        window.closeIncidenciaModal = function() {
            incidenciasTemp = [];
            renderizarListaIncidencias();
            oldCloseIncidenciaModal();
        };
        
        // Funciones para editar incidencias
        window.editarIncidencia = function(incidenciaId) {
            // Buscar la incidencia en los datos cargados
            const incidencia = <?= json_encode($incidencias ?? []) ?>.find(i => i.id == incidenciaId);
            
            if (!incidencia) {
                mostrarMensaje('No se encontró la incidencia', 'error');
                return;
            }
            
            // Llenar el formulario con los datos de la incidencia
            document.getElementById('incidencia_id_editar').value = incidencia.id;
            document.getElementById('trampa_id_editar').value = incidencia.id_trampa;
            
            // Mostrar información de la trampa
            const trampaInfo = document.getElementById('trampaInfoEditar');
            trampaInfo.innerHTML = `<p class="text-sm text-gray-600"><strong>ID de Trampa:</strong> ${incidencia.id_trampa || 'N/A'}<br><strong>Ubicación:</strong> ${incidencia.trampa_ubicacion || 'N/A'}</p>`;
            
            // Tipo de plaga
            const tipoPlaga = incidencia.tipo_plaga || '';
            const tipoPlagaSelect = document.getElementById('tipo_plaga_select_editar');
            const tipoPlagaHidden = document.getElementById('tipo_plaga_editar');
            const tipoPlagaPersonalizado = document.getElementById('tipo_plaga_personalizado_editar');
            const tipoPlagaPersonalizadoContainer = document.getElementById('tipo_plaga_personalizado_container_editar');
            
            // Verificar si el tipo de plaga está en las opciones
            let encontrado = false;
            for (let option of tipoPlagaSelect.options) {
                if (option.value === tipoPlaga) {
                    tipoPlagaSelect.value = tipoPlaga;
                    tipoPlagaHidden.value = tipoPlaga;
                    encontrado = true;
                    tipoPlagaPersonalizadoContainer.style.display = 'none';
                    break;
                }
            }
            
            if (!encontrado && tipoPlaga) {
                // Si no está en las opciones, usar "otro" y poner el valor en el campo personalizado
                tipoPlagaSelect.value = 'otro';
                tipoPlagaPersonalizado.value = tipoPlaga;
                tipoPlagaHidden.value = tipoPlaga;
                tipoPlagaPersonalizadoContainer.style.display = 'block';
            }
            
            // Tipo de incidencia
            document.getElementById('tipo_incidencia_editar').value = incidencia.tipo_incidencia || 'Captura';
            
            // Tipo de insecto
            document.getElementById('tipo_insecto_editar').value = incidencia.tipo_insecto || 'Volador';
            
            // Fecha - convertir al formato datetime-local
            if (incidencia.fecha) {
                const fecha = new Date(incidencia.fecha);
                const fechaLocal = new Date(fecha.getTime() - fecha.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
                document.getElementById('fecha_incidencia_editar').value = fechaLocal;
            }
            
            // Inspector
            document.getElementById('inspector_editar').value = incidencia.inspector || '';
            
            // Notas
            document.getElementById('notas_editar').value = incidencia.notas || '';
            
            // Cantidad de organismos
            const cantidadContainer = document.getElementById('cantidad_organismos_container_editar');
            const cantidadInput = document.getElementById('cantidad_organismos_editar');
            if (incidencia.cantidad_organismos) {
                cantidadInput.value = incidencia.cantidad_organismos;
                cantidadContainer.style.display = 'block';
            } else {
                cantidadContainer.style.display = 'block';
            }
            
            // Mostrar el modal
            document.getElementById('modalEditarIncidencia').classList.remove('hidden');
        };
        
        window.closeEditarIncidenciaModal = function() {
            document.getElementById('modalEditarIncidencia').classList.add('hidden');
            // Limpiar el formulario
            document.getElementById('formEditarIncidencia').reset();
            document.getElementById('tipo_plaga_personalizado_container_editar').style.display = 'none';
        };
        
        // Manejar cambio del select de tipo de plaga en el modal de edición
        document.getElementById('tipo_plaga_select_editar').addEventListener('change', function() {
            const tipoPlagaPersonalizadoContainer = document.getElementById('tipo_plaga_personalizado_container_editar');
            const cantidadContainer = document.getElementById('cantidad_organismos_container_editar');
            const tipoPlagaHidden = document.getElementById('tipo_plaga_editar');
            
            tipoPlagaHidden.value = this.value;
            
            if (this.value === 'otro') {
                tipoPlagaPersonalizadoContainer.style.display = 'block';
                document.getElementById('tipo_plaga_personalizado_editar').setAttribute('required', 'required');
                tipoPlagaHidden.value = '';
            } else {
                tipoPlagaPersonalizadoContainer.style.display = 'none';
                document.getElementById('tipo_plaga_personalizado_editar').removeAttribute('required');
            }
            
            if (this.value) {
                cantidadContainer.style.display = 'block';
                document.getElementById('cantidad_organismos_editar').setAttribute('required', 'required');
            } else {
                cantidadContainer.style.display = 'none';
                document.getElementById('cantidad_organismos_editar').removeAttribute('required');
            }
        });
        
        // Actualizar campo oculto cuando se escribe en el campo personalizado
        document.getElementById('tipo_plaga_personalizado_editar').addEventListener('input', function() {
            document.getElementById('tipo_plaga_editar').value = this.value;
        });
        
        // Guardar cambios de la incidencia editada
        document.getElementById('btnGuardarIncidenciaEditar').addEventListener('click', function() {
            const form = document.getElementById('formEditarIncidencia');
            const formData = new FormData(form);
            
            // Verificar que se haya seleccionado un tipo de plaga
            if (!formData.get('tipo_plaga_editar')) {
                mostrarMensaje('Debe seleccionar un tipo de plaga', 'error');
                return;
            }
            
            // Verificar que se haya proporcionado una fecha
            if (!formData.get('fecha_incidencia_editar')) {
                mostrarMensaje('Debe ingresar la fecha de incidencia', 'error');
                return;
            }
            
            // Deshabilitar el botón mientras se guarda
            const btnGuardar = this;
            btnGuardar.disabled = true;
            btnGuardar.textContent = 'Guardando...';
            
            fetch('<?= base_url('blueprints/actualizar_incidencia') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje('Incidencia actualizada correctamente', 'success');
                    closeEditarIncidenciaModal();
                    // Recargar la página para mostrar los cambios
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    mostrarMensaje(data.message || 'Error al actualizar la incidencia', 'error');
                    btnGuardar.disabled = false;
                    btnGuardar.textContent = 'Guardar Cambios';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al actualizar la incidencia', 'error');
                btnGuardar.disabled = false;
                btnGuardar.textContent = 'Guardar Cambios';
            });
        });
        
        // Modo edición inline de la tabla de incidencias
        let modoEdicionActivo = false;
        
        window.toggleModoEdicion = function() {
            modoEdicionActivo = !modoEdicionActivo;
            const btnModoEdicion = document.getElementById('btnModoEdicion');
            const textoModoEdicion = document.getElementById('textoModoEdicion');
            const filas = document.querySelectorAll('#tbodyIncidencias tr');
            
            if (modoEdicionActivo) {
                // Activar modo edición
                btnModoEdicion.classList.add('hidden');
                document.getElementById('botonesModoEdicion').classList.remove('hidden');
                
                filas.forEach(fila => {
                    // Ocultar elementos de visualización
                    fila.querySelectorAll('.display-mode').forEach(el => el.classList.add('hidden'));
                    // Mostrar elementos de edición
                    fila.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('hidden'));
                    
                    // Asegurar que el campo de fecha tenga el valor correcto
                    const campoFecha = fila.querySelector('input[data-field="fecha_incidencia"]');
                    if (campoFecha && !campoFecha.value) {
                        // Si el campo está vacío, intentar obtener el valor del data-original-data
                        const originalData = JSON.parse(fila.getAttribute('data-original-data') || '{}');
                        if (originalData.fecha) {
                            campoFecha.value = originalData.fecha;
                        }
                    }
                    
                    // Manejar select de tipo de plaga
                    const selectTipoPlaga = fila.querySelector('select[data-field="tipo_plaga"]');
                    const inputTipoPlagaPersonalizado = fila.querySelector('.tipo-plaga-personalizado');
                    const divTipoPlagaPersonalizado = inputTipoPlagaPersonalizado ? inputTipoPlagaPersonalizado.closest('div') : null;
                    
                    if (selectTipoPlaga && inputTipoPlagaPersonalizado) {
                        // Verificar si el valor actual no está en las opciones
                        const valorActual = selectTipoPlaga.value;
                        if (valorActual === 'otro' || (!valorActual && inputTipoPlagaPersonalizado.value)) {
                            if (divTipoPlagaPersonalizado) {
                                divTipoPlagaPersonalizado.style.display = 'block';
                                inputTipoPlagaPersonalizado.style.display = 'block';
                            }
                            inputTipoPlagaPersonalizado.setAttribute('required', 'required');
                        }
                        
                        // Remover listeners anteriores si existen
                        const nuevoSelect = selectTipoPlaga.cloneNode(true);
                        selectTipoPlaga.parentNode.replaceChild(nuevoSelect, selectTipoPlaga);
                        
                        nuevoSelect.addEventListener('change', function() {
                            if (this.value === 'otro') {
                                if (divTipoPlagaPersonalizado) {
                                    divTipoPlagaPersonalizado.style.display = 'block';
                                    inputTipoPlagaPersonalizado.style.display = 'block';
                                }
                                inputTipoPlagaPersonalizado.setAttribute('required', 'required');
                            } else {
                                if (divTipoPlagaPersonalizado) {
                                    divTipoPlagaPersonalizado.style.display = 'none';
                                    inputTipoPlagaPersonalizado.style.display = 'none';
                                }
                                inputTipoPlagaPersonalizado.removeAttribute('required');
                            }
                        });
                    }
                });
            } else {
                // Desactivar modo edición (esta función solo se llama desde confirmarDesactivarModoEdicionConfirmado)
                btnModoEdicion.classList.remove('hidden');
                document.getElementById('botonesModoEdicion').classList.add('hidden');
                
                filas.forEach(fila => {
                    // Mostrar elementos de visualización
                    fila.querySelectorAll('.display-mode').forEach(el => el.classList.remove('hidden'));
                    // Ocultar elementos de edición
                    fila.querySelectorAll('.edit-mode').forEach(el => el.classList.add('hidden'));
                    
                    // Restaurar valores originales
                    cancelarEdicionFila(fila);
                });
            }
        };
        
        window.guardarFilaIncidencia = function(button) {
            const fila = button.closest('tr');
            const incidenciaId = fila.getAttribute('data-incidencia-id');
            const campos = fila.querySelectorAll('[data-field]');
            
            // Recopilar datos
            const datos = {
                incidencia_id: incidenciaId
            };
            
            // Obtener el campo de fecha primero para validarlo
            const campoFecha = fila.querySelector('input[data-field="fecha_incidencia"]');
            const valorFecha = campoFecha ? campoFecha.value.trim() : '';
            
            campos.forEach(campo => {
                const fieldName = campo.getAttribute('data-field');
                if (fieldName === 'tipo_plaga') {
                    const selectTipoPlaga = fila.querySelector('select[data-field="tipo_plaga"]');
                    const inputPersonalizado = fila.querySelector('.tipo-plaga-personalizado');
                    
                    if (selectTipoPlaga.value === 'otro' && inputPersonalizado && inputPersonalizado.value) {
                        datos['tipo_plaga_editar'] = inputPersonalizado.value;
                    } else {
                        datos['tipo_plaga_editar'] = selectTipoPlaga.value;
                    }
                } else if (fieldName === 'fecha_incidencia') {
                    // Ya lo capturamos arriba, lo agregamos aquí
                    datos['fecha_incidencia_editar'] = valorFecha;
                } else {
                    datos[fieldName + '_editar'] = campo.value;
                }
            });
            
            // Validar campos requeridos
            if (!datos['tipo_plaga_editar'] || datos['tipo_plaga_editar'].trim() === '') {
                mostrarMensaje('Debe seleccionar un tipo de plaga', 'error');
                return;
            }
            
            // Validar fecha - verificar que tenga un valor válido
            if (!valorFecha || valorFecha === '' || valorFecha === 'null' || valorFecha === 'undefined') {
                mostrarMensaje('Debe ingresar la fecha de incidencia', 'error');
                return;
            }
            
            // Asegurar que siempre se envíe como fecha_incidencia_editar
            datos['fecha_incidencia_editar'] = valorFecha;
            
            // Deshabilitar botón mientras se guarda
            button.disabled = true;
            button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Guardando...';
            
            // Enviar datos
            const formData = new FormData();
            Object.keys(datos).forEach(key => {
                formData.append(key, datos[key]);
            });
            
            fetch('<?= base_url('blueprints/actualizar_incidencia') ?>', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarMensaje('Incidencia actualizada correctamente', 'success');
                    // Recargar la página para mostrar los cambios
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    mostrarMensaje(data.message || 'Error al actualizar la incidencia', 'error');
                    button.disabled = false;
                    button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Guardar';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarMensaje('Error al actualizar la incidencia', 'error');
                button.disabled = false;
                button.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg> Guardar';
            });
        };
        
        window.cancelarEdicionFila = function(fila) {
            if (!fila) return;
            
            // Restaurar valores originales desde data-original-data
            const originalData = JSON.parse(fila.getAttribute('data-original-data') || '{}');
            const campos = fila.querySelectorAll('[data-field]');
            
            campos.forEach(campo => {
                const fieldName = campo.getAttribute('data-field');
                if (fieldName === 'tipo_plaga') {
                    const selectTipoPlaga = fila.querySelector('select[data-field="tipo_plaga"]');
                    const inputPersonalizado = fila.querySelector('.tipo-plaga-personalizado');
                    
                    // Verificar si el valor original está en las opciones
                    const valorOriginal = originalData['tipo_plaga'] || '';
                    let encontrado = false;
                    for (let option of selectTipoPlaga.options) {
                        if (option.value === valorOriginal) {
                            selectTipoPlaga.value = valorOriginal;
                            encontrado = true;
                            if (inputPersonalizado) {
                                const divPersonalizado = inputPersonalizado.closest('div');
                                if (divPersonalizado) {
                                    divPersonalizado.style.display = 'none';
                                    inputPersonalizado.style.display = 'none';
                                }
                            }
                            break;
                        }
                    }
                    
                    if (!encontrado && valorOriginal) {
                        selectTipoPlaga.value = 'otro';
                        if (inputPersonalizado) {
                            inputPersonalizado.value = valorOriginal;
                            const divPersonalizado = inputPersonalizado.closest('div');
                            if (divPersonalizado) {
                                divPersonalizado.style.display = 'block';
                                inputPersonalizado.style.display = 'block';
                            }
                        }
                    } else {
                        const divPersonalizado = inputPersonalizado ? inputPersonalizado.closest('div') : null;
                        if (divPersonalizado) {
                            divPersonalizado.style.display = 'none';
                            inputPersonalizado.style.display = 'none';
                        }
                    }
                } else {
                    const valorOriginal = originalData[fieldName] || '';
                    if (campo.tagName === 'INPUT' || campo.tagName === 'TEXTAREA') {
                        campo.value = valorOriginal;
                    } else if (campo.tagName === 'SELECT') {
                        campo.value = valorOriginal;
                    }
                }
            });
        };
        
        // Función para comparar valores y detectar cambios
        function valoresDiferentes(valorActual, valorOriginal) {
            // Normalizar valores para comparación
            const actual = String(valorActual || '').trim();
            const original = String(valorOriginal || '').trim();
            return actual !== original;
        }
        
        // Función para guardar todos los cambios (solo los modificados)
        window.guardarTodosLosCambios = function() {
            const filas = document.querySelectorAll('#tbodyIncidencias tr');
            const cambios = [];
            const errores = [];
            
            // Recopilar solo los cambios de filas modificadas
            filas.forEach(fila => {
                const incidenciaId = fila.getAttribute('data-incidencia-id');
                const originalData = JSON.parse(fila.getAttribute('data-original-data') || '{}');
                const campos = fila.querySelectorAll('[data-field]');
                
                // Obtener valores actuales
                const valoresActuales = {};
                
                // Obtener el campo de fecha primero para validarlo
                const campoFecha = fila.querySelector('input[data-field="fecha_incidencia"]');
                const valorFecha = campoFecha ? campoFecha.value.trim() : '';
                
                // Recopilar todos los valores actuales
                campos.forEach(campo => {
                    const fieldName = campo.getAttribute('data-field');
                    if (fieldName === 'tipo_plaga') {
                        const selectTipoPlaga = fila.querySelector('select[data-field="tipo_plaga"]');
                        const inputPersonalizado = fila.querySelector('.tipo-plaga-personalizado');
                        
                        if (selectTipoPlaga.value === 'otro' && inputPersonalizado && inputPersonalizado.value) {
                            valoresActuales['tipo_plaga'] = inputPersonalizado.value;
                        } else {
                            valoresActuales['tipo_plaga'] = selectTipoPlaga.value;
                        }
                    } else if (fieldName === 'fecha_incidencia') {
                        valoresActuales['fecha'] = valorFecha;
                    } else {
                        valoresActuales[fieldName] = campo.value;
                    }
                });
                
                // Validar campos requeridos (solo si hay cambios o si es necesario)
                if (!valoresActuales['tipo_plaga'] || valoresActuales['tipo_plaga'].trim() === '') {
                    errores.push(`Incidencia ${incidenciaId}: Debe seleccionar un tipo de plaga`);
                    return;
                }
                
                if (!valoresActuales['fecha'] || valoresActuales['fecha'] === '' || valoresActuales['fecha'] === 'null' || valoresActuales['fecha'] === 'undefined') {
                    errores.push(`Incidencia ${incidenciaId}: Debe ingresar la fecha de incidencia`);
                    return;
                }
                
                // Detectar si hay cambios comparando con valores originales
                let hayCambios = false;
                const camposModificados = {};
                
                // Comparar cada campo
                Object.keys(valoresActuales).forEach(key => {
                    const valorOriginal = originalData[key] || '';
                    const valorActual = valoresActuales[key] || '';
                    
                    if (valoresDiferentes(valorActual, valorOriginal)) {
                        hayCambios = true;
                        camposModificados[key] = valorActual;
                    }
                });
                
                // Solo agregar a cambios si hay diferencias
                if (hayCambios) {
                    const datos = {
                        incidencia_id: incidenciaId,
                        tipo_plaga_editar: valoresActuales['tipo_plaga'],
                        tipo_incidencia_editar: valoresActuales['tipo_incidencia'] || originalData['tipo_incidencia'] || 'Captura',
                        tipo_insecto_editar: valoresActuales['tipo_insecto'] || originalData['tipo_insecto'] || 'Volador',
                        cantidad_organismos_editar: valoresActuales['cantidad_organismos'] || originalData['cantidad_organismos'] || null,
                        fecha_incidencia_editar: valoresActuales['fecha'],
                        inspector_editar: valoresActuales['inspector'] || originalData['inspector'] || '',
                        notas_editar: valoresActuales['notas'] || originalData['notas'] || ''
                    };
                    cambios.push(datos);
                }
            });
            
            // Si hay errores, mostrarlos
            if (errores.length > 0) {
                mostrarMensaje('Hay errores en algunos registros:\n' + errores.join('\n'), 'error');
                return;
            }
            
            if (cambios.length === 0) {
                mostrarMensaje('No hay cambios para guardar', 'info');
                return;
            }
            
            // Deshabilitar botón mientras se guarda
            const btnGuardarTodos = document.getElementById('btnGuardarTodos');
            const textoOriginal = btnGuardarTodos.innerHTML;
            btnGuardarTodos.disabled = true;
            btnGuardarTodos.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg> Guardando...';
            
            // Guardar todos los cambios en paralelo
            const promesas = cambios.map(datos => {
                const formData = new FormData();
                Object.keys(datos).forEach(key => {
                    formData.append(key, datos[key]);
                });
                
                return fetch('<?= base_url('blueprints/actualizar_incidencia') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || 'Error al actualizar');
                    }
                    return data;
                });
            });
            
            Promise.all(promesas)
                .then(results => {
                    mostrarMensaje(`${results.length} incidencia(s) actualizada(s) correctamente de ${filas.length} total`, 'success');
                    // Recargar la página para mostrar los cambios
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarMensaje('Error al guardar algunos cambios: ' + error.message, 'error');
                    btnGuardarTodos.disabled = false;
                    btnGuardarTodos.innerHTML = textoOriginal;
                });
        };
        
        // Función para confirmar desactivar modo edición
        window.confirmarDesactivarModoEdicion = function() {
            document.getElementById('modalConfirmarDesactivar').classList.remove('hidden');
        };
        
        // Función para cerrar el modal de confirmación
        window.cerrarModalConfirmarDesactivar = function() {
            document.getElementById('modalConfirmarDesactivar').classList.add('hidden');
        };
        
        // Función para desactivar modo edición confirmado
        window.desactivarModoEdicionConfirmado = function() {
            cerrarModalConfirmarDesactivar();
            modoEdicionActivo = false;
            toggleModoEdicion();
        };
        
        // Funciones para filtrar la tabla de incidencias
        window.aplicarFiltros = function() {
            const filtroIdTrampa = document.getElementById('filtroIdTrampa').value.toLowerCase().trim();
            const filtroTipoPlaga = document.getElementById('filtroTipoPlaga').value;
            const filtroTipoIncidencia = document.getElementById('filtroTipoIncidencia').value;
            const filtroTipoInsecto = document.getElementById('filtroTipoInsecto').value;
            
            const filas = document.querySelectorAll('#tbodyIncidencias tr');
            let filasVisibles = 0;
            
            filas.forEach(fila => {
                let mostrar = true;
                
                // Filtro por ID de Trampa
                if (filtroIdTrampa) {
                    const idTrampa = fila.querySelector('td:first-child span').textContent.toLowerCase().trim();
                    if (!idTrampa.includes(filtroIdTrampa)) {
                        mostrar = false;
                    }
                }
                
                // Filtro por Tipo de Plaga
                if (mostrar && filtroTipoPlaga) {
                    const tipoPlagaCell = fila.querySelector('td:nth-child(2)');
                    const tipoPlagaValue = tipoPlagaCell.getAttribute('data-tipo-plaga') || '';
                    if (tipoPlagaValue !== filtroTipoPlaga) {
                        mostrar = false;
                    }
                }
                
                // Filtro por Tipo de Incidencia
                if (mostrar && filtroTipoIncidencia) {
                    const tipoIncidenciaCell = fila.querySelector('td:nth-child(3)');
                    const tipoIncidenciaValue = tipoIncidenciaCell.getAttribute('data-tipo-incidencia') || '';
                    if (tipoIncidenciaValue !== filtroTipoIncidencia) {
                        mostrar = false;
                    }
                }
                
                // Filtro por Tipo de Insecto
                if (mostrar && filtroTipoInsecto) {
                    const tipoInsectoCell = fila.querySelector('td:nth-child(4)');
                    const tipoInsectoValue = tipoInsectoCell.getAttribute('data-tipo-insecto') || '';
                    if (tipoInsectoValue !== filtroTipoInsecto) {
                        mostrar = false;
                    }
                }
                
                // Mostrar u ocultar fila
                if (mostrar) {
                    fila.style.display = '';
                    filasVisibles++;
                } else {
                    fila.style.display = 'none';
                }
            });
            
            // Actualizar contador
            document.getElementById('incidenciasVisibles').textContent = filasVisibles;
        };
        
        window.limpiarFiltros = function() {
            document.getElementById('filtroIdTrampa').value = '';
            document.getElementById('filtroTipoPlaga').value = '';
            document.getElementById('filtroTipoIncidencia').value = '';
            document.getElementById('filtroTipoInsecto').value = '';
            aplicarFiltros();
        };
        
        // Render inicial
        renderizarListaIncidencias();
        // --- FIN: Lógica para incidencias múltiples ---
    });
</script>

<?= $this->endSection() ?> 