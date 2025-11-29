<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<!-- Encabezado con gradiente -->
<div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl p-6 text-white shadow-lg text-center mb-6">
    <div class="flex flex-col items-center justify-center">
        <h1 class="text-3xl font-bold text-white mb-2 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
            </svg>
            Nueva Queja
        </h1>
        <p class="text-blue-100">Registra una nueva queja en el sistema</p>
    </div>
</div>

<?php if (session()->has('error')): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
    <strong class="font-bold">Error!</strong>
    <span class="block sm:inline"><?= session('error') ?></span>
</div>
<?php endif; ?>

<?php if (session()->has('errors')): ?>
<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
    <strong class="font-bold">Por favor, corrija los siguientes errores:</strong>
    <ul class="list-disc list-inside">
    <?php foreach (session('errors') as $field => $error): ?>
        <li><?= $error ?></li>
    <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<div class="bg-white rounded-lg shadow-sm p-6">
    <form action="<?= base_url('quejas/create') ?>" method="post" enctype="multipart/form-data" class="space-y-6">
        <?= csrf_field() ?>
        
        <!-- Fecha -->
        <div>
            <label for="fecha" class="block text-sm font-medium text-gray-700">Fecha</label>
            <input type="date" 
                   name="fecha" 
                   id="fecha" 
                   value="<?= old('fecha', date('Y-m-d')) ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   required>
        </div>

        <!-- Sede -->
        <div>
            <label for="sede_id" class="block text-sm font-medium text-gray-700">Sede</label>
            <select name="sede_id" 
                    id="sede_id" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
                <option value="">Seleccione una planta</option>
                <?php foreach ($sedes as $sede): ?>
                    <option value="<?= $sede['id'] ?>" <?= old('sede_id') == $sede['id'] ? 'selected' : '' ?>>
                        <?= esc($sede['nombre']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Insecto -->
        <div>
            <label for="insecto" class="block text-sm font-medium text-gray-700">Tipo de Insecto</label>
            <input type="text" 
                   name="insecto" 
                   id="insecto" 
                   value="<?= old('insecto') ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   required>
        </div>

        <!-- Ubicación -->
        <div>
            <label for="ubicacion" class="block text-sm font-medium text-gray-700">Ubicación</label>
            <input type="text" 
                   name="ubicacion" 
                   id="ubicacion" 
                   value="<?= old('ubicacion') ?>"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   required>
        </div>

        <!-- Líneas -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Líneas Afectadas</label>
            
            <!-- Ubicaciones existentes -->
            <div class="mb-3">
                <label for="lineas_existentes" class="block text-sm font-medium text-gray-600 mb-1">Seleccione una ubicación existente:</label>
                <select id="lineas_existentes" 
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Seleccionar ubicación existente --</option>
                    <?php foreach ($ubicaciones_trampas as $ubicacion): ?>
                        <option value="<?= esc($ubicacion['ubicacion']) ?>">
                            <?= esc($ubicacion['ubicacion']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Separador -->
            <div class="flex items-center py-2">
                <div class="flex-grow border-t border-gray-300"></div>
                <span class="mx-4 text-sm text-gray-500">O</span>
                <div class="flex-grow border-t border-gray-300"></div>
            </div>
            
            <!-- Nueva ubicación -->
            <div>
                <label for="lineas" class="block text-sm font-medium text-gray-600 mb-1">Ingrese una nueva ubicación:</label>
                <input type="text" 
                       name="lineas" 
                       id="lineas" 
                       value="<?= old('lineas') ?>"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       placeholder="Ingrese la ubicación de líneas afectadas"
                       required>
            </div>
        </div>

        <!-- Archivo adjunto -->
        <div>
            <label for="archivo" class="block text-sm font-medium text-gray-700 mb-2">Archivo adjunto (opcional)</label>
            <div class="flex items-center justify-center w-full">
                <label for="archivo" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                        </svg>
                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Haga clic para cargar</span> o arrastre y suelte</p>
                        <p class="text-xs text-gray-500">PDF, PNG, JPG o JPEG (MAX. 5MB)</p>
                    </div>
                    <input id="archivo" name="archivo" type="file" class="hidden" accept=".pdf,.png,.jpg,.jpeg" />
                </label>
            </div>
            <div id="archivo-preview" class="mt-2 hidden">
                <div class="flex items-center p-2 bg-blue-50 border border-blue-200 rounded-lg">
                    <svg class="w-4 h-4 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4z"/>
                        <path d="M8.5 11.5l1-1L12 13H4l4.5-6 2 2.5z"/>
                    </svg>
                    <span id="archivo-name" class="text-sm text-blue-700"></span>
                    <button type="button" id="remove-archivo" class="ml-auto text-blue-500 hover:text-blue-700">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 2a8 8 0 100 16 8 8 0 000-16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Clasificación -->
        <div>
            <label for="clasificacion" class="block text-sm font-medium text-gray-700">Clasificación</label>
            <select name="clasificacion" 
                    id="clasificacion" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
                <option value="">Seleccione una clasificación</option>
                <option value="Crítico" <?= old('clasificacion') == 'Crítico' ? 'selected' : '' ?>>Crítico</option>
                <option value="Alto" <?= old('clasificacion') == 'Alto' ? 'selected' : '' ?>>Alto</option>
                <option value="Medio" <?= old('clasificacion') == 'Medio' ? 'selected' : '' ?>>Medio</option>
                <option value="Bajo" <?= old('clasificacion') == 'Bajo' ? 'selected' : '' ?>>Bajo</option>
            </select>
        </div>

        <!-- Estado -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Estado del Insecto</label>
            <div class="flex gap-4">
                <label class="inline-flex items-center">
                    <input type="radio" 
                           name="estado" 
                           value="Vivo" 
                           <?= old('estado', 'Vivo') == 'Vivo' ? 'checked' : '' ?> 
                           class="form-radio h-4 w-4 text-blue-600" 
                           required>
                    <span class="ml-2 text-gray-700">Vivo</span>
                </label>
                <label class="inline-flex items-center">
                    <input type="radio" 
                           name="estado" 
                           value="Muerto" 
                           <?= old('estado') == 'Muerto' ? 'checked' : '' ?> 
                           class="form-radio h-4 w-4 text-blue-600" 
                           required>
                    <span class="ml-2 text-gray-700">Muerto</span>
                </label>
            </div>
        </div>

        <!-- Estado de la Queja -->
        <div>
            <label for="estado_queja" class="block text-sm font-medium text-gray-700">Estado de la Queja</label>
            <select name="estado_queja" 
                    id="estado_queja" 
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                    required>
                <option value="">Seleccione un estado</option>
                <option value="Pendiente" <?= old('estado_queja') == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
                <option value="Resuelta" <?= old('estado_queja') == 'Resuelta' ? 'selected' : '' ?>>Resuelta</option>
            </select>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end gap-4">
            <a href="<?= site_url('quejas') ?>" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                Cancelar
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                Guardar
            </button>
        </div>
    </form>
</div>

<?php if (session()->getFlashdata('error')): ?>
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
    <?= session()->getFlashdata('error') ?>
</div>
<?php endif; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const lineasExistentes = document.getElementById('lineas_existentes');
    const lineasInput = document.getElementById('lineas');
    
    // Cuando se selecciona una ubicación existente, llenar el input
    lineasExistentes.addEventListener('change', function() {
        if (this.value) {
            lineasInput.value = this.value;
        }
    });
    
    // Cuando se escribe en el input, limpiar la selección
    lineasInput.addEventListener('input', function() {
        if (this.value) {
            lineasExistentes.value = '';
        }
    });
    
    // Si hay un valor guardado (old input), seleccionar la opción correspondiente
    const valorGuardado = lineasInput.value;
    if (valorGuardado) {
        // Buscar si existe en las opciones
        const opciones = lineasExistentes.options;
        for (let i = 0; i < opciones.length; i++) {
            if (opciones[i].value === valorGuardado) {
                lineasExistentes.value = valorGuardado;
                break;
            }
        }
    }
    
    // Manejo del archivo
    const archivoInput = document.getElementById('archivo');
    const archivoPreview = document.getElementById('archivo-preview');
    const archivoName = document.getElementById('archivo-name');
    const removeArchivo = document.getElementById('remove-archivo');
    
    archivoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validar tamaño del archivo (5MB máximo)
            const maxSize = 5 * 1024 * 1024; // 5MB en bytes
            if (file.size > maxSize) {
                alert('El archivo es demasiado grande. El tamaño máximo es 5MB.');
                archivoInput.value = '';
                return;
            }
            
            // Validar tipo de archivo
            const allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                alert('Tipo de archivo no permitido. Solo se permiten archivos PDF, PNG, JPG o JPEG.');
                archivoInput.value = '';
                return;
            }
            
            // Mostrar preview
            archivoName.textContent = file.name;
            archivoPreview.classList.remove('hidden');
        }
    });
    
    removeArchivo.addEventListener('click', function() {
        archivoInput.value = '';
        archivoPreview.classList.add('hidden');
    });
});
</script>

<?= $this->endSection() ?> 