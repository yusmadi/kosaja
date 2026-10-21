<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Models\ReportModel;
use App\Models\SettlementModel;
use App\Models\DisputeModel;
use App\Libraries\AdminScopeService;

class Dashboard extends BaseController
{
    protected AdminScopeService $scopeService;
    protected AuditLogModel $auditModel;
    protected ReportModel $reportModel;
    protected SettlementModel $settlementModel;
    protected DisputeModel $disputeModel;

    public function __construct()
    {
        $this->scopeService    = new AdminScopeService();
        $this->auditModel      = new AuditLogModel();
        $this->reportModel     = new ReportModel();
        $this->settlementModel = new SettlementModel();
        $this->disputeModel    = new DisputeModel();
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $isSuperAdmin = $this->scopeService->isSuperAdmin();

        if ($isSuperAdmin) {
            return $this->superAdminDashboard($db);
        }

        return $this->regionalAdminDashboard($db);
    }

    /**
     * SA-01: Global Governance Dashboard for Super Admin
     */
    protected function superAdminDashboard($db)
    {
        // 1. High-Level Metrics
        $totalUsers      = $db->table('users')->countAllResults();
        $totalTenants    = $db->table('users')->where('role', 'tenant')->countAllResults();
        $totalProviders  = $db->table('users')->where('role', 'provider')->countAllResults();
        $totalAdminDaerah= $db->table('users')->where('role', 'admin_daerah')->countAllResults();

        $totalProperties = $db->table('properties')->countAllResults();
        $totalListings   = $db->table('listings')->countAllResults();
        $publishedListings = $db->table('listings')->where('publication_status', 'published')->countAllResults();
        $pendingListings   = $db->table('listings')->where('publication_status', 'pending_review')->countAllResults();

        // Financials (GTV & Platform Revenue)
        $paidInvoices = $db->table('invoices')->where('status', 'paid')->get()->getResultArray();
        $gtv = 0.0;
        $platformRevenue = 0.0;
        foreach ($paidInvoices as $inv) {
            $gtv += (float) ($inv['total_amount'] ?? 0);
            $platformRevenue += (float) ($inv['gateway_fee'] ?? 4500);
        }

        // Settlements
        $totalSettled = (float) ($db->table('settlements')->where('status', 'completed')->selectSum('net_amount')->get()->getRow()->net_amount ?? 0);
        $pendingSettlementsCount = $db->table('settlements')->where('status', 'pending')->countAllResults();
        $pendingSettlementsAmount = (float) ($db->table('settlements')->where('status', 'pending')->selectSum('net_amount')->get()->getRow()->net_amount ?? 0);

        // Refunds & Disputes
        $totalRefundsAmount = (float) ($db->table('refunds')->selectSum('amount')->get()->getRow()->amount ?? 0);
        $totalDisputes = $db->table('disputes')->countAllResults();
        $escalatedDisputes = $db->table('disputes')->where('is_escalated', 1)->where('status !=', 'resolved')->countAllResults();

        // 2. Regional Performance Breakdown
        $regions = $db->table('regions')->where('level', 'city')->orderBy('id', 'ASC')->get()->getResultArray();
        $regionalPerformance = [];
        foreach ($regions as $reg) {
            // Find operational children
            $children = $db->table('regions')->where('parent_id', $reg['id'])->get()->getResultArray();
            $scopeIds = array_merge([(int) $reg['id']], array_map(fn($c) => (int)$c['id'], $children));

            $propCount = $db->table('properties')->whereIn('region_id', $scopeIds)->countAllResults();
            $listCount = $db->table('listings')
                ->join('units', 'units.id = listings.unit_id')
                ->join('properties', 'properties.id = units.property_id')
                ->whereIn('properties.region_id', $scopeIds)
                ->where('listings.publication_status', 'published')
                ->countAllResults();

            // Regional admin assigned
            $assignedAdmin = $db->table('users')->where('role', 'admin_daerah')->where('region_id', $reg['id'])->get()->getRowArray();

            $regionalPerformance[] = [
                'region_name'     => $reg['name'],
                'region_id'       => $reg['id'],
                'properties_count'=> $propCount,
                'listings_count'  => $listCount,
                'admin_name'      => $assignedAdmin['name'] ?? 'Belum Ditugaskan',
                'admin_email'     => $assignedAdmin['email'] ?? '-',
                'status'          => $assignedAdmin ? 'Aktif Beroperasi' : 'Perlu Admin',
            ];
        }

        // 3. Pending Payouts / Settlements
        $pendingSettlements = $this->settlementModel->getSettlementsWithDetails('pending');

        // 4. Escalated Disputes
        $escalatedDisputeList = $this->disputeModel->getDisputesWithDetails(null, null, true);

        // 5. Recent Global Audit Logs
        $recentAuditLogs = $this->auditModel->getRecentLogs(8);

        return view('admin/dashboard_super', [
            'title'               => 'Global Governance Dashboard — Super Admin',
            'role'                => 'super_admin',
            'activeMenu'          => 'dashboard',
            'stats'               => [
                'total_users'         => $totalUsers,
                'total_tenants'       => $totalTenants,
                'total_providers'     => $totalProviders,
                'total_admin_daerah'  => $totalAdminDaerah,
                'total_properties'    => $totalProperties,
                'total_listings'      => $totalListings,
                'published_listings'  => $publishedListings,
                'pending_listings'    => $pendingListings,
                'gtv'                 => $gtv,
                'platform_revenue'    => $platformRevenue,
                'total_settled'       => $totalSettled,
                'pending_settlements_count' => $pendingSettlementsCount,
                'pending_settlements_amount'=> $pendingSettlementsAmount,
                'total_refunds_amount'=> $totalRefundsAmount,
                'total_disputes'      => $totalDisputes,
                'escalated_disputes'  => $escalatedDisputes,
            ],
            'regionalPerformance' => $regionalPerformance,
            'pendingSettlements'  => $pendingSettlements,
            'escalatedDisputes'   => $escalatedDisputeList,
            'recentAuditLogs'     => $recentAuditLogs,
        ]);
    }

    /**
     * ADM-01: Action-Oriented Queue Dashboard for Admin Daerah
     */
    protected function regionalAdminDashboard($db)
    {
        $scopedRegionIds = $this->scopeService->getScopedRegionIds() ?: [0];
        $scopedLabel     = $this->scopeService->getScopedRegionLabel();
        $clustersLabel   = $this->scopeService->getScopedClustersLabel();
        $adminRegionId   = $this->scopeService->getAdminRegionId();

        // 1. Action Queue Metrics (PRD 37.3 & UX 34)
        // Pending Provider Verification in Region
        $pendingKYC = $db->table('users')
            ->select('users.id')
            ->join('provider_profiles', 'provider_profiles.user_id = users.id')
            ->whereIn('users.region_id', ! empty($scopedRegionIds) ? $scopedRegionIds : [0])
            ->where('provider_profiles.verification_status', 'pending')
            ->countAllResults();

        // Pending Listing Moderation in Region
        $pendingListings = $db->table('listings')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->whereIn('properties.region_id', $scopedRegionIds)
            ->where('listings.publication_status', 'pending_review')
            ->countAllResults();

        // Open Violation Reports in Region
        $openReports = $db->table('reports')
            ->whereIn('region_id', $scopedRegionIds)
            ->whereIn('status', ['open', 'investigating'])
            ->countAllResults();

        // Open Disputes in Region
        $openDisputes = $db->table('disputes')
            ->join('contracts', 'contracts.id = disputes.contract_id')
            ->join('properties', 'properties.id = contracts.property_id')
            ->whereIn('properties.region_id', $scopedRegionIds)
            ->whereIn('disputes.status', ['opened', 'investigating'])
            ->countAllResults();

        // Restricted / Suspended in Region
        $restrictedProviders = $db->table('users')
            ->select('users.id')
            ->join('provider_profiles', 'provider_profiles.user_id = users.id')
            ->whereIn('users.region_id', ! empty($scopedRegionIds) ? $scopedRegionIds : [0])
            ->where('provider_profiles.verification_status', 'suspended')
            ->countAllResults();

        $inactiveListings = $db->table('listings')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->whereIn('properties.region_id', $scopedRegionIds)
            ->where('listings.publication_status', 'inactive')
            ->countAllResults();

        // 2. Queue Items for Immediate Action
        // Urgent Provider KYC Verifications to Review
        $providersQueue = $db->table('users')
            ->select('users.*, regions.name as region_name, provider_profiles.id as profile_id, provider_profiles.verification_status, provider_profiles.id_card_photo, provider_profiles.bank_name, provider_profiles.bank_account_number, provider_profiles.created_at as submitted_at')
            ->join('provider_profiles', 'provider_profiles.user_id = users.id', 'left')
            ->join('regions', 'regions.id = users.region_id', 'left')
            ->where('users.role', 'provider')
            ->whereIn('users.region_id', ! empty($scopedRegionIds) ? $scopedRegionIds : [0])
            ->where('(provider_profiles.verification_status = "pending" OR provider_profiles.verification_status IS NULL)')
            ->orderBy('users.id', 'DESC')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Urgent Listings to Review
        $listingsQueue = $db->table('listings')
            ->select('listings.*, units.name as unit_name, properties.name as property_name, properties.target_gender, users.name as provider_name, regions.name as region_name')
            ->join('units', 'units.id = listings.unit_id')
            ->join('properties', 'properties.id = units.property_id')
            ->join('users', 'users.id = properties.provider_id')
            ->join('regions', 'regions.id = properties.region_id', 'left')
            ->whereIn('properties.region_id', $scopedRegionIds)
            ->where('listings.publication_status', 'pending_review')
            ->limit(5)
            ->get()
            ->getResultArray();

        // Open Reports Queue
        $reportsQueue = $adminRegionId ? $this->reportModel->getReportsWithDetails('open', $adminRegionId) : [];

        // Active Disputes Queue
        $disputesQueue = $adminRegionId ? $this->disputeModel->getDisputesWithDetails('opened', $adminRegionId) : [];

        // Recent Regional Audit Logs scoped to regional jurisdiction
        $adminUserId = session()->get('user_id') ? (int) session()->get('user_id') : null;
        $recentAuditLogs = $this->auditModel->getRecentLogsForRegion($scopedRegionIds, $adminUserId, 8);

        return view('admin/dashboard_regional', [
            'title'           => 'Dasbor Operasional Regional — Admin ' . esc($scopedLabel),
            'role'            => 'admin_daerah',
            'activeMenu'      => 'dashboard',
            'scopedLabel'     => $scopedLabel,
            'clustersLabel'   => $clustersLabel,
            'stats'           => [
                'pending_kyc'          => $pendingKYC,
                'pending_listings'     => $pendingListings,
                'open_reports'         => $openReports,
                'open_disputes'        => $openDisputes,
                'restricted_providers' => $restrictedProviders,
                'inactive_listings'    => $inactiveListings,
            ],
            'providersQueue'  => $providersQueue,
            'listingsQueue'   => $listingsQueue,
            'reportsQueue'    => $reportsQueue,
            'disputesQueue'   => $disputesQueue,
            'recentAuditLogs' => $recentAuditLogs,
        ]);
    }
}
