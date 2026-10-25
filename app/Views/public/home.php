<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<!-- Hero Section -->
<section class="hero-section">
    <div class="container text-center">
        <span class="badge bg-white bg-opacity-10 text-white border border-light border-opacity-25 py-2 px-3 rounded-pill mb-3 small">
            <i class="bi bi-patch-check-fill text-warning me-1"></i> Direktori Kos & Rumah Sewa Terpercaya
        </span>
        <h1 class="hero-title text-white">
            Cari Kos & Kontrakan Nyaman,<br>
            <span style="color: #5eead4;">Bebas Calo & Sesuai Syariat</span>
        </h1>
        <p class="hero-subtitle mx-auto">
            Platform persewaan terintegrasi untuk mahasiswa, pekerja, dan keluarga muslim. Transparan, booking instan, dan bebas markup perantara.
        </p>
    </div>
</section>

<!-- Search Box Section (Overlapping Hero) -->
<div class="container">
    <div class="search-filter-box">
        <form id="searchForm" action="<?= base_url('/search') ?>" method="get" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label for="searchKeyword" class="search-field-label">
                    <i class="bi bi-geo-alt me-1 text-primary"></i> Cari Lokasi / Kampus / Nama Kos
                </label>
                <input type="text" name="keyword" id="searchKeyword" class="search-field-input" placeholder="Misal: UGM, Pogung, Seturan...">
            </div>
            <div class="col-md-3">
                <label for="searchType" class="search-field-label">
                    <i class="bi bi-building me-1 text-primary"></i> Tipe Properti
                </label>
                <select name="type" id="searchType" class="search-field-input">
                    <option value="">Semua Tipe</option>
                    <option value="kos">Kos / Kamar</option>
                    <option value="kontrakan">Kontrakan Rumah</option>
                    <option value="rumah_sewa">Rumah Sewa</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="searchGender" class="search-field-label">
                    <i class="bi bi-gender-ambiguous me-1 text-primary"></i> Target Penghuni
                </label>
                <select name="target_gender" id="searchGender" class="search-field-input">
                    <option value="">Semua Penghuni</option>
                    <option value="putri">Khusus Putri</option>
                    <option value="putra">Khusus Putra</option>
                    <option value="campur_keluarga">Pasutri / Keluarga</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-kosaja-primary w-100 py-2">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Property Listings Section -->
<section class="py-5 mt-2">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h2 class="h3 fw-bold mb-1">Pilihan Properti Tersedia</h2>
                <p class="text-muted small mb-0">Menampilkan properti siap huni yang telah lolos verifikasi tim KosAja</p>
            </div>
            <!-- Quick Filter Pills -->
            <div class="d-flex flex-wrap gap-2">
                <button class="btn btn-sm btn-kosaja-outline btn-quick-filter active" data-filter="all">Semua (<?= count($listings ?? []) ?>)</button>
                <button class="btn btn-sm btn-kosaja-outline btn-quick-filter" data-filter="putri"><i class="bi bi-gender-female text-danger"></i> Khusus Putri</button>
                <button class="btn btn-sm btn-kosaja-outline btn-quick-filter" data-filter="putra"><i class="bi bi-gender-male text-primary"></i> Khusus Putra</button>
                <button class="btn btn-sm btn-kosaja-outline btn-quick-filter" data-filter="campur_keluarga"><i class="bi bi-people text-purple"></i> Keluarga / Pasutri</button>
            </div>
        </div>

        <div class="row g-4" id="listingsContainer">
            <?php if (!empty($listings)): ?>
                <?php foreach ($listings as $item): ?>
                    <?php 
                        $isFav = in_array((int)$item['id'], $userFavorites ?? []); 
                        $propPhoto = !empty($item['property_photo']) ? $item['property_photo'] : 'assets/images/exterior-kos-1.jpg';
                        $unitPhoto = !empty($item['unit_photo']) ? $item['unit_photo'] : 'assets/images/sample-kos-1.jpg';
                    ?>
                    <div class="col-md-6 col-lg-4 property-card-col" 
                         data-gender="<?= esc($item['target_gender']) ?>" 
                         data-type="<?= esc($item['type']) ?>">
                        <div class="property-card">
                            <div class="property-img-wrapper position-relative" data-active-view="property">
                                <!-- Main Photos: Property Exterior & Inside Kamar -->
                                <a href="<?= base_url('/listing/' . $item['id']) ?>" class="photo-link-wrapper" title="<?= esc($item['title']) ?>">
                                    <img src="<?= base_url(esc($propPhoto)) ?>" 
                                         alt="<?= esc($item['title']) ?> - Foto Gedung Properti" 
                                         class="property-img img-property-view"
                                         loading="lazy">
                                    <img src="<?= base_url(esc($unitPhoto)) ?>" 
                                         alt="<?= esc($item['unit_name']) ?> - Inside Kamar" 
                                         class="property-img img-inside-view"
                                         loading="lazy">
                                </a>

                                <!-- View Switcher Pills (Gedung & Inside Kamar) -->
                                <div class="photo-view-switcher" onclick="event.stopPropagation();">
                                    <button type="button" 
                                            class="view-pill active" 
                                            data-target="property" 
                                            onclick="switchCardPhoto(this, 'property', event)"
                                            title="Tampilkan foto tampak depan gedung">
                                        <i class="bi bi-building me-1"></i> Gedung
                                    </button>
                                    <button type="button" 
                                            class="view-pill" 
                                            data-target="inside" 
                                            onclick="switchCardPhoto(this, 'inside', event)"
                                            title="Tampilkan foto kamar tidur (inside)">
                                        <i class="bi bi-door-open-fill me-1"></i> Inside Kamar
                                    </button>
                                </div>

                                <!-- Inset Mini Thumbnail (Displays Room photo when property view is active, and vice versa) -->
                                <div class="photo-inset-preview" 
                                     onclick="toggleCardPhotoInset(this, event)"
                                     title="Klik untuk melihat foto kamar tidur (inside)"
                                     data-property-src="<?= base_url(esc($propPhoto)) ?>"
                                     data-inside-src="<?= base_url(esc($unitPhoto)) ?>">
                                    <img src="<?= base_url(esc($unitPhoto)) ?>" 
                                         alt="Inside Kamar" 
                                         class="inset-img">
                                    <div class="inset-badge">
                                        <i class="bi bi-door-open-fill me-1 text-teal"></i> <span class="inset-text">Inside</span>
                                    </div>
                                </div>
                                
                                <!-- Favorite Heart Button -->
                                <button type="button" 
                                        class="btn-favorite-icon <?= $isFav ? 'active' : '' ?>" 
                                        title="<?= $isFav ? 'Hapus dari favorit' : 'Simpan ke favorit' ?>"
                                        onclick="toggleFavorite(<?= $item['id'] ?>, this, event)">
                                    <i class="bi <?= $isFav ? 'bi-heart-fill' : 'bi-heart' ?>"></i>
                                </button>

                                <!-- Gender Badge -->
                                <span class="gender-badge <?= esc($item['target_gender']) ?>">
                                    <?php 
                                        if ($item['target_gender'] === 'putri') echo '<i class="bi bi-gender-female me-1"></i> Putri';
                                        elseif ($item['target_gender'] === 'putra') echo '<i class="bi bi-gender-male me-1"></i> Putra';
                                        else echo '<i class="bi bi-people me-1"></i> Pasutri / Keluarga';
                                    ?>
                                </span>
                                <span class="property-type-tag">
                                    <?= esc(str_replace('_', ' ', $item['type'])) ?>
                                </span>
                            </div>
                            <div class="property-card-body">
                                <div class="property-location">
                                    <i class="bi bi-geo-alt-fill text-danger"></i>
                                    <span><?= esc($item['region_name'] ?? 'Sleman, DIY') ?></span>
                                </div>
                                <h3 class="property-title">
                                    <a href="<?= base_url('/listing/' . $item['id']) ?>" class="text-decoration-none text-dark">
                                        <?= esc($item['title']) ?>
                                    </a>
                                </h3>
                                <div class="small text-muted mb-2 d-flex align-items-center justify-content-between">
                                    <div><i class="bi bi-door-open me-1 text-teal"></i> <?= esc($item['unit_name']) ?> (<?= esc($item['unit_size'] ?? '3x4 m') ?>)</div>
                                    <span class="badge bg-light text-secondary border px-2 py-1"><i class="bi bi-images text-teal me-1"></i> Properti & Inside</span>
                                </div>
                                <p class="text-muted small mb-3 flex-grow-1" style="font-size: 0.85rem; line-height: 1.4;">
                                    <?= character_limiter(esc($item['description']), 85) ?>
                                </p>
                                <div class="d-flex align-items-center justify-content-between pt-3 border-top mt-auto">
                                    <div>
                                        <?php
                                            $activePrices = [];
                                            if (!empty($item['price_monthly']) && (float)$item['price_monthly'] > 0) {
                                                $activePrices[] = [
                                                    'amount' => (float)$item['price_monthly'],
                                                    'label'  => '/ bln',
                                                ];
                                            }
                                            if (!empty($item['price_six_monthly']) && (float)$item['price_six_monthly'] > 0) {
                                                $activePrices[] = [
                                                    'amount' => (float)$item['price_six_monthly'],
                                                    'label'  => '/ enam bulan',
                                                ];
                                            }
                                            if (!empty($item['price_yearly']) && (float)$item['price_yearly'] > 0) {
                                                $activePrices[] = [
                                                    'amount' => (float)$item['price_yearly'],
                                                    'label'  => '/ thn',
                                                ];
                                            }
                                        ?>
                                        <?php if (!empty($activePrices)): ?>
                                            <div class="property-price">
                                                Rp <?= number_format($activePrices[0]['amount'], 0, ',', '.') ?>
                                                <span><?= $activePrices[0]['label'] ?></span>
                                            </div>
                                            <?php if (count($activePrices) > 1): ?>
                                                <div class="text-muted" style="font-size: 0.75rem; margin-top: 2px;">
                                                    <?php 
                                                        $otherLabels = [];
                                                        for ($i = 1; $i < count($activePrices); $i++) {
                                                            $otherLabels[] = 'Rp ' . number_format($activePrices[$i]['amount'], 0, ',', '.') . ' ' . $activePrices[$i]['label'];
                                                        }
                                                        echo implode(' &bull; ', $otherLabels);
                                                    ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="property-price text-muted" style="font-size: 0.95rem;">
                                                Hubungi Pemilik
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <a href="<?= base_url('/listing/' . $item['id']) ?>" class="btn btn-sm btn-kosaja-outline">
                                        Detail <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <p class="text-muted">Belum ada listing yang aktif.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Value Proposition Section -->
<section class="py-5 bg-white border-top border-bottom" id="keunggulan">
    <div class="container">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="badge bg-teal bg-opacity-10 text-teal py-2 px-3 rounded-pill fw-semibold mb-2">Kenapa KosAja?</span>
            <h2 class="h2 fw-bold">Platform Hunian Sewa yang Beradab & Amanah</h2>
            <p class="text-muted">Dirancang khusus untuk menghadirkan kenyamanan, kepastian hukum, dan bebas kecurangan calo.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="bi bi-slash-circle"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-2">100% Bebas Calo</h4>
                    <p class="text-muted small mb-0">
                        Harga asli langsung dari pemilik properti. Tidak ada markup harga liar dan biaya perantara tersembunyi.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-2">Kepatuhan Syariat</h4>
                    <p class="text-muted small mb-0">
                        Pemisahan jelas antara kos putra, kos putri, dan kontrakan pasutri bersertifikat buku nikah, menjaga kenyamanan lingkungan.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <div class="feature-icon-wrapper">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-2">Kontrak & Transaksi Transparan</h4>
                    <p class="text-muted small mb-0">
                        Invoice tercatat rapi, riwayat sewa terlacak, dan locking unit otomatis setelah pembayaran uang muka / tagihan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call To Action for Kos Owners -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="p-4 p-lg-5 rounded-4" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 100%); color: #ffffff;">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h3 class="h2 fw-bold text-white mb-2">Punya Kos atau Kontrakan?</h3>
                    <p class="text-white-50 mb-0">
                        Daftarkan properti Anda di KosAja. Nikmati <strong>Gratis Biaya Listing 30 Hari Pertama</strong> atau hingga kamar pertama Anda terisi bagi mitra pemilik baru.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end">
                    <a href="<?= base_url('/provider/dashboard') ?>" class="btn btn-light text-teal fw-bold py-3 px-4 shadow-sm">
                        <i class="bi bi-plus-circle me-1"></i> Mulai Jadi Penyedia
                    </a>
                </div>
            </div>
        </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Quick filter pill handler
    document.querySelectorAll('.btn-quick-filter').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.btn-quick-filter').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            
            document.querySelectorAll('.property-card-col').forEach(card => {
                if (filter === 'all' || card.dataset.gender === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    // Favorite button handler
    function toggleFavorite(listingId, btn, e) {
        if (e) e.preventDefault();

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
            const icon = btn.querySelector('i');
            if (data.is_favorited) {
                btn.classList.add('active');
                icon.className = 'bi bi-heart-fill';
                btn.title = 'Hapus dari favorit';
            } else {
                btn.classList.remove('active');
                icon.className = 'bi bi-heart';
                btn.title = 'Simpan ke favorit';
            }
        })
        .catch(err => console.error('Error toggling favorite:', err));
    }

    // Switch between Property and Inside Kamar photo view
    function switchCardPhoto(btn, targetView, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const wrapper = btn.closest('.property-img-wrapper');
        if (!wrapper) return;

        wrapper.setAttribute('data-active-view', targetView);
        
        // Update pills
        wrapper.querySelectorAll('.view-pill').forEach(p => p.classList.remove('active'));
        btn.classList.add('active');

        // Update inset preview thumbnail and label
        const inset = wrapper.querySelector('.photo-inset-preview');
        if (inset) {
            const insetImg = inset.querySelector('.inset-img');
            const insetText = inset.querySelector('.inset-text');
            const insetIcon = inset.querySelector('.inset-badge i');
            
            if (targetView === 'inside') {
                // Main is showing inside, so inset shows property exterior
                insetImg.src = inset.dataset.propertySrc;
                insetText.textContent = 'Gedung';
                insetIcon.className = 'bi bi-building me-1 text-teal';
                inset.title = 'Klik untuk kembali ke foto tampak depan gedung';
            } else {
                // Main is showing property, so inset shows inside kamar
                insetImg.src = inset.dataset.insideSrc;
                insetText.textContent = 'Inside';
                insetIcon.className = 'bi bi-door-open-fill me-1 text-teal';
                inset.title = 'Klik untuk melihat foto kamar tidur (inside)';
            }
        }
    }

    // Toggle photo when clicking inset thumbnail
    function toggleCardPhotoInset(inset, event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const wrapper = inset.closest('.property-img-wrapper');
        if (!wrapper) return;

        const currentView = wrapper.getAttribute('data-active-view') || 'property';
        const nextView = currentView === 'property' ? 'inside' : 'property';

        const targetPill = wrapper.querySelector(`.view-pill[data-target="${nextView}"]`);
        if (targetPill) {
            switchCardPhoto(targetPill, nextView, event);
        } else {
            wrapper.setAttribute('data-active-view', nextView);
        }
    }
</script>
<?= $this->endSection() ?>
