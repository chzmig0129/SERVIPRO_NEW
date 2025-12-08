<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<style>
    .plano-preview {
        height: 160px;
        background-color: #f3f4f6;
        border-radius: 0.5rem;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
    }
    
    .plano-preview img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    
    .plano-card {
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .plano-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    
    .plano-content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    
    .plano-actions {
        margin-top: auto;
    }
</style>

<div class="space-y-6">
    <!-- Encabezado con botón de regreso y agregar -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="<?= base_url('blueprints') ?>" class="text-gray-600 hover:text-gray-900">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold"><?= $sede['nombre'] ?></h1>
                    <p class="text-gray-500"><?= $sede['direccion'] ?></p>
                </div>
                <div class="flex items-center gap-4 ml-4 pl-4 border-l border-gray-300">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 font-medium">Total Planos</p>
                        <p class="text-2xl font-bold text-blue-600"><?= number_format($total_planos ?? 0) ?></p>
                    </div>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 font-medium">Total Incidencias</p>
                        <p class="text-2xl font-bold text-green-600"><?= number_format($total_incidencias ?? 0) ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <button onclick="openModal()" class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Agregar Plano
            </button>
            <button onclick="confirmarDeshabilitar(<?= $sede['id'] ?>, '<?= esc($sede['nombre'], 'js') ?>')" 
                    class="flex items-center gap-2 bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                Deshabilitar Planta
            </button>
        </div>
    </div>

    <!-- Lista de Planos -->
    <?php if (empty($planos)): ?>
        <div class="bg-white rounded-lg shadow-md p-6 text-center">
            <div class="flex flex-col items-center justify-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900">No hay planos disponibles</h3>
                <p class="text-gray-500 mt-2">Comience agregando un nuevo plano para esta sede</p>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($planos as $plano): ?>
            <div class="bg-white rounded-lg shadow-md p-6 plano-card">
                <div class="plano-preview">
                    <?php if (!empty($plano['preview_image'])): ?>
                        <img src="<?= $plano['preview_image'] ?>" alt="Vista previa del plano" />
                    <?php else: ?>
                        <div class="text-gray-400 flex flex-col items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="text-sm">Sin imagen</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="plano-content">
                    <h3 class="text-lg font-semibold"><?= $plano['nombre'] ?></h3>
                    <p class="text-gray-500 text-sm mt-2"><?= $plano['descripcion'] ?></p>
                    <div class="mt-4 flex items-center justify-between">
                        <div class="text-sm text-gray-500">
                            Creado: <?= date('d/m/Y H:i', strtotime($plano['fecha_creacion'])) ?>
                        </div>
                        <div class="flex items-center gap-2 bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-sm font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span><?= isset($plano['conteo_incidencias']) ? $plano['conteo_incidencias'] : 0 ?> incidencia<?= (isset($plano['conteo_incidencias']) && $plano['conteo_incidencias'] != 1) ? 's' : '' ?></span>
                        </div>
                    </div>
                    <div class="mt-4 plano-actions">
                        <a href="<?= base_url('blueprints/viewplano/' . $plano['id']) ?>" 
                           class="inline-block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                            Ver Plano
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Modal para Agregar Plano -->
    <div id="modalAgregarPlano" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">Agregar Nuevo Plano</h3>
                    <form action="<?= base_url('blueprints/guardar_plano') ?>" method="POST">
                        <input type="hidden" name="sede_id" value="<?= $sede['id'] ?>">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nombre del Plano</label>
                                <input type="text" name="nombre" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Descripción</label>
                                <textarea name="descripcion" rows="3" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button type="button" onclick="closeModal()"
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
    </div>

    <!-- Mensajes de éxito/error -->
    <?php if (session()->getFlashdata('message')): ?>
        <div id="alertMessage" class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div id="alertMessage" class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Modal Confirmar Deshabilitar -->
    <div id="modalConfirmarDeshabilitar" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-red-100 rounded-full p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-center mb-2">Confirmar Deshabilitación</h3>
                <p class="text-center text-gray-600 mb-6">
                    ¿Estás seguro que deseas deshabilitar la planta <span id="nombreSedeDeshabilitar" class="font-medium text-red-600"></span>? 
                    La planta dejará de aparecer en las vistas y estadísticas, pero la información se mantendrá en la base de datos.
                </p>
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="cerrarModalDeshabilitar()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors duration-200">
                        Cancelar
                    </button>
                    <form id="formDeshabilitarSede" action="" method="POST" class="inline">
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200">
                            Sí, Deshabilitar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('modalAgregarPlano').classList.remove('hidden');
}

function closeModal() {
    document.getElementById('modalAgregarPlano').classList.add('hidden');
}

function confirmarDeshabilitar(id, nombre) {
    document.getElementById('nombreSedeDeshabilitar').textContent = nombre;
    document.getElementById('formDeshabilitarSede').action = '<?= base_url('sedes/deshabilitar/') ?>' + id;
    document.getElementById('modalConfirmarDeshabilitar').classList.remove('hidden');
}

function cerrarModalDeshabilitar() {
    document.getElementById('modalConfirmarDeshabilitar').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera
document.getElementById('modalConfirmarDeshabilitar').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalDeshabilitar();
    }
});

// Cerrar modal al hacer clic fuera
document.getElementById('modalAgregarPlano').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Ocultar mensajes de alerta después de 3 segundos
const alertMessage = document.getElementById('alertMessage');
if (alertMessage) {
                            setTimeout(() => {
        alertMessage.style.opacity = '0';
        alertMessage.style.transition = 'opacity 0.5s';
        setTimeout(() => alertMessage.remove(), 500);
    }, 3000);
}
</script>
<?= $this->endSection() ?> 