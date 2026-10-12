<?php

namespace App\Controllers\Provider;

use App\Controllers\BaseController;
use App\Models\RentalApplicationModel;
use App\Models\ReservationModel;
use App\Models\UnitModel;
use App\Models\AuditLogModel;
use App\Libraries\WhatsAppService;
use Config\ReservationConfig;

class Applications extends BaseController
{
    protected RentalApplicationModel $applicationModel;
    protected ReservationModel $reservationModel;
    protected UnitModel $unitModel;
    protected AuditLogModel $auditModel;
    protected ReservationConfig $reservationConfig;
    protected WhatsAppService $whatsappService;

    public function __construct()
    {
        $this->applicationModel  = new RentalApplicationModel();
        $this->reservationModel  = new ReservationModel();
        $this->unitModel         = new UnitModel();
        $this->auditModel        = new AuditLogModel();
        $this->reservationConfig = config('ReservationConfig') ?? new ReservationConfig();
        $this->whatsappService   = new WhatsAppService();
    }

    /**
     * List all incoming rental applications for the logged-in provider
     */
    public function index()
    {
        $userId = (int) (session()->get('user_id') ?? 3);

        // Auto expire outdated reservations
        $this->reservationModel->checkAndExpireReservations();

        $statusFilter = $this->request->getGet('status');
        $applications = $this->applicationModel->getApplicationsByProvider($userId, $statusFilter);

        // Counts for tabs
        $allApps = $this->applicationModel->getApplicationsByProvider($userId, null);
        $counts = [
            'all'      => count($allApps),
            'pending'  => count(array_filter($allApps, fn($a) => $a['status'] === 'pending')),
            'approved' => count(array_filter($allApps, fn($a) => $a['status'] === 'approved')),
            'rejected' => count(array_filter($allApps, fn($a) => $a['status'] === 'rejected')),
            'expired'  => count(array_filter($allApps, fn($a) => $a['status'] === 'expired')),
        ];

        return view('provider/applications/index', [
            'title'        => 'Pengajuan Sewa Masuk — KosAja',
            'role'         => 'provider',
            'activeMenu'   => 'applications',
            'applications' => $applications,
            'counts'       => $counts,
            'currentTab'   => $statusFilter ?? 'all',
        ]);
    }

    /**
     * Show application details with Pasutri validation & approval/rejection controls
     */
    public function show(int $id)
    {
        $userId = (int) (session()->get('user_id') ?? 3);

        // Auto expire outdated reservations
        $this->reservationModel->checkAndExpireReservations();

        $application = $this->applicationModel->getApplicationWithDetails($id);

        if (! $application) {
            return redirect()->to(base_url('/provider/applications'))->with('error', 'Pengajuan sewa tidak ditemukan.');
        }

        // Verify that this property belongs to the provider (unless super admin)
        if ((int) $application['provider_id'] !== $userId && session()->get('role') !== 'super_admin') {
            return redirect()->to(base_url('/provider/applications'))->with('error', 'Anda tidak memiliki otorisasi untuk melihat pengajuan ini.');
        }

        // Fetch reservation if already approved
        $reservation = null;
        if (! empty($application['reservation_id'])) {
            $reservation = $this->reservationModel->find($application['reservation_id']);
        }

        return view('provider/applications/show', [
            'title'             => 'Review Pengajuan Sewa #' . $application['id'] . ' — KosAja',
            'role'              => 'provider',
            'activeMenu'        => 'applications',
            'application'       => $application,
            'reservation'       => $reservation,
            'reservationConfig' => $this->reservationConfig,
        ]);
    }

    /**
     * Approve Rental Application:
     * - State transition: PENDING -> APPROVED
     * - Validates Pasutri requirements if family property
     * - Locks Unit: AVAILABLE -> RESERVED
     * - Generates RESERVATION entity with Expiration Window
     * - Records Audit Log
     */
    public function approve(int $id)
    {
        $userId = (int) (session()->get('user_id') ?? 3);
        $application = $this->applicationModel->getApplicationWithDetails($id);

        if (! $application || ((int) $application['provider_id'] !== $userId && session()->get('role') !== 'super_admin')) {
            return redirect()->to(base_url('/provider/applications'))->with('error', 'Pengajuan tidak valid.');
        }

        if ($application['status'] !== 'pending') {
            return redirect()->to(base_url('/provider/applications/' . $id))
                             ->with('error', 'Hanya pengajuan berstatus PENDING yang dapat disetujui. Status saat ini: ' . strtoupper($application['status']));
        }

        // Check if unit is available
        if ($application['unit_status'] !== 'available') {
            return redirect()->to(base_url('/provider/applications/' . $id))
                             ->with('error', 'Gagal menyetujui: Unit fisik ini sedang tidak berstatus Available (Status: ' . ucfirst($application['unit_status']) . '). Pastikan unit tidak sedang ditempati atau dikunci pengajuan lain.');
        }

        // If campur_keluarga, check if provider checked family verification
        $isFamilyVerified = $this->request->getPost('confirm_family_check') ? 1 : 0;
        if ($application['target_gender'] === 'campur_keluarga' && ! $isFamilyVerified) {
            // If they didn't check the confirmation checkbox, require it
            return redirect()->to(base_url('/provider/applications/' . $id))
                             ->with('error', 'Untuk properti keluarga/pasutri, Anda wajib mencentang konfirmasi verifikasi dokumen nikah/keluarga sebelum menyetujui pengajuan.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1. Update application status to APPROVED
        $this->applicationModel->update($id, [
            'status'             => 'approved',
            'is_family_verified' => $isFamilyVerified,
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);

        // 2. Lock unit status to RESERVED
        $this->unitModel->update($application['unit_id'], [
            'status'     => 'reserved',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        // 3. Create RESERVATION entity with Expiration Window
        $windowHours = $this->reservationConfig->expirationWindowHours;
        $expiresAt   = date('Y-m-d H:i:s', strtotime("+{$windowHours} hours"));

        $reservationId = $this->reservationModel->insert([
            'application_id' => $id,
            'expires_at'     => $expiresAt,
            'status'         => 'active',
        ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->to(base_url('/provider/applications/' . $id))->with('error', 'Terjadi kesalahan sistem saat memproses persetujuan pengajuan.');
        }

        // 4. Audit Trail
        $this->auditModel->record(
            $userId,
            'APPROVE_APPLICATION',
            'rental_applications',
            $id,
            ['status' => 'pending'],
            ['status' => 'approved', 'reservation_id' => $reservationId, 'expires_at' => $expiresAt],
            'Pemilik menyetujui pengajuan sewa. Reservasi dibentuk dengan batas kedaluwarsa ' . $windowHours . ' jam.'
        );

        $this->auditModel->record(
            $userId,
            'CREATE_RESERVATION',
            'reservations',
            $reservationId,
            null,
            ['application_id' => $id, 'expires_at' => $expiresAt, 'status' => 'active'],
            'Unit kamar ' . $application['unit_name'] . ' dikunci (RESERVED) untuk penyewa ' . $application['tenant_name']
        );

        // 5. Send WhatsApp notification to Tenant
        $resData = $this->reservationModel->find($reservationId);
        $this->whatsappService->sendApplicationApprovedNotification($application, $resData);

        return redirect()->to(base_url('/provider/applications/' . $id))
                         ->with('success', 'Pengajuan sewa BERHASIL DISETUJUI! Notifikasi WhatsApp telah dikirimkan ke calon penyewa. Unit telah dikunci (Reserved) dan reservasi aktif dibuat dengan batas pembayaran ' . $windowHours . ' jam (' . date('d M Y, H:i', strtotime($expiresAt)) . ').');
    }

    /**
     * Reject Rental Application:
     * - Requires mandatory rejection reason
     * - State transition: PENDING -> REJECTED
     * - Records Audit Log
     */
    public function reject(int $id)
    {
        $userId = (int) (session()->get('user_id') ?? 3);
        $application = $this->applicationModel->getApplicationWithDetails($id);

        if (! $application || ((int) $application['provider_id'] !== $userId && session()->get('role') !== 'super_admin')) {
            return redirect()->to(base_url('/provider/applications'))->with('error', 'Pengajuan tidak valid.');
        }

        if ($application['status'] !== 'pending') {
            return redirect()->to(base_url('/provider/applications/' . $id))
                             ->with('error', 'Hanya pengajuan berstatus PENDING yang dapat ditolak.');
        }

        $reason = trim((string) $this->request->getPost('rejection_reason'));
        if (empty($reason) || strlen($reason) < 5) {
            return redirect()->to(base_url('/provider/applications/' . $id))
                             ->with('error', 'Alasan penolakan pengajuan wajib diisi minimal 5 karakter untuk transparansi kepada penyewa.');
        }

        $this->applicationModel->update($id, [
            'status'           => 'rejected',
            'rejection_reason' => $reason,
            'updated_at'       => date('Y-m-d H:i:s'),
        ]);

        $this->auditModel->record(
            $userId,
            'REJECT_APPLICATION',
            'rental_applications',
            $id,
            ['status' => 'pending'],
            ['status' => 'rejected'],
            $reason
        );

        // Send WhatsApp notification to Tenant with rejection reason
        $this->whatsappService->sendApplicationRejectedNotification($application, $reason);

        return redirect()->to(base_url('/provider/applications/' . $id))
                         ->with('success', 'Pengajuan sewa telah ditolak. Notifikasi WhatsApp beserta alasan penolakan telah dikirimkan ke calon penyewa.');
    }

    /**
     * View list of active and past reservations
     */
    public function reservations()
    {
        $userId = (int) (session()->get('user_id') ?? 3);

        // Auto expire outdated reservations
        $this->reservationModel->checkAndExpireReservations();

        $reservations = $this->reservationModel->getReservationsByProvider($userId);

        return view('provider/applications/reservations', [
            'title'        => 'Daftar Reservasi Kamar — KosAja',
            'role'         => 'provider',
            'activeMenu'   => 'reservations',
            'reservations' => $reservations,
        ]);
    }
}
