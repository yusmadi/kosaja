<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ReportModel;
use App\Models\AuditLogModel;
use App\Libraries\AdminScopeService;

class Reports extends BaseController
{
    protected ReportModel $reportModel;
    protected AuditLogModel $auditModel;
    protected AdminScopeService $scopeService;

    public function __construct()
    {
        $this->reportModel  = new ReportModel();
        $this->auditModel   = new AuditLogModel();
        $this->scopeService = new AdminScopeService();
    }

    /**
     * ADM-06: Reports list with status filter and regional scope
     */
    public function index()
    {
        $statusFilter = $this->request->getGet('status');
        $regionId = $this->scopeService->getAdminRegionId();
        if ($this->scopeService->isSuperAdmin()) {
            $regionId = null; // Unrestricted for Super Admin
        }

        $reports = $this->reportModel->getReportsWithDetails($statusFilter ?: null, $regionId);

        $counts = [
            'all'           => count($this->reportModel->getReportsWithDetails(null, $regionId)),
            'open'          => count($this->reportModel->getReportsWithDetails('open', $regionId)),
            'investigating' => count($this->reportModel->getReportsWithDetails('investigating', $regionId)),
            'resolved'      => count($this->reportModel->getReportsWithDetails('resolved', $regionId)),
        ];

        return view('admin/reports/index', [
            'title'        => 'Laporan Pelanggaran & Pengaduan — Backoffice',
            'role'         => session()->get('role') ?? 'admin_daerah',
            'activeMenu'   => 'reports',
            'reports'      => $reports,
            'counts'       => $counts,
            'statusFilter' => $statusFilter,
            'scopedLabel'  => $this->scopeService->getScopedRegionLabel(),
        ]);
    }

    /**
     * ADM-07: Report detail and action handling
     */
    public function show(int $id)
    {
        $report = $this->reportModel->find($id);
        if (! $report) {
            return redirect()->to(base_url('/admin/reports'))->with('error', 'Laporan tidak ditemukan.');
        }

        $db = \Config\Database::connect();
        $reporter = $db->table('users')->where('id', $report['reporter_id'])->get()->getRowArray();
        $region   = $report['region_id'] ? $db->table('regions')->where('id', $report['region_id'])->get()->getRowArray() : null;

        // Fetch reported object detail
        $objectDetail = null;
        if ($report['object_type'] === 'listing') {
            $objectDetail = $db->table('listings')
                ->select('listings.*, properties.name as property_name, properties.address, users.name as provider_name')
                ->join('units', 'units.id = listings.unit_id')
                ->join('properties', 'properties.id = units.property_id')
                ->join('users', 'users.id = properties.provider_id')
                ->where('listings.id', $report['object_id'])
                ->get()->getRowArray();
        } elseif ($report['object_type'] === 'property') {
            $objectDetail = $db->table('properties')
                ->select('properties.*, users.name as provider_name')
                ->join('users', 'users.id = properties.provider_id')
                ->where('properties.id', $report['object_id'])
                ->get()->getRowArray();
        }

        return view('admin/reports/show', [
            'title'        => "Detail Laporan Pengaduan #RPT-{$id}",
            'role'         => session()->get('role') ?? 'admin_daerah',
            'activeMenu'   => 'reports',
            'report'       => $report,
            'reporter'     => $reporter,
            'region'       => $region,
            'objectDetail' => $objectDetail,
            'scopedLabel'  => $this->scopeService->getScopedRegionLabel(),
        ]);
    }

    /**
     * Set report to investigating
     */
    public function investigate(int $id)
    {
        $adminId = session()->get('user_id') ?? 2;
        $this->reportModel->update($id, [
            'status'         => 'investigating',
            'resolved_by_id' => $adminId,
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);

        $this->auditModel->record(
            $adminId,
            'REPORT_INVESTIGATING',
            'reports',
            $id,
            ['status' => 'open'],
            ['status' => 'investigating'],
            "Admin memulai investigasi laporan #RPT-{$id}."
        );

        return redirect()->to(base_url('/admin/reports/' . $id))->with('success', 'Status laporan diperbarui ke INVESTIGASI.');
    }

    /**
     * Resolve report with action notes
     */
    public function resolve(int $id)
    {
        $adminId = session()->get('user_id') ?? 2;
        $notes   = trim((string) $this->request->getPost('admin_notes'));

        if (empty($notes)) {
            return redirect()->back()->with('error', 'Catatan penyelesaian laporan wajib diisi.');
        }

        $now = date('Y-m-d H:i:s');
        $this->reportModel->update($id, [
            'status'         => 'resolved',
            'admin_notes'    => $notes,
            'resolved_by_id' => $adminId,
            'resolved_at'    => $now,
            'updated_at'     => $now,
        ]);

        $this->auditModel->record(
            $adminId,
            'REPORT_RESOLVED',
            'reports',
            $id,
            null,
            ['status' => 'resolved', 'notes' => $notes],
            "Laporan #RPT-{$id} diselesaikan dengan catatan: {$notes}"
        );

        return redirect()->to(base_url('/admin/reports/' . $id))->with('success', 'Laporan berhasil diselesaikan.');
    }
}
