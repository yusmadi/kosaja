<?php

namespace App\Models;

use CodeIgniter\Model;

class ProviderProfileModel extends Model
{
    protected $table            = 'provider_profiles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'user_id',
        'verification_status',
        'rejection_reason',
        'id_card_photo',
        'bank_name',
        'bank_account_number',
        'free_period_end',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'user_id'             => 'required|is_not_unique[users.id]',
        'verification_status' => 'required|in_list[pending,verified,rejected,suspended]',
        'bank_name'           => 'permit_empty|max_length[100]',
        'bank_account_number' => 'permit_empty|max_length[100]',
    ];

    /**
     * Get profile by user_id
     */
    public function getByUserId(int $userId)
    {
        return $this->where('user_id', $userId)->first();
    }
}
