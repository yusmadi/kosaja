<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<!-- Breadcrumb & Page Header -->
<section class="bg-white border-bottom py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-teal text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pencarian Properti</li>
            </ol>
        </nav>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <div>
                <h1 class="h3 fw-bold mb-1 text-dark">
                    Eksplorasi Kos, Kontrakan & Rumah Sewa
                </h1>
                <p class="text-muted small mb-0">
                    Menemukan <strong class="text-teal"><?= count($listings) ?></strong> hunian terverifikasi bebas calo & siap huni.
                </p>
            </div>
            <!-- Quick Sort Form -->
            <div class="d-flex align-items-center gap-2">
                <label for="sortSelect" class="small text-muted text-nowrap mb-0"><i class="bi bi-sort-down"></i> Urutkan:</label>
                <select id="sortSelect" class="form-select form-select-sm" style="width: auto;" onchange="updateSort(this.value)">
                    <option value="newest" <?= ($filters['sort'] === 'newest') ? 'selected' : '' ?>>Listing Terbaru</option>
                    <option value="price_asc" <?= ($filters['sort'] === 'price_asc') ? 'selected' : '' ?>>Harga Termurah</option>
                    <option value="price_desc" <?= ($filters['sort'] === 'price_desc') ? 'selected' : '' ?>>Harga Termahal</option>
                    <option value="oldest" <?= ($filters['sort'] === 'oldest') ? 'selected' : '' ?>>Listing Terlama</option>
                </select>
            </div>
        </div>
    </div>
</section>

<!-- Search and Filter Section -->
<section class="py-4">
    <div class="container">
        <div class="row g-4">
            <!-- Filter Sidebar -->
            <div class="col-lg-3">
                <div class="filter-card p-3 p-lg-4 sticky-top" style="top: 85px; z-index: 10;">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span class="fw-bold text-dark"><i class="bi bi-funnel-fill text-teal me-1"></i> Filter Pencarian</span>
                        <a href="<?= base_url('/search') ?>" class="small text-danger text-decoration-none">Reset</a>
                    </div>

                    <form id="filterForm" action="<?= base_url('/search') ?>" method="get">
                        <input type="hidden" name="sort" id="filterSortInput" value="<?= esc($filters['sort']) ?>">

                        <!-- Keyword -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Kata Kunci / Lokasi</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light"><i class="bi bi-search"></i></span>
                                <input type="text" name="keyword" class="form-control" placeholder="Nama kos, kampus, jalan..." value="<?= esc($filters['keyword']) ?>">
                            </div>
                        </div>

                        <!-- Wilayah / Kota -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Wilayah / Daerah</label>
                            <select name="region_id" class="form-select form-select-sm">
                                <option value="">Semua Wilayah</option>
                                <?php foreach ($regions as $r): ?>
                                    <option value="<?= $r['id'] ?>" <?= ((string)$filters['region_id'] === (string)$r['id']) ? 'selected' : '' ?>>
                                        <?= esc($r['name']) ?><?= !empty($r['level']) ? ' (' . esc(ucfirst($r['level'])) . ')' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tipe Properti -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Tipe Properti</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">Semua Tipe</option>
                                <option value="kos" <?= ($filters['type'] === 'kos') ? 'selected' : '' ?>>Kos / Kamar</option>
                                <option value="kontrakan" <?= ($filters['type'] === 'kontrakan') ? 'selected' : '' ?>>Kontrakan Rumah</option>
                                <option value="rumah_sewa" <?= ($filters['type'] === 'rumah_sewa') ? 'selected' : '' ?>>Rumah Sewa</option>
                            </select>
                        </div>

                        <!-- Target Gender / Syariat -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-muted mb-1">Kesesuaian Penghuni</label>
                            <div class="d-flex flex-column gap-2">
                                <div class="form-check form-check-sm">
                                    <input class="form-check-input" type="radio" name="target_gender" id="genderAll" value="" <?= empty($filters['target_gender']) ? 'checked' : '' ?>>
                                    <label class="form-check-label small" for="genderAll">Semua Kategori</label>
                                </div>
                                <div class="form-check form-check-sm">
                                    <input class="form-check-input" type="radio" name="target_gender" id="genderPutri" value="putri" <?= ($filters['target_gender'] === 'putri') ? 'checked' : '' ?>>
                                    <label class="form-check-label small text-danger fw-semibold" for="genderPutri">
                                        <i class="bi bi-gender-female me-1"></i> Khusus Putri
                                    </label>
                                </div>
                                <div class="form-check form-check-sm">
                                    <input class="form-check-input" type="radio" name="target_gender" id="genderPutra" value="putra" <?= ($filters['target_gender'] === 'putra') ? 'checked' : '' ?>>
                                    <label class="form-check-label small text-primary fw-semibold" for="genderPutra">
                                        <i class="bi bi-gender-male me-1"></i> Khusus Putra
                                    </label>
                                </div>
                                <div class="form-check form-check-sm">
                                    <input class="form-check-input" type="radio" name="target_gender" id="genderKeluarga" value="campur_keluarga" <?= ($filters['target_gender'] === 'campur_keluarga') ? 'checked' : '' ?>>
                                    <label class="form-check-label small text-purple fw-semibold" for="genderKeluarga">
                                        <i class="bi bi-people me-1"></i> Pasutri / Keluarga
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Rentang Harga Bulanan -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted mb-1">Harga Bulanan (Rp)</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control form-control-sm" placeholder="Min" value="<?= esc($filters['min_price']) ?>">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control form-control-sm" placeholder="Max" value="<?= esc($filters['max_price']) ?>">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-kosaja-primary btn-sm w-100 py-2 fw-semibold">
                            <i class="bi bi-search me-1"></i> Terapkan Filter
                        </button>
                    </form>
                </div>
            </div>

            <!-- Listings Results Grid -->
            <div class="col-lg-9">
                <!-- Active Filter Tags -->
                <?php 
                    $hasActiveFilter = !empty($filters['keyword']) || !empty($filters['region_id']) || !empty($filters['type']) || !empty($filters['target_gender']) || !empty($filters['min_price']) || !empty($filters['max_price']);
                ?>
                <?php if ($hasActiveFilter): ?>
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-3 p-2 bg-light rounded-3">
                        <span class="small text-muted ms-1"><i class="bi bi-tags me-1"></i> Filter Aktif:</span>
                        <?php if (!empty($filters['keyword'])): ?>
                            <span class="badge bg-white text-dark border">Kata Kunci: "<?= esc($filters['keyword']) ?>"</span>
                        <?php endif; ?>
                        <?php if (!empty($filters['type'])): ?>
                            <span class="badge bg-white text-dark border">Tipe: <?= esc(ucfirst(str_replace('_', ' ', $filters['type']))) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($filters['target_gender'])): ?>
                            <span class="badge bg-white text-dark border">Penghuni: <?= esc(ucfirst($filters['target_gender'])) ?></span>
                        <?php endif; ?>
                        <?php if (!empty($filters['min_price']) || !empty($filters['max_price'])): ?>
                            <span class="badge bg-white text-dark border">Rp <?= number_format((float)($filters['min_price'] ?: 0), 0, ',', '.') ?> - Rp <?= number_format((float)($filters['max_price'] ?: 0), 0, ',', '.') ?></span>
                        <?php endif; ?>
                        <a href="<?= base_url('/search') ?>" class="btn btn-sm btn-link text-danger text-decoration-none p-0 ms-auto me-2 small">Hapus Semua</a>
                    </div>
                <?php endif; ?>

                <?php if (empty($listings)): ?>
                    <div class="card border-0 shadow-sm text-center py-5">
                        <div class="card-body py-5">
                            <div class="mb-3 text-muted">
                                <i class="bi bi-search display-3 opacity-50"></i>
                            </div>
                            <h3 class="h4 fw-bold text-dark mb-2">Tidak Ditemukan Properti yang Cocok</h3>
                            <p class="text-muted small mx-auto mb-4" style="max-width: 480px;">
                                Coba kurangi kriteria filter atau gunakan kata kunci lokasi lain (contoh: Pogung, Seturan, Sleman, atau UGM).
                            </p>
                            <a href="<?= base_url('/search') ?>" class="btn btn-kosaja-primary px-4">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reset Semua Filter
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="row g-4">
                        <?php foreach ($listings as $item): ?>
                            <?php $isFav = in_array((int)$item['id'], $userFavorites); ?>
                            <div class="col-md-6 col-xl-4">
                                <div class="property-card">
                                    <div class="property-img-wrapper position-relative">
                                        <img src="<?= base_url(esc($item['photo_url'] ?? 'assets/images/sample-kos-1.jpg')) ?>" 
                                             alt="<?= esc($item['title']) ?>" 
                                             class="property-img">
                                        
                                        <!-- Favorite Button -->
                                        <button type="button" 
                                                class="btn-favorite-icon <?= $isFav ? 'active' : '' ?>" 
                                                data-id="<?= $item['id'] ?>" 
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

                                        <!-- Type Tag -->
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
                                        <div class="small text-muted mb-2">
                                            <i class="bi bi-door-open me-1 text-teal"></i> <?= esc($item['unit_name']) ?> (<?= esc($item['unit_size'] ?? '3x4 m') ?>)
                                        </div>
                                        <p class="text-muted small mb-3 flex-grow-1" style="font-size: 0.85rem; line-height: 1.4;">
                                            <?= character_limiter(esc($item['description']), 75) ?>
                                        </p>
                                        <div class="d-flex align-items-center justify-content-between pt-3 border-top">
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
        </div>
    </div>
</section>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function updateSort(val) {
        document.getElementById('filterSortInput').value = val;
        document.getElementById('filterForm').submit();
    }

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
</script>
<?= $this->endSection() ?>
