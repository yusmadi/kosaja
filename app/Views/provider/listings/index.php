<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-bullhorn text-teal mr-2"></i> Manajemen Listing Iklan
            </h1>
            <p class="text-muted small mb-0">Publikasikan unit kamar Anda ke marketplace agar dapat ditemukan calon penyewa.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <span class="badge badge-light border mr-2 py-2 px-3">
                <i class="fas fa-boxes-stacked text-teal mr-1"></i> Kuota: <strong><?= $totalListings ?? 0 ?> / <?= $maxFreeListings ?? 3 ?></strong> (<?= esc($quota['plan_name'] ?? 'Paket') ?>)
            </span>
            <?php if (!empty($isExpired)): ?>
                <a href="<?= base_url('/provider/subscription') ?>" class="btn btn-danger btn-sm shadow-sm font-weight-bold">
                    <i class="fas fa-exclamation-triangle mr-1"></i> Re-Aktivasi Paket
                </a>
            <?php elseif (!empty($isQuotaFull)): ?>
                <a href="<?= base_url('/provider/subscription') ?>" class="btn btn-warning text-dark btn-sm shadow-sm font-weight-bold">
                    <i class="fas fa-bolt mr-1"></i> Upgrade Kuota
                </a>
            <?php else: ?>
                <a href="<?= base_url('/provider/listings/create') ?>" class="btn btn-teal text-white btn-sm shadow-sm font-weight-bold">
                    <i class="fas fa-plus-circle mr-1"></i> Buat Listing Iklan Baru
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Flash alerts -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i> <?= session()->getFlashdata('warning') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-times-circle mr-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (!empty($isExpired)): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-3 d-flex justify-content-between align-items-center" style="background-color: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444 !important;">
        <div class="mr-3">
            <strong class="d-block mb-1 font-weight-bold" style="color: #7f1d1d;">
                <i class="fas fa-ban mr-2" style="color: #dc2626;"></i>Masa Aktif Paket Berakhir
            </strong>
            <p class="mb-0 text-sm" style="color: #991b1b; line-height: 1.45;">
                Masa aktif paket iklan Anda telah habis. Seluruh listing unit yang belum tersewa dinonaktifkan sementara. Silakan aktifkan paket langganan atau slot listing baru untuk melanjutkan penayangan iklan.
            </p>
        </div>
        <div class="flex-shrink-0">
            <a href="<?= base_url('/provider/subscription') ?>" class="btn btn-danger btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-bolt mr-1"></i> Beli Paket Sekarang
            </a>
        </div>
    </div>
<?php elseif (!empty($isQuotaFull)): ?>
    <div class="alert alert-warning border-0 shadow-sm mb-3 d-flex justify-content-between align-items-center" style="background-color: #fef3c7; color: #92400e; border-left: 4px solid #f59e0b !important;">
        <div class="mr-3">
            <strong class="d-block mb-1 font-weight-bold" style="color: #78350f;">
                <i class="fas fa-exclamation-circle mr-2" style="color: #d97706;"></i>Kapasitas Kuota Listing Telah Penuh (<?= $totalListings ?> / <?= $maxFreeListings ?> Iklan)
            </strong>
            <p class="mb-0 text-sm" style="color: #92400e; line-height: 1.45;">
                Anda telah mencapai batas maksimal paket <strong><?= esc($quota['plan_name'] ?? 'Aktif') ?></strong> (<?= $maxFreeListings ?> iklan). Untuk menambah iklan unit kamar lainnya, tingkatkan paket Anda ke kapasitas yang lebih besar.
            </p>
        </div>
        <div class="flex-shrink-0">
            <a href="<?= base_url('/provider/subscription') ?>" class="btn btn-warning text-dark btn-sm font-weight-bold shadow-sm">
                <i class="fas fa-crown mr-1"></i> Upgrade Paket
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Filter Tabs -->
<div class="mb-3">
    <div class="btn-group shadow-sm">
        <a href="<?= base_url('/provider/listings?status=all') ?>" class="btn btn-sm <?= ($statusFilter === 'all') ? 'btn-teal text-white' : 'btn-light border' ?>">
            Semua Listing <span class="badge badge-light ml-1"><?= $counts['all'] ?></span>
        </a>
        <a href="<?= base_url('/provider/listings?status=published') ?>" class="btn btn-sm <?= ($statusFilter === 'published') ? 'btn-success' : 'btn-light border' ?>">
            <i class="fas fa-check-circle mr-1"></i> Tayang Aktif <span class="badge badge-secondary ml-1"><?= $counts['published'] ?></span>
        </a>
        <a href="<?= base_url('/provider/listings?status=pending_review') ?>" class="btn btn-sm <?= ($statusFilter === 'pending_review') ? 'btn-warning text-dark font-weight-bold' : 'btn-light border' ?>">
            <i class="fas fa-clock mr-1"></i> Menunggu Review <span class="badge badge-dark ml-1"><?= $counts['pending_review'] ?></span>
        </a>
        <a href="<?= base_url('/provider/listings?status=draft') ?>" class="btn btn-sm <?= ($statusFilter === 'draft') ? 'btn-secondary' : 'btn-light border' ?>">
            <i class="fas fa-file mr-1"></i> Draf <span class="badge badge-light ml-1"><?= $counts['draft'] ?></span>
        </a>
        <a href="<?= base_url('/provider/listings?status=inactive') ?>" class="btn btn-sm <?= ($statusFilter === 'inactive') ? 'btn-dark' : 'btn-light border' ?>">
            <i class="fas fa-pause mr-1"></i> Nonaktif <span class="badge badge-light ml-1"><?= $counts['inactive'] ?></span>
        </a>
    </div>
</div>

<!-- Unlisted Units Banner if any -->
<?php if (!empty($unlistedUnits)): ?>
    <div class="alert alert-light border shadow-sm mb-4 d-flex justify-content-between align-items-center">
        <div>
            <strong class="text-teal"><i class="fas fa-lightbulb mr-1"></i> Ada <?= count($unlistedUnits) ?> unit kamar yang belum memiliki listing iklan!</strong>
            <p class="text-muted text-xs mb-0">Pasang listing agar unit kamar Anda dapat disewa oleh calon penghuni.</p>
        </div>
        <?php if (!empty($isQuotaFull)): ?>
            <span class="badge badge-light border text-muted py-2 px-3">
                <i class="fas fa-lock text-warning mr-1"></i> Kuota Listing Penuh (<?= $totalListings ?>/<?= $maxFreeListings ?>)
            </span>
        <?php else: ?>
            <a href="<?= base_url('/provider/listings/create') ?>" class="btn btn-xs btn-teal text-white font-weight-bold px-3">
                <i class="fas fa-plus mr-1"></i> Pasang Iklan Sekarang
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Listings Table / Cards -->
<div class="card card-outline card-teal shadow-sm">
    <div class="card-header border-0">
        <h3 class="card-title font-weight-bold">
            <i class="fas fa-rectangle-ad text-teal mr-2"></i> Daftar Iklan Listing (<?= count($listings) ?> Data)
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover m-0 text-sm align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Foto & Judul Iklan</th>
                        <th>Kamar & Properti</th>
                        <th>Target Syariat</th>
                        <th>Harga Sewa</th>
                        <th>Status Publikasi</th>
                        <th class="text-right" style="width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($listings)): ?>
                        <?php foreach ($listings as $l): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php 
                                        $thumb = !empty($l['unit_photo']) 
                                            ? $l['unit_photo'] 
                                            : (!empty($l['property_photo']) ? $l['property_photo'] : 'assets/images/sample-kos-1.jpg');
                                        ?>
                                        <img src="<?= \App\Libraries\StorageService::getUrl($thumb) ?>" alt="Thumb" class="rounded mr-2 shadow-sm" style="width: 55px; height: 50px; object-fit: cover;">
                                        <div>
                                            <a href="<?= base_url('/provider/listings/edit/' . $l['id']) ?>" class="font-weight-bold text-dark text-decoration-none">
                                                <?= esc($l['title']) ?>
                                            </a>
                                            <small class="text-muted d-block text-truncate" style="max-width: 250px;">
                                                <?= esc(strip_tags($l['description'])) ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="font-weight-bold text-dark"><?= esc($l['unit_name']) ?></span>
                                    <small class="text-muted d-block"><?= esc($l['property_name']) ?></small>
                                    <span class="badge badge-light border text-xs"><?= esc($l['unit_size'] ?? '-') ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $l['target_gender'] === 'putri' ? 'danger' : ($l['target_gender'] === 'putra' ? 'primary' : 'purple') ?>">
                                        <?= esc($l['target_gender']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if (!empty($l['price_monthly'])): ?>
                                        <div class="font-weight-bold text-teal">
                                            Rp <?= number_format($l['price_monthly'], 0, ',', '.') ?> <small class="text-muted">/ bln</small>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($l['price_six_monthly'])): ?>
                                        <small class="text-muted d-block">
                                            Rp <?= number_format($l['price_six_monthly'], 0, ',', '.') ?> / 6 bln
                                        </small>
                                    <?php endif; ?>
                                    <?php if (!empty($l['price_yearly'])): ?>
                                        <small class="text-muted d-block">
                                            Rp <?= number_format($l['price_yearly'], 0, ',', '.') ?> / thn
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php $pub = $l['publication_status']; ?>
                                    <?php if ($pub === 'published'): ?>
                                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Tayang Aktif</span>
                                    <?php elseif ($pub === 'pending_review'): ?>
                                        <span class="badge badge-warning text-dark"><i class="fas fa-clock mr-1"></i> Menunggu Review</span>
                                    <?php elseif ($pub === 'inactive'): ?>
                                        <span class="badge badge-dark"><i class="fas fa-pause mr-1"></i> Nonaktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><i class="fas fa-file mr-1"></i> Draf</span>
                                    <?php endif; ?>

                                    <?php if (!empty($l['rejection_reason'])): ?>
                                        <small class="text-danger d-block mt-1 font-weight-bold">
                                            <i class="fas fa-exclamation-circle mr-1"></i> Ditolak: "<?= esc($l['rejection_reason']) ?>"
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <a href="<?= base_url('/provider/listings/edit/' . $l['id']) ?>" class="btn btn-xs btn-light border" title="Edit Listing">
                                        <i class="fas fa-pen text-muted"></i> Edit
                                    </a>

                                    <?php if ($pub === 'draft'): ?>
                                        <form action="<?= base_url('/provider/listings/submit-review/' . $l['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-xs btn-outline-warning" title="Ajukan Review ke Admin">
                                                <i class="fas fa-paper-plane mr-1"></i> Review
                                            </button>
                                        </form>
                                    <?php elseif ($pub === 'published' || $pub === 'inactive'): ?>
                                        <form action="<?= base_url('/provider/listings/toggle-status/' . $l['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-xs btn-light border text-<?= $pub === 'published' ? 'secondary' : 'success' ?>" title="<?= $pub === 'published' ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                                <i class="fas fa-<?= $pub === 'published' ? 'pause' : 'play' ?>"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($pub === 'draft'): ?>
                                        <form action="<?= base_url('/provider/listings/delete/' . $l['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus draf listing ini?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-xs btn-light border text-danger" title="Hapus Listing">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="fas fa-bullhorn fa-3x mb-2 d-block"></i>
                                Tidak ada listing iklan dalam kategori ini.
                                <div class="mt-2">
                                    <a href="<?= base_url('/provider/listings/create') ?>" class="btn btn-teal text-white btn-sm font-weight-bold">
                                        <i class="fas fa-plus mr-1"></i> Buat Listing Baru
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
