<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'invoice_id',
        'amount_paid',
        'gateway_trx_id',
        'payment_method',
        'status',
        'paid_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'invoice_id'     => 'required|is_not_unique[invoices.id]',
        'amount_paid'    => 'required|numeric',
        'status'         => 'required|in_list[pending,success,failed,refunded]',
    ];

    /**
     * Get payments by invoice ID
     */
    public function getPaymentsByInvoice(int $invoiceId)
    {
        return $this->where('invoice_id', $invoiceId)
                    ->orderBy('id', 'DESC')
                    ->findAll();
    }
}
