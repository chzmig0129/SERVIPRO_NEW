<?php

namespace App\Controllers;

use App\Models\QuejaModel;
use App\Models\SedeModel;
use CodeIgniter\Controller;

class QuejasController extends Controller
{
    protected $quejaModel;
    protected $sedeModel;

    public function __construct()
    {
        $this->quejaModel = new QuejaModel();
        $this->sedeModel = new SedeModel();
    }

    public function index()
    {
        $sedeId = $this->request->getGet('sede_id');
        $fechaInicio = $this->request->getGet('fecha_inicio');
        $fechaFin = $this->request->getGet('fecha_fin');
        $estadoQueja = $this->request->getGet('estado_queja');
        
        // Obtener solo las sedes activas (estatus = 1) para el filtro
        $data['sedes'] = $this->sedeModel->where('estatus', 1)->findAll();
        
        // Construir la consulta base
        $builder = $this->quejaModel
            ->select('quejas.*, sedes.nombre as nombre_sede')
            ->join('sedes', 'sedes.id = quejas.sede_id');

        // Aplicar filtro si se seleccionó una sede
        if ($sedeId) {
            $builder->where('quejas.sede_id', $sedeId);
        }

        // Aplicar filtros de fecha
        if ($fechaInicio) {
            $builder->where('DATE(quejas.fecha) >=', $fechaInicio);
        }
        if ($fechaFin) {
            $builder->where('DATE(quejas.fecha) <=', $fechaFin);
        }
        
        // Aplicar filtro de estado de queja
        if ($estadoQueja) {
            $builder->where('quejas.estado_queja', $estadoQueja);
        }

        // Obtener las quejas filtradas
        $data['quejas'] = $builder->orderBy('fecha', 'DESC')->findAll();
        
        // Guardar los filtros seleccionados para mantenerlos en la vista
        $data['sede_seleccionada'] = $sedeId;
        $data['fecha_inicio'] = $fechaInicio;
        $data['fecha_fin'] = $fechaFin;
        $data['estado_queja_seleccionado'] = $estadoQueja;

        return view('quejas/index', $data);
    }

    public function estadisticas()
    {
        $db = \Config\Database::connect();
        $sedeId = $this->request->getGet('sede_id');
        $fechaInicio = $this->request->getGet('fecha_inicio');
        $fechaFin = $this->request->getGet('fecha_fin');
        
        // Obtener solo las sedes activas (estatus = 1) para el filtro
        $data['sedes'] = $this->sedeModel->where('estatus', 1)->findAll();
        $data['sede_seleccionada'] = $sedeId;
        $data['fecha_inicio'] = $fechaInicio;
        $data['fecha_fin'] = $fechaFin;

        // Condición WHERE base para el filtro de sede y fechas
        $whereSedeCondition = $sedeId ? "AND q.sede_id = $sedeId" : "";
        $whereFechaInicio = $fechaInicio ? "AND DATE(q.fecha) >= '$fechaInicio'" : "";
        $whereFechaFin = $fechaFin ? "AND DATE(q.fecha) <= '$fechaFin'" : "";
        $whereConditions = "WHERE 1=1 $whereSedeCondition $whereFechaInicio $whereFechaFin";
        
        // Obtener conteo de quejas por semana para el año actual y anterior
        $querySemanal = $db->query("
            SELECT 
                YEAR(fecha) as año,
                WEEK(fecha) as semana,
                COUNT(*) as total
            FROM quejas q
            $whereConditions
            GROUP BY YEAR(fecha), WEEK(fecha)
            ORDER BY YEAR(fecha), WEEK(fecha)
        ");
        $data['estadisticasSemanales'] = $querySemanal->getResultArray();

        // Obtener frecuencia de líneas afectadas
        $queryLineas = $db->query("
            SELECT 
                lineas,
                COUNT(*) as frecuencia
            FROM quejas q
            $whereConditions
            GROUP BY lineas
            ORDER BY frecuencia DESC
        ");
        $data['estadisticasLineas'] = $queryLineas->getResultArray();

        // Obtener conteo de quejas por año
        $queryAnual = $db->query("
            SELECT YEAR(fecha) as año, COUNT(*) as total
            FROM quejas q
            $whereConditions
            GROUP BY YEAR(fecha)
            ORDER BY año DESC
        ");
        $data['estadisticasAnuales'] = $queryAnual->getResultArray();

        // Obtener conteo de tipos de insectos
        $queryInsectos = $db->query("
            SELECT insecto, COUNT(*) as total
            FROM quejas q
            $whereConditions
            GROUP BY insecto
            ORDER BY total DESC
        ");
        $data['estadisticasInsectos'] = $queryInsectos->getResultArray();

        // Obtener conteo por clasificación
        $queryClasificacion = $db->query("
            SELECT clasificacion, COUNT(*) as total
            FROM quejas q
            $whereConditions
            GROUP BY clasificacion
            ORDER BY 
                CASE 
                    WHEN clasificacion = 'Crítico' THEN 1
                    WHEN clasificacion = 'Alto' THEN 2
                    WHEN clasificacion = 'Medio' THEN 3
                    WHEN clasificacion = 'Bajo' THEN 4
                    ELSE 5
                END
        ");
        $data['estadisticasClasificacion'] = $queryClasificacion->getResultArray();

        // Obtener conteo por estado
        $queryEstado = $db->query("
            SELECT estado, COUNT(*) as total
            FROM quejas q
            $whereConditions
            GROUP BY estado
            ORDER BY estado
        ");
        $data['estadisticasEstado'] = $queryEstado->getResultArray();

        // Obtener conteo por estado_queja
        $queryEstadoQueja = $db->query("
            SELECT 
                estado_queja,
                COUNT(*) as total,
                ROUND((COUNT(*) * 100.0) / (SELECT COUNT(*) FROM quejas q $whereConditions), 2) as porcentaje
            FROM quejas q
            $whereConditions
            GROUP BY estado_queja
            ORDER BY estado_queja
        ");
        $data['estadisticasEstadoQueja'] = $queryEstadoQueja->getResultArray();

        // Obtener estadísticas por sede (solo si no hay filtro de sede)
        if (!$sedeId) {
            $queryPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_quejas,
                    COUNT(DISTINCT q.lineas) as total_lineas_afectadas
                FROM quejas q
                JOIN sedes s ON s.id = q.sede_id
                " . str_replace('q.sede_id', 'q.sede_id', $whereConditions) . "
                GROUP BY q.sede_id, s.nombre
                ORDER BY total_quejas DESC
            ");
            $data['estadisticasPorSede'] = $queryPorSede->getResultArray();
        } else {
            // Si hay filtro, obtener solo los datos de la sede seleccionada
            $queryPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_quejas,
                    COUNT(DISTINCT q.lineas) as total_lineas_afectadas
                FROM quejas q
                JOIN sedes s ON s.id = q.sede_id
                WHERE q.sede_id = $sedeId
                " . ($fechaInicio ? "AND DATE(q.fecha) >= '$fechaInicio'" : "") . "
                " . ($fechaFin ? "AND DATE(q.fecha) <= '$fechaFin'" : "") . "
                GROUP BY q.sede_id, s.nombre
            ");
            $data['estadisticasPorSede'] = $queryPorSede->getResultArray();
        }

        // Si hay una sede seleccionada, obtener su nombre
        if ($sedeId) {
            $sede = $this->sedeModel->find($sedeId);
            $data['nombre_sede_seleccionada'] = $sede ? $sede['nombre'] : '';
        }

        return view('quejas/estadisticas', $data);
    }

    public function new()
    {
        $data['sedes'] = $this->sedeModel->findAll();
        
        // Obtener las ubicaciones únicas de las trampas
        $db = \Config\Database::connect();
        $query = $db->query("SELECT DISTINCT ubicacion FROM trampas ORDER BY ubicacion ASC");
        $data['ubicaciones_trampas'] = $query->getResultArray();
        
        return view('quejas/create', $data);
    }

    public function create()
    {
        // Log de los datos recibidos
        log_message('info', 'Datos recibidos en create: ' . print_r($this->request->getPost(), true));

        // Validación de datos
        $rules = [
            'fecha' => 'required|valid_date',
            'insecto' => 'required|min_length[3]|max_length[100]',
            'ubicacion' => 'required|max_length[255]',
            'lineas' => 'required|max_length[100]',
            'clasificacion' => 'required|in_list[Crítico,Alto,Medio,Bajo]',
            'sede_id' => 'required|integer|is_not_unique[sedes.id]',
            'estado' => 'required|in_list[Vivo,Muerto]',
            'estado_queja' => 'required|in_list[Pendiente,En Proceso,Resuelta,Cerrada]'
        ];

        // Validar archivo si se subió uno
        $archivo = $this->request->getFile('archivo');
        if ($archivo && $archivo->isValid()) {
            $rules['archivo'] = 'uploaded[archivo]|max_size[archivo,5120]|ext_in[archivo,pdf,png,jpg,jpeg]';
        }

        if (!$this->validate($rules)) {
            // Log de errores de validación
            log_message('error', 'Errores de validación: ' . print_r($this->validator->getErrors(), true));
            
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Por favor, verifique los datos ingresados.');
        }

        try {
            $data = [
                'fecha' => date('Y-m-d', strtotime($this->request->getPost('fecha'))),
                'insecto' => $this->request->getPost('insecto'),
                'ubicacion' => $this->request->getPost('ubicacion'),
                'lineas' => $this->request->getPost('lineas'),
                'clasificacion' => $this->request->getPost('clasificacion'),
                'sede_id' => $this->request->getPost('sede_id'),
                'estado' => $this->request->getPost('estado'),
                'estado_queja' => $this->request->getPost('estado_queja')
            ];

            // Manejar archivo si se subió uno
            if ($archivo && $archivo->isValid()) {
                $nombreArchivo = $archivo->getRandomName();
                $archivo->move(FCPATH . 'uploads/quejas/', $nombreArchivo);
                $data['archivo'] = $nombreArchivo;
                
                log_message('info', 'Archivo subido: ' . $nombreArchivo);
            }

            // Log de los datos a insertar
            log_message('info', 'Intentando insertar datos: ' . print_r($data, true));

            if ($this->quejaModel->insert($data)) {
                return redirect()->to('/quejas')->with('success', 'Queja registrada correctamente');
            }

            // Log si falla la inserción
            log_message('error', 'Error al insertar: ' . print_r($this->quejaModel->errors(), true));

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar la queja. Por favor, intente nuevamente.');
        } catch (\Exception $e) {
            // Log de cualquier excepción
            log_message('error', 'Excepción al crear queja: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $data['queja'] = $this->quejaModel->find($id);
        $data['sedes'] = $this->sedeModel->findAll();
        
        // Obtener las ubicaciones únicas de las trampas
        $db = \Config\Database::connect();
        $query = $db->query("SELECT DISTINCT ubicacion FROM trampas ORDER BY ubicacion ASC");
        $data['ubicaciones_trampas'] = $query->getResultArray();
        
        if (empty($data['queja'])) {
            return redirect()->to('/quejas')->with('error', 'Queja no encontrada');
        }

        return view('quejas/edit', $data);
    }

    public function update($id)
    {
        // Validación de datos
        $rules = [
            'fecha' => 'required|valid_date',
            'insecto' => 'required|min_length[3]|max_length[100]',
            'ubicacion' => 'required|max_length[255]',
            'lineas' => 'required|max_length[100]',
            'clasificacion' => 'required|in_list[Crítico,Alto,Medio,Bajo]',
            'sede_id' => 'required|integer|is_not_unique[sedes.id]',
            'estado' => 'required|in_list[Vivo,Muerto]',
            'estado_queja' => 'required|in_list[Pendiente,En Proceso,Resuelta,Cerrada]'
        ];

        // Validar archivo si se subió uno
        $archivo = $this->request->getFile('archivo');
        if ($archivo && $archivo->isValid()) {
            $rules['archivo'] = 'uploaded[archivo]|max_size[archivo,5120]|ext_in[archivo,pdf,png,jpg,jpeg]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Por favor, verifique los datos ingresados.');
        }

        try {
            $data = [
                'fecha' => date('Y-m-d', strtotime($this->request->getPost('fecha'))),
                'insecto' => $this->request->getPost('insecto'),
                'ubicacion' => $this->request->getPost('ubicacion'),
                'lineas' => $this->request->getPost('lineas'),
                'clasificacion' => $this->request->getPost('clasificacion'),
                'sede_id' => $this->request->getPost('sede_id'),
                'estado' => $this->request->getPost('estado'),
                'estado_queja' => $this->request->getPost('estado_queja')
            ];

            // Manejar archivo si se subió uno nuevo
            if ($archivo && $archivo->isValid()) {
                // Obtener la queja actual para eliminar el archivo anterior
                $quejaActual = $this->quejaModel->find($id);
                if ($quejaActual && !empty($quejaActual['archivo'])) {
                    $archivoAnterior = FCPATH . 'uploads/quejas/' . $quejaActual['archivo'];
                    if (file_exists($archivoAnterior)) {
                        unlink($archivoAnterior);
                        log_message('info', 'Archivo anterior eliminado: ' . $quejaActual['archivo']);
                    }
                }
                
                // Subir el nuevo archivo
                $nombreArchivo = $archivo->getRandomName();
                $archivo->move(FCPATH . 'uploads/quejas/', $nombreArchivo);
                $data['archivo'] = $nombreArchivo;
                
                log_message('info', 'Nuevo archivo subido: ' . $nombreArchivo);
            }

            if ($this->quejaModel->update($id, $data)) {
                return redirect()->to('/quejas')->with('success', 'Queja actualizada correctamente');
            }

            return redirect()->back()->withInput()->with('error', 'Error al actualizar la queja');
        } catch (\Exception $e) {
            log_message('error', 'Excepción al actualizar queja: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if ($this->quejaModel->delete($id)) {
            return redirect()->to('/quejas')->with('success', 'Queja eliminada correctamente');
        }

        return redirect()->to('/quejas')->with('error', 'Error al eliminar la queja');
    }

    /**
     * Actualiza el estado de una queja vía AJAX
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function actualizarEstado()
    {
        // Verificar si es una solicitud AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Método no permitido'
            ]);
        }
        
        try {
            // Obtener datos del request
            $quejaId = $this->request->getPost('queja_id');
            $nuevoEstado = $this->request->getPost('estado_queja');
            
            // Validar datos
            if (empty($quejaId) || empty($nuevoEstado)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Datos incompletos para actualizar el estado'
                ]);
            }
            
            // Validar que el estado sea válido
            $estadosValidos = ['Pendiente', 'Resuelta', 'En Proceso', 'Cerrada'];
            if (!in_array($nuevoEstado, $estadosValidos)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Estado no válido'
                ]);
            }
            
            // Actualizar el estado en la base de datos
            $actualizado = $this->quejaModel->update($quejaId, [
                'estado_queja' => $nuevoEstado,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            if ($actualizado) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Estado de la queja actualizado correctamente'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al actualizar el estado de la queja: ' . 
                                json_encode($this->quejaModel->errors())
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error al actualizar estado de queja: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar el estado: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Genera un PDF con las estadísticas de quejas
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function generarPDF()
    {
        // Obtener los mismos datos que para la vista de estadísticas
        $db = \Config\Database::connect();
        $sedeId = $this->request->getGet('sede_id');
        
        // Verificar si se están recibiendo datos de gráficos por POST
        $chartImages = $this->request->getPost('chart_images');
        $usarChartImages = !empty($chartImages);
        
        // Obtener solo las sedes activas (estatus = 1) para el filtro
        $data['sedes'] = $this->sedeModel->where('estatus', 1)->findAll();
        $data['sede_seleccionada'] = $sedeId;

        // Condición WHERE base para el filtro de sede
        $whereSedeCondition = $sedeId ? "AND q.sede_id = $sedeId" : "";
        
        // Obtener estadísticas por insecto
        $queryInsectos = $db->query("
            SELECT 
                insecto,
                COUNT(*) as frecuencia
            FROM quejas q
            WHERE 1=1 $whereSedeCondition
            GROUP BY insecto
            ORDER BY frecuencia DESC
        ");
        $data['estadisticasInsectos'] = $queryInsectos->getResultArray();

        // Obtener conteo de quejas por clasificación
        $queryClasificacion = $db->query("
            SELECT 
                clasificacion, 
                COUNT(*) as total
            FROM quejas q
            WHERE 1=1 $whereSedeCondition
            GROUP BY clasificacion
            ORDER BY FIELD(clasificacion, 'Crítico', 'Alto', 'Medio', 'Bajo')
        ");
        $data['estadisticasClasificacion'] = $queryClasificacion->getResultArray();
        
        // Obtener estadísticas por ubicación
        $queryUbicacion = $db->query("
            SELECT 
                ubicacion,
                COUNT(*) as total
            FROM quejas q
            WHERE 1=1 $whereSedeCondition
            GROUP BY ubicacion
            ORDER BY total DESC
        ");
        $data['estadisticasUbicacion'] = $queryUbicacion->getResultArray();

        // Obtener estadísticas por mes
        $queryMensual = $db->query("
            SELECT 
                DATE_FORMAT(fecha, '%Y-%m') as mes,
                COUNT(*) as total
            FROM quejas q
            WHERE 1=1 $whereSedeCondition
            GROUP BY DATE_FORMAT(fecha, '%Y-%m')
            ORDER BY mes DESC
        ");
        $data['estadisticasMensuales'] = $queryMensual->getResultArray();

        // Obtener estadísticas por sede (solo si no hay filtro)
        if (!$sedeId) {
            $queryPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_quejas
                FROM quejas q
                JOIN sedes s ON s.id = q.sede_id
                GROUP BY q.sede_id, s.nombre
                ORDER BY total_quejas DESC
            ");
            $data['estadisticasPorSede'] = $queryPorSede->getResultArray();
        } else {
            // Si hay filtro, obtener solo los datos de la sede seleccionada
            $queryPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_quejas
                FROM quejas q
                JOIN sedes s ON s.id = q.sede_id
                WHERE q.sede_id = $sedeId
                GROUP BY q.sede_id, s.nombre
            ");
            $data['estadisticasPorSede'] = $queryPorSede->getResultArray();
        }
        
        // Calcular estadísticas generales
        $totalQuejas = 0;
        $totalCriticos = 0;
        
        if (!empty($data['estadisticasClasificacion'])) {
            foreach ($data['estadisticasClasificacion'] as $estadistica) {
                $totalQuejas += $estadistica['total'];
                if ($estadistica['clasificacion'] === 'Crítico') {
                    $totalCriticos = $estadistica['total'];
                }
            }
        }
        
        $data['totalQuejas'] = $totalQuejas;
        $data['totalCriticos'] = $totalCriticos;
        
        // Si hay una sede seleccionada, obtener su nombre
        if ($sedeId) {
            $sede = $this->sedeModel->find($sedeId);
            $data['nombre_sede_seleccionada'] = $sede ? $sede['nombre'] : '';
        }
        
        // Pasar las imágenes de gráficos si se recibieron
        if ($usarChartImages) {
            $data['chart_images'] = json_decode($chartImages, true);
        }
        
        // Cargar la vista especial para PDF
        $html = view('quejas/pdf_estadisticas', $data);

        // Configurar opciones de DOMPDF
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isFontSubsettingEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        
        // Crear instancia de DOMPDF
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Cargar el HTML
        $dompdf->loadHtml($html);
        
        // Configurar papel y orientación
        $dompdf->setPaper('A4', 'portrait');
        
        // Renderizar el PDF
        $dompdf->render();
        
        // Nombre del archivo
        $filename = 'Estadisticas_Quejas_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Enviar el archivo al navegador
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }
    
    /**
     * Genera un PDF con las estadísticas de quejas incluyendo gráficas
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function generarPDFConGraficas()
    {
        // Obtener los mismos datos que para la vista de estadísticas
        $db = \Config\Database::connect();
        $sedeId = $this->request->getGet('sede_id');
        
        // Obtener solo las sedes activas (estatus = 1) para el filtro
        $data['sedes'] = $this->sedeModel->where('estatus', 1)->findAll();
        $data['sede_seleccionada'] = $sedeId;

        // Condición WHERE base para el filtro de sede
        $whereSedeCondition = $sedeId ? "AND q.sede_id = $sedeId" : "";
        
        // Obtener conteo de quejas por semana para el año actual y anterior
        $querySemanal = $db->query("
            SELECT 
                YEAR(fecha) as año,
                WEEK(fecha) as semana,
                COUNT(*) as total
            FROM quejas q
            WHERE YEAR(fecha) IN (YEAR(CURRENT_DATE), YEAR(CURRENT_DATE) - 1)
            $whereSedeCondition
            GROUP BY YEAR(fecha), WEEK(fecha)
            ORDER BY YEAR(fecha), WEEK(fecha)
        ");
        $data['estadisticasSemanales'] = $querySemanal->getResultArray();

        // Obtener frecuencia de líneas afectadas
        $queryLineas = $db->query("
            SELECT 
                lineas,
                COUNT(*) as frecuencia
            FROM quejas q
            WHERE 1=1 $whereSedeCondition
            GROUP BY lineas
            ORDER BY frecuencia DESC
        ");
        $data['estadisticasLineas'] = $queryLineas->getResultArray();

        // Obtener conteo de quejas por año
        $queryAnual = $db->query("
            SELECT YEAR(fecha) as año, COUNT(*) as total
            FROM quejas q
            WHERE 1=1 $whereSedeCondition
            GROUP BY YEAR(fecha)
            ORDER BY año DESC
        ");
        $data['estadisticasAnuales'] = $queryAnual->getResultArray();

        // Obtener conteo de tipos de insectos
        $queryInsectos = $db->query("
            SELECT insecto, COUNT(*) as total, COUNT(*) as frecuencia
            FROM quejas q
            WHERE 1=1 $whereSedeCondition
            GROUP BY insecto
            ORDER BY total DESC
        ");
        $data['estadisticasInsectos'] = $queryInsectos->getResultArray();

        // Obtener conteo por clasificación
        $queryClasificacion = $db->query("
            SELECT clasificacion, COUNT(*) as total
            FROM quejas q
            WHERE 1=1 $whereSedeCondition
            GROUP BY clasificacion
            ORDER BY 
                CASE 
                    WHEN clasificacion = 'Crítico' THEN 1
                    WHEN clasificacion = 'Alto' THEN 2
                    WHEN clasificacion = 'Medio' THEN 3
                    WHEN clasificacion = 'Bajo' THEN 4
                    ELSE 5
                END
        ");
        $data['estadisticasClasificacion'] = $queryClasificacion->getResultArray();

        // Obtener conteo por estado
        $queryEstado = $db->query("
            SELECT estado, COUNT(*) as total
            FROM quejas q
            WHERE 1=1 $whereSedeCondition
            GROUP BY estado
            ORDER BY estado
        ");
        $data['estadisticasEstado'] = $queryEstado->getResultArray();
        
        // Calcular estadísticas generales
        $totalQuejas = 0;
        $totalCriticos = 0;
        
        if (!empty($data['estadisticasClasificacion'])) {
            foreach ($data['estadisticasClasificacion'] as $estadistica) {
                $totalQuejas += $estadistica['total'];
                if ($estadistica['clasificacion'] === 'Crítico') {
                    $totalCriticos = $estadistica['total'];
                }
            }
        }
        
        $data['totalQuejas'] = $totalQuejas;
        $data['totalCriticos'] = $totalCriticos;
        
        // Si hay una sede seleccionada, obtener su nombre
        if ($sedeId) {
            $sede = $this->sedeModel->find($sedeId);
            $data['nombre_sede_seleccionada'] = $sede ? $sede['nombre'] : '';
        }
        
        // Cargar la vista de PDF con gráficas
        return view('quejas/pdf_con_graficas', $data);
    }
} 