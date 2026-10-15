<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb Header -->
<section class="bg-white border-bottom py-3">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-teal text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('/search?region_id=' . ($listing['region_id'] ?? '')) ?>" class="text-teal text-decoration-none"><?= esc($listing['region_name'] ?? 'Wilayah') ?></a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('/search?type=' . $listing['type']) ?>" class="text-teal text-decoration-none"><?= esc(ucfirst(str_replace('_', ' ', $listing['type']))) ?></a></li>
                <li class="breadcrumb-item active text-truncate" style="max-width: 320px;" aria-current="page"><?= esc($listing['title']) ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Unpublished Preview Notice -->
<?php if (!empty($isPreview)): ?>
    <div class="alert alert-warning border-0 rounded-0 mb-0 py-3 text-center">
        <i class="bi bi-eye-fill me-2"></i> <strong>Mode Pratinjau:</strong> Listing ini berstatus <span class="badge bg-dark"><?= esc(strtoupper($listing['publication_status'])) ?></span> dan belum aktif untuk umum.
    </div>
<?php endif; ?>

<!-- Main Detail Section -->
<section class="py-4">
    <div class="container">
        <!-- Title and Badges Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start gap-3 mb-4">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <!-- Syariat Target Gender Badge -->
                    <?php if ($listing['target_gender'] === 'putri'): ?>
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 py-2 px-3 rounded-pill fw-bold">
                            <i class="bi bi-gender-female me-1"></i> Khusus Putri
                        </span>
                    <?php elseif ($listing['target_gender'] === 'putra'): ?>
                        <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 py-2 px-3 rounded-pill fw-bold">
                            <i class="bi bi-gender-male me-1"></i> Khusus Putra
                        </span>
                    <?php else: ?>
                        <span class="badge bg-purple bg-opacity-10 text-purple border border-purple border-opacity-25 py-2 px-3 rounded-pill fw-bold" style="color: #8b5cf6 !important; border-color: #8b5cf6 !important;">
                            <i class="bi bi-people me-1"></i> Pasutri / Keluarga
                        </span>
                    <?php endif; ?>

                    <span class="badge bg-light text-dark border py-2 px-3 rounded-pill fw-semibold text-uppercase">
                        <?= esc(str_replace('_', ' ', $listing['type'])) ?>
                    </span>

                    <span class="badge bg-teal bg-opacity-10 text-teal py-2 px-3 rounded-pill fw-semibold">
                        <i class="bi bi-shield-check me-1"></i> Syariat Checked
                    </span>
                </div>

                <h1 class="h2 fw-bold text-dark mb-2"><?= esc($listing['title']) ?></h1>
                <p class="text-muted small mb-0">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> <?= esc($listing['address']) ?> 
                    <span class="mx-1">•</span> <strong class="text-secondary"><?= esc($listing['region_name'] ?? 'Sleman, DIY') ?></strong>
                </p>
            </div>

            <div class="d-flex align-items-center gap-2">
                <!-- Favorite Button -->
                <button type="button" 
                        class="btn btn-outline-danger btn-sm px-3 py-2 <?= $isFavorited ? 'active' : '' ?>" 
                        id="detailFavBtn"
                        onclick="toggleFavoriteDetail(<?= $listing['id'] ?>)">
                    <i class="bi <?= $isFavorited ? 'bi-heart-fill' : 'bi-heart' ?> me-1"></i>
                    <span id="detailFavText"><?= $isFavorited ? 'Tersimpan di Favorit' : 'Simpan Favorit' ?></span>
                </button>
                
                <!-- Share Button -->
                <button type="button" class="btn btn-outline-secondary btn-sm px-3 py-2" onclick="shareListing()">
                    <i class="bi bi-share me-1"></i> Bagikan
                </button>
            </div>
        </div>

        <!-- Photo Gallery -->
        <div class="row g-3 mb-5">
            <?php 
                $allPhotos = array_merge($propertyPhotos, $unitPhotos);
                $heroPhoto = !empty($allPhotos) ? $allPhotos[0]['photo_url'] : 'assets/images/sample-kos-1.jpg';
            ?>
            <div class="col-lg-8">
                <div class="position-relative overflow-hidden rounded-4 shadow-sm" style="height: 420px; background: #e2e8f0;">
                    <img id="mainGalleryImg" src="<?= base_url(esc($heroPhoto)) ?>" alt="<?= esc($listing['title']) ?>" class="w-100 h-100" style="object-fit: cover;">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="d-flex flex-column gap-3 h-100">
                    <?php 
                        $thumbPhotos = array_slice($allPhotos, 1, 3);
                    ?>
                    <?php if (empty($thumbPhotos)): ?>
                        <div class="h-100 d-flex align-items-center justify-content-center bg-light border rounded-4 text-muted small p-3 text-center">
                            <span><i class="bi bi-images display-6 d-block mb-2 opacity-50"></i>Galeri foto tambahan akan segera diperbarui.</span>
                        </div>
                    <?php else: ?>
                        <?php foreach ($thumbPhotos as $tp): ?>
                            <div class="position-relative overflow-hidden rounded-4 shadow-sm flex-grow-1" style="height: 125px; cursor: pointer;" onclick="swapGalleryImg('<?= base_url(esc($tp['photo_url'])) ?>')">
                                <img src="<?= base_url(esc($tp['photo_url'])) ?>" alt="Thumbnail" class="w-100 h-100" style="object-fit: cover;">
                                <div class="position-absolute bottom-0 start-0 end-0 bg-dark bg-opacity-50 text-white p-1 text-center small" style="font-size: 0.75rem;">
                                    <?= esc($tp['caption'] ?? 'Lihat Foto') ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Content & Booking Grid -->
        <div class="row g-4">
            <!-- Left Details Column -->
            <div class="col-lg-8">
                <!-- Key Specifications Card -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h3 class="h5 fw-bold text-dark mb-3">Spesifikasi Unit Hunian</h3>
                    <div class="row g-3">
                        <div class="col-sm-6 col-md-3">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="bi bi-door-closed text-teal fs-3 mb-1 d-block"></i>
                                <span class="text-muted small d-block">Unit Kamar</span>
                                <strong class="text-dark"><?= esc($listing['unit_name']) ?></strong>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="bi bi-aspect-ratio text-teal fs-3 mb-1 d-block"></i>
                                <span class="text-muted small d-block">Ukuran Kamar</span>
                                <strong class="text-dark"><?= esc($listing['unit_size'] ?? '3 x 4 m') ?></strong>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="bi bi-layers text-teal fs-3 mb-1 d-block"></i>
                                <span class="text-muted small d-block">Lantai</span>
                                <strong class="text-dark"><?= esc($listing['unit_floor'] ?? 'Lantai 1') ?></strong>
                            </div>
                        </div>
                        <div class="col-sm-6 col-md-3">
                            <div class="p-3 bg-light rounded-3 text-center">
                                <i class="bi bi-check-circle text-teal fs-3 mb-1 d-block"></i>
                                <span class="text-muted small d-block">Ketersediaan</span>
                                <strong class="text-success"><?= ($listing['unit_status'] === 'available') ? 'Siap Huni' : 'Terisi' ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Facilities Card -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h3 class="h5 fw-bold text-dark mb-3">Fasilitas Properti & Bersama</h3>
                    <?php if (empty($facilities)): ?>
                        <p class="text-muted small mb-0">Informasi fasilitas properti belum ditambahkan oleh pemilik.</p>
                    <?php else: ?>
                        <div class="d-flex flex-wrap gap-2">
                            <?php foreach ($facilities as $f): ?>
                                <div class="amenity-chip">
                                    <i class="bi <?= esc($f['icon'] ?? 'bi-check2-circle') ?> text-teal fs-5"></i>
                                    <span><?= esc($f['facility_name']) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Description Card -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h3 class="h5 fw-bold text-dark mb-3">Deskripsi Lengkap</h3>
                    <div class="text-secondary" style="line-height: 1.8; white-space: pre-line;">
                        <?= esc($listing['description']) ?>
                    </div>
                </div>

                <!-- House Rules & Syariat Conformity -->
                <div class="card border-0 shadow-sm p-4 mb-4" style="background: #f0fdf4; border: 1px solid #bbf7d0 !important;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-shield-shaded text-teal fs-4"></i>
                        <h3 class="h5 fw-bold text-teal mb-0">Tata Tertib & Ketentuan Syariat</h3>
                    </div>
                    <p class="text-muted small mb-3">
                        Hunian ini menerapkan nilai adab dan kesopanan lingkungan untuk kenyamanan bersama para penghuni.
                    </p>
                    <div class="p-3 bg-white rounded-3 text-secondary small border" style="line-height: 1.7; white-space: pre-line;">
                        <?= esc($listing['rules'] ?? "1. Wajib menjaga ketertiban, kebersihan, dan kesopanan lingkungan.\n2. Lawan jenis dilarang masuk ke dalam kamar hunian.\n3. Bagi penyewa pasangan suami istri, wajib menunjukkan Buku Nikah / Kartu Keluarga resmi saat check-in.") ?>
                    </div>
                </div>

                <!-- Provider Profile & KYC -->
                <div class="card border-0 shadow-sm p-4 mb-4">
                    <h3 class="h5 fw-bold text-dark mb-3">Pemilik & Pengelola Properti</h3>
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-teal text-white fw-bold d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; font-size: 1.5rem;">
                            <?= esc(substr($listing['provider_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <h4 class="h6 fw-bold text-dark mb-1">
                                <?= esc($listing['provider_name']) ?>
                                <?php if ($listing['provider_kyc'] === 'verified'): ?>
                                    <span class="badge bg-success ms-1 small"><i class="bi bi-patch-check-fill me-1"></i> Terverifikasi KosAja</span>
                                <?php endif; ?>
                            </h4>
                            <p class="text-muted small mb-0">Mitra Terdaftar sejak <?= date('Y', strtotime($listing['provider_joined'])) ?> • Transaksi Bebas Calo</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sticky Booking Column -->
            <div class="col-lg-4">
                <div class="sticky-booking-card">
                    <span class="text-muted small text-uppercase fw-semibold">Biaya Sewa Hunian</span>
                    <div class="d-flex align-items-baseline gap-1 my-2">
                        <span class="h2 fw-bold text-teal mb-0">
                            Rp <?= number_format($listing['price_monthly'], 0, ',', '.') ?>
                        </span>
                        <span class="text-muted">/ bulan</span>
                    </div>

                    <?php if (!empty($listing['price_six_monthly'])): ?>
                        <div class="small text-muted mb-1">
                            Atau <strong>Rp <?= number_format($listing['price_six_monthly'], 0, ',', '.') ?></strong> / 6 bulan
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($listing['price_yearly'])): ?>
                        <div class="small text-muted mb-3">
                            Atau <strong>Rp <?= number_format($listing['price_yearly'], 0, ',', '.') ?></strong> / tahun (hemat lebih banyak)
                        </div>
                    <?php else: ?>
                        <div class="mb-3"></div>
                    <?php endif; ?>

                    <div class="p-3 bg-light rounded-3 mb-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Biaya Administrasi:</span>
                            <span class="text-success fw-bold">Gratis (Rp 0)</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Markup Calo:</span>
                            <span class="text-success fw-bold">0% (Bebas Calo)</span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top">
                            <strong class="text-dark">Status Kamar:</strong>
                            <strong class="text-teal"><?= ($listing['unit_status'] === 'available') ? 'Tersedia' : 'Terisi' ?></strong>
                        </div>
                    </div>

                    <!-- Application CTA (Booking Engine) -->
                    <?php if ($listing['unit_status'] === 'available'): ?>
                        <a href="<?= base_url('/apply/' . $listing['id']) ?>" 
                           class="btn btn-kosaja-primary w-100 py-3 fw-bold mb-2 shadow-sm">
                            <i class="bi bi-calendar-check me-1"></i> Ajukan Sewa Kamar Ini
                        </a>
                    <?php else: ?>
                        <button type="button" class="btn btn-secondary w-100 py-3 fw-bold mb-2 shadow-sm" disabled>
                            <i class="bi bi-x-circle me-1"></i> Kamar Tidak Tersedia
                        </button>
                    <?php endif; ?>

                    <!-- WhatsApp Consultation -->
                    <?php if (!empty($listing['provider_whatsapp'])): ?>
                        <?php 
                            $waNumber = preg_replace('/[^0-9]/', '', $listing['provider_whatsapp']);
                            $waMessage = urlencode("Halo Bapak/Ibu " . $listing['provider_name'] . ", saya tertarik dengan listing '" . $listing['title'] . "' di KosAja. Apakah unit masih tersedia?");
                        ?>
                        <a href="https://wa.me/<?= $waNumber ?>?text=<?= $waMessage ?>" target="_blank" class="btn btn-outline-success w-100 py-2 small fw-semibold">
                            <i class="bi bi-whatsapp me-1"></i> Tanya Pemilik via WhatsApp
                        </a>
                    <?php endif; ?>

                    <div class="text-center mt-3">
                        <small class="text-muted" style="font-size: 0.78rem;">
                            <i class="bi bi-lock-fill me-1"></i> Transaksi terjamin aman dan tercatat resmi dalam sistem KosAja.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Similar Listings Section -->
        <?php if (!empty($similarListings)): ?>
            <div class="mt-5 pt-4 border-top">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="h4 fw-bold text-dark mb-1">Hunian Serupa Lainnya</h3>
                        <p class="text-muted small mb-0">Rekomendasi properti dengan kriteria penghuni yang sama</p>
                    </div>
                    <a href="<?= base_url('/search?target_gender=' . $listing['target_gender']) ?>" class="btn btn-sm btn-kosaja-outline">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>

                <div class="row g-4">
                    <?php foreach ($similarListings as $sim): ?>
                        <div class="col-md-4">
                            <div class="property-card">
                                <div class="property-img-wrapper">
                                    <img src="<?= base_url(esc($sim['photo_url'] ?? 'assets/images/sample-kos-1.jpg')) ?>" alt="<?= esc($sim['title']) ?>" class="property-img">
                                    <span class="gender-badge <?= esc($sim['target_gender']) ?>">
                                        <?= esc(ucfirst($sim['target_gender'])) ?>
                                    </span>
                                </div>
                                <div class="property-card-body">
                                    <div class="property-location">
                                        <i class="bi bi-geo-alt-fill text-danger"></i>
                                        <span><?= esc($sim['region_name'] ?? 'Sleman') ?></span>
                                    </div>
                                    <h4 class="property-title">
                                        <a href="<?= base_url('/listing/' . $sim['id']) ?>" class="text-decoration-none text-dark">
                                            <?= esc($sim['title']) ?>
                                        </a>
                                    </h4>
                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                        <div class="property-price">
                                            Rp <?= number_format($sim['price_monthly'], 0, ',', '.') ?>
                                            <span>/ bln</span>
                                        </div>
                                        <a href="<?= base_url('/listing/' . $sim['id']) ?>" class="btn btn-sm btn-kosaja-outline">
                                            Detail
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function swapGalleryImg(newSrc) {
        document.getElementById('mainGalleryImg').src = newSrc;
    }

    function toggleFavoriteDetail(listingId) {
        fetch('<?= base_url('/favorites/toggle/') ?>' + listingId, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
            }
        })
        .then(res => {
            if (res.status === 401) {
                window.location.href = '<?= base_url('/login') ?>';
                return;
            }
            return res.json();
        })
        .then(data => {
            if (!data) return;
            const btn = document.getElementById('detailFavBtn');
            const icon = btn.querySelector('i');
            const txt = document.getElementById('detailFavText');

            if (data.is_favorited) {
                btn.classList.add('active');
                icon.className = 'bi bi-heart-fill me-1';
                txt.textContent = 'Tersimpan di Favorit';
            } else {
                btn.classList.remove('active');
                icon.className = 'bi bi-heart me-1';
                txt.textContent = 'Simpan Favorit';
            }
        })
        .catch(err => console.error('Error toggling favorite:', err));
    }

    function shareListing() {
        if (navigator.share) {
            navigator.share({
                title: '<?= esc($listing['title']) ?>',
                text: 'Temukan kos/kontrakan bebas calo di KosAja: <?= esc($listing['title']) ?>',
                url: window.location.href,
            });
        } else {
            navigator.clipboard.writeText(window.location.href);
            alert('Tautan listing berhasil disalin ke clipboard!');
        }
    }
</script>
<?= $this->endSection() ?>
