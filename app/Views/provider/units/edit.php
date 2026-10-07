<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-pen text-teal mr-2"></i> Edit Kamar — <?= esc($unit['name']) ?>
            </h1>
            <p class="text-muted small mb-0">Properti: <strong><?= esc($unit['property_name']) ?></strong></p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/provider/properties/' . $unit['property_id']) ?>" class="btn btn-outline-secondary btn-sm">
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

<div class="row">
    <div class="col-lg-7">
        <div class="card card-outline card-teal shadow-sm mb-4">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-door-closed text-teal mr-2"></i> Perbarui Data Kamar
                </h3>
            </div>
            <form action="<?= base_url('/provider/units/update/' . $unit['id']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label font-weight-bold">Nama / Nomor Kamar <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control font-weight-bold" value="<?= esc(old('name', $unit['name'])) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="size" class="form-label font-weight-bold">Ukuran Kamar</label>
                            <input type="text" name="size" id="size" class="form-control" value="<?= esc(old('size', $unit['size'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="floor" class="form-label font-weight-bold">Posisi / Lantai</label>
                            <input type="text" name="floor" id="floor" class="form-control" value="<?= esc(old('floor', $unit['floor'] ?? '')) ?>">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="status" class="form-label font-weight-bold">Status Fisik Kamar <span class="text-danger">*</span></label>
                        <?php $currentStatus = old('status', $unit['status']); ?>
                        <select name="status" id="status" class="form-control" required>
                            <option value="available" <?= $currentStatus === 'available' ? 'selected' : '' ?>>Tersedia (Available) — Siap Disewa</option>
                            <option value="occupied" <?= $currentStatus === 'occupied' ? 'selected' : '' ?>>Terisi (Occupied) — Sedang Ada Penghuni</option>
                            <option value="maintenance" <?= $currentStatus === 'maintenance' ? 'selected' : '' ?>>Perawatan (Maintenance) — Renovasi / Cat</option>
                            <option value="reserved" <?= $currentStatus === 'reserved' ? 'selected' : '' ?>>Dipesan (Reserved)</option>
                            <option value="inactive" <?= $currentStatus === 'inactive' ? 'selected' : '' ?>>Nonaktif (Inactive)</option>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label for="editUnitPhotos" class="form-label font-weight-bold">Tambah Foto Ruangan Kamar</label>
                        <div class="custom-file mb-1">
                            <input type="file" class="custom-file-input" id="editUnitPhotos" name="photos[]" multiple accept="image/jpeg,image/png,image/webp">
                            <label class="custom-file-label" for="editUnitPhotos">Pilih foto tambahan...</label>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-top text-right">
                    <button type="submit" class="btn btn-teal text-white font-weight-bold px-4 shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Foto Unit Kamar yang Sudah Ada -->
    <div class="col-lg-5">
        <div class="card card-outline card-teal shadow-sm mb-4">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-images text-teal mr-2"></i> Foto Unit Saat Ini
                </h3>
            </div>
            <div class="card-body p-3">
                <?php if (!empty($unit['photos'])): ?>
                    <div class="row">
                        <?php foreach ($unit['photos'] as $p): ?>
                            <div class="col-6 mb-3">
                                <div class="position-relative border rounded overflow-hidden shadow-sm">
                                    <img src="<?= \App\Libraries\StorageService::getUrl($p['photo_url']) ?>" alt="Foto Kamar" class="w-100" style="height: 100px; object-fit: cover;">
                                    <?php if ($p['is_primary']): ?>
                                        <span class="badge badge-success position-absolute" style="top: 5px; left: 5px;">Sampul</span>
                                    <?php endif; ?>
                                    <div class="p-1 bg-white text-right">
                                        <form action="<?= base_url('/provider/units/photos/delete/' . $p['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus foto ini?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-xs btn-link p-0 text-danger" title="Hapus Foto">
                                                <i class="fas fa-trash"></i> Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-camera fa-2x mb-2 d-block"></i>
                        Belum ada foto kamar yang diunggah.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var fileInput = document.getElementById('editUnitPhotos');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                var count = e.target.files.length;
                var label = count > 1 ? count + ' foto dipilih' : (e.target.files[0] ? e.target.files[0].name : 'Pilih foto...');
                var nextSibling = e.target.nextElementSibling;
                if (nextSibling) {
                    nextSibling.innerText = label;
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
