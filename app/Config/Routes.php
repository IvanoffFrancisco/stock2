<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Auth::login');
$routes->get('login', 'Auth::login');
$routes->get('register', 'Auth::register');

$routes->post('auth/autenticar', 'Auth::autenticar');
$routes->post('auth/guardarRegistro', 'Auth::guardarRegistro');

$routes->get('logout', 'Auth::logout', ['filter' => 'auth']);
$routes->get('dashboard', 'Auth::dashboard', ['filter' => 'auth']);

$routes->group('usuarios', ['filter' => 'admin'], function($routes) {
    $routes->get('/', 'Usuarios::index');
    $routes->get('create', 'Usuarios::create');
    $routes->post('store', 'Usuarios::store');
});

$routes->group('categorias', ['filter' => 'admin'], function($routes) {
    $routes->get('/', 'Categorias::index');
    $routes->get('create', 'Categorias::create');
    $routes->post('store', 'Categorias::store');
    $routes->get('edit/(:num)', 'Categorias::edit/$1');
    $routes->post('update/(:num)', 'Categorias::update/$1');
    $routes->post('delete/(:num)', 'Categorias::delete/$1');
});

$routes->group('productos', ['filter' => 'adminvendedor'], function($routes) {
    $routes->get('/', 'Productos::index');
    $routes->get('create', 'Productos::create');
    $routes->post('store', 'Productos::store');
    $routes->get('edit/(:num)', 'Productos::edit/$1');
    $routes->post('update/(:num)', 'Productos::update/$1');
});

$routes->group('precio-productos', ['filter' => 'adminvendedor'], function($routes) {
    $routes->get('/', 'PrecioProductos::index');
    $routes->get('create', 'PrecioProductos::create');
    $routes->post('store', 'PrecioProductos::store');
    $routes->get('edit/(:num)', 'PrecioProductos::edit/$1');
    $routes->post('update/(:num)', 'PrecioProductos::update/$1');
});

$routes->group('movimientos-stock', ['filter' => 'admin'], function($routes) {
    $routes->get('/', 'MovimientosStock::index');
    $routes->get('create', 'MovimientosStock::create');
    $routes->post('store', 'MovimientosStock::store');
});

$routes->group('clientes', ['filter' => 'adminvendedor'], function($routes) {
    $routes->get('/', 'Clientes::index');
    $routes->get('create', 'Clientes::create');
    $routes->post('store', 'Clientes::store');
    $routes->get('edit/(:num)', 'Clientes::edit/$1');
    $routes->post('update/(:num)', 'Clientes::update/$1');
});

$routes->group('pedidos', ['filter' => 'adminvendedor'], function($routes) {
    $routes->get('/', 'Pedidos::index');
    $routes->get('create', 'Pedidos::create');
    $routes->post('store', 'Pedidos::store');
    $routes->get('precio-sugerido', 'Pedidos::precioSugerido');
    $routes->get('show/(:num)', 'Pedidos::show/$1');
    $routes->get('edit/(:num)', 'Pedidos::edit/$1');
    $routes->post('update/(:num)', 'Pedidos::update/$1');
    $routes->post('cambiar-estado/(:num)', 'Pedidos::cambiarEstado/$1');
    $routes->get('pdf/(:num)', 'Pedidos::pdf/$1');
});

$routes->group('ventas', ['filter' => 'adminvendedor'], function($routes) {
    $routes->get('/', 'Ventas::index');
    $routes->get('show/(:num)', 'Ventas::show/$1');
    $routes->get('pdf/(:num)', 'Ventas::pdf/$1');
});

$routes->group('metricas', ['filter' => 'admin'], function($routes) {
    $routes->get('/', 'Metricas::index');
});