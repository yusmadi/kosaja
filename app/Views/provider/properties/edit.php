<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-pen text-teal mr-2"></i> Edit Properti — <?= esc($property['name']) ?>
            </h1>
            <p class="text-muted small mb-0">Perbarui data informasi gedung, fasilitas umum, dan foto properti.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/provider/properties/' . $property['id']) ?>" class="btn btn-outline-secondary btn-sm">
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

<form action="<?= base_url('/provider/properties/update/' . $property['id']) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="row">
        <!-- Main Form Column -->
        <div class="col-lg-8">
            <div class="card card-outline card-teal shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-info-circle text-teal mr-2"></i> Informasi Bangunan Properti
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label for="name" class="form-label font-weight-bold">Nama Properti <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" value="<?= esc(old('name', $property['name'])) ?>" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="type" class="form-label font-weight-bold">Kategori Bangunan <span class="text-danger">*</span></label>
                            <select name="type" id="type" class="form-control" required>
                                <?php $currentType = old('type', $property['type']); ?>
                                <option value="kos" <?= $currentType === 'kos' ? 'selected' : '' ?>>Rumah Kos / Kamar Kos</option>
                                <option value="kontrakan" <?= $currentType === 'kontrakan' ? 'selected' : '' ?>>Rumah Kontrakan</option>
                                <option value="rumah_sewa" <?= $currentType === 'rumah_sewa' ? 'selected' : '' ?>>Rumah Sewa Utuh</option>
                                <option value="lainnya" <?= $currentType === 'lainnya' ? 'selected' : '' ?>>Paviliun / Lainnya</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="region_id" class="form-label font-weight-bold">Wilayah Operasional <span class="text-danger">*</span></label>
                            <select name="region_id" id="region_id" class="form-control" required>
                                <?php $currentRegion = old('region_id', $property['region_id']); ?>
                                <?php foreach ($regions as $r): ?>
                                    <option value="<?= $r['id'] ?>" <?= $currentRegion == $r['id'] ? 'selected' : '' ?>>
                                        <?= !empty($r['depth']) ? '&nbsp;&nbsp;↳ ' : '' ?><?= esc($r['name']) ?> (<?= ucfirst($r['level']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-lock text-teal mr-1"></i> Terikat pada wilayah operasional akun: <strong><?= esc($providerMainRegion['name'] ?? 'Wilayah Terdaftar') ?></strong>
                            </small>
                        </div>
                    </div>

                    <!-- Target Gender Syariat -->
                    <div class="form-group mb-4">
                        <label class="form-label font-weight-bold d-block">
                            Ketentuan Syariat Hunian (Target Penghuni) <span class="text-danger">*</span>
                        </label>
                        <?php $currentGender = old('target_gender', $property['target_gender']); ?>
                        <div class="row">
                            <div class="col-md-4 mb-2">
                                <label class="border rounded p-3 d-block text-center cursor-pointer shadow-sm h-100 bg-white" style="cursor: pointer;">
                                    <input type="radio" name="target_gender" value="putri" class="mr-1" <?= $currentGender === 'putri' ? 'checked' : '' ?> required>
                                    <span class="badge badge-danger d-block my-2 py-2"><i class="fas fa-female fa-lg mr-1"></i> Khusus Putri</span>
                                    <small class="text-muted d-block">Hunian khusus perempuan.</small>
                                </label>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="border rounded p-3 d-block text-center cursor-pointer shadow-sm h-100 bg-white" style="cursor: pointer;">
                                    <input type="radio" name="target_gender" value="putra" class="mr-1" <?= $currentGender === 'putra' ? 'checked' : '' ?>>
                                    <span class="badge badge-primary d-block my-2 py-2"><i class="fas fa-male fa-lg mr-1"></i> Khusus Putra</span>
                                    <small class="text-muted d-block">Hunian khusus laki-laki.</small>
                                </label>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="border rounded p-3 d-block text-center cursor-pointer shadow-sm h-100 bg-white" style="cursor: pointer;">
                                    <input type="radio" name="target_gender" value="campur_keluarga" class="mr-1" <?= $currentGender === 'campur_keluarga' ? 'checked' : '' ?>>
                                    <span class="badge badge-purple d-block my-2 py-2 text-white" style="background-color: #7c3aed;"><i class="fas fa-people-roof fa-lg mr-1"></i> Pasutri & Keluarga</span>
                                    <small class="text-muted d-block">Wajib Buku Nikah / KK sah.</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="address" class="form-label font-weight-bold">Alamat Lengkap <span class="text-danger">*</span></label>
                        <textarea name="address" id="address" class="form-control" rows="3" required><?= esc(old('address', $property['address'])) ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="latitude" class="form-label font-weight-bold">Latitude (Opsional)</label>
                            <input type="text" name="latitude" id="latitude" class="form-control" value="<?= esc(old('latitude', $property['latitude'] ?? '')) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="longitude" class="form-label font-weight-bold">Longitude (Opsional)</label>
                            <input type="text" name="longitude" id="longitude" class="form-control" value="<?= esc(old('longitude', $property['longitude'] ?? '')) ?>">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label for="description" class="form-label font-weight-bold">Deskripsi Umum Properti</label>
                        <textarea name="description" id="description" class="form-control" rows="4"><?= esc(old('description', $property['description'] ?? '')) ?></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label for="rules" class="form-label font-weight-bold">Peraturan & Tata Tertib Properti</label>
                        <textarea name="rules" id="rules" class="form-control" rows="3"><?= esc(old('rules', $property['rules'] ?? '')) ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Fasilitas Umum Properti -->
            <div class="card card-outline card-teal shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-list-check text-teal mr-2"></i> Fasilitas Umum Bangunan
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($facilityPresets as $preset): ?>
                            <div class="col-md-6 mb-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="facilities[]" value="<?= esc($preset) ?>" class="custom-control-input" id="fac_<?= md5($preset) ?>" <?= in_array($preset, $currentFacilityNames) ? 'checked' : '' ?>>
                                    <label class="custom-control-label" for="fac_<?= md5($preset) ?>"><?= esc($preset) ?></label>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="mt-3">
                        <label for="custom_facilities" class="form-label font-weight-bold text-sm">Fasilitas Lainnya (Pisahkan dengan koma)</label>
                        <input type="text" name="custom_facilities" id="custom_facilities" class="form-control" placeholder="Contoh: Gazebo Santai, Dispenser">
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column: Tambah Foto & Submit -->
        <div class="col-lg-4">
            <div class="card card-outline card-teal shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-images text-teal mr-2"></i> Tambah Foto Baru
                    </h3>
                </div>
                <div class="card-body">
                    <div class="form-group mb-0">
                        <div class="custom-file mb-2">
                            <input type="file" class="custom-file-input" id="editPropertyPhotos" name="photos[]" multiple accept="image/jpeg,image/png,image/webp">
                            <label class="custom-file-label" for="editPropertyPhotos">Pilih foto tambahan...</label>
                        </div>
                        <small class="text-muted d-block">Foto yang diunggah akan ditambahkan ke galeri properti.</small>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4 bg-light">
                <div class="card-body">
                    <button type="submit" class="btn btn-teal text-white btn-block font-weight-bold shadow-sm py-2">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                    <a href="<?= base_url('/provider/properties/' . $property['id']) ?>" class="btn btn-outline-secondary btn-block mt-2">
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var fileInput = document.getElementById('editPropertyPhotos');
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
