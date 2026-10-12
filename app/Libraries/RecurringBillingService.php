<?php

namespace App\Libraries;

use App\Models\ContractModel;
use App\Models\InvoiceModel;
use App\Models\PaymentModel;
use App\Models\AuditLogModel;
use App\Models\NotificationLogModel;
use App\Libraries\WhatsAppService;

class RecurringBillingService
{
    protected ContractModel $contractModel;
    protected InvoiceModel $invoiceModel;
    protected PaymentModel $paymentModel;
    protected AuditLogModel $auditModel;
    protected NotificationLogModel $notifModel;
    protected WhatsAppService $whatsApp;

    public function __construct()
    {
        $this->contractModel = new ContractModel();
        $this->invoiceModel  = new InvoiceModel();
        $this->paymentModel  = new PaymentModel();
        $this->auditModel    = new AuditLogModel();
        $this->notifModel    = new NotificationLogModel();
        $this->whatsApp      = new WhatsAppService();
    }

    /**
     * Generate recurring billing schedule for a contract based on its payment schedule
     *
     * @param int|array $contract
     * @return array List of generated invoices
     */
    public function generateScheduleForContract($contract): array
    {
        if (is_numeric($contract)) {
            $contract = $this->contractModel->find((int) $contract);
        }

        if (! $contract || empty($contract['id'])) {
            return [];
        }

        $contractId      = (int) $contract['id'];
        $tenantId        = (int) $contract['tenant_id'];
        $startDate       = $contract['start_date'];
        $endDate         = $contract['end_date'];
        $agreedPrice     = (float) $contract['agreed_price'];
        $paymentSchedule = $contract['payment_schedule'] ?? 'monthly';
        $gatewayFee      = 4500.00;

        // Determine billing interval in months
        $intervalMonths = match($paymentSchedule) {
            'monthly'    => 1,
            '3_monthly'  => 3,
            '6_monthly'  => 6,
            'yearly'     => 12,
            'full_upfront' => 0, // only single invoice
            default      => 1,
        };

        if ($intervalMonths === 0) {
            // Full upfront is handled at checkout
            return [];
        }

        $startTs = strtotime($startDate);
        $endTs   = strtotime($endDate);

        // Calculate total months duration
        $totalMonths = (int) round(($endTs - $startTs) / (30 * 86400));
        if ($totalMonths <= 0) {
            $totalMonths = 1;
        }

        $existingInvoices = $this->invoiceModel->where('contract_id', $contractId)->findAll();
        $existingDueDates = array_column($existingInvoices, 'due_date');

        $generated = [];
        $currentMonthOffset = 0;
        $today = date('Y-m-d');

        while ($currentMonthOffset < $totalMonths) {
            $dueDate = date('Y-m-d', strtotime("+{$currentMonthOffset} months", $startTs));

            // Stop if due date exceeds contract end date
            if (strtotime($dueDate) >= $endTs) {
                break;
            }

            // If an invoice already exists for this due date, skip
            if (! in_array($dueDate, $existingDueDates)) {
                $rentalAmount = $agreedPrice * min($intervalMonths, $totalMonths - $currentMonthOffset);
                $totalAmount  = $rentalAmount + $gatewayFee;

                // Determine initial status
                $status = 'upcoming';
                if ($dueDate <= $today) {
                    $gracePeriodEnd = date('Y-m-d', strtotime('+3 days', strtotime($dueDate)));
                    if ($today > $gracePeriodEnd) {
                        $status = 'overdue';
                    } else {
                        $status = 'pending';
                    }
                } elseif (strtotime($dueDate) <= strtotime('+7 days', strtotime($today))) {
                    $status = 'pending';
                }

                $invoiceData = [
                    'contract_id'   => $contractId,
                    'tenant_id'     => $tenantId,
                    'rental_amount' => $rentalAmount,
                    'gateway_fee'   => $gatewayFee,
                    'total_amount'  => $totalAmount,
                    'due_date'      => $dueDate,
                    'status'        => $status,
                ];

                $invId = $this->invoiceModel->insert($invoiceData);
                $invoiceData['id'] = $invId;
                $generated[] = $invoiceData;
                $existingDueDates[] = $dueDate;
            }

            $currentMonthOffset += $intervalMonths;
        }

        return $generated;
    }

    /**
     * Run billing schedule generation for all active contracts
     */
    public function generateSchedulesForAllActiveContracts(): int
    {
        $contracts = $this->contractModel->where('status', 'active')->findAll();
        $totalGenerated = 0;

        foreach ($contracts as $contract) {
            $created = $this->generateScheduleForContract($contract);
            $totalGenerated += count($created);
        }

        return $totalGenerated;
    }

    /**
     * Process Grace Period (3 days) and transition overdue invoices
     * FR-LATE-001: Grace period = 3 hari. Overdue occurs on day 4 after due date.
     */
    public function processGracePeriodAndOverdue(): array
    {
        $today = date('Y-m-d');
        $invoices = $this->invoiceModel->whereIn('status', ['pending', 'upcoming'])->findAll();

        $markedOverdue       = 0;
        $inGracePeriod       = 0;
        $activatedToPending  = 0;

        foreach ($invoices as $inv) {
            $dueDate = $inv['due_date'];
            $dueTs   = strtotime($dueDate);
            $todayTs = strtotime($today);

            // 1. Activate upcoming invoices that are due within 7 days
            if ($inv['status'] === 'upcoming' && $todayTs >= strtotime('-7 days', $dueTs)) {
                $this->invoiceModel->update($inv['id'], ['status' => 'pending']);
                $activatedToPending++;
                $inv['status'] = 'pending';
            }

            if ($inv['status'] === 'pending') {
                $daysPastDue = (int) round(($todayTs - $dueTs) / 86400);

                if ($daysPastDue > 3) {
                    // Grace period expired (3 days over): Transition to OVERDUE
                    $this->invoiceModel->update($inv['id'], ['status' => 'overdue']);
                    $markedOverdue++;

                    // Record Audit Log
                    $this->auditModel->record(
                        (int) $inv['tenant_id'],
                        'INVOICE_OVERDUE',
                        'invoices',
                        (int) $inv['id'],
                        ['status' => 'pending'],
                        ['status' => 'overdue'],
                        "Masa tenggang (grace period 3 hari) berakhir untuk invoice #INV-{$inv['id']}."
                    );

                    // Send WhatsApp Overdue Alert
                    $details = $this->invoiceModel->getInvoiceWithDetails($inv['id']);
                    if ($details) {
                        $this->whatsApp->sendInvoiceOverdueNotification($details);
                    }
                } elseif ($daysPastDue >= 1 && $daysPastDue <= 3) {
                    // Currently inside 3-day grace period
                    $inGracePeriod++;
                    $daysRemaining = 3 - $daysPastDue + 1;
                    $details = $this->invoiceModel->getInvoiceWithDetails($inv['id']);
                    if ($details) {
                        $this->whatsApp->sendInvoiceGracePeriodNotification($details, $daysRemaining);
                    }
                }
            }
        }

        return [
            'marked_overdue'      => $markedOverdue,
            'in_grace_period'     => $inGracePeriod,
            'activated_pending'   => $activatedToPending,
        ];
    }

    /**
     * Dispatch WhatsApp Reminder H-3 Before Due Date
     */
    public function dispatchH3DueReminders(): array
    {
        $targetDate = date('Y-m-d', strtotime('+3 days'));
        
        $invoices = $this->invoiceModel
            ->whereIn('status', ['pending', 'upcoming'])
            ->where('due_date', $targetDate)
            ->findAll();

        $sentCount = 0;
        $sentList  = [];

        foreach ($invoices as $inv) {
            $details = $this->invoiceModel->getInvoiceWithDetails($inv['id']);
            if (! $details) {
                continue;
            }

            // Check if reminder was already sent today for this invoice
            $alreadySent = $this->notifModel
                ->where('recipient_phone', $details['tenant_whatsapp'])
                ->where('event_type', 'invoice_reminder_h3')
                ->where('created_at >=', date('Y-m-d 00:00:00'))
                ->first();

            if (! $alreadySent) {
                $result = $this->whatsApp->sendInvoiceReminderH3Notification($details);
                if ($result['success']) {
                    $sentCount++;
                    $sentList[] = [
                        'invoice_id' => $inv['id'],
                        'recipient'  => $details['tenant_whatsapp'],
                        'tenant'     => $details['tenant_name'],
                        'due_date'   => $inv['due_date'],
                    ];
                }
            }
        }

        return [
            'target_date' => $targetDate,
            'sent_count'  => $sentCount,
            'sent_list'   => $sentList,
        ];
    }

    /**
     * Settle & Pay an Invoice
     */
    public function payInvoice(int $invoiceId, string $paymentMethod = 'bsi_va', ?string $gatewayTrxId = null): array
    {
        $invoice = $this->invoiceModel->find($invoiceId);
        if (! $invoice) {
            return ['success' => false, 'message' => 'Invoice tidak ditemukan.'];
        }

        if ($invoice['status'] === 'paid') {
            return ['success' => false, 'message' => 'Invoice sudah dibayar.'];
        }

        $now = date('Y-m-d H:i:s');
        $gatewayTrxId = $gatewayTrxId ?? ('PAY-REC-' . $invoiceId . '-' . time());

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Update invoice status
        $this->invoiceModel->update($invoiceId, [
            'status'     => 'paid',
            'updated_at' => $now,
        ]);

        // 2. Create Payment record
        $paymentData = [
            'invoice_id'     => $invoiceId,
            'amount_paid'    => $invoice['total_amount'],
            'gateway_trx_id' => $gatewayTrxId,
            'payment_method' => $paymentMethod,
            'status'         => 'success',
            'paid_at'        => $now,
        ];
        $paymentId = $this->paymentModel->insert($paymentData);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return ['success' => false, 'message' => 'Gagal memproses transaksi database.'];
        }

        // 3. Record Audit Log
        $this->auditModel->record(
            (int) $invoice['tenant_id'],
            'INVOICE_PAYMENT_SUCCESS',
            'invoices',
            $invoiceId,
            ['status' => $invoice['status']],
            ['status' => 'paid', 'payment_id' => $paymentId],
            "Pembayaran tagihan rutin #INV-{$invoiceId} sebesar Rp " . number_format($invoice['total_amount'], 0, ',', '.') . " sukses."
        );

        // 4. FR-PM-002 & FR-PM-004: Check & Complete Provider Free Period early if first rental paid
        $contract = $this->contractModel->find($invoice['contract_id']);
        if ($contract && ! empty($contract['provider_id'])) {
            $providerProfileModel = new \App\Models\ProviderProfileModel();
            $providerProfileModel->completeFreePeriodIfFirstRentalPaid((int) $contract['provider_id']);
        }

        return [
            'success'    => true,
            'message'    => 'Pembayaran tagihan berhasil diverifikasi.',
            'payment_id' => $paymentId,
            'invoice_id' => $invoiceId,
        ];
    }
}
