<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\PaymentGatewayService;
use App\Libraries\WhatsAppService;
use App\Models\ReservationModel;
use App\Models\RentalApplicationModel;
use App\Models\ContractModel;
use App\Models\InvoiceModel;
use App\Models\PaymentModel;
use App\Models\UnitModel;
use App\Models\AuditLogModel;
use CodeIgniter\HTTP\ResponseInterface;

class Webhook extends BaseController
{
    protected PaymentGatewayService $paymentGateway;
    protected WhatsAppService $whatsappService;
    protected ReservationModel $reservationModel;
    protected RentalApplicationModel $applicationModel;
    protected ContractModel $contractModel;
    protected InvoiceModel $invoiceModel;
    protected PaymentModel $paymentModel;
    protected UnitModel $unitModel;
    protected AuditLogModel $auditModel;

    public function __construct()
    {
        $this->paymentGateway    = new PaymentGatewayService();
        $this->whatsappService   = new WhatsAppService();
        $this->reservationModel  = new ReservationModel();
        $this->applicationModel  = new RentalApplicationModel();
        $this->contractModel     = new ContractModel();
        $this->invoiceModel      = new InvoiceModel();
        $this->paymentModel      = new PaymentModel();
        $this->unitModel         = new UnitModel();
        $this->auditModel        = new AuditLogModel();
    }

    /**
     * Handle incoming Payment Gateway Webhook
     * Route: POST /webhook/payment
     */
    public function payment(): ResponseInterface
    {
        $request  = $this->request ?? service('request');
        $response = $this->response ?? service('response');

        $rawInput = null;
        if (method_exists($request, 'getJSON')) {
            $rawInput = $request->getJSON(true);
        }
        if (empty($rawInput) && method_exists($request, 'getPost')) {
            $rawInput = $request->getPost();
        }
        if (empty($rawInput) && !empty($_POST)) {
            $rawInput = $_POST;
        }
        if (empty($rawInput)) {
            $phpInput = @file_get_contents('php://input');
            if (!empty($phpInput)) {
                $rawInput = json_decode($phpInput, true);
            }
        }
        $rawInput = $rawInput ?? [];

        $parsed = $this->paymentGateway->parseWebhookPayload($rawInput);
        $orderId = $parsed['order_id'];

        if (empty($orderId)) {
            return $response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Missing order_id in webhook payload.',
            ]);
        }

        // Extract reservation ID from order_id format (KOS-{resId}-{timestamp})
        preg_match('/KOS-(\d+)-/i', $orderId, $matches);
        $resId = isset($matches[1]) ? (int) $matches[1] : (int) ($_GET['res_id'] ?? (method_exists($request, 'getGet') ? $request->getGet('res_id') : 0));

        if (! $resId) {
            return $response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Unable to determine reservation ID from order_id: ' . $orderId,
            ]);
        }

        $reservation = $this->reservationModel->getReservationWithDetails($resId);
        if (! $reservation) {
            return $response->setStatusCode(404)->setJSON([
                'success' => false,
                'message' => 'Reservation not found for ID: ' . $resId,
            ]);
        }

        // Handle FAILED or CANCELLED status
        if ($parsed['status'] === 'FAILED') {
            $this->auditModel->record(
                null,
                'PAYMENT_FAILED',
                'reservations',
                $resId,
                ['status' => $reservation['status']],
                ['status' => 'payment_failed', 'trx_id' => $parsed['gateway_trx_id']],
                'Pembayaran transaksi digital gagal atau dibatalkan oleh penyedia gateway.'
            );

            return $response->setJSON([
                'success' => true,
                'message' => 'Payment failure logged.',
                'status'  => 'FAILED',
            ]);
        }

        // Handle SUCCESS (Settlement)
        if ($parsed['status'] === 'SUCCESS') {
            // Check if contract already generated for this application to maintain idempotency
            $existingContract = $this->contractModel->where('application_id', $reservation['application_id'])->first();
            if ($existingContract) {
                return $response->setJSON([
                    'success'     => true,
                    'message'     => 'Payment already processed and contract already exists.',
                    'contract_id' => $existingContract['id'],
                ]);
            }

            $db = \Config\Database::connect();
            $db->transStart();

            $now = date('Y-m-d H:i:s');
            $startDate = $reservation['start_date'];
            $durationMonths = (int) $reservation['duration_months'];
            $endDate = date('Y-m-d', strtotime("+{$durationMonths} months", strtotime($startDate)));

            // 1. Create CONTRACT
            $contractData = [
                'application_id'   => $reservation['application_id'],
                'tenant_id'        => $reservation['tenant_id'],
                'provider_id'      => $reservation['provider_id'],
                'property_id'      => $reservation['property_id'],
                'unit_id'          => $reservation['unit_id'],
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'agreed_price'     => $reservation['rental_amount'],
                'payment_schedule' => $reservation['payment_schedule'],
                'status'           => 'active',
            ];
            $contractId = $this->contractModel->insert($contractData);

            // 2. Create first INVOICE
            $rentalAmount = (float) $reservation['rental_amount'];
            $gatewayFee   = (float) ($reservation['gateway_fee'] > 0 ? $reservation['gateway_fee'] : 4500);
            $totalAmount  = (float) ($parsed['amount'] > 0 ? $parsed['amount'] : ($rentalAmount + $gatewayFee));

            $invoiceData = [
                'contract_id'   => $contractId,
                'tenant_id'     => $reservation['tenant_id'],
                'rental_amount' => $rentalAmount,
                'gateway_fee'   => $gatewayFee,
                'total_amount'  => $totalAmount,
                'due_date'      => $startDate,
                'status'        => 'paid',
            ];
            $invoiceId = $this->invoiceModel->insert($invoiceData);

            // 3. Create PAYMENT record
            $paymentData = [
                'invoice_id'     => $invoiceId,
                'amount_paid'    => $totalAmount,
                'gateway_trx_id' => $parsed['gateway_trx_id'],
                'payment_method' => $parsed['payment_method'],
                'status'         => 'success',
                'paid_at'        => $now,
            ];
            $paymentId = $this->paymentModel->insert($paymentData);

            // 4. State Transitions:
            // - Reservation -> CONVERTED
            $this->reservationModel->update($resId, [
                'status'     => 'converted',
                'updated_at' => $now,
            ]);

            // - Unit -> OCCUPIED (Rental Active)
            $this->unitModel->update($reservation['unit_id'], [
                'status'     => 'occupied',
                'updated_at' => $now,
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Database transaction failed during contract generation.',
                ]);
            }

            // 5. Audit Logging
            $this->auditModel->record(
                (int) $reservation['tenant_id'],
                'PAYMENT_SUCCESS',
                'payments',
                $paymentId,
                null,
                $paymentData,
                'Pembayaran termin awal Rp ' . number_format($totalAmount, 0, ',', '.') . ' berhasil diverifikasi.'
            );

            $this->auditModel->record(
                (int) $reservation['tenant_id'],
                'CREATE_CONTRACT',
                'contracts',
                $contractId,
                null,
                $contractData,
                'Kontrak sewa resmi terbentuk otomatis setelah pembayaran pertama sukses.'
            );

            $this->auditModel->record(
                null,
                'RENTAL_ACTIVE',
                'units',
                (int) $reservation['unit_id'],
                ['status' => 'reserved'],
                ['status' => 'occupied'],
                'Unit kamar ' . $reservation['unit_name'] . ' kini aktif ditempati penyewa ' . $reservation['tenant_name']
            );

            // 6. Generate Recurring Invoices Billing Schedule for the entire contract term
            $billingService = new \App\Libraries\RecurringBillingService();
            $billingService->generateScheduleForContract($contractId);

            // 7. FR-PM-002 & FR-PM-004: Complete Provider Free Period early if first rental payment
            $providerProfileModel = new \App\Models\ProviderProfileModel();
            $completedTrial = $providerProfileModel->completeFreePeriodIfFirstRentalPaid((int) $reservation['provider_id']);
            if ($completedTrial) {
                $this->auditModel->record(
                    (int) $reservation['provider_id'],
                    'PROVIDER_FREE_PERIOD_CONVERTED',
                    'provider_profiles',
                    (int) $reservation['provider_id'],
                    null,
                    ['first_contract_id' => $contractId, 'first_invoice_id' => $invoiceId],
                    'Masa Free Period onboarding provider selesai lebih awal karena transaksi sewa pertama berhasil lunas (FR-PM-002). Masa transisi 3 hari aktif (FR-PM-004).'
                );
            }

            // 7. WhatsApp Critical Notifications
            $fullContract = $this->contractModel->getContractWithDetails($contractId);
            $this->whatsappService->sendPaymentSuccessNotifications($fullContract, $invoiceData, $paymentData);

            return $response->setJSON([
                'success'     => true,
                'message'     => 'Payment processed successfully, contract created and unit marked occupied.',
                'contract_id' => $contractId,
                'status'      => 'SUCCESS',
            ]);
        }

        return $response->setJSON([
            'success' => true,
            'message' => 'Webhook received with status: ' . $parsed['status'],
        ]);
    }
}
