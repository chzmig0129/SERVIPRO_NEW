<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6 max-w-7xl mx-auto px-4">
    
    <!-- Encabezado -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl p-6 text-white shadow-lg mb-6">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-white mb-2">Repositorio de Planes de Acción y Documentos</h1>
                <?php if(!empty($sedeSeleccionadaNombre)): ?>
                    <p class="text-blue-100">Planta: <?= esc($sedeSeleccionadaNombre) ?></p>
                <?php endif; ?>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="<?= base_url('locations') ?>?sede_id=<?= $sedeSeleccionada ?>" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-white text-blue-700 rounded-lg hover:bg-blue-50 transition-colors font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver a Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Selector de planta (si hay múltiples plantas) -->
    <?php if(!empty($sedes) && count($sedes) > 1): ?>
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 border border-gray-200">
        <label for="sede-selector" class="block text-sm font-medium text-gray-700 mb-2">Seleccionar Planta:</label>
        <select id="sede-selector" name="sede_id" 
                class="w-full md:w-64 p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                onchange="cambiarSede(this.value)">
            <?php foreach($sedes as $sede): ?>
                <option value="<?= $sede['id'] ?>" <?= ($sedeSeleccionada == $sede['id']) ? 'selected' : '' ?>>
                    <?= esc($sede['nombre']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>

    <!-- Mensaje si no hay sede seleccionada -->
    <?php if(empty($sedeSeleccionada)): ?>
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">Por favor, selecciona una planta para ver su repositorio de documentos.</p>
            </div>
        </div>
    </div>
    <?php else: ?>

    <!-- Botones de acción -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 border border-gray-200">
        <div class="flex flex-col sm:flex-row gap-3 justify-between items-center">
            <div class="flex gap-3">
                <button onclick="mostrarModalSubir()" 
                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Subir Documento
                </button>
                
                <!-- Filtros -->
                <div class="flex gap-2">
                    <select id="filtro-tipo" 
                            class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            onchange="filtrarDocumentos()">
                        <option value="">Todos los tipos</option>
                        <option value="plan_accion">Plan de Acción</option>
                        <option value="documento">Documento</option>
                        <option value="reporte">Reporte</option>
                        <option value="otro">Otro</option>
                    </select>
                    
                    <input type="text" 
                           id="buscar-documento" 
                           placeholder="Buscar documento..."
                           class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                           onkeyup="filtrarDocumentos()">
                </div>
            </div>
            
            <div class="text-sm text-gray-600">
                <span id="total-documentos">0</span> documento(s) encontrado(s)
            </div>
        </div>
    </div>

    <!-- Lista de documentos -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-6">
            <div id="lista-documentos" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Los documentos se cargarán aquí dinámicamente -->
                <?php 
                // Por ahora, datos de ejemplo - se reemplazarán con datos reales del backend
                $documentos = isset($documentos) ? $documentos : [];
                
                if(empty($documentos)): ?>
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-lg font-medium mb-2">No hay documentos disponibles</p>
                        <p class="text-sm">Comienza subiendo tu primer documento</p>
                    </div>
                <?php else: ?>
                    <?php foreach($documentos as $documento): ?>
                        <div class="documento-card bg-gray-50 rounded-lg p-4 border border-gray-200 hover:shadow-md transition-shadow" 
                             data-tipo="<?= esc($documento['tipo'] ?? '') ?>"
                             data-titulo="<?= esc(strtolower($documento['titulo'] ?? '')) ?>"
                             data-descripcion="<?= esc(strtolower($documento['descripcion'] ?? '')) ?>">
                            <div class="flex items-start justify-between mb-3">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-800 mb-1">
                                        <?= esc($documento['titulo'] ?? 'Sin título') ?>
                                    </h3>
                                    <p class="text-xs text-gray-500 mb-2">
                                        <?php 
                                        // Mapear tipos de documentos a etiquetas más amigables
                                        $tiposLabels = [
                                            'plan_accion' => 'Plan de Acción',
                                            'documento' => 'Documento',
                                            'reporte' => 'Reporte',
                                            'otro' => 'Otro'
                                        ];
                                        $tipoMostrar = isset($tiposLabels[$documento['tipo']]) ? $tiposLabels[$documento['tipo']] : $documento['tipo'];
                                        ?>
                                        <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 rounded">
                                            <?= esc($tipoMostrar ?? 'Documento') ?>
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-400 mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <?= !empty($documento['created_at']) ? date('d/m/Y H:i', strtotime($documento['created_at'])) : 'Sin fecha' ?>
                                    </p>
                                    <?php if(!empty($documento['descripcion'])): ?>
                                        <p class="text-sm text-gray-600 line-clamp-2">
                                            <?= esc($documento['descripcion']) ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                <div class="ml-3">
                                    <?php 
                                    $extension = !empty($documento['nombre_archivo']) ? strtolower(pathinfo($documento['nombre_archivo'], PATHINFO_EXTENSION)) : '';
                                    $icono = 'file';
                                    $color = 'text-gray-500';
                                    
                                    if(in_array($extension, ['pdf'])) {
                                        $icono = 'file-pdf';
                                        $color = 'text-red-500';
                                    } elseif(in_array($extension, ['doc', 'docx'])) {
                                        $icono = 'file-word';
                                        $color = 'text-blue-500';
                                    } elseif(in_array($extension, ['xls', 'xlsx'])) {
                                        $icono = 'file-excel';
                                        $color = 'text-green-500';
                                    } elseif(in_array($extension, ['ppt', 'pptx'])) {
                                        $icono = 'file-presentation';
                                        $color = 'text-orange-500';
                                    } elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                                        $icono = 'file-image';
                                        $color = 'text-purple-500';
                                    }
                                    ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 <?= $color ?>" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            </div>
                            
                            <div class="flex gap-2 mt-4">
                                <?php if(!empty($documento['ruta_archivo'])): ?>
                                    <a href="<?= base_url($documento['ruta_archivo']) ?>" 
                                       target="_blank"
                                       class="flex-1 text-center px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-sm font-medium">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Ver
                                    </a>
                                    <a href="<?= base_url('repositorio/descargar/' . $documento['id']) ?>" 
                                       class="flex-1 text-center px-3 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 transition-colors text-sm font-medium">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Descargar
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php endif; ?>
</div>

<!-- Modal para subir documento -->
<div id="modal-subir" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900">Subir Documento</h3>
                <button onclick="cerrarModalSubir()" class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            
            <!-- Mensajes de éxito/error -->
            <div id="mensaje-subida" class="hidden mb-4 p-3 rounded"></div>
            
            <form id="form-subir-documento" enctype="multipart/form-data">
                <input type="hidden" name="sede_id" id="form-sede-id" value="<?= $sedeSeleccionada ?? '' ?>">
                
                <div class="space-y-4">
                    <!-- Título -->
                    <div>
                        <label for="titulo" class="block text-sm font-medium text-gray-700 mb-1">
                            Título <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="titulo" 
                               name="titulo" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Ingresa un título descriptivo para el documento</p>
                    </div>
                    
                    <!-- Tipo -->
                    <div>
                        <label for="tipo" class="block text-sm font-medium text-gray-700 mb-1">
                            Tipo de Documento <span class="text-red-500">*</span>
                        </label>
                        <select id="tipo" 
                                name="tipo" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Selecciona un tipo</option>
                            <option value="plan_accion">Plan de Acción</option>
                            <option value="documento">Documento</option>
                            <option value="reporte">Reporte</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                    
                    <!-- Descripción -->
                    <div>
                        <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">
                            Descripción (Opcional)
                        </label>
                        <textarea id="descripcion" 
                                  name="descripcion" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                    
                    <!-- Fecha del Documento -->
                    <div>
                        <label for="fecha_documento" class="block text-sm font-medium text-gray-700 mb-1">
                            Fecha del Documento (Opcional)
                        </label>
                        <input type="datetime-local" 
                               id="fecha_documento" 
                               name="fecha_documento"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <!-- Archivo -->
                    <div>
                        <label for="archivo" class="block text-sm font-medium text-gray-700 mb-1">
                            Archivo <span class="text-red-500">*</span>
                        </label>
                        <input type="file" 
                               id="archivo" 
                               name="archivo" 
                               required
                               accept=".pdf,.doc,.docx,.ppt,.pptx"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Formatos permitidos: PDF, DOC, DOCX, PPT, PPTX (Máx. 50MB)</p>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3 mt-6">
                    <button type="button" 
                            onclick="cerrarModalSubir()" 
                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors font-medium">
                        Cancelar
                    </button>
                    <button type="submit" 
                            id="btn-subir"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                        <span id="btn-subir-texto">Subir Documento</span>
                        <span id="btn-subir-cargando" class="hidden">
                            <svg class="animate-spin h-4 w-4 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Subiendo...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function cambiarSede(sedeId) {
    if(sedeId) {
        window.location.href = '<?= base_url('repositorio') ?>?sede_id=' + sedeId;
    }
}

function mostrarModalSubir() {
    document.getElementById('modal-subir').classList.remove('hidden');
    // Limpiar formulario
    document.getElementById('form-subir-documento').reset();
    document.getElementById('mensaje-subida').classList.add('hidden');
}

function cerrarModalSubir() {
    document.getElementById('modal-subir').classList.add('hidden');
}

function filtrarDocumentos() {
    const filtroTipo = document.getElementById('filtro-tipo').value.toLowerCase();
    const buscarTexto = document.getElementById('buscar-documento').value.toLowerCase();
    const documentos = document.querySelectorAll('.documento-card');
    let totalVisible = 0;
    
    documentos.forEach(doc => {
        const tipo = doc.getAttribute('data-tipo').toLowerCase();
        const titulo = doc.getAttribute('data-titulo');
        const descripcion = doc.getAttribute('data-descripcion') || '';
        
        const coincideTipo = !filtroTipo || tipo.includes(filtroTipo);
        const coincideTexto = !buscarTexto || titulo.includes(buscarTexto) || descripcion.includes(buscarTexto);
        
        if(coincideTipo && coincideTexto) {
            doc.style.display = 'block';
            totalVisible++;
        } else {
            doc.style.display = 'none';
        }
    });
    
    document.getElementById('total-documentos').textContent = totalVisible;
}

// Manejar envío del formulario
document.addEventListener('DOMContentLoaded', function() {
    const total = document.querySelectorAll('.documento-card').length;
    document.getElementById('total-documentos').textContent = total;
    
    const form = document.getElementById('form-subir-documento');
    if(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            const btnSubir = document.getElementById('btn-subir');
            const btnTexto = document.getElementById('btn-subir-texto');
            const btnCargando = document.getElementById('btn-subir-cargando');
            const mensaje = document.getElementById('mensaje-subida');
            
            // Mostrar estado de carga
            btnSubir.disabled = true;
            btnTexto.classList.add('hidden');
            btnCargando.classList.remove('hidden');
            mensaje.classList.add('hidden');
            
            // Enviar formulario
            fetch('<?= base_url('repositorio/subir') ?>', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    mensaje.className = 'mb-4 p-3 rounded bg-green-100 border border-green-400 text-green-700';
                    mensaje.textContent = data.message || 'Documento subido correctamente';
                    mensaje.classList.remove('hidden');
                    
                    // Limpiar formulario
                    form.reset();
                    
                    // Recargar página después de 1.5 segundos
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    let mensajeError = data.message || 'Error al subir el documento';
                    if(data.errors) {
                        const errores = Object.values(data.errors).join(', ');
                        mensajeError += ': ' + errores;
                    }
                    mensaje.className = 'mb-4 p-3 rounded bg-red-100 border border-red-400 text-red-700';
                    mensaje.textContent = mensajeError;
                    mensaje.classList.remove('hidden');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mensaje.className = 'mb-4 p-3 rounded bg-red-100 border border-red-400 text-red-700';
                mensaje.textContent = 'Error de conexión. Por favor, intenta nuevamente.';
                mensaje.classList.remove('hidden');
            })
            .finally(() => {
                // Restaurar botón
                btnSubir.disabled = false;
                btnTexto.classList.remove('hidden');
                btnCargando.classList.add('hidden');
            });
        });
    }
});
</script>

<?= $this->endSection() ?>

