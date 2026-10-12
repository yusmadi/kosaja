<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProviderProfileModel;
use App\Models\UserModel;
use App\Models\AuditLogModel;

class Providers extends BaseController
{
    protected $providerProfileModel;
    protected $userModel;
    protected $auditLogModel;

    public function __construct()
    {
        $this->providerProfileModel = new ProviderProfileModel();
        $this->userModel = new UserModel();
        $this->auditLogModel = new AuditLogModel();
    }

    /**
     * List all providers with their verification status
     */
    public function index()
    {
        $statusFilter = $this->request->getGet('status') ?? 'all';

        $db = \Config\Database::connect();
        $builder = $db->table('users')
            ->select('users.*, provider_profiles.id as profile_id, provider_profiles.verification_status, provider_profiles.rejection_reason, provider_profiles.id_card_photo, provider_profiles.bank_name, provider_profiles.bank_account_number, provider_profiles.free_period_end,
                (SELECT COUNT(*) FROM properties WHERE properties.provider_id = users.id) as total_properties,
                (SELECT COUNT(*) FROM units JOIN properties ON properties.id = units.property_id WHERE properties.provider_id = users.id) as total_units')
            ->join('provider_profiles', 'provider_profiles.user_id = users.id', 'left')
            ->where('users.role', 'provider');

        $scopeService = new \App\Libraries\AdminScopeService();
        $isSuperAdmin = $scopeService->isSuperAdmin();
        $scopedRegionIds = $scopeService->getScopedRegionIds();

        if ($statusFilter !== 'all') {
            $builder->where('provider_profiles.verification_status', $statusFilter);
        }

        if (! $isSuperAdmin) {
            $builder->join('properties', 'properties.provider_id = users.id')
                    ->whereIn('properties.region_id', ! empty($scopedRegionIds) ? $scopedRegionIds : [0])
                    ->groupBy('users.id');
        }

        $providers = $builder->orderBy('users.id', 'DESC')->get()->getResultArray();

        // Counts
        $counts = [
            'all'       => count($providers),
            'pending'   => count(array_filter($providers, fn($p) => ($p['verification_status'] ?? '') === 'pending')),
            'verified'  => count(array_filter($providers, fn($p) => ($p['verification_status'] ?? '') === 'verified')),
            'rejected'  => count(array_filter($providers, fn($p) => ($p['verification_status'] ?? '') === 'rejected')),
            'suspended' => count(array_filter($providers, fn($p) => ($p['verification_status'] ?? '') === 'suspended')),
        ];

        return view('admin/providers/index', [
            'title'        => $isSuperAdmin ? 'Mitra Penyedia Global (SA-03) — Super Admin' : 'Verifikasi Mitra Provider (ADM-02) — Admin Daerah',
            'role'         => session()->get('role') ?? ($isSuperAdmin ? 'super_admin' : 'admin_daerah'),
            'activeMenu'   => 'providers',
            'providers'    => $providers,
            'counts'       => $counts,
            'statusFilter' => $statusFilter,
            'isSuperAdmin' => $isSuperAdmin,
            'scopedLabel'  => $scopeService->getScopedRegionLabel(),
        ]);
    }

    /**
     * Approve / Verify Provider
     */
    public function verify(int $profileId)
    {
        $profile = $this->providerProfileModel->find($profileId);
        if (! $profile) {
            return redirect()->back()->with('error', 'Profil provider tidak ditemukan.');
        }

        $previousState = $profile;

        $this->providerProfileModel->update($profileId, [
            'verification_status' => 'verified',
            'rejection_reason'    => null,
        ]);

        $newState = $this->providerProfileModel->find($profileId);

        // Record to audit logs
        $actorId = session()->get('user_id');
        $this->auditLogModel->record(
            $actorId,
            'VERIFY_PROVIDER',
            'provider_profile',
            $profileId,
            $previousState,
            $newState,
            'Dokumen identitas (KTP) dan nomor rekening valid.'
        );

        return redirect()->back()->with('success', 'Mitra provider berhasil diverifikasi (Status: Terverifikasi).');
    }

    /**
     * Reject Provider Verification with Reason
     */
    public function reject(int $profileId)
    {
        $profile = $this->providerProfileModel->find($profileId);
        if (! $profile) {
            return redirect()->back()->with('error', 'Profil provider tidak ditemukan.');
        }

        $reason = $this->request->getPost('reason');
        if (empty(trim($reason ?? ''))) {
            return redirect()->back()->with('error', 'Alasan penolakan verifikasi wajib diisi.');
        }

        $previousState = $profile;

        $this->providerProfileModel->update($profileId, [
            'verification_status' => 'rejected',
            'rejection_reason'    => trim($reason),
        ]);

        $newState = $this->providerProfileModel->find($profileId);

        // Record to audit logs
        $actorId = session()->get('user_id');
        $this->auditLogModel->record(
            $actorId,
            'REJECT_PROVIDER',
            'provider_profile',
            $profileId,
            $previousState,
            $newState,
            trim($reason)
        );

        return redirect()->back()->with('warning', 'Pengajuan verifikasi provider telah ditolak dengan alasan: ' . esc($reason));
    }

    /**
     * Suspend Provider
     */
    public function suspend(int $profileId)
    {
        $profile = $this->providerProfileModel->find($profileId);
        if (! $profile) {
            return redirect()->back()->with('error', 'Profil provider tidak ditemukan.');
        }

        $reason = $this->request->getPost('reason') ?? 'Pelanggaran syarat & ketentuan platform.';
        $previousState = $profile;

        $this->providerProfileModel->update($profileId, [
            'verification_status' => 'suspended',
            'rejection_reason'    => $reason,
        ]);

        $newState = $this->providerProfileModel->find($profileId);

        // Record to audit logs
        $actorId = session()->get('user_id');
        $this->auditLogModel->record(
            $actorId,
            'SUSPEND_PROVIDER',
            'provider_profile',
            $profileId,
            $previousState,
            $newState,
            $reason
        );

        return redirect()->back()->with('warning', 'Mitra provider telah ditangguhkan (Suspended).');
    }
}
