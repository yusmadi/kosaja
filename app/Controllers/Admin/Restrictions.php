<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Libraries\AdminScopeService;

class Restrictions extends BaseController
{
    protected AuditLogModel $auditModel;
    protected AdminScopeService $scopeService;

    public function __construct()
    {
        $this->auditModel   = new AuditLogModel();
        $this->scopeService = new AdminScopeService();
    }

    /**
     * ADM-10: Restrictions & Sanctions Management Panel
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $scopedRegionIds = $this->scopeService->getScopedRegionIds() ?: [0];

        // 1. Providers in region with their status
        $providers = $db->table('users')
            ->select('users.*, provider_profiles.verification_status, provider_profiles.rejection_reason')
            ->join('provider_profiles', 'provider_profiles.user_id = users.id')
            ->join('properties', 'properties.provider_id = users.id')
            ->whereIn('properties.region_id', $scopedRegionIds)
            ->distinct()
            ->get()->getResultArray();

        // 2. Listings in region with their publication status
        $listings = $db->table('listings')
            ->select('listings.*, units.name as unit_name, properties.name as property_name, users.name as provider_name')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('users', 'users.id = properties.provider_id')
            ->whereIn('properties.region_id', $scopedRegionIds)
            ->get()->getResultArray();

        return view('admin/restrictions/index', [
            'title'       => 'Restriksi & Sanksi Kepatuhan — Admin Regional',
            'role'        => session()->get('role') ?? 'admin_daerah',
            'activeMenu'  => 'restrictions',
            'providers'   => $providers,
            'listings'    => $listings,
            'scopedLabel' => $this->scopeService->getScopedRegionLabel(),
        ]);
    }

    /**
     * Apply restriction/suspension to a provider or listing
     */
    public function apply()
    {
        $adminId    = session()->get('user_id') ?? 2;
        $targetType = $this->request->getPost('target_type'); // 'provider' or 'listing'
        $targetId   = (int) $this->request->getPost('target_id');
        $reason     = trim((string) $this->request->getPost('reason'));

        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan pengenaan sanksi/restriksi wajib dicantumkan sesuai regulasi platform.');
        }

        $db = \Config\Database::connect();

        if ($targetType === 'provider') {
            $db->table('provider_profiles')->where('user_id', $targetId)->update([
                'verification_status' => 'suspended',
                'rejection_reason'    => $reason,
                'updated_at'          => date('Y-m-d H:i:s'),
            ]);

            $this->auditModel->record(
                $adminId,
                'PROVIDER_RESTRICTED',
                'users',
                $targetId,
                null,
                ['status' => 'suspended', 'reason' => $reason],
                "Mitra Provider #{$targetId} dikenakan sanksi SUSPEND dengan alasan: {$reason}"
            );

            return redirect()->to(base_url('/admin/restrictions'))->with('success', 'Sanksi penangguhan mitra berhasil diterapkan.');
        } elseif ($targetType === 'listing') {
            $db->table('listings')->where('id', $targetId)->update([
                'publication_status' => 'inactive',
                'updated_at'         => date('Y-m-d H:i:s'),
            ]);

            $this->auditModel->record(
                $adminId,
                'LISTING_RESTRICTED',
                'listings',
                $targetId,
                null,
                ['status' => 'inactive', 'reason' => $reason],
                "Listing #{$targetId} dinonaktifkan paksa dengan alasan: {$reason}"
            );

            return redirect()->to(base_url('/admin/restrictions'))->with('success', 'Iklan listing berhasil dinonaktifkan (inactive).');
        }

        return redirect()->back()->with('error', 'Jenis target restriksi tidak valid.');
    }

    /**
     * Lift restriction / revoke sanction
     */
    public function lift(int $id)
    {
        $adminId    = session()->get('user_id') ?? 2;
        $targetType = $this->request->getGet('type'); // 'provider' or 'listing'
        $db = \Config\Database::connect();

        if ($targetType === 'provider') {
            $db->table('provider_profiles')->where('user_id', $id)->update([
                'verification_status' => 'verified',
                'rejection_reason'    => null,
                'updated_at'          => date('Y-m-d H:i:s'),
            ]);

            $this->auditModel->record(
                $adminId,
                'PROVIDER_RESTRICTION_LIFTED',
                'users',
                $id,
                ['status' => 'suspended'],
                ['status' => 'verified'],
                "Sanksi penangguhan mitra provider #{$id} telah dicabut."
            );

            return redirect()->to(base_url('/admin/restrictions'))->with('success', 'Sanksi mitra berhasil dicabut, status kembali Terverifikasi.');
        } elseif ($targetType === 'listing') {
            $db->table('listings')->where('id', $id)->update([
                'publication_status' => 'published',
                'updated_at'         => date('Y-m-d H:i:s'),
            ]);

            $this->auditModel->record(
                $adminId,
                'LISTING_RESTRICTION_LIFTED',
                'listings',
                $id,
                ['status' => 'inactive'],
                ['status' => 'published'],
                "Penonaktifan listing #{$id} dicabut, iklan kembali tayang (published)."
            );

            return redirect()->to(base_url('/admin/restrictions'))->with('success', 'Listing iklan berhasil dipublikasikan kembali.');
        }

        return redirect()->to(base_url('/admin/restrictions'))->with('error', 'Tipe restriksi tidak valid.');
    }
}
