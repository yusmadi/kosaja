<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="bg-light py-4 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-teal text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kontrak Sewa Saya</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Kontrak Sewa Saya</h1>
                <p class="text-muted small mb-0">Daftar dokumen kesepakatan sewa resmi dan riwayat hunian aktif Anda</p>
            </div>
            <a href="<?= base_url('/applications') ?>" class="btn btn-outline-teal btn-sm">
                <i class="bi bi-clock-history me-1"></i> Riwayat Pengajuan Sewa
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

    <?php if (empty($contracts)): ?>
        <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3">
            <div class="py-4">
                <i class="bi bi-file-earmark-text display-3 text-teal opacity-50 mb-3 d-block"></i>
                <h3 class="h5 fw-bold text-dark mb-2">Belum Ada Kontrak Sewa Aktif</h3>
                <p class="text-muted small mb-4 mx-auto" style="max-width: 480px;">
                    Kontrak resmi akan otomatis terbentuk setelah pemilik menyetujui pengajuan sewa dan Anda menyelesaikan pembayaran pertama melalui platform.
                </p>
                <a href="<?= base_url('/search') ?>" class="btn btn-kosaja-primary px-4 py-2">
                    <i class="bi bi-search me-1"></i> Cari Hunian Pilihan
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($contracts as $c): ?>
                <?php 
                    $isActive = ($c['status'] === 'active');
                    $badgeClass = match($c['status']) {
                        'active'     => 'bg-success text-white',
                        'completed'  => 'bg-secondary text-white',
                        'terminated' => 'bg-danger text-white',
                        default      => 'bg-light text-dark',
                    };
                    $badgeLabel = match($c['status']) {
                        'active'     => 'Sewa Aktif Berjalan',
                        'completed'  => 'Kontrak Selesai',
                        'terminated' => 'Diberhentikan Awal',
                        default      => ucfirst($c['status']),
                    };
                ?>
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-3 overflow-hidden <?= $isActive ? 'border-start border-4 border-teal' : '' ?>">
                        <div class="card-body p-4">
                            <div class="row g-3 align-items-center">
                                <div class="col-md-5">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= base_url(esc($c['unit_photo'] ?? ($c['property_photo'] ?? 'assets/images/sample-kos-1.jpg'))) ?>" 
                                             alt="<?= esc($c['unit_name']) ?>" 
                                             class="rounded-3 object-fit-cover shadow-sm flex-shrink-0" 
                                             style="width: 85px; height: 85px;">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <span class="badge <?= $badgeClass ?> px-2 py-1 small">
                                                    <?= esc($badgeLabel) ?>
                                                </span>
                                                <small class="text-muted font-monospace" style="font-size: 0.75rem;">
                                                    #CTR-<?= str_pad($c['id'], 5, '0', STR_PAD_LEFT) ?>
                                                </small>
                                            </div>
                                            <h3 class="h6 fw-bold text-dark mb-1">
                                                <a href="<?= base_url('/contracts/' . $c['id']) ?>" class="text-decoration-none text-dark hover-teal">
                                                    <?= esc($c['property_name']) ?> — Kamar <?= esc($c['unit_name']) ?>
                                                </a>
                                            </h3>
                                            <div class="text-secondary small mb-1">
                                                Pemilik: <strong><?= esc($c['provider_name']) ?></strong>
                                            </div>
                                            <div class="text-muted small" style="font-size: 0.78rem;">
                                                <i class="bi bi-geo-alt me-1 text-danger"></i><?= esc($c['property_address']) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4 border-start-md">
                                    <div class="small">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Masa Berlaku Sewa:</span>
                                            <strong class="text-dark">
                                                <?= date('d M Y', strtotime($c['start_date'])) ?> s/d <?= date('d M Y', strtotime($c['end_date'])) ?>
                                            </strong>
                                        </div>
                                        <div class="d-flex justify-content-between mb-1">
                                            <span class="text-muted">Skema Pembayaran:</span>
                                            <span class="text-dark text-capitalize"><?= str_replace('_', ' ', esc($c['payment_schedule'])) ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span class="text-muted">Nilai Kontrak Sewa:</span>
                                            <strong class="text-teal fs-6">Rp <?= number_format($c['agreed_price'], 0, ',', '.') ?></strong>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 text-md-end">
                                    <a href="<?= base_url('/contracts/' . $c['id']) ?>" class="btn btn-kosaja-primary btn-sm w-100 py-2 fw-bold shadow-sm mb-2">
                                        <i class="bi bi-file-earmark-text me-1"></i> Buka Surat Kontrak
                                    </a>
                                    <?php if (!empty($c['provider_whatsapp'])): ?>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $c['provider_whatsapp']) ?>" target="_blank" class="btn btn-outline-success btn-sm w-100 py-1" style="font-size: 0.78rem;">
                                            <i class="bi bi-whatsapp me-1"></i> WhatsApp Pemilik
                                        </a>
                                    <?php endif; ?>
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
    @media (min-width: 768px) {
        .border-start-md {
            border-left: 1px solid #e5e7eb;
            padding-left: 1.5rem;
        }
    }
</style>
<?= $this->endSection() ?>
