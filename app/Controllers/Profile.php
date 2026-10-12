<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\ProviderProfileModel;
use App\Models\AuditLogModel;

class Profile extends BaseController
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
     * Show Profile Page
     */
    public function index()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (! $user) {
            return redirect()->to('/login')->with('error', 'Sesi telah berakhir.');
        }

        $providerProfile = null;
        if ($user['role'] === 'provider') {
            $providerProfile = $this->providerProfileModel->getByUserId($userId);
        }

        return view('profile/index', [
            'title'           => 'Profil Akun Saya — KosAja',
            'user'            => $user,
            'providerProfile' => $providerProfile,
        ]);
    }

    /**
     * Update Basic Profile (Name, WhatsApp, Gender, Provider Bank Info)
     */
    public function update()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        $rules = [
            'name'     => 'required|min_length[3]|max_length[255]',
            'whatsapp' => 'required|numeric|min_length[9]|max_length[15]',
            'gender'   => 'required|in_list[L,P]',
        ];

        $messages = [
            'gender' => [
                'required' => 'Jenis kelamin wajib diisi demi kepatuhan Syariat.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userData = [
            'name'     => $this->request->getPost('name'),
            'whatsapp' => $this->request->getPost('whatsapp'),
            'gender'   => $this->request->getPost('gender'),
        ];

        $this->userModel->update($userId, $userData);

        // Update session
        session()->set([
            'name'     => $userData['name'],
            'whatsapp' => $userData['whatsapp'],
            'gender'   => $userData['gender'],
        ]);

        // If provider, update bank information
        if ($user['role'] === 'provider') {
            $profile = $this->providerProfileModel->getByUserId($userId);
            $profileData = [
                'bank_name'           => $this->request->getPost('bank_name'),
                'bank_account_number' => $this->request->getPost('bank_account_number'),
            ];

            if ($profile) {
                $this->providerProfileModel->update($profile['id'], $profileData);
            } else {
                $profileData['user_id'] = $userId;
                $profileData['verification_status'] = 'pending';
                $this->providerProfileModel->insert($profileData);
            }
        }

        // Record Audit Trail
        $this->auditModel->record(
            (int) $userId,
            'UPDATE_PROFILE',
            'users',
            (int) $userId,
            null,
            $userData,
            "Pengguna {$userData['name']} memperbarui informasi profil akun."
        );

        return redirect()->to('/profile')->with('success', 'Profil Anda berhasil diperbarui.');
    }

    /**
     * Change Password
     */
    public function updatePassword()
    {
        $userId = session()->get('user_id');
        $user = $this->userModel->find($userId);

        $rules = [
            'current_password' => 'required',
            'new_password'     => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $currentPassword = $this->request->getPost('current_password');
        if (! password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Password saat ini tidak sesuai.');
        }

        $newPassword = $this->request->getPost('new_password');
        $this->userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_BCRYPT),
        ]);

        // Record Audit Trail
        $this->auditModel->record(
            (int) $userId,
            'UPDATE_PASSWORD',
            'users',
            (int) $userId,
            null,
            null,
            "Pengguna {$user['name']} berhasil memperbarui kata sandi (password) akun."
        );

        return redirect()->to('/profile')->with('success', 'Password Anda berhasil diperbarui!');
    }
}
