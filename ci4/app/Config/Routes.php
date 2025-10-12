<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Users');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

$routes->get('google-login', 'Auth::googleLogin');
$routes->get('callback', 'Auth::callback');
$routes->get('google-logout', 'Auth::googleLogout');

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// Debug permissions route
$routes->get('debug-permissions', 'DebugPermissions::index');
$routes->get('debug_permissions', 'DebugPermissions::index');

$routes->get('users/delete/(:num)', 'Users::delete/$1');
//$routes->post('users/update/(:num)', 'Users::update');

//Ping function
$routes->get('/api/v1/ping', 'Home::ping');

//API V2
//Users Request API
$routes->resource('api/v2/users');
$routes->resource('api/v2/timeslips');
$routes->resource('api/v2/webpages');
$routes->resource('api/v2/tasks');
$routes->resource('api/v2/customers');
$routes->resource('api/v2/businesses');
$routes->resource('api/v2/contacts');
$routes->resource('api/v2/menu');
$routes->resource('api/v2/categories');
$routes->resource('api/v2/projects');
$routes->resource('api/v2/employees');
$routes->resource('api/v2/sprints');
$routes->resource('api/v2/userbusiness');
$routes->resource('api/v2/documents'); //
$routes->resource('api/v2/media'); //
$routes->resource('api/v2/enquiries');
$routes->resource('api/v2/taxes');
$routes->resource('api/v2/purchase_invoices');
$routes->resource('api/v2/sales_invoices');
$routes->resource('api/v2/work_orders');
$routes->resource('api/v2/purchase_orders');
$routes->resource('api/v2/blocks');
$routes->resource('api/v2/secrets');
$routes->resource('api/v2/create_domain');
$routes->resource('api/v2/services');
$routes->resource('api/v2/companies');
$routes->resource('api/v2/vm', ['controller' => 'Api\v2\VmController']);
$routes->resource('api/v2/incidents');
$routes->resource('api/v2/knowledge-base', ['controller' => 'Api\v2\KnowledgeBase']);
$routes->resource('api/v2/email-campaigns', ['controller' => 'Api\v2\EmailCampaigns']);
$routes->resource('api/v2/tags');
$routes->resource('api/v2/roles');
$routes->resource('api/v2/vat-returns', ['controller' => 'Api\v2\VatReturns']);
$routes->resource('api/v2/deployments');
$routes->get('api/v2/deployments/stats', 'Api\V2\Deployments::stats');
$routes->resource('api/v2/launchpad');
$routes->post('api/v2/launchpad/click/(:segment)', 'Api\V2\Launchpad::click/$1');
$routes->post('api/v2/launchpad/share', 'Api\V2\Launchpad::share');
$routes->get('api/v2/launchpad/recent', 'Api\V2\Launchpad::recent');
$routes->resource('scim/v2/Users', ['controller' => 'Api\V2\ScimUserController']);
$routes->resource('scim/v2/Groups', ['controller' => 'Api\V2\ScimGroupController']);

// New API v2 endpoints - added for complete menu coverage
$routes->resource('api/v2/tenants');
$routes->resource('api/v2/domains');
$routes->resource('api/v2/blog-comments', ['controller' => 'Api\v2\BlogComments']);
$routes->resource('api/v2/jobs');
$routes->resource('api/v2/job-applications', ['controller' => 'Api\v2\JobApplications']);
$routes->resource('api/v2/templates');
$routes->resource('api/v2/interviews');
$routes->resource('api/v2/products');
$routes->resource('api/v2/payments');
$routes->resource('api/v2/receipts');

// Hospital Management System APIs
$routes->resource('api/v2/hospital_staff');
$routes->resource('api/v2/patient_logs');
$routes->get('api/v2/patient_logs/timeline/(:num)', 'Api\V2\PatientLogs::timeline/$1');
$routes->get('api/v2/patient_logs/flagged', 'Api\V2\PatientLogs::flagged');
$routes->get('api/v2/patient_logs/medications/(:num)', 'Api\V2\PatientLogs::medications/$1');
$routes->get('api/v2/patient_logs/vital-signs/(:num)', 'Api\V2\PatientLogs::vitalSigns/$1');

// List project by business Id
$routes->get('api/v2/business/(:segment)/projects', 'Api\V2\Projects::projectsByBId/$1');
// Timeslip by UUID Id
$routes->get('api/v2/business/(:segment)/employee/(:segment)/tasks/(:segment)/timeslip', 'Api\V2\Timeslips::timeslipByTaskId/$1/$2/$3');
// List Task by business Id
$routes->get('api/v2/business/(:segment)/projects/(:segment)/employee/(:segment)/tasks', 'Api\V2\Tasks::tasksByPId/$1/$2/$3');
// List Tasks and Status by Employee Id
$routes->get('api/v2/business/(:segment)/employee/(:segment)/tasks-status', 'Api\V2\Tasks::tasksStatusByEId/$1/$2');
// Update Task Status by Task ID
$routes->put('api/v2/business/(:segment)/projects/(:segment)/tasks/update-status', 'Api\V2\Tasks::updateStatusByUuid');
// Add enquiry by business code
$routes->post('api/v2/enquiries/business-enqury', 'Api\V2\Enquiries::addEnquiryByBCode');
// List webpages by contacts
$routes->get('api/v2/business/(:segment)/contact/(:segment)/webpages', 'Api\V2\Webpages::getWebPages/$1/$2');
$routes->get('api/v2/business/(:segment)/contact/(:segment)/blogs', 'Api\V2\Webpages::getBlogsByCategory/$1/$2');
$routes->get('api/v2/business/(:segment)/contact/(:segment)/blog/(:segment)', 'Api\V2\Webpages::getBlogsByCode/$1/$2/$3');
$routes->get('api/v2/business/(:segment)/public/blogs', 'Api\V2\Webpages::getPublicBlogs/$1');
$routes->get('api/v2/business/(:segment)/public/blog/(:segment)', 'Api\V2\Webpages::getPublicBlog/$1/$2');

// API Documentation Routes
$routes->get('swagger', 'Swagger::index');
$routes->get('swagger/json', 'Swagger::json');
$routes->get('swagger/yaml', 'Swagger::yaml');
$routes->get('api-docs', 'Swagger::ui');
$routes->get('api/docs', 'Swagger::ui');

// Document Preview and Download Routes
$routes->get('documents/preview/(:segment)', 'Documents::preview/$1');
$routes->get('documents/download/(:segment)', 'Documents::download/$1');

// Accounting Module Routes
$routes->group('accounts', function($routes) {
    $routes->get('/', 'Accounts::index');
    $routes->get('edit/(:segment)', 'Accounts::edit/$1');
    $routes->get('edit', 'Accounts::edit');
    $routes->post('update', 'Accounts::update');
    $routes->post('delete/(:segment)', 'Accounts::delete/$1');
    $routes->get('accountsList', 'Accounts::accountsList');
    $routes->post('initializeChartOfAccounts', 'Accounts::initializeChartOfAccounts');
});

// Journal Entries Routes
$routes->group('journal-entries', function($routes) {
    $routes->get('/', 'JournalEntries::index');
    $routes->get('edit/(:segment)', 'JournalEntries::edit/$1');
    $routes->get('edit', 'JournalEntries::edit');
    $routes->post('update', 'JournalEntries::update');
    $routes->post('post/(:segment)', 'JournalEntries::post/$1');
    $routes->post('delete/(:segment)', 'JournalEntries::delete/$1');
    $routes->get('journalEntriesList', 'JournalEntries::journalEntriesList');
});

// Accounting Periods Routes
$routes->group('accounting-periods', function($routes) {
    $routes->get('/', 'AccountingPeriods::index');
    $routes->get('edit/(:segment)', 'AccountingPeriods::edit/$1');
    $routes->get('edit', 'AccountingPeriods::edit');
    $routes->post('update', 'AccountingPeriods::update');
    $routes->post('set-current/(:segment)', 'AccountingPeriods::setCurrent/$1');
    $routes->post('close-period/(:segment)', 'AccountingPeriods::closePeriod/$1');
    $routes->get('periodsList', 'AccountingPeriods::periodsList');
});

// Alternative route with underscores (for backward compatibility)
$routes->group('accounting_periods', function($routes) {
    $routes->get('/', 'AccountingPeriods::index');
    $routes->get('edit/(:segment)', 'AccountingPeriods::edit/$1');
    $routes->get('edit', 'AccountingPeriods::edit');
    $routes->post('update', 'AccountingPeriods::update');
    $routes->post('set-current/(:segment)', 'AccountingPeriods::setCurrent/$1');
    $routes->post('close-period/(:segment)', 'AccountingPeriods::closePeriod/$1');
    $routes->get('periodsList', 'AccountingPeriods::periodsList');
});

// Payments Routes
$routes->group('payments', function($routes) {
    $routes->get('/', 'Payments::index');
    $routes->get('edit/(:segment)', 'Payments::edit/$1');
    $routes->get('edit', 'Payments::edit');
    $routes->post('update', 'Payments::update');
    $routes->post('delete/(:segment)', 'Payments::delete/$1');
    $routes->get('paymentsList', 'Payments::paymentsList');
    $routes->post('post/(:segment)', 'Payments::post/$1');
    $routes->get('pdf/(:segment)', 'Payments::printRemittance/$1');
    $routes->get('download/(:segment)', 'Payments::downloadPDF/$1');
});

// Receipts Routes
$routes->group('receipts', function($routes) {
    $routes->get('/', 'Receipts::index');
    $routes->get('edit/(:segment)', 'Receipts::edit/$1');
    $routes->get('edit', 'Receipts::edit');
    $routes->post('update', 'Receipts::update');
    $routes->post('delete/(:segment)', 'Receipts::delete/$1');
    $routes->get('receiptsList', 'Receipts::receiptsList');
    $routes->post('post/(:segment)', 'Receipts::post/$1');
    $routes->get('pdf/(:segment)', 'Receipts::printReceipt/$1');
    $routes->get('download/(:segment)', 'Receipts::downloadPDF/$1');
});

// Hospital Staff Routes
$routes->group('hospital_staff', function($routes) {
    $routes->get('/', 'HospitalStaff::index');
    $routes->get('edit/(:segment)', 'HospitalStaff::edit/$1');
    $routes->get('edit', 'HospitalStaff::edit');
    $routes->post('update', 'HospitalStaff::update');
    $routes->post('delete/(:segment)', 'HospitalStaff::delete/$1');
    $routes->get('staffList', 'HospitalStaff::staffList');
    $routes->get('dashboard', 'HospitalStaff::dashboard');
    $routes->get('byDepartment/(:segment)', 'HospitalStaff::byDepartment/$1');
});

// Patient Logs Routes
$routes->group('patient_logs', function($routes) {
    $routes->get('/', 'PatientLogs::index');
    $routes->get('edit/(:segment)', 'PatientLogs::edit/$1');
    $routes->get('edit', 'PatientLogs::edit');
    $routes->post('update', 'PatientLogs::update');
    $routes->post('delete/(:segment)', 'PatientLogs::delete/$1');
    $routes->get('logsList', 'PatientLogs::logsList');
    $routes->get('timeline/(:num)', 'PatientLogs::timeline/$1');
    $routes->get('flagged', 'PatientLogs::flagged');
    $routes->get('scheduled', 'PatientLogs::scheduled');
    $routes->get('quickLog', 'PatientLogs::quickLog');
    $routes->post('saveQuickLog', 'PatientLogs::saveQuickLog');
});

// Financial Reports Routes
$routes->get('balance-sheet', 'BalanceSheet::index');
$routes->get('balance-sheet/export-pdf', 'BalanceSheet::exportPDF');
$routes->get('trial-balance', 'TrialBalance::index');
$routes->get('profit-loss', 'ProfitLoss::index');
$routes->get('cash-flow', 'CashFlow::index');
$routes->post('cash-flow/generate', 'CashFlow::generate');
$routes->get('cash-flow/exportPDF', 'CashFlow::exportPDF');

// Deployments Routes
$routes->group('deployments', function($routes) {
    $routes->get('/', 'Deployments::index');
    $routes->get('edit/(:segment)', 'Deployments::edit/$1');
    $routes->get('edit', 'Deployments::edit');
    $routes->post('update', 'Deployments::update');
    $routes->post('delete/(:segment)', 'Deployments::delete/$1');
    $routes->get('deploymentsList', 'Deployments::deploymentsList');
    $routes->post('checkDeploymentAccess', 'Deployments::checkDeploymentAccess');
    $routes->post('executeDeployment', 'Deployments::executeDeployment');
    $routes->get('managePermissions', 'Deployments::managePermissions');
    $routes->post('savePermission', 'Deployments::savePermission');
    $routes->post('generatePasscode', 'Deployments::generatePasscode');
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
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
