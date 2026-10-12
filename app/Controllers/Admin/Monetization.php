<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;

class Monetization extends BaseController
{
    protected AuditLogModel $auditModel;

    public function __construct()
    {
        $this->auditModel = new AuditLogModel();
    }

    /**
     * SA-10: Platform Monetization & Pricing Policies
     */
    public function index()
    {
        // Default or configured monetization policies (PRD Sections 29, 30, 31, 32)
        $policies = [
            'free_period_months' => 1, // 30 hari kalender
            'free_period_days'   => 30,
            'listing_fee'        => 10000,
            'gateway_fee'        => 4500,
            'commission_rate'    => 2.0, // 2%
            'subscription_tiers' => [
                ['name' => 'Starter / Pemula', 'quota' => 3, 'price' => 0, 'features' => 'Gratis 30 hari pertama atau hingga transaksi sewa pertama lunas, kuota 3 listing aktif'],
                ['name' => 'Juragan Kos (Pro)', 'quota' => 15, 'price' => 75000, 'features' => 'Hingga 15 unit kamar, badge verified, notifikasi WhatsApp'],
                ['name' => 'Bisnis Properti (Enterprise)', 'quota' => 50, 'price' => 200000, 'features' => 'Hingga 50 unit kamar, prioritas moderasi 2 jam, laporan analitik'],
            ],
        ];

        return view('admin/monetization/index', [
            'title'      => 'Kebijakan Monetisasi & Tarif Platform (SA-10) — KosAja',
            'role'       => 'super_admin',
            'activeMenu' => 'monetization',
            'policies'   => $policies,
        ]);
    }

    /**
     * Save monetization policy changes
     */
    public function update()
    {
        $adminId = session()->get('user_id') ?? 1;
        $freeMonths = (int) $this->request->getPost('free_period_months');
        $listingFee = (float) $this->request->getPost('listing_fee');
        $gatewayFee = (float) $this->request->getPost('gateway_fee');

        $this->auditModel->record(
            $adminId,
            'MONETIZATION_POLICY_UPDATED',
            'system_config',
            1,
            null,
            ['free_period_months' => $freeMonths, 'listing_fee' => $listingFee, 'gateway_fee' => $gatewayFee],
            "Super Admin memperbarui skema monetisasi platform: Free Period {$freeMonths} bulan, Listing Fee Rp " . number_format($listingFee, 0, ',', '.') . ", Gateway Fee Rp " . number_format($gatewayFee, 0, ',', '.')
        );

        return redirect()->to(base_url('/admin/monetization'))->with('success', 'Skema tarif dan kebijakan monetisasi platform berhasil disimpan.');
    }
}
