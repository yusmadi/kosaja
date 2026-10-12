<?php

namespace App\Models;

use CodeIgniter\Model;

class InvoiceModel extends Model
{
    protected $table            = 'invoices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'contract_id',
        'tenant_id',
        'rental_amount',
        'gateway_fee',
        'total_amount',
        'due_date',
        'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'contract_id'   => 'required|is_not_unique[contracts.id]',
        'tenant_id'     => 'required|is_not_unique[users.id]',
        'rental_amount' => 'required|numeric',
        'gateway_fee'   => 'required|numeric',
        'total_amount'  => 'required|numeric',
        'due_date'      => 'required|valid_date',
        'status'        => 'required|in_list[upcoming,pending,paid,overdue,cancelled]',
    ];

    /**
     * Get invoices by contract ID
     */
    public function getInvoicesByContract(int $contractId)
    {
        return $this->where('contract_id', $contractId)
                    ->orderBy('due_date', 'ASC')
                    ->findAll();
    }

    /**
     * Get invoices with contract, property, and payment details
     */
    public function getInvoiceWithDetails(int $id)
    {
        return $this->select('invoices.*, 
                contracts.agreed_price,
                contracts.payment_schedule,
                contracts.start_date,
                contracts.end_date,
                properties.name as property_name,
                units.name as unit_name,
                tenants.name as tenant_name,
                tenants.email as tenant_email,
                tenants.whatsapp as tenant_whatsapp,
                providers.name as provider_name,
                providers.whatsapp as provider_whatsapp')
            ->join('contracts', 'contracts.id = invoices.contract_id')
            ->join('properties', 'properties.id = contracts.property_id')
            ->join('units', 'units.id = contracts.unit_id')
            ->join('users as tenants', 'tenants.id = invoices.tenant_id')
            ->join('users as providers', 'providers.id = contracts.provider_id')
            ->where('invoices.id', $id)
            ->first();
    }
}
