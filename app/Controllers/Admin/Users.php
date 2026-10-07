<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function index()
    {
        $users = $this->userModel->orderBy('id', 'DESC')->findAll();

        return view('admin/users', [
            'title'      => 'Manajemen Pengguna & Otoritas (IAM)',
            'role'       => 'admin',
            'activeMenu' => 'users',
            'users'      => $users,
        ]);
    }

    public function toggleStatus($id)
    {
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

        return redirect()->to('/admin/users')->with('success', "Status akun {$user['name']} diubah menjadi " . strtoupper($newStatus));
    }
}
