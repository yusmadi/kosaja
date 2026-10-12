<?php

namespace App\Controllers\Tenant;

use App\Controllers\BaseController;
use App\Models\ContractModel;
use App\Models\InvoiceModel;
use App\Models\PaymentModel;
use App\Models\RentalApplicationModel;
use App\Models\DisputeModel;
use App\Models\AuditLogModel;
use App\Libraries\RecurringBillingService;

class Dashboard extends BaseController
{
    protected ContractModel $contractModel;
    protected InvoiceModel $invoiceModel;
    protected PaymentModel $paymentModel;
    protected RentalApplicationModel $appModel;
    protected DisputeModel $disputeModel;
    protected AuditLogModel $auditModel;
    protected RecurringBillingService $billingService;

    public function __construct()
    {
        $this->contractModel  = new ContractModel();
        $this->invoiceModel   = new InvoiceModel();
        $this->paymentModel   = new PaymentModel();
        $this->appModel       = new RentalApplicationModel();
        $this->disputeModel   = new DisputeModel();
        $this->auditModel     = new AuditLogModel();
        $this->billingService = new RecurringBillingService();
    }

    /**
     * Comprehensive Tenant Dashboard View
     */
    public function index()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'))->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $tenantId = (int) session()->get('user_id');
        $today    = date('Y-m-d');

        // Contracts
        $contracts = $this->contractModel->getContractsByTenant($tenantId);
        $activeContracts = array_filter($contracts, fn($c) => $c['status'] === 'active');

        // Ensure all active contracts have recurring billing generated
        foreach ($activeContracts as $ac) {
            $this->billingService->generateScheduleForContract($ac);
        }

        // Run worker pass to ensure fresh overdue/grace period state
        $this->billingService->processGracePeriodAndOverdue();

        // Invoices for this tenant
        $db = \Config\Database::connect();
        $invoices = $db->table('invoices')
            ->select('invoices.*, 
                contracts.start_date, contracts.end_date, contracts.agreed_price, contracts.payment_schedule,
                properties.name as property_name, properties.id as property_id,
                units.name as unit_name')
            ->join('contracts', 'contracts.id = invoices.contract_id')
            ->join('properties', 'properties.id = contracts.property_id')
            ->join('units', 'units.id = contracts.unit_id')
            ->where('invoices.tenant_id', $tenantId)
            ->orderBy('invoices.due_date', 'ASC')
            ->get()
            ->getResultArray();

        $activeInvoices   = [];
        $paidInvoices     = [];
        $upcomingInvoices = [];
        $totalDueAmount   = 0;
        $overdueCount     = 0;

        foreach ($invoices as &$inv) {
            $dueDate = $inv['due_date'];
            $dueTs   = strtotime($dueDate);
            $todayTs = strtotime($today);

            $inv['is_in_grace_period'] = false;
            $inv['grace_days_left']    = 0;

            if ($inv['status'] === 'pending') {
                if ($todayTs > $dueTs && $todayTs <= strtotime('+3 days', $dueTs)) {
                    $inv['is_in_grace_period'] = true;
                    $daysPast = (int) round(($todayTs - $dueTs) / 86400);
                    $inv['grace_days_left'] = max(1, 3 - $daysPast + 1);
                }
            }

            if (in_array($inv['status'], ['pending', 'overdue'])) {
                $activeInvoices[] = $inv;
                $totalDueAmount += (float) $inv['total_amount'];
                if ($inv['status'] === 'overdue') {
                    $overdueCount++;
                }
            } elseif ($inv['status'] === 'paid') {
                $paidInvoices[] = $inv;
            } elseif ($inv['status'] === 'upcoming') {
                $upcomingInvoices[] = $inv;
            }
        }

        // Recent applications
        $applications = $this->appModel->getApplicationsByTenant($tenantId);

        // Disputes
        $disputes = $this->disputeModel->getDisputesByTenant($tenantId);

        $stats = [
            'active_contracts_count' => count($activeContracts),
            'unpaid_invoices_count'  => count($activeInvoices),
            'total_due_amount'       => $totalDueAmount,
            'overdue_count'          => $overdueCount,
            'paid_invoices_count'    => count($paidInvoices),
        ];

        return view('tenant/dashboard', [
            'title'            => 'Dasbor Penyewa — KosAja',
            'stats'            => $stats,
            'activeContracts'  => $activeContracts,
            'activeInvoices'   => $activeInvoices,
            'upcomingInvoices' => $upcomingInvoices,
            'paidInvoices'     => $paidInvoices,
            'applications'     => array_slice($applications, 0, 5),
            'disputes'         => $disputes,
        ]);
    }

    /**
     * Settle & Pay an Invoice directly from tenant dashboard
     */
    public function payInvoice(int $id)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'))->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $tenantId = (int) session()->get('user_id');
        $invoice  = $this->invoiceModel->find($id);

        if (! $invoice || (int) $invoice['tenant_id'] !== $tenantId) {
            return redirect()->to(base_url('/tenant/dashboard'))->with('error', 'Tagihan tidak ditemukan atau Anda tidak berwenang.');
        }

        if ($invoice['status'] === 'paid') {
            return redirect()->to(base_url('/tenant/dashboard'))->with('info', 'Tagihan ini sudah lunas.');
        }

        $paymentMethod = $this->request->getPost('payment_method') ?? 'bsi_va';
        $result = $this->billingService->payInvoice($id, $paymentMethod);

        if ($result['success']) {
            return redirect()->to(base_url('/tenant/dashboard'))->with('success', 'Alhamdulillah! Pembayaran tagihan #' . $id . ' sebesar Rp ' . number_format($invoice['total_amount'], 0, ',', '.') . ' berhasil diverifikasi.');
        }

        return redirect()->to(base_url('/tenant/dashboard'))->with('error', 'Gagal memproses pembayaran: ' . ($result['message'] ?? 'Error internal.'));
    }

    /**
     * Tenant raises a dispute / complaint
     */
    public function createDispute()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'))->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $tenantId   = (int) session()->get('user_id');
        $contractId = (int) $this->request->getPost('contract_id');
        $invoiceId  = (int) $this->request->getPost('invoice_id');
        $title      = trim((string) $this->request->getPost('title'));
        $reason     = trim((string) $this->request->getPost('reason'));

        $contract = $this->contractModel->find($contractId);
        if (! $contract || (int) $contract['tenant_id'] !== $tenantId) {
            return redirect()->to(base_url('/tenant/dashboard'))->with('error', 'Kontrak tidak valid untuk pengajuan komplain.');
        }

        if (empty($title) || strlen($title) < 5) {
            return redirect()->to(base_url('/tenant/dashboard'))->with('error', 'Judul komplain minimal 5 karakter.');
        }

        if (empty($reason) || strlen($reason) < 10) {
            return redirect()->to(base_url('/tenant/dashboard'))->with('error', 'Penjelasan komplain minimal 10 karakter.');
        }

        $disputeId = $this->disputeModel->insert([
            'contract_id'     => $contractId,
            'invoice_id'      => $invoiceId > 0 ? $invoiceId : null,
            'raised_by_id'    => $tenantId,
            'against_user_id' => (int) $contract['provider_id'],
            'title'           => $title,
            'reason'          => $reason,
            'status'          => 'opened',
            'decision'        => 'none',
        ]);

        // Audit Log
        $this->auditModel->record(
            $tenantId,
            'DISPUTE_OPENED',
            'disputes',
            $disputeId,
            null,
            ['status' => 'opened', 'title' => $title, 'contract_id' => $contractId],
            "Penyewa mengajukan dispute/komplain #DSP-{$disputeId}: {$title}"
        );

        return redirect()->to(base_url('/tenant/dashboard'))->with('success', 'Komplain dispute Anda telah dicatat (#DSP-' . $disputeId . ') dan segera diinvestigasi oleh Admin Backoffice.');
    }
}
