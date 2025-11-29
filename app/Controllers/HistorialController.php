<?php

namespace App\Controllers;

use App\Models\MovimientoTrampaModel;
use CodeIgniter\Controller;

class HistorialController extends Controller
{
    protected $movimientoModel;

    public function __construct()
    {
        $this->movimientoModel = new MovimientoTrampaModel();
    }

    public function index($planoId = null)
    {
        if (!$planoId) {
            return redirect()->to('/blueprints');
        }

        // Obtener los filtros de la URL
        $tipoTrampa = $this->request->getGet('tipo_trampa');
        $fechaInicio = $this->request->getGet('fecha_inicio');
        $fechaFin = $this->request->getGet('fecha_fin');

        // Construir la consulta base
        $builder = $this->movimientoModel->where('plano_id', $planoId);

        // Aplicar filtros si existen
        if ($tipoTrampa) {
            $builder->where('tipo', $tipoTrampa);
        }
        if ($fechaInicio) {
            $builder->where('DATE(fecha_movimiento) >=', $fechaInicio);
        }
        if ($fechaFin) {
            $builder->where('DATE(fecha_movimiento) <=', $fechaFin);
        }

        // Obtener los movimientos filtrados
        $data['movimientos'] = $builder->orderBy('fecha_movimiento', 'DESC')->findAll();

        // Obtener tipos únicos de trampas para el filtro
        $data['tipos_trampa'] = $this->movimientoModel
            ->distinct()
            ->select('tipo')
            ->where('plano_id', $planoId)
            ->get()
            ->getResultArray();

        $data['plano_id'] = $planoId;
        $data['filtros'] = [
            'tipo_trampa' => $tipoTrampa,
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ];
        
        return view('historial/index', $data);
    }
} 