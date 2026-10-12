<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-shield-halved text-danger mr-2"></i> Dasbor Backoffice Administrator
            </h1>
            <p class="text-muted small mb-0">Kendali terpusat moderasi properti, verifikasi penyedia, dan audit log KosAja.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <span class="badge badge-success px-3 py-2"><i class="fas fa-circle mr-1" style="font-size: 8px;"></i> Sistem Operasional Normal</span>
        </div>
    </div>
</div>

<!-- Info boxes -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Pengguna</span>
                <span class="info-box-number"><?= esc($stats['total_users'] ?? 5) ?> <small>Akun</small></span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-warning elevation-1"><i class="fas fa-user-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Mitra Penyedia</span>
                <span class="info-box-number"><?= esc($stats['total_providers'] ?? 1) ?> <small>Aktif</small></span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-success elevation-1"><i class="fas fa-building"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Properti</span>
                <span class="info-box-number"><?= esc($stats['total_properties'] ?? 3) ?> <small>Lokasi</small></span>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-bullhorn"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Listing Terpublikasi</span>
                <span class="info-box-number"><?= esc($stats['published_listings'] ?? 3) ?> <small>Kamar/Unit</small></span>
            </div>
        </div>
    </div>
</div>

<!-- Sprint 7: Financial Disputes & Audit Banner -->
<div class="row mb-3">
    <div class="col-sm-6 col-lg-4">
        <div class="info-box shadow-sm border-left border-warning" style="border-left: 4px solid #f59e0b !important;">
            <span class="info-box-icon bg-warning text-dark"><i class="fas fa-scale-balanced"></i></span>
            <div class="info-box-content">
                <span class="info-box-text font-weight-bold">Kasus Dispute Terbuka</span>
                <span class="info-box-number"><?= $stats['open_disputes'] ?? 0 ?> <small>Kasus Mediasi</small></span>
                <a href="<?= base_url('/admin/disputes') ?>" class="small text-muted font-weight-bold">
                    Kelola Kasus <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="info-box shadow-sm border-left border-danger" style="border-left: 4px solid #ef4444 !important;">
            <span class="info-box-icon bg-danger"><i class="fas fa-hand-holding-dollar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text font-weight-bold">Total Pengembalian Dana</span>
                <span class="info-box-number text-danger font-weight-bold">Rp <?= number_format($stats['total_refunds_amount'] ?? 0, 0, ',', '.') ?></span>
                <a href="<?= base_url('/admin/refunds') ?>" class="small text-muted font-weight-bold">
                    Riwayat Refund <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="info-box shadow-sm border-left border-teal" style="border-left: 4px solid #0d9488 !important;">
            <span class="info-box-icon bg-teal text-white"><i class="fas fa-clock-rotate-left"></i></span>
            <div class="info-box-content">
                <span class="info-box-text font-weight-bold">Immutable Audit Trail</span>
                <span class="info-box-number">Aktif & Persisten</span>
                <a href="<?= base_url('/admin/audit') ?>" class="small text-teal font-weight-bold">
                    Buka Log Viewer <i class="fas fa-arrow-circle-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Moderation & Verification Row -->
<div class="row">
    <!-- Verifikasi Penyedia -->
    <div class="col-lg-6" id="providers">
        <div class="card card-outline card-teal shadow-sm">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-address-card text-teal mr-2"></i> Verifikasi Identitas Penyedia (KYC)
                </h3>
                <a href="<?= base_url('/admin/providers') ?>" class="btn btn-xs btn-outline-teal font-weight-bold">
                    Kelola Semua KYC
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover m-0 text-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>Nama Penyedia</th>
                                <th>WhatsApp</th>
                                <th>Status KYC</th>
                                <th>Bank / Settlement</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($providers)): ?>
                                <?php foreach ($providers as $p): ?>
                                    <tr>
                                        <td class="font-weight-bold"><?= esc($p['name']) ?></td>
                                        <td><?= esc($p['whatsapp'] ?? '-') ?></td>
                                        <td>
                                            <?php if ($p['verification_status'] === 'verified'): ?>
                                                <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Terverifikasi</span>
                                            <?php elseif ($p['verification_status'] === 'rejected'): ?>
                                                <span class="badge badge-danger">Ditolak</span>
                                            <?php else: ?>
                                                <span class="badge badge-warning">Menunggu</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($p['bank_name'] ?? 'BSI') ?> (<?= esc($p['bank_account_number'] ?? '-') ?>)</td>
                                        <td>
                                            <a href="<?= base_url('/admin/providers') ?>" class="btn btn-xs btn-outline-teal">Review</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="text-center text-muted">Belum ada pengajuan penyedia.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Moderasi Listing -->
    <div class="col-lg-6" id="moderation">
        <div class="card card-outline card-teal shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-check-double text-teal mr-2"></i> Listing Properti Aktif
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover m-0 text-sm">
                        <thead class="bg-light">
                            <tr>
                                <th>Judul Iklan</th>
                                <th>Target</th>
                                <th>Harga / Bln</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($listings)): ?>
                                <?php foreach ($listings as $l): ?>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-truncate" style="max-width: 240px;"><?= esc($l['title']) ?></div>
                                            <small class="text-muted"><?= esc($l['property_name'] ?? 'Properti') ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-<?= $l['target_gender'] === 'putri' ? 'danger' : ($l['target_gender'] === 'putra' ? 'primary' : 'purple') ?>">
                                                <?= esc($l['target_gender']) ?>
                                            </span>
                                        </td>
                                        <td class="font-weight-bold">Rp <?= number_format($l['price_monthly'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="badge badge-success"><?= esc($l['publication_status']) ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted">Belum ada listing.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Regions & Audit Trail -->
<div class="row" id="regions">
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-map-marked-alt text-teal mr-2"></i> Struktur Wilayah (Regions)
                </h3>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush text-sm">
                    <?php foreach ($regions as $r): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <?php if ($r['level'] === 'province'): ?>
                                    <strong><i class="fas fa-landmark text-primary mr-2"></i> <?= esc($r['name']) ?></strong>
                                <?php elseif ($r['level'] === 'city'): ?>
                                    <span class="ml-3"><i class="fas fa-city text-secondary mr-2"></i> <?= esc($r['name']) ?></span>
                                <?php else: ?>
                                    <span class="ml-4"><i class="fas fa-location-dot text-danger mr-2"></i> <?= esc($r['name']) ?></span>
                                <?php endif; ?>
                            </span>
                            <span class="badge badge-light border text-uppercase" style="font-size: 0.65rem;"><?= esc($r['level']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-7" id="audit">
        <div class="card shadow-sm border-0">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold mb-0">
                    <i class="fas fa-clock-rotate-left text-teal mr-2"></i> Immutable Audit Trail (System Logs)
                </h3>
                <a href="<?= base_url('/admin/audit') ?>" class="btn btn-xs btn-outline-teal font-weight-bold">
                    Lihat Semua Log (Viewer)
                </a>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">Semua mutasi finansial, pergantian state kontrak, dan persetujuan listing dicatat secara permanen.</p>
                <div class="timeline text-sm">
                    <div class="time-label">
                        <span class="bg-teal"><?= date('d M Y') ?></span>
                    </div>

                    <?php if (!empty($recentAuditLogs)): ?>
                        <?php foreach ($recentAuditLogs as $log): ?>
                            <div>
                                <?php 
                                $iconClass = match($log['action']) {
                                    'PAYMENT_SUCCESS', 'INVOICE_PAYMENT_SUCCESS', 'CREATE_CONTRACT' => 'fa-credit-card bg-success',
                                    'INVOICE_OVERDUE', 'PAYMENT_FAILED', 'REFUND_PROCESSED'          => 'fa-triangle-exclamation bg-danger',
                                    'DISPUTE_OPENED', 'DISPUTE_INVESTIGATING'                        => 'fa-scale-balanced bg-warning',
                                    'DISPUTE_DECIDED'                                               => 'fa-stamp bg-teal',
                                    default                                                         => 'fa-circle-check bg-primary',
                                };
                                ?>
                                <i class="fas <?= $iconClass ?>"></i>
                                <div class="timeline-item shadow-none border">
                                    <span class="time"><i class="fas fa-clock"></i> <?= date('H:i', strtotime($log['created_at'])) ?></span>
                                    <h3 class="timeline-header">
                                        <span class="font-weight-bold text-dark"><?= esc($log['actor_name'] ?? 'System') ?></span>: 
                                        <span class="badge badge-light border"><?= esc($log['action']) ?></span> pada <code><?= esc($log['object_type']) ?> #<?= $log['object_id'] ?></code>
                                    </h3>
                                    <?php if (!empty($log['reason'])): ?>
                                        <div class="timeline-body text-muted small py-1">
                                            <?= esc($log['reason']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div>
                            <i class="fas fa-database bg-primary"></i>
                            <div class="timeline-item shadow-none border">
                                <span class="time"><i class="fas fa-clock"></i> Baru saja</span>
                                <h3 class="timeline-header"><span class="font-weight-bold text-primary">System</span> inisialisasi skema database KosAja.</h3>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div>
                        <i class="fas fa-check bg-gray"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
