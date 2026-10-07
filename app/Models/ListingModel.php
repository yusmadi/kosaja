<?php

namespace App\Models;

use CodeIgniter\Model;

class ListingModel extends Model
{
    protected $table            = 'listings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'unit_id',
        'title',
        'description',
        'price_monthly',
        'price_yearly',
        'publication_status',
        'rejection_reason',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'unit_id'            => 'required|is_not_unique[units.id]',
        'title'              => 'required|min_length[5]|max_length[255]',
        'description'        => 'required|min_length[10]',
        'publication_status' => 'required|in_list[draft,pending_review,published,inactive]',
        'price_monthly'      => 'permit_empty|numeric',
        'price_yearly'       => 'permit_empty|numeric',
    ];

    protected $validationMessages = [
        'title' => [
            'required' => 'Judul iklan listing wajib diisi.',
            'min_length' => 'Judul iklan minimal 5 karakter.',
        ],
        'description' => [
            'required' => 'Deskripsi listing wajib diisi.',
            'min_length' => 'Deskripsi listing minimal 10 karakter.',
        ],
    ];

    /**
     * Get listings belonging to a provider
     */
    public function getListingsByProvider(int $providerId)
    {
        return $this->select('listings.*, units.name as unit_name, units.size as unit_size, units.status as unit_status, properties.id as property_id, properties.name as property_name, properties.target_gender, properties.type as property_type, properties.address,
            (SELECT photo_url FROM unit_photos WHERE unit_photos.unit_id = units.id ORDER BY is_primary DESC, id ASC LIMIT 1) as unit_photo,
            (SELECT photo_url FROM property_photos WHERE property_photos.property_id = properties.id ORDER BY is_primary DESC, id ASC LIMIT 1) as property_photo')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->where('properties.provider_id', $providerId)
            ->orderBy('listings.id', 'DESC')
            ->findAll();
    }

    /**
     * Get listing with unit and property details
     */
    public function getListingWithDetails(int $listingId, ?int $providerId = null)
    {
        $db = \Config\Database::connect();

        $builder = $this->select('listings.*, units.name as unit_name, units.size as unit_size, units.floor as unit_floor, units.status as unit_status, properties.id as property_id, properties.name as property_name, properties.provider_id, properties.target_gender, properties.type as property_type, properties.address, properties.description as property_description, properties.rules as property_rules, regions.name as region_name')
                        ->join('units', 'units.id = listings.unit_id')
                        ->join('properties', 'properties.id = units.property_id')
                        ->join('regions', 'regions.id = properties.region_id', 'left')
                        ->where('listings.id', $listingId);

        if ($providerId !== null) {
            $builder->where('properties.provider_id', $providerId);
        }

        $listing = $builder->first();

        if (! $listing) {
            return null;
        }

        // Attach photos from unit and property
        $listing['unit_photos'] = $db->table('unit_photos')
                                    ->where('unit_id', $listing['unit_id'])
                                    ->orderBy('is_primary', 'DESC')
                                    ->get()
                                    ->getResultArray();

        $listing['property_photos'] = $db->table('property_photos')
                                        ->where('property_id', $listing['property_id'])
                                        ->orderBy('is_primary', 'DESC')
                                        ->get()
                                        ->getResultArray();

        $listing['facilities'] = $db->table('property_facilities')
                                    ->where('property_id', $listing['property_id'])
                                    ->get()
                                    ->getResultArray();

        return $listing;
    }

    /**
     * Check if a unit already has a listing
     */
    public function getListingByUnitId(int $unitId)
    {
        return $this->where('unit_id', $unitId)->first();
    }
}
