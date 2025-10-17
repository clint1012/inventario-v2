<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// ==========================
// 📌 RECURSOS RESTful
// ==========================
$routes->resource('bienes', ['placeholder' => '(:num)']);
$routes->resource('personas', ['placeholder' => '(:num)']);
$routes->resource('inventario2025', ['placeholder' => '(:num)']);
$routes->resource('baja', ['placeholder' => '(:num)']);
$routes->resource('reportes', ['placeholder' => '(:num)']);
$routes->resource('ip', [
    'controller' => 'IpController',
    'placeholder' => '(:num)'
]);

// 🚀 Movimientos usando el controlador Asignacion
$routes->resource('movimientos', [
    'controller'  => 'Asignacion',
    'placeholder' => '(:num)'
]);

// ==========================
// 📌 Rutas adicionales de movimientos
// ==========================
$routes->get('movimientos/descargarCargo/(:num)', 'Asignacion::descargarCargo/$1');
$routes->get('movimientos/descargarCargoLote/(:segment)', 'Asignacion::descargarCargoLote/$1');
$routes->get('movimientos/buscarBienes', 'Asignacion::buscarBienes');

// ✅ Nueva ruta para generar acta por usuario
$routes->get('movimientos/descargarActa/(:num)', 'Asignacion::descargarActa/$1');
$routes->get('movimientos/descargarActa/(:num)/(:segment)', 'Asignacion::descargarActa/$1/$2');


// ==========================
// 📌 BIENES extras
// ==========================
$routes->post('bienes/subida_masiva', 'Bienes::subida_masiva');
$routes->get('bienes/reporte_bienes', 'Bienes::reporte_bienes');
$routes->post('bienes/verificarCodigo', 'Bienes::verificarCodigo');
$routes->post('bienes/desactivar', 'Bienes::desactivar');
$routes->get('bienes/getUsuariosSugeridos', 'Bienes::getUsuariosSugeridos');

// ==========================
// 📌 INVENTARIO extras
// ==========================
$routes->post('inventario2025/create', 'Inventario2025::create');
$routes->post('inventario2025/getBienDescripcion', 'Inventario2025::getBienDescripcion');
$routes->get('inventario2025/getUsuariosSugeridos', 'Inventario2025::getUsuariosSugeridos');
$routes->get('inventario2025/reporte_asignacion', 'Inventario2025::reporte_asignacion');
$routes->put('inventario2025/(:num)', 'Inventario2025::update/$1');
$routes->get('inventario2025/editar/(:num)', 'Inventario2025::edit/$1');
$routes->delete('inventario2025/delete/(:num)', 'Inventario2025::delete/$1');

// ==========================
// 📌 BAJA extras
// ==========================
$routes->post('baja/recuperar/(:num)', 'Baja::recuperar/$1');
$routes->get('baja/reportePDF', 'Baja::reportePDF');
$routes->get('baja/exportarExcel', 'Baja::exportarExcel');

// ==========================
// 📌 IPs extras
// ==========================
$routes->get('ip/exportarExcel', 'Ip::exportarExcel');
$routes->get('ip/exportarPDF', 'Ip::exportarPDF');
$routes->get('ip/asignar', 'IpController::asignar');
$routes->post('ip/asignar', 'IpController::asignar');
$routes->get('ip/liberar/(:num)', 'IpController::liberar/$1');
$routes->get('ip/eliminar192', 'IpController::eliminar192');
$routes->get('ip/buscarIpsDisponibles', 'IpController::buscarIpsDisponibles');
$routes->get('ip/buscarBienes', 'IpController::buscarBienes');

// ==========================
// 📌 Alias para Asignacion (grupo limpio)
// ==========================
$routes->group('asignacion', function($routes) {
    $routes->get('/', 'Asignacion::index');
    $routes->get('new', 'Asignacion::new');
    $routes->post('create', 'Asignacion::create');
    $routes->get('descargarCargo/(:num)', 'Asignacion::descargarCargo/$1');
    $routes->get('descargarCargoLote/(:segment)', 'Asignacion::descargarCargoLote/$1');
    $routes->get('buscarBienes', 'Asignacion::buscarBienes');
});
