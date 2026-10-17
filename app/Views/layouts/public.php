<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'KosAja — Cari Kos, Kontrakan & Rumah Sewa Amanah') ?></title>
    <meta name="description" content="Platform cari sewa kos, kontrakan, dan rumah sewa berbasis syariat, bebas calo, transparan, dan amanah di Indonesia.">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- KosAja Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body>

    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-kosaja sticky-top">
        <div class="container">
            <a class="navbar-brand-kosaja" href="<?= base_url('/') ?>">
                <i class="bi bi-houses-fill text-teal"></i> KosAja
                <span class="badge-brand">Syariat Ready</span>
            </a>
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="<?= base_url('/search') ?>">Cari Hunian</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="<?= base_url('/favorites') ?>">
                            <i class="bi bi-heart text-danger me-1"></i> Favorit
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="<?= base_url('/#keunggulan') ?>">Keunggulan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-link-custom" href="<?= base_url('/#panduan') ?>">Panduan Syariat</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <div class="dropdown">
                            <button class="btn btn-kosaja-primary btn-sm dropdown-toggle px-3" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i> <?= esc(session()->get('name')) ?>
                                <span class="badge bg-white text-dark ms-1 text-uppercase" style="font-size: 0.65rem;"><?= esc(session()->get('role')) ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                                <li><h6 class="dropdown-header text-muted small"><?= esc(session()->get('email')) ?></h6></li>
                                <li><a class="dropdown-item py-2" href="<?= base_url('/profile') ?>"><i class="bi bi-person-gear me-2 text-teal"></i> Profil & Pengaturan</a></li>
                                <li><a class="dropdown-item py-2" href="<?= base_url('/tenant/dashboard') ?>"><i class="bi bi-speedometer2 me-2 text-teal"></i> Dasbor Penyewa Saya</a></li>
                                <li><a class="dropdown-item py-2" href="<?= base_url('/favorites') ?>"><i class="bi bi-heart-fill me-2 text-danger"></i> Hunian Favorit Saya</a></li>
                                <li><a class="dropdown-item py-2" href="<?= base_url('/applications') ?>"><i class="bi bi-journal-check me-2 text-primary"></i> Pengajuan Sewa Saya</a></li>
                                <li><a class="dropdown-item py-2" href="<?= base_url('/contracts') ?>"><i class="bi bi-file-earmark-check me-2 text-success"></i> Kontrak Sewa Saya</a></li>
                                <?php if (in_array(session()->get('role'), ['super_admin', 'admin_daerah'])): ?>
                                    <li><a class="dropdown-item py-2" href="<?= base_url('/admin/dashboard') ?>"><i class="bi bi-shield-lock me-2 text-danger"></i> Backoffice Admin</a></li>
                                <?php endif; ?>
                                <?php if (session()->get('role') === 'provider'): ?>
                                    <li><a class="dropdown-item py-2" href="<?= base_url('/provider/dashboard') ?>"><i class="bi bi-speedometer2 me-2 text-primary"></i> Dasbor Penyedia</a></li>
                                <?php endif; ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2 text-danger" href="<?= base_url('/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a></li>
                            </ul>
                        </div>
                    <?php else: ?>
                        <div class="dropdown">
                            <button class="btn btn-kosaja-outline btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-speedometer2"></i> Demo Panel
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item py-2" href="<?= base_url('/admin/dashboard') ?>"><i class="bi bi-shield-lock me-2 text-danger"></i> Dasbor Admin</a></li>
                                <li><a class="dropdown-item py-2" href="<?= base_url('/provider/dashboard') ?>"><i class="bi bi-person-workspace me-2 text-primary"></i> Dasbor Penyedia (Owner)</a></li>
                            </ul>
                        </div>
                        <a href="<?= base_url('/login') ?>" class="btn btn-kosaja-primary btn-sm px-3">
                            <i class="bi bi-box-arrow-in-right"></i> Masuk / Daftar
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Injection -->
    <main>
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="footer-kosaja">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="footer-brand mb-3">
                        <i class="bi bi-houses-fill text-teal"></i> KosAja
                    </div>
                    <p class="text-secondary small pe-lg-4">
                        Platform direktori dan persewaan kos, kontrakan, serta rumah sewa transparan tanpa perantara (calo), dengan jaminan kepatuhan syariat dan transaksi aman.
                    </p>
                </div>
                <div class="col-6 col-lg-2">
                    <h6 class="text-white fw-bold mb-3">Kategori</h6>
                    <a href="#" class="footer-link small">Kos Putri</a>
                    <a href="#" class="footer-link small">Kos Putra</a>
                    <a href="#" class="footer-link small">Kontrakan Pasutri</a>
                    <a href="#" class="footer-link small">Rumah Sewa Tahunan</a>
                </div>
                <div class="col-6 col-lg-3">
                    <h6 class="text-white fw-bold mb-3">Wilayah Terpopuler</h6>
                    <a href="#" class="footer-link small">Sleman (UGM / UPN / UNY)</a>
                    <a href="#" class="footer-link small">Kota Yogyakarta (Lempuyangan)</a>
                    <a href="#" class="footer-link small">Bantul (ISI / UMY)</a>
                    <a href="#" class="footer-link small">Kawasan Mlati & Monjali</a>
                </div>
                <div class="col-lg-3">
                    <h6 class="text-white fw-bold mb-3">Bantuan & Hubungi</h6>
                    <p class="small text-secondary mb-2"><a href="https://wa.me/6289516563349" target="_blank" class="text-secondary text-decoration-none"><i class="bi bi-whatsapp me-2 text-success"></i> WhatsApp CS: 0895-1656-3349</a></p>
                    <p class="small text-secondary mb-2"><i class="bi bi-envelope me-2"></i> info@kosaja.id</p>
                    <div class="mt-3">
                        <span class="badge bg-dark border border-secondary text-light py-2 px-3 small">
                            <i class="bi bi-shield-check text-success me-1"></i> Phase 0 CI4 Foundation
                        </span>
                    </div>
                </div>
            </div>
            <hr class="border-secondary my-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-secondary">
                <span>&copy; <?= date('Y') ?> KosAja Indonesia. All rights reserved.</span>
                <span>Dibangun dengan CodeIgniter 4 & Bootstrap 5</span>
            </div>
        </div>
    </footer>

    <!-- Vendor Scripts: jQuery & Bootstrap 5 Bundle -->
    <script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
    <script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
    <!-- App Custom Script -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
