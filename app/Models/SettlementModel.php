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
        'provider_id',
        'contract_id',
        'gross_amount',
        'platform_fee',
        'net_amount',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'status',
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
     * Get settlements with provider & contract details
     */
    public function getSettlementsWithDetails(?string $status = null)
    {
        $builder = $this->db->table('settlements')
            ->select('settlements.*, 
                      providers.name as provider_name, providers.email as provider_email, providers.whatsapp as provider_whatsapp,
                      contracts.id as contract_id_ref,
                      processors.name as processor_name')
            ->join('users as providers', 'providers.id = settlements.provider_id', 'left')
            ->join('contracts', 'contracts.id = settlements.contract_id', 'left')
            ->join('users as processors', 'processors.id = settlements.processed_by_id', 'left')
            ->orderBy('settlements.id', 'DESC');

        if (! empty($status)) {
            $builder->where('settlements.status', $status);
        }

        return $builder->get()->getResultArray();
    }
}
