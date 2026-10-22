<?php

namespace App\Models;

use CodeIgniter\Model;

class ProviderPaymentModel extends Model
{
    protected $table            = 'provider_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'invoice_id',
        'amount_paid',
        'gateway_trx_id',
        'payment_method',
        'status',
        'paid_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get payments by invoice ID
     */
    public function getByInvoiceId(int $invoiceId): array
    {
        return $this->where('invoice_id', $invoiceId)
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}
