<?php

namespace App\Models;

use CodeIgniter\Model;

class RegionModel extends Model
{
    protected $table            = 'regions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'parent_id',
        'name',
        'level',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'  => 'required|min_length[2]|max_length[255]',
        'level' => 'required|in_list[province,city,operational]',
    ];

    /**
     * Get hierarchy with parent info
     */
    public function getHierarchy()
    {
        return $this->select('regions.*, p.name as parent_name')
                    ->join('regions p', 'p.id = regions.parent_id', 'left')
                    ->orderBy('regions.level', 'ASC')
                    ->orderBy('regions.id', 'ASC')
                    ->findAll();
    }
}
