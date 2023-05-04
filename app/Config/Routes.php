<?php

namespace Config;

use Myth\Auth\Config\Auth as AuthConfig;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->get('/', 'Home::index', ['filter' => ['login', 'role:owner,supervisor,operator']]);
$routes->get('trial', 'Home::trial');

// Myth/Auth Routes
$routes->group('/', static function ($routes) {
    $config         = config(AuthConfig::class);
    $reservedRoutes = $config->reservedRoutes;

    // Login/out
    $routes->get($reservedRoutes['login'], 'Auth::login', ['as' => 'login']);
    $routes->post($reservedRoutes['login'], 'Auth::attemptLogin');
    $routes->get($reservedRoutes['logout'], 'Auth::logout', ['as' => 'logout']);

    // Registration
    $routes->get($reservedRoutes['register'], 'Auth::register', ['as' => 'register']);
    $routes->post($reservedRoutes['register'], 'Auth::attemptRegister');

    // Activation
    $routes->get($reservedRoutes['activate-account'], 'Auth::activateAccount', ['as' => 'activate-account']);
    $routes->get($reservedRoutes['resend-activate-account'], 'Auth::resendActivateAccount', ['as' => 'resend-activate-account']);

    // Forgot/Resets
    $routes->get($reservedRoutes['forgot'], 'Auth::forgotPassword', ['as' => 'forgot']);
    $routes->post($reservedRoutes['forgot'], 'Auth::attemptForgot');
    $routes->get($reservedRoutes['reset-password'], 'Auth::resetPassword', ['as' => 'reset-password']);
    $routes->post($reservedRoutes['reset-password'], 'Auth::attemptReset');
});

// Require login
$routes->group('/', ['filter' => 'login'], function($routes) {
    
});

// Account Routes
$routes->group('account', ['filter'=>'login'], function($routes){
    $routes->get('', 'Account::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update', 'Account::updateaccount', ['filter' => 'role:owner,supervisor,operator']);
});

// Business Routes
$routes->group('business', ['filter'=>'login'], function($routes){
    $routes->get('', 'Account::business', ['filter' => 'role:owner']);
});

// User Routes
$routes->group('user', ['filter'=>'login'], function($routes){

    $routes->get('', 'User::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'User::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('edit(:num)', 'User::edit/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'User::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'User::delete/$1', ['filter' => 'role:owner,supervisor,operator']);
});

//Outlet Routes
$routes->group('outlet', ['filter'=>'login'], function($routes){

    $routes->get('', 'Outlet::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Outlet::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'Outlet::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Outlet::delete/$1', ['filter' => 'role:owner,supervisor,operator']);
});

//Product Routes
$routes->group('product', ['filter'=>'login'], function($routes){

    //product
    $routes->get('', 'Product::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Product::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('edit(:num)', 'Product::edit/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'Product::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Product::delete/$1', ['filter' => 'role:owner,supervisor,operator']);

    //category
    $routes->post('createcat', 'Product::createcat', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('editcat(:num)', 'Product::editcat/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('deletecat/(:num)', 'Product::deletecat/$1', ['filter' => 'role:owner,supervisor,operator']);

    // brand
    $routes->post('createbrand', 'Product::createbrand', ['filter' => 'role:owner,supervisor,operator']);
    $routes->Post('editbrand(:num)', 'Product::editbrand/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('deletebrand/(:num)', 'Product::deletebrand/$1', ['filter' => 'role:owner,supervisor,operator']);

    // variant
    $routes->get('indexvar/(:num)', 'Product::indexvar/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('createvar/(:num)', 'Product::createvar/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('createvar/(:num)', 'Product::createvar/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('editvar/(:num)', 'Product::editvar/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('deletevar/(:num)', 'Product::deletevar/$1', ['filter' => 'role:owner,supervisor,operator']);
  
});

//Customer Routes
$routes->group('customer', ['filter'=>'login'], function($routes){

    $routes->get('', 'Customer::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Customer::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'CUstomer::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Customer::delete/$1', ['filter' => 'role:owner,supervisor,operator']);
});

//Transaction Routes
$routes->group('transaction', ['filter'=>'login'], function($routes){

    $routes->get('', 'Transaction::index', ['filter' => 'role:owner,supervisor,operator']);
});



/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
