<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettlementModel;
use App\Models\AuditLogModel;

class Settlements extends BaseController
{
    protected SettlementModel $settlementModel;
    protected AuditLogModel $auditModel;

    public function __construct()
    {
        $this->settlementModel = new SettlementModel();
        $this->auditModel      = new AuditLogModel();
    }

    /**
     * SA-07: Provider Payouts & Settlements Registry
     */
    public function index()
    {
        $statusFilter = $this->request->getGet('status');
        $settlements  = $this->settlementModel->getSettlementsWithDetails($statusFilter ?: null);

        $db = \Config\Database::connect();
        $totalCompleted = (float) ($db->table('settlements')->where('status', 'completed')->selectSum('net_amount')->get()->getRow()->net_amount ?? 0);
        $totalPending   = (float) ($db->table('settlements')->where('status', 'pending')->selectSum('net_amount')->get()->getRow()->net_amount ?? 0);
        $countPending   = $db->table('settlements')->where('status', 'pending')->countAllResults();

        return view('admin/settlements/index', [
            'title'        => 'Pencairan Dana Mitra (Settlements SA-07) — KosAja',
            'role'         => 'super_admin',
            'activeMenu'   => 'settlements',
            'settlements'  => $settlements,
            'statusFilter' => $statusFilter,
            'stats'        => [
                'total_completed' => $totalCompleted,
                'total_pending'   => $totalPending,
                'count_pending'   => $countPending,
            ],
        ]);
    }

    /**
     * Process/Approve payout settlement
     */
    public function process(int $id)
    {
        $adminId = session()->get('user_id') ?? 1;
        $settlement = $this->settlementModel->find($id);

        if (! $settlement) {
            return redirect()->to(base_url('/admin/settlements'))->with('error', 'Data settlement tidak ditemukan.');
        }

        $now = date('Y-m-d H:i:s');
        $ref = 'PAYOUT-' . date('Ymd') . '-' . sprintf('%03d', $id);

        $this->settlementModel->update($id, [
            'status'           => 'completed',
            'payout_reference' => $ref,
            'processed_by_id'  => $adminId,
            'processed_at'     => $now,
            'updated_at'       => $now,
        ]);

        $this->auditModel->record(
            $adminId,
            'SETTLEMENT_PROCESSED',
            'settlements',
            $id,
            ['status' => $settlement['status']],
            ['status' => 'completed', 'reference' => $ref],
            "Super Admin menyetujui pencairan dana Rp " . number_format($settlement['net_amount'], 0, ',', '.') . " ke rekening {$settlement['bank_name']} {$settlement['bank_account_number']} (Ref: {$ref})."
        );

        return redirect()->to(base_url('/admin/settlements'))->with('success', "Settlement #{$id} berhasil diproses dengan nomor referensi transfer: {$ref}");
    }
}
