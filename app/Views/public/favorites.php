<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb Header -->
<section class="bg-white border-bottom py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-teal text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Hunian Favorit</li>
            </ol>
        </nav>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h1 class="h3 fw-bold mb-1 text-dark">
                    <i class="bi bi-heart-fill text-danger me-2"></i> Favorit Anda
                </h1>
                <p class="text-muted small mb-0">
                    Daftar hunian yang telah Anda simpan untuk referensi perbandingan dan pengajuan sewa.
                </p>
            </div>
            <div>
                <a href="<?= base_url('/search') ?>" class="btn btn-sm btn-kosaja-outline">
                    <i class="bi bi-search me-1"></i> Cari Hunian Lain
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Flash alerts -->
<div class="container mt-4">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('warning')): ?>
        <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('warning') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>

<!-- Favorites List Section -->
<section class="py-4">
    <div class="container">
        <?php if (empty($favorites)): ?>
            <!-- Tenant Empty State as per UX Blueprint Section 19 & 30 -->
            <div class="card border-0 shadow-sm text-center py-5">
                <div class="card-body py-5">
                    <div class="mb-3 text-muted">
                        <i class="bi bi-heart display-1 text-danger opacity-25"></i>
                    </div>
                    <h3 class="h4 fw-bold text-dark mb-2">Belum ada hunian favorit.</h3>
                    <p class="text-muted small mx-auto mb-4" style="max-width: 480px;">
                        Simpan listing kamar kos atau kontrakan idaman Anda dengan menekan ikon hati agar mudah dibandingkan sebelum mengajukan sewa.
                    </p>
                    <a href="<?= base_url('/search') ?>" class="btn btn-kosaja-primary px-4 py-2">
                        <i class="bi bi-search me-1"></i> Mulai Jelajah Hunian
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div class="row g-4">
                <?php foreach ($favorites as $item): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="property-card h-100">
                            <div class="property-img-wrapper position-relative">
                                <img src="<?= base_url(esc($item['photo_url'] ?? 'assets/images/sample-kos-1.jpg')) ?>" 
                                     alt="<?= esc($item['title']) ?>" 
                                     class="property-img">
                                
                                <!-- Gender Badge -->
                                <span class="gender-badge <?= esc($item['target_gender']) ?>">
                                    <?php 
                                        if ($item['target_gender'] === 'putri') echo '<i class="bi bi-gender-female me-1"></i> Putri';
                                        elseif ($item['target_gender'] === 'putra') echo '<i class="bi bi-gender-male me-1"></i> Putra';
                                        else echo '<i class="bi bi-people me-1"></i> Pasutri / Keluarga';
                                    ?>
                                </span>

                                <!-- Type Tag -->
                                <span class="property-type-tag">
                                    <?= esc(str_replace('_', ' ', $item['type'])) ?>
                                </span>

                                <!-- Remove Favorite Form -->
                                <form action="<?= base_url('/favorites/remove/' . $item['id']) ?>" method="post" class="position-absolute" style="top: 12px; right: 12px;">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn-favorite-icon active" title="Hapus dari favorit" onclick="return confirm('Hapus hunian ini dari daftar favorit?')">
                                        <i class="bi bi-heart-fill"></i>
                                    </button>
                                </form>
                            </div>

                            <div class="property-card-body">
                                <!-- Inactive Listing Notice (UX Blueprint rule) -->
                                <?php if ($item['publication_status'] !== 'published'): ?>
                                    <div class="alert alert-warning py-1 px-2 mb-2 small text-center fw-semibold">
                                        <i class="bi bi-exclamation-circle me-1"></i> Listing ini tidak tersedia untuk pengajuan baru.
                                    </div>
                                <?php endif; ?>

                                <div class="property-location">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                    <span><?= esc($item['region_name'] ?? 'Sleman, DIY') ?></span>
                                </div>
                                
                                <h3 class="property-title">
                                    <a href="<?= base_url('/listing/' . $item['id']) ?>" class="text-decoration-none text-dark">
                                        <?= esc($item['title']) ?>
                                    </a>
                                </h3>

                                <div class="small text-muted mb-2">
                                    <i class="bi bi-door-open me-1 text-teal"></i> <?= esc($item['unit_name']) ?> (<?= esc($item['unit_size'] ?? '3x4 m') ?>)
                                </div>

                                <p class="text-muted small mb-3 flex-grow-1" style="font-size: 0.85rem; line-height: 1.4;">
                                    <?= character_limiter(esc($item['description']), 75) ?>
                                </p>

                                <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                    <div class="property-price">
                                        Rp <?= number_format($item['price_monthly'], 0, ',', '.') ?>
                                        <span>/ bln</span>
                                    </div>
                                    <a href="<?= base_url('/listing/' . $item['id']) ?>" class="btn btn-sm btn-kosaja-outline">
                                        Lihat Detail <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>
