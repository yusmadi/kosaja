<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-store text-teal mr-2"></i> Dasbor Mitra Pemilik (Penyedia)
            </h1>
            <p class="text-muted small mb-0">Selamat datang, <strong><?= esc(session()->get('name') ?? 'Haji Ahmad Santoso') ?></strong>. Kelola properti, unit kamar, dan listing iklan Anda.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/provider/properties/create') ?>" class="btn btn-teal text-white btn-sm shadow-sm font-weight-bold mr-1">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Properti Baru
            </a>
            <a href="<?= base_url('/provider/listings/create') ?>" class="btn btn-outline-teal btn-sm font-weight-bold">
                <i class="fas fa-bullhorn mr-1"></i> Pasang Iklan
            </a>
        </div>
    </div>
</div>

<!-- KYC Verification Alert Banner -->
<?php $kycStatus = $profile['verification_status'] ?? 'pending'; ?>
<?php if ($kycStatus === 'verified'): ?>
    <div class="alert alert-success border-0 shadow-sm d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-shield-check fa-2x mr-3 text-success"></i>
            <div>
                <strong class="d-block">Akun Mitra Terverifikasi (KYC Verified)</strong>
                <span class="text-sm">Identitas KTP dan nomor rekening settlement Anda telah disetujui.</span>
            </div>
        </div>
        <a href="<?= base_url('/provider/verification') ?>" class="btn btn-xs btn-outline-success font-weight-bold">
            Detail Identitas
        </a>
    </div>
<?php elseif ($kycStatus === 'rejected'): ?>
    <div class="alert alert-danger border-0 shadow-sm d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <i class="fas fa-exclamation-triangle fa-2x mr-3 text-danger"></i>
            <div>
                <strong class="d-block">Verifikasi Identitas Memerlukan Perbaikan</strong>
                <span class="text-sm">Alasan: "<?= esc($profile['rejection_reason'] ?? '-') ?>". Mohon perbaiki segera.</span>
            </div>
        </div>
        <a href="<?= base_url('/provider/verification') ?>" class="btn btn-xs btn-danger font-weight-bold">
            Perbaiki Sekarang
        </a>
    </div>
<?php else: ?>
    <div class="alert alert-warning border-0 shadow-sm d-flex justify-content-between align-items-center mb-4" style="background-color: #fef3c7; color: #92400e;">
        <div class="d-flex align-items-center">
            <i class="fas fa-clock fa-2x mr-3 text-warning"></i>
            <div>
                <strong class="d-block">Status Verifikasi Identitas: Menunggu Peninjauan (Pending)</strong>
                <span class="text-sm">Anda tetap dapat menginput properti & kamar sekarang selagi menunggu approval dokumen.</span>
            </div>
        </div>
        <a href="<?= base_url('/provider/verification') ?>" class="btn btn-xs btn-warning text-dark font-weight-bold">
            Kelola Dokumen KYC
        </a>
    </div>
<?php endif; ?>

<!-- Promotion / Free Period Notice Banner -->
<div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4" style="background-color: #ecfeff; color: #0891b2;">
    <i class="fas fa-gift fa-2x mr-3"></i>
    <div>
        <h6 class="font-weight-bold mb-1">Promo Bebas Biaya Listing (30 Hari / Hingga Transaksi Sewa Pertama) Aktif!</h6>
        <p class="mb-0 text-sm">
            Properti Anda bebas potongan biaya listing platform hingga tanggal <strong><?= date('d M Y', strtotime($profile['free_period_end'] ?? '+30 days')) ?></strong> atau hingga transaksi sewa pertama Anda berhasil. Nikmati 100% pendapatan sewa langsung ke rekening Anda.
        </p>
    </div>
</div>

<!-- Info boxes -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-teal text-white elevation-1"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Properti Saya</span>
                <span class="info-box-number"><?= esc($stats['my_properties'] ?? 0) ?> <small>Lokasi</small></span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-info elevation-1 text-white"><i class="fas fa-bed"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Kamar Fisik</span>
                <span class="info-box-number"><?= esc($stats['total_units'] ?? 0) ?> <small>Kamar</small></span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-success elevation-1 text-white"><i class="fas fa-door-open"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Kamar Tersedia</span>
                <span class="info-box-number"><?= esc($stats['available_units'] ?? 0) ?> <small>Siap Sewa</small></span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-warning elevation-1 text-dark"><i class="fas fa-bullhorn"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Listing Iklan</span>
                <span class="info-box-number"><?= esc($stats['total_listings'] ?? 0) ?> <small>Iklan</small></span>
            </div>
        </div>
    </div>
</div>

<!-- Section: Okupansi & Kapasitas Listing Properti (Sprint 7) -->
<div class="row mb-4">
    <!-- Tingkat Okupansi -->
    <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">
        <div class="card h-100 shadow-sm border-0" style="border-top: 4px solid #0d9488 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Tingkat Okupansi</span>
                    <span class="badge badge-pill <?= ($stats['occupancy_rate'] >= 70) ? 'badge-success' : (($stats['occupancy_rate'] >= 30) ? 'badge-info' : 'badge-warning') ?>">
                        <?= $stats['occupancy_rate'] >= 70 ? 'Optimal' : ($stats['occupancy_rate'] >= 30 ? 'Sedang' : 'Tersedia Banyak') ?>
                    </span>
                </div>
                <div class="d-flex align-items-baseline mb-3">
                    <h2 class="display-5 font-weight-bold mb-0 text-teal"><?= $stats['occupancy_rate'] ?>%</h2>
                    <span class="text-muted ml-2 small">terisi dari <?= $stats['total_units'] ?> kamar</span>
                </div>
                <div class="progress mb-3" style="height: 10px; border-radius: 6px;">
                    <div class="progress-bar bg-teal" role="progressbar" style="width: <?= $stats['occupancy_rate'] ?>%;" aria-valuenow="<?= $stats['occupancy_rate'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between text-xs text-muted pt-1 border-top">
                    <span><i class="fas fa-circle text-teal mr-1"></i> Terisi: <strong><?= $stats['occupied_units'] ?></strong></span>
                    <span><i class="fas fa-circle text-warning mr-1"></i> Booking: <strong><?= $stats['reserved_units'] ?></strong></span>
                    <span><i class="fas fa-circle text-success mr-1"></i> Kosong: <strong><?= $stats['available_units'] ?></strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kapasitas Listing Platform -->
    <div class="col-lg-4 col-md-6 mb-3 mb-lg-0">
        <div class="card h-100 shadow-sm border-0" style="border-top: 4px solid #3b82f6 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Kapasitas Listing (Free Tier)</span>
                    <?php if ($stats['remaining_capacity'] <= 0): ?>
                        <span class="badge badge-pill badge-warning text-dark font-weight-bold"><i class="fas fa-lock mr-1"></i> Kuota Penuh</span>
                    <?php else: ?>
                        <span class="badge badge-pill badge-primary">Free Tier (3 Listing)</span>
                    <?php endif; ?>
                </div>
                <div class="d-flex align-items-baseline mb-3">
                    <h2 class="display-5 font-weight-bold mb-0 text-primary"><?= $stats['total_listings'] ?> <small class="text-muted font-weight-normal h5">/ <?= $stats['max_free_listings'] ?></small></h2>
                    <span class="text-muted ml-2 small">slot iklan terpakai</span>
                </div>
                <div class="progress mb-3" style="height: 10px; border-radius: 6px;">
                    <div class="progress-bar <?= $stats['remaining_capacity'] <= 0 ? 'bg-warning' : 'bg-primary' ?>" role="progressbar" style="width: <?= $stats['listing_capacity_percent'] ?>%;" aria-valuenow="<?= $stats['listing_capacity_percent'] ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <div class="d-flex justify-content-between text-xs text-muted pt-1 border-top">
                    <span><i class="fas fa-check-circle text-success mr-1"></i> Published: <strong><?= $stats['published_listings'] ?></strong></span>
                    <span><i class="fas fa-box-archive text-primary mr-1"></i> Sisa Slot: <strong><?= $stats['remaining_capacity'] ?> Iklan</strong></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pendapatan & Kontrak Aktif -->
    <div class="col-lg-4 col-md-12">
        <div class="card h-100 shadow-sm border-0" style="border-top: 4px solid #10b981 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Omzet Sewa & Kontrak</span>
                    <span class="badge badge-pill badge-success"><?= $stats['active_contracts_count'] ?> Kontrak Aktif</span>
                </div>
                <div class="d-flex align-items-baseline mb-3">
                    <h3 class="font-weight-bold mb-0 text-success">Rp <?= number_format($stats['monthly_revenue'], 0, ',', '.') ?></h3>
                    <span class="text-muted ml-1 small">/ bln</span>
                </div>
                <p class="text-muted small mb-3">
                    Total realisasi penerimaan sewa terbayar: <strong class="text-dark">Rp <?= number_format($stats['invoice_stats']['paid_amount'], 0, ',', '.') ?></strong>
                </p>
                <div class="d-flex justify-content-between text-xs text-muted pt-1 border-top">
                    <span><i class="fas fa-file-invoice-dollar text-success mr-1"></i> Terbayar: <strong><?= $stats['invoice_stats']['paid'] ?></strong></span>
                    <span><i class="fas fa-clock text-warning mr-1"></i> Menunggu: <strong><?= $stats['invoice_stats']['pending'] ?></strong></span>
                    <span><i class="fas fa-circle-exclamation text-danger mr-1"></i> Overdue: <strong><?= $stats['invoice_stats']['overdue'] ?></strong></span>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Main Row: Unit list & Applications -->
<div class="row">
    <!-- Daftar Kamar / Unit -->
    <div class="col-lg-7" id="properties">
        <div class="card card-outline card-teal shadow-sm mb-4">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-door-closed text-teal mr-2"></i> Inventaris Unit Kamar
                </h3>
                <a href="<?= base_url('/provider/units') ?>" class="btn btn-xs btn-outline-teal font-weight-bold">
                    Lihat Semua Kamar
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover m-0 text-sm align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Unit</th>
                                <th>Properti</th>
                                <th>Ukuran</th>
                                <th>Status Kamar</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($units)): ?>
                                <?php foreach ($units as $u): ?>
                                    <tr>
                                        <td class="font-weight-bold text-dark"><?= esc($u['name']) ?></td>
                                        <td>
                                            <?= esc($u['property_name']) ?>
                                            <br>
                                            <small class="badge badge-<?= $u['target_gender'] === 'putri' ? 'danger' : ($u['target_gender'] === 'putra' ? 'primary' : 'purple') ?>">
                                                <?= esc($u['target_gender']) ?>
                                            </small>
                                        </td>
                                        <td><?= esc($u['size'] ?? '-') ?></td>
                                        <td>
                                            <?php if ($u['status'] === 'available'): ?>
                                                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Tersedia</span>
                                            <?php elseif ($u['status'] === 'occupied'): ?>
                                                <span class="badge badge-secondary"><i class="fas fa-user-lock mr-1"></i> Terisi</span>
                                            <?php elseif ($u['status'] === 'reserved'): ?>
                                                <span class="badge badge-info"><i class="fas fa-bookmark mr-1"></i> Dipesan</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning"><?= esc($u['status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?= base_url('/provider/units/edit/' . $u['id']) ?>" class="btn btn-xs btn-outline-teal">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        Belum ada unit terdaftar. <a href="<?= base_url('/provider/properties/create') ?>">Tambah properti & unit pertama Anda.</a>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Info Rekening & Settlement -->
    <div class="col-lg-5" id="payout">
        <div class="card shadow-sm mb-4">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-credit-card text-teal mr-2"></i> Rekening Penerimaan Settlement
                </h3>
            </div>
            <div class="card-body text-sm">
                <div class="p-3 bg-light rounded border mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Bank:</span>
                        <strong><?= esc($profile['bank_name'] ?? 'Belum diisi') ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Nomor Rekening:</span>
                        <code><?= esc($profile['bank_account_number'] ?? 'Belum diisi') ?></code>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Atas Nama:</span>
                        <strong><?= esc(session()->get('name') ?? 'Haji Ahmad Santoso') ?></strong>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <?php if ($kycStatus === 'verified'): ?>
                        <span class="text-success"><i class="fas fa-check-circle mr-1"></i> Rekening Terverifikasi</span>
                    <?php else: ?>
                        <span class="text-warning"><i class="fas fa-clock mr-1"></i> Belum Terverifikasi</span>
                    <?php endif; ?>
                    <a href="<?= base_url('/provider/verification') ?>" class="btn btn-xs btn-outline-secondary">
                        Kelola Rekening
                    </a>
                </div>
            </div>
        </div>

        <div class="card card-outline card-teal shadow-sm" id="applications">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-inbox text-teal mr-2"></i> Pengajuan Masuk Penyewa
                </h3>
                <a href="<?= base_url('/provider/applications') ?>" class="btn btn-xs btn-outline-teal font-weight-bold">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0 text-sm">
                <?php if (!empty($recentApplications)): ?>
                    <ul class="products-list product-list-in-card pl-2 pr-2">
                        <?php foreach ($recentApplications as $app): ?>
                            <li class="item py-2 border-bottom">
                                <div class="product-info ml-2">
                                    <a href="<?= base_url('/provider/applications/' . $app['id']) ?>" class="product-title font-weight-bold text-dark">
                                        <?= esc($app['tenant_name']) ?>
                                        <span class="badge float-right <?= $app['status'] === 'pending' ? 'badge-warning' : ($app['status'] === 'approved' ? 'badge-success' : 'badge-secondary') ?>">
                                            <?= ucfirst($app['status']) ?>
                                        </span>
                                    </a>
                                    <span class="product-description text-muted small">
                                        Kamar <?= esc($app['unit_name']) ?> • Mulai <?= date('d M Y', strtotime($app['start_date'])) ?>
                                    </span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-envelope-open-text fa-2x mb-2 opacity-50"></i>
                        <p class="mb-0 small">Belum ada pengajuan sewa baru yang masuk.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
