<?php

namespace App\Models;

use CodeIgniter\Model;

class PropertyPhotoModel extends Model
{
    protected $table            = 'property_photos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'property_id',
        'photo_url',
        'caption',
        'is_primary',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPhotosByProperty(int $propertyId)
    {
        return $this->where('property_id', $propertyId)
                    ->orderBy('is_primary', 'DESC')
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    public function setPrimary(int $propertyId, int $photoId)
    {
        $this->where('property_id', $propertyId)->set(['is_primary' => 0])->update();
        return $this->update($photoId, ['is_primary' => 1]);
    }
}
