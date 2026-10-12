<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AuditLogModel;
use App\Libraries\AdminScopeService;

class AuditTrail extends BaseController
{
    protected AuditLogModel $auditModel;
    protected AdminScopeService $scopeService;

    public function __construct()
    {
        $this->auditModel   = new AuditLogModel();
        $this->scopeService = new AdminScopeService();
    }

    /**
     * Interactive Immutable Audit Trail Viewer (with Regional Scoping & Jakarta WIB Timezone)
     */
    public function index()
    {
        $action       = $this->request->getGet('action');
        $objectType   = $this->request->getGet('object_type');
        $isSuperAdmin = $this->scopeService->isSuperAdmin();

        $builder = $this->auditModel
            ->select('audit_logs.*, users.name as actor_name, users.email as actor_email, users.role as actor_role, regions.name as region_name')
            ->join('users', 'users.id = audit_logs.actor_id', 'left')
            ->join('regions', 'regions.id = audit_logs.region_id', 'left')
            ->orderBy('audit_logs.id', 'DESC');

        // Apply regional scoping for Admin Daerah
        if (! $isSuperAdmin) {
            $scopedRegionIds = $this->scopeService->getScopedRegionIds() ?: [0];
            $adminUserId     = session()->get('user_id') ? (int) session()->get('user_id') : 0;

            $builder->groupStart()
                ->whereIn('audit_logs.region_id', $scopedRegionIds)
                ->orWhere('audit_logs.actor_id', $adminUserId)
                ->orWhereIn('users.region_id', $scopedRegionIds)
                ->groupEnd();
        }

        if (! empty($action)) {
            $builder->where('audit_logs.action', $action);
        }

        if (! empty($objectType)) {
            $builder->where('audit_logs.object_type', $objectType);
        }

        $logs = $builder->limit(100)->findAll();

        // Get unique action types for filter dropdown
        $actionsQuery = $this->auditModel->select('action')->distinct();
        if (! $isSuperAdmin) {
            $scopedRegionIds = $this->scopeService->getScopedRegionIds() ?: [0];
            $adminUserId     = session()->get('user_id') ? (int) session()->get('user_id') : 0;
            $actionsQuery->groupStart()
                ->whereIn('region_id', $scopedRegionIds)
                ->orWhere('actor_id', $adminUserId)
                ->groupEnd();
        }
        $actions = $actionsQuery->findAll();
        $actionList = array_column($actions, 'action');

        return view('admin/audit_logs/index', [
            'title'              => $isSuperAdmin ? 'Global Immutable Audit Trail (SA-12) — Super Admin' : 'Log Audit Aktivitas Regional (ADM-12) — KosAja',
            'role'               => session()->get('role') ?? ($isSuperAdmin ? 'super_admin' : 'admin_daerah'),
            'activeMenu'         => 'audit',
            'logs'               => $logs,
            'actionList'         => $actionList,
            'selectedAction'     => $action,
            'selectedObjectType' => $objectType,
            'scopedLabel'        => $this->scopeService->getScopedRegionLabel(),
            'isSuperAdmin'       => $isSuperAdmin,
        ]);
    }
}
