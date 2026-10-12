<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-inbox text-teal mr-2"></i>Pengajuan Sewa Masuk
                </h1>
                <p class="text-muted small mb-0">Tinjau dan kelola permohonan sewa dari calon penyewa properti Anda</p>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?= base_url('/provider/reservations') ?>" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-clock mr-1"></i> Lihat Reservasi Kamar Aktif
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <!-- Status Filter Tabs -->
        <div class="card card-outline card-teal shadow-sm mb-4">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentTab === 'all' || empty($currentTab)) ? 'active bg-teal' : '' ?>" href="<?= base_url('/provider/applications?status=all') ?>">
                            Semua <span class="badge badge-light ml-1"><?= $counts['all'] ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentTab === 'pending' ? 'active bg-warning text-dark font-weight-bold' : '' ?>" href="<?= base_url('/provider/applications?status=pending') ?>">
                            <i class="fas fa-hourglass-half mr-1"></i> Menunggu Review 
                            <span class="badge badge-warning ml-1"><?= $counts['pending'] ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentTab === 'approved' ? 'active bg-success' : '' ?>" href="<?= base_url('/provider/applications?status=approved') ?>">
                            <i class="fas fa-check mr-1"></i> Disetujui 
                            <span class="badge badge-light ml-1"><?= $counts['approved'] ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentTab === 'rejected' ? 'active bg-danger' : '' ?>" href="<?= base_url('/provider/applications?status=rejected') ?>">
                            <i class="fas fa-times mr-1"></i> Ditolak 
                            <span class="badge badge-light ml-1"><?= $counts['rejected'] ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= $currentTab === 'expired' ? 'active bg-secondary' : '' ?>" href="<?= base_url('/provider/applications?status=expired') ?>">
                            <i class="fas fa-clock mr-1"></i> Kedaluwarsa 
                            <span class="badge badge-light ml-1"><?= $counts['expired'] ?></span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="card-body p-0">
                <?php if (empty($applications)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3 opacity-50"></i>
                        <h5 class="text-secondary font-weight-bold">Tidak Ada Pengajuan Sewa</h5>
                        <p class="text-muted small mb-0">Belum ada pengajuan sewa dengan filter status ini.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 80px;">ID</th>
                                    <th>Calon Penyewa</th>
                                    <th>Kamar & Properti</th>
                                    <th>Rencana Sewa</th>
                                    <th>Nominal Sewa</th>
                                    <th>Status</th>
                                    <th class="text-right" style="width: 150px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($applications as $app): ?>
                                    <?php 
                                        $statusBadge = match($app['status']) {
                                            'pending'   => 'badge-warning',
                                            'approved'  => 'badge-success',
                                            'rejected'  => 'badge-danger',
                                            'cancelled' => 'badge-secondary',
                                            'expired'   => 'badge-dark',
                                            default     => 'badge-light',
                                        };
                                        $statusLabel = match($app['status']) {
                                            'pending'   => 'Menunggu Review',
                                            'approved'  => 'Disetujui',
                                            'rejected'  => 'Ditolak',
                                            'cancelled' => 'Dibatalkan',
                                            'expired'   => 'Kedaluwarsa',
                                            default     => ucfirst($app['status']),
                                        };
                                    ?>
                                    <tr>
                                        <td>
                                            <span class="font-weight-bold text-muted">#<?= str_pad($app['id'], 4, '0', STR_PAD_LEFT) ?></span>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark"><?= esc($app['tenant_name']) ?></div>
                                            <div class="small text-muted">
                                                <i class="fas <?= $app['tenant_gender'] === 'L' ? 'fa-mars text-primary' : 'fa-venus text-danger' ?> mr-1"></i>
                                                <?= $app['tenant_gender'] === 'L' ? 'Laki-laki (Putra)' : 'Perempuan (Putri)' ?>
                                            </div>
                                            <div class="small text-muted">
                                                <i class="fab fa-whatsapp text-success mr-1"></i><?= esc($app['tenant_whatsapp'] ?? '-') ?>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark"><?= esc($app['unit_name']) ?></div>
                                            <div class="small text-muted"><?= esc($app['property_name']) ?></div>
                                            <span class="badge badge-pill <?= $app['target_gender'] === 'putra' ? 'badge-primary' : ($app['target_gender'] === 'putri' ? 'badge-danger' : 'badge-purple text-white') ?>" style="<?= $app['target_gender'] === 'campur_keluarga' ? 'background-color: #7c3aed;' : '' ?> font-size: 0.68rem;">
                                                Khusus <?= esc(ucfirst($app['target_gender'] === 'campur_keluarga' ? 'Pasutri/Keluarga' : $app['target_gender'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="small font-weight-bold text-dark">Mulai: <?= date('d M Y', strtotime($app['start_date'])) ?></div>
                                            <div class="small text-muted">Durasi: <?= esc($app['duration_months']) ?> Bulan</div>
                                            <div class="small text-muted">Skema: <?= ucfirst(str_replace('_', ' ', $app['payment_schedule'])) ?></div>
                                        </td>
                                        <td>
                                            <strong class="text-teal">Rp <?= number_format($app['rental_amount'], 0, ',', '.') ?></strong>
                                            <div class="text-muted" style="font-size: 0.72rem;">Termin Awal</div>
                                        </td>
                                        <td>
                                            <span class="badge <?= $statusBadge ?> px-2 py-1">
                                                <?= esc($statusLabel) ?>
                                            </span>
                                            <?php if ($app['status'] === 'approved' && !empty($app['reservation_expires_at'])): ?>
                                                <div class="text-muted" style="font-size: 0.70rem;">
                                                    Batas: <?= date('d M, H:i', strtotime($app['reservation_expires_at'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?= base_url('/provider/applications/' . $app['id']) ?>" class="btn btn-sm btn-teal font-weight-bold">
                                                <i class="fas fa-clipboard-check mr-1"></i> Review
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
