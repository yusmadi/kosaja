<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-7">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-list-check text-warning mr-2"></i> Dasbor Operasional Regional
            </h1>
            <p class="text-muted small mb-0">Antrean tugas verifikasi mitra, moderasi listing, penanganan laporan, dan mediasi sengketa.</p>
        </div>
        <div class="col-sm-5 text-sm-right mt-2 mt-sm-0">
            <span class="badge badge-warning px-3 py-2 mr-1 shadow-sm text-dark font-weight-bold">
                <i class="fas fa-location-dot mr-1 text-danger"></i> Wilayah Tugas: <?= esc($scopedLabel) ?>
            </span>
            <span class="badge badge-success px-3 py-2 shadow-sm">
                <i class="fas fa-bolt mr-1"></i> Mode Antrean Aktif
            </span>
        </div>
    </div>
</div>

<!-- Regional Scope Information Banner -->
<div class="alert alert-info border-0 shadow-sm mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap">
        <div>
            <i class="fas fa-map-marked-alt mr-2 fa-lg"></i>
            <strong>Cakupan Yurisdiksi Operasional:</strong> 
            Anda sedang mengelola operasional wilayah <strong><?= esc($scopedLabel) ?></strong><?= ! empty($clustersLabel) ? ' (termasuk klaster ' . esc($clustersLabel) . ')' : '' ?>. Data di luar wilayah ini dibatasi sesuai regulasi multi-region platform KosAja.
        </div>
        <div class="mt-2 mt-sm-0">
            <a href="<?= base_url('/admin/regional-reports') ?>" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-chart-line mr-1"></i> Performa Wilayah
            </a>
        </div>
    </div>
</div>

<!-- 4 Action Queue Cards (PRD 37.3 & UX 34 Minimal Requirements) -->
<div class="row">
    <!-- PENDING VERIFICATION -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box shadow-sm border-0" style="border-left: 4px solid #f59e0b !important;">
            <span class="info-box-icon bg-warning text-dark"><i class="fas fa-user-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-uppercase font-weight-bold text-muted small">Pending Verification</span>
                <span class="info-box-number text-dark font-weight-bold" style="font-size: 1.5rem;">
                    <?= esc($stats['pending_kyc'] ?? 0) ?>
                </span>
                <a href="<?= base_url('/admin/providers?status=pending') ?>" class="small text-warning font-weight-bold">
                    Verifikasi KYC Mitra <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- PENDING LISTING REVIEW -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box shadow-sm border-0" style="border-left: 4px solid #3b82f6 !important;">
            <span class="info-box-icon bg-primary text-white"><i class="fas fa-shield-halved"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-uppercase font-weight-bold text-muted small">Pending Listing Review</span>
                <span class="info-box-number text-dark font-weight-bold" style="font-size: 1.5rem;">
                    <?= esc($stats['pending_listings'] ?? 0) ?>
                </span>
                <a href="<?= base_url('/admin/listings?status=pending_review') ?>" class="small text-primary font-weight-bold">
                    Moderasi Iklan Masuk <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- OPEN REPORTS -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box shadow-sm border-0" style="border-left: 4px solid #dc2626 !important;">
            <span class="info-box-icon bg-danger text-white"><i class="fas fa-flag"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-uppercase font-weight-bold text-muted small">Open Reports</span>
                <span class="info-box-number text-dark font-weight-bold" style="font-size: 1.5rem;">
                    <?= esc($stats['open_reports'] ?? 0) ?>
                </span>
                <a href="<?= base_url('/admin/reports') ?>" class="small text-danger font-weight-bold">
                    Laporan Pelanggaran <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- OPEN DISPUTES -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box shadow-sm border-0" style="border-left: 4px solid #8b5cf6 !important;">
            <span class="info-box-icon bg-purple text-white" style="background-color: #8b5cf6 !important;"><i class="fas fa-scale-balanced"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-uppercase font-weight-bold text-muted small">Open Disputes</span>
                <span class="info-box-number text-dark font-weight-bold" style="font-size: 1.5rem;">
                    <?= esc($stats['open_disputes'] ?? 0) ?>
                </span>
                <a href="<?= base_url('/admin/disputes') ?>" class="small font-weight-bold" style="color: #8b5cf6;">
                    Mediasi Sengketa Sewa <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Section: Priority Operational Action Queues -->
<div class="row">
    <!-- Left Column: Queues for Moderation & Reports -->
    <div class="col-lg-8">
        <!-- Queue 1: Pending Listings Needing Moderation -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-inbox text-primary mr-2"></i> Antrean Moderasi Listing Wilayah
                </h5>
                <a href="<?= base_url('/admin/listings?status=pending_review') ?>" class="small font-weight-bold text-primary">
                    Buka Antrean Lengkap <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (! empty($listingsQueue)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th>Listing / Unit</th>
                                <th>Gedung Properti</th>
                                <th>Mitra Pemilik</th>
                                <th>Kesesuaian Gender</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($listingsQueue as $item): ?>
                            <tr>
                                <td>
                                    <div class="font-weight-bold text-dark"><?= esc($item['title'] ?? $item['unit_name']) ?></div>
                                    <small class="text-muted"><i class="fas fa-door-open mr-1"></i> Unit: <?= esc($item['unit_name']) ?></small>
                                </td>
                                <td>
                                    <div class="small font-weight-bold text-dark"><?= esc($item['property_name']) ?></div>
                                    <small class="text-muted"><?= esc($item['region_name'] ?? $scopedLabel) ?></small>
                                </td>
                                <td>
                                    <div class="small text-dark font-weight-bold"><?= esc($item['provider_name']) ?></div>
                                    <span class="badge badge-light border">Mitra Terdaftar</span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $item['target_gender'] === 'putri' ? 'danger' : ($item['target_gender'] === 'putra' ? 'primary' : 'success') ?>">
                                        <?= strtoupper(str_replace('_', ' ', $item['target_gender'])) ?>
                                    </span>
                                </td>
                                <td class="text-right">
                                    <a href="<?= base_url('/admin/listings/' . $item['id']) ?>" class="btn btn-xs btn-primary font-weight-bold">
                                        <i class="fas fa-check-double mr-1"></i> Moderasi
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <p class="mb-0">Tidak ada listing iklan yang menunggu moderasi di wilayah <?= esc($scopedLabel) ?>.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Queue 2: Open Reports / Laporan Pelanggaran Warga -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-triangle-exclamation text-danger mr-2"></i> Pengaduan &amp; Laporan Pelanggaran Masuk
                </h5>
                <a href="<?= base_url('/admin/reports') ?>" class="small font-weight-bold text-danger">
                    Semua Laporan <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (! empty($reportsQueue)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th>Pelapor</th>
                                <th>Judul Laporan</th>
                                <th>Objek Laporan</th>
                                <th>Status</th>
                                <th class="text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportsQueue as $rpt): ?>
                            <tr>
                                <td>
                                    <div class="font-weight-bold small text-dark"><?= esc($rpt['reporter_name']) ?></div>
                                    <small class="text-muted"><?= esc($rpt['reporter_email']) ?></small>
                                </td>
                                <td>
                                    <div class="small font-weight-bold text-dark"><?= esc($rpt['title']) ?></div>
                                    <small class="text-muted text-truncate d-block" style="max-width: 250px;"><?= esc($rpt['reason']) ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-light border text-uppercase"><?= esc($rpt['object_type']) ?> #<?= esc($rpt['object_id']) ?></span>
                                </td>
                                <td>
                                    <span class="badge badge-danger">Perlu Investigasi</span>
                                </td>
                                <td class="text-right">
                                    <a href="<?= base_url('/admin/reports/' . $rpt['id']) ?>" class="btn btn-xs btn-outline-danger font-weight-bold">
                                        <i class="fas fa-magnifying-glass mr-1"></i> Investigasi
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-shield-heart fa-2x text-teal mb-2"></i>
                    <p class="mb-0">Tidak ada laporan pengaduan pelanggaran aktif di wilayah ini saat ini.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Queue 3: Active Rental Disputes -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-scale-balanced text-purple mr-2" style="color: #8b5cf6;"></i> Mediasi Sengketa Sewa Regional
                </h5>
                <a href="<?= base_url('/admin/disputes') ?>" class="small font-weight-bold" style="color: #8b5cf6;">
                    Semua Sengketa <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (! empty($disputesQueue)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th>Kasus #</th>
                                <th>Pihak Terlibat</th>
                                <th>Properti</th>
                                <th>Status / Eskalasi</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($disputesQueue as $dsp): ?>
                            <tr>
                                <td class="font-weight-bold text-dark">#DSP-<?= esc($dsp['id']) ?></td>
                                <td>
                                    <div class="small">Penyewa: <strong><?= esc($dsp['raised_by_name']) ?></strong></div>
                                    <div class="small text-muted">Pemilik: <?= esc($dsp['against_user_name']) ?></div>
                                </td>
                                <td>
                                    <div class="small font-weight-bold text-dark"><?= esc($dsp['property_name']) ?></div>
                                    <small class="text-muted">Unit <?= esc($dsp['unit_name']) ?></small>
                                </td>
                                <td>
                                    <?php if (! empty($dsp['is_escalated'])): ?>
                                        <span class="badge badge-warning text-dark"><i class="fas fa-arrow-up mr-1"></i> Dieskalasi ke Super Admin</span>
                                    <?php else: ?>
                                        <span class="badge badge-info">Mediasi Daerah</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right">
                                    <a href="<?= base_url('/admin/disputes/' . $dsp['id']) ?>" class="btn btn-xs btn-purple text-white" style="background-color: #8b5cf6;">
                                        <i class="fas fa-handshake mr-1"></i> Mediasi
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-face-smile fa-2x text-success mb-2"></i>
                    <p class="mb-0">Tidak ada sengketa penyewa-mitra yang sedang terbuka di wilayah ini.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Regional Tools & Quick Actions -->
    <div class="col-lg-4">
        <!-- Quick Regional Operational Tools -->
        <div class="card shadow-sm border-0 mb-3 bg-dark text-white">
            <div class="card-body p-3">
                <h6 class="font-weight-bold mb-2">
                    <i class="fas fa-toolbox text-warning mr-2"></i> Tindakan Administratif Daerah
                </h6>
                <p class="small text-white-50 mb-3">Tindakan operasional lapangan dengan kewajiban input alasan resmi (PRD 5.3):</p>
                
                <div class="d-grid gap-2">
                    <a href="<?= base_url('/admin/restrictions') ?>" class="btn btn-sm btn-outline-warning text-left mb-2 d-block">
                        <i class="fas fa-ban mr-2"></i> Kelola Restriksi / Sanksi Mitra
                    </a>
                    <a href="<?= base_url('/admin/regional-reports') ?>" class="btn btn-sm btn-outline-info text-left mb-2 d-block">
                        <i class="fas fa-chart-pie mr-2"></i> Laporan Okupansi &amp; SLA Wilayah
                    </a>
                    <a href="<?= base_url('/admin/audit') ?>" class="btn btn-sm btn-outline-light text-left d-block">
                        <i class="fas fa-clock-rotate-left mr-2"></i> Log Aktivitas Moderasi Daerah
                    </a>
                </div>
            </div>
        </div>

        <!-- Suspended / Restricted Watchlist -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-triangle-exclamation text-danger mr-2"></i> Daftar Pengawasan Sanksi
                </h6>
            </div>
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                    <span class="small text-muted">Mitra Disuspensi di Wilayah:</span>
                    <span class="badge badge-danger"><?= esc($stats['restricted_providers']) ?> Mitra</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="small text-muted">Listing Ditangguhkan (Inactive):</span>
                    <span class="badge badge-warning text-dark"><?= esc($stats['inactive_listings']) ?> Unit</span>
                </div>
                <div class="mt-3">
                    <a href="<?= base_url('/admin/restrictions') ?>" class="btn btn-xs btn-outline-danger btn-block font-weight-bold">
                        Buka Panel Restriksi
                    </a>
                </div>
            </div>
        </div>

        <!-- Regional Audit Log Feed -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <h6 class="card-title font-weight-bold mb-0 text-dark">
                        <i class="fas fa-history text-teal mr-2"></i> Jejak Audit Daerah
                    </h6>
                    <span class="badge badge-teal ml-2" style="font-size: 0.65rem;">WIB</span>
                </div>
                <a href="<?= base_url('/admin/audit') ?>" class="btn btn-xs btn-outline-teal font-weight-bold">
                    Detail <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (! empty($recentAuditLogs)): ?>
                    <ul class="list-group list-group-flush small">
                        <?php foreach (array_slice($recentAuditLogs, 0, 6) as $log): ?>
                        <li class="list-group-item px-3 py-2 border-bottom">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <?php 
                                $actClass = match($log['action'] ?? '') {
                                    'PAYMENT_SUCCESS', 'INVOICE_PAYMENT_SUCCESS', 'CREATE_CONTRACT', 'APPROVE_LISTING', 'VERIFY_PROVIDER' => 'badge-success',
                                    'INVOICE_OVERDUE', 'PAYMENT_FAILED', 'REFUND_PROCESSED', 'REJECT_LISTING', 'REJECT_PROVIDER', 'SUSPEND_PROVIDER' => 'badge-danger',
                                    'DISPUTE_OPENED', 'DISPUTE_INVESTIGATING' => 'badge-warning text-dark',
                                    'USER_LOGIN', 'USER_REGISTER' => 'badge-primary',
                                    default => 'badge-info',
                                };
                                ?>
                                <span class="badge <?= $actClass ?> font-weight-bold text-truncate" style="max-width: 60%; font-size: 0.7rem;">
                                    <?= esc($log['action']) ?>
                                </span>
                                <span class="text-muted font-weight-bold" style="font-size: 11px;">
                                    <i class="far fa-clock mr-1 text-teal"></i><?= \App\Models\AuditLogModel::formatJakartaTime($log['created_at'], 'H:i d/m') ?>
                                </span>
                            </div>
                            <div class="text-dark font-weight-500 mb-1" style="font-size: 0.8rem; line-height: 1.3;">
                                <?= esc($log['reason'] ?? $log['description'] ?? 'Aksi regional tercatat') ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center text-muted" style="font-size: 0.72rem;">
                                <span>
                                    <i class="fas fa-user-circle mr-1 text-secondary"></i>
                                    <strong><?= esc($log['actor_name'] ?? 'SYSTEM / CRON') ?></strong>
                                    <?php if (! empty($log['actor_role'])): ?>
                                        <span class="badge badge-light border text-uppercase" style="font-size: 0.6rem;"><?= esc($log['actor_role']) ?></span>
                                    <?php endif; ?>
                                </span>
                                <span>
                                    <code><?= esc($log['object_type']) ?>#<?= esc($log['object_id']) ?></code>
                                    <?php if (! empty($log['region_name'])): ?>
                                        <span class="badge badge-light text-teal ml-1" title="Wilayah"><i class="fas fa-map-marker-alt"></i> <?= esc($log['region_name']) ?></span>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-clipboard-list fa-2x mb-2 text-secondary opacity-50"></i>
                        <p class="small mb-0">Belum ada aktivitas terekam di wilayah <?= esc($scopedLabel) ?>.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
