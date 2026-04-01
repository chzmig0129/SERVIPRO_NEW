<?php

namespace App\Controllers;
use App\Models\SedeModel;
use App\Models\TrampaModel;
use App\Models\PlanoModel;

class inicio extends BaseController
{
    public function index(): string
    {
        // Verificar si el usuario ha iniciado sesión
        $this->verificarSesion();
        
        // Cargar los modelos necesarios
        $sedeModel = new SedeModel();
        $trampaModel = new TrampaModel();
        $planoModel = new PlanoModel();

        // Obtener solo las sedes activas (estatus = 1)
        $sedes = $sedeModel->where('estatus', 1)->findAll();

        // Calcular estadísticas para cada sede
        foreach ($sedes as &$sede) {
            $sede['total_planos'] = $planoModel->where('sede_id', $sede['id'])->where('habilitado', 1)->countAllResults();
            $sede['total_trampas'] = $trampaModel->where('sede_id', $sede['id'])->countAllResults();
        }

        // Pasar los datos a la vista
        $data = [
            'sedes' => $sedes
        ];

        return view('dashboard/index', $data);
    }
}
