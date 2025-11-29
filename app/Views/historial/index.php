<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Historial de Movimientos</h1>
        <a href="<?= base_url('blueprints/viewplano/' . $plano_id) ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
            Volver al Plano
        </a>
    </div>

    <!-- Formulario de Filtros -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <form method="get" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="tipo_trampa" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Trampa</label>
                <select name="tipo_trampa" id="tipo_trampa" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <option value="">Todos los tipos</option>
                    <?php foreach ($tipos_trampa as $tipo): ?>
                        <option value="<?= $tipo['tipo'] ?>" <?= ($filtros['tipo_trampa'] == $tipo['tipo']) ? 'selected' : '' ?>>
                            <?= $tipo['tipo'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" id="fecha_inicio" 
                       value="<?= $filtros['fecha_inicio'] ?>"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin" 
                       value="<?= $filtros['fecha_fin'] ?>"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2">
                    Filtrar
                </button>
                <a href="<?= base_url('historial/index/' . $plano_id) ?>" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                    Limpiar
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow-md rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Trampa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zona Anterior</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zona Nueva</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coordenadas Anteriores</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Coordenadas Nuevas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Comentario</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (!empty($movimientos)): ?>
                    <?php foreach ($movimientos as $movimiento): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $movimiento['id_trampa'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $movimiento['tipo'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $movimiento['zona_anterior'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= $movimiento['zona_nueva'] ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                X: <?= number_format($movimiento['x_anterior'], 2) ?>, 
                                Y: <?= number_format($movimiento['y_anterior'], 2) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                X: <?= number_format($movimiento['x_nueva'], 2) ?>, 
                                Y: <?= number_format($movimiento['y_nueva'], 2) ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?= date('d/m/Y H:i:s', strtotime($movimiento['fecha_movimiento'])) ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                <?= $movimiento['comentario'] ?: '<span class="text-gray-400 italic">Sin comentario</span>' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                            No hay movimientos registrados para este plano.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?> 