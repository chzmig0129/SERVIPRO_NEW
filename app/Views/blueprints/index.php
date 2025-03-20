<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-6">Sistema de Mapeo de Trampas</h1>
    
    <!-- Sección de Sedes -->
    <div class="mb-12">
        <h2 class="text-2xl font-semibold mb-4">Sedes Disponibles</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($sedes as $sede): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold"><?= $sede['nombre'] ?></h3>
                        <p class="text-gray-600"><?= $sede['direccion'] ?>, <?= $sede['ciudad'] ?>, <?= $sede['pais'] ?></p>
                    </div>
                    <div class="p-4 bg-gray-50 border-t border-gray-200">
                        <a href="<?= base_url('blueprints/view/' . $sede['id']) ?>" 
                           class="inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Ver planos
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Sección de Planos con Imágenes -->
    <div>
        <h2 class="text-2xl font-semibold mb-4">Todos los Planos</h2>
        
        <?php if (empty($planos)): ?>
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <p class="text-gray-500">No hay planos disponibles en el sistema.</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($planos as $plano): ?>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="relative h-48">
                            <?php if ($plano['preview_image']): ?>
                                <img src="<?= $plano['preview_image'] ?>" alt="<?= $plano['nombre'] ?>" 
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">Sin imagen</span>
                                </div>
                            <?php endif; ?>
                            <div class="absolute top-0 right-0 bg-blue-600 text-white px-2 py-1 text-xs">
                                <?= $plano['sede_nombre'] ?>
                            </div>
                        </div>
                        <div class="p-4">
                            <h3 class="font-semibold text-lg mb-1"><?= $plano['nombre'] ?></h3>
                            <p class="text-gray-600 text-sm mb-3 line-clamp-2"><?= $plano['descripcion'] ?></p>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500">
                                    <?= date('d/m/Y', strtotime($plano['fecha_creacion'])) ?>
                                </span>
                                <a href="<?= base_url('blueprints/viewplano/' . $plano['id']) ?>" 
                                   class="inline-block px-3 py-1 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                                    Ver plano
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Botón para crear nuevo plano -->
    <div class="mt-8 text-center">
        <button type="button" onclick="mostrarModalNuevoPlano()" 
                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 inline-flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Crear Nuevo Plano
        </button>
    </div>
</div>

<!-- Modal para crear nuevo plano -->
<div id="modalNuevoPlano" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
        <div class="p-6">
            <h3 class="text-lg font-semibold mb-4">Crear Nuevo Plano</h3>
            <form action="<?= base_url('blueprints/guardar_plano') ?>" method="post">
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
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">Seleccione una sede</option>
                            <?php foreach ($sedes as $sede): ?>
                                <option value="<?= $sede['id'] ?>"><?= $sede['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="mt-6 flex justify-end space-x-3">
                    <button type="button" onclick="cerrarModalNuevoPlano()"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 hover:bg-gray-200 rounded-md">
                        Cancelar
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md">
                        Guardar Plano
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function mostrarModalNuevoPlano() {
        document.getElementById('modalNuevoPlano').classList.remove('hidden');
    }
    
    function cerrarModalNuevoPlano() {
        document.getElementById('modalNuevoPlano').classList.add('hidden');
    }
</script>
<?= $this->endSection() ?> 