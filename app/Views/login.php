<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiPro - Control de Plagas | Login</title>
    <style>
        :root {
            --primary-color: #3bb78f;
            --primary-light: #45c89b;
            --primary-dark: #2e9671;
            --secondary-color: #e74c3c;
            --text-color: #333;
            --light-color: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(-45deg,rgb(76, 116, 202),rgb(63, 98, 186),rgb(215, 231, 230),rgb(191, 194, 190));
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            padding: 20px;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            color: var(--text-color);
            position: relative;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 400px;
            position: relative;
            z-index: 1000;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .logo i {
            font-size: 50px;
            color:rgb(52, 105, 196);
        }

        h1 {
            color: #2d3436;
            text-align: center;
            margin-bottom: 30px;
            font-size: 28px;
            position: relative;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 50px;
            height: 3px;
            background:rgb(187, 212, 204);
            border-radius: 2px;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            color: #636e72;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #dfe6e9;
            border-radius: 10px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .input-group input:focus {
            border-color:rgb(224, 237, 233);
            outline: none;
            box-shadow: 0 0 20px rgba(59, 183, 143, 0.2);
            transform: translateY(-2px);
        }

        .login-button {
            width: 100%;
            padding: 15px;
            background:rgb(184, 194, 190);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300%;
            height: 300%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%) rotate(45deg);
            transition: transform 0.6s ease;
        }

        .login-button:hover {
            background:rgb(56, 77, 184);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(45, 139, 107, 0.3);
        }

        .login-button:hover::before {
            transform: translate(-50%, -50%) rotate(45deg) translateY(-100%);
        }

        .forgot-password {
            text-align: right;
            margin: 10px 0 20px;
        }

        .forgot-password a {
            color:rgb(59, 104, 183);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color:rgb(45, 61, 139);
            text-decoration: underline;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #636e72;
        }

        .register-link a {
            color:rgb(59, 102, 183);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .register-link a:hover {
            color:rgb(45, 86, 139);
        }

        /* Estilos para los ratones */
        .mouse {
            position: absolute;
            pointer-events: none;
            z-index: 1;
        }

        .mouse-body {
            position: relative;
            width: 30px;
            height: 20px;
            background: #666;
            border-radius: 15px 20px 20px 15px;
            transform-origin: left center;
        }

        .mouse-ear {
            position: absolute;
            width: 12px;
            height: 12px;
            background: #666;
            border-radius: 50%;
            top: -4px;
        }

        .mouse-ear.left {
            left: 2px;
        }

        .mouse-ear.right {
            left: 10px;
        }

        .mouse-nose {
            position: absolute;
            width: 6px;
            height: 4px;
            background: #ff9999;
            border-radius: 3px;
            right: -2px;
            top: 8px;
        }

        .mouse-tail {
            position: absolute;
            width: 25px;
            height: 2px;
            background: #666;
            left: -20px;
            top: 10px;
            transform-origin: right center;
            animation: tailWag 1s ease-in-out infinite;
        }

        .mouse-eye {
            position: absolute;
            width: 4px;
            height: 4px;
            background: #000;
            border-radius: 50%;
            top: 5px;
            right: 8px;
        }

        .mouse-whisker {
            position: absolute;
            width: 10px;
            height: 1px;
            background: #888;
            right: -5px;
        }

        .mouse-whisker.top {
            top: 6px;
            transform: rotate(-15deg);
        }

        .mouse-whisker.middle {
            top: 9px;
        }

        .mouse-whisker.bottom {
            top: 12px;
            transform: rotate(15deg);
        }

        @keyframes tailWag {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(-10deg); }
        }

        @keyframes mouseRun1 {
            0% { transform: translate(0, 0) scaleX(1); }
            25% { transform: translate(200px, 50px) scaleX(1); }
            50% { transform: translate(400px, 0) scaleX(1); }
            50.1% { transform: translate(400px, 0) scaleX(-1); }
            75% { transform: translate(200px, -50px) scaleX(-1); }
            100% { transform: translate(0, 0) scaleX(-1); }
        }

        @keyframes mouseRun2 {
            0% { transform: translate(0, 0) scaleX(-1); }
            25% { transform: translate(-200px, 100px) scaleX(-1); }
            50% { transform: translate(-400px, 0) scaleX(-1); }
            50.1% { transform: translate(-400px, 0) scaleX(1); }
            75% { transform: translate(-200px, -100px) scaleX(1); }
            100% { transform: translate(0, 0) scaleX(1); }
        }

        .mouse.scared {
            transition: all 0.5s ease;
            transform: scale(0) rotate(360deg) !important;
            opacity: 0;
        }

        .spray {
            position: absolute;
            pointer-events: none;
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,255,255,0.8) 0%, rgba(255,255,255,0) 70%);
            animation: sprayEffect 1s ease-out forwards;
        }

        @keyframes sprayEffect {
            0% { transform: scale(0.3); opacity: 0.8; }
            100% { transform: scale(2); opacity: 0; }
        }

        .footprint {
            position: absolute;
            width: 6px;
            height: 10px;
            background: rgba(102, 102, 102, 0.2);
            border-radius: 50%;
            pointer-events: none;
            animation: fadeOut 2s forwards;
        }

        @keyframes fadeOut {
            to { opacity: 0; }
        }

        /* Estilo para el mensaje de error de sesión */
        .error-message {
            background-color: #ffe0e0;
            color: #e74c3c;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            border-left: 4px solid #e74c3c;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>
<body>
    <!-- Template para el ratón -->
    <template id="mouseTemplate">
        <div class="mouse">
            <div class="mouse-body">
                <div class="mouse-ear left"></div>
                <div class="mouse-ear right"></div>
                <div class="mouse-eye"></div>
                <div class="mouse-nose"></div>
                <div class="mouse-whisker top"></div>
                <div class="mouse-whisker middle"></div>
                <div class="mouse-whisker bottom"></div>
            </div>
            <div class="mouse-tail"></div>
        </div>
    </template>

    <div class="login-container">
        <div class="logo">
            <i class="fas fa-bug"></i>
        </div>
        <h1>Bienvenidos a ServiPro</h1>
        
        <!-- Área para mostrar mensajes de error de sesión -->
        <?php if(session()->getFlashdata('error')): ?>
        <div class="error-message">
            <?= session()->getFlashdata('error') ?>
        </div>
        <?php endif; ?>
        
        <form id="loginForm" action="<?= base_url('authenticate') ?>" method="post">
            <div class="input-group">
                <label for="usuario">Usuario</label>
                <input 
                    type="text" 
                    id="usuario" 
                    name="usuario" 
                    placeholder="Ingresa tu usuario"
                    required>
            </div>
            <div class="input-group">
                <label for="password">Contraseña</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Ingresa tu contraseña"
                    required>
            </div>

            <div class="forgot-password">
                <a href="#" id="forgotPassword">¿Olvidaste tu contraseña?</a>
            </div>
            <button type="submit" class="login-button">
                <span>Iniciar Sesión</span>
            </button>
            <div class="register-link">
               
            </div>
        </form>
    </div>

    <script>
        function createMouse() {
            const template = document.getElementById('mouseTemplate');
            const mouse = template.content.cloneNode(true).children[0];
            
            mouse.style.left = Math.random() * window.innerWidth + 'px';
            mouse.style.top = Math.random() * window.innerHeight + 'px';
            
            const animations = ['mouseRun1', 'mouseRun2'];
            const randomAnim = animations[Math.floor(Math.random() * animations.length)];
            const duration = 15 + Math.random() * 10 + 's';
            const delay = Math.random() * -20 + 's';
            
            mouse.style.animation = `${randomAnim} ${duration} linear infinite`;
            mouse.style.animationDelay = delay;
            
            document.body.appendChild(mouse);

            let lastFootprint = Date.now();
            const createFootprint = () => {
                if (Date.now() - lastFootprint > 200) {
                    const footprint = document.createElement('div');
                    footprint.className = 'footprint';
                    const rect = mouse.getBoundingClientRect();
                    footprint.style.left = (rect.left + rect.width/2) + 'px';
                    footprint.style.top = (rect.top + rect.height) + 'px';
                    document.body.appendChild(footprint);
                    
                    setTimeout(() => footprint.remove(), 2000);
                    lastFootprint = Date.now();
                }
            };

            const footprintInterval = setInterval(createFootprint, 200);
            mouse.addEventListener('remove', () => clearInterval(footprintInterval));

            return mouse;
        }

        // Crear ratones iniciales
        for(let i = 0; i < 5; i++) {
            createMouse();
        }

        // Crear nuevos ratones periódicamente
        setInterval(createMouse, 5000);

        // Manejar el envío del formulario
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            processLogin_original();
        });
        
        // También agregamos un evento directamente al botón como respaldo
        document.querySelector('.login-button').addEventListener('click', function(e) {
            e.preventDefault();
            processLogin_original();
        });
        
        // Función de login que verifica el usuario en la base de datos usando el server
        function processLogin_original() {
            const usuario = document.getElementById('usuario').value;
            const password = document.getElementById('password').value;
            
            // Validación básica
            if (!usuario || !password) {
                alert('Por favor, completa todos los campos');
                return;
            }
            
            const button = document.querySelector('.login-button');
            button.innerHTML = '<span style="display: inline-block; animation: pulse 1s infinite;">Procesando...</span>';
            button.style.pointerEvents = 'none';
            
            // Enviar datos al servidor
            fetch('<?= base_url('authenticate') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    'usuario': usuario,
                    'password': password
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Guardar información de sesión en localStorage
                    localStorage.setItem('servipro_user', JSON.stringify({
                        nombre: data.user.nombre,
                        correo: data.user.correo,
                        logged_in: true
                    }));
                    
                    // Login exitoso
                    button.innerHTML = '<span>¡Bienvenido!</span>';
                    button.style.background = '#2ecc71';
                    
                    setTimeout(() => {
                        // Redireccionar al dashboard principal
                        window.location.href = '<?= base_url("Inicio") ?>';
                    }, 1000);
                } else {
                    // Error de autenticación
                    button.innerHTML = '<span>Error</span>';
                    button.style.background = '#e74c3c';
                    
                    setTimeout(() => {
                        button.innerHTML = '<span>Iniciar Sesión</span>';
                        button.style.pointerEvents = 'auto';
                        button.style.background = '#3bb78f';
                        alert(data.message || 'Usuario o contraseña incorrectos');
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Error de autenticación:', error);
                button.innerHTML = '<span>Error</span>';
                button.style.background = '#e74c3c';
                
                setTimeout(() => {
                    button.innerHTML = '<span>Iniciar Sesión</span>';
                    button.style.pointerEvents = 'auto';
                    button.style.background = '#3bb78f';
                    alert('Error de conexión. Por favor, inténtalo de nuevo.');
                }, 1000);
            });
        }

        // Efecto de spray al hacer clic
        document.addEventListener('click', function(e) {
            const spray = document.createElement('div');
            spray.className = 'spray';
            spray.style.left = (e.clientX - 50) + 'px';
            spray.style.top = (e.clientY - 50) + 'px';
            document.body.appendChild(spray);

            const mice = document.querySelectorAll('.mouse');
            mice.forEach(mouse => {
                const rect = mouse.getBoundingClientRect();
                const distance = Math.hypot(
                    e.clientX - (rect.left + rect.width/2),
                    e.clientY - (rect.top + rect.height/2)
                );
                if (distance < 100) {
                    mouse.classList.add('scared');
                    setTimeout(() => mouse.remove(), 500);
                }
            });

            setTimeout(() => spray.remove(), 1000);
        });

        // Hacer que los ratones huyan del cursor
        document.addEventListener('mousemove', function(e) {
            const mice = document.querySelectorAll('.mouse');
            mice.forEach(mouse => {
                const rect = mouse.getBoundingClientRect();
                const distance = Math.hypot(
                    e.clientX - (rect.left + rect.width/2),
                    e.clientY - (rect.top + rect.height/2)
                );
                
                if (distance < 150) {
                    const angle = Math.atan2(rect.top - e.clientY, rect.left - e.clientX);
                    const speed = (150 - distance) / 3;
                    mouse.style.transform += ` translate(
                        ${Math.cos(angle) * speed}px,
                        ${Math.sin(angle) * speed}px
                    )`;
                }
            });
        });

        // Enlaces interactivos
        document.getElementById('forgotPassword').addEventListener('click', function(e) {
            e.preventDefault();
            alert('Función de recuperación de contraseña en desarrollo');
        });

        document.getElementById('registerLink').addEventListener('click', function(e) {
            e.preventDefault();
            alert('Página de registro en desarrollo');
        });
    </script>
    <!-- Agregar Font Awesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>