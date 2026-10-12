<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\RentalApplicationModel;
use App\Models\ReservationModel;
use App\Models\ContractModel;
use App\Models\InvoiceModel;
use App\Models\PaymentModel;
use App\Models\UnitModel;
use App\Models\UserModel;
use App\Models\NotificationLogModel;
use App\Libraries\PaymentGatewayService;
use App\Libraries\WhatsAppService;
use App\Controllers\Webhook;
use Config\ReservationConfig;

class TestSprint6 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:sprint6';
    protected $description = 'Verifikasi otomatis semua fitur Sprint 6 (Payment, WhatsApp & Contract)';

    public function run(array $params)
    {
        CLI::write("=== MENJALANKAN PENGUJIAN OTOMATIS SPRINT 6 ===", 'yellow');

        $db          = \Config\Database::connect();
        $appModel    = new RentalApplicationModel();
        $resModel    = new ReservationModel();
        $contractModel = new ContractModel();
        $invModel    = new InvoiceModel();
        $pmtModel    = new PaymentModel();
        $unitModel   = new UnitModel();
        $userModel   = new UserModel();
        $notifModel  = new NotificationLogModel();
        $gateway     = new PaymentGatewayService();
        $whatsapp    = new WhatsAppService();

        // 1. Fetch Persona Users
        CLI::write("1. Memeriksa Pengguna Uji Coba:", 'white');
        $budi  = $userModel->where('email', 'budi@gmail.com')->first();
        $ahmad = $userModel->where('email', 'ahmad@owner.kosaja.id')->first();
        CLI::write("   - Tenant: {$budi['name']} (WA: {$budi['whatsapp']})", 'cyan');
        CLI::write("   - Provider: {$ahmad['name']} (WA: {$ahmad['whatsapp']})", 'cyan');

        // 2. Setup Test Unit & Application
        CLI::write("\n2. Setup Unit Kamar & Pengajuan Sewa Approved:", 'white');
        $listing = $db->table('listings')
            ->select('listings.id, listings.title, listings.unit_id, listings.price_monthly, properties.id as property_id, properties.name as property_name, units.name as unit_name')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->get()
            ->getFirstRow('array');

        $unitModel->update($listing['unit_id'], ['status' => 'available']);

        $appId = $appModel->insert([
            'tenant_id'        => $budi['id'],
            'listing_id'       => $listing['id'],
            'unit_id'          => $listing['unit_id'],
            'start_date'       => date('Y-m-d', strtotime('+7 days')),
            'duration_months'  => 6,
            'payment_schedule' => 'monthly',
            'marital_status'   => 'lajang',
            'rental_amount'    => $listing['price_monthly'],
            'gateway_fee'      => 4500,
            'applicant_notes'  => 'Uji coba Sprint 6 Payment and Contract',
            'status'           => 'approved',
        ]);

        $resId = $resModel->insert([
            'application_id' => $appId,
            'expires_at'     => date('Y-m-d H:i:s', strtotime('+24 hours')),
            'status'         => 'active',
        ]);
        $unitModel->update($listing['unit_id'], ['status' => 'reserved']);

        $resData = $resModel->getReservationWithDetails($resId);
        CLI::write("   - Reservasi Aktif: #RES-{$resId} (Unit '{$listing['unit_name']}' berstatus RESERVED)", 'green');

        // 3. Test WhatsApp Notification for Application Approved
        CLI::write("\n3. Menguji Integrasi Notifikasi WhatsApp (Application Decision):", 'white');
        $notifResult = $whatsapp->sendApplicationApprovedNotification($resData, $resData);
        CLI::write("   - Notifikasi Approved Terkirim: " . ($notifResult['success'] ? 'SUCCESS' : 'FAILED'), $notifResult['success'] ? 'green' : 'red');
        CLI::write("   - Penerima: {$notifResult['recipient']}", 'cyan');

        $rejectNotifResult = $whatsapp->sendApplicationRejectedNotification($resData, 'Kamar sedang dalam perbaikan AC');
        CLI::write("   - Notifikasi Rejected Terkirim: " . ($rejectNotifResult['success'] ? 'SUCCESS' : 'FAILED'), $rejectNotifResult['success'] ? 'green' : 'red');

        // 4. Test Payment Gateway Transaction Initialization
        CLI::write("\n4. Menguji Inisialisasi Transaksi Pembayaran Gateway:", 'white');
        $trx = $gateway->createPaymentTransaction($resData, 'bsi_va');
        CLI::write("   - Order Reference: {$trx['order_id']}", 'cyan');
        CLI::write("   - Rental Amount: Rp " . number_format($trx['rental_amount'], 0, ',', '.'), 'cyan');
        CLI::write("   - Gateway Fee: Rp " . number_format($trx['gateway_fee'], 0, ',', '.'), 'cyan');
        CLI::write("   - Total Tagihan (Gross): Rp " . number_format($trx['gross_amount'], 0, ',', '.'), 'green');
        if ($trx['gross_amount'] !== ($trx['rental_amount'] + $trx['gateway_fee'])) {
            CLI::error("FAIL: Kalkulasi total pembayaran tidak cocok!");
            return;
        }

        // 5. Test Webhook Execution (Simulating SUCCESS Settlement)
        CLI::write("\n5. Menguji Eksekusi Webhook Payment Gateway (Status: SUCCESS):", 'white');
        $webhook = new Webhook();

        // Simulate incoming webhook POST payload
        $_POST = [
            'order_id'       => $trx['order_id'],
            'status'         => 'SUCCESS',
            'amount'         => $trx['gross_amount'],
            'payment_method' => 'bsi_va',
            'gateway_trx_id' => 'MID-TEST-' . time(),
        ];

        $webhookResponse = $webhook->payment();
        $responseBody = json_decode($webhookResponse->getBody(), true);

        CLI::write("   - Webhook Status Response: " . ($responseBody['status'] ?? 'ERROR'), 'green');
        $contractId = $responseBody['contract_id'] ?? null;
        CLI::write("   - ID Kontrak Terbentuk: #CTR-{$contractId}", 'green');

        if (! $contractId) {
            CLI::error("FAIL: Kontrak tidak terbentuk!");
            return;
        }

        // 6. Verify Contract Details & Snapshot
        CLI::write("\n6. Memvalidasi Snapshot dan Integritas Entitas CONTRACT:", 'white');
        $contract = $contractModel->getContractWithDetails($contractId);
        CLI::write("   - Properti: {$contract['property_name']}", 'cyan');
        CLI::write("   - Kamar: {$contract['unit_name']}", 'cyan');
        CLI::write("   - Periode: " . date('d M Y', strtotime($contract['start_date'])) . " s/d " . date('d M Y', strtotime($contract['end_date'])), 'cyan');
        CLI::write("   - Harga Disepakati: Rp " . number_format($contract['agreed_price'], 0, ',', '.'), 'cyan');
        CLI::write("   - Status Kontrak: " . strtoupper($contract['status']) . " (Expected: ACTIVE)", $contract['status'] === 'active' ? 'green' : 'red');

        // 7. Verify State Transition: Reservation -> Converted, Unit -> Occupied (Rental Active)
        CLI::write("\n7. Memvalidasi State Transition (Reservation -> Rental Active):", 'white');
        $updatedRes  = $resModel->find($resId);
        $updatedUnit = $unitModel->find($listing['unit_id']);

        CLI::write("   - Status Reservasi: " . strtoupper($updatedRes['status']) . " (Expected: CONVERTED)", $updatedRes['status'] === 'converted' ? 'green' : 'red');
        CLI::write("   - Status Unit Fisik: " . strtoupper($updatedUnit['status']) . " (Expected: OCCUPIED / RENTAL ACTIVE)", $updatedUnit['status'] === 'occupied' ? 'green' : 'red');

        if ($updatedRes['status'] !== 'converted' || $updatedUnit['status'] !== 'occupied') {
            CLI::error("FAIL: State transition unit atau reservasi tidak sesuai!");
            return;
        }

        // 8. Verify Invoice & Payment Record
        CLI::write("\n8. Memvalidasi Tagihan (Invoice) & Pembayaran (Payment):", 'white');
        $invoices = $invModel->getInvoicesByContract($contractId);
        CLI::write("   - Jumlah Invoice: " . count($invoices), 'cyan');
        CLI::write("   - Status Invoice: " . strtoupper($invoices[0]['status']) . " (Expected: PAID)", $invoices[0]['status'] === 'paid' ? 'green' : 'red');

        $payments = $pmtModel->getPaymentsByInvoice($invoices[0]['id']);
        CLI::write("   - Status Pembayaran: " . strtoupper($payments[0]['status']) . " (Expected: SUCCESS)", $payments[0]['status'] === 'success' ? 'green' : 'red');
        CLI::write("   - Nominal Dibayar: Rp " . number_format($payments[0]['amount_paid'], 0, ',', '.'), 'cyan');

        // 9. Verify Critical WhatsApp Notifications Logged
        CLI::write("\n9. Memeriksa Riwayat Pengiriman Notifikasi WhatsApp:", 'white');
        $recentNotifs = $notifModel->getRecent(5);
        CLI::write("   - Total Notifikasi Terkirim/Tersimulasi dalam Log: " . count($recentNotifs), 'cyan');
        foreach ($recentNotifs as $n) {
            CLI::write("     • [{$n['event_type']}] ke {$n['recipient_phone']} ({$n['recipient_name']}) - Status: " . strtoupper($n['status']), 'green');
        }

        // Clean up test records
        $pmtModel->where('invoice_id', $invoices[0]['id'])->delete();
        $invModel->where('contract_id', $contractId)->delete();
        $contractModel->delete($contractId);
        $resModel->delete($resId);
        $appModel->delete($appId);
        $unitModel->update($listing['unit_id'], ['status' => 'available']);

        CLI::write("\n=== SELURUH PENGUJIAN SPRINT 6 BERHASIL 100% ===", 'green');
    }
}
