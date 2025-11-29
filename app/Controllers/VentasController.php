<?php

namespace App\Controllers;

use App\Models\VentaModel;
use App\Models\SedeModel;
use App\Models\UsuarioModel;
use CodeIgniter\Controller;

class VentasController extends Controller
{
    protected $ventaModel;
    protected $sedeModel;
    protected $usuarioModel;

    public function __construct()
    {
        // Load the form helper
        helper('form');
        
        $this->ventaModel = new VentaModel();
        $this->sedeModel = new SedeModel();
        // Verificar si existe el modelo de usuario, de lo contrario, usar un array vacío
        if (class_exists('\\App\\Models\\UsuarioModel')) {
            $this->usuarioModel = new UsuarioModel();
        } else {
            $this->usuarioModel = null;
        }
    }

    public function index()
    {
        $sedeId = $this->request->getGet('sede_id');
        $fechaInicio = $this->request->getGet('fecha_inicio');
        $fechaFin = $this->request->getGet('fecha_fin');
        
        // Obtener todas las sedes para el filtro
        $data['sedes'] = $this->sedeModel->findAll();
        
        // Construir la consulta base
        $builder = $this->ventaModel
            ->select('ventas.*, sedes.nombre as nombre_sede')
            ->join('sedes', 'sedes.id = ventas.sede_id');

        // Si existe el modelo de usuario, unirlo
        if ($this->usuarioModel !== null) {
            $builder->select('usuarios.nombre as nombre_usuario')
                   ->join('usuarios', 'usuarios.id = ventas.usuario_id', 'left');
        }

        // Aplicar filtros
        if ($sedeId) {
            $builder->where('ventas.sede_id', $sedeId);
        }
        if ($fechaInicio) {
            $builder->where('DATE(ventas.fecha) >=', $fechaInicio);
        }
        if ($fechaFin) {
            $builder->where('DATE(ventas.fecha) <=', $fechaFin);
        }

        // Obtener las ventas filtradas
        $data['ventas'] = $builder->orderBy('fecha', 'DESC')->findAll();
        
        // Guardar los filtros seleccionados para mantenerlos
        $data['sede_seleccionada'] = $sedeId;
        $data['fecha_inicio'] = $fechaInicio;
        $data['fecha_fin'] = $fechaFin;

        return view('ventas/index', $data);
    }

    public function estadisticas()
    {
        $db = \Config\Database::connect();
        $sedeId = $this->request->getGet('sede_id');
        $fechaInicio = $this->request->getGet('fecha_inicio');
        $fechaFin = $this->request->getGet('fecha_fin');
        
        // Obtener todas las sedes para el filtro
        $data['sedes'] = $this->sedeModel->findAll();
        $data['sede_seleccionada'] = $sedeId;
        $data['fecha_inicio'] = $fechaInicio;
        $data['fecha_fin'] = $fechaFin;

        // Condición WHERE base para el filtro de sede y fechas
        $whereSedeCondition = $sedeId ? "AND v.sede_id = $sedeId" : "";
        $whereFechaInicio = $fechaInicio ? "AND DATE(v.fecha) >= '$fechaInicio'" : "";
        $whereFechaFin = $fechaFin ? "AND DATE(v.fecha) <= '$fechaFin'" : "";
        $whereConditions = "WHERE 1=1 $whereSedeCondition $whereFechaInicio $whereFechaFin";
        
        // Obtener conteo de ventas por semana para el año actual y anterior
        $querySemanal = $db->query("
            SELECT 
                YEAR(fecha) as año,
                WEEK(fecha) as semana,
                COUNT(*) as total,
                SUM(monto) as importe_total
            FROM ventas v
            $whereConditions
            GROUP BY YEAR(fecha), WEEK(fecha)
            ORDER BY YEAR(fecha), WEEK(fecha)
        ");
        $data['estadisticasSemanales'] = $querySemanal->getResultArray();

        // Obtener estadísticas por concepto
        $queryConceptos = $db->query("
            SELECT 
                concepto,
                COUNT(*) as frecuencia,
                SUM(monto) as importe_total
            FROM ventas v
            $whereConditions
            GROUP BY concepto
            ORDER BY frecuencia DESC
        ");
        $data['estadisticasConceptos'] = $queryConceptos->getResultArray();

        // Obtener conteo de ventas por año
        $queryAnual = $db->query("
            SELECT 
                YEAR(fecha) as año, 
                COUNT(*) as total,
                SUM(monto) as importe_total
            FROM ventas v
            $whereConditions
            GROUP BY YEAR(fecha)
            ORDER BY año DESC
        ");
        $data['estadisticasAnuales'] = $queryAnual->getResultArray();

        // Obtener estadísticas por usuario
        $queryPorUsuario = $db->query("
            SELECT 
                u.nombre as usuario,
                COUNT(*) as total_ventas,
                SUM(v.monto) as importe_total
            FROM ventas v
            JOIN usuarios u ON u.id = v.usuario_id
            $whereConditions
            GROUP BY v.usuario_id, u.nombre
            ORDER BY total_ventas DESC
        ");
        $data['estadisticasPorUsuario'] = $queryPorUsuario->getResultArray();

        // Obtener estadísticas por sede (solo si no hay filtro de sede)
        if (!$sedeId) {
            $queryPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_ventas,
                    SUM(v.monto) as importe_total
                FROM ventas v
                JOIN sedes s ON s.id = v.sede_id
                " . str_replace('v.sede_id', 'v.sede_id', $whereConditions) . "
                GROUP BY v.sede_id, s.nombre
                ORDER BY total_ventas DESC
            ");
            $data['estadisticasPorSede'] = $queryPorSede->getResultArray();
        } else {
            // Si hay filtro, obtener solo los datos de la sede seleccionada
            $queryPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_ventas,
                    SUM(v.monto) as importe_total
                FROM ventas v
                JOIN sedes s ON s.id = v.sede_id
                WHERE v.sede_id = $sedeId
                " . ($fechaInicio ? "AND DATE(v.fecha) >= '$fechaInicio'" : "") . "
                " . ($fechaFin ? "AND DATE(v.fecha) <= '$fechaFin'" : "") . "
                GROUP BY v.sede_id, s.nombre
            ");
            $data['estadisticasPorSede'] = $queryPorSede->getResultArray();
        }

        // Si hay una sede seleccionada, obtener su nombre
        if ($sedeId) {
            $sede = $this->sedeModel->find($sedeId);
            $data['nombre_sede_seleccionada'] = $sede ? $sede['nombre'] : '';
        }

        return view('ventas/estadisticas', $data);
    }

    public function new()
    {
        $data['sedes'] = $this->sedeModel->findAll();
        
        // Si existe un modelo de usuario, obtener la lista de usuarios
        if ($this->usuarioModel !== null) {
            $data['usuarios'] = $this->usuarioModel->findAll();
        } else {
            // Si no existe, usar un array con solo el ID 1 (asumiendo que existe)
            $data['usuarios'] = [['id' => 1, 'nombre' => 'Administrador']];
        }
        
        return view('ventas/create', $data);
    }

    public function create()
    {
        // Validación de datos
        $rules = [
            'concepto' => 'required|min_length[3]|max_length[100]',
            'descripcion' => 'permit_empty',
            'monto' => 'required|numeric|greater_than[0]',
            'fecha' => 'required|valid_date',
            'sede_id' => 'required|integer|is_not_unique[sedes.id]',
            'usuario_id' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Por favor, verifique los datos ingresados.');
        }

        try {
            $data = [
                'concepto' => $this->request->getPost('concepto'),
                'descripcion' => $this->request->getPost('descripcion'),
                'monto' => $this->request->getPost('monto'),
                'fecha' => date('Y-m-d', strtotime($this->request->getPost('fecha'))),
                'sede_id' => $this->request->getPost('sede_id'),
                'usuario_id' => $this->request->getPost('usuario_id')
            ];

            if ($this->ventaModel->insert($data)) {
                return redirect()->to('/ventas')->with('success', 'Venta registrada correctamente');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar la venta. Por favor, intente nuevamente.');
        } catch (\Exception $e) {
            log_message('error', 'Excepción al crear venta: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $data['venta'] = $this->ventaModel->find($id);
        $data['sedes'] = $this->sedeModel->findAll();
        
        // Si existe un modelo de usuario, obtener la lista de usuarios
        if ($this->usuarioModel !== null) {
            $data['usuarios'] = $this->usuarioModel->findAll();
        } else {
            // Si no existe, usar un array con solo el ID 1 (asumiendo que existe)
            $data['usuarios'] = [['id' => 1, 'nombre' => 'Administrador']];
        }
        
        if (empty($data['venta'])) {
            return redirect()->to('/ventas')->with('error', 'Venta no encontrada');
        }

        return view('ventas/edit', $data);
    }

    public function update($id)
    {
        // Validación de datos
        $rules = [
            'concepto' => 'required|min_length[3]|max_length[100]',
            'descripcion' => 'permit_empty',
            'monto' => 'required|numeric|greater_than[0]',
            'fecha' => 'required|valid_date',
            'sede_id' => 'required|integer|is_not_unique[sedes.id]',
            'usuario_id' => 'required|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Por favor, verifique los datos ingresados.');
        }

        try {
            $data = [
                'concepto' => $this->request->getPost('concepto'),
                'descripcion' => $this->request->getPost('descripcion'),
                'monto' => $this->request->getPost('monto'),
                'fecha' => date('Y-m-d', strtotime($this->request->getPost('fecha'))),
                'sede_id' => $this->request->getPost('sede_id'),
                'usuario_id' => $this->request->getPost('usuario_id')
            ];

            if ($this->ventaModel->update($id, $data)) {
                return redirect()->to('/ventas')->with('success', 'Venta actualizada correctamente');
            }

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar la venta. Por favor, intente nuevamente.');
        } catch (\Exception $e) {
            log_message('error', 'Excepción al actualizar venta: ' . $e->getMessage());

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        if ($this->ventaModel->delete($id)) {
            return redirect()->to('/ventas')->with('success', 'Venta eliminada correctamente');
        }
        
        return redirect()->to('/ventas')->with('error', 'Error al eliminar la venta');
    }
    
    /**
     * Genera un PDF con las estadísticas de ventas
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function generarPDF()
    {
        // Obtener los mismos datos que para la vista de estadísticas
        $db = \Config\Database::connect();
        $sedeId = $this->request->getGet('sede_id');
        
        // Obtener todas las sedes para el filtro
        $data['sedes'] = $this->sedeModel->findAll();
        $data['sede_seleccionada'] = $sedeId;

        // Condición WHERE base para el filtro de sede
        $whereSedeCondition = $sedeId ? "AND v.sede_id = $sedeId" : "";
        
        // Obtener estadísticas por concepto
        $queryConceptos = $db->query("
            SELECT 
                concepto,
                COUNT(*) as frecuencia,
                SUM(monto) as importe_total
            FROM ventas v
            WHERE 1=1 $whereSedeCondition
            GROUP BY concepto
            ORDER BY frecuencia DESC
        ");
        $data['estadisticasConceptos'] = $queryConceptos->getResultArray();

        // Obtener conteo de ventas por año
        $queryAnual = $db->query("
            SELECT 
                YEAR(fecha) as año, 
                COUNT(*) as total,
                SUM(monto) as importe_total
            FROM ventas v
            WHERE 1=1 $whereSedeCondition
            GROUP BY YEAR(fecha)
            ORDER BY año DESC
        ");
        $data['estadisticasAnuales'] = $queryAnual->getResultArray();

        // Obtener estadísticas por usuario
        $queryPorUsuario = $db->query("
            SELECT 
                u.nombre as usuario,
                COUNT(*) as total_ventas,
                SUM(v.monto) as importe_total
            FROM ventas v
            JOIN usuarios u ON u.id = v.usuario_id
            WHERE 1=1 $whereSedeCondition
            GROUP BY v.usuario_id, u.nombre
            ORDER BY total_ventas DESC
        ");
        $data['estadisticasPorUsuario'] = $queryPorUsuario->getResultArray();

        // Obtener estadísticas por sede (solo si no hay filtro)
        if (!$sedeId) {
            $queryPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_ventas,
                    SUM(v.monto) as importe_total
                FROM ventas v
                JOIN sedes s ON s.id = v.sede_id
                GROUP BY v.sede_id, s.nombre
                ORDER BY total_ventas DESC
            ");
            $data['estadisticasPorSede'] = $queryPorSede->getResultArray();
        } else {
            // Si hay filtro, obtener solo los datos de la sede seleccionada
            $queryPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_ventas,
                    SUM(v.monto) as importe_total
                FROM ventas v
                JOIN sedes s ON s.id = v.sede_id
                WHERE v.sede_id = $sedeId
                GROUP BY v.sede_id, s.nombre
            ");
            $data['estadisticasPorSede'] = $queryPorSede->getResultArray();
        }
        
        // Calcular estadísticas generales
        $totalVentas = 0;
        $importeTotal = 0;
        
        if (!empty($data['estadisticasAnuales'])) {
            foreach ($data['estadisticasAnuales'] as $estadistica) {
                $totalVentas += $estadistica['total'];
                $importeTotal += $estadistica['importe_total'];
            }
        }
        
        $data['totalVentas'] = $totalVentas;
        $data['importeTotal'] = $importeTotal;
        
        // Si hay una sede seleccionada, obtener su nombre
        if ($sedeId) {
            $sede = $this->sedeModel->find($sedeId);
            $data['nombre_sede_seleccionada'] = $sede ? $sede['nombre'] : '';
        }
        
        // Cargar la vista especial para PDF
        $html = view('ventas/pdf_estadisticas', $data);

        // Configurar opciones de DOMPDF
        $options = new \Dompdf\Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('isPhpEnabled', true);
        $options->set('isFontSubsettingEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('debugKeepTemp', true);
        $options->set('debugCss', true);
        
        // Crear instancia de DOMPDF
        $dompdf = new \Dompdf\Dompdf($options);
        
        // Cargar el HTML
        $dompdf->loadHtml($html);
        
        // Configurar papel y orientación
        $dompdf->setPaper('A4', 'portrait');
        
        // Renderizar el PDF
        $dompdf->render();
        
        // Nombre del archivo
        $filename = 'Estadisticas_Ventas_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Enviar el archivo al navegador para descarga en lugar de visualización
        return $this->response
            ->setHeader('Content-Type', 'application/pdf')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($dompdf->output());
    }
    
    /**
     * Genera un PDF con las estadísticas de ventas incluyendo gráficas
     * 
     * @return \CodeIgniter\HTTP\Response
     */
    public function generarPDFConGraficas()
    {
        // Obtener los mismos datos que para la vista de estadísticas
        $db = \Config\Database::connect();
        $sedeId = $this->request->getGet('sede_id');
        
        // Obtener todas las sedes para el filtro
        $data['sedes'] = $this->sedeModel->findAll();
        $data['sede_seleccionada'] = $sedeId;

        // Condición WHERE base para el filtro de sede
        $whereSedeCondition = $sedeId ? "AND v.sede_id = $sedeId" : "";
        
        // Obtener estadísticas por concepto
        $queryConceptos = $db->query("
            SELECT 
                concepto,
                COUNT(*) as frecuencia,
                SUM(monto) as importe_total
            FROM ventas v
            WHERE 1=1 $whereSedeCondition
            GROUP BY concepto
            ORDER BY frecuencia DESC
        ");
        $data['estadisticasConceptos'] = $queryConceptos->getResultArray();

        // Obtener conteo de ventas por año
        $queryAnual = $db->query("
            SELECT 
                YEAR(fecha) as año, 
                COUNT(*) as total,
                SUM(monto) as importe_total
            FROM ventas v
            WHERE 1=1 $whereSedeCondition
            GROUP BY YEAR(fecha)
            ORDER BY año DESC
        ");
        $data['estadisticasAnuales'] = $queryAnual->getResultArray();

        // Obtener estadísticas por usuario
        $queryPorUsuario = $db->query("
            SELECT 
                u.nombre as usuario,
                COUNT(*) as total_ventas,
                SUM(v.monto) as importe_total
            FROM ventas v
            JOIN usuarios u ON u.id = v.usuario_id
            WHERE 1=1 $whereSedeCondition
            GROUP BY v.usuario_id, u.nombre
            ORDER BY total_ventas DESC
        ");
        $data['estadisticasPorUsuario'] = $queryPorUsuario->getResultArray();

        // Obtener estadísticas por sede (solo si no hay filtro)
        if (!$sedeId) {
            $queryPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_ventas,
                    SUM(v.monto) as importe_total
                FROM ventas v
                JOIN sedes s ON s.id = v.sede_id
                GROUP BY v.sede_id, s.nombre
                ORDER BY total_ventas DESC
            ");
            $data['estadisticasPorSede'] = $queryPorSede->getResultArray();
        } else {
            // Si hay filtro, obtener solo los datos de la sede seleccionada
            $queryPorSede = $db->query("
                SELECT 
                    s.nombre as sede,
                    COUNT(*) as total_ventas,
                    SUM(v.monto) as importe_total
                FROM ventas v
                JOIN sedes s ON s.id = v.sede_id
                WHERE v.sede_id = $sedeId
                GROUP BY v.sede_id, s.nombre
            ");
            $data['estadisticasPorSede'] = $queryPorSede->getResultArray();
        }
        
        // Calcular estadísticas generales
        $totalVentas = 0;
        $importeTotal = 0;
        
        if (!empty($data['estadisticasAnuales'])) {
            foreach ($data['estadisticasAnuales'] as $estadistica) {
                $totalVentas += $estadistica['total'];
                $importeTotal += $estadistica['importe_total'];
            }
        }
        
        $data['totalVentas'] = $totalVentas;
        $data['importeTotal'] = $importeTotal;
        
        // Si hay una sede seleccionada, obtener su nombre
        if ($sedeId) {
            $sede = $this->sedeModel->find($sedeId);
            $data['nombre_sede_seleccionada'] = $sede ? $sede['nombre'] : '';
        }
        
        // Cargar la vista especial para PDF con gráficas
        return view('ventas/pdf_con_graficas', $data);
    }
} 