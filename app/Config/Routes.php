<?php namespace Config;

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
// $routes->get('/', 'Home::index');
$routes->get('/', 'V2\Colleges::index');
$routes->add('logout', 'Home::logout');
$routes->add('login', 'Home::login');
$routes->add('heartbeat', 'Home::heartbeat');
// 调用入口
$routes->add('eai-bin/(:any)', 'Api\Eai::main/$1');
$routes->group('upload',function ($routes){
    $routes->add('dofile/(:any)', 'Comm\Upload::dofile/$1');
    $routes->add('do/(:any)', 'Comm\Upload::do/$1');
    $routes->add('bucket', 'Comm\Upload::bucket');
});

// 院校
$routes->group('colleges',function ($routes) {
    $routes->add('index','V2\Colleges::index');
    $routes->add('detail','V2\Colleges::detail');
});

// 专业
$routes->group('special',function ($routes) {
    $routes->add('index','V2\Specials::index');
    $routes->add('choose','V2\Specials::choose');
});

$routes->add('monzy', 'V2\Colleges::suggest');
$routes->add('report', 'V2\Colleges::report');
$routes->add('mycard', 'V2\Aspiration::mycard');
$routes->add('score', 'V2\Tool::score');
$routes->add('section', 'V2\Tool::section');

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
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
