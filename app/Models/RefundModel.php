<?php

namespace App\Models;

use CodeIgniter\Model;

class RefundModel extends Model
{
    protected $table            = 'refunds';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'dispute_id',
        'payment_id',
        'invoice_id',
        'amount',
        'refund_type',
        'status',
        'reason',
        'processed_by_id',
        'processed_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'payment_id'      => 'required|is_not_unique[payments.id]',
        'invoice_id'      => 'required|is_not_unique[invoices.id]',
        'amount'          => 'required|numeric',
        'refund_type'     => 'required|in_list[full,partial]',
        'status'          => 'required|in_list[pending,completed,failed]',
        'reason'          => 'required|min_length[5]',
        'processed_by_id' => 'required|is_not_unique[users.id]',
    ];

    /**
     * Get refunds with details
     */
    public function getRefundsWithDetails()
    {
        return $this->select('refunds.*, 
                payments.amount_paid, payments.payment_method, payments.gateway_trx_id,
                invoices.tenant_id,
                tenants.name as tenant_name, tenants.email as tenant_email,
                processors.name as processed_by_name')
            ->join('payments', 'payments.id = refunds.payment_id')
            ->join('invoices', 'invoices.id = refunds.invoice_id')
            ->join('users as tenants', 'tenants.id = invoices.tenant_id')
            ->join('users as processors', 'processors.id = refunds.processed_by_id')
            ->orderBy('refunds.id', 'DESC')
            ->findAll();
    }
}
