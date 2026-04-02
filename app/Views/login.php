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

        /* ── Password toggle ── */
        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 42px;
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            padding: 4px;
            color: var(--muted);
            transition: color 0.2s ease;
            display: flex;
            align-items: center;
        }

        .password-toggle:hover {
            color: var(--text);
        }

        .password-toggle svg {
            width: 18px;
            height: 18px;
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
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Ingresa tu contraseña"
                        required
                        autocomplete="current-password">
                    <button type="button" class="password-toggle" id="togglePassword" aria-label="Mostrar contraseña">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <svg id="eyeOffIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" style="display:none;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div style="margin-bottom: 24px;"></div>

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

        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');
            const eyeOffIcon = document.getElementById('eyeOffIcon');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.style.display = 'none';
                eyeOffIcon.style.display = 'block';
                this.setAttribute('aria-label', 'Ocultar contraseña');
            } else {
                passwordInput.type = 'password';
                eyeIcon.style.display = 'block';
                eyeOffIcon.style.display = 'none';
                this.setAttribute('aria-label', 'Mostrar contraseña');
            }
        });
    </script>

</body>
</html>
