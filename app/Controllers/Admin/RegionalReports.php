<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\AdminScopeService;

class RegionalReports extends BaseController
{
    protected AdminScopeService $scopeService;

    public function __construct()
    {
        $this->scopeService = new AdminScopeService();
    }

    /**
     * ADM-11: Regional Reports & Analytics
     */
    public function index()
    {
        $db = \Config\Database::connect();
        $scopedRegionIds = $this->scopeService->getScopedRegionIds() ?: [0];
        $scopedLabel     = $this->scopeService->getScopedRegionLabel();

        // 1. Regional Occupancy Metrics
        $totalUnits = $db->table('units')
            ->join('properties', 'properties.id = units.property_id')
            ->whereIn('properties.region_id', $scopedRegionIds)
            ->countAllResults();

        $occupiedUnits = $db->table('units')
            ->join('properties', 'properties.id = units.property_id')
            ->whereIn('properties.region_id', $scopedRegionIds)
            ->where('units.status', 'occupied')
            ->countAllResults();

        $availableUnits = $db->table('units')
            ->join('properties', 'properties.id = units.property_id')
            ->whereIn('properties.region_id', $scopedRegionIds)
            ->where('units.status', 'available')
            ->countAllResults();

        $occupancyRate = $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 1) : 0.0;

        // 2. Listing Moderation Performance
        $publishedListings = $db->table('listings')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->whereIn('properties.region_id', $scopedRegionIds)
            ->where('listings.publication_status', 'published')
            ->countAllResults();

        $pendingListings = $db->table('listings')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->whereIn('properties.region_id', $scopedRegionIds)
            ->where('listings.publication_status', 'pending_review')
            ->countAllResults();

        // 3. Disputes & Complaints
        $resolvedDisputes = $db->table('disputes')
            ->join('contracts', 'contracts.id = disputes.contract_id')
            ->join('properties', 'properties.id = contracts.property_id')
            ->whereIn('properties.region_id', $scopedRegionIds)
            ->where('disputes.status', 'resolved')
            ->countAllResults();

        $openDisputes = $db->table('disputes')
            ->join('contracts', 'contracts.id = disputes.contract_id')
            ->join('properties', 'properties.id = contracts.property_id')
            ->whereIn('properties.region_id', $scopedRegionIds)
            ->whereIn('disputes.status', ['opened', 'investigating'])
            ->countAllResults();

        // 4. Cluster breakdown (Depok, Mlati, dsb)
        $clusters = $db->table('regions')->whereIn('id', $scopedRegionIds)->get()->getResultArray();
        $clusterStats = [];
        foreach ($clusters as $c) {
            $cProps = $db->table('properties')->where('region_id', $c['id'])->countAllResults();
            $cUnits = $db->table('units')
                ->join('properties', 'properties.id = units.property_id')
                ->where('properties.region_id', $c['id'])
                ->countAllResults();

            $clusterStats[] = [
                'name'       => $c['name'],
                'level'      => $c['level'],
                'properties' => $cProps,
                'units'      => $cUnits,
            ];
        }

        return view('admin/regional_reports/index', [
            'title'       => 'Laporan & Metrik Operasional Wilayah — ' . esc($scopedLabel),
            'role'        => session()->get('role') ?? 'admin_daerah',
            'activeMenu'  => 'regional-reports',
            'scopedLabel' => $scopedLabel,
            'stats'       => [
                'total_units'        => $totalUnits,
                'occupied_units'     => $occupiedUnits,
                'available_units'    => $availableUnits,
                'occupancy_rate'     => $occupancyRate,
                'published_listings' => $publishedListings,
                'pending_listings'   => $pendingListings,
                'resolved_disputes'  => $resolvedDisputes,
                'open_disputes'      => $openDisputes,
            ],
            'clusterStats' => $clusterStats,
        ]);
    }
}
