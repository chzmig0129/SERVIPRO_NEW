<!-- Mobile Sidebar Trigger -->
<button class="lg:hidden fixed left-4 top-4 p-2 rounded-lg bg-white hover:bg-gray-100 shadow-md z-50">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-700">
        <line x1="4" y1="12" x2="20" y2="12"/>
        <line x1="4" y1="6" x2="20" y2="6"/>
        <line x1="4" y1="18" x2="20" y2="18"/>
    </svg>
</button>

<!-- Mobile Sidebar -->
<div class="lg:hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm z-40" id="mobile-sidebar-overlay" style="display: none;"></div>
<div class="lg:hidden fixed inset-y-0 left-0 w-72 bg-white z-50 transform -translate-x-full transition-transform duration-300 shadow-2xl" id="mobile-sidebar">
    <div class="flex h-16 items-center border-b px-6 bg-gradient-to-r from-blue-600 to-indigo-700">
        <h2 class="text-lg font-semibold text-white flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            Control de Plagas
        </h2>
    </div>
    <div class="overflow-y-auto h-[calc(100vh-4rem)] p-4">
        <nav class="flex flex-col gap-3">
            <?php include('sidebar_content.php'); ?>
        </nav>
    </div>
</div>

<!-- Desktop Sidebar -->
<aside class="hidden lg:flex fixed inset-y-0 left-0 flex-col w-72 bg-white border-r shadow-sm">
    <div class="flex h-16 items-center border-b px-6 bg-gradient-to-r from-blue-600 to-indigo-700">
        <h2 class="text-lg font-semibold text-white flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                <polyline points="9 22 9 12 15 12 15 22"/>
            </svg>
            SERVIPRO
        </h2>
    </div>
    <div class="flex-1 overflow-y-auto p-4">
        <nav class="flex flex-col gap-3">
            <?php 
            $current_url = current_url(true)->getPath();
            $session = session();
            $userName = $session->get('nombre') ?? 'Usuario';
            ?>
            
            <?php if($session->get('logged_in')): ?>
            <!-- User info -->
            <div class="bg-blue-50 rounded-lg p-3 mb-2 flex items-center gap-3">
                <div class="bg-blue-100 w-10 h-10 rounded-full flex items-center justify-center text-blue-600 font-bold">
                    <?= substr($userName, 0, 1) ?>
                </div>
                <div>
                    <div class="text-sm font-medium text-blue-800"><?= $userName ?></div>
                    <div class="text-xs text-blue-600"><?= $session->get('correo') ?? 'Sin correo' ?></div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Principal</div>
            
            <a href="<?= site_url('Inicio') ?>" 
               class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/Inicio' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Inicio
            </a>

            <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4">Gestión</div>
            
            <a href="<?= site_url('locations') ?>" 
               class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/locations' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/>
                    <path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/>
                    <path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/>
                </svg>
                Plantas
            </a>

            <a href="<?= site_url('blueprints') ?>" 
               class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/blueprints' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14.106 5.553a2 2 0 0 0 1.788 0l3.659-1.83A1 1 0 0 1 21 4.619v12.764a1 1 0 0 1-.553.894l-4.553 2.277a2 2 0 0 1-1.788 0l-4.212-2.106a2 2 0 0 0-1.788 0l-3.659 1.83A1 1 0 0 1 3 19.381V6.618a1 1 0 0 1 .553-.894l4.553-2.277a2 2 0 0 1 1.788 0z"/>
                </svg>
                Planos
            </a>

            <a href="<?= site_url('incidents') ?>" 
               class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/incidents' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
                Evidencias
            </a>

            <a href="<?= site_url('registro_tecnico') ?>" 
               class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/registro_tecnico' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Registro Técnico por visita
            </a>

            <a href="<?= site_url('quejas') ?>" 
               class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/quejas' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                </svg>
                Quejas
            </a>

            <a href="<?= site_url('ventas') ?>" 
               class="flex items-center gap-3 rounded-xl px-4 py-3 text-sm transition-all duration-200 <?= $current_url == '/ventas' ? 'bg-blue-50 text-blue-700 font-medium shadow-sm' : 'text-gray-700 hover:bg-gray-50' ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
                Ventas
            </a>
            
            <!-- Separator for logout -->
            <div class="my-6 border-t border-gray-200"></div>
            
            <!-- Logout link -->
            <a href="<?= base_url('logout') ?>" class="flex items-center p-3 text-gray-700 rounded-lg hover:bg-gray-50 hover:shadow-sm transition-all group">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-500 mr-3 group-hover:text-red-500">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span class="group-hover:text-red-500">Cerrar Sesión</span>
            </a>
        </nav>
    </div>
</aside>

<style>
/* Animaciones suaves */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 200ms;
}

/* Efecto de hover mejorado */
.hover\:bg-gray-50:hover {
    background-color: rgba(249, 250, 251, 0.8);
    transform: translateX(4px);
}

/* Estilo activo mejorado */
.bg-blue-50 {
    position: relative;
    overflow: hidden;
}

.bg-blue-50::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: linear-gradient(to bottom, #2563eb, #4f46e5);
    border-radius: 0 2px 2px 0;
}

/* Mejora de la sombra en hover */
.hover\:shadow-sm:hover {
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

/* Efecto de backdrop blur para el overlay del sidebar móvil */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}
</style>

<script>
// Verificar si hay un usuario en localStorage y mostrar la información
document.addEventListener('DOMContentLoaded', function() {
    const userInfo = localStorage.getItem('servipro_user');
    if (userInfo) {
        try {
            const user = JSON.parse(userInfo);
            const userInfoElement = document.querySelector('.bg-blue-50');
            
            if (userInfoElement) {
                // Ya existe un elemento de usuario, actualizar su contenido
                const nameElement = userInfoElement.querySelector('.text-sm');
                const emailElement = userInfoElement.querySelector('.text-xs');
                const initialElement = userInfoElement.querySelector('.bg-blue-100');
                
                if (nameElement) nameElement.textContent = user.nombre;
                if (emailElement) emailElement.textContent = user.correo;
                if (initialElement) initialElement.textContent = user.nombre.charAt(0);
            } else {
                // No existe el elemento, crear uno nuevo
                const userDiv = document.createElement('div');
                userDiv.className = 'bg-blue-50 rounded-lg p-3 mb-2 flex items-center gap-3';
                userDiv.innerHTML = `
                    <div class="bg-blue-100 w-10 h-10 rounded-full flex items-center justify-center text-blue-600 font-bold">
                        ${user.nombre.charAt(0)}
                    </div>
                    <div>
                        <div class="text-sm font-medium text-blue-800">${user.nombre}</div>
                        <div class="text-xs text-blue-600">${user.correo}</div>
                    </div>
                `;
                
                // Insertar antes del primer elemento de navegación
                const navElement = document.querySelector('nav');
                if (navElement) {
                    navElement.insertBefore(userDiv, navElement.firstChild);
                }
            }
        } catch (error) {
            console.error('Error al procesar información de usuario:', error);
        }
    }
});

// Añadir funcionalidad al enlace de logout
document.addEventListener('DOMContentLoaded', function() {
    const logoutLink = document.querySelector('a[href*="logout"]');
    if (logoutLink) {
        logoutLink.addEventListener('click', function(e) {
            e.preventDefault();
            localStorage.removeItem('servipro_user');
            window.location.href = '<?= base_url() ?>';
        });
    }
});
</script> 