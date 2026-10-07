<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-bed text-teal mr-2"></i> Inventaris Unit & Kamar
            </h1>
            <p class="text-muted small mb-0">Kelola ketersediaan fisik seluruh unit kamar di semua properti Anda.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/provider/properties') ?>" class="btn btn-outline-teal btn-sm font-weight-bold">
                <i class="fas fa-building mr-1"></i> Lihat Berdasarkan Properti
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
        <i class="fas fa-times-circle mr-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="card card-outline card-teal shadow-sm">
    <div class="card-header border-0 d-flex justify-content-between align-items-center">
        <h3 class="card-title font-weight-bold">
            <i class="fas fa-door-open text-teal mr-2"></i> Total Inventaris (<?= count($units) ?> Kamar)
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover m-0 text-sm align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Nama Unit</th>
                        <th>Gedung Properti</th>
                        <th>Ukuran / Lantai</th>
                        <th>Status Fisik</th>
                        <th>Listing Iklan</th>
                        <th class="text-right" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($units)): ?>
                        <?php foreach ($units as $u): ?>
                            <tr>
                                <td class="font-weight-bold text-dark"><?= esc($u['name']) ?></td>
                                <td>
                                    <span class="font-weight-bold"><?= esc($u['property_name']) ?></span>
                                    <br>
                                    <small class="badge badge-<?= $u['target_gender'] === 'putri' ? 'danger' : ($u['target_gender'] === 'putra' ? 'primary' : 'purple') ?>">
                                        <?= esc($u['target_gender']) ?>
                                    </small>
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
                                        <span class="badge badge-<?= $u['publication_status'] === 'published' ? 'success' : ($u['publication_status'] === 'pending_review' ? 'warning' : 'secondary') ?>">
                                            <?= ucfirst($u['publication_status']) ?>
                                        </span>
                                        <a href="<?= base_url('/provider/listings/edit/' . $u['listing_id']) ?>" class="btn btn-xs btn-link text-teal">
                                            Edit Iklan
                                        </a>
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
                                    <form action="<?= base_url('/provider/units/delete/' . $u['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus unit ini?');">
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
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="fas fa-bed fa-2x mb-2 d-block"></i>
                                Belum ada unit kamar terdaftar. Silakan pilih properti Anda dan tambahkan unit kamar fisik.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
