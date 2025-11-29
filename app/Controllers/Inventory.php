<?php

namespace App\Controllers;

class Inventory extends BaseController
{
    public function traps(): string
    {
        // Verificar si el usuario ha iniciado sesión
        $this->verificarSesion();
        
        return view('inventory/traps');
    }

    public function supplies(): string
    {
        // Verificar si el usuario ha iniciado sesión
        $this->verificarSesion();
        
        return view('inventory/supplies');
    }
} 