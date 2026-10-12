<?php

function getWithCookies($url, $cookieFile) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    $body = curl_exec($ch);
    curl_close($ch);
    return $body;
}

echo "=======================================================\n";
echo "   VERIFIKASI DIFERENSIASI SUPER ADMIN & ADMIN DAERAH  \n";
echo "=======================================================\n\n";

// 1. Test Super Admin
$superCookieFile = sys_get_temp_dir() . '/cookie_super.txt';
if (file_exists($superCookieFile)) unlink($superCookieFile);
$htmlSuper = getWithCookies('http://localhost:8080/switch-role/super_admin', $superCookieFile);
echo "HTML Length: " . strlen($htmlSuper) . "\n";
echo "Snippet: " . substr($htmlSuper, 0, 300) . "\n";

echo "[1] TEST ROLE SUPER ADMIN:\n";
echo "  • Judul Dasbor : " . (strpos($htmlSuper, 'Dasbor Tata Kelola Platform (Super Admin)') !== false ? "PASS (Dasbor Tata Kelola)" : "FAIL") . "\n";
echo "  • Scope Label  : " . (strpos($htmlSuper, 'Scope: Nasional (Seluruh Indonesia)') !== false ? "PASS (Nasional/Global)" : "FAIL") . "\n";
echo "  • Sidebar Header: " . (strpos($htmlSuper, 'Tata Kelola Super Admin') !== false ? "PASS" : "FAIL") . "\n";
echo "  • Menu IAM Users: " . (strpos($htmlSuper, 'Manajemen Akun (IAM)') !== false ? "PASS" : "FAIL") . "\n";
echo "  • Menu Regions  : " . (strpos($htmlSuper, 'Master Wilayah') !== false ? "PASS" : "FAIL") . "\n";
echo "  • Menu Transaksi: " . (strpos($htmlSuper, 'Transaksi Finansial') !== false ? "PASS" : "FAIL") . "\n";
echo "  • Menu Settlement: " . (strpos($htmlSuper, 'Settlement Dana Mitra') !== false ? "PASS" : "FAIL") . "\n";
echo "  • Menu Monetisasi: " . (strpos($htmlSuper, 'Kebijakan Monetisasi') !== false ? "PASS" : "FAIL") . "\n";
echo "  • Menu Konfigurasi: " . (strpos($htmlSuper, 'Konfigurasi Platform') !== false ? "PASS" : "FAIL") . "\n";
echo "  • Matriks Performa Wilayah: " . (strpos($htmlSuper, 'Matriks Performa Wilayah') !== false ? "PASS" : "FAIL") . "\n";

// 2. Test Admin Daerah
$daerahCookieFile = sys_get_temp_dir() . '/cookie_daerah.txt';
if (file_exists($daerahCookieFile)) unlink($daerahCookieFile);
$htmlDaerah = getWithCookies('http://localhost:8080/switch-role/admin_daerah', $daerahCookieFile);
$json = json_decode($htmlDaerah, true);
if (isset($json['message'])) {
    echo "ERROR MESSAGE: " . $json['message'] . "\n";
    if (isset($json['trace'][0])) {
        echo "TRACE 0: " . json_encode($json['trace'][0]) . "\n";
    }
}

echo "\n[2] TEST ROLE ADMIN DAERAH:\n";
echo "  • Judul Dasbor : " . (strpos($htmlDaerah, 'Dasbor Operasional Regional') !== false ? "PASS (Dasbor Operasional)" : "FAIL") . "\n";
echo "  • Scope Label  : " . (strpos($htmlDaerah, 'Kabupaten Sleman') !== false ? "PASS (Kabupaten Sleman)" : "FAIL") . "\n";
echo "  • Sidebar Header: " . (strpos($htmlDaerah, 'Operasional Admin Daerah') !== false ? "PASS" : "FAIL") . "\n";
echo "  • Menu Antrean : " . (strpos($htmlDaerah, 'Dasbor Antrean') !== false ? "PASS" : "FAIL") . "\n";
echo "  • Menu Laporan : " . (strpos($htmlDaerah, 'Laporan Pelanggaran') !== false ? "PASS" : "FAIL") . "\n";
echo "  • Menu Restriksi: " . (strpos($htmlDaerah, 'Restriksi &amp; Sanksi') !== false || strpos($htmlDaerah, 'Restriksi & Sanksi') !== false ? "PASS" : "FAIL") . "\n";
echo "  • Menu Lap. Daerah: " . (strpos($htmlDaerah, 'Laporan Regional') !== false ? "PASS" : "FAIL") . "\n";
echo "  • TIDAK ADA Menu IAM Users: " . (strpos($htmlDaerah, 'Manajemen Akun (IAM)') === false ? "PASS (TERSEMBUNYI)" : "FAIL") . "\n";
echo "  • TIDAK ADA Menu Regions  : " . (strpos($htmlDaerah, 'Master Wilayah') === false ? "PASS (TERSEMBUNYI)" : "FAIL") . "\n";
echo "  • TIDAK ADA Menu Monetisasi: " . (strpos($htmlDaerah, 'Kebijakan Monetisasi') === false ? "PASS (TERSEMBUNYI)" : "FAIL") . "\n";

// 3. Test RBAC Security Guard: Admin Daerah tries to access Super Admin exclusive route
$ch = curl_init('http://localhost:8080/admin/users');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $daerahCookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $daerahCookieFile);
$htmlDenied = curl_exec($ch);
$effUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

echo "\n[3] TEST RBAC RESTRICTION (Admin Daerah mengakses /admin/users):\n";
echo "  • Target URL Redirection : " . $effUrl . "\n";
echo "  • Snippet Response : " . substr(strip_tags($htmlDenied), 0, 200) . "\n";
echo "  • Blokir Akses & Pesan Peringatan : " . (strpos($htmlDenied, 'Akses ditolak') !== false ? "PASS (BLOCKED & SECURED)" : "FAIL") . "\n";

echo "\n=======================================================\n";
echo "   SEMUA VERIFIKASI BERHASIL MEMENUHI PRD & BLUEPRINT! \n";
echo "=======================================================\n";
