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
    $routes->get('coba', 'Coba::ajax', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('coba', 'Coba::ajax', ['filter' => 'role:owner,supervisor,operator']);
});

// Upload Routes
$routes->group('upload', ['filter' => 'login'], function($routes) {
    $routes->post('profile', 'Upload::profile', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('removeprofile', 'Upload::removeprofile', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('logo', 'Upload::logo', ['filter' => 'role:owner']);
    $routes->post('removelogo', 'Upload::removelogo', ['filter' => 'role:owner']);
    $routes->post('productcreate', 'Upload::productcreate', ['filter' => 'role:owner,supervisor']);
    $routes->post('removeproductcreate', 'Upload::removeproductcreate', ['filter' => 'role:owner,supervisor']);
    $routes->post('productedit/(:num)', 'Upload::productedit/$1', ['filter' => 'role:owner,supervisor']);
    $routes->post('removeproductedit/(:num)', 'Upload::removeproductedit/$1', ['filter' => 'role:owner,supervisor']);
});

// Account Routes
$routes->group('account', ['filter'=>'login'], function($routes){
    $routes->get('', 'Account::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update', 'Account::updateaccount', ['filter' => 'role:owner,supervisor,operator']);
});

// Business Routes
$routes->group('business', ['filter'=>'login'], function($routes){
    $routes->get('', 'Account::business', ['filter' => 'role:owner']);
    $routes->post('save', 'Account::updatebusiness', ['filter' => 'role:owner']);
});

// User Routes
$routes->group('user', ['filter'=>'login'], function($routes){
    $routes->get('', 'User::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'User::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('edit(:num)', 'User::edit/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'User::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'User::delete/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Outlet Routes
$routes->group('outlet', ['filter'=>'login'], function($routes){
    $routes->get('', 'Outlet::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Outlet::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'Outlet::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Outlet::delete/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('pick/(:num)', 'Home::outletses/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Product Routes
$routes->group('product', ['filter'=>'login'], function($routes){

    // product
    $routes->get('', 'Product::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('favorite/(:num)', 'Product::favorite/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('favorite/(:num)', 'Product::favorite/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Product::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('edit(:num)', 'Product::edit/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'Product::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Product::delete/$1', ['filter' => 'role:owner,supervisor,operator']);

    // category
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

// Customer Routes
$routes->group('customer', ['filter'=>'login'], function($routes){
    $routes->get('', 'Customer::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Customer::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'CUstomer::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Customer::delete/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Stock
$routes->group('stock', ['filter'=>'login'], function($routes){
    // Stock
    $routes->get('', 'Stock::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Stock::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'Stock::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Stock::delete/$1', ['filter' => 'role:owner,supervisor,operator']);

    // Stock Cycle
    $routes->get('stockcycle', 'Stock::stockcycle', ['filter' => 'role:owner,supervisor,operator']);

    // Supplier
    $routes->get('supplier', 'Stock::indexsupplier', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('createsup', 'Stock::createsup', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('updatesup/(:num)', 'Stock::updatesup/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('deletesup/(:num)', 'Stock::deletesup/$1', ['filter' => 'role:owner,supervisor,operator']);

    // Purchase
    $routes->get('purchase', 'Stock::indexpurchase', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('createpur', 'Stock::createpur', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('confirm/(:num)', 'Stock::confirmpur/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('updatepur/(:num)', 'Stock::updatepur/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('cancelpur/(:num)', 'Stock::cancelpur/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Transaction Other / cashin cashout
$routes->group('cashinout', ['filter'=>'login'], function($routes){
    $routes->get('', 'Trxother::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Trxother::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('withdraw', 'Trxother::withdraw', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'Trxother::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Trxother::delete/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Transaction Routes
$routes->group('transaction', ['filter'=>'login'], function($routes){
    $routes->get('', 'Transaction::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('create', 'Transaction::index',['filter'=> 'role:owner,supervisor,operator']);
    $routes->post('create', 'Transaction::create',['filter'=> 'role:owner,supervisor,operator']);
    $routes->post('pay', 'Transaction::pay',['filter'=> 'role:owner,supervisor,operator']);
    $routes->post('restorestock', 'Transaction::restorestock',['filter'=> 'role:owner,supervisor,operator']);
});

// Transaction History
$routes->group('trxhistory', ['filter'=>'login'], function($routes){
    $routes->get('', 'Debt::indextrx', ['filter' => 'role:owner,supervisor,operator']);
});

// Debt
$routes->group('debt', ['filter'=>'login'], function($routes){
    $routes->get('', 'Debt::indexdebt', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('debtpay', 'Debt::indexdebtins', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('pay/(:num)', 'Debt::paydebt/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Top Up
$routes->group('topup', ['filter'=>'login'], function($routes){
    $routes->get('', 'Debt::indextopup', ['filter' => 'role:owner,supervisor,operator']);
});

// Pay Routes
$routes->group('pay', ['filter'=>'login'], function($routes){
    $routes->get('', 'Pay::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Pay::create',['filter'=> 'role:owner,supervisor,operator']);
    $routes->post('save', 'Pay::save',['filter'=> 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Pay::bookingdelete/$1',['filter'=> 'role:owner,supervisor,operator']);
    $routes->post('pay', 'Pay::pay',['filter'=> 'role:owner,supervisor,operator']);
    $routes->get('copyprint/(:num)', 'Pay::copyprint/$1',['filter'=> 'role:owner,supervisor,operator']);
    $routes->get('bookprint/(:num)','Pay::bookprint/$1',['filter'=>'role:owner,supervisor,operator']);
    $routes->post('topup', 'Pay::topup',['filter' =>'role:owner,supervisor']);
});

//Invoice 
$routes->get('pay/invoice/(:num)', 'Pay::invoice/$1');
$routes->get('pay/invoicebook/(:num)', 'Pay::invoicebook/$1');

// Report Routes
$routes->group('report', ['filter'=>'login'], function($routes){
    $routes->get('', 'Report::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('penjualan', 'Report::penjualan', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('keuntungan', 'Report::keuntungan', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('keuntungandasar', 'Report::keuntungandasar', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('diskon', 'Report::diskon', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('bundle', 'Report::bundle', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('payment', 'Report::payment', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('product', 'Report::product', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('presence', 'Report::presence', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('presence/(:num)', 'Report::presencedetail/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('employe', 'Report::employe', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('customer', 'Report::customer', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('customerdetail/(:num)', 'Report::customerdetail/$1', ['filter' => 'role:owner,supervisor,operator']);

});

// Sop Routes
$routes->group('sop', ['filter'=>'login'], function($routes){
    $routes->get('', 'Sop::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Sop::create',['filter'=> 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'Sop::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Sop::delete/$1',['filter' => 'role:owner,supervisor,operator']);
    $routes->get('todolist', 'Sop::todolist',['filter'=> 'role:owner,supervisor,operator']);
});

// Restock Routes
$routes->group('stock', ['filter'=>'login'], function($routes){
    $routes->get('', 'Stock::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('restock', 'Stock::restock', ['filter' => 'role:owner,supervisor,operator']);
});

// Stock Movement
$routes->group('stockmove', ['filter'=>'login'], function($routes){
    $routes->get('', 'StockMove::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'StockMove::create', ['filter' => 'role:owner,supervisor,operator']);
});

// Stock Adjustment
$routes->group('stockadjustment', ['filter'=>'login'], function($routes){
    $routes->get('', 'StockAdjustment::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'StockAdjustment::create', ['filter' => 'role:owner,supervisor,operator']);
});

// Wallet Management
$routes->group('walletman', ['filter'=>'login'], function($routes){
    $routes->get('', 'CashMan::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'CashMan::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'CashMan::update/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Wallet Movement
$routes->group('walletmove', ['filter'=>'login'], function($routes){
    $routes->get('', 'CashMove::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'CashMove::create', ['filter' => 'role:owner,supervisor,operator']);
});

// Payment
$routes->group('payment', ['filter'=>'login'], function($routes){
    $routes->get('', 'Payment::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Payment::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'Payment::update/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('delete/(:num)', 'Payment::delete/$1', ['filter' => 'role:owner,supervisor']);

});

// Bundle
$routes->group('bundle', ['filter'=>'login'], function($routes){
    // bundle
    $routes->get('', 'Bundle::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Bundle::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'Bundle::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Bundle::delete/$1', ['filter' => 'role:owner,supervisor,operator']);

    // bundle detail
    $routes->get('indexbund/(:num)', 'Bundle::indexbund/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('createbund/(:num)', 'Bundle::createbund/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('createbund/(:num)', 'Bundle::createbund/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('editbund/(:num)', 'Bundle::editbund/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('deletebund/(:num)', 'Bundle::deletebund/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Presence
$routes->group('presence', ['filter'=>'login'], function($routes){
    // presence
    $routes->get('', 'presence::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'presence::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'presence::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'presence::delete/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Reminder
$routes->group('reminder', ['filter'=>'login'], function($routes){
    $routes->get('', 'Reminder::index', ['filter' => 'role:owner,supervisor,operator']);
});

// Daily Report
$routes->group('dayrep', ['filter'=>'login'], function($routes){
    $routes->get('', 'DailyReport::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('open', 'DailyReport::open', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('close', 'DailyReport::close', ['filter' => 'role:owner,supervisor,operator']);
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
