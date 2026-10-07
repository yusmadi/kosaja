<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'KosAja Panel') ?> | KosAja Dashboard</title>

    <!-- Google Font: Plus Jakarta Sans & Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- AdminLTE Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets/vendor/adminlte/adminlte.min.css') ?>">
    
    <style>
        body, .main-sidebar, .brand-text {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .sidebar-dark-teal .nav-sidebar>.nav-item>.nav-link.active, 
        .sidebar-light-teal .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #0d9488 !important;
            color: #ffffff !important;
        }
        .badge-role-admin {
            background-color: #dc2626;
            color: #fff;
        }
        .badge-role-provider {
            background-color: #0d9488;
            color: #fff;
        }
        .stat-card-custom {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="<?= base_url('/') ?>" class="nav-link" target="_blank">
                    <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Web Publik
                </a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Role Switcher for MVP Testing -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fas fa-arrows-rotate mr-1"></i> Mode: <strong><?= ($role ?? 'admin') === 'admin' ? 'Backoffice Admin' : 'Mitra Penyedia' ?></strong>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    <a href="<?= base_url('/admin/dashboard') ?>" class="dropdown-item">
                        <i class="fas fa-user-shield text-danger mr-2"></i> Mode Administrator
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?= base_url('/provider/dashboard') ?>" class="dropdown-item">
                        <i class="fas fa-store text-teal mr-2"></i> Mode Penyedia (Owner)
                    </a>
                </div>
            </li>

            <!-- User Menu -->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <i class="far fa-user-circle fa-lg mr-1 text-teal"></i>
                    <span class="d-none d-md-inline font-weight-bold">
                        <?= ($role ?? 'admin') === 'admin' ? 'Super Admin' : 'Haji Ahmad Santoso' ?>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <li class="user-header bg-dark">
                        <i class="fas fa-user-circle fa-4x text-teal mb-2"></i>
                        <p>
                            <?= ($role ?? 'admin') === 'admin' ? 'Super Administrator' : 'Haji Ahmad Santoso' ?>
                            <small class="text-white-50"><?= ($role ?? 'admin') === 'admin' ? 'admin@kosaja.id' : 'ahmad@owner.kosaja.id' ?></small>
                        </p>
                    </li>
                    <li class="user-footer">
                        <a href="<?= base_url('/profile') ?>" class="btn btn-default btn-flat">Profil Saya</a>
                        <a href="<?= base_url('/logout') ?>" class="btn btn-default btn-flat float-right text-danger">Keluar</a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-teal elevation-3 bg-dark">
        <!-- Brand Logo -->
        <a href="<?= base_url('/') ?>" class="brand-link border-bottom border-secondary text-decoration-none">
            <span class="brand-image ml-2"><i class="bi bi-houses-fill text-teal fa-2x"></i></span>
            <span class="brand-text font-weight-bold ml-2 text-white">KosAja Panel</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar user panel -->
            <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center border-bottom border-secondary">
                <div class="image ml-2">
                    <i class="fas fa-circle-user fa-2x text-muted"></i>
                </div>
                <div class="info">
                    <a href="<?= base_url('/profile') ?>" class="d-block font-weight-bold text-white text-decoration-none">
                        <?= esc(session()->get('name') ?? (($role ?? 'admin') === 'admin' ? 'Super Admin' : 'Haji Ahmad Santoso')) ?>
                    </a>
                    <span class="badge <?= ($role ?? 'admin') === 'admin' ? 'badge-role-admin' : 'badge-role-provider' ?> text-uppercase" style="font-size: 0.68rem;">
                        <?= esc(session()->get('role') ?? (($role ?? 'admin') === 'admin' ? 'ADMINISTRATOR' : 'MITRA PENYEDIA')) ?>
                    </span>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    
                    <?php if (($role ?? 'admin') === 'admin'): ?>
                        <!-- ADMIN MENU -->
                        <li class="nav-header text-uppercase text-muted font-weight-bold" style="font-size: 0.72rem;">Backoffice Super Admin</li>
                        
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/dashboard') ?>" class="nav-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard Overview</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/users') ?>" class="nav-link <?= ($activeMenu ?? '') === 'users' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-users-gear"></i>
                                <p>Manajemen Akun (IAM)</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/regions') ?>" class="nav-link <?= ($activeMenu ?? '') === 'regions' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-map-location-dot"></i>
                                <p>Wilayah Operasional</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/providers') ?>" class="nav-link <?= ($activeMenu ?? '') === 'providers' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-user-check"></i>
                                <p>
                                    Verifikasi Penyedia (KYC)
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/dashboard#moderation') ?>" class="nav-link <?= ($activeMenu ?? '') === 'moderation' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-shield-halved"></i>
                                <p>Moderasi Listing Iklan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/dashboard#audit') ?>" class="nav-link <?= ($activeMenu ?? '') === 'audit' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-clock-rotate-left"></i>
                                <p>Audit Trail (Logs)</p>
                            </a>
                        </li>

                    <?php else: ?>
                        <!-- PROVIDER MENU -->
                        <li class="nav-header text-uppercase text-muted font-weight-bold" style="font-size: 0.72rem;">Area Pengelola & Pemilik</li>

                        <li class="nav-item">
                            <a href="<?= base_url('/provider/dashboard') ?>" class="nav-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-chart-pie"></i>
                                <p>Ringkasan Dasbor</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/provider/verification') ?>" class="nav-link <?= ($activeMenu ?? '') === 'verification' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-id-card"></i>
                                <p>Verifikasi Identitas (KYC)</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/provider/properties') ?>" class="nav-link <?= ($activeMenu ?? '') === 'properties' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-building"></i>
                                <p>Properti Saya</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/provider/units') ?>" class="nav-link <?= ($activeMenu ?? '') === 'units' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-bed"></i>
                                <p>Unit & Kamar</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/provider/listings') ?>" class="nav-link <?= ($activeMenu ?? '') === 'listings' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-bullhorn"></i>
                                <p>Listing Iklan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/provider/dashboard#applications') ?>" class="nav-link <?= ($activeMenu ?? '') === 'applications' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-inbox"></i>
                                <p>
                                    Pengajuan Masuk
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/provider/dashboard#contracts') ?>" class="nav-link <?= ($activeMenu ?? '') === 'contracts' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-file-contract"></i>
                                <p>Kontrak Penyewa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/provider/verification') ?>" class="nav-link <?= ($activeMenu ?? '') === 'payout' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-wallet"></i>
                                <p>Settlement & Rekening</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/profile') ?>" class="nav-link">
                                <i class="nav-icon fas fa-user-gear"></i>
                                <p>Profil & Keamanan</p>
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-header text-uppercase text-muted font-weight-bold mt-3" style="font-size: 0.72rem;">Sistem</li>
                    <li class="nav-item">
                        <a href="<?= base_url('/') ?>" class="nav-link text-warning">
                            <i class="nav-icon fas fa-globe"></i>
                            <p>Kembali ke Web</p>
                        </a>
                    </li>
                </ul>
            </nav>
            <!-- /.sidebar-menu -->
        </div>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Main content -->
        <section class="content pt-3 pb-5">
            <div class="container-fluid">
                <?= $this->renderSection('content') ?>
            </div>
        </section>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <footer class="main-footer text-sm">
        <strong>&copy; <?= date('Y') ?> <a href="<?= base_url('/') ?>" class="text-teal">KosAja Platform</a>.</strong>
        AdminLTE 3 & CodeIgniter 4 Integration (Phase 0).
        <div class="float-right d-none d-sm-inline-block">
            <b>Environment:</b> <?= ENVIRONMENT ?>
        </div>
    </footer>
</div>
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->
<!-- jQuery -->
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js') ?>"></script>
<!-- Bootstrap 4 / 5 -->
<script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<!-- AdminLTE App -->
<script src="<?= base_url('assets/vendor/adminlte/adminlte.min.js') ?>"></script>

<?= $this->renderSection('scripts') ?>
</body>
</html>
