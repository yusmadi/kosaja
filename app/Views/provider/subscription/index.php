<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<!-- Content Header -->
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-7">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-crown text-teal mr-2"></i> Paket & Langganan Mitra
            </h1>
            <p class="text-muted small mb-0">Kelola paket aktif, tambah kuota publikasi iklan listing kos, dan pantau riwayat tagihan Anda.</p>
        </div>
        <div class="col-sm-5 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/provider/listings') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold mr-1">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Listing
            </a>
            <a href="#katalog-paket" class="btn btn-teal text-white btn-sm shadow-sm font-weight-bold">
                <i class="fas fa-bolt mr-1"></i> Upgrade Kuota Sekarang
            </a>
        </div>
    </div>
</div>

<!-- Flash Alerts -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close text-dark" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="close text-dark" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- 1. GRACE PERIOD / EXPIRED ALERT BANNERS (Harmonized with Listings & Dashboard) -->
<?php if (!empty($quota['is_in_grace_period'])): ?>
    <div class="alert alert-warning border-0 shadow-sm mb-4 d-flex justify-content-between align-items-center" style="background-color: #fef3c7; color: #92400e; border-left: 4px solid #f59e0b !important; border-radius: 8px;">
        <div class="d-flex align-items-center mr-3">
            <i class="fas fa-hourglass-half fa-2x mr-3 text-warning"></i>
            <div>
                <strong class="d-block mb-1" style="color: #78350f;">
                    Masa Transisi (Grace Period) Berlangsung: Sisa <?= esc($quota['days_remaining']) ?> Hari!
                </strong>
                <span class="text-sm" style="color: #92400e; line-height: 1.45;">
                    Sewa pertama Anda telah berhasil lunas. Sesuai ketentuan PRD KosAja (FR-PM-004), silakan pilih paket langganan atau listing fee berbayar sebelum tanggal <strong><?= date('d M Y', strtotime($quota['end_date'])) ?></strong> agar sisa unit kamar iklan Anda tetap aktif di marketplace.
                </span>
            </div>
        </div>
        <div class="flex-shrink-0">
            <a href="#katalog-paket" class="btn btn-warning text-dark font-weight-bold shadow-sm btn-sm">
                <i class="fas fa-bolt mr-1"></i> Pilih Paket Berbayar
            </a>
        </div>
    </div>
<?php elseif ($quota['status'] === 'expired'): ?>
    <div class="alert alert-danger border-0 shadow-sm mb-4 d-flex justify-content-between align-items-center" style="background-color: #fee2e2; color: #991b1b; border-left: 4px solid #ef4444 !important; border-radius: 8px;">
        <div class="d-flex align-items-center mr-3">
            <i class="fas fa-ban fa-2x mr-3 text-danger"></i>
            <div>
                <strong class="d-block mb-1" style="color: #7f1d1d;">
                    Masa Aktif Paket Telah Berakhir
                </strong>
                <span class="text-sm" style="color: #991b1b; line-height: 1.45;">
                    Listing iklan unit yang belum tersewa telah dinonaktifkan sementara. Aktifkan paket langganan atau listing fee untuk memulihkan dan mempublikasikan unit kamar Anda kembali.
                </span>
            </div>
        </div>
        <div class="flex-shrink-0">
            <a href="#katalog-paket" class="btn btn-danger font-weight-bold shadow-sm btn-sm">
                <i class="fas fa-redo mr-1"></i> Re-Aktivasi Paket
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- 2. STATUS CARD & CAPACITY METER (Matches Dashboard 4px accent top border) -->
<div class="row mb-4">
    <!-- Status Paket Aktif -->
    <div class="col-lg-6 mb-3 mb-lg-0">
        <div class="card h-100 shadow-sm border-0" style="border-top: 4px solid #0d9488 !important; border-radius: 8px;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Paket Aktif Saat Ini</span>
                        <h4 class="font-weight-bold text-dark mt-1 mb-0">
                            <?= esc($quota['plan_name']) ?>
                        </h4>
                    </div>
                    <div>
                        <?php if ($quota['status'] === 'active'): ?>
                            <span class="badge badge-pill badge-success px-3 py-2 text-uppercase" style="font-size: 0.75rem;">
                                <i class="fas fa-check-circle mr-1"></i> Aktif
                            </span>
                        <?php elseif ($quota['status'] === 'grace_period'): ?>
                            <span class="badge badge-pill badge-warning text-dark px-3 py-2 text-uppercase" style="font-size: 0.75rem;">
                                <i class="fas fa-hourglass-half mr-1"></i> Masa Transisi
                            </span>
                        <?php else: ?>
                            <span class="badge badge-pill badge-danger px-3 py-2 text-uppercase" style="font-size: 0.75rem;">
                                <i class="fas fa-times-circle mr-1"></i> Kedaluwarsa
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="row pt-2 pb-2 border-top border-bottom text-center">
                    <div class="col-4 border-right">
                        <span class="text-muted small d-block">Tipe Siklus</span>
                        <strong class="text-dark">
                            <?= ($quota['billing_cycle'] === 'monthly') ? 'Bulanan (30h)' : (($quota['billing_cycle'] === '15_days') ? '15 Harian' : 'Uji Coba') ?>
                        </strong>
                    </div>
                    <div class="col-4 border-right">
                        <span class="text-muted small d-block">Batas Kuota</span>
                        <strong class="text-teal font-weight-bold">
                            <?= esc($quota['capacity_limit']) ?> Listing
                        </strong>
                    </div>
                    <div class="col-4">
                        <span class="text-muted small d-block">Sisa Waktu</span>
                        <strong class="<?= $quota['days_remaining'] <= 3 ? 'text-danger font-weight-bold' : 'text-dark' ?>">
                            <?= esc($quota['days_remaining']) ?> Hari
                        </strong>
                    </div>
                </div>

                <div class="mt-3">
                    <small class="text-muted">
                        <i class="far fa-calendar-alt text-teal mr-1"></i> 
                        <?php if (!empty($quota['end_date'])): ?>
                            Masa aktif berlaku sampai: <strong class="text-dark"><?= date('d F Y', strtotime($quota['end_date'])) ?></strong>
                        <?php else: ?>
                            Belum ada masa aktif berbayar.
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Penggunaan Kapasitas Listing -->
    <div class="col-lg-6">
        <div class="card h-100 shadow-sm border-0" style="border-top: 4px solid #3b82f6 !important; border-radius: 8px;">
            <div class="card-body p-4">
                <?php 
                    $cap = max(1, (int) $quota['capacity_limit']);
                    $pct = min(100, (int) round(($publishedCount / $cap) * 100));
                    $isFull = ($publishedCount >= $quota['capacity_limit']);
                ?>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">Penggunaan Kapasitas Listing</span>
                    <span class="badge badge-pill <?= $isFull ? 'badge-danger' : 'badge-primary' ?> px-2 py-1">
                        <?= $pct ?>% Terpakai
                    </span>
                </div>

                <div class="d-flex align-items-baseline mb-3">
                    <h2 class="display-5 font-weight-bold mb-0 text-primary">
                        <?= esc($publishedCount) ?> <small class="text-muted font-weight-normal h5">/ <?= esc($quota['capacity_limit']) ?> Listing Aktif</small>
                    </h2>
                </div>

                <div class="progress mb-3" style="height: 10px; border-radius: 6px;">
                    <div class="progress-bar <?= $isFull ? 'bg-danger' : 'bg-primary' ?>" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="d-flex justify-content-between text-xs text-muted pt-2 border-top">
                    <span><i class="fas fa-boxes-stacked text-teal mr-1"></i> Total Iklan: <strong><?= esc($totalListings) ?> unit</strong></span>
                    <span><i class="fas fa-check-circle text-success mr-1"></i> Terpublikasi: <strong><?= esc($publishedCount) ?> unit</strong></span>
                    <span>
                        <?php if ($isFull): ?>
                            <strong class="text-danger"><i class="fas fa-lock mr-1"></i> Kuota Penuh</strong>
                        <?php else: ?>
                            <strong class="text-teal"><i class="fas fa-plus mr-1"></i> Sisa: <?= max(0, $quota['capacity_limit'] - $publishedCount) ?> Slot</strong>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. KATALOG PAKET BERBAYAR -->
<div id="katalog-paket" class="card card-outline card-teal shadow-sm mb-4">
    <div class="card-header border-0 pt-4 px-4 pb-0 bg-white">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div class="mb-3 mb-md-0">
                <h3 class="card-title font-weight-bold text-dark mb-1 float-none d-block" style="font-size: 1.25rem; float: none !important;">
                    <i class="fas fa-tags text-teal mr-2"></i> Pilih Paket Layanan Penyedia
                </h3>
                <p class="text-muted small mb-0">Tingkatkan performa kos Anda dengan paket langganan bulanan hemat atau slot listing 15 harian fleksibel.</p>
            </div>
            <!-- Pill Navigation Tabs -->
            <ul class="nav nav-pills mt-2 mt-md-0" id="billingTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active font-weight-bold px-3 py-2 text-sm" 
                       id="subscription-tab" 
                       data-toggle="pill" 
                       data-bs-toggle="pill" 
                       href="#tab-subscription" 
                       data-bs-target="#tab-subscription" 
                       role="tab" 
                       aria-controls="tab-subscription" 
                       aria-selected="true">
                        <i class="fas fa-calendar-alt mr-1"></i> Langganan Bulanan (30 Hari)
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link font-weight-bold px-3 py-2 text-sm" 
                       id="listingfee-tab" 
                       data-toggle="pill" 
                       data-bs-toggle="pill" 
                       href="#tab-listingfee" 
                       data-bs-target="#tab-listingfee" 
                       role="tab" 
                       aria-controls="tab-listingfee" 
                       aria-selected="false">
                        <i class="fas fa-ticket-alt mr-1"></i> Listing Fee (15 Hari)
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="card-body p-4">
        <div class="tab-content" id="billingTabContent">
            
            <!-- TAB 1: SUBSCRIPTION BULANAN -->
            <div class="tab-pane fade show active" id="tab-subscription" role="tabpanel" aria-labelledby="subscription-tab">
                <div class="row">
                    <?php foreach ($subscriptionPlans as $plan): ?>
                        <?php 
                            $isPopular = ($plan['slug'] === 'subscription-growth');
                            $isCurrent = ($quota['plan_slug'] === $plan['slug'] && $quota['status'] === 'active');
                            $features = !empty($plan['features']) ? json_decode($plan['features'], true) : [];
                        ?>
                        <div class="col-lg-3 col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border pricing-card position-relative <?= $isPopular ? 'popular-plan' : '' ?> <?= $isCurrent ? 'current-plan' : '' ?>" style="border-radius: 10px;">
                                <?php if ($isCurrent): ?>
                                    <div class="badge-plan-status bg-success text-white">
                                        <i class="fas fa-check-circle mr-1"></i> PAKET AKTIF
                                    </div>
                                <?php elseif ($isPopular): ?>
                                    <div class="badge-plan-status bg-teal text-white">
                                        <i class="fas fa-star mr-1"></i> PALING POPULER
                                    </div>
                                <?php endif; ?>

                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="text-center mb-3">
                                        <h5 class="font-weight-bold text-dark mb-1"><?= esc($plan['name']) ?></h5>
                                        <span class="badge badge-light border text-teal px-2 py-1 font-weight-bold" style="font-size: 0.75rem;">
                                            <i class="fas fa-boxes-stacked mr-1"></i> Hingga <?= esc($plan['listing_capacity'] >= 999 ? 'Unlimited (>100)' : $plan['listing_capacity']) ?> Listing Aktif
                                        </span>
                                    </div>

                                    <div class="text-center my-3 pb-3 border-bottom">
                                        <div class="h3 font-weight-bold text-dark mb-0">
                                            Rp <?= number_format($plan['price'], 0, ',', '.') ?>
                                        </div>
                                        <span class="text-muted small">/ bulan (30 hari)</span>
                                    </div>

                                    <p class="text-muted small text-center mb-3" style="min-height: 40px;">
                                        <?= esc($plan['description']) ?>
                                    </p>

                                    <ul class="list-unstyled small mb-4 flex-grow-1" style="line-height: 1.6;">
                                        <?php foreach ($features as $feat): ?>
                                            <li class="mb-2 d-flex align-items-center">
                                                <i class="fas fa-check-circle text-teal mr-2 flex-shrink-0"></i>
                                                <span class="text-secondary"><?= esc($feat) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                    <div class="mt-auto">
                                        <?php if ($isCurrent): ?>
                                            <button class="btn btn-outline-success btn-block font-weight-bold" disabled>
                                                <i class="fas fa-check mr-1"></i> Paket Sedang Aktif
                                            </button>
                                        <?php else: ?>
                                            <a href="<?= base_url('/provider/subscription/checkout/' . $plan['id']) ?>" class="btn <?= $isPopular ? 'btn-teal text-white' : 'btn-outline-teal' ?> btn-block font-weight-bold shadow-sm">
                                                Pilih Paket Ini
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- TAB 2: LISTING FEE 15 HARI -->
            <div class="tab-pane fade" id="tab-listingfee" role="tabpanel" aria-labelledby="listingfee-tab">
                <div class="row">
                    <?php foreach ($listingFeePlans as $plan): ?>
                        <?php 
                            $isCurrent = ($quota['plan_slug'] === $plan['slug'] && $quota['status'] === 'active');
                            $features = !empty($plan['features']) ? json_decode($plan['features'], true) : [];
                            $isPopularFee = ($plan['slug'] === 'listing-fee-20');
                        ?>
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card h-100 shadow-sm border pricing-card position-relative <?= $isPopularFee ? 'popular-plan' : '' ?> <?= $isCurrent ? 'current-plan' : '' ?>" style="border-radius: 10px;">
                                <?php if ($isCurrent): ?>
                                    <div class="badge-plan-status bg-success text-white">
                                        <i class="fas fa-check-circle mr-1"></i> PAKET AKTIF
                                    </div>
                                <?php elseif ($isPopularFee): ?>
                                    <div class="badge-plan-status bg-teal text-white">
                                        <i class="fas fa-star mr-1"></i> REKOMENDASI
                                    </div>
                                <?php endif; ?>

                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="text-center mb-3">
                                        <h5 class="font-weight-bold text-dark mb-1"><?= esc($plan['name']) ?></h5>
                                        <span class="badge badge-light border text-teal px-2 py-1 font-weight-bold" style="font-size: 0.75rem;">
                                            <i class="fas fa-ticket-alt mr-1"></i> Kuota <?= esc($plan['listing_capacity']) ?> Listing Aktif
                                        </span>
                                    </div>

                                    <div class="text-center my-3 pb-3 border-bottom">
                                        <div class="h3 font-weight-bold text-dark mb-0">
                                            Rp <?= number_format($plan['price'], 0, ',', '.') ?>
                                        </div>
                                        <span class="text-muted small">/ 15 hari kalender</span>
                                    </div>

                                    <p class="text-muted small text-center mb-3" style="min-height: 40px;">
                                        <?= esc($plan['description']) ?>
                                    </p>

                                    <ul class="list-unstyled small mb-4 flex-grow-1" style="line-height: 1.6;">
                                        <?php foreach ($features as $feat): ?>
                                            <li class="mb-2 d-flex align-items-center">
                                                <i class="fas fa-check-circle text-teal mr-2 flex-shrink-0"></i>
                                                <span class="text-secondary"><?= esc($feat) ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                    <div class="mt-auto">
                                        <?php if ($isCurrent): ?>
                                            <button class="btn btn-outline-success btn-block font-weight-bold" disabled>
                                                <i class="fas fa-check mr-1"></i> Paket Sedang Aktif
                                            </button>
                                        <?php else: ?>
                                            <a href="<?= base_url('/provider/subscription/checkout/' . $plan['id']) ?>" class="btn <?= $isPopularFee ? 'btn-teal text-white' : 'btn-outline-teal' ?> btn-block font-weight-bold shadow-sm">
                                                Pilih Slot Ini
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- 4. RIWAYAT TAGIHAN & INVOICE -->
<div class="card card-outline card-teal shadow-sm">
    <div class="card-header border-0 pt-4 px-4 pb-2 bg-white">
        <div>
            <h3 class="card-title font-weight-bold text-dark mb-1 float-none d-block" style="font-size: 1.25rem; float: none !important;">
                <i class="fas fa-file-invoice-dollar text-teal mr-2"></i> Riwayat Tagihan & Pembayaran Paket
            </h3>
            <p class="text-muted small mb-0">Daftar transaksi pembelian paket langganan dan listing fee Anda.</p>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover m-0 text-sm align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="pl-4">No. Invoice</th>
                        <th>Paket Layanan</th>
                        <th>Nominal</th>
                        <th>Tanggal Terbit</th>
                        <th>Jatuh Tempo</th>
                        <th>Status</th>
                        <th class="text-right pr-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-receipt fa-3x mb-2 text-muted d-block opacity-50"></i>
                                Belum ada riwayat tagihan paket berbayar.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                            <?php 
                                $isExpired = ($inv['status'] === 'expired') || ($inv['status'] === 'pending' && strtotime($inv['due_date']) < strtotime(date('Y-m-d')));
                            ?>
                            <tr>
                                <td class="pl-4 font-weight-bold text-dark">
                                    <?= esc($inv['invoice_number']) ?>
                                    <span class="d-block text-muted text-xs" style="font-family: monospace;"><?= esc($inv['order_id']) ?></span>
                                </td>
                                <td>
                                    <strong><?= esc($inv['plan_name']) ?></strong>
                                    <span class="d-block text-muted text-xs">
                                        <?= ($inv['billing_cycle'] === 'monthly') ? 'Bulanan (30 hari)' : '15 Harian' ?> &bull; Kuota <?= esc($inv['listing_capacity'] >= 999 ? 'Unlimited' : $inv['listing_capacity']) ?> listing
                                    </span>
                                </td>
                                <td class="font-weight-bold text-dark">
                                    Rp <?= number_format($inv['amount'], 0, ',', '.') ?>
                                </td>
                                <td>
                                    <?= date('d M Y, H:i', strtotime($inv['created_at'])) ?>
                                </td>
                                <td>
                                    <?= date('d M Y', strtotime($inv['due_date'])) ?>
                                </td>
                                <td>
                                    <?php if ($inv['status'] === 'paid'): ?>
                                        <span class="badge badge-success px-2 py-1">
                                            <i class="fas fa-check-circle mr-1"></i> LUNAS
                                        </span>
                                        <?php if (!empty($inv['payment_method'])): ?>
                                            <span class="d-block text-muted text-xs mt-1 text-uppercase">
                                                <i class="fas fa-wallet mr-1 text-teal"></i> <?= esc(str_replace('_', ' ', $inv['payment_method'])) ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php elseif ($isExpired): ?>
                                        <span class="badge badge-danger px-2 py-1">
                                            <i class="fas fa-calendar-times mr-1"></i> KADALUARSA
                                        </span>
                                        <span class="d-block text-danger text-xs mt-1">
                                            Lewat Jatuh Tempo
                                        </span>
                                    <?php elseif ($inv['status'] === 'pending'): ?>
                                        <span class="badge badge-warning text-dark px-2 py-1">
                                            <i class="fas fa-clock mr-1"></i> MENUNGGU PEMBAYARAN
                                        </span>
                                        <span class="d-block text-muted text-xs mt-1">
                                            <i class="fas fa-hourglass-half text-warning mr-1"></i> S.d. <?= date('d M Y', strtotime($inv['due_date'])) ?>
                                        </span>
                                    <?php elseif ($inv['status'] === 'cancelled'): ?>
                                        <span class="badge badge-secondary px-2 py-1">
                                            <i class="fas fa-times-circle mr-1"></i> DIBATALKAN
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-1">
                                            <?= strtoupper(esc($inv['status'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right pr-4">
                                    <?php if ($inv['status'] === 'paid'): ?>
                                        <button type="button" class="btn btn-outline-teal btn-xs font-weight-bold px-2 shadow-sm" data-toggle="modal" data-target="#receiptModal-<?= $inv['id'] ?>" data-bs-toggle="modal" data-bs-target="#receiptModal-<?= $inv['id'] ?>" title="Lihat Bukti Pembayaran">
                                            <i class="fas fa-receipt mr-1"></i> Resi
                                        </button>
                                        <a href="<?= base_url('/provider/subscription/invoice/' . $inv['id']) ?>" target="_blank" class="btn btn-outline-secondary btn-xs font-weight-bold px-2 ml-1" title="Cetak / Simpan Invoice Resmi (PDF)">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    <?php elseif ($isExpired): ?>
                                        <button type="button" class="btn btn-secondary btn-xs px-2" disabled title="Tagihan telah melewati batas waktu pembayaran">
                                            <i class="fas fa-ban mr-1"></i> Kadaluarsa
                                        </button>
                                        <button type="button" class="btn btn-outline-danger btn-xs px-2 ml-1" title="Hapus / Batalkan Tagihan" onclick="cancelPendingInvoice(<?= $inv['id'] ?>, '<?= esc($inv['invoice_number']) ?>')">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    <?php elseif ($inv['status'] === 'pending'): ?>
                                        <button type="button" class="btn btn-teal btn-xs text-white font-weight-bold px-3 shadow-sm" data-toggle="modal" data-target="#payModal-<?= $inv['id'] ?>" data-bs-toggle="modal" data-bs-target="#payModal-<?= $inv['id'] ?>">
                                            <i class="fas fa-credit-card mr-1"></i> Bayar
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary btn-xs px-2 ml-1" title="Batalkan Tagihan" onclick="cancelPendingInvoice(<?= $inv['id'] ?>, '<?= esc($inv['invoice_number']) ?>')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="fas fa-ban mr-1"></i> Selesai</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ================= MODALS & ACTION DIALOGS ================= -->
<?php if (!empty($invoices)): ?>
    <?php foreach ($invoices as $inv): ?>
        <?php 
            $isExpired = ($inv['status'] === 'expired') || ($inv['status'] === 'pending' && strtotime($inv['due_date']) < strtotime(date('Y-m-d')));
        ?>

        <!-- 1. MODAL PEMBAYARAN INVOICE (PENDING) -->
        <?php if ($inv['status'] === 'pending' && !$isExpired): ?>
            <div class="modal fade" id="payModal-<?= $inv['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="payModalLabel-<?= $inv['id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                        <div class="modal-header bg-teal text-white border-0 py-3 px-4">
                            <div>
                                <h5 class="modal-title font-weight-bold mb-0" id="payModalLabel-<?= $inv['id'] ?>">
                                    <i class="fas fa-cash-register mr-2"></i> Konfirmasi & Pembayaran Tagihan
                                </h5>
                                <small class="text-white-50">Invoice: <strong><?= esc($inv['invoice_number']) ?></strong> &bull; Order ID: <?= esc($inv['order_id']) ?></small>
                            </div>
                            <button type="button" class="close text-white opacity-75" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        
                        <form action="<?= base_url('/provider/subscription/simulate-execute/' . $inv['id']) ?>" method="POST" id="formPay-<?= $inv['id'] ?>">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="success">
                            <div class="modal-body p-4 text-left">
                                <!-- Ringkasan Paket & Tagihan -->
                                <div class="p-3 bg-light rounded border mb-4">
                                    <div class="row align-items-center">
                                        <div class="col-sm-7 border-right mb-3 mb-sm-0">
                                            <span class="badge badge-teal text-white px-2 py-1 text-uppercase font-weight-bold mb-1" style="font-size: 0.7rem;">
                                                <?= ($inv['plan_type'] === 'subscription') ? 'Langganan Bulanan' : 'Listing Fee 15 Hari' ?>
                                            </span>
                                            <h5 class="font-weight-bold text-dark mb-1"><?= esc($inv['plan_name']) ?></h5>
                                            <div class="text-muted small">
                                                <i class="fas fa-layer-group text-teal mr-1"></i> Kapasitas: <strong><?= esc($inv['listing_capacity'] >= 999 ? 'Unlimited (>100)' : $inv['listing_capacity']) ?> Listing Aktif</strong>
                                                &bull; 
                                                <i class="fas fa-calendar-day text-teal mr-1"></i> Durasi: <strong><?= ($inv['billing_cycle'] === 'monthly') ? '30 Hari' : '15 Hari' ?></strong>
                                            </div>
                                            <div class="text-muted text-xs mt-2">
                                                <i class="fas fa-clock text-warning mr-1"></i> Batas Jatuh Tempo: <strong><?= date('d M Y', strtotime($inv['due_date'])) ?></strong>
                                            </div>
                                        </div>
                                        <div class="col-sm-5 text-sm-right pl-sm-4">
                                            <span class="text-muted small d-block mb-1">Total yang Harus Dibayar:</span>
                                            <div class="h3 font-weight-bold text-teal mb-0">
                                                Rp <?= number_format($inv['amount'], 0, ',', '.') ?>
                                            </div>
                                            <span class="badge badge-warning text-dark text-xs px-2 py-1 mt-1 font-weight-bold">
                                                <i class="fas fa-clock mr-1"></i> MENUNGGU PEMBAYARAN
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Pilihan Metode Pembayaran Digital -->
                                <h6 class="font-weight-bold text-dark mb-2">
                                    <i class="fas fa-wallet text-teal mr-1"></i> Pilih Metode Pembayaran Digital:
                                </h6>
                                <div class="row mb-3">
                                    <div class="col-sm-6 mb-2">
                                        <label class="d-block p-3 border rounded cursor-pointer bg-white payment-radio-card" for="pm_bsi_<?= $inv['id'] ?>" style="cursor: pointer;">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="pm_bsi_<?= $inv['id'] ?>" name="payment_method" value="bsi_va" checked class="custom-control-input">
                                                <label class="custom-control-label font-weight-bold text-dark" for="pm_bsi_<?= $inv['id'] ?>">
                                                    BSI Virtual Account
                                                </label>
                                            </div>
                                            <small class="text-muted ml-4 d-block">Bank Syariah Indonesia &bull; Bebas Riba & Otomatis</small>
                                        </label>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <label class="d-block p-3 border rounded cursor-pointer bg-white payment-radio-card" for="pm_bca_<?= $inv['id'] ?>" style="cursor: pointer;">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="pm_bca_<?= $inv['id'] ?>" name="payment_method" value="bca_va" class="custom-control-input">
                                                <label class="custom-control-label font-weight-bold text-dark" for="pm_bca_<?= $inv['id'] ?>">
                                                    BCA Virtual Account
                                                </label>
                                            </div>
                                            <small class="text-muted ml-4 d-block">Bank Central Asia &bull; Verifikasi Real-time</small>
                                        </label>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <label class="d-block p-3 border rounded cursor-pointer bg-white payment-radio-card" for="pm_mandiri_<?= $inv['id'] ?>" style="cursor: pointer;">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="pm_mandiri_<?= $inv['id'] ?>" name="payment_method" value="mandiri_va" class="custom-control-input">
                                                <label class="custom-control-label font-weight-bold text-dark" for="pm_mandiri_<?= $inv['id'] ?>">
                                                    Mandiri Virtual Account
                                                </label>
                                            </div>
                                            <small class="text-muted ml-4 d-block">Bank Mandiri Livin' &bull; Verifikasi Otomatis</small>
                                        </label>
                                    </div>
                                    <div class="col-sm-6 mb-2">
                                        <label class="d-block p-3 border rounded cursor-pointer bg-white payment-radio-card" for="pm_qris_<?= $inv['id'] ?>" style="cursor: pointer;">
                                            <div class="custom-control custom-radio">
                                                <input type="radio" id="pm_qris_<?= $inv['id'] ?>" name="payment_method" value="qris" class="custom-control-input">
                                                <label class="custom-control-label font-weight-bold text-dark" for="pm_qris_<?= $inv['id'] ?>">
                                                    QRIS (Semua Bank & E-Wallet)
                                                </label>
                                            </div>
                                            <small class="text-muted ml-4 d-block">GoPay, OVO, Dana, ShopeePay, LinkAja</small>
                                        </label>
                                    </div>
                                </div>

                                <!-- Fitur Snap / Sandbox Alert -->
                                <div class="alert alert-light border p-3 rounded mb-0">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between">
                                        <div class="mb-2 mb-sm-0">
                                            <span class="font-weight-bold text-dark d-block">
                                                <i class="fas fa-shield-alt text-teal mr-1"></i> Sandbox Gateway & Auto Verifikasi
                                            </span>
                                            <small class="text-muted">Sistem akan memproses pembayaran dan menambahkan kuota iklan secara instan.</small>
                                        </div>
                                        <?php if (!empty($inv['snap_token']) && !str_contains($inv['snap_token'], 'mock')): ?>
                                            <button type="button" class="btn btn-outline-teal btn-sm font-weight-bold" onclick="triggerSnapPayment('<?= esc($inv['snap_token']) ?>')">
                                                <i class="fas fa-bolt mr-1"></i> Buka Midtrans Snap
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                                <div>
                                    <button type="button" class="btn btn-outline-danger btn-sm font-weight-bold" onclick="cancelPendingInvoice(<?= $inv['id'] ?>, '<?= esc($inv['invoice_number']) ?>')">
                                        <i class="fas fa-times mr-1"></i> Batalkan Tagihan
                                    </button>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-secondary btn-sm px-3 mr-2 font-weight-bold" data-dismiss="modal" data-bs-dismiss="modal">
                                        Tutup
                                    </button>
                                    <button type="button" id="btnPaySubmit-<?= $inv['id'] ?>" class="btn btn-teal text-white btn-sm px-4 font-weight-bold shadow-sm" onclick="submitPaymentSimulation(<?= $inv['id'] ?>)">
                                        <i class="fas fa-check-circle mr-1"></i> Bayar Sekarang (Simulasi Lunas)
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- 2. MODAL RESI / BUKTI BAYAR (PAID) -->
        <?php if ($inv['status'] === 'paid'): ?>
            <div class="modal fade" id="receiptModal-<?= $inv['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="receiptModalLabel-<?= $inv['id'] ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                        <div class="modal-header bg-success text-white border-0 py-3 px-4">
                            <div>
                                <h5 class="modal-title font-weight-bold mb-0" id="receiptModalLabel-<?= $inv['id'] ?>">
                                    <i class="fas fa-receipt mr-2"></i> Bukti Pembayaran Resmi
                                </h5>
                                <small class="text-white-50">KosAja Billing Receipt &bull; Terverifikasi Sah</small>
                            </div>
                            <button type="button" class="close text-white opacity-75" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body p-4 text-left">
                            <div class="text-center pb-3 border-bottom mb-3">
                                <div class="font-weight-bold" style="font-size: 1.5rem; color: #0d9488;">Kos<span class="text-warning">Aja</span></div>
                                <span class="badge badge-success px-3 py-1 font-weight-bold text-uppercase mt-1" style="font-size: 0.75rem; letter-spacing: 1px;">
                                    <i class="fas fa-check-circle mr-1"></i> LUNAS TERVERIFIKASI
                                </span>
                                <div class="h3 font-weight-bold text-dark mt-2 mb-0">
                                    Rp <?= number_format($inv['amount'], 0, ',', '.') ?>
                                </div>
                                <small class="text-muted"><?= date('d F Y, H:i', strtotime($inv['paid_at'] ?? $inv['updated_at'] ?? $inv['created_at'])) ?> WIB</small>
                            </div>

                            <div class="small">
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Nomor Invoice</span>
                                    <strong class="text-dark font-monospace"><?= esc($inv['invoice_number']) ?></strong>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Order ID</span>
                                    <code class="text-dark"><?= esc($inv['order_id']) ?></code>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Paket Layanan</span>
                                    <strong class="text-dark"><?= esc($inv['plan_name']) ?></strong>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Tipe & Siklus</span>
                                    <span class="text-dark"><?= ($inv['billing_cycle'] === 'monthly') ? 'Langganan Bulanan (30 Hari)' : 'Listing Fee (15 Hari)' ?></span>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Kapasitas Slot Listing</span>
                                    <strong class="text-teal">+<?= esc($inv['listing_capacity'] >= 999 ? 'Unlimited' : $inv['listing_capacity']) ?> Listing Aktif</strong>
                                </div>
                                <div class="d-flex justify-content-between py-1 border-bottom">
                                    <span class="text-muted">Metode Pembayaran</span>
                                    <strong class="text-dark text-uppercase"><?= esc(str_replace('_', ' ', $inv['payment_method'] ?? 'Transfer Digital')) ?></strong>
                                </div>
                                <?php if (!empty($inv['gateway_trx_id'])): ?>
                                    <div class="d-flex justify-content-between py-1 border-bottom">
                                        <span class="text-muted">Ref. Transaksi Gateway</span>
                                        <span class="text-dark font-monospace"><?= esc($inv['gateway_trx_id']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="alert alert-success border-0 p-3 rounded mt-3 mb-0 text-center small" style="background-color: #f0fdf4; color: #15803d;">
                                <i class="fas fa-check-double mr-1"></i> Kuota listing telah berhasil aktif di akun Mitra Anda.
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0 py-3 px-4 d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary btn-sm px-3 font-weight-bold" data-dismiss="modal" data-bs-dismiss="modal">
                                Tutup
                            </button>
                            <a href="<?= base_url('/provider/subscription/invoice/' . $inv['id']) ?>" target="_blank" class="btn btn-teal text-white btn-sm px-4 font-weight-bold shadow-sm">
                                <i class="fas fa-print mr-1"></i> Cetak / Simpan PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- 3. HIDDEN CANCEL FORM -->
        <form id="cancelForm-<?= $inv['id'] ?>" action="<?= base_url('/provider/subscription/cancel/' . $inv['id']) ?>" method="POST" style="display: none;">
            <?= csrf_field() ?>
        </form>

    <?php endforeach; ?>
<?php endif; ?>

<style>
    /* KosAja Teal Design Tokens & Proportional Layout */
    .card-header .card-title.float-none {
        float: none !important;
        display: block !important;
        width: 100%;
    }
    .pricing-card {
        border-color: #e2e8f0 !important;
        border-top: 3px solid #cbd5e1 !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .pricing-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08) !important;
    }
    .pricing-card.popular-plan {
        border-color: #0d9488 !important;
        border-top: 4px solid #0d9488 !important;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.12) !important;
    }
    .pricing-card.current-plan {
        border-color: #10b981 !important;
        border-top: 4px solid #10b981 !important;
    }
    .badge-plan-status {
        position: absolute;
        top: -12px;
        left: 50%;
        transform: translateX(-50%);
        border-radius: 20px;
        padding: 3px 14px;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        z-index: 2;
    }
    .btn-outline-teal {
        color: #0d9488;
        border-color: #0d9488;
        background-color: transparent;
    }
    .btn-outline-teal:hover {
        background-color: #0d9488;
        color: #ffffff;
    }
    #billingTabs .nav-link {
        color: #475569;
        background-color: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-right: 6px;
        transition: all 0.2s ease;
    }
    #billingTabs .nav-link:hover {
        color: #0d9488;
        background-color: #e6fffa;
    }
    #billingTabs .nav-link.active, #billingTabs .show > .nav-link {
        background-color: #0d9488 !important;
        border-color: #0d9488 !important;
        color: #ffffff !important;
    }
    .payment-radio-card {
        transition: all 0.2s ease;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
    }
    .payment-radio-card:hover {
        border-color: #0d9488 !important;
        background-color: #f0fdf4 !important;
    }
    .modal {
        z-index: 1060 !important;
    }
    .modal-backdrop {
        z-index: 1050 !important;
    }
    .modal-content {
        border-radius: 12px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<?php if (!empty($paymentConfig->clientKey) && !str_contains($paymentConfig->clientKey, 'demo')): ?>
    <script src="<?= $paymentConfig->isProduction ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' ?>" data-client-key="<?= esc($paymentConfig->clientKey) ?>"></script>
<?php endif; ?>
<script>
function submitPaymentSimulation(invoiceId) {
    var form = document.getElementById('formPay-' + invoiceId);
    if (form) {
        var btn = document.getElementById('btnPaySubmit-' + invoiceId);
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> Memproses...';
        }
        form.submit();
    } else {
        console.error('Payment form not found for invoice id: ' + invoiceId);
    }
}

function cancelPendingInvoice(invoiceId, invoiceNumber) {
    if (confirm('Apakah Anda yakin ingin membatalkan pesanan tagihan #' + invoiceNumber + '? Status tagihan akan diubah menjadi Dibatalkan.')) {
        var form = document.getElementById('cancelForm-' + invoiceId);
        if (form) {
            form.submit();
        } else {
            console.error('Cancel form not found for invoice id: ' + invoiceId);
        }
    }
}

function triggerSnapPayment(snapToken) {
    if (window.snap && typeof window.snap.pay === 'function') {
        window.snap.pay(snapToken, {
            onSuccess: function(result) {
                window.location.reload();
            },
            onPending: function(result) {
                window.location.reload();
            },
            onError: function(result) {
                alert('Pembayaran tidak berhasil atau dibatalkan.');
            },
            onClose: function() {
                console.log('Dialog pembayaran ditutup tanpa menyelesaikan transaksi.');
            }
        });
    } else {
        alert('Fitur Midtrans Snap SDK saat ini aktif dalam mode simulasi. Silakan gunakan tombol "Bayar Sekarang (Simulasi Lunas)" di dalam modal.');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Relocate modals to document.body to prevent backdrop stacking context traps in AdminLTE
    document.querySelectorAll('.modal').forEach(function(modal) {
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
    });

    var subscriptionTab = document.getElementById('subscription-tab');
    var listingfeeTab = document.getElementById('listingfee-tab');
    var subscriptionPane = document.getElementById('tab-subscription');
    var listingfeePane = document.getElementById('tab-listingfee');

    function switchTab(target, shouldScroll) {
        var isListingFee = (target === '#tab-listingfee' || target === 'tab-listingfee' || target === '#listingfee-tab' || target === 'listingfee-tab');
        
        var activeTab = isListingFee ? listingfeeTab : subscriptionTab;
        var inactiveTab = isListingFee ? subscriptionTab : listingfeeTab;
        var activePane = isListingFee ? listingfeePane : subscriptionPane;
        var inactivePane = isListingFee ? subscriptionPane : listingfeePane;

        if (!activeTab || !activePane) return;

        // 1. Bootstrap 5 Tab instance if supported
        if (window.bootstrap && window.bootstrap.Tab) {
            try {
                var bsTab = bootstrap.Tab.getOrCreateInstance(activeTab);
                bsTab.show();
            } catch (e) {}
        } else if (window.$ && typeof $(activeTab).tab === 'function') {
            try {
                $(activeTab).tab('show');
            } catch (e) {}
        }

        // 2. Direct DOM class manipulation fallback to guarantee visual activation
        if (inactiveTab) {
            inactiveTab.classList.remove('active');
            inactiveTab.setAttribute('aria-selected', 'false');
        }
        activeTab.classList.add('active');
        activeTab.setAttribute('aria-selected', 'true');

        if (inactivePane) {
            inactivePane.classList.remove('show', 'active');
        }
        activePane.classList.add('show', 'active');

        // 3. Update URL hash without causing a page jump
        var hashVal = isListingFee ? '#tab-listingfee' : '#tab-subscription';
        if (window.location.hash !== hashVal) {
            if (window.history && window.history.replaceState) {
                window.history.replaceState(null, null, hashVal);
            } else {
                window.location.hash = hashVal;
            }
        }

        // 4. Smooth scroll to catalog section if initiated by deep link
        if (shouldScroll) {
            var katalog = document.getElementById('katalog-paket');
            if (katalog) {
                katalog.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }

    // Check hash on initial page load
    var initialHash = window.location.hash;
    if (initialHash === '#tab-listingfee' || initialHash === '#listingfee-tab') {
        switchTab('#tab-listingfee', true);
    } else if (initialHash === '#tab-subscription' || initialHash === '#subscription-tab') {
        switchTab('#tab-subscription', false);
    }

    // Respond to browser back/forward or hash changes
    window.addEventListener('hashchange', function() {
        var h = window.location.hash;
        if (h === '#tab-listingfee' || h === '#listingfee-tab') {
            switchTab('#tab-listingfee', true);
        } else if (h === '#tab-subscription' || h === '#subscription-tab') {
            switchTab('#tab-subscription', false);
        }
    });

    // Tab button click handlers
    if (subscriptionTab) {
        subscriptionTab.addEventListener('click', function(e) {
            e.preventDefault();
            switchTab('#tab-subscription', false);
        });
    }

    if (listingfeeTab) {
        listingfeeTab.addEventListener('click', function(e) {
            e.preventDefault();
            switchTab('#tab-listingfee', false);
        });
    }

    // Any in-page anchor linking to #tab-listingfee
    document.querySelectorAll('a[href="#tab-listingfee"]').forEach(function(link) {
        if (link.id !== 'listingfee-tab') {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                switchTab('#tab-listingfee', true);
            });
        }
    });
});
</script>
<?= $this->endSection() ?>
