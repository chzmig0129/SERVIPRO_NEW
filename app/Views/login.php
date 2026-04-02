<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ServiPro | Gestión Integrada de Plagas</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary:        #1e3a5f;
            --primary-dark:   #152d4a;
            --accent:         #d4a843;
            --bg:             #f0f2f5;
            --card:           #ffffff;
            --text:           #1a1a2e;
            --muted:          #6b7280;
            --border:         #d1d5db;
            --error-bg:       #fef2f2;
            --error-border:   #dc2626;
            --error-text:     #dc2626;
            --success:        #16a34a;
            --focus-shadow:   rgba(30, 58, 95, 0.15);
        }

        *, *::before, *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--bg);
            font-family: 'Inter', sans-serif;
            color: var(--text);
            padding: 20px;
        }

        /* ── Login card ── */
        .login-card {
            background: var(--card);
            width: 100%;
            max-width: 420px;
            border-radius: 12px;
            padding: 40px;
            box-shadow:
                0 1px 3px rgba(0, 0, 0, 0.08),
                0 8px 24px rgba(0, 0, 0, 0.10);
            position: relative;
            overflow: hidden;
        }

        /* Gold accent line at top of card */
        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--accent);
        }

        /* ── Logo & tagline ── */
        .login-logo-wrap {
            text-align: center;
            margin-bottom: 8px;
        }

        .login-logo {
            height: 60px;
            width: auto;
            display: inline-block;
        }

        .login-tagline {
            text-align: center;
            font-size: 12px;
            font-weight: 500;
            color: var(--muted);
            letter-spacing: 0.04em;
            text-transform: uppercase;
            margin-bottom: 32px;
        }

        /* ── Error message ── */
        .error-message {
            background-color: var(--error-bg);
            color: var(--error-text);
            border-left: 3px solid var(--error-border);
            border-radius: 6px;
            padding: 11px 14px;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 20px;
            line-height: 1.4;
        }

        /* ── Form fields ── */
        .input-group {
            margin-bottom: 18px;
        }

        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text);
            margin-bottom: 6px;
        }

        .input-group input {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 15px;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            background: #fff;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            outline: none;
        }

        .input-group input::placeholder {
            color: #b0b7c3;
        }

        .input-group input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--focus-shadow);
        }

        /* ── Forgot password ── */
        .forgot-password {
            text-align: right;
            margin-top: -8px;
            margin-bottom: 24px;
        }

        .forgot-password a {
            font-size: 12px;
            font-weight: 500;
            color: var(--primary);
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.2s ease;
        }

        .forgot-password a:hover {
            opacity: 1;
            text-decoration: underline;
        }

        /* ── Submit button ── */
        .login-button {
            width: 100%;
            padding: 13px;
            background: var(--primary);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
            letter-spacing: 0.01em;
        }

        .login-button:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(30, 58, 95, 0.25);
        }

        .login-button:active {
            transform: translateY(0);
            box-shadow: none;
        }

        /* ── Card footer ── */
        .login-footer {
            text-align: center;
            font-size: 11px;
            color: var(--muted);
            margin-top: 32px;
        }

        /* ── Responsive ── */
        @media (max-width: 480px) {
            .login-card {
                padding: 28px 20px;
                margin: 0 20px;
                border-radius: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="login-card">

        <div class="login-logo-wrap">
            <img src="<?= base_url('img/servipro-logo-login.png') ?>" alt="ServiPro" class="login-logo">
        </div>

        <p class="login-tagline">Gestión Integrada de Plagas</p>

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
                    required
                    autocomplete="username">
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Ingresa tu contraseña"
                    required
                    autocomplete="current-password">
            </div>

            <div class="forgot-password">
                <a href="#" id="forgotPassword">¿Olvidaste tu contraseña?</a>
            </div>

            <button type="submit" class="login-button">
                <span>Iniciar Sesión</span>
            </button>

        </form>

        <p class="login-footer">© 2026 ServiPro de México, S.A. de C.V.</p>

    </div>

    <script>
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
                    button.style.background = '#16a34a';

                    setTimeout(() => {
                        // Redireccionar al dashboard principal
                        window.location.href = '<?= base_url("Inicio") ?>';
                    }, 1000);
                } else {
                    // Error de autenticación
                    button.innerHTML = '<span>Error</span>';
                    button.style.background = '#dc2626';

                    setTimeout(() => {
                        button.innerHTML = '<span>Iniciar Sesión</span>';
                        button.style.pointerEvents = 'auto';
                        button.style.background = '';
                        alert(data.message || 'Usuario o contraseña incorrectos');
                    }, 1000);
                }
            })
            .catch(error => {
                console.error('Error de autenticación:', error);
                button.innerHTML = '<span>Error</span>';
                button.style.background = '#dc2626';

                setTimeout(() => {
                    button.innerHTML = '<span>Iniciar Sesión</span>';
                    button.style.pointerEvents = 'auto';
                    button.style.background = '';
                    alert('Error de conexión. Por favor, inténtalo de nuevo.');
                }, 1000);
            });
        }

        // Recuperación de contraseña
        document.getElementById('forgotPassword').addEventListener('click', function(e) {
            e.preventDefault();
            alert('Función de recuperación de contraseña en desarrollo');
        });
    </script>

</body>
</html>
