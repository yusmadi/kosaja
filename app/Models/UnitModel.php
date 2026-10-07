<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitModel extends Model
{
    protected $table            = 'units';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'property_id',
        'name',
        'size',
        'floor',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'property_id' => 'required|is_not_unique[properties.id]',
        'name'        => 'required|min_length[1]|max_length[100]',
        'status'      => 'required|in_list[available,reserved,occupied,maintenance,inactive]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama atau nomor unit/kamar wajib diisi.',
        ],
        'status' => [
            'required' => 'Status unit wajib dipilih.',
            'in_list'  => 'Status unit harus salah satu dari: available, reserved, occupied, maintenance, inactive.',
        ],
    ];

    public function getUnitsByProperty(int $propertyId)
    {
        return $this->select('units.*, listings.id as listing_id, listings.title as listing_title, listings.price_monthly, listings.publication_status,
            (SELECT photo_url FROM unit_photos WHERE unit_photos.unit_id = units.id ORDER BY is_primary DESC, id ASC LIMIT 1) as primary_photo')
            ->join('listings', 'listings.unit_id = units.id', 'left')
            ->where('units.property_id', $propertyId)
            ->orderBy('units.id', 'ASC')
            ->findAll();
    }

    public function getUnitWithDetails(int $unitId)
    {
        $db = \Config\Database::connect();

        $unit = $this->select('units.*, properties.name as property_name, properties.provider_id, properties.target_gender, properties.address, properties.type as property_type, listings.id as listing_id, listings.title as listing_title, listings.description as listing_description, listings.price_monthly, listings.price_yearly, listings.publication_status, listings.rejection_reason')
                     ->join('properties', 'properties.id = units.property_id')
                     ->join('listings', 'listings.unit_id = units.id', 'left')
                     ->where('units.id', $unitId)
                     ->first();

        if (! $unit) {
            return null;
        }

        $unit['photos'] = $db->table('unit_photos')
                             ->where('unit_id', $unitId)
                             ->orderBy('is_primary', 'DESC')
                             ->orderBy('id', 'ASC')
                             ->get()
                             ->getResultArray();

        return $unit;
    }

    /**
     * Get all units owned by a provider
     */
    public function getUnitsByProvider(int $providerId)
    {
        return $this->select('units.*, properties.name as property_name, properties.target_gender, listings.id as listing_id, listings.publication_status, listings.price_monthly')
                    ->join('properties', 'properties.id = units.property_id')
                    ->join('listings', 'listings.unit_id = units.id', 'left')
                    ->where('properties.provider_id', $providerId)
                    ->orderBy('units.id', 'DESC')
                    ->findAll();
    }

    /**
     * Get units of a provider that don't have a listing yet
     */
    public function getUnitsWithoutListing(int $providerId)
    {
        return $this->select('units.*, properties.name as property_name, properties.target_gender')
                    ->join('properties', 'properties.id = units.property_id')
                    ->join('listings', 'listings.unit_id = units.id', 'left')
                    ->where('properties.provider_id', $providerId)
                    ->where('listings.id IS NULL')
                    ->orderBy('properties.name', 'ASC')
                    ->orderBy('units.name', 'ASC')
                    ->findAll();
    }
}
