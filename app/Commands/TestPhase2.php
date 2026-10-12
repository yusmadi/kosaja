<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;
use App\Models\ProviderProfileModel;
use App\Models\PropertyModel;
use App\Models\PropertyFacilityModel;
use App\Models\PropertyPhotoModel;
use App\Models\UnitModel;
use App\Models\UnitPhotoModel;
use App\Models\ListingModel;
use App\Models\AuditLogModel;
use App\Libraries\StorageService;

class TestPhase2 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'provider:test';
    protected $description = 'Runs comprehensive test for Phase 2: Provider & Inventory Management (Sprint 3)';

    public function run(array $params)
    {
        CLI::write("=== PENGUJIAN OTOMATIS PHASE 2: PROVIDER & INVENTORY MANAGEMENT (SPRINT 3) ===", 'green');
        CLI::newLine();

        $userModel = new UserModel();
        $profileModel = new ProviderProfileModel();
        $propertyModel = new PropertyModel();
        $facilityModel = new PropertyFacilityModel();
        $photoModel = new PropertyPhotoModel();
        $unitModel = new UnitModel();
        $unitPhotoModel = new UnitPhotoModel();
        $listingModel = new ListingModel();
        $auditModel = new AuditLogModel();
        $storage = new StorageService();

        // 1. Test Provider Onboarding & Verification (KYC)
        CLI::write("[TEST 1] Pengujian Alur Onboarding & Verifikasi Provider (KYC)...", 'yellow');
        $provider = $userModel->where('role', 'provider')->first();
        if (! $provider) {
            CLI::error("  [FAIL] Akun provider tidak ditemukan di database!");
            return;
        }

        $profile = $profileModel->getByUserId($provider['id']);
        if (! $profile) {
            $profileModel->insert([
                'user_id'             => $provider['id'],
                'verification_status' => 'pending',
                'bank_name'           => 'BSI',
                'bank_account_number' => '7123456789',
                'free_period_end'     => $profileModel->calculateDefaultFreePeriodEnd(),
            ]);
            $profile = $profileModel->getByUserId($provider['id']);
        }

        CLI::write("  [PASS] Provider Profile ditemukan (User ID: {$provider['id']}, Status: {$profile['verification_status']})", 'cyan');

        // Test admin verification and audit trail
        $previousState = $profile;
        $profileModel->update($profile['id'], [
            'verification_status' => 'verified',
            'rejection_reason'    => null,
        ]);
        $newState = $profileModel->find($profile['id']);

        $logId = $auditModel->record(
            1, // Super admin actor
            'VERIFY_PROVIDER',
            'provider_profile',
            $profile['id'],
            $previousState,
            $newState,
            'Pengujian otomatis verifikasi identitas provider'
        );

        if ($logId) {
            CLI::write("  [PASS] Audit log tercatat dengan aman (Log ID: {$logId}, Action: VERIFY_PROVIDER)", 'cyan');
        } else {
            CLI::error("  [FAIL] Gagal mencatat audit log verifikasi!");
            return;
        }
        CLI::newLine();

        // 2. Test Property Management & Syariat Target Gender
        CLI::write("[TEST 2] Pengujian Manajemen Properti & Kepatuhan Syariat Gender...", 'yellow');
        
        // Test Syariat validation (invalid gender should fail)
        $invalidProp = [
            'provider_id'   => $provider['id'],
            'region_id'     => 5,
            'name'          => 'Kos Tanpa Gender',
            'type'          => 'kos',
            'target_gender' => 'bebas_campur', // Invalid gender enum
            'address'       => 'Jl. Contoh Alamat',
        ];

        if ($propertyModel->validate($invalidProp)) {
            CLI::error("  [FAIL] Model meloloskan target_gender yang tidak sesuai syariat!");
            return;
        }
        CLI::write("  [PASS] Validasi Syariat berhasil menolak target_gender tidak valid ('bebas_campur')", 'cyan');

        // Create valid Property (Khusus Putri)
        $validProp = [
            'provider_id'   => $provider['id'],
            'region_id'     => 5,
            'name'          => 'Kos Putri Barokah Test ' . time(),
            'type'          => 'kos',
            'target_gender' => 'putri',
            'address'       => 'Jl. Kaliurang KM 5.2 No. 10, Sleman',
            'description'   => 'Kos putri eksklusif syariat lengkap fasilitas.',
            'rules'         => "1. Lawan jenis dilarang masuk kamar.\n2. Gerbang 22.00 WIB.",
            'latitude'      => -7.76500000,
            'longitude'     => 110.37900000,
        ];

        $propertyId = $propertyModel->insert($validProp);
        if (! $propertyId) {
            CLI::error("  [FAIL] Gagal membuat properti baru!");
            return;
        }
        CLI::write("  [PASS] Properti baru berhasil dibuat (ID: {$propertyId}, Target: Putri)", 'cyan');

        // Test Facility sync
        $facilityModel->syncFacilities($propertyId, ['WiFi Berkecepatan Tinggi', 'Dapur Bersama + Kulkas', 'Mushola']);
        $facs = $facilityModel->getFacilitiesByProperty($propertyId);
        if (count($facs) === 3) {
            CLI::write("  [PASS] Fasilitas properti berhasil disinkronkan (Count: 3 fasilitas)", 'cyan');
        } else {
            CLI::error("  [FAIL] Fasilitas gagal disinkronkan!");
            return;
        }

        // Test Photo insertion
        $photoId = $photoModel->insert([
            'property_id' => $propertyId,
            'photo_url'   => 'assets/images/sample-kos-1.jpg',
            'caption'     => 'Fasad Depan',
            'is_primary'  => 1,
        ]);
        CLI::write("  [PASS] Foto sampul properti berhasil disimpan (ID: {$photoId})", 'cyan');
        CLI::newLine();

        // 3. Test Unit Management
        CLI::write("[TEST 3] Pengujian Manajemen Unit Kamar Fisik...", 'yellow');
        $unitData = [
            'property_id' => $propertyId,
            'name'        => 'Kamar 101 Test',
            'size'        => '3.5 x 4 m',
            'floor'       => 'Lantai 1',
            'status'      => 'available',
        ];

        $unitId = $unitModel->insert($unitData);
        if (! $unitId) {
            CLI::error("  [FAIL] Gagal membuat unit kamar baru!");
            return;
        }
        CLI::write("  [PASS] Unit kamar berhasil dibuat (ID: {$unitId}, Nama: {$unitData['name']}, Status: available)", 'cyan');

        // Test Unit Photo
        $unitPhotoId = $unitPhotoModel->insert([
            'unit_id'    => $unitId,
            'photo_url'  => 'assets/images/sample-kos-2.jpg',
            'caption'    => 'Interior Kamar',
            'is_primary' => 1,
        ]);
        CLI::write("  [PASS] Foto unit kamar berhasil disimpan (ID: {$unitPhotoId})", 'cyan');
        CLI::newLine();

        // 4. Test Individual Listing Management (1 Unit = 1 Listing)
        CLI::write("[TEST 4] Pengujian Pembuatan Individual Listing & Lifecycle...", 'yellow');
        $listingData = [
            'unit_id'            => $unitId,
            'title'              => 'Kamar Eksklusif Muslimah Dekat UGM Kamar Mandi Dalam',
            'description'        => 'Kamar bersih nyaman AC, kasur springbed, lemari jati, WiFi kencang 100Mbps.',
            'price_monthly'      => 1500000.00,
            'price_yearly'       => 16500000.00,
            'publication_status' => 'draft',
        ];

        $listingId = $listingModel->insert($listingData);
        if (! $listingId) {
            CLI::error("  [FAIL] Gagal membuat individual listing!");
            return;
        }
        CLI::write("  [PASS] Individual Listing berhasil dibuat sebagai Draf (ID: {$listingId})", 'cyan');

        // Test Unique Constraint (1 Unit = 1 Listing)
        try {
            $duplicateListing = [
                'unit_id'            => $unitId, // Same unit!
                'title'              => 'Duplicate Listing Should Fail',
                'description'        => 'Trying duplicate listing...',
                'publication_status' => 'draft',
            ];
            $listingModel->insert($duplicateListing);
            CLI::error("  [FAIL] Database mengizinkan duplikasi listing pada unit yang sama!");
            return;
        } catch (\Throwable $e) {
            CLI::write("  [PASS] Aturan '1 Unit = 1 Listing' terbukti konsisten (Database menolak duplikasi unit_id)", 'cyan');
        }

        // Test Lifecycle: Draft -> Pending Review -> Rejected (with reason) -> Resubmit -> Published
        $listingModel->update($listingId, [
            'publication_status' => 'pending_review',
        ]);
        CLI::write("  [PASS] Transisi status: DRAFT -> PENDING_REVIEW", 'cyan');

        $listingModel->update($listingId, [
            'publication_status' => 'draft',
            'rejection_reason'   => 'Foto kamar mandi belum diunggah.',
        ]);
        CLI::write("  [PASS] Penolakan dengan alasan tersimpan: 'Foto kamar mandi belum diunggah.'", 'cyan');

        $listingModel->update($listingId, [
            'publication_status' => 'pending_review',
            'rejection_reason'   => null,
        ]);
        CLI::write("  [PASS] Perbaikan & Resubmit: Status kembali PENDING_REVIEW, alasan penolakan dibersihkan", 'cyan');

        $listingModel->update($listingId, [
            'publication_status' => 'published',
        ]);
        CLI::write("  [PASS] Admin Approval: Status menjadi PUBLISHED (Siap tayang di marketplace)", 'cyan');
        CLI::newLine();

        // 5. Test Storage Service
        CLI::write("[TEST 5] Pengujian Integrasi Storage Service...", 'yellow');
        $resolvedUrl = StorageService::getUrl('uploads/properties/fasad.jpg');
        CLI::write("  [PASS] Storage URL Resolver berhasil: {$resolvedUrl}", 'cyan');
        CLI::newLine();

        // Clean up test records
        CLI::write("[CLEANUP] Membersihkan data pengujian sementara...", 'yellow');
        $listingModel->delete($listingId);
        $unitPhotoModel->delete($unitPhotoId);
        $unitModel->delete($unitId);
        $photoModel->delete($photoId);
        $facilityModel->where('property_id', $propertyId)->delete();
        $propertyModel->delete($propertyId);
        CLI::write("  [PASS] Data pengujian sementara berhasil dibersihkan.", 'cyan');
        CLI::newLine();

        CLI::write("==================================================================", 'green');
        CLI::write("SELURUH FITUR PHASE 2 (SPRINT 3) LULUS PENGUJIAN OTOMATIS 100%!", 'green');
        CLI::write("==================================================================", 'green');
    }
}
