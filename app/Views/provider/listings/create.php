<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-plus-circle text-teal mr-2"></i> Buat Listing Iklan Baru
            </h1>
            <p class="text-muted small mb-0">Publikasikan unit kamar fisik Anda ke etalase pencarian kos/kontrakan.</p>
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

<form action="<?= base_url('/provider/listings/store') ?>" method="post">
    <?= csrf_field() ?>
    <div class="row">
        <!-- Form Column -->
        <div class="col-lg-8">
            <div class="card card-outline card-teal shadow-sm mb-4">
                <div class="card-header">
                    <h3 class="card-title font-weight-bold">
                        <i class="fas fa-bullhorn text-teal mr-2"></i> Informasi Iklan Unit
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Unit Selector -->
                    <div class="form-group mb-3">
                        <label for="unit_id" class="form-label font-weight-bold">Pilih Unit Kamar yang Diiklankan <span class="text-danger">*</span></label>
                        <select name="unit_id" id="unit_id" class="form-control" required onchange="updateDefaultTitle(this)">
                            <option value="">-- Pilih Unit Kamar Fisik --</option>
                            <?php foreach ($unlistedUnits as $u): ?>
                                <?php $isSelected = (!empty($selectedUnit) && (int)$selectedUnit['id'] === (int)$u['id']) || old('unit_id') == $u['id']; ?>
                                <option value="<?= $u['id'] ?>" 
                                    data-property="<?= esc($u['property_name']) ?>" 
                                    data-unit="<?= esc($u['name']) ?>" 
                                    data-gender="<?= esc($u['target_gender']) ?>"
                                    <?= $isSelected ? 'selected' : '' ?>>
                                    [<?= esc($u['property_name']) ?>] — <?= esc($u['name']) ?> (<?= esc($u['target_gender']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">1 Unit Fisik mewakili 1 Individual Listing (sesuai spesifikasi sistem).</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="title" class="form-label font-weight-bold">Judul Iklan Listing <span class="text-danger">*</span></label>
                        <?php 
                        $defaultTitle = '';
                        if (!empty($selectedUnit)) {
                            $defaultTitle = $selectedUnit['name'] . ' di ' . $selectedUnit['property_name'];
                        }
                        ?>
                        <input type="text" name="title" id="title" class="form-control font-weight-bold" placeholder="Contoh: Kamar Nyaman Dekat Kampus AC & Kamar Mandi Dalam" value="<?= esc(old('title', $defaultTitle)) ?>" required>
                        <small class="text-muted">Judul yang menarik dan informatif meningkatkan klik pencari sewa.</small>
                    </div>

                    <div class="form-group mb-3">
                        <label for="description" class="form-label font-weight-bold">Deskripsi Lengkap Iklan <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="5" placeholder="Tuliskan spesifikasi kamar, perabotan yang tersedia, kenyamanan kamar, aturan tamu, dll..." required><?= old('description') ?></textarea>
                    </div>

                    <hr class="my-3">

                    <h5 class="font-weight-bold text-teal mb-3">
                        <i class="fas fa-tag mr-1"></i> Penentuan Harga Sewa
                    </h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="price_monthly" class="form-label font-weight-bold">Harga Sewa Bulanan (Rp)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">Rp</span>
                                </div>
                                <input type="number" name="price_monthly" id="price_monthly" class="form-control font-weight-bold" placeholder="Contoh: 1250000" value="<?= old('price_monthly') ?>" step="50000">
                            </div>
                            <small class="text-muted">Kosongkan jika hanya menerima sewa tahunan.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="price_yearly" class="form-label font-weight-bold">Harga Sewa Tahunan (Rp)</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text font-weight-bold">Rp</span>
                                </div>
                                <input type="number" name="price_yearly" id="price_yearly" class="form-control font-weight-bold" placeholder="Contoh: 14000000" value="<?= old('price_yearly') ?>" step="100000">
                            </div>
                            <small class="text-muted">Biasanya menawarkan diskon untuk sewa 1 tahun penuh.</small>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center">
                    <button type="submit" name="action" value="draft" class="btn btn-light border font-weight-bold text-secondary">
                        <i class="fas fa-file mr-1"></i> Simpan sebagai Draf
                    </button>
                    <button type="submit" name="action" value="submit_review" class="btn btn-teal text-white font-weight-bold px-4 shadow-sm">
                        <i class="fas fa-paper-plane mr-1"></i> Ajukan Review Admin
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Info Column -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4 bg-light">
                <div class="card-body text-sm">
                    <h6 class="font-weight-bold text-dark mb-2">
                        <i class="fas fa-shield-halved text-teal mr-1"></i> Proses Peninjauan Listing
                    </h6>
                    <p class="text-muted mb-3">
                        Listing yang diajukan ke <strong>Admin Review</strong> akan diverifikasi oleh Admin Daerah dalam 1x24 jam untuk memastikan kepatuhan syariat hunian dan foto yang layak sebelum tayang secara publik di hasil pencarian.
                    </p>
                    <div class="p-3 bg-white rounded border">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-secondary mr-2">Draf</span>
                            <small class="text-muted">Hanya dapat dilihat oleh Anda.</small>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge badge-warning mr-2">Review</span>
                            <small class="text-muted">Sedang ditinjau oleh Admin.</small>
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="badge badge-success mr-2">Tayang</span>
                            <small class="text-muted">Aktif dicari oleh calon penyewa.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<?= $this->section('scripts') ?>
<script>
    function updateDefaultTitle(select) {
        var opt = select.options[select.selectedIndex];
        if (opt && opt.value) {
            var unit = opt.getAttribute('data-unit');
            var prop = opt.getAttribute('data-property');
            var titleInput = document.getElementById('title');
            if (titleInput && (!titleInput.value || titleInput.value.trim() === '')) {
                titleInput.value = unit + ' — ' + prop;
            }
        }
    }
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
