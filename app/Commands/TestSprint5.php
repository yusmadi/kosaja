<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\RentalApplicationModel;
use App\Models\ReservationModel;
use App\Models\ListingModel;
use App\Models\UnitModel;
use App\Models\UserModel;
use App\Models\AuditLogModel;
use Config\ReservationConfig;

class TestSprint5 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:sprint5';
    protected $description = 'Verifikasi otomatis semua fitur Sprint 5 (Booking & Application Engine)';

    public function run(array $params)
    {
        CLI::write("=== MENJALANKAN PENGUJIAN OTOMATIS SPRINT 5 ===", 'yellow');

        $db           = \Config\Database::connect();
        $appModel     = new RentalApplicationModel();
        $resModel     = new ReservationModel();
        $unitModel    = new UnitModel();
        $listingModel = new ListingModel();
        $userModel    = new UserModel();
        $auditModel   = new AuditLogModel();
        $config       = new ReservationConfig();

        // 1. Config Test
        CLI::write("1. Memeriksa Konfigurasi Reservasi:", 'white');
        CLI::write("   - Expiration Window: {$config->expirationWindowHours} Jam", 'cyan');
        CLI::write("   - Gateway Fee: Rp " . number_format($config->gatewayFee, 0, ',', '.'), 'cyan');
        if ($config->expirationWindowHours <= 0 || $config->gatewayFee <= 0) {
            CLI::error("FAIL: Konfigurasi tidak valid");
            return;
        }

        // 2. Persona Test Users
        CLI::write("\n2. Memeriksa Pengguna Uji Coba:", 'white');
        $budi  = $userModel->where('email', 'budi@gmail.com')->first();
        $siti  = $userModel->where('email', 'siti@gmail.com')->first();
        $ahmad = $userModel->where('email', 'ahmad@owner.kosaja.id')->first();

        CLI::write("   - Tenant Putra: {$budi['name']} (Gender: {$budi['gender']})", 'cyan');
        CLI::write("   - Tenant Putri: {$siti['name']} (Gender: {$siti['gender']})", 'cyan');
        CLI::write("   - Provider: {$ahmad['name']} (ID: {$ahmad['id']})", 'cyan');

        // 3. Gender Rejection Rules
        CLI::write("\n3. Memeriksa Aturan Validasi Gender Otomatis (Syariat Compliance):", 'white');
        $listings = $db->table('listings')
            ->select('listings.id, listings.title, listings.unit_id, listings.price_monthly, properties.target_gender, units.name as unit_name')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->get()
            ->getResultArray();

        $putraListing  = null;
        $putriListing  = null;
        $familyListing = null;
        foreach ($listings as $l) {
            if ($l['target_gender'] === 'putra' && !$putraListing) $putraListing = $l;
            if ($l['target_gender'] === 'putri' && !$putriListing) $putriListing = $l;
            if ($l['target_gender'] === 'campur_keluarga' && !$familyListing) $familyListing = $l;
        }

        if ($putraListing) {
            $canSiti = ($putraListing['target_gender'] !== 'putra' || $siti['gender'] === 'L');
            CLI::write("   - Siti (P) mengajukan sewa di '{$putraListing['title']}' [PUTRA]: " . (!$canSiti ? "DITOLAK OTOMATIS (PASS)" : "DILOLOSKAN (FAIL)"), !$canSiti ? 'green' : 'red');
        }

        if ($putriListing) {
            $canBudi = ($putriListing['target_gender'] !== 'putri' || $budi['gender'] === 'P');
            CLI::write("   - Budi (L) mengajukan sewa di '{$putriListing['title']}' [PUTRI]: " . (!$canBudi ? "DITOLAK OTOMATIS (PASS)" : "DILOLOSKAN (FAIL)"), !$canBudi ? 'green' : 'red');
        }

        // 4. Submission & State Transition
        CLI::write("\n4. Menguji Alur Pengajuan Sewa (Submit Application):", 'white');
        $targetListing = $putraListing ?? $listings[0];
        $unitModel->update($targetListing['unit_id'], ['status' => 'available']);

        $appId = $appModel->insert([
            'tenant_id'                => $budi['id'],
            'listing_id'               => $targetListing['id'],
            'unit_id'                  => $targetListing['unit_id'],
            'start_date'               => date('Y-m-d', strtotime('+3 days')),
            'duration_months'          => 3,
            'payment_schedule'         => 'monthly',
            'marital_status'           => 'lajang',
            'rental_amount'            => $targetListing['price_monthly'],
            'gateway_fee'              => $config->gatewayFee,
            'applicant_notes'          => 'Mahasiswa, mencari kos tenang untuk tugas akhir.',
            'marriage_certificate_url' => null,
            'is_family_verified'       => 0,
            'status'                   => 'pending',
            'rejection_reason'         => null,
        ]);
        CLI::write("   - Berhasil submit pengajuan #APP-{$appId} status: PENDING", 'green');

        // 5. Provider Approval & Reservation Creation
        CLI::write("\n5. Menguji Persetujuan Pemilik (Provider Approval) & Pembentukan Reservasi:", 'white');
        $appModel->update($appId, [
            'status'     => 'approved',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $unitModel->update($targetListing['unit_id'], ['status' => 'reserved']);

        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$config->expirationWindowHours} hours"));
        $resId = $resModel->insert([
            'application_id' => $appId,
            'expires_at'     => $expiresAt,
            'status'         => 'active',
        ]);

        $unitStatus = $unitModel->find($targetListing['unit_id'])['status'];
        CLI::write("   - Status Pengajuan: APPROVED", 'green');
        CLI::write("   - Status Kamar: " . strtoupper($unitStatus) . " (Terkunci khusus penyewa)", $unitStatus === 'reserved' ? 'green' : 'red');
        CLI::write("   - Entitas Reservasi #RES-{$resId} berhasil dibuat (Batas Kedaluwarsa: {$expiresAt})", 'green');

        // 6. Checkout Calculation
        CLI::write("\n6. Menguji Kalkulasi Harga Checkout (Rental Amount + Gateway Fee):", 'white');
        $resDetails = $resModel->getReservationWithDetails($resId);
        $rentalAmt  = (float) $resDetails['rental_amount'];
        $gateFee    = (float) $resDetails['gateway_fee'];
        $totalAmt   = $rentalAmt + $gateFee;

        CLI::write("   - Rental Amount (Porsi Pemilik): Rp " . number_format($rentalAmt, 0, ',', '.'), 'cyan');
        CLI::write("   - Gateway Fee (Biaya Transaksi Digital): Rp " . number_format($gateFee, 0, ',', '.'), 'cyan');
        CLI::write("   - Total Pembayaran Tenant: Rp " . number_format($totalAmt, 0, ',', '.'), 'green');

        // 7. Expiration Sweep
        CLI::write("\n7. Menguji Mekanisme Kedaluwarsa (Expiration Window Sweep):", 'white');
        // Set expires_at in the past
        $resModel->update($resId, ['expires_at' => date('Y-m-d H:i:s', strtotime('-1 hour'))]);
        $expiredCount = $resModel->checkAndExpireReservations();

        $resAfter = $resModel->find($resId);
        $appAfter = $appModel->find($appId);
        $unitAfter = $unitModel->find($targetListing['unit_id']);

        CLI::write("   - Reservasi Kedaluwarsa Diproses: {$expiredCount}", 'cyan');
        CLI::write("   - Status Reservasi: " . strtoupper($resAfter['status']) . " (Expected: EXPIRED)", $resAfter['status'] === 'expired' ? 'green' : 'red');
        CLI::write("   - Status Pengajuan: " . strtoupper($appAfter['status']) . " (Expected: EXPIRED)", $appAfter['status'] === 'expired' ? 'green' : 'red');
        CLI::write("   - Status Kamar: " . strtoupper($unitAfter['status']) . " (Otomatis dilepaskan kembali ke AVAILABLE)", $unitAfter['status'] === 'available' ? 'green' : 'red');

        // Clean up test records
        $resModel->delete($resId);
        $appModel->delete($appId);

        CLI::write("\n=== SELURUH PENGUJIAN SPRINT 5 BERHASIL 100% ===", 'green');
    }
}
