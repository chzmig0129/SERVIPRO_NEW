<?= $this->extend('layouts/main') ?>

<?= $this->section('styles') ?>
<style>
/* Estilos básicos y necesarios, eliminando cualquier cosa que pueda interferir */
table.min-w-full td {
    vertical-align: middle;
}

/* Estilos específicos para el botón de acción */
.btn-accion-queja {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-weight: 500;
    text-align: center;
    color: white;
    background-color: #059669;
    cursor: pointer;
    text-decoration: none;
    border: none;
    width: 100%;
}

.btn-accion-queja:hover {
    background-color: #047857;
}

.btn-accion-queja.btn-pendiente {
    background-color: #d97706;
}

.btn-accion-queja.btn-pendiente:hover {
    background-color: #b45309;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Registro Técnico</h1>
    
    <div class="bg-white rounded-lg shadow-md p-6">
        <!-- Selector de Sede -->
        <div class="mb-6">
            <label for="sede" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Planta</label>
            <select id="sede" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                <option value="">Seleccione una planta</option>
                <?php foreach ($sedes as $sede): ?>
                    <option value="<?= $sede['id'] ?>" <?= ($sedeSeleccionada == $sede['id']) ? 'selected' : '' ?>><?= esc($sede['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <!-- Sección de Quejas Pendientes -->
        <?php if (!empty($quejasPendientes)): ?>
        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-amber-700 mb-3 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
                Quejas Pendientes (<?= count($quejasPendientes) ?>)
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-amber-200 border border-amber-200 rounded-lg">
                    <thead class="bg-amber-100">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-amber-800 uppercase tracking-wider">Fecha</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-amber-800 uppercase tracking-wider">Insecto</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-amber-800 uppercase tracking-wider">Ubicación</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-amber-800 uppercase tracking-wider">Líneas</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-amber-800 uppercase tracking-wider">Clasificación</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-amber-800 uppercase tracking-wider">Estado</th>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-amber-800 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-amber-100">
                        <?php foreach ($quejasPendientes as $queja): ?>
                        <tr class="hover:bg-amber-50 transition-colors" data-queja-id="<?= $queja['id'] ?>" data-estado-queja="<?= $queja['estado_queja'] ?>">
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">
                                <?= date('d/m/Y', strtotime($queja['fecha'])) ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">
                                <?= esc($queja['insecto']) ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">
                                <?= esc($queja['ubicacion']) ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700">
                                <?= esc($queja['lineas']) ?>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php 
                                    switch ($queja['clasificacion']) {
                                        case 'Crítico': echo 'bg-red-100 text-red-800'; break;
                                        case 'Alto': echo 'bg-orange-100 text-orange-800'; break;
                                        case 'Medio': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'Bajo': echo 'bg-green-100 text-green-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?= esc($queja['clasificacion']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?= ($queja['estado'] == 'Vivo') ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' ?>">
                                    <?= esc($queja['estado']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 whitespace-nowrap text-sm">
                                <button type="button" 
                                        onclick="cambiarEstadoQueja(<?= $queja['id'] ?>, 'Resuelta');" 
                                        class="btn-accion-queja">
                                    Marcar Resuelta
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="mt-3 text-right">
                <a href="<?= base_url('quejas') ?>" class="text-amber-600 hover:text-amber-800 text-sm font-medium">
                    Ver todas las quejas →
                </a>
            </div>
        </div>
        <?php endif; ?>

        <!-- Grid de Planos -->
        <div id="planos-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (!empty($planos)): ?>
                <?php foreach ($planos as $plano): ?>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition-all">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2"><?= esc($plano['nombre']) ?></h3>
                        <?php if (!empty($plano['descripcion'])): ?>
                            <p class="text-sm text-gray-600 mb-3"><?= esc($plano['descripcion']) ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($plano['preview_image'])): ?>
                            <div class="mb-3 border border-gray-200 rounded-lg overflow-hidden bg-white">
                                <img src="<?= $plano['preview_image'] ?>" alt="<?= esc($plano['nombre']) ?>" class="w-full h-32 object-contain">
                            </div>
                        <?php endif; ?>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                <?= date('d/m/Y', strtotime($plano['fecha_creacion'])) ?>
                            </span>
                            <a href="<?= base_url('registro_tecnico/ver_trampas/' . $plano['id']) ?>" 
                               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-sm">
                                Ver Trampas
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-8">
                    <p class="text-gray-500">No hay planos disponibles para esta sede</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tabla de Trampas -->
        <div id="trampas-container" class="hidden mt-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-semibold text-gray-800" id="trampas-titulo">Trampas del Plano</h2>
                <div class="flex space-x-2">
                    <button id="btn-exportar-excel" class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors text-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Exportar a Excel
                    </button>
                    <button id="btn-imprimir" class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Imprimir
                    </button>
                </div>
            </div>
            
            <!-- Filtros para la tabla -->
            <div class="bg-gray-50 p-4 rounded-lg mb-4 border border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="filtro-tipo" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por tipo</label>
                        <select id="filtro-tipo" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todos los tipos</option>
                        </select>
                    </div>
                    <div>
                        <label for="filtro-ubicacion" class="block text-sm font-medium text-gray-700 mb-1">Filtrar por ubicación</label>
                        <select id="filtro-ubicacion" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Todas las ubicaciones</option>
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
                        <!-- Las trampas se cargarán aquí dinámicamente -->
                    </tbody>
                </table>
                
                <!-- Mensaje cuando no hay trampas -->
                <div id="no-trampas" class="hidden text-center py-8">
                    <p class="text-gray-500">No se encontraron trampas para este plano</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Registrar Incidencia (idéntico a viewplano.php) -->
<div id="modalIncidencia" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
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
                        <button type="button" onclick="closeIncidenciaModal()"
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Función para cambiar el estado de una queja
    window.cambiarEstadoQueja = function(quejaId, nuevoEstado) {
        console.log('Cambiando estado de queja:', quejaId, 'a', nuevoEstado);
        
        // Obtener el botón que fue clicado
        const boton = event.target;
        const textoOriginal = boton.textContent.trim();
        
        // Cambiar el texto del botón para indicar que está procesando
        boton.textContent = 'Procesando...';
        boton.style.backgroundColor = '#888';
        boton.disabled = true;
        
        // Enviar solicitud AJAX para cambiar el estado
        const formData = new FormData();
        formData.append('queja_id', quejaId);
        formData.append('estado_queja', nuevoEstado);
        
        fetch('<?= base_url('registro_tecnico/actualizarEstadoQueja') ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const fila = boton.closest('tr');
                
                // Mostrar mensaje de éxito
                alert(data.message);
                
                // Aplicar efecto visual y luego eliminar la fila
                fila.style.backgroundColor = '#d1fae5'; // bg-green-100
                fila.style.transition = 'opacity 0.5s ease, height 0.5s ease, padding 0.5s ease';
                setTimeout(() => {
                    fila.style.opacity = '0';
                    fila.style.height = '0';
                    fila.style.padding = '0';
                    setTimeout(() => {
                        fila.remove();
                        
                        // Actualizar el contador
                        const filasPendientes = document.querySelectorAll('tbody tr').length;
                        const contadorQuejas = document.querySelector('h3.text-lg.font-semibold.text-amber-700');
                        
                        if (contadorQuejas) {
                            const textoActual = contadorQuejas.textContent;
                            const nuevoTexto = textoActual.replace(/\(\d+\)/, `(${filasPendientes})`);
                            contadorQuejas.textContent = nuevoTexto;
                        }
                        
                        // Si no quedan quejas pendientes, ocultar toda la sección
                        if (filasPendientes === 0) {
                            const seccionQuejas = document.querySelector('.mb-6.bg-yellow-50');
                            if (seccionQuejas) {
                                seccionQuejas.style.display = 'none';
                            }
                        }
                    }, 500);
                }, 1000);
            } else {
                // Restablecer el botón y mostrar error
                boton.textContent = textoOriginal;
                boton.style.backgroundColor = '#059669';
                boton.disabled = false;
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            boton.textContent = textoOriginal;
            boton.style.backgroundColor = '#059669';
            boton.disabled = false;
            alert('Error al procesar la solicitud. Por favor, inténtelo de nuevo.');
        });
    };
    
    // Código de depuración para verificar los botones
    console.log('Verificando elementos de cambio de estado...');
    
    const sedeSelect = document.getElementById('sede');
    const planosGrid = document.getElementById('planos-grid');
    const trampasContainer = document.getElementById('trampas-container');
    const trampasBody = document.getElementById('trampas-body');
    const noTrampas = document.getElementById('no-trampas');
    const trampasTitulo = document.getElementById('trampas-titulo');
    const filtroTipo = document.getElementById('filtro-tipo');
    const filtroUbicacion = document.getElementById('filtro-ubicacion');
    const btnLimpiarFiltros = document.getElementById('btn-limpiar-filtros');
    const btnExportarExcel = document.getElementById('btn-exportar-excel');
    const btnImprimir = document.getElementById('btn-imprimir');
    
    let trampasData = []; // Almacena todas las trampas sin filtrar
    let planoActual = null; // ID del plano actual
    
    // Cuando se selecciona una sede
    sedeSelect.addEventListener('change', function() {
        const sedeId = this.value;
        if (sedeId) {
            window.location.href = `<?= base_url('registro_tecnico') ?>?sede_id=${sedeId}`;
        }
    });
    
    // Función para cargar las trampas de un plano
    window.cargarTrampas = function(planoId, planoNombre) {
        planoActual = planoId;
        trampasTitulo.textContent = `Trampas del Plano: ${planoNombre}`;
        
        // Mostrar un indicador de carga
        trampasBody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center">Cargando trampas...</td></tr>';
        trampasContainer.classList.remove('hidden');
        noTrampas.classList.add('hidden');
        
        // Limpiar filtros
        filtroTipo.innerHTML = '<option value="">Todos los tipos</option>';
        filtroUbicacion.innerHTML = '<option value="">Todas las ubicaciones</option>';
        
        fetch(`/registro_tecnico/trampas/${planoId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error de red: ${response.status}`);
                }
                return response.json();
            })
            .then(result => {
                console.log('Respuesta del servidor:', result);
                trampasBody.innerHTML = '';
                
                if (result.success && result.data && result.data.length > 0) {
                    trampasData = result.data;
                    
                    // Llenar los filtros con datos únicos
                    const tipos = new Set();
                    const ubicaciones = new Set();
                    
                    trampasData.forEach(trampa => {
                        if (trampa.tipo) tipos.add(trampa.tipo);
                        if (trampa.ubicacion) ubicaciones.add(trampa.ubicacion);
                    });
                    
                    // Agregar opciones al filtro de tipo
                    tipos.forEach(tipo => {
                        const option = document.createElement('option');
                        option.value = tipo;
                        option.textContent = tipo;
                        filtroTipo.appendChild(option);
                    });
                    
                    // Agregar opciones al filtro de ubicación
                    ubicaciones.forEach(ubicacion => {
                        const option = document.createElement('option');
                        option.value = ubicacion;
                        option.textContent = ubicacion;
                        filtroUbicacion.appendChild(option);
                    });
                    
                    // Mostrar las trampas en la tabla
                    mostrarTrampas(trampasData);
                } else {
                    let mensaje = 'No se encontraron trampas para este plano';
                    if (result.message) {
                        mensaje = result.message;
                    }
                    noTrampas.classList.remove('hidden');
                    noTrampas.querySelector('p').textContent = mensaje;
                    trampasBody.innerHTML = '';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                trampasBody.innerHTML = `<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">Error al cargar las trampas: ${error.message}</td></tr>`;
            });
    };
    
    // Función para mostrar las trampas filtradas en la tabla
    function mostrarTrampas(trampas) {
        if (trampas.length === 0) {
            noTrampas.classList.remove('hidden');
            trampasBody.innerHTML = '';
            return;
        }
        
        noTrampas.classList.add('hidden');
        trampasBody.innerHTML = '';
        
        trampas.forEach(trampa => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 transition-colors';
            row.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${trampa.id}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${trampa.id_trampa || '-'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${trampa.tipo || 'No especificado'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${trampa.ubicacion || 'No especificado'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${trampa.fecha_instalacion ? new Date(trampa.fecha_instalacion).toLocaleDateString() : 'No especificado'}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <button class="text-blue-600 hover:text-blue-800" onclick="verDetalleTrampa(${trampa.id})">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                    <button class="text-green-600 hover:text-green-800 ml-2 rounded-full p-1 hover:bg-green-100" onclick="abrirModalIncidencia('${trampa.id}', '${trampa.id_trampa ? trampa.id_trampa.replace(/'/g, "\\'") : '-'}', '${trampa.ubicacion ? trampa.ubicacion.replace(/'/g, "\\'") : 'No especificado'}')">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </td>
            `;
            trampasBody.appendChild(row);
        });
    }
    
    // Función para ver el detalle de una trampa (a implementar)
    window.verDetalleTrampa = function(trampaId) {
        alert('Ver detalle de la trampa ' + trampaId);
        // Aquí implementarías la lógica para mostrar el detalle
    };
    
    // Filtrar trampas cuando cambian los filtros
    filtroTipo.addEventListener('change', aplicarFiltros);
    filtroUbicacion.addEventListener('change', aplicarFiltros);
    
    // Limpiar filtros
    btnLimpiarFiltros.addEventListener('click', function() {
        filtroTipo.value = '';
        filtroUbicacion.value = '';
        aplicarFiltros();
    });
    
    // Función para aplicar filtros
    function aplicarFiltros() {
        const tipo = filtroTipo.value;
        const ubicacion = filtroUbicacion.value;
        
        const trampasFiltradas = trampasData.filter(trampa => {
            const matchTipo = !tipo || trampa.tipo === tipo;
            const matchUbicacion = !ubicacion || trampa.ubicacion === ubicacion;
            
            return matchTipo && matchUbicacion;
        });
        
        mostrarTrampas(trampasFiltradas);
    }
    
    // Exportar a Excel (simulado)
    btnExportarExcel.addEventListener('click', function() {
        if (!planoActual) return;
        alert('Función de exportar a Excel - Plano ID: ' + planoActual);
        // Aquí implementarías la lógica para exportar a Excel
    });
    
    // Imprimir (simulado)
    btnImprimir.addEventListener('click', function() {
        if (!planoActual) return;
        alert('Función de imprimir - Plano ID: ' + planoActual);
        // Aquí implementarías la lógica para imprimir
    });
});

// Función global para abrir el modal desde el botón +
window.abrirModalIncidencia = function(trampaId, codigo, ubicacion) {
    document.getElementById('trampa_id').value = trampaId;
    document.getElementById('trampaDbIdDisplay').textContent = codigo || 'Sin ID';
    document.getElementById('trampaZonaDisplay').textContent = ubicacion;
    document.getElementById('modalIncidencia').classList.remove('hidden');
    // Set fecha actual por defecto
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, '0');
    const day = String(now.getDate()).padStart(2, '0');
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    document.getElementById('fecha_incidencia').value = `${year}-${month}-${day}T${hours}:${minutes}`;
};
window.closeIncidenciaModal = function() {
    document.getElementById('modalIncidencia').classList.add('hidden');
    document.getElementById('formIncidencia').reset();
    
    document.getElementById('tipo_plaga_personalizado_container').style.display = 'none';
    document.getElementById('cantidad_organismos_container').style.display = 'none';
    document.getElementById('tipo_plaga_personalizado').removeAttribute('required');
    document.getElementById('cantidad_organismos').removeAttribute('required');
    incidenciasTemp = [];
    renderizarListaIncidencias();
};

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
    
    // Preparar datos para enviar al servidor
    const formData = new FormData();
    formData.append('trampa_id_actual', window.trampaIdPersonalizado || 'TEMP-' + Date.now());
    formData.append('nuevo_id_trampa', nuevoId);
    
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
}

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
    form.reset();
    document.getElementById('tipo_plaga_personalizado_container').style.display = 'none';
    document.getElementById('cantidad_organismos_container').style.display = 'none';
    document.getElementById('tipo_plaga_personalizado').removeAttribute('required');
    document.getElementById('cantidad_organismos').removeAttribute('required');
});
document.getElementById('btnGuardarTodasIncidencias').addEventListener('click', function() {
    if (incidenciasTemp.length === 0) {
        alert('No hay incidencias para guardar');
        return;
    }
    let guardadas = 0;
    let errores = 0;
    incidenciasTemp.forEach((inc, idx) => {
        const formData = new FormData();
        Object.entries(inc).forEach(([k, v]) => formData.append(k, v));
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
            }
            if (guardadas + errores === incidenciasTemp.length) {
                alert(`${guardadas} incidencias guardadas, ${errores} errores`);
                incidenciasTemp = [];
                renderizarListaIncidencias();
                closeIncidenciaModal();
            }
        })
        .catch(() => {
            errores++;
            if (guardadas + errores === incidenciasTemp.length) {
                alert(`${guardadas} incidencias guardadas, ${errores} errores`);
                incidenciasTemp = [];
                renderizarListaIncidencias();
                closeIncidenciaModal();
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
renderizarListaIncidencias();
</script>
<!-- Fin modal incidencia -->
<?= $this->endSection() ?> 