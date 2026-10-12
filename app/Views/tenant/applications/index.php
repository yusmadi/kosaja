<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="bg-light py-4 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-teal text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengajuan Sewa Saya</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Pengajuan Sewa Saya</h1>
                <p class="text-muted small mb-0">Pantau status peninjauan pengajuan sewa, reservasi unit, dan proses checkout</p>
            </div>
            <a href="<?= base_url('/search') ?>" class="btn btn-kosaja-primary btn-sm px-3">
                <i class="bi bi-search me-1"></i> Cari Hunian Lain
            </a>
        </div>
    </div>
</div>

<div class="container py-5">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('info')): ?>
        <div class="alert alert-info alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
            <?= session()->getFlashdata('info') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($applications)): ?>
        <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3">
            <div class="py-4">
                <div class="mb-3 text-muted">
                    <i class="bi bi-journal-x display-3 text-teal opacity-50"></i>
                </div>
                <h3 class="h5 fw-bold text-dark mb-2">Belum Ada Pengajuan Sewa</h3>
                <p class="text-muted small mb-4 mx-auto" style="max-width: 450px;">
                    Anda belum pernah mengajukan sewa hunian. Jelajahi katalog kos dan kontrakan terverifikasi kami sekarang.
                </p>
                <a href="<?= base_url('/search') ?>" class="btn btn-kosaja-primary px-4 py-2">
                    <i class="bi bi-search me-1"></i> Jelajahi Listing Hunian
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($applications as $app): ?>
                <?php 
                    $status = $app['status'];
                    $badgeClass = match($status) {
                        'pending'   => 'bg-warning text-dark',
                        'approved'  => 'bg-success text-white',
                        'rejected'  => 'bg-danger text-white',
                        'cancelled' => 'bg-secondary text-white',
                        'expired'   => 'bg-dark text-white',
                        default     => 'bg-light text-dark',
                    };
                    $statusLabel = match($status) {
                        'pending'   => 'Menunggu Review Pemilik',
                        'approved'  => 'Disetujui (Reservasi Aktif)',
                        'rejected'  => 'Pengajuan Ditolak',
                        'cancelled' => 'Dibatalkan',
                        'expired'   => 'Kedaluwarsa',
                        default     => ucfirst($status),
                    };
                ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden <?= $status === 'approved' ? 'border-start border-4 border-success' : '' ?>">
                        <div class="card-body p-4">
                            <div class="row g-3 align-items-center">
                                <!-- Photo & Title -->
                                <div class="col-md-5">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= base_url(esc($app['unit_photo'] ?? ($app['property_photo'] ?? 'assets/images/sample-kos-1.jpg'))) ?>" 
                                             alt="<?= esc($app['unit_name']) ?>" 
                                             class="rounded-3 object-fit-cover shadow-sm flex-shrink-0" 
                                             style="width: 85px; height: 85px;">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge <?= $badgeClass ?> px-2 py-1 small">
                                                    <?= esc($statusLabel) ?>
                                                </span>
                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                    #APP-<?= str_pad($app['id'], 5, '0', STR_PAD_LEFT) ?>
                                                </small>
                                            </div>
                                            <h3 class="h6 fw-bold text-dark mb-1">
                                                <a href="<?= base_url('/applications/' . $app['id']) ?>" class="text-decoration-none text-dark hover-teal">
                                                    <?= esc($app['listing_title']) ?>
                                                </a>
                                            </h3>
                                            <div class="text-secondary small mb-1">
                                                <strong>Kamar: <?= esc($app['unit_name']) ?></strong> • <?= esc($app['property_name']) ?>
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.78rem;">
                                                Diajukan pada: <?= date('d M Y, H:i', strtotime($app['created_at'])) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Schedule & Pricing Details -->
                                <div class="col-md-4 border-start-md">
                                    <div class="small">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Mulai Masuk:</span>
                                            <strong class="text-dark"><?= date('d M Y', strtotime($app['start_date'])) ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Durasi:</span>
                                            <strong class="text-dark"><?= esc($app['duration_months']) ?> Bulan</strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Biaya Sewa Awal:</span>
                                            <strong class="text-teal">Rp <?= number_format($app['rental_amount'], 0, ',', '.') ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Pemilik:</span>
                                            <span class="text-dark"><?= esc($app['provider_name']) ?></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action CTA -->
                                <div class="col-md-3 text-md-end">
                                    <?php if ($status === 'approved' && !empty($app['reservation_id']) && $app['reservation_status'] === 'active'): ?>
                                        <a href="<?= base_url('/checkout/' . $app['reservation_id']) ?>" class="btn btn-success btn-sm w-100 py-2 fw-bold shadow-sm mb-2 pulse-animation">
                                            <i class="bi bi-wallet2 me-1"></i> Bayar & Checkout
                                        </a>
                                        <div class="text-danger small mb-1" style="font-size: 0.74rem;">
                                            <i class="bi bi-clock-history me-1"></i> Batas: <?= date('d M, H:i', strtotime($app['reservation_expires_at'])) ?>
                                        </div>
                                    <?php elseif ($status === 'rejected'): ?>
                                        <a href="<?= base_url('/applications/' . $app['id']) ?>" class="btn btn-outline-danger btn-sm w-100 py-2 mb-2">
                                            <i class="bi bi-info-circle me-1"></i> Lihat Alasan Penolakan
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= base_url('/applications/' . $app['id']) ?>" class="btn btn-kosaja-primary btn-sm w-100 py-2 mb-2">
                                            <i class="bi bi-eye me-1"></i> Pantau Detail
                                        </a>
                                    <?php endif; ?>

                                    <a href="<?= base_url('/listing/' . $app['listing_id']) ?>" class="btn btn-link btn-sm text-muted text-decoration-none p-0" style="font-size: 0.78rem;">
                                        Lihat Iklan Properti <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
    @keyframes pulse-glow {
        0% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(22, 163, 74, 0); }
        100% { box-shadow: 0 0 0 0 rgba(22, 163, 74, 0); }
    }
    .pulse-animation {
        animation: pulse-glow 2s infinite;
    }
    @media (min-width: 768px) {
        .border-start-md {
            border-left: 1px solid #e5e7eb;
            padding-left: 1.5rem;
        }
    }
</style>
<?= $this->endSection() ?>
