<?php

namespace App\Models;

use CodeIgniter\Model;

class SubscriptionPlanModel extends Model
{
    protected $table            = 'subscription_plans';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name',
        'slug',
        'type',
        'billing_cycle',
        'price',
        'duration_days',
        'listing_capacity',
        'description',
        'features',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get active plans grouped by type
     */
    public function getActivePlans(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('type', 'ASC')
            ->orderBy('price', 'ASC')
            ->findAll();
    }

    /**
     * Get plans by type (listing_fee or subscription)
     */
    public function getPlansByType(string $type): array
    {
        return $this->where('type', $type)
            ->where('is_active', 1)
            ->orderBy('price', 'ASC')
            ->findAll();
    }
}
