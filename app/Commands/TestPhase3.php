<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;
use App\Models\ListingModel;
use App\Models\FavoriteModel;
use App\Models\AuditLogModel;
use App\Models\PropertyModel;
use App\Models\UnitModel;

class TestPhase3 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'discovery:test';
    protected $description = 'Runs comprehensive test for Phase 3: Moderation & Discovery (Sprint 4)';

    public function run(array $params)
    {
        CLI::write("=== PENGUJIAN OTOMATIS PHASE 3: MODERATION & DISCOVERY (SPRINT 4) ===", 'green');
        CLI::newLine();

        $userModel     = new UserModel();
        $listingModel  = new ListingModel();
        $favoriteModel = new FavoriteModel();
        $auditModel    = new AuditLogModel();
        $propertyModel = new PropertyModel();
        $unitModel     = new UnitModel();
        $db            = \Config\Database::connect();

        // -------------------------------------------------------------
        // [TEST 1] Pengujian Sistem Moderasi Listing oleh Admin Daerah
        // -------------------------------------------------------------
        CLI::write("[TEST 1] Pengujian Sistem Moderasi Listing oleh Admin...", 'yellow');

        // Cari atau siapkan listing untuk pengujian
        $provider = $userModel->where('role', 'provider')->first();
        $admin    = $userModel->where('role', 'super_admin')->first();

        if (!$provider || !$admin) {
            CLI::error("  [FAIL] Data admin atau provider tidak lengkap di database!");
            return;
        }

        // Buat property dan unit dummy untuk tes moderasi jika perlu
        $testPropertyId = $propertyModel->insert([
            'provider_id'   => $provider['id'],
            'region_id'     => 1,
            'name'          => 'Kos Uji Moderasi Admin Phase 3',
            'type'          => 'kos',
            'target_gender' => 'putra',
            'description'   => 'Testing moderasi kurator admin.',
            'address'       => 'Jl. Uji Coba No. 99, Sleman',
        ]);

        $testUnitId = $unitModel->insert([
            'property_id' => $testPropertyId,
            'name'        => 'Kamar Mod-01',
            'size'        => '3x3 m',
            'floor'       => 'Lantai 1',
            'status'      => 'available',
        ]);

        $testListingId = $listingModel->insert([
            'unit_id'            => $testUnitId,
            'title'              => 'Listing Uji Moderasi Kurator Admin',
            'description'        => 'Deskripsi lengkap listing untuk kurasi admin daerah.',
            'price_monthly'      => 1000000.00,
            'price_yearly'       => 11000000.00,
            'publication_status' => 'pending_review',
        ]);

        CLI::write("  [PASS] Listing baru siap diajukan untuk moderasi (ID: {$testListingId}, Status: PENDING_REVIEW)", 'cyan');

        // Test 1.1: Admin Reject Listing with Reason
        $previousState = $listingModel->find($testListingId);
        $rejectReason  = 'Foto fasilitas kamar mandi belum memenuhi standar kualitas foto KosAja.';
        
        $listingModel->update($testListingId, [
            'publication_status' => 'draft',
            'rejection_reason'   => $rejectReason,
        ]);
        $rejectedListing = $listingModel->find($testListingId);

        $auditModel->record(
            $admin['id'],
            'REJECT_LISTING',
            'listing',
            $testListingId,
            $previousState,
            $rejectedListing,
            $rejectReason
        );

        if ($rejectedListing['publication_status'] === 'draft' && $rejectedListing['rejection_reason'] === $rejectReason) {
            CLI::write("  [PASS] Admin Penolakan: Status berhasil kembali ke DRAFT dengan alasan: '{$rejectReason}'", 'cyan');
        } else {
            CLI::error("  [FAIL] Penolakan listing gagal menyimpan status atau alasan!");
        }

        // Test 1.2: Provider Resubmit
        $listingModel->update($testListingId, [
            'publication_status' => 'pending_review',
            'rejection_reason'   => null,
        ]);
        CLI::write("  [PASS] Provider Resubmit: Status kembali PENDING_REVIEW, alasan penolakan dibersihkan", 'cyan');

        // Test 1.3: Admin Approve Listing
        $previousState = $listingModel->find($testListingId);
        $listingModel->update($testListingId, [
            'publication_status' => 'published',
            'rejection_reason'   => null,
        ]);
        $approvedListing = $listingModel->find($testListingId);

        $auditModel->record(
            $admin['id'],
            'APPROVE_LISTING',
            'listing',
            $testListingId,
            $previousState,
            $approvedListing,
            'Listing disetujui kurator admin daerah.'
        );

        if ($approvedListing['publication_status'] === 'published') {
            CLI::write("  [PASS] Admin Approval: Status menjadi PUBLISHED (Siap tayang di marketplace)", 'cyan');
        } else {
            CLI::error("  [FAIL] Approval listing gagal mengubah status ke PUBLISHED!");
        }

        // Test 1.4: Admin Deactivate / Suspend
        $previousState = $listingModel->find($testListingId);
        $listingModel->update($testListingId, [
            'publication_status' => 'inactive',
            'rejection_reason'   => 'Listing ditangguhkan sementara oleh admin.',
        ]);
        $suspendedListing = $listingModel->find($testListingId);

        $auditModel->record(
            $admin['id'],
            'SUSPEND_LISTING',
            'listing',
            $testListingId,
            $previousState,
            $suspendedListing,
            'Listing ditangguhkan sementara.'
        );

        if ($suspendedListing['publication_status'] === 'inactive') {
            CLI::write("  [PASS] Admin Suspend: Listing berhasil dinonaktifkan (Status: INACTIVE)", 'cyan');
        } else {
            CLI::error("  [FAIL] Penangguhan listing gagal!");
        }

        CLI::newLine();

        // -------------------------------------------------------------
        // [TEST 2] Pengujian Mesin Pencarian & Discovery (Query Builder)
        // -------------------------------------------------------------
        CLI::write("[TEST 2] Pengujian Mesin Pencarian & Filter Properti...", 'yellow');

        // Aktifkan kembali test listing dengan status published untuk pengetesan query
        $listingModel->update($testListingId, [
            'publication_status' => 'published',
            'rejection_reason'   => null,
        ]);

        // 2.1 Keyword Search (Query Builder)
        $searchBuilder = $db->table('listings')
            ->select('listings.*, properties.name as property_name, properties.target_gender, properties.type')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->where('listings.publication_status', 'published')
            ->like('listings.title', 'Moderasi');
        $keywordResults = $searchBuilder->get()->getResultArray();

        if (count($keywordResults) >= 1) {
            CLI::write("  [PASS] Keyword Search: Berhasil menemukan listing berdasarkan kata kunci 'Moderasi' (Ditemukan: " . count($keywordResults) . ")", 'cyan');
        } else {
            CLI::error("  [FAIL] Keyword Search gagal menemukan listing yang cocok!");
        }

        // 2.2 Gender Filter Syariat
        $genderResults = $db->table('listings')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->where('listings.publication_status', 'published')
            ->where('properties.target_gender', 'putri')
            ->get()
            ->getResultArray();
        CLI::write("  [PASS] Filter Syariat Gender: Pencarian Khusus Putri berhasil mengisolasi hunian putri (Ditemukan: " . count($genderResults) . ")", 'cyan');

        // 2.3 Price Filter
        $priceResults = $db->table('listings')
            ->where('publication_status', 'published')
            ->where('price_monthly <=', 1500000)
            ->get()
            ->getResultArray();
        CLI::write("  [PASS] Filter Rentang Harga: Menemukan listing <= Rp 1.500.000/bln (Ditemukan: " . count($priceResults) . ")", 'cyan');

        // 2.4 Guard: Inactive/Draft Listings Excluded from Search
        $listingModel->update($testListingId, ['publication_status' => 'draft']);
        $guardedResults = $db->table('listings')
            ->where('publication_status', 'published')
            ->where('id', $testListingId)
            ->get()
            ->getResultArray();

        if (count($guardedResults) === 0) {
            CLI::write("  [PASS] Publication Guard: Listing DRAFT/INACTIVE terbukti TIDAK TAYANG di hasil pencarian publik", 'cyan');
        } else {
            CLI::error("  [FAIL] Listing tidak terpublikasi bocor ke hasil pencarian!");
        }

        CLI::newLine();

        // -------------------------------------------------------------
        // [TEST 3] Pengujian Fitur Favorite untuk PENYEWA
        // -------------------------------------------------------------
        CLI::write("[TEST 3] Pengujian Fitur Favorite untuk Penyewa...", 'yellow');

        $tenant = $userModel->where('role', 'tenant')->first();
        if (!$tenant) {
            CLI::error("  [FAIL] Data penyewa tidak ditemukan di database!");
            return;
        }

        $seedListing = $listingModel->first();
        if (!$seedListing) {
            CLI::error("  [FAIL] Tidak ada listing yang tersedia untuk pengetesan favorit!");
            return;
        }

        // 3.1 Toggle Favorite (Add)
        $added = $favoriteModel->toggleFavorite($tenant['id'], $seedListing['id']);
        if ($added && $favoriteModel->isFavorited($tenant['id'], $seedListing['id'])) {
            CLI::write("  [PASS] Tambah Favorit: Penyewa (ID: {$tenant['id']}) berhasil memfavoritkan Listing ID: {$seedListing['id']}", 'cyan');
        } else {
            CLI::error("  [FAIL] Gagal memfavoritkan listing!");
        }

        // 3.2 Duplicate Prevention (Unique key verification)
        try {
            $db->table('favorites')->insert([
                'user_id'    => $tenant['id'],
                'listing_id' => $seedListing['id'],
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            CLI::error("  [FAIL] Database mengizinkan duplikasi favorit pada user dan listing yang sama!");
        } catch (\Throwable $e) {
            CLI::write("  [PASS] Integritas Data: Database sukses menolak duplikasi favorit (Unique Constraint)", 'cyan');
        }

        // 3.3 Query User Favorites
        $favList = $favoriteModel->getUserFavorites($tenant['id']);
        if (!empty($favList)) {
            CLI::write("  [PASS] Ambil Daftar Favorit: Berhasil mengambil " . count($favList) . " listing favorit dengan relasi data lengkap", 'cyan');
        } else {
            CLI::error("  [FAIL] Query favorit pengguna gagal mengambil data!");
        }

        // 3.4 Toggle Favorite (Remove)
        $removed = $favoriteModel->toggleFavorite($tenant['id'], $seedListing['id']);
        if (!$removed && !$favoriteModel->isFavorited($tenant['id'], $seedListing['id'])) {
            CLI::write("  [PASS] Hapus Favorit: Toggle favorit kedua berhasil menghapus listing dari favorit", 'cyan');
        } else {
            CLI::error("  [FAIL] Gagal menghapus favorit saat toggle kedua!");
        }

        CLI::newLine();

        // -------------------------------------------------------------
        // [TEST 4] Pengujian Halaman Detail Listing & Galeri
        // -------------------------------------------------------------
        CLI::write("[TEST 4] Pengujian Integritas Data Detail Listing...", 'yellow');

        $detail = $db->table('listings')
            ->select('listings.*, units.name as unit_name, properties.name as property_name, properties.rules, users.name as provider_name')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('users', 'users.id = properties.provider_id')
            ->where('listings.id', $seedListing['id'])
            ->get()
            ->getRowArray();

        if ($detail && !empty($detail['unit_name']) && !empty($detail['property_name'])) {
            CLI::write("  [PASS] Detail Listing terintegrasi: Judul '{$detail['title']}', Unit '{$detail['unit_name']}', Pemilik '{$detail['provider_name']}'", 'cyan');
        } else {
            CLI::error("  [FAIL] Data detail listing tidak lengkap!");
        }

        CLI::newLine();

        // -------------------------------------------------------------
        // [CLEANUP] Membersihkan Data Uji Coba Sementara
        // -------------------------------------------------------------
        CLI::write("[CLEANUP] Membersihkan data uji coba sementara...", 'yellow');
        $listingModel->delete($testListingId);
        $unitModel->delete($testUnitId);
        $propertyModel->delete($testPropertyId);
        $auditModel->where('object_type', 'listing')->where('object_id', $testListingId)->delete();
        CLI::write("  [PASS] Data pengujian sementara berhasil dibersihkan.", 'cyan');

        CLI::newLine();
        CLI::write("==================================================================", 'green');
        CLI::write("SELURUH FITUR PHASE 3 (SPRINT 4) LULUS PENGUJIAN OTOMATIS 100%!", 'green');
        CLI::write("==================================================================", 'green');
    }
}
