<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="bg-light py-4 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-teal text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('/applications') ?>" class="text-teal text-decoration-none">Pengajuan Sewa</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('/applications/' . $reservation['application_id']) ?>" class="text-teal text-decoration-none">Detail #APP-<?= str_pad($reservation['application_id'], 5, '0', STR_PAD_LEFT) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page">Checkout Reservasi</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Checkout Pembayaran Sewa</h1>
                <p class="text-muted small mb-0">Selesaikan transaksi sebelum batas kedaluwarsa reservasi untuk mengaktifkan kontrak sewa resmi</p>
            </div>
            <div>
                <span class="badge bg-success px-3 py-2 fs-6 shadow-sm">
                    <i class="bi bi-shield-check me-1"></i> Reservasi Terkunci (Active)
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

    <!-- Real-Time Countdown Timer Banner -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-dark text-white overflow-hidden">
        <div class="card-body p-4">
            <div class="row align-items-center g-3">
                <div class="col-md-7">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-hourglass-split text-warning fs-4"></i>
                        <h2 class="h5 fw-bold mb-0 text-white">Sisa Waktu Pembayaran Reservasi</h2>
                    </div>
                    <p class="text-white-50 small mb-0">
                        Batas akhir: <strong class="text-warning"><?= date('d F Y, H:i', strtotime($reservation['expires_at'])) ?> WIB</strong>. Jika melewati batas ini, unit akan otomatis kedaluwarsa dan tersedia kembali untuk umum.
                    </p>
                </div>
                <div class="col-md-5 text-md-end">
                    <div class="d-inline-flex gap-2 text-center" id="countdownClock">
                        <div class="bg-black bg-opacity-50 px-3 py-2 rounded-3 border border-secondary">
                            <span class="h4 fw-bold text-warning d-block mb-0" id="hoursVal">--</span>
                            <small class="text-white-50" style="font-size: 0.65rem;">JAM</small>
                        </div>
                        <div class="bg-black bg-opacity-50 px-3 py-2 rounded-3 border border-secondary">
                            <span class="h4 fw-bold text-warning d-block mb-0" id="minsVal">--</span>
                            <small class="text-white-50" style="font-size: 0.65rem;">MENIT</small>
                        </div>
                        <div class="bg-black bg-opacity-50 px-3 py-2 rounded-3 border border-secondary">
                            <span class="h4 fw-bold text-warning d-block mb-0" id="secsVal">--</span>
                            <small class="text-white-50" style="font-size: 0.65rem;">DETIK</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="<?= base_url('/checkout/process/' . $reservation['id']) ?>" method="POST" id="checkoutForm">
        <?= csrf_field() ?>

        <div class="row g-4">
            <!-- Left Column: Order Review & Payment Selection -->
            <div class="col-lg-8">
                <!-- Unit & Reservation Details -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h3 class="h5 fw-bold text-dark mb-0"><i class="bi bi-house-door me-2 text-teal"></i>Rincian Unit Sewa</h3>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 mb-3">
                            <img src="<?= base_url(esc($reservation['unit_photo'] ?? ($reservation['property_photo'] ?? 'assets/images/sample-kos-1.jpg'))) ?>" 
                                 alt="<?= esc($reservation['unit_name']) ?>" 
                                 class="rounded-3 object-fit-cover shadow-sm" 
                                 style="width: 80px; height: 80px;">
                            <div>
                                <h4 class="h6 fw-bold text-dark mb-1"><?= esc($reservation['listing_title']) ?></h4>
                                <div class="text-secondary small mb-1">
                                    <strong>Kamar: <?= esc($reservation['unit_name']) ?></strong> • <?= esc($reservation['property_name']) ?>
                                </div>
                                <div class="text-muted small">
                                    <i class="bi bi-geo-alt me-1 text-danger"></i><?= esc($reservation['property_address'] ?? 'Yogyakarta') ?>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 small">
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Penyewa:</span>
                                <strong class="text-dark"><?= esc($reservation['tenant_name']) ?> (<?= esc($reservation['tenant_gender'] === 'L' ? 'Putra' : 'Putri') ?>)</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Pemilik Hunian:</span>
                                <strong class="text-dark"><?= esc($reservation['provider_name']) ?></strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Tanggal Mulai Sewa:</span>
                                <strong class="text-dark"><?= date('d F Y', strtotime($reservation['start_date'])) ?></strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Durasi Kontrak & Termin:</span>
                                <strong class="text-dark"><?= esc($reservation['duration_months']) ?> Bulan (<?= ucfirst(str_replace('_', ' ', $reservation['payment_schedule'])) ?>)</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method Selection -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h3 class="h5 fw-bold text-dark mb-0"><i class="bi bi-credit-card-2-front me-2 text-teal"></i>Pilih Metode Pembayaran</h3>
                            <span class="badge bg-light text-muted border small">Verifikasi Otomatis</span>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-3">
                            <!-- Virtual Account BSI (Syariah) -->
                            <div class="col-md-6">
                                <label class="payment-method-card p-3 border rounded-3 d-block cursor-pointer h-100">
                                    <input type="radio" name="payment_method" value="bsi_va" class="form-check-input me-2" checked>
                                    <strong class="text-dark d-block">BSI Virtual Account</strong>
                                    <span class="small text-success fw-semibold"><i class="bi bi-patch-check-fill me-1"></i>Syariah Bebas Riba</span>
                                    <small class="text-muted d-block mt-1">Konfirmasi instan 24/7 tanpa perlu kirim bukti transfer</small>
                                </label>
                            </div>

                            <!-- Virtual Account BCA / Mandiri -->
                            <div class="col-md-6">
                                <label class="payment-method-card p-3 border rounded-3 d-block cursor-pointer h-100">
                                    <input type="radio" name="payment_method" value="bank_transfer_va" class="form-check-input me-2">
                                    <strong class="text-dark d-block">BCA / Mandiri / BRI / BNI VA</strong>
                                    <span class="small text-muted">Virtual Account Multi-Bank</span>
                                    <small class="text-muted d-block mt-1">Dapat dibayar dari m-banking, ATM, atau internet banking</small>
                                </label>
                            </div>

                            <!-- QRIS Instant -->
                            <div class="col-md-6">
                                <label class="payment-method-card p-3 border rounded-3 d-block cursor-pointer h-100">
                                    <input type="radio" name="payment_method" value="qris" class="form-check-input me-2">
                                    <strong class="text-dark d-block">QRIS (Semua Pembayaran)</strong>
                                    <span class="small text-muted">BCA, Gopay, OVO, Dana, ShopeePay</span>
                                    <small class="text-muted d-block mt-1">Scan barcode langsung dari aplikasi perbankan atau e-wallet</small>
                                </label>
                            </div>

                            <!-- E-Wallet -->
                            <div class="col-md-6">
                                <label class="payment-method-card p-3 border rounded-3 d-block cursor-pointer h-100">
                                    <input type="radio" name="payment_method" value="ewallet" class="form-check-input me-2">
                                    <strong class="text-dark d-block">E-Wallet Direct</strong>
                                    <span class="small text-muted">GoPay / ShopeePay / DANA</span>
                                    <small class="text-muted d-block mt-1">Koneksi langsung ke aplikasi dompet digital Anda</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Transparent Financial Calculation Breakdown -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 90px; z-index: 10;">
                    <div class="card-header bg-teal text-white py-3">
                        <h3 class="h6 fw-bold mb-0"><i class="bi bi-shield-check me-2"></i>Rincian Biaya Transparan</h3>
                    </div>
                    <div class="card-body p-4">
                        <!-- Rental Amount Component -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="fw-semibold text-dark d-block small">Rental Amount</span>
                                <small class="text-muted" style="font-size: 0.72rem;">(Sewa Termin 1 untuk Pemilik)</small>
                            </div>
                            <strong class="text-dark">Rp <?= number_format($rentalAmount, 0, ',', '.') ?></strong>
                        </div>

                        <!-- Gateway Fee Component -->
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <span class="fw-semibold text-dark d-block small">Gateway Fee</span>
                                <small class="text-muted" style="font-size: 0.72rem;">(Biaya Payment Gateway Pihak ke-3)</small>
                            </div>
                            <span class="text-muted small">Rp <?= number_format($gatewayFee, 0, ',', '.') ?></span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted small">Komisi Calo / Agen:</span>
                            <span class="text-success fw-bold small">Rp 0 (Bebas Calo)</span>
                        </div>

                        <hr class="my-3">

                        <!-- Total Calculation -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="h6 fw-bold text-dark mb-0">Total Pembayaran:</span>
                            <span class="h4 fw-bold text-teal mb-0">Rp <?= number_format($totalAmount, 0, ',', '.') ?></span>
                        </div>

                        <!-- Educational Alert as required by PRD FR-PAY-002, FR-PAY-003, FR-PAY-004 -->
                        <div class="p-3 bg-light rounded-3 text-secondary mb-4 small" style="font-size: 0.78rem; line-height: 1.5; border-left: 3px solid #0f766e;">
                            <i class="bi bi-info-circle-fill text-teal me-1"></i>
                            <strong>Pemberitahuan Transparansi:</strong> Gateway Fee adalah biaya pemrosesan transaksi dari penyedia payment gateway pihak ketiga dan bukan merupakan pendapatan harga sewa platform.
                        </div>

                        <button type="submit" class="btn btn-kosaja-primary w-100 py-3 fw-bold shadow-sm mb-3">
                            <i class="bi bi-shield-lock-fill me-2"></i>Konfirmasi Pembayaran
                        </button>

                        <div class="text-center">
                            <a href="<?= base_url('/applications/' . $reservation['application_id']) ?>" class="small text-muted text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i>Kembali ke Detail Pengajuan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    // Countdown Timer Logic
    const remainingSeconds = <?= (int) $remainingSeconds ?>;
    let secondsLeft = remainingSeconds;

    function updateCountdown() {
        if (secondsLeft <= 0) {
            document.getElementById('hoursVal').textContent = '00';
            document.getElementById('minsVal').textContent = '00';
            document.getElementById('secsVal').textContent = '00';
            return;
        }

        const hours = Math.floor(secondsLeft / 3600);
        const mins  = Math.floor((secondsLeft % 3600) / 60);
        const secs  = secondsLeft % 60;

        document.getElementById('hoursVal').textContent = String(hours).padStart(2, '0');
        document.getElementById('minsVal').textContent  = String(mins).padStart(2, '0');
        document.getElementById('secsVal').textContent  = String(secs).padStart(2, '0');

        secondsLeft--;
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();
</script>

<style>
    .payment-method-card {
        transition: all 0.2s ease-in-out;
        border: 2px solid #e5e7eb !important;
    }
    .payment-method-card:hover {
        border-color: #0f766e !important;
        background-color: #f0fdfa;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>
<?= $this->endSection() ?>
