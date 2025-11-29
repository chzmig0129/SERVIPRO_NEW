<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Encabezado con gradiente -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl p-6 text-white shadow-lg text-center mb-6">
        <div class="flex flex-col items-center justify-center">
            <h1 class="text-3xl font-bold text-white mb-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                Gestión de Quejas
            </h1>
            <p class="text-blue-100">Registro y seguimiento de quejas por insectos</p>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex gap-4">
            <a href="<?= site_url('quejas/estadisticas') ?>" 
               class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 3v18h18"></path>
                    <path d="M18 17V9"></path>
                    <path d="M13 17V5"></path>
                    <path d="M8 17v-3"></path>
                </svg>
                Ver Estadísticas
            </a>
            <a href="<?= site_url('quejas/new') ?>" 
               class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Nueva Queja
            </a>
        </div>

        <!-- Filtro por sede -->
        <div class="flex items-center gap-4">
            <form action="<?= site_url('quejas') ?>" method="get" class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <label for="sede_id" class="text-sm font-medium text-gray-700">Filtrar por Plantas:</label>
                    <select name="sede_id" 
                            id="sede_id" 
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todas las plantas</option>
                        <?php foreach ($sedes as $sede): ?>
                            <option value="<?= $sede['id'] ?>" <?= ($sede_seleccionada == $sede['id']) ? 'selected' : '' ?>>
                                <?= esc($sede['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <label for="fecha_inicio" class="text-sm font-medium text-gray-700">Fecha Inicio:</label>
                    <input type="date" 
                           name="fecha_inicio" 
                           id="fecha_inicio"
                           value="<?= $fecha_inicio ?? '' ?>"
                           class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="flex items-center gap-2">
                    <label for="fecha_fin" class="text-sm font-medium text-gray-700">Fecha Fin:</label>
                    <input type="date" 
                           name="fecha_fin" 
                           id="fecha_fin"
                           value="<?= $fecha_fin ?? '' ?>"
                           class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="flex items-center gap-2">
                    <label for="estado_queja" class="text-sm font-medium text-gray-700">Estado de la Queja:</label>
                    <select name="estado_queja" 
                            id="estado_queja" 
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todos</option>
                        <option value="Pendiente" <?= ($estado_queja_seleccionado == 'Pendiente') ? 'selected' : '' ?>>Pendiente</option>
                        <option value="Resuelta" <?= ($estado_queja_seleccionado == 'Resuelta') ? 'selected' : '' ?>>Resuelta</option>
                    </select>
                </div>

                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    Filtrar
                </button>

                <?php if(isset($sede_seleccionada) || isset($fecha_inicio) || isset($fecha_fin) || isset($estado_queja_seleccionado)): ?>
                    <a href="<?= site_url('quejas') ?>" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Limpiar
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Tabla de Quejas -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sede</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Insecto</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clasificación</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado de la Queja</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Líneas</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivo</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($quejas)): ?>
                        <?php foreach ($quejas as $queja): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $queja['id'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d/m/Y', strtotime($queja['fecha'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($queja['nombre_sede']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($queja['insecto']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($queja['ubicacion']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php
                                        switch($queja['clasificacion']) {
                                            case 'Crítico':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                            case 'Alto':
                                                echo 'bg-orange-100 text-orange-800';
                                                break;
                                            case 'Medio':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'Bajo':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= esc($queja['clasificacion']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php
                                        switch($queja['estado'] ?? '') {
                                            case 'Vivo':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            case 'Muerto':
                                                echo 'bg-red-100 text-red-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= esc($queja['estado'] ?? 'No especificado') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?php
                                        switch($queja['estado_queja'] ?? '') {
                                            case 'Pendiente':
                                                echo 'bg-yellow-100 text-yellow-800';
                                                break;
                                            case 'Resuelta':
                                                echo 'bg-green-100 text-green-800';
                                                break;
                                            default:
                                                echo 'bg-gray-100 text-gray-800';
                                        }
                                        ?>">
                                        <?= esc($queja['estado_queja'] ?? 'No especificado') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($queja['lineas']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?php if (!empty($queja['archivo'])): ?>
                                        <a href="<?= base_url('uploads/quejas/' . $queja['archivo']) ?>" 
                                           target="_blank" 
                                           class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full hover:bg-blue-200">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                                            </svg>
                                            Ver archivo
                                        </a>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">Sin archivo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-3">
                                        <a href="<?= site_url('quejas/edit/' . $queja['id']) ?>" 
                                           class="text-blue-600 hover:text-blue-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                            </svg>
                                        </a>
                                        
                                        <button type="button" 
                                               class="cambiar-estado-queja text-<?= ($queja['estado_queja'] === 'Pendiente') ? 'green' : 'yellow' ?>-600 hover:text-<?= ($queja['estado_queja'] === 'Pendiente') ? 'green' : 'yellow' ?>-800"
                                               data-queja-id="<?= $queja['id'] ?>"
                                               data-estado-actual="<?= $queja['estado_queja'] ?>"
                                               data-nuevo-estado="<?= ($queja['estado_queja'] === 'Pendiente') ? 'Resuelta' : 'Pendiente' ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                        
                                        <a href="<?= site_url('quejas/delete/' . $queja['id']) ?>" 
                                           class="text-red-600 hover:text-red-800"
                                           onclick="return confirm('¿Está seguro de que desea eliminar esta queja?');">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="px-6 py-4 text-center text-gray-500">
                                No hay quejas registradas
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Scripts para DataTables -->
<script>
    $(document).ready(function() {
        $('table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
            },
            "pageLength": 10,
            "order": [[0, "desc"]]
        });
    });
</script>

<?php if (session()->getFlashdata('success')): ?>
<div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
    <?= session()->getFlashdata('success') ?>
</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
<div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
    <?= session()->getFlashdata('error') ?>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Manejar el cambio de estado de quejas
    const botonesEstadoQueja = document.querySelectorAll('.cambiar-estado-queja');
    botonesEstadoQueja.forEach(boton => {
        boton.addEventListener('click', function() {
            const quejaId = this.dataset.quejaId;
            const estadoActual = this.dataset.estadoActual;
            const nuevoEstado = this.dataset.nuevoEstado;
            
            if (confirm(`¿Desea cambiar el estado de la queja de "${estadoActual}" a "${nuevoEstado}"?`)) {
                // Mostrar indicador de procesamiento
                const svgOriginal = this.innerHTML;
                this.innerHTML = '<svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>';
                
                // Preparar datos para enviar
                const formData = new FormData();
                formData.append('queja_id', quejaId);
                formData.append('estado_queja', nuevoEstado);
                
                // Enviar solicitud
                fetch('<?= base_url('quejas/actualizar-estado') ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Actualizar la interfaz
                        this.dataset.estadoActual = nuevoEstado;
                        this.dataset.nuevoEstado = estadoActual;
                        
                        // Cambiar el color del botón
                        if (nuevoEstado === 'Pendiente') {
                            this.className = this.className.replace('green', 'yellow');
                        } else {
                            this.className = this.className.replace('yellow', 'green');
                        }
                        
                        // Actualizar la etiqueta de estado en la fila
                        const fila = this.closest('tr');
                        const celdaEstado = fila.querySelector('td:nth-child(8) span');
                        
                        if (celdaEstado) {
                            if (nuevoEstado === 'Pendiente') {
                                celdaEstado.className = celdaEstado.className.replace('green-100 text-green-800', 'yellow-100 text-yellow-800');
                            } else {
                                celdaEstado.className = celdaEstado.className.replace('yellow-100 text-yellow-800', 'green-100 text-green-800');
                            }
                            celdaEstado.textContent = nuevoEstado;
                        }
                        
                        // Restaurar el botón original con un icono de éxito
                        this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';
                        
                        // Mostrar mensaje de éxito
                        alert(data.message);
                    } else {
                        // Mostrar mensaje de error
                        alert('Error: ' + data.message);
                        this.innerHTML = svgOriginal;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al procesar la solicitud');
                    this.innerHTML = svgOriginal;
                });
            }
        });
    });
});
</script>
<?= $this->endSection() ?> 