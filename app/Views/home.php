<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Principal - Sistema de Gestión</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <!-- Encabezado con botón de agregar -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Panel Principal</h1>
            <button onclick="openModal()" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center">
                <i class="fas fa-plus-circle mr-2"></i>
                Agregar Sede
            </button>
        </div>

        <!-- Estadísticas Generales -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full">
                        <i class="fas fa-building text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Sedes</h3>
                        <p class="text-2xl font-semibold"><?= $total_sedes ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full">
                        <i class="fas fa-map text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Planos</h3>
                        <p class="text-2xl font-semibold"><?= $total_planos ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full">
                        <i class="fas fa-mouse-pointer text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-gray-500 text-sm">Total Trampas</h3>
                        <p class="text-2xl font-semibold"><?= $total_trampas ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de Sedes -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php foreach ($sedes as $sede): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800"><?= $sede['nombre'] ?></h2>
                            <p class="text-gray-600 text-sm"><?= $sede['direccion'] ?></p>
                            <p class="text-gray-500 text-sm"><?= $sede['ciudad'] ?>, <?= $sede['pais'] ?></p>
                        </div>
                        <a href="<?= base_url('sedes/ver/' . $sede['id']) ?>" 
                           class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-4">
                        <div class="text-center p-2 bg-gray-50 rounded">
                            <p class="text-sm text-gray-600">Planos</p>
                            <p class="text-lg font-semibold"><?= $sede['total_planos'] ?></p>
                        </div>
                        <div class="text-center p-2 bg-gray-50 rounded">
                            <p class="text-sm text-gray-600">Trampas</p>
                            <p class="text-lg font-semibold"><?= $sede['total_trampas'] ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Modal de Agregar Sede -->
        <div id="modalAgregarSede" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                <div class="mt-3">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">
                        <i class="fas fa-building mr-2"></i>
                        Agregar Nueva Sede
                    </h3>
                    <form id="formAgregarSede" action="<?= base_url('sedes/guardar') ?>" method="POST">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm text-gray-700 mb-2">Nombre de la Sede</label>
                                <input type="text" name="nombre" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-2">Dirección</label>
                                <input type="text" name="direccion" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-2">Ciudad</label>
                                <input type="text" name="ciudad" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700 mb-2">País</label>
                                <input type="text" name="pais" required
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" onclick="closeModal()"
                                    class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                                Cancelar
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Guardar Sede
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mensajes de éxito/error -->
        <?php if (session()->getFlashdata('message')): ?>
            <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg" id="flashMessage">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg" id="flashMessage">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Funciones para el modal
        function openModal() {
            document.getElementById('modalAgregarSede').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modalAgregarSede').classList.add('hidden');
        }

        // Ocultar mensajes flash después de 3 segundos
        const flashMessage = document.getElementById('flashMessage');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.opacity = '0';
                setTimeout(() => {
                    flashMessage.remove();
                }, 300);
            }, 3000);
        }

        // Cerrar modal al hacer clic fuera
        window.onclick = function(event) {
            const modal = document.getElementById('modalAgregarSede');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>