<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-4">
    <div class="row align-items-center">
        <div class="col-sm-7">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-crown text-warning mr-2"></i> Paket & Langganan Mitra
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

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- 1. GRACE PERIOD WARNING BANNER (IF APPLICABLE) -->
<?php if (!empty($quota['is_in_grace_period'])): ?>
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%); border-left: 5px solid #f59e0b !important;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                <div class="d-flex align-items-start mb-3 mb-md-0">
                    <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fas fa-hourglass-half"></i>
                    </div>
                    <div>
                        <h5 class="font-weight-bold text-dark mb-1">
                            Masa Transisi (Grace Period) Berlangsung: Sisa <?= esc($quota['days_remaining']) ?> Hari!
                        </h5>
                        <p class="text-secondary small mb-0">
                            Sewa pertama Anda telah berhasil lunas. Sesuai ketentuan PRD KosAja (FR-PM-004), silakan pilih paket langganan atau listing fee berbayar sebelum tanggal <strong><?= date('d M Y', strtotime($quota['end_date'])) ?></strong> agar sisa unit kamar iklan Anda tetap aktif di marketplace.
                        </p>
                    </div>
                </div>
                <div class="text-md-right flex-shrink-0 ml-md-3">
                    <a href="#katalog-paket" class="btn btn-warning text-dark font-weight-bold shadow-sm px-4">
                        <i class="fas fa-bolt mr-1"></i> Pilih Paket Berbayar
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php elseif ($quota['status'] === 'expired'): ?>
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%); border-left: 5px solid #ef4444 !important;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                <div class="d-flex align-items-start mb-3 mb-md-0">
                    <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center mr-3 flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.3rem;">
                        <i class="fas fa-ban"></i>
                    </div>
                    <div>
                        <h5 class="font-weight-bold text-danger mb-1">
                            Masa Aktif Paket Telah Berakhir
                        </h5>
                        <p class="text-secondary small mb-0">
                            Listing iklan unit yang belum tersewa telah dinonaktifkan sementara. Aktifkan paket langganan atau listing fee untuk memulihkan dan mempublikasikan unit kamar Anda kembali.
                        </p>
                    </div>
                </div>
                <div class="text-md-right flex-shrink-0 ml-md-3">
                    <a href="#katalog-paket" class="btn btn-danger font-weight-bold shadow-sm px-4">
                        <i class="fas fa-redo mr-1"></i> Re-Aktivasi Paket
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- 2. STATUS CARD & CAPACITY METER -->
<div class="row mb-4">
    <div class="col-lg-6 mb-3 mb-lg-0">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Paket Aktif Saat Ini</span>
                        <h4 class="font-weight-bold text-dark mt-1 mb-0">
                            <?= esc($quota['plan_name']) ?>
                        </h4>
                    </div>
                    <div>
                        <?php if ($quota['status'] === 'active'): ?>
                            <span class="badge badge-success px-3 py-2 text-uppercase" style="font-size: 0.75rem;">
                                <i class="fas fa-check-circle mr-1"></i> Aktif
                            </span>
                        <?php elseif ($quota['status'] === 'grace_period'): ?>
                            <span class="badge badge-warning text-dark px-3 py-2 text-uppercase" style="font-size: 0.75rem;">
                                <i class="fas fa-hourglass-half mr-1"></i> Masa Transisi
                            </span>
                        <?php else: ?>
                            <span class="badge badge-danger px-3 py-2 text-uppercase" style="font-size: 0.75rem;">
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
                        <i class="far fa-calendar-alt mr-1"></i> 
                        <?php if (!empty($quota['end_date'])): ?>
                            Masa aktif berlaku sampai: <strong><?= date('d F Y', strtotime($quota['end_date'])) ?></strong>
                        <?php else: ?>
                            Belum ada masa aktif berbayar.
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">Penggunaan Kapasitas Listing</span>
                <div class="d-flex justify-content-between align-items-baseline mt-1 mb-2">
                    <h3 class="font-weight-bold text-dark mb-0">
                        <?= esc($publishedCount) ?> <span class="text-muted" style="font-size: 1rem; font-weight: 500;">/ <?= esc($quota['capacity_limit']) ?> Listing Aktif</span>
                    </h3>
                    <?php 
                        $cap = max(1, (int) $quota['capacity_limit']);
                        $pct = min(100, (int) round(($publishedCount / $cap) * 100));
                        $isFull = ($publishedCount >= $quota['capacity_limit']);
                    ?>
                    <span class="badge <?= $isFull ? 'badge-danger' : 'badge-teal' ?> px-2 py-1">
                        <?= $pct ?>% Terpakai
                    </span>
                </div>

                <div class="progress mb-3" style="height: 10px; border-radius: 6px; background-color: #f1f5f9;">
                    <div class="progress-bar <?= $isFull ? 'bg-danger' : 'bg-teal' ?>" role="progressbar" style="width: <?= $pct ?>%;" aria-valuenow="<?= $pct ?>" aria-valuemin="0" aria-valuemax="100"></div>
                </div>

                <div class="p-3 bg-light rounded text-muted small">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Total Iklan Terdaftar: <strong><?= esc($totalListings) ?> unit</strong></span>
                        <span>Iklan Terpublikasi: <strong><?= esc($publishedCount) ?> unit</strong></span>
                    </div>
                    <?php if ($isFull): ?>
                        <div class="text-danger mt-2 font-weight-bold">
                            <i class="fas fa-exclamation-circle mr-1"></i> Kuota listing Anda telah penuh! Upgrade paket untuk menambah listing baru.
                        </div>
                    <?php else: ?>
                        <div class="text-success mt-2">
                            <i class="fas fa-check mr-1"></i> Anda masih memiliki sisa <strong><?= max(0, $quota['capacity_limit'] - $publishedCount) ?> slot listing</strong> yang dapat dipublikasikan.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. KATALOG PAKET BERBAYAR -->
<div id="katalog-paket" class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <div>
                <h4 class="font-weight-bold text-dark mb-1">
                    <i class="fas fa-tags text-teal mr-2"></i> Pilih Paket Layanan Penyedia
                </h4>
                <p class="text-muted small mb-0">Pilih antara fleksibilitas slot 15-harian atau kenyamanan langganan bulanan tanpa repot.</p>
            </div>
            <!-- Pill Navigation Tabs -->
            <ul class="nav nav-pills mt-3 mt-md-0" id="billingTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold px-3 py-2" id="subscription-tab" data-toggle="pill" href="#tab-subscription" role="tab">
                        <i class="fas fa-calendar-alt mr-1"></i> Langganan Bulanan (30 Hari)
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold px-3 py-2" id="listingfee-tab" data-toggle="pill" href="#tab-listingfee" role="tab">
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
                            <div class="card h-100 border shadow-sm position-relative <?= $isPopular ? 'border-teal' : '' ?>" style="border-radius: 12px; transition: transform 0.2s; <?= $isPopular ? 'box-shadow: 0 10px 25px rgba(13, 148, 136, 0.15) !important;' : '' ?>">
                                <?php if ($isPopular): ?>
                                    <div class="position-absolute text-center text-white font-weight-bold py-1 px-3" style="top: -12px; left: 50%; transform: translateX(-50%); background: #0d9488; border-radius: 20px; font-size: 0.7rem; letter-spacing: 0.5px; z-index: 2;">
                                        PALING POPULER
                                    </div>
                                <?php endif; ?>

                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="text-center mb-3">
                                        <h5 class="font-weight-bold text-dark mb-1"><?= esc($plan['name']) ?></h5>
                                        <span class="badge badge-light border text-muted px-2 py-1" style="font-size: 0.72rem;">
                                            Hingga <?= esc($plan['listing_capacity'] >= 999 ? 'Unlimited (>100)' : $plan['listing_capacity']) ?> Listing Aktif
                                        </span>
                                    </div>

                                    <div class="text-center my-3 pb-3 border-bottom">
                                        <div class="h3 font-weight-bold text-dark mb-0">
                                            Rp <?= number_format($plan['price'], 0, ',', '.') ?>
                                        </div>
                                        <span class="text-muted small">/ bulan (30 hari)</span>
                                    </div>

                                    <p class="text-muted small text-center mb-3">
                                        <?= esc($plan['description']) ?>
                                    </p>

                                    <ul class="list-unstyled small mb-4 flex-grow-1">
                                        <?php foreach ($features as $feat): ?>
                                            <li class="mb-2 d-flex align-items-center">
                                                <i class="fas fa-check-circle text-success mr-2 flex-shrink-0"></i>
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
                        ?>
                        <div class="col-lg col-md-6 mb-4">
                            <div class="card h-100 border shadow-sm" style="border-radius: 12px;">
                                <div class="card-body p-3 d-flex flex-column text-center">
                                    <span class="badge badge-teal text-white px-2 py-1 mx-auto mb-2 text-uppercase" style="font-size: 0.68rem;">
                                        Slot 15 Hari
                                    </span>
                                    <h5 class="font-weight-bold text-dark mb-1"><?= esc($plan['name']) ?></h5>
                                    <span class="text-muted small mb-2">Kuota s.d. <strong><?= esc($plan['listing_capacity']) ?> listing</strong></span>

                                    <div class="my-2 py-2 border-top border-bottom">
                                        <div class="h4 font-weight-bold text-teal mb-0">
                                            Rp <?= number_format($plan['price'], 0, ',', '.') ?>
                                        </div>
                                        <span class="text-muted small">/ 15 hari</span>
                                    </div>

                                    <p class="text-muted small mb-3">
                                        <?= esc($plan['description']) ?>
                                    </p>

                                    <ul class="list-unstyled text-left small mb-3 flex-grow-1" style="font-size: 0.8rem;">
                                        <?php foreach ($features as $feat): ?>
                                            <li class="mb-1 text-secondary">
                                                <i class="fas fa-check text-success mr-1"></i> <?= esc($feat) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>

                                    <div class="mt-auto">
                                        <?php if ($isCurrent): ?>
                                            <button class="btn btn-outline-success btn-block btn-sm font-weight-bold" disabled>
                                                <i class="fas fa-check mr-1"></i> Sedang Aktif
                                            </button>
                                        <?php else: ?>
                                            <a href="<?= base_url('/provider/subscription/checkout/' . $plan['id']) ?>" class="btn btn-outline-teal btn-block btn-sm font-weight-bold">
                                                Pilih Slot
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
<div class="card border-0 shadow-sm" style="border-radius: 12px;">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
        <h5 class="font-weight-bold text-dark mb-1">
            <i class="fas fa-file-invoice-dollar text-teal mr-2"></i> Riwayat Tagihan & Pembayaran Paket
        </h5>
        <p class="text-muted small mb-0">Daftar transaksi pembelian paket langganan dan listing fee Anda.</p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
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
                <tbody class="small">
                    <?php if (empty($invoices)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-receipt fa-2x mb-2 text-secondary d-block"></i>
                                Belum ada riwayat tagihan paket berbayar.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td class="pl-4 font-weight-bold text-dark">
                                    <?= esc($inv['invoice_number']) ?>
                                    <span class="d-block text-muted" style="font-size: 0.7rem; font-family: monospace;"><?= esc($inv['order_id']) ?></span>
                                </td>
                                <td>
                                    <strong><?= esc($inv['plan_name']) ?></strong>
                                    <span class="d-block text-muted" style="font-size: 0.75rem;">
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
                                        <span class="text-muted"><i class="fas fa-check text-success"></i> Selesai</span>
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
    .border-teal {
        border-color: #0d9488 !important;
    }
    .btn-outline-teal {
        color: #0d9488;
        border-color: #0d9488;
    }
    .btn-outline-teal:hover {
        background-color: #0d9488;
        color: #fff;
    }
    .nav-pills .nav-link.active, .nav-pills .show>.nav-link {
        background-color: #0d9488 !important;
        color: #fff !important;
    }
    .nav-pills .nav-link {
        color: #475569;
        background: #f1f5f9;
        margin-right: 8px;
        border-radius: 8px;
    }
</style>
<?= $this->endSection() ?>
