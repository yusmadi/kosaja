<?php

namespace App\Controllers\Provider;

use App\Controllers\BaseController;
use App\Models\ProviderProfileModel;
use App\Models\PropertyModel;
use App\Models\UnitModel;
use App\Models\ListingModel;
use App\Models\ContractModel;
use App\Models\InvoiceModel;
use App\Models\RentalApplicationModel;
use App\Models\ReservationModel;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $providerId = session()->get('user_id') ?? 3;

        $profileModel = new ProviderProfileModel();
        $profile = $profileModel->getByUserId($providerId);

        // Auto expire outdated reservations
        (new ReservationModel())->checkAndExpireReservations();

        // 1. Occupancy & Unit Metrics
        $totalUnits = $db->table('units')
            ->join('properties', 'properties.id = units.property_id')
            ->where('properties.provider_id', $providerId)
            ->countAllResults();

        $occupiedUnits = $db->table('units')
            ->join('properties', 'properties.id = units.property_id')
            ->where('properties.provider_id', $providerId)
            ->where('units.status', 'occupied')
            ->countAllResults();

        $reservedUnits = $db->table('units')
            ->join('properties', 'properties.id = units.property_id')
            ->where('properties.provider_id', $providerId)
            ->where('units.status', 'reserved')
            ->countAllResults();

        $availableUnits = $db->table('units')
            ->join('properties', 'properties.id = units.property_id')
            ->where('properties.provider_id', $providerId)
            ->where('units.status', 'available')
            ->countAllResults();

        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0;

        // 2. Listing Capacity (Free Tier Quota = 3 Listings)
        $maxFreeListings = 3;
        $totalListings = $db->table('listings')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->where('properties.provider_id', $providerId)
            ->countAllResults();

        $publishedListings = $db->table('listings')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->where('properties.provider_id', $providerId)
            ->where('listings.publication_status', 'published')
            ->countAllResults();

        $remainingListingCapacity = max(0, $maxFreeListings - $totalListings);
        $listingCapacityPercent   = min(100, round(($totalListings / $maxFreeListings) * 100, 1));

        // 3. Contracts & Monthly Revenue
        $contractModel = new ContractModel();
        $contracts = $contractModel->getContractsByProvider($providerId);
        $activeContracts = array_filter($contracts, fn($c) => $c['status'] === 'active');
        $monthlyRevenue = array_reduce($activeContracts, fn($carry, $c) => $carry + (float) $c['agreed_price'], 0);

        // 4. Invoices overview for provider's units
        $invoicesQuery = $db->table('invoices')
            ->select('invoices.status, count(*) as count, sum(invoices.total_amount) as total')
            ->join('contracts', 'contracts.id = invoices.contract_id')
            ->where('contracts.provider_id', $providerId)
            ->groupBy('invoices.status')
            ->get()
            ->getResultArray();

        $invoiceStats = [
            'paid'    => 0,
            'pending' => 0,
            'overdue' => 0,
            'paid_amount' => 0,
        ];
        foreach ($invoicesQuery as $iq) {
            $status = $iq['status'];
            if (isset($invoiceStats[$status])) {
                $invoiceStats[$status] = (int) $iq['count'];
            }
            if ($status === 'paid') {
                $invoiceStats['paid_amount'] = (float) $iq['total'];
            }
        }

        $stats = [
            'my_properties'            => $db->table('properties')->where('provider_id', $providerId)->countAllResults(),
            'total_units'              => $totalUnits,
            'occupied_units'           => $occupiedUnits,
            'reserved_units'           => $reservedUnits,
            'available_units'          => $availableUnits,
            'occupancy_rate'           => $occupancyRate,
            'total_listings'           => $totalListings,
            'published_listings'       => $publishedListings,
            'max_free_listings'        => $maxFreeListings,
            'remaining_capacity'       => $remainingListingCapacity,
            'listing_capacity_percent' => $listingCapacityPercent,
            'active_contracts_count'   => count($activeContracts),
            'monthly_revenue'          => $monthlyRevenue,
            'invoice_stats'            => $invoiceStats,
        ];

        // Recent units
        $units = $db->table('units')
            ->select('units.*, properties.name as property_name, properties.target_gender, listings.id as listing_id, listings.publication_status')
            ->join('properties', 'properties.id = units.property_id')
            ->join('listings', 'listings.unit_id = units.id', 'left')
            ->where('properties.provider_id', $providerId)
            ->limit(10)
            ->get()
            ->getResultArray();

        // Recent properties
        $properties = $db->table('properties')
            ->select('properties.*, regions.name as region_name,
                (SELECT COUNT(*) FROM units WHERE units.property_id = properties.id) as total_units,
                (SELECT photo_url FROM property_photos WHERE property_photos.property_id = properties.id ORDER BY is_primary DESC, id ASC LIMIT 1) as primary_photo')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->where('properties.provider_id', $providerId)
            ->limit(5)
            ->get()
            ->getResultArray();

        // Recent applications
        $appModel = new RentalApplicationModel();
        $recentApplications = $appModel->getApplicationsByProvider($providerId, null);
        $pendingAppsCount = count(array_filter($recentApplications, fn($a) => $a['status'] === 'pending'));

        $stats['pending_applications'] = $pendingAppsCount;

        return view('provider/dashboard', [
            'title'              => 'Mitra Provider Dashboard — KosAja',
            'role'               => 'provider',
            'activeMenu'         => 'dashboard',
            'stats'              => $stats,
            'units'              => $units,
            'properties'         => $properties,
            'profile'            => $profile,
            'recentApplications' => array_slice($recentApplications, 0, 5),
            'activeContracts'    => array_slice($activeContracts, 0, 5),
        ]);
    }
}
