<?php

namespace App\Controllers\Provider;

use App\Controllers\BaseController;
use App\Models\SubscriptionPlanModel;
use App\Models\ProviderSubscriptionModel;
use App\Models\ProviderInvoiceModel;
use App\Models\ListingModel;
use App\Models\UserModel;
use App\Libraries\PaymentGatewayService;

class Subscription extends BaseController
{
    protected SubscriptionPlanModel $planModel;
    protected ProviderSubscriptionModel $subscriptionModel;
    protected ProviderInvoiceModel $invoiceModel;
    protected ListingModel $listingModel;
    protected UserModel $userModel;
    protected PaymentGatewayService $paymentGateway;

    public function __construct()
    {
        $this->planModel         = new SubscriptionPlanModel();
        $this->subscriptionModel = new ProviderSubscriptionModel();
        $this->invoiceModel      = new ProviderInvoiceModel();
        $this->listingModel      = new ListingModel();
        $this->userModel         = new UserModel();
        $this->paymentGateway    = new PaymentGatewayService();
    }

    /**
     * Display Subscription & Listing Fee Catalog, Status, and Invoices
     */
    public function index()
    {
        $providerId = (int) (session()->get('user_id') ?? 3);

        // Auto-expire pending invoices that passed due_date
        $this->invoiceModel->where('provider_id', $providerId)
            ->where('status', 'pending')
            ->where('due_date <', date('Y-m-d'))
            ->set(['status' => 'expired'])
            ->update();

        // 1. Quota & Status
        $quota = $this->subscriptionModel->getEffectiveQuota($providerId);

        // 2. Count Active / Published listings vs Total
        $allListings = $this->listingModel->getListingsByProvider($providerId);
        $publishedCount = count(array_filter($allListings, fn($l) => $l['publication_status'] === 'published'));
        $totalListings = count($allListings);

        // 3. Plans Catalog
        $listingFeePlans = $this->planModel->getPlansByType('listing_fee');
        $subscriptionPlans = $this->planModel->getPlansByType('subscription');

        // 4. Invoices History
        $invoices = $this->invoiceModel->getInvoicesByProvider($providerId);

        $paymentConfig = config('PaymentConfig') ?? new \Config\PaymentConfig();

        return view('provider/subscription/index', [
            'title'             => 'Paket & Langganan — KosAja',
            'role'              => 'provider',
            'activeMenu'        => 'subscription',
            'quota'             => $quota,
            'publishedCount'    => $publishedCount,
            'totalListings'     => $totalListings,
            'listingFeePlans'   => $listingFeePlans,
            'subscriptionPlans' => $subscriptionPlans,
            'invoices'          => $invoices,
            'paymentConfig'     => $paymentConfig,
        ]);
    }

    /**
     * Initiate checkout for a subscription plan or listing fee
     */
    public function checkout(int $planId)
    {
        $providerId = (int) (session()->get('user_id') ?? 3);
        $plan = $this->planModel->find($planId);

        if (! $plan || ! $plan['is_active']) {
            return redirect()->back()->with('error', 'Paket yang dipilih tidak valid atau sudah dinonaktifkan.');
        }

        $provider = $this->userModel->find($providerId) ?? [
            'id'       => $providerId,
            'name'     => session()->get('name') ?? 'Mitra KosAja',
            'email'    => session()->get('email') ?? 'mitra@kosaja.id',
            'whatsapp' => '08123456789',
        ];

        // Create new pending invoice
        $invoiceNumber = $this->invoiceModel->generateInvoiceNumber();
        $orderId       = $this->invoiceModel->generateOrderId($providerId, $planId);
        $dueDate       = date('Y-m-d', strtotime('+1 day'));

        $invoiceId = $this->invoiceModel->insert([
            'invoice_number' => $invoiceNumber,
            'order_id'       => $orderId,
            'provider_id'    => $providerId,
            'plan_id'        => $planId,
            'amount'         => $plan['price'],
            'status'         => 'pending',
            'due_date'       => $dueDate,
        ]);

        $invoice = $this->invoiceModel->find($invoiceId);

        // Request payment transaction
        $paymentResult = $this->paymentGateway->createSubscriptionPaymentTransaction($invoice, $plan, $provider);

        if (! empty($paymentResult['snap_token'])) {
            $this->invoiceModel->update($invoiceId, [
                'snap_token' => $paymentResult['snap_token'],
            ]);
        }

        // If AJAX request, return JSON
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'      => true,
                'invoice_id'   => $invoiceId,
                'order_id'     => $orderId,
                'snap_token'   => $paymentResult['snap_token'] ?? null,
                'redirect_url' => $paymentResult['redirect_url'] ?? base_url('/provider/subscription/simulate/' . $invoiceId),
            ]);
        }

        // Redirect to simulation or payment redirect url
        if (! empty($paymentResult['redirect_url'])) {
            return redirect()->to($paymentResult['redirect_url']);
        }

        return redirect()->to(base_url('/provider/subscription/simulate/' . $invoiceId));
    }

    /**
     * Show Simulation Payment Screen for Sandbox Testing
     */
    public function simulate(int $invoiceId)
    {
        $providerId = (int) (session()->get('user_id') ?? 3);
        $invoice = $this->invoiceModel->find($invoiceId);

        if (! $invoice || (int) $invoice['provider_id'] !== $providerId) {
            return redirect()->to(base_url('/provider/subscription'))->with('error', 'Tagihan tidak ditemukan.');
        }

        $plan = $this->planModel->find($invoice['plan_id']);

        return view('provider/subscription/simulate', [
            'title'      => 'Simulasi Pembayaran Paket — KosAja',
            'role'       => 'provider',
            'activeMenu' => 'subscription',
            'invoice'    => $invoice,
            'plan'       => $plan,
        ]);
    }

    /**
     * Execute simulation payment and trigger webhook internally
     */
    public function executeSimulation(int $invoiceId)
    {
        $providerId = (int) (session()->get('user_id') ?? 3);
        $invoice = $this->invoiceModel->find($invoiceId);

        if (! $invoice || (int) $invoice['provider_id'] !== $providerId) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Tagihan tidak ditemukan.',
                ]);
            }
            return redirect()->to(base_url('/provider/subscription'))->with('error', 'Tagihan tidak ditemukan.');
        }

        $action = $this->request->getPost('action') ?? 'success';

        $payload = [
            'order_id'       => $invoice['order_id'],
            'status'         => ($action === 'success') ? 'SUCCESS' : 'FAILED',
            'amount'         => (float) $invoice['amount'],
            'payment_method' => $this->request->getPost('payment_method') ?? 'bank_transfer_va',
            'gateway_trx_id' => 'SIM-SUB-' . strtoupper(bin2hex(random_bytes(5))),
        ];

        // Call Webhook controller internally
        $webhook = new \App\Controllers\Webhook();
        $webhook->payment($payload);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success'      => ($action === 'success'),
                'message'      => ($action === 'success') ? 'Pembayaran berhasil diproses! Kuota paket telah aktif.' : 'Pembayaran dibatalkan atau gagal.',
                'redirect_url' => base_url('/provider/subscription'),
            ]);
        }

        if ($action === 'success') {
            return redirect()->to(base_url('/provider/subscription'))->with('success', 'Pembayaran berhasil diproses! Kuota paket telah aktif.');
        }

        return redirect()->to(base_url('/provider/subscription'))->with('error', 'Pembayaran dibatalkan atau gagal.');
    }

    /**
     * Cancel or delete a pending, expired, or cancelled provider invoice
     */
    public function cancelInvoice(int $invoiceId)
    {
        $providerId = (int) (session()->get('user_id') ?? 3);
        $invoice = $this->invoiceModel->find($invoiceId);

        if (! $invoice || (int) $invoice['provider_id'] !== $providerId) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Tagihan tidak ditemukan.',
                ]);
            }
            return redirect()->to(base_url('/provider/subscription'))->with('error', 'Tagihan tidak ditemukan.');
        }

        if (! in_array($invoice['status'], ['pending', 'expired', 'cancelled'])) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'message' => 'Tagihan ini tidak dapat dibatalkan atau dihapus karena sudah diproses atau lunas.',
                ]);
            }
            return redirect()->to(base_url('/provider/subscription'))->with('error', 'Tagihan ini tidak dapat dibatalkan atau dihapus karena sudah diproses atau lunas.');
        }

        $action = $this->request->getPost('action') ?? 'cancel';

        if ($action === 'delete' || $invoice['status'] === 'cancelled' || $invoice['status'] === 'expired') {
            $this->invoiceModel->delete($invoiceId);
            $msg = 'Tagihan ' . esc($invoice['invoice_number']) . ' berhasil dihapus dari riwayat.';
        } else {
            $this->invoiceModel->update($invoiceId, [
                'status' => 'cancelled',
            ]);
            $msg = 'Tagihan ' . esc($invoice['invoice_number']) . ' berhasil dibatalkan.';
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $msg,
            ]);
        }

        return redirect()->to(base_url('/provider/subscription'))->with('success', $msg);
    }

    /**
     * View/Print Invoice Receipt
     */
    public function invoiceReceipt(int $invoiceId)
    {
        $providerId = (int) (session()->get('user_id') ?? 3);
        $invoice = $this->invoiceModel->find($invoiceId);

        if (! $invoice || (int) $invoice['provider_id'] !== $providerId) {
            return redirect()->to(base_url('/provider/subscription'))->with('error', 'Tagihan tidak ditemukan.');
        }

        $plan = $this->planModel->find($invoice['plan_id']);
        $provider = $this->userModel->find($providerId) ?? [
            'name'     => session()->get('name') ?? 'Mitra KosAja',
            'email'    => session()->get('email') ?? 'mitra@kosaja.id',
            'whatsapp' => '08123456789',
        ];

        $paymentModel = new \App\Models\ProviderPaymentModel();
        $payments = $paymentModel->getByInvoiceId($invoiceId);
        $payment = !empty($payments) ? $payments[0] : null;

        return view('provider/subscription/invoice_print', [
            'title'    => 'Kuitansi Invoice ' . $invoice['invoice_number'] . ' — KosAja',
            'invoice'  => $invoice,
            'plan'     => $plan,
            'provider' => $provider,
            'payment'  => $payment,
        ]);
    }
}
