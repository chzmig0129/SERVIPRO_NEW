<?= $this->extend('layouts/main') ?>

<?php
// Función helper para normalizar nombres: quitar guiones bajos y capitalizar
function normalizarNombre($texto) {
    if (empty($texto)) return $texto;
    // Reemplazar guiones bajos con espacios
    $texto = str_replace('_', ' ', $texto);
    // Capitalizar la primera letra de cada palabra
    return ucwords(strtolower($texto));
}

// Función helper para formatear fecha en español: "mes día de año hora:minuto"
function formatearFechaEspanol($fecha) {
    if (empty($fecha)) return '';
    
    // Nombres de los meses en español
    $meses = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
    ];
    
    // Convertir la fecha a timestamp
    $timestamp = strtotime($fecha);
    if ($timestamp === false) return $fecha;
    
    // Obtener día, mes, año, hora y minuto
    $dia = date('j', $timestamp); // día sin cero inicial
    $mes = date('n', $timestamp); // mes numérico sin cero inicial
    $anio = date('Y', $timestamp);
    $hora = date('H:i', $timestamp);
    
    // Formatear: "mes día de año hora:minuto"
    return $meses[$mes] . ' ' . $dia . ' de ' . $anio . ' ' . $hora;
}
?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-7xl mx-auto px-4">
    
    <!-- Encabezado con selector y botón de reporte -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl p-6 text-white shadow-lg text-center mb-6">
        <div class="flex flex-col items-center justify-center">
            <h1 class="text-3xl font-bold text-white mb-2">Dashboard de Plantas</h1>
            <p class="text-blue-100 mb-4">Análisis y métricas detalladas por planta</p>
            
            <div class="flex flex-col md:flex-row gap-3 mt-2">
                <select id="sede-selector" name="sede_id" class="w-full md:w-64 p-2 border border-white rounded-lg bg-white text-blue-700 font-medium focus:ring-2 focus:ring-white/50 focus:border-white/50 transition-all" onchange="cambiarSede(this.value)">
                <?php if(empty($sedes)): ?>
                    <option>No hay plantas disponibles</option>
                <?php else: ?>
                    <option value="">Seleccione una planta</option>
                    <?php foreach($sedes as $sede): ?>
                        <option value="<?= $sede['id'] ?>" <?= ($sedeSeleccionada == $sede['id']) ? 'selected' : '' ?>><?= esc($sede['nombre']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
                </select>
                
                <div class="flex gap-2">
                    <button onclick="descargarPDF()" class="flex items-center justify-center gap-2 px-4 py-2 bg-white text-blue-700 rounded-lg hover:bg-blue-50 transition-colors font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Exportar PDF
                    </button>
                    <button onclick="exportarAPowerPoint()" class="flex items-center justify-center gap-2 px-4 py-2 bg-white text-blue-700 rounded-lg hover:bg-blue-50 transition-colors font-medium">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                            <polyline points="14 2 14 8 20 8"/>
                            <path d="M12 18v-6"/>
                            <path d="M8 18v-1"/>
                            <path d="M16 18v-3"/>
                        </svg>
                        Exportar PowerPoint
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Banner de filtro de fechas -->
    <?php if(isset($filtroFechaAplicado) && $filtroFechaAplicado): ?>
    <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-lg shadow-sm">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-blue-800">Filtro de tiempo aplicado</h3>
                <p class="text-blue-700"><?= $mensajeFiltroFecha ?></p>
                <div class="mt-2">
                    <a href="<?= base_url('locations') ?>?<?= !empty($sedeSeleccionada) ? 'sede_id='.$sedeSeleccionada : '' ?>" class="text-sm font-medium text-blue-600 hover:text-blue-800 underline">
                        Quitar filtro de fechas
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Mostrar mensaje de error si existe -->
    <?php if(isset($mensaje_error)): ?>
    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded-lg" role="alert">
        <p><?= $mensaje_error ?></p>
    </div>
    <?php endif; ?>

    <!-- Grid de resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Tarjeta: Total de Trampas -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Total de Trampas en la Planta</h3>
            <p class="text-3xl font-bold text-blue-600"><?= $totalTrampasSede; ?></p>
            <p class="text-sm text-gray-500">trampas instaladas</p>
        </div>
        
        <!-- Tarjeta: Total de Incidencias -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Total de Incidencias en la Planta</h3>
            <p class="text-3xl font-bold text-amber-600">
                <?php 
                    $sumaTotal = array_sum(array_column($totalIncidenciasPorTipo, 'total'));
                    echo $sumaTotal;
                ?>
            </p>
            <p class="text-sm text-gray-500">incidencias registradas</p>
        </div>
        
        <!-- Tarjeta: Planos Disponibles -->
        <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-100">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Planos Disponibles</h3>
            <p class="text-3xl font-bold text-green-600"><?= count($planos); ?></p>
            <p class="text-sm text-gray-500">planos de ubicación</p>
        </div>
    </div>
    
    <!-- Valor oculto para el total real de trampas -->
    <span id="total-trampas-real" style="display:none;"><?= $totalTrampasSede ?></span>
    
    <!-- Selección de Gráficas para el Reporte -->
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Selección de Gráficas para el Reporte</h3>
        <p class="text-gray-600 mb-4">Selecciona las gráficas que deseas incluir en el reporte PDF:</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <input type="checkbox" id="plagasMayorPresencia" name="graficas[]" value="plagasMayorPresencia" class="mt-1 h-5 w-5 text-blue-600 rounded" checked>
                    <div>
                        <label for="plagasMayorPresencia" class="block text-gray-800 font-medium">Plagas con Mayor Presencia</label>
                        <p class="text-sm text-gray-600">Muestra las plagas que tienen mayor presencia en la sede.</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <input type="checkbox" id="areasMayorIncidencia" name="graficas[]" value="areasMayorIncidencia" class="mt-1 h-5 w-5 text-blue-600 rounded" checked>
                    <div>
                        <label for="areasMayorIncidencia" class="block text-gray-800 font-medium">Áreas con Mayor Incidencia</label>
                        <p class="text-sm text-gray-600">Muestra las áreas que presentan mayor incidencia de plagas.</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <input type="checkbox" id="trampasMayorCaptura" name="graficas[]" value="trampasMayorCaptura" class="mt-1 h-5 w-5 text-blue-600 rounded" checked>
                    <div>
                        <label for="trampasMayorCaptura" class="block text-gray-800 font-medium">Trampas con Mayor Captura</label>
                        <p class="text-sm text-gray-600">Muestra las trampas que han capturado más organismos.</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <input type="checkbox" id="areasCapturasPorPlaga" name="graficas[]" value="areasCapturasPorPlaga" class="mt-1 h-5 w-5 text-blue-600 rounded" checked>
                    <div>
                        <label for="areasCapturasPorPlaga" class="block text-gray-800 font-medium">Áreas que Presentaron Capturas</label>
                        <p class="text-sm text-gray-600">Muestra las áreas donde se han registrado capturas por tipo de plaga.</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <input type="checkbox" id="incidenciasTipo" name="graficas[]" value="incidenciasTipo" class="mt-1 h-5 w-5 text-blue-600 rounded" checked>
                    <div>
                        <label for="incidenciasTipo" class="block text-gray-800 font-medium">Incidencias por Tipo y Mes</label>
                        <p class="text-sm text-gray-600">Muestra la distribución de incidencias por tipo y por mes.</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 hover:shadow-md transition-all">
                <div class="flex items-start space-x-3">
                    <input type="checkbox" id="trampasPorUbicacion" name="graficas[]" value="trampasPorUbicacion" class="mt-1 h-5 w-5 text-blue-600 rounded" checked>
                    <div>
                        <label for="trampasPorUbicacion" class="block text-gray-800 font-medium">Distribución de Trampas</label>
                        <p class="text-sm text-gray-600">Muestra la distribución de trampas por ubicación en la sede.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 flex justify-between">
            <button id="seleccionar-todos" class="px-4 py-2 bg-blue-100 text-blue-800 rounded-lg hover:bg-blue-200 transition-colors text-sm">Seleccionar Todos</button>
            <button id="deseleccionar-todos" class="px-4 py-2 bg-gray-100 text-gray-800 rounded-lg hover:bg-gray-200 transition-colors text-sm">Deseleccionar Todos</button>
        </div>
    </div>

    <!-- Detalle de Trampas -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Detalle de Trampas en la Planta</h3>
        
        <!-- Filtros para la tabla de trampas -->
        <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Filtro por tipo de trampa -->
                <div>
                    <label for="filtro-tipo-trampa" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por tipo de trampa</label>
                    <select id="filtro-tipo-trampa" class="w-full p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">Todos los tipos</option>
                        <?php 
                        $tiposTrampa = [];
                        foreach ($trampasDetalle as $trampa) {
                            if (!in_array($trampa['tipo'], $tiposTrampa)) {
                                $tiposTrampa[] = $trampa['tipo'];
                            }
                        }
                        sort($tiposTrampa);
                        foreach ($tiposTrampa as $tipo): ?>
                            <option value="<?= $tipo ?>"><?= normalizarNombre($tipo) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Filtro por ubicación -->
                <div>
                    <label for="filtro-ubicacion" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por ubicación</label>
                    <select id="filtro-ubicacion" class="w-full p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">Todas las ubicaciones</option>
                        <?php 
                        $ubicaciones = [];
                        foreach ($trampasDetalle as $trampa) {
                            if (!in_array($trampa['ubicacion'], $ubicaciones)) {
                                $ubicaciones[] = $trampa['ubicacion'];
                            }
                        }
                        sort($ubicaciones);
                        foreach ($ubicaciones as $ubicacion): ?>
                            <option value="<?= $ubicacion ?>"><?= $ubicacion ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Filtro por plano -->
                <div>
                    <label for="filtro-plano" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por plano</label>
                    <select id="filtro-plano" class="w-full p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <option value="">Todos los planos</option>
                        <?php if (!empty($planos)): ?>
                            <?php foreach ($planos as $plano): ?>
                                <option value="<?= $plano['id'] ?>"><?= esc($plano['nombre']) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            
            <!-- Filtro por rango de fechas para trampas -->
            <div class="mt-4">
                <p class="block text-sm font-medium text-gray-700 mb-2">Filtrar por rango de fechas</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="filtro-fecha-inicio-trampas" class="block text-sm text-gray-600 mb-1">Desde</label>
                        <input type="date" id="filtro-fecha-inicio-trampas" class="w-full p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <p class="text-xs text-gray-500 mt-1">Fecha de inicio para filtrar trampas</p>
                    </div>
                    <div>
                        <label for="filtro-fecha-fin-trampas" class="block text-sm text-gray-600 mb-1">Hasta</label>
                        <input type="date" id="filtro-fecha-fin-trampas" class="w-full p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                        <p class="text-xs text-gray-500 mt-1">Fecha final para filtrar trampas</p>
                    </div>
                </div>
            </div>
            
            <!-- Contador de resultados y botón para limpiar filtros -->
            <div class="flex justify-between mt-3">
                <div id="contador-resultados" class="text-sm text-gray-600">
                    Mostrando <span id="cantidad-trampas"><?= count($trampasDetalle) ?></span> trampas
                </div>
                <button id="limpiar-filtros" class="px-3 py-1 text-sm text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Limpiar filtros
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto mb-6">
            <table class="min-w-full bg-white border border-gray-300">
        <thead>
                    <tr class="bg-gray-100">
                        <th class="py-3 px-4 border text-left font-semibold text-gray-700">ID</th>
                        <th class="py-3 px-4 border text-left font-semibold text-gray-700">Tipo de Trampa</th>
                        <th class="py-3 px-4 border text-left font-semibold text-gray-700">Ubicación</th>
                        <th class="py-3 px-4 border text-left font-semibold text-gray-700">Plano</th>
            </tr>
        </thead>
        <tbody id="tabla-trampas-body">
            <?php 
            $contador = 0;
            foreach ($trampasDetalle as $trampa): 
                $contador++;
                $visible = $contador <= 5 ? '' : 'hidden trampa-oculta';
            ?>
                        <tr class="hover:bg-gray-50 fila-trampa <?= $visible ?>" 
                            data-tipo="<?= htmlspecialchars($trampa['tipo']); ?>" 
                            data-ubicacion="<?= htmlspecialchars($trampa['ubicacion']); ?>"
                            data-plano="<?= htmlspecialchars($trampa['plano_id'] ?? ''); ?>"
                            data-fecha="<?= htmlspecialchars($trampa['fecha_instalacion'] ?? date('Y-m-d')); ?>">
                            <td class="py-3 px-4 border"><?= $trampa['id']; ?></td>
                            <td class="py-3 px-4 border"><?= normalizarNombre($trampa['tipo']); ?></td>
                            <td class="py-3 px-4 border"><?= $trampa['ubicacion']; ?></td>
                            <td class="py-3 px-4 border">
                                <?php 
                                if (!empty($trampa['plano_id'])) {
                                    foreach ($planos as $plano) {
                                        if ($plano['id'] == $trampa['plano_id']) {
                                            echo esc($plano['nombre']);
                                            break;
                                        }
                                    }
                                } else {
                                    echo '<span class="text-gray-400">Sin plano</span>';
                                }
                                ?>
                            </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <!-- Botón para expandir/contraer la tabla -->
    <div class="mt-4 text-center">
        <button id="btn-expandir-tabla" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
            <span>Ver todas las trampas</span>
        </button>
    </div>
</div>
    </div>

    <!-- Planos de la Sede -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-xl font-semibold text-gray-800 mb-4">Mapas de calor por plano</h3>
        
        <?php if (empty($planos)): ?>
            <p class="text-gray-500 italic text-center py-4">No hay planos disponibles para esta planta.</p>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                <?php foreach ($planos as $plano): ?>
                    <div class="bg-white border rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-all">
                        <div class="relative h-48 overflow-hidden">
                            <?php if (!empty($plano['preview_image'])): ?>
                                <img src="<?= $plano['preview_image'] ?>" alt="<?= esc($plano['nombre']) ?>" 
                                     class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">Sin imagen</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="p-4">
                            <h4 class="font-medium text-lg text-gray-800"><?= esc($plano['nombre']) ?></h4>
                            <p class="text-sm text-gray-600 line-clamp-2 mt-1"><?= esc($plano['descripcion']) ?></p>
                            <div class="mt-4 flex justify-end">
                                <a href="<?= base_url('blueprints/verImagen/' . $plano['id']) ?>" 
                                   class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
                                    Ver imagen
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1"><path d="m9 18 6-6-6-6"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
</div>

    <!-- Incidencias -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <div class="mb-4">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Detalle de Incidencias</h3>
            
            <!-- Filtros principales -->
            <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-3">
                    <div class="flex flex-col">
                        <label for="filtro-id-trampa-incidencias" class="text-sm font-medium text-gray-700 mb-1">Filtrar por ID trampa:</label>
                        <select id="filtro-id-trampa-incidencias" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 w-full">
                            <option value="">Todas las trampas</option>
                            <?php
                            $idsTrampas = [];
                            foreach ($todasLasIncidencias as $incidencia) {
                                if (!empty($incidencia['id_trampa']) && !in_array($incidencia['id_trampa'], $idsTrampas)) {
                                    $idsTrampas[] = $incidencia['id_trampa'];
                                }
                            }
                            sort($idsTrampas);
                            foreach ($idsTrampas as $idTrampa):
                            ?>
                            <option value="<?= htmlspecialchars($idTrampa) ?>"><?= htmlspecialchars($idTrampa) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label for="filtro-tipo-trampa-incidencias" class="text-sm font-medium text-gray-700 mb-1">Filtrar por tipo de trampa:</label>
                        <select id="filtro-tipo-trampa-incidencias" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 w-full">
                            <option value="">Todos los tipos</option>
                            <?php
                            $tiposTrampas = [];
                            foreach ($todasLasIncidencias as $incidencia) {
                                if (!empty($incidencia['tipo_trampa']) && !in_array($incidencia['tipo_trampa'], $tiposTrampas)) {
                                    $tiposTrampas[] = $incidencia['tipo_trampa'];
                                }
                            }
                            sort($tiposTrampas);
                            foreach ($tiposTrampas as $tipoTrampa):
                            ?>
                            <option value="<?= htmlspecialchars($tipoTrampa) ?>"><?= htmlspecialchars(normalizarNombre($tipoTrampa)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="flex flex-col">
                        <label for="filtro-insecto" class="text-sm font-medium text-gray-700 mb-1">Filtrar por insecto:</label>
                        <select id="filtro-insecto" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 w-full">
                            <option value="">Todos los insectos</option>
                            <?php
                            $tiposInsectos = [];
                            foreach ($todasLasIncidencias as $incidencia) {
                                if (!empty($incidencia['tipo_insecto']) && !in_array($incidencia['tipo_insecto'], $tiposInsectos)) {
                                    $tiposInsectos[] = $incidencia['tipo_insecto'];
                                }
                            }
                            sort($tiposInsectos);
                            foreach ($tiposInsectos as $tipoInsecto):
                            ?>
                            <option value="<?= htmlspecialchars($tipoInsecto) ?>"><?= htmlspecialchars(normalizarNombre($tipoInsecto)) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Total: <span id="contador-incidencias" class="font-semibold text-gray-800"><?= count($todasLasIncidencias) ?></span> incidencias
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Filtro por rango de fechas para incidencias -->
        <div class="bg-gray-50 rounded-lg p-4 mb-4 border border-gray-200">
            <p class="block text-sm font-medium text-gray-700 mb-2">Filtrar por rango de fechas</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="filtro-fecha-inicio-incidencias" class="block text-sm text-gray-600 mb-1">Desde</label>
                    <input type="date" id="filtro-fecha-inicio-incidencias" class="w-full p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    <p class="text-xs text-gray-500 mt-1">Seleccione para ver incidencias a partir de esta fecha</p>
                </div>
                <div>
                    <label for="filtro-fecha-fin-incidencias" class="block text-sm text-gray-600 mb-1">Hasta</label>
                    <input type="date" id="filtro-fecha-fin-incidencias" class="w-full p-2 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    <p class="text-xs text-gray-500 mt-1">Seleccione para ver incidencias hasta esta fecha</p>
                </div>
            </div>
            <div class="flex justify-end mt-3">
                <button id="limpiar-filtros-incidencias" class="px-3 py-1 text-sm text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
                    Limpiar filtros
        </button>
    </div>
</div>
        
        <div class="overflow-x-auto mb-6">
            <?php if (empty($todasLasIncidencias)): ?>
                <div class="bg-gray-50 text-gray-500 p-8 text-center rounded-lg border border-gray-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.879 7.121l7.121 7.121m0-7.121l-7.121 7.121M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />
                    </svg>
                    <p class="text-lg">No se encontraron incidencias para la sede seleccionada</p>
                    <p class="mt-2">Seleccione otra sede o registre nuevas incidencias</p>
                </div>
            <?php else: ?>
                <table id="tabla-incidencias" class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="py-3 px-4 border text-left font-semibold text-gray-700">ID Trampa</th>
                            <th class="py-3 px-4 border text-left font-semibold text-gray-700">Tipo de Trampa</th>
                            <th class="py-3 px-4 border text-left font-semibold text-gray-700">Tipo de Incidencia</th>
                            <th class="py-3 px-4 border text-left font-semibold text-gray-700">Tipo de Plaga</th>
                            <th class="py-3 px-4 border text-left font-semibold text-gray-700">Cantidad de Organismos</th>
                            <th class="py-3 px-4 border text-left font-semibold text-gray-700">Tipo de Insecto</th>
                            <th class="py-3 px-4 border text-left font-semibold text-gray-700">Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($todasLasIncidencias as $incidencia): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 border"><?= htmlspecialchars($incidencia['id_trampa'] ?? 'N/A'); ?></td>
                                <td class="py-3 px-4 border"><?= htmlspecialchars(normalizarNombre($incidencia['tipo_trampa'] ?? 'N/A')); ?></td>
                                <td class="py-3 px-4 border"><?= htmlspecialchars($incidencia['tipo_incidencia']); ?></td>
                                <td class="py-3 px-4 border"><?= htmlspecialchars(normalizarNombre($incidencia['tipo_plaga'])); ?></td>
                                <td class="py-3 px-4 border"><?= htmlspecialchars($incidencia['cantidad_organismos'] ?? 0); ?></td>
                                <td class="py-3 px-4 border"><?= htmlspecialchars(normalizarNombre($incidencia['tipo_insecto'] ?? '')); ?></td>
                                <td class="py-3 px-4 border"><?= formatearFechaEspanol($incidencia['fecha']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Instrucciones para Umbrales -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">Cómo usar los umbrales en las gráficas</h3>
                    <p class="text-sm text-blue-700 mt-1">
                        Las gráficas de barras incluyen controles de umbral que te permiten establecer límites y recibir alertas automáticas:
                    </p>
                    <ul class="text-sm text-blue-700 mt-2 list-disc list-inside space-y-1">
                        <li><strong>Configura el valor:</strong> Ingresa el número que consideres como límite crítico</li>
                        <li><strong>Activa el umbral:</strong> Haz clic en "Activar" para mostrar la línea de umbral en la gráfica</li>
                        <li><strong>Alertas automáticas:</strong> Recibirás notificaciones cuando los valores excedan el umbral</li>
                        <li><strong>Persistencia:</strong> Los umbrales se guardan automáticamente para futuras sesiones</li>
                    </ul>
                </div>
            </div>
            <div class="flex-shrink-0 ml-4">
                <button onclick="limpiarTodosLosUmbrales()" 
                        title="Eliminar todos los umbrales configurados y limpiar localStorage"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Limpiar Umbrales
                </button>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="grid grid-cols-1 gap-6">
        <!-- Gráfico: Plagas con Mayor Presencia -->
        <div class="bg-white rounded-lg shadow-sm p-6 relative">
            <div class="absolute top-4 right-4 flex gap-2">
                <button class="btn-descargar-grafica bg-gray-100 hover:bg-green-100 text-green-600 rounded-full p-2 shadow transition" data-canvas="plagasMayorPresenciaChart" data-titulo="Plaga con Mayor Presencia durante el Mes">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </button>
                <button class="btn-ampliar-grafica bg-gray-100 hover:bg-blue-100 text-blue-600 rounded-full p-2 shadow transition" data-canvas="plagasMayorPresenciaChart" data-titulo="Plaga con Mayor Presencia durante el Mes">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4a1 1 0 011-1h4m6 0h4a1 1 0 011 1v4m0 6v4a1 1 0 01-1 1h-4m-6 0H5a1 1 0 01-1-1v-4"/></svg>
                </button>
            </div>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <h3 class="text-lg font-semibold">Comparación de Plagas por Mes</h3>
                <button 
                    onclick="toggleConfiguracionAvanzada()" 
                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-sm flex items-center gap-2"
                    id="btn-config-avanzada">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Configuración Avanzada
                                 </button>
            </div>
            
            <!-- Configuración Avanzada (Colapsable) - Diseño moderno e intuitivo -->
            <div id="configuracion-avanzada" class="mb-6 transition-all duration-500 ease-in-out transform bg-gradient-to-r from-blue-50 to-indigo-50 border-2 border-blue-200 rounded-xl p-6 shadow-lg"
                 style="display: none;">
                <!-- Header con icono y descripción -->
                <div class="flex items-center mb-6">
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-1">Personalizar Comparación</h4>
                        <p class="text-sm text-gray-600">Selecciona exactamente qué datos quieres comparar en tu análisis</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Sección de Meses -->
                    <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-800">Períodos de Tiempo</h5>
                                <p class="text-xs text-gray-500" id="contador-meses">Selecciona los meses a comparar</p>
                            </div>
                        </div>

                        <!-- Lista visual de meses -->
                        <div class="space-y-2 mb-4 max-h-32 overflow-y-auto border rounded-lg p-2 bg-gray-50" id="lista-meses-visual">
                            <?php if(!empty($listaMeses)): ?>
                                <?php foreach($listaMeses as $mes): ?>
                                    <label class="flex items-center p-2 rounded-lg hover:bg-white transition-colors cursor-pointer group">
                                        <input 
                                            type="checkbox" 
                                            value="<?= $mes['mes_valor'] ?>" 
                                            data-nombre="<?= esc($mes['mes_nombre']) ?>"
                                            <?= ($mesSeleccionado == $mes['mes_valor']) ? 'checked' : '' ?>
                                            class="mes-checkbox w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            onchange="actualizarSeleccionMeses()">
                                        <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900"><?= esc($mes['mes_nombre']) ?></span>
                                        <span class="ml-auto text-xs text-gray-400 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <?= $mes['mes_valor'] ?>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center text-gray-500 py-4">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <p class="text-sm">No hay datos disponibles</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Botones de acción para meses -->
                        <div class="flex gap-2">
                            <button onclick="seleccionarTodosMesesVisual()" 
                                    class="flex-1 px-3 py-2 bg-blue-50 text-blue-700 rounded-lg hover:bg-blue-100 transition-colors text-sm font-medium flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Todos
                            </button>
                            <button onclick="limpiarSeleccionMesesVisual()" 
                                    class="flex-1 px-3 py-2 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Limpiar
                            </button>
                        </div>
                    </div>

                    <!-- Sección de Plagas -->
                    <div class="bg-white rounded-lg border border-gray-200 p-5 shadow-sm hover:shadow-md transition-shadow">
                        <div class="flex items-center mb-4">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <h5 class="font-medium text-gray-800">Tipos de Plaga</h5>
                                <p class="text-xs text-gray-500" id="contador-plagas">Cargando plagas disponibles...</p>
                            </div>
                        </div>

                        <!-- Lista visual de plagas -->
                        <div class="space-y-2 mb-4 max-h-32 overflow-y-auto border rounded-lg p-2 bg-gray-50" id="lista-plagas-visual">
                            <div class="text-center text-gray-500 py-4">
                                <div class="animate-spin w-6 h-6 border-2 border-blue-500 border-t-transparent rounded-full mx-auto mb-2"></div>
                                <p class="text-sm">Cargando plagas...</p>
                            </div>
                        </div>

                        <!-- Botones de acción para plagas -->
                        <div class="flex gap-2">
                            <button onclick="seleccionarTodasPlagasVisual()" 
                                    class="flex-1 px-3 py-2 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-sm font-medium flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Todas
                            </button>
                            <button onclick="limpiarSeleccionPlagasVisual()" 
                                    class="flex-1 px-3 py-2 bg-gray-50 text-gray-700 rounded-lg hover:bg-gray-100 transition-colors text-sm font-medium flex items-center justify-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Selectores ocultos para mantener compatibilidad -->
                <select id="meses-comparacion" multiple style="display: none;" onchange="actualizarComparacionMeses()">
                    <?php if(!empty($listaMeses)): ?>
                        <?php foreach($listaMeses as $mes): ?>
                            <option value="<?= $mes['mes_valor'] ?>" <?= ($mesSeleccionado == $mes['mes_valor']) ? 'selected' : '' ?>><?= esc($mes['mes_nombre']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <select id="plagas-comparacion" multiple style="display: none;" onchange="actualizarComparacionMeses()">
                    <option disabled>Cargando plagas...</option>
                </select>

                <!-- Botón de aplicar cambios -->
                <div class="mt-6 text-center">
                    <button onclick="aplicarConfiguracion()" 
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 font-medium shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 flex items-center gap-2 mx-auto">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Actualizar Gráfica
                    </button>
                </div>
            </div>
            </div>
            <!-- Fin Configuración Avanzada -->
            
            <div style="width: 100%; text-align: center; margin: 2rem 0;">
                <canvas id="plagasMayorPresenciaChart" class="grafica-barra" style="height: 700px; max-width: 100%; margin: 0 auto;"></canvas>
            </div>
            
            <!-- Control de Umbrales (3 niveles) - Movido debajo de la gráfica -->
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mt-6">
                <h4 class="text-sm font-medium text-gray-800 mb-3">Umbrales de Alerta (cantidad de organismos):</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                    <!-- Umbral Bajo -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <label for="umbral-bajo-plagasMayorPresenciaChart" class="block text-xs font-medium text-green-800 mb-1">
                            Nivel Bajo
                        </label>
                        <input 
                            type="number" 
                            id="umbral-bajo-plagasMayorPresenciaChart"
                            class="umbral-input w-full p-2 border border-green-300 rounded text-center focus:ring-2 focus:ring-green-500 focus:border-green-500"
                            placeholder="100"
                            min="0"
                            step="1"
                            onchange="actualizarUmbralesGrafica('plagasMayorPresenciaChart')"
                            oninput="actualizarUmbralesEnTiempoReal('plagasMayorPresenciaChart')"
                        />
                    </div>
                    <!-- Umbral Medio -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <label for="umbral-medio-plagasMayorPresenciaChart" class="block text-xs font-medium text-yellow-800 mb-1">
                            Nivel Medio
                        </label>
                        <input 
                            type="number" 
                            id="umbral-medio-plagasMayorPresenciaChart"
                            class="umbral-input w-full p-2 border border-yellow-300 rounded text-center focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                            placeholder="500"
                            min="0"
                            step="1"
                            onchange="actualizarUmbralesGrafica('plagasMayorPresenciaChart')"
                            oninput="actualizarUmbralesEnTiempoReal('plagasMayorPresenciaChart')"
                        />
                    </div>
                    <!-- Umbral Alto -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                        <label for="umbral-alto-plagasMayorPresenciaChart" class="block text-xs font-medium text-red-800 mb-1">
                            Nivel Alto
                        </label>
                        <input 
                            type="number" 
                            id="umbral-alto-plagasMayorPresenciaChart"
                            class="umbral-input w-full p-2 border border-red-300 rounded text-center focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="1000"
                            min="0"
                            step="1"
                            onchange="actualizarUmbralesGrafica('plagasMayorPresenciaChart')"
                            oninput="actualizarUmbralesEnTiempoReal('plagasMayorPresenciaChart')"
                        />
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <span class="text-xs text-gray-600">Verde: Normal | Amarillo: Precaución | Rojo: Crítico</span>
                    <div class="flex gap-2">
                        <button 
                            type="button" 
                            onclick="establecerUmbralesEjemplo('plagasMayorPresenciaChart')"
                            class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full hover:bg-blue-200 transition-colors"
                            title="Cargar valores de ejemplo para ver cómo funcionan los umbrales">
                            Ejemplo
                        </button>
                        <button 
                            type="button" 
                            onclick="toggleUmbralesGrafica('plagasMayorPresenciaChart')"
                            id="toggle-umbrales-plagasMayorPresenciaChart"
                            class="px-3 py-1 text-xs bg-gray-200 text-gray-800 rounded-full hover:bg-gray-300 transition-colors">
                            Activar Umbrales
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Notas - Movidas debajo de la gráfica -->
            <div class="mt-4">
                <label for="notas-grafico-plagasMayorPresenciaChart" class="block text-sm font-medium text-gray-700 mb-1">
                    Notas sobre esta gráfica:
                </label>
                <textarea
                    id="notas-grafico-plagasMayorPresenciaChart"
                    data-grafico="plagasMayorPresenciaChart"
                    class="w-full p-2 border border-gray-300 rounded-lg"
                    rows="2"
                    placeholder="Ej: 'Se observa mayor presencia de moscas en el mes actual. Revisar sistema de ventilación en área de producción.'"></textarea>
                <div class="mt-2 flex justify-end">
                    <button 
                        type="button" 
                        class="limpiar-notas px-3 py-1 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors"
                        data-target="notas-grafico-plagasMayorPresenciaChart"
                        title="Limpiar todas las notas de esta gráfica">
                        🗑️ Limpiar
                    </button>
                </div>
            </div>
        </div>

        <!-- Gráfico: Áreas con Mayor Incidencia -->
        <div class="bg-white rounded-lg shadow-sm p-6 relative">
            <div class="absolute top-4 right-4 flex gap-2">
                <button class="btn-descargar-grafica bg-gray-100 hover:bg-green-100 text-green-600 rounded-full p-2 shadow transition" data-canvas="areasMayorIncidenciaChart" data-titulo="Áreas con Mayor Incidencia">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </button>
                <button class="btn-ampliar-grafica bg-gray-100 hover:bg-blue-100 text-blue-600 rounded-full p-2 shadow transition" data-canvas="areasMayorIncidenciaChart" data-titulo="Áreas con Mayor Incidencia">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4a1 1 0 011-1h4m6 0h4a1 1 0 011 1v4m0 6v4a1 1 0 01-1 1h-4m-6 0H5a1 1 0 01-1-1v-4"/></svg>
                </button>
            </div>
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-4">
                    <h3 class="text-lg font-semibold">Áreas con Mayor Incidencia</h3>
                    <div class="flex items-center gap-3">
                        <label for="selector-plaga-incidencia" class="text-sm font-medium text-gray-700">Plaga:</label>
                        <select id="selector-plaga-incidencia" class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[180px]">
                            <option value="">Seleccionar plaga...</option>
                        </select>
                    </div>
                </div>
            </div>
            <div style="width: 100%; text-align: center; margin-bottom: 2rem;">
                <canvas id="areasMayorIncidenciaChart" class="grafica-circular" style="max-height: 600px;"></canvas>
            </div>
            <div class="mt-6">
                <label for="notas-grafico-areasMayorIncidenciaChart" class="block text-sm font-medium text-gray-700 mb-1">
                    Notas sobre esta gráfica:
                </label>
                <textarea
                    id="notas-grafico-areasMayorIncidenciaChart"
                    data-grafico="areasMayorIncidenciaChart"
                    class="w-full p-2 border border-gray-300 rounded-lg"
                    rows="2"
                    placeholder="Ej: 'El área de almacén presenta mayor incidencia. Implementar medidas preventivas adicionales.'"></textarea>
            </div>
        </div>

        <!-- Gráfico: Trampas con Mayor Captura -->
        <div class="bg-white rounded-lg shadow-sm p-6 relative">
            <div class="absolute top-4 right-4 flex gap-2">
                <button class="btn-descargar-grafica bg-gray-100 hover:bg-green-100 text-green-600 rounded-full p-2 shadow transition" data-canvas="trampasMayorCapturaChart" data-titulo="Trampas con Mayor Captura">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </button>
                <button class="btn-ampliar-grafica bg-gray-100 hover:bg-blue-100 text-blue-600 rounded-full p-2 shadow transition" data-canvas="trampasMayorCapturaChart" data-titulo="Trampas con Mayor Captura">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4a1 1 0 011-1h4m6 0h4a1 1 0 011 1v4m0 6v4a1 1 0 01-1 1h-4m-6 0H5a1 1 0 01-1-1v-4"/></svg>
                </button>
            </div>
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4 mb-4">
                    <h3 class="text-lg font-semibold">Trampas con Mayor Captura</h3>
                    <div class="flex items-center gap-3">
                        <label for="selector-plaga-trampa" class="text-sm font-medium text-gray-700">Filtrar por plaga:</label>
                        <select id="selector-plaga-trampa" class="px-3 py-2 border border-gray-300 rounded-lg bg-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 min-w-[180px]">
                            <option value="">Todas las plagas</option>
                        </select>
                    </div>
                </div>
            </div>
            <div style="width: 100%; text-align: center;">
                <canvas id="trampasMayorCapturaChart" class="grafica-barra"></canvas>
            </div>
            <div class="mt-4 space-y-3">
                <!-- Control de Umbrales (3 niveles) -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-800 mb-3">Umbrales de Alerta (número de capturas):</h4>
                    <div class="grid grid-cols-3 gap-4 mb-3">
                        <!-- Umbral Bajo -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <label for="umbral-bajo-trampasMayorCapturaChart" class="block text-xs font-medium text-green-800 mb-1">
                                Nivel Bajo
                            </label>
                            <input 
                                type="number" 
                                id="umbral-bajo-trampasMayorCapturaChart"
                                class="umbral-input w-full p-2 border border-green-300 rounded text-center focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="5"
                                min="0"
                                step="1"
                                onchange="actualizarUmbralesGrafica('trampasMayorCapturaChart')"
                            />
                        </div>
                        <!-- Umbral Medio -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <label for="umbral-medio-trampasMayorCapturaChart" class="block text-xs font-medium text-yellow-800 mb-1">
                                Nivel Medio
                            </label>
                            <input 
                                type="number" 
                                id="umbral-medio-trampasMayorCapturaChart"
                                class="umbral-input w-full p-2 border border-yellow-300 rounded text-center focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                placeholder="10"
                                min="0"
                                step="1"
                                onchange="actualizarUmbralesGrafica('trampasMayorCapturaChart')"
                            />
                        </div>
                        <!-- Umbral Alto -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <label for="umbral-alto-trampasMayorCapturaChart" class="block text-xs font-medium text-red-800 mb-1">
                                Nivel Alto
                            </label>
                            <input 
                                type="number" 
                                id="umbral-alto-trampasMayorCapturaChart"
                                class="umbral-input w-full p-2 border border-red-300 rounded text-center focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="20"
                                min="0"
                                step="1"
                                onchange="actualizarUmbralesGrafica('trampasMayorCapturaChart')"
                            />
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600">Verde: Normal | Amarillo: Precaución | Rojo: Crítico</span>
                        <button 
                            type="button" 
                            onclick="toggleUmbralesGrafica('trampasMayorCapturaChart')"
                            id="toggle-umbrales-trampasMayorCapturaChart"
                            class="px-3 py-1 text-xs bg-gray-200 text-gray-800 rounded-full hover:bg-gray-300 transition-colors">
                            Activar Umbrales
                        </button>
                    </div>
                </div>
                
                <!-- Notas -->
                <div>
                    <label for="notas-grafico-trampasMayorCapturaChart" class="block text-sm font-medium text-gray-700 mb-1">
                        Notas sobre esta gráfica:
                    </label>
                    <textarea
                        id="notas-grafico-trampasMayorCapturaChart"
                        data-grafico="trampasMayorCapturaChart"
                        class="w-full p-2 border border-gray-300 rounded-lg"
                        rows="2"
                        placeholder="Ej: 'Trampa T-001 muestra alta efectividad. Considerar replicar su ubicación estratégica.'"></textarea>
                </div>
            </div>
        </div>

        <!-- Gráfico: Áreas que Presentaron Capturas -->
        <div class="bg-white rounded-lg shadow-sm p-6 relative">
            <div class="absolute top-4 right-4 flex gap-2">
                <button class="btn-descargar-grafica bg-gray-100 hover:bg-green-100 text-green-600 rounded-full p-2 shadow transition" data-canvas="areasCapturasPorPlagaChart" data-titulo="Áreas que Presentaron Capturas">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </button>
                <button class="btn-ampliar-grafica bg-gray-100 hover:bg-blue-100 text-blue-600 rounded-full p-2 shadow transition" data-canvas="areasCapturasPorPlagaChart" data-titulo="Áreas que Presentaron Capturas">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4a1 1 0 011-1h4m6 0h4a1 1 0 011 1v4m0 6v4a1 1 0 01-1 1h-4m-6 0H5a1 1 0 01-1-1v-4"/></svg>
                </button>
            </div>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <h3 class="text-lg font-semibold">Áreas que Presentaron Capturas</h3>
            </div>
            <div style="width: 100%; text-align: center;">
                <canvas id="areasCapturasPorPlagaChart" class="grafica-barra"></canvas>
            </div>
            
            <!-- Nota explicativa de escala adaptativa -->
            <div class="mt-3 bg-blue-50 border border-blue-200 rounded-lg p-3">
                <div class="flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-sm">
                        <p class="font-medium text-blue-800">Escala Adaptativa</p>
                        <p class="text-blue-700">Esta gráfica usa una escala optimizada para mejor visualización de valores pequeños. Las ubicaciones con valores muy altos (>2500) muestran un indicador <span class="inline-flex items-center px-1 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">⚠️</span> y el valor real aparece en el tooltip.</p>
                    </div>
                </div>
            </div>
            
            <div class="mt-4 space-y-3">
                <!-- Control de Umbrales (3 niveles) -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-800 mb-3">Umbrales de Alerta (capturas por área):</h4>
                    <div class="grid grid-cols-3 gap-4 mb-3">
                        <!-- Umbral Bajo -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <label for="umbral-bajo-areasCapturasPorPlagaChart" class="block text-xs font-medium text-green-800 mb-1">
                                Nivel Bajo
                            </label>
                            <input 
                                type="number" 
                                id="umbral-bajo-areasCapturasPorPlagaChart"
                                class="umbral-input w-full p-2 border border-green-300 rounded text-center focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="5"
                                min="0"
                                step="1"
                                onchange="actualizarUmbralesGrafica('areasCapturasPorPlagaChart')"
                            />
                        </div>
                        <!-- Umbral Medio -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <label for="umbral-medio-areasCapturasPorPlagaChart" class="block text-xs font-medium text-yellow-800 mb-1">
                                Nivel Medio
                            </label>
                            <input 
                                type="number" 
                                id="umbral-medio-areasCapturasPorPlagaChart"
                                class="umbral-input w-full p-2 border border-yellow-300 rounded text-center focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                placeholder="15"
                                min="0"
                                step="1"
                                onchange="actualizarUmbralesGrafica('areasCapturasPorPlagaChart')"
                            />
                        </div>
                        <!-- Umbral Alto -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <label for="umbral-alto-areasCapturasPorPlagaChart" class="block text-xs font-medium text-red-800 mb-1">
                                Nivel Alto
                            </label>
                            <input 
                                type="number" 
                                id="umbral-alto-areasCapturasPorPlagaChart"
                                class="umbral-input w-full p-2 border border-red-300 rounded text-center focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="30"
                                min="0"
                                step="1"
                                onchange="actualizarUmbralesGrafica('areasCapturasPorPlagaChart')"
                            />
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600">Verde: Normal | Amarillo: Precaución | Rojo: Crítico</span>
                        <button 
                            type="button" 
                            onclick="toggleUmbralesGrafica('areasCapturasPorPlagaChart')"
                            id="toggle-umbrales-areasCapturasPorPlagaChart"
                            class="px-3 py-1 text-xs bg-gray-200 text-gray-800 rounded-full hover:bg-gray-300 transition-colors">
                            Activar Umbrales
                        </button>
                    </div>
                </div>
                
                <!-- Notas -->
                <div>
                    <label for="notas-grafico-areasCapturasPorPlagaChart" class="block text-sm font-medium text-gray-700 mb-1">
                        Notas sobre esta gráfica:
                    </label>
                    <textarea
                        id="notas-grafico-areasCapturasPorPlagaChart"
                        data-grafico="areasCapturasPorPlagaChart"
                        class="w-full p-2 border border-gray-300 rounded-lg"
                        rows="2"
                        placeholder="Ej: 'Distribución uniforme de capturas. Monitoreo preventivo funcionando correctamente.'"></textarea>
                </div>
            </div>
        </div>

        <!-- Gráfico: Incidencias por Tipo y Mes -->
        <div class="bg-white rounded-lg shadow-sm p-6 relative">
            <div class="absolute top-4 right-4 flex gap-2">
                <button class="btn-descargar-grafica bg-gray-100 hover:bg-green-100 text-green-600 rounded-full p-2 shadow transition" data-canvas="incidenciasTipoChart" data-titulo="Incidencias por Tipo y Mes">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </button>
                <button class="btn-ampliar-grafica bg-gray-100 hover:bg-blue-100 text-blue-600 rounded-full p-2 shadow transition" data-canvas="incidenciasTipoChart" data-titulo="Incidencias por Tipo y Mes">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4a1 1 0 011-1h4m6 0h4a1 1 0 011 1v4m0 6v4a1 1 0 01-1 1h-4m-6 0H5a1 1 0 01-1-1v-4"/></svg>
                </button>
            </div>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <h3 class="text-lg font-semibold">Incidencias por Tipo y Mes</h3>
            </div>
            <div style="width: 100%; text-align: center;">
                <canvas id="incidenciasTipoChart" class="grafica-barra"></canvas>
            </div>
            <div class="mt-4 space-y-3">
                <!-- Control de Umbrales (3 niveles) -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-800 mb-3">Umbrales de Alerta (incidencias mensuales):</h4>
                    <div class="grid grid-cols-3 gap-4 mb-3">
                        <!-- Umbral Bajo -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <label for="umbral-bajo-incidenciasTipoChart" class="block text-xs font-medium text-green-800 mb-1">
                                Nivel Bajo
                            </label>
                            <input 
                                type="number" 
                                id="umbral-bajo-incidenciasTipoChart"
                                class="umbral-input w-full p-2 border border-green-300 rounded text-center focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="10"
                                min="0"
                                step="1"
                                onchange="actualizarUmbralesGrafica('incidenciasTipoChart')"
                            />
                        </div>
                        <!-- Umbral Medio -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <label for="umbral-medio-incidenciasTipoChart" class="block text-xs font-medium text-yellow-800 mb-1">
                                Nivel Medio
                            </label>
                            <input 
                                type="number" 
                                id="umbral-medio-incidenciasTipoChart"
                                class="umbral-input w-full p-2 border border-yellow-300 rounded text-center focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                placeholder="20"
                                min="0"
                                step="1"
                                onchange="actualizarUmbralesGrafica('incidenciasTipoChart')"
                            />
                        </div>
                        <!-- Umbral Alto -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <label for="umbral-alto-incidenciasTipoChart" class="block text-xs font-medium text-red-800 mb-1">
                                Nivel Alto
                            </label>
                            <input 
                                type="number" 
                                id="umbral-alto-incidenciasTipoChart"
                                class="umbral-input w-full p-2 border border-red-300 rounded text-center focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="40"
                                min="0"
                                step="1"
                                onchange="actualizarUmbralesGrafica('incidenciasTipoChart')"
                            />
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600">Verde: Normal | Amarillo: Precaución | Rojo: Crítico</span>
                        <button 
                            type="button" 
                            onclick="toggleUmbralesGrafica('incidenciasTipoChart')"
                            id="toggle-umbrales-incidenciasTipoChart"
                            class="px-3 py-1 text-xs bg-gray-200 text-gray-800 rounded-full hover:bg-gray-300 transition-colors">
                            Activar Umbrales
                        </button>
                    </div>
                </div>
                
                <!-- Notas -->
                <div>
                    <label for="notas-grafico-incidenciasTipoChart" class="block text-sm font-medium text-gray-700 mb-1">
                        Notas sobre esta gráfica:
                    </label>
                    <textarea
                        id="notas-grafico-incidenciasTipoChart"
                        data-grafico="incidenciasTipoChart"
                        class="w-full p-2 border border-gray-300 rounded-lg"
                        rows="2"
                        placeholder="Ej: 'Tendencia estacional observada. Incrementar vigilancia durante meses de mayor actividad.'"></textarea>
                </div>
            </div>
        </div>

        <!-- Gráfico: Distribución de Trampas -->
        <div class="bg-white rounded-lg shadow-sm p-6 relative">
            <div class="absolute top-4 right-4 flex gap-2">
                <button class="btn-descargar-grafica bg-gray-100 hover:bg-green-100 text-green-600 rounded-full p-2 shadow transition" data-canvas="trampasPorUbicacionChart" data-titulo="Distribución de Trampas">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                </button>
                <button class="btn-ampliar-grafica bg-gray-100 hover:bg-blue-100 text-blue-600 rounded-full p-2 shadow transition" data-canvas="trampasPorUbicacionChart" data-titulo="Distribución de Trampas">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4a1 1 0 011-1h4m6 0h4a1 1 0 011 1v4m0 6v4a1 1 0 01-1 1h-4m-6 0H5a1 1 0 01-1-1v-4"/></svg>
                </button>
            </div>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <h3 class="text-lg font-semibold">Distribución de Trampas</h3>
            </div>
            <div style="width: 100%; text-align: center;">
                <canvas id="trampasPorUbicacionChart" class="grafica-barra"></canvas>
            </div>
            <div class="mt-4 space-y-3">
                <!-- Control de Umbrales (3 niveles) -->
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-gray-800 mb-3">Umbrales de Distribución (trampas por ubicación):</h4>
                    <div class="grid grid-cols-3 gap-4 mb-3">
                        <!-- Umbral Bajo -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                            <label for="umbral-bajo-trampasPorUbicacionChart" class="block text-xs font-medium text-green-800 mb-1">
                                Nivel Bajo
                            </label>
                            <input 
                                type="number" 
                                id="umbral-bajo-trampasPorUbicacionChart"
                                class="umbral-input w-full p-2 border border-green-300 rounded text-center focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                placeholder="1"
                                min="0"
                                step="1"
                                onchange="actualizarUmbralesGrafica('trampasPorUbicacionChart')"
                            />
                        </div>
                        <!-- Umbral Medio -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <label for="umbral-medio-trampasPorUbicacionChart" class="block text-xs font-medium text-yellow-800 mb-1">
                                Nivel Medio
                            </label>
                            <input 
                                type="number" 
                                id="umbral-medio-trampasPorUbicacionChart"
                                class="umbral-input w-full p-2 border border-yellow-300 rounded text-center focus:ring-2 focus:ring-yellow-500 focus:border-yellow-500"
                                placeholder="3"
                                min="0"
                                step="1"
                                onchange="actualizarUmbralesGrafica('trampasPorUbicacionChart')"
                            />
                        </div>
                        <!-- Umbral Alto -->
                        <div class="bg-red-50 border border-red-200 rounded-lg p-3">
                            <label for="umbral-alto-trampasPorUbicacionChart" class="block text-xs font-medium text-red-800 mb-1">
                                Nivel Alto
                            </label>
                            <input 
                                type="number" 
                                id="umbral-alto-trampasPorUbicacionChart"
                                class="umbral-input w-full p-2 border border-red-300 rounded text-center focus:ring-2 focus:ring-red-500 focus:border-red-500"
                                placeholder="5"
                                min="0"
                                step="1"
                                onchange="actualizarUmbralesGrafica('trampasPorUbicacionChart')"
                            />
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-600">Verde: Baja cobertura | Amarillo: Cobertura adecuada | Rojo: Alta concentración</span>
                        <button 
                            type="button" 
                            onclick="toggleUmbralesGrafica('trampasPorUbicacionChart')"
                            id="toggle-umbrales-trampasPorUbicacionChart"
                            class="px-3 py-1 text-xs bg-gray-200 text-gray-800 rounded-full hover:bg-gray-300 transition-colors">
                            Activar Umbrales
                        </button>
                    </div>
                </div>
                
                <!-- Notas -->
                <div>
                    <label for="notas-grafico-trampasPorUbicacionChart" class="block text-sm font-medium text-gray-700 mb-1">
                        Notas sobre esta gráfica:
                    </label>
                    <textarea
                        id="notas-grafico-trampasPorUbicacionChart"
                        data-grafico="trampasPorUbicacionChart"
                        class="w-full p-2 border border-gray-300 rounded-lg"
                        rows="2"
                        placeholder="Ej: 'Cobertura adecuada en áreas críticas. Considerar instalar trampa adicional en zona de carga.'"></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de Acciones de seguimiento -->
    <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-xl font-semibold text-gray-800">Acciones de Seguimiento</h3>
            <button type="button" class="limpiar-notas px-3 py-1 text-sm text-blue-600 hover:text-blue-800 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors" data-target="acciones-seguimiento">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Limpiar
            </button>
        </div>
        <p class="text-gray-600 mb-4">
            <strong>Registre aquí las acciones de seguimiento para este informe.</strong> Esta información se incluirá en el reporte final PDF.
            <br><small class="text-gray-500">Ejemplo: "Revisar trampas en área de producción semanalmente", "Incrementar frecuencia de limpieza en zona X", etc.</small>
        </p>
        
        <div class="border border-gray-200 rounded-lg p-4">
            <textarea id="acciones-seguimiento" class="w-full h-48 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none" 
                placeholder="Detalle aquí las acciones de seguimiento propuestas:

• Acción 1: [Descripción] - Responsable: [Nombre] - Fecha: [DD/MM/YYYY]
• Acción 2: [Descripción] - Responsable: [Nombre] - Fecha: [DD/MM/YYYY]
• Acción 3: [Descripción] - Responsable: [Nombre] - Fecha: [DD/MM/YYYY]

Observaciones adicionales:
[Escriba aquí cualquier observación relevante para el seguimiento]"></textarea>
        </div>
        
        <div class="mt-3 text-sm text-gray-500">
            <strong>Tip:</strong> Las notas que escriba en cada gráfica y estas acciones de seguimiento aparecerán automáticamente en el PDF generado.
        </div>
    </div>

    <!-- Botón para generar PDF con todas las tablas y gráficas -->
    <div class="mt-8 mb-12 text-center">
        <button id="generarPdfBtn" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 shadow-md transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block mr-2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/><path d="M9 15v-2"/><path d="M12 15v-6"/><path d="M15 15v-4"/></svg>
            Generar Informe Completo
        </button>
    </div>
</div>

<!-- Mantener todos los scripts originales -->
<script>
// Función JavaScript para normalizar nombres: quitar guiones bajos y capitalizar
function normalizarNombreJS(texto) {
    if (!texto || texto === '') return texto;
    // Reemplazar guiones bajos con espacios
    texto = texto.replace(/_/g, ' ');
    // Capitalizar la primera letra de cada palabra
    return texto.split(' ').map(palabra => {
        return palabra.charAt(0).toUpperCase() + palabra.slice(1).toLowerCase();
    }).join(' ');
}

function cambiarSede(sedeId) {
    if (sedeId) {
        console.log('Cambiando a sede: ' + sedeId);
        window.location.href = '<?= base_url('locations') ?>?sede_id=' + sedeId;
    } else {
        console.log('No se seleccionó ninguna sede');
    }
}

function descargarPDF() {
    // Mostrar indicador de carga
    mostrarCargando('Generando PDF...');

    // Esperar a que las imágenes y fuentes se carguen
    setTimeout(function() {
        generarPDFConDOM2PDF();
    }, 500);
}

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
        
        // Capturar todas las imágenes de los gráficos seleccionados
        const graficasSeleccionadas = [];
        document.querySelectorAll('input[name="graficas[]"]:checked').forEach(checkbox => {
            graficasSeleccionadas.push(checkbox.value);
        });
        
        // Mapear los IDs de los gráficos según la configuración
        const chartsConfig = {
            'plagasMayorPresencia': { 
                id: 'plagasMayorPresenciaChart', 
                title: 'Plaga con Mayor Presencia'
            },
            'areasMayorIncidencia': { 
                id: 'areasMayorIncidenciaChart', 
                title: 'Áreas con Mayor Incidencia de Plaga' 
            },
            'incidenciasTipo': { 
                id: 'incidenciasTipoChart', 
                title: 'Incidencias por Tipo y Mes' 
            },
            'trampasPorUbicacion': { 
                id: 'trampasPorUbicacionChart', 
                title: 'Distribución de Trampas por Ubicación' 
            },
            'trampasMayorCaptura': { 
                id: 'trampasMayorCapturaChart', 
                title: 'Trampas con Mayor Captura'
            },
            'areasCapturasPorPlaga': { 
                id: 'areasCapturasPorPlagaChart', 
                title: 'Áreas que Presentaron Capturas' 
            }
        };
        
        // Capturar solo los gráficos seleccionados
        graficasSeleccionadas.forEach(grafica => {
            if (chartsConfig[grafica]) {
                chartImagesObj[grafica] = getChartImageData(chartsConfig[grafica].id);
            }
        });
        
        // Capturar las notas de cada gráfica seleccionada
        const notasGraficas = {};
        graficasSeleccionadas.forEach(grafica => {
            if (chartsConfig[grafica]) {
                const chartId = chartsConfig[grafica].id;
                const notasTextarea = document.getElementById(`notas-grafico-${chartId}`);
                if (notasTextarea) {
                    notasGraficas[grafica] = notasTextarea.value.trim();
                }
            }
        });
        
        // Capturar las acciones de seguimiento
        const accionesSeguimiento = document.getElementById('acciones-seguimiento');
        const accionesSeguimientoTexto = accionesSeguimiento ? accionesSeguimiento.value.trim() : '';
        
        console.log('Debug - Acciones de seguimiento:');
        console.log('Elemento encontrado:', accionesSeguimiento);
        console.log('Valor capturado:', accionesSeguimientoTexto);
        console.log('Longitud del texto:', accionesSeguimientoTexto.length);
        
        // Obtener sede seleccionada para la URL
        const sedeId = document.getElementById('sede-selector').value;
        
        // Crear un formulario para enviar los datos al servidor
        mostrarCargando('Generando PDF en el servidor...');
        
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?= base_url('locations/generarPDF') ?>?sede_id=' + sedeId;
        form.target = '_blank'; // Abrir en nueva pestaña
        
        // Crear campo oculto para las imágenes de los gráficos
        const chartImagesInput = document.createElement('input');
        chartImagesInput.type = 'hidden';
        chartImagesInput.name = 'chart_images';
        chartImagesInput.value = JSON.stringify(chartImagesObj);
        
        // Crear campo oculto para las notas de las gráficas
        const notasGraficasInput = document.createElement('input');
        notasGraficasInput.type = 'hidden';
        notasGraficasInput.name = 'notas_graficas';
        notasGraficasInput.value = JSON.stringify(notasGraficas);
        
        // Crear campo oculto para las acciones de seguimiento
        const accionesSeguimientoInput = document.createElement('input');
        accionesSeguimientoInput.type = 'hidden';
        accionesSeguimientoInput.name = 'acciones_seguimiento';
        accionesSeguimientoInput.value = accionesSeguimientoTexto;
        
        // Agregar campos al formulario
        form.appendChild(chartImagesInput);
        form.appendChild(notasGraficasInput);
        form.appendChild(accionesSeguimientoInput);
        
        console.log('Debug - Formulario creado:');
        console.log('Acción del formulario:', form.action);
        console.log('Método:', form.method);
        console.log('Campos del formulario:', form.elements.length);
        console.log('Campo acciones_seguimiento:', accionesSeguimientoInput.name, '=', accionesSeguimientoInput.value);
        
        // Agregar formulario al documento, enviarlo y luego eliminarlo
        document.body.appendChild(form);
        form.submit();
        
        // Mostrar mensaje de confirmación
        const notasIncluidas = Object.keys(notasGraficas).filter(key => notasGraficas[key].length > 0).length;
        const tieneAcciones = accionesSeguimientoTexto.length > 0;
        
        let mensaje = 'PDF generado exitosamente';
        if (notasIncluidas > 0 || tieneAcciones) {
            mensaje += ' incluyendo';
            if (notasIncluidas > 0) {
                mensaje += ` notas de ${notasIncluidas} gráfica(s)`;
            }
            if (tieneAcciones) {
                mensaje += notasIncluidas > 0 ? ' y acciones de seguimiento' : ' acciones de seguimiento';
            }
        }
        
        // Limpiar
        setTimeout(() => {
            document.body.removeChild(form);
            ocultarCargando();
            
            // Mostrar notificación de éxito
            const notification = document.createElement('div');
            notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white py-3 px-6 rounded-lg shadow-lg z-50';
            notification.textContent = mensaje;
            document.body.appendChild(notification);
            
            // Auto-eliminar después de 4 segundos
            setTimeout(() => {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => notification.remove(), 500);
            }, 4000);
        }, 1000);
    } catch (error) {
        console.error('Error en el proceso de generación de PDF:', error);
        alert('Error en el proceso: ' + error.message);
        ocultarCargando();
    }
}

// Verificar si el selector de sedes existe y está funcionando correctamente
document.addEventListener('DOMContentLoaded', function() {
    const sedeSelector = document.getElementById('sede-selector');
    if (sedeSelector) {
        console.log('Selector de sedes cargado correctamente');
        console.log('Valor actual: ' + sedeSelector.value);
    } else {
        console.error('Error: No se encontró el selector de sedes');
    }
    
    // Configurar los botones de selección de gráficas
    const seleccionarTodosBtn = document.getElementById('seleccionar-todos');
    const deseleccionarTodosBtn = document.getElementById('deseleccionar-todos');
    const checkboxesGraficas = document.querySelectorAll('input[name="graficas[]"]');
    
    if (seleccionarTodosBtn) {
        seleccionarTodosBtn.addEventListener('click', function() {
            checkboxesGraficas.forEach(checkbox => {
                checkbox.checked = true;
            });
        });
    }
    
    if (deseleccionarTodosBtn) {
        deseleccionarTodosBtn.addEventListener('click', function() {
            checkboxesGraficas.forEach(checkbox => {
                checkbox.checked = false;
            });
        });
    }
});

// Función para actualizar el eje Y de cualquier gráfica
function actualizarEjeYGrafica(chartId, maxValue) {
    const chartInstance = Chart.getChart(chartId);
    if (chartInstance) {
        // Convertir a número y validar
        const newMax = parseInt(maxValue);
        if (!isNaN(newMax) && newMax > 0) {
            // Actualizar la configuración del eje Y
            chartInstance.options.scales.y.max = newMax;
            // Volver a renderizar la gráfica
            chartInstance.update();
            console.log(`Eje Y de ${chartId} actualizado a ${newMax}`);
        } else {
            console.error('El valor máximo debe ser un número positivo');
            // Restaurar el valor anterior en caso de entrada no válida
            document.getElementById(`max-y-${chartId}`).value = chartInstance.options.scales.y.max || 5;
        }
    } else {
        console.error(`No se encontró la gráfica con ID: ${chartId}`);
    }
}
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener datos de PHP para trampas por ubicación
        const trampasPorUbicacion = <?= json_encode($trampasPorUbicacion ?? []); ?>;

        // Preparar datos para el gráfico
        const ubicaciones = trampasPorUbicacion.map(item => item.ubicacion);
        const totales = trampasPorUbicacion.map(item => parseInt(item.total));

        // Calcular el valor máximo para configurar el eje Y
        const maxTotal = Math.max(...totales, 1); // Asegurar al menos 1 si no hay datos

        // Verificar si el canvas existe antes de crear el gráfico
        const canvas = document.getElementById('trampasPorUbicacionChart');
        if (!canvas) {
            console.error("Error: No se encontró el canvas 'trampasPorUbicacionChart'");
            return;
        }

        // Generar gradiente para las barras - uso colores más intensos para mejor visibilidad
        const ctx = canvas.getContext('2d');
        const gradiente = ctx.createLinearGradient(0, 0, 0, 400);
        gradiente.addColorStop(0, 'rgba(54, 162, 235, 0.9)'); // Azul más intenso
        gradiente.addColorStop(1, 'rgba(54, 162, 235, 0.6)'); // Más opaco para mejor visibilidad

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ubicaciones,
                datasets: [{
                    label: 'Número de Trampas',
                    data: totales,
                    backgroundColor: gradiente,
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 2,
                    borderRadius: 6,
                    barPercentage: 0.7,
                    categoryPercentage: 0.8,
                    hoverBackgroundColor: 'rgba(54, 162, 235, 0.9)',
                    hoverBorderWidth: 3,
                    minBarLength: 5 // Garantizar que incluso valores pequeños sean visibles
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 30,
                        right: 40,
                        top: 40,
                        bottom: 30
                    }
                },
                plugins: {
                    legend: { 
                        display: true,
                        position: 'top',
                        align: 'center',
                        labels: {
                            boxWidth: 18,
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'rect',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'DISTRIBUCIÓN DE TRAMPAS POR UBICACIÓN',
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            size: 18,
                            weight: 'bold'
                        },
                        color: '#1f2937',
                        padding: {
                            top: 15,
                            bottom: 35
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y;
                                }
                                return label;
                            }
                        },
                        backgroundColor: 'rgba(30, 41, 59, 0.9)',
                        titleFont: {
                            size: 15,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 14
                        },
                        padding: 12,
                        borderColor: 'rgba(255, 255, 255, 0.3)',
                        borderWidth: 2,
                        displayColors: true,
                        boxWidth: 12,
                        boxHeight: 12,
                        usePointStyle: true
                    },
                    datalabels: {
                        anchor: 'end',
                        align: 'top',
                        formatter: Math.round,
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            weight: 'bold',
                            size: 14
                        },
                        color: 'rgba(54, 162, 235, 1)',
                        offset: 8
                    }
                },
                scales: {
                    x: { 
                        title: { 
                            display: true, 
                            text: 'Ubicación',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 15,
                                weight: 'bold'
                            },
                            color: '#1f2937',
                            padding: {top: 15, bottom: 5}
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            autoSkip: false,
                            maxTicksLimit: 20,
                            maxRotation: 90,
                            minRotation: 60,
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 11,
                                weight: 'bold'
                            },
                            padding: 8,
                            color: '#374151'
                        }
                    },
                    y: { 
                        beginAtZero: true,
                        // Configurar un valor máximo razonable basado en los datos
                        max: Math.max(5, Math.ceil(maxTotal * 1.2)), // al menos 5 o 20% más que el valor máximo
                        title: { 
                            display: true, 
                            text: 'Número de Trampas',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 15,
                                weight: 'bold'
                            },
                            color: '#1f2937',
                            padding: {top: 5, bottom: 15}
                        },
                        grid: {
                            color: 'rgba(226, 232, 240, 0.8)',
                            borderDash: [4, 4],
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: 1,
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 12,
                                weight: 'bold'
                            },
                            padding: 10,
                            color: '#374151'
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            },
            plugins: [ChartDataLabels]
        });
    });
    
    // Filtros para la tabla de trampas
    document.addEventListener('DOMContentLoaded', function() {
        const filtroTipoTrampa = document.getElementById('filtro-tipo-trampa');
        const filtroUbicacion = document.getElementById('filtro-ubicacion');
        const filtroPlano = document.getElementById('filtro-plano');
        const limpiarFiltros = document.getElementById('limpiar-filtros');
        const contadorTrampas = document.getElementById('cantidad-trampas');
        const filasTrampa = document.querySelectorAll('.fila-trampa');
        
        // Función para aplicar los filtros
        function aplicarFiltros() {
            const tipoSeleccionado = filtroTipoTrampa.value;
            const ubicacionSeleccionada = filtroUbicacion.value;
            const planoSeleccionado = filtroPlano.value;
            
            let trampasVisibles = 0;
            
            filasTrampa.forEach(fila => {
                const tipo = fila.dataset.tipo;
                const ubicacion = fila.dataset.ubicacion;
                const plano = fila.dataset.plano;
                
                // Verificar si la fila cumple con todos los filtros activos
                const cumpleTipo = !tipoSeleccionado || tipo === tipoSeleccionado;
                const cumpleUbicacion = !ubicacionSeleccionada || ubicacion === ubicacionSeleccionada;
                const cumplePlano = !planoSeleccionado || plano === planoSeleccionado;
                
                // Mostrar u ocultar la fila según los filtros
                if (cumpleTipo && cumpleUbicacion && cumplePlano) {
                    fila.style.display = '';
                    trampasVisibles++;
                } else {
                    fila.style.display = 'none';
                }
            });
            
            // Actualizar el contador de resultados
            contadorTrampas.textContent = trampasVisibles;
        }
        
        // Evento para limpiar todos los filtros
        limpiarFiltros.addEventListener('click', function() {
            filtroTipoTrampa.value = '';
            filtroUbicacion.value = '';
            filtroPlano.value = '';
            
            // Mostrar todas las filas nuevamente
            filasTrampa.forEach(fila => {
                fila.style.display = '';
            });
            
            // Actualizar el contador de resultados
            contadorTrampas.textContent = filasTrampa.length;
        });
        
        // Agregar eventos a los filtros
        filtroTipoTrampa.addEventListener('change', aplicarFiltros);
        filtroUbicacion.addEventListener('change', aplicarFiltros);
        filtroPlano.addEventListener('change', aplicarFiltros);
    });
</script>

<!-- Cargar Chart.js y plugins -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation"></script>

<style>
/* Estilos para mejorar la experiencia de usuario */
.umbral-input:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.textarea-highlight {
    transition: border-color 0.3s ease, background-color 0.3s ease;
}

.loading-spinner {
    display: inline-block;
    width: 12px;
    height: 12px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.modal-entrance {
    animation: modalEnter 0.3s ease-out;
}

@keyframes modalEnter {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>

<script>
// Registrar el plugin de anotaciones globalmente
Chart.register(ChartDataLabels);
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener datos de PHP
        const incidenciasPorTipoPlaga = <?= json_encode($incidenciasPorTipoPlaga ?? []); ?>;

        let incidenciasMap = {};

        // Procesar los datos para agrupar por tipo de plaga
        incidenciasPorTipoPlaga.forEach(item => {
            let plaga = item.tipo_plaga || "Desconocida";
            let total = parseInt(item.total, 10) || 0;

            if (!incidenciasMap[plaga]) {
                incidenciasMap[plaga] = 0;
            }
            incidenciasMap[plaga] += total;
        });

        // Extraer datos para el gráfico y normalizar nombres
        // Mantener las claves originales para acceder a los valores
        let clavesOriginales = Object.keys(incidenciasMap);
        // Normalizar solo para mostrar en las etiquetas
        let etiquetas = clavesOriginales.map(plaga => normalizarNombreJS(plaga));
        // Usar las claves originales para obtener los valores
        let valores = clavesOriginales.map(plaga => incidenciasMap[plaga]);
        
        // Calcular el valor total para ayudar con los porcentajes y la escala
        const valorTotal = valores.reduce((sum, value) => sum + value, 0);

        // Usar paleta de colores azules neutrales para gráfico de pastel
        const colores = generateBlueNeutralPalette(etiquetas.length);

        // Verificar si el canvas existe antes de crear el gráfico
        const canvas = document.getElementById('incidenciasTipoChart');
        if (!canvas) {
            console.error("Error: No se encontró el canvas 'incidenciasTipoChart'");
            return;
        }

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: etiquetas,
                datasets: [{
                    data: valores,
                    backgroundColor: colores,
                    borderColor: colores.map(color => color.replace('0.9', '1')),
                    borderWidth: 2,
                    hoverBorderWidth: 3,
                    borderRadius: 5,
                    spacing: 4,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '40%',  // Para hacer una gráfica de tipo donut
                layout: {
                    padding: 20
                },
                plugins: {
                    legend: { 
                        position: 'right',
                        align: 'center',
                        labels: {
                            boxWidth: 15,
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 11,
                                weight: 'bold'
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'DISTRIBUCIÓN DE INCIDENCIAS POR TIPO DE PLAGA',
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    // Asegurar que los datos siempre sean visibles
                    beforeDraw: function(chart) {
                        if (chart.data.datasets[0].data.length === 0) {
                            // Si no hay datos, mostrar mensaje
                            const ctx = chart.ctx;
                            const width = chart.width;
                            const height = chart.height;
                            chart.clear();
                            
                            ctx.save();
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.font = '16px Arial';
                            ctx.fillStyle = '#666';
                            ctx.fillText('No hay datos disponibles', width / 2, height / 2);
                            ctx.restore();
                        }
                    },
                    datalabels: {
                        color: '#fff',
                        formatter: (value, ctx) => {
                            let total = ctx.chart.data.datasets[0].data.reduce((acc, val) => acc + val, 0);
                            let porcentaje = ((value / total) * 100).toFixed(1) + "%";
                            return porcentaje;
                        },
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            weight: 'bold',
                            size: 12
                        },
                        textStrokeColor: 'rgba(0, 0, 0, 0.5)',
                        textStrokeWidth: 2,
                        textShadowBlur: 3,
                        textShadowColor: 'rgba(0, 0, 0, 0.5)'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.raw;
                                const total = context.chart.data.datasets[0].data.reduce((acc, data) => acc + data, 0);
                                const percentage = (value / total) * 100;
                                let percentageText;
                                if (percentage < 1 && percentage > 0) {
                                    percentageText = '>1%';
                                } else {
                                    percentageText = Math.round(percentage) + '%';
                                }
                                return `${label}: ${value} (${percentageText})`;
                            }
                        },
                        backgroundColor: 'rgba(30, 41, 59, 0.8)',
                        titleFont: {
                            size: 13
                        },
                        bodyFont: {
                            size: 12
                        },
                        padding: 10,
                        borderColor: 'rgba(255, 255, 255, 0.2)',
                        borderWidth: 1,
                        displayColors: true,
                        boxWidth: 12,
                        boxHeight: 12,
                        usePointStyle: true
                    }
                },
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            },
            plugins: [ChartDataLabels]
        });
    });
</script>

<!-- Cargar bibliotecas necesarias -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Limpiar todas las notas y acciones de seguimiento al cargar la página
        document.querySelectorAll('textarea[id^="notas-grafico-"]').forEach(textarea => {
            // Limpiar el contenido del textarea
            textarea.value = '';
            
            // Limpiar localStorage para este gráfico
            const graficoId = textarea.dataset.grafico;
            if (graficoId) {
                localStorage.removeItem(`notas-${graficoId}`);
            }
        });
        
        // Limpiar también el campo de acciones de seguimiento
        const accionesSeguimiento = document.getElementById('acciones-seguimiento');
        if (accionesSeguimiento) {
            accionesSeguimiento.value = '';
            localStorage.removeItem('acciones-seguimiento');
        }
        
        // Configurar botón para generar PDF
        document.getElementById('generarPdfBtn').addEventListener('click', function() {
            descargarPDF();
        });
        
        // Configurar botones de limpieza para todos los textareas
        document.querySelectorAll('.limpiar-notas').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const textarea = document.getElementById(targetId);
                
                if (textarea) {
                    // Pregunta de confirmación
                    if (textarea.value.trim() !== '' && confirm('¿Estás seguro de que deseas borrar todo el contenido de este campo?')) {
                        textarea.value = '';
                        
                        // Si hay un localStorage asociado, limpiarlo también
                        if (textarea.id === 'acciones-seguimiento') {
                            localStorage.removeItem('acciones-seguimiento');
                        } else if (textarea.dataset.grafico) {
                            localStorage.removeItem(`notas-${textarea.dataset.grafico}`);
                        }
                        
                        // Mensaje de éxito
                        const notification = document.createElement('div');
                        notification.className = 'fixed bottom-4 right-4 bg-green-500 text-white py-2 px-4 rounded-lg shadow-lg z-50';
                        notification.textContent = 'Campo limpiado correctamente';
                        document.body.appendChild(notification);
                        
                        // Auto-eliminar después de 2 segundos
                        setTimeout(() => {
                            notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                            setTimeout(() => notification.remove(), 500);
                        }, 2000);
                    }
                }
            });
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener datos de PHP
        const incidenciasPorTipoIncidencia = <?= json_encode($incidenciasPorTipoIncidencia ?? []); ?>;

        let incidenciasMap = {};
        let mesesSet = new Set();

        // Procesar los datos para agrupar por tipo de incidencia y mes
        incidenciasPorTipoIncidencia.forEach(item => {
            let mes = item.mes;
            let tipo = item.tipo_incidencia || "Desconocido";
            let total = parseInt(item.total, 10) || 0;

            if (!incidenciasMap[tipo]) {
                incidenciasMap[tipo] = {};
            }
            incidenciasMap[tipo][mes] = total;
            mesesSet.add(mes);
        });

        // Convertir meses en array ordenado
        let mesesOrdenados = Array.from(mesesSet).sort();

        // Crear datasets por tipo de incidencia con colores más visibles
        // Solo incluir tipos de incidencia que tengan al menos una ocurrencia
        let datasets = Object.keys(incidenciasMap).filter(tipo => {
            // Verificar si este tipo tiene al menos un valor mayor a 0
            return Object.values(incidenciasMap[tipo]).some(valor => valor > 0);
        }).map((tipo, index) => {
            // Usar una paleta de colores definida para mejor visibilidad
            const paletaColores = [
                'rgba(54, 162, 235, 0.9)', // Azul
                'rgba(255, 99, 132, 0.9)',  // Rojo
                'rgba(255, 206, 86, 0.9)',  // Amarillo
                'rgba(75, 192, 192, 0.9)',  // Verde agua
                'rgba(153, 102, 255, 0.9)', // Púrpura
                'rgba(255, 159, 64, 0.9)',  // Naranja
                'rgba(40, 167, 69, 0.9)',   // Verde
                'rgba(111, 66, 193, 0.9)',  // Violeta
                'rgba(23, 162, 184, 0.9)',  // Cyan
                'rgba(220, 53, 69, 0.9)'    // Rojo oscuro
            ];
            
            const color = paletaColores[index % paletaColores.length];
            
            return {
                label: tipo,
                data: mesesOrdenados.map(mes => incidenciasMap[tipo][mes] || 0),
                borderWidth: 1,
                backgroundColor: color,
                // Añadir borde para mejor visibilidad
                borderColor: color.replace('0.9', '1')
            };
        });
        
        // Calcular el valor máximo para configurar el eje Y
        let maxValue = 0;
        datasets.forEach(dataset => {
            const datasetMax = Math.max(...dataset.data);
            if (datasetMax > maxValue) maxValue = datasetMax;
        });

        // Verificar si el canvas existe antes de crear el gráfico
        const canvas = document.getElementById('incidenciasTipoChart');
        if (!canvas) {
            console.error("Error: No se encontró el canvas 'incidenciasTipoChart'");
            return;
        }

        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: mesesOrdenados,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 30,
                        right: 40,
                        top: 40,
                        bottom: 30
                    }
                },
                plugins: {
                    legend: { 
                        position: 'top',
                        labels: {
                            boxWidth: 15,
                            padding: 20,
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 13,
                                weight: 'bold'
                            }
                        }
                    }
                },
                scales: {
                    x: { 
                        title: { 
                            display: true, 
                            text: 'Mes',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 15,
                                weight: 'bold'
                            },
                            color: '#1f2937',
                            padding: {top: 15, bottom: 5}
                        },
                        ticks: {
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 11,
                                weight: 'bold'
                            },
                            padding: 8,
                            color: '#374151'
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        min: 0,
                        // Eliminar el max manual y dejar que Chart.js lo calcule automáticamente
                        title: {
                            display: true,
                            text: 'Cantidad de Organismos',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 15,
                                weight: 'bold'
                            },
                            color: '#1f2937',
                            padding: {top: 5, bottom: 15}
                        },
                        grid: {
                            color: 'rgba(226, 232, 240, 0.8)',
                            borderDash: [4, 4],
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            // Dejar que Chart.js calcule automáticamente el stepSize apropiado
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 12,
                                weight: 'bold'
                            },
                            padding: 10,
                            color: '#374151'
                        }
                    }
                }
            }
        });

        // Función para generar colores aleatorios
        function getRandomColor() {
            return `rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.6)`;
        }
    });
</script>

<style>
/* Estilos para mejorar la apariencia */
.bg-white {
    transition: all 0.3s ease;
}

.bg-white:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    border-radius: 0.5rem;
    overflow: hidden;
}

th {
    background-color: #f9fafb;
    font-weight: 600;
}

tr:hover {
    background-color: #f9fafb;
}

canvas {
    border-radius: 0.5rem;
}

textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.25);
}

/* Nuevos estilos mejorados */
body {
    background-color: #f5f7fa;
    color: #2d3748;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.bg-white {
    background-color: #ffffff;
    border-radius: 0.75rem;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.04), 0 1px 3px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.bg-white:hover {
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    transform: translateY(-2px);
}

h1, h2, h3, h4 {
    font-weight: 700;
    color: #1a202c;
}

h1 {
    font-size: 1.875rem;
    letter-spacing: -0.025em;
}

h3 {
    position: relative;
    padding-bottom: 0.5rem;
}

h3:after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    height: 3px;
    width: 40px;
    background-color: #3b82f6;
    border-radius: 3px;
}

select, button {
    transition: all 0.2s ease;
}

select {
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236b7280'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 1em;
}

select:hover {
    border-color: #a0aec0;
}

table {
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

th {
    background-color: #edf2f7;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    color: #4a5568;
    padding: 1rem 0.75rem;
}

td {
    padding: 0.75rem;
    border-bottom: 1px solid #edf2f7;
}

tr:last-child td {
    border-bottom: none;
}

.rounded-lg {
    border-radius: 0.75rem;
}

textarea {
    resize: vertical;
    min-height: 80px;
    background-color: #f9fafc;
    transition: all 0.2s ease;
}

textarea:hover {
    background-color: #f8faff;
}

button {
    position: relative;
    overflow: hidden;
}

button:after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.1);
    transform: translateX(-100%);
    transition: transform 0.3s ease-out;
}

button:hover:after {
    transform: translateX(0);
}

/* Estilos para las tarjetas de resumen */
.grid-cols-3 .bg-white {
    position: relative;
    overflow: hidden;
}

.grid-cols-3 .bg-white:before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 5px;
}

.grid-cols-3 .bg-white:nth-child(1):before {
    background-color: #3b82f6;
}

.grid-cols-3 .bg-white:nth-child(2):before {
    background-color: #f59e0b;
}

.grid-cols-3 .bg-white:nth-child(3):before {
    background-color: #10b981;
}

/* Estilos para los gráficos */
canvas {
    padding: 0.5rem;
    background-color: #ffffff;
}

/* Mejora para los botones de generación de reportes */
#generarPdfBtn {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
    border: none;
    font-weight: 600;
    letter-spacing: 0.5px;
}

#generarPdfBtn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.6);
}

/* Estilo para tooltip */
[data-tooltip] {
    position: relative;
}

[data-tooltip]:after {
    content: attr(data-tooltip);
    position: absolute;
    bottom: 130%;
    left: 50%;
    transform: translateX(-50%);
    background-color: #1e293b;
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
    opacity: 0;
    visibility: hidden;
    transition: all 0.2s ease;
    white-space: nowrap;
    z-index: 10;
}

[data-tooltip]:hover:after {
    opacity: 1;
    visibility: visible;
}

.grafica-circular {
  width: 100%;
  max-width: 800px;
  height: 700px;
  margin: 0 auto;
  display: block;
}
.grafica-barra {
  width: 100%;
  max-width: 1000px;
  height: 600px;
  margin: 0 auto;
  display: block;
}
</style>

<!-- Script para cambiar la plaga seleccionada -->
<script>
    function cambiarPlaga(plaga) {
        if (plaga) {
            console.log('Cambiando a plaga: ' + plaga);
            // Construir URL manteniendo el sede_id si existe
            const urlParams = new URLSearchParams(window.location.search);
            const sedeId = urlParams.get('sede_id');
            const mes = urlParams.get('mes');
            
            let url = '<?= base_url('locations') ?>?plaga=' + encodeURIComponent(plaga);
            
            if (sedeId) {
                url += '&sede_id=' + sedeId;
            }
            
            if (mes) {
                url += '&mes=' + mes;
            }
            
            window.location.href = url;
        } else {
            console.log('No se seleccionó ninguna plaga');
        }
    }
    
    function cambiarPlagaCaptura(plaga) {
        // Usar la misma función que cambiarPlaga, ya que ambas usan el mismo parámetro
        cambiarPlaga(plaga);
    }
    
    // Nuevas funciones para comparación de meses (compatibles con interfaz visual)
    function seleccionarTodosMeses() {
        const selector = document.getElementById('meses-comparacion');
        for (let option of selector.options) {
            if (!option.disabled) {
                option.selected = true;
            }
        }
        // Sincronizar con interfaz visual
        if (typeof seleccionarTodosMesesVisual === 'function') {
            const checkboxes = document.querySelectorAll('.mes-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = true);
            actualizarSeleccionMeses();
        }
        actualizarComparacionMeses();
    }
    
    function limpiarSeleccionMeses() {
        const selector = document.getElementById('meses-comparacion');
        for (let option of selector.options) {
            option.selected = false;
        }
        // Sincronizar con interfaz visual
        if (typeof limpiarSeleccionMesesVisual === 'function') {
            const checkboxes = document.querySelectorAll('.mes-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = false);
            actualizarSeleccionMeses();
        }
        actualizarComparacionMeses();
    }
    
    // Funciones para el selector de plagas (compatibles con interfaz visual)
         function seleccionarTodasPlagas() {
         const selector = document.getElementById('plagas-comparacion');
         for (let option of selector.options) {
             if (!option.disabled) {
                 option.selected = true;
             }
         }
         // Sincronizar con interfaz visual
         if (typeof seleccionarTodasPlagasVisual === 'function') {
             const checkboxes = document.querySelectorAll('.plaga-checkbox');
             checkboxes.forEach(checkbox => checkbox.checked = true);
             actualizarSeleccionPlagas();
         }
         actualizarContadorPlagas();
         actualizarComparacionMeses();
     }

     function limpiarSeleccionPlagas() {
         const selector = document.getElementById('plagas-comparacion');
         for (let option of selector.options) {
             option.selected = false;
         }
         // Sincronizar con interfaz visual
         if (typeof limpiarSeleccionPlagasVisual === 'function') {
             const checkboxes = document.querySelectorAll('.plaga-checkbox');
             checkboxes.forEach(checkbox => checkbox.checked = false);
             actualizarSeleccionPlagas();
         }
         actualizarContadorPlagas();
         actualizarComparacionMeses();
     }

           // Función para mostrar/ocultar configuración avanzada
     function toggleConfiguracionAvanzada() {
         const configDiv = document.getElementById('configuracion-avanzada');
         const btnConfig = document.getElementById('btn-config-avanzada');
         
         if (configDiv.style.display === 'none' || configDiv.style.display === '') {
             // Mostrar configuración con animación
             configDiv.style.display = 'block';
             configDiv.style.opacity = '0';
             configDiv.style.transform = 'translateY(-10px)';
             
             // Aplicar animación
             setTimeout(() => {
                 configDiv.style.opacity = '1';
                 configDiv.style.transform = 'translateY(0)';
             }, 10);
             
             btnConfig.innerHTML = `
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                 </svg>
                 Ocultar Configuración
             `;
             btnConfig.classList.remove('bg-gray-100', 'text-gray-700');
             btnConfig.classList.add('bg-blue-100', 'text-blue-700');
         } else {
             // Ocultar configuración con animación
             configDiv.style.opacity = '0';
             configDiv.style.transform = 'translateY(-10px)';
             
             setTimeout(() => {
                 configDiv.style.display = 'none';
             }, 300);
             
             btnConfig.innerHTML = `
                 <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                 </svg>
                 Configuración Avanzada
             `;
             btnConfig.classList.remove('bg-blue-100', 'text-blue-700');
             btnConfig.classList.add('bg-gray-100', 'text-gray-700');
         }
     }

     // Funciones para la nueva interfaz visual de meses
     function actualizarSeleccionMeses() {
         const checkboxes = document.querySelectorAll('.mes-checkbox');
         const selectorOculto = document.getElementById('meses-comparacion');
         
         // Limpiar selecciones del selector oculto
         Array.from(selectorOculto.options).forEach(option => option.selected = false);
         
         // Marcar opciones seleccionadas según checkboxes
         const seleccionados = [];
         checkboxes.forEach(checkbox => {
             if (checkbox.checked) {
                 const valor = checkbox.value;
                 const nombre = checkbox.dataset.nombre;
                 seleccionados.push(nombre);
                 
                 // Seleccionar en el selector oculto
                 Array.from(selectorOculto.options).forEach(option => {
                     if (option.value === valor) {
                         option.selected = true;
                     }
                 });
             }
         });
         
         // Actualizar contador
         const contador = document.getElementById('contador-meses');
         if (seleccionados.length === 0) {
             contador.textContent = 'Selecciona los meses a comparar';
             contador.className = 'text-xs text-gray-500';
         } else if (seleccionados.length === 1) {
             contador.textContent = `1 mes seleccionado: ${seleccionados[0]}`;
             contador.className = 'text-xs text-blue-600';
         } else {
             contador.textContent = `${seleccionados.length} meses seleccionados`;
             contador.className = 'text-xs text-blue-600';
         }
     }

     function seleccionarTodosMesesVisual() {
         const checkboxes = document.querySelectorAll('.mes-checkbox');
         checkboxes.forEach(checkbox => {
             checkbox.checked = true;
         });
         actualizarSeleccionMeses();
     }

     function limpiarSeleccionMesesVisual() {
         const checkboxes = document.querySelectorAll('.mes-checkbox');
         checkboxes.forEach(checkbox => {
             checkbox.checked = false;
         });
         actualizarSeleccionMeses();
     }

     // Funciones para la nueva interfaz visual de plagas
     function cargarPlagasEnInterfazVisual(plagas) {
         const container = document.getElementById('lista-plagas-visual');
         const contador = document.getElementById('contador-plagas');
         
         if (!plagas || plagas.length === 0) {
             container.innerHTML = `
                 <div class="text-center text-gray-500 py-4">
                     <svg class="w-8 h-8 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                     </svg>
                     <p class="text-sm">No hay plagas disponibles</p>
                 </div>
             `;
             contador.textContent = 'No hay plagas disponibles';
             return;
         }

        // Crear checkboxes para cada plaga
        container.innerHTML = plagas.map(plaga => `
            <label class="flex items-center p-2 rounded-lg hover:bg-white transition-colors cursor-pointer group">
                <input 
                    type="checkbox" 
                    value="${plaga}" 
                    checked
                    class="plaga-checkbox w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
                    onchange="actualizarSeleccionPlagas()">
                <span class="ml-3 text-sm text-gray-700 group-hover:text-gray-900">${normalizarNombreJS(plaga)}</span>
                 <span class="ml-auto">
                     <div class="w-3 h-3 rounded-full bg-green-400 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                 </span>
             </label>
         `).join('');

         // Actualizar contador inicial
         contador.textContent = `${plagas.length} plagas disponibles (todas seleccionadas)`;
         contador.className = 'text-xs text-green-600';
     }

     function actualizarSeleccionPlagas() {
         const checkboxes = document.querySelectorAll('.plaga-checkbox');
         const selectorOculto = document.getElementById('plagas-comparacion');
         
         // Limpiar selecciones del selector oculto
         Array.from(selectorOculto.options).forEach(option => option.selected = false);
         
         // Marcar opciones seleccionadas según checkboxes
         const seleccionados = [];
         checkboxes.forEach(checkbox => {
             if (checkbox.checked) {
                 const valor = checkbox.value;
                 seleccionados.push(valor);
                 
                 // Seleccionar en el selector oculto
                 Array.from(selectorOculto.options).forEach(option => {
                     if (option.value === valor) {
                         option.selected = true;
                     }
                 });
             }
         });
         
         // Actualizar contador
         const contador = document.getElementById('contador-plagas');
         const total = checkboxes.length;
         
         if (seleccionados.length === 0) {
             contador.textContent = 'Ninguna plaga seleccionada (se mostrarán todas)';
             contador.className = 'text-xs text-amber-600';
         } else if (seleccionados.length === total) {
             contador.textContent = `Todas las plagas seleccionadas (${total})`;
             contador.className = 'text-xs text-green-600';
         } else {
             contador.textContent = `${seleccionados.length} de ${total} plagas seleccionadas`;
             contador.className = 'text-xs text-blue-600';
         }
     }

     function seleccionarTodasPlagasVisual() {
         const checkboxes = document.querySelectorAll('.plaga-checkbox');
         checkboxes.forEach(checkbox => {
             checkbox.checked = true;
         });
         actualizarSeleccionPlagas();
     }

     function limpiarSeleccionPlagasVisual() {
         const checkboxes = document.querySelectorAll('.plaga-checkbox');
         checkboxes.forEach(checkbox => {
             checkbox.checked = false;
         });
         actualizarSeleccionPlagas();
     }

     function aplicarConfiguracion() {
         // Actualizar ambas selecciones
         actualizarSeleccionMeses();
         actualizarSeleccionPlagas();
         
         // Trigger de actualización de la gráfica
         actualizarComparacionMeses();
         
         // Feedback visual
         const boton = event.target;
         const textoOriginal = boton.innerHTML;
         
         boton.innerHTML = `
             <svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
             </svg>
             Actualizando...
         `;
         boton.disabled = true;
         
         setTimeout(() => {
             boton.innerHTML = textoOriginal;
             boton.disabled = false;
         }, 2000);
     }
    
         function actualizarComparacionMeses() {
         const selectorMeses = document.getElementById('meses-comparacion');
         const selectorPlagas = document.getElementById('plagas-comparacion');
         
         const mesesSeleccionados = Array.from(selectorMeses.selectedOptions).map(option => option.value);
         const plagasSeleccionadas = Array.from(selectorPlagas.selectedOptions).map(option => option.value);
         
         if (mesesSeleccionados.length === 0) {
             console.log('No hay meses seleccionados');
             mostrarMensajeEnCanvas('📅 Selecciona al menos un mes para comparar');
             return;
         }
         
         // Verificar si hay plagas cargadas en el selector
         const hayPlagasDisponibles = selectorPlagas.options.length > 0 && !selectorPlagas.options[0].disabled;
         
         if (!hayPlagasDisponibles) {
             console.log('No hay plagas cargadas, esto no debería suceder');
             mostrarMensajeEnCanvas('⚠️ Error: Las plagas no están cargadas\n🔄 Recarga la página');
             return;
         }
         
         // Si no hay plagas seleccionadas pero hay disponibles, usar todas
         const plagasFinales = plagasSeleccionadas.length > 0 ? plagasSeleccionadas : 
                             Array.from(selectorPlagas.options).map(opt => opt.value).filter(val => val);
         
         console.log('Meses seleccionados:', mesesSeleccionados);
         console.log('Plagas seleccionadas:', plagasSeleccionadas);
         console.log('Plagas finales a usar:', plagasFinales);
         
         cargarDatosComparacionMeses(mesesSeleccionados, plagasFinales);
     }
    
    function cargarDatosComparacionMeses(meses, plagas = []) {
            const urlParams = new URLSearchParams(window.location.search);
            const sedeId = urlParams.get('sede_id');
        
        if (!sedeId) {
            console.log('No hay sede seleccionada');
            mostrarMensajeEnCanvas('⚠️ Por favor selecciona una planta en el selector superior');
            return;
        }
        
        console.log('Cargando datos para sede:', sedeId, 'meses:', meses, 'plagas:', plagas);
        
        // Mostrar indicador de carga
        mostrarMensajeEnCanvas('Cargando datos...');
        
        // Crear URL para AJAX
        const url = '<?= base_url('locations/getDatosComparacionMeses') ?>';
        const formData = new FormData();
        formData.append('sede_id', sedeId);
        formData.append('meses', JSON.stringify(meses));
        formData.append('plagas_filtro', JSON.stringify(plagas));
        
        console.log('Enviando petición a:', url);
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Respuesta recibida:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            if (data.success) {
                                if (data.datos && data.datos.length > 0) {
                    // Las plagas ya están cargadas desde areasCapturasPorPlaga, solo actualizar la gráfica
                    console.log('📊 Datos recibidos para actualizar gráfica:', data.datos);
                    actualizarGraficaComparacion(data.datos, meses, plagas);
                } else {
                    console.log('No hay datos disponibles para los meses seleccionados');
                    mostrarMensajeEnCanvas('No hay datos disponibles para los meses seleccionados');
                }
            } else {
                console.error('Error al cargar datos:', data.message);
                mostrarMensajeEnCanvas('Error: ' + (data.message || 'Error desconocido'));
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            mostrarMensajeEnCanvas('❌ Error de conexión\n🔄 Intenta recargar la página o contacta al administrador');
        });
    }
    
    function mostrarMensajeEnCanvas(mensaje) {
        const canvas = document.getElementById('plagasMayorPresenciaChart');
        if (!canvas) return;
        
        // Destruir gráfico anterior si existe
        if (chartComparacionPlagas) {
            chartComparacionPlagas.destroy();
            chartComparacionPlagas = null;
        }
        
        const ctx = canvas.getContext('2d');
        
        // Asegurar que el canvas tenga un tamaño mínimo
        canvas.width = Math.max(canvas.width, 800);
        canvas.height = Math.max(canvas.height, 400);
        
        // Limpiar canvas
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        // Dibujar fondo sutil
        ctx.fillStyle = '#f8f9fa';
        ctx.fillRect(0, 0, canvas.width, canvas.height);
        
        // Configurar texto principal
        ctx.font = 'bold 18px "Segoe UI", Arial, sans-serif';
        ctx.fillStyle = '#495057';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        
        // Calcular posición central
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        
        // Manejar mensajes de múltiples líneas
        const lineas = mensaje.split('\n');
        const alturaLinea = 25;
        const inicioY = centerY - ((lineas.length - 1) * alturaLinea / 2);
        
        lineas.forEach((linea, index) => {
            const yPosition = inicioY + (index * alturaLinea);
            
            // Si es la primera línea y contiene emojis, usar tamaño normal
            if (index === 0) {
                ctx.font = 'bold 18px "Segoe UI", Arial, sans-serif';
                ctx.fillStyle = '#495057';
            } else {
                // Líneas adicionales con menor énfasis
                ctx.font = '14px "Segoe UI", Arial, sans-serif';
                ctx.fillStyle = '#6c757d';
            }
            
            ctx.fillText(linea, centerX, yPosition);
        });
        
        // Si es el mensaje de "seleccionar planta", agregar texto adicional
        if (mensaje.includes('selecciona una planta')) {
            ctx.font = '14px "Segoe UI", Arial, sans-serif';
            ctx.fillStyle = '#6c757d';
            ctx.fillText('Usa el selector "Seleccione una planta" en la parte superior', centerX, centerY + 40);
        }
        
        // Agregar borde
        ctx.strokeStyle = '#dee2e6';
        ctx.lineWidth = 2;
        ctx.strokeRect(1, 1, canvas.width - 2, canvas.height - 2);
    }
    
    // Función legacy para compatibilidad
    function cambiarMes(mes) {
        const selector = document.getElementById('meses-comparacion');
        // Limpiar selección actual
        for (let option of selector.options) {
            option.selected = false;
        }
        // Seleccionar el mes específico
        for (let option of selector.options) {
            if (option.value === mes) {
                option.selected = true;
                break;
            }
        }
        actualizarComparacionMeses();
    }
    
         // Función NUEVA para cargar TODAS las plagas del sistema (igual que la gráfica "Áreas con Mayor Incidencia")
         function cargarTodasLasPlagasEnSelector(todasLasPlagas) {
        const selectorPlagas = document.getElementById('plagas-comparacion');
        
        if (!selectorPlagas) {
            console.error('No se encontró el selector de plagas');
            return;
        }
        
        console.log('🚀 Cargando TODAS las plagas del sistema:', todasLasPlagas);
        
        // Limpiar selector
        selectorPlagas.innerHTML = '';
        
        if (todasLasPlagas.length === 0) {
            // Si no hay plagas, mostrar mensaje
            const option = document.createElement('option');
            option.disabled = true;
            option.textContent = 'No hay plagas disponibles';
            selectorPlagas.appendChild(option);
            console.log('❌ No hay plagas disponibles en el sistema');
            
            // Actualizar interfaz visual - sin plagas
            cargarPlagasEnInterfazVisual([]);
        } else {
            // Agregar opciones de plagas (ya están ordenadas alfabéticamente)
            todasLasPlagas.forEach((plaga, index) => {
                const option = document.createElement('option');
                option.value = plaga;
                option.textContent = normalizarNombreJS(plaga);
                option.selected = true; // Seleccionar todas por defecto
                selectorPlagas.appendChild(option);
                console.log(`✅ Plaga ${index + 1}: ${plaga} agregada al selector`);
            });
            
            console.log(`🎉 ÉXITO: ${todasLasPlagas.length} plagas cargadas desde areasCapturasPorPlaga`);
            
            // Cargar también en la interfaz visual nueva
            cargarPlagasEnInterfazVisual(todasLasPlagas);
        }
        
        // Actualizar contador de plagas seleccionadas
        actualizarContadorPlagas();
        
        return todasLasPlagas;
    }

     // Función para cargar las plagas disponibles en el selector (MÉTODO ANTERIOR - mantener para compatibilidad)
     function cargarPlagasDisponibles(datos) {
         const selectorPlagas = document.getElementById('plagas-comparacion');
         
         if (!selectorPlagas) {
             console.error('No se encontró el selector de plagas');
             return;
         }
         
         // Obtener todas las plagas únicas de TODOS los datos recibidos
         console.log('📊 Datos recibidos para cargar plagas:', datos);
         
         const todasLasPlagas = [...new Set(datos.flatMap(mes => {
             console.log(`📅 Mes: ${mes.mes}, Plagas encontradas:`, mes.plagas.map(p => p.tipo_plaga));
             return mes.plagas.map(p => p.tipo_plaga);
         }))];
         
         console.log('🔍 TODAS las plagas únicas encontradas:', todasLasPlagas);
         
         // Verificar si ya están cargadas TODAS las plagas (comparar las plagas actuales con las nuevas)
         const plagasActuales = Array.from(selectorPlagas.options).map(opt => opt.value).filter(val => val);
         const plagasIguales = todasLasPlagas.length === plagasActuales.length && 
                             todasLasPlagas.every(plaga => plagasActuales.includes(plaga));
         
         if (plagasIguales && selectorPlagas.options.length > 0 && !selectorPlagas.options[0].disabled) {
             console.log('✅ Las plagas ya están cargadas correctamente, no es necesario actualizar');
             return; // Ya están cargadas
         }
         
         console.log('🔄 Actualizando selector con todas las plagas:', todasLasPlagas);
         
         // Limpiar selector
         selectorPlagas.innerHTML = '';
         
         if (todasLasPlagas.length === 0) {
             // Si no hay plagas, mostrar mensaje
             const option = document.createElement('option');
             option.disabled = true;
             option.textContent = 'No hay plagas disponibles';
             selectorPlagas.appendChild(option);
             console.log('❌ No hay plagas disponibles en los datos');
         } else {
            // Agregar opciones de plagas (ordenadas alfabéticamente)
            todasLasPlagas.sort().forEach((plaga, index) => {
                const option = document.createElement('option');
                option.value = plaga;
                option.textContent = normalizarNombreJS(plaga);
                option.selected = true; // Seleccionar todas por defecto
                 selectorPlagas.appendChild(option);
                 console.log(`✅ Plaga ${index + 1}: ${plaga} agregada al selector`);
             });
             
             console.log(`🎉 Plagas cargadas exitosamente: ${todasLasPlagas.length} plagas totales`);
         }
         
         // Actualizar contador de plagas seleccionadas
         actualizarContadorPlagas();
         
         return todasLasPlagas; // Retornar las plagas para uso posterior
     }
     
     // Función para actualizar el contador de plagas seleccionadas
     function actualizarContadorPlagas() {
         const selector = document.getElementById('plagas-comparacion');
         const label = document.querySelector('label[for="plagas-comparacion"]');
         
         if (!selector || !label) return;
         
         const seleccionadas = Array.from(selector.selectedOptions).length;
         const total = selector.options.length;
         
         // Restaurar texto original si no hay datos específicos
         if (total === 0 || (total === 1 && selector.options[0].disabled)) {
             label.textContent = 'Seleccionar plagas a mostrar:';
             return;
         }
         
         if (seleccionadas === 0) {
             label.textContent = 'Seleccionar plagas a mostrar: (Ninguna seleccionada)';
             label.style.color = '#dc2626'; // Rojo para indicar problema
         } else if (seleccionadas === total) {
             label.textContent = `Seleccionar plagas a mostrar: (Todas - ${seleccionadas})`;
             label.style.color = '#059669'; // Verde para indicar que están todas
         } else {
             label.textContent = `Seleccionar plagas a mostrar: (${seleccionadas} de ${total})`;
             label.style.color = '#2563eb'; // Azul para selección parcial
        }
    }
</script>

<!-- Script para gráfica de Comparación de Plagas por Mes -->
<script>
    let chartComparacionPlagas; // Variable global para el gráfico
    
    document.addEventListener("DOMContentLoaded", function() {
        console.log('🚀 Iniciando carga de gráfica de comparación de plagas');
        
        // Inicializar la nueva interfaz visual
        actualizarSeleccionMeses();
        
        const selector = document.getElementById('meses-comparacion');
        const selectorPlagas = document.getElementById('plagas-comparacion');
        
        // Verificar elementos necesarios
        console.log('Elementos encontrados:', {
            selectorMeses: !!selector,
            selectorPlagas: !!selectorPlagas,
            canvas: !!document.getElementById('plagasMayorPresenciaChart')
        });
        
        // Verificar si hay opciones disponibles
        if (!selector || selector.options.length === 0) {
            console.log('No hay meses disponibles en el selector');
            mostrarMensajeEnCanvas('📅 No hay meses disponibles para mostrar');
            return;
        }
        
        console.log('Meses disponibles en el selector:');
        for (let i = 0; i < selector.options.length; i++) {
            const option = selector.options[i];
            console.log(`- ${option.text} (${option.value}) ${option.disabled ? '[DESHABILITADO]' : ''}`);
        }
        
        // Seleccionar el mes actual por defecto si está disponible
        const mesSeleccionado = "<?= $mesSeleccionado ?? ''; ?>";
        let mesSelecionado = false;
        
        if (mesSeleccionado) {
            for (let option of selector.options) {
                if (option.value === mesSeleccionado) {
                    option.selected = true;
                    mesSelecionado = true;
                    break;
                }
            }
        }
        
        // Si no hay mes seleccionado, seleccionar el primero disponible
        if (!mesSelecionado && selector.options.length > 0) {
            // Seleccionar el primer mes que no esté deshabilitado
            for (let option of selector.options) {
                if (!option.disabled) {
                    option.selected = true;
                    console.log('Seleccionado mes por defecto:', option.value);
                    break;
                }
            }
        }
        
        // Verificar si hay sede seleccionada
        const urlParams = new URLSearchParams(window.location.search);
        const sedeId = urlParams.get('sede_id');
        console.log('Sede ID encontrada:', sedeId);
        
                 if (!sedeId) {
             console.warn('No hay sede seleccionada. Por favor selecciona una planta primero.');
             // Mostrar mensaje más claro en el canvas
             mostrarMensajeEnCanvas('⚠️ Por favor selecciona una planta en el selector superior');
            return;
        }

        // Cargar datos iniciales
        console.log('Iniciando carga de datos...');
        
        // Agregar event listener al selector de plagas para actualizar contador
        if (selectorPlagas) {
            selectorPlagas.addEventListener('change', function() {
                actualizarContadorPlagas();
            });
        }
        
                // NUEVA LÓGICA: Usar los datos que YA están disponibles (igual que la gráfica "Áreas con Mayor Incidencia")
        const areasCapturasPorPlaga = <?= json_encode($areasCapturasPorPlaga ?? []); ?>;
        console.log('📊 Datos areasCapturasPorPlaga disponibles:', areasCapturasPorPlaga);
        
        if (areasCapturasPorPlaga && areasCapturasPorPlaga.length > 0) {
            // Extraer TODAS las plagas únicas del sistema (igual que hace la otra gráfica)
            const todasLasPlagasDelSistema = [...new Set(areasCapturasPorPlaga.map(item => item.tipo_plaga))].sort();
            console.log('🎯 TODAS las plagas del sistema encontradas:', todasLasPlagasDelSistema);
            
            // Cargar inmediatamente las plagas en el selector
            cargarTodasLasPlagasEnSelector(todasLasPlagasDelSistema);
            
            // Ahora cargar los datos específicos de los meses seleccionados
            const mesesSeleccionadosIniciales = Array.from(selector.selectedOptions).map(option => option.value);
            if (mesesSeleccionadosIniciales.length > 0) {
                console.log('📅 Cargando datos para meses seleccionados:', mesesSeleccionadosIniciales);
                cargarDatosComparacionMeses(mesesSeleccionadosIniciales);
            } else {
                mostrarMensajeEnCanvas('✅ Todas las plagas cargadas\n📅 Selecciona los meses que deseas comparar para continuar');
            }
        } else {
            console.log('No hay datos de plagas disponibles');
            mostrarMensajeEnCanvas('📅 No hay datos de plagas disponibles\n⚠️ Contacta al administrador del sistema');
        }
        
        // Como backup, si hay datos de PHP disponibles, usarlos temporalmente
        const plagasMayorPresenciaBackup = <?= json_encode($plagasMayorPresencia ?? []); ?>;
        const mesSeleccionadoBackup = "<?= $mesSeleccionado ?? ''; ?>";
        
        if (plagasMayorPresenciaBackup && plagasMayorPresenciaBackup.length > 0 && mesSeleccionadoBackup) {
            console.log('Usando datos de backup del PHP:', plagasMayorPresenciaBackup);
            // Crear estructura compatible con la nueva función
            const datosBackup = [{
                mes: mesSeleccionadoBackup,
                plagas: plagasMayorPresenciaBackup
            }];
            actualizarGraficaComparacion(datosBackup, [mesSeleccionadoBackup]);
        } else {
            // Si no hay datos de backup, mostrar mensaje inicial
            mostrarMensajeEnCanvas('🏭 Datos de la planta cargados\n📅 Selecciona los meses que deseas comparar para comenzar');
        }
    });
    
    function actualizarGraficaComparacion(datos, mesesSeleccionados, plagasSeleccionadas = []) {
        console.log('Actualizando gráfica con datos:', datos, 'meses:', mesesSeleccionados, 'plagas:', plagasSeleccionadas);
        
        const canvas = document.getElementById('plagasMayorPresenciaChart');
        if (!canvas) {
            console.error("Error: No se encontró el canvas 'plagasMayorPresenciaChart'");
            return;
        }
        
        // Destruir gráfico anterior si existe
        if (chartComparacionPlagas) {
            chartComparacionPlagas.destroy();
        }
        
        // Verificar que hay datos
        if (!datos || datos.length === 0) {
            console.log('No hay datos para mostrar');
            mostrarMensajeEnCanvas('No hay datos para mostrar');
            return;
        }
        
        // ✅ ORDENAR MESES CRONOLÓGICAMENTE
        const mesesOrdenadosCronologicamente = [...mesesSeleccionados].sort();
        console.log('Meses originales:', mesesSeleccionados);
        console.log('Meses ordenados cronológicamente:', mesesOrdenadosCronologicamente);
        
        // Preparar datos para gráfico de barras agrupadas
        let todasLasPlagas = [...new Set(datos.flatMap(mes => mes.plagas.map(p => p.tipo_plaga)))];
        
        // Filtrar plagas si hay selección específica
        if (plagasSeleccionadas && plagasSeleccionadas.length > 0) {
            todasLasPlagas = todasLasPlagas.filter(plaga => plagasSeleccionadas.includes(plaga));
            console.log('Plagas filtradas por selección:', todasLasPlagas);
        } else {
            console.log('Mostrando todas las plagas encontradas:', todasLasPlagas);
        }
        
        if (todasLasPlagas.length === 0) {
            console.log('No se encontraron plagas en los datos después del filtro');
            if (plagasSeleccionadas.length > 0) {
                mostrarMensajeEnCanvas('🚫 No hay datos para las plagas seleccionadas\n💡 Intenta seleccionar otras plagas o cambiar los meses');
            } else {
                mostrarMensajeEnCanvas('📊 No hay datos de plagas disponibles\n📅 Verifica que los meses seleccionados tengan información');
            }
            return;
        }
        
        const coloresPlagas = generateColorPalette(todasLasPlagas.length);
        
        // Crear datasets para cada plaga - USANDO ORDEN CRONOLÓGICO
        const datasets = todasLasPlagas.map((plaga, index) => {
            const datosPlaga = mesesOrdenadosCronologicamente.map(mes => {
                const mesData = datos.find(d => d.mes === mes);
                if (mesData) {
                    const plagaData = mesData.plagas.find(p => p.tipo_plaga === plaga);
                    return plagaData ? parseInt(plagaData.total_organismos) : 0;
                }
                return 0;
            });
            
            return {
                label: normalizarNombreJS(plaga),
                data: datosPlaga,
                backgroundColor: coloresPlagas[index],
                borderColor: coloresPlagas[index].replace('0.6', '1'),
                borderWidth: 2,
                borderRadius: 4,
                borderSkipped: false,
            };
        });
        
        // Crear etiquetas de meses - USANDO ORDEN CRONOLÓGICO
        const selector = document.getElementById('meses-comparacion');
        const nombresMeses = mesesOrdenadosCronologicamente.map(mes => {
            const option = Array.from(selector.options).find(opt => opt.value === mes);
            return option ? option.text : mes;
        });
        
        console.log('Creando gráfica con datasets:', datasets);
        console.log('Etiquetas de meses:', nombresMeses);

        const ctx = canvas.getContext('2d');
        chartComparacionPlagas = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: nombresMeses,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        top: 80,
                        right: 40,
                        bottom: 30,
                        left: 40
                    }
                },
                plugins: {
                    datalabels: {
                        display: true,
                        anchor: function(context) {
                            // Alternar entre arriba y abajo según el índice del dataset
                            return context.datasetIndex % 2 === 0 ? 'end' : 'start';
                        },
                        align: function(context) {
                            // Alternar entre arriba y abajo según el índice del dataset
                            return context.datasetIndex % 2 === 0 ? 'top' : 'bottom';
                        },
                        formatter: function(value, context) {
                            // Solo mostrar valores mayores a 0
                            return value > 0 ? value : '';
                        },
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            size: 12,
                            weight: 'bold'
                        },
                        color: function(context) {
                            // Color diferente para arriba y abajo para mejor contraste
                            return context.datasetIndex % 2 === 0 ? '#374151' : '#1f2937';
                        },
                        backgroundColor: function(context) {
                            // Fondo diferente para arriba y abajo
                            return context.datasetIndex % 2 === 0 ? 'rgba(255, 255, 255, 0.9)' : 'rgba(248, 250, 252, 0.9)';
                        },
                        borderColor: 'rgba(0, 0, 0, 0.2)',
                        borderWidth: 1,
                        borderRadius: 3,
                        padding: {
                            top: 1,
                            bottom: 1,
                            left: 3,
                            right: 3
                        },
                        offset: function(context) {
                            // Agregar un pequeño offset para separar más los números
                            return context.datasetIndex % 2 === 0 ? 4 : -4;
                        },
                        listeners: {
                            enter: function(context, event) {
                                // Cuando el mouse entra al número, mostrar tooltip
                                const chart = context.chart;
                                const tooltip = chart.tooltip;
                                
                                // Crear evento simulado para activar el tooltip
                                const chartPosition = Chart.helpers.getRelativePosition(event, chart);
                                const datasetIndex = context.datasetIndex;
                                const dataIndex = context.dataIndex;
                                
                                // Mostrar tooltip manualmente
                                tooltip.setActiveElements([{
                                    datasetIndex: datasetIndex,
                                    index: dataIndex
                                }], {
                                    x: chartPosition.x,
                                    y: chartPosition.y
                                });
                                chart.update('none');
                                
                                // Cambiar cursor a pointer
                                chart.canvas.style.cursor = 'pointer';
                            },
                            leave: function(context, event) {
                                // Cuando el mouse sale del número, ocultar tooltip
                                const chart = context.chart;
                                const tooltip = chart.tooltip;
                                
                                // Ocultar tooltip
                                tooltip.setActiveElements([], {x: 0, y: 0});
                                chart.update('none');
                                
                                // Restaurar cursor
                                chart.canvas.style.cursor = 'default';
                            }
                        }
                    },
                    legend: { 
                        position: 'bottom',
                        align: 'center',
                        labels: {
                            boxWidth: 15,
                            padding: 15,
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 14,
                                weight: 'bold'
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    title: {
                        display: true,
                        text: generarTituloGrafica(mesesSeleccionados, plagasSeleccionadas),
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            size: 20,
                            weight: 'bold'
                        },
                        color: '#1f2937',
                        padding: {
                            top: 15,
                            bottom: 35
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const plaga = context.dataset.label;
                                const valor = context.raw;
                                const mes = context.label;
                                return `${plaga}: ${valor} organismos en ${mes}`;
                            }
                        },
                        backgroundColor: 'rgba(30, 41, 59, 0.9)',
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        borderColor: 'rgba(255, 255, 255, 0.3)',
                        borderWidth: 2,
                        padding: 10
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Meses',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 16,
                                weight: 'bold'
                            },
                            color: '#1f2937'
                        },
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad de Organismos',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 16,
                                weight: 'bold'
                            },
                            color: '#1f2937'
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            borderDash: [2, 2]
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 14
                            }
                        },
                        afterFit: function(scale) {
                            // Agregar espacio extra arriba para los datalabels
                            scale.paddingTop = 30;
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                animation: {
                    duration: 800,
                    easing: 'easeOutQuart'
                }
            },
            plugins: [ChartDataLabels, {
                id: 'umbralesPlugin',
                afterDatasetsDraw(chart, args, options) {
                    // Solo dibujar umbrales si están activados
                    const toggle = document.getElementById('toggle-umbrales-plagasMayorPresenciaChart');
                    if (!toggle || toggle.textContent.trim() === 'Activar Umbrales') {
                        return;
                    }
                    
                    const { ctx, chartArea, scales: { y } } = chart;
                    
                    // Obtener valores de umbrales
                    const umbrales = obtenerValoresUmbrales('plagasMayorPresenciaChart');
                    
                    if (umbrales.bajo || umbrales.medio || umbrales.alto) {
                        ctx.save();
                        
                        // Dibujar línea umbral bajo (verde)
                        if (umbrales.bajo && umbrales.bajo > 0) {
                            const yPosition = y.getPixelForValue(umbrales.bajo);
                            if (yPosition >= chartArea.top && yPosition <= chartArea.bottom) {
                                ctx.strokeStyle = '#22c55e';
                                ctx.lineWidth = 2;
                                ctx.setLineDash([5, 5]);
                                ctx.beginPath();
                                ctx.moveTo(chartArea.left, yPosition);
                                ctx.lineTo(chartArea.right, yPosition);
                                ctx.stroke();
                                
                                // Etiqueta
                                ctx.fillStyle = '#22c55e';
                                ctx.font = 'bold 11px Arial';
                                ctx.fillText(`Bajo: ${umbrales.bajo}`, chartArea.left + 5, yPosition - 5);
                            }
                        }
                        
                        // Dibujar línea umbral medio (amarillo)
                        if (umbrales.medio && umbrales.medio > 0) {
                            const yPosition = y.getPixelForValue(umbrales.medio);
                            if (yPosition >= chartArea.top && yPosition <= chartArea.bottom) {
                                ctx.strokeStyle = '#eab308';
                                ctx.lineWidth = 2;
                                ctx.setLineDash([5, 5]);
                                ctx.beginPath();
                                ctx.moveTo(chartArea.left, yPosition);
                                ctx.lineTo(chartArea.right, yPosition);
                                ctx.stroke();
                                
                                // Etiqueta
                                ctx.fillStyle = '#eab308';
                                ctx.font = 'bold 11px Arial';
                                ctx.fillText(`Medio: ${umbrales.medio}`, chartArea.left + 5, yPosition - 5);
                            }
                        }
                        
                        // Dibujar línea umbral alto (rojo)
                        if (umbrales.alto && umbrales.alto > 0) {
                            const yPosition = y.getPixelForValue(umbrales.alto);
                            if (yPosition >= chartArea.top && yPosition <= chartArea.bottom) {
                                ctx.strokeStyle = '#ef4444';
                                ctx.lineWidth = 2;
                                ctx.setLineDash([5, 5]);
                                ctx.beginPath();
                                ctx.moveTo(chartArea.left, yPosition);
                                ctx.lineTo(chartArea.right, yPosition);
                                ctx.stroke();
                                
                                // Etiqueta
                                ctx.fillStyle = '#ef4444';
                                ctx.font = 'bold 11px Arial';
                                ctx.fillText(`Alto: ${umbrales.alto}`, chartArea.left + 5, yPosition - 5);
                            }
                        }
                        
                        ctx.restore();
                    }
                }
            }]
        });
        
        console.log('Gráfica creada exitosamente');
        
        // Aplicar umbrales si están activados
        setTimeout(() => {
            const toggle = document.getElementById('toggle-umbrales-plagasMayorPresenciaChart');
            if (toggle && toggle.textContent.trim() === 'Desactivar Umbrales') {
                actualizarGraficaComparacionConUmbrales();
                console.log('Umbrales aplicados automáticamente después de la actualización');
            }
        }, 100);
     }
     
     // Función para generar título dinámico de la gráfica
     function generarTituloGrafica(mesesSeleccionados, plagasSeleccionadas) {
         let titulo = `Comparación de Plagas por Mes (${mesesSeleccionados.length} ${mesesSeleccionados.length === 1 ? 'mes' : 'meses'} seleccionados)`;
         
         if (plagasSeleccionadas && plagasSeleccionadas.length > 0) {
             if (plagasSeleccionadas.length === 1) {
                 titulo += `\nMostrando solo: ${plagasSeleccionadas[0]}`;
             } else if (plagasSeleccionadas.length <= 3) {
                 titulo += `\nMostrando: ${plagasSeleccionadas.join(', ')}`;
             } else {
                 titulo += `\nMostrando ${plagasSeleccionadas.length} plagas seleccionadas`;
             }
         }
         
         return titulo;
     }
     
     // Función específica para obtener valores de umbrales (utilizada por el plugin)
     function obtenerValoresUmbrales(chartId) {
         if (!umbralesActivos[chartId]) {
             return { bajo: null, medio: null, alto: null };
         }
         
         return {
             bajo: umbralesActivos[chartId].bajo,
             medio: umbralesActivos[chartId].medio,
             alto: umbralesActivos[chartId].alto
         };
     }
     
           // Función para actualizar la gráfica de comparación con umbrales dinámicamente
      function actualizarGraficaComparacionConUmbrales() {
          if (chartComparacionPlagas) {
              // Simplemente redibujar la gráfica - el plugin se encarga de mostrar/ocultar umbrales
              chartComparacionPlagas.update('none');
              
              console.log('Gráfica de comparación actualizada con umbrales');
          } else {
              console.log('No hay gráfica de comparación activa para actualizar');
          }
      }
      
      // Variable para debounce de actualización en tiempo real
      let timerActualizacionUmbrales = null;
      
      // Función para actualizar umbrales en tiempo real mientras se escribe
      function actualizarUmbralesEnTiempoReal(chartId) {
          // Solo para la gráfica de comparación de plagas
          if (chartId !== 'plagasMayorPresenciaChart') {
              return;
          }
          
          // Verificar si los umbrales están activados
          const toggle = document.getElementById('toggle-umbrales-plagasMayorPresenciaChart');
          if (!toggle || toggle.textContent.trim() === 'Activar Umbrales') {
              return;
          }
          
          // Limpiar timer anterior para evitar muchas actualizaciones
          if (timerActualizacionUmbrales) {
              clearTimeout(timerActualizacionUmbrales);
          }
          
          // Programar actualización después de 500ms de inactividad
          timerActualizacionUmbrales = setTimeout(() => {
              const inputBajo = document.getElementById('umbral-bajo-plagasMayorPresenciaChart');
              const inputMedio = document.getElementById('umbral-medio-plagasMayorPresenciaChart');
              const inputAlto = document.getElementById('umbral-alto-plagasMayorPresenciaChart');
              
              const valorBajo = parseFloat(inputBajo.value);
              const valorMedio = parseFloat(inputMedio.value);
              const valorAlto = parseFloat(inputAlto.value);
              
                             // Validar y dar retroalimentación visual
               const todosDatos = !isNaN(valorBajo) && !isNaN(valorMedio) && !isNaN(valorAlto);
               const ordenCorrecto = valorBajo < valorMedio && valorMedio < valorAlto;
               
               // Remover clases de error previas
               [inputBajo, inputMedio, inputAlto].forEach(input => {
                   input.classList.remove('border-red-500', 'bg-red-50');
                   input.classList.add('border-green-300');
               });
               
               if (todosDatos && ordenCorrecto) {
                   // Valores válidos - actualizar gráfica
                   if (!umbralesActivos[chartId]) {
                       umbralesActivos[chartId] = { activo: true, bajo: null, medio: null, alto: null };
                   }
                   
                   umbralesActivos[chartId].bajo = valorBajo;
                   umbralesActivos[chartId].medio = valorMedio;
                   umbralesActivos[chartId].alto = valorAlto;
                   
                   // Actualizar gráfica
                   actualizarGraficaComparacionConUmbrales();
                   
                   // Guardar en localStorage
                   guardarUmbrales();
                   
                   console.log('Umbrales actualizados en tiempo real:', valorBajo, valorMedio, valorAlto);
               } else if (todosDatos && !ordenCorrecto) {
                   // Valores en orden incorrecto - marcar con rojo
                   if (valorBajo >= valorMedio) {
                       inputBajo.classList.add('border-red-500', 'bg-red-50');
                       inputMedio.classList.add('border-red-500', 'bg-red-50');
                   }
                   if (valorMedio >= valorAlto) {
                       inputMedio.classList.add('border-red-500', 'bg-red-50');
                       inputAlto.classList.add('border-red-500', 'bg-red-50');
                   }
                   console.log('Orden incorrecto de umbrales');
                              }
           }, 500);
       }
       
       // Función para mostrar indicador visual de estado de umbrales
       function mostrarEstadoUmbrales(chartId, estado, mensaje = '') {
           const toggle = document.getElementById(`toggle-umbrales-${chartId}`);
           if (!toggle) return;
           
           // Remover clases previas de estado
           toggle.classList.remove('bg-yellow-200', 'text-yellow-800', 'bg-red-200', 'text-red-800');
           
           switch (estado) {
               case 'validando':
                   toggle.classList.add('bg-yellow-200', 'text-yellow-800');
                   toggle.textContent = 'Validando...';
                   break;
               case 'error':
                   toggle.classList.add('bg-red-200', 'text-red-800');
                   toggle.textContent = 'Error en valores';
                   break;
               case 'activo':
                   toggle.classList.remove('bg-gray-200', 'text-gray-800');
                   toggle.classList.add('bg-green-200', 'text-green-800');
                   toggle.textContent = 'Desactivar Umbrales';
                   break;
               case 'inactivo':
                   toggle.classList.remove('bg-green-200', 'text-green-800');
                   toggle.classList.add('bg-gray-200', 'text-gray-800');
                   toggle.textContent = 'Activar Umbrales';
                   break;
           }
           
           if (mensaje) {
               console.log(`Estado umbrales ${chartId}: ${estado} - ${mensaje}`);
           }
               }
    </script>
    
    <!-- Script adicional para mejorar la UX de umbrales -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Agregar eventos de entrada para feedback visual inmediato
        const inputsUmbrales = [
            'umbral-bajo-plagasMayorPresenciaChart',
            'umbral-medio-plagasMayorPresenciaChart',
            'umbral-alto-plagasMayorPresenciaChart'
        ];
        
        inputsUmbrales.forEach(inputId => {
            const input = document.getElementById(inputId);
            if (input) {
                // Al enfocar el input
                input.addEventListener('focus', function() {
                    this.classList.add('ring-2', 'ring-blue-500');
                });
                
                // Al perder el foco
                input.addEventListener('blur', function() {
                    this.classList.remove('ring-2', 'ring-blue-500');
                });
                
                // Al presionar teclas
                input.addEventListener('keyup', function(e) {
                    // Si presiona Enter, activar/actualizar umbrales
                    if (e.key === 'Enter') {
                        actualizarUmbralesGrafica('plagasMayorPresenciaChart');
                    }
                });
            }
        });
        
        // Agregar tooltip informativo al botón de umbrales
        const toggleButton = document.getElementById('toggle-umbrales-plagasMayorPresenciaChart');
        if (toggleButton) {
            toggleButton.title = 'Click para activar/desactivar las líneas de referencia de umbrales en la gráfica';
        }
        
        // Mejorar accesibilidad de los inputs
        inputsUmbrales.forEach((inputId, index) => {
            const input = document.getElementById(inputId);
            if (input) {
                const nivel = ['bajo', 'medio', 'alto'][index];
                input.title = `Valor del umbral ${nivel}. Los valores deben seguir el orden: Bajo < Medio < Alto`;
            }
        });
        
                 // Mejorar la experiencia de los selectores múltiples
         const selectoresMultiples = ['meses-comparacion', 'plagas-comparacion'];
         selectoresMultiples.forEach(selectorId => {
             const selector = document.getElementById(selectorId);
             if (selector) {
                 // Agregar efecto hover
                 selector.addEventListener('mouseover', function() {
                     this.style.borderColor = '#3b82f6';
                 });
                 
                 selector.addEventListener('mouseout', function() {
                     this.style.borderColor = '#d1d5db';
                 });
                 
                 // Resaltar cuando tiene focus
                 selector.addEventListener('focus', function() {
                     this.style.boxShadow = '0 0 0 3px rgba(59, 130, 246, 0.1)';
                 });
                 
                 selector.addEventListener('blur', function() {
                     this.style.boxShadow = 'none';
                 });
             }
         });
         
         // Función para establecer valores de ejemplo en umbrales
         window.establecerUmbralesEjemplo = function(chartId) {
            if (chartId === 'plagasMayorPresenciaChart') {
                // Establecer valores de ejemplo
                const inputBajo = document.getElementById('umbral-bajo-plagasMayorPresenciaChart');
                const inputMedio = document.getElementById('umbral-medio-plagasMayorPresenciaChart');
                const inputAlto = document.getElementById('umbral-alto-plagasMayorPresenciaChart');
                
                if (inputBajo && inputMedio && inputAlto) {
                    // Usar valores razonables basados en los datos visibles
                    inputBajo.value = '50';
                    inputMedio.value = '300';
                    inputAlto.value = '800';
                    
                    // Animar los campos para mostrar que han cambiado
                    [inputBajo, inputMedio, inputAlto].forEach(input => {
                        input.classList.add('bg-blue-50', 'border-blue-400');
                        setTimeout(() => {
                            input.classList.remove('bg-blue-50', 'border-blue-400');
                        }, 1000);
                    });
                    
                    // Actualizar umbrales
                    actualizarUmbralesGrafica('plagasMayorPresenciaChart');
                    
                    // Activar umbrales si no están activados
                    const toggle = document.getElementById('toggle-umbrales-plagasMayorPresenciaChart');
                    if (toggle && toggle.textContent.trim() === 'Activar Umbrales') {
                        setTimeout(() => {
                            toggleUmbralesGrafica('plagasMayorPresenciaChart');
                        }, 500);
                    }
                    
                    console.log('Valores de ejemplo establecidos: 50, 300, 800');
                                 }
             }
         };
         
         // Función para toggle de configuración avanzada
         window.toggleConfiguracionAvanzada = function() {
             const config = document.getElementById('configuracion-avanzada');
             const btn = document.getElementById('btn-config-avanzada');
             
             if (!config || !btn) return;
             
             if (config.style.display === 'none') {
                 // Mostrar configuración
                 config.style.display = 'block';
                 btn.innerHTML = `
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/>
                     </svg>
                     Ocultar Configuración
                 `;
                 btn.classList.remove('bg-gray-100', 'text-gray-700');
                 btn.classList.add('bg-blue-100', 'text-blue-700');
             } else {
                 // Ocultar configuración
                 config.style.display = 'none';
                 btn.innerHTML = `
                     <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                     </svg>
                     Configuración Avanzada
                 `;
                 btn.classList.remove('bg-blue-100', 'text-blue-700');
                 btn.classList.add('bg-gray-100', 'text-gray-700');
             }
         };
         
         // Inicializar configuración avanzada como visible por defecto
         document.addEventListener('DOMContentLoaded', function() {
             const config = document.getElementById('configuracion-avanzada');
             if (config) {
                 config.style.display = 'block';
             }
         });
    });
</script>

<!-- Script para gráfica de Áreas con Mayor Incidencia -->
<script>
    let chartIncidencia; // Variable global para el gráfico

    document.addEventListener("DOMContentLoaded", function() {
        // Obtener datos de PHP - usar los datos de áreas por plaga que ya tenemos
        const areasCapturasPorPlaga = <?= json_encode($areasCapturasPorPlaga ?? []); ?>;
        const selectorPlaga = document.getElementById('selector-plaga-incidencia');
        
        // Verificar si hay datos
        if (!areasCapturasPorPlaga || areasCapturasPorPlaga.length === 0) {
            console.log('No hay datos de áreas con capturas por plaga');
            return;
        }

        // Obtener todas las plagas únicas
        const plagasDisponibles = [...new Set(areasCapturasPorPlaga.map(item => item.tipo_plaga))].sort();
        
        // Llenar el selector con las plagas disponibles
        plagasDisponibles.forEach(plaga => {
            const option = document.createElement('option');
            option.value = plaga;
            option.textContent = normalizarNombreJS(plaga);
            selectorPlaga.appendChild(option);
        });

        // Seleccionar la primera plaga por defecto
        if (plagasDisponibles.length > 0) {
            selectorPlaga.value = plagasDisponibles[0];
            actualizarGraficaIncidencia(plagasDisponibles[0]);
        }

        // Agregar evento de cambio al selector
        selectorPlaga.addEventListener('change', function() {
            if (this.value) {
                actualizarGraficaIncidencia(this.value);
            }
        });

        function actualizarGraficaIncidencia(plagaSeleccionada) {
            // Filtrar datos para la plaga seleccionada
            const datosPlaga = areasCapturasPorPlaga.filter(item => item.tipo_plaga === plagaSeleccionada);
            
            if (datosPlaga.length === 0) {
                console.log('No hay datos para la plaga: ' + plagaSeleccionada);
                return;
            }

            // Preparar datos para el gráfico
            const labels = datosPlaga.map(item => item.ubicacion || 'Sin ubicación');
            const data = datosPlaga.map(item => parseInt(item.cantidad_total) || 0);
            
            // Generar colores azules neutrales para cada segmento del gráfico de pastel
            const colors = generateBlueNeutralPalette(labels.length);

            // Verificar si el canvas existe
            const canvas = document.getElementById('areasMayorIncidenciaChart');
            if (!canvas) {
                console.error("Error: No se encontró el canvas 'areasMayorIncidenciaChart'");
                return;
            }

            // Destruir el gráfico anterior si existe
            if (chartIncidencia) {
                chartIncidencia.destroy();
            }

            // Crear nuevo gráfico
            const ctx = canvas.getContext('2d');
            chartIncidencia = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: colors,
                        borderColor: colors.map(color => color.replace('0.6', '1')),
                        borderWidth: 2,
                        hoverOffset: 8,
                        cutout: '30%'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: 30,
                            right: 30,
                            bottom: 80,
                            left: 30
                        }
                    },
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            align: 'center',
                            labels: {
                                boxWidth: 15,
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                    size: 12,
                                    weight: 'bold'
                                },
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (data.labels.length && data.datasets.length) {
                                        const dataset = data.datasets[0];
                                        const total = dataset.data.reduce((a, b) => a + b, 0);
                                        
                                        return data.labels.map((label, i) => {
                                            const value = dataset.data[i];
                                            const percentage = ((value / total) * 100);
                                            let percentageText;
                                            if (percentage < 1 && percentage > 0) {
                                                percentageText = '>1%';
                                            } else {
                                                percentageText = Math.round(percentage) + '%';
                                            }
                                            return {
                                                text: `${label} (${percentageText})`,
                                                fillStyle: dataset.backgroundColor[i],
                                                strokeStyle: dataset.borderColor[i],
                                                lineWidth: dataset.borderWidth,
                                                pointStyle: 'circle',
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                    return [];
                                }
                            },
                            maxHeight: 200,
                            onClick: function(e, legendItem, legend) {
                                const index = legendItem.index;
                                const chart = legend.chart;
                                const meta = chart.getDatasetMeta(0);
                                
                                meta.data[index].hidden = !meta.data[index].hidden;
                                chart.update();
                            }
                        },
                        title: {
                            display: true,
                            text: 'Áreas con Mayor Incidencia de ' + plagaSeleccionada,
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 16,
                                weight: 'bold'
                            },
                            color: '#1f2937',
                            padding: {
                                top: 10,
                                bottom: 30
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100);
                                    let percentageText;
                                    if (percentage < 1 && percentage > 0) {
                                        percentageText = '>1%';
                                    } else {
                                        percentageText = Math.round(percentage) + '%';
                                    }
                                    return `${label}: ${value} organismos (${percentageText})`;
                                }
                            },
                            backgroundColor: 'rgba(30, 41, 59, 0.9)',
                            titleFont: {
                                size: 15,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 14
                            },
                            borderColor: 'rgba(255, 255, 255, 0.3)',
                            borderWidth: 2,
                            padding: 12
                        },
                        datalabels: {
                            color: '#fff',
                            formatter: (value, ctx) => {
                                let sum = ctx.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                let percentage = (value * 100) / sum;
                                // Solo mostrar porcentaje si es >= 3%
                                if (percentage >= 3) {
                                    if (percentage < 1 && percentage > 0) {
                                        return '>1%';
                                    } else {
                                        return Math.round(percentage) + '%';
                                    }
                                }
                                return '';
                            },
                            font: {
                                weight: 'bold',
                                size: 14
                            },
                            textStrokeColor: 'rgba(0, 0, 0, 0.5)',
                            textStrokeWidth: 1
                        }
                    },
                    animation: {
                        duration: 800,
                        easing: 'easeOutQuart'
                    }
                },
                plugins: [ChartDataLabels]
            });
        }
    });

    // Función para generar paleta de colores azules neutrales para gráficos de pastel
    function generateBlueNeutralPalette(count) {
        const blueNeutralColors = [
            'rgba(59, 130, 246, 0.8)',   // Azul principal
            'rgba(99, 165, 255, 0.8)',   // Azul claro
            'rgba(30, 100, 200, 0.8)',   // Azul medio
            'rgba(125, 180, 255, 0.8)',  // Azul pastel
            'rgba(70, 150, 230, 0.8)',   // Azul océano
            'rgba(150, 200, 255, 0.8)',  // Azul cielo
            'rgba(45, 120, 210, 0.8)',   // Azul profundo
            'rgba(170, 210, 255, 0.8)',  // Azul hielo
            'rgba(85, 160, 240, 0.8)',   // Azul cobalto
            'rgba(190, 220, 255, 0.8)',  // Azul muy claro
            'rgba(50, 110, 190, 0.8)',   // Azul marino
            'rgba(130, 190, 255, 0.8)',  // Azul suave
            'rgba(40, 95, 175, 0.8)',    // Azul oscuro
            'rgba(200, 230, 255, 0.8)',  // Azul cristal
            'rgba(75, 140, 220, 0.8)',   // Azul real
            'rgba(210, 235, 255, 0.8)'   // Azul nube
        ];
        
        const colors = [];
        for (let i = 0; i < count; i++) {
            if (i < blueNeutralColors.length) {
                colors.push(blueNeutralColors[i]);
            } else {
                // Generar variaciones de azul para elementos adicionales
                const baseBlue = 100 + Math.floor(Math.random() * 100); // 100-200
                const variation = Math.floor(Math.random() * 50); // 0-50
                colors.push(`rgba(${variation}, ${baseBlue}, ${200 + variation}, 0.8)`);
            }
        }
        
        return colors;
    }

    // Función para generar paleta de colores (reutilizar la existente para gráficos de barras)
    function generateColorPalette(count) {
        const baseColors = [
            'rgba(54, 162, 235, 0.9)',   // Azul
            'rgba(255, 99, 132, 0.9)',   // Rojo
            'rgba(255, 206, 86, 0.9)',   // Amarillo
            'rgba(75, 192, 192, 0.9)',   // Verde
            'rgba(153, 102, 255, 0.9)',  // Púrpura
            'rgba(255, 159, 64, 0.9)',   // Naranja
            'rgba(199, 199, 199, 0.9)',  // Gris
            'rgba(83, 102, 255, 0.9)',   // Azul claro
            'rgba(255, 99, 255, 0.9)',   // Rosa
            'rgba(165, 42, 42, 0.9)',    // Marrón
            'rgba(0, 128, 128, 0.9)',    // Verde azulado
            'rgba(128, 0, 128, 0.9)',    // Púrpura oscuro
            'rgba(255, 215, 0, 0.9)',    // Dorado
            'rgba(192, 192, 192, 0.9)',  // Plata
            'rgba(139, 69, 19, 0.9)',    // Marrón oscuro
            'rgba(46, 139, 87, 0.9)'     // Verde mar
        ];
        
        // Si hay más elementos que colores base, generar colores aleatorios adicionales
        const colors = [];
        for (let i = 0; i < count; i++) {
            if (i < baseColors.length) {
                colors.push(baseColors[i]);
            } else {
                // Generar color aleatorio
                const r = Math.floor(Math.random() * 255);
                const g = Math.floor(Math.random() * 255);
                const b = Math.floor(Math.random() * 255);
                colors.push(`rgba(${r}, ${g}, ${b}, 0.9)`);
            }
        }
        
        return colors;
    }
</script>

<!-- Script para gráfica de Trampas con Mayor Captura -->
<script>
    let chartTrampasMayorCaptura; // Variable global para el gráfico

    document.addEventListener("DOMContentLoaded", function() {
        // Obtener datos de PHP
        const todasTrampasCaptura = <?= json_encode($todasTrampasCaptura ?? []); ?>;
        const selectorPlagaTrampa = document.getElementById('selector-plaga-trampa');
        
        // Verificar si hay datos
        if (!todasTrampasCaptura || todasTrampasCaptura.length === 0) {
            console.log('No hay datos de trampas con capturas');
            return;
        }

        // Obtener todas las plagas únicas
        const plagasDisponibles = [...new Set(todasTrampasCaptura.map(item => item.tipo_plaga))].sort();
        
        // Llenar el selector con las plagas disponibles
        plagasDisponibles.forEach(plaga => {
            const option = document.createElement('option');
            option.value = plaga;
            option.textContent = normalizarNombreJS(plaga);
            selectorPlagaTrampa.appendChild(option);
        });

        // Función para actualizar la gráfica de trampas
        function actualizarGraficaTrampas(plagaSeleccionada) {
            let datosFiltrados;
            let titulo;
            
            if (plagaSeleccionada === '' || plagaSeleccionada === null) {
                // Mostrar todas las plagas - agregar las cantidades por trampa
                const trampasSumadas = {};
                todasTrampasCaptura.forEach(item => {
                    const key = `${item.id_trampa} (${item.ubicacion})`;
                    if (!trampasSumadas[key]) {
                        trampasSumadas[key] = {
                            id_trampa: item.id_trampa,
                            trampa_nombre: item.trampa_nombre,
                            ubicacion: item.ubicacion,
                            cantidad_total: 0,
                            total_capturas: 0
                        };
                    }
                    trampasSumadas[key].cantidad_total += parseInt(item.cantidad_total) || 0;
                    trampasSumadas[key].total_capturas += parseInt(item.total_capturas) || 0;
                });
                
                datosFiltrados = Object.values(trampasSumadas);
                titulo = 'TRAMPAS CON MAYOR CAPTURA - TODAS LAS PLAGAS';
            } else {
                // Filtrar datos para la plaga seleccionada
                datosFiltrados = todasTrampasCaptura.filter(item => item.tipo_plaga === plagaSeleccionada);
                titulo = 'TRAMPAS CON MAYOR CAPTURA DE ' + plagaSeleccionada.toUpperCase();
            }
            
            if (datosFiltrados.length === 0) {
                console.log('No hay datos para la plaga: ' + plagaSeleccionada);
                return;
            }

            // Ordenar las trampas por cantidad total de organismos (de mayor a menor)
            datosFiltrados.sort((a, b) => parseInt(b.cantidad_total) - parseInt(a.cantidad_total));
            
            // Limitar a las 10 primeras
            datosFiltrados = datosFiltrados.slice(0, 10);
            
            // Preparar datos para el gráfico
            const labels = datosFiltrados.map(item => `${item.id_trampa} (${item.ubicacion})`);
            const data = datosFiltrados.map(item => parseInt(item.cantidad_total) || 0);
            
            // Verificar si el canvas existe
            const canvas = document.getElementById('trampasMayorCapturaChart');
            if (!canvas) {
                console.error("Error: No se encontró el canvas 'trampasMayorCapturaChart'");
                return;
            }

            // Destruir el gráfico anterior si existe
            if (chartTrampasMayorCaptura) {
                chartTrampasMayorCaptura.destroy();
            }

            // Generar un color principal para las barras (azul)
            const barColor = 'rgba(54, 162, 235, 0.9)';
            const borderColor = 'rgba(54, 162, 235, 1)';
            
            const ctx = canvas.getContext('2d');
            chartTrampasMayorCaptura = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cantidad de Organismos',
                        data: data.map(value => value > 0 ? Math.max(value, 0.5) : 0), // Asegurar valores mínimos visibles para datos > 0
                        backgroundColor: barColor,
                        borderColor: borderColor,
                        borderWidth: 1,
                        barPercentage: 0.8, // Hacer las barras más anchas
                        categoryPercentage: 0.9,
                        minBarLength: 5 // Garantizar que incluso valores pequeños sean visibles
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            left: 10,
                            right: 10,
                            top: 20,
                            bottom: 20
                        }
                    },
                    plugins: {
                        legend: { 
                            display: false
                        },
                        title: {
                            display: true,
                            text: titulo,
                            font: {
                                size: 16,
                                weight: 'bold'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                title: function(context) {
                                    const index = context[0].dataIndex;
                                    const item = datosFiltrados[index];
                                    return `Trampa ${item.id_trampa} - ${item.trampa_nombre || 'Sin nombre'}`;
                                },
                                label: function(context) {
                                    let label = 'Organismos: ';
                                    if (context.parsed.y !== null) {
                                        label += context.parsed.y;
                                    }
                                    return label;
                                },
                                afterLabel: function(context) {
                                    const index = context.dataIndex;
                                    const item = datosFiltrados[index];
                                    return [
                                        `Ubicación: ${item.ubicacion}`,
                                        `Frecuencia: ${item.total_capturas} capturas`
                                    ];
                                }
                            }
                        }
                    },
                    scales: {
                        x: { 
                            title: { 
                                display: false
                            },
                            grid: {
                                display: false
                            },
                            ticks: {
                                autoSkip: true,
                                maxTicksLimit: 10,
                                maxRotation: 45,
                                minRotation: 45,
                                font: {
                                    size: 10
                                }
                            }
                        },
                        y: { 
                            beginAtZero: true,
                            min: 0,
                            max: Math.max(3, Math.ceil(Math.max(...data) * 1.3)), // Al menos 3 o 30% más que el máximo
                            title: { 
                                display: true,
                                text: 'Cantidad de organismos capturados'
                            },
                            grid: {
                                borderDash: [2, 2]
                            },
                            ticks: {
                                precision: 0,
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }

        // Mostrar todas las plagas por defecto
        actualizarGraficaTrampas('');

        // Agregar evento de cambio al selector
        selectorPlagaTrampa.addEventListener('change', function() {
            actualizarGraficaTrampas(this.value);
        });
    });
</script>

<!-- Script para gráfica de Áreas que Presentaron Capturas -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Obtener datos de PHP
        const areasCapturasPorPlaga = <?= json_encode($areasCapturasPorPlaga ?? []); ?>;
        
        // Verificar si hay datos
        if (!areasCapturasPorPlaga || areasCapturasPorPlaga.length === 0) {
            console.log('No hay datos de áreas con capturas por plaga');
            return;
        }
        
        // Procesar datos para el gráfico de barras apiladas
        const ubicaciones = [...new Set(areasCapturasPorPlaga.map(item => item.ubicacion))];
        const todasPlagas = [...new Set(areasCapturasPorPlaga.map(item => item.tipo_plaga))];
        
        // Crear datasets para cada tipo de plaga, pero solo incluir plagas que tienen capturas
        const datasets = [];
        const colores = generateColorPalette(todasPlagas.length);
        
        todasPlagas.forEach((plaga, index) => {
            const datosPorUbicacion = [];
            let tieneAlgunaCapturaPositiva = false;
            
            // Para cada ubicación, buscar la cantidad de capturas para esta plaga
            ubicaciones.forEach(ubicacion => {
                const item = areasCapturasPorPlaga.find(item => 
                    item.ubicacion === ubicacion && 
                    item.tipo_plaga === plaga
                );
                
                let valor = item ? parseInt(item.cantidad_total) : 0;
                
                if (valor > 0) {
                    tieneAlgunaCapturaPositiva = true;
                }
                
                datosPorUbicacion.push(valor);
            });
            
            // Solo agregar el dataset si esta plaga tiene al menos una captura en alguna ubicación
            if (tieneAlgunaCapturaPositiva) {
                datasets.push({
                    label: normalizarNombreJS(plaga),
                    data: datosPorUbicacion,
                    backgroundColor: colores[index].replace('0.6', '0.9'), // Aumentar opacidad
                    borderColor: colores[index].replace('0.6', '1'),
                    borderWidth: 1,
                    borderRadius: 4,
                    hoverOffset: 4,
                    hoverBorderWidth: 2
                });
            }
        });
        
        // Verificar si el canvas existe antes de crear el gráfico
        const canvas = document.getElementById('areasCapturasPorPlagaChart');
        if (!canvas) {
            console.error("Error: No se encontró el canvas 'areasCapturasPorPlagaChart'");
            return;
        }
        
        const ctx = canvas.getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ubicaciones,
                datasets: datasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 30,
                        right: 40,
                        top: 40,
                        bottom: 30
                    }
                },
                plugins: {
                    datalabels: {
                        display: false
                    },
                    legend: {
                        position: 'bottom',
                        align: 'center',
                        labels: {
                            boxWidth: 15,
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 13,
                                weight: 'bold'
                            }
                        },
                        title: {
                            display: true,
                            text: 'Tipos de Plagas',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 15,
                                weight: 'bold'
                            },
                            color: '#1f2937',
                            padding: {
                                top: 15,
                                bottom: 15
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'ÁREAS QUE PRESENTARON CAPTURAS',
                        font: {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            size: 18,
                            weight: 'bold'
                        },
                        color: '#1f2937',
                        padding: {
                            top: 15,
                            bottom: 35
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += context.parsed.y + ' organismos';
                                }
                                
                                // Buscar los datos de capturas originales
                                const ubicacion = ubicaciones[context.dataIndex];
                                const plaga = context.dataset.label;
                                const item = areasCapturasPorPlaga.find(item => 
                                    item.ubicacion === ubicacion && 
                                    item.tipo_plaga === plaga
                                );
                                if (item) {
                                    return `${label} (${item.total} capturas)`;
                                }
                                
                                return label;
                            },
                            afterBody: function(tooltipItems) {
                                // Calcular el total real para esta ubicación
                                const locationIndex = tooltipItems[0].dataIndex;
                                let totalReal = 0;
                                
                                // Sumar todos los valores de todos los datasets para esta ubicación
                                tooltipItems[0].chart.data.datasets.forEach(dataset => {
                                    totalReal += dataset.data[locationIndex] || 0;
                                });
                                
                                // Si el total supera el límite visual, mostrar advertencia
                                const limiteDeLaGrafica = tooltipItems[0].chart.scales.y.max;
                                if (totalReal > limiteDeLaGrafica) {
                                    return [`⚠️ Total real: ${totalReal} organismos`, `(La gráfica está limitada a ${limiteDeLaGrafica} para mejor visualización)`];
                                }
                                
                                return [`Total en esta ubicación: ${totalReal} organismos`];
                            }
                        },
                        backgroundColor: 'rgba(30, 41, 59, 0.9)',
                        titleFont: {
                            size: 15,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 14
                        },
                        borderColor: 'rgba(255, 255, 255, 0.3)',
                        borderWidth: 2,
                        padding: 12,
                        displayColors: true,
                        boxWidth: 12,
                        boxHeight: 12,
                        usePointStyle: true
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        title: {
                            display: true,
                            text: 'Ubicación',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 15,
                                weight: 'bold'
                            },
                            color: '#1f2937',
                            padding: {top: 15, bottom: 5}
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            autoSkip: false,
                            maxTicksLimit: 15,
                            maxRotation: 90,
                            minRotation: 60,
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 11,
                                weight: 'bold'
                            },
                            padding: 8,
                            color: '#374151'
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        min: 0,
                        max: function(context) {
                            // Calcular el máximo real de las barras apiladas
                            let maxStackedValue = 0;
                            
                            if (!context.chart.data.datasets || context.chart.data.datasets.length === 0) {
                                return 1500;
                            }
                            
                            const numLocations = context.chart.data.labels ? context.chart.data.labels.length : 0;
                            
                            // Para cada ubicación, sumar todos los valores de todos los datasets
                            for (let locationIndex = 0; locationIndex < numLocations; locationIndex++) {
                                let stackSum = 0;
                                
                                for (let datasetIndex = 0; datasetIndex < context.chart.data.datasets.length; datasetIndex++) {
                                    const dataset = context.chart.data.datasets[datasetIndex];
                                    const value = dataset.data[locationIndex] || 0;
                                    stackSum += value;
                                }
                                
                                if (stackSum > maxStackedValue) {
                                    maxStackedValue = stackSum;
                                }
                            }
                            
                            // Si el máximo es muy alto, usar una escala adaptativa
                            if (maxStackedValue > 2500) {
                                // Usar una escala que permita ver mejor los valores medianos
                                return 2500;
                            } else if (maxStackedValue > 1000) {
                                return Math.ceil(maxStackedValue * 1.2);
                            } else {
                                return Math.ceil(maxStackedValue * 1.3);
                            }
                        },
                        title: {
                            display: true,
                            text: 'Cantidad de Organismos',
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 15,
                                weight: 'bold'
                            },
                            color: '#1f2937',
                            padding: {top: 5, bottom: 15}
                        },
                        grid: {
                            color: 'rgba(226, 232, 240, 0.8)',
                            borderDash: [4, 4],
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            stepSize: function(context) {
                                const max = context.chart.scales.y.max || 100;
                                if (max <= 100) return 10;
                                if (max <= 300) return 25;
                                if (max <= 500) return 50;
                                if (max <= 1000) return 100;
                                if (max <= 2500) return 250;
                                return Math.ceil(max / 10);
                            },
                            font: {
                                family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                                size: 12,
                                weight: 'bold'
                            },
                            padding: 10,
                            color: '#374151'
                        }
                    }
                },
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                }
            },
            plugins: [{
                id: 'indicadorLimiteExcedido',
                afterDatasetsDraw: function(chart) {
                    const ctx = chart.ctx;
                    const chartArea = chart.chartArea;
                    const maxY = chart.scales.y.max;
                    
                    // Verificar cada ubicación para ver si supera el límite
                    chart.data.labels.forEach((label, locationIndex) => {
                        let totalReal = 0;
                        
                        // Sumar todos los valores de todos los datasets para esta ubicación
                        chart.data.datasets.forEach(dataset => {
                            totalReal += dataset.data[locationIndex] || 0;
                        });
                        
                        // Si supera el límite, dibujar indicador
                        if (totalReal > maxY) {
                            const meta = chart.getDatasetMeta(0); // Usar el primer dataset para obtener la posición X
                            const x = meta.data[locationIndex].x;
                            const topY = chartArea.top;
                            
                            // Dibujar triángulo de advertencia
                            ctx.save();
                            ctx.fillStyle = '#ef4444'; // Rojo
                            ctx.strokeStyle = '#ffffff';
                            ctx.lineWidth = 2;
                            
                            ctx.beginPath();
                            ctx.moveTo(x, topY - 5);
                            ctx.lineTo(x - 8, topY - 20);
                            ctx.lineTo(x + 8, topY - 20);
                            ctx.closePath();
                            ctx.fill();
                            ctx.stroke();
                            
                            // Dibujar exclamación
                            ctx.fillStyle = '#ffffff';
                            ctx.font = 'bold 10px Arial';
                            ctx.textAlign = 'center';
                            ctx.fillText('!', x, topY - 11);
                            
                            ctx.restore();
                        }
                    });
                }
            }]
        });
    });
</script>

<!-- Script para exportación de tablas -->
<script>
// Funciones de exportación
function exportarTablaToPDF(tableId, fileName) {
    // Usar jsPDF si está disponible, de lo contrario cargarla
    if (typeof window.jspdf === 'undefined') {
        // Alerta al usuario que se está cargando la biblioteca
        alert('Preparando la exportación a PDF, por favor espere...');
        
        // Crear elemento script para cargar jsPDF
        const script1 = document.createElement('script');
        script1.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
        
        // Crear elemento script para cargar jspdf-autotable
        const script2 = document.createElement('script');
        script2.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js';
        
        // Cargar las bibliotecas y luego exportar
        document.body.appendChild(script1);
        script1.onload = function() {
            document.body.appendChild(script2);
            script2.onload = function() {
                setTimeout(function() {
                    realizarExportacionPDF(tableId, fileName);
                }, 1000); // Dar tiempo para que se inicialicen las bibliotecas
            };
        };
    } else {
        realizarExportacionPDF(tableId, fileName);
    }
}

function realizarExportacionPDF(tableId, fileName) {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'pt', 'a4'); // Landscape orientation
    
    // Añadir título
    doc.setFontSize(18);
    doc.text(fileName.replace(/_/g, ' '), 40, 40);
    
    // Importar tabla
    doc.autoTable({
        html: '#' + tableId,
        startY: 60,
        theme: 'grid',
        headStyles: { fillColor: [41, 128, 185], textColor: 255 },
        styles: { overflow: 'linebreak', cellPadding: 3 },
        margin: { top: 60, right: 40, bottom: 40, left: 40 }
    });
    
    // Guardar PDF
    doc.save(fileName + '.pdf');
}

function exportarTablaToExcel(tableId, fileName) {
    const table = document.getElementById(tableId);
    
    // Si no está disponible la biblioteca SheetJS, usar enfoque sencillo
    const tableHTML = table.outerHTML;
    
    // Crear un libro de trabajo temporal
    let uri = 'data:application/vnd.ms-excel;base64,';
    let template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
    template += '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    template += '<meta http-equiv="content-type" content="text/plain; charset=UTF-8"/></head>';
    template += '<body><table>{table}</table></body></html>';
    
    // Reemplazar las variables en la plantilla
    let context = { worksheet: fileName, table: tableHTML };
    let templateFinal = template.replace(/{(\w+)}/g, function(m, p) { return context[p]; });
    
    // Codificar en Base64
    const base64 = (s) => window.btoa(unescape(encodeURIComponent(s)));
    
    // Crear el enlace de descarga y hacer clic en él
    let link = document.createElement("a");
    link.download = fileName + ".xls";
    link.href = uri + base64(templateFinal);
    link.click();
}
</script>

<!-- Script para filtrado de incidencias -->
<script>
// Función para filtrar la tabla de incidencias por tipo de insecto
document.addEventListener('DOMContentLoaded', function() {
    const filtroInsecto = document.getElementById('filtro-insecto');
    const tablaIncidencias = document.querySelector('#tabla-incidencias');
    const contadorIncidencias = document.getElementById('contador-incidencias');
    
    // Función para filtrar la tabla
    function filtrarTabla() {
        const tipoInsectoSeleccionado = filtroInsecto.value;
        let filasVisibles = 0;
        
        // Obtener todas las filas de la tabla excepto el encabezado
        const filas = tablaIncidencias.querySelectorAll('tbody tr');
        
        filas.forEach(fila => {
            // La columna del tipo de insecto es la 4ta (índice 3)
            const celdaInsecto = fila.querySelector('td:nth-child(4)');
            const tipoInsecto = celdaInsecto.textContent.trim();
            
            // Mostrar todas las filas si no hay filtro, o solo las que coinciden con el filtro
            if (tipoInsectoSeleccionado === '' || tipoInsecto === tipoInsectoSeleccionado) {
                fila.style.display = '';
                filasVisibles++;
            } else {
                fila.style.display = 'none';
            }
        });
        
        // Actualizar el contador de incidencias visibles
        contadorIncidencias.textContent = filasVisibles;
    }
    
    // Aplicar filtro cuando cambie el selector
    if (filtroInsecto) {
        filtroInsecto.addEventListener('change', filtrarTabla);
    }
});
</script>

<!-- Script para filtros de rango de fechas -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos para filtros de trampas
        const filtroFechaInicioTrampas = document.getElementById('filtro-fecha-inicio-trampas');
        const filtroFechaFinTrampas = document.getElementById('filtro-fecha-fin-trampas');
        
        // Elementos para filtros de incidencias
        const filtroFechaInicioIncidencias = document.getElementById('filtro-fecha-inicio-incidencias');
        const filtroFechaFinIncidencias = document.getElementById('filtro-fecha-fin-incidencias');
        const filtroIdTrampaIncidencias = document.getElementById('filtro-id-trampa-incidencias');
        const filtroTipoTrampaIncidencias = document.getElementById('filtro-tipo-trampa-incidencias');
        const filtroInsecto = document.getElementById('filtro-insecto');
        const contadorIncidencias = document.getElementById('contador-incidencias');
        const limpiarFiltrosIncidencias = document.getElementById('limpiar-filtros-incidencias');
        
        // Inicializar fechas con valores predeterminados
        // Fecha de inicio: 30 días atrás
        const fechaInicio = new Date();
        fechaInicio.setDate(fechaInicio.getDate() - 30);
        
        // Fecha fin: hoy
        const fechaFin = new Date();
        
        // Formatear fechas para inputs de tipo date (YYYY-MM-DD)
        const formatearFecha = (fecha) => {
            const year = fecha.getFullYear();
            const month = String(fecha.getMonth() + 1).padStart(2, '0');
            const day = String(fecha.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        
        // Establecer valores predeterminados
        if (filtroFechaInicioTrampas) filtroFechaInicioTrampas.value = formatearFecha(fechaInicio);
        if (filtroFechaFinTrampas) filtroFechaFinTrampas.value = formatearFecha(fechaFin);
        if (filtroFechaInicioIncidencias) filtroFechaInicioIncidencias.value = formatearFecha(fechaInicio);
        if (filtroFechaFinIncidencias) filtroFechaFinIncidencias.value = formatearFecha(fechaFin);
        
        // Elementos de tabla de incidencias
        const tablaIncidencias = document.getElementById('tabla-incidencias');
        let filasIncidencias = [];
        
        // Verificar si la tabla de incidencias existe
        if (tablaIncidencias) {
            filasIncidencias = tablaIncidencias.querySelectorAll('tbody tr');
        }
        
        // Aplicar filtros a la tabla de incidencias
        function aplicarFiltrosIncidencias() {
            const idTrampaSeleccionado = filtroIdTrampaIncidencias ? filtroIdTrampaIncidencias.value : '';
            const tipoTrampaSeleccionado = filtroTipoTrampaIncidencias ? filtroTipoTrampaIncidencias.value : '';
            const insectoSeleccionado = filtroInsecto ? filtroInsecto.value : '';
            const fechaInicio = filtroFechaInicioIncidencias.value ? new Date(filtroFechaInicioIncidencias.value) : null;
            const fechaFin = filtroFechaFinIncidencias.value ? new Date(filtroFechaFinIncidencias.value) : null;
            
            // Obtener el texto normalizado del option seleccionado para comparar con el texto de la tabla
            let tipoTrampaTextoNormalizado = '';
            if (filtroTipoTrampaIncidencias && filtroTipoTrampaIncidencias.selectedIndex > 0) {
                tipoTrampaTextoNormalizado = filtroTipoTrampaIncidencias.options[filtroTipoTrampaIncidencias.selectedIndex].textContent.trim();
            }
            
            let insectoTextoNormalizado = '';
            if (filtroInsecto && filtroInsecto.selectedIndex > 0) {
                insectoTextoNormalizado = filtroInsecto.options[filtroInsecto.selectedIndex].textContent.trim();
            }
            
            // Si la fecha fin existe, ajustarla para incluir todo el día
            if (fechaFin) {
                fechaFin.setHours(23, 59, 59, 999);
            }
            
            let incidenciasVisibles = 0;
            
            filasIncidencias.forEach(fila => {
                // Índices de columnas actualizados:
                // 0: ID Trampa, 1: Tipo de Trampa, 2: Tipo de Incidencia, 3: Tipo de Plaga, 
                // 4: Cantidad de Organismos, 5: Tipo de Insecto, 6: Fecha
                const idTrampa = fila.children[0].textContent.trim(); // Columna de ID trampa (índice 0)
                const tipoTrampa = fila.children[1].textContent.trim(); // Columna de tipo de trampa (índice 1)
                const tipoInsecto = fila.children[5].textContent.trim(); // Columna de tipo de insecto (índice 5)
                const fechaTexto = fila.children[6].textContent.trim(); // Columna de fecha (índice 6)
                
                // Convertir fecha del formato "mes día de año hora:minuto" a objeto Date
                // Ejemplo: "abril 14 de 2025 19:45"
                const mesesEspanol = {
                    'enero': 0, 'febrero': 1, 'marzo': 2, 'abril': 3,
                    'mayo': 4, 'junio': 5, 'julio': 6, 'agosto': 7,
                    'septiembre': 8, 'octubre': 9, 'noviembre': 10, 'diciembre': 11
                };
                
                // Parsear el formato "mes día de año hora:minuto"
                let fecha = null;
                
                // Intentar parsear formato nuevo: "mes día de año hora:minuto"
                const regexNuevoFormato = /^(\w+)\s+(\d+)\s+de\s+(\d{4})\s+(\d{1,2}):(\d{2})$/;
                const matchNuevo = fechaTexto.match(regexNuevoFormato);
                
                if (matchNuevo) {
                    const mesNombre = matchNuevo[1].toLowerCase();
                    const dia = parseInt(matchNuevo[2]);
                    const anio = parseInt(matchNuevo[3]);
                    const hora = parseInt(matchNuevo[4]);
                    const minuto = parseInt(matchNuevo[5]);
                    
                    if (mesesEspanol.hasOwnProperty(mesNombre)) {
                        fecha = new Date(anio, mesesEspanol[mesNombre], dia, hora, minuto);
                    }
                }
                
                // Si no se pudo parsear, intentar con formato antiguo como fallback
                if (!fecha || isNaN(fecha.getTime())) {
                    // Intentar parsear formato antiguo dd/mm/yyyy HH:MM
                    const regexAntiguo = /^(\d{1,2})\/(\d{1,2})\/(\d{4})\s+(\d{1,2}):(\d{2})$/;
                    const matchAntiguo = fechaTexto.match(regexAntiguo);
                    
                    if (matchAntiguo) {
                        fecha = new Date(
                            parseInt(matchAntiguo[3]), // año
                            parseInt(matchAntiguo[2]) - 1, // mes (0-11)
                            parseInt(matchAntiguo[1]), // día
                            parseInt(matchAntiguo[4]), // hora
                            parseInt(matchAntiguo[5])  // minutos
                        );
                    }
                }
                
                // Si aún no se pudo parsear, usar fecha inválida para que no pase el filtro
                if (!fecha || isNaN(fecha.getTime())) {
                    fecha = new Date(0); // Fecha inválida
                }
                
                // Verificar si cumple con todos los filtros
                // Comparar el texto normalizado de la tabla con el texto normalizado del select
                const cumpleIdTrampa = !idTrampaSeleccionado || idTrampa === idTrampaSeleccionado;
                const cumpleTipoTrampa = !tipoTrampaSeleccionado || tipoTrampa === tipoTrampaTextoNormalizado;
                const cumpleInsecto = !insectoSeleccionado || tipoInsecto === insectoTextoNormalizado;
                const cumpleFechaInicio = !fechaInicio || fecha >= fechaInicio;
                const cumpleFechaFin = !fechaFin || fecha <= fechaFin;
                
                // Mostrar u ocultar según los filtros
                if (cumpleIdTrampa && cumpleTipoTrampa && cumpleInsecto && cumpleFechaInicio && cumpleFechaFin) {
                    fila.style.display = '';
                    incidenciasVisibles++;
                } else {
                    fila.style.display = 'none';
                }
            });
            
            // Actualizar contador
            if (contadorIncidencias) {
                contadorIncidencias.textContent = incidenciasVisibles;
            }
        }
        
        
        // Aplicar filtros a la tabla de trampas
        const aplicarFiltrosTrampas = function() {
            // Obtener valores de los filtros existentes
            const tipoSeleccionado = document.getElementById('filtro-tipo-trampa').value;
            const ubicacionSeleccionada = document.getElementById('filtro-ubicacion').value;
            const planoSeleccionado = document.getElementById('filtro-plano').value;
            const fechaInicio = filtroFechaInicioTrampas.value ? new Date(filtroFechaInicioTrampas.value) : null;
            const fechaFin = filtroFechaFinTrampas.value ? new Date(filtroFechaFinTrampas.value) : null;
            
            // Si la fecha fin existe, ajustarla para incluir todo el día
            if (fechaFin) {
                fechaFin.setHours(23, 59, 59, 999);
            }
            
            const filasTrampa = document.querySelectorAll('.fila-trampa');
            let trampasVisibles = 0;
            
            filasTrampa.forEach(fila => {
                const tipo = fila.dataset.tipo;
                const ubicacion = fila.dataset.ubicacion;
                const plano = fila.dataset.plano;
                const fechaTexto = fila.dataset.fecha;
                
                // Convertir la fecha de la trampa a objeto Date
                const fechaTrampa = new Date(fechaTexto);
                
                // Verificar si cumple con el filtro de fecha
                const cumpleFechaInicio = !fechaInicio || fechaTrampa >= fechaInicio;
                const cumpleFechaFin = !fechaFin || fechaTrampa <= fechaFin;
                const cumpleFecha = cumpleFechaInicio && cumpleFechaFin;
                
                // Verificar si la fila cumple con todos los filtros activos
                const cumpleTipo = !tipoSeleccionado || tipo === tipoSeleccionado;
                const cumpleUbicacion = !ubicacionSeleccionada || ubicacion === ubicacionSeleccionada;
                const cumplePlano = !planoSeleccionado || plano === planoSeleccionado;
                
                // Mostrar u ocultar la fila según los filtros
                if (cumpleTipo && cumpleUbicacion && cumplePlano && cumpleFecha) {
                    fila.style.display = '';
                    trampasVisibles++;
                } else {
                    fila.style.display = 'none';
                }
            });
            
            // Actualizar el contador de resultados
            document.getElementById('cantidad-trampas').textContent = trampasVisibles;
        };
        
        // Evento para limpiar todos los filtros de trampas
        document.getElementById('limpiar-filtros').addEventListener('click', function() {
            document.getElementById('filtro-tipo-trampa').value = '';
            document.getElementById('filtro-ubicacion').value = '';
            document.getElementById('filtro-plano').value = '';
            filtroFechaInicioTrampas.value = '';
            filtroFechaFinTrampas.value = '';
            
            // Mostrar todas las filas nuevamente
            const filasTrampa = document.querySelectorAll('.fila-trampa');
            filasTrampa.forEach(fila => {
                fila.style.display = '';
            });
            
            // Actualizar el contador de resultados
            document.getElementById('cantidad-trampas').textContent = filasTrampa.length;
        });
        
        // Evento para limpiar filtros de incidencias
        if (limpiarFiltrosIncidencias) {
            limpiarFiltrosIncidencias.addEventListener('click', function() {
                if (filtroIdTrampaIncidencias) filtroIdTrampaIncidencias.value = '';
                if (filtroTipoTrampaIncidencias) filtroTipoTrampaIncidencias.value = '';
                if (filtroInsecto) filtroInsecto.value = '';
                if (filtroFechaInicioIncidencias) filtroFechaInicioIncidencias.value = '';
                if (filtroFechaFinIncidencias) filtroFechaFinIncidencias.value = '';
                
                // Mostrar todas las incidencias
                filasIncidencias.forEach(fila => {
                    fila.style.display = '';
                });
                
                // Actualizar contador
                if (contadorIncidencias) {
                    contadorIncidencias.textContent = filasIncidencias.length;
                }
            });
        }
        
        // Agregar eventos a los filtros de trampas
        document.getElementById('filtro-tipo-trampa').addEventListener('change', aplicarFiltrosTrampas);
        document.getElementById('filtro-ubicacion').addEventListener('change', aplicarFiltrosTrampas);
        document.getElementById('filtro-plano').addEventListener('change', aplicarFiltrosTrampas);
        if (filtroFechaInicioTrampas) filtroFechaInicioTrampas.addEventListener('change', aplicarFiltrosTrampas);
        if (filtroFechaFinTrampas) filtroFechaFinTrampas.addEventListener('change', aplicarFiltrosTrampas);
        
        // Agregar eventos a los filtros de incidencias
        if (filtroIdTrampaIncidencias) filtroIdTrampaIncidencias.addEventListener('change', aplicarFiltrosIncidencias);
        if (filtroTipoTrampaIncidencias) filtroTipoTrampaIncidencias.addEventListener('change', aplicarFiltrosIncidencias);
        if (filtroInsecto) filtroInsecto.addEventListener('change', aplicarFiltrosIncidencias);
        if (filtroFechaInicioIncidencias) filtroFechaInicioIncidencias.addEventListener('change', aplicarFiltrosIncidencias);
        if (filtroFechaFinIncidencias) filtroFechaFinIncidencias.addEventListener('change', aplicarFiltrosIncidencias);
        
        // Aplicar filtros automáticamente al cargar la página
        aplicarFiltrosTrampas();
        aplicarFiltrosIncidencias();
        
        // Añadir un mensaje de notificación para indicar que se han aplicado los filtros
        const mostrarNotificacion = (mensaje) => {
            const notificacion = document.createElement('div');
            notificacion.className = 'fixed bottom-4 right-4 bg-blue-500 text-white py-2 px-4 rounded-lg shadow-lg z-50';
            notificacion.textContent = mensaje;
            document.body.appendChild(notificacion);
            
            // Auto-eliminar después de 3 segundos
            setTimeout(() => {
                notificacion.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => notificacion.remove(), 500);
            }, 3000);
        };
        
        mostrarNotificacion('Filtros aplicados: mostrando datos de los últimos 30 días');
    });
</script>

<!-- Script para filtrar gráficas por rango de fechas -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elementos de los filtros de fecha para gráficas
        const filtroFechaInicioGraficas = document.getElementById('filtro-fecha-inicio-graficas');
        const filtroFechaFinGraficas = document.getElementById('filtro-fecha-fin-graficas');
        const btnAplicarFiltrosGraficas = document.getElementById('aplicar-filtros-graficas');
        
        // Establecer valores predeterminados (últimos 30 días)
        const fechaInicio = new Date();
        fechaInicio.setDate(fechaInicio.getDate() - 30);
        const fechaFin = new Date();
        
        // Formatear fechas para inputs de tipo date (YYYY-MM-DD)
        const formatearFecha = (fecha) => {
            const year = fecha.getFullYear();
            const month = String(fecha.getMonth() + 1).padStart(2, '0');
            const day = String(fecha.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        
        if (filtroFechaInicioGraficas) filtroFechaInicioGraficas.value = formatearFecha(fechaInicio);
        if (filtroFechaFinGraficas) filtroFechaFinGraficas.value = formatearFecha(fechaFin);
        
        // Función para actualizar gráficas con los filtros de fecha
        function actualizarGraficasConFiltrosFecha() {
            // Mostrar indicador de carga
            mostrarIndicadorCarga('Actualizando gráficas...');
            
            // Obtener las fechas seleccionadas
            const fechaInicio = filtroFechaInicioGraficas.value;
            const fechaFin = filtroFechaFinGraficas.value;
            
            // Verificar que ambas fechas estén seleccionadas
            if (!fechaInicio || !fechaFin) {
                mostrarNotificacion('Por favor seleccione ambas fechas', 'error');
                ocultarIndicadorCarga();
                return;
            }
            
            // Construir URL con parámetros de fecha
            const urlParams = new URLSearchParams(window.location.search);
            
            // Mantener otros parámetros existentes
            const sedeId = urlParams.get('sede_id');
            const plaga = urlParams.get('plaga');
            const mes = urlParams.get('mes');
            
            // Crear nueva URL con todos los parámetros
            let url = '<?= base_url('locations') ?>?';
            
            // Añadir parámetros de fecha para filtrar
            url += `fecha_inicio=${fechaInicio}&fecha_fin=${fechaFin}`;
            
            // Añadir otros parámetros si existen
            if (sedeId) url += `&sede_id=${sedeId}`;
            if (plaga) url += `&plaga=${encodeURIComponent(plaga)}`;
            if (mes) url += `&mes=${mes}`;
            
            // Recargar la página con los nuevos parámetros
            window.location.href = url;
        }
        
        // Función para mostrar indicador de carga
        function mostrarIndicadorCarga(mensaje) {
            const indicador = document.createElement('div');
            indicador.id = 'indicador-carga';
            indicador.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
            indicador.innerHTML = `
                <div class="bg-white p-6 rounded-lg shadow-lg text-center">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500 mx-auto"></div>
                    <p class="mt-4">${mensaje}</p>
                </div>
            `;
            document.body.appendChild(indicador);
        }
        
        // Función para ocultar indicador de carga
        function ocultarIndicadorCarga() {
            const indicador = document.getElementById('indicador-carga');
            if (indicador) {
                document.body.removeChild(indicador);
            }
        }
        
        // Función para mostrar notificaciones
        function mostrarNotificacion(mensaje, tipo = 'info') {
            const colorFondo = tipo === 'error' ? 'bg-red-500' : 'bg-blue-500';
            
            const notificacion = document.createElement('div');
            notificacion.className = `fixed bottom-4 right-4 ${colorFondo} text-white py-2 px-4 rounded-lg shadow-lg z-50`;
            notificacion.textContent = mensaje;
            document.body.appendChild(notificacion);
            
            // Auto-eliminar después de 3 segundos
            setTimeout(() => {
                notificacion.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => notificacion.remove(), 500);
            }, 3000);
        }
        
        // Evento click para el botón de aplicar filtros
        if (btnAplicarFiltrosGraficas) {
            btnAplicarFiltrosGraficas.addEventListener('click', actualizarGraficasConFiltrosFecha);
        }
        
        // Verificar si hay parámetros de fecha en la URL actual
        const urlParams = new URLSearchParams(window.location.search);
        const fechaInicioUrl = urlParams.get('fecha_inicio');
        const fechaFinUrl = urlParams.get('fecha_fin');
        
        // Si hay parámetros de fecha, actualizar los inputs
        if (fechaInicioUrl && filtroFechaInicioGraficas) {
            filtroFechaInicioGraficas.value = fechaInicioUrl;
        }
        
        if (fechaFinUrl && filtroFechaFinGraficas) {
            filtroFechaFinGraficas.value = fechaFinUrl;
        }
        
        // Si se han aplicado filtros de fecha, mostrar un mensaje
        if (fechaInicioUrl && fechaFinUrl) {
            mostrarNotificacion(`Mostrando gráficas del ${fechaInicioUrl} al ${fechaFinUrl}`);
        }
    });
</script>

<!-- Script para expandir/contraer la tabla de trampas -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnExpandirTabla = document.getElementById('btn-expandir-tabla');
        const trampasOcultas = document.querySelectorAll('.trampa-oculta');
        const spanTextoBoton = btnExpandirTabla.querySelector('span');
        let tablaDesplegada = false;
        
        // Actualizar el contador con la cantidad visible inicialmente
        actualizarContadorTrampas();
        
        btnExpandirTabla.addEventListener('click', function() {
            if (!tablaDesplegada) {
                // Mostrar todas las trampas
                trampasOcultas.forEach(trampa => {
                    trampa.classList.remove('hidden');
                });
                
                // Cambiar el texto y el ícono del botón
                spanTextoBoton.textContent = 'Mostrar solo 5 trampas';
                btnExpandirTabla.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7" />';
                
                tablaDesplegada = true;
            } else {
                // Ocultar trampas adicionales
                trampasOcultas.forEach(trampa => {
                    trampa.classList.add('hidden');
                });
                
                // Restaurar el texto y el ícono del botón
                spanTextoBoton.textContent = 'Ver todas las trampas';
                btnExpandirTabla.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />';
                
                tablaDesplegada = false;
            }
            
            // Actualizar el contador después de expandir/contraer
            actualizarContadorTrampas();
        });
        
        // Función para actualizar el contador de trampas visibles
        function actualizarContadorTrampas() {
            const contadorTrampas = document.getElementById('cantidad-trampas');
            const filasVisibles = document.querySelectorAll('.fila-trampa:not(.hidden)').length;
            contadorTrampas.textContent = filasVisibles;
        }
        
        // Modificar la función de filtrado para mantener la coherencia
        const filtroTipoTrampa = document.getElementById('filtro-tipo-trampa');
        const filtroUbicacion = document.getElementById('filtro-ubicacion');
        const filtroPlano = document.getElementById('filtro-plano');
        
        function aplicarFiltros() {
            const tipoSeleccionado = filtroTipoTrampa.value;
            const ubicacionSeleccionada = filtroUbicacion.value;
            const planoSeleccionado = filtroPlano.value;
            
            let trampasVisibles = 0;
            let todasLasTrampas = document.querySelectorAll('.fila-trampa');
            
            todasLasTrampas.forEach(fila => {
                const tipo = fila.dataset.tipo;
                const ubicacion = fila.dataset.ubicacion;
                const plano = fila.dataset.plano;
                
                // Verificar si la fila cumple con todos los filtros activos
                const cumpleTipo = !tipoSeleccionado || tipo === tipoSeleccionado;
                const cumpleUbicacion = !ubicacionSeleccionada || ubicacion === ubicacionSeleccionada;
                const cumplePlano = !planoSeleccionado || plano === planoSeleccionado;
                
                // Si los filtros coinciden, mostrar la fila pero respetando el estado de expansión
                if (cumpleTipo && cumpleUbicacion && cumplePlano) {
                    if (!tablaDesplegada && fila.classList.contains('trampa-oculta')) {
                        fila.classList.add('hidden');
                    } else {
                        fila.classList.remove('hidden');
                        trampasVisibles++;
                    }
                } else {
                    fila.classList.add('hidden');
                }
            });
            
            // Actualizar el contador de resultados
            document.getElementById('cantidad-trampas').textContent = trampasVisibles;
        }
        
        // Reemplazar la función original de aplicar filtros
        filtroTipoTrampa.addEventListener('change', aplicarFiltros);
        filtroUbicacion.addEventListener('change', aplicarFiltros);
        filtroPlano.addEventListener('change', aplicarFiltros);
        
        // Actualizar la función de limpiar filtros
        document.getElementById('limpiar-filtros').addEventListener('click', function() {
            filtroTipoTrampa.value = '';
            filtroUbicacion.value = '';
            filtroPlano.value = '';
            
            // Mostrar todas las filas pero respetando el estado de expansión
            const todasLasTrampas = document.querySelectorAll('.fila-trampa');
            todasLasTrampas.forEach(fila => {
                if (!tablaDesplegada && fila.classList.contains('trampa-oculta')) {
                    fila.classList.add('hidden');
                } else {
                    fila.classList.remove('hidden');
                }
            });
            
            // Actualizar el contador de resultados
            const filasVisibles = document.querySelectorAll('.fila-trampa:not(.hidden)').length;
            document.getElementById('cantidad-trampas').textContent = filasVisibles;
        });
    });
</script>

<!-- MODAL PARA AMPLIAR GRÁFICAS (fullscreen) -->
<div id="modal-grafica" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 hidden">
    <div class="bg-white w-screen h-screen max-w-none max-h-none rounded-none p-0 flex flex-col">
        <div class="flex items-center justify-between px-8 pt-6 pb-2">
            <h3 id="modal-grafica-titulo" class="text-2xl font-semibold">&nbsp;</h3>
            <button id="cerrar-modal-grafica" class="text-gray-500 hover:text-blue-600 text-3xl font-bold">&times;</button>
        </div>
        <div class="flex-1 flex flex-col items-center justify-center w-full">
            <canvas id="modal-grafica-canvas" style="max-width:1200px; max-height:700px; width:90vw; height:70vh; background:#fff; border-radius:12px;"></canvas>
        </div>
        <div id="modal-grafica-leyenda" class="px-8 pb-6"></div>
    </div>
</div>

<!-- SCRIPT para funcionalidad de ampliación de gráficas -->
<script>
let modalChartInstance = null;
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btn-ampliar-grafica').forEach(btn => {
        btn.addEventListener('click', function() {
            const canvasId = this.getAttribute('data-canvas');
            const titulo = this.getAttribute('data-titulo');
            const originalCanvas = document.getElementById(canvasId);
            const modal = document.getElementById('modal-grafica');
            const modalTitulo = document.getElementById('modal-grafica-titulo');
            const modalCanvas = document.getElementById('modal-grafica-canvas');
            const modalLeyenda = document.getElementById('modal-grafica-leyenda');

            // Obtener la instancia Chart.js original
            const originalChart = Chart.getChart(canvasId);
            if (!originalChart) return;

            // Limpiar el canvas del modal y destruir el gráfico anterior si existe
            if (modalChartInstance) {
                modalChartInstance.destroy();
                modalChartInstance = null;
            }
            const dpr = window.devicePixelRatio || 2;
            modalCanvas.width = Math.round(window.innerWidth * dpr);
            modalCanvas.height = Math.round(window.innerHeight * 0.8 * dpr);
            modalCanvas.style.width = '100vw';
            modalCanvas.style.height = '80vh';

            // Clonar los datos y opciones, y aumentar fuentes y resolución
            const clonedData = JSON.parse(JSON.stringify(originalChart.data));
            const clonedOptions = JSON.parse(JSON.stringify(originalChart.options));

            // Mejorar fuentes y colores para el modal
            if (clonedOptions.scales) {
                for (const axis in clonedOptions.scales) {
                    if (clonedOptions.scales[axis].ticks) {
                        clonedOptions.scales[axis].ticks.font = {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            size: 18,
                            weight: 'bold'
                        };
                        clonedOptions.scales[axis].ticks.color = '#222';
                    }
                    if (clonedOptions.scales[axis].title) {
                        clonedOptions.scales[axis].title.font = {
                            family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                            size: 20,
                            weight: 'bold'
                        };
                        clonedOptions.scales[axis].title.color = '#222';
                    }
                }
            }
            if (clonedOptions.plugins) {
                if (clonedOptions.plugins.title) {
                    clonedOptions.plugins.title.font = {
                        family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                        size: 24,
                        weight: 'bold'
                    };
                    clonedOptions.plugins.title.color = '#222';
                }
                if (clonedOptions.plugins.legend && clonedOptions.plugins.legend.labels) {
                    clonedOptions.plugins.legend.labels.font = {
                        family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                        size: 18,
                        weight: 'bold'
                    };
                    clonedOptions.plugins.legend.labels.color = '#222';
                }
                if (clonedOptions.plugins.datalabels) {
                    clonedOptions.plugins.datalabels.font = {
                        family: "'Segoe UI', 'Helvetica', 'Arial', sans-serif",
                        size: 18,
                        weight: 'bold'
                    };
                    clonedOptions.plugins.datalabels.color = '#222';
                }
            }
            clonedOptions.responsive = false;
            clonedOptions.maintainAspectRatio = false;
            clonedOptions.animation = false;
            clonedOptions.plugins = clonedOptions.plugins || {};
            clonedOptions.plugins.background = {
                color: '#fff'
            };

            // Título
            modalTitulo.textContent = titulo;
            // Leyenda (si existe)
            const legend = originalCanvas.parentElement.parentElement.querySelector('legend, .chart-legend');
            if (legend) {
                modalLeyenda.innerHTML = legend.outerHTML;
            } else {
                modalLeyenda.innerHTML = '';
            }

            // Renderizar el nuevo gráfico en el modal
            modalChartInstance = new Chart(modalCanvas.getContext('2d'), {
                type: originalChart.config.type,
                data: clonedData,
                options: clonedOptions,
                plugins: originalChart.config.plugins || []
            });

            // Mostrar modal
            modal.classList.remove('hidden');
        });
    });
    // Botón de cerrar modal
    document.getElementById('cerrar-modal-grafica').addEventListener('click', function() {
        document.getElementById('modal-grafica').classList.add('hidden');
        if (modalChartInstance) {
            modalChartInstance.destroy();
            modalChartInstance = null;
        }
    });
    // Cerrar modal al hacer click fuera del contenido
    document.getElementById('modal-grafica').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.add('hidden');
            if (modalChartInstance) {
                modalChartInstance.destroy();
                modalChartInstance = null;
            }
        }
    });
});
</script>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para descargar gráficas
    document.querySelectorAll('.btn-descargar-grafica').forEach(button => {
        button.addEventListener('click', function() {
            const canvasId = this.getAttribute('data-canvas');
            const titulo = this.getAttribute('data-titulo');
            const canvas = document.getElementById(canvasId);
            
            if (canvas) {
                try {
                    // Crear un enlace temporal
                    const link = document.createElement('a');
                    link.download = `${titulo.replace(/\s+/g, '_')}_${new Date().toISOString().split('T')[0]}.png`;
                    link.href = canvas.toDataURL('image/png');
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } catch (error) {
                    console.error('Error al descargar la gráfica:', error);
                    alert('Hubo un error al descargar la gráfica. Por favor, intente nuevamente.');
                }
            } else {
                console.error('No se encontró el canvas:', canvasId);
                alert('No se pudo encontrar la gráfica para descargar.');
            }
        });
    });

    // Función para expandir gráficas
    document.querySelectorAll('.btn-ampliar-grafica').forEach(button => {
        button.addEventListener('click', function() {
            const canvasId = this.getAttribute('data-canvas');
            const titulo = this.getAttribute('data-titulo');
            const canvas = document.getElementById(canvasId);
            
            if (canvas) {
                // Crear el modal si no existe
                let modal = document.getElementById('modal-grafica');
                if (!modal) {
                    modal = document.createElement('div');
                    modal.id = 'modal-grafica';
                    modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center';
                    modal.innerHTML = `
                        <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 relative">
                            <button class="absolute top-4 right-4 text-gray-500 hover:text-gray-700" onclick="document.getElementById('modal-grafica').classList.add('hidden')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                            <h3 class="text-xl font-semibold mb-4"></h3>
                            <div class="w-full h-[600px] flex items-center justify-center">
                                <canvas class="max-w-full max-h-full"></canvas>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);
                }

                // Actualizar el contenido del modal
                const modalTitle = modal.querySelector('h3');
                const modalCanvas = modal.querySelector('canvas');
                modalTitle.textContent = titulo;

                // Copiar el canvas original al modal
                const context = modalCanvas.getContext('2d');
                modalCanvas.width = canvas.width;
                modalCanvas.height = canvas.height;
                context.drawImage(canvas, 0, 0);

                // Mostrar el modal
                modal.classList.remove('hidden');
            }
        });
    });
});

// Función para exportar a PowerPoint
async function exportarAPowerPoint() {
    try {
        // Create a new PowerPoint presentation
        const pptx = new PptxGenJS();
        
        // Get selected charts
        const selectedCharts = Array.from(document.querySelectorAll('input[name="graficas[]"]:checked')).map(input => input.value);
        
        // Chart configurations
        const chartConfigs = {
            'plagasMayorPresencia': { 
                id: 'plagasMayorPresenciaChart', 
                title: 'Plaga con Mayor Presencia durante ' + (document.getElementById('mes-selector')?.options[document.getElementById('mes-selector')?.selectedIndex]?.text || 'el Mes')
            },
            'areasMayorIncidencia': { 
                id: 'areasMayorIncidenciaChart', 
                title: 'Áreas con Mayor Incidencia de Plaga'
            },
            'trampasMayorCaptura': { 
                id: 'trampasMayorCapturaChart', 
                title: 'Trampas con Mayor Captura'
            },
            'areasCapturasPorPlaga': { 
                id: 'areasCapturasPorPlagaChart', 
                title: 'Áreas que Presentaron Capturas'
            },
            'incidenciasTipo': { 
                id: 'incidenciasPlagaChart', 
                title: 'Incidencias por Tipo y Mes'
            },
            'trampasPorUbicacion': { 
                id: 'trampasPorUbicacionChart', 
                title: 'Distribución de Trampas'
            }
        };

        // Add title slide
        let slide = pptx.addSlide();
        slide.addText('Dashboard de Plantas', {
            x: '10%',
            y: '40%',
            w: '80%',
            h: '20%',
            fontSize: 44,
            color: '0088CC',
            bold: true,
            align: 'center'
        });

        // Add subtitle with date and selected plant
        const selectedPlant = document.getElementById('sede-selector').options[document.getElementById('sede-selector').selectedIndex].text;
        slide.addText([
            { text: selectedPlant + '\n', options: { fontSize: 24, color: '666666', align: 'center' } },
            { text: new Date().toLocaleDateString('es-ES', { year: 'numeric', month: 'long', day: 'numeric' }), options: { fontSize: 18, color: '666666', align: 'center' } }
        ], {
            x: '10%',
            y: '65%',
            w: '80%',
            h: '20%'
        });

        // Process each selected chart
        for (const chartKey of selectedCharts) {
            const config = chartConfigs[chartKey];
            if (!config) continue;

            const canvas = document.getElementById(config.id);
            if (!canvas) {
                console.error('No se encontró el canvas', config.id);
                continue;
            }

            // Create new slide
            slide = pptx.addSlide();
            
            // Add title
            slide.addText(config.title, {
                x: '5%',
                y: '5%',
                w: '90%',
                h: '10%',
                fontSize: 24,
                color: '0088CC',
                bold: true,
                align: 'center'
            });

            try {
                // Convert canvas to image
                const imgData = canvas.toDataURL('image/png');
                
                // Add chart image
                slide.addImage({
                    data: imgData,
                    x: '25%',
                    y: '25%',
                    w: '50%',
                    h: '40%'
                });
            } catch (error) {
                console.error('Error al procesar el gráfico:', error);
            }
        }

        // Save the presentation
        await pptx.writeFile({ fileName: 'Dashboard_Plantas_' + selectedPlant.replace(/[^a-z0-9]/gi, '_').toLowerCase() + '.pptx' });
    } catch (error) {
        console.error('Error al generar la presentación:', error);
        alert('Hubo un error al generar la presentación de PowerPoint. Por favor, intente nuevamente.\\n' + error);
    }
}

// ===== FUNCIONES DE UMBRALES (3 NIVELES) =====

// Estado global de umbrales - ahora maneja 3 niveles por gráfica
let umbralesActivos = {};

// Cargar umbrales guardados al inicializar
document.addEventListener('DOMContentLoaded', function() {
    cargarUmbralesGuardados();
});

// Función para cargar umbrales desde localStorage
function cargarUmbralesGuardados() {
    try {
        const umbralesGuardados = localStorage.getItem('umbrales_graficas_3niveles');
        if (umbralesGuardados) {
            umbralesActivos = JSON.parse(umbralesGuardados);
            
            // Aplicar umbrales guardados a los inputs
            Object.keys(umbralesActivos).forEach(chartId => {
                const umbral = umbralesActivos[chartId];
                
                // Aplicar valores a cada nivel
                ['bajo', 'medio', 'alto'].forEach(nivel => {
                    const input = document.getElementById(`umbral-${nivel}-${chartId}`);
                    if (input && umbral[nivel]) {
                        input.value = umbral[nivel];
                    }
                });
                
                // Aplicar estado del toggle
                const toggle = document.getElementById(`toggle-umbrales-${chartId}`);
                if (toggle && umbral.activo) {
                    toggle.textContent = 'Desactivar Umbrales';
                    toggle.classList.remove('bg-gray-200');
                    toggle.classList.add('bg-green-200', 'text-green-800');
                }
            });
        }
    } catch (error) {
        console.error('Error al cargar umbrales:', error);
    }
}

// Función para guardar umbrales en localStorage
function guardarUmbrales() {
    try {
        localStorage.setItem('umbrales_graficas_3niveles', JSON.stringify(umbralesActivos));
    } catch (error) {
        console.error('Error al guardar umbrales:', error);
    }
}

// Función para activar/desactivar umbrales (3 niveles)
function toggleUmbralesGrafica(chartId) {
    const toggle = document.getElementById(`toggle-umbrales-${chartId}`);
    const inputBajo = document.getElementById(`umbral-bajo-${chartId}`);
    const inputMedio = document.getElementById(`umbral-medio-${chartId}`);
    const inputAlto = document.getElementById(`umbral-alto-${chartId}`);
    
    if (!umbralesActivos[chartId]) {
        umbralesActivos[chartId] = { activo: false, bajo: null, medio: null, alto: null };
    }
    
    if (umbralesActivos[chartId].activo) {
        // Desactivar
        umbralesActivos[chartId].activo = false;
        toggle.textContent = 'Activar Umbrales';
        toggle.classList.remove('bg-green-200', 'text-green-800');
        toggle.classList.add('bg-gray-200', 'text-gray-800');
        
        // Para la gráfica de comparación de plagas, usar redibujado directo
        if (chartId === 'plagasMayorPresenciaChart') {
            actualizarGraficaComparacionConUmbrales();
        } else {
            // Para otras gráficas, usar el método de anotaciones
        removerUmbralesDeGrafica(chartId);
        }
    } else {
        // Validar valores
        const valorBajo = parseFloat(inputBajo.value);
        const valorMedio = parseFloat(inputMedio.value);
        const valorAlto = parseFloat(inputAlto.value);
        
        if (isNaN(valorBajo) || isNaN(valorMedio) || isNaN(valorAlto)) {
            alert('Por favor, complete todos los valores de umbral (bajo, medio, alto).');
            return;
        }
        
        if (valorBajo >= valorMedio || valorMedio >= valorAlto) {
            alert('Los valores deben ser: Bajo < Medio < Alto');
            return;
        }
        
        // Activar
        umbralesActivos[chartId].activo = true;
        umbralesActivos[chartId].bajo = valorBajo;
        umbralesActivos[chartId].medio = valorMedio;
        umbralesActivos[chartId].alto = valorAlto;
        
        toggle.textContent = 'Desactivar Umbrales';
        toggle.classList.remove('bg-gray-200', 'text-gray-800');
        toggle.classList.add('bg-green-200', 'text-green-800');
        
        // Para la gráfica de comparación de plagas, usar redibujado directo
        if (chartId === 'plagasMayorPresenciaChart') {
            actualizarGraficaComparacionConUmbrales();
        } else {
            // Para otras gráficas, usar el método de anotaciones
        agregarUmbralesAGrafica(chartId, valorBajo, valorMedio, valorAlto);
        }
    }
    
    guardarUmbrales();
}

// Función para actualizar valores de umbrales
function actualizarUmbralesGrafica(chartId) {
    const inputBajo = document.getElementById(`umbral-bajo-${chartId}`);
    const inputMedio = document.getElementById(`umbral-medio-${chartId}`);
    const inputAlto = document.getElementById(`umbral-alto-${chartId}`);
    
    if (!umbralesActivos[chartId]) {
        umbralesActivos[chartId] = { activo: false, bajo: null, medio: null, alto: null };
    }
    
    const valorBajo = parseFloat(inputBajo.value);
    const valorMedio = parseFloat(inputMedio.value);
    const valorAlto = parseFloat(inputAlto.value);
    
    // Actualizar valores
    umbralesActivos[chartId].bajo = isNaN(valorBajo) ? null : valorBajo;
    umbralesActivos[chartId].medio = isNaN(valorMedio) ? null : valorMedio;
    umbralesActivos[chartId].alto = isNaN(valorAlto) ? null : valorAlto;
    
    // Para la gráfica de comparación de plagas, usar redibujado directo
    if (chartId === 'plagasMayorPresenciaChart') {
        actualizarGraficaComparacionConUmbrales();
    } else {
        // Para otras gráficas, usar el método de anotaciones
    if (umbralesActivos[chartId].activo && !isNaN(valorBajo) && !isNaN(valorMedio) && !isNaN(valorAlto)) {
        if (valorBajo < valorMedio && valorMedio < valorAlto) {
            agregarUmbralesAGrafica(chartId, valorBajo, valorMedio, valorAlto);
            }
        }
    }
    
    guardarUmbrales();
}

// Función para agregar líneas de umbral (3 niveles) a una gráfica
function agregarUmbralesAGrafica(chartId, valorBajo, valorMedio, valorAlto) {
    const chart = Chart.getChart(chartId);
    if (!chart) return;
    
    // Configurar anotaciones de umbrales
    if (!chart.options.plugins) {
        chart.options.plugins = {};
    }
    
    if (!chart.options.plugins.annotation) {
        chart.options.plugins.annotation = {
            annotations: {}
        };
    }
    
    // Colores para cada nivel
    const colores = {
        bajo: '#22c55e',    // Verde
        medio: '#eab308',   // Amarillo/Naranja
        alto: '#ef4444'     // Rojo
    };
    
    // Agregar línea de umbral bajo (verde)
    chart.options.plugins.annotation.annotations[`umbral-bajo-${chartId}`] = {
        type: 'line',
        yMin: valorBajo,
        yMax: valorBajo,
        borderColor: colores.bajo,
        borderWidth: 2,
        borderDash: [5, 5],
        label: {
            display: true,
            content: `Bajo: ${valorBajo}`,
            position: 'start',
            backgroundColor: colores.bajo,
            color: 'white',
            font: {
                size: 10,
                weight: 'bold'
            },
            padding: 3,
            cornerRadius: 3
        }
    };
    
    // Agregar línea de umbral medio (amarillo)
    chart.options.plugins.annotation.annotations[`umbral-medio-${chartId}`] = {
        type: 'line',
        yMin: valorMedio,
        yMax: valorMedio,
        borderColor: colores.medio,
        borderWidth: 2,
        borderDash: [5, 5],
        label: {
            display: true,
            content: `Medio: ${valorMedio}`,
            position: 'center',
            backgroundColor: colores.medio,
            color: 'white',
            font: {
                size: 10,
                weight: 'bold'
            },
            padding: 3,
            cornerRadius: 3
        }
    };
    
    // Agregar línea de umbral alto (rojo)
    chart.options.plugins.annotation.annotations[`umbral-alto-${chartId}`] = {
        type: 'line',
        yMin: valorAlto,
        yMax: valorAlto,
        borderColor: colores.alto,
        borderWidth: 2,
        borderDash: [5, 5],
        label: {
            display: true,
            content: `Alto: ${valorAlto}`,
            position: 'end',
            backgroundColor: colores.alto,
            color: 'white',
            font: {
                size: 10,
                weight: 'bold'
            },
            padding: 3,
            cornerRadius: 3
        }
    };
    
    // Actualizar gráfica
    chart.update();
    
    // Verificar si algún valor excede los umbrales
    verificarExcesosUmbrales(chartId, valorBajo, valorMedio, valorAlto, chart);
}

// Función para remover umbrales de una gráfica
function removerUmbralesDeGrafica(chartId) {
    const chart = Chart.getChart(chartId);
    if (!chart || !chart.options.plugins?.annotation?.annotations) return;
    
    // Remover anotaciones de umbrales
    delete chart.options.plugins.annotation.annotations[`umbral-bajo-${chartId}`];
    delete chart.options.plugins.annotation.annotations[`umbral-medio-${chartId}`];
    delete chart.options.plugins.annotation.annotations[`umbral-alto-${chartId}`];
    
    chart.update();
}

// Función para verificar si hay valores que exceden los umbrales (3 niveles)
function verificarExcesosUmbrales(chartId, valorBajo, valorMedio, valorAlto, chart) {
    let valoresExcedidos = [];
    
    console.log('Verificando umbrales para:', chartId, 'Bajo:', valorBajo, 'Medio:', valorMedio, 'Alto:', valorAlto);
    console.log('Datos de la gráfica:', chart.data);
    
    // Obtener datos de la gráfica
    if (chart.data && chart.data.datasets) {
        chart.data.datasets.forEach((dataset, datasetIndex) => {
            console.log(`Dataset ${datasetIndex}:`, dataset);
            
            if (dataset.data) {
                dataset.data.forEach((valor, index) => {
                    const label = chart.data.labels ? chart.data.labels[index] : `Elemento ${index + 1}`;
                    
                    // Asegurar que el valor sea numérico
                    const valorNumerico = typeof valor === 'number' ? valor : parseFloat(valor);
                    
                    console.log(`Verificando ${label}: ${valorNumerico} vs umbrales Bajo:${valorBajo} Medio:${valorMedio} Alto:${valorAlto}`);
                    
                    // Determinar el nivel de alerta basado en los 3 umbrales
                    let nivelAlerta = '';
                    let colorAlerta = '';
                    
                    if (valorNumerico >= valorAlto) {
                        nivelAlerta = 'CRÍTICO';
                        colorAlerta = 'rojo';
                        valoresExcedidos.push(`${label}: ${valorNumerico} (Nivel CRÍTICO - por encima de ${valorAlto})`);
                        console.log('Valor CRÍTICO encontrado:', label, valorNumerico);
                    } else if (valorNumerico >= valorMedio) {
                        nivelAlerta = 'MEDIO';
                        colorAlerta = 'amarillo';
                        valoresExcedidos.push(`${label}: ${valorNumerico} (Nivel MEDIO - entre ${valorMedio} y ${valorAlto})`);
                        console.log('Valor MEDIO encontrado:', label, valorNumerico);
                    } else if (valorNumerico >= valorBajo) {
                        nivelAlerta = 'BAJO';
                        colorAlerta = 'verde';
                        // Solo reportar nivel bajo si es significativo
                        if (chartId !== 'trampasPorUbicacionChart') {
                            valoresExcedidos.push(`${label}: ${valorNumerico} (Nivel BAJO - entre ${valorBajo} y ${valorMedio})`);
                            console.log('Valor BAJO encontrado:', label, valorNumerico);
                        }
                    }
                });
            }
        });
        
        // Para gráficas con datasets múltiples o estructura compleja, verificar también datos apilados
        if (chart.config.type === 'bar' && chart.data.datasets.length > 1) {
            console.log('Gráfica de barras con múltiples datasets, verificando totales apilados...');
            
            // Calcular totales por categoría cuando hay múltiples datasets
            if (chart.data.labels) {
                chart.data.labels.forEach((label, index) => {
                    let totalPorCategoria = 0;
                    
                    chart.data.datasets.forEach(dataset => {
                        if (dataset.data && dataset.data[index] !== undefined) {
                            const valor = typeof dataset.data[index] === 'number' ? dataset.data[index] : parseFloat(dataset.data[index]);
                            if (!isNaN(valor)) {
                                totalPorCategoria += valor;
                            }
                        }
                    });
                    
                    console.log(`Total apilado para ${label}: ${totalPorCategoria}`);
                    
                    // Determinar el nivel de alerta para totales apilados basado en los 3 umbrales
                    let nivelAlerta = '';
                    let colorAlerta = '';
                    
                    if (totalPorCategoria >= valorAlto) {
                        nivelAlerta = 'CRÍTICO';
                        colorAlerta = 'rojo';
                        // Evitar duplicados
                        const yaExiste = valoresExcedidos.some(item => item.includes(label));
                        if (!yaExiste) {
                            valoresExcedidos.push(`${label}: ${totalPorCategoria} (Total CRÍTICO - por encima de ${valorAlto})`);
                            console.log('Total apilado CRÍTICO:', label, totalPorCategoria);
                        }
                    } else if (totalPorCategoria >= valorMedio) {
                        nivelAlerta = 'MEDIO';
                        colorAlerta = 'amarillo';
                        // Evitar duplicados
                        const yaExiste = valoresExcedidos.some(item => item.includes(label));
                        if (!yaExiste) {
                            valoresExcedidos.push(`${label}: ${totalPorCategoria} (Total MEDIO - entre ${valorMedio} y ${valorAlto})`);
                            console.log('Total apilado MEDIO:', label, totalPorCategoria);
                        }
                    } else if (totalPorCategoria >= valorBajo) {
                        nivelAlerta = 'BAJO';
                        colorAlerta = 'verde';
                        // Solo reportar nivel bajo si es significativo y no es gráfica de trampas
                        if (chartId !== 'trampasPorUbicacionChart') {
                            const yaExiste = valoresExcedidos.some(item => item.includes(label));
                            if (!yaExiste) {
                                valoresExcedidos.push(`${label}: ${totalPorCategoria} (Total BAJO - entre ${valorBajo} y ${valorMedio})`);
                                console.log('Total apilado BAJO:', label, totalPorCategoria);
                            }
                        }
                    }
                });
            }
        }
    }
    
    console.log('Valores que exceden los umbrales encontrados:', valoresExcedidos);
    
    // Mostrar alertas si hay excesos
    if (valoresExcedidos.length > 0) {
        mostrarAlertaUmbrales(chartId, valoresExcedidos, valorBajo, valorMedio, valorAlto);
    } else {
        console.log('No se encontraron valores que excedan los umbrales');
    }
}

// Función para mostrar alerta de umbrales excedidos (3 niveles)
function mostrarAlertaUmbrales(chartId, valoresExcedidos, valorBajo, valorMedio, valorAlto) {
    const nombreGrafica = {
        'trampasMayorCapturaChart': 'Trampas con Mayor Captura',
        'areasCapturasPorPlagaChart': 'Áreas que Presentaron Capturas',
        'incidenciasTipoChart': 'Incidencias por Tipo y Mes',
        'trampasPorUbicacionChart': 'Distribución de Trampas'
    };
    
    const tipoAlerta = 'en diferentes niveles de umbral';
    const fechaHora = new Date().toLocaleString('es-ES');
    
    // Verificar si ya existe un modal de alerta y cerrarlo
    const modalExistente = document.getElementById('modal-alerta-umbral');
    if (modalExistente) {
        modalExistente.remove();
    }
    
    // Crear modal de alerta completa
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-start justify-center pt-4 pb-4 overflow-y-auto';
    modal.id = 'modal-alerta-umbral';
    
         // Generar texto completo de la alerta
     const textoAlerta = `ALERTA DE UMBRALES - ${nombreGrafica[chartId]}
Fecha y hora: ${fechaHora}
Tipo de alerta: Valores ${tipoAlerta}
Umbrales configurados:
  • Nivel Bajo: ${valorBajo} (Verde)
  • Nivel Medio: ${valorMedio} (Amarillo)  
  • Nivel Alto: ${valorAlto} (Rojo)

Valores detectados:
${valoresExcedidos.map((valor, index) => `${index + 1}. ${valor}`).join('\n')}

Recomendaciones:
${chartId === 'trampasPorUbicacionChart' 
    ? '- Considerar instalar trampas adicionales en las áreas con menos cobertura\n- Revisar la efectividad de las trampas existentes\n- Evaluar si es necesario reubicar algunas trampas'
    : '- Investigar las causas del incremento en las capturas/incidencias\n- Implementar medidas correctivas inmediatas\n- Programar inspecciones adicionales en las áreas afectadas\n- Considerar ajustar la estrategia de control de plagas'
}`;
    
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[90vh] flex flex-col">
            <!-- Header - Fixed at top -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-red-50 flex-shrink-0">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-semibold text-red-800">Alerta de Umbral</h3>
                        <p class="text-sm text-red-600">${nombreGrafica[chartId]}</p>
                    </div>
                </div>
                <button onclick="document.getElementById('modal-alerta-umbral').remove()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            <!-- Content - Scrollable -->
            <div class="p-6 flex-1 overflow-y-auto">
                <div class="mb-4">
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-gray-600"><strong>Fecha y hora:</strong> ${fechaHora}</span>
                    </div>
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm0-2a6 6 0 100-12 6 6 0 000 12zm0-9a1 1 0 011 1v4a1 1 0 11-2 0V8a1 1 0 011-1z" clip-rule="evenodd"/>
                        </svg>
                        <span class="text-sm text-gray-600"><strong>Tipo de alerta:</strong> Valores ${tipoAlerta}</span>
                    </div>
                    <div class="mb-4">
                        <div class="flex items-center mb-2">
                            <svg class="w-5 h-5 text-purple-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"/>
                            </svg>
                            <span class="text-sm text-gray-800 font-medium">Umbrales configurados:</span>
                        </div>
                        <div class="ml-7 space-y-1">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Nivel Bajo: ${valorBajo}</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Nivel Medio: ${valorMedio}</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                <span class="text-sm text-gray-600">Nivel Alto: ${valorAlto}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 text-red-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        Valores detectados:
                    </h4>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <ul class="space-y-2">
                            ${valoresExcedidos.map((valor, index) => `
                                <li class="flex items-center text-sm">
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full mr-3 font-medium">${index + 1}</span>
                                    <span class="text-gray-700">${valor}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                </div>

                <div class="mb-6">
                    <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" clip-rule="evenodd"/>
                        </svg>
                        Recomendaciones:
                    </h4>
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <ul class="space-y-2 text-sm text-blue-800">
                            ${(chartId === 'trampasPorUbicacionChart' 
                                ? [
                                    'Considerar instalar trampas adicionales en las áreas con menos cobertura',
                                    'Revisar la efectividad de las trampas existentes',
                                    'Evaluar si es necesario reubicar algunas trampas'
                                  ]
                                : [
                                    'Investigar las causas del incremento en las capturas/incidencias',
                                    'Implementar medidas correctivas inmediatas',
                                    'Programar inspecciones adicionales en las áreas afectadas',
                                    'Considerar ajustar la estrategia de control de plagas'
                                  ]
                            ).map(rec => `
                                <li class="flex items-start">
                                    <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span>${rec}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                </div>

            </div>
            
            <!-- Botones de acción - Fixed at bottom -->
            <div class="flex flex-col sm:flex-row gap-3 p-4 border-t border-gray-200 bg-gray-50 flex-shrink-0">
                    <button onclick="agregarAlertaANotas('${chartId}', \`${textoAlerta.replace(/`/g, '\\`')}\`)" 
                            class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Agregar a Notas de la Gráfica
                    </button>
                    <button onclick="copiarAlertaAlPortapapeles(\`${textoAlerta.replace(/`/g, '\\`')}\`)" 
                            class="flex-1 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Copiar al Portapapeles
                    </button>
                <button onclick="document.getElementById('modal-alerta-umbral').remove()" 
                        class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Funcionalidad para cerrar el modal
    const cerrarModal = () => {
        const modalElement = document.getElementById('modal-alerta-umbral');
        if (modalElement) {
            modalElement.remove();
        }
    };
    
    // Cerrar al presionar Escape
    modal.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            cerrarModal();
        }
    });
    
    // Cerrar al hacer click fuera del contenido del modal
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            cerrarModal();
        }
    });
    
    // Enfocar en el modal para accesibilidad
    modal.focus();
    modal.setAttribute('tabindex', '-1');
}

// Función para aplicar umbrales después de que se carguen las gráficas
function aplicarUmbralesAlCargar() {
    // Esperar un poco para que las gráficas se terminen de cargar
    setTimeout(() => {
        Object.keys(umbralesActivos).forEach(chartId => {
            const umbral = umbralesActivos[chartId];
            if (umbral.activo && umbral.valor) {
                agregarUmbralAGrafica(chartId, umbral.valor);
            }
        });
    }, 2000);
}

// Llamar la función cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    aplicarUmbralesAlCargar();
});

// Función para agregar alerta a las notas de la gráfica
function agregarAlertaANotas(chartId, textoAlerta) {
    const textarea = document.getElementById(`notas-grafico-${chartId}`);
    if (textarea) {
        // Preparar el texto de la alerta para las notas de forma más legible
        const fechaHora = new Date().toLocaleString('es-ES');
        const separador = '='.repeat(50);
        const alertaFormateada = `\n\n${separador}\nALERTA DE UMBRAL - ${fechaHora}\n${separador}\n${textoAlerta}\n${separador}\n`;
        
        // Agregar la alerta al final del contenido existente
        if (textarea.value.trim()) {
            textarea.value += alertaFormateada;
        } else {
            textarea.value = alertaFormateada.trim();
        }
        
        // Enfocar en el textarea y hacer scroll hacia abajo
        textarea.focus();
        textarea.scrollTop = textarea.scrollHeight;
        
        // Resaltar el textarea temporalmente
        textarea.classList.add('textarea-highlight');
        textarea.style.border = '2px solid #10B981';
        textarea.style.backgroundColor = '#F0FDF4';
        setTimeout(() => {
            textarea.style.border = '';
            textarea.style.backgroundColor = '';
            textarea.classList.remove('textarea-highlight');
        }, 2000);
        
        // Mostrar confirmación
        mostrarNotificacion('Alerta agregada a las notas de la gráfica', 'success');
        
        // Cerrar el modal
        const modal = document.getElementById('modal-alerta-umbral');
        if (modal) {
            modal.remove();
        }
    } else {
        mostrarNotificacion('No se pudo encontrar el área de notas', 'error');
    }
}

// Función para copiar alerta al portapapeles
async function copiarAlertaAlPortapapeles(textoAlerta) {
    try {
        if (navigator.clipboard && window.isSecureContext) {
            // Usar la API moderna de clipboard
            await navigator.clipboard.writeText(textoAlerta);
            mostrarNotificacion('Alerta copiada al portapapeles', 'success');
        } else {
            // Fallback para navegadores más antiguos
            const textarea = document.createElement('textarea');
            textarea.value = textoAlerta;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.focus();
            textarea.select();
            
            if (document.execCommand('copy')) {
                mostrarNotificacion('Alerta copiada al portapapeles', 'success');
            } else {
                throw new Error('No se pudo copiar usando execCommand');
            }
            
            document.body.removeChild(textarea);
        }
    } catch (error) {
        console.error('Error al copiar al portapapeles:', error);
        mostrarNotificacion('Error al copiar al portapapeles', 'error');
        
        // Como último recurso, mostrar un modal con el texto seleccionado
        mostrarTextoParaCopiar(textoAlerta);
    }
}

// Función para mostrar texto para copiar manualmente
function mostrarTextoParaCopiar(texto) {
    const modal = document.createElement('div');
    modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
    modal.innerHTML = `
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full mx-4 max-h-[80vh] overflow-y-auto modal-entrance">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Copiar texto manualmente</h3>
                    <button onclick="this.closest('.fixed').remove()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-gray-600 mb-4">Selecciona todo el texto y cópialo manualmente (Ctrl+C):</p>
                <textarea class="w-full h-64 p-3 border rounded-lg font-mono text-sm" readonly onclick="this.select()">${texto}</textarea>
                <div class="flex justify-end mt-4">
                    <button onclick="this.closest('.fixed').remove()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                        Cerrar
                    </button>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'info') {
    const colores = {
        'success': 'bg-green-500',
        'error': 'bg-red-500',
        'info': 'bg-blue-500',
        'warning': 'bg-yellow-500'
    };
    
    const notificacion = document.createElement('div');
    notificacion.className = `fixed top-4 right-4 ${colores[tipo]} text-white px-4 py-3 rounded-lg shadow-lg z-50 max-w-sm`;
    notificacion.innerHTML = `
        <div class="flex items-center">
            <span class="text-sm font-medium">${mensaje}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(notificacion);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notificacion.parentElement) {
            notificacion.remove();
        }
    }, 5000);
}

// Función para limpiar todos los umbrales
function limpiarTodosLosUmbrales() {
    if (confirm('¿Estás seguro de que quieres limpiar todos los umbrales configurados?\n\nEsta acción no se puede deshacer.')) {
        // Limpiar el estado global
        umbralesActivos = {};
        
        // Limpiar localStorage
        localStorage.removeItem('umbrales_graficas');
        
        // Limpiar todos los inputs y botones
        document.querySelectorAll('.umbral-input').forEach(input => {
            input.value = '';
        });
        
        document.querySelectorAll('[id^="toggle-umbral-"]').forEach(button => {
            button.textContent = 'Activar';
            button.classList.remove('bg-green-200');
            button.classList.add('bg-yellow-200', 'bg-red-200', 'bg-orange-200', 'bg-blue-200');
        });
        
        // Remover todas las líneas de umbral de las gráficas
        const chartIds = ['trampasMayorCapturaChart', 'areasCapturasPorPlagaChart', 'incidenciasTipoChart', 'trampasPorUbicacionChart'];
        chartIds.forEach(chartId => {
            removerUmbralDeGrafica(chartId);
        });
        
        // Mostrar confirmación
        mostrarNotificacion('Todos los umbrales han sido eliminados', 'success');
    }
}
</script>
<?= $this->endSection() ?> 
