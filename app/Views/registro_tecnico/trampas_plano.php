<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Trampas del Plano: <?= esc($plano['nombre']) ?></h1>
        <div class="flex space-x-2">
            <a href="<?= base_url('reports/reporte_visita?plano_id='.$planoId.'&fecha='.date('Y-m-d')) ?>" 
               class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 inline-flex items-center">
                <i class="fas fa-file-pdf mr-2"></i>Reporte de Visita
            </a>
            <a href="<?= base_url('registro_tecnico') ?>" class="px-3 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors text-sm flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z" />
                </svg>
                Volver
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Información del plano y sede -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600"><strong>Plano:</strong> <?= esc($plano['nombre']) ?></p>
                    <p class="text-sm text-gray-600"><strong>Sede:</strong> <?= esc($sede['nombre']) ?></p>
                </div>
                <div>
                    <p class="text-sm text-gray-600"><strong>Fecha de creación:</strong> <?= date('d/m/Y', strtotime($plano['fecha_creacion'])) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Filtros para la tabla -->
        <div class="bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="filtro-tipo" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por tipo</label>
                    <select id="filtro-tipo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todos los tipos</option>
                        <?php 
                        $tipos = array_unique(array_column($trampas, 'tipo'));
                        foreach ($tipos as $tipo): 
                            if (!empty($tipo)):
                        ?>
                            <option value="<?= esc($tipo) ?>"><?= esc($tipo) ?></option>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </select>
                </div>
                <div>
                    <label for="filtro-ubicacion" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por ubicación</label>
                    <select id="filtro-ubicacion" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Todas las ubicaciones</option>
                        <?php 
                        $ubicaciones = array_unique(array_column($trampas, 'ubicacion'));
                        foreach ($ubicaciones as $ubicacion): 
                            if (!empty($ubicacion)):
                        ?>
                            <option value="<?= esc($ubicacion) ?>"><?= esc($ubicacion) ?></option>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </select>
                </div>
            </div>
            <div class="mt-4 flex justify-end">
                <button id="btn-limpiar-filtros" class="px-3 py-1 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 transition-colors text-sm">
                    Limpiar filtros
                </button>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border border-gray-200 divide-y divide-gray-200 rounded-lg">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Instalación</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody id="trampas-body" class="bg-white divide-y divide-gray-200">
                    <?php if (empty($trampas)): ?>
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">No se encontraron trampas para este plano</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($trampas as $trampa): ?>
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $trampa['id'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $trampa['id_trampa'] ?? '-' ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $trampa['tipo'] ?? 'No especificado' ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $trampa['ubicacion'] ?? 'No especificado' ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= $trampa['fecha_instalacion'] ? date('d/m/Y', strtotime($trampa['fecha_instalacion'])) : 'No especificado' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center space-x-2">
                                        <button class="text-blue-600 hover:text-blue-800 rounded-full p-1 hover:bg-blue-100" onclick="verDetalleTrampa(<?= $trampa['id'] ?>)">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z" />
                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        <button class="text-green-600 hover:text-green-800 rounded-full p-1 hover:bg-green-100" 
                                            onclick="abrirModalIncidencia(<?= $trampa['id'] ?>, '<?= addslashes($trampa['id_trampa'] ?? '-') ?>', '<?= addslashes($trampa['ubicacion'] ?? 'No especificado') ?>')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Registrar Incidencia -->
<div id="modalIncidencia" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50" style="display: none; align-items: center; justify-content: center;">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Registrar Incidencia</h3>
                    <span class="text-sm text-gray-500">Incidencias en lista: <span id="contadorIncidencias">0</span></span>
                </div>
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
                        <div id="cantidad_organismos_container" style="display: none;">
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
                        <button type="button" onclick="cerrarModalIncidencia()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                            Cancelar
                        </button>
                        <button type="button" id="btnAgregarIncidenciaLista"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-md flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                            </svg>
                            Agregar a lista
                        </button>
                        <button type="button" id="btnGuardarTodasIncidencias"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            Guardar todas
                        </button>
                    </div>
                </form>
                <!-- Lista de incidencias agregadas -->
                <div id="listaIncidenciasModal" class="mt-6">
                    <h4 class="font-semibold mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1.323l3.954 1.582 1.599-.8a1 1 0 01.894 1.79l-1.233.616 1.738 5.42a1 1 0 01-.285 1.05A3.989 3.989 0 0115 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.715-5.349L11 4.477V16h2a1 1 0 110 2H7a1 1 0 110-2h2V4.477L6.237 7.582l1.715 5.349a1 1 0 01-.285 1.05A3.989 3.989 0 015 15a3.989 3.989 0 01-2.667-1.019 1 1 0 01-.285-1.05l1.738-5.42-1.233-.616a1 1 0 01.894-1.79l1.599.8L9 4.323V3a1 1 0 011-1z" clip-rule="evenodd" />
                        </svg>
                        Incidencias agregadas
                    </h4>
                    <ul id="incidenciasAgregadas" class="space-y-2 max-h-60 overflow-y-auto"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Editar ID de Trampa -->
<div id="modalEditarId" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
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

<!-- Estilos adicionales para el modal -->
<style>
.tab-content { transition: all 0.3s ease; }
.tab-button { transition: all 0.2s ease; }
.tab-button:hover { transform: translateY(-1px); }
#modalHistorialTrampa { 
    display: none !important; 
}
#modalHistorialTrampa.show { 
    display: flex !important; 
    align-items: center; 
    justify-content: center; 
}
.animate-spin { animation: spin 1s linear infinite; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

/* Scrollbar personalizada - Forzar visibilidad */
#modalHistorialTrampa .custom-scrollbar {
    overflow-y: scroll !important;
    scrollbar-width: auto !important;
    scrollbar-color: #3B82F6 #E5E7EB !important;
}

#modalHistorialTrampa .custom-scrollbar::-webkit-scrollbar {
    width: 16px !important;
    height: 16px !important;
    display: block !important;
}

#modalHistorialTrampa .custom-scrollbar::-webkit-scrollbar-track {
    background: #E5E7EB !important;
    border-radius: 8px !important;
    border: 1px solid #D1D5DB !important;
}

#modalHistorialTrampa .custom-scrollbar::-webkit-scrollbar-thumb {
    background: #3B82F6 !important;
    border-radius: 8px !important;
    border: 2px solid #E5E7EB !important;
    min-height: 30px !important;
}

#modalHistorialTrampa .custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #1D4ED8 !important;
}

#modalHistorialTrampa .custom-scrollbar::-webkit-scrollbar-button {
    display: none !important;
}

/* Scrollbar específica para el contenido del modal */
#historialContent {
    overflow-y: scroll !important;
    max-height: calc(90vh - 200px) !important;
}

/* Forzar scrollbar en área de pestañas */
.tab-content-container {
    overflow-y: scroll !important;
    max-height: 400px !important;
}

/* Asegurar scrollbar visible en Firefox */
#modalHistorialTrampa * {
    scrollbar-width: auto !important;
    scrollbar-color: #3B82F6 #E5E7EB !important;
}

/* Forzar scrollbar siempre visible */
#modalHistorialTrampa .custom-scrollbar,
#historialContent,
.tab-content-container {
    overflow-y: scroll !important;
    -ms-overflow-style: scrollbar !important;
}

/* CSS específico para asegurar visibilidad */
#modalHistorialTrampa .custom-scrollbar::-webkit-scrollbar {
    -webkit-appearance: none !important;
}

#modalHistorialTrampa .custom-scrollbar::-webkit-scrollbar:vertical {
    width: 16px !important;
}

#modalHistorialTrampa .custom-scrollbar::-webkit-scrollbar:horizontal {
    height: 16px !important;
}

/* Asegurar que el modal sea responsive */
@media (max-width: 768px) {
    #modalHistorialTrampa .max-w-6xl {
        max-width: 95vw;
    }
}
</style>

<!-- Modal para Ver Historial de Trampa -->
<div id="modalHistorialTrampa" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4" style="display: none;" onclick="cerrarModalHistorial()">
    <div class="w-full max-w-6xl max-h-[90vh] bg-white shadow-xl rounded-2xl flex flex-col custom-scrollbar" onclick="event.stopPropagation()" style="overflow-y: scroll !important;">
            <!-- Header del Modal -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gradient-to-r from-blue-500 to-blue-600">
                <div class="text-white">
                    <h3 class="text-xl font-bold">Historial de Trampa</h3>
                    <p class="text-blue-100 text-sm" id="trampaHeaderInfo">Cargando información...</p>
                </div>
                <button onclick="cerrarModalHistorial()" class="text-white hover:text-gray-200 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Loading State -->
            <div id="historialLoading" class="p-8 text-center">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
                <p class="text-gray-600">Cargando historial de la trampa...</p>
            </div>

            <!-- Content -->
            <div id="historialContent" class="hidden flex-1 flex flex-col overflow-y-scroll custom-scrollbar" style="overflow-y: scroll !important;">
                <!-- Estadísticas Resumen -->
                <div class="p-6 bg-gray-50 border-b flex-shrink-0">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                            <div class="text-2xl font-bold text-blue-600" id="totalIncidencias">0</div>
                            <div class="text-sm text-gray-600">Total Incidencias</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                            <div class="text-2xl font-bold text-green-600" id="totalOrganismos">0</div>
                            <div class="text-sm text-gray-600">Total Organismos</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                            <div class="text-2xl font-bold text-purple-600" id="tiposPlagaDiferentes">0</div>
                            <div class="text-sm text-gray-600">Tipos de Plaga</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg shadow-sm text-center">
                            <div class="text-2xl font-bold text-orange-600" id="ultimaIncidencia">-</div>
                            <div class="text-sm text-gray-600">Última Incidencia</div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="border-b border-gray-200 flex-shrink-0">
                    <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                        <button onclick="cambiarTab('incidencias')" id="tabIncidencias" class="tab-button py-2 px-1 border-b-2 border-blue-500 text-blue-600 whitespace-nowrap font-medium text-sm">
                            📊 Incidencias
                        </button>
                        <button onclick="cambiarTab('movimientos')" id="tabMovimientos" class="tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap font-medium text-sm">
                            🔄 Movimientos
                        </button>
                        <button onclick="cambiarTab('estadisticas')" id="tabEstadisticas" class="tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap font-medium text-sm">
                            📈 Estadísticas
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div class="p-6 flex-1 tab-content-container custom-scrollbar" style="overflow-y: scroll !important; max-height: 400px !important;">
                    <!-- Tab Incidencias -->
                    <div id="contentIncidencias" class="tab-content">
                        <h4 class="text-lg font-semibold mb-4">📊 Historial de Incidencias</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo Plaga</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo Incidencia</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cantidad</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Inspector</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notas</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaIncidencias" class="bg-white divide-y divide-gray-200">
                                    <!-- Se llenará dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Movimientos -->
                    <div id="contentMovimientos" class="tab-content hidden">
                        <h4 class="text-lg font-semibold mb-4">🔄 Historial de Movimientos</h4>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fecha</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zona Anterior</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zona Nueva</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comentario</th>
                                    </tr>
                                </thead>
                                <tbody id="tablaMovimientos" class="bg-white divide-y divide-gray-200">
                                    <!-- Se llenará dinámicamente -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Tab Estadísticas -->
                    <div id="contentEstadisticas" class="tab-content hidden">
                        <h4 class="text-lg font-semibold mb-4">📈 Estadísticas por Tipo de Plaga</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="bg-white p-4 rounded-lg border">
                                <h5 class="font-medium mb-3">Distribución por Tipo de Plaga</h5>
                                <div id="listaTiposPlagas">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                            </div>
                            <div class="bg-white p-4 rounded-lg border">
                                <h5 class="font-medium mb-3">Información Adicional</h5>
                                <div id="infoAdicional" class="text-sm text-gray-600">
                                    <!-- Se llenará dinámicamente -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end flex-shrink-0">
                <button onclick="cerrarModalHistorial()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Cerrar
                </button>
            </div>
        </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, inicializando componentes...');
    
    // Referencias a elementos del DOM
    const filtroTipo = document.getElementById('filtro-tipo');
    const filtroUbicacion = document.getElementById('filtro-ubicacion');
    const btnLimpiarFiltros = document.getElementById('btn-limpiar-filtros');
    const trampasBody = document.getElementById('trampas-body');
    const modalIncidencia = document.getElementById('modalIncidencia');
    const formIncidencia = document.getElementById('formIncidencia');
    const tipoPlagaSelect = document.getElementById('tipo_plaga_select');
    const tipoPlagaPersonalizadoContainer = document.getElementById('tipo_plaga_personalizado_container');
    const tipoPlagaPersonalizado = document.getElementById('tipo_plaga_personalizado');
    const tipoPlaga = document.getElementById('tipo_plaga');
    
    console.log('Estado del modal:', modalIncidencia ? 'Encontrado' : 'No encontrado');
    
    // Configurar fecha actual por defecto
    const fechaIncidencia = document.getElementById('fecha_incidencia');
    if (fechaIncidencia) {
        // Crear fecha actual con formato adecuado para el input datetime-local
        const ahora = new Date();
        // Asegurar que la fecha tenga zona horaria local para evitar problemas de conversión
        const year = ahora.getFullYear();
        const month = String(ahora.getMonth() + 1).padStart(2, '0');
        const day = String(ahora.getDate()).padStart(2, '0');
        const hours = String(ahora.getHours()).padStart(2, '0');
        const minutes = String(ahora.getMinutes()).padStart(2, '0');
        const fechaFormateada = `${year}-${month}-${day}T${hours}:${minutes}`;
        fechaIncidencia.value = fechaFormateada;
        console.log('Fecha de incidencia inicializada:', fechaFormateada);
    }
    
    // Función para formatear una fecha para la base de datos
    function formatearFechaParaDB(fechaStr) {
        try {
            const fecha = new Date(fechaStr);
            // Verificar si la fecha es válida
            if (isNaN(fecha.getTime())) {
                console.error('Fecha inválida:', fechaStr);
                return null;
            }
            
            // Formatear como YYYY-MM-DD HH:MM:SS
            const year = fecha.getFullYear();
            const month = String(fecha.getMonth() + 1).padStart(2, '0');
            const day = String(fecha.getDate()).padStart(2, '0');
            const hours = String(fecha.getHours()).padStart(2, '0');
            const minutes = String(fecha.getMinutes()).padStart(2, '0');
            const seconds = String(fecha.getSeconds()).padStart(2, '0');
            
            return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
        } catch (e) {
            console.error('Error al formatear fecha:', e);
            return null;
        }
    }
    
    // Función para abrir el modal de incidencia
    window.abrirModalIncidencia = function(trampaId, idTrampa, ubicacion) {
        console.log('Abriendo modal para trampa:', trampaId, idTrampa, ubicacion);
        
        // Asignar valores al formulario
        document.getElementById('trampa_id').value = trampaId;
        document.getElementById('trampaDbIdDisplay').textContent = idTrampa || 'Sin ID';
        document.getElementById('trampaZonaDisplay').textContent = ubicacion;
        
        // Mostrar el modal centrado
        const modal = document.getElementById('modalIncidencia');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            console.log('Modal abierto correctamente');
        }
    };
    
    // Función para cerrar el modal de incidencia
    window.cerrarModalIncidencia = function() {
        console.log('Cerrando modal de incidencia');
        
        // Ocultar el modal
        const modal = document.getElementById('modalIncidencia');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
        }
        
        // Resetear el formulario
        const form = document.getElementById('formIncidencia');
        if (form) {
            form.reset();
        }
        
        // Limpiar el ID personalizado
        window.trampaIdPersonalizado = null;
        
        // Ocultar el campo de tipo personalizado
        const container = document.getElementById('tipo_plaga_personalizado_container');
        if (container) {
            container.style.display = 'none';
        }
    };
    
    // Manejar cambio en el tipo de plaga
    if (tipoPlagaSelect) {
        tipoPlagaSelect.addEventListener('change', function() {
            if (this.value === 'otro') {
                tipoPlagaPersonalizadoContainer.style.display = 'block';
                tipoPlaga.value = '';
            } else {
                tipoPlagaPersonalizadoContainer.style.display = 'none';
                tipoPlaga.value = this.value;
            }
        });
    }
    
    // Manejar cambio en el tipo de plaga personalizado
    if (tipoPlagaPersonalizado) {
        tipoPlagaPersonalizado.addEventListener('input', function() {
            tipoPlaga.value = this.value;
        });
    }
    
    // Reemplazo del submit handler para guardar incidencia (copia de viewplano)
    if (formIncidencia) {
        formIncidencia.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('plano_id', '<?= $planoId ?>');
            
            // Agregar el ID de trampa personalizado si existe
            if (window.trampaIdPersonalizado) {
                formData.set('trampa_codigo', window.trampaIdPersonalizado);
            }
            if (!formData.get('tipo_plaga')) {
                alert('Debe seleccionar un tipo de plaga');
                return;
            }
            fetch('<?= base_url('registro_tecnico/guardar_incidencia') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Incidencia guardada correctamente');
                    cerrarModalIncidencia();
                } else {
                    alert('Error: ' + (data.message || 'No se pudo guardar la incidencia'));
                }
            })
            .catch(error => {
                alert('Error al guardar incidencia: ' + error.message);
            });
        });
    }
    
    // Función para filtrar trampas
    function filtrarTrampas() {
        const tipo = filtroTipo.value;
        const ubicacion = filtroUbicacion.value;
        
        // Obtener todas las filas de trampas
        const filas = trampasBody.querySelectorAll('tr');
        
        // Si no hay filas, no hay nada que filtrar
        if (filas.length === 0 || (filas.length === 1 && filas[0].cells.length === 1 && filas[0].cells[0].getAttribute('colspan'))) {
            return;
        }
        
        // Por cada fila, verificar si cumple con los filtros
        filas.forEach(fila => {
            const celdaTipo = fila.cells[2].textContent.trim();
            const celdaUbicacion = fila.cells[3].textContent.trim();
            
            const mostrarPorTipo = !tipo || celdaTipo === tipo;
            const mostrarPorUbicacion = !ubicacion || celdaUbicacion === ubicacion;
            
            // Mostrar u ocultar la fila según los filtros
            if (mostrarPorTipo && mostrarPorUbicacion) {
                fila.style.display = '';
            } else {
                fila.style.display = 'none';
            }
        });
        
        // Verificar si todas las filas están ocultas
        let todasOcultas = true;
        filas.forEach(fila => {
            if (fila.style.display !== 'none') {
                todasOcultas = false;
            }
        });
        
        // Mostrar un mensaje si todas las filas están ocultas
        if (todasOcultas) {
            const filaNoHayResultados = document.createElement('tr');
            filaNoHayResultados.id = 'fila-no-resultados';
            filaNoHayResultados.innerHTML = `
                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                    No hay resultados que coincidan con los filtros
                </td>
            `;
            trampasBody.appendChild(filaNoHayResultados);
        } else {
            const filaNoHayResultados = document.getElementById('fila-no-resultados');
            if (filaNoHayResultados) {
                filaNoHayResultados.remove();
            }
        }
    }
    
    // Eventos para los filtros
    if (filtroTipo) {
        filtroTipo.addEventListener('change', filtrarTrampas);
    }
    
    if (filtroUbicacion) {
        filtroUbicacion.addEventListener('change', filtrarTrampas);
    }
    
    // Botón para limpiar filtros
    if (btnLimpiarFiltros) {
        btnLimpiarFiltros.addEventListener('click', function() {
            filtroTipo.value = '';
            filtroUbicacion.value = '';
            filtrarTrampas();
        });
    }
    
    // Función para ver detalle de trampa
    window.verDetalleTrampa = function(trampaId) {
        abrirModalHistorial(trampaId);
    };

    // Funciones para el modal de historial
    window.abrirModalHistorial = function(trampaId) {
        const modal = document.getElementById('modalHistorialTrampa');
        const loading = document.getElementById('historialLoading');
        const content = document.getElementById('historialContent');
        
        // Mostrar modal y loading
        modal.classList.remove('hidden');
        modal.classList.add('show');
        loading.classList.remove('hidden');
        content.classList.add('hidden');
        
        // Cargar datos del historial
        fetch(`<?= base_url('registro_tecnico/historial_trampa/') ?>${trampaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarHistorialTrampa(data.data);
                    loading.classList.add('hidden');
                    content.classList.remove('hidden');
                } else {
                    alert('Error al cargar el historial: ' + data.message);
                    cerrarModalHistorial();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar el historial de la trampa');
                cerrarModalHistorial();
            });
    };

    window.cerrarModalHistorial = function() {
        const modal = document.getElementById('modalHistorialTrampa');
        modal.classList.add('hidden');
        modal.classList.remove('show');
    };

    window.cambiarTab = function(tab) {
        // Ocultar todos los contenidos
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Desactivar todos los botones
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Mostrar contenido seleccionado
        document.getElementById(`content${tab.charAt(0).toUpperCase() + tab.slice(1)}`).classList.remove('hidden');
        
        // Activar botón seleccionado
        const activeButton = document.getElementById(`tab${tab.charAt(0).toUpperCase() + tab.slice(1)}`);
        activeButton.classList.remove('border-transparent', 'text-gray-500');
        activeButton.classList.add('border-blue-500', 'text-blue-600');
    };

    function mostrarHistorialTrampa(data) {
        const { trampa, incidencias, movimientos, estadisticas, tipos_plagas } = data;
        
        // Actualizar información del header
        document.getElementById('trampaHeaderInfo').textContent = 
            `ID: ${trampa.id} | Código: ${trampa.id_trampa || 'N/A'} | Tipo: ${trampa.tipo} | Ubicación: ${trampa.ubicacion}`;
        
        // Actualizar estadísticas del resumen
        document.getElementById('totalIncidencias').textContent = estadisticas.total_incidencias || 0;
        document.getElementById('totalOrganismos').textContent = estadisticas.total_organismos || 0;
        document.getElementById('tiposPlagaDiferentes').textContent = estadisticas.tipos_plaga_diferentes || 0;
        document.getElementById('ultimaIncidencia').textContent = 
            estadisticas.ultima_incidencia ? new Date(estadisticas.ultima_incidencia).toLocaleDateString() : 'N/A';
        
        // Llenar tabla de incidencias
        const tablaIncidencias = document.getElementById('tablaIncidencias');
        tablaIncidencias.innerHTML = '';
        
        if (incidencias.length === 0) {
            tablaIncidencias.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No hay incidencias registradas</td></tr>';
        } else {
            incidencias.forEach(incidencia => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${incidencia.fecha_formateada}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${incidencia.tipo_plaga || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${incidencia.tipo_incidencia || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${incidencia.cantidad_organismos || 0}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${incidencia.inspector || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${incidencia.notas || '-'}</td>
                `;
                tablaIncidencias.appendChild(row);
            });
        }
        
        // Llenar tabla de movimientos
        const tablaMovimientos = document.getElementById('tablaMovimientos');
        tablaMovimientos.innerHTML = '';
        
        if (movimientos.length === 0) {
            tablaMovimientos.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No hay movimientos registrados</td></tr>';
        } else {
            movimientos.forEach(movimiento => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${movimiento.fecha_formateada}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${movimiento.tipo || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${movimiento.zona_anterior || 'N/A'}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${movimiento.zona_nueva || 'N/A'}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">${movimiento.comentario || '-'}</td>
                `;
                tablaMovimientos.appendChild(row);
            });
        }
        
        // Llenar estadísticas por tipo de plaga
        const listaTiposPlagas = document.getElementById('listaTiposPlagas');
        listaTiposPlagas.innerHTML = '';
        
        if (tipos_plagas.length === 0) {
            listaTiposPlagas.innerHTML = '<p class="text-gray-500">No hay tipos de plaga registrados</p>';
        } else {
            tipos_plagas.forEach(plaga => {
                const item = document.createElement('div');
                item.className = 'flex justify-between items-center py-2 border-b border-gray-100 last:border-b-0';
                item.innerHTML = `
                    <span class="font-medium">${plaga.tipo_plaga}</span>
                    <div class="text-right">
                        <div class="text-sm font-medium">${plaga.total_organismos} organismos</div>
                        <div class="text-xs text-gray-500">${plaga.frecuencia} incidencia(s)</div>
                    </div>
                `;
                listaTiposPlagas.appendChild(item);
            });
        }
        
        // Información adicional
        const infoAdicional = document.getElementById('infoAdicional');
        infoAdicional.innerHTML = `
            <div class="space-y-2">
                <p><strong>Fecha de instalación:</strong> ${trampa.fecha_instalacion ? new Date(trampa.fecha_instalacion).toLocaleDateString() : 'No registrada'}</p>
                <p><strong>Estado:</strong> ${trampa.activa == 1 ? 'Activa' : 'Inactiva'}</p>
                <p><strong>Coordenadas:</strong> X: ${trampa.coordenada_x || 'N/A'}, Y: ${trampa.coordenada_y || 'N/A'}</p>
                <p><strong>Última actualización:</strong> ${trampa.updated_at ? new Date(trampa.updated_at).toLocaleDateString() : 'No registrada'}</p>
            </div>
        `;
        
        // Activar primer tab por defecto
        cambiarTab('incidencias');
    }

    // Agregar soporte para cerrar modal con tecla Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('modalHistorialTrampa');
            if (!modal.classList.contains('hidden')) {
                cerrarModalHistorial();
            }
        }
    });

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
    };

    window.cerrarModalEditarId = function() {
        document.getElementById('modalEditarId').classList.add('hidden');
        document.getElementById('nuevoIdTrampa').value = '';
    };

    window.guardarNuevoId = function() {
        const nuevoId = document.getElementById('nuevoIdTrampa').value.trim();
        
        if (!nuevoId) {
            alert('Por favor ingrese un ID válido');
            return;
        }
        
        // Preparar datos para enviar al servidor
        const formData = new FormData();
        formData.append('trampa_id_actual', window.trampaIdPersonalizado || 'TEMP-' + Date.now());
        formData.append('nuevo_id_trampa', nuevoId);
        formData.append('plano_id', '<?= $planoId ?>');
        
        // Enviar al servidor
        fetch('<?= base_url('registro_tecnico/actualizar_id_trampa') ?>', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Actualizar el ID mostrado en el modal de incidencia
                document.getElementById('trampaDbIdDisplay').textContent = nuevoId;
                
                // Guardar el ID para enviarlo con el formulario
                window.trampaIdPersonalizado = nuevoId;
                
                // Cerrar el modal
                cerrarModalEditarId();
                
                // Mostrar mensaje de confirmación
                alert(`ID de trampa actualizado correctamente a: ${nuevoId}`);
            } else {
                alert(`Error: ${data.message}`);
            }
        })
        .catch(error => {
            console.error('Error al actualizar ID de trampa:', error);
            alert('Error al comunicarse con el servidor');
        });
    };

    // Agregar soporte para cerrar modal con Escape y Enter para guardar
    document.addEventListener('keydown', function(event) {
        const modalEditarId = document.getElementById('modalEditarId');
        if (modalEditarId && !modalEditarId.classList.contains('hidden')) {
            if (event.key === 'Escape') {
                cerrarModalEditarId();
            } else if (event.key === 'Enter') {
                event.preventDefault();
                guardarNuevoId();
            }
        }
    });
});

// Lógica de lista temporal y guardado masivo
let incidenciasTemp = [];

function renderizarListaIncidencias() {
    const ul = document.getElementById('incidenciasAgregadas');
    const contador = document.getElementById('contadorIncidencias');
    ul.innerHTML = '';
    
    if (incidenciasTemp.length === 0) {
        ul.innerHTML = '<li class="text-gray-400 italic">No hay incidencias agregadas.</li>';
        contador.textContent = '0';
        return;
    }
    
    contador.textContent = incidenciasTemp.length;
    
    incidenciasTemp.forEach((inc, idx) => {
        const li = document.createElement('li');
        li.className = 'flex items-center justify-between bg-gray-50 rounded px-3 py-2 hover:bg-gray-100 transition-colors';
        
        const fecha = new Date(inc.fecha_incidencia).toLocaleString();
        const tipoPlaga = inc.tipo_plaga || inc.tipo_plaga_select;
        
        li.innerHTML = `
            <div class="flex-1">
                <div class="flex items-center">
                    <span class="font-medium text-gray-900">${tipoPlaga}</span>
                    <span class="mx-2 text-gray-400">•</span>
                    <span class="text-sm text-gray-600">${inc.tipo_incidencia}</span>
                </div>
                <div class="text-sm text-gray-500">
                    ${fecha} - ${inc.inspector || 'Sin inspector'}
                </div>
            </div>
            <button type="button" 
                    class="ml-2 text-red-600 hover:text-red-800 p-1 rounded-full hover:bg-red-100 transition-colors" 
                    onclick="eliminarIncidenciaDeLista(${idx})"
                    title="Eliminar incidencia">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
        `;
        ul.appendChild(li);
    });
}

window.eliminarIncidenciaDeLista = function(idx) {
    incidenciasTemp.splice(idx, 1);
    renderizarListaIncidencias();
};

document.getElementById('btnAgregarIncidenciaLista').addEventListener('click', function() {
    const form = document.getElementById('formIncidencia');
    const formData = new FormData(form);
    
    if (!formData.get('tipo_plaga') && !formData.get('tipo_plaga_select')) {
        alert('Debe seleccionar un tipo de plaga');
        return;
    }
    if (!formData.get('fecha_incidencia')) {
        alert('Debe ingresar la fecha de incidencia');
        return;
    }
    
    const obj = {};
    formData.forEach((v, k) => obj[k] = v);
    incidenciasTemp.push(obj);
    renderizarListaIncidencias();
    
    // Limpiar el formulario
    form.reset();
    document.getElementById('tipo_plaga_personalizado_container').style.display = 'none';
    document.getElementById('cantidad_organismos_container').style.display = 'none';
    document.getElementById('tipo_plaga_personalizado').removeAttribute('required');
    document.getElementById('cantidad_organismos').removeAttribute('required');
    
    // Restaurar la fecha actual
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById('fecha_incidencia').value = `${year}-${month}-${day}T${hours}:${minutes}`;
});

document.getElementById('btnGuardarTodasIncidencias').addEventListener('click', function() {
    if (incidenciasTemp.length === 0) {
        alert('No hay incidencias para guardar');
        return;
    }
    
    let guardadas = 0;
    let errores = 0;
    let mensajesError = [];
    
    incidenciasTemp.forEach((inc, idx) => {
        const formData = new FormData();
        formData.append('trampa_id', inc.trampa_id);
        formData.append('tipo_plaga', inc.tipo_plaga || inc.tipo_plaga_select);
        formData.append('tipo_incidencia', inc.tipo_incidencia);
        formData.append('tipo_insecto', inc.tipo_insecto);
        formData.append('cantidad_organismos', inc.cantidad_organismos || '');
        formData.append('notas', inc.notas || '');
        formData.append('inspector', inc.inspector || '');
        // Formatear fecha para MySQL
        let fecha = inc.fecha_incidencia;
        if (fecha && fecha.length === 16) { // formato yyyy-MM-ddTHH:mm
            fecha = fecha.replace('T', ' ') + ':00';
        }
        formData.append('fecha_incidencia', fecha);
        formData.append('plano_id', '<?= $planoId ?>');
        fetch('<?= base_url('registro_tecnico/guardar_incidencia') ?>', {
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
                mensajesError.push(data.message || 'Error desconocido');
            }
            
            if (guardadas + errores === incidenciasTemp.length) {
                let msg = `${guardadas} incidencias guardadas, ${errores} errores`;
                if (errores > 0) {
                    msg += '\n' + mensajesError.join('\n');
                }
                alert(msg);
                incidenciasTemp = [];
                renderizarListaIncidencias();
                cerrarModalIncidencia();
            }
        })
        .catch((e) => {
            errores++;
            mensajesError.push('Error de red o servidor');
            if (guardadas + errores === incidenciasTemp.length) {
                let msg = `${guardadas} incidencias guardadas, ${errores} errores`;
                if (errores > 0) {
                    msg += '\n' + mensajesError.join('\n');
                }
                alert(msg);
                incidenciasTemp = [];
                renderizarListaIncidencias();
                cerrarModalIncidencia();
            }
        });
    });
});

// Mostrar/ocultar campos personalizados
const tipoPlagaSelect = document.getElementById('tipo_plaga_select');
const tipoPlagaPersonalizadoContainer = document.getElementById('tipo_plaga_personalizado_container');
const tipoPlagaPersonalizado = document.getElementById('tipo_plaga_personalizado');
const tipoPlaga = document.getElementById('tipo_plaga');
const cantidadOrganismosContainer = document.getElementById('cantidad_organismos_container');
const cantidadOrganismos = document.getElementById('cantidad_organismos');

if (tipoPlagaSelect) {
    tipoPlagaSelect.addEventListener('change', function() {
        tipoPlaga.value = this.value;
        if (this.value === 'otro') {
            tipoPlagaPersonalizadoContainer.style.display = 'block';
            tipoPlagaPersonalizado.setAttribute('required', 'required');
            tipoPlaga.value = '';
        } else {
            tipoPlagaPersonalizadoContainer.style.display = 'none';
            tipoPlagaPersonalizado.removeAttribute('required');
        }
        if (this.value) {
            cantidadOrganismosContainer.style.display = 'block';
            cantidadOrganismos.setAttribute('required', 'required');
        } else {
            cantidadOrganismosContainer.style.display = 'none';
            cantidadOrganismos.removeAttribute('required');
        }
    });
}

if (tipoPlagaPersonalizado) {
    tipoPlagaPersonalizado.addEventListener('input', function() {
        tipoPlaga.value = this.value;
    });
}

// Inicializar la lista de incidencias
renderizarListaIncidencias();
</script>
<?= $this->endSection() ?> 