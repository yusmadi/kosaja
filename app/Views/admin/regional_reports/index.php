<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-chart-line text-info mr-2"></i> Laporan &amp; Kinerja Regional
            </h1>
            <p class="text-muted small mb-0">Statistik okupansi hunian, efektivitas moderasi iklan, dan resolusi sengketa di wilayah tugas.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <span class="badge badge-secondary px-3 py-2">
                <i class="fas fa-location-dot mr-1"></i> Scope: <?= esc($scopedLabel) ?>
            </span>
        </div>
    </div>
</div>

<!-- Regional Summary Cards -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm border-0">
            <span class="info-box-icon bg-success text-white"><i class="fas fa-percent"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Tingkat Okupansi</span>
                <span class="info-box-number text-success font-weight-bold" style="font-size: 1.5rem;">
                    <?= esc($stats['occupancy_rate']) ?>%
                </span>
                <small class="text-muted"><?= esc($stats['occupied_units']) ?> dari <?= esc($stats['total_units']) ?> unit terisi</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm border-0">
            <span class="info-box-icon bg-info text-white"><i class="fas fa-door-open"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Kamar Siap Sewa</span>
                <span class="info-box-number text-info font-weight-bold" style="font-size: 1.5rem;">
                    <?= esc($stats['available_units']) ?>
                </span>
                <small class="text-muted">Unit kamar kosong/tersedia</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm border-0">
            <span class="info-box-icon bg-primary text-white"><i class="fas fa-bullhorn"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Listing Terpublikasi</span>
                <span class="info-box-number text-primary font-weight-bold" style="font-size: 1.5rem;">
                    <?= esc($stats['published_listings']) ?>
                </span>
                <small class="text-muted"><?= esc($stats['pending_listings']) ?> antrean moderasi</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm border-0">
            <span class="info-box-icon bg-purple text-white" style="background-color: #8b5cf6 !important;"><i class="fas fa-scale-balanced"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Penyelesaian Sengketa</span>
                <span class="info-box-number font-weight-bold" style="font-size: 1.5rem; color: #8b5cf6;">
                    <?= esc($stats['resolved_disputes']) ?> Selesai
                </span>
                <small class="text-muted"><?= esc($stats['open_disputes']) ?> kasus aktif</small>
            </div>
        </div>
    </div>
</div>

<!-- Cluster & Operational Distribution Table -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-network-wired text-primary mr-2"></i> Distribusi Operasional per Klaster Wilayah
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th>Nama Wilayah / Klaster</th>
                                <th>Hierarki Tingkatan</th>
                                <th>Jumlah Gedung Properti</th>
                                <th>Total Unit Kamar</th>
                                <th>Status Layanan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($clusterStats as $cs): ?>
                            <tr>
                                <td class="font-weight-bold text-dark">
                                    <i class="fas fa-map-pin text-danger mr-2"></i> <?= esc($cs['name']) ?>
                                </td>
                                <td>
                                    <span class="badge badge-light border text-uppercase"><?= esc($cs['level']) ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-info px-2 py-1"><?= esc($cs['properties']) ?> Gedung</span>
                                </td>
                                <td>
                                    <span class="badge badge-secondary px-2 py-1"><?= esc($cs['units']) ?> Kamar Fisik</span>
                                </td>
                                <td>
                                    <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Aktif Terlayani</span>
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
<?= $this->endSection() ?>
