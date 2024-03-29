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

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

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
$routes->get('api/v2/business/(:segment)/public/blogs', 'Api\V2\Webpages::getPublicBlogs/$1');


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
