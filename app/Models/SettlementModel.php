<?php

namespace App\Models;

use CodeIgniter\Model;

class SettlementModel extends Model
{
    protected $table            = 'settlements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'region_id',
        'provider_id',
        'contract_id',
        'gross_amount',
        'platform_fee',
        'net_amount',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'status',
        'verified_by_id',
        'verified_at',
        'verification_notes',
        'rejection_reason',
        'payout_reference',
        'processed_by_id',
        'processed_at',
        'notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get settlements with provider, verifier (Maker), processor (Checker) & regional details
     */
    public function getSettlementsWithDetails(?string $status = null, ?array $scopedRegionIds = null)
    {
        $builder = $this->db->table('settlements')
            ->select('settlements.*, 
                      providers.name as provider_name, providers.email as provider_email, providers.whatsapp as provider_whatsapp,
                      contracts.id as contract_id_ref, contracts.start_date as rental_start_date,
                      properties.name as property_name,
                      COALESCE(regions.name, "Wilayah Umum") as region_name,
                      verifiers.name as verifier_name,
                      processors.name as processor_name')
            ->join('users as providers', 'providers.id = settlements.provider_id', 'left')
            ->join('contracts', 'contracts.id = settlements.contract_id', 'left')
            ->join('properties', 'properties.id = contracts.property_id', 'left')
            ->join('regions', 'regions.id = COALESCE(settlements.region_id, properties.region_id, providers.region_id)', 'left')
            ->join('users as verifiers', 'verifiers.id = settlements.verified_by_id', 'left')
            ->join('users as processors', 'processors.id = settlements.processed_by_id', 'left')
            ->orderBy('settlements.id', 'DESC');

        if (! empty($scopedRegionIds)) {
            $builder->groupStart()
                ->whereIn('settlements.region_id', $scopedRegionIds)
                ->orWhereIn('properties.region_id', $scopedRegionIds)
                ->orWhereIn('providers.region_id', $scopedRegionIds)
            ->groupEnd();
        }

        if (! empty($status)) {
            $builder->where('settlements.status', $status);
        }

        return $builder->get()->getResultArray();
    }
}
