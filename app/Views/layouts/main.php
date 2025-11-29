<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Control de Plagas' ?></title>
    
    <!-- Script de verificación de sesión -->
    <script>
        // Verificar si el usuario ha iniciado sesión
        function verificarSesion() {
            const userInfo = localStorage.getItem('servipro_user');
            if (!userInfo) {
                // Si no hay información de usuario en localStorage, redirigir al login
                window.location.href = '<?= base_url() ?>';
                return false;
            }
            
            try {
                const user = JSON.parse(userInfo);
                if (!user.logged_in) {
                    // Si no tiene la propiedad logged_in, redirigir al login
                    window.location.href = '<?= base_url() ?>';
                    return false;
                }
                return true;
            } catch (error) {
                console.error('Error al verificar sesión:', error);
                window.location.href = '<?= base_url() ?>';
                return false;
            }
        }
        
        // Ejecutar la verificación al cargar la página
        verificarSesion();
    </script>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?=base_url()?>css/styles.css?v=1.0">
    
    <!-- Bibliotecas para exportación -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/pptxgenjs@3.12.0/dist/pptxgen.min.js"></script>
    
    <?= $this->renderSection('styles') ?>
</head>
<body class="bg-gray-100">
    <!-- Sidebar -->
    <?= $this->include('partials/sidebar') ?>

    <!-- Contenido principal -->
    <div class="lg:pl-72">
        <main class="p-6" id="main-content">
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>

    <!-- Scripts -->
    <script>
        // Script para el sidebar móvil
        const mobileSidebar = document.getElementById('mobile-sidebar');
        const mobileSidebarOverlay = document.getElementById('mobile-sidebar-overlay');
        const mobileSidebarTrigger = document.querySelector('button[class*="lg:hidden"]');

        mobileSidebarTrigger.addEventListener('click', () => {
            mobileSidebar.style.transform = 'translateX(0)';
            mobileSidebarOverlay.style.display = 'block';
        });

        mobileSidebarOverlay.addEventListener('click', () => {
            mobileSidebar.style.transform = 'translateX(-100%)';
            mobileSidebarOverlay.style.display = 'none';
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html> 