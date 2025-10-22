<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Login
$routes->get('/login', 'Login::index');
$routes->post('/login/doLogin', 'Login::doLogin');
$routes->get('/logout', 'Login::logout');

// Home protegido
$routes->get('/', 'Home::index', ['filter' => 'auth']);
$routes->get('/home', 'Home::index', ['filter' => 'auth']);

// ======================================================
//  RESTful
// ======================================================
$routes->resource('bienes', ['placeholder' => '(:num)', 'filter' => 'auth']);
$routes->resource('personas', ['placeholder' => '(:num)', 'filter' => 'auth']);
$routes->resource('inventario2025', ['placeholder' => '(:num)', 'filter' => 'auth']);
$routes->resource('baja', ['placeholder' => '(:num)', 'filter' => 'auth']);
$routes->resource('reportes', ['placeholder' => '(:num)', 'filter' => 'auth']);
$routes->resource('ip', ['controller' => 'IpController', 'placeholder' => '(:num)', 'filter' => 'auth']);

// ======================================================
//  Movimientos (Asignación) — REST principal
// ======================================================
$routes->resource('movimientos', [
    'controller' => 'Asignacion',
    'placeholder' => '(:num)',
    'filter' => 'auth'
]);

// Rutas adicionales Movimientos
$routes->get('movimientos/buscarBienes', 'Asignacion::buscarBienes', ['filter' => 'auth']);
$routes->get('movimientos/descargarCargo/(:num)', 'Asignacion::descargarCargo/$1', ['filter' => 'auth']);
$routes->get('movimientos/descargarCargoLote/(:segment)', 'Asignacion::descargarCargoLote/$1', ['filter' => 'auth']);
$routes->get('movimientos/descargarActa/(:num)', 'Asignacion::descargarActa/$1', ['filter' => 'auth']);
$routes->get('movimientos/descargarActa/(:num)/(:segment)', 'Asignacion::descargarActa/$1/$2', ['filter' => 'auth']);

// ======================================================
// Bienes extras
// ======================================================
$routes->post('bienes/subida_masiva', 'Bienes::subida_masiva', ['filter' => 'auth']);
$routes->get('bienes/reporte_bienes', 'Bienes::reporte_bienes', ['filter' => 'auth']);
$routes->post('bienes/verificarCodigo', 'Bienes::verificarCodigo', ['filter' => 'auth']);
$routes->post('bienes/desactivar', 'Bienes::desactivar', ['filter' => 'auth']);
$routes->get('bienes/getUsuariosSugeridos', 'Bienes::getUsuariosSugeridos', ['filter' => 'auth']);

// ======================================================
// Inventario extras
// ======================================================
$routes->post('inventario2025/create', 'Inventario2025::create', ['filter' => 'auth']);
$routes->post('inventario2025/getBienDescripcion', 'Inventario2025::getBienDescripcion', ['filter' => 'auth']);
$routes->get('inventario2025/getUsuariosSugeridos', 'Inventario2025::getUsuariosSugeridos', ['filter' => 'auth']);
$routes->get('inventario2025/reporte_asignacion', 'Inventario2025::reporte_asignacion', ['filter' => 'auth']);
$routes->put('inventario2025/(:num)', 'Inventario2025::update/$1', ['filter' => 'auth']);
$routes->get('inventario2025/editar/(:num)', 'Inventario2025::edit/$1', ['filter' => 'auth']);
$routes->delete('inventario2025/delete/(:num)', 'Inventario2025::delete/$1', ['filter' => 'auth']);

// ======================================================
// Baja extras
// ======================================================
$routes->post('baja/recuperar/(:num)', 'Baja::recuperar/$1', ['filter' => 'auth']);
$routes->get('baja/reportePDF', 'Baja::reportePDF', ['filter' => 'auth']);
$routes->get('baja/exportarExcel', 'Baja::exportarExcel', ['filter' => 'auth']);

// ======================================================
// IPs extras
// ======================================================
$routes->get('ip/exportarExcel', 'Ip::exportarExcel', ['filter' => 'auth']);
$routes->get('ip/exportarPDF', 'Ip::exportarPDF', ['filter' => 'auth']);
$routes->get('ip/asignar', 'IpController::asignar', ['filter' => 'auth']);
$routes->post('ip/asignar', 'IpController::asignar', ['filter' => 'auth']);
$routes->get('ip/liberar/(:num)', 'IpController::liberar/$1', ['filter' => 'auth']);
$routes->get('ip/eliminar192', 'IpController::eliminar192', ['filter' => 'auth']);
$routes->get('ip/buscarIpsDisponibles', 'IpController::buscarIpsDisponibles', ['filter' => 'auth']);
$routes->get('ip/buscarBienes', 'IpController::buscarBienes', ['filter' => 'auth']);

// ======================================================
// Usuarios
// ======================================================
$routes->group('usuarios', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Usuarios::index');
    $routes->get('get/(:num)', 'Usuarios::get/$1');
    $routes->post('store', 'Usuarios::store');
    $routes->post('update/(:num)', 'Usuarios::update/$1');
    $routes->post('toggle/(:num)', 'Usuarios::toggle/$1');
    $routes->get('roles/(:num)', 'Usuarios::roles/$1');
    $routes->post('roles/save', 'Usuarios::saveRoles');
    $routes->get('permisos/(:num)', 'Usuarios::permisos/$1');
    $routes->post('permisos/save', 'Usuarios::savePermisos');
});

// ======================================================
// Perfil (editar datos + foto)
// ======================================================
$routes->get('perfil', 'PerfilController::index', ['filter' => 'auth']);
$routes->post('perfil/guardar', 'PerfilController::actualizarPerfil', ['filter' => 'auth']);
$routes->post('perfil/foto', 'PerfilController::actualizarFoto', ['filter' => 'auth']);
