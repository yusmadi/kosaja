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

<?php
$rentalConfig = [
    'monthly' => [
        'key'        => 'monthly',
        'label'      => 'Sewa Bulanan',
        'price'      => !empty($listing['price_monthly']) ? (float)$listing['price_monthly'] : 0,
        'unit'       => '/ bulan',
        'durations'  => [
            ['value' => 1, 'label' => '1 Bulan'],
            ['value' => 2, 'label' => '2 Bulan'],
            ['value' => 3, 'label' => '3 Bulan'],
            ['value' => 4, 'label' => '4 Bulan'],
            ['value' => 5, 'label' => '5 Bulan'],
        ],
        'schedules'  => [
            [
                'value' => 'monthly',
                'id'    => 'sched_monthly',
                'label' => 'Bayar Bulanan (Per Bulan)',
                'desc'  => 'Tagihan sewa diterbitkan rutin setiap bulan.',
            ],
            [
                'value' => 'full_upfront',
                'id'    => 'sched_full',
                'label' => 'Lunas di Awal (Full Upfront)',
                'desc'  => 'Satu kali pembayaran untuk seluruh durasi sewa yang dipilih.',
            ],
        ],
    ],
    'six_monthly' => [
        'key'        => 'six_monthly',
        'label'      => 'Sewa Enam Bulan',
        'price'      => !empty($listing['price_six_monthly']) ? (float)$listing['price_six_monthly'] : 0,
        'unit'       => '/ 6 bulan',
        'durations'  => [
            ['value' => 6, 'label' => '6 Bulan'],
        ],
        'schedules'  => [
            [
                'value' => 'full_upfront',
                'id'    => 'sched_full',
                'label' => 'Lunas di Awal (Full Upfront)',
                'desc'  => 'Satu kali pembayaran lunas di awal untuk masa sewa 6 bulan.',
            ],
        ],
    ],
    'yearly' => [
        'key'        => 'yearly',
        'label'      => 'Sewa Tahunan',
        'price'      => !empty($listing['price_yearly']) ? (float)$listing['price_yearly'] : 0,
        'unit'       => '/ tahun',
        'durations'  => [
            ['value' => 12, 'label' => '1 Tahun'],
        ],
        'schedules'  => [
            [
                'value' => 'full_upfront',
                'id'    => 'sched_full',
                'label' => 'Lunas di Awal (Full Upfront)',
                'desc'  => 'Satu kali pembayaran lunas di awal untuk masa sewa 1 tahun penuh.',
            ],
        ],
    ],
];
?>

<style>
.schedule-radio-box {
    cursor: pointer;
    background-color: #ffffff;
    transition: all 0.2s ease-in-out;
}
.schedule-radio-box:hover {
    border-color: #0d9488 !important;
    background-color: #f0fdfa;
}
.schedule-radio-box.active {
    border-color: #0d9488 !important;
    background-color: #f0fdfa;
    box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.25);
}

/* Custom Rent Type Package Selector */
.rent-type-card {
    background-color: #ffffff;
    border: 1.5px solid #cbd5e1;
    color: #1e293b;
    border-radius: 0.5rem;
    padding: 0.6rem 1.1rem;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
    display: inline-block;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.rent-type-card .rent-type-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: #1e293b;
    transition: color 0.2s ease-in-out;
}

.rent-type-card .rent-type-price {
    font-size: 0.8rem;
    color: #64748b;
    margin-top: 2px;
    transition: color 0.2s ease-in-out;
}

/* Hover state: Soft mint/teal tint with crisp teal border and dark teal text */
.rent-type-card:hover {
    background-color: #f0fdfa !important;
    border-color: #0d9488 !important;
    box-shadow: 0 3px 8px rgba(13, 148, 136, 0.18);
    transform: translateY(-1px);
}

.rent-type-card:hover .rent-type-title {
    color: #0f766e !important;
}

.rent-type-card:hover .rent-type-price {
    color: #0d9488 !important;
}

/* Checked/Active state: Solid primary teal with crisp white text */
.btn-check:checked + .rent-type-card {
    background-color: #0d9488 !important;
    border-color: #0d9488 !important;
    box-shadow: 0 4px 12px rgba(13, 148, 136, 0.3) !important;
    transform: translateY(-1px);
}

.btn-check:checked + .rent-type-card .rent-type-title {
    color: #ffffff !important;
}

.btn-check:checked + .rent-type-card .rent-type-price {
    color: rgba(255, 255, 255, 0.95) !important;
}
</style>

                <!-- Section 2: Jadwal & Durasi Sewa -->
                <div class="card border-0 shadow-sm rounded-3 mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-circle bg-teal text-white d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; font-size: 0.85rem; font-weight: bold;">2</div>
                            <h2 class="h5 fw-bold text-dark mb-0">Jadwal & Skema Sewa</h2>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <!-- Jenis / Tipe Listing Banner or Selector -->
                        <?php if (count($availableTypes) > 1): ?>
                            <div class="p-3 bg-light rounded-3 border mb-3">
                                <label class="form-label small fw-semibold text-dark mb-2 d-block">
                                    <i class="bi bi-tag-fill text-teal me-1"></i>Pilih Jenis / Paket Sewa: <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($availableTypes as $typeKey => $t): ?>
                                        <div class="form-check form-check-inline p-0 m-0">
                                            <input class="btn-check" type="radio" name="rent_type" id="rent_type_<?= $typeKey ?>" value="<?= $typeKey ?>" <?= ($selectedType === $typeKey) ? 'checked' : '' ?> onchange="updateRentType(this.value)">
                                            <label class="rent-type-card text-start" for="rent_type_<?= $typeKey ?>">
                                                <div class="rent-type-title"><?= esc($t['label']) ?></div>
                                                <div class="rent-type-price">Rp <?= number_format($t['price'], 0, ',', '.') ?> <?= $t['unit'] ?></div>
                                            </label>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="rent_type" id="rent_type" value="<?= esc($selectedType) ?>">
                            <div class="p-3 rounded-3 border mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2" style="background-color: #f0fdfa; border-color: #99f6e4 !important;">
                                <div>
                                    <span class="badge bg-teal text-white px-3 py-2 fs-6 mb-1">
                                        <i class="bi bi-tag-fill me-1"></i>Jenis Listing: <?= esc($availableTypes[$selectedType]['label'] ?? 'Sewa Hunian') ?>
                                    </span>
                                    <div class="text-muted small mt-1">Durasi dan skema pembayaran disesuaikan dengan jenis listing ini.</div>
                                </div>
                                <div class="text-end">
                                    <span class="text-muted small d-block">Tarif Sewa:</span>
                                    <strong class="text-teal fs-5">Rp <?= number_format($availableTypes[$selectedType]['price'] ?? 0, 0, ',', '.') ?></strong>
                                    <span class="text-muted small"><?= esc($availableTypes[$selectedType]['unit'] ?? '') ?></span>
                                </div>
                            </div>
                        <?php endif; ?>

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
                                <select class="form-select" id="duration_months" name="duration_months" onchange="onDurationChanged()" required>
                                    <!-- Populated dynamically by updateRentType -->
                                </select>
                                <div class="form-text small" id="durationHelpText">Pilihan durasi sesuai dengan jenis listing.</div>
                            </div>

                            <!-- Payment Schedule -->
                            <div class="col-12">
                                <label class="form-label small fw-semibold text-dark">Skema Jadwal Pembayaran <span class="text-danger">*</span></label>
                                <div class="row g-2" id="payment_schedule_container">
                                    <!-- Populated dynamically by updateRentType -->
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
                        <div class="mb-3 pb-2 border-bottom">
                            <span class="text-muted small text-uppercase fw-semibold d-block mb-1">Tarif Unit Listing</span>
                            <?php if (!empty($listing['price_monthly']) && (float)$listing['price_monthly'] > 0): ?>
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">Tarif Sewa Bulanan:</span>
                                    <span class="fw-semibold">Rp <?= number_format($listing['price_monthly'], 0, ',', '.') ?> / bln</span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($listing['price_six_monthly']) && (float)$listing['price_six_monthly'] > 0): ?>
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">Tarif Sewa 6 Bulan:</span>
                                    <span class="fw-semibold">Rp <?= number_format($listing['price_six_monthly'], 0, ',', '.') ?> / 6 bln</span>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($listing['price_yearly']) && (float)$listing['price_yearly'] > 0): ?>
                                <div class="d-flex justify-content-between mb-1 small">
                                    <span class="text-muted">Tarif Sewa Tahunan:</span>
                                    <span class="fw-semibold">Rp <?= number_format($listing['price_yearly'], 0, ',', '.') ?> / thn</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Jenis Sewa:</span>
                            <span class="fw-bold text-teal" id="summaryRentType"><?= esc($availableTypes[$selectedType]['label'] ?? 'Sewa Hunian') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Rencana Durasi:</span>
                            <span class="fw-bold text-dark" id="summaryDuration">1 Tahun</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 small">
                            <span class="text-muted">Skema Pembayaran:</span>
                            <span class="fw-bold text-dark" id="summarySchedule">Tahunan</span>
                        </div>

                        <hr class="my-3">

                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small">Biaya Sewa Termin Awal:</span>
                            <strong class="text-teal fs-5" id="summaryRentalAmount">Rp 0</strong>
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
                                Rp 0
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
    const rentalConfig  = <?= json_encode($rentalConfig) ?>;
    const gatewayFee    = <?= (float) $reservationConfig->gatewayFee ?>;
    let currentRentType = <?= json_encode($selectedType) ?>;
    let oldDuration     = <?= (int) old('duration_months', 0) ?>;
    let oldSchedule     = <?= json_encode(old('payment_schedule', '')) ?>;

    function formatRupiah(number) {
        return 'Rp ' + Math.round(number).toLocaleString('id-ID');
    }

    function updateRentType(newType) {
        if (!rentalConfig[newType]) return;
        currentRentType = newType;
        renderDurationSelect();
        renderPaymentSchedule();
        calculateRentalPrice();
    }

    function renderDurationSelect() {
        const durationSelect = document.getElementById('duration_months');
        const config = rentalConfig[currentRentType];
        if (!config) return;

        durationSelect.innerHTML = '';
        let matchedOld = false;

        config.durations.forEach(opt => {
            const option = document.createElement('option');
            option.value = opt.value;
            option.textContent = opt.label;
            if (oldDuration && oldDuration == opt.value) {
                option.selected = true;
                matchedOld = true;
            }
            durationSelect.appendChild(option);
        });

        if (!matchedOld && config.durations.length > 0) {
            durationSelect.value = config.durations[0].value;
        }

        const helpText = document.getElementById('durationHelpText');
        if (helpText) {
            if (currentRentType === 'monthly') {
                helpText.textContent = 'Tersedia pilihan sewa 1 hingga 5 bulan.';
            } else if (currentRentType === 'six_monthly') {
                helpText.textContent = 'Khusus paket sewa 6 bulan (satu semester).';
            } else if (currentRentType === 'yearly') {
                helpText.textContent = 'Khusus paket sewa 1 tahun.';
            }
        }
    }

    function renderPaymentSchedule() {
        const container = document.getElementById('payment_schedule_container');
        const config = rentalConfig[currentRentType];
        if (!config) return;

        container.innerHTML = '';
        const colClass = config.schedules.length === 1 ? 'col-12' : 'col-md-6';
        let matchedOld = false;

        config.schedules.forEach((item, index) => {
            const col = document.createElement('div');
            col.className = colClass;

            const isChecked = (oldSchedule && oldSchedule === item.value) || (!oldSchedule && index === 0);
            if (isChecked) matchedOld = true;

            col.innerHTML = `
                <div class="form-check p-3 border rounded-3 h-100 schedule-radio-box ${isChecked ? 'active' : ''}" onclick="selectScheduleRadio('${item.id}')">
                    <input class="form-check-input ms-0 me-2" type="radio" name="payment_schedule" id="${item.id}" value="${item.value}" onchange="onScheduleChanged()" ${isChecked ? 'checked' : ''} required>
                    <label class="form-check-label fw-semibold text-dark" for="${item.id}">
                        ${item.label}
                        <div class="text-muted small fw-normal">${item.desc}</div>
                    </label>
                </div>
            `;
            container.appendChild(col);
        });

        if (!matchedOld && config.schedules.length > 0) {
            const firstRadio = document.getElementById(config.schedules[0].id);
            if (firstRadio) {
                firstRadio.checked = true;
                firstRadio.closest('.schedule-radio-box')?.classList.add('active');
            }
        }
    }

    function selectScheduleRadio(id) {
        const radio = document.getElementById(id);
        if (radio) {
            radio.checked = true;
            onScheduleChanged();
        }
    }

    function onScheduleChanged() {
        document.querySelectorAll('.schedule-radio-box').forEach(box => {
            const input = box.querySelector('input[type="radio"]');
            if (input && input.checked) {
                box.classList.add('active');
            } else {
                box.classList.remove('active');
            }
        });
        calculateRentalPrice();
    }

    function onDurationChanged() {
        calculateRentalPrice();
    }

    function calculateRentalPrice() {
        const durationSelect = document.getElementById('duration_months');
        const duration = parseInt(durationSelect.value) || 1;

        const scheduleRadio = document.querySelector('input[name="payment_schedule"]:checked');
        const schedule = scheduleRadio ? scheduleRadio.value : '';

        const config = rentalConfig[currentRentType];
        if (!config) return;

        let rentalAmount = 0;
        let scheduleLabel = '';
        let durationText = duration + ' Bulan';

        if (currentRentType === 'yearly') {
            durationText = '1 Tahun';
            rentalAmount = config.price;
            scheduleLabel = 'Lunas di Awal (Full Upfront)';
        } else if (currentRentType === 'six_monthly') {
            durationText = '6 Bulan';
            rentalAmount = config.price;
            scheduleLabel = 'Lunas di Awal (Full Upfront)';
        } else { // monthly
            durationText = duration + ' Bulan';
            if (schedule === 'monthly') {
                rentalAmount = config.price;
                scheduleLabel = 'Bayar Bulanan (Per Bulan)';
            } else { // full_upfront
                rentalAmount = config.price * duration;
                scheduleLabel = 'Lunas di Awal (' + duration + ' Bulan)';
            }
        }

        const totalAmount = rentalAmount + gatewayFee;

        // Update summary display
        const rentTypeElem = document.getElementById('summaryRentType');
        if (rentTypeElem) {
            rentTypeElem.textContent = config.label;
        }
        document.getElementById('summaryDuration').textContent = durationText;
        document.getElementById('summarySchedule').textContent = scheduleLabel;
        document.getElementById('summaryRentalAmount').textContent = formatRupiah(rentalAmount);
        document.getElementById('summaryTotalAmount').textContent = formatRupiah(totalAmount);
    }

    document.addEventListener('DOMContentLoaded', function() {
        renderDurationSelect();
        renderPaymentSchedule();
        calculateRentalPrice();
    });
</script>
<?= $this->endSection() ?>
