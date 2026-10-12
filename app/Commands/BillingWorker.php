<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Libraries\RecurringBillingService;

class BillingWorker extends BaseCommand
{
    protected $group       = 'KosAja';
    protected $name        = 'billing:worker';
    protected $description = 'Worker otomatis recurring billing, grace period 3 hari (PENDING -> OVERDUE), dan dispatch notifikasi WhatsApp H-3';

    public function run(array $params)
    {
        CLI::write("=== MENJALANKAN KOSAJA BILLING WORKER ===", 'yellow');

        $billingService = new RecurringBillingService();

        // 1. Generate schedules for any active contracts
        CLI::write("1. Memeriksa jadwal tagihan recurring kontrak aktif...", 'white');
        $generatedCount = $billingService->generateSchedulesForAllActiveContracts();
        CLI::write("   - Tagihan baru terbentuk: {$generatedCount}", 'cyan');

        // 2. Process Grace Period (3 days) and Overdue status
        CLI::write("\n2. Memproses Masa Tenggang (Grace Period 3 Hari) & Status Overdue...", 'white');
        $overdueResult = $billingService->processGracePeriodAndOverdue();
        CLI::write("   - Tagihan beralih ke OVERDUE: {$overdueResult['marked_overdue']}", $overdueResult['marked_overdue'] > 0 ? 'red' : 'green');
        CLI::write("   - Tagihan dalam Masa Tenggang (Grace Period): {$overdueResult['in_grace_period']}", 'yellow');
        CLI::write("   - Tagihan masa sewa baru diaktifkan ke PENDING: {$overdueResult['activated_pending']}", 'cyan');

        // 3. Dispatch WhatsApp H-3 reminders
        CLI::write("\n3. Memeriksa & Mengirimkan Pengingat WhatsApp H-3 Jatuh Tempo...", 'white');
        $h3Result = $billingService->dispatchH3DueReminders();
        CLI::write("   - Target Jatuh Tempo H-3: {$h3Result['target_date']}", 'cyan');
        CLI::write("   - Notifikasi WhatsApp H-3 Terkirim: {$h3Result['sent_count']}", 'green');

        CLI::write("\n=== BILLING WORKER SELESAI DIEKSEKUSI ===", 'green');
    }
}
