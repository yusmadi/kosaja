<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProviderProfileModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $providerProfileModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->providerProfileModel = new ProviderProfileModel();
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
                'free_period_end'     => date('Y-m-d', strtotime('+6 months')),
            ]);
        }

        return redirect()->to('/login')->with('success', 'Pendaftaran akun berhasil! Silakan masuk dengan email dan password Anda.');
    }

    /**
     * Process Logout
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('success', 'Anda telah berhasil keluar (logout).');
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
