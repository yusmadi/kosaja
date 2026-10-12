<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RegionModel;

class Regions extends BaseController
{
    protected $regionModel;
    protected $auditModel;

    public function __construct()
    {
        $this->regionModel = new RegionModel();
        $this->auditModel  = new \App\Models\AuditLogModel();
    }

    public function index()
    {
        if (session()->get('role') !== 'super_admin') {
            return redirect()->to(base_url('/admin/dashboard'))->with('error', 'Akses ditolak: Master Wilayah Operasional (Regions) hanya dapat dikelola oleh Super Administrator.');
        }

        $regions = $this->regionModel->getHierarchy();
        $provincesAndCities = array_values(array_filter($regions, static function ($r) {
            return in_array($r['level'], ['province', 'city'], true);
        }));

        return view('admin/regions', [
            'title'              => 'Master Wilayah Operasional (Regions SA-04) — Super Admin',
            'role'               => 'super_admin',
            'activeMenu'         => 'regions',
            'regions'            => $regions,
            'provincesAndCities' => $provincesAndCities,
        ]);
    }

    public function create()
    {
        $rules = [
            'name'  => 'required|min_length[2]|max_length[255]',
            'level' => 'required|in_list[province,city,operational]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $parentId = $this->request->getPost('parent_id');
        $data = [
            'name'      => $this->request->getPost('name'),
            'level'     => $this->request->getPost('level'),
            'parent_id' => ! empty($parentId) ? $parentId : null,
        ];

        $regionId = $this->regionModel->insert($data);

        // Record Audit Trail
        $adminId = session()->get('user_id') ?? 1;
        $this->auditModel->record(
            $adminId,
            'CREATE_REGION',
            'regions',
            $regionId,
            null,
            $data,
            "Super Admin menambahkan wilayah operasional baru: {$data['name']} (Level: {$data['level']})"
        );

        return redirect()->to('/admin/regions')->with('success', 'Wilayah operasional berhasil ditambahkan!');
    }

    public function delete($id)
    {
        $region = $this->regionModel->find($id);
        $this->regionModel->delete($id);

        if ($region) {
            $adminId = session()->get('user_id') ?? 1;
            $this->auditModel->record(
                $adminId,
                'DELETE_REGION',
                'regions',
                (int) $id,
                $region,
                null,
                "Super Admin menghapus wilayah operasional: {$region['name']} (Level: {$region['level']})"
            );
        }

        return redirect()->to('/admin/regions')->with('success', 'Wilayah berhasil dihapus.');
    }
}
