<?php

namespace App\Controllers;
use App\Models\SedeModel;
use App\Models\TrampaModel;
use App\Models\PlanoModel;
use App\Models\UsuarioModel;

class Home extends BaseController
{
    public function index(): string
    {
        // Cargar los modelos necesarios
        $sedeModel = new SedeModel();
        $trampaModel = new TrampaModel();
        $planoModel = new PlanoModel();

        // Obtener solo las sedes activas (estatus = 1)
        $sedes = $sedeModel->where('estatus', 1)->findAll();

        // Calcular estadísticas para cada sede
        foreach ($sedes as &$sede) {
            $sede['total_planos'] = $planoModel->where('sede_id', $sede['id'])->countAllResults();
            $sede['total_trampas'] = $trampaModel->where('sede_id', $sede['id'])->countAllResults();
        }

        // Pasar los datos a la vista
        $data = [
            'sedes' => $sedes
        ];

        return view('login', $data);
    }

    // Función de diagnóstico para base de datos
    public function db_diagnostico(): string
    {
        $output = '<h1>Diagnóstico de Base de Datos</h1>';
        
        try {
            // Conectar a la base de datos
            $db = \Config\Database::connect();
            $output .= '<p style="color: green;">✓ Conexión a la base de datos establecida correctamente.</p>';
            
            // Verificar la tabla sedes
            $query = $db->query('SHOW TABLES LIKE "sedes"');
            $existeTabla = $query->getNumRows() > 0;
            
            if ($existeTabla) {
                $output .= '<p style="color: green;">✓ La tabla "sedes" existe.</p>';
                
                // Contar registros
                $query = $db->query('SELECT COUNT(*) as total FROM sedes');
                $row = $query->getRow();
                $totalSedes = $row->total;
                
                if ($totalSedes > 0) {
                    $output .= "<p style=\"color: green;\">✓ La tabla \"sedes\" contiene {$totalSedes} registros.</p>";
                    
                    // Mostrar los primeros 10 registros
                    $query = $db->query('SELECT * FROM sedes LIMIT 10');
                    $sedes = $query->getResultArray();
                    
                    $output .= '<h2>Registros en la tabla "sedes":</h2>';
                    $output .= '<table border="1" cellpadding="5">';
                    $output .= '<tr><th>ID</th><th>Nombre</th><th>Dirección</th><th>Ciudad</th><th>País</th><th>Fecha Creación</th></tr>';
                    
                    foreach ($sedes as $sede) {
                        $output .= '<tr>';
                        $output .= "<td>{$sede['id']}</td>";
                        $output .= "<td>{$sede['nombre']}</td>";
                        $output .= "<td>{$sede['direccion']}</td>";
                        $output .= "<td>{$sede['ciudad']}</td>";
                        $output .= "<td>{$sede['pais']}</td>";
                        $output .= "<td>{$sede['fecha_creacion']}</td>";
                        $output .= '</tr>';
                    }
                    
                    $output .= '</table>';
                } else {
                    $output .= '<p style="color: red;">✗ La tabla "sedes" no contiene registros.</p>';
                    $output .= '<p>Consejo: Necesita agregar al menos una sede para que el selector funcione.</p>';
                }
            } else {
                $output .= '<p style="color: red;">✗ La tabla "sedes" no existe.</p>';
                
                // Mostrar las tablas existentes
                $query = $db->query('SHOW TABLES');
                $tables = $query->getResultArray();
                
                $output .= '<h2>Tablas existentes en la base de datos:</h2>';
                $output .= '<ul>';
                
                foreach ($tables as $table) {
                    $tableName = array_values($table)[0];
                    $output .= "<li>{$tableName}</li>";
                }
                
                $output .= '</ul>';
            }
            
            // Verificar la estructura del modelo SedeModel
            $output .= '<h2>Verificación del Modelo SedeModel:</h2>';
            try {
                $model = new \App\Models\SedeModel();
                $output .= '<p style="color: green;">✓ Modelo SedeModel cargado correctamente.</p>';
                
                // Mostrar información del modelo
                $output .= '<p>Tabla: ' . $model->table . '</p>';
                $output .= '<p>Clave Primaria: ' . $model->primaryKey . '</p>';
                $output .= '<p>Campos permitidos: ' . implode(', ', $model->allowedFields) . '</p>';
            } catch (\Exception $e) {
                $output .= '<p style="color: red;">✗ Error al cargar el modelo SedeModel: ' . $e->getMessage() . '</p>';
                
                // Intentar leer el archivo directamente
                $modelFile = APPPATH . 'Models/SedeModel.php';
                if (file_exists($modelFile)) {
                    $output .= '<p>El archivo del modelo existe. Contenido:</p>';
                    $output .= '<pre>' . htmlspecialchars(file_get_contents($modelFile)) . '</pre>';
                } else {
                    $output .= '<p>El archivo del modelo no existe en: ' . $modelFile . '</p>';
                }
            }
            
        } catch (\Exception $e) {
            $output .= '<p style="color: red;">✗ Error de conexión a la base de datos: ' . $e->getMessage() . '</p>';
        }
        
        return $output;
    }

    public function authenticate()
    {
        try {
            // Obtener datos del formulario
            $usuario = $this->request->getPost('usuario');
            $password = $this->request->getPost('password');

            // Validaciones básicas
            if (empty($usuario) || empty($password)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario y contraseña son requeridos'
                ]);
            }
            
            // Cargar el modelo de usuarios
            $usuarioModel = new UsuarioModel();
            
            // Verificar si el usuario existe por correo
            $user = $usuarioModel->where('correo', $usuario)->first();
            
            // Si no lo encuentra por correo, verificar por nombre de usuario
            if (!$user) {
                $user = $usuarioModel->where('nombre', $usuario)->first();
            }
            
            // Si no se encuentra el usuario
            if (!$user) {
                // Registrar intento de login fallido
                log_login(0, $usuario, false);
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ]);
            }
            
            // Verificar contraseña
            if (!isset($user['password']) || $user['password'] != $password) {
                // Registrar intento de login fallido
                log_login($user['id'], $user['nombre'], false);
                
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Contraseña incorrecta'
                ]);
            }
            
            // Si todo está correcto, iniciar sesión
            $session = session();
            $userData = [
                'id' => $user['id'],
                'nombre' => $user['nombre'],
                'correo' => $user['correo'] ?? '',
                'logged_in' => true
            ];
            $session->set($userData);
            
            // Registrar login exitoso
            log_login($user['id'], $user['nombre'], true);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => '¡Bienvenido!',
                'user' => $userData
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error en authenticate: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error en el servidor'
            ]);
        }
    }

    public function crear_usuario_prueba()
    {
        try {
            // Conectar a la base de datos
            $db = \Config\Database::connect();
            
            // Verificar si existe la tabla usuarios
            $tableExists = $db->tableExists('usuarios');
            
            if (!$tableExists) {
                // Crear la tabla usuarios
                $forge = \Config\Database::forge();
                
                $fields = [
                    'id' => [
                        'type' => 'INT',
                        'constraint' => 11,
                        'unsigned' => true,
                        'auto_increment' => true,
                    ],
                    'nombre' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                    ],
                    'correo' => [
                        'type' => 'VARCHAR',
                        'constraint' => 100,
                    ],
                    'password' => [
                        'type' => 'VARCHAR',
                        'constraint' => 255,
                    ],
                ];
                
                $forge->addField($fields);
                $forge->addKey('id', true);
                $forge->createTable('usuarios', true);
                
                echo "Tabla usuarios creada correctamente.<br>";
            } else {
                echo "La tabla usuarios ya existe.<br>";
            }
            
            // Crear usuario de prueba
            $usuarioModel = new UsuarioModel();
            
            // Verificar si el usuario admin ya existe
            $admin = $usuarioModel->where('correo', 'admin@servipro.com')->first();
            
            if (!$admin) {
                $data = [
                    'nombre' => 'Administrador',
                    'correo' => 'admin@servipro.com',
                    'password' => 'admin123'
                ];
                
                $usuarioModel->insert($data);
                echo "Usuario de prueba creado correctamente.<br>";
                echo "Correo: admin@servipro.com<br>";
                echo "Contraseña: admin123<br>";
            } else {
                echo "El usuario de prueba ya existe.<br>";
                echo "Correo: admin@servipro.com<br>";
                echo "Contraseña: admin123<br>";
            }
            
            return "¡Listo! Ahora puedes iniciar sesión con el usuario de prueba.";
            
        } catch (\Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    /**
     * Cierra la sesión del usuario y redirige al login
     */
    public function logout()
    {
        $session = session();
        
        // Registrar logout antes de destruir la sesión
        $usuarioId = $session->get('id');
        $usuarioNombre = $session->get('nombre') ?? 'Usuario';
        
        if ($usuarioId) {
            log_logout($usuarioId, $usuarioNombre);
        }
        
        $session->destroy();
        return redirect()->to('/');
    }
}
