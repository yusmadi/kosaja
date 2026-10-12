<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DisputeModel;
use App\Models\RefundModel;
use App\Models\ContractModel;
use App\Models\InvoiceModel;
use App\Models\PaymentModel;
use App\Models\AuditLogModel;
use App\Libraries\WhatsAppService;

class Disputes extends BaseController
{
    protected DisputeModel $disputeModel;
    protected RefundModel $refundModel;
    protected ContractModel $contractModel;
    protected InvoiceModel $invoiceModel;
    protected PaymentModel $paymentModel;
    protected AuditLogModel $auditModel;
    protected WhatsAppService $whatsapp;

    public function __construct()
    {
        $this->disputeModel  = new DisputeModel();
        $this->refundModel   = new RefundModel();
        $this->contractModel = new ContractModel();
        $this->invoiceModel  = new InvoiceModel();
        $this->paymentModel  = new PaymentModel();
        $this->auditModel    = new AuditLogModel();
        $this->whatsapp      = new WhatsAppService();
    }

    /**
     * List all disputes with filter and regional scope
     */
    public function index()
    {
        $scopeService = new \App\Libraries\AdminScopeService();
        $isSuperAdmin = $scopeService->isSuperAdmin();
        $regionId     = $isSuperAdmin ? null : $scopeService->getAdminRegionId();

        $status = $this->request->getGet('status');
        $onlyEscalated = ($status === 'escalated');
        $filterStatus = $onlyEscalated ? null : ($status ?: null);

        $disputes = $this->disputeModel->getDisputesWithDetails($filterStatus, $regionId, $onlyEscalated);

        $allInScope = $this->disputeModel->getDisputesWithDetails(null, $regionId);
        $counts = [
            'all'           => count($allInScope),
            'opened'        => count(array_filter($allInScope, fn($d) => $d['status'] === 'opened')),
            'investigating' => count(array_filter($allInScope, fn($d) => $d['status'] === 'investigating')),
            'resolved'      => count(array_filter($allInScope, fn($d) => $d['status'] === 'resolved')),
            'escalated'     => count(array_filter($allInScope, fn($d) => !empty($d['is_escalated']))),
        ];

        return view('admin/disputes/index', [
            'title'          => $isSuperAdmin ? 'Eskalasi & Mediasi Sengketa (SA-09) — Super Admin' : 'Mediasi Sengketa Regional (ADM-08) — KosAja',
            'role'           => session()->get('role') ?? ($isSuperAdmin ? 'super_admin' : 'admin_daerah'),
            'activeMenu'     => 'disputes',
            'disputes'       => $disputes,
            'counts'         => $counts,
            'selectedStatus' => $status,
            'isSuperAdmin'   => $isSuperAdmin,
            'scopedLabel'    => $scopeService->getScopedRegionLabel(),
        ]);
    }

    /**
     * Detail of single dispute with complete evidence and actions
     */
    public function show(int $id)
    {
        $dispute = $this->disputeModel->getDisputeWithDetails($id);
        if (! $dispute) {
            return redirect()->to(base_url('/admin/disputes'))->with('error', 'Dispute tidak ditemukan.');
        }

        // Fetch related payments for refund options
        $invoices = $this->invoiceModel->getInvoicesByContract($dispute['contract_id']);
        $payments = [];
        foreach ($invoices as $inv) {
            $pmts = $this->paymentModel->getPaymentsByInvoice($inv['id']);
            $payments = array_merge($payments, $pmts);
        }

        // Check if refund was already issued for this dispute
        $refund = $this->refundModel->where('dispute_id', $id)->first();

        return view('admin/disputes/show', [
            'title'      => 'Tinjau Dispute #' . $id . ' — KosAja Backoffice',
            'role'       => 'admin',
            'activeMenu' => 'disputes',
            'dispute'    => $dispute,
            'payments'   => $payments,
            'refund'     => $refund,
        ]);
    }

    /**
     * Start investigation
     */
    public function investigate(int $id)
    {
        $adminId = (int) (session()->get('user_id') ?? 1);
        $dispute = $this->disputeModel->find($id);

        if (! $dispute) {
            return redirect()->to(base_url('/admin/disputes'))->with('error', 'Dispute tidak ditemukan.');
        }

        $this->disputeModel->update($id, [
            'status'     => 'investigating',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->auditModel->record(
            $adminId,
            'DISPUTE_INVESTIGATING',
            'disputes',
            $id,
            ['status' => $dispute['status']],
            ['status' => 'investigating'],
            "Admin memulai proses investigasi komplain #DSP-{$id}."
        );

        return redirect()->to(base_url('/admin/disputes/' . $id))->with('success', 'Status dispute diperbarui menjadi INVESTIGASI.');
    }

    /**
     * Escalate regional dispute to Super Admin (PRD 5.3 & 27)
     */
    public function escalate(int $id)
    {
        $adminId = (int) (session()->get('user_id') ?? 2);
        $notes   = trim((string) $this->request->getPost('escalation_notes'));

        $dispute = $this->disputeModel->find($id);
        if (! $dispute) {
            return redirect()->to(base_url('/admin/disputes'))->with('error', 'Dispute tidak ditemukan.');
        }

        if (empty($notes)) {
            return redirect()->back()->with('error', 'Catatan alasan eskalasi ke Super Admin wajib diisi.');
        }

        $now = date('Y-m-d H:i:s');
        $this->disputeModel->update($id, [
            'is_escalated'     => 1,
            'escalation_notes' => $notes,
            'escalated_at'     => $now,
            'status'           => 'investigating',
            'updated_at'       => $now,
        ]);

        $this->auditModel->record(
            $adminId,
            'DISPUTE_ESCALATED',
            'disputes',
            $id,
            null,
            ['is_escalated' => 1, 'notes' => $notes],
            "Admin Daerah mengekspos sengketa #DSP-{$id} ke Super Admin untuk putusan akhir. Alasan: {$notes}"
        );

        return redirect()->to(base_url('/admin/disputes/' . $id))->with('success', 'Sengketa berhasil dieskalasikan ke Super Administrator.');
    }

    /**
     * Make final decision and process refund if applicable
     */
    public function decide(int $id)
    {
        $adminId       = (int) (session()->get('user_id') ?? 1);
        $decision      = $this->request->getPost('decision'); // no_refund, partial_refund, full_refund
        $decisionNotes = trim((string) $this->request->getPost('decision_notes'));
        $refundAmount  = (float) $this->request->getPost('refund_amount');
        $paymentId     = (int) $this->request->getPost('payment_id');

        $dispute = $this->disputeModel->getDisputeWithDetails($id);
        if (! $dispute) {
            return redirect()->to(base_url('/admin/disputes'))->with('error', 'Dispute tidak ditemukan.');
        }

        if (! in_array($decision, ['no_refund', 'partial_refund', 'full_refund'])) {
            return redirect()->to(base_url('/admin/disputes/' . $id))->with('error', 'Pilihan keputusan tidak valid.');
        }

        if (empty($decisionNotes)) {
            return redirect()->to(base_url('/admin/disputes/' . $id))->with('error', 'Catatan dasar keputusan wajib diisi.');
        }

        $now = date('Y-m-d H:i:s');
        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Update dispute record
        $this->disputeModel->update($id, [
            'status'         => 'resolved',
            'decision'       => $decision,
            'decision_notes' => $decisionNotes,
            'decided_by_id'  => $adminId,
            'decided_at'     => $now,
            'updated_at'     => $now,
        ]);

        $refundData = null;

        // 2. Process refund if applicable
        if (in_array($decision, ['partial_refund', 'full_refund']) && $refundAmount > 0) {
            $payment = $this->paymentModel->find($paymentId);
            if (! $payment) {
                // Pick latest payment from contract invoices
                $invoices = $this->invoiceModel->getInvoicesByContract($dispute['contract_id']);
                foreach ($invoices as $inv) {
                    $p = $this->paymentModel->where('invoice_id', $inv['id'])->first();
                    if ($p) {
                        $payment = $p;
                        break;
                    }
                }
            }

            if ($payment) {
                $refundType = ($decision === 'full_refund') ? 'full' : 'partial';

                $refundRecord = [
                    'dispute_id'      => $id,
                    'payment_id'      => $payment['id'],
                    'invoice_id'      => $payment['invoice_id'],
                    'amount'          => $refundAmount,
                    'refund_type'     => $refundType,
                    'status'          => 'completed',
                    'reason'          => $decisionNotes,
                    'processed_by_id' => $adminId,
                    'processed_at'    => $now,
                ];
                $refundId = $this->refundModel->insert($refundRecord);
                $refundRecord['id'] = $refundId;
                $refundData = $refundRecord;

                if ($decision === 'full_refund') {
                    $this->paymentModel->update($payment['id'], ['status' => 'refunded']);
                }

                $this->auditModel->record(
                    $adminId,
                    'REFUND_PROCESSED',
                    'refunds',
                    $refundId,
                    null,
                    $refundRecord,
                    "Pengembalian dana Rp " . number_format($refundAmount, 0, ',', '.') . " disetujui untuk dispute #DSP-{$id}."
                );
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to(base_url('/admin/disputes/' . $id))->with('error', 'Gagal memproses transaksi database keputusan dispute.');
        }

        // 3. Record Audit Log
        $this->auditModel->record(
            $adminId,
            'DISPUTE_DECIDED',
            'disputes',
            $id,
            ['status' => $dispute['status'], 'decision' => $dispute['decision']],
            ['status' => 'resolved', 'decision' => $decision],
            "Keputusan dispute #DSP-{$id}: {$decision}. Catatan: {$decisionNotes}"
        );

        // 4. WhatsApp Notification to Tenant
        $dispute['decision']       = $decision;
        $dispute['decision_notes'] = $decisionNotes;
        $this->whatsapp->sendDisputeDecisionNotification($dispute, $refundData);

        return redirect()->to(base_url('/admin/disputes/' . $id))->with('success', 'Keputusan dispute berhasil disimpan dan notifikasi telah dikirimkan ke penyewa.');
    }

    /**
     * Refunds Management Overview
     */
    public function refunds()
    {
        $refunds = $this->refundModel->getRefundsWithDetails();
        $totalAmount = array_reduce($refunds, fn($carry, $r) => $carry + (float) $r['amount'], 0);

        return view('admin/disputes/refunds', [
            'title'       => 'Riwayat Pengembalian Dana (Refunds SA-08) — KosAja',
            'role'        => session()->get('role') ?? 'super_admin',
            'activeMenu'  => 'refunds',
            'refunds'     => $refunds,
            'totalAmount' => $totalAmount,
        ]);
    }
}
