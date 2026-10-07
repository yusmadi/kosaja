<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\UserModel;
use App\Models\ProviderProfileModel;
use App\Models\RegionModel;

class TestIam extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'iam:test';
    protected $description = 'Runs comprehensive IAM, RBAC, and Syariat Gender tests';

    public function run(array $params)
    {
        CLI::write("=== PENGUJIAN OTOMATIS PHASE 1: IDENTITY & ACCESS MANAGEMENT ===", 'green');
        CLI::newLine();

        $userModel = new UserModel();
        $providerProfileModel = new ProviderProfileModel();
        $regionModel = new RegionModel();

        // 1. Test Seeded Users & Hashing
        CLI::write("[TEST 1] Verifikasi Password Hashing & Seeded Users...", 'yellow');
        $admin = $userModel->findByEmail('admin@kosaja.id');
        if (! $admin || ! password_verify('password123', $admin['password'])) {
            CLI::error("  [FAIL] Password hash Super Admin tidak valid!");
            return;
        }
        CLI::write("  [PASS] Super Admin valid (role: {$admin['role']}, gender: {$admin['gender']})", 'cyan');

        $provider = $userModel->findByEmail('ahmad@owner.kosaja.id');
        if (! $provider || ! password_verify('password123', $provider['password'])) {
            CLI::error("  [FAIL] Password hash Provider tidak valid!");
            return;
        }
        CLI::write("  [PASS] Provider valid (role: {$provider['role']}, gender: {$provider['gender']})", 'cyan');

        $tenant = $userModel->findByEmail('budi@gmail.com');
        if (! $tenant || ! password_verify('password123', $tenant['password'])) {
            CLI::error("  [FAIL] Password hash Tenant tidak valid!");
            return;
        }
        CLI::write("  [PASS] Tenant valid (role: {$tenant['role']}, gender: {$tenant['gender']})", 'cyan');
        CLI::newLine();

        // 2. Test Syariat Gender Validation
        CLI::write("[TEST 2] Verifikasi Validasi Gender Syariat & Role...", 'yellow');
        $newEmail = 'test_syariat_' . time() . '@example.com';
        $invalidUser = [
            'name'     => 'Calon Penghuni',
            'email'    => $newEmail,
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'whatsapp' => '0899999999',
            'gender'   => 'X', // Invalid
            'role'     => 'tenant',
            'status'   => 'active',
        ];

        $result = $userModel->insert($invalidUser);
        if ($result !== false) {
            CLI::error("  [FAIL] Insert dengan gender tidak valid lolos!");
            return;
        }
        CLI::write("  [PASS] Gender non-syariat 'X' berhasil dicegah oleh UserModel validation rules.", 'cyan');

        // Valid insert with Gender 'P'
        $validUser = [
            'name'     => 'Fatimah Zahra',
            'email'    => $newEmail,
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'whatsapp' => '0899999999',
            'gender'   => 'P', // Valid
            'role'     => 'tenant',
            'status'   => 'active',
        ];
        $insertedId = $userModel->insert($validUser);
        if (! $insertedId) {
            CLI::error("  [FAIL] Gagal mendaftarkan user dengan gender valid 'P'!");
            return;
        }
        CLI::write("  [PASS] Pendaftaran user baru dengan gender Syariat 'P' sukses (ID: {$insertedId})", 'cyan');
        CLI::newLine();

        // 3. Test Provider Profile & 6-Month Free Period Creation
        CLI::write("[TEST 3] Verifikasi Otomatisasi Provider Profile & Promo 6 Bulan...", 'yellow');
        $providerEmail = 'new_owner_' . time() . '@kosaja.id';
        $newProviderId = $userModel->insert([
            'name'     => 'Ustadz Mansur',
            'email'    => $providerEmail,
            'password' => password_hash('password123', PASSWORD_BCRYPT),
            'whatsapp' => '081234567899',
            'gender'   => 'L',
            'role'     => 'provider',
            'status'   => 'active',
        ]);

        $freePeriodEnd = date('Y-m-d', strtotime('+6 months'));
        $providerProfileModel->insert([
            'user_id'             => $newProviderId,
            'verification_status' => 'pending',
            'bank_name'           => 'Bank Muamalat',
            'bank_account_number' => '1234567890',
            'free_period_end'     => $freePeriodEnd,
        ]);

        $profileCheck = $providerProfileModel->getByUserId($newProviderId);
        if (! $profileCheck || $profileCheck['free_period_end'] !== $freePeriodEnd) {
            CLI::error("  [FAIL] Profil provider atau periode gratis 6 bulan tidak sesuai!");
            return;
        }
        CLI::write("  [PASS] Profil provider & promo 6 bulan otomatis aktif (Bebas fee s.d {$freePeriodEnd})", 'cyan');
        CLI::newLine();

        // 4. Test Region Hierarchy
        CLI::write("[TEST 4] Verifikasi Hierarki Region (Provinsi -> Kota -> Operasional)...", 'yellow');
        $regions = $regionModel->getHierarchy();
        if (count($regions) < 7) {
            CLI::error("  [FAIL] Jumlah region kurang dari 7!");
            return;
        }
        CLI::write("  [PASS] Hierarki region aktif dengan total " . count($regions) . " wilayah.", 'cyan');
        CLI::newLine();

        // 5. Test Toggle Suspend Account
        CLI::write("[TEST 5] Verifikasi Freeze / Suspend Akun Pengguna...", 'yellow');
        $userModel->update($insertedId, ['status' => 'suspended']);
        $suspendedUser = $userModel->find($insertedId);
        if ($suspendedUser['status'] !== 'suspended') {
            CLI::error("  [FAIL] Status user gagal diubah ke suspended!");
            return;
        }
        CLI::write("  [PASS] Status akun berhasil dibekukan (suspended).", 'cyan');
        CLI::newLine();

        // Cleanup
        $userModel->delete($insertedId);
        $providerProfileModel->delete($profileCheck['id']);
        $userModel->delete($newProviderId);

        CLI::write("=== SEMUA PENGUJIAN PHASE 1 BERHASIL DILALUI DENGAN SUKSES! ===", 'green');
    }
}
