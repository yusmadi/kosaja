<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RentalApplicationModel;
use App\Models\ReservationModel;
use App\Models\AuditLogModel;
use App\Libraries\PaymentGatewayService;
use Config\ReservationConfig;

class Checkout extends BaseController
{
    protected RentalApplicationModel $applicationModel;
    protected ReservationModel $reservationModel;
    protected AuditLogModel $auditModel;
    protected ReservationConfig $reservationConfig;
    protected PaymentGatewayService $paymentGateway;

    public function __construct()
    {
        $this->applicationModel  = new RentalApplicationModel();
        $this->reservationModel  = new ReservationModel();
        $this->auditModel        = new AuditLogModel();
        $this->reservationConfig = config('ReservationConfig') ?? new ReservationConfig();
        $this->paymentGateway    = new PaymentGatewayService();
    }

    /**
     * Show Checkout Page for an approved reservation
     */
    public function show(int $reservationId)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'))->with('error', 'Silakan masuk terlebih dahulu.');
        }

        // Trigger reservation expiration sweep
        $this->reservationModel->checkAndExpireReservations();

        $userId = (int) session()->get('user_id');
        $reservation = $this->reservationModel->getReservationWithDetails($reservationId);

        if (! $reservation) {
            return redirect()->to(base_url('/applications'))->with('error', 'Data reservasi tidak ditemukan atau telah kedaluwarsa.');
        }

        // Check ownership
        if ((int) $reservation['tenant_id'] !== $userId && ! in_array(session()->get('role'), ['super_admin', 'admin_daerah'])) {
            return redirect()->to(base_url('/applications'))->with('error', 'Akses ditolak.');
        }

        // Check reservation status
        if ($reservation['status'] !== 'active') {
            return redirect()->to(base_url('/applications/' . $reservation['application_id']))
                             ->with('info', 'Status reservasi saat ini: ' . strtoupper($reservation['status']) . '. Checkout hanya dapat diakses saat reservasi berstatus aktif.');
        }

        // Financial Calculation (Separation of Rental Amount and Gateway Fee per FR-PAY-002)
        $rentalAmount = (float) $reservation['rental_amount'];
        $gatewayFee   = (float) ($reservation['gateway_fee'] > 0 ? $reservation['gateway_fee'] : $this->reservationConfig->gatewayFee);
        $totalAmount  = $rentalAmount + $gatewayFee;

        // Calculate remaining seconds until expiration
        $expiresTimestamp = strtotime($reservation['expires_at']);
        $remainingSeconds = max(0, $expiresTimestamp - time());

        return view('tenant/checkout/show', [
            'title'             => 'Checkout Pembayaran Sewa — ' . esc($reservation['property_name']),
            'reservation'       => $reservation,
            'rentalAmount'      => $rentalAmount,
            'gatewayFee'        => $gatewayFee,
            'totalAmount'       => $totalAmount,
            'remainingSeconds'  => $remainingSeconds,
            'reservationConfig' => $this->reservationConfig,
        ]);
    }

    /**
     * Process checkout and initialize payment transaction
     */
    public function process(int $reservationId)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'));
        }

        $userId = (int) session()->get('user_id');
        $reservation = $this->reservationModel->getReservationWithDetails($reservationId);

        if (! $reservation || (int) $reservation['tenant_id'] !== $userId) {
            return redirect()->to(base_url('/applications'))->with('error', 'Data tidak valid.');
        }

        if ($reservation['status'] !== 'active') {
            return redirect()->to(base_url('/applications/' . $reservation['application_id']))->with('error', 'Reservasi ini telah kedaluwarsa atau tidak aktif.');
        }

        $paymentMethod = $this->request->getPost('payment_method') ?? 'bsi_va';

        // Call payment gateway service
        $trx = $this->paymentGateway->createPaymentTransaction($reservation, $paymentMethod);

        // Log checkout initiation
        $this->auditModel->record(
            $userId,
            'CHECKOUT_INITIATED',
            'reservations',
            $reservationId,
            ['status' => 'active'],
            ['order_id' => $trx['order_id'], 'payment_method' => $paymentMethod],
            'Penyewa memulai proses pembayaran digital via: ' . $paymentMethod
        );

        if (isset($trx['redirect_url'])) {
            return redirect()->to($trx['redirect_url']);
        }

        return redirect()->to(base_url('/checkout/simulation/' . $reservationId . '?order_id=' . $trx['order_id'] . '&method=' . $paymentMethod));
    }

    /**
     * Interactive Sandbox Payment Screen for development and demonstration
     */
    public function simulation(int $reservationId)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'));
        }

        $userId = (int) session()->get('user_id');
        $reservation = $this->reservationModel->getReservationWithDetails($reservationId);

        if (! $reservation || (int) $reservation['tenant_id'] !== $userId) {
            return redirect()->to(base_url('/applications'))->with('error', 'Data tidak valid.');
        }

        $orderId       = $this->request->getGet('order_id') ?? ('KOS-' . $reservationId . '-' . date('ymdHis'));
        $paymentMethod = $this->request->getGet('method') ?? 'bsi_va';
        $rentalAmount  = (float) $reservation['rental_amount'];
        $gatewayFee    = (float) ($reservation['gateway_fee'] > 0 ? $reservation['gateway_fee'] : 4500);
        $totalAmount   = $rentalAmount + $gatewayFee;

        $vaNumber = match($paymentMethod) {
            'bsi_va' => '7123 ' . str_pad($reservationId, 4, '0', STR_PAD_LEFT) . ' 9988',
            'bca_va' => '8077 ' . str_pad($reservationId, 4, '0', STR_PAD_LEFT) . ' 1234',
            default  => '9988 ' . str_pad($reservationId, 4, '0', STR_PAD_LEFT) . ' 5678',
        };

        return view('tenant/checkout/simulation', [
            'title'         => 'Simulator Pembayaran Sandbox — KosAja',
            'reservation'   => $reservation,
            'orderId'       => $orderId,
            'paymentMethod' => $paymentMethod,
            'rentalAmount'  => $rentalAmount,
            'gatewayFee'    => $gatewayFee,
            'totalAmount'   => $totalAmount,
            'vaNumber'      => $vaNumber,
        ]);
    }

    /**
     * Trigger simulation webhook from test UI
     */
    public function executeSimulation(int $reservationId)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'));
        }

        $userId = (int) session()->get('user_id');
        $reservation = $this->reservationModel->getReservationWithDetails($reservationId);

        if (! $reservation || (int) $reservation['tenant_id'] !== $userId) {
            return redirect()->to(base_url('/applications'))->with('error', 'Data reservasi tidak valid.');
        }

        $orderId = $this->request->getPost('order_id') ?? ('KOS-' . $reservationId . '-' . date('ymdHis'));
        $amount  = (float) ($this->request->getPost('amount') ?? ($reservation['rental_amount'] + ($reservation['gateway_fee'] > 0 ? $reservation['gateway_fee'] : 4500)));
        $method  = $this->request->getPost('payment_method') ?? 'bsi_va';
        $outcome = $this->request->getPost('outcome') ?? $this->request->getPost('status') ?? 'SUCCESS';

        $payload = [
            'order_id'       => $orderId,
            'status'         => $outcome,
            'outcome'        => $outcome,
            'amount'         => $amount,
            'payment_method' => $method,
            'gateway_trx_id' => 'SIM-MID-' . strtoupper(bin2hex(random_bytes(4))),
        ];

        $webhookController = new Webhook();
        $response = $webhookController->payment($payload);
        $json = json_decode($response->getBody(), true);

        if (isset($json['contract_id'])) {
            return redirect()->to(base_url('/contracts/' . $json['contract_id']))
                             ->with('success', 'Alhamdulillah! Pembayaran berhasil diverifikasi secara instan. Kontrak sewa resmi Anda telah AKTIF.');
        }

        if ($outcome === 'FAILED') {
            return redirect()->to(base_url('/checkout/' . $reservationId))
                             ->with('error', 'Simulasi: Pembayaran transaksi dibatalkan / gagal.');
        }

        return redirect()->to(base_url('/applications/' . $reservation['application_id']));
    }
}
