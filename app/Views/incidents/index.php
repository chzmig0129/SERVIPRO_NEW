<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl p-6 text-white shadow-lg text-center mb-6">
    <div class="flex flex-col items-center justify-center">
        <h1 class="text-3xl font-bold text-white mb-2">Evidencias</h1>
        <p class="text-blue-100 mb-4">Registro y seguimiento de evidencias</p>
        
        <button id="registrar-evidencia-btn" class="bg-white hover:bg-blue-50 text-blue-700 font-medium py-2.5 px-5 rounded-lg flex items-center gap-2 transition-colors duration-200 shadow-md">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            <span>Registrar Evidencia</span>
        </button>
    </div>
</div>

<!-- Dashboard con filtros -->
<div class="bg-gray-50 rounded-lg p-5 mb-8 border border-gray-200">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Selector de Sede -->
        <div>
            <label for="location-selector" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Sede</label>
            <div class="relative">
                <select id="location-selector" class="w-full rounded-lg border-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 pl-4 pr-10 py-2.5 appearance-none bg-white text-blue-700 font-medium">
                    <option value="">Todas las sedes</option>
                    <?php foreach ($sedes as $sede): ?>
                        <option value="<?= $sede['id'] ?>"><?= esc($sede['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                    <svg class="w-5 h-5 text-blue-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>
        
        <!-- Selector de Plano -->
        <div>
            <label for="blueprint-selector" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Plano</label>
            <div class="relative">
                <select id="blueprint-selector" class="w-full rounded-lg border-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 pl-4 pr-10 py-2.5 appearance-none bg-white text-blue-700 font-medium" disabled>
                    <option value="">Seleccione una sede primero</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                    <svg class="w-5 h-5 text-blue-700" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Vista previa del plano seleccionado -->
    <div id="blueprint-preview" class="hidden mb-8">
        <h2 class="text-xl font-semibold mb-4 text-gray-800">Vista del Plano</h2>
        <div class="bg-gray-100 border border-gray-200 rounded-lg p-4 flex items-center justify-center relative h-[450px]">
            <!-- Contenedor para la imagen y las trampas -->
            <div id="blueprint-container" class="relative w-full h-full flex items-center justify-center overflow-hidden">
                <img id="blueprint-image" src="/placeholder.svg" alt="Plano seleccionado" class="max-h-full max-w-full object-contain hidden">
                
                <!-- Contenedor para las trampas -->
                <div id="traps-container" class="absolute top-0 left-0 w-full h-full pointer-events-none">
                    <!-- Las trampas se agregarán aquí dinámicamente -->
                </div>
                
                <!-- Placeholder cuando no hay imagen -->
                <div id="blueprint-placeholder" class="text-gray-400 flex flex-col items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14.106 5.553a2 2 0 0 0 1.788 0l3.659-1.83A1 1 0 0 1 21 4.619v12.764a1 1 0 0 1-.553.894l-4.553 2.277a2 2 0 0 1-1.788 0l-4.212-2.106a2 2 0 0 0-1.788 0l-3.659 1.83A1 1 0 0 1 3 19.381V6.618a1 1 0 0 1 .553-.894l4.553-2.277a2 2 0 0 1 1.788 0z"/>
                    </svg>
                    <p class="mt-4 text-lg">Seleccione una sede y un plano para visualizar</p>
                </div>
            </div>
            
            <!-- Leyenda de trampas (se mostrará cuando haya trampas) -->
            <div id="traps-legend" class="absolute top-4 right-4 bg-white bg-opacity-90 rounded-lg p-3 shadow-lg hidden border border-gray-200">
                <h3 class="text-sm font-semibold mb-2 text-gray-800">Trampas</h3>
                <ul class="text-xs space-y-2" id="traps-list"></ul>
            </div>
        </div>
    </div>

    <!-- Lista de incidencias por plano o sede -->
    <div id="incidents-container">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-800">Registro de Evidencias</h2>
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"></circle>
                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                </svg>
                <span id="incidents-count">0 registros encontrados</span>
            </div>
        </div>
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sede</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200" id="incidents-list">
                    <!-- Aquí se cargarán los incidentes según los filtros -->
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-3">
                                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                    <line x1="12" y1="9" x2="12" y2="13"></line>
                                    <line x1="12" y1="17" x2="12.01" y2="17"></line>
                                </svg>
                                <p class="text-lg">Seleccione un plano para ver las evidencias</p>
                                <p class="text-sm text-gray-400 mt-1">No se han encontrado registros</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para registrar evidencia -->
<div id="evidence-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 animate-fadeIn">
        <div class="border-b p-4 flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-800">Registrar Evidencia</h3>
            <button id="close-modal" class="text-gray-400 hover:text-gray-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <form id="evidence-form" class="p-6">
            <input type="hidden" id="point-x" name="point-x">
            <input type="hidden" id="point-y" name="point-y">
            
            <div class="mb-5">
                <label for="zone" class="block text-sm font-medium text-gray-700 mb-2">Zona/Ubicación</label>
                <input type="text" id="zone" name="zone" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-2.5" required>
            </div>
            
            <div class="mb-5">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea id="description" name="description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required></textarea>
            </div>
            
            <div class="mb-6">
                <label for="evidence-images" class="block text-sm font-medium text-gray-700 mb-2">Imágenes de Evidencia</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:bg-gray-50 transition-colors cursor-pointer">
                    <input type="file" id="evidence-images" name="evidence-images[]" class="hidden" accept="image/*" multiple>
                    <label for="evidence-images" class="cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 mb-2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <span class="text-sm text-gray-600">Haga clic para seleccionar imágenes</span>
                        <p class="text-xs text-gray-500 mt-1">Puede seleccionar múltiples imágenes</p>
                    </label>
                </div>
                <div id="selected-files" class="mt-2 text-sm text-gray-600"></div>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" id="cancel-evidence" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2.5 px-5 rounded-lg transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-5 rounded-lg transition-colors shadow-md">
                    Guardar Evidencia
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal para ver evidencia -->
<div id="view-evidence-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 animate-fadeIn">
        <div class="border-b p-4 flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-800" id="view-evidence-title">Detalles de la Evidencia</h3>
            <button id="close-view-modal" class="text-gray-400 hover:text-gray-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Ubicación:</h4>
                    <p id="view-evidence-location" class="text-gray-800 bg-gray-50 p-2 rounded-md"></p>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-700 mb-2">Fecha de registro:</h4>
                    <p id="view-evidence-date" class="text-gray-800 bg-gray-50 p-2 rounded-md"></p>
                </div>
            </div>
            <div class="mb-5">
                <h4 class="font-medium text-gray-700 mb-2">Descripción:</h4>
                <p id="view-evidence-description" class="text-gray-800 bg-gray-50 p-3 rounded-md min-h-[60px]"></p>
            </div>
            <div class="mb-4">
                <h4 class="font-medium text-gray-700 mb-3">Estado:</h4>
                <div class="flex items-center gap-2 mb-4">
                    <span id="view-evidence-status" class="px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                        Pendiente
                    </span>
                    <span id="view-evidence-resolution-date" class="text-sm text-gray-500 hidden"></span>
                </div>
                
                <h4 class="font-medium text-gray-700 mb-3">Imágenes:</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Imagen Original (Antes) -->
                    <div class="bg-gray-50 p-3 rounded-md">
                        <h5 class="text-sm font-medium text-gray-600 mb-2">Evidencia Original</h5>
                        <div class="flex justify-center">
                            <img id="view-evidence-image" src="/placeholder.svg" alt="Imagen de evidencia" class="max-h-48 max-w-full rounded-lg shadow-sm">
                        </div>
                        <p id="view-evidence-no-image" class="text-gray-500 text-center italic mt-2 hidden">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 mb-1">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                            </svg>
                            No hay imagen disponible
                        </p>
                    </div>
                    
                    <!-- Imagen Resuelta (Después) -->
                    <div class="bg-gray-50 p-3 rounded-md">
                        <h5 class="text-sm font-medium text-gray-600 mb-2">Evidencia Resuelta</h5>
                        <div class="flex justify-center">
                            <img id="view-evidence-resolved-image" src="/placeholder.svg" alt="Imagen de evidencia resuelta" class="max-h-48 max-w-full rounded-lg shadow-sm hidden">
                        </div>
                        <p id="view-evidence-no-resolved-image" class="text-gray-500 text-center italic mt-2">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 mb-1">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="9" y1="9" x2="15" y2="15"></line>
                                <line x1="15" y1="9" x2="9" y2="15"></line>
                            </svg>
                            No hay imagen de resolución
                        </p>
                        
                        <!-- Formulario para subir imagen resuelta -->
                        <div id="upload-resolved-section" class="mt-3 hidden">
                            <input type="file" id="resolved-image-input" accept="image/*" class="hidden">
                            <button type="button" id="upload-resolved-btn" class="w-full bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium py-2 px-3 rounded-md text-sm transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-1">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                    <polyline points="7 10 12 15 17 10"></polyline>
                                    <line x1="12" y1="15" x2="12" y2="3"></line>
                                </svg>
                                Subir imagen resuelta
                            </button>
                            <p id="resolved-file-name" class="text-xs text-gray-500 mt-1 hidden"></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Checkbox de Visto Bueno -->
<div class="mb-4 flex items-center gap-2">
    <input type="checkbox" id="evidence-approval-checkbox" class="h-5 w-5 text-green-600 border-gray-300 rounded focus:ring-green-500">
    <label for="evidence-approval-checkbox" class="text-gray-700 font-medium select-none">Visto Bueno</label>
    <span id="approval-status" class="text-sm text-gray-500 ml-2"></span>
</div>
        <div class="bg-gray-50 px-6 py-4 flex justify-between items-center rounded-b-lg border-t">
            <div class="flex gap-3">
                <button id="mark-resolved-btn" class="bg-green-600 hover:bg-green-700 text-white font-medium py-2.5 px-5 rounded-lg transition-colors hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-1">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    Marcar como Resuelta
                </button>
                <button id="reopen-evidence-btn" class="bg-yellow-600 hover:bg-yellow-700 text-white font-medium py-2.5 px-5 rounded-lg transition-colors hidden">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-1">
                        <path d="M1 4v6h6"></path>
                        <path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"></path>
                    </svg>
                    Reabrir Evidencia
                </button>
            </div>
            <button id="close-view-evidence" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-2.5 px-5 rounded-lg transition-colors">
                Cerrar
            </button>
        </div>
    </div>
</div>

<!-- Modal para editar evidencia -->
<div id="edit-evidence-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg shadow-xl max-w-lg w-full mx-4 animate-fadeIn">
        <div class="border-b p-4 flex justify-between items-center">
            <h3 class="text-xl font-semibold text-gray-800">Editar Evidencia</h3>
            <button id="close-edit-modal" class="text-gray-400 hover:text-gray-500 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
        <form id="edit-evidence-form" class="p-6">
            <input type="hidden" id="edit-evidence-id" name="edit-evidence-id">
            
            <div class="mb-5">
                <label for="edit-zone" class="block text-sm font-medium text-gray-700 mb-2">Zona/Ubicación</label>
                <input type="text" id="edit-zone" name="edit-zone" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 py-2.5" readonly>
                <p class="text-xs text-gray-500 mt-1">La ubicación no se puede modificar</p>
            </div>
            
            <div class="mb-5">
                <label for="edit-description" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                <textarea id="edit-description" name="edit-description" rows="3" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50" required></textarea>
            </div>
            
            <div class="mb-6">
                <label for="edit-evidence-images" class="block text-sm font-medium text-gray-700 mb-2">Añadir Más Imágenes</label>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center hover:bg-gray-50 transition-colors cursor-pointer">
                    <input type="file" id="edit-evidence-images" name="edit-evidence-images[]" class="hidden" accept="image/*" multiple>
                    <label for="edit-evidence-images" class="cursor-pointer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mx-auto text-gray-400 mb-2">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                            <circle cx="8.5" cy="8.5" r="1.5"></circle>
                            <polyline points="21 15 16 10 5 21"></polyline>
                        </svg>
                        <span class="text-sm text-gray-600">Haga clic para añadir imágenes adicionales</span>
                        <p class="text-xs text-gray-500 mt-1">Puede seleccionar múltiples imágenes</p>
                    </label>
                </div>
                <div id="edit-selected-files" class="mt-2 text-sm text-gray-600"></div>
            </div>
            
            <div class="flex justify-end gap-3">
                <button type="button" id="cancel-edit-evidence" class="bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2.5 px-5 rounded-lg transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 px-5 rounded-lg transition-colors shadow-md">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Notificación flotante -->
<div id="notification-container" class="fixed top-4 right-4 z-50"></div>

<style>
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }
    
    .animate-pulse {
        animation: pulse 1.5s infinite;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const locationSelector = document.getElementById('location-selector');
    const blueprintSelector = document.getElementById('blueprint-selector');
    const blueprintPreview = document.getElementById('blueprint-preview');
    const blueprintImage = document.getElementById('blueprint-image');
    const blueprintPlaceholder = document.getElementById('blueprint-placeholder');
    const incidentsList = document.getElementById('incidents-list');
    const incidentsCount = document.getElementById('incidents-count');
    const registerButton = document.getElementById('registrar-evidencia-btn');
    const evidenceModal = document.getElementById('evidence-modal');
    const closeModal = document.getElementById('close-modal');
    const cancelEvidence = document.getElementById('cancel-evidence');
    const evidenceForm = document.getElementById('evidence-form');
    const pointX = document.getElementById('point-x');
    const pointY = document.getElementById('point-y');
    const blueprintContainer = document.getElementById('blueprint-container');
    const fileInput = document.getElementById('evidence-images');
    const selectedFiles = document.getElementById('selected-files');
    
    // Referencias al modal de ver evidencia
    const viewEvidenceModal = document.getElementById('view-evidence-modal');
    const closeViewModal = document.getElementById('close-view-modal');
    const closeViewEvidence = document.getElementById('close-view-evidence');
    
    // Referencias al modal de editar evidencia
    const editEvidenceModal = document.getElementById('edit-evidence-modal');
    const closeEditModal = document.getElementById('close-edit-modal');
    const cancelEditEvidence = document.getElementById('cancel-edit-evidence');
    const editEvidenceForm = document.getElementById('edit-evidence-form');
    const editFileInput = document.getElementById('edit-evidence-images');
    const editSelectedFiles = document.getElementById('edit-selected-files');
    
    // Variable para controlar el modo de selección de punto
    let isSelectingPoint = false;
    let temporaryMarker = null;
    
    // Mostrar archivos seleccionados
    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            let fileNames = Array.from(this.files).map(file => file.name).join(', ');
            selectedFiles.innerHTML = `<div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span>${this.files.length} ${this.files.length === 1 ? 'archivo seleccionado' : 'archivos seleccionados'}</span>
            </div>`;
        } else {
            selectedFiles.innerHTML = '';
        }
    });
    
    // Mostrar archivos seleccionados en el modal de edición
    editFileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            let fileNames = Array.from(this.files).map(file => file.name).join(', ');
            editSelectedFiles.innerHTML = `<div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span>${this.files.length} ${this.files.length === 1 ? 'archivo seleccionado' : 'archivos seleccionados'}</span>
            </div>`;
        } else {
            editSelectedFiles.innerHTML = '';
        }
    });
    
    // Evento al hacer clic en el botón "Registrar Evidencia"
    registerButton.addEventListener('click', function() {
        // Verificar si hay un plano seleccionado
        if (blueprintSelector.value && !blueprintImage.classList.contains('hidden')) {
            // Activar modo de selección de punto
            isSelectingPoint = true;
            // Cambiar el puntero a modo "crosshair" para indicar que está en modo selección
            blueprintContainer.style.cursor = 'crosshair';
            // Mostrar instrucción temporal
            showNotification('Haga clic en el plano para marcar la ubicación de la evidencia', 'info');
        } else {
            showNotification('Por favor, seleccione una sede y un plano antes de registrar una evidencia.', 'warning');
        }
    });
    
    // Función para mostrar una notificación temporal
    function showNotification(message, type = 'success') {
        const container = document.getElementById('notification-container');
        
        // Crear elemento de notificación
        const notification = document.createElement('div');
        
        // Aplicar clases según el tipo
        let bgColor, icon;
        
        if (type === 'error') {
            bgColor = 'bg-red-500';
            icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>';
        } else if (type === 'warning') {
            bgColor = 'bg-amber-500';
            icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>';
        } else if (type === 'info') {
            bgColor = 'bg-blue-500';
            icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>';
        } else {
            bgColor = 'bg-green-500';
            icon = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>';
        }
        
        notification.className = `${bgColor} text-white px-4 py-3 rounded-lg shadow-lg flex items-start gap-3 mb-3 animate-fadeIn max-w-md`;
        notification.innerHTML = `
            <div class="flex-shrink-0 mt-0.5">${icon}</div>
            <div class="flex-1">${message}</div>
            <button class="text-white ml-2 flex-shrink-0 hover:text-gray-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        `;
        
        container.appendChild(notification);
        
        // Agregar evento para cerrar la notificación al hacer clic en el botón de cerrar
        const closeButton = notification.querySelector('button');
        closeButton.addEventListener('click', function() {
            notification.remove();
        });
        
        // Eliminar después de 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.classList.add('opacity-0');
                notification.style.transition = 'opacity 0.5s ease-out';
                setTimeout(() => notification.remove(), 500);
            }
        }, 5000);
    }
    
    // Evento al hacer clic en el plano
    blueprintContainer.addEventListener('click', function(e) {
        if (!isSelectingPoint || blueprintImage.classList.contains('hidden')) return;
        
        // Obtener las dimensiones actuales de la imagen y el contenedor
        const planoRect = blueprintContainer.getBoundingClientRect();
        const imagenRect = blueprintImage.getBoundingClientRect();
        
        // Calcular la posición relativa al contenedor
        const imagenLeft = imagenRect.left - planoRect.left;
        const imagenTop = imagenRect.top - planoRect.top;
        
        // Verificar si el clic está dentro de la imagen
        if (e.clientX < imagenRect.left || e.clientX > imagenRect.right || 
            e.clientY < imagenRect.top || e.clientY > imagenRect.bottom) {
            return; // Clic fuera de la imagen
        }
        
        // Calcular posición en coordenadas relativas a la imagen (no en porcentaje)
        const posX = e.clientX - imagenRect.left;
        const posY = e.clientY - imagenRect.top;
        
        // Guardar las coordenadas en el formulario (en coordenadas absolutas dentro de la imagen)
        pointX.value = posX;
        pointY.value = posY;
        
        // Eliminar marcador temporal anterior, si existe
        if (temporaryMarker) temporaryMarker.remove();
        
        // Crear un marcador en la posición seleccionada usando coordenadas absolutas
        temporaryMarker = document.createElement('div');
        temporaryMarker.className = 'absolute transform -translate-x-1/2 -translate-y-1/2 z-20 pointer-events-none';
        temporaryMarker.style.position = 'absolute';
        temporaryMarker.style.left = `${imagenLeft + posX}px`;
        temporaryMarker.style.top = `${imagenTop + posY}px`;
        temporaryMarker.style.width = '24px';
        temporaryMarker.style.height = '24px';
        temporaryMarker.innerHTML = `
            <div class="w-6 h-6 rounded-full bg-white border-2 border-red-500 flex items-center justify-center animate-pulse shadow-lg">
                <span class="w-3 h-3 rounded-full bg-red-500"></span>
            </div>
        `;
        
        // Agregar el marcador al contenedor
        blueprintContainer.appendChild(temporaryMarker);
        
        // Desactivar modo de selección de punto
        isSelectingPoint = false;
        blueprintContainer.style.cursor = 'default';
        
        // Mostrar el modal de registro de evidencia
        evidenceModal.classList.remove('hidden');
    });
    
    // Evento para cerrar el modal
    closeModal.addEventListener('click', closeEvidenceModal);
    cancelEvidence.addEventListener('click', closeEvidenceModal);
    
    // Función para cerrar el modal y limpiar
    function closeEvidenceModal() {
        evidenceModal.classList.add('hidden');
        evidenceForm.reset();
        selectedFiles.innerHTML = '';
        
        // Eliminar el marcador temporal
        if (temporaryMarker) {
            temporaryMarker.remove();
            temporaryMarker = null;
        }
    }
    
    // Manejar el envío del formulario
    evidenceForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Obtener los datos del formulario
        const zona = document.getElementById('zone').value;
        const descripcion = document.getElementById('description').value;
        const posX = parseFloat(pointX.value);
        const posY = parseFloat(pointY.value);
        
        // Crear un objeto FormData para enviar los datos, incluidas las imágenes
        const formData = new FormData();
        formData.append('zona_ubicacion', zona);
        formData.append('description', descripcion);
        formData.append('coordenada_x', posX);
        formData.append('coordenada_y', posY);
        
        // Agregar el ID del plano seleccionado
        formData.append('plano_id', blueprintSelector.value);
        
        // Agregar todas las imágenes seleccionadas
        const imageFiles = document.getElementById('evidence-images').files;
        for (let i = 0; i < imageFiles.length; i++) {
            formData.append('evidencia_imagenes[]', imageFiles[i]);
        }
        
        // Mostrar indicador de carga
        const submitBtn = evidenceForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Guardando...
        `;
        
        // Enviar los datos al servidor mediante AJAX
        fetch('<?= site_url('evidencia/guardarEvidencia') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la solicitud: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Mostrar mensaje de éxito
                showNotification('Evidencia registrada correctamente', 'success');
                
                // Obtener las dimensiones actuales de la imagen y el contenedor
                const planoRect = blueprintContainer.getBoundingClientRect();
                const imagenRect = blueprintImage.getBoundingClientRect();
                
                // Calcular la posición relativa al contenedor
                const imagenLeft = imagenRect.left - planoRect.left;
                const imagenTop = imagenRect.top - planoRect.top;
                
                // Color para las evidencias
                const color = '#ff4500'; // Naranjo rojizo para evidencias
                
                // Crear un marcador permanente para la evidencia
                const evidenceMarker = document.createElement('div');
                evidenceMarker.className = 'absolute transform -translate-x-1/2 -translate-y-1/2 z-10 pointer-events-none';
                evidenceMarker.style.position = 'absolute';
                evidenceMarker.style.left = `${imagenLeft + posX}px`;
                evidenceMarker.style.top = `${imagenTop + posY}px`;
                evidenceMarker.style.width = '20px';
                evidenceMarker.style.height = '20px';
                evidenceMarker.innerHTML = `
                    <div class="w-5 h-5 rounded-full bg-white border-2 flex items-center justify-center shadow-md"
                        style="border-color: ${color};">
                        <span class="w-3 h-3 rounded-full" style="background-color: ${color};"></span>
                    </div>
                `;
                
                // Guardar las coordenadas originales y ID como atributos de datos
                evidenceMarker.dataset.originalX = posX;
                evidenceMarker.dataset.originalY = posY;
                evidenceMarker.dataset.evidenceId = data.evidencia_id || '';
                
                // Agregar metadatos al marcador para futuras interacciones
                evidenceMarker.dataset.evidenceType = 'N/A';
                evidenceMarker.dataset.evidenceZone = zona;
                
                // Agregar el marcador al contenedor
                blueprintContainer.appendChild(evidenceMarker);
                
                // Actualizar la lista de evidencias
                loadIncidents();
            } else {
                // Mostrar mensaje de error
                showNotification('Error al registrar la evidencia: ' + (data.message || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al registrar la evidencia: ' + error.message, 'error');
        })
        .finally(() => {
            // Restaurar el botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            
            // Cerrar modal
            closeEvidenceModal();
        });
    });
    
    // Función para reposicionar los marcadores cuando cambia el tamaño de la ventana o la imagen
    function reposicionarMarcadores() {
        // Obtener las dimensiones actuales de la imagen y el contenedor
        const planoRect = blueprintContainer.getBoundingClientRect();
        const imagenRect = blueprintImage.getBoundingClientRect();
        
        // Si la imagen no está visible, no hacer nada
        if (blueprintImage.classList.contains('hidden')) return;
        
        // Calcular la posición relativa al contenedor
        const imagenLeft = imagenRect.left - planoRect.left;
        const imagenTop = imagenRect.top - planoRect.top;
        
        // Reposicionar todos los marcadores de trampas
        document.querySelectorAll('#traps-container > div').forEach(marker => {
            if (marker.dataset.originalX && marker.dataset.originalY) {
                const posX = parseFloat(marker.dataset.originalX);
                const posY = parseFloat(marker.dataset.originalY);
                
                marker.style.left = `${imagenLeft + posX}px`;
                marker.style.top = `${imagenTop + posY}px`;
            }
        });
        
        // Reposicionar todos los marcadores de evidencias
        document.querySelectorAll('#blueprint-container > div[data-evidence-id]').forEach(marker => {
            if (marker.dataset.originalX && marker.dataset.originalY) {
                const posX = parseFloat(marker.dataset.originalX);
                const posY = parseFloat(marker.dataset.originalY);
                
                marker.style.left = `${imagenLeft + posX}px`;
                marker.style.top = `${imagenTop + posY}px`;
            }
        });
    }
    
    // Reposicionar los marcadores cuando cambie el tamaño de la ventana o la imagen
    window.addEventListener('resize', reposicionarMarcadores);
    blueprintImage.addEventListener('load', reposicionarMarcadores);
    
    // Evento al cambiar de sede
    locationSelector.addEventListener('change', function() {
        // Limpiar selector de planos
        blueprintSelector.innerHTML = '';
        
        if (this.value) {
            // Habilitar selector de planos
            blueprintSelector.disabled = false;
            
            // Mostrar indicador de carga en el selector de planos
            const loadingOption = document.createElement('option');
            loadingOption.value = '';
            loadingOption.textContent = 'Cargando planos...';
            blueprintSelector.appendChild(loadingOption);
            
            // Cargar planos asociados a la sede seleccionada via AJAX
            fetch(`<?= site_url('incidents/getPlanosBySede') ?>/${this.value}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Error en la solicitud: ${response.status}`);
                    }
                    return response.json();
                })
                .then(planos => {
                    // Limpiar selector
                    blueprintSelector.innerHTML = '';
                    
                    // Opción por defecto
                    const defaultOption = document.createElement('option');
                    defaultOption.value = '';
                    defaultOption.textContent = 'Seleccione un plano';
                    blueprintSelector.appendChild(defaultOption);
                    
                    // Cargar planos obtenidos del servidor
                    if (planos && planos.length > 0) {
                        planos.forEach(plano => {
                            const option = document.createElement('option');
                            option.value = plano.id;
                            option.textContent = plano.nombre;
                            option.dataset.archivo = plano.archivo || '';
                            blueprintSelector.appendChild(option);
                        });
                        
                        // Actualizar contador
                        showNotification(`Se encontraron ${planos.length} planos disponibles`, 'info');
                    } else {
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'No hay planos disponibles para esta sede';
                        option.disabled = true;
                        blueprintSelector.appendChild(option);
                        
                        showNotification('No se encontraron planos para esta sede', 'warning');
                    }
                })
                .catch(error => {
                    console.error('Error al cargar planos:', error);
                    blueprintSelector.innerHTML = '';
                    const option = document.createElement('option');
                    option.value = '';
                    option.textContent = `Error al cargar planos`;
                    option.disabled = true;
                    blueprintSelector.appendChild(option);
                    
                    showNotification(`Error al cargar planos: ${error.message}`, 'error');
                });
        } else {
            // Si no hay sede seleccionada, deshabilitar selector de planos
            blueprintSelector.disabled = true;
            const option = document.createElement('option');
            option.value = '';
            option.textContent = 'Seleccione una sede primero';
            blueprintSelector.appendChild(option);
            
            // Ocultar vista previa
            blueprintPreview.classList.add('hidden');
        }
        
        // Cargar incidencias según filtros
        loadIncidents();
    });
    
    // También reposicionar cuando cambie la selección de plano
    blueprintSelector.addEventListener('change', function() {
        // Limpiar los contenedores previos
        document.getElementById('traps-container').innerHTML = '';
        document.getElementById('traps-list').innerHTML = '';
        document.getElementById('traps-legend').classList.add('hidden');
        
        // Limpiar los marcadores de evidencias previos
        document.querySelectorAll('#blueprint-container > div[data-evidence-id]').forEach(marker => {
            marker.remove();
        });
        
        if (this.value) {
            const selectedOption = this.options[this.selectedIndex];
            const archivoData = selectedOption.dataset.archivo;
            
            // Mostrar vista previa
            blueprintPreview.classList.remove('hidden');
            
            if (archivoData) {
                try {
                    // Intentar parsear datos del plano (JSON)
                    const planoData = JSON.parse(archivoData);
                    
                    // Verificar si hay una imagen
                    if (planoData.imagen) {
                        blueprintImage.src = planoData.imagen;
                        blueprintImage.alt = selectedOption.textContent;
                        blueprintImage.classList.remove('hidden');
                        blueprintPlaceholder.classList.add('hidden');
                        
                        // Cargar las trampas si existen
                        if (planoData.trampas && planoData.trampas.length > 0) {
                            renderTraps(planoData.trampas);
                        }
                    } else if (planoData.background) {
                        // Formato alternativo
                        blueprintImage.src = planoData.background;
                        blueprintImage.alt = selectedOption.textContent;
                        blueprintImage.classList.remove('hidden');
                        blueprintPlaceholder.classList.add('hidden');
                        
                        // Cargar las trampas si existen en el formato alternativo
                        if (planoData.elements && planoData.elements.length > 0) {
                            const trampas = planoData.elements.filter(el => el.type === 'trampa' || el.type === 'trap');
                            if (trampas.length > 0) {
                                renderTraps(trampas);
                            }
                        }
                    } else {
                        // No hay imagen en el JSON, mostrar placeholder
                        blueprintImage.classList.add('hidden');
                        blueprintPlaceholder.classList.remove('hidden');
                    }
                } catch (e) {
                    console.error('Error al parsear el JSON:', e);
                    
                    // Comprobar si es una ruta de imagen directa
                    if (archivoData.includes('.jpg') || archivoData.includes('.png') || archivoData.includes('.jpeg') || archivoData.includes('.gif')) {
                        // Es una ruta de imagen
                        blueprintImage.src = `<?= base_url() ?>/uploads/planos/${archivoData}`;
                        blueprintImage.alt = selectedOption.textContent;
                        blueprintImage.classList.remove('hidden');
                        blueprintPlaceholder.classList.add('hidden');
                    } else {
                        // No es un JSON válido ni una imagen
                        blueprintImage.classList.add('hidden');
                        blueprintPlaceholder.classList.remove('hidden');
                        showNotification('El formato del plano no es válido', 'warning');
                    }
                }
            } else {
                // Si no hay datos del archivo, mostrar placeholder
                blueprintImage.classList.add('hidden');
                blueprintPlaceholder.classList.remove('hidden');
            }
            
            // Cargar evidencias existentes y mostrarlas en el plano
            loadEvidenceMarkers(this.value);
        } else {
            // Si no hay plano seleccionado, ocultar imagen
            blueprintImage.classList.add('hidden');
            blueprintPlaceholder.classList.remove('hidden');
        }
        
        // Cargar incidencias según filtros
        loadIncidents();
        
        // Reposicionar marcadores cuando se cargue la nueva imagen
        if (!blueprintImage.complete) {
            blueprintImage.onload = function() {
                setTimeout(reposicionarMarcadores, 100);
            };
        } else {
            setTimeout(reposicionarMarcadores, 100);
        }
    });
    
    // Función para cargar incidencias según los filtros seleccionados
    function loadIncidents() {
        const locationId = locationSelector.value;
        const blueprintId = blueprintSelector.value;
        
        // Si no hay plano seleccionado, mostrar mensaje por defecto
        if (!blueprintId) {
            incidentsList.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    <div class="flex flex-col items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-3">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                        <p class="text-lg">Seleccione un plano para ver las evidencias</p>
                        <p class="text-sm text-gray-400 mt-1">No se han encontrado registros</p>
                    </div>
                </td>
            </tr>
            `;
            incidentsCount.textContent = '0 registros encontrados';
            return;
        }
        
        // Mostrar indicador de carga
        incidentsList.innerHTML = `
        <tr>
            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                <div class="flex flex-col items-center justify-center">
                    <svg class="animate-spin h-10 w-10 text-blue-500 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="text-lg">Cargando evidencias...</p>
                </div>
            </td>
        </tr>
        `;
        
        // Realizar petición AJAX para obtener las evidencias
        fetch(`<?= site_url('evidencia/getEvidenciasPorPlano') ?>/${blueprintId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Actualizar contador
                incidentsCount.textContent = `${data.length} ${data.length === 1 ? 'registro encontrado' : 'registros encontrados'}`;
                
                if (data.length > 0) {
                    // Generar filas para cada evidencia
                    let html = '';
                    const locationName = locationSelector.options[locationSelector.selectedIndex].textContent;
                    
                    data.forEach(evidencia => {
                        // Determinar el estado y su estilo
                        let estadoClass = 'bg-yellow-100 text-yellow-800';
                        let estadoTexto = 'Pendiente';
                        
                        if (evidencia.estado === 'Resuelta') {
                            estadoClass = 'bg-green-100 text-green-800';
                            estadoTexto = 'Resuelta';
                        }
                        
                        html += `
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">${locationName}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${evidencia.ubicacion}</td>
                            <td class="px-6 py-4 whitespace-nowrap max-w-xs truncate">${evidencia.descripcion}</td>
                            <td class="px-6 py-4 whitespace-nowrap">${formatDate(evidencia.fecha_registro)}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${estadoClass}">
                                    ${estadoTexto}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <button class="text-blue-600 hover:text-blue-800 font-medium mr-3 transition-colors" onclick="verEvidencia(${evidencia.id})">
                                    Ver
                                </button>
                                <button class="text-gray-600 hover:text-gray-800 font-medium transition-colors" onclick="editarEvidencia(${evidencia.id})">
                                    Editar
                                </button>
                            </td>
                        </tr>
                        `;
                    });
                    
                    incidentsList.innerHTML = html;
                } else {
                    // No hay evidencias para este plano
                    incidentsList.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-gray-300 mb-3">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="8" y1="12" x2="16" y2="12"></line>
                                </svg>
                                <p class="text-lg">No se encontraron evidencias</p>
                                <p class="text-sm text-gray-400 mt-1">No hay registros para el plano seleccionado</p>
                            </div>
                        </td>
                    </tr>
                    `;
                }
            })
            .catch(error => {
                console.error('Error al cargar evidencias:', error);
                incidentsList.innerHTML = `
                <tr>
                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                        <div class="flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-red-400 mb-3">
                                <circle cx="12" cy="12" r="10"></circle>
                                <line x1="12" y1="8" x2="12" y2="12"></line>
                                <line x1="12" y1="16" x2="12.01" y2="16"></line>
                            </svg>
                            <p class="text-lg">Error al cargar evidencias</p>
                            <p class="text-sm text-gray-400 mt-1">${error.message}</p>
                        </div>
                    </td>
                </tr>
                `;
                incidentsCount.textContent = '0 registros encontrados';
                showNotification(`Error al cargar evidencias: ${error.message}`, 'error');
            });
    }

    // Función para formatear fecha
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        return date.toLocaleDateString('es-ES', { 
            day: '2-digit', 
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // Variable para almacenar el ID de la evidencia actual
    let currentEvidenceId = null;

    // Funciones para ver, editar y eliminar evidencias
    window.verEvidencia = function(id) {
        currentEvidenceId = id;
        
        // Mostrar indicador de carga en el modal
        viewEvidenceModal.classList.remove('hidden');
        document.getElementById('view-evidence-location').innerHTML = '<div class="animate-pulse bg-gray-200 h-5 rounded"></div>';
        document.getElementById('view-evidence-description').innerHTML = '<div class="animate-pulse bg-gray-200 h-20 rounded"></div>';
        document.getElementById('view-evidence-date').innerHTML = '<div class="animate-pulse bg-gray-200 h-5 rounded"></div>';
        document.getElementById('view-evidence-image').classList.add('hidden');
        document.getElementById('view-evidence-no-image').classList.add('hidden');
        document.getElementById('view-evidence-resolved-image').classList.add('hidden');
        document.getElementById('view-evidence-no-resolved-image').classList.remove('hidden');
        document.getElementById('view-evidence-title').textContent = 'Cargando detalles...';
        
        // Obtener los detalles de la evidencia mediante AJAX
        fetch(`<?= site_url('evidencia/getEvidencia') ?>/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status}`);
                }
                return response.json();
            })
            .then(evidencia => {
                // Actualizar el modal con los datos de la evidencia
                document.getElementById('view-evidence-title').textContent = `Evidencia: ${evidencia.ubicacion}`;
                document.getElementById('view-evidence-location').textContent = evidencia.ubicacion;
                document.getElementById('view-evidence-description').textContent = evidencia.descripcion;
                document.getElementById('view-evidence-date').textContent = formatDate(evidencia.fecha_registro);
                
                // Manejar el estado de la evidencia
                const statusElement = document.getElementById('view-evidence-status');
                const resolutionDateElement = document.getElementById('view-evidence-resolution-date');
                const markResolvedBtn = document.getElementById('mark-resolved-btn');
                const reopenBtn = document.getElementById('reopen-evidence-btn');
                const uploadSection = document.getElementById('upload-resolved-section');
                
                if (evidencia.estado === 'Resuelta') {
                    statusElement.textContent = 'Resuelta';
                    statusElement.className = 'px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800';
                    markResolvedBtn.classList.add('hidden');
                    reopenBtn.classList.remove('hidden');
                    uploadSection.classList.add('hidden');
                    
                    if (evidencia.fecha_resolucion) {
                        resolutionDateElement.textContent = `Resuelta el ${formatDate(evidencia.fecha_resolucion)}`;
                        resolutionDateElement.classList.remove('hidden');
                    }
                } else {
                    statusElement.textContent = 'Pendiente';
                    statusElement.className = 'px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800';
                    markResolvedBtn.classList.remove('hidden');
                    reopenBtn.classList.add('hidden');
                    uploadSection.classList.remove('hidden');
                    resolutionDateElement.classList.add('hidden');
                }
                
                // Manejar la imagen original
                const imageElement = document.getElementById('view-evidence-image');
                const noImageElement = document.getElementById('view-evidence-no-image');
                
                if (evidencia.imagen_evidencia) {
                    imageElement.src = `<?= base_url() ?>/${evidencia.imagen_evidencia}`;
                    imageElement.classList.remove('hidden');
                    noImageElement.classList.add('hidden');
                    
                    imageElement.onerror = function() {
                        imageElement.classList.add('hidden');
                        noImageElement.textContent = 'Error al cargar la imagen';
                        noImageElement.classList.remove('hidden');
                    };
                } else {
                    imageElement.classList.add('hidden');
                    noImageElement.classList.remove('hidden');
                }
                
                // Manejar la imagen resuelta
                const resolvedImageElement = document.getElementById('view-evidence-resolved-image');
                const noResolvedImageElement = document.getElementById('view-evidence-no-resolved-image');
                
                if (evidencia.imagen_resuelta) {
                    resolvedImageElement.src = `<?= base_url() ?>/${evidencia.imagen_resuelta}`;
                    resolvedImageElement.classList.remove('hidden');
                    noResolvedImageElement.classList.add('hidden');
                    
                    resolvedImageElement.onerror = function() {
                        resolvedImageElement.classList.add('hidden');
                        noResolvedImageElement.textContent = 'Error al cargar la imagen resuelta';
                        noResolvedImageElement.classList.remove('hidden');
                    };
                } else {
                    resolvedImageElement.classList.add('hidden');
                    noResolvedImageElement.classList.remove('hidden');
                }
                
                // Manejar el checkbox de Visto Bueno
                const approvalCheckbox = document.getElementById('evidence-approval-checkbox');
                const approvalStatus = document.getElementById('approval-status');
                
                // Limpiar eventos previos
                approvalCheckbox.removeEventListener('change', handleApprovalChange);
                
                // Configurar el estado del checkbox
                approvalCheckbox.checked = evidencia.visto_bueno_supervisor == 1;
                
                // Actualizar el texto de estado
                if (evidencia.visto_bueno_supervisor == 1) {
                    approvalStatus.textContent = '✓ Aprobado';
                    approvalStatus.className = 'text-sm text-green-600 ml-2 font-medium';
                } else {
                    approvalStatus.textContent = 'Pendiente de aprobación';
                    approvalStatus.className = 'text-sm text-gray-500 ml-2';
                }
                
                // Agregar evento para manejar cambios
                approvalCheckbox.addEventListener('change', handleApprovalChange);
            })
            .catch(error => {
                console.error('Error al cargar los detalles de la evidencia:', error);
                document.getElementById('view-evidence-title').textContent = 'Error al cargar detalles';
                document.getElementById('view-evidence-location').textContent = '-';
                document.getElementById('view-evidence-description').textContent = `Error: ${error.message}`;
                document.getElementById('view-evidence-date').textContent = '-';
                document.getElementById('view-evidence-image').classList.add('hidden');
                document.getElementById('view-evidence-no-image').classList.remove('hidden');
                document.getElementById('view-evidence-no-image').textContent = 'No se pudo cargar la información';
                
                showNotification('Error al cargar los detalles de la evidencia: ' + error.message, 'error');
            });
    };
    
    window.editarEvidencia = function(id) {
        // Limpiar formulario
        editEvidenceForm.reset();
        editSelectedFiles.innerHTML = '';
        
        // Mostrar modal de edición con indicador de carga
        editEvidenceModal.classList.remove('hidden');
        document.getElementById('edit-evidence-id').value = id;
        
        // Obtener los detalles de la evidencia mediante AJAX
        fetch(`<?= site_url('evidencia/getEvidencia') ?>/${id}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status}`);
                }
                return response.json();
            })
            .then(evidencia => {
                // Llenar el formulario con los datos de la evidencia
                document.getElementById('edit-zone').value = evidencia.ubicacion;
                document.getElementById('edit-description').value = evidencia.descripcion;
            })
            .catch(error => {
                console.error('Error al cargar los detalles de la evidencia:', error);
                showNotification('Error al cargar los detalles de la evidencia: ' + error.message, 'error');
                closeEditEvidenceModal();
            });
    };
    
    // Manejar el envío del formulario de edición
    editEvidenceForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Obtener los datos del formulario
        const evidenciaId = document.getElementById('edit-evidence-id').value;
        const descripcion = document.getElementById('edit-description').value;
        
        // Crear un objeto FormData para enviar los datos, incluidas las imágenes
        const formData = new FormData();
        formData.append('evidencia_id', evidenciaId);
        formData.append('description', descripcion);
        
        // Agregar todas las imágenes seleccionadas
        const imageFiles = document.getElementById('edit-evidence-images').files;
        for (let i = 0; i < imageFiles.length; i++) {
            formData.append('evidencia_imagenes[]', imageFiles[i]);
        }
        
        // Mostrar indicador de carga
        const submitBtn = editEvidenceForm.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Guardando...
        `;
        
        // Enviar los datos al servidor mediante AJAX
        fetch('<?= site_url('evidencia/actualizarEvidencia') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la solicitud: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Mostrar mensaje de éxito
                showNotification('Evidencia actualizada correctamente', 'success');
                
                // Recargar la lista de evidencias
                loadIncidents();
            } else {
                // Mostrar mensaje de error
                showNotification('Error al actualizar la evidencia: ' + (data.message || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al actualizar la evidencia: ' + error.message, 'error');
        })
        .finally(() => {
            // Restaurar el botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
            
            // Cerrar modal
            closeEditEvidenceModal();
        });
    });
    
    // Eventos para los botones de evidencia resuelta
    document.getElementById('upload-resolved-btn').addEventListener('click', function() {
        document.getElementById('resolved-image-input').click();
    });
    
    document.getElementById('resolved-image-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('resolved-file-name').textContent = `Archivo seleccionado: ${file.name}`;
            document.getElementById('resolved-file-name').classList.remove('hidden');
            
            // Mostrar vista previa inmediata
            const reader = new FileReader();
            reader.onload = function(e) {
                const resolvedImage = document.getElementById('view-evidence-resolved-image');
                const noResolvedImage = document.getElementById('view-evidence-no-resolved-image');
                
                resolvedImage.src = e.target.result;
                resolvedImage.classList.remove('hidden');
                noResolvedImage.classList.add('hidden');
            };
            reader.readAsDataURL(file);
            
            // Subir la imagen automáticamente
            uploadResolvedImage(file);
        }
    });
    
    document.getElementById('mark-resolved-btn').addEventListener('click', function() {
        if (confirm('¿Está seguro de que desea marcar esta evidencia como resuelta?')) {
            markEvidenceAsResolved();
        }
    });
    
    document.getElementById('reopen-evidence-btn').addEventListener('click', function() {
        if (confirm('¿Está seguro de que desea reabrir esta evidencia?')) {
            reopenEvidence();
        }
    });
    
    // Función para subir imagen resuelta
    function uploadResolvedImage(file) {
        if (!currentEvidenceId) return;
        
        const formData = new FormData();
        formData.append('evidencia_id', currentEvidenceId);
        formData.append('imagen_resuelta', file);
        
        fetch('<?= site_url('evidencia/subirImagenResuelta') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Imagen de resolución subida correctamente', 'success');
            } else {
                showNotification('Error al subir la imagen: ' + (data.message || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al subir la imagen: ' + error.message, 'error');
        });
    }
    
    // Función para marcar evidencia como resuelta
    function markEvidenceAsResolved() {
        if (!currentEvidenceId) return;
        
        const formData = new FormData();
        formData.append('evidencia_id', currentEvidenceId);
        formData.append('estado', 'Resuelta');
        
        fetch('<?= site_url('evidencia/cambiarEstado') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Evidencia marcada como resuelta', 'success');
                // Actualizar la vista
                verEvidencia(currentEvidenceId);
                // Recargar la lista
                loadIncidents();
            } else {
                showNotification('Error al marcar como resuelta: ' + (data.message || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al marcar como resuelta: ' + error.message, 'error');
        });
    }
    
    // Función para reabrir evidencia
    function reopenEvidence() {
        if (!currentEvidenceId) return;
        
        const formData = new FormData();
        formData.append('evidencia_id', currentEvidenceId);
        formData.append('estado', 'Pendiente');
        
        fetch('<?= site_url('evidencia/cambiarEstado') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Evidencia reabierta', 'success');
                // Actualizar la vista
                verEvidencia(currentEvidenceId);
                // Recargar la lista
                loadIncidents();
            } else {
                showNotification('Error al reabrir evidencia: ' + (data.message || 'Error desconocido'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al reabrir evidencia: ' + error.message, 'error');
        });
    }
    
    // Función para manejar cambios en el checkbox de Visto Bueno
    function handleApprovalChange() {
        if (!currentEvidenceId) {
            console.error('No hay evidencia seleccionada');
            return;
        }
        
        const approvalCheckbox = document.getElementById('evidence-approval-checkbox');
        const approvalStatus = document.getElementById('approval-status');
        const isApproved = approvalCheckbox.checked;
        
        console.log('Manejando cambio de Visto Bueno:', {
            evidenciaId: currentEvidenceId,
            isApproved: isApproved
        });
        
        // Mostrar indicador de carga
        approvalStatus.textContent = 'Guardando...';
        approvalStatus.className = 'text-sm text-blue-600 ml-2 font-medium';
        
        const formData = new FormData();
        formData.append('evidencia_id', currentEvidenceId);
        formData.append('visto_bueno', isApproved ? 1 : 0);
        
        console.log('Enviando datos:', {
            evidencia_id: currentEvidenceId,
            visto_bueno: isApproved ? 1 : 0
        });
        
        fetch('<?= site_url('evidencia/vistoBuenoSupervisor') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            console.log('Respuesta del servidor:', response);
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            
            if (data.success) {
                if (isApproved) {
                    approvalStatus.textContent = '✓ Aprobado';
                    approvalStatus.className = 'text-sm text-green-600 ml-2 font-medium';
                    showNotification('Visto Bueno aplicado correctamente', 'success');
                } else {
                    approvalStatus.textContent = 'Pendiente de aprobación';
                    approvalStatus.className = 'text-sm text-gray-500 ml-2';
                    showNotification('Visto Bueno removido correctamente', 'success');
                }
                
                // Recargar la lista de evidencias para reflejar el cambio
                loadIncidents();
            } else {
                // Revertir el checkbox si hubo error
                approvalCheckbox.checked = !isApproved;
                approvalStatus.textContent = isApproved ? 'Pendiente de aprobación' : '✓ Aprobado';
                approvalStatus.className = isApproved ? 'text-sm text-gray-500 ml-2' : 'text-sm text-green-600 ml-2 font-medium';
                
                const errorMessage = data.message || 'Error desconocido';
                console.error('Error del servidor:', data);
                showNotification('Error al actualizar Visto Bueno: ' + errorMessage, 'error');
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            // Revertir el checkbox si hubo error
            approvalCheckbox.checked = !isApproved;
            approvalStatus.textContent = isApproved ? 'Pendiente de aprobación' : '✓ Aprobado';
            approvalStatus.className = isApproved ? 'text-sm text-gray-500 ml-2' : 'text-sm text-green-600 ml-2 font-medium';
            showNotification('Error al actualizar Visto Bueno: ' + error.message, 'error');
        });
    }
    
    // Eventos para cerrar los modales
    closeEditModal.addEventListener('click', closeEditEvidenceModal);
    cancelEditEvidence.addEventListener('click', closeEditEvidenceModal);
    
    closeViewModal.addEventListener('click', closeViewEvidenceModal);
    closeViewEvidence.addEventListener('click', closeViewEvidenceModal);
    
    // Funciones para cerrar los modales
    function closeEditEvidenceModal() {
        editEvidenceModal.classList.add('hidden');
        editEvidenceForm.reset();
        editSelectedFiles.innerHTML = '';
    }
    
    function closeViewEvidenceModal() {
        viewEvidenceModal.classList.add('hidden');
        currentEvidenceId = null;
        // Limpiar los campos de imagen resuelta
        document.getElementById('resolved-file-name').classList.add('hidden');
        document.getElementById('resolved-image-input').value = '';
        
        // Limpiar el estado del checkbox de Visto Bueno
        const approvalCheckbox = document.getElementById('evidence-approval-checkbox');
        const approvalStatus = document.getElementById('approval-status');
        approvalCheckbox.checked = false;
        approvalStatus.textContent = '';
        approvalStatus.className = 'text-sm text-gray-500 ml-2';
        
        // Remover el evento del checkbox
        approvalCheckbox.removeEventListener('change', handleApprovalChange);
    }

    // Función para renderizar las trampas en el plano
    function renderTraps(trampas) {
        const trapsContainer = document.getElementById('traps-container');
        const trapsList = document.getElementById('traps-list');
        const trapsLegend = document.getElementById('traps-legend');
        
        // Mostrar la leyenda
        trapsLegend.classList.remove('hidden');
        
        // Obtener las dimensiones de la imagen para calcular posiciones relativas
        const image = document.getElementById('blueprint-image');
        
        // No podemos saber las dimensiones exactas hasta que la imagen cargue
        image.onload = function() {
            const imageWidth = image.width;
            const imageHeight = image.height;
            
            // Limpiar contenedores por si acaso
            trapsContainer.innerHTML = '';
            trapsList.innerHTML = '';
            
            // Colores para diferentes tipos de trampas
            const trapColors = {
                'Quimicas': '#3B82F6', // Azul
                'UV': '#1E3A8A',       // Azul oscuro
                'Mecanica': '#1D4ED8', // Azul medio
                'Feromona': '#2563EB', // Azul claro
                'default': '#1E40AF'   // Azul por defecto
            };
            
            // Para llevar un registro de los tipos de trampas
            const trapTypes = new Set();
            
            // Renderizar cada trampa
            trampas.forEach((trampa, index) => {
                // Determinar el tipo de trampa
                let tipo = trampa.tipo || trampa.type || 'default';
                if (typeof tipo === 'object' && tipo.nombre) {
                    tipo = tipo.nombre;
                }
                trapTypes.add(tipo);
                
                // Determinar la posición de la trampa
                let x, y;
                
                if (trampa.position) {
                    // Formato: { x: porcentaje, y: porcentaje }
                    x = trampa.position.x * imageWidth / 100;
                    y = trampa.position.y * imageHeight / 100;
                } else if (trampa.x !== undefined && trampa.y !== undefined) {
                    // Formato: { x: porcentaje, y: porcentaje } directamente
                    x = trampa.x * imageWidth / 100;
                    y = trampa.y * imageHeight / 100;
                } else {
                    // Si no hay datos de posición, omitir esta trampa
                    return;
                }
                
                // Determinar el color basado en el tipo
                const color = trapColors[tipo] || trapColors.default;
                
                // Crear el marcador de la trampa
                const trapMarker = document.createElement('div');
                trapMarker.className = 'absolute transform -translate-x-1/2 -translate-y-1/2';
                trapMarker.style.left = `${x}px`;
                trapMarker.style.top = `${y}px`;
                trapMarker.style.width = '16px';
                trapMarker.style.height = '16px';
                trapMarker.innerHTML = `
                    <div class="w-4 h-4 rounded-full bg-white border-2 flex items-center justify-center shadow-sm"
                         style="border-color: ${color};">
                        <span class="w-2 h-2 rounded-full" style="background-color: ${color};"></span>
                    </div>
                `;
                
                // Agregar un atributo de datos para identificar la trampa
                trapMarker.dataset.trapId = trampa.id || index;
                trapMarker.dataset.trapType = tipo;
                
                // Agregar el marcador al contenedor
                trapsContainer.appendChild(trapMarker);
            });
            
            // Crear la leyenda de tipos de trampas
            Array.from(trapTypes).forEach(tipo => {
                const color = trapColors[tipo] || trapColors.default;
                const listItem = document.createElement('li');
                listItem.className = 'flex items-center gap-2';
                listItem.innerHTML = `
                    <span class="inline-block w-3 h-3 rounded-full" style="background-color: ${color};"></span>
                    <span>${tipo}</span>
                `;
                trapsList.appendChild(listItem);
            });
        };
    }

    // Función para cargar marcadores de evidencias existentes
    function loadEvidenceMarkers(blueprintId) {
        // Obtener evidencias para este plano desde el servidor
        fetch(`<?= site_url('evidencia/getEvidenciasPorPlano') ?>/${blueprintId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Error en la solicitud: ${response.status}`);
                }
                return response.json();
            })
            .then(evidencias => {
                // Color para las evidencias
                const color = '#ff4500'; // Naranjo rojizo para evidencias
                
                // Esperar a que la imagen termine de cargar
                if (blueprintImage.complete) {
                    renderEvidenceMarkers(evidencias, color);
                } else {
                    // Si la imagen aún no está cargada, esperar a que termine
                    blueprintImage.onload = function() {
                        renderEvidenceMarkers(evidencias, color);
                    };
                }
            })
            .catch(error => {
                console.error('Error al cargar evidencias para el plano:', error);
                showNotification('Error al cargar evidencias para el plano', 'error');
            });
    }
    
    // Función para renderizar los marcadores de evidencias
    function renderEvidenceMarkers(evidencias, color) {
        // Obtener las dimensiones de la imagen y el contenedor
        const planoRect = blueprintContainer.getBoundingClientRect();
        const imagenRect = blueprintImage.getBoundingClientRect();
        
        // Si la imagen no está visible, no hacer nada
        if (blueprintImage.classList.contains('hidden')) return;
        
        // Calcular la posición relativa al contenedor
        const imagenLeft = imagenRect.left - planoRect.left;
        const imagenTop = imagenRect.top - planoRect.top;
        
        // Crear marcadores para cada evidencia
        evidencias.forEach(evidencia => {
            // Crear un marcador para la evidencia
            const evidenceMarker = document.createElement('div');
            evidenceMarker.className = 'absolute transform -translate-x-1/2 -translate-y-1/2 z-10 pointer-events-none';
            evidenceMarker.style.position = 'absolute';
            evidenceMarker.style.left = `${imagenLeft + parseFloat(evidencia.coordenada_x)}px`;
            evidenceMarker.style.top = `${imagenTop + parseFloat(evidencia.coordenada_y)}px`;
            evidenceMarker.style.width = '20px';
            evidenceMarker.style.height = '20px';
            evidenceMarker.innerHTML = `
                <div class="w-5 h-5 rounded-full bg-white border-2 flex items-center justify-center shadow-md"
                    style="border-color: ${color};">
                    <span class="w-3 h-3 rounded-full" style="background-color: ${color};"></span>
                </div>
            `;
            
            // Guardar las coordenadas originales y ID como atributos de datos
            evidenceMarker.dataset.originalX = evidencia.coordenada_x;
            evidenceMarker.dataset.originalY = evidencia.coordenada_y;
            evidenceMarker.dataset.evidenceId = evidencia.id;
            
            // Agregar metadatos al marcador
            evidenceMarker.dataset.evidenceZone = evidencia.ubicacion;
            
            // Agregar el marcador al contenedor
            blueprintContainer.appendChild(evidenceMarker);
        });
    }

    // Evento para cerrar el modal de ver evidencia
    closeViewModal.addEventListener('click', closeViewEvidenceModal);
    closeViewEvidence.addEventListener('click', closeViewEvidenceModal);
    
    // Función para cerrar el modal de ver evidencia
    function closeViewEvidenceModal() {
        viewEvidenceModal.classList.add('hidden');
    }
});
</script>
<?= $this->endSection() ?>

