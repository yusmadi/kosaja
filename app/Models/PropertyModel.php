<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyModel extends Model
{
    protected $table            = 'properties';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'provider_id',
        'region_id',
        'name',
        'type',
        'target_gender',
        'description',
        'address',
        'latitude',
        'longitude',
        'rules',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'provider_id'   => 'required|is_not_unique[users.id]',
        'region_id'     => 'required|is_not_unique[regions.id]',
        'name'          => 'required|min_length[3]|max_length[255]',
        'type'          => 'required|in_list[kos,kontrakan,rumah_sewa,lainnya]',
        'target_gender' => 'required|in_list[putra,putri,campur_keluarga]',
        'address'       => 'required|min_length[5]',
    ];

    protected $validationMessages = [
        'name' => [
            'required' => 'Nama properti wajib diisi.',
            'min_length' => 'Nama properti minimal 3 karakter.',
        ],
        'region_id' => [
            'required' => 'Wilayah/Region operasional wajib dipilih.',
            'is_not_unique' => 'Wilayah yang dipilih tidak valid.',
        ],
        'target_gender' => [
            'required' => 'Ketentuan Syariat Hunian (Gender) wajib dipilih.',
            'in_list' => 'Pilihan target hunian harus Putra, Putri, atau Pasutri/Keluarga.',
        ],
        'address' => [
            'required' => 'Alamat lengkap properti wajib diisi.',
        ],
    ];

    /**
     * Get list of properties owned by provider with summary counts and primary photo
     */
    public function getPropertiesByProvider(int $providerId)
    {
        return $this->select('properties.*, regions.name as region_name, 
            (SELECT COUNT(*) FROM units WHERE units.property_id = properties.id) as total_units,
            (SELECT COUNT(*) FROM units WHERE units.property_id = properties.id AND units.status = "available") as available_units,
            (SELECT photo_url FROM property_photos WHERE property_photos.property_id = properties.id ORDER BY is_primary DESC, id ASC LIMIT 1) as primary_photo')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->where('properties.provider_id', $providerId)
            ->orderBy('properties.id', 'DESC')
            ->findAll();
    }

    /**
     * Get property with its facilities, photos, and units count
     */
    public function getPropertyWithDetails(int $propertyId, ?int $providerId = null)
    {
        $builder = $this->select('properties.*, regions.name as region_name, users.name as provider_name, users.whatsapp as provider_whatsapp')
                        ->join('regions', 'regions.id = properties.region_id', 'left')
                        ->join('users', 'users.id = properties.provider_id', 'left')
                        ->where('properties.id', $propertyId);

        if ($providerId !== null) {
            $builder->where('properties.provider_id', $providerId);
        }

        $property = $builder->first();

        if (! $property) {
            return null;
        }

        $db = \Config\Database::connect();

        // Attach facilities
        $property['facilities'] = $db->table('property_facilities')
                                     ->where('property_id', $propertyId)
                                     ->get()
                                     ->getResultArray();

        // Attach photos
        $property['photos'] = $db->table('property_photos')
                                 ->where('property_id', $propertyId)
                                 ->orderBy('is_primary', 'DESC')
                                 ->orderBy('id', 'ASC')
                                 ->get()
                                 ->getResultArray();

        // Attach units
        $property['units'] = $db->table('units')
                                ->select('units.*, listings.id as listing_id, listings.title as listing_title, listings.price_monthly, listings.publication_status')
                                ->join('listings', 'listings.unit_id = units.id', 'left')
                                ->where('units.property_id', $propertyId)
                                ->orderBy('units.id', 'ASC')
                                ->get()
                                ->getResultArray();

        return $property;
    }
}
