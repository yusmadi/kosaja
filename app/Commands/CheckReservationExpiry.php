<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ReservationModel;

class CheckReservationExpiry extends BaseCommand
{
    protected $group       = 'KosAja';
    protected $name        = 'reservations:check-expiry';
    protected $description = 'Pemeriksaan otomatis reservasi kedaluwarsa (Expiration Window) dan pelepasan unit kamar.';

    public function run(array $params)
    {
        CLI::write('Checking expired reservations...', 'yellow');

        $reservationModel = new ReservationModel();
        $expiredCount = $reservationModel->checkAndExpireReservations();

        if ($expiredCount > 0) {
            CLI::write("Berhasil memproses {$expiredCount} reservasi yang kedaluwarsa. Unit terkait telah dikembalikan ke status AVAILABLE.", 'green');
        } else {
            CLI::write('Tidak ada reservasi aktif yang melebihi batas waktu kedaluwarsa.', 'white');
        }
    }
}
