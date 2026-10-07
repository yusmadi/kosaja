<?php

namespace App\Controllers\Provider;

use App\Controllers\BaseController;
use App\Models\ProviderProfileModel;
use App\Models\UserModel;
use App\Libraries\StorageService;

class Onboarding extends BaseController
{
    protected $providerProfileModel;
    protected $userModel;
    protected $storageService;

    public function __construct()
    {
        $this->providerProfileModel = new ProviderProfileModel();
        $this->userModel = new UserModel();
        $this->storageService = new StorageService();
    }

    /**
     * Display Provider Onboarding & Verification Page
     */
    public function index()
    {
        $userId = session()->get('user_id') ?? 3;
        $user = $this->userModel->find($userId);
        $profile = $this->providerProfileModel->getByUserId($userId);

        if (! $profile) {
            // Create default profile if not exists
            $this->providerProfileModel->insert([
                'user_id'             => $userId,
                'verification_status' => 'pending',
                'free_period_end'     => date('Y-m-d', strtotime('+6 months')),
            ]);
            $profile = $this->providerProfileModel->getByUserId($userId);
        }

        return view('provider/onboarding', [
            'title'      => 'Verifikasi Identitas Mitra (KYC) — KosAja',
            'role'       => 'provider',
            'activeMenu' => 'verification',
            'user'       => $user,
            'profile'    => $profile,
        ]);
    }

    /**
     * Submit or Update Verification Data (KTP & Bank)
     */
    public function update()
    {
        $userId = session()->get('user_id') ?? 3;
        $profile = $this->providerProfileModel->getByUserId($userId);

        $rules = [
            'bank_name'           => 'required|max_length[100]',
            'bank_account_number' => 'required|numeric|min_length[5]|max_length[30]',
        ];

        $messages = [
            'bank_name' => [
                'required' => 'Nama bank untuk pencairan dana wajib dipilih atau diisi.',
            ],
            'bank_account_number' => [
                'required' => 'Nomor rekening bank wajib diisi dengan angka.',
                'numeric'  => 'Nomor rekening hanya boleh berisi angka.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $updateData = [
            'bank_name'           => $this->request->getPost('bank_name'),
            'bank_account_number' => $this->request->getPost('bank_account_number'),
        ];

        // Process KTP Photo upload if provided
        $ktpFile = $this->request->getFile('id_card_photo');
        if ($ktpFile && $ktpFile->isValid() && ! $ktpFile->hasMoved()) {
            $uploadResult = $this->storageService->uploadImage($ktpFile, 'kyc', 5120);
            if (! $uploadResult['success']) {
                return redirect()->back()->withInput()->with('error', $uploadResult['error']);
            }

            // Remove old KTP if exists
            if (!empty($profile['id_card_photo'])) {
                $this->storageService->deleteImage($profile['id_card_photo']);
            }

            $updateData['id_card_photo'] = $uploadResult['url'];
        } elseif (empty($profile['id_card_photo'])) {
            return redirect()->back()->withInput()->with('error', 'Foto KTP / Kartu Identitas wajib diunggah untuk verifikasi.');
        }

        // If previously rejected or pending, reset status to pending for admin re-verification
        if (empty($profile['verification_status']) || $profile['verification_status'] === 'rejected') {
            $updateData['verification_status'] = 'pending';
            $updateData['rejection_reason'] = null;
        }

        if ($profile) {
            $this->providerProfileModel->update($profile['id'], $updateData);
        } else {
            $updateData['user_id'] = $userId;
            $updateData['free_period_end'] = date('Y-m-d', strtotime('+6 months'));
            $this->providerProfileModel->insert($updateData);
        }

        return redirect()->to('/provider/verification')->with('success', 'Data identitas dan rekening bank berhasil disimpan! Tim verifikasi kami akan meninjau dokumen Anda.');
    }
}
