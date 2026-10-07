<?php

namespace App\Controllers\Provider;

use App\Controllers\BaseController;
use App\Models\ProviderProfileModel;
use App\Models\PropertyModel;
use App\Models\UnitModel;
use App\Models\ListingModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $providerId = session()->get('user_id') ?? 3;

        $profileModel = new ProviderProfileModel();
        $profile = $profileModel->getByUserId($providerId);

        $stats = [
            'my_properties'   => $db->table('properties')->where('provider_id', $providerId)->countAllResults(),
            'total_units'     => $db->table('units')
                                    ->join('properties', 'properties.id = units.property_id')
                                    ->where('properties.provider_id', $providerId)
                                    ->countAllResults(),
            'available_units' => $db->table('units')
                                    ->join('properties', 'properties.id = units.property_id')
                                    ->where('properties.provider_id', $providerId)
                                    ->where('units.status', 'available')
                                    ->countAllResults(),
            'total_listings'  => $db->table('listings')
                                    ->join('units', 'units.id = listings.unit_id')
                                    ->join('properties', 'properties.id = units.property_id')
                                    ->where('properties.provider_id', $providerId)
                                    ->countAllResults(),
        ];

        // Recent units
        $units = $db->table('units')
            ->select('units.*, properties.name as property_name, properties.target_gender, listings.id as listing_id, listings.publication_status')
            ->join('properties', 'properties.id = units.property_id')
            ->join('listings', 'listings.unit_id = units.id', 'left')
            ->where('properties.provider_id', $providerId)
            ->limit(10)
            ->get()
            ->getResultArray();

        // Recent properties
        $properties = $db->table('properties')
            ->select('properties.*, regions.name as region_name,
                (SELECT COUNT(*) FROM units WHERE units.property_id = properties.id) as total_units,
                (SELECT photo_url FROM property_photos WHERE property_photos.property_id = properties.id ORDER BY is_primary DESC, id ASC LIMIT 1) as primary_photo')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->where('properties.provider_id', $providerId)
            ->limit(5)
            ->get()
            ->getResultArray();

        return view('provider/dashboard', [
            'title'      => 'Mitra Provider Dashboard — KosAja',
            'role'       => 'provider',
            'activeMenu' => 'dashboard',
            'stats'      => $stats,
            'units'      => $units,
            'properties' => $properties,
            'profile'    => $profile,
        ]);
    }
}
