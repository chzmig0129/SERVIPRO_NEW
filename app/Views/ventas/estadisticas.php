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
                Estadísticas de Ventas
            </h1>
            <p class="text-blue-100">
                <?php if (!empty($nombre_sede_seleccionada)): ?>
                    Estadísticas para la sede: <?= esc($nombre_sede_seleccionada) ?>
                <?php else: ?>
                    Estadísticas globales de todas las sedes
                <?php endif; ?>
            </p>
        </div>
    </div>

    <!-- Barra de navegación -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6 flex justify-between">
        <a href="<?= site_url('ventas') ?>" 
           class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Volver a Ventas
        </a>
        
        <div class="flex items-center gap-4">
            <a href="<?= site_url('reportes/pdf_optimizado/ventas') . (isset($_GET['sede_id']) ? '?sede_id=' . $_GET['sede_id'] : '') ?>" 
               class="bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <line x1="10" y1="9" x2="8" y2="9"></line>
                </svg>
                PDF Optimizado
            </a>
            
            <a href="<?= site_url('ventas/estadisticas/pdf_con_graficas') . (isset($_GET['sede_id']) ? '?sede_id=' . $_GET['sede_id'] : '') ?>" 
               class="bg-purple-600 hover:bg-purple-700 text-white font-medium px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <rect x="8" y="12" width="8" height="6" rx="1"></rect>
                    <line x1="10" y1="9" x2="8" y2="9"></line>
                </svg>
                PDF con Gráficas
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <form action="<?= site_url('ventas/estadisticas') ?>" method="get" class="flex items-center gap-4">
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
                <a href="<?= site_url('ventas/estadisticas') ?>" 
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

    <!-- Estadísticas generales -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <?php
        // Calcular el total general de ventas
        $totalVentas = 0;
        $importeTotal = 0;
        
        if (!empty($estadisticasAnuales)) {
            foreach ($estadisticasAnuales as $estadistica) {
                $totalVentas += $estadistica['total'];
                $importeTotal += $estadistica['importe_total'];
            }
        }
        ?>
        
        <!-- Total de ventas registradas -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total de Ventas</h3>
                    <p class="text-2xl font-bold text-gray-900 mt-1"><?= number_format($totalVentas) ?></p>
                </div>
                <div class="bg-blue-100 p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500">Todas las ventas registradas</span>
            </div>
        </div>

        <!-- Importe total -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Importe Total</h3>
                    <p class="text-2xl font-bold text-gray-900 mt-1">$<?= number_format($importeTotal, 2) ?></p>
                </div>
                <div class="bg-green-100 p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500">Suma de todos los importes</span>
            </div>
        </div>

        <!-- Promedio por venta -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Promedio por Venta</h3>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        $<?= $totalVentas > 0 ? number_format($importeTotal / $totalVentas, 2) : '0.00' ?>
                    </p>
                </div>
                <div class="bg-purple-100 p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-purple-600">
                        <polygon points="12 2 2 7 12 12 22 7 12 2"></polygon>
                        <polyline points="2 17 12 22 22 17"></polyline>
                        <polyline points="2 12 12 17 22 12"></polyline>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500">Importe promedio por venta</span>
            </div>
        </div>

        <!-- Ventas anuales -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Ventas este Año</h3>
                    <p class="text-2xl font-bold text-gray-900 mt-1">
                        <?php 
                        $ventasAnioActual = 0;
                        $anioActual = date('Y');
                        if (!empty($estadisticasAnuales)) {
                            foreach ($estadisticasAnuales as $estadistica) {
                                if ($estadistica['año'] == $anioActual) {
                                    $ventasAnioActual = $estadistica['total'];
                                    break;
                                }
                            }
                        }
                        echo number_format($ventasAnioActual);
                        ?>
                    </p>
                </div>
                <div class="bg-yellow-100 p-2 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-yellow-600">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-500">Total de ventas del año actual</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Productos más vendidos -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Conceptos más Frecuentes</h3>
            <?php if (!empty($estadisticasConceptos)): ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Concepto</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ocurrencias</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Importe Total</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($estadisticasConceptos as $estadistica): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($estadistica['concepto']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= number_format($estadistica['frecuencia']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?= number_format($estadistica['importe_total'], 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-center py-4">No hay datos disponibles.</p>
            <?php endif; ?>
        </div>

        <!-- Ventas por sede (solo si no hay filtro) -->
        <?php if (empty($sede_seleccionada) && !empty($estadisticasPorSede)): ?>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ventas por Sede</h3>
                <canvas id="ventasPorSede" height="300"></canvas>
            </div>
        <?php endif; ?>
    </div>

    <!-- Ventas por Sede (solo si no hay filtro) - Tabla detallada -->
    <?php if (empty($sede_seleccionada) && !empty($estadisticasPorSede)): ?>
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Detalle de Ventas por Sede</h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sede</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ventas</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Importe Total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio por Venta</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($estadisticasPorSede as $estadistica): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($estadistica['sede']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= number_format($estadistica['total_ventas']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?= number_format($estadistica['importe_total'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    $<?= $estadistica['total_ventas'] > 0 ? number_format($estadistica['importe_total'] / $estadistica['total_ventas'], 2) : '0.00' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Ventas por Usuario -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Gráfico de Ventas por Usuario -->
        <?php if (!empty($estadisticasPorUsuario)): ?>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ventas por Usuario</h3>
                <canvas id="ventasPorUsuario" height="300"></canvas>
            </div>
        
            <!-- Detalle de Ventas por Usuario -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Detalle de Ventas por Usuario</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ventas</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Importe Total</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio por Venta</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($estadisticasPorUsuario as $estadistica): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= esc($estadistica['usuario']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= number_format($estadistica['total_ventas']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?= number_format($estadistica['importe_total'], 2) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        $<?= $estadistica['total_ventas'] > 0 ? number_format($estadistica['importe_total'] / $estadistica['total_ventas'], 2) : '0.00' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-sm p-6 md:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Ventas por Usuario</h3>
                <p class="text-gray-500 text-center py-4">No hay datos disponibles.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Ventas Anuales -->
    <div class="bg-white rounded-lg shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Ventas por Año</h3>
        <?php if (!empty($estadisticasAnuales)): ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Año</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Ventas</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Importe Total</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Promedio por Venta</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($estadisticasAnuales as $estadistica): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $estadistica['año'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= number_format($estadistica['total']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">$<?= number_format($estadistica['importe_total'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    $<?= $estadistica['total'] > 0 ? number_format($estadistica['importe_total'] / $estadistica['total'], 2) : '0.00' ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray-500 text-center py-4">No hay datos disponibles.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Añadir Chart.js para las gráficas -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gráfico de ventas por sede (si está disponible)
        <?php if (empty($sede_seleccionada) && !empty($estadisticasPorSede)): ?>
            <?php 
            $sedes = [];
            $totalesSede = [];
            $importesSede = [];
            $colores = [
                'rgba(59, 130, 246, 0.7)', 
                'rgba(16, 185, 129, 0.7)', 
                'rgba(139, 92, 246, 0.7)', 
                'rgba(245, 158, 11, 0.7)',
                'rgba(236, 72, 153, 0.7)',
                'rgba(239, 68, 68, 0.7)',
                'rgba(37, 99, 235, 0.7)'
            ];
            
            foreach ($estadisticasPorSede as $i => $estadistica) {
                $sedes[] = $estadistica['sede'];
                $totalesSede[] = $estadistica['total_ventas'];
                $importesSede[] = $estadistica['importe_total'];
            }
            ?>
            
            const ctxSede = document.getElementById('ventasPorSede').getContext('2d');
            const ventasPorSede = new Chart(ctxSede, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($sedes) ?>,
                    datasets: [{
                        label: 'Total Ventas',
                        data: <?= json_encode($totalesSede) ?>,
                        backgroundColor: <?= json_encode($colores) ?>,
                        borderColor: 'rgba(255, 255, 255, 0.7)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = 'Ventas: ' + context.raw;
                                    let importe = <?= json_encode($importesSede) ?>[context.dataIndex];
                                    return [label, 'Importe: $' + parseFloat(importe).toLocaleString('es-MX')];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Número de Ventas'
                            }
                        }
                    }
                }
            });
        <?php endif; ?>

        // Gráfico de ventas por usuario (si está disponible)
        <?php if (!empty($estadisticasPorUsuario)): ?>
            <?php 
            $usuarios = [];
            $totalesUsuario = [];
            $importesUsuario = [];
            $coloresUsuario = [
                'rgba(59, 130, 246, 0.7)', 
                'rgba(16, 185, 129, 0.7)', 
                'rgba(139, 92, 246, 0.7)', 
                'rgba(245, 158, 11, 0.7)',
                'rgba(236, 72, 153, 0.7)',
                'rgba(239, 68, 68, 0.7)',
                'rgba(37, 99, 235, 0.7)'
            ];
            
            foreach ($estadisticasPorUsuario as $i => $estadistica) {
                $usuarios[] = $estadistica['usuario'];
                $totalesUsuario[] = $estadistica['total_ventas'];
                $importesUsuario[] = $estadistica['importe_total'];
            }
            ?>
            
            const ctxUsuario = document.getElementById('ventasPorUsuario').getContext('2d');
            const ventasPorUsuario = new Chart(ctxUsuario, {
                type: 'bar',
                data: {
                    labels: <?= json_encode($usuarios) ?>,
                    datasets: [{
                        label: 'Total Ventas',
                        data: <?= json_encode($totalesUsuario) ?>,
                        backgroundColor: <?= json_encode($coloresUsuario) ?>,
                        borderColor: 'rgba(255, 255, 255, 0.7)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = 'Ventas: ' + context.raw;
                                    let importe = <?= json_encode($importesUsuario) ?>[context.dataIndex];
                                    return [label, 'Importe: $' + parseFloat(importe).toLocaleString('es-MX')];
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Número de Ventas'
                            }
                        }
                    }
                }
            });
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?> 