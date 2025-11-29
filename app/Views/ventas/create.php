<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h1 class="text-2xl font-bold text-gray-800 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Registrar Nueva Venta
        </h1>
        <a href="<?= site_url('ventas') ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium px-4 py-2 rounded-lg transition-colors duration-200 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Volver al Listado
        </a>
    </div>
    
    <?php if (session()->has('error')): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p><?= session('error') ?></p>
        </div>
    <?php endif; ?>
    
    <div class="bg-white shadow-sm rounded-lg p-6">
        <form action="<?= site_url('ventas/create') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Fecha -->
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700 mb-1">Fecha <span class="text-red-500">*</span></label>
                    <input 
                        type="date" 
                        id="fecha" 
                        name="fecha" 
                        value="<?= set_value('fecha', date('Y-m-d')) ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                    <?php if (isset(session('errors')['fecha'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors')['fecha'] ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Sede -->
                <div>
                    <label for="sede_id" class="block text-sm font-medium text-gray-700 mb-1">Sede <span class="text-red-500">*</span></label>
                    <select 
                        id="sede_id" 
                        name="sede_id" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        <option value="">Seleccionar Sede</option>
                        <?php foreach ($sedes as $sede): ?>
                            <option value="<?= $sede['id'] ?>" <?= set_select('sede_id', $sede['id']) ?>>
                                <?= esc($sede['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset(session('errors')['sede_id'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors')['sede_id'] ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Concepto -->
                <div>
                    <label for="concepto" class="block text-sm font-medium text-gray-700 mb-1">Concepto <span class="text-red-500">*</span></label>
                    <input 
                        type="text" 
                        id="concepto" 
                        name="concepto" 
                        value="<?= set_value('concepto') ?>" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Pago de servicio, compra, etc."
                        required
                    >
                    <?php if (isset(session('errors')['concepto'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors')['concepto'] ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Monto -->
                <div>
                    <label for="monto" class="block text-sm font-medium text-gray-700 mb-1">Monto <span class="text-red-500">*</span></label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">$</span>
                        </div>
                        <input 
                            type="number" 
                            id="monto" 
                            name="monto" 
                            value="<?= set_value('monto') ?>" 
                            class="block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="0.00"
                            step="0.01"
                            min="0.01"
                            required
                        >
                    </div>
                    <?php if (isset(session('errors')['monto'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors')['monto'] ?></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Usuario -->
                <div>
                    <label for="usuario_id" class="block text-sm font-medium text-gray-700 mb-1">Usuario <span class="text-red-500">*</span></label>
                    <select 
                        id="usuario_id" 
                        name="usuario_id" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        required
                    >
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?= $usuario['id'] ?>" <?= set_select('usuario_id', $usuario['id'], ($usuario['id'] == 1)) ?>>
                                <?= esc($usuario['nombre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset(session('errors')['usuario_id'])): ?>
                        <p class="mt-1 text-sm text-red-600"><?= session('errors')['usuario_id'] ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Descripción (opcional) -->
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-1">Descripción (opcional)</label>
                    <textarea 
                        id="descripcion" 
                        name="descripcion" 
                        rows="3" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="Detalles adicionales sobre la venta..."
                    ><?= set_value('descripcion') ?></textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <button 
                    type="submit" 
                    class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Guardar Venta
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?> 