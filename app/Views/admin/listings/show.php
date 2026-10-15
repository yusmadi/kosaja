<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-7">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-file-shield text-teal mr-2"></i> Review Moderasi Listing
            </h1>
            <p class="text-muted small mb-0">Periksa kepatuhan syariat, kelengkapan informasi, foto fasilitas, dan harga sewa sebelum menyetujui iklan.</p>
        </div>
        <div class="col-sm-5 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/admin/listings') ?>" class="btn btn-outline-secondary btn-sm mr-1">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
            <a href="<?= base_url('/listing/' . $listing['id']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-external-link-alt mr-1"></i> Pratinjau Publik
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

<!-- Status Banner / Action Bar -->
<div class="card shadow-sm border-0 mb-4 <?= $listing['publication_status'] === 'pending_review' ? 'border-left border-warning' : ($listing['publication_status'] === 'published' ? 'border-left border-success' : '') ?>">
    <div class="card-body p-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="text-muted small text-uppercase font-weight-bold">Status Listing Saat Ini:</span>
                <div class="mt-1 d-flex align-items-center">
                    <?php if ($listing['publication_status'] === 'published'): ?>
                        <span class="badge badge-success px-3 py-2 font-weight-bold h6 mb-0">
                            <i class="fas fa-circle-check mr-1"></i> TAYANG AKTIF (PUBLISHED)
                        </span>
                    <?php elseif ($listing['publication_status'] === 'pending_review'): ?>
                        <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold h6 mb-0 animate-pulse">
                            <i class="fas fa-hourglass-half mr-1"></i> MENUNGGU REVIEW KURATOR
                        </span>
                    <?php elseif ($listing['publication_status'] === 'draft'): ?>
                        <span class="badge badge-secondary px-3 py-2 font-weight-bold h6 mb-0">
                            <i class="fas fa-pencil mr-1"></i> DRAF / PERBAIKAN MITRA
                        </span>
                    <?php else: ?>
                        <span class="badge badge-dark px-3 py-2 font-weight-bold h6 mb-0">
                            <i class="fas fa-ban mr-1"></i> NONAKTIF (INACTIVE)
                        </span>
                    <?php endif; ?>
                </div>

                <?php if (!empty($listing['rejection_reason'])): ?>
                    <div class="mt-2 text-danger small font-weight-bold bg-danger-subtle p-2 rounded">
                        <i class="fas fa-exclamation-triangle mr-1"></i> Catatan Penolakan Terakhir: <?= esc($listing['rejection_reason']) ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <?php if ($listing['publication_status'] === 'pending_review'): ?>
                    <!-- Approve Button -->
                    <form action="<?= base_url('/admin/listings/approve/' . $listing['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui listing ini untuk tayang di marketplace?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success font-weight-bold px-3 shadow-sm">
                            <i class="fas fa-check-circle mr-1"></i> Setujui & Publikasikan
                        </button>
                    </form>

                    <!-- Reject Modal Trigger -->
                    <button type="button" class="btn btn-danger font-weight-bold px-3 shadow-sm" data-toggle="modal" data-target="#rejectModal">
                        <i class="fas fa-times-circle mr-1"></i> Tolak Listing
                    </button>
                <?php elseif ($listing['publication_status'] === 'published'): ?>
                    <form action="<?= base_url('/admin/listings/toggle/' . $listing['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan listing ini?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="reason" value="Listing dinonaktifkan oleh admin.">
                        <button type="submit" class="btn btn-outline-danger font-weight-bold">
                            <i class="fas fa-pause mr-1"></i> Tangguhkan / Nonaktifkan
                        </button>
                    </form>
                <?php else: ?>
                    <!-- Quick Approve / Force Publish -->
                    <form action="<?= base_url('/admin/listings/approve/' . $listing['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin mengaktifkan listing ini?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-success font-weight-bold">
                            <i class="fas fa-check mr-1"></i> Aktifkan ke Publik
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Detailed Information -->
    <div class="col-lg-8">
        <!-- Main Listing Details Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold text-dark m-0">
                    <i class="fas fa-info-circle text-teal mr-2"></i> Informasi Listing & Unit
                </h5>
            </div>
            <div class="card-body">
                <h3 class="h4 font-weight-bold text-dark mb-2"><?= esc($listing['title']) ?></h3>
                
                <div class="d-flex flex-wrap gap-2 mb-3">
                    <span class="badge badge-teal text-white px-2 py-1">
                        <i class="fas fa-building mr-1"></i> <?= esc(str_replace('_', ' ', strtoupper($listing['type']))) ?>
                    </span>
                    <?php if ($listing['target_gender'] === 'putri'): ?>
                        <span class="badge badge-danger px-2 py-1"><i class="fas fa-venus mr-1"></i> KHUSUS PUTRI</span>
                    <?php elseif ($listing['target_gender'] === 'putra'): ?>
                        <span class="badge badge-primary px-2 py-1"><i class="fas fa-mars mr-1"></i> KHUSUS PUTRA</span>
                    <?php else: ?>
                        <span class="badge badge-purple text-white px-2 py-1"><i class="fas fa-users mr-1"></i> PASUTRI / KELUARGA</span>
                    <?php endif; ?>
                    <span class="badge badge-light border px-2 py-1">
                        <i class="fas fa-map-marker-alt text-danger mr-1"></i> <?= esc($listing['region_name'] ?? 'Sleman') ?>
                    </span>
                </div>

                <div class="row g-3 p-3 bg-light rounded-3 mb-4">
                    <div class="col-sm-4">
                        <div class="text-muted small">Harga Sewa Bulanan</div>
                        <div class="h5 font-weight-bold text-teal mb-0">
                            Rp <?= number_format($listing['price_monthly'], 0, ',', '.') ?> <span class="small text-muted font-weight-normal">/ bulan</span>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Harga Sewa Enam Bulan</div>
                        <div class="h5 font-weight-bold text-dark mb-0">
                            <?= !empty($listing['price_six_monthly']) ? 'Rp ' . number_format($listing['price_six_monthly'], 0, ',', '.') . ' <span class="small text-muted font-weight-normal">/ 6 bulan</span>' : '<span class="text-muted font-weight-normal">Tidak ditawarkan</span>' ?>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="text-muted small">Harga Sewa Tahunan</div>
                        <div class="h5 font-weight-bold text-dark mb-0">
                            <?= !empty($listing['price_yearly']) ? 'Rp ' . number_format($listing['price_yearly'], 0, ',', '.') . ' <span class="small text-muted font-weight-normal">/ tahun</span>' : '<span class="text-muted font-weight-normal">Tidak ditawarkan</span>' ?>
                        </div>
                    </div>
                </div>

                <h6 class="font-weight-bold text-dark mb-2">Deskripsi Listing</h6>
                <div class="text-secondary mb-4" style="line-height: 1.7; white-space: pre-line;">
                    <?= esc($listing['description']) ?>
                </div>

                <hr>

                <!-- Unit Specification -->
                <h6 class="font-weight-bold text-dark mb-3">Spesifikasi Unit Kamar Fisik</h6>
                <div class="row g-3 mb-4">
                    <div class="col-sm-4">
                        <div class="p-2 border rounded text-center">
                            <div class="text-muted small">Nama / Kode Unit</div>
                            <div class="font-weight-bold text-dark"><?= esc($listing['unit_name']) ?></div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-2 border rounded text-center">
                            <div class="text-muted small">Dimensi / Ukuran</div>
                            <div class="font-weight-bold text-dark"><?= esc($listing['unit_size'] ?? '3 x 4 m') ?></div>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="p-2 border rounded text-center">
                            <div class="text-muted small">Lantai</div>
                            <div class="font-weight-bold text-dark"><?= esc($listing['unit_floor'] ?? 'Lantai 1') ?></div>
                        </div>
                    </div>
                </div>

                <h6 class="font-weight-bold text-dark mb-2">Fasilitas Properti</h6>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <?php if (empty($facilities)): ?>
                        <span class="text-muted small">Tidak ada fasilitas khusus yang didaftarkan.</span>
                    <?php else: ?>
                        <?php foreach ($facilities as $f): ?>
                            <span class="badge badge-light border text-dark p-2" style="font-size: 0.85rem;">
                                <i class="bi <?= esc($f['icon'] ?? 'bi-check-circle') ?> text-teal mr-1"></i>
                                <?= esc($f['facility_name']) ?>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <h6 class="font-weight-bold text-dark mb-2">Tata Tertib & Syariat Properti</h6>
                <div class="p-3 bg-light border-left border-teal rounded mb-0 text-secondary" style="white-space: pre-line;">
                    <?= esc($listing['rules'] ?? "1. Wajib menjaga kesopanan & ketertiban lingkungan.\n2. Lawan jenis dilarang masuk ke kamar hunian.\n3. Wajib melampirkan fotokopi Buku Nikah jika menyewa sebagai pasangan suami istri.") ?>
                </div>
            </div>
        </div>

        <!-- Photo Gallery Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold text-dark m-0">
                    <i class="fas fa-images text-teal mr-2"></i> Galeri Foto Properti & Unit
                </h5>
            </div>
            <div class="card-body">
                <h6 class="font-weight-bold text-muted small text-uppercase mb-2">Foto Properti / Gedung</h6>
                <div class="row g-2 mb-4">
                    <?php if (empty($propertyPhotos)): ?>
                        <div class="col-12"><p class="text-muted small">Belum ada foto gedung yang diunggah.</p></div>
                    <?php else: ?>
                        <?php foreach ($propertyPhotos as $p): ?>
                            <div class="col-md-4 col-6">
                                <div class="position-relative border rounded overflow-hidden shadow-sm" style="height: 140px;">
                                    <img src="<?= base_url(esc($p['photo_url'])) ?>" alt="Gedung" class="w-100 h-100" style="object-fit: cover;">
                                    <?php if ($p['is_primary']): ?>
                                        <span class="badge badge-teal position-absolute" style="top: 5px; left: 5px;">Sampul Utama</span>
                                    <?php endif; ?>
                                </div>
                                <div class="small text-muted text-truncate mt-1"><?= esc($p['caption'] ?? 'Foto Gedung') ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <h6 class="font-weight-bold text-muted small text-uppercase mb-2">Foto Unit Kamar</h6>
                <div class="row g-2">
                    <?php if (empty($unitPhotos)): ?>
                        <div class="col-12"><p class="text-muted small">Belum ada foto kamar spesifik yang diunggah.</p></div>
                    <?php else: ?>
                        <?php foreach ($unitPhotos as $up): ?>
                            <div class="col-md-4 col-6">
                                <div class="position-relative border rounded overflow-hidden shadow-sm" style="height: 140px;">
                                    <img src="<?= base_url(esc($up['photo_url'])) ?>" alt="Unit" class="w-100 h-100" style="object-fit: cover;">
                                </div>
                                <div class="small text-muted text-truncate mt-1"><?= esc($up['caption'] ?? 'Foto Kamar') ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Provider & Audit Logs -->
    <div class="col-lg-4">
        <!-- Provider Info Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold text-dark m-0">
                    <i class="fas fa-user-tie text-teal mr-2"></i> Mitra Pemilik (Provider)
                </h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar-circle bg-teal text-white mr-3 font-weight-bold d-flex align-items-center justify-content-center rounded-circle" style="width: 48px; height: 48px; font-size: 1.2rem;">
                        <?= esc(substr($listing['provider_name'], 0, 1)) ?>
                    </div>
                    <div>
                        <h6 class="font-weight-bold text-dark mb-0"><?= esc($listing['provider_name']) ?></h6>
                        <span class="small text-muted"><?= esc($listing['provider_email']) ?></span>
                    </div>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block">Status Verifikasi KYC:</span>
                    <?php if ($listing['provider_kyc'] === 'verified'): ?>
                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Terverifikasi (KTP & Bank Valid)</span>
                    <?php else: ?>
                        <span class="badge badge-warning text-dark px-2 py-1"><i class="fas fa-clock mr-1"></i> Belum Terverifikasi (Pending KYC)</span>
                    <?php endif; ?>
                </div>

                <div class="mb-3">
                    <span class="text-muted small d-block">WhatsApp:</span>
                    <a href="https://wa.me/<?= esc(preg_replace('/[^0-9]/', '', $listing['provider_whatsapp'] ?? '')) ?>" target="_blank" class="font-weight-bold text-teal text-decoration-none">
                        <i class="fab fa-whatsapp text-success mr-1"></i> <?= esc($listing['provider_whatsapp'] ?? '-') ?>
                    </a>
                </div>

                <div class="p-3 bg-light rounded text-secondary small">
                    <div class="font-weight-bold text-dark mb-1">Rekening Pencairan:</div>
                    <div>Bank: <?= esc($listing['bank_name'] ?? 'BSI') ?></div>
                    <div>No. Rek: <?= esc($listing['bank_account_number'] ?? '-') ?></div>
                </div>
            </div>
        </div>

        <!-- Property Location Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold text-dark m-0">
                    <i class="fas fa-map-location-dot text-teal mr-2"></i> Lokasi Properti
                </h5>
            </div>
            <div class="card-body">
                <div class="font-weight-bold text-dark mb-1"><?= esc($listing['property_name']) ?></div>
                <p class="text-muted small mb-2"><i class="fas fa-map-marker-alt text-danger mr-1"></i> <?= esc($listing['address']) ?></p>
                <div class="small text-secondary">
                    <div><strong>Wilayah:</strong> <?= esc($listing['region_name'] ?? 'Sleman, DIY') ?></div>
                    <div><strong>Koordinat:</strong> <?= esc($listing['latitude'] ?? '-') ?>, <?= esc($listing['longitude'] ?? '-') ?></div>
                </div>
            </div>
        </div>

        <!-- Audit Trail Card -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold text-dark m-0">
                    <i class="fas fa-clock-rotate-left text-teal mr-2"></i> Riwayat Audit Moderasi
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="timeline p-3 mb-0" style="font-size: 0.85rem;">
                    <?php if (empty($auditLogs)): ?>
                        <p class="text-muted small mb-0 p-2">Belum ada riwayat audit untuk listing ini.</p>
                    <?php else: ?>
                        <?php foreach ($auditLogs as $log): ?>
                            <div class="mb-3 border-bottom pb-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge badge-<?= strpos($log['action'], 'APPROVE') !== false ? 'success' : (strpos($log['action'], 'REJECT') !== false ? 'danger' : 'secondary') ?>">
                                        <?= esc($log['action']) ?>
                                    </span>
                                    <span class="text-muted" style="font-size: 0.75rem;"><?= date('d M Y H:i', strtotime($log['created_at'])) ?></span>
                                </div>
                                <div class="text-dark font-weight-bold mt-1">Oleh: <?= esc($log['actor_name'] ?? 'Admin') ?></div>
                                <?php if (!empty($log['reason'])): ?>
                                    <div class="text-secondary small mt-1 font-italic">"<?= esc($log['reason']) ?>"</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="<?= base_url('/admin/listings/reject/' . $listing['id']) ?>" method="post">
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
                    <p class="mb-2 text-dark font-weight-bold">Listing: <?= esc($listing['title']) ?></p>
                    <p class="small text-muted mb-3">Tuliskan alasan penolakan secara spesifik agar pemilik properti dapat melakukan perbaikan yang diperlukan.</p>
                    <div class="form-group">
                        <label class="font-weight-bold text-dark small">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="4" required placeholder="Contoh: Foto kamar mandi belum diunggah, deskripsi fasilitas belum lengkap, atau informasi harga tidak sesuai."></textarea>
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
<?= $this->endSection() ?>
