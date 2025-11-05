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
$routes->resource('mantenimiento', ['placeholder' => '(:num)', 'filter' => 'auth']);
$routes->resource('reportes', ['placeholder' => '(:num)', 'filter' => 'auth']);
$routes->resource('ip', ['controller' => 'IpController', 'placeholder' => '(:num)', 'filter' => 'auth']);
$routes->resource('proveedor', ['controller' => 'ProveedorController','placeholder' => '(:num)','filter' => 'auth']);


// ======================================================
//  Proveedor — REST principal
// ======================================================
$routes->get('proveedor/pdf/(:num)', 'ProveedorController::pdf/$1', ['filter' => 'auth']);


// ======================================================
//  Mantenimiento — REST principal
// ======================================================
$routes->post('mantenimiento/recuperar/(:num)', 'Mantenimiento::recuperar/$1', ['filter' => 'auth']);



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
$routes->post('movimientos/anular/(:num)', 'Asignacion::anular/$1', ['filter'=> 'auth']);


// ======================================================
// Bienes extras
// ======================================================
$routes->post('bienes/subida_masiva', 'Bienes::subida_masiva', ['filter' => 'auth']);
$routes->get('bienes/reporte_bienes', 'Bienes::reporte_bienes', ['filter' => 'auth']);
$routes->post('bienes/verificarCodigo', 'Bienes::verificarCodigo', ['filter' => 'auth']);
$routes->post('bienes/desactivar', 'Bienes::desactivar', ['filter' => 'auth']);
$routes->post('bienes/getMantenimiento', 'Bienes::getMantenimiento', ['filter' => 'auth']);
$routes->get('bienes/getUsuariosSugeridos', 'Bienes::getUsuariosSugeridos', ['filter' => 'auth']);
$routes->get('bienes/locales', 'Bienes::getLocales', ['filter' => 'auth']);
$routes->get('bienes/departamentos', 'Bienes::getDepartamentos', ['filter' => 'auth']);
$routes->get('bienes/marcas', 'Bienes::getMarcas', ['filter'=>'auth'] );
$routes->get('bienes/modelos', 'Bienes::getModelos', ['filter'=>'auth'] );
$routes->get('bienes/buscarDescripcion', 'Bienes::buscarDescripcion', ['filter'=>'auth'] );


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

// 1. RUTA PRINCIPAL (DataTables) - Ya cubierta por resource, pero la ponemos para claridad:
// $routes->get('ip', 'IpController::index', ['filter' => 'auth']); 

// 2. ENDPOINT AJAX para DataTables (Carga los datos)
$routes->get('ip/datatables', 'IpController::datatables', ['filter' => 'auth']);

// 3. EDICIÓN (Formulario GET y Procesamiento POST) - Estos son los métodos clave
$routes->get('ip/editar/(:num)', 'IpController::editar/$1', ['filter' => 'auth']);
$routes->post('ip/actualizar/(:num)', 'IpController::actualizar/$1', ['filter' => 'auth']); // El POST para actualizar

// 4. MANTENEMOS LAS OTRAS RUTAS ÚTILES (Exportaciones, Búsquedas AJAX, etc.)
$routes->get('ip/exportarExcel', 'IpController::exportarExcel', ['filter' => 'auth']); // NOTA: Asegúrate que el controlador sea IpController
$routes->get('ip/exportarPDF', 'IpController::exportarPDF', ['filter' => 'auth']);     // NOTA: Asegúrate que el controlador sea IpController
$routes->get('ip/eliminar192', 'IpController::eliminar192', ['filter' => 'auth']);
$routes->get('ip/buscarAsignacionBien', 'IpController::buscarAsignacionBien', ['filter' => 'auth']);



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
