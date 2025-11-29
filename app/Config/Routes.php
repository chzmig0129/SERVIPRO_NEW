<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('/diagnostico', 'Home::db_diagnostico');
$routes->get('/crear-sedes-prueba', 'SedesController::crear_sedes_prueba');
$routes->get('/mostrar-sedes', 'SedesController::mostrar_sedes');
$routes->get('/crear-usuario-prueba', 'Home::crear_usuario_prueba');

// Ruta para la autenticación de usuarios
$routes->post('/authenticate', 'Home::authenticate');
$routes->get('/logout', 'Home::logout');

// Rutas del módulo de quejas
$routes->get('quejas', 'QuejasController::index');
$routes->get('quejas/estadisticas', 'QuejasController::estadisticas');
$routes->get('quejas/estadisticas/pdf', 'QuejasController::generarPDF');
$routes->get('quejas/estadisticas/pdf_con_graficas', 'QuejasController::generarPDFConGraficas');
$routes->get('quejas/new', 'QuejasController::new');
$routes->post('quejas/create', 'QuejasController::create');
$routes->get('quejas/edit/(:num)', 'QuejasController::edit/$1');
$routes->post('quejas/update/(:num)', 'QuejasController::update/$1');
$routes->get('quejas/delete/(:num)', 'QuejasController::delete/$1');

// Ruta para actualizar estado de queja
$routes->post('quejas/actualizar-estado', 'QuejasController::actualizarEstado');

$routes->get('locations', 'Locations::index');
$routes->post('locations/getDatosComparacionMeses', 'Locations::getDatosComparacionMeses');
$routes->get('Inicio', 'Inicio::index');
$routes->get('blueprints', 'Blueprints::index');
$routes->get('incidents', 'Incidents::index');
$routes->get('incidents/getPlanosBySede/(:num)', 'Incidents::getPlanosBySede/$1');
$routes->get('inventory/traps', 'Inventory::traps');
$routes->get('inventory/supplies', 'Inventory::supplies');
$routes->get('staff', 'Staff::index');
$routes->get('analytics', 'Analytics::index');
$routes->get('blueprints/view/(:num)', 'Blueprints::view/$1');
$routes->get('blueprints/viewplano/(:num)', 'Blueprints::viewplano/$1');
$routes->get('blueprints/verImagen/(:num)', 'Blueprints::verImagen/$1');

// Rutas para reportes PDF
$routes->get('reports/pdf_trampas/(:num)', 'Reports::pdf_trampas/$1');
$routes->get('reports/pdf_incidencias/(:num)', 'Reports::pdf_incidencias/$1');
$routes->get('reports/pdf_completo/(:num)', 'Reports::pdf_completo/$1');
$routes->get('reports/reporte_visita', 'Reports::reporte_visita');

// Rutas para reportes PDF optimizados
$routes->get('reportes/pdf_optimizado/ventas_quejas', 'ReporteController::generarPDFOptimizado/ventas_quejas');
$routes->get('reportes/pdf_optimizado/ventas', 'ReporteController::generarPDFOptimizado/ventas');
$routes->get('reportes/pdf_optimizado/quejas', 'ReporteController::generarPDFOptimizado/quejas');

// Rutas alternativas para reportes en español
$routes->get('reportes/pdf_trampas/(:num)', 'Reports::pdf_trampas/$1');
$routes->get('reportes/pdf_incidencias/(:num)', 'Reports::pdf_incidencias/$1');
$routes->get('reportes/pdf_completo/(:num)', 'Reports::pdf_completo/$1');
$routes->get('reportes/reporte_visita', 'Reports::reporte_visita');

// Rutas para reportes antiguos (compatibilidad)
$routes->get('/reportes/plano/(:num)', 'ReporteController::generarReportePlano/$1');
$routes->get('/reportes/sede/(:num)', 'ReporteController::generarReporteSede/$1');
$routes->get('/reportes/captura/(:num)', 'ReporteController::capturarVista/$1');

// Rutas para el controlador de evidencias
$routes->post('evidencia/guardarEvidencia', 'EvidenciaController::guardarEvidencia');
$routes->get('evidencia/getEvidenciasPorPlano/(:num)', 'EvidenciaController::getEvidenciasPorPlano/$1');
$routes->get('evidencia/getEvidencia/(:num)', 'EvidenciaController::getEvidencia/$1');
$routes->post('evidencia/actualizarEvidencia', 'EvidenciaController::actualizarEvidencia');
$routes->post('evidencia/eliminarEvidencia/(:num)', 'EvidenciaController::eliminarEvidencia/$1');
$routes->get('evidencia/eliminarEvidencia/(:num)', 'EvidenciaController::eliminarEvidencia/$1');
$routes->post('evidencia/subirImagenResuelta', 'EvidenciaController::subirImagenResuelta');
$routes->post('evidencia/cambiarEstado', 'EvidenciaController::cambiarEstado');
$routes->post('evidencia/vistoBuenoSupervisor', 'EvidenciaController::vistoBuenoSupervisor');

$routes->post('sedes/guardar', 'SedesController::guardar');
$routes->get('sedes/listar', 'SedesController::listar');
$routes->get('sedes/ver/(:num)', 'SedesController::ver/$1');
$routes->get('sedes', 'SedesController::index');

$routes->post('blueprints/guardar_plano', 'Blueprints::guardar_plano');
$routes->post('blueprints/guardar_estado', 'Blueprints::guardar_estado');
$routes->post('blueprints/guardar_trampa', 'Blueprints::guardar_trampa');
$routes->post('blueprints/guardar_incidencia', 'Blueprints::guardar_incidencia');
$routes->post('blueprints/actualizar_id_trampa', 'Blueprints::actualizar_id_trampa');

// Rutas para el historial de movimientos
$routes->get('historial/index/(:num)', 'HistorialController::index/$1');

// Rutas del módulo de ventas
$routes->get('ventas', 'VentasController::index');
$routes->get('ventas/estadisticas', 'VentasController::estadisticas');
$routes->get('ventas/estadisticas/pdf', 'VentasController::generarPDF');
$routes->get('ventas/estadisticas/pdf_con_graficas', 'VentasController::generarPDFConGraficas');
$routes->get('ventas/new', 'VentasController::new');
$routes->post('ventas/create', 'VentasController::create');
$routes->get('ventas/edit/(:num)', 'VentasController::edit/$1');
$routes->post('ventas/update/(:num)', 'VentasController::update/$1');
$routes->put('ventas/update/(:num)', 'VentasController::update/$1');
$routes->get('ventas/delete/(:num)', 'VentasController::delete/$1');

// Rutas para registro técnico
$routes->get('registro_tecnico', 'RegistroTecnico::index');
$routes->get('registro_tecnico/planos/(:num)', 'RegistroTecnico::getPlanosBySede/$1');
$routes->get('registro_tecnico/trampas/(:num)', 'RegistroTecnico::getTrampasPlano/$1');
$routes->get('registro_tecnico/ver_trampas/(:num)', 'RegistroTecnico::verTrampasPorPlano/$1');
$routes->get('registro_tecnico/historial_trampa/(:num)', 'RegistroTecnico::verHistorialTrampa/$1');
$routes->post('registro_tecnico/guardar_incidencia', 'RegistroTecnico::guardarIncidencia');
$routes->post('registro_tecnico/actualizarEstadoQueja', 'RegistroTecnico::actualizarEstadoQueja');
$routes->post('registro_tecnico/actualizar_id_trampa', 'RegistroTecnico::actualizar_id_trampa');

$routes->match(['get', 'post'], 'locations/generarPDF', 'LocationsPdfController::generarPDF');