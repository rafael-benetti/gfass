<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');



##Rotas para a API
$routes->post('api/login', 'Api\Login::getToken');

//Deve-se informar o controller pois ele está dentro de uma subpasta.
$routes->resource('api/usuario', ['controller' => 'Api\Usuario']);
$routes->resource('api/categoria', ['controller' => 'Api\Categoria']);
$routes->resource('api/orcamento', ['controller' => 'Api\Orcamento']);
$routes->resource('api/grafico', ['controller' => 'Api\Grafico']);
// $routes->resource('api/lancamento', ['controller' => 'Api\Lancamento']);

$routes->put('api/categoria', 'Api\Categoria::update');
$routes->put('api/orcamento', 'Api\Orcamento::update');
$routes->put('api/usuario', 'Api\Usuario::update');
$routes->put('api/lancamento', 'Api\Lancamento::update');

$routes->resource('api/lancamento', ['placeholder' => '(:hash)', 'except' => 'index', 'controller' => 'Api\Lancamento']);
$routes->get('api/lancamento/mes/(:num)/ano/(:num)', 'Api\Lancamento::getByData/$1/$2');

##CLI
$routes->cli('cron', 'Cron::index');


##Rotas Padrão
$routes->get('lancamento/(:hash)/edit', 'Lancamento::edit/$1');
$routes->get('lancamento/(:hash)/delete', 'Lancamento::delete/$1');

$routes->get('upload', 'Upload::lancamento');
$routes->post('upload/upload', 'Upload::upload');

$routes->get('categoria/(:hash)/edit', 'Categoria::edit/$1');
$routes->get('categoria/(:hash)/delete', 'Categoria::delete/$1');

$routes->get('orcamento/(:hash)/edit', 'Orcamento::edit/$1');
$routes->get('orcamento/(:hash)/delete', 'Orcamento::delete/$1');


$routes->get('perfil/(:hash)/edit', 'Perfil::edit/$1');
$routes->get('perfil/(:hash)/delete', 'Perfil::delete/$1');

$routes->get('usuario/(:hash)/edit', 'Usuario::edit/$1');
$routes->get('usuario/(:hash)/delete', 'Usuario::delete/$1');


##Área administrativa
$routes->get('admin/pagina/(:hash)/edit', 'Admin\Pagina::edit/$1');
$routes->get('admin/pagina/(:hash)/delete', 'Admin\Pagina::delete/$1');

$routes->get('admin/usuario/(:hash)/edit', 'Admin\Usuario::edit/$1');
$routes->get('admin/usuario/(:hash)/delete', 'Admin\Usuario::delete/$1');




/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need to it be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
