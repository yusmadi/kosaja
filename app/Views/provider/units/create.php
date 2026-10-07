<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-plus-circle text-teal mr-2"></i> Tambah Kamar/Unit Baru
            </h1>
            <p class="text-muted small mb-0">Menambahkan kamar fisik ke dalam properti: <strong><?= esc($property['name']) ?></strong></p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/provider/properties/' . $property['id']) ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Properti
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

<form action="<?= base_url('/provider/properties/' . $property['id'] . '/units/store') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <div class="col-lg-7">
            <div class="card card-outline card-teal shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-door-closed text-teal mr-2"></i> Detail Kamar Fisik
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label font-weight-bold">Nama / Nomor Kamar <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control font-weight-bold" placeholder="Contoh: Kamar A-01, Paviliun Mawar 2" value="<?= old('name') ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="size" class="form-label font-weight-bold">Ukuran Kamar</label>
                            <input type="text" name="size" id="size" class="form-control" placeholder="Contoh: 3 x 4 m" value="<?= old('size') ?>">
                            <small class="text-muted">Dimensi fisik ruangan kamar.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="floor" class="form-label font-weight-bold">Posisi / Lantai</label>
                            <input type="text" name="floor" id="floor" class="form-control" placeholder="Contoh: Lantai 1, Lantai 2" value="<?= old('floor', 'Lantai 1') ?>">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="status" class="form-label font-weight-bold">Status Fisik Awal <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="available" <?= old('status', 'available') === 'available' ? 'selected' : '' ?>>Tersedia (Available) — Siap Ditempati</option>
                            <option value="occupied" <?= old('status') === 'occupied' ? 'selected' : '' ?>>Terisi (Occupied) — Sedang Ditempati</option>
                            <option value="maintenance" <?= old('status') === 'maintenance' ? 'selected' : '' ?>>Perawatan (Maintenance) — Renovasi / Cat</option>
                            <option value="reserved" <?= old('status') === 'reserved' ? 'selected' : '' ?>>Dipesan (Reserved)</option>
                            <option value="inactive" <?= old('status') === 'inactive' ? 'selected' : '' ?>>Nonaktif (Inactive)</option>
                        </select>
                    </div>

                    <div class="form-group mb-0">
                        <label for="unitPhotos" class="form-label font-weight-bold">Foto Ruangan Kamar</label>
                        <div class="custom-file mb-1">
                            <input type="file" class="custom-file-input" id="unitPhotos" name="photos[]" multiple accept="image/jpeg,image/png,image/webp">
                            <label class="custom-file-label" for="unitPhotos">Pilih foto kamar (bisa lebih dari 1)...</label>
                        </div>
                        <small class="text-muted">Foto tempat tidur, kamar mandi dalam (jika ada), meja belajar, dll.</small>
                    </div>
                </div>
                <div class="card-footer bg-white border-top text-right">
                    <button type="submit" class="btn btn-teal text-white font-weight-bold px-4 shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Kamar
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4 bg-light">
                <div class="card-body">
                    <h6 class="font-weight-bold text-dark mb-2">Properti Terpilih:</h6>
                    <div class="p-3 bg-white rounded border mb-3">
                        <h5 class="font-weight-bold text-teal mb-1"><?= esc($property['name']) ?></h5>
                        <span class="badge badge-<?= $property['target_gender'] === 'putri' ? 'danger' : ($property['target_gender'] === 'putra' ? 'primary' : 'purple') ?> mb-2">
                            <?= esc($property['target_gender']) ?>
                        </span>
                        <p class="text-xs text-muted mb-0"><?= esc($property['address']) ?></p>
                    </div>
                    <p class="text-xs text-muted">
                        Setelah unit tersimpan, unit ini akan langsung tercatat di inventaris dan Anda dapat membuat listing iklan komersial dari unit ini.
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var fileInput = document.getElementById('unitPhotos');
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
