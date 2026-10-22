<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\SubscriptionPlanModel;
use App\Models\ProviderSubscriptionModel;
use App\Models\ProviderInvoiceModel;
use App\Models\ListingModel;
use App\Models\AuditLogModel;
use App\Controllers\Webhook;

class TestSubscriptionBilling extends BaseCommand
{
    protected $group       = 'KosAja';
    protected $name        = 'test:billing';
    protected $description = 'UAT & Verifikasi End-to-End Modul Billing & Subscription Provider KosAja.';

    public function run(array $params)
    {
        CLI::write("==================================================================", 'yellow');
        CLI::write("   KOSAJA: PENGUJIAN END-TO-END MODUL BILLING & SUBSCRIPTION      ", 'yellow');
        CLI::write("==================================================================", 'yellow');

        $subModel     = new ProviderSubscriptionModel();
        $planModel    = new SubscriptionPlanModel();
        $invoiceModel = new ProviderInvoiceModel();
        $listingModel = new ListingModel();
        $auditModel   = new AuditLogModel();
        $testProviderId = 3; // Haji Ahmad Santoso

        // ---------------------------------------------------------
        // TEST 1: Verifikasi Katalog Paket Tersedia & Terisi
        // ---------------------------------------------------------
        CLI::write("\n[1] Verifikasi Katalog Paket (Listing Fee & Subscription)...", 'cyan');
        $listingFeePlans   = $planModel->getPlansByType('listing_fee');
        $subscriptionPlans = $planModel->getPlansByType('subscription');

        if (count($listingFeePlans) === 5 && count($subscriptionPlans) === 4) {
            CLI::write("  [PASS] 5 Paket Listing Fee (15 Hari) dan 4 Paket Subscription Bulanan (30 Hari) lengkap.", 'green');
        } else {
            CLI::write("  [FAIL] Jumlah paket tidak sesuai. Listing Fee: " . count($listingFeePlans) . ", Subscription: " . count($subscriptionPlans), 'red');
            return;
        }

        // ---------------------------------------------------------
        // TEST 2: Simulasi Pembelian Listing Fee 10 (Rp 25.000, 15 Hari)
        // ---------------------------------------------------------
        CLI::write("\n[2] Simulasi Order & Invoicing Listing Fee 10...", 'cyan');
        $plan10 = $planModel->where('slug', 'listing-fee-10')->first();
        if (! $plan10) {
            CLI::write("  [FAIL] Paket listing-fee-10 tidak ditemukan.", 'red');
            return;
        }

        $invNumber = $invoiceModel->generateInvoiceNumber();
        $orderId   = $invoiceModel->generateOrderId($testProviderId, (int) $plan10['id']);
        $invId = $invoiceModel->insert([
            'invoice_number' => $invNumber,
            'order_id'       => $orderId,
            'provider_id'    => $testProviderId,
            'plan_id'        => $plan10['id'],
            'amount'         => $plan10['price'],
            'status'         => 'pending',
            'due_date'       => date('Y-m-d', strtotime('+1 day')),
        ]);

        $createdInv = $invoiceModel->find($invId);
        if ($createdInv && $createdInv['status'] === 'pending' && (float)$createdInv['amount'] === 25000.00) {
            CLI::write("  [PASS] Invoice #{$invNumber} (Order ID: {$orderId}) berhasil diterbitkan (Nominal: Rp 25.000).", 'green');
        } else {
            CLI::write("  [FAIL] Gagal menerbitkan invoice tagihan paket.", 'red');
            return;
        }

        // ---------------------------------------------------------
        // TEST 3: Simulasi Webhook Pelunasan (Settlement Gateway)
        // ---------------------------------------------------------
        CLI::write("\n[3] Simulasi Webhook Pelunasan Payment Gateway...", 'cyan');
        $webhook = new Webhook();
        $payload = [
            'order_id'       => $orderId,
            'status'         => 'SUCCESS',
            'amount'         => 25000.00,
            'payment_method' => 'bsi_va',
            'gateway_trx_id' => 'TRX-TEST-' . time(),
        ];

        $webhookResp = $webhook->payment($payload);
        $paidInv = $invoiceModel->find($invId);

        if ($paidInv['status'] === 'paid' && !empty($paidInv['subscription_id'])) {
            CLI::write("  [PASS] Webhook berhasil diproses! Invoice status = PAID, subscription_id = {$paidInv['subscription_id']}.", 'green');
        } else {
            CLI::write("  [FAIL] Invoice tidak berubah menjadi PAID setelah webhook settlement.", 'red');
            return;
        }

        // ---------------------------------------------------------
        // TEST 4: Verifikasi Kuota Dinamis Baru Pasca Pembayaran
        // ---------------------------------------------------------
        CLI::write("\n[4] Verifikasi Kuota Efektif Provider Pasca-Pelunasan...", 'cyan');
        $quota = $subModel->getEffectiveQuota($testProviderId);

        if ($quota['has_active_package'] && (int)$quota['capacity_limit'] === 10 && $quota['status'] === 'active') {
            CLI::write("  [PASS] Kuota efektif aktif: {$quota['capacity_limit']} listing aktif, masa berlaku s.d. {$quota['end_date']}.", 'green');
        } else {
            CLI::write("  [FAIL] Kuota provider tidak bertambah menjadi 10 listing. Aktual: " . json_encode($quota), 'red');
            return;
        }

        // ---------------------------------------------------------
        // TEST 5: Verifikasi Pemulihan / Re-Aktivasi Listing
        // ---------------------------------------------------------
        CLI::write("\n[5] Mengaktifkan Kembali Listing Iklan yang Sempat Dinonaktifkan...", 'cyan');
        $inactiveListings = $listingModel->where('publication_status', 'inactive')->findAll();
        $reactivatedCount = 0;

        foreach ($inactiveListings as $il) {
            $listingModel->update($il['id'], ['publication_status' => 'published']);
            $reactivatedCount++;
            if ($reactivatedCount >= 3) break;
        }

        $allListings = $listingModel->getListingsByProvider($testProviderId);
        $publishedCount = count(array_filter($allListings, fn($l) => $l['publication_status'] === 'published'));

        if ($publishedCount > 0 && $publishedCount <= (int)$quota['capacity_limit']) {
            CLI::write("  [PASS] {$reactivatedCount} listing berhasil diaktifkan kembali. Total listing aktif: {$publishedCount}/{$quota['capacity_limit']}.", 'green');
        } else {
            CLI::write("  [FAIL] Gagal mengaktifkan kembali listing iklan.", 'red');
            return;
        }

        // ---------------------------------------------------------
        // TEST 6: Verifikasi Immutable Audit Trail
        // ---------------------------------------------------------
        CLI::write("\n[6] Memverifikasi Log Audit Trail Billing & Langganan...", 'cyan');
        $logs = $auditModel->where('action', 'PROVIDER_SUBSCRIPTION_ACTIVATED')
            ->where('actor_id', $testProviderId)
            ->orderBy('id', 'DESC')
            ->findAll();

        if (!empty($logs)) {
            CLI::write("  [PASS] Log PROVIDER_SUBSCRIPTION_ACTIVATED tercatat di database: " . ($logs[0]['reason'] ?? ''), 'green');
        } else {
            CLI::write("  [FAIL] Audit log tidak ditemukan.", 'red');
            return;
        }

        CLI::write("\n==================================================================", 'yellow');
        CLI::write("  SELURUH SKENARIO PENGUJIAN MODUL BILLING BERHASIL 100%! [PASSED] ", 'green');
        CLI::write("==================================================================\n", 'yellow');
    }
}
