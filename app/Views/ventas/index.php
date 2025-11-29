<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Encabezado con gradiente -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl p-6 text-white shadow-lg text-center mb-6">
        <div class="flex flex-col items-center justify-center">
            <h1 class="text-3xl font-bold text-white mb-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                Gestión de Ventas
            </h1>
            <p class="text-blue-100">Registro y seguimiento de ventas y transacciones</p>
        </div>
    </div>

    <!-- Botones de acción y filtros -->
    <div class="flex flex-col md:flex-row justify-between items-start gap-4 mb-6">
        <div class="flex gap-4">
            <a href="<?= site_url('ventas/estadisticas') ?>" 
               class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 3v18h18"></path>
                    <path d="M18 17V9"></path>
                    <path d="M13 17V5"></path>
                    <path d="M8 17v-3"></path>
                </svg>
                Ver Estadísticas
            </a>
            <a href="<?= site_url('ventas/new') ?>" 
               class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Nueva Venta
            </a>
        </div>

        <!-- Filtros -->
        <div class="w-full md:w-auto bg-white rounded-lg shadow-sm p-4">
            <form action="<?= site_url('ventas') ?>" method="get" class="flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <label for="sede_id" class="text-sm font-medium text-gray-700">Filtrar por Sede:</label>
                    <select name="sede_id" 
                            id="sede_id" 
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Todas las sedes</option>
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

                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    Filtrar
                </button>

                <?php if(isset($sede_seleccionada) || isset($fecha_inicio) || isset($fecha_fin)): ?>
                    <a href="<?= site_url('ventas') ?>" 
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

    <!-- Tabla de Ventas -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sede</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Concepto</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Monto</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($ventas)): ?>
                        <?php foreach ($ventas as $venta): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $venta['id'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('d/m/Y', strtotime($venta['fecha'])) ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($venta['nombre_sede']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= esc($venta['concepto']) ?></td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    <?= !empty($venta['descripcion']) ? esc($venta['descripcion']) : '<span class="text-gray-400 italic">Sin descripción</span>' ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">$<?= number_format($venta['monto'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= isset($venta['nombre_usuario']) ? esc($venta['nombre_usuario']) : 'Usuario #' . $venta['usuario_id'] ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center gap-3">
                                        <a href="<?= site_url('ventas/edit/' . $venta['id']) ?>" 
                                           class="text-blue-600 hover:text-blue-800">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                            </svg>
                                        </a>
                                        <a href="<?= site_url('ventas/delete/' . $venta['id']) ?>" 
                                           class="text-red-600 hover:text-red-800"
                                           onclick="return confirm('¿Está seguro de que desea eliminar esta venta?');">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                <line x1="10" y1="11" x2="10" y2="17"></line>
                                                <line x1="14" y1="11" x2="14" y2="17"></line>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                                No hay ventas registradas
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