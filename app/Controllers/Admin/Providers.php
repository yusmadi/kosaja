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
            ->select('users.*, regions.name as region_name, provider_profiles.id as profile_id, provider_profiles.verification_status, provider_profiles.rejection_reason, provider_profiles.id_card_photo, provider_profiles.bank_name, provider_profiles.bank_account_number, provider_profiles.free_period_end,
                (SELECT COUNT(*) FROM properties WHERE properties.provider_id = users.id) as total_properties,
                (SELECT COUNT(*) FROM units JOIN properties ON properties.id = units.property_id WHERE properties.provider_id = users.id) as total_units')
            ->join('provider_profiles', 'provider_profiles.user_id = users.id', 'left')
            ->join('regions', 'regions.id = users.region_id', 'left')
            ->where('users.role', 'provider');

        $scopeService = new \App\Libraries\AdminScopeService();
        $isSuperAdmin = $scopeService->isSuperAdmin();
        $scopedRegionIds = $scopeService->getScopedRegionIds();

        if (! $isSuperAdmin) {
            $builder->whereIn('users.region_id', ! empty($scopedRegionIds) ? $scopedRegionIds : [0]);
        }

        $allProviders = $builder->orderBy('users.id', 'DESC')->get()->getResultArray();

        // Counts across all providers in current admin scope (independent of active filter tab)
        $counts = [
            'all'       => count($allProviders),
            'pending'   => count(array_filter($allProviders, fn($p) => ($p['verification_status'] ?? 'pending') === 'pending')),
            'verified'  => count(array_filter($allProviders, fn($p) => ($p['verification_status'] ?? '') === 'verified')),
            'rejected'  => count(array_filter($allProviders, fn($p) => ($p['verification_status'] ?? '') === 'rejected')),
            'suspended' => count(array_filter($allProviders, fn($p) => ($p['verification_status'] ?? '') === 'suspended')),
        ];

        // Filter providers based on active tab
        if ($statusFilter === 'all') {
            $providers = $allProviders;
        } elseif ($statusFilter === 'pending') {
            $providers = array_values(array_filter($allProviders, fn($p) => ($p['verification_status'] ?? 'pending') === 'pending'));
        } else {
            $providers = array_values(array_filter($allProviders, fn($p) => ($p['verification_status'] ?? '') === $statusFilter));
        }

        return view('admin/providers/index', [
            'title'        => $isSuperAdmin ? 'Mitra Penyedia Global (SA-03) — Super Admin' : 'Verifikasi Mitra Provider (ADM-02) — Admin ' . esc($scopeService->getScopedRegionLabel()),
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
     * Approve / Verify Provider (Admin Daerah Exclusive)
     */
    public function verify(int $profileId)
    {
        $scopeService = new \App\Libraries\AdminScopeService();

        // FR: Verification is exclusively delegated to Admin Daerah based on operational jurisdiction
        if ($scopeService->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super Admin tidak memiliki wewenang verifikasi langsung. Proses verifikasi dokumen identitas mitra merupakan kewenangan operasional Admin Daerah pada wilayah masing-masing.');
        }

        if (! $scopeService->isAdminDaerah()) {
            return redirect()->back()->with('error', 'Hanya Admin Daerah yang berwenang memverifikasi dokumen mitra penyedia.');
        }

        $profile = $this->providerProfileModel->find($profileId);
        if (! $profile) {
            // Fallback: check if $profileId was passed as user_id
            $profile = $this->providerProfileModel->getByUserId($profileId);
            if (! $profile) {
                // If user exists as provider, auto-create profile
                $user = $this->userModel->find($profileId);
                if ($user && $user['role'] === 'provider') {
                    $newProfileId = $this->providerProfileModel->insert([
                        'user_id'             => $user['id'],
                        'verification_status' => 'pending',
                        'free_period_end'     => $this->providerProfileModel->calculateDefaultFreePeriodEnd(),
                    ]);
                    $profile = $this->providerProfileModel->find($newProfileId);
                }
            }
        }

        if (! $profile) {
            return redirect()->back()->with('error', 'Profil provider tidak ditemukan.');
        }

        $actualProfileId = (int) $profile['id'];

        $scopedRegionIds = $scopeService->getScopedRegionIds() ?? [];
        $targetUser = $this->userModel->find($profile['user_id']);
        if (! $targetUser || ! in_array((int) ($targetUser['region_id'] ?? 0), $scopedRegionIds, true)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk memverifikasi mitra di luar wilayah yurisdiksi tugas Anda.');
        }

        $previousState = $profile;

        $this->providerProfileModel->update($actualProfileId, [
            'verification_status' => 'verified',
            'rejection_reason'    => null,
        ]);

        $newState = $this->providerProfileModel->find($actualProfileId);

        // Record to audit logs
        $actorId = session()->get('user_id');
        $this->auditLogModel->record(
            $actorId,
            'VERIFY_PROVIDER',
            'provider_profile',
            $actualProfileId,
            $previousState,
            $newState,
            "Dokumen identitas (KTP) dan nomor rekening valid. Diverifikasi oleh Admin Daerah ({$scopeService->getScopedRegionLabel()})."
        );

        return redirect()->back()->with('success', 'Mitra provider berhasil diverifikasi (Status: Terverifikasi).');
    }

    /**
     * Reject Provider Verification with Reason (Admin Daerah Exclusive)
     */
    public function reject(int $profileId)
    {
        $scopeService = new \App\Libraries\AdminScopeService();

        // FR: Rejection is exclusively delegated to Admin Daerah
        if ($scopeService->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super Admin tidak memiliki wewenang penolakan verifikasi langsung. Proses moderasi dokumen mitra adalah kewenangan Admin Daerah.');
        }

        if (! $scopeService->isAdminDaerah()) {
            return redirect()->back()->with('error', 'Hanya Admin Daerah yang berwenang menolak verifikasi mitra penyedia.');
        }

        $profile = $this->providerProfileModel->find($profileId);
        if (! $profile) {
            $profile = $this->providerProfileModel->getByUserId($profileId);
        }

        if (! $profile) {
            return redirect()->back()->with('error', 'Profil provider tidak ditemukan.');
        }

        $actualProfileId = (int) $profile['id'];

        $scopedRegionIds = $scopeService->getScopedRegionIds() ?? [];
        $targetUser = $this->userModel->find($profile['user_id']);
        if (! $targetUser || ! in_array((int) ($targetUser['region_id'] ?? 0), $scopedRegionIds, true)) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menolak mitra di luar wilayah yurisdiksi tugas Anda.');
        }

        $reason = $this->request->getPost('reason');
        if (empty(trim($reason ?? ''))) {
            return redirect()->back()->with('error', 'Alasan penolakan verifikasi wajib diisi.');
        }

        $previousState = $profile;

        $this->providerProfileModel->update($actualProfileId, [
            'verification_status' => 'rejected',
            'rejection_reason'    => trim($reason),
        ]);

        $newState = $this->providerProfileModel->find($actualProfileId);

        // Record to audit logs
        $actorId = session()->get('user_id');
        $this->auditLogModel->record(
            $actorId,
            'REJECT_PROVIDER',
            'provider_profile',
            $actualProfileId,
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
            $profile = $this->providerProfileModel->getByUserId($profileId);
        }

        if (! $profile) {
            return redirect()->back()->with('error', 'Profil provider tidak ditemukan.');
        }

        $actualProfileId = (int) $profile['id'];

        $scopeService = new \App\Libraries\AdminScopeService();
        if (! $scopeService->isSuperAdmin()) {
            $scopedRegionIds = $scopeService->getScopedRegionIds() ?? [];
            $targetUser = $this->userModel->find($profile['user_id']);
            if (! $targetUser || ! in_array((int) ($targetUser['region_id'] ?? 0), $scopedRegionIds, true)) {
                return redirect()->back()->with('error', 'Anda tidak memiliki hak akses untuk menangguhkan mitra di luar wilayah tugas Anda.');
            }
        }

        $reason = $this->request->getPost('reason') ?? 'Pelanggaran syarat & ketentuan platform.';
        $previousState = $profile;

        $this->providerProfileModel->update($actualProfileId, [
            'verification_status' => 'suspended',
            'rejection_reason'    => $reason,
        ]);

        $newState = $this->providerProfileModel->find($actualProfileId);

        // Record to audit logs
        $actorId = session()->get('user_id');
        $this->auditLogModel->record(
            $actorId,
            'SUSPEND_PROVIDER',
            'provider_profile',
            $actualProfileId,
            $previousState,
            $newState,
            $reason
        );

        return redirect()->back()->with('warning', 'Mitra provider telah ditangguhkan (Suspended).');
    }
}
