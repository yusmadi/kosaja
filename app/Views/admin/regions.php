<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-map-location-dot text-teal mr-2"></i> Manajemen Wilayah Operasional
            </h1>
            <p class="text-muted small mb-0">Kelola hierarki wilayah KosAja (Provinsi &rarr; Kota/Kabupaten &rarr; Klaster/Kecamatan Operasional).</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <button class="btn btn-teal text-white btn-sm shadow-sm" data-toggle="modal" data-target="#modalAddRegion">
                <i class="fas fa-plus mr-1"></i> Tambah Wilayah Baru
            </button>
        </div>
    </div>
</div>

<!-- Alerts -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show text-sm" role="alert">
        <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger text-sm">
        <ul class="mb-0 pl-3">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-teal shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-layer-group text-teal mr-2"></i> Daftar Hierarki Wilayah
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-sm m-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 60px;">ID</th>
                                <th>Nama Wilayah</th>
                                <th>Tingkat (Level)</th>
                                <th>Induk (Parent Region)</th>
                                <th style="width: 120px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($regions)): ?>
                                <?php foreach ($regions as $r): ?>
                                    <tr>
                                        <td><code>#<?= esc($r['id']) ?></code></td>
                                        <td class="font-weight-bold">
                                            <?php if ($r['level'] === 'province'): ?>
                                                <i class="fas fa-landmark text-primary mr-2"></i> <?= esc($r['name']) ?>
                                            <?php elseif ($r['level'] === 'city'): ?>
                                                <span class="ml-3"><i class="fas fa-city text-info mr-2"></i> <?= esc($r['name']) ?></span>
                                            <?php else: ?>
                                                <span class="ml-5"><i class="fas fa-location-dot text-danger mr-2"></i> <?= esc($r['name']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($r['level'] === 'province'): ?>
                                                <span class="badge badge-primary text-uppercase">PROVINSI</span>
                                            <?php elseif ($r['level'] === 'city'): ?>
                                                <span class="badge badge-info text-uppercase">KOTA / KABUPATEN</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning text-uppercase">OPERASIONAL (KLASTER/KEC)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?= !empty($r['parent_name']) ? esc($r['parent_name']) : '<span class="text-muted font-italic">- Pusat -</span>' ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="<?= base_url('/admin/regions/delete/' . $r['id']) ?>" 
                                               class="btn btn-xs btn-outline-danger" 
                                               onclick="return confirm('Apakah Anda yakin ingin menghapus wilayah ini?')">
                                                <i class="fas fa-trash-alt"></i> Hapus
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Belum ada data wilayah.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Region -->
<div class="modal fade" id="modalAddRegion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= base_url('/admin/regions/create') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header bg-teal text-white">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-2"></i> Tambah Wilayah Operasional</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body text-sm">
                    <div class="form-group">
                        <label for="name" class="font-weight-bold">Nama Wilayah <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Contoh: Depok (Sleman) atau Kulon Progo" required>
                    </div>
                    <div class="form-group">
                        <label for="level" class="font-weight-bold">Tingkat Wilayah <span class="text-danger">*</span></label>
                        <select name="level" id="level" class="form-control" required>
                            <option value="operational">Operasional (Klaster Kampus / Area)</option>
                            <option value="city">Kota / Kabupaten</option>
                            <option value="province">Provinsi</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="parent_id" class="font-weight-bold">Wilayah Induk (Parent)</label>
                        <select name="parent_id" id="parent_id" class="form-control">
                            <option value="">-- Tidak Ada (Pusat) --</option>
                            <?php foreach ($provincesAndCities as $pc): ?>
                                <option value="<?= esc($pc['id']) ?>"><?= esc($pc['name']) ?> (<?= esc($pc['level']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Pilih Provinsi untuk Kota/Kabupaten, atau Kota/Kabupaten untuk Klaster Operasional.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-teal text-white btn-sm"><i class="fas fa-save mr-1"></i> Simpan Wilayah</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
