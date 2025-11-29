<?php
// Bootstrap CodeIgniter
require 'vendor/autoload.php';
$app = \Config\Services::codeigniter();
$app->initialize();

// Obtener una instancia del controlador Reports
$reportsController = new \App\Controllers\Reports();

// Verificar si existe el método reporte_visita
if (method_exists($reportsController, 'reporte_visita')) {
    echo "El método reporte_visita existe en el controlador Reports.";
} else {
    echo "El método reporte_visita NO existe en el controlador Reports.";
}

// Verificar si existe la vista reporte_visita.php
$viewPath = APPPATH . 'Views/reportes/reporte_visita.php';
if (file_exists($viewPath)) {
    echo "\nLa vista reporte_visita.php existe en la ruta Views/reportes/.";
} else {
    echo "\nLa vista reporte_visita.php NO existe en la ruta Views/reportes/.";
} 