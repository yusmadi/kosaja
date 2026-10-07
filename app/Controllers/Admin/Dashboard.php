<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $stats = [
            'total_users'        => $db->table('users')->countAllResults(),
            'total_providers'    => $db->table('users')->where('role', 'provider')->countAllResults(),
            'total_properties'   => $db->table('properties')->countAllResults(),
            'published_listings' => $db->table('listings')->where('publication_status', 'published')->countAllResults(),
        ];

        // Providers data
        $providers = $db->table('users')
            ->select('users.*, provider_profiles.verification_status, provider_profiles.bank_name, provider_profiles.bank_account_number')
            ->join('provider_profiles', 'provider_profiles.user_id = users.id', 'left')
            ->where('users.role', 'provider')
            ->get()
            ->getResultArray();

        // Listings data
        $listings = $db->table('listings')
            ->select('listings.*, properties.name as property_name, properties.target_gender')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->get()
            ->getResultArray();

        // Regions
        $regions = $db->table('regions')->orderBy('id', 'ASC')->get()->getResultArray();

        return view('admin/dashboard', [
            'title'      => 'Admin Backoffice Overview',
            'role'       => 'admin',
            'activeMenu' => 'dashboard',
            'stats'      => $stats,
            'providers'  => $providers,
            'listings'   => $listings,
            'regions'    => $regions,
        ]);
    }
}
