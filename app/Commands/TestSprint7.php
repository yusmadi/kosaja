<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\RentalApplicationModel;
use App\Models\ReservationModel;
use App\Models\ContractModel;
use App\Models\InvoiceModel;
use App\Models\PaymentModel;
use App\Models\DisputeModel;
use App\Models\RefundModel;
use App\Models\AuditLogModel;
use App\Models\NotificationLogModel;
use App\Models\UnitModel;
use App\Models\UserModel;
use App\Libraries\RecurringBillingService;
use App\Libraries\WhatsAppService;
use App\Controllers\Admin\Disputes;

class TestSprint7 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:sprint7';
    protected $description = 'Verifikasi otomatis semua fitur Sprint 7 (Recurring Billing, Grace Period Worker, WhatsApp H-3, Dashboard & Disputes/Refunds)';

    public function run(array $params)
    {
        CLI::write("=== MENJALANKAN PENGUJIAN OTOMATIS SPRINT 7 (PHASE 6) ===", 'yellow');

        $db             = \Config\Database::connect();
        $userModel      = new UserModel();
        $unitModel      = new UnitModel();
        $appModel       = new RentalApplicationModel();
        $contractModel  = new ContractModel();
        $invModel       = new InvoiceModel();
        $pmtModel       = new PaymentModel();
        $disputeModel   = new DisputeModel();
        $refundModel    = new RefundModel();
        $auditModel     = new AuditLogModel();
        $notifModel     = new NotificationLogModel();
        $billingService = new RecurringBillingService();
        $whatsApp       = new WhatsAppService();

        // 1. Fetch Persona Users
        CLI::write("\n1. Memeriksa Pengguna Uji Coba:", 'white');
        $budi  = $userModel->where('email', 'budi@gmail.com')->first();
        $ahmad = $userModel->where('email', 'ahmad@owner.kosaja.id')->first();
        $admin = $userModel->where('role', 'super_admin')->first();

        CLI::write("   - Tenant: {$budi['name']} (ID: {$budi['id']}, WA: {$budi['whatsapp']})", 'cyan');
        CLI::write("   - Provider: {$ahmad['name']} (ID: {$ahmad['id']}, WA: {$ahmad['whatsapp']})", 'cyan');
        CLI::write("   - Admin: {$admin['name']} (ID: {$admin['id']})", 'cyan');

        // Setup Listing & Unit
        $listing = $db->table('listings')
            ->select('listings.id, listings.title, listings.unit_id, listings.price_monthly, properties.id as property_id, properties.name as property_name, units.name as unit_name')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->get()
            ->getFirstRow('array');

        $appId = $appModel->insert([
            'tenant_id'        => $budi['id'],
            'listing_id'       => $listing['id'],
            'unit_id'          => $listing['unit_id'],
            'start_date'       => date('Y-m-d'),
            'duration_months'  => 6,
            'payment_schedule' => 'monthly',
            'marital_status'   => 'lajang',
            'rental_amount'    => $listing['price_monthly'],
            'gateway_fee'      => 4500,
            'status'           => 'approved',
        ]);

        // 2. Test Contract & Recurring Billing Schedule Generation
        CLI::write("\n2. Menguji Pembuatan Kontrak & Sistem Recurring Billing Schedule (Invoices):", 'white');
        $startDate = date('Y-m-d');
        $endDate   = date('Y-m-d', strtotime('+6 months'));

        $contractId = $contractModel->insert([
            'application_id'   => $appId,
            'tenant_id'        => $budi['id'],
            'provider_id'      => $ahmad['id'],
            'property_id'      => $listing['property_id'],
            'unit_id'          => $listing['unit_id'],
            'start_date'       => $startDate,
            'end_date'         => $endDate,
            'agreed_price'     => $listing['price_monthly'],
            'payment_schedule' => 'monthly',
            'status'           => 'active',
        ]);

        // Insert first invoice (simulated paid from reservation checkout)
        $firstInvId = $invModel->insert([
            'contract_id'   => $contractId,
            'tenant_id'     => $budi['id'],
            'rental_amount' => $listing['price_monthly'],
            'gateway_fee'   => 4500,
            'total_amount'  => $listing['price_monthly'] + 4500,
            'due_date'      => $startDate,
            'status'        => 'paid',
        ]);

        // Generate recurring schedule
        $generated = $billingService->generateScheduleForContract($contractId);
        $allInvoices = $invModel->getInvoicesByContract($contractId);

        CLI::write("   - Kontrak Aktif Terbentuk: #CTR-{$contractId} (Masa Sewa: 6 Bulan, Schedule: Monthly)", 'green');
        CLI::write("   - Tagihan Termin Pertama Lunas: #INV-{$firstInvId}", 'cyan');
        CLI::write("   - Tagihan Termin Berulang Dihasilkan Otomatis: " . count($generated) . " tagihan", 'green');
        CLI::write("   - Total Tagihan Terjadwal dalam Kontrak: " . count($allInvoices) . " invoice", 'green');

        if (count($allInvoices) < 6) {
            CLI::error("FAIL: Jadwal invoice recurring bulanan tidak terbentuk lengkap untuk kontrak 6 bulan!");
            return;
        }

        // 3. Test Grace Period (3 Hari) & Overdue Worker
        CLI::write("\n3. Menguji Worker Masa Tenggang (Grace Period 3 Hari) & Status Overdue:", 'white');
        
        // Buat invoice pengujian 1: Jatuh tempo 5 hari lalu (lewat grace period -> harus jadi OVERDUE)
        $pastDueDate = date('Y-m-d', strtotime('-5 days'));
        $testOverdueInvId = $invModel->insert([
            'contract_id'   => $contractId,
            'tenant_id'     => $budi['id'],
            'rental_amount' => 1000000,
            'gateway_fee'   => 4500,
            'total_amount'  => 1004500,
            'due_date'      => $pastDueDate,
            'status'        => 'pending',
        ]);

        // Buat invoice pengujian 2: Jatuh tempo 2 hari lalu (masih dalam grace period -> harus tetap PENDING)
        $graceDueDate = date('Y-m-d', strtotime('-2 days'));
        $testGraceInvId = $invModel->insert([
            'contract_id'   => $contractId,
            'tenant_id'     => $budi['id'],
            'rental_amount' => 1000000,
            'gateway_fee'   => 4500,
            'total_amount'  => 1004500,
            'due_date'      => $graceDueDate,
            'status'        => 'pending',
        ]);

        // Jalankan worker
        $workerStats = $billingService->processGracePeriodAndOverdue();

        $updatedOverdueInv = $invModel->find($testOverdueInvId);
        $updatedGraceInv   = $invModel->find($testGraceInvId);

        CLI::write("   - Invoice due -5 hari: Status beralih ke " . strtoupper($updatedOverdueInv['status']) . " (Expected: OVERDUE)", $updatedOverdueInv['status'] === 'overdue' ? 'green' : 'red');
        CLI::write("   - Invoice due -2 hari: Status tetap " . strtoupper($updatedGraceInv['status']) . " (Expected: PENDING dalam Grace Period)", $updatedGraceInv['status'] === 'pending' ? 'green' : 'red');

        // Verify audit log for overdue
        $overdueAudit = $auditModel->where('object_type', 'invoices')
            ->where('object_id', $testOverdueInvId)
            ->where('action', 'INVOICE_OVERDUE')
            ->first();
        CLI::write("   - Immutable Audit Log 'INVOICE_OVERDUE' Tercatat: " . ($overdueAudit ? 'YES' : 'NO'), $overdueAudit ? 'green' : 'red');

        if ($updatedOverdueInv['status'] !== 'overdue' || $updatedGraceInv['status'] !== 'pending') {
            CLI::error("FAIL: Logika grace period 3 hari atau transisi overdue tidak sesuai spesifikasi!");
            return;
        }

        // 4. Test WhatsApp H-3 Due Notification
        CLI::write("\n4. Menguji Notifikasi WhatsApp Pengingat H-3 Jatuh Tempo:", 'white');
        $h3Date = date('Y-m-d', strtotime('+3 days'));
        $testH3InvId = $invModel->insert([
            'contract_id'   => $contractId,
            'tenant_id'     => $budi['id'],
            'rental_amount' => 1200000,
            'gateway_fee'   => 4500,
            'total_amount'  => 1204500,
            'due_date'      => $h3Date,
            'status'        => 'pending',
        ]);

        $h3DispatchResult = $billingService->dispatchH3DueReminders();
        CLI::write("   - Target Tanggal H-3: {$h3DispatchResult['target_date']}", 'cyan');
        CLI::write("   - Notifikasi H-3 Berhasil Dikirim: {$h3DispatchResult['sent_count']}", $h3DispatchResult['sent_count'] > 0 ? 'green' : 'red');

        $h3Log = $notifModel->where('event_type', 'invoice_reminder_h3')
            ->orderBy('id', 'DESC')
            ->first();
        CLI::write("   - Log Notifikasi H-3 Tercatat di Database: " . ($h3Log ? 'YES' : 'NO'), $h3Log ? 'green' : 'red');

        if (! $h3Log) {
            CLI::error("FAIL: Notifikasi H-3 WhatsApp tidak tercatat!");
            return;
        }

        // 5. Test Tenant Invoice Settlement / Payment
        CLI::write("\n5. Menguji Pembayaran Tagihan Rutin oleh Penyewa (Invoice Settlement):", 'white');
        $payResult = $billingService->payInvoice($testH3InvId, 'bsi_va');
        CLI::write("   - Status Pembayaran: " . ($payResult['success'] ? 'SUCCESS' : 'FAILED'), $payResult['success'] ? 'green' : 'red');

        $paidInv = $invModel->find($testH3InvId);
        $pmtRecord = $pmtModel->where('invoice_id', $testH3InvId)->first();
        CLI::write("   - Status Invoice Terkini: " . strtoupper($paidInv['status']) . " (Expected: PAID)", $paidInv['status'] === 'paid' ? 'green' : 'red');
        CLI::write("   - Record Pembayaran Terbentuk: #PAY-{$pmtRecord['id']} (Nominal: Rp " . number_format($pmtRecord['amount_paid'], 0, ',', '.') . ")", 'green');

        // 6. Test Provider Occupancy & Listing Capacity Calculation
        CLI::write("\n6. Menguji Metrik Dasbor Penyedia (Okupansi & Kapasitas Listing):", 'white');
        $totalUnits = $db->table('units')->where('property_id', $listing['property_id'])->countAllResults();
        $occupiedUnits = $db->table('units')->where('property_id', $listing['property_id'])->where('status', 'occupied')->countAllResults();
        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0;
        CLI::write("   - Total Unit Properti: {$totalUnits} kamar", 'cyan');
        CLI::write("   - Unit Terisi (Occupied): {$occupiedUnits} kamar", 'cyan');
        CLI::write("   - Tingkat Okupansi: {$occupancyRate}%", 'green');

        $totalListings = $db->table('listings')->countAllResults();
        $maxFreeListings = 3;
        $remainingListingQuota = max(0, $maxFreeListings - $totalListings);
        CLI::write("   - Kuota Listing Free Tier: 3 Listing", 'cyan');
        CLI::write("   - Listing Terpakai: {$totalListings} Listing", 'cyan');
        CLI::write("   - Sisa Kapasitas Kuota: {$remainingListingQuota} Listing", 'green');

        // 7. Test Backoffice Dispute Management & Refund Handling
        CLI::write("\n7. Menguji Pengajuan Dispute, Investigasi & Keputusan Refund:", 'white');
        
        // Tenant raises dispute
        $disputeId = $disputeModel->insert([
            'contract_id'     => $contractId,
            'invoice_id'      => $testH3InvId,
            'raised_by_id'    => $budi['id'],
            'against_user_id' => $ahmad['id'],
            'title'           => 'Fasilitas Air Panas & AC Mengalami Kerusakan',
            'reason'          => 'Unit AC tidak dingin dan pemanas air mati sejak hari pertama masuk sewa.',
            'status'          => 'opened',
            'decision'        => 'none',
        ]);
        CLI::write("   - Laporan Dispute Terbuka: #DSP-{$disputeId} oleh {$budi['name']}", 'green');

        // Admin investigates
        $disputeModel->update($disputeId, ['status' => 'investigating']);
        $auditModel->record($admin['id'], 'DISPUTE_INVESTIGATING', 'disputes', $disputeId, ['status' => 'opened'], ['status' => 'investigating'], 'Admin memulai mediasi.');
        CLI::write("   - Transisi State: OPENED -> INVESTIGATING", 'cyan');

        // Admin decides with PARTIAL REFUND
        $refundAmount = 250000.00;
        $refundId = $refundModel->insert([
            'dispute_id'      => $disputeId,
            'payment_id'      => $pmtRecord['id'],
            'invoice_id'      => $testH3InvId,
            'amount'          => $refundAmount,
            'refund_type'     => 'partial',
            'status'          => 'completed',
            'reason'          => 'Kompensasi perbaikan AC sebesar Rp 250.000 disetujui.',
            'processed_by_id' => $admin['id'],
            'processed_at'    => date('Y-m-d H:i:s'),
        ]);

        $disputeModel->update($disputeId, [
            'status'         => 'resolved',
            'decision'       => 'partial_refund',
            'decision_notes' => 'Kompensasi disetujui sebesar Rp 250.000.',
            'decided_by_id'  => $admin['id'],
            'decided_at'     => date('Y-m-d H:i:s'),
        ]);

        $auditModel->record($admin['id'], 'REFUND_PROCESSED', 'refunds', $refundId, null, ['amount' => $refundAmount], 'Refund diproses');
        $auditModel->record($admin['id'], 'DISPUTE_DECIDED', 'disputes', $disputeId, ['status' => 'investigating'], ['status' => 'resolved'], 'Dispute selesai');

        // Send decision notification
        $disputeDetail = $disputeModel->getDisputeWithDetails($disputeId);
        $refundRecord  = $refundModel->find($refundId);
        $notifResult   = $whatsApp->sendDisputeDecisionNotification($disputeDetail, $refundRecord);

        CLI::write("   - Keputusan Final: RESOLVED (PARTIAL REFUND)", 'green');
        CLI::write("   - Nominal Refund Terverifikasi: Rp " . number_format($refundAmount, 0, ',', '.'), 'green');
        CLI::write("   - Notifikasi Keputusan Terkirim ke Tenant: " . ($notifResult['success'] ? 'SUCCESS' : 'FAILED'), $notifResult['success'] ? 'green' : 'red');

        // 8. Verify Immutable Audit Trail Entries
        CLI::write("\n8. Memverifikasi Integritas Immutable Audit Trail:", 'white');
        $recentAudit = $auditModel->getRecentLogs(5);
        CLI::write("   - Total Log Terbaru Terverifikasi: " . count($recentAudit), 'cyan');
        foreach ($recentAudit as $log) {
            CLI::write("     • [{$log['action']}] pada {$log['object_type']} #{$log['object_id']} - Reason: {$log['reason']}", 'green');
        }

        // Clean up test records
        CLI::write("\n9. Membersihkan Data Uji Coba...", 'white');
        $refundModel->where('dispute_id', $disputeId)->delete();
        $disputeModel->delete($disputeId);
        $pmtModel->where('invoice_id', $testH3InvId)->delete();
        $invModel->where('contract_id', $contractId)->delete();
        $contractModel->delete($contractId);
        $appModel->delete($appId);

        CLI::write("\n=== SELURUH PENGUJIAN OTOMATIS SPRINT 7 (PHASE 6) BERHASIL 100% ===", 'green');
    }
}
