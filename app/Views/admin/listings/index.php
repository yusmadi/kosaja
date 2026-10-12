<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-shield-halved text-teal mr-2"></i> Moderasi Listing Iklan
            </h1>
            <p class="text-muted small mb-0">Kurasi dan verifikasi iklan hunian dari mitra provider sebelum dipublikasikan ke marketplace KosAja.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/admin/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Dashboard Overview
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

<!-- Filter Tabs & Search Bar -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <!-- Filter Tabs -->
            <div class="btn-group shadow-sm mb-2 mb-md-0">
                <a href="<?= base_url('/admin/listings?status=all') ?>" class="btn btn-sm <?= ($statusFilter === 'all') ? 'btn-teal text-white' : 'btn-light border' ?>">
                    Semua <span class="badge badge-light ml-1"><?= $counts['all'] ?></span>
                </a>
                <a href="<?= base_url('/admin/listings?status=pending_review') ?>" class="btn btn-sm <?= ($statusFilter === 'pending_review') ? 'btn-warning text-dark font-weight-bold' : 'btn-light border' ?>">
                    <i class="fas fa-clock mr-1"></i> Butuh Moderasi <span class="badge badge-danger ml-1"><?= $counts['pending_review'] ?></span>
                </a>
                <a href="<?= base_url('/admin/listings?status=published') ?>" class="btn btn-sm <?= ($statusFilter === 'published') ? 'btn-success' : 'btn-light border' ?>">
                    <i class="fas fa-check mr-1"></i> Tayang Aktif <span class="badge badge-secondary ml-1"><?= $counts['published'] ?></span>
                </a>
                <a href="<?= base_url('/admin/listings?status=draft') ?>" class="btn btn-sm <?= ($statusFilter === 'draft') ? 'btn-info' : 'btn-light border' ?>">
                    Draf / Ditolak <span class="badge badge-secondary ml-1"><?= $counts['draft'] ?></span>
                </a>
                <a href="<?= base_url('/admin/listings?status=inactive') ?>" class="btn btn-sm <?= ($statusFilter === 'inactive') ? 'btn-secondary' : 'btn-light border' ?>">
                    Nonaktif <span class="badge badge-secondary ml-1"><?= $counts['inactive'] ?></span>
                </a>
            </div>

            <!-- Search Form -->
            <form action="<?= base_url('/admin/listings') ?>" method="get" class="form-inline">
                <input type="hidden" name="status" value="<?= esc($statusFilter) ?>">
                <div class="input-group input-group-sm">
                    <input type="text" name="q" class="form-control" placeholder="Cari judul, properti, mitra..." value="<?= esc($keyword) ?>">
                    <div class="input-group-append">
                        <button class="btn btn-teal text-white" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        <?php if (!empty($keyword)): ?>
                            <a href="<?= base_url('/admin/listings?status=' . esc($statusFilter)) ?>" class="btn btn-outline-secondary" title="Reset Pencarian">
                                <i class="fas fa-times"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Moderation Listings Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3">
        <h5 class="card-title font-weight-bold text-dark m-0">
            <i class="fas fa-list text-muted mr-2"></i> Daftar Listing Iklan
            <span class="badge badge-light border ml-1"><?= count($listings) ?> listing ditemukan</span>
        </h5>
    </div>
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-uppercase text-secondary" style="font-size: 0.75rem;">
                <tr>
                    <th style="width: 50px;">#</th>
                    <th style="width: 80px;">Foto</th>
                    <th>Judul Listing & Unit</th>
                    <th>Properti & Wilayah</th>
                    <th>Mitra Provider</th>
                    <th>Kategori / Syariat</th>
                    <th>Harga Bulanan</th>
                    <th>Status</th>
                    <th style="width: 170px;" class="text-center">Aksi Moderasi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($listings)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-3x mb-3 text-secondary opacity-50"></i>
                            <p class="font-weight-bold mb-1">Tidak ada listing iklan yang sesuai filter.</p>
                            <span class="small">Silakan pilih status lain atau bersihkan kata kunci pencarian.</span>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($listings as $idx => $l): ?>
                        <tr class="<?= $l['publication_status'] === 'pending_review' ? 'table-warning-subtle font-weight-500' : '' ?>">
                            <td><?= $idx + 1 ?></td>
                            <td>
                                <img src="<?= base_url(esc($l['photo_url'] ?? 'assets/images/sample-kos-1.jpg')) ?>" 
                                     alt="Thumb" 
                                     class="rounded shadow-sm" 
                                     style="width: 60px; height: 50px; object-fit: cover;">
                            </td>
                            <td>
                                <a href="<?= base_url('/admin/listings/' . $l['id']) ?>" class="font-weight-bold text-dark text-decoration-none">
                                    <?= esc($l['title']) ?>
                                </a>
                                <div class="small text-muted">
                                    <i class="fas fa-bed mr-1 text-teal"></i> <?= esc($l['unit_name']) ?> (<?= esc($l['unit_size'] ?? '-') ?>)
                                    <?php if ($l['unit_status'] === 'occupied'): ?>
                                        <span class="badge badge-secondary ml-1">Terisi</span>
                                    <?php endif; ?>
                                </div>
                                <?php if (!empty($l['rejection_reason'])): ?>
                                    <div class="mt-1 small text-danger font-italic">
                                        <i class="fas fa-exclamation-circle mr-1"></i> Catatan Penolakan: <?= esc($l['rejection_reason']) ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="font-weight-bold text-secondary"><?= esc($l['property_name']) ?></span>
                                <div class="small text-muted">
                                    <i class="fas fa-map-marker-alt text-danger mr-1"></i> <?= esc($l['region_name'] ?? 'Sleman') ?>
                                </div>
                            </td>
                            <td>
                                <div class="font-weight-bold text-dark"><?= esc($l['provider_name']) ?></div>
                                <div class="small">
                                    <?php if ($l['provider_kyc'] === 'verified'): ?>
                                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> KYC Valid</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning text-dark"><i class="fas fa-clock mr-1"></i> KYC <?= esc(ucfirst($l['provider_kyc'] ?? 'Unverified')) ?></span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <div class="mb-1">
                                    <?php if ($l['target_gender'] === 'putri'): ?>
                                        <span class="badge badge-danger"><i class="fas fa-venus mr-1"></i> Khusus Putri</span>
                                    <?php elseif ($l['target_gender'] === 'putra'): ?>
                                        <span class="badge badge-primary"><i class="fas fa-mars mr-1"></i> Khusus Putra</span>
                                    <?php else: ?>
                                        <span class="badge badge-purple text-white"><i class="fas fa-users mr-1"></i> Keluarga</span>
                                    <?php endif; ?>
                                </div>
                                <span class="badge badge-light border text-uppercase" style="font-size: 0.68rem;">
                                    <?= esc(str_replace('_', ' ', $l['type'])) ?>
                                </span>
                            </td>
                            <td>
                                <span class="font-weight-bold text-teal">
                                    Rp <?= number_format($l['price_monthly'], 0, ',', '.') ?>
                                </span>
                                <span class="text-muted small">/bln</span>
                            </td>
                            <td>
                                <?php if ($l['publication_status'] === 'published'): ?>
                                    <span class="badge badge-success px-2 py-1"><i class="fas fa-circle-check mr-1"></i> Tayang Aktif</span>
                                <?php elseif ($l['publication_status'] === 'pending_review'): ?>
                                    <span class="badge badge-warning px-2 py-1 text-dark animate-pulse"><i class="fas fa-hourglass-half mr-1"></i> Butuh Review</span>
                                <?php elseif ($l['publication_status'] === 'draft'): ?>
                                    <span class="badge badge-secondary px-2 py-1"><i class="fas fa-pencil mr-1"></i> Draf</span>
                                <?php else: ?>
                                    <span class="badge badge-dark px-2 py-1"><i class="fas fa-ban mr-1"></i> Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm shadow-sm">
                                    <a href="<?= base_url('/admin/listings/' . $l['id']) ?>" class="btn btn-outline-info" title="Review Detail">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>

                                    <?php if ($l['publication_status'] === 'pending_review'): ?>
                                        <!-- Quick Approve -->
                                        <a href="<?= base_url('/admin/listings/' . $l['id']) ?>" class="btn btn-success" title="Setujui Listing">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <!-- Quick Reject Modal Trigger -->
                                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectModal<?= $l['id'] ?>" title="Tolak Listing">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php elseif ($l['publication_status'] === 'published'): ?>
                                        <!-- Toggle Suspend -->
                                        <form action="<?= base_url('/admin/listings/toggle/' . $l['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan listing ini?')">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="reason" value="Dinonaktifkan oleh administrator daerah.">
                                            <button type="submit" class="btn btn-outline-secondary" title="Nonaktifkan Listing">
                                                <i class="fas fa-pause"></i>
                                            </button>
                                        </form>
                                    <?php elseif ($l['publication_status'] === 'inactive'): ?>
                                        <form action="<?= base_url('/admin/listings/toggle/' . $l['id']) ?>" method="post" class="d-inline">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="reason" value="Diaktifkan kembali oleh administrator daerah.">
                                            <button type="submit" class="btn btn-outline-success" title="Aktifkan Kembali">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>

                                <!-- Reject Modal for row -->
                                <?php if ($l['publication_status'] === 'pending_review'): ?>
                                    <div class="modal fade" id="rejectModal<?= $l['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content text-left">
                                                <form action="<?= base_url('/admin/listings/reject/' . $l['id']) ?>" method="post">
                                                    <?= csrf_field() ?>
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title font-weight-bold">
                                                            <i class="fas fa-exclamation-triangle mr-2"></i> Tolak Listing Iklan
                                                        </h5>
                                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-2 text-dark font-weight-bold">Listing: <?= esc($l['title']) ?></p>
                                                        <p class="small text-muted mb-3">Tuliskan alasan penolakan secara jelas agar pemilik properti dapat melakukan perbaikan yang diperlukan.</p>
                                                        <div class="form-group">
                                                            <label class="font-weight-bold text-dark small">Alasan Penolakan <span class="text-danger">*</span></label>
                                                            <textarea name="reason" class="form-control" rows="3" required placeholder="Contoh: Foto kamar mandi belum diunggah, deskripsi fasilitas belum lengkap, atau informasi harga tidak sesuai."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                                        <button type="submit" class="btn btn-danger btn-sm font-weight-bold">
                                                            <i class="fas fa-ban mr-1"></i> Konfirmasi Penolakan
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
