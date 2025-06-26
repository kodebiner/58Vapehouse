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

$routes->get('/', 'Transaction::index', ['filter' => ['login', 'role:owner,supervisor,operator,guests']]);
$routes->get('home/index', 'Transaction::index', ['filter' => ['login', 'role:owner,supervisor,operator,guests']]);
$routes->get('dashboard', 'Home::index', ['filter' => ['login', 'role:owner,supervisor,operator,guests']]);
$routes->get('billhistory', 'Home::billhistory', ['filter' => ['login', 'role:owner,supervisor,operator']]);
// $routes->get('home/dashboard', 'Home::index', ['filter' => ['login', 'role:owner,supervisor,operator,guests']]);
$routes->get('trial', 'Home::trial');
$routes->get('ownership', 'Home::ownership');
// $routes->get('stockmovemigrate', 'Home::stockmove');
// $routes->get('createsku', 'Home::sku');

//Invoice 
$routes->get('pay/copyprint/(:num)', 'Pay::copyprint/$1');
$routes->get('pay/invoicebook/(:num)', 'Pay::invoicebook/$1');
$routes->get('debt/invoice/(:num)', 'Debt::invoice/$1');

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
    $routes->post('profile', 'Upload::profile', ['filter' => 'role:owner']);
    $routes->post('removeprofile', 'Upload::removeprofile', ['filter' => 'role:owner']);
    $routes->post('logo', 'Upload::logo', ['filter' => 'role:owner']);
    $routes->post('removelogo', 'Upload::removelogo', ['filter' => 'role:owner']);
    $routes->post('productcreate', 'Upload::productcreate', ['filter' => 'role:owner']);
    $routes->post('removeproductcreate', 'Upload::removeproductcreate', ['filter' => 'role:owner']);
    $routes->post('productedit/(:num)', 'Upload::productedit/$1', ['filter' => 'role:owner']);
    $routes->post('removeproductedit/(:num)', 'Upload::removeproductedit/$1', ['filter' => 'role:owner']);
    $routes->post('promocreate', 'Upload::promocreate', ['filter' => 'role:owner,supervisor']);
    $routes->post('removepromocreate', 'Upload::removepromocreate', ['filter' => 'role:owner,supervisor']);
    $routes->post('promoedit/(:num)', 'Upload::promoedit/$1', ['filter' => 'role:owner,supervisor']);
    $routes->post('removepromoedit/(:num)', 'Upload::removepromoedit/$1', ['filter' => 'role:owner,supervisor']);
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
    $routes->get('', 'User::index', ['filter' => 'role:owner,supervisor']);
    $routes->post('create', 'User::create', ['filter' => 'role:owner,supervisor']);
    $routes->get('edit(:num)', 'User::edit/$1', ['filter' => 'role:owner,supervisor']);
    $routes->post('update/(:num)', 'User::update/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('delete/(:num)', 'User::delete/$1', ['filter' => 'role:owner,supervisor']);
});

// Outlet Routes
$routes->group('outlet', ['filter'=>'login'], function($routes){
    $routes->get('', 'Outlet::index', ['filter' => 'role:owner,supervisor']);
    $routes->post('create', 'Outlet::create', ['filter' => 'role:owner,supervisor']);
    $routes->post('update/(:num)', 'Outlet::update/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('delete/(:num)', 'Outlet::delete/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('pick/(:num)', 'Home::outletses/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Product Routes
$routes->group('product', ['filter'=>'login'], function($routes){

    // product
    $routes->get('', 'Product::index', ['filter' => 'role:owner,supervisor']);
    $routes->post('favorite/(:num)', 'Product::favorite/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('favorite/(:num)', 'Product::favorite/$1', ['filter' => 'role:owner,supervisor']);
    $routes->post('create', 'Product::create', ['filter' => 'role:owner,supervisor']);
    $routes->get('edit(:num)', 'Product::edit/$1', ['filter' => 'role:owner,supervisor']);
    $routes->post('update/(:num)', 'Product::update/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('delete/(:num)', 'Product::delete/$1', ['filter' => 'role:owner,supervisor']);

    // category
    $routes->post('createcat', 'Product::createcat', ['filter' => 'role:owner,supervisor']);
    $routes->post('editcat(:num)', 'Product::editcat/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('deletecat/(:num)', 'Product::deletecat/$1', ['filter' => 'role:owner,supervisor']);

    // brand
    $routes->post('createbrand', 'Product::createbrand', ['filter' => 'role:owner,supervisor']);
    $routes->Post('editbrand(:num)', 'Product::editbrand/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('deletebrand/(:num)', 'Product::deletebrand/$1', ['filter' => 'role:owner,supervisor']);

    // variant
    $routes->get('indexvar/(:num)', 'Product::indexvar/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('createvar/(:num)', 'Product::createvar/$1', ['filter' => 'role:owner,supervisor']);
    $routes->post('createvar/(:num)', 'Product::createvar/$1', ['filter' => 'role:owner,supervisor']);
    $routes->post('editvar/(:num)', 'Product::editvar/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('deletevar/(:num)', 'Product::deletevar/$1', ['filter' => 'role:owner,supervisor']);

    // Export
    $routes->get('export', 'Product::export', ['filter' => 'role:owner,supervisor']);

    // // Change Status
    // $routes->get('change', 'Product::change', ['filter' => 'role:owner,supervisor']);

    // Stock History
    $routes->get('history/(:num)', 'Product::history/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Export
$routes->group('export', ['filter'=>'login'], function($routes){
    $routes->get('prod', 'Export::prod', ['filter' => 'role:owner,supervisor']);
    $routes->get('transaction', 'Export::transaction', ['filter' => 'role:owner,supervisor']);
    $routes->get('payment', 'Export::payment', ['filter' => 'role:owner,supervisor']);
    $routes->get('product', 'Export::product', ['filter' => 'role:owner,supervisor']);
    $routes->get('profit', 'Export::profit', ['filter' => 'role:owner,supervisor']);
    $routes->get('employe', 'Export::employe', ['filter' => 'role:owner,supervisor']);
    $routes->get('customer', 'Export::customer', ['filter' => 'role:owner,supervisor']);
    $routes->get('presence', 'Export::presence', ['filter' => 'role:owner,supervisor']);
    $routes->get('bundle', 'Export::bundle', ['filter' => 'role:owner,supervisor']);
    $routes->get('diskon', 'Export::diskon', ['filter' => 'role:owner,supervisor']);
    $routes->get('sales', 'Export::sales', ['filter' => 'role:owner,supervisor']);
    $routes->get('category', 'Export::category', ['filter' => 'role:owner,supervisor']);
    $routes->get('brand', 'Export::brand', ['filter' => 'role:owner,supervisor']);
    $routes->get('stockcategory', 'Export::stockcategory', ['filter' => 'role:owner,supervisor']);
    $routes->get('customerlist', 'Export::customerlist', ['filter' => 'role:owner,supervisor,']);
    $routes->get('sop', 'Export::sop', ['filter' => 'role:owner,supervisor']);
    $routes->get('dayrep', 'Export::dayrep', ['filter' => 'role:owner,supervisor']);
    $routes->get('dailysell', 'Export::dailysell', ['filter' => 'role:owner,supervisor']);
});

// Customer Routes
$routes->group('customer', ['filter'=>'login'], function($routes){
    $routes->get('', 'Customer::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Customer::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'Customer::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Customer::delete/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Stock
$routes->group('stock', ['filter'=>'login'], function($routes){
    // Stock
    $routes->get('', 'Stock::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Stock::create', ['filter' => 'role:owner']);
    $routes->post('update/(:num)', 'Stock::update/$1', ['filter' => 'role:owner']);
    $routes->get('delete/(:num)', 'Stock::delete/$1', ['filter' => 'role:owner']);

    // Stock Cycle
    $routes->get('stockcycle', 'Stock::stockcycle', ['filter' => 'role:owner']);

    // Supplier
    $routes->get('supplier', 'Stock::indexsupplier', ['filter' => 'role:owner']);
    $routes->post('createsup', 'Stock::createsup', ['filter' => 'role:owner']);
    $routes->post('updatesup/(:num)', 'Stock::updatesup/$1', ['filter' => 'role:owner']);
    $routes->get('deletesup/(:num)', 'Stock::deletesup/$1', ['filter' => 'role:owner']);

    // Purchase
    $routes->get('purchase', 'Stock::indexpurchase', ['filter' => 'role:owner']);
    $routes->post('product', 'Stock::product', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('createpur', 'Stock::createpur', ['filter' => 'role:owner']);
    $routes->post('confirm/(:num)', 'Stock::confirmpur/$1', ['filter' => 'role:owner']);
    $routes->post('updatepur/(:num)', 'Stock::updatepur/$1', ['filter' => 'role:owner']);
    $routes->post('cancelpur/(:num)', 'Stock::cancelpur/$1', ['filter' => 'role:owner']);

    // Inventory
    $routes->get('inventory', 'Stock::indexinventory', ['filter' => 'role:owner']);
    $routes->post('createinv', 'Stock::createinv', ['filter' => 'role:owner']);
    $routes->post('updateinv/(:num)', 'Stock::updateinv/$1', ['filter' => 'role:owner']);
    $routes->get('deleteinv/(:num)', 'Stock::deleteinv/$1', ['filter' => 'role:owner']);
});

// Transaction Other / cashin cashout
$routes->group('cashinout', ['filter'=>'login'], function($routes){
    $routes->get('', 'Trxother::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Trxother::create', ['filter' => 'role:owner,supervisor,operator']);
    // $routes->post('withdraw', 'Trxother::withdraw', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'Trxother::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Trxother::delete/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Transaction Routes
$routes->group('transaction', ['filter'=>'login'], function($routes){
    $routes->get('', 'Transaction::index', ['filter' => 'role:owner,supervisor,operator']);
    // $routes->get('create', 'Transaction::index',['filter'=> 'role:owner,supervisor,operator']);
    // $routes->post('create', 'Transaction::create',['filter'=> 'role:owner,supervisor,operator']);
    // $routes->post('pay', 'Transaction::pay',['filter'=> 'role:owner,supervisor,operator']);
    // $routes->post('restorestock', 'Transaction::restorestock',['filter'=> 'role:owner,supervisor,operator']);
    // $routes->get('refund/(:num)', 'Transaction::refund/$1');
});

// Transaction History
$routes->group('trxhistory', ['filter'=>'login'], function($routes){
    $routes->get('', 'Debt::indextrx', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('trx', 'Debt::indextrx', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('debt', 'Debt::indexdebt', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('debtpay', 'Debt::indexdebtins', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('topup', 'Debt::indextopup', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('refund/(:num)', 'Debt::refund/$1', ['filter' => 'role:owner']);
});

// Debt
$routes->group('debt', ['filter'=>'login'], function($routes){
    $routes->get('', 'Debt::indexdebt', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('debtpay', 'Debt::indexdebtins', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('pay/(:num)', 'Debt::paydebt/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('refund/(:num)', 'Debt::refundins/$1', ['filter' => 'role:owner']);
});

// Top Up
$routes->group('topup', ['filter'=>'login'], function($routes){
    $routes->get('', 'Debt::indextopup', ['filter' => 'role:owner,supervisor,operator']);
});

// Pay Routes
$routes->group('pay', ['filter'=>'login'], function($routes){
    $routes->get('', 'Pay::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Pay::create',['filter'=> 'role:owner,supervisor,operator']);
    // $routes->post('save', 'Pay::save',['filter'=> 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Pay::bookingdelete/$1',['filter'=> 'role:owner,supervisor,operator']);
    $routes->post('pay', 'Pay::pay',['filter'=> 'role:owner,supervisor,operator']);
    $routes->get('copyprint/(:num)', 'Pay::copyprint/$1',['filter'=> 'role:owner,supervisor,operator']);
    $routes->get('bookprint/(:num)','Pay::bookprint/$1',['filter'=>'role:owner,supervisor,operator']);
    $routes->post('topup', 'Pay::topup',['filter' =>'role:owner,supervisor,operator']);
});

$routes->group('api/member', function($routes) {
    $routes->get('search', 'Api\MemberApiController::search');
    $routes->get('detail', 'Api\MemberApiController::detail');
});

// Report Routes
$routes->group('report', ['filter'=>'login'], function($routes){
    $routes->get('', 'Report::index', ['filter' => 'role:owner,supervisor']);
    $routes->get('penjualan', 'Report::penjualan', ['filter' => 'role:owner,supervisor']);
    $routes->get('keuntungan', 'Report::keuntungan', ['filter' => 'role:owner,supervisor']);
    $routes->get('keuntungandasar', 'Report::keuntungandasar', ['filter' => 'role:owner,supervisor']);
    $routes->get('diskon', 'Report::diskon', ['filter' => 'role:owner,supervisor']);
    $routes->get('bundle', 'Report::bundle', ['filter' => 'role:owner,supervisor']);
    $routes->get('payment', 'Report::payment', ['filter' => 'role:owner,supervisor']);
    $routes->get('product', 'Report::product', ['filter' => 'role:owner,supervisor']);
    $routes->get('presence', 'Report::presence', ['filter' => 'role:owner,supervisor']);
    $routes->get('presence/(:num)', 'Report::presencedetail/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('presence/(:any)', 'Report::presencedetail/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('employe', 'Report::employe', ['filter' => 'role:owner,supervisor']);
    $routes->get('customer', 'Report::customer', ['filter' => 'role:owner,supervisor']);
    $routes->get('customerdetail/(:num)', 'Report::customerdetail/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('category', 'Report::category', ['filter' => 'role:owner,supervisor']);
    $routes->get('brand', 'Report::brand', ['filter' => 'role:owner,supervisor']);
    $routes->get('sop', 'Report::sop', ['filter' => 'role:owner,supervisor']);
    $routes->get('dailysell', 'Report::dailysell', ['filter' => 'role:owner,supervisor']);
    // $routes->get('stockcategory', 'Report::stockcategory', ['filter' => 'role:owner,supervisor']);

});

// Sop Routes
$routes->group('sop', ['filter'=>'login'], function($routes){
    $routes->get('', 'Sop::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Sop::create',['filter'=> 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'Sop::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Sop::delete/$1',['filter' => 'role:owner,supervisor,operator']);
    $routes->get('todolist', 'Sop::todolist',['filter'=> 'role:owner,supervisor,operator']);
    $routes->post('updatetodo', 'Sop::updatetodo',['filter'=> 'role:owner,supervisor,operator']);
});

// Stock Movement
$routes->group('stockmove', ['filter'=>'login'], function($routes){
    $routes->get('', 'StockMovement::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('product', 'StockMovement::product', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'StockMovement::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('confirm/(:num)', 'StockMovement::confirm/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'StockMovement::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('cancel/(:num)', 'StockMovement::cancel/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('stockmovementprint/(:num)', 'StockMovement::print/$1', ['filter' => 'role:owner,supervisor,operator']);
});

// Stock Adjustment
$routes->group('stockadjustment', ['filter'=>'login'], function($routes){
    $routes->get('', 'StockAdjustment::index', ['filter' => 'role:owner']);
    $routes->post('create', 'StockAdjustment::create', ['filter' => 'role:owner']);
    $routes->post('product', 'StockAdjustment::product', ['filter' => 'role:owner']);
});

// Wallet Management
$routes->group('walletman', ['filter'=>'login'], function($routes){
    $routes->get('', 'CashMan::index', ['filter' => 'role:owner']);
    $routes->post('create', 'CashMan::create', ['filter' => 'role:owner']);
    $routes->post('update/(:num)', 'CashMan::update/$1', ['filter' => 'role:owner']);
});

// Wallet Movement
$routes->group('walletmove', ['filter'=>'login'], function($routes){
    $routes->get('', 'CashMove::index', ['filter' => 'role:owner']);
    $routes->post('create', 'CashMove::create', ['filter' => 'role:owner']);
});

// Cash Expenses
$routes->group('cashexp', ['filter'=>'login'], function($routes){
    $routes->get('', 'CashExp::index', ['filter' => 'role:owner']);
    $routes->post('create', 'CashExp::create', ['filter' => 'role:owner']);
});

// Payment
$routes->group('payment', ['filter'=>'login'], function($routes){
    $routes->get('', 'Payment::index', ['filter' => 'role:owner']);
    $routes->post('create', 'Payment::create', ['filter' => 'role:owner']);
    $routes->post('update/(:num)', 'Payment::update/$1', ['filter' => 'role:owner']);
    $routes->get('delete/(:num)', 'Payment::delete/$1', ['filter' => 'role:owner']);

});

// Bundle
$routes->group('bundle', ['filter'=>'login'], function($routes){
    // bundle
    $routes->get('', 'Bundle::index', ['filter' => 'role:owner,supervisor']);
    $routes->post('create', 'Bundle::create', ['filter' => 'role:owner,supervisor']);
    $routes->post('update/(:num)', 'Bundle::update/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('delete/(:num)', 'Bundle::delete/$1', ['filter' => 'role:owner,supervisor']);

    // bundle detail
    $routes->get('indexbund/(:num)', 'Bundle::indexbund/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('createbund/(:num)', 'Bundle::createbund/$1', ['filter' => 'role:owner,supervisor']);
    $routes->post('createbund/(:num)', 'Bundle::createbund/$1', ['filter' => 'role:owner,supervisor']);
    $routes->post('editbund/(:num)', 'Bundle::editbund/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('deletebund/(:num)', 'Bundle::deletebund/$1', ['filter' => 'role:owner,supervisor']);
});

// Presence
$routes->group('presence', ['filter'=>'login'], function($routes){
    // presence
    $routes->get('', 'Presence::index', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('create', 'Presence::create', ['filter' => 'role:owner,supervisor,operator']);
    $routes->post('update/(:num)', 'Presence::update/$1', ['filter' => 'role:owner,supervisor,operator']);
    $routes->get('delete/(:num)', 'Presence::delete/$1', ['filter' => 'role:owner,supervisor,operator']);
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
    $routes->post('checkpoint', 'Trxother::checkpoint', ['filter' => 'role:owner,supervisor,operator']);
});

// Promo
$routes->group('promo', ['filter'=>'login'], function($routes){
    $routes->get('', 'Promo::index', ['filter' => 'role:owner,supervisor']);
    $routes->post('create', 'Promo::create', ['filter' => 'role:owner,supervisor']);
    $routes->post('update/(:num)', 'Promo::update/$1', ['filter' => 'role:owner,supervisor']);
    $routes->get('delete/(:num)', 'Promo::delete/$1', ['filter' => 'role:owner,supervisor']);
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
