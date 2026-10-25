<?php

namespace App\Controllers;

use App\Models\FavoriteModel;

class Home extends BaseController
{
    public function index(): string
    {
        $db = \Config\Database::connect();
        
        // Load published listings with related property, unit, region, property exterior photo, and unit inside photo
        $listings = $db->table('listings')
            ->select('listings.*, units.name as unit_name, units.size as unit_size, properties.id as property_id, properties.name as property_name, properties.type, properties.target_gender, properties.address, regions.name as region_name,
                (SELECT photo_url FROM property_photos WHERE property_photos.property_id = properties.id ORDER BY is_primary DESC, id ASC LIMIT 1) as property_photo,
                (SELECT photo_url FROM unit_photos WHERE unit_photos.unit_id = units.id ORDER BY is_primary DESC, id ASC LIMIT 1) as unit_photo')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->where('listings.publication_status', 'published')
            ->orderBy('listings.id', 'DESC')
            ->get()
            ->getResultArray();

        foreach ($listings as &$item) {
            // Default photo_url for compatibility
            $item['photo_url'] = !empty($item['property_photo']) 
                ? $item['property_photo'] 
                : (!empty($item['unit_photo']) ? $item['unit_photo'] : 'assets/images/exterior-kos-1.jpg');
            
            if (empty($item['property_photo'])) {
                $item['property_photo'] = $item['photo_url'];
            }
            if (empty($item['unit_photo'])) {
                $item['unit_photo'] = 'assets/images/sample-kos-1.jpg';
            }
        }
        unset($item);

        $regions = $db->table('regions')->orderBy('name', 'ASC')->get()->getResultArray();

        $userFavorites = [];
        if (session()->get('isLoggedIn')) {
            $favoriteModel = new FavoriteModel();
            $userFavorites = $favoriteModel->getUserFavoriteListingIds((int)session()->get('user_id'));
        }

        return view('public/home', [
            'title'         => 'KosAja — Cari Kos, Kontrakan & Rumah Sewa Bebas Calo & Syariat Friendly',
            'listings'      => $listings,
            'regions'       => $regions,
            'userFavorites' => $userFavorites,
        ]);
    }
}
