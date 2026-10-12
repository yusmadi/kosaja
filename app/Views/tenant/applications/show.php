<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="bg-light py-4 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-teal text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('/applications') ?>" class="text-teal text-decoration-none">Pengajuan Sewa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail #APP-<?= str_pad($application['id'], 5, '0', STR_PAD_LEFT) ?></li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Status Pengajuan Sewa</h1>
                <p class="text-muted small mb-0">Nomor Registrasi Pengajuan: <strong>#APP-<?= str_pad($application['id'], 5, '0', STR_PAD_LEFT) ?></strong></p>
            </div>
            <div>
                <?php 
                    $status = $application['status'];
                    $badgeClass = match($status) {
                        'pending'   => 'bg-warning text-dark',
                        'approved'  => 'bg-success text-white',
                        'rejected'  => 'bg-danger text-white',
                        'cancelled' => 'bg-secondary text-white',
                        'expired'   => 'bg-dark text-white',
                        default     => 'bg-light text-dark',
                    };
                    $statusLabel = match($status) {
                        'pending'   => 'Menunggu Review Pemilik',
                        'approved'  => 'Disetujui (Reservasi Aktif)',
                        'rejected'  => 'Pengajuan Ditolak',
                        'cancelled' => 'Dibatalkan',
                        'expired'   => 'Kedaluwarsa',
                        default     => ucfirst($status),
                    };
                ?>
                <span class="badge <?= $badgeClass ?> px-3 py-2 fs-6 shadow-sm">
                    <?= esc($statusLabel) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Progress Journey Bar -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row text-center g-3 position-relative">
                <!-- Step 1 -->
                <div class="col-3">
                    <div class="step-circle bg-success text-white mx-auto mb-2">
                        <i class="bi bi-check-lg"></i>
                    </div>
                    <div class="fw-bold small text-dark">1. Pengajuan Dikirim</div>
                    <small class="text-muted d-none d-md-block" style="font-size: 0.72rem;"><?= date('d M, H:i', strtotime($application['created_at'])) ?></small>
                </div>
                <!-- Step 2 -->
                <div class="col-3">
                    <div class="step-circle <?= in_array($status, ['approved', 'rejected']) ? ($status === 'approved' ? 'bg-success text-white' : 'bg-danger text-white') : 'bg-warning text-dark' ?> mx-auto mb-2">
                        <i class="bi <?= $status === 'approved' ? 'bi-check-lg' : ($status === 'rejected' ? 'bi-x-lg' : 'bi-hourglass-split') ?>"></i>
                    </div>
                    <div class="fw-bold small text-dark">2. Review Pemilik</div>
                    <small class="text-muted d-none d-md-block" style="font-size: 0.72rem;">
                        <?= $status === 'approved' ? 'Disetujui' : ($status === 'rejected' ? 'Ditolak' : 'Sedang Ditinjau') ?>
                    </small>
                </div>
                <!-- Step 3 -->
                <div class="col-3">
                    <div class="step-circle <?= ($status === 'approved' && !empty($reservation) && $reservation['status'] === 'active') ? 'bg-primary text-white pulse-animation' : ($status === 'approved' ? 'bg-success text-white' : 'bg-light text-muted border') ?> mx-auto mb-2">
                        <i class="bi bi-lock-fill"></i>
                    </div>
                    <div class="fw-bold small text-dark">3. Reservasi & Checkout</div>
                    <small class="text-muted d-none d-md-block" style="font-size: 0.72rem;">
                        <?= ($status === 'approved' && !empty($reservation) && $reservation['status'] === 'active') ? 'Menunggu Pembayaran' : 'Kunci Kamar' ?>
                    </small>
                </div>
                <!-- Step 4 -->
                <div class="col-3">
                    <div class="step-circle bg-light text-muted border mx-auto mb-2">
                        <i class="bi bi-file-earmark-check"></i>
                    </div>
                    <div class="fw-bold small text-dark">4. Kontrak & Check-In</div>
                    <small class="text-muted d-none d-md-block" style="font-size: 0.72rem;">Tahap Pembayaran</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Reservation Banner (If Approved) -->
    <?php if ($status === 'approved' && !empty($reservation) && $reservation['status'] === 'active'): ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4 text-white overflow-hidden" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);">
            <div class="card-body p-4 p-md-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <span class="badge bg-warning text-dark px-3 py-1 mb-2 fw-bold text-uppercase">
                            <i class="bi bi-shield-lock-fill me-1"></i> Unit Telah Dikunci Khusus Untuk Anda
                        </span>
                        <h2 class="h3 fw-bold mb-2">Selamat! Pengajuan Sewa Anda Telah Disetujui</h2>
                        <p class="text-white-50 mb-3" style="line-height: 1.6;">
                            Pemilik properti telah menyetujui permohonan Anda. Kamar <strong><?= esc($application['unit_name']) ?></strong> saat ini berstatus <strong>RESERVED</strong> dan tidak dapat diambil oleh calon penyewa lain. Selesaikan checkout pembayaran sebelum batas waktu kedaluwarsa.
                        </p>
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div class="bg-black bg-opacity-25 px-3 py-2 rounded-3">
                                <span class="small text-white-50 d-block" style="font-size: 0.75rem;">Batas Waktu Pembayaran:</span>
                                <strong class="text-white fs-6">
                                    <i class="bi bi-alarm me-1 text-warning"></i> <?= date('d M Y, H:i', strtotime($reservation['expires_at'])) ?> WIB
                                </strong>
                            </div>
                            <div class="bg-black bg-opacity-25 px-3 py-2 rounded-3">
                                <span class="small text-white-50 d-block" style="font-size: 0.75rem;">Total Nominal Awal:</span>
                                <strong class="text-warning fs-6">
                                    Rp <?= number_format($application['rental_amount'] + ($application['gateway_fee'] > 0 ? $application['gateway_fee'] : $reservationConfig->gatewayFee), 0, ',', '.') ?>
                                </strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="<?= base_url('/checkout/' . $reservation['id']) ?>" class="btn btn-warning btn-lg fw-bold px-4 py-3 shadow-lg w-100 w-lg-auto text-dark">
                            <i class="bi bi-wallet2 me-2"></i> Lanjut ke Checkout & Bayar
                        </a>
                        <small class="d-block text-white-50 mt-2 text-center text-lg-end" style="font-size: 0.76rem;">
                            Pembayaran aman & bergaransi KosAja
                        </small>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif ($status === 'rejected'): ?>
        <!-- Rejection Notice -->
        <div class="card border-danger border-2 shadow-sm rounded-4 mb-4 bg-danger bg-opacity-10">
            <div class="card-body p-4">
                <div class="d-flex gap-3">
                    <div class="text-danger fs-2">
                        <i class="bi bi-x-circle-fill"></i>
                    </div>
                    <div>
                        <h3 class="h5 fw-bold text-danger mb-1">Pengajuan Sewa Tidak Disetujui oleh Pemilik</h3>
                        <p class="text-secondary small mb-2">
                            Mohon maaf, permohonan sewa Anda pada kamar ini belum dapat diterima oleh pemilik/pengelola properti.
                        </p>
                        <div class="p-3 bg-white rounded-3 border mb-3">
                            <span class="small fw-bold text-muted d-block mb-1">Alasan Penolakan dari Pemilik:</span>
                            <div class="text-dark small" style="white-space: pre-line;">
                                "<?= esc($application['rejection_reason'] ?? 'Kamar belum siap huni pada tanggal yang diajukan atau kuota hunian telah terisi.') ?>"
                            </div>
                        </div>
                        <a href="<?= base_url('/search') ?>" class="btn btn-danger btn-sm px-4 py-2">
                            <i class="bi bi-search me-1"></i> Cari Pilihan Hunian Lain di KosAja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php elseif ($status === 'pending'): ?>
        <!-- Pending Info Banner -->
        <div class="alert alert-warning border-0 shadow-sm rounded-3 p-4 mb-4" role="alert">
            <div class="d-flex gap-3 align-items-center">
                <i class="bi bi-hourglass-split display-6 text-warning"></i>
                <div>
                    <h3 class="h6 fw-bold text-dark mb-1">Pengajuan Sedang Ditinjau oleh Pemilik Properti</h3>
                    <p class="text-secondary small mb-0">
                        Pemilik biasanya merespons dalam waktu 1x24 jam. Jika disetujui, kamar akan langsung dikunci otomatis menjadi status Reservasi Aktif untuk Anda.
                    </p>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Application & Room Specs -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h2 class="h5 fw-bold text-dark mb-0">Rincian Unit & Kontrak Sewa</h2>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-4">
                        <img src="<?= base_url(esc($application['unit_photo'] ?? ($application['property_photo'] ?? 'assets/images/sample-kos-1.jpg'))) ?>" 
                             alt="<?= esc($application['unit_name']) ?>" 
                             class="rounded-3 object-fit-cover shadow-sm" 
                             style="width: 80px; height: 80px;">
                        <div>
                            <h3 class="h6 fw-bold text-dark mb-1"><?= esc($application['listing_title']) ?></h3>
                            <div class="text-secondary small mb-1">
                                <strong>Kamar: <?= esc($application['unit_name']) ?></strong>
                                <?php if (!empty($application['unit_size'])): ?>
                                    • Ukuran: <?= esc($application['unit_size']) ?>
                                <?php endif; ?>
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-geo-alt me-1 text-danger"></i><?= esc($application['property_name']) ?>, <?= esc($application['region_name'] ?? 'Sleman') ?>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Tanggal Mulai Menempati:</span>
                            <strong class="text-dark"><?= date('d F Y', strtotime($application['start_date'])) ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Durasi Kontrak:</span>
                            <strong class="text-dark"><?= esc($application['duration_months']) ?> Bulan</strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Skema Pembayaran:</span>
                            <strong class="text-dark text-capitalize"><?= str_replace('_', ' ', esc($application['payment_schedule'])) ?></strong>
                        </div>
                        <div class="col-sm-6">
                            <span class="text-muted small d-block">Biaya Sewa Awal:</span>
                            <strong class="text-teal fs-6">Rp <?= number_format($application['rental_amount'], 0, ',', '.') ?></strong>
                        </div>

                        <?php if (!empty($application['marital_status']) && $application['target_gender'] === 'campur_keluarga'): ?>
                            <div class="col-12 border-top pt-3">
                                <span class="text-muted small d-block mb-1">Status Penghuni Keluarga / Pasutri:</span>
                                <span class="badge bg-purple text-white px-2 py-1" style="background-color: #7c3aed;">
                                    <i class="bi bi-people-fill me-1"></i> <?= esc(ucfirst(str_replace('_', ' ', $application['marital_status']))) ?>
                                </span>
                                <?php if ($application['is_family_verified']): ?>
                                    <span class="badge bg-success ms-1"><i class="bi bi-check-circle me-1"></i> Terverifikasi oleh Pemilik</span>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($application['applicant_notes'])): ?>
                            <div class="col-12 border-top pt-3">
                                <span class="text-muted small d-block mb-1">Catatan Anda untuk Pemilik:</span>
                                <div class="p-2 bg-light rounded text-secondary small">
                                    <?= nl2br(esc($application['applicant_notes'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Provider Contact & Quick Actions -->
        <div class="col-lg-4">
            <!-- Provider Card -->
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3 border-0">
                    <h3 class="h6 fw-bold text-dark mb-0"><i class="bi bi-person-badge me-2 text-teal"></i>Pemilik Properti</h3>
                </div>
                <div class="card-body pt-0">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-teal text-white fw-bold d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; font-size: 1.2rem;">
                            <?= esc(substr($application['provider_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <h4 class="h6 fw-bold text-dark mb-0"><?= esc($application['provider_name']) ?></h4>
                            <small class="text-muted">Mitra Terverifikasi KosAja</small>
                        </div>
                    </div>

                    <?php if (!empty($application['provider_whatsapp'])): ?>
                        <?php 
                            $waNum = preg_replace('/[^0-9]/', '', $application['provider_whatsapp']);
                            $waText = urlencode("Halo Bapak/Ibu {$application['provider_name']}, saya {$application['tenant_name']} mengenai pengajuan sewa #APP-{$application['id']} kamar {$application['unit_name']} di KosAja.");
                        ?>
                        <a href="https://wa.me/<?= $waNum ?>?text=<?= $waText ?>" target="_blank" class="btn btn-outline-success w-100 py-2 small fw-semibold mb-2">
                            <i class="bi bi-whatsapp me-1"></i> Hubungi Pemilik via WhatsApp
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cancel Application Action (If eligible) -->
            <?php if (in_array($status, ['pending', 'approved'])): ?>
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3 text-center">
                        <p class="text-muted small mb-2">Ingin membatalkan rencana sewa ini?</p>
                        <form action="<?= base_url('/applications/cancel/' . $application['id']) ?>" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan sewa ini? Unit akan dilepaskan kembali ke status tersedia.');">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="bi bi-x-circle me-1"></i> Batalkan Pengajuan
                            </button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
    .step-circle {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: bold;
    }
</style>
<?= $this->endSection() ?>
