<?php

namespace App\Models;

use CodeIgniter\Model;

class ProviderInvoiceModel extends Model
{
    protected $table            = 'provider_invoices';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'invoice_number',
        'order_id',
        'provider_id',
        'plan_id',
        'subscription_id',
        'amount',
        'status',
        'payment_method',
        'snap_token',
        'due_date',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate unique human-readable invoice number
     */
    public function generateInvoiceNumber(): string
    {
        $prefix = 'INV-SUB-' . date('Ymd');
        $random = strtoupper(bin2hex(random_bytes(3)));

        return $prefix . '-' . $random;
    }

    /**
     * Generate unique order_id for Midtrans
     */
    public function generateOrderId(int $providerId, int $planId): string
    {
        return 'SUB-' . $providerId . '-' . $planId . '-' . date('ymdHis');
    }

    /**
     * Get invoice history for provider
     */
    public function getInvoicesByProvider(int $providerId): array
    {
        return $this->select('provider_invoices.*, subscription_plans.name as plan_name, subscription_plans.type as plan_type, subscription_plans.billing_cycle, subscription_plans.duration_days, subscription_plans.listing_capacity')
            ->join('subscription_plans', 'subscription_plans.id = provider_invoices.plan_id')
            ->where('provider_invoices.provider_id', $providerId)
            ->orderBy('provider_invoices.id', 'DESC')
            ->findAll();
    }
}
