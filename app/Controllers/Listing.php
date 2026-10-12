<?php

namespace App\Controllers;

use App\Models\ListingModel;
use App\Models\FavoriteModel;

class Listing extends BaseController
{
    protected $listingModel;
    protected $favoriteModel;

    public function __construct()
    {
        $this->listingModel = new ListingModel();
        $this->favoriteModel = new FavoriteModel();
    }

    /**
     * Search and discovery page with comprehensive dynamic filters
     */
    public function search()
    {
        $keyword      = trim($this->request->getGet('keyword') ?? $this->request->getGet('q') ?? '');
        $regionId     = $this->request->getGet('region_id') ?? '';
        $type         = $this->request->getGet('type') ?? '';
        $targetGender = $this->request->getGet('target_gender') ?? $this->request->getGet('gender') ?? '';
        $minPrice     = $this->request->getGet('min_price') ?? '';
        $maxPrice     = $this->request->getGet('max_price') ?? '';
        $sort         = $this->request->getGet('sort') ?? 'newest';

        $db = \Config\Database::connect();
        $builder = $db->table('listings')
            ->select('listings.*, 
                units.name as unit_name, units.size as unit_size, units.floor as unit_floor, units.status as unit_status,
                properties.id as property_id, properties.name as property_name, properties.type, properties.target_gender, properties.address,
                regions.name as region_name,
                property_photos.photo_url')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->join('property_photos', 'property_photos.property_id = properties.id AND property_photos.is_primary = 1', 'left')
            ->where('listings.publication_status', 'published');

        // Keyword filter: searches title, description, property name, address, region name
        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('listings.title', $keyword)
                ->orLike('listings.description', $keyword)
                ->orLike('properties.name', $keyword)
                ->orLike('properties.address', $keyword)
                ->orLike('regions.name', $keyword)
                ->groupEnd();
        }

        // Region filter
        if (!empty($regionId) && is_numeric($regionId)) {
            $builder->where('properties.region_id', (int)$regionId);
        }

        // Property type filter
        if (!empty($type) && in_array($type, ['kos', 'kontrakan', 'rumah_sewa'])) {
            $builder->where('properties.type', $type);
        }

        // Target gender filter (Syariat conformity)
        if (!empty($targetGender) && in_array($targetGender, ['putra', 'putri', 'campur_keluarga'])) {
            $builder->where('properties.target_gender', $targetGender);
        }

        // Price range filters
        if (!empty($minPrice) && is_numeric($minPrice)) {
            $builder->where('listings.price_monthly >=', (float)$minPrice);
        }
        if (!empty($maxPrice) && is_numeric($maxPrice)) {
            $builder->where('listings.price_monthly <=', (float)$maxPrice);
        }

        // Sorting
        switch ($sort) {
            case 'price_asc':
                $builder->orderBy('listings.price_monthly', 'ASC');
                break;
            case 'price_desc':
                $builder->orderBy('listings.price_monthly', 'DESC');
                break;
            case 'oldest':
                $builder->orderBy('listings.id', 'ASC');
                break;
            case 'newest':
            default:
                $builder->orderBy('listings.id', 'DESC');
                break;
        }

        $listings = $builder->get()->getResultArray();
        $regions  = $db->table('regions')->orderBy('name', 'ASC')->get()->getResultArray();

        // Check user favorites
        $userFavorites = [];
        if (session()->get('isLoggedIn')) {
            $userFavorites = $this->favoriteModel->getUserFavoriteListingIds((int)session()->get('user_id'));
        }

        return view('public/search', [
            'title'         => 'Cari Kos & Kontrakan — Hasil Pencarian KosAja',
            'listings'      => $listings,
            'regions'       => $regions,
            'filters'       => [
                'keyword'       => $keyword,
                'region_id'     => $regionId,
                'type'          => $type,
                'target_gender' => $targetGender,
                'min_price'     => $minPrice,
                'max_price'     => $maxPrice,
                'sort'          => $sort,
            ],
            'userFavorites' => $userFavorites,
        ]);
    }

    /**
     * Detail page for a specific listing
     */
    public function show(int $id)
    {
        $db = \Config\Database::connect();

        $listing = $db->table('listings')
            ->select('listings.*, 
                units.id as unit_id, units.name as unit_name, units.size as unit_size, units.floor as unit_floor, units.status as unit_status,
                properties.id as property_id, properties.name as property_name, properties.type, properties.target_gender, properties.description as property_description, properties.address, properties.rules, properties.latitude, properties.longitude,
                regions.name as region_name,
                users.id as provider_id, users.name as provider_name, users.whatsapp as provider_whatsapp, users.created_at as provider_joined,
                provider_profiles.verification_status as provider_kyc')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('users', 'users.id = properties.provider_id')
            ->join('provider_profiles', 'provider_profiles.user_id = users.id', 'left')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->where('listings.id', $id)
            ->get()
            ->getRowArray();

        if (!$listing) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Listing properti tidak ditemukan.');
        }

        // Access control for draft/inactive listings
        $userId   = session()->get('user_id');
        $userRole = session()->get('role');
        $isOwner  = ($userId && $listing['provider_id'] == $userId);
        $isAdmin  = in_array($userRole, ['super_admin', 'admin_daerah']);

        if ($listing['publication_status'] !== 'published' && !$isOwner && !$isAdmin) {
            return redirect()->to('/')->with('warning', 'Listing ini saat ini belum dipublikasikan atau sedang dalam proses verifikasi.');
        }

        // Property Photos
        $propertyPhotos = $db->table('property_photos')
            ->where('property_id', $listing['property_id'])
            ->orderBy('is_primary', 'DESC')
            ->get()
            ->getResultArray();

        // Unit Photos
        $unitPhotos = $db->table('unit_photos')
            ->where('unit_id', $listing['unit_id'])
            ->orderBy('is_primary', 'DESC')
            ->get()
            ->getResultArray();

        // Property Facilities
        $facilities = $db->table('property_facilities')
            ->where('property_id', $listing['property_id'])
            ->get()
            ->getResultArray();

        // Check if favorited by current user
        $isFavorited = false;
        if ($userId) {
            $isFavorited = $this->favoriteModel->isFavorited((int)$userId, $id);
        }

        // Similar Listings
        $similarListings = $db->table('listings')
            ->select('listings.*, units.name as unit_name, properties.name as property_name, properties.type, properties.target_gender, properties.address, regions.name as region_name, property_photos.photo_url')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->join('property_photos', 'property_photos.property_id = properties.id AND property_photos.is_primary = 1', 'left')
            ->where('listings.publication_status', 'published')
            ->where('listings.id !=', $id)
            ->where('properties.target_gender', $listing['target_gender'])
            ->limit(3)
            ->get()
            ->getResultArray();

        return view('public/listing_detail', [
            'title'           => esc($listing['title']) . ' — KosAja',
            'listing'         => $listing,
            'propertyPhotos'  => $propertyPhotos,
            'unitPhotos'      => $unitPhotos,
            'facilities'      => $facilities,
            'isFavorited'     => $isFavorited,
            'similarListings' => $similarListings,
            'isPreview'       => ($listing['publication_status'] !== 'published'),
        ]);
    }
}
