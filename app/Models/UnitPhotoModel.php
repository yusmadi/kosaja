<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitPhotoModel extends Model
{
    protected $table            = 'unit_photos';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'unit_id',
        'photo_url',
        'caption',
        'is_primary',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getPhotosByUnit(int $unitId)
    {
        return $this->where('unit_id', $unitId)
                    ->orderBy('is_primary', 'DESC')
                    ->orderBy('id', 'ASC')
                    ->findAll();
    }

    public function setPrimary(int $unitId, int $photoId)
    {
        $this->where('unit_id', $unitId)->set(['is_primary' => 0])->update();
        return $this->update($photoId, ['is_primary' => 1]);
    }
}
