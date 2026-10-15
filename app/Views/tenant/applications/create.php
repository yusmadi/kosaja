<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="bg-light py-4 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-teal text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('/search') ?>" class="text-teal text-decoration-none">Cari Hunian</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('/listing/' . $listing['id']) ?>" class="text-teal text-decoration-none"><?= esc($listing['title']) ?></a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Sewa</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Pengajuan Sewa Hunian</h1>
                <p class="text-muted small mb-0">Lengkapi data rencana sewa Anda untuk ditinjau oleh pemilik/pengelola properti</p>
            </div>
            <div>
                <span class="badge <?= $listing['target_gender'] === 'putra' ? 'bg-primary' : ($listing['target_gender'] === 'putri' ? 'bg-danger' : 'badge-purple text-white') ?> px-3 py-2 fs-6 shadow-sm" style="<?= $listing['target_gender'] === 'campur_keluarga' ? 'background-color: #7c3aed;' : '' ?>">
                    <i class="bi <?= $listing['target_gender'] === 'putra' ? 'bi-gender-male' : ($listing['target_gender'] === 'putri' ? 'bi-gender-female' : 'bi-people-fill') ?> me-1"></i>
                    Khusus <?= esc(ucfirst($listing['target_gender'] === 'campur_keluarga' ? 'Pasutri / Keluarga' : $listing['target_gender'])) ?>
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-2"></i>Terdapat kesalahan pengisian:</div>
            <ul class="mb-0 small ps-3">
                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                    <li><?= esc($err) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('/apply/' . $listing['id']) ?>" method="POST" enctype="multipart/form-data" id="applicationForm">
        <?= csrf_field() ?>

        <div class="row g-4">
            <!-- Left Column: Form Fields -->
            <div class="col-lg-8">
                <!-- Section 1: Data Hunian & Unit -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-teal text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.85rem; font-weight: bold;">1</div>
                            <h2 class="h5 fw-bold text-dark mb-0">Informasi Unit Hunian</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3">
                            <img src="<?= base_url(esc($listing['unit_photos'][0]['photo_url'] ?? ($listing['property_photos'][0]['photo_url'] ?? 'assets/images/sample-kos-1.jpg'))) ?>" 
                                 alt="<?= esc($listing['unit_name']) ?>" 
                                 class="rounded-3 object-fit-cover shadow-sm" 
                                 style="width: 80px; height: 80px;">
                            <div class="flex-grow-1">
                                <h3 class="h6 fw-bold text-dark mb-1"><?= esc($listing['title']) ?></h3>
                                <div class="text-muted small mb-1">
                                    <i class="bi bi-door-closed me-1 text-teal"></i><strong>Kamar: <?= esc($listing['unit_name']) ?></strong>
                                    <?php if (!empty($listing['unit_size'])): ?>
                                        <span class="mx-1">•</span> Dimensi: <?= esc($listing['unit_size']) ?>
                                    <?php endif; ?>
                                    <?php if (!empty($listing['unit_floor'])): ?>
                                        <span class="mx-1">•</span> Lantai: <?= esc($listing['unit_floor']) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="text-secondary small">
                                    <i class="bi bi-geo-alt me-1 text-danger"></i><?= esc($listing['property_name']) ?>, <?= esc($listing['region_name'] ?? 'Sleman') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Jadwal & Durasi Sewa -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-teal text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.85rem; font-weight: bold;">2</div>
                            <h2 class="h5 fw-bold text-dark mb-0">Jadwal & Skema Sewa</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row g-3">
                            <!-- Start Date -->
                            <div class="col-md-6">
                                <label for="start_date" class="form-label small fw-semibold text-dark">Tanggal Mulai Menempati <span class="text-danger">*</span></label>
                                <input type="date" 
                                       class="form-control" 
                                       id="start_date" 
                                       name="start_date" 
                                       min="<?= date('Y-m-d') ?>" 
                                       value="<?= old('start_date', date('Y-m-d', strtotime('+1 day'))) ?>" 
                                       required>
                                <div class="form-text small">Pilih tanggal awal Anda berencana masuk / check-in.</div>
                            </div>

                            <!-- Duration Months -->
                            <div class="col-md-6">
                                <label for="duration_months" class="form-label small fw-semibold text-dark">Lama Durasi Sewa <span class="text-danger">*</span></label>
                                <select class="form-select" id="duration_months" name="duration_months" onchange="calculateRentalPrice()" required>
                                    <option value="1" <?= old('duration_months') == '1' ? 'selected' : '' ?>>1 Bulan</option>
                                    <option value="3" <?= old('duration_months') == '3' ? 'selected' : '' ?>>3 Bulan</option>
                                    <option value="6" <?= old('duration_months') == '6' ? 'selected' : '' ?>>6 Bulan</option>
                                    <option value="12" <?= old('duration_months', '12') == '12' ? 'selected' : '' ?>>1 Tahun (12 Bulan)</option>
                                    <option value="24" <?= old('duration_months') == '24' ? 'selected' : '' ?>>2 Tahun (24 Bulan)</option>
                                </select>
                            </div>

                            <!-- Payment Schedule -->
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark">Skema Jadwal Pembayaran <span class="text-danger">*</span></label>
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-check p-3 border rounded-3 h-100 schedule-radio-box">
                                            <input class="form-check-input ms-0 me-2" type="radio" name="payment_schedule" id="sched_monthly" value="monthly" onchange="calculateRentalPrice()" <?= old('payment_schedule', 'monthly') === 'monthly' ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-semibold text-dark" for="sched_monthly">
                                                Bayar Bulanan (Per Bulan)
                                                <div class="text-muted small fw-normal">Tagihan diterbitkan rutin setiap tanggal jatuh tempo sewa.</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check p-3 border rounded-3 h-100 schedule-radio-box">
                                            <input class="form-check-input ms-0 me-2" type="radio" name="payment_schedule" id="sched_3monthly" value="3_monthly" onchange="calculateRentalPrice()" <?= old('payment_schedule') === '3_monthly' ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-semibold text-dark" for="sched_3monthly">
                                                Termin Per 3 Bulan
                                                <div class="text-muted small fw-normal">Pembayaran sewa per kuartal (3 bulan sekali).</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check p-3 border rounded-3 h-100 schedule-radio-box">
                                            <input class="form-check-input ms-0 me-2" type="radio" name="payment_schedule" id="sched_yearly" value="yearly" onchange="calculateRentalPrice()" <?= old('payment_schedule') === 'yearly' ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-semibold text-dark" for="sched_yearly">
                                                Tahunan (Per 1 Tahun)
                                                <div class="text-muted small fw-normal">Tarif tahunan hemat untuk masa kontrak panjang.</div>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check p-3 border rounded-3 h-100 schedule-radio-box">
                                            <input class="form-check-input ms-0 me-2" type="radio" name="payment_schedule" id="sched_full" value="full_upfront" onchange="calculateRentalPrice()" <?= old('payment_schedule') === 'full_upfront' ? 'checked' : '' ?>>
                                            <label class="form-check-label fw-semibold text-dark" for="sched_full">
                                                Lunas di Awal (Full Upfront)
                                                <div class="text-muted small fw-normal">Satu kali pembayaran untuk seluruh durasi sewa yang dipilih.</div>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Data Pemohon & Verifikasi Syariat -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-teal text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.85rem; font-weight: bold;">3</div>
                            <h2 class="h5 fw-bold text-dark mb-0">Identitas Pemohon & Kepatuhan Syariat</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <!-- Applicant profile preview -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Nama Lengkap Pemohon</label>
                                <input type="text" class="form-control bg-light" value="<?= esc($user['name']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Jenis Kelamin Akun</label>
                                <input type="text" class="form-control bg-light fw-bold <?= $user['gender'] === 'L' ? 'text-primary' : 'text-danger' ?>" value="<?= $user['gender'] === 'L' ? 'Laki-laki (Putra)' : 'Perempuan (Putri)' ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Email</label>
                                <input type="email" class="form-control bg-light" value="<?= esc($user['email']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-semibold text-muted mb-1">Nomor WhatsApp Aktif</label>
                                <input type="text" class="form-control bg-light" value="<?= esc($user['whatsapp'] ?? '-') ?>" readonly>
                            </div>
                        </div>

                        <!-- Special Section for Campur / Pasutri / Keluarga -->
                        <?php if ($listing['target_gender'] === 'campur_keluarga'): ?>
                            <div class="p-3 rounded-3 mb-3 border border-purple" style="background-color: #faf5ff; border-color: #d8b4fe !important;">
                                <div class="d-flex align-items-center gap-2 mb-2 text-purple" style="color: #6b21a8;">
                                    <i class="bi bi-people-fill fs-5"></i>
                                    <h4 class="h6 fw-bold mb-0">Verifikasi Syarat Pasangan Suami-Istri / Keluarga Sah</h4>
                                </div>
                                <p class="small text-muted mb-3">
                                    Properti ini menerapkan lingkungan khusus keluarga dan pasutri sah. Silakan cantumkan keterangan status dan unggah bukti Buku Nikah atau Kartu Keluarga untuk diverifikasi oleh pemilik properti sebelum pengajuan disetujui.
                                </p>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="marital_status" class="form-label small fw-semibold text-dark">Status Pasangan / Keluarga <span class="text-danger">*</span></label>
                                        <select class="form-select" id="marital_status" name="marital_status" required>
                                            <option value="menikah" <?= old('marital_status') === 'menikah' ? 'selected' : '' ?>>Pasangan Suami Istri (Pasutri)</option>
                                            <option value="keluarga_sah" <?= old('marital_status') === 'keluarga_sah' ? 'selected' : '' ?>>Keluarga (Orang Tua / Saudara Kandung Sah)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="marriage_certificate" class="form-label small fw-semibold text-dark">Foto / Dokumen Buku Nikah atau KK</label>
                                        <input type="file" class="form-control" id="marriage_certificate" name="marriage_certificate" accept="image/jpeg,image/png,image/webp">
                                        <div class="form-text small" style="font-size: 0.76rem;">Format JPG/PNG/WebP, maks 5MB. Hanya dapat dilihat oleh Pemilik properti.</div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Applicant Notes -->
                        <div class="mb-3">
                            <label for="applicant_notes" class="form-label small fw-semibold text-dark">Catatan Tambahan untuk Pemilik (Opsional)</label>
                            <textarea class="form-control" id="applicant_notes" name="applicant_notes" rows="3" placeholder="Contoh: Pekerjaan (karyawan swasta), jam pulang kerja, rencana membawa kendaraan motor 1 unit, dll."><?= old('applicant_notes') ?></textarea>
                            <div class="form-text small">Informasi ini membantu pemilik mempertimbangkan dan mempercepat persetujuan pengajuan Anda.</div>
                        </div>

                        <!-- Syariat Agreement Checkbox -->
                        <div class="form-check p-3 bg-light rounded-3 border">
                            <input class="form-check-input ms-0 me-2" type="checkbox" id="syariat_consent" name="syariat_consent" value="1" required <?= old('syariat_consent') ? 'checked' : '' ?>>
                            <label class="form-check-label small text-secondary" for="syariat_consent">
                                <strong>Saya menyatakan dan menyetujui:</strong> Data yang saya isi adalah benar. Saya bersedia mematuhi tata tertib hunian, adab lingkungan syariat, tidak membawa lawan jenis bukan mahram ke dalam kamar, serta siap diverifikasi identitas resminya saat serah terima kunci.
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Summary Card -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-3 sticky-top" style="top: 90px; z-index: 10;">
                    <div class="card-header bg-teal text-white py-3">
                        <h3 class="h6 fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Ringkasan Estimasi Biaya</h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Tarif Sewa Kamar:</span>
                            <span class="fw-semibold">Rp <?= number_format($listing['price_monthly'], 0, ',', '.') ?> / bln</span>
                        </div>
                        <?php if (!empty($listing['price_six_monthly'])): ?>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Tarif 6 Bulan:</span>
                                <span class="fw-semibold">Rp <?= number_format($listing['price_six_monthly'], 0, ',', '.') ?> / 6 bln</span>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($listing['price_yearly'])): ?>
                            <div class="d-flex justify-content-between mb-2 small">
                                <span class="text-muted">Tarif Tahunan:</span>
                                <span class="fw-semibold">Rp <?= number_format($listing['price_yearly'], 0, ',', '.') ?> / thn</span>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Rencana Durasi:</span>
                            <span class="fw-bold text-dark" id="summaryDuration">1 Bulan</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Skema Pembayaran:</span>
                            <span class="fw-bold text-dark" id="summarySchedule">Bulanan</span>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Biaya Sewa Termin Awal:</span>
                            <strong class="text-teal fs-5" id="summaryRentalAmount">Rp <?= number_format($listing['price_monthly'], 0, ',', '.') ?></strong>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span class="text-muted">Gateway Fee (Sistem):</span>
                            <span class="text-muted" id="summaryGatewayFee">Rp <?= number_format($reservationConfig->gatewayFee, 0, ',', '.') ?></span>
                        </div>

                        <div class="p-2 bg-light rounded-2 small text-muted mb-3" style="font-size: 0.76rem; line-height: 1.4;">
                            <i class="bi bi-info-circle me-1 text-teal"></i>
                            <strong>Bebas Biaya Calo 100%.</strong> Biaya sewa dibayarkan setelah pemilik menyetujui pengajuan Anda.
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-2 border-top mb-3">
                            <div>
                                <span class="small fw-bold text-dark d-block">Total Pembayaran Awal:</span>
                                <small class="text-muted" style="font-size: 0.72rem;">(Saat Checkout Disetujui)</small>
                            </div>
                            <strong class="h4 fw-bold text-dark mb-0" id="summaryTotalAmount">
                                Rp <?= number_format($listing['price_monthly'] + $reservationConfig->gatewayFee, 0, ',', '.') ?>
                            </strong>
                        </div>

                        <button type="submit" class="btn btn-kosaja-primary w-100 py-3 fw-bold shadow-sm">
                            <i class="bi bi-send-check me-2"></i>Kirim Pengajuan Sewa
                        </button>

                        <div class="text-center mt-3">
                            <a href="<?= base_url('/listing/' . $listing['id']) ?>" class="small text-muted text-decoration-none">
                                <i class="bi bi-arrow-left me-1"></i>Kembali ke Detail Listing
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    const priceMonthly     = <?= (float) $listing['price_monthly'] ?>;
    const priceSixMonthly  = <?= !empty($listing['price_six_monthly']) ? (float) $listing['price_six_monthly'] : 'null' ?>;
    const priceYearly      = <?= !empty($listing['price_yearly']) ? (float) $listing['price_yearly'] : ((float) $listing['price_monthly'] * 12) ?>;
    const gatewayFee       = <?= (float) $reservationConfig->gatewayFee ?>;

    function formatRupiah(number) {
        return 'Rp ' + Math.round(number).toLocaleString('id-ID');
    }

    function calculateRentalPrice() {
        const durationSelect = document.getElementById('duration_months');
        const duration = parseInt(durationSelect.value) || 1;

        const scheduleRadio = document.querySelector('input[name="payment_schedule"]:checked');
        const schedule = scheduleRadio ? scheduleRadio.value : 'monthly';

        let rentalAmount = priceMonthly;
        let scheduleLabel = 'Bulanan';

        if (schedule === 'monthly') {
            rentalAmount = priceMonthly;
            scheduleLabel = 'Per Bulan';
        } else if (schedule === '3_monthly') {
            rentalAmount = priceMonthly * Math.min(3, duration);
            scheduleLabel = 'Per 3 Bulan';
        } else if (schedule === '6_monthly') {
            rentalAmount = priceSixMonthly ? priceSixMonthly : (priceMonthly * Math.min(6, duration));
            scheduleLabel = 'Per 6 Bulan';
        } else if (schedule === 'yearly') {
            rentalAmount = (duration >= 12) ? priceYearly : (priceMonthly * 12);
            scheduleLabel = 'Tahunan';
        } else if (schedule === 'full_upfront') {
            if (duration >= 12) {
                const years = Math.floor(duration / 12);
                const remMonths = duration % 12;
                rentalAmount = (priceYearly * years) + (priceMonthly * remMonths);
            } else {
                rentalAmount = priceMonthly * duration;
            }
            scheduleLabel = 'Lunas di Awal (' + duration + ' Bulan)';
        }

        const totalAmount = rentalAmount + gatewayFee;

        // Update summary display
        document.getElementById('summaryDuration').textContent = duration + ' Bulan';
        document.getElementById('summarySchedule').textContent = scheduleLabel;
        document.getElementById('summaryRentalAmount').textContent = formatRupiah(rentalAmount);
        document.getElementById('summaryTotalAmount').textContent = formatRupiah(totalAmount);
    }

    document.addEventListener('DOMContentLoaded', function() {
        calculateRentalPrice();
    });
</script>
<?= $this->endSection() ?>
