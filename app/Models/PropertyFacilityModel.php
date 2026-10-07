<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyFacilityModel extends Model
{
    protected $table            = 'property_facilities';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'property_id',
        'facility_name',
        'icon',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    public function getFacilitiesByProperty(int $propertyId)
    {
        return $this->where('property_id', $propertyId)->findAll();
    }

    /**
     * Synchronize facilities for a property
     */
    public function syncFacilities(int $propertyId, array $facilities)
    {
        // Remove existing facilities
        $this->where('property_id', $propertyId)->delete();

        // Standard icon mapping
        $iconMap = [
            'wifi'             => 'bi-wifi',
            'internet'         => 'bi-wifi',
            'ac'               => 'bi-snow',
            'air conditioner'  => 'bi-snow',
            'dapur'            => 'bi-cup-hot',
            'parkir'           => 'bi-car-front',
            'parkir motor'     => 'bi-bicycle',
            'cctv'             => 'bi-shield-check',
            'keamanan'         => 'bi-shield-lock',
            'mushola'          => 'bi-building',
            'kamar mandi dalam'=> 'bi-droplet-half',
            'kasur'            => 'bi-lamp',
            'lemari'           => 'bi-inbox',
            'listrik'          => 'bi-lightning',
            'mesin cuci'       => 'bi-water',
        ];

        $insertBatch = [];
        $now = date('Y-m-d H:i:s');

        foreach ($facilities as $facility) {
            $name = trim($facility);
            if (empty($name)) {
                continue;
            }

            $lower = strtolower($name);
            $icon = 'bi-check-circle';
            foreach ($iconMap as $key => $iconClass) {
                if (str_contains($lower, $key)) {
                    $icon = $iconClass;
                    break;
                }
            }

            $insertBatch[] = [
                'property_id'   => $propertyId,
                'facility_name' => $name,
                'icon'          => $icon,
                'created_at'    => $now,
            ];
        }

        if (!empty($insertBatch)) {
            $this->insertBatch($insertBatch);
        }
    }
}
