<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-ban text-danger mr-2"></i> Restriksi &amp; Sanksi Regional
            </h1>
            <p class="text-muted small mb-0">Penangguhan akun mitra dan penonaktifan iklan listing dengan alasan resmi sesuai PRD 5.3.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <span class="badge badge-secondary px-3 py-2">
                <i class="fas fa-location-dot mr-1"></i> Scope: <?= esc($scopedLabel) ?>
            </span>
        </div>
    </div>
</div>

<div class="row">
    <!-- Form: Apply Restriction -->
    <div class="col-lg-4 mb-3">
        <div class="card shadow-sm border-0 border-left border-danger" style="border-left: 4px solid #dc2626 !important;">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-gavel text-danger mr-2"></i> Terapkan Sanksi / Restriksi
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/admin/restrictions/apply') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label class="font-weight-bold small">Jenis Objek:</label>
                        <select name="target_type" id="targetTypeSelect" class="form-control" required onchange="updateTargetOptions()">
                            <option value="provider">Mitra Provider (Akun Pemilik)</option>
                            <option value="listing">Listing Iklan (Unit Kamar)</option>
                        </select>
                    </div>

                    <div class="form-group" id="providerSelectGroup">
                        <label class="font-weight-bold small">Pilih Mitra Provider:</label>
                        <select name="target_id" class="form-control" id="providerSelect">
                            <?php foreach ($providers as $prov): ?>
                                <option value="<?= $prov['id'] ?>">
                                    <?= esc($prov['name']) ?> (Status: <?= esc($prov['verification_status'] ?? 'pending') ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group d-none" id="listingSelectGroup">
                        <label class="font-weight-bold small">Pilih Listing Iklan:</label>
                        <select name="target_id" class="form-control" id="listingSelect" disabled>
                            <?php foreach ($listings as $lst): ?>
                                <option value="<?= $lst['id'] ?>">
                                    #<?= $lst['id'] ?> - <?= esc($lst['title']) ?> (<?= esc($lst['property_name']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold small">Alasan Resmi Sanksi (Wajib):</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="Contoh: Terbukti melanggar aturan gender Syariat / Iklan palsu berulang kali..." required></textarea>
                    </div>

                    <button type="submit" class="btn btn-danger btn-block font-weight-bold">
                        <i class="fas fa-ban mr-1"></i> Terapkan Restriksi
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Tables: List of Providers & Listings with Status -->
    <div class="col-lg-8">
        <!-- Providers Status Table -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-user-shield text-warning mr-2"></i> Status Pengawasan Mitra Provider
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th>Nama Mitra</th>
                                <th>Email / Kontak</th>
                                <th>Status KYC</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($providers as $p): ?>
                            <tr>
                                <td class="font-weight-bold text-dark"><?= esc($p['name']) ?></td>
                                <td class="small text-muted"><?= esc($p['email']) ?> | <?= esc($p['whatsapp'] ?? '-') ?></td>
                                <td>
                                    <?php if (($p['verification_status'] ?? '') === 'suspended'): ?>
                                        <span class="badge badge-danger">SUSPENDED / DISANKSI</span>
                                        <?php if (! empty($p['rejection_reason'])): ?>
                                            <small class="d-block text-danger font-italic"><?= esc($p['rejection_reason']) ?></small>
                                        <?php endif; ?>
                                    <?php elseif (($p['verification_status'] ?? '') === 'verified'): ?>
                                        <span class="badge badge-success">TERVERIFIKASI AKTIF</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= strtoupper(esc($p['verification_status'] ?? 'PENDING')) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <?php if (($p['verification_status'] ?? '') === 'suspended'): ?>
                                        <a href="<?= base_url('/admin/restrictions/lift/' . $p['id'] . '?type=provider') ?>" class="btn btn-xs btn-outline-success font-weight-bold" onclick="return confirm('Cabut sanksi untuk mitra ini?')">
                                            <i class="fas fa-unlock mr-1"></i> Cabut Sanksi
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Normal</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Listings Status Table -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-bullhorn text-primary mr-2"></i> Status Listing Iklan di Wilayah
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th>Judul Listing</th>
                                <th>Properti / Pemilik</th>
                                <th>Status Publikasi</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listings as $l): ?>
                            <tr>
                                <td class="font-weight-bold text-dark">
                                    #<?= $l['id'] ?> - <?= esc($l['title']) ?>
                                </td>
                                <td>
                                    <div class="small font-weight-bold text-dark"><?= esc($l['property_name']) ?></div>
                                    <small class="text-muted">Mitra: <?= esc($l['provider_name']) ?></small>
                                </td>
                                <td>
                                    <?php if ($l['publication_status'] === 'inactive'): ?>
                                        <span class="badge badge-danger">NONAKTIF (INACTIVE)</span>
                                    <?php elseif ($l['publication_status'] === 'published'): ?>
                                        <span class="badge badge-success">TERBIT (PUBLISHED)</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning text-dark"><?= strtoupper($l['publication_status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <?php if ($l['publication_status'] === 'inactive'): ?>
                                        <a href="<?= base_url('/admin/restrictions/lift/' . $l['id'] . '?type=listing') ?>" class="btn btn-xs btn-outline-success font-weight-bold" onclick="return confirm('Aktifkan kembali listing ini?')">
                                            <i class="fas fa-rotate-left mr-1"></i> Pulihkan
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">Normal</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updateTargetOptions() {
    var select = document.getElementById('targetTypeSelect').value;
    var provGroup = document.getElementById('providerSelectGroup');
    var listGroup = document.getElementById('listingSelectGroup');
    var provSelect = document.getElementById('providerSelect');
    var listSelect = document.getElementById('listingSelect');

    if (select === 'provider') {
        provGroup.classList.remove('d-none');
        provSelect.disabled = false;
        listGroup.classList.add('d-none');
        listSelect.disabled = true;
    } else {
        provGroup.classList.add('d-none');
        provSelect.disabled = true;
        listGroup.classList.remove('d-none');
        listSelect.disabled = false;
    }
}
</script>
<?= $this->endSection() ?>
