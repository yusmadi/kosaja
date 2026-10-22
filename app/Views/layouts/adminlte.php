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
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body, .main-sidebar, .brand-text {
            font-family: 'Plus Jakarta Sans', sans-serif !important;
        }
        .sidebar-dark-teal .nav-sidebar>.nav-item>.nav-link.active, 
        .sidebar-light-teal .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #0d9488 !important;
            color: #ffffff !important;
        }
        .sidebar-dark-teal .nav-sidebar>.nav-item>.nav-link.active i,
        .sidebar-light-teal .nav-sidebar>.nav-item>.nav-link.active i {
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
        .btn-teal {
            background-color: #0d9488 !important;
            border-color: #0d9488 !important;
            color: #ffffff !important;
        }
        .btn-teal:hover, .btn-teal:focus, .btn-teal:active {
            background-color: #0f766e !important;
            border-color: #0f766e !important;
            color: #ffffff !important;
        }
        .badge-teal, .bg-teal {
            background-color: #0d9488 !important;
            color: #ffffff !important;
        }
        .modal {
            z-index: 1060 !important;
        }
        .modal-backdrop {
            z-index: 1050 !important;
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
            <?php 
                $sessionRole = session()->get('role') ?? ($role ?? 'super_admin');
                $sessionName = session()->get('name') ?? ($sessionRole === 'admin_daerah' ? 'Admin Daerah Sleman' : ($sessionRole === 'provider' ? 'Haji Ahmad Santoso' : 'Super Administrator'));
                $sessionEmail = session()->get('email') ?? ($sessionRole === 'admin_daerah' ? 'admin.sleman@kosaja.id' : ($sessionRole === 'provider' ? 'ahmad@owner.kosaja.id' : 'admin@kosaja.id'));
            ?>
            <!-- Role Switcher for Testing (PRD Multi-Role Validation) -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fas fa-arrows-rotate mr-1 text-warning"></i> 
                    Mode: <strong>
                        <?php if ($sessionRole === 'super_admin'): ?>
                            <span class="text-danger font-weight-bold">Super Admin (Nasional)</span>
                        <?php elseif ($sessionRole === 'admin_daerah'): ?>
                            <?php $currentRegionLabel = (new \App\Libraries\AdminScopeService())->getScopedRegionLabel(); ?>
                            <span class="text-warning font-weight-bold">Admin Daerah (<?= esc($currentRegionLabel) ?>)</span>
                        <?php elseif ($sessionRole === 'provider'): ?>
                            <span class="text-teal font-weight-bold">Mitra Penyedia</span>
                        <?php else: ?>
                            <span class="text-info font-weight-bold">Penyewa (Tenant)</span>
                        <?php endif; ?>
                    </strong>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow border-0" style="min-width: 260px;">
                    <div class="dropdown-header text-uppercase font-weight-bold small text-muted">Beralih Persona Akun (Testing)</div>
                    <a href="<?= base_url('/switch-role/super_admin') ?>" class="dropdown-item <?= $sessionRole === 'super_admin' ? 'active bg-danger text-white' : '' ?>">
                        <i class="fas fa-globe mr-2 text-danger"></i> <strong>Super Admin</strong>
                        <small class="d-block text-muted">Tata Kelola &amp; Pengawasan Nasional</small>
                    </a>
                    <a href="<?= base_url('/switch-role/2') ?>" class="dropdown-item <?= ($sessionRole === 'admin_daerah' && (session()->get('region_id') == 2 || (session()->get('user_id') == 2))) ? 'active bg-warning text-dark' : '' ?>">
                        <i class="fas fa-location-dot mr-2 text-warning"></i> <strong>Admin Daerah (Sleman)</strong>
                        <small class="d-block text-muted">Antrean KYC &amp; Moderasi Sleman</small>
                    </a>
                    <a href="<?= base_url('/switch-role/19') ?>" class="dropdown-item <?= ($sessionRole === 'admin_daerah' && (session()->get('region_id') == 9 || (session()->get('user_id') == 19))) ? 'active bg-warning text-dark' : '' ?>">
                        <i class="fas fa-location-dot mr-2 text-warning"></i> <strong>Admin Daerah (Banda Aceh)</strong>
                        <small class="d-block text-muted">Antrean KYC &amp; Moderasi Banda Aceh</small>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?= base_url('/switch-role/3') ?>" class="dropdown-item <?= ($sessionRole === 'provider' && session()->get('user_id') == 3) ? 'active bg-teal text-white' : '' ?>">
                        <i class="fas fa-store mr-2 text-teal"></i> <strong>Mitra Penyedia (Sleman)</strong>
                        <small class="d-block text-muted">Haji Ahmad Santoso (Terverifikasi)</small>
                    </a>
                    <a href="<?= base_url('/switch-role/29') ?>" class="dropdown-item <?= ($sessionRole === 'provider' && session()->get('user_id') == 29) ? 'active bg-teal text-white' : '' ?>">
                        <i class="fas fa-user-clock mr-2 text-warning"></i> <strong>Calon Mitra Pending (Sleman)</strong>
                        <small class="d-block text-muted">Yusmadi (Status Pending KYC)</small>
                    </a>
                    <a href="<?= base_url('/switch-role/28') ?>" class="dropdown-item <?= ($sessionRole === 'provider' && session()->get('user_id') == 28) ? 'active bg-teal text-white' : '' ?>">
                        <i class="fas fa-store mr-2 text-teal"></i> <strong>Mitra Penyedia (Banda Aceh)</strong>
                        <small class="d-block text-muted">Yusmadi (Terverifikasi)</small>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?= base_url('/switch-role/tenant') ?>" class="dropdown-item <?= $sessionRole === 'tenant' ? 'active bg-info text-white' : '' ?>">
                        <i class="fas fa-user mr-2 text-info"></i> <strong>Penyewa (Tenant)</strong>
                        <small class="d-block text-muted">Eksplorasi Kos, Sewa &amp; Tagihan</small>
                    </a>
                </div>
            </li>

            <!-- User Menu -->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
                    <i class="far fa-user-circle fa-lg mr-1 text-teal"></i>
                    <span class="d-none d-md-inline font-weight-bold">
                        <?= esc($sessionName) ?>
                    </span>
                </a>
                <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right shadow border-0">
                    <li class="user-header bg-dark">
                        <i class="fas fa-user-circle fa-4x text-teal mb-2"></i>
                        <p>
                            <?= esc($sessionName) ?>
                            <small class="text-white-50"><?= esc($sessionEmail) ?></small>
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
                        <?= esc($sessionName) ?>
                    </a>
                    <?php if ($sessionRole === 'super_admin'): ?>
                        <span class="badge badge-danger text-uppercase px-2 py-1" style="font-size: 0.65rem;">
                            <i class="fas fa-globe mr-1"></i> SUPER ADMIN (GLOBAL)
                        </span>
                    <?php elseif ($sessionRole === 'admin_daerah'): ?>
                        <span class="badge badge-warning text-dark text-uppercase px-2 py-1 font-weight-bold" style="font-size: 0.65rem;">
                            <i class="fas fa-location-dot mr-1"></i> ADMIN REGIONAL
                        </span>
                    <?php else: ?>
                        <span class="badge badge-role-provider text-uppercase px-2 py-1" style="font-size: 0.65rem;">
                            <i class="fas fa-store mr-1"></i> MITRA PENYEDIA
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                    
                    <?php if ($sessionRole === 'super_admin'): ?>
                        <!-- ============================================== -->
                        <!-- 1. SUPER ADMIN MENU (UX Blueprint 4.4 & 5.5)   -->
                        <!-- ============================================== -->
                        <li class="nav-header text-uppercase text-danger font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            <i class="fas fa-globe mr-1"></i> Tata Kelola Super Admin
                        </li>
                        
                        <!-- SA-01: Global Dashboard -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/dashboard') ?>" class="nav-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard Global</p>
                            </a>
                        </li>
                        <!-- SA-02: Users (IAM) -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/users') ?>" class="nav-link <?= ($activeMenu ?? '') === 'users' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-users-gear text-warning"></i>
                                <p>Manajemen Akun (IAM)</p>
                            </a>
                        </li>
                        <!-- SA-03: Providers -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/providers') ?>" class="nav-link <?= ($activeMenu ?? '') === 'providers' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-user-check"></i>
                                <p>Mitra Penyedia Global</p>
                            </a>
                        </li>
                        <!-- SA-04: Regions -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/regions') ?>" class="nav-link <?= ($activeMenu ?? '') === 'regions' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-map-location-dot text-danger"></i>
                                <p>Master Wilayah</p>
                            </a>
                        </li>
                        <!-- SA-05: Listings -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/listings') ?>" class="nav-link <?= ($activeMenu ?? '') === 'moderation' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-shield-halved"></i>
                                <p>Master Listing Iklan</p>
                            </a>
                        </li>
                        <!-- SA-06: Transactions -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/transactions') ?>" class="nav-link <?= ($activeMenu ?? '') === 'transactions' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-receipt text-success"></i>
                                <p>Transaksi Finansial</p>
                            </a>
                        </li>
                        <!-- SA-07: Settlements -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/settlements') ?>" class="nav-link <?= ($activeMenu ?? '') === 'settlements' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-money-bill-transfer text-teal"></i>
                                <p>Settlement Dana Mitra</p>
                            </a>
                        </li>
                        <!-- SA-08: Refunds -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/refunds') ?>" class="nav-link <?= ($activeMenu ?? '') === 'refunds' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-hand-holding-dollar text-danger"></i>
                                <p>Pengembalian Dana</p>
                            </a>
                        </li>
                        <!-- SA-09: Disputes Escalation -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/disputes') ?>" class="nav-link <?= ($activeMenu ?? '') === 'disputes' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-scale-balanced text-warning"></i>
                                <p>Eskalasi Sengketa</p>
                            </a>
                        </li>
                        <!-- SA-10: Monetization -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/monetization') ?>" class="nav-link <?= ($activeMenu ?? '') === 'monetization' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-coins text-warning"></i>
                                <p>Kebijakan Monetisasi</p>
                            </a>
                        </li>
                        <!-- SA-11: Configuration -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/configuration') ?>" class="nav-link <?= ($activeMenu ?? '') === 'configuration' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-sliders text-info"></i>
                                <p>Konfigurasi Platform</p>
                            </a>
                        </li>
                        <!-- SA-12: Audit -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/audit') ?>" class="nav-link <?= ($activeMenu ?? '') === 'audit' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-clock-rotate-left text-teal"></i>
                                <p>Audit Trail Global</p>
                            </a>
                        </li>

                    <?php elseif ($sessionRole === 'admin_daerah'): ?>
                        <!-- ============================================== -->
                        <!-- 2. ADMIN DAERAH MENU (UX Blueprint 4.3 & 5.4)  -->
                        <!-- ============================================== -->
                        <li class="nav-header text-uppercase text-warning font-weight-bold" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                            <i class="fas fa-location-dot mr-1"></i> Operasional Admin Daerah
                        </li>
                        
                        <!-- ADM-01: Dashboard Antrean -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/dashboard') ?>" class="nav-link <?= ($activeMenu ?? '') === 'dashboard' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-list-check text-warning"></i>
                                <p>Dasbor Antrean</p>
                            </a>
                        </li>
                        <!-- ADM-02: Provider Verification (KYC) -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/providers') ?>" class="nav-link <?= ($activeMenu ?? '') === 'providers' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-user-check"></i>
                                <p>Verifikasi Mitra (KYC)</p>
                            </a>
                        </li>
                        <!-- ADM-04: Listing Moderation -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/listings') ?>" class="nav-link <?= ($activeMenu ?? '') === 'moderation' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-shield-halved"></i>
                                <p>Moderasi Listing Daerah</p>
                            </a>
                        </li>
                        <!-- ADM-06: Reports (Pengaduan/Laporan Warga) -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/reports') ?>" class="nav-link <?= ($activeMenu ?? '') === 'reports' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-flag text-danger"></i>
                                <p>Laporan Pelanggaran</p>
                            </a>
                        </li>
                        <!-- ADM-08: Disputes (Sengketa Mediasi) -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/disputes') ?>" class="nav-link <?= ($activeMenu ?? '') === 'disputes' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-scale-balanced" style="color: #a78bfa;"></i>
                                <p>Sengketa &amp; Mediasi</p>
                            </a>
                        </li>
                        <!-- ADM-10: Restrictions (Restriksi/Sanksi) -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/restrictions') ?>" class="nav-link <?= ($activeMenu ?? '') === 'restrictions' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-ban text-danger"></i>
                                <p>Restriksi &amp; Sanksi</p>
                            </a>
                        </li>
                        <!-- ADM-11: Regional Reports -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/regional-reports') ?>" class="nav-link <?= ($activeMenu ?? '') === 'regional-reports' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-chart-line text-info"></i>
                                <p>Laporan Regional</p>
                            </a>
                        </li>
                        <!-- ADM-12: Regional Audit Logs -->
                        <li class="nav-item">
                            <a href="<?= base_url('/admin/audit') ?>" class="nav-link <?= ($activeMenu ?? '') === 'audit' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-clock-rotate-left text-teal"></i>
                                <p>Log Audit Regional</p>
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
                            <a href="<?= base_url('/provider/applications') ?>" class="nav-link <?= ($activeMenu ?? '') === 'applications' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-inbox"></i>
                                <p>
                                    Pengajuan Masuk
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/provider/reservations') ?>" class="nav-link <?= ($activeMenu ?? '') === 'reservations' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-clock"></i>
                                <p>
                                    Reservasi Kamar
                                </p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/provider/contracts') ?>" class="nav-link <?= ($activeMenu ?? '') === 'contracts' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-file-contract"></i>
                                <p>Kontrak Penyewa</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?= base_url('/provider/subscription') ?>" class="nav-link <?= ($activeMenu ?? '') === 'subscription' ? 'active' : '' ?>">
                                <i class="nav-icon fas fa-crown"></i>
                                <p>Paket & Langganan</p>
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
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                        <i class="fas fa-triangle-exclamation mr-2"></i> <strong>Akses Dibatasi:</strong> <?= session()->getFlashdata('error') ?>
                        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('message') || session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                        <i class="fas fa-circle-check mr-2"></i> <?= session()->getFlashdata('message') ?? session()->getFlashdata('success') ?>
                        <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('warning')): ?>
                    <div class="alert alert-warning alert-dismissible fade show shadow-sm border-0 text-dark" role="alert">
                        <i class="fas fa-circle-exclamation mr-2"></i> <?= session()->getFlashdata('warning') ?>
                        <button type="button" class="close text-dark" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>

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
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Global Bootstrap 4 & 5 Modal Compatibility Bridge -->
<script>
window.openModalCustom = function(modalId) {
    var modalEl = document.getElementById(modalId);
    if (!modalEl) return;
    if (modalEl.parentElement !== document.body) {
        document.body.appendChild(modalEl);
    }
    if (window.bootstrap && window.bootstrap.Modal) {
        try {
            var m = bootstrap.Modal.getOrCreateInstance(modalEl);
            m.show();
            return;
        } catch(e) {}
    }
    if (window.$ && typeof $('#' + modalId).modal === 'function') {
        try {
            $('#' + modalId).modal('show');
            return;
        } catch(e) {}
    }
    modalEl.classList.add('show');
    modalEl.style.display = 'block';
    modalEl.removeAttribute('aria-hidden');
    modalEl.setAttribute('aria-modal', 'true');
    var existingBd = document.getElementById(modalId + '-backdrop');
    if (!existingBd) {
        var bd = document.createElement('div');
        bd.id = modalId + '-backdrop';
        bd.className = 'modal-backdrop fade show';
        document.body.appendChild(bd);
    }
};

window.closeModalCustom = function(modalId) {
    var modalEl = document.getElementById(modalId);
    if (!modalEl) return;
    if (window.bootstrap && window.bootstrap.Modal) {
        try {
            var m = bootstrap.Modal.getInstance(modalEl);
            if (m) { m.hide(); return; }
        } catch(e) {}
    }
    if (window.$ && typeof $('#' + modalId).modal === 'function') {
        try {
            $('#' + modalId).modal('hide');
            return;
        } catch(e) {}
    }
    modalEl.classList.remove('show');
    modalEl.style.display = 'none';
    modalEl.setAttribute('aria-hidden', 'true');
    modalEl.removeAttribute('aria-modal');
    var bd = document.getElementById(modalId + '-backdrop');
    if (bd) bd.remove();
    document.querySelectorAll('.modal-backdrop').forEach(function(el) { el.remove(); });
};

document.addEventListener('click', function(e) {
    var toggleBtn = e.target.closest('[data-toggle="modal"], [data-bs-toggle="modal"]');
    if (toggleBtn) {
        var targetSelector = toggleBtn.getAttribute('data-bs-target') || toggleBtn.getAttribute('data-target') || toggleBtn.getAttribute('href');
        if (targetSelector && targetSelector.startsWith('#')) {
            var targetId = targetSelector.substring(1);
            if (targetId) {
                e.preventDefault();
                window.openModalCustom(targetId);
            }
        }
    }

    var dismissBtn = e.target.closest('[data-dismiss="modal"], [data-bs-dismiss="modal"]');
    if (dismissBtn) {
        var modalEl = dismissBtn.closest('.modal');
        if (modalEl && modalEl.id) {
            e.preventDefault();
            window.closeModalCustom(modalEl.id);
        }
    }

    var tabBtn = e.target.closest('[data-toggle="pill"], [data-toggle="tab"]');
    if (tabBtn && !tabBtn.hasAttribute('data-bs-toggle')) {
        if (window.bootstrap && window.bootstrap.Tab) {
            try {
                e.preventDefault();
                bootstrap.Tab.getOrCreateInstance(tabBtn).show();
            } catch(e) {}
        }
    }
});
</script>

<?= $this->renderSection('scripts') ?>
</body>
</html>
