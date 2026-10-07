<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-building text-teal mr-2"></i> Manajemen Properti Saya
            </h1>
            <p class="text-muted small mb-0">Kelola daftar gedung kos, kontrakan, atau rumah sewa yang Anda miliki.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/provider/properties/create') ?>" class="btn btn-teal text-white btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-plus-circle mr-1"></i> Tambah Properti Baru
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

<div class="row">
    <?php if (!empty($properties)): ?>
        <?php foreach ($properties as $prop): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0 overflow-hidden d-flex flex-column">
                    <div class="position-relative" style="height: 190px; background-color: #f1f5f9;">
                        <?php $imgSrc = !empty($prop['primary_photo']) ? \App\Libraries\StorageService::getUrl($prop['primary_photo']) : base_url('assets/images/sample-kos-1.jpg'); ?>
                        <img src="<?= $imgSrc ?>" alt="<?= esc($prop['name']) ?>" class="w-100 h-100" style="object-fit: cover;">
                        
                        <!-- Gender & Type Badges -->
                        <div class="position-absolute" style="top: 10px; left: 10px;">
                            <?php if ($prop['target_gender'] === 'putri'): ?>
                                <span class="badge badge-danger px-2 py-1 shadow-sm"><i class="fas fa-female mr-1"></i> Khusus Putri</span>
                            <?php elseif ($prop['target_gender'] === 'putra'): ?>
                                <span class="badge badge-primary px-2 py-1 shadow-sm"><i class="fas fa-male mr-1"></i> Khusus Putra</span>
                            <?php else: ?>
                                <span class="badge badge-purple px-2 py-1 shadow-sm text-white" style="background-color: #7c3aed;"><i class="fas fa-people-roof mr-1"></i> Pasutri / Keluarga</span>
                            <?php endif; ?>
                        </div>

                        <div class="position-absolute" style="top: 10px; right: 10px;">
                            <span class="badge badge-dark text-capitalize px-2 py-1 shadow-sm"><?= esc($prop['type']) ?></span>
                        </div>
                    </div>

                    <div class="card-body p-3 flex-grow-1">
                        <h5 class="card-title font-weight-bold text-dark mb-1 text-truncate" style="max-width: 100%;">
                            <a href="<?= base_url('/provider/properties/' . $prop['id']) ?>" class="text-dark text-decoration-none">
                                <?= esc($prop['name']) ?>
                            </a>
                        </h5>
                        <p class="text-muted text-xs mb-2">
                            <i class="fas fa-map-marker-alt text-danger mr-1"></i> <?= esc($prop['region_name'] ?? 'Wilayah') ?>
                        </p>
                        <p class="text-secondary text-sm text-truncate mb-3" style="max-width: 100%;">
                            <?= esc($prop['address']) ?>
                        </p>

                        <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light mb-3 text-sm">
                            <div>
                                <span class="text-muted d-block text-xs">Total Unit:</span>
                                <strong><?= $prop['total_units'] ?> Kamar</strong>
                            </div>
                            <div class="text-right">
                                <span class="text-muted d-block text-xs">Tersedia:</span>
                                <strong class="text-success"><?= $prop['available_units'] ?> Kamar</strong>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white border-top p-3 d-flex justify-content-between align-items-center">
                        <a href="<?= base_url('/provider/properties/' . $prop['id']) ?>" class="btn btn-sm btn-outline-teal font-weight-bold">
                            <i class="fas fa-door-open mr-1"></i> Kelola Unit (<?= $prop['total_units'] ?>)
                        </a>
                        <div>
                            <a href="<?= base_url('/provider/properties/edit/' . $prop['id']) ?>" class="btn btn-sm btn-light border" title="Edit Properti">
                                <i class="fas fa-pen text-muted"></i>
                            </a>
                            <form action="<?= base_url('/provider/properties/delete/' . $prop['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus properti ini beserta seluruh unit di dalamnya?');">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-sm btn-light border text-danger" title="Hapus Properti">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="card shadow-sm border-0 text-center py-5">
                <div class="card-body">
                    <i class="fas fa-building text-muted fa-4x mb-3"></i>
                    <h4 class="font-weight-bold text-dark">Belum Ada Properti Terdaftar</h4>
                    <p class="text-muted text-sm" style="max-width: 500px; margin: 0 auto;">
                        Mulai daftarkan gedung kos, kontrakan, atau rumah sewa Anda untuk menambahkan kamar fisik dan mempublikasikan listing iklan.
                    </p>
                    <a href="<?= base_url('/provider/properties/create') ?>" class="btn btn-teal text-white mt-3 px-4 font-weight-bold shadow-sm">
                        <i class="fas fa-plus mr-1"></i> Tambah Properti Sekarang
                    </a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
