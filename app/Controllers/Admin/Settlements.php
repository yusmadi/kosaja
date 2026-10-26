<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettlementModel;
use App\Models\AuditLogModel;
use App\Libraries\AdminScopeService;

class Settlements extends BaseController
{
    protected SettlementModel $settlementModel;
    protected AuditLogModel $auditModel;
    protected AdminScopeService $scopeService;

    public function __construct()
    {
        $this->settlementModel = new SettlementModel();
        $this->auditModel      = new AuditLogModel();
        $this->scopeService    = new AdminScopeService();
    }

    /**
     * Maker-Checker Settlements Hub:
     * - Admin Daerah (Maker): Memeriksa kesiapan lapangan & merekomendasikan pencairan (pending -> verified).
     * - Super Admin (Checker): Mengotorisasi & mengeksekusi transfer dana dari escrow ke mitra (verified -> completed).
     */
    public function index()
    {
        $isSuperAdmin    = $this->scopeService->isSuperAdmin();
        $scopedRegionIds = $this->scopeService->getScopedRegionIds();
        $scopedLabel     = $this->scopeService->getScopedRegionLabel();

        $statusFilter = $this->request->getGet('status');
        $settlements  = $this->settlementModel->getSettlementsWithDetails($statusFilter ?: null, $scopedRegionIds);

        // Calculate scoped stats
        $db = \Config\Database::connect();
        
        $baseBuilder = function() use ($db, $scopedRegionIds) {
            $builder = $db->table('settlements')
                ->join('contracts', 'contracts.id = settlements.contract_id', 'left')
                ->join('properties', 'properties.id = contracts.property_id', 'left')
                ->join('users as providers', 'providers.id = settlements.provider_id', 'left');

            if (! empty($scopedRegionIds)) {
                $builder->groupStart()
                    ->whereIn('settlements.region_id', $scopedRegionIds)
                    ->orWhereIn('properties.region_id', $scopedRegionIds)
                    ->orWhereIn('providers.region_id', $scopedRegionIds)
                ->groupEnd();
            }

            return $builder;
        };

        $totalCompleted = (float) ($baseBuilder()->where('settlements.status', 'completed')->selectSum('settlements.net_amount')->get()->getRow()->net_amount ?? 0);
        $totalPending   = (float) ($baseBuilder()->where('settlements.status', 'pending')->selectSum('settlements.net_amount')->get()->getRow()->net_amount ?? 0);
        $countPending   = $baseBuilder()->where('settlements.status', 'pending')->countAllResults();

        $totalVerified  = (float) ($baseBuilder()->where('settlements.status', 'verified')->selectSum('settlements.net_amount')->get()->getRow()->net_amount ?? 0);
        $countVerified  = $baseBuilder()->where('settlements.status', 'verified')->countAllResults();

        $countRejected  = $baseBuilder()->where('settlements.status', 'rejected')->countAllResults();

        return view('admin/settlements/index', [
            'title'           => $isSuperAdmin 
                ? 'Otorisasi Pencairan Dana Mitra (Settlements SA-07) — KosAja' 
                : 'Verifikasi Pencairan Sewa Mitra (ADM-09) — KosAja',
            'role'            => session()->get('role') ?? ($isSuperAdmin ? 'super_admin' : 'admin_daerah'),
            'activeMenu'      => 'settlements',
            'isSuperAdmin'    => $isSuperAdmin,
            'scopedLabel'     => $scopedLabel,
            'settlements'     => $settlements,
            'statusFilter'    => $statusFilter,
            'stats'           => [
                'total_completed' => $totalCompleted,
                'total_pending'   => $totalPending,
                'count_pending'   => $countPending,
                'total_verified'  => $totalVerified,
                'count_verified'  => $countVerified,
                'count_rejected'  => $countRejected,
            ],
        ]);
    }

    /**
     * TAHAP 1 (Maker): Admin Daerah memverifikasi kesiapan operasional lapangan
     * dan merekomendasikan pencairan ke Super Admin.
     */
    public function verify(int $id)
    {
        $adminId   = (int) (session()->get('user_id') ?? 1);
        $adminName = session()->get('name') ?? 'Admin';
        $settlement = $this->settlementModel->find($id);

        if (! $settlement) {
            return redirect()->to(base_url('/admin/settlements'))->with('error', 'Data settlement tidak ditemukan.');
        }

        if ($settlement['status'] === 'completed') {
            return redirect()->to(base_url('/admin/settlements'))->with('error', 'Settlement ini telah dicairkan dan tidak dapat diubah lagi.');
        }

        $notes = trim((string) $this->request->getPost('verification_notes'));
        if (empty($notes)) {
            $notes = 'Kondisi hunian normal, penyewa telah menempati kamar fisik dengan aman, tidak ada laporan sengketa aktif. Rekomendasi pencairan dana sewa disetujui.';
        }

        $now = date('Y-m-d H:i:s');
        $this->settlementModel->update($id, [
            'status'             => 'verified',
            'verified_by_id'     => $adminId,
            'verified_at'        => $now,
            'verification_notes' => $notes,
            'rejection_reason'   => null,
            'updated_at'         => $now,
        ]);

        $this->auditModel->record(
            $adminId,
            'SETTLEMENT_VERIFIED',
            'settlements',
            $id,
            ['status' => $settlement['status']],
            ['status' => 'verified', 'notes' => $notes],
            "Verifikator ({$adminName}) memverifikasi operasional lapangan dan merekomendasikan pencairan dana Rp " . number_format($settlement['net_amount'], 0, ',', '.') . " untuk settlement #{$id}. Catatan: {$notes}"
        );

        return redirect()->to(base_url('/admin/settlements'))->with('success', "Settlement #{$id} berhasil diverifikasi! Permohonan kini masuk antrean eksekusi transfer Super Admin.");
    }

    /**
     * TAHAP 1 (Maker): Admin Daerah menahan pencairan sementara jika ada kendala di lapangan/sengketa.
     */
    public function hold(int $id)
    {
        $adminId   = (int) (session()->get('user_id') ?? 1);
        $adminName = session()->get('name') ?? 'Admin';
        $settlement = $this->settlementModel->find($id);

        if (! $settlement) {
            return redirect()->to(base_url('/admin/settlements'))->with('error', 'Data settlement tidak ditemukan.');
        }

        $reason = trim((string) $this->request->getPost('rejection_reason'));
        if (empty($reason)) {
            return redirect()->back()->with('error', 'Alasan penahanan pencairan wajib diisi untuk transparansi.');
        }

        $now = date('Y-m-d H:i:s');
        $this->settlementModel->update($id, [
            'status'           => 'rejected',
            'verified_by_id'   => $adminId,
            'verified_at'      => $now,
            'rejection_reason' => $reason,
            'updated_at'       => $now,
        ]);

        $this->auditModel->record(
            $adminId,
            'SETTLEMENT_HELD',
            'settlements',
            $id,
            ['status' => $settlement['status']],
            ['status' => 'rejected', 'reason' => $reason],
            "Verifikator ({$adminName}) menahan pencairan settlement #{$id} sebesar Rp " . number_format($settlement['net_amount'], 0, ',', '.') . ". Alasan: {$reason}"
        );

        return redirect()->to(base_url('/admin/settlements'))->with('warning', "Pencairan settlement #{$id} ditahan sementara. Alasan dicatat pada sistem audit.");
    }

    /**
     * TAHAP 2 (Checker): Super Admin mengeksekusi transfer dana dari escrow platform ke rekening bank mitra.
     * Hanya dapat dijalankan oleh role Super Admin (Guarded).
     */
    public function process(int $id)
    {
        if (! $this->scopeService->isSuperAdmin()) {
            return redirect()->to(base_url('/admin/settlements'))->with('error', 'Akses ditolak: Otorisasi transfer dana ke rekening perbankan merupakan wewenang eksklusif Super Admin.');
        }

        $adminId    = (int) (session()->get('user_id') ?? 1);
        $settlement = $this->settlementModel->find($id);

        if (! $settlement) {
            return redirect()->to(base_url('/admin/settlements'))->with('error', 'Data settlement tidak ditemukan.');
        }

        if ($settlement['status'] === 'completed') {
            return redirect()->to(base_url('/admin/settlements'))->with('error', 'Settlement ini telah diproses sebelumnya.');
        }

        $customRef = trim((string) $this->request->getPost('payout_reference'));
        $ref       = ! empty($customRef) ? $customRef : ('PAYOUT-' . date('Ymd') . '-' . sprintf('%03d', $id));
        $adminNote = trim((string) $this->request->getPost('notes'));
        $note      = ! empty($adminNote) ? $adminNote : 'Super Admin menyetujui pencairan dana sewa ke rekening resmi mitra.';

        $now = date('Y-m-d H:i:s');
        $this->settlementModel->update($id, [
            'status'           => 'completed',
            'payout_reference' => $ref,
            'processed_by_id'  => $adminId,
            'processed_at'     => $now,
            'notes'            => $note,
            'updated_at'       => $now,
        ]);

        $this->auditModel->record(
            $adminId,
            'SETTLEMENT_PROCESSED',
            'settlements',
            $id,
            ['status' => $settlement['status']],
            ['status' => 'completed', 'reference' => $ref],
            "Super Admin menyetujui transfer pencairan dana Rp " . number_format($settlement['net_amount'], 0, ',', '.') . " ke rekening {$settlement['bank_name']} {$settlement['bank_account_number']} (Ref: {$ref})."
        );

        return redirect()->to(base_url('/admin/settlements'))->with('success', "Settlement #{$id} berhasil diproses lunas dengan nomor referensi transfer: {$ref}");
    }
}
