<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl p-6 text-white shadow-lg text-center mb-6">
    <div class="flex flex-col items-center justify-center">
        <h1 class="text-3xl font-bold text-white mb-2">Sistema de Mapeo de Trampas</h1>
        <p class="text-blue-100">Visualización y gestión de planos</p>
    </div>
</div>

<div class="container mx-auto px-4 py-8">
    <!-- Sección de Sedes -->
    <section aria-labelledby="sedes-heading" class="mb-12">
        <h2 id="sedes-heading" class="text-2xl font-semibold mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            Plantas Disponibles
        </h2>
        
        <?php if (empty($sedes)): ?>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-500">No hay plantas disponibles en el sistema.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($sedes as $sede): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300">
                        <div class="p-5 border-l-4 border-blue-600">
                            <h3 class="text-xl font-semibold mb-2"><?= esc($sede['nombre']) ?></h3>
                            <p class="text-gray-600 mb-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <?= esc($sede['direccion']) ?>, <?= esc($sede['ciudad']) ?>, <?= esc($sede['pais']) ?>
                            </p>
                        </div>
                        <div class="p-4 bg-gray-50 border-t border-gray-200 flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                            </span>
                            <a href="<?= base_url('blueprints/view/' . $sede['id']) ?>" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                <span>Ver planos</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
    
    <!-- Sección de Planos con Imágenes -->
    <section aria-labelledby="planos-heading" class="mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 id="planos-heading" class="text-2xl font-semibold flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Todos los Planos
            </h2>
            
            <button type="button" onclick="mostrarModalNuevoPlano()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-300 inline-flex items-center focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                </svg>
                Crear Nuevo Plano
            </button>
        </div>
        
        <?php if (empty($planos)): ?>
            <div class="bg-white rounded-lg shadow-md p-8 text-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-gray-500 mb-4">No hay planos disponibles en el sistema.</p>
                <button type="button" onclick="mostrarModalNuevoPlano()" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-300 inline-flex items-center focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Crear tu primer plano
                </button>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($planos as $plano): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 flex flex-col h-full">
                        <div class="relative h-48">
                            <?php if ($plano['preview_image']): ?>
                                <img src="<?= $plano['preview_image'] ?>" alt="Vista previa de <?= esc($plano['nombre']) ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            <?php endif; ?>
                            <div class="absolute top-0 right-0 bg-blue-600 text-white px-3 py-1 text-xs font-medium rounded-bl-lg">
                                <?= esc($plano['sede_nombre']) ?>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/70 to-transparent p-3">
                                <h3 class="font-semibold text-lg text-white"><?= esc($plano['nombre']) ?></h3>
                            </div>
                        </div>
                        <div class="p-4 flex-grow">
                            <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?= esc($plano['descripcion']) ?></p>
                            <div class="flex justify-between items-center mt-auto pt-3 border-t border-gray-100">
                                <span class="text-xs text-gray-500 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <?= date('d/m/Y', strtotime($plano['fecha_creacion'])) ?>
                                </span>
                                <a href="<?= base_url('blueprints/viewplano/' . $plano['id']) ?>" 
                                   class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1">
                                    <span>Ver plano</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Paginación (si es necesaria) -->
            <?php if (isset($pager)): ?>
                <div class="mt-8">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</div>

<!-- Modal para crear nuevo plano -->
<div id="modalNuevoPlano" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center" aria-modal="true" role="dialog" aria-labelledby="modal-title">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4 relative">
        <button type="button" onclick="cerrarModalNuevoPlano()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600" aria-label="Cerrar modal">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        
        <div class="p-6">
            <h3 id="modal-title" class="text-lg font-semibold mb-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Crear Nuevo Plano
            </h3>
            
            <form action="<?= base_url('blueprints/guardar_plano') ?>" method="post" enctype="multipart/form-data" id="formNuevoPlano">
                <div class="space-y-4">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700">Nombre del Plano</label>
                        <input type="text" name="nombre" id="nombre" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="3" required
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                    </div>
                    
                    <div>
                        <label for="sede_id" class="block text-sm font-medium text-gray-700">Sede</label>
                        <select name="sede_id" id="sede_id" required
                                class="mt-1 block w-full rounded-md border-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-20 transition-shadow duration-200 text-blue-700 font-medium">
                            <option value="">Seleccione una sede</option>
                            <?php foreach ($sedes as $sede): ?>
                                <option value="<?= $sede['id'] ?>"><?= esc($sede['nombre']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="imagen_plano" class="block text-sm font-medium text-gray-700">Imagen del Plano (opcional)</label>
                        <div class="mt-1 flex items-center">
                            <label class="w-full flex items-center justify-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Seleccionar imagen</span>
                                <input id="imagen_plano" name="imagen_plano" type="file" accept="image/*" class="sr-only">
                            </label>
                        </div>
                        <div id="preview-container" class="mt-2 hidden">
                            <img id="preview-image" src="#" alt="Vista previa" class="h-32 object-cover rounded-md">
                            <button type="button" id="remove-image" class="mt-1 text-sm text-red-600 hover:text-red-800">
                                Eliminar imagen
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="cerrarModalNuevoPlano()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                        Cancelar
                    </button>
                    <button type="submit" id="btnGuardarPlano"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Guardar Plano
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Funciones para el modal
    function mostrarModalNuevoPlano() {
        document.getElementById('modalNuevoPlano').classList.remove('hidden');
        document.getElementById('modalNuevoPlano').classList.add('flex');
        document.body.classList.add('overflow-hidden');
        document.getElementById('nombre').focus();
    }
    
    function cerrarModalNuevoPlano() {
        document.getElementById('modalNuevoPlano').classList.add('hidden');
        document.getElementById('modalNuevoPlano').classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
        document.getElementById('formNuevoPlano').reset();
        document.getElementById('preview-container').classList.add('hidden');
    }
    
    // Cerrar modal con Escape
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && !document.getElementById('modalNuevoPlano').classList.contains('hidden')) {
            cerrarModalNuevoPlano();
        }
    });
    
    // Previsualización de imagen
    document.getElementById('imagen_plano').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview-image').src = e.target.result;
                document.getElementById('preview-container').classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
    
    // Eliminar imagen seleccionada
    document.getElementById('remove-image').addEventListener('click', function() {
        document.getElementById('imagen_plano').value = '';
        document.getElementById('preview-container').classList.add('hidden');
    });
    
    // Validación del formulario
    document.getElementById('formNuevoPlano').addEventListener('submit', function(event) {
        const nombre = document.getElementById('nombre').value.trim();
        const descripcion = document.getElementById('descripcion').value.trim();
        const sede = document.getElementById('sede_id').value;
        
        if (!nombre || !descripcion || !sede) {
            event.preventDefault();
            alert('Por favor complete todos los campos requeridos.');
        } else {
            document.getElementById('btnGuardarPlano').disabled = true;
            document.getElementById('btnGuardarPlano').innerHTML = `
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Guardando...
            `;
        }
    });
</script>
<?= $this->endSection() ?>