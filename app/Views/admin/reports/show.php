<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-flag text-danger mr-2"></i> Detail Laporan #RPT-<?= esc($report['id']) ?>
            </h1>
            <p class="text-muted small mb-0">Tinjau isi pengaduan, konfirmasi bukti, dan ambil tindakan administratif.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/admin/reports') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Antrean
            </a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Report Details & Evidence -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-circle-info text-primary mr-2"></i> Informasi Pengaduan
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h5 class="font-weight-bold text-danger"><?= esc($report['title']) ?></h5>
                    <p class="text-muted" style="white-space: pre-line;"><?= esc($report['reason']) ?></p>
                </div>

                <hr>

                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label class="text-muted small text-uppercase font-weight-bold">Pelapor (Tenant/Warga):</label>
                        <div class="font-weight-bold text-dark"><?= esc($reporter['name'] ?? 'Penyewa Terdaftar') ?></div>
                        <div class="small text-muted"><?= esc($reporter['email'] ?? '-') ?> | WA: <?= esc($reporter['whatsapp'] ?? '-') ?></div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="text-muted small text-uppercase font-weight-bold">Objek Dilaporkan:</label>
                        <div>
                            <span class="badge badge-primary text-uppercase font-weight-bold px-2 py-1">
                                <?= esc($report['object_type']) ?> #<?= esc($report['object_id']) ?>
                            </span>
                        </div>
                        <?php if ($objectDetail): ?>
                            <div class="small font-weight-bold text-dark mt-1"><?= esc($objectDetail['title'] ?? $objectDetail['name'] ?? '-') ?></div>
                            <div class="small text-muted">Pemilik: <?= esc($objectDetail['provider_name'] ?? '-') ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (! empty($report['admin_notes'])): ?>
                <div class="alert alert-light border mt-2">
                    <strong class="text-dark"><i class="fas fa-note-sticky text-warning mr-1"></i> Catatan Tindakan Admin:</strong>
                    <p class="mb-0 mt-1 text-muted"><?= esc($report['admin_notes']) ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Status & Administrative Actions -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-gavel text-danger mr-2"></i> Tindakan Administratif
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted small text-uppercase font-weight-bold">Status Saat Ini:</label>
                    <div>
                        <?php if ($report['status'] === 'open'): ?>
                            <span class="badge badge-danger px-3 py-2 font-weight-bold">TERBUKA (MENUNGGU TINDAKAN)</span>
                        <?php elseif ($report['status'] === 'investigating'): ?>
                            <span class="badge badge-warning text-dark px-3 py-2 font-weight-bold">DALAM INVESTIGASI</span>
                        <?php elseif ($report['status'] === 'resolved'): ?>
                            <span class="badge badge-success px-3 py-2 font-weight-bold">SELESAI DITANGANI</span>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($report['status'] === 'open'): ?>
                <form action="<?= base_url('/admin/reports/investigate/' . $report['id']) ?>" method="post" class="mb-3">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-warning btn-block font-weight-bold text-dark">
                        <i class="fas fa-magnifying-glass mr-1"></i> Mulai Investigasi Laporan
                    </button>
                </form>
                <?php endif; ?>

                <?php if ($report['status'] !== 'resolved'): ?>
                <form action="<?= base_url('/admin/reports/resolve/' . $report['id']) ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="form-group">
                        <label class="font-weight-bold small">Catatan Penyelesaian (Wajib):</label>
                        <textarea name="admin_notes" class="form-control" rows="3" placeholder="Tuliskan tindakan yang diambil (misal: teguran ke pemilik, pembaruan fasilitas, dsb)..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-success btn-block font-weight-bold">
                        <i class="fas fa-check-circle mr-1"></i> Selesaikan Laporan
                    </button>
                </form>

                <div class="border-top mt-3 pt-3">
                    <a href="<?= base_url('/admin/restrictions') ?>" class="btn btn-outline-danger btn-block btn-sm">
                        <i class="fas fa-ban mr-1"></i> Kenakan Sanksi/Restriksi Pemilik
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
