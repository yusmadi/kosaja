<?php

namespace App\Models;

use CodeIgniter\Model;

class DisputeModel extends Model
{
    protected $table            = 'disputes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'contract_id',
        'invoice_id',
        'raised_by_id',
        'against_user_id',
        'title',
        'reason',
        'evidence_url',
        'status',
        'is_escalated',
        'escalation_notes',
        'escalated_at',
        'decision',
        'decision_notes',
        'decided_by_id',
        'decided_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'contract_id'     => 'required|is_not_unique[contracts.id]',
        'raised_by_id'    => 'required|is_not_unique[users.id]',
        'against_user_id' => 'required|is_not_unique[users.id]',
        'title'           => 'required|min_length[5]|max_length[255]',
        'reason'          => 'required|min_length[10]',
        'status'          => 'required|in_list[opened,investigating,resolved,closed]',
        'decision'        => 'permit_empty|in_list[none,no_refund,partial_refund,full_refund]',
    ];

    /**
     * Get disputes list with comprehensive relation details
     */
    public function getDisputesWithDetails(?string $status = null, ?int $regionId = null, ?bool $onlyEscalated = null)
    {
        $builder = $this->select('disputes.*, 
                contracts.start_date, contracts.end_date, contracts.agreed_price,
                properties.name as property_name, properties.id as property_id, properties.region_id,
                regions.name as region_name,
                units.name as unit_name,
                tenants.name as raised_by_name, tenants.email as raised_by_email, tenants.whatsapp as raised_by_whatsapp,
                providers.name as against_user_name, providers.email as against_user_email, providers.whatsapp as against_user_whatsapp,
                deciders.name as decided_by_name')
            ->join('contracts', 'contracts.id = disputes.contract_id')
            ->join('properties', 'properties.id = contracts.property_id')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->join('units', 'units.id = contracts.unit_id')
            ->join('users as tenants', 'tenants.id = disputes.raised_by_id')
            ->join('users as providers', 'providers.id = disputes.against_user_id')
            ->join('users as deciders', 'deciders.id = disputes.decided_by_id', 'left');

        if (! empty($status)) {
            $builder->where('disputes.status', $status);
        }

        if ($onlyEscalated === true) {
            $builder->where('disputes.is_escalated', 1);
        }

        if (! empty($regionId)) {
            $db = \Config\Database::connect();
            $children = $db->table('regions')->where('parent_id', $regionId)->get()->getResultArray();
            $targetRegionIds = [$regionId];
            foreach ($children as $c) {
                $targetRegionIds[] = (int) $c['id'];
            }
            $builder->whereIn('properties.region_id', $targetRegionIds);
        }

        return $builder->orderBy('disputes.id', 'DESC')->findAll();
    }

    /**
     * Get single dispute with complete details and evidence
     */
    public function getDisputeWithDetails(int $id)
    {
        return $this->select('disputes.*, 
                contracts.start_date, contracts.end_date, contracts.agreed_price, contracts.payment_schedule,
                invoices.rental_amount, invoices.gateway_fee, invoices.total_amount as invoice_total, invoices.status as invoice_status,
                properties.name as property_name, properties.address as property_address,
                units.name as unit_name,
                tenants.name as raised_by_name, tenants.email as raised_by_email, tenants.whatsapp as raised_by_whatsapp,
                providers.name as against_user_name, providers.email as against_user_email, providers.whatsapp as against_user_whatsapp,
                deciders.name as decided_by_name')
            ->join('contracts', 'contracts.id = disputes.contract_id')
            ->join('invoices', 'invoices.id = disputes.invoice_id', 'left')
            ->join('properties', 'properties.id = contracts.property_id')
            ->join('units', 'units.id = contracts.unit_id')
            ->join('users as tenants', 'tenants.id = disputes.raised_by_id')
            ->join('users as providers', 'providers.id = disputes.against_user_id')
            ->join('users as deciders', 'deciders.id = disputes.decided_by_id', 'left')
            ->where('disputes.id', $id)
            ->first();
    }

    /**
     * Get disputes raised by a tenant
     */
    public function getDisputesByTenant(int $tenantId)
    {
        return $this->select('disputes.*, properties.name as property_name, units.name as unit_name')
            ->join('contracts', 'contracts.id = disputes.contract_id')
            ->join('properties', 'properties.id = contracts.property_id')
            ->join('units', 'units.id = contracts.unit_id')
            ->where('disputes.raised_by_id', $tenantId)
            ->orderBy('disputes.id', 'DESC')
            ->findAll();
    }
}
