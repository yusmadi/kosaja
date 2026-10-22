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
            <div>
                <h3 class="card-title font-weight-bold mb-1" style="font-size: 1.25rem;">
                    <i class="fas fa-tags text-teal mr-2"></i> Pilih Paket Layanan Penyedia
                </h3>
                <p class="text-muted small mb-0 d-block">Tingkatkan performa kos Anda dengan paket langganan bulanan hemat atau slot listing 15 harian fleksibel.</p>
            </div>
            <!-- Pill Navigation Tabs -->
            <ul class="nav nav-pills mt-3 mt-md-0" id="billingTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold px-3 py-2 text-sm" id="subscription-tab" data-toggle="pill" href="#tab-subscription" role="tab">
                        <i class="fas fa-calendar-alt mr-1"></i> Langganan Bulanan (30 Hari)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold px-3 py-2 text-sm" id="listingfee-tab" data-toggle="pill" href="#tab-listingfee" role="tab">
                        <i class="fas fa-ticket-alt mr-1"></i> Listing Fee (15 Hari)
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="card-body p-4">
        <div class="tab-content" id="billingTabContent">
            
            <!-- TAB 1: SUBSCRIPTION BULANAN -->
            <div class="tab-pane fade show active" id="tab-subscription" role="tabpanel">
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
            <div class="tab-pane fade" id="tab-listingfee" role="tabpanel">
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
    <div class="card-header border-0 d-flex justify-content-between align-items-center bg-white">
        <div>
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-file-invoice-dollar text-teal mr-2"></i> Riwayat Tagihan & Pembayaran Paket
            </h3>
            <span class="text-muted small d-block mt-1">Daftar transaksi pembelian paket langganan dan listing fee Anda.</span>
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
                            <tr>
                                <td class="pl-4 font-weight-bold text-dark">
                                    <?= esc($inv['invoice_number']) ?>
                                    <span class="d-block text-muted text-xs" style="font-family: monospace;"><?= esc($inv['order_id']) ?></span>
                                </td>
                                <td>
                                    <strong><?= esc($inv['plan_name']) ?></strong>
                                    <span class="d-block text-muted text-xs">
                                        <?= ($inv['billing_cycle'] === 'monthly') ? 'Bulanan (30 hari)' : '15 Harian' ?> &bull; Kuota <?= esc($inv['listing_capacity']) ?> listing
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
                                    <?php elseif ($inv['status'] === 'pending'): ?>
                                        <span class="badge badge-warning text-dark px-2 py-1">
                                            <i class="fas fa-clock mr-1"></i> MENUNGGU PEMBAYARAN
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-1">
                                            <?= strtoupper(esc($inv['status'])) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right pr-4">
                                    <?php if ($inv['status'] === 'pending'): ?>
                                        <a href="<?= base_url('/provider/subscription/simulate/' . $inv['id']) ?>" class="btn btn-teal btn-xs text-white font-weight-bold px-3">
                                            <i class="fas fa-credit-card mr-1"></i> Bayar
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small"><i class="fas fa-check text-success mr-1"></i> Selesai</span>
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

<style>
    /* KosAja Teal Design Tokens & Proportional Layout */
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
    .nav-pills .nav-link {
        color: #475569;
        background-color: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        margin-right: 6px;
        transition: all 0.2s ease;
    }
    .nav-pills .nav-link:hover {
        color: #0d9488;
        background-color: #e6fffa;
    }
    .nav-pills .nav-link.active, .nav-pills .show > .nav-link {
        background-color: #0d9488 !important;
        border-color: #0d9488 !important;
        color: #ffffff !important;
    }
</style>
<?= $this->endSection() ?>
