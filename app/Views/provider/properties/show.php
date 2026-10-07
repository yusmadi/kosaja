<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-7">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-building text-teal mr-2"></i> <?= esc($property['name']) ?>
            </h1>
            <p class="text-muted small mb-0">
                <i class="fas fa-map-marker-alt text-danger mr-1"></i> <?= esc($property['region_name']) ?> — <?= esc($property['address']) ?>
            </p>
        </div>
        <div class="col-sm-5 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/provider/properties/' . $property['id'] . '/units/create') ?>" class="btn btn-teal text-white btn-sm shadow-sm font-weight-bold mr-1">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Kamar/Unit Baru
            </a>
            <a href="<?= base_url('/provider/properties/edit/' . $property['id']) ?>" class="btn btn-outline-secondary btn-sm mr-1">
                <i class="fas fa-pen mr-1"></i> Edit Properti
            </a>
            <a href="<?= base_url('/provider/properties') ?>" class="btn btn-light border btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Daftar Properti
            </a>
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

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Overview Cards Row -->
<div class="row mb-3">
    <div class="col-md-3 col-sm-6 mb-2">
        <div class="info-box shadow-sm mb-0">
            <span class="info-box-icon bg-teal text-white elevation-1"><i class="fas fa-layer-group"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Kategori</span>
                <span class="info-box-number text-capitalize"><?= esc($property['type']) ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <div class="info-box shadow-sm mb-0">
            <span class="info-box-icon <?= $property['target_gender'] === 'putri' ? 'bg-danger' : ($property['target_gender'] === 'putra' ? 'bg-primary' : 'bg-purple') ?> text-white elevation-1">
                <i class="fas <?= $property['target_gender'] === 'putri' ? 'fa-female' : ($property['target_gender'] === 'putra' ? 'fa-male' : 'fa-people-roof') ?>"></i>
            </span>
            <div class="info-box-content">
                <span class="info-box-text">Target Syariat</span>
                <span class="info-box-number text-capitalize"><?= esc($property['target_gender']) ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <div class="info-box shadow-sm mb-0">
            <span class="info-box-icon bg-info elevation-1 text-white"><i class="fas fa-door-open"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Unit Kamar</span>
                <span class="info-box-number"><?= count($property['units']) ?> Kamar</span>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6 mb-2">
        <div class="info-box shadow-sm mb-0">
            <span class="info-box-icon bg-success elevation-1 text-white"><i class="fas fa-circle-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Kamar Tersedia</span>
                <span class="info-box-number">
                    <?= count(array_filter($property['units'], fn($u) => $u['status'] === 'available')) ?> Kamar
                </span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Kolom Kiri: Tabel Unit Kamar Fisik & Listing -->
    <div class="col-lg-8">
        <div class="card card-outline card-teal shadow-sm mb-4">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-bed text-teal mr-2"></i> Daftar Unit Kamar Fisik (<?= count($property['units']) ?> Unit)
                </h3>
                <a href="<?= base_url('/provider/properties/' . $property['id'] . '/units/create') ?>" class="btn btn-xs btn-teal text-white font-weight-bold">
                    <i class="fas fa-plus mr-1"></i> Tambah Kamar
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover m-0 text-sm align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama / No. Unit</th>
                                <th>Ukuran & Posisi</th>
                                <th>Status Kamar</th>
                                <th>Listing Iklan</th>
                                <th class="text-right" style="width: 140px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($property['units'])): ?>
                                <?php foreach ($property['units'] as $u): ?>
                                    <tr>
                                        <td>
                                            <span class="font-weight-bold text-dark"><?= esc($u['name']) ?></span>
                                        </td>
                                        <td>
                                            <span><?= esc($u['size'] ?? '-') ?></span>
                                            <small class="text-muted d-block"><?= esc($u['floor'] ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <?php if ($u['status'] === 'available'): ?>
                                                <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Tersedia</span>
                                            <?php elseif ($u['status'] === 'occupied'): ?>
                                                <span class="badge badge-secondary"><i class="fas fa-user-lock mr-1"></i> Terisi</span>
                                            <?php elseif ($u['status'] === 'reserved'): ?>
                                                <span class="badge badge-info"><i class="fas fa-bookmark mr-1"></i> Dipesan</span>
                                            <?php elseif ($u['status'] === 'maintenance'): ?>
                                                <span class="badge badge-warning text-dark"><i class="fas fa-wrench mr-1"></i> Perawatan</span>
                                            <?php else: ?>
                                                <span class="badge badge-light border"><?= esc($u['status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($u['listing_id'])): ?>
                                                <div class="text-truncate font-weight-bold text-dark" style="max-width: 160px;">
                                                    <?= esc($u['listing_title']) ?>
                                                </div>
                                                <div class="d-flex align-items-center mt-1">
                                                    <?php $pub = $u['publication_status'] ?? 'draft'; ?>
                                                    <?php if ($pub === 'published'): ?>
                                                        <span class="badge badge-success mr-1">Tayang</span>
                                                    <?php elseif ($pub === 'pending_review'): ?>
                                                        <span class="badge badge-warning mr-1">Review</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-secondary mr-1"><?= ucfirst($pub) ?></span>
                                                    <?php endif; ?>
                                                    <a href="<?= base_url('/provider/listings/edit/' . $u['listing_id']) ?>" class="btn btn-xs btn-link p-0 text-teal">
                                                        Edit Iklan
                                                    </a>
                                                </div>
                                            <?php else: ?>
                                                <a href="<?= base_url('/provider/listings/create/' . $u['id']) ?>" class="btn btn-xs btn-outline-teal font-weight-bold">
                                                    <i class="fas fa-bullhorn mr-1"></i> Buat Iklan
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?= base_url('/provider/units/edit/' . $u['id']) ?>" class="btn btn-xs btn-light border mr-1" title="Edit Unit">
                                                <i class="fas fa-pen text-muted"></i>
                                            </a>
                                            <form action="<?= base_url('/provider/units/delete/' . $u['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus unit kamar ini?');">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-xs btn-light border text-danger" title="Hapus Unit">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fas fa-door-closed fa-2x mb-2 d-block"></i>
                                        Belum ada unit kamar di properti ini.
                                        <div class="mt-2">
                                            <a href="<?= base_url('/provider/properties/' . $property['id'] . '/units/create') ?>" class="btn btn-xs btn-teal text-white">
                                                <i class="fas fa-plus mr-1"></i> Tambah Unit Pertama
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

        <!-- Fasilitas & Deskripsi -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-list-check text-teal mr-2"></i> Fasilitas & Peraturan
                </h5>
            </div>
            <div class="card-body">
                <h6 class="font-weight-bold text-dark mb-2">Fasilitas Bersama:</h6>
                <div class="row mb-3">
                    <?php if (!empty($property['facilities'])): ?>
                        <?php foreach ($property['facilities'] as $fac): ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi <?= esc($fac['icon'] ?? 'bi-check-circle') ?> text-teal fa-lg mr-2"></i>
                                    <span><?= esc($fac['facility_name']) ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-muted text-sm">Belum ada fasilitas yang dicantumkan.</div>
                    <?php endif; ?>
                </div>

                <hr>

                <h6 class="font-weight-bold text-dark mb-2">Deskripsi Bangunan:</h6>
                <p class="text-secondary text-sm mb-3">
                    <?= nl2br(esc($property['description'] ?? 'Tidak ada deskripsi.')) ?>
                </p>

                <h6 class="font-weight-bold text-dark mb-2">Tata Tertib & Aturan Properti:</h6>
                <div class="p-3 bg-light rounded text-sm text-dark border">
                    <?= nl2br(esc($property['rules'] ?? 'Tidak ada peraturan khusus.')) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Galeri Foto Properti -->
    <div class="col-lg-4">
        <div class="card card-outline card-teal shadow-sm mb-4">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-images text-teal mr-2"></i> Galeri Foto Properti
                </h3>
            </div>
            <div class="card-body p-3">
                <?php if (!empty($property['photos'])): ?>
                    <div class="row">
                        <?php foreach ($property['photos'] as $photo): ?>
                            <div class="col-6 mb-3">
                                <div class="position-relative border rounded overflow-hidden shadow-sm">
                                    <img src="<?= \App\Libraries\StorageService::getUrl($photo['photo_url']) ?>" alt="Foto Properti" class="w-100" style="height: 110px; object-fit: cover;">
                                    <?php if ($photo['is_primary']): ?>
                                        <span class="badge badge-success position-absolute" style="top: 5px; left: 5px;">Sampul</span>
                                    <?php endif; ?>
                                    <div class="p-1 bg-white d-flex justify-content-between align-items-center">
                                        <?php if (!$photo['is_primary']): ?>
                                            <form action="<?= base_url('/provider/properties/photos/set-primary/' . $property['id'] . '/' . $photo['id']) ?>" method="post" class="d-inline">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-xs btn-link p-0 text-teal" title="Jadikan Sampul">
                                                    <i class="fas fa-star"></i> Sampul
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-xs text-muted"><i class="fas fa-check text-success"></i> Utama</span>
                                        <?php endif; ?>

                                        <form action="<?= base_url('/provider/properties/photos/delete/' . $photo['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus foto ini?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-xs btn-link p-0 text-danger" title="Hapus Foto">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-image fa-2x mb-2 d-block"></i>
                        Belum ada foto yang diunggah.
                    </div>
                <?php endif; ?>

                <a href="<?= base_url('/provider/properties/edit/' . $property['id']) ?>" class="btn btn-outline-teal btn-block btn-sm mt-2">
                    <i class="fas fa-upload mr-1"></i> Tambah / Kelola Foto
                </a>
            </div>
        </div>

        <!-- Quick Help Card -->
        <div class="card shadow-sm border-0 bg-light">
            <div class="card-body text-sm">
                <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-lightbulb text-warning mr-1"></i> Panduan Alur Sprint 3</h6>
                <ol class="pl-3 mb-0 text-muted">
                    <li class="mb-1"><strong>Buat Properti</strong>: Wadah fisik gedung (sudah dibuat).</li>
                    <li class="mb-1"><strong>Tambah Unit</strong>: Daftarkan kamar-kamar fisik di dalamnya.</li>
                    <li><strong>Buat Listing</strong>: Pasang iklan per unit kamar agar dapat dipromosikan ke calon penyewa.</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
