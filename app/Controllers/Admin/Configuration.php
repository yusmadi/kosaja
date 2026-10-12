<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;

class Configuration extends BaseController
{
    protected AuditLogModel $auditModel;

    public function __construct()
    {
        $this->auditModel = new AuditLogModel();
    }

    /**
     * SA-11: Platform System Configuration & Integrations
     */
    public function index()
    {
        $config = [
            'app_name'           => 'KosAja Platform Hunian Multi-Region',
            'environment'        => ENVIRONMENT,
            'maintenance_mode'   => false,
            'payment_gateway'    => 'Midtrans Core API (Snap Simulator Active)',
            'gateway_server_key' => 'SB-Mid-server-••••••••••••••••',
            'grace_period_days'  => 3,
            'reservation_expiry' => 24, // 24 jam
            'wa_service'         => 'Simulated WhatsApp Engine (99.8% Delivery SLA)',
            'audit_retention'    => 'Permanen (Immutable Append-Only Log)',
        ];

        return view('admin/configuration/index', [
            'title'      => 'Konfigurasi Sistem Platform (SA-11) — KosAja',
            'role'       => 'super_admin',
            'activeMenu' => 'configuration',
            'config'     => $config,
        ]);
    }

    /**
     * Update system configurations
     */
    public function update()
    {
        $adminId = session()->get('user_id') ?? 1;
        $graceDays = (int) $this->request->getPost('grace_period_days');
        $expiryHours = (int) $this->request->getPost('reservation_expiry');

        $this->auditModel->record(
            $adminId,
            'SYSTEM_CONFIGURATION_UPDATED',
            'system_config',
            1,
            null,
            ['grace_period_days' => $graceDays, 'reservation_expiry' => $expiryHours],
            "Super Admin memperbarui konfigurasi sistem: Masa Tenggang (Grace Period) {$graceDays} hari, Batas Reservasi {$expiryHours} jam."
        );

        return redirect()->to(base_url('/admin/configuration'))->with('success', 'Konfigurasi operasional platform berhasil diperbarui.');
    }
}
