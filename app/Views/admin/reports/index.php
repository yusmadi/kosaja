<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-flag text-danger mr-2"></i> Laporan Pelanggaran &amp; Pengaduan
            </h1>
            <p class="text-muted small mb-0">Moderasi pengaduan ketidaksesuaian fasilitas, komplain lingkungan, dan pelanggaran tata tertib.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <span class="badge badge-secondary px-3 py-2">
                <i class="fas fa-location-dot mr-1"></i> Scope: <?= esc($scopedLabel) ?>
            </span>
        </div>
    </div>
</div>

<!-- Filter Tabs -->
<div class="row mb-3">
    <div class="col-12">
        <div class="btn-group">
            <a href="<?= base_url('/admin/reports') ?>" class="btn btn-sm <?= empty($statusFilter) ? 'btn-danger font-weight-bold' : 'btn-outline-secondary' ?>">
                Semua (<?= esc($counts['all']) ?>)
            </a>
            <a href="<?= base_url('/admin/reports?status=open') ?>" class="btn btn-sm <?= $statusFilter === 'open' ? 'btn-danger font-weight-bold' : 'btn-outline-secondary' ?>">
                <i class="fas fa-circle-exclamation mr-1"></i> Terbuka (<?= esc($counts['open']) ?>)
            </a>
            <a href="<?= base_url('/admin/reports?status=investigating') ?>" class="btn btn-sm <?= $statusFilter === 'investigating' ? 'btn-warning font-weight-bold' : 'btn-outline-secondary' ?>">
                <i class="fas fa-magnifying-glass mr-1"></i> Investigasi (<?= esc($counts['investigating']) ?>)
            </a>
            <a href="<?= base_url('/admin/reports?status=resolved') ?>" class="btn btn-sm <?= $statusFilter === 'resolved' ? 'btn-success font-weight-bold' : 'btn-outline-secondary' ?>">
                <i class="fas fa-check-circle mr-1"></i> Selesai (<?= esc($counts['resolved']) ?>)
            </a>
        </div>
    </div>
</div>

<!-- Reports Table -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <?php if (! empty($reports)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th>ID</th>
                        <th>Pelapor</th>
                        <th>Judul Pengaduan</th>
                        <th>Objek Terlapor</th>
                        <th>Wilayah</th>
                        <th>Status</th>
                        <th>Waktu Laporan</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reports as $rpt): ?>
                    <tr>
                        <td class="font-weight-bold text-dark">#RPT-<?= esc($rpt['id']) ?></td>
                        <td>
                            <div class="font-weight-bold text-dark"><?= esc($rpt['reporter_name']) ?></div>
                            <small class="text-muted"><?= esc($rpt['reporter_email']) ?></small>
                        </td>
                        <td>
                            <div class="font-weight-bold text-dark"><?= esc($rpt['title']) ?></div>
                            <small class="text-muted text-truncate d-block" style="max-width: 300px;"><?= esc($rpt['reason']) ?></small>
                        </td>
                        <td>
                            <span class="badge badge-light border text-uppercase font-weight-bold">
                                <?= esc($rpt['object_type']) ?> #<?= esc($rpt['object_id']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-secondary"><?= esc($rpt['region_name'] ?? 'Wilayah Sleman') ?></span>
                        </td>
                        <td>
                            <?php if ($rpt['status'] === 'open'): ?>
                                <span class="badge badge-danger">TERBUKA</span>
                            <?php elseif ($rpt['status'] === 'investigating'): ?>
                                <span class="badge badge-warning text-dark">INVESTIGASI</span>
                            <?php elseif ($rpt['status'] === 'resolved'): ?>
                                <span class="badge badge-success">SELESAI</span>
                            <?php else: ?>
                                <span class="badge badge-secondary"><?= strtoupper(esc($rpt['status'])) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted small">
                            <?= date('d M Y H:i', strtotime($rpt['created_at'])) ?>
                        </td>
                        <td class="text-right">
                            <a href="<?= base_url('/admin/reports/' . $rpt['id']) ?>" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-folder-open mr-1"></i> Periksa
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="p-5 text-center text-muted">
            <i class="fas fa-check-double fa-3x text-success mb-3"></i>
            <h5 class="font-weight-bold">Tidak Ada Laporan Pelanggaran</h5>
            <p class="mb-0">Semua listing dan properti di wilayah <?= esc($scopedLabel) ?> beroperasi sesuai standar kepatuhan.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
