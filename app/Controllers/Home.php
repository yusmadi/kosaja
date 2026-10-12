<?php

namespace App\Controllers;

use App\Models\FavoriteModel;

class Home extends BaseController
{
    public function index(): string
    {
        $db = \Config\Database::connect();
        
        // Load published listings with related property, unit, region, and primary photo
        $listings = $db->table('listings')
            ->select('listings.*, units.name as unit_name, units.size as unit_size, properties.name as property_name, properties.type, properties.target_gender, properties.address, regions.name as region_name, property_photos.photo_url')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->join('property_photos', 'property_photos.property_id = properties.id AND property_photos.is_primary = 1', 'left')
            ->where('listings.publication_status', 'published')
            ->orderBy('listings.id', 'DESC')
            ->get()
            ->getResultArray();

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
