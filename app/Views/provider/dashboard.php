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
        <h6 class="font-weight-bold mb-1">Promo Bebas Biaya Listing 6 Bulan Aktif!</h6>
        <p class="mb-0 text-sm">
            Properti Anda bebas potongan biaya listing platform hingga tanggal <strong><?= date('d M Y', strtotime($profile['free_period_end'] ?? '+6 months')) ?></strong>. Nikmati 100% pendapatan sewa langsung ke rekening Anda.
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

        <div class="card shadow-sm" id="applications">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-inbox text-teal mr-2"></i> Pengajuan Masuk Penyewa
                </h3>
            </div>
            <div class="card-body text-sm text-center py-4">
                <i class="fas fa-envelope-open-text fa-3x text-muted mb-3"></i>
                <h6 class="font-weight-bold">Belum Ada Pengajuan Baru</h6>
                <p class="text-muted small mb-0">Pengajuan sewa dari calon penyewa yang masuk akan muncul di sini untuk persetujuan Anda.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
