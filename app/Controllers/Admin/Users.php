<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RegionModel;
use App\Models\AuditLogModel;

class Users extends BaseController
{
    protected $userModel;
    protected $regionModel;
    protected $auditModel;

    public function __construct()
    {
        $this->userModel   = new UserModel();
        $this->regionModel = new RegionModel();
        $this->auditModel  = new AuditLogModel();
    }

    /**
     * Check if user is Super Admin
     */
    protected function checkSuperAdmin()
    {
        if (session()->get('role') !== 'super_admin') {
            return redirect()->to(base_url('/admin/dashboard'))->with('error', 'Akses ditolak: Manajemen Akun & Otoritas (IAM) hanya dapat diakses oleh Super Administrator.');
        }
        return null;
    }

    public function index()
    {
        if ($redirect = $this->checkSuperAdmin()) {
            return $redirect;
        }

        $db = \Config\Database::connect();
        $users = $db->table('users')
            ->select('users.*, regions.name as region_name')
            ->join('regions', 'regions.id = users.region_id', 'left')
            ->orderBy('users.id', 'ASC')
            ->get()->getResultArray();

        $regions = $this->regionModel->getHierarchy();

        return view('admin/users', [
            'title'      => 'Manajemen Pengguna & Otoritas (IAM SA-02) — Super Admin',
            'role'       => 'super_admin',
            'activeMenu' => 'users',
            'users'      => $users,
            'regions'    => $regions,
        ]);
    }

    /**
     * Tambah Pengguna / Admin Daerah Baru (IAM SA-02)
     */
    public function create()
    {
        if ($redirect = $this->checkSuperAdmin()) {
            return $redirect;
        }

        $rules = [
            'name'      => 'required|min_length[3]|max_length[255]',
            'email'     => 'required|valid_email|is_unique[users.email]',
            'password'  => 'required|min_length[6]',
            'whatsapp'  => 'permit_empty|min_length[9]|max_length[20]',
            'gender'    => 'required|in_list[L,P]',
            'role'      => 'required|in_list[super_admin,admin_daerah,provider,tenant]',
            'region_id' => 'permit_empty|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $role = $this->request->getPost('role');
        $regionId = $this->request->getPost('region_id');

        // Admin daerah wajib ditentukan wilayah operasional tugasnya
        if ($role === 'admin_daerah' && empty($regionId)) {
            return redirect()->back()->withInput()->with('error', 'Admin Daerah wajib ditentukan wilayah operasional tugasnya.');
        }

        $data = [
            'name'       => trim($this->request->getPost('name')),
            'email'      => trim(strtolower($this->request->getPost('email'))),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'whatsapp'   => trim($this->request->getPost('whatsapp')),
            'gender'     => $this->request->getPost('gender'),
            'role'       => $role,
            'region_id'  => (! empty($regionId) && in_array($role, ['admin_daerah', 'provider'])) ? (int) $regionId : null,
            'status'     => 'active',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $userId = $this->userModel->insert($data);

        // Cari nama wilayah untuk catatan audit
        $regionName = 'Nasional / Multi-Region';
        if (! empty($data['region_id'])) {
            $reg = $this->regionModel->find($data['region_id']);
            $regionName = $reg['name'] ?? 'Wilayah ID #' . $data['region_id'];
        }

        $this->auditModel->record(
            session()->get('user_id') ?? 1,
            'USER_CREATED',
            'users',
            $userId,
            null,
            [
                'name'      => $data['name'],
                'email'     => $data['email'],
                'role'      => $data['role'],
                'region_id' => $data['region_id'],
            ],
            "Super Admin mendaftarkan akun baru: {$data['name']} (Peran: " . strtoupper($data['role']) . ", Wilayah: {$regionName})"
        );

        return redirect()->to('/admin/users')->with('success', "Akun {$data['name']} berhasil didaftarkan sebagai " . strtoupper(str_replace('_', ' ', $data['role'])) . " ({$regionName})!");
    }

    public function toggleStatus($id)
    {
        if ($redirect = $this->checkSuperAdmin()) {
            return $redirect;
        }

        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/admin/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        // Prevent suspending super_admin user 1
        if ($user['id'] == 1) {
            return redirect()->to('/admin/users')->with('error', 'Super Administrator utama tidak dapat dinonaktifkan.');
        }

        $newStatus = ($user['status'] === 'active') ? 'suspended' : 'active';
        $this->userModel->update($id, ['status' => $newStatus]);

        $this->auditModel->record(
            session()->get('user_id') ?? 1,
            'USER_STATUS_TOGGLED',
            'users',
            $id,
            ['status' => $user['status']],
            ['status' => $newStatus],
            "Super Admin mengubah status akun {$user['name']} menjadi " . strtoupper($newStatus)
        );

        return redirect()->to('/admin/users')->with('success', "Status akun {$user['name']} diubah menjadi " . strtoupper($newStatus));
    }

    public function assignRegion($id)
    {
        if ($redirect = $this->checkSuperAdmin()) {
            return $redirect;
        }

        $user = $this->userModel->find($id);
        if (! $user) {
            return redirect()->to('/admin/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        $oldRegionId = $user['region_id'];
        $newRegionId = $this->request->getPost('region_id');
        $newRegionId = ! empty($newRegionId) ? (int) $newRegionId : null;

        $this->userModel->update($id, ['region_id' => $newRegionId]);

        $oldRegion = $oldRegionId ? $this->regionModel->find($oldRegionId) : null;
        $newRegion = $newRegionId ? $this->regionModel->find($newRegionId) : null;
        $oldRegionName = $oldRegion['name'] ?? 'Belum Ditugaskan';
        $newRegionName = $newRegion['name'] ?? 'Dilepas (Tanpa Wilayah)';

        $this->auditModel->record(
            session()->get('user_id') ?? 1,
            'ADMIN_REGION_ASSIGNED',
            'users',
            $id,
            ['region_id' => $oldRegionId, 'region_name' => $oldRegionName],
            ['region_id' => $newRegionId, 'region_name' => $newRegionName],
            "Super Admin memperbarui wilayah tugas {$user['name']} dari [{$oldRegionName}] menjadi [{$newRegionName}]"
        );

        return redirect()->to('/admin/users')->with('success', "Wilayah tugas untuk {$user['name']} berhasil diperbarui menjadi: {$newRegionName}.");
    }
}
