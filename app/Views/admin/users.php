<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-users-gear text-teal mr-2"></i> Manajemen Pengguna & Otoritas (IAM)
            </h1>
            <p class="text-muted small mb-0">Kelola akun terdaftar, peran (RBAC), kepatuhan Syariat (Gender), dan status akun pengguna KosAja.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <span class="badge badge-teal px-3 py-2 text-white font-weight-normal">
                <i class="fas fa-user-lock mr-1"></i> RBAC Security Active
            </span>
        </div>
    </div>
</div>

<!-- Alerts -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show text-sm" role="alert">
        <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show text-sm" role="alert">
        <i class="fas fa-exclamation-triangle mr-1"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-teal shadow-sm">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-id-badge text-teal mr-2"></i> Daftar Akun Terdaftar (<?= count($users) ?> Akun)
                </h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover text-sm m-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>WhatsApp</th>
                                <th>Gender (Syariat)</th>
                                <th>Peran (Role)</th>
                                <th>Status</th>
                                <th style="width: 140px;" class="text-center">Aksi Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><code>#<?= esc($u['id']) ?></code></td>
                                    <td class="font-weight-bold"><?= esc($u['name']) ?></td>
                                    <td><?= esc($u['email']) ?></td>
                                    <td><?= esc($u['whatsapp'] ?? '-') ?></td>
                                    <td>
                                        <?php if ($u['gender'] === 'L'): ?>
                                            <span class="badge badge-primary"><i class="fas fa-mars mr-1"></i> Laki-laki</span>
                                        <?php elseif ($u['gender'] === 'P'): ?>
                                            <span class="badge badge-danger"><i class="fas fa-venus mr-1"></i> Perempuan</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($u['role'] === 'super_admin'): ?>
                                            <span class="badge badge-dark">SUPER ADMIN</span>
                                        <?php elseif ($u['role'] === 'admin_daerah'): ?>
                                            <span class="badge badge-danger">ADMIN DAERAH</span>
                                        <?php elseif ($u['role'] === 'provider'): ?>
                                            <span class="badge badge-teal text-white">PENYEDIA (OWNER)</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">PENYEWA (TENANT)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($u['status'] === 'active'): ?>
                                            <span class="badge badge-success"><i class="fas fa-check mr-1"></i> Aktif</span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary"><i class="fas fa-ban mr-1"></i> Ditangguhkan</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($u['id'] != 1): ?>
                                            <a href="<?= base_url('/admin/users/toggle/' . $u['id']) ?>" 
                                               class="btn btn-xs <?= $u['status'] === 'active' ? 'btn-outline-warning' : 'btn-outline-success' ?>">
                                                <?= $u['status'] === 'active' ? '<i class="fas fa-pause mr-1"></i> Tangguhkan' : '<i class="fas fa-play mr-1"></i> Aktifkan' ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted font-italic small">Protected</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
