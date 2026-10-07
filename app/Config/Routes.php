<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Public Routes (Tenant & General)
$routes->get('/', 'Home::index');

// Authentication Routes
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attemptLogin');
$routes->get('register', 'Auth::register');
$routes->post('register', 'Auth::attemptRegister');
$routes->get('logout', 'Auth::logout');

// User Profile Management (Guarded by auth filter)
$routes->group('profile', ['filter' => 'auth'], static function ($routes) {
    $routes->get('', 'Profile::index');
    $routes->post('update', 'Profile::update');
    $routes->post('update-password', 'Profile::updatePassword');
});

// Admin Backoffice Routes (Guarded by RBAC: super_admin, admin_daerah)
$routes->group('admin', ['filter' => 'role:super_admin,admin_daerah'], static function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');
    
    // IAM & User Management
    $routes->get('users', 'Admin\Users::index');
    $routes->get('users/toggle/(:num)', 'Admin\Users::toggleStatus/$1');

    // Region Hierarchy Management
    $routes->get('regions', 'Admin\Regions::index');
    $routes->post('regions/create', 'Admin\Regions::create');
    $routes->get('regions/delete/(:num)', 'Admin\Regions::delete/$1');

    // Provider Verification (KYC)
    $routes->get('providers', 'Admin\Providers::index');
    $routes->post('providers/verify/(:num)', 'Admin\Providers::verify/$1');
    $routes->post('providers/reject/(:num)', 'Admin\Providers::reject/$1');
    $routes->post('providers/suspend/(:num)', 'Admin\Providers::suspend/$1');
});

// Provider Dashboard & Inventory Routes (Guarded by RBAC: provider, super_admin)
$routes->group('provider', ['filter' => 'role:provider,super_admin'], static function ($routes) {
    $routes->get('dashboard', 'Provider\Dashboard::index');

    // Onboarding & Verification (KYC)
    $routes->get('verification', 'Provider\Onboarding::index');
    $routes->post('verification/update', 'Provider\Onboarding::update');

    // Properties Management
    $routes->get('properties', 'Provider\Properties::index');
    $routes->get('properties/create', 'Provider\Properties::create');
    $routes->post('properties/store', 'Provider\Properties::store');
    $routes->get('properties/(:num)', 'Provider\Properties::show/$1');
    $routes->get('properties/edit/(:num)', 'Provider\Properties::edit/$1');
    $routes->post('properties/update/(:num)', 'Provider\Properties::update/$1');
    $routes->post('properties/delete/(:num)', 'Provider\Properties::delete/$1');
    $routes->post('properties/photos/delete/(:num)', 'Provider\Properties::deletePhoto/$1');
    $routes->post('properties/photos/set-primary/(:num)/(:num)', 'Provider\Properties::setPrimaryPhoto/$1/$2');

    // Units Management
    $routes->get('units', 'Provider\Units::index');
    $routes->get('properties/(:num)/units/create', 'Provider\Units::create/$1');
    $routes->post('properties/(:num)/units/store', 'Provider\Units::store/$1');
    $routes->get('units/edit/(:num)', 'Provider\Units::edit/$1');
    $routes->post('units/update/(:num)', 'Provider\Units::update/$1');
    $routes->post('units/delete/(:num)', 'Provider\Units::delete/$1');
    $routes->post('units/photos/delete/(:num)', 'Provider\Units::deletePhoto/$1');

    // Individual Listings Management
    $routes->get('listings', 'Provider\Listings::index');
    $routes->get('listings/create', 'Provider\Listings::create');
    $routes->get('listings/create/(:num)', 'Provider\Listings::create/$1');
    $routes->post('listings/store', 'Provider\Listings::store');
    $routes->get('listings/edit/(:num)', 'Provider\Listings::edit/$1');
    $routes->post('listings/update/(:num)', 'Provider\Listings::update/$1');
    $routes->post('listings/submit-review/(:num)', 'Provider\Listings::submitReview/$1');
    $routes->post('listings/toggle-status/(:num)', 'Provider\Listings::toggleStatus/$1');
    $routes->post('listings/delete/(:num)', 'Provider\Listings::delete/$1');
});

