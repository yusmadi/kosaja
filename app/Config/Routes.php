<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// Public Routes (Tenant & General Discovery)
$routes->get('/', 'Home::index');
$routes->get('search', 'Listing::search');
$routes->get('listing/(:num)', 'Listing::show/$1');
$routes->get('listings/(:num)', 'Listing::show/$1');

// Tenant Favorites
$routes->get('favorites', 'Favorites::index');
$routes->post('favorites/toggle/(:num)', 'Favorites::toggle/$1');
$routes->post('favorites/remove/(:num)', 'Favorites::remove/$1');

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

$routes->get('switch-role/(:segment)', 'Auth::switchRole/$1');

// Admin Backoffice Common Routes (Guarded by RBAC: super_admin, admin_daerah)
$routes->group('admin', ['filter' => 'role:super_admin,admin_daerah'], static function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Provider Verification (KYC - Scoped for Admin Daerah, Global for Super Admin)
    $routes->get('providers', 'Admin\Providers::index');
    $routes->post('providers/verify/(:num)', 'Admin\Providers::verify/$1');
    $routes->post('providers/reject/(:num)', 'Admin\Providers::reject/$1');
    $routes->post('providers/suspend/(:num)', 'Admin\Providers::suspend/$1');

    // Listing Moderation (Scoped for Admin Daerah, Global for Super Admin)
    $routes->get('listings', 'Admin\Listings::index');
    $routes->get('listings/(:num)', 'Admin\Listings::show/$1');
    $routes->post('listings/approve/(:num)', 'Admin\Listings::approve/$1');
    $routes->post('listings/reject/(:num)', 'Admin\Listings::reject/$1');
    $routes->post('listings/toggle/(:num)', 'Admin\Listings::toggle/$1');

    // Dispute Management & Refund Handling
    $routes->get('disputes', 'Admin\Disputes::index');
    $routes->get('disputes/(:num)', 'Admin\Disputes::show/$1');
    $routes->post('disputes/investigate/(:num)', 'Admin\Disputes::investigate/$1');
    $routes->post('disputes/decide/(:num)', 'Admin\Disputes::decide/$1');
    $routes->post('disputes/escalate/(:num)', 'Admin\Disputes::escalate/$1');
    $routes->get('refunds', 'Admin\Disputes::refunds');

    // Immutable Audit Trail Viewer
    $routes->get('audit', 'Admin\AuditTrail::index');

    // Admin Daerah Dedicated Features (ADM-06, ADM-10, ADM-11)
    $routes->get('reports', 'Admin\Reports::index');
    $routes->get('reports/(:num)', 'Admin\Reports::show/$1');
    $routes->post('reports/investigate/(:num)', 'Admin\Reports::investigate/$1');
    $routes->post('reports/resolve/(:num)', 'Admin\Reports::resolve/$1');

    $routes->get('restrictions', 'Admin\Restrictions::index');
    $routes->post('restrictions/apply', 'Admin\Restrictions::apply');
    $routes->get('restrictions/lift/(:num)', 'Admin\Restrictions::lift/$1');

    $routes->get('regional-reports', 'Admin\RegionalReports::index');

    // Provider Payout Settlements (Maker-Checker: Admin Daerah verifies/recommends, Super Admin authorizes)
    $routes->get('settlements', 'Admin\Settlements::index');
    $routes->post('settlements/verify/(:num)', 'Admin\Settlements::verify/$1');
    $routes->post('settlements/hold/(:num)', 'Admin\Settlements::hold/$1');
});

// Super Admin Exclusive Backoffice Routes (Guarded by RBAC: super_admin only)
$routes->group('admin', ['filter' => 'role:super_admin'], static function ($routes) {
    // IAM & User Management (SA-02)
    $routes->get('users', 'Admin\Users::index');
    $routes->post('users/create', 'Admin\Users::create');
    $routes->get('users/toggle/(:num)', 'Admin\Users::toggleStatus/$1');
    $routes->post('users/assign-region/(:num)', 'Admin\Users::assignRegion/$1');

    // Region Hierarchy Management (SA-04)
    $routes->get('regions', 'Admin\Regions::index');
    $routes->post('regions/create', 'Admin\Regions::create');
    $routes->get('regions/delete/(:num)', 'Admin\Regions::delete/$1');

    // Global Financial Transactions Registry (SA-06)
    $routes->get('transactions', 'Admin\Transactions::index');

    // Provider Payout Settlements Final Authorization & Execution (SA-07 - Checker)
    $routes->post('settlements/process/(:num)', 'Admin\Settlements::process/$1');

    // Platform Monetization & Pricing Policies (SA-10)
    $routes->get('monetization', 'Admin\Monetization::index');
    $routes->post('monetization/update', 'Admin\Monetization::update');

    // Platform System Configuration & SLA (SA-11)
    $routes->get('configuration', 'Admin\Configuration::index');
    $routes->post('configuration/update', 'Admin\Configuration::update');
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

    // Rental Applications Review & Decision (Sprint 5)
    $routes->get('applications', 'Provider\Applications::index');
    $routes->get('applications/(:num)', 'Provider\Applications::show/$1');
    $routes->post('applications/approve/(:num)', 'Provider\Applications::approve/$1');
    $routes->post('applications/reject/(:num)', 'Provider\Applications::reject/$1');
    $routes->get('reservations', 'Provider\Applications::reservations');

    // Contracts Management (Sprint 6)
    $routes->get('contracts', 'Provider\Contracts::index');
    $routes->get('contracts/(:num)', 'Provider\Contracts::show/$1');

    // Subscription & Listing Fee Management (Modul Billing)
    $routes->get('subscription', 'Provider\Subscription::index');
    $routes->get('subscription/checkout/(:num)', 'Provider\Subscription::checkout/$1');
    $routes->post('subscription/checkout/(:num)', 'Provider\Subscription::checkout/$1');
    $routes->get('subscription/simulate/(:num)', 'Provider\Subscription::simulate/$1');
    $routes->post('subscription/simulate-execute/(:num)', 'Provider\Subscription::executeSimulation/$1');
    $routes->post('subscription/cancel/(:num)', 'Provider\Subscription::cancelInvoice/$1');
    $routes->post('subscription/delete/(:num)', 'Provider\Subscription::cancelInvoice/$1');
    $routes->get('subscription/invoice/(:num)', 'Provider\Subscription::invoiceReceipt/$1');
});

// Payment Gateway Webhook (Public POST endpoint)
$routes->post('webhook/payment', 'Webhook::payment');

// Tenant Rental Application, Booking, Billing & Contract Routes (Guarded by auth filter)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    // Tenant Dashboard & Invoices (Sprint 7)
    $routes->get('tenant/dashboard', 'Tenant\Dashboard::index');
    $routes->post('tenant/invoices/pay/(:num)', 'Tenant\Dashboard::payInvoice/$1');
    $routes->post('tenant/disputes/create', 'Tenant\Dashboard::createDispute');

    // Role-based dashboard redirect helper
    $routes->get('dashboard', static function() {
        $role = session()->get('role');
        if ($role === 'provider') return redirect()->to(base_url('/provider/dashboard'));
        if (in_array($role, ['super_admin', 'admin_daerah'])) return redirect()->to(base_url('/admin/dashboard'));
        return redirect()->to(base_url('/tenant/dashboard'));
    });

    // Rental Application Form & Actions
    $routes->get('apply/(:num)', 'Application::create/$1');
    $routes->post('apply/(:num)', 'Application::store/$1');

    // Tenant Applications Tracking
    $routes->get('applications', 'Application::index');
    $routes->get('applications/(:num)', 'Application::show/$1');
    $routes->post('applications/cancel/(:num)', 'Application::cancel/$1');

    // Reservation Checkout & Pricing Engine
    $routes->get('checkout/(:num)', 'Checkout::show/$1');
    $routes->post('checkout/process/(:num)', 'Checkout::process/$1');
    $routes->get('checkout/simulation/(:num)', 'Checkout::simulation/$1');
    $routes->post('checkout/simulation-execute/(:num)', 'Checkout::executeSimulation/$1');
    $routes->get('reservations/(:num)', 'Checkout::show/$1');

    // Digital Rental Contracts (Sprint 6)
    $routes->get('contracts', 'Contract::index');
    $routes->get('contracts/(:num)', 'Contract::show/$1');
});




