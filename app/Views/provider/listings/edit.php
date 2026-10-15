<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-pen text-teal mr-2"></i> Edit Listing Iklan
            </h1>
            <p class="text-muted small mb-0">Unit: <strong><?= esc($listing['unit_name']) ?></strong> (<?= esc($listing['property_name']) ?>)</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/provider/listings') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Batal & Kembali
            </a>
        </div>
    </div>
</div>

<!-- Errors alert -->
<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <strong><i class="fas fa-exclamation-triangle mr-2"></i> Terjadi kesalahan input:</strong>
        <ul class="mb-0 mt-1 pl-3">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Rejection Banner if rejected -->
<?php if (!empty($listing['rejection_reason'])): ?>
    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4">
        <i class="fas fa-exclamation-circle fa-2x mr-3 text-danger"></i>
        <div>
            <h6 class="font-weight-bold mb-1">Listing Perlu Direvisi Sebelum Dapat Tayang</h6>
            <p class="mb-0 text-sm">
                Alasan dari Admin: "<strong><?= esc($listing['rejection_reason']) ?></strong>".
                Silakan perbaiki data di bawah ini, lalu klik <strong>Kirim Ulang untuk Review</strong>.
            </p>
        </div>
    </div>
<?php endif; ?>

<form action="<?= base_url('/provider/listings/update/' . $listing['id']) ?>" method="post">
    <?= csrf_field() ?>
    <div class="row">
        <!-- Form Column -->
        <div class="col-lg-8">
            <div class="card card-outline card-teal shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-bullhorn text-teal mr-2"></i> Detail Iklan Listing
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="title" class="form-label font-weight-bold">Judul Iklan Listing <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control font-weight-bold" value="<?= esc(old('title', $listing['title'])) ?>" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="description" class="form-label font-weight-bold">Deskripsi Lengkap Iklan <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="6" required><?= esc(old('description', $listing['description'])) ?></textarea>
                    </div>

                    <hr class="my-3">

                    <h5 class="font-weight-bold text-teal mb-3">
                        <i class="fas fa-tag mr-1"></i> Harga Sewa
                    </h5>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="price_monthly" class="form-label font-weight-bold">Harga Sewa Bulanan (Rp)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">Rp</span>
                                </div>
                                <input type="number" name="price_monthly" id="price_monthly" class="form-control font-weight-bold" value="<?= esc(old('price_monthly', $listing['price_monthly'] ?? '')) ?>" step="50000">
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="price_six_monthly" class="form-label font-weight-bold">Harga Sewa Enam Bulan (Rp)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">Rp</span>
                                </div>
                                <input type="number" name="price_six_monthly" id="price_six_monthly" class="form-control font-weight-bold" value="<?= esc(old('price_six_monthly', $listing['price_six_monthly'] ?? '')) ?>" step="50000">
                            </div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="price_yearly" class="form-label font-weight-bold">Harga Sewa Tahunan (Rp)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">Rp</span>
                                </div>
                                <input type="number" name="price_yearly" id="price_yearly" class="form-control font-weight-bold" value="<?= esc(old('price_yearly', $listing['price_yearly'] ?? '')) ?>" step="100000">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
                    <button type="submit" name="action" value="save" class="btn btn-teal text-white font-weight-bold px-4 shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>

                    <?php if ($listing['publication_status'] === 'draft' || !empty($listing['rejection_reason'])): ?>
                        <button type="submit" name="action" value="submit_review" class="btn btn-warning text-dark font-weight-bold px-3 shadow-sm">
                            <i class="fas fa-paper-plane mr-1"></i> Kirim Ulang untuk Review
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Info Column -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 bg-light">
                <div class="card-body text-sm">
                    <h6 class="font-weight-bold text-dark mb-2">Informasi Kamar & Status</h6>
                    <div class="p-3 bg-white rounded border mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Status Publikasi:</span>
                            <span class="badge badge-<?= $listing['publication_status'] === 'published' ? 'success' : ($listing['publication_status'] === 'pending_review' ? 'warning' : 'secondary') ?>">
                                <?= ucfirst($listing['publication_status']) ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Target Gender:</span>
                            <span class="badge badge-<?= $listing['target_gender'] === 'putri' ? 'danger' : ($listing['target_gender'] === 'putra' ? 'primary' : 'purple') ?>">
                                <?= esc($listing['target_gender']) ?>
                            </span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Ukuran Kamar:</span>
                            <strong><?= esc($listing['unit_size'] ?? '-') ?></strong>
                        </div>
                    </div>

                    <p class="text-muted text-xs mb-0">
                        Pastikan detail harga sewa dan deskripsi sesuai dengan kondisi fisik unit agar penyewa puas dan tidak terjadi komplain saat serah terima kunci.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
