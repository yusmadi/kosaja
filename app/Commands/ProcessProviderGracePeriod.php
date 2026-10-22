<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\ProviderProfileModel;
use App\Models\ProviderSubscriptionModel;
use App\Models\ListingModel;
use App\Models\AuditLogModel;

class ProcessProviderGracePeriod extends BaseCommand
{
    protected $group       = 'KosAja';
    protected $name        = 'provider:process-grace-period';
    protected $description = 'Otomasi penonaktifan unit listing yang belum tersewa ketika masa grace period / paket langganan provider berakhir (FR-PM-004).';

    public function run(array $params)
    {
        CLI::write('=== Memulai Pemrosesan Grace Period & Subscription Expiry Mitra ===', 'yellow');

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $subModel = new ProviderSubscriptionModel();
        $listingModel = new ListingModel();
        $auditModel = new AuditLogModel();

        // 1. Mark expired active subscriptions where end_date < today
        $expiredSubs = $db->table('provider_subscriptions')
            ->where('status', 'active')
            ->where('end_date <', $today)
            ->get()
            ->getResultArray();

        $markedCount = 0;
        foreach ($expiredSubs as $sub) {
            $db->table('provider_subscriptions')
                ->where('id', $sub['id'])
                ->update(['status' => 'expired']);
            $markedCount++;
        }

        if ($markedCount > 0) {
            CLI::write("Mengubah status {$markedCount} langganan yang lewat jatuh tempo menjadi EXPIRED.", 'light_gray');
        }

        // 2. Find all providers whose free_period has ended AND have no active subscription
        $providers = $db->table('users')
            ->select('users.id as provider_id, users.name, users.email, provider_profiles.free_period_end')
            ->join('provider_profiles', 'provider_profiles.user_id = users.id')
            ->where('users.role', 'provider')
            ->get()
            ->getResultArray();

        $processedProviders = 0;
        $totalDeactivatedListings = 0;

        foreach ($providers as $prov) {
            $providerId = (int) $prov['provider_id'];

            // Check if provider has any active paid subscription
            $activeSub = $subModel->getActiveSubscription($providerId);
            if ($activeSub) {
                // Provider has active paid plan, skip
                continue;
            }

            // Check if provider is still within free period
            $freePeriodEnd = $prov['free_period_end'];
            if (! empty($freePeriodEnd) && $freePeriodEnd >= $today) {
                // Free period still active, skip
                continue;
            }

            // Provider has expired free period & no active subscription!
            // According to Decision A2: Deactivate all published listings whose unit is NOT rented.
            $allListings = $listingModel->getListingsByProvider($providerId);
            $publishedListings = array_filter($allListings, fn($l) => $l['publication_status'] === 'published');

            $deactivatedForThisProvider = 0;
            foreach ($publishedListings as $listing) {
                // If unit is already rented/occupied, keep it safe
                if ($listing['unit_status'] === 'occupied') {
                    continue;
                }

                // Deactivate non-rented listing
                $listingModel->update($listing['id'], [
                    'publication_status' => 'inactive',
                ]);

                $auditModel->record(
                    $providerId,
                    'LISTING_AUTO_DEACTIVATED_BILLING_EXPIRED',
                    'listings',
                    (int) $listing['id'],
                    ['publication_status' => 'published'],
                    ['publication_status' => 'inactive'],
                    "Listing '{$listing['title']}' unit '{$listing['unit_name']}' dinonaktifkan otomatis oleh sistem karena masa transisi / paket langganan mitra telah kedaluwarsa (FR-PM-004)."
                );

                $deactivatedForThisProvider++;
                $totalDeactivatedListings++;
            }

            if ($deactivatedForThisProvider > 0) {
                $processedProviders++;
                CLI::write("Provider [ID: {$providerId}] '{$prov['name']}': {$deactivatedForThisProvider} unit listing berhasil dinonaktifkan.", 'cyan');
            }
        }

        CLI::write("=== Pemrosesan Selesai ===", 'green');
        CLI::write("Total Provider Diproses: {$processedProviders}", 'white');
        CLI::write("Total Listing Dinonaktifkan: {$totalDeactivatedListings}", 'white');
    }
}
