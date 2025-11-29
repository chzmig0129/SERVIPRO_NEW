<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="space-y-8">
    <!-- Header with gradient background and centered content -->
    <div class="bg-gradient-to-r from-blue-500 to-blue-700 rounded-xl p-6 text-white shadow-lg text-center">
        <div class="flex flex-col items-center justify-center mb-4">
            <h1 class="text-3xl font-bold text-white mb-2 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-3">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
                Panel Principal
            </h1>
            <p class="text-blue-100 mt-1 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                    <circle cx="12" cy="10" r="3"></circle>
                </svg>
                Gestión de plantas y ubicaciones     
            </p>
            
            <button 
                onclick="sedeModal.show()"
                class="mt-4 flex items-center justify-center bg-white text-blue-700 hover:bg-blue-50 px-5 py-2.5 rounded-lg transition-all duration-200 shadow-sm font-medium"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                    <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                    <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                    <path d="M10 6h4"/>
                    <path d="M10 10h4"/>
                    <path d="M10 14h4"/>
                    <path d="M10 18h4"/>
                </svg>
                Agregar planta
            </button>
        </div>
    </div>

    <!-- Stats summary -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow duration-200 group">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-lg mr-4 group-hover:bg-blue-200 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600">
                        <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                        <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                        <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Plantas</h3>
                    <p class="text-3xl font-bold mt-1"><?= count($sedes) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow duration-200 group">
            <div class="flex items-center">
                <div class="bg-green-100 p-3 rounded-lg mr-4 group-hover:bg-green-200 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="3" y1="9" x2="21" y2="9"></line>
                        <line x1="9" y1="21" x2="9" y2="9"></line>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Planos</h3>
                    <p class="text-3xl font-bold mt-1"><?= array_sum(array_column($sedes, 'total_planos')) ?></p>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border p-6 hover:shadow-md transition-shadow duration-200 group">
            <div class="flex items-center mb-4">
                <div class="bg-amber-100 p-3 rounded-lg mr-4 group-hover:bg-amber-200 transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600">
                        <path d="M18 8a6 6 0 0 0-9.33-5"></path>
                        <path d="m10.67 5.8-.67.2"></path>
                        <path d="M6 8a6 6 0 1 0 9.33 5"></path>
                        <path d="m13.33 18.2.67-.2"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-medium text-gray-500">Total Trampas</h3>
                    <p class="text-3xl font-bold mt-1"><?= array_sum(array_column($sedes, 'total_trampas')) ?></p>
                </div>
            </div>
            <div class="space-y-2 mt-2 pt-3 border-t">
                <?php foreach ($sedes as $sede): ?>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600"><?= $sede['nombre'] ?></span>
                        <span class="font-medium text-amber-600"><?= $sede['total_trampas'] ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Sedes Cards -->
    <?php if (empty($sedes)): ?>
        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-8 text-center">
            <div class="bg-amber-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600">
                    <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                    <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                    <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                </svg>
            </div>
            <h3 class="text-xl font-medium text-amber-800 mb-2">No hay plantas registradas</h3>
            <p class="text-amber-700 mb-6 max-w-md mx-auto">Comienza agregando tu primera planta con el botón "Agregar Sede" para gestionar tus ubicaciones.</p>
            <button 
                onclick="sedeModal.show()"
                class="inline-flex items-center justify-center bg-amber-600 hover:bg-amber-700 text-white px-5 py-2.5 rounded-lg transition-colors duration-200 shadow-sm font-medium"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Agregar Mi Primera Planta
            </button>
        </div>
    <?php else: ?>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 text-blue-600">
                    <path d="M3 9h18v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9Z"></path>
                    <path d="m3 9 2.45-4.9A2 2 0 0 1 7.24 3h9.52a2 2 0 0 1 1.8 1.1L21 9"></path>
                    <path d="M12 3v6"></path>
                </svg>
                Plantas Registradas
            </h2>
            <div class="flex items-center text-sm text-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="16" x2="12" y2="12"></line>
                    <line x1="12" y1="8" x2="12.01" y2="8"></line>
                </svg>
            </div>
        </div>
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($sedes as $sede): ?>
            <div class="bg-white rounded-xl shadow-sm border hover:shadow-md transition-all duration-200 transform hover:-translate-y-1">
                <div class="p-6 border-b">
                    <div class="flex items-start">
                        <div class="bg-blue-100 p-3 rounded-lg mr-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-600">
                                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-800"><?= $sede['nombre'] ?></h2>
                            <p class="text-gray-500 mt-1 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                    <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                    <circle cx="12" cy="10" r="3"></circle>
                                </svg>
                                <?= $sede['direccion'] ?>
                            </p>
                            <?php if (!empty($sede['ciudad']) || !empty($sede['pais'])): ?>
                            <p class="text-gray-500 text-sm mt-1 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                </svg>
                                <?= !empty($sede['ciudad']) ? $sede['ciudad'] : '' ?>
                                <?= !empty($sede['ciudad']) && !empty($sede['pais']) ? ', ' : '' ?>
                                <?= !empty($sede['pais']) ? $sede['pais'] : '' ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-6">
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-4 text-center hover:shadow-sm transition-shadow duration-200">
                            <div class="flex items-center justify-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-600">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="3" y1="9" x2="21" y2="9"></line>
                                    <line x1="9" y1="21" x2="9" y2="9"></line>
                                </svg>
                            </div>
                            <p class="text-gray-600 text-sm font-medium">Planos</p>
                            <p class="text-2xl font-bold mt-1 text-gray-800"><?= $sede['total_planos'] ?></p>
                        </div>
                        <div class="bg-gradient-to-br from-amber-50 to-yellow-50 rounded-lg p-4 text-center hover:shadow-sm transition-shadow duration-200">
                            <div class="flex items-center justify-center mb-2">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600">
                                    <path d="M18 8a6 6 0 0 0-9.33-5"></path>
                                    <path d="m10.67 5.8-.67.2"></path>
                                    <path d="M6 8a6 6 0 1 0 9.33 5"></path>
                                    <path d="m13.33 18.2.67-.2"></path>
                                </svg>
                            </div>
                            <p class="text-gray-600 text-sm font-medium">Trampas</p>
                            <p class="text-2xl font-bold mt-1 text-gray-800"><?= $sede['total_trampas'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Modal Agregar Sede -->
    <div id="modalAgregarSede" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 animate-fade-in">
            <div class="flex items-center justify-between border-b p-4 bg-gradient-to-r from-blue-600 to-indigo-700 text-white rounded-t-xl">
                <h3 class="text-lg font-semibold flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
                        <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                        <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                        <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                    </svg>
                    Agregar Nueva Planta
                </h3>
                <button type="button" onclick="sedeModal.hide()" class="text-white hover:text-gray-200 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"></line>
                        <line x1="6" y1="6" x2="18" y2="18"></line>
                    </svg>
                </button>
            </div>
            <form action="<?= base_url('sedes/guardar') ?>" method="POST" id="formAgregarSede">
                <div class="p-6 space-y-4">
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1 text-blue-600">
                                <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                                <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                                <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                            </svg>
                            Nombre de la Planta <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre" name="nombre" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-20 transition-shadow duration-200"
                            placeholder="Ej: Sede Principal">
                    </div>
                    <div>
                        <label for="direccion" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1 text-blue-600">
                                <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
                                <circle cx="12" cy="10" r="3"></circle>
                            </svg>
                            Dirección <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="direccion" name="direccion" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-20 transition-shadow duration-200"
                            placeholder="Ej: Av. Principal #123">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1 text-blue-600">
                                    <path d="M12 22s-8-4.5-8-11.8A8 8 0 0 1 12 2a8 8 0 0 1 8 8.2c0 7.3-8 11.8-8 11.8z"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                                Ciudad <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="ciudad" name="ciudad" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-20 transition-shadow duration-200"
                                placeholder="Ej: CDMX">
                        </div>
                        <div>
                            <label for="pais" class="block text-sm font-medium text-gray-700 mb-1 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1 text-blue-600">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                </svg>
                                País <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="pais" name="pais" required
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-20 transition-shadow duration-200"
                                placeholder="Ej: México">
                        </div>
                    </div>
                    <div class="bg-blue-50 p-3 rounded-lg text-sm text-blue-700 flex items-start mt-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 mt-0.5 text-blue-600">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span>Todos los campos marcados con <span class="text-red-500">*</span> son obligatorios.</span>
                    </div>
                </div>
                <div class="border-t p-4 flex justify-end space-x-3 bg-gray-50 rounded-b-xl">
                    <button type="button" onclick="sedeModal.hide()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors duration-200 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors duration-200 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                            <polyline points="17 21 17 13 7 13 7 21"></polyline>
                            <polyline points="7 3 7 8 15 8"></polyline>
                        </svg>
                        Guardar Sede
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Confirmar Eliminación -->
    <div id="modalConfirmarEliminar" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 animate-fade-in">
            <div class="p-6">
                <div class="flex items-center justify-center mb-4">
                    <div class="bg-red-100 rounded-full p-3">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-600">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                </div>
                <h3 class="text-lg font-semibold text-center mb-2">Confirmar Eliminación</h3>
                <p class="text-center text-gray-600 mb-6">¿Estás seguro que deseas eliminar la planta <span id="nombreSedeEliminar" class="font-medium text-red-600"></span>? Esta acción no se puede deshacer.</p>
                <div class="flex justify-center space-x-3">
                    <button type="button" onclick="eliminarModal.hide()"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 rounded-lg transition-colors duration-200 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                        Cancelar
                    </button>
                    <form id="formEliminarSede" action="" method="POST">
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors duration-200 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1">
                                <path d="M3 6h18"></path>
                                <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                            </svg>
                            Sí, Eliminar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div id="toastContainer" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2"></div>
</div>

<script>
// Modal management
const sedeModal = {
    element: document.getElementById('modalAgregarSede'),
    show: function() {
        this.element.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        // Focus first input when modal opens
        setTimeout(() => document.getElementById('nombre').focus(), 100);
    },
    hide: function() {
        this.element.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
        // Reset form
        document.getElementById('formAgregarSede').reset();
    }
};

const eliminarModal = {
    element: document.getElementById('modalConfirmarEliminar'),
    show: function() {
        this.element.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
    },
    hide: function() {
        this.element.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
};

// Close modals when clicking outside
document.getElementById('modalAgregarSede').addEventListener('click', function(e) {
    if (e.target === this) sedeModal.hide();
});

document.getElementById('modalConfirmarEliminar').addEventListener('click', function(e) {
    if (e.target === this) eliminarModal.hide();
});

// Dropdown toggle
function toggleDropdown(event, id) {
    event.stopPropagation();
    
    // Close all other dropdowns first
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu.id !== id) menu.classList.add('hidden');
    });
    
    const dropdown = document.getElementById(id);
    dropdown.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function() {
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        menu.classList.add('hidden');
    });
});

// Confirm delete
function confirmarEliminar(id, nombre) {
    document.getElementById('nombreSedeEliminar').textContent = nombre;
    document.getElementById('formEliminarSede').action = `<?= base_url('sedes/eliminar/') ?>${id}`;
    eliminarModal.show();
}

// Toast notifications
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `px-6 py-3 rounded-lg shadow-lg flex items-center ${type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'} animate-slide-up`;
    toast.innerHTML = `
        <div class="mr-3">
            ${type === 'success' 
                ? '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>'
                : '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>'
            }
        </div>
        <span>${message}</span>
    `;
    
    document.getElementById('toastContainer').appendChild(toast);
    
    // Remove after delay
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Show flash messages as toasts
<?php if (session()->getFlashdata('message')): ?>
    showToast('<?= session()->getFlashdata('message') ?>', 'success');
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    showToast('<?= session()->getFlashdata('error') ?>', 'error');
<?php endif; ?>

// Form validation
document.getElementById('formAgregarSede').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    const direccion = document.getElementById('direccion').value.trim();
    const ciudad = document.getElementById('ciudad').value.trim();
    const pais = document.getElementById('pais').value.trim();
    
    if (!nombre || !direccion || !ciudad || !pais) {
        e.preventDefault();
        showToast('Por favor complete todos los campos requeridos', 'error');
    }
});

// Add hover effects to cards
document.querySelectorAll('.grid > div').forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.classList.add('shadow-md');
    });
    card.addEventListener('mouseleave', function() {
        this.classList.remove('shadow-md');
    });
});
</script>

<style>
/* Animation for dropdowns */
.dropdown-menu {
    transform-origin: top right;
    transition: opacity 0.2s, transform 0.2s;
}

/* Animation for toasts */
#toastContainer > div {
    transition: opacity 0.3s, transform 0.3s;
    opacity: 0;
    transform: translateY(10px);
}

/* Ensure dropdowns are positioned correctly */
.dropdown {
    position: relative;
}

/* Animations */
.animate-fade-in {
    animation: fadeIn 0.3s ease-out;
}

.animate-slide-up {
    animation: slideUp 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { 
        opacity: 0;
        transform: translateY(20px);
    }
    to { 
        opacity: 1;
        transform: translateY(0);
    }
}

/* Hover transitions */
a, button {
    transition: all 0.2s ease;
}
</style>
<?= $this->endSection() ?>

