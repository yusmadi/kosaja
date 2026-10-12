<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProviderProfileModel;
use App\Models\AuditLogModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $providerProfileModel;
    protected $auditModel;

    public function __construct()
    {
        $this->userModel            = new UserModel();
        $this->providerProfileModel = new ProviderProfileModel();
        $this->auditModel           = new AuditLogModel();
    }

    /**
     * Display Login Page
     */
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return $this->redirectBasedOnRole(session()->get('role'));
        }

        return view('auth/login', [
            'title' => 'Masuk ke Akun — KosAja',
        ]);
    }

    /**
     * Process Login Attempt
     */
    public function attemptLogin()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);

        if (! $user || ! password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Kombinasi Email atau Password salah. Silakan coba lagi.');
        }

        if ($user['status'] !== 'active') {
            return redirect()->back()->withInput()->with('error', 'Akun Anda berstatus non-aktif atau ditangguhkan. Silakan hubungi admin.');
        }

        // Set session
        session()->set([
            'isLoggedIn' => true,
            'user_id'    => $user['id'],
            'name'       => $user['name'],
            'email'      => $user['email'],
            'role'       => $user['role'],
            'gender'     => $user['gender'],
            'whatsapp'   => $user['whatsapp'],
        ]);

        // Record Audit Trail
        $this->auditModel->record(
            (int) $user['id'],
            'USER_LOGIN',
            'users',
            (int) $user['id'],
            null,
            ['role' => $user['role'], 'ip' => $this->request->getIPAddress()],
            "Pengguna {$user['name']} (" . strtoupper(str_replace('_', ' ', $user['role'])) . ") berhasil masuk ke sistem KosAja."
        );

        $redirectUrl = session()->get('redirect_url');
        if ($redirectUrl) {
            session()->remove('redirect_url');
            return redirect()->to($redirectUrl)->with('success', 'Selamat datang kembali, ' . esc($user['name']) . '!');
        }

        return $this->redirectBasedOnRole($user['role'])
                    ->with('success', 'Selamat datang kembali, ' . esc($user['name']) . '!');
    }

    /**
     * Display Register Page
     */
    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return $this->redirectBasedOnRole(session()->get('role'));
        }

        $defaultRole = $this->request->getGet('role') ?? 'tenant';
        if (! in_array($defaultRole, ['tenant', 'provider'])) {
            $defaultRole = 'tenant';
        }

        return view('auth/register', [
            'title'       => 'Daftar Akun Baru — KosAja',
            'defaultRole' => $defaultRole,
        ]);
    }

    /**
     * Process Registration Attempt
     */
    public function attemptRegister()
    {
        $rules = [
            'name'             => 'required|min_length[3]|max_length[255]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'whatsapp'         => 'required|numeric|min_length[9]|max_length[15]',
            'gender'           => 'required|in_list[L,P]',
            'role'             => 'required|in_list[tenant,provider]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        $messages = [
            'email' => [
                'is_unique' => 'Email ini sudah digunakan. Silakan gunakan email lain atau langsung Masuk.',
            ],
            'gender' => [
                'required' => 'Pilihan jenis kelamin wajib diisi demi kepatuhan Syariat hunian.',
            ],
            'password_confirm' => [
                'matches' => 'Konfirmasi password tidak cocok dengan password yang dimasukkan.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $role = $this->request->getPost('role');

        $userData = [
            'name'       => $this->request->getPost('name'),
            'email'      => $this->request->getPost('email'),
            'password'   => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
            'whatsapp'   => $this->request->getPost('whatsapp'),
            'gender'     => $this->request->getPost('gender'),
            'role'       => $role,
            'status'     => 'active',
        ];

        $newUserId = $this->userModel->insert($userData);

        if (! $newUserId) {
            return redirect()->back()->withInput()->with('error', 'Gagal mendaftarkan akun. Silakan ulangi.');
        }

        // If registered as provider, initialize provider_profile
        if ($role === 'provider') {
            $this->providerProfileModel->insert([
                'user_id'             => $newUserId,
                'verification_status' => 'pending',
                'bank_name'           => $this->request->getPost('bank_name') ?? null,
                'bank_account_number' => $this->request->getPost('bank_account_number') ?? null,
                'free_period_end'     => $this->providerProfileModel->calculateDefaultFreePeriodEnd(),
            ]);
        }

        // Record Audit Trail
        $this->auditModel->record(
            (int) $newUserId,
            'USER_REGISTER',
            'users',
            (int) $newUserId,
            null,
            ['name' => $userData['name'], 'email' => $userData['email'], 'role' => $role],
            "Pengguna baru {$userData['name']} mendaftar sebagai " . strtoupper(str_replace('_', ' ', $role)) . " di platform KosAja."
        );

        return redirect()->to('/login')->with('success', 'Pendaftaran akun berhasil! Silakan masuk dengan email dan password Anda.');
    }

    /**
     * Process Logout
     */
    public function logout()
    {
        $userId = session()->get('user_id');
        $name   = session()->get('name') ?? 'Pengguna';
        $role   = session()->get('role') ?? 'user';

        if ($userId) {
            $this->auditModel->record(
                (int) $userId,
                'USER_LOGOUT',
                'users',
                (int) $userId,
                null,
                null,
                "Pengguna {$name} (" . strtoupper(str_replace('_', ' ', $role)) . ") keluar (logout) dari sistem."
            );
        }

        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar (logout).');
    }

    /**
     * Quick Role Switcher for Testing (Super Admin vs Admin Daerah vs Provider vs Tenant)
     */
    public function switchRole(string $targetRole)
    {
        $db = \Config\Database::connect();
        $targetUser = null;

        if (is_numeric($targetRole)) {
            $targetUser = $db->table('users')->where('id', (int) $targetRole)->get()->getRowArray();
        } elseif ($targetRole === 'super_admin') {
            $targetUser = $db->table('users')->where('role', 'super_admin')->get()->getRowArray();
        } elseif ($targetRole === 'admin_daerah') {
            $targetUser = $db->table('users')->where('role', 'admin_daerah')->get()->getRowArray();
        } elseif ($targetRole === 'provider') {
            $targetUser = $db->table('users')->where('role', 'provider')->get()->getRowArray();
        } elseif ($targetRole === 'tenant') {
            $targetUser = $db->table('users')->where('role', 'tenant')->get()->getRowArray();
        }

        if ($targetUser) {
            session()->set([
                'isLoggedIn' => true,
                'user_id'    => $targetUser['id'],
                'name'       => $targetUser['name'],
                'email'      => $targetUser['email'],
                'role'       => $targetUser['role'],
                'gender'     => $targetUser['gender'],
                'whatsapp'   => $targetUser['whatsapp'],
            ]);

            $this->auditModel->record(
                (int) $targetUser['id'],
                'USER_ROLE_SWITCHED',
                'users',
                (int) $targetUser['id'],
                null,
                ['role' => $targetUser['role']],
                "Beralih role ke akun: {$targetUser['name']} (" . strtoupper(str_replace('_', ' ', $targetUser['role'])) . ")"
            );

            return $this->redirectBasedOnRole($targetUser['role'])
                ->with('success', "Beralih ke mode: " . strtoupper(str_replace('_', ' ', $targetUser['role'])) . " ({$targetUser['name']})");
        }

        return redirect()->to('/login');
    }

    /**
     * Helper to redirect based on user role
     */
    protected function redirectBasedOnRole(string $role)
    {
        switch ($role) {
            case 'super_admin':
            case 'admin_daerah':
                return redirect()->to('/admin/dashboard');
            case 'provider':
                return redirect()->to('/provider/dashboard');
            case 'tenant':
            default:
                return redirect()->to('/');
        }
    }
}
