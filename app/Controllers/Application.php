<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\RentalApplicationModel;
use App\Models\ReservationModel;
use App\Models\ListingModel;
use App\Models\UnitModel;
use App\Models\UserModel;
use App\Models\AuditLogModel;
use App\Libraries\StorageService;
use Config\ReservationConfig;

class Application extends BaseController
{
    protected RentalApplicationModel $applicationModel;
    protected ReservationModel $reservationModel;
    protected ListingModel $listingModel;
    protected UnitModel $unitModel;
    protected UserModel $userModel;
    protected AuditLogModel $auditModel;
    protected StorageService $storageService;
    protected ReservationConfig $reservationConfig;

    public function __construct()
    {
        $this->applicationModel  = new RentalApplicationModel();
        $this->reservationModel  = new ReservationModel();
        $this->listingModel      = new ListingModel();
        $this->unitModel         = new UnitModel();
        $this->userModel         = new UserModel();
        $this->auditModel        = new AuditLogModel();
        $this->storageService    = new StorageService();
        $this->reservationConfig = config('ReservationConfig') ?? new ReservationConfig();
    }

    /**
     * Display Rental Application Form for a specific listing
     */
    public function create(int $listingId)
    {
        // 1. Ensure user is logged in
        if (! session()->get('isLoggedIn')) {
            session()->set('redirect_url', current_url());
            return redirect()->to(base_url('/login'))->with('info', 'Silakan masuk ke akun Anda terlebih dahulu untuk mengajukan sewa hunian.');
        }

        $userId = (int) session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (! $user) {
            return redirect()->to(base_url('/login'))->with('error', 'Sesi login tidak valid. Silakan masuk kembali.');
        }

        // 2. Fetch listing with details
        $listing = $this->listingModel->getListingWithDetails($listingId);
        if (! $listing || $listing['publication_status'] !== 'published') {
            return redirect()->to(base_url('/search'))->with('error', 'Listing hunian tidak ditemukan atau belum dipublikasikan.');
        }

        // 3. Check unit availability
        if ($listing['unit_status'] !== 'available') {
            return redirect()->to(base_url('/listing/' . $listingId))->with('error', 'Kamar / unit ini sedang tidak tersedia untuk pengajuan sewa baru (Status: ' . ucfirst($listing['unit_status']) . ').');
        }

        // 4. Check duplicate active application
        if ($this->applicationModel->hasActiveApplication($userId, (int) $listing['unit_id'])) {
            $existingApp = $this->applicationModel->where('tenant_id', $userId)
                                                  ->where('unit_id', $listing['unit_id'])
                                                  ->whereIn('status', ['pending', 'approved'])
                                                  ->first();
            return redirect()->to(base_url('/applications/' . $existingApp['id']))
                             ->with('info', 'Anda sudah memiliki pengajuan aktif untuk kamar ini. Silakan pantau perkembangannya.');
        }

        // 5. Gender Syariat Validation (Automatic strict check)
        $userGender   = strtoupper($user['gender'] ?? '');
        $targetGender = strtolower($listing['target_gender'] ?? 'campur_keluarga');

        if (empty($userGender)) {
            return redirect()->to(base_url('/profile'))->with('error', 'Demi kepatuhan syariat hunian, silakan lengkapi data Jenis Kelamin pada profil Anda sebelum mengajukan sewa.');
        }

        if ($targetGender === 'putra' && $userGender === 'P') {
            return redirect()->to(base_url('/listing/' . $listingId))->with('error', 'SISTEM MENOLAK OTOMATIS: Properti ini dikhususkan bagi penghuni PUTRA. Jenis kelamin akun Anda adalah PEREMPUAN. Demi kenyamanan dan kepatuhan syariat, Anda tidak dapat mengajukan sewa di properti ini.');
        }

        if ($targetGender === 'putri' && $userGender === 'L') {
            return redirect()->to(base_url('/listing/' . $listingId))->with('error', 'SISTEM MENOLAK OTOMATIS: Properti ini dikhususkan bagi penghuni PUTRI. Jenis kelamin akun Anda adalah LAKI-LAKI. Demi kenyamanan dan kepatuhan syariat, Anda tidak dapat mengajukan sewa di properti ini.');
        }

        return view('tenant/applications/create', [
            'title'             => 'Form Pengajuan Sewa — ' . esc($listing['title']),
            'listing'           => $listing,
            'user'              => $user,
            'reservationConfig' => $this->reservationConfig,
        ]);
    }

    /**
     * Submit Rental Application
     */
    public function store(int $listingId)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'))->with('error', 'Sesi telah berakhir.');
        }

        $userId = (int) session()->get('user_id');
        $user = $this->userModel->find($userId);

        $listing = $this->listingModel->getListingWithDetails($listingId);
        if (! $listing || $listing['publication_status'] !== 'published') {
            return redirect()->to(base_url('/search'))->with('error', 'Listing tidak valid.');
        }

        // Gender enforcement check
        $userGender   = strtoupper($user['gender'] ?? '');
        $targetGender = strtolower($listing['target_gender'] ?? '');

        if ($targetGender === 'putra' && $userGender === 'P') {
            return redirect()->to(base_url('/listing/' . $listingId))->with('error', 'Pengajuan ditolak otomatis: Properti khusus PUTRA.');
        }
        if ($targetGender === 'putri' && $userGender === 'L') {
            return redirect()->to(base_url('/listing/' . $listingId))->with('error', 'Pengajuan ditolak otomatis: Properti khusus PUTRI.');
        }

        // Check if unit is still available
        if ($listing['unit_status'] !== 'available') {
            return redirect()->to(base_url('/listing/' . $listingId))->with('error', 'Kamar ini sudah tidak tersedia.');
        }

        // Validation rules
        $rules = [
            'start_date'       => 'required|valid_date',
            'duration_months'  => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[36]',
            'payment_schedule' => 'required|in_list[monthly,3_monthly,6_monthly,yearly,full_upfront]',
            'syariat_consent'  => 'required',
        ];

        // If campur_keluarga, require marital_status
        if ($targetGender === 'campur_keluarga') {
            $rules['marital_status'] = 'required|in_list[menikah,keluarga_sah]';
        }

        $messages = [
            'start_date' => [
                'required'   => 'Tanggal mulai sewa wajib dipilih.',
                'valid_date' => 'Format tanggal mulai sewa tidak valid.',
            ],
            'duration_months' => [
                'required'              => 'Durasi sewa wajib dipilih.',
                'greater_than_equal_to' => 'Durasi sewa minimal 1 bulan.',
            ],
            'payment_schedule' => [
                'required' => 'Skema pembayaran sewa wajib dipilih.',
                'in_list'  => 'Pilihan skema pembayaran tidak valid.',
            ],
            'marital_status' => [
                'required' => 'Untuk hunian Pasutri/Keluarga, status pernikahan wajib dipilih.',
            ],
            'syariat_consent' => [
                'required' => 'Anda wajib menyetujui pernyataan tata tertib dan kepatuhan syariat lingkungan.',
            ],
        ];

        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $startDate       = $this->request->getPost('start_date');
        $durationMonths  = (int) $this->request->getPost('duration_months');
        $paymentSchedule = $this->request->getPost('payment_schedule');
        $maritalStatus   = $this->request->getPost('marital_status') ?? 'lajang';
        $applicantNotes  = $this->request->getPost('applicant_notes');

        // Verify start date is not in the past
        if (strtotime($startDate) < strtotime(date('Y-m-d'))) {
            return redirect()->back()->withInput()->with('error', 'Tanggal mulai sewa tidak boleh di masa lampau.');
        }

        // Calculate rental amount for the first payment schedule
        $monthlyPrice    = (float) $listing['price_monthly'];
        $sixMonthlyPrice = !empty($listing['price_six_monthly']) ? (float) $listing['price_six_monthly'] : ($monthlyPrice * min(6, $durationMonths));
        $yearlyPrice     = !empty($listing['price_yearly']) ? (float) $listing['price_yearly'] : ($monthlyPrice * 12);

        $rentalAmount = match ($paymentSchedule) {
            'monthly'      => $monthlyPrice,
            '3_monthly'    => $monthlyPrice * min(3, $durationMonths),
            '6_monthly'    => $sixMonthlyPrice,
            'yearly'       => ($durationMonths >= 12 && !empty($listing['price_yearly'])) ? $yearlyPrice : ($monthlyPrice * 12),
            'full_upfront' => ($durationMonths >= 12 && !empty($listing['price_yearly'])) ? ($yearlyPrice * floor($durationMonths / 12) + ($monthlyPrice * ($durationMonths % 12))) : ($monthlyPrice * $durationMonths),
            default        => $monthlyPrice,
        };

        // Handle marriage certificate upload if provided
        $marriageCertUrl = null;
        $certFile = $this->request->getFile('marriage_certificate');
        if ($certFile && $certFile->isValid() && ! $certFile->hasMoved()) {
            $uploadRes = $this->storageService->uploadImage($certFile, 'applications', 5120);
            if ($uploadRes['success']) {
                $marriageCertUrl = $uploadRes['url'];
            } else {
                return redirect()->back()->withInput()->with('error', 'Gagal mengunggah dokumen buku nikah: ' . $uploadRes['error']);
            }
        }

        // Insert rental application
        $insertData = [
            'tenant_id'                => $userId,
            'listing_id'               => $listingId,
            'unit_id'                  => (int) $listing['unit_id'],
            'start_date'               => $startDate,
            'duration_months'          => $durationMonths,
            'payment_schedule'         => $paymentSchedule,
            'marital_status'           => $maritalStatus,
            'rental_amount'            => $rentalAmount,
            'gateway_fee'              => $this->reservationConfig->gatewayFee,
            'applicant_notes'          => $applicantNotes,
            'marriage_certificate_url' => $marriageCertUrl,
            'is_family_verified'       => 0,
            'status'                   => 'pending',
            'rejection_reason'         => null,
        ];

        $appId = $this->applicationModel->insert($insertData);

        // Record Audit Log
        $this->auditModel->record(
            $userId,
            'SUBMIT_APPLICATION',
            'rental_applications',
            $appId,
            null,
            $insertData,
            'Penyewa mengajukan sewa kamar unit ' . $listing['unit_name']
        );

        return redirect()->to(base_url('/applications/' . $appId))
                         ->with('success', 'Pengajuan sewa Anda berhasil dikirim ke pemilik hunian! Mohon tunggu konfirmasi persetujuan dari penyedia.');
    }

    /**
     * Tenant Applications List
     */
    public function index()
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'))->with('error', 'Silakan masuk terlebih dahulu.');
        }

        // Trigger reservation expiration sweep
        $this->reservationModel->checkAndExpireReservations();

        $userId = (int) session()->get('user_id');
        $applications = $this->applicationModel->getApplicationsByTenant($userId);

        return view('tenant/applications/index', [
            'title'        => 'Pengajuan Sewa Saya — KosAja',
            'applications' => $applications,
        ]);
    }

    /**
     * Tenant Application Detail
     */
    public function show(int $id)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'))->with('error', 'Silakan masuk terlebih dahulu.');
        }

        // Trigger reservation expiration sweep
        $this->reservationModel->checkAndExpireReservations();

        $userId = (int) session()->get('user_id');
        $application = $this->applicationModel->getApplicationWithDetails($id);

        if (! $application) {
            return redirect()->to(base_url('/applications'))->with('error', 'Data pengajuan sewa tidak ditemukan.');
        }

        // Authorization check: only tenant or provider can view
        if ((int) $application['tenant_id'] !== $userId && (int) $application['provider_id'] !== $userId && ! in_array(session()->get('role'), ['super_admin', 'admin_daerah'])) {
            return redirect()->to(base_url('/applications'))->with('error', 'Anda tidak memiliki akses ke pengajuan ini.');
        }

        // If reservation exists, fetch details
        $reservation = null;
        if (! empty($application['reservation_id'])) {
            $reservation = $this->reservationModel->find($application['reservation_id']);
        }

        return view('tenant/applications/show', [
            'title'             => 'Detail Pengajuan Sewa #' . $application['id'] . ' — KosAja',
            'application'       => $application,
            'reservation'       => $reservation,
            'reservationConfig' => $this->reservationConfig,
        ]);
    }

    /**
     * Cancel an application (by Tenant)
     */
    public function cancel(int $id)
    {
        if (! session()->get('isLoggedIn')) {
            return redirect()->to(base_url('/login'));
        }

        $userId = (int) session()->get('user_id');
        $application = $this->applicationModel->find($id);

        if (! $application || (int) $application['tenant_id'] !== $userId) {
            return redirect()->to(base_url('/applications'))->with('error', 'Pengajuan tidak ditemukan.');
        }

        if (! in_array($application['status'], ['pending', 'approved'])) {
            return redirect()->to(base_url('/applications/' . $id))->with('error', 'Pengajuan dengan status ini tidak dapat dibatalkan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Update application status
        $this->applicationModel->update($id, [
            'status' => 'cancelled',
        ]);

        // 2. If reservation exists, cancel it and release unit
        $reservation = $this->reservationModel->where('application_id', $id)->first();
        if ($reservation) {
            $this->reservationModel->update($reservation['id'], [
                'status' => 'cancelled',
            ]);
            $this->unitModel->update($application['unit_id'], [
                'status' => 'available',
            ]);
        }

        $db->transComplete();

        $this->auditModel->record(
            $userId,
            'CANCEL_APPLICATION',
            'rental_applications',
            $id,
            ['status' => $application['status']],
            ['status' => 'cancelled'],
            'Penyewa membatalkan pengajuan sewa.'
        );

        return redirect()->to(base_url('/applications/' . $id))->with('success', 'Pengajuan sewa telah berhasil dibatalkan.');
    }
}
