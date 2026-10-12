<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Transactions extends BaseController
{
    /**
     * SA-06: Global Financial Transactions Registry
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $statusFilter = $this->request->getGet('status');

        $builder = $db->table('invoices')
            ->select('invoices.*, 
                      CONCAT("#INV-", invoices.id) as invoice_number,
                      contracts.id as contract_id, contracts.start_date, contracts.end_date,
                      tenants.name as tenant_name, tenants.email as tenant_email,
                      providers.name as provider_name,
                      properties.name as property_name,
                      payments.payment_method, payments.gateway_trx_id, payments.status as payment_status')
            ->join('contracts', 'contracts.id = invoices.contract_id')
            ->join('users as tenants', 'tenants.id = contracts.tenant_id')
            ->join('users as providers', 'providers.id = contracts.provider_id')
            ->join('properties', 'properties.id = contracts.property_id')
            ->join('payments', 'payments.invoice_id = invoices.id', 'left')
            ->orderBy('invoices.id', 'DESC');

        if (! empty($statusFilter)) {
            $builder->where('invoices.status', $statusFilter);
        }

        $invoices = $builder->get()->getResultArray();

        // Summaries
        $totalPaid = (float) ($db->table('invoices')->where('status', 'paid')->selectSum('total_amount')->get()->getRow()->total_amount ?? 0);
        $totalFees = (float) ($db->table('invoices')->where('status', 'paid')->selectSum('gateway_fee')->get()->getRow()->gateway_fee ?? 0);
        $countPaid = $db->table('invoices')->where('status', 'paid')->countAllResults();
        $countPending = $db->table('invoices')->where('status', 'pending')->countAllResults();

        return view('admin/transactions/index', [
            'title'        => 'Transaksi Finansial Global (SA-06) — KosAja',
            'role'         => 'super_admin',
            'activeMenu'   => 'transactions',
            'invoices'     => $invoices,
            'statusFilter' => $statusFilter,
            'stats'        => [
                'total_paid'    => $totalPaid,
                'total_fees'    => $totalFees,
                'count_paid'    => $countPaid,
                'count_pending' => $countPending,
            ],
        ]);
    }
}
