<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\RentalApplicationModel;
use App\Models\ListingModel;
use App\Models\UserModel;
use App\Controllers\Application;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\SiteURI;
use CodeIgniter\HTTP\UserAgent;

class TestApply24 extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'test:apply24';
    protected $description = 'Verifikasi fitur halaman apply/24, combo durasi, dan skema pembayaran';

    public function run(array $params)
    {
        CLI::write("=== MENJALANKAN PENGUJIAN HALAMAN APPLY/24 ===", 'yellow');

        // Create mock IncomingRequest with SiteURI for Web Controller simulation
        $config = config('App');
        $siteUri = new SiteURI($config, 'apply/24');
        $incomingRequest = new IncomingRequest($config, $siteUri, null, new UserAgent());
        \Config\Services::injectMock('request', $incomingRequest);

        $session = \Config\Services::session();
        $session->set([
            'isLoggedIn' => true,
            'user_id'    => 4, // Budi Pratama (tenant, gender L)
            'role'       => 'tenant',
        ]);

        $response = \Config\Services::response();
        $logger   = \Config\Services::logger();

        $controller = new Application();
        $controller->initController($incomingRequest, $response, $logger);

        // 1. Test create(24)
        CLI::write("\n1. Menguji Controller create(24)...", 'white');
        $output = $controller->create(24);

        if ($output instanceof \CodeIgniter\HTTP\ResponseInterface) {
            CLI::error("FAIL: Redirected to " . $output->getHeaderLine('Location'));
            return;
        }

        CLI::write("   Render output length: " . strlen($output), 'cyan');

        $checks = [
            'Jenis Listing: Sewa Tahunan'     => str_contains($output, 'Jenis Listing: Sewa Tahunan'),
            'Tarif Sewa: Rp 10.000.000'       => str_contains($output, '10.000.000'),
            'Hanya Lunas di Awal (Full Upfront)' => str_contains($output, 'Lunas di Awal (Full Upfront)') && !str_contains($output, 'Tahunan (Per 1 Tahun)'),
            'const rentalConfig'              => str_contains($output, 'const rentalConfig'),
            'durations'                       => str_contains($output, 'durations'),
            '1 Tahun'                         => str_contains($output, '1 Tahun'),
        ];

        foreach ($checks as $name => $passed) {
            if ($passed) {
                CLI::write("   [PASS] $name", 'green');
            } else {
                CLI::error("   [FAIL] $name");
            }
        }

        // 2. Test rental types categorization
        CLI::write("\n2. Menguji Pengkategorian Jenis Listing dari database...", 'white');
        $db = \Config\Database::connect();
        $listing24 = $db->table('listings')->where('id', 24)->get()->getRowArray();
        CLI::write("   Listing 24: Monthly=" . ($listing24['price_monthly'] ?? 'null') . ", 6M=" . ($listing24['price_six_monthly'] ?? 'null') . ", Yearly=" . ($listing24['price_yearly'] ?? 'null'), 'cyan');
        if (empty($listing24['price_monthly']) && empty($listing24['price_six_monthly']) && !empty($listing24['price_yearly'])) {
            CLI::write("   [PASS] Listing 24 terkategori Sewa Tahunan", 'green');
        } else {
            CLI::error("   [FAIL] Listing 24 tidak sesuai harga tahunan");
        }

        // 3. Test listing 21 (Monthly + Six Monthly)
        CLI::write("\n3. Menguji Listing 21 (Bulanan)...", 'white');
        $output21 = $controller->create(21);
        $checks21 = [
            'Listing 21 contains Sewa Bulanan'     => str_contains($output21, 'Sewa Bulanan'),
            'Listing 21 contains Bayar Bulanan'    => str_contains($output21, 'Bayar Bulanan (Per Bulan)'),
            'Listing 21 contains Lunas di Awal'    => str_contains($output21, 'Lunas di Awal (Full Upfront)'),
        ];
        foreach ($checks21 as $name => $passed) {
            if ($passed) {
                CLI::write("   [PASS] $name", 'green');
            } else {
                CLI::error("   [FAIL] $name");
            }
        }

        CLI::write("\n=== PENGUJIAN SELESAI ===", 'green');
    }
}
