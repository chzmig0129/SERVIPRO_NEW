<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SedeModel;
use CodeIgniter\I18n\Time;

class SedesController extends BaseController
{
    public function index()
    {
        return view('prueba');
    }

    public function guardar()
    {
        // Validar los datos del formulario
        $validation = \Config\Services::validation();
        $validation->setRules([
            'nombre'    => 'required|max_length[255]',
            'direccion' => 'required|max_length[255]',
            'ciudad'    => 'required|max_length[100]',
            'pais'      => 'required|max_length[100]',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            log_message('error', 'Validation failed: ' . print_r($validation->getErrors(), true));
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Por favor, complete todos los campos correctamente.');
        }

        try {
            // Crear fecha actual en zona horaria de México
            $now = Time::now('America/Mexico_City');

            // Obtener los datos del formulario
            $data = [
                'nombre'         => $this->request->getPost('nombre'),
                'direccion'      => $this->request->getPost('direccion'),
                'ciudad'         => $this->request->getPost('ciudad'),
                'pais'           => $this->request->getPost('pais'),
                'fecha_creacion' => $now->format('Y-m-d H:i:s'),
                'estatus'        => 1  // Por defecto, las nuevas sedes están activas
            ];

            // Log de los datos que se intentan guardar
            log_message('info', 'Intentando guardar sede con datos: ' . print_r($data, true));

            // Guardar los datos en la base de datos
            $sedeModel = new SedeModel();
            $sedeModel->insert($data);

            // Log de éxito
            log_message('info', 'Sede guardada correctamente con ID: ' . $sedeModel->getInsertID());

            return redirect()->to('Inicio')
                            ->with('message', 'Sede guardada correctamente.');
        } catch (\Exception $e) {
            // Log del error
            log_message('error', 'Error al guardar sede: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Error al guardar la sede. Por favor, intente nuevamente.');
        }
    }

    public function listar()
    {
        // Cargar el modelo
        $sedeModel = new SedeModel();

        // Obtener todas las sedes
        $data['sedes'] = $sedeModel->findAll();

        // Cargar la vista y pasar los datos
        return view('prueba2', $data);
    }

    public function ver($id = null)
    {
        // Verificar si se proporcionó un ID
        if ($id === null) {
            return redirect()->to('/sedes/listar')->with('error', 'ID de sede no proporcionado.');
        }

        // Cargar el modelo
        $sedeModel = new SedeModel();

        // Obtener la sede por su ID
        $data['sede'] = $sedeModel->find($id);

        // Verificar si la sede existe
        if (empty($data['sede'])) {
            return redirect()->to('/sedes/listar')->with('error', 'Sede no encontrada.');
        }

        // Cargar la vista y pasar los datos
        return view('prueba3', $data);
    }
    
    public function vista()
    {
        return view('welcome_message');
    }

    public function crear_sedes_prueba()
    {
        $output = '<h1>Creación de Sedes de Prueba</h1>';
        
        try {
            // Verificar si ya existen registros en la tabla sedes
            $db = \Config\Database::connect();
            $query = $db->query('SELECT COUNT(*) as total FROM sedes');
            $row = $query->getRow();
            $totalSedes = $row->total;
            
            if ($totalSedes > 0) {
                $output .= "<p>Ya existen {$totalSedes} sedes en la base de datos.</p>";
                
                // Mostrar las sedes existentes
                $query = $db->query('SELECT * FROM sedes');
                $sedes = $query->getResultArray();
                
                $output .= '<h2>Sedes Existentes:</h2>';
                $output .= '<table border="1" cellpadding="5">';
                $output .= '<tr><th>ID</th><th>Nombre</th><th>Dirección</th><th>Ciudad</th><th>País</th></tr>';
                
                foreach ($sedes as $sede) {
                    $output .= '<tr>';
                    $output .= "<td>{$sede['id']}</td>";
                    $output .= "<td>{$sede['nombre']}</td>";
                    $output .= "<td>{$sede['direccion']}</td>";
                    $output .= "<td>{$sede['ciudad']}</td>";
                    $output .= "<td>{$sede['pais']}</td>";
                    $output .= '</tr>';
                }
                
                $output .= '</table>';
            } else {
                // Crear sedes de prueba
                $sedeModel = new \App\Models\SedeModel();
                
                // Datos de sedes de prueba
                $sedesPrueba = [
                    [
                        'nombre' => 'Sede Principal',
                        'direccion' => 'Av. Insurgentes Sur 1000',
                        'ciudad' => 'Ciudad de México',
                        'pais' => 'México',
                        'fecha_creacion' => date('Y-m-d H:i:s')
                    ],
                    [
                        'nombre' => 'Sede Norte',
                        'direccion' => 'Blvd. Manuel Ávila Camacho 1200',
                        'ciudad' => 'Monterrey',
                        'pais' => 'México',
                        'fecha_creacion' => date('Y-m-d H:i:s')
                    ],
                    [
                        'nombre' => 'Sede Sur',
                        'direccion' => 'Av. Chapultepec 500',
                        'ciudad' => 'Guadalajara',
                        'pais' => 'México',
                        'fecha_creacion' => date('Y-m-d H:i:s')
                    ]
                ];
                
                // Insertar las sedes
                foreach ($sedesPrueba as $sede) {
                    try {
                        $sedeModel->insert($sede);
                        $output .= "<p style='color: green;'>✓ Sede '{$sede['nombre']}' creada correctamente.</p>";
                    } catch (\Exception $e) {
                        $output .= "<p style='color: red;'>✗ Error al crear sede '{$sede['nombre']}': {$e->getMessage()}</p>";
                    }
                }
                
                $output .= '<p>Se han creado sedes de prueba. <a href="' . base_url('locations') . '">Ir a la página de sedes</a></p>';
            }
        } catch (\Exception $e) {
            $output .= "<p style='color: red;'>✗ Error: {$e->getMessage()}</p>";
        }
        
        return $output;
    }

    public function mostrar_sedes()
    {
        $output = "<h1>Información de sedes</h1>";
        
        try {
            // Obtener todas las sedes usando el modelo
            $sedeModel = new \App\Models\SedeModel();
            $sedes = $sedeModel->findAll();
            
            if (empty($sedes)) {
                $output .= "<p style='color: red;'>No hay sedes registradas en la base de datos.</p>";
                $output .= "<p><a href='" . base_url('sedes/crear_sedes_prueba') . "'>Crear sedes de prueba</a></p>";
            } else {
                $output .= "<p style='color: green;'>Se encontraron " . count($sedes) . " sedes.</p>";
                
                // Mostrar las sedes en una tabla
                $output .= "<table border='1' cellpadding='5'>";
                $output .= "<tr><th>ID</th><th>Nombre</th><th>Dirección</th><th>Ciudad</th><th>País</th><th>Fecha Creación</th></tr>";
                
                foreach ($sedes as $sede) {
                    $output .= "<tr>";
                    $output .= "<td>{$sede['id']}</td>";
                    $output .= "<td>{$sede['nombre']}</td>";
                    $output .= "<td>{$sede['direccion']}</td>";
                    $output .= "<td>{$sede['ciudad']}</td>";
                    $output .= "<td>{$sede['pais']}</td>";
                    $output .= "<td>{$sede['fecha_creacion']}</td>";
                    $output .= "</tr>";
                }
                
                $output .= "</table>";
            }
        } catch (\Exception $e) {
            $output .= "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
            
            // Mostrar información de la tabla
            try {
                $db = \Config\Database::connect();
                
                // Verificar si la tabla existe
                $query = $db->query("SHOW TABLES LIKE 'sedes'");
                $tableExists = $query->getNumRows() > 0;
                
                if ($tableExists) {
                    $output .= "<p>La tabla 'sedes' existe, pero hay un error al acceder a ella.</p>";
                    
                    // Mostrar la estructura de la tabla
                    $query = $db->query("DESCRIBE sedes");
                    $columns = $query->getResultArray();
                    
                    $output .= "<h3>Estructura de la tabla 'sedes':</h3>";
                    $output .= "<table border='1' cellpadding='5'>";
                    $output .= "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Clave</th><th>Predeterminado</th><th>Extra</th></tr>";
                    
                    foreach ($columns as $column) {
                        $output .= "<tr>";
                        $output .= "<td>{$column['Field']}</td>";
                        $output .= "<td>{$column['Type']}</td>";
                        $output .= "<td>{$column['Null']}</td>";
                        $output .= "<td>{$column['Key']}</td>";
                        $output .= "<td>{$column['Default']}</td>";
                        $output .= "<td>{$column['Extra']}</td>";
                        $output .= "</tr>";
                    }
                    
                    $output .= "</table>";
                } else {
                    $output .= "<p>La tabla 'sedes' no existe en la base de datos.</p>";
                    
                    // Mostrar las tablas existentes
                    $query = $db->query("SHOW TABLES");
                    $tables = $query->getResultArray();
                    
                    $output .= "<h3>Tablas existentes en la base de datos:</h3>";
                    $output .= "<ul>";
                    
                    foreach ($tables as $table) {
                        $tableName = array_values($table)[0];
                        $output .= "<li>{$tableName}</li>";
                    }
                    
                    $output .= "</ul>";
                    
                    // Ofrecer crear la tabla
                    $output .= "<p><a href='" . base_url('sedes/crear_tabla_sedes') . "'>Crear tabla 'sedes'</a></p>";
                }
            } catch (\Exception $dbError) {
                $output .= "<p style='color: red;'>Error al verificar la tabla: " . $dbError->getMessage() . "</p>";
            }
        }
        
        return $output;
    }

    public function crear_tabla_sedes()
    {
        $output = "<h1>Creación de tabla 'sedes'</h1>";
        
        try {
            $db = \Config\Database::connect();
            
            // Verificar si la tabla ya existe
            $query = $db->query("SHOW TABLES LIKE 'sedes'");
            $tableExists = $query->getNumRows() > 0;
            
            if ($tableExists) {
                $output .= "<p>La tabla 'sedes' ya existe en la base de datos.</p>";
                $output .= "<p><a href='" . base_url('mostrar-sedes') . "'>Ver sedes</a></p>";
                return $output;
            }
            
            // Crear la tabla
            $db->query("CREATE TABLE `sedes` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `nombre` varchar(255) NOT NULL,
                `direccion` varchar(255) NOT NULL,
                `ciudad` varchar(100) NOT NULL,
                `pais` varchar(100) NOT NULL,
                `fecha_creacion` datetime NOT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
            
            $output .= "<p style='color: green;'>La tabla 'sedes' ha sido creada correctamente.</p>";
            
            // Crear algunas sedes de ejemplo
            $sedesPrueba = [
                [
                    'nombre' => 'Sede Principal',
                    'direccion' => 'Av. Insurgentes Sur 1000',
                    'ciudad' => 'Ciudad de México',
                    'pais' => 'México',
                    'fecha_creacion' => date('Y-m-d H:i:s')
                ],
                [
                    'nombre' => 'Sede Norte',
                    'direccion' => 'Blvd. Manuel Ávila Camacho 1200',
                    'ciudad' => 'Monterrey',
                    'pais' => 'México',
                    'fecha_creacion' => date('Y-m-d H:i:s')
                ],
                [
                    'nombre' => 'Sede Sur',
                    'direccion' => 'Av. Chapultepec 500',
                    'ciudad' => 'Guadalajara',
                    'pais' => 'México',
                    'fecha_creacion' => date('Y-m-d H:i:s')
                ]
            ];
            
            // Insertar las sedes
            foreach ($sedesPrueba as $sede) {
                $db->table('sedes')->insert($sede);
                $output .= "<p>Sede '{$sede['nombre']}' creada correctamente.</p>";
            }
            
            $output .= "<p><a href='" . base_url('mostrar-sedes') . "'>Ver sedes</a></p>";
            $output .= "<p><a href='" . base_url('locations') . "'>Ir a la página de sedes</a></p>";
            
        } catch (\Exception $e) {
            $output .= "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
        }
        
        return $output;
    }

    /**
     * Deshabilita una sede cambiando su estatus a 0 (soft delete)
     * No elimina físicamente el registro para mantener la información sensible
     */
    public function deshabilitar($id = null)
    {
        // Verificar si se proporcionó un ID
        if ($id === null) {
            return redirect()->back()->with('error', 'ID de sede no proporcionado.');
        }

        try {
            // Cargar el modelo
            $sedeModel = new SedeModel();

            // Verificar que la sede existe
            $sede = $sedeModel->find($id);
            if (!$sede) {
                return redirect()->back()->with('error', 'Sede no encontrada.');
            }

            // Cambiar el estatus a 0 (deshabilitado)
            // Usar skipValidation para omitir las reglas de validación que esperan strings
            $resultado = $sedeModel->skipValidation(true)->update($id, ['estatus' => 0]);
            
            // Si el update falla, usar consulta directa como fallback
            if (!$resultado) {
                $db = \Config\Database::connect();
                $db->table('sedes')->where('id', $id)->update(['estatus' => 0]);
                
                // Verificar que se actualizó
                $sedeActualizada = $sedeModel->find($id);
                if (!$sedeActualizada || $sedeActualizada['estatus'] != 0) {
                    log_message('error', 'No se pudo actualizar el estatus de la sede ID: ' . $id);
                    throw new \Exception('No se pudo actualizar el estatus de la sede');
                }
            }
            
            log_message('info', 'Sede deshabilitada correctamente. ID: ' . $id . ', Estatus anterior: ' . ($sede['estatus'] ?? 'N/A'));

            // Redirigir según desde dónde se llamó
            $referer = $this->request->getHeaderLine('Referer');
            if (strpos($referer, 'blueprints') !== false) {
                return redirect()->to('/blueprints')->with('message', 'Planta deshabilitada correctamente. Ya no aparecerá en las vistas ni estadísticas.');
            }

            return redirect()->to('/Inicio')->with('message', 'Planta deshabilitada correctamente. Ya no aparecerá en las vistas ni estadísticas.');
        } catch (\Exception $e) {
            log_message('error', 'Error al deshabilitar sede ID ' . $id . ': ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Error al deshabilitar la planta: ' . $e->getMessage());
        }
    }
}