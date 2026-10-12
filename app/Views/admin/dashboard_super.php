<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-7">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-globe text-primary mr-2"></i> Dasbor Tata Kelola Platform (Super Admin)
            </h1>
            <p class="text-muted small mb-0">Pengawasan global multi-region, metrik finansial platform, dan eskalasi seluruh Indonesia.</p>
        </div>
        <div class="col-sm-5 text-sm-right mt-2 mt-sm-0">
            <span class="badge badge-primary px-3 py-2 mr-1 shadow-sm">
                <i class="fas fa-earth-asia mr-1"></i> Scope: Nasional (Seluruh Indonesia)
            </span>
            <span class="badge badge-success px-3 py-2 shadow-sm">
                <i class="fas fa-shield-halved mr-1"></i> Otoritas Penuh
            </span>
        </div>
    </div>
</div>

<!-- Alert Banner: Escalations & Payouts Waiting -->
<?php if (($stats['escalated_disputes'] ?? 0) > 0 || ($stats['pending_settlements_count'] ?? 0) > 0): ?>
<div class="alert alert-warning border-0 shadow-sm mb-3">
    <div class="d-flex align-items-center justify-content-between flex-wrap">
        <div>
            <i class="fas fa-triangle-exclamation mr-2 fa-lg"></i>
            <strong>Perhatian Eksekutif Super Admin:</strong> 
            Terdapat <?= esc($stats['escalated_disputes']) ?> sengketa dieskalasi dari Admin Daerah &amp; 
            <?= esc($stats['pending_settlements_count']) ?> pencairan dana mitra (Rp <?= number_format($stats['pending_settlements_amount'], 0, ',', '.') ?>) menunggu otorisasi Anda.
        </div>
        <div class="mt-2 mt-sm-0">
            <a href="<?= base_url('/admin/settlements') ?>" class="btn btn-sm btn-dark mr-1">
                <i class="fas fa-money-bill-transfer mr-1"></i> Otorisasi Settlement
            </a>
            <a href="<?= base_url('/admin/disputes?status=escalated') ?>" class="btn btn-sm btn-outline-dark">
                <i class="fas fa-scale-balanced mr-1"></i> Tinjau Eskalasi
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Top Row: Financial & Platform KPIs -->
<div class="row">
    <!-- GTV -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box shadow-sm border-0" style="border-left: 4px solid #3b82f6 !important;">
            <span class="info-box-icon bg-primary text-white"><i class="fas fa-coins"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Gross Transaction Volume (GMV)</span>
                <span class="info-box-number text-dark font-weight-bold" style="font-size: 1.25rem;">
                    Rp <?= number_format($stats['gtv'] ?? 0, 0, ',', '.') ?>
                </span>
                <small class="text-success"><i class="fas fa-arrow-trend-up mr-1"></i> Total perputaran sewa</small>
            </div>
        </div>
    </div>
    <!-- Platform Revenue -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box shadow-sm border-0" style="border-left: 4px solid #10b981 !important;">
            <span class="info-box-icon bg-success text-white"><i class="fas fa-hand-holding-dollar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Pendapatan Platform KosAja</span>
                <span class="info-box-number text-success font-weight-bold" style="font-size: 1.25rem;">
                    Rp <?= number_format($stats['platform_revenue'] ?? 0, 0, ',', '.') ?>
                </span>
                <small class="text-muted"><i class="fas fa-receipt mr-1"></i> Gateway &amp; service fee</small>
            </div>
        </div>
    </div>
    <!-- Settlements Handled -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box shadow-sm border-0" style="border-left: 4px solid #0d9488 !important;">
            <span class="info-box-icon bg-teal text-white"><i class="fas fa-money-bill-transfer"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Dana Mitra Dicairkan</span>
                <span class="info-box-number text-teal font-weight-bold" style="font-size: 1.25rem;">
                    Rp <?= number_format($stats['total_settled'] ?? 0, 0, ',', '.') ?>
                </span>
                <small class="text-muted">Settlement mitra sukses</small>
            </div>
        </div>
    </div>
    <!-- Refunds Issued -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box shadow-sm border-0" style="border-left: 4px solid #ef4444 !important;">
            <span class="info-box-icon bg-danger text-white"><i class="fas fa-rotate-left"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Pengembalian (Refund)</span>
                <span class="info-box-number text-danger font-weight-bold" style="font-size: 1.25rem;">
                    Rp <?= number_format($stats['total_refunds_amount'] ?? 0, 0, ',', '.') ?>
                </span>
                <small class="text-muted">Hasil keputusan mediasi</small>
            </div>
        </div>
    </div>
</div>

<!-- Secondary Row: User Ecosystem & Global Inventories -->
<div class="row">
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <span class="badge bg-light p-3 text-primary"><i class="fas fa-users fa-2x"></i></span>
                    </div>
                    <div>
                        <div class="text-muted small">Total Pengguna IAM</div>
                        <div class="h4 font-weight-bold mb-0"><?= esc($stats['total_users']) ?></div>
                        <small class="text-muted"><?= esc($stats['total_tenants']) ?> Penyewa | <?= esc($stats['total_admin_daerah']) ?> Admin</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <span class="badge bg-light p-3 text-warning"><i class="fas fa-user-tie fa-2x text-warning"></i></span>
                    </div>
                    <div>
                        <div class="text-muted small">Mitra Penyedia Aktif</div>
                        <div class="h4 font-weight-bold mb-0"><?= esc($stats['total_providers']) ?></div>
                        <small class="text-muted">Pemilik kos &amp; kontrakan</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <span class="badge bg-light p-3 text-info"><i class="fas fa-building fa-2x text-info"></i></span>
                    </div>
                    <div>
                        <div class="text-muted small">Inventori Properti</div>
                        <div class="h4 font-weight-bold mb-0"><?= esc($stats['total_properties']) ?></div>
                        <small class="text-muted">Bangunan terdaftar</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <div class="mr-3">
                        <span class="badge bg-light p-3 text-success"><i class="fas fa-bullhorn fa-2x text-success"></i></span>
                    </div>
                    <div>
                        <div class="text-muted small">Listing Terbit</div>
                        <div class="h4 font-weight-bold mb-0"><?= esc($stats['published_listings']) ?></div>
                        <small class="text-muted"><?= esc($stats['pending_listings']) ?> Menunggu Review</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section: Regional Performance Matrix & Multi-Region Governance -->
<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-map-location-dot text-danger mr-2"></i> Matriks Performa Wilayah (Multi-Region)
                </h5>
                <a href="<?= base_url('/admin/regions') ?>" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-cog mr-1"></i> Kelola Master Wilayah
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th>Wilayah / Kota</th>
                                <th class="text-center">Properti</th>
                                <th class="text-center">Listing Aktif</th>
                                <th>Admin Daerah Bertugas</th>
                                <th>Status Operasional</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($regionalPerformance as $region): ?>
                            <tr>
                                <td class="font-weight-bold text-dark">
                                    <i class="fas fa-location-dot text-danger mr-2"></i>
                                    <?= esc($region['region_name']) ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-light border px-2 py-1"><?= esc($region['properties_count']) ?> Properti</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-success px-2 py-1"><?= esc($region['listings_count']) ?> Unit Tayang</span>
                                </td>
                                <td>
                                    <?php if ($region['admin_name'] !== 'Belum Ditugaskan'): ?>
                                        <div class="font-weight-bold small text-dark"><i class="fas fa-user-shield text-info mr-1"></i> <?= esc($region['admin_name']) ?></div>
                                        <small class="text-muted"><?= esc($region['admin_email']) ?></small>
                                    <?php else: ?>
                                        <span class="badge badge-warning text-dark"><i class="fas fa-triangle-exclamation mr-1"></i> Belum Ada Admin</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($region['status'] === 'Aktif Beroperasi'): ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Beroperasi</span>
                                    <?php else: ?>
                                        <a href="<?= base_url('/admin/users?action=add_admin&region_id=' . $region['region_id']) ?>" class="btn btn-xs btn-outline-danger">
                                            <i class="fas fa-plus mr-1"></i> Tugaskan
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pending Settlement Authorizations -->
        <div class="card shadow-sm border-0 mt-3">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-money-bill-transfer text-teal mr-2"></i> Antrean Pencairan Dana Mitra (Settlements)
                </h5>
                <a href="<?= base_url('/admin/settlements') ?>" class="small font-weight-bold text-teal">
                    Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (! empty($pendingSettlements)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th>Mitra Provider</th>
                                <th>Rekening Tujuan</th>
                                <th>Jumlah Bersih</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($pendingSettlements, 0, 4) as $stl): ?>
                            <tr>
                                <td>
                                    <div class="font-weight-bold text-dark"><?= esc($stl['provider_name']) ?></div>
                                    <small class="text-muted"><?= esc($stl['provider_email']) ?></small>
                                </td>
                                <td>
                                    <div class="small font-weight-bold text-dark"><?= esc($stl['bank_name']) ?></div>
                                    <small class="text-muted"><?= esc($stl['bank_account_number']) ?> a/n <?= esc($stl['bank_account_holder']) ?></small>
                                </td>
                                <td>
                                    <span class="font-weight-bold text-success">
                                        Rp <?= number_format($stl['net_amount'], 0, ',', '.') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-warning">Menunggu Otorisasi</span>
                                </td>
                                <td class="text-right">
                                    <a href="<?= base_url('/admin/settlements') ?>" class="btn btn-xs btn-success">
                                        <i class="fas fa-check mr-1"></i> Proses
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="p-4 text-center text-muted">
                    <i class="fas fa-circle-check fa-2x text-success mb-2"></i>
                    <p class="mb-0">Semua settlement pencairan dana mitra telah diproses lunas.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Escalated Disputes & System Controls -->
    <div class="col-lg-4">
        <!-- Quick Master Controls Card -->
        <div class="card shadow-sm border-0 mb-3 bg-dark text-white">
            <div class="card-body p-3">
                <h6 class="font-weight-bold mb-2"><i class="fas fa-shield-halved text-teal mr-2"></i> Pusat Kendali Super Admin</h6>
                <p class="small text-white-50 mb-3">Wewenang tingkat platform yang tidak dimiliki oleh Admin Daerah:</p>
                <div class="d-grid gap-2">
                    <a href="<?= base_url('/admin/users') ?>" class="btn btn-sm btn-outline-light text-left mb-2 d-block">
                        <i class="fas fa-users-gear mr-2 text-warning"></i> Manajemen IAM &amp; Admin Daerah
                    </a>
                    <a href="<?= base_url('/admin/monetization') ?>" class="btn btn-sm btn-outline-light text-left mb-2 d-block">
                        <i class="fas fa-coins mr-2 text-teal"></i> Kebijakan Monetisasi &amp; Fee
                    </a>
                    <a href="<?= base_url('/admin/configuration') ?>" class="btn btn-sm btn-outline-light text-left mb-2 d-block">
                        <i class="fas fa-sliders mr-2 text-info"></i> Konfigurasi Payment Gateway &amp; SLA
                    </a>
                    <a href="<?= base_url('/admin/transactions') ?>" class="btn btn-sm btn-outline-light text-left d-block">
                        <i class="fas fa-receipt mr-2 text-success"></i> Riwayat Transaksi Finansial Global
                    </a>
                </div>
            </div>
        </div>

        <!-- Escalated Disputes Queue -->
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-scale-balanced text-warning mr-2"></i> Sengketa Dieskalasi
                </h6>
            </div>
            <div class="card-body p-0">
                <?php if (! empty($escalatedDisputes)): ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($escalatedDisputes as $dsp): ?>
                    <li class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge badge-danger">ESKALASI DAERAH</span>
                            <small class="text-muted"><?= date('d M Y', strtotime($dsp['created_at'])) ?></small>
                        </div>
                        <div class="font-weight-bold text-dark mb-1"><?= esc($dsp['title']) ?></div>
                        <div class="small text-muted mb-2">
                            Penyewa: <strong><?= esc($dsp['raised_by_name']) ?></strong> vs Mitra: <strong><?= esc($dsp['against_user_name']) ?></strong>
                        </div>
                        <a href="<?= base_url('/admin/disputes/' . $dsp['id']) ?>" class="btn btn-xs btn-outline-danger btn-block">
                            <i class="fas fa-gavel mr-1"></i> Tinjau &amp; Putuskan Kasus
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="p-3 text-center text-muted small">
                    <i class="fas fa-hand-holding-hand fa-2x text-muted mb-2"></i>
                    <p class="mb-0">Tidak ada sengketa yang sedang dieskalasi ke Super Admin saat ini.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Global Audit Trail Preview -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <h6 class="card-title font-weight-bold mb-0 text-dark">
                        <i class="fas fa-clock-rotate-left text-teal mr-2"></i> Log Audit Global
                    </h6>
                    <span class="badge badge-teal ml-2" style="font-size: 0.65rem;">Live WIB</span>
                </div>
                <a href="<?= base_url('/admin/audit') ?>" class="btn btn-xs btn-outline-teal font-weight-bold">
                    Viewer <i class="fas fa-arrow-right ml-1"></i>
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
                                <?= esc($log['reason'] ?? $log['description'] ?? 'Aksi sistem terekam') ?>
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
                        <p class="small mb-0">Belum ada aktivitas terekam.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
