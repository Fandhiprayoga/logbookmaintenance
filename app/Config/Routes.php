<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ---------------------------------------------------------------
// Auth Routes (Shield)
// ---------------------------------------------------------------
service('auth')->routes($routes);

// ---------------------------------------------------------------
// Public Routes
// ---------------------------------------------------------------
$routes->get('/', 'AuthController::login');

// ---------------------------------------------------------------
// Protected Routes (require login)
// ---------------------------------------------------------------
$routes->group('', ['filter' => 'session'], static function ($routes) {

    // Dashboard
    $routes->get('dashboard', 'DashboardController::index');

    // Profile
    $routes->get('profile', 'ProfileController::index');
    $routes->post('profile/update', 'ProfileController::update');

    // ---------------------------------------------------------------
    // Admin Routes (require admin.access permission)
    // ---------------------------------------------------------------
    $routes->group('admin', ['filter' => 'permission:admin.access'], static function ($routes) {

        // User Management
        $routes->group('users', static function ($routes) {
            $routes->get('/', 'UserController::index', ['filter' => 'permission:users.list']);
            $routes->get('create', 'UserController::create', ['filter' => 'permission:users.create']);
            $routes->post('store', 'UserController::store', ['filter' => 'permission:users.create']);
            $routes->get('edit/(:num)', 'UserController::edit/$1', ['filter' => 'permission:users.edit']);
            $routes->post('update/(:num)', 'UserController::update/$1', ['filter' => 'permission:users.edit']);
            $routes->post('delete/(:num)', 'UserController::delete/$1', ['filter' => 'permission:users.delete']);
            $routes->post('assign-role/(:num)', 'UserController::assignRole/$1', ['filter' => 'permission:users.manage-roles']);
        });

        // Role Management (superadmin only)
        $routes->group('roles', ['filter' => 'role:superadmin'], static function ($routes) {
            $routes->get('/', 'RoleController::index');
            $routes->get('permissions', 'RoleController::permissions');
        });

        // Settings
        $routes->get('settings', 'SettingController::index', ['filter' => 'permission:admin.settings']);
        $routes->post('settings/update', 'SettingController::update', ['filter' => 'permission:admin.settings']);

        // Master Aplikasi
        $routes->group('apps', static function ($routes) {
            $routes->get('/', 'AppController::index', ['filter' => 'permission:apps.list']);
            $routes->get('create', 'AppController::create', ['filter' => 'permission:apps.create']);
            $routes->post('store', 'AppController::store', ['filter' => 'permission:apps.create']);
            $routes->get('edit/(:num)', 'AppController::edit/$1', ['filter' => 'permission:apps.edit']);
            $routes->post('update/(:num)', 'AppController::update/$1', ['filter' => 'permission:apps.edit']);
            $routes->post('delete/(:num)', 'AppController::delete/$1', ['filter' => 'permission:apps.delete']);
        });

        // Master Kategori
        $routes->group('categories', static function ($routes) {
            $routes->get('/', 'CategoryController::index', ['filter' => 'permission:categories.list']);
            $routes->get('create', 'CategoryController::create', ['filter' => 'permission:categories.create']);
            $routes->post('store', 'CategoryController::store', ['filter' => 'permission:categories.create']);
            $routes->get('edit/(:num)', 'CategoryController::edit/$1', ['filter' => 'permission:categories.edit']);
            $routes->post('update/(:num)', 'CategoryController::update/$1', ['filter' => 'permission:categories.edit']);
            $routes->post('delete/(:num)', 'CategoryController::delete/$1', ['filter' => 'permission:categories.delete']);
        });

        });
        // Log Maintenance
        $routes->group('maintenance-logs', static function ($routes) {
            $routes->get('/', 'MaintenanceLogController::index', ['filter' => 'permission:logs.list']);
            $routes->get('data', 'MaintenanceLogController::data', ['filter' => 'permission:logs.list']);
            $routes->get('create', 'MaintenanceLogController::create', ['filter' => 'permission:logs.create']);
            $routes->post('store', 'MaintenanceLogController::store', ['filter' => 'permission:logs.create']);
            $routes->get('show/(:num)', 'MaintenanceLogController::show/$1', ['filter' => 'permission:logs.list']);
            $routes->get('attachment/(:num)', 'MaintenanceLogController::attachment/$1', ['filter' => 'permission:logs.list']);
            $routes->get('edit/(:num)', 'MaintenanceLogController::edit/$1', ['filter' => 'permission:logs.edit']);
            $routes->post('update/(:num)', 'MaintenanceLogController::update/$1', ['filter' => 'permission:logs.edit']);
            $routes->post('update-status/(:num)', 'MaintenanceLogController::updateStatus/$1', ['filter' => 'permission:logs.review']);
            $routes->post('close-ticket/(:num)', 'MaintenanceLogController::closeTicket/$1', ['filter' => 'permission:logs.review']);
            $routes->post('reopen-ticket/(:num)', 'MaintenanceLogController::reopenTicket/$1', ['filter' => 'permission:logs.review']);
            $routes->post('delete/(:num)', 'MaintenanceLogController::delete/$1', ['filter' => 'permission:logs.delete']);
        });

        // Log Kerjaan Harian (To-Do)
        $routes->group('daily-work-logs', static function ($routes) {
            $routes->get('/', 'DailyWorkLogController::index');
            $routes->post('store', 'DailyWorkLogController::store');
            $routes->post('toggle/(:num)', 'DailyWorkLogController::toggle/$1');
            $routes->post('reorder', 'DailyWorkLogController::reorder');
            $routes->post('delete/(:num)', 'DailyWorkLogController::delete/$1');
        });
});
