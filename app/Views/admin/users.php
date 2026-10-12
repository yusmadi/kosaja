<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-users-gear text-teal mr-2"></i> Manajemen Pengguna & Otoritas (IAM)
            </h1>
            <p class="text-muted small mb-0">Kelola akun terdaftar, peran (RBAC), penugasan wilayah operasional (ABAC), dan kepatuhan Syariat (Gender).</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <button type="button" 
                    class="btn btn-teal text-white shadow font-weight-bold px-3 py-2" 
                    style="background-color: #0d9488 !important; color: #ffffff !important; border: 1px solid #0d9488 !important;"
                    data-toggle="modal" 
                    data-bs-toggle="modal" 
                    data-target="#modalAddUser" 
                    data-bs-target="#modalAddUser"
                    onclick="openModalCustom('modalAddUser')"
                    id="btnAddUser">
                <i class="fas fa-user-plus mr-1 me-1"></i> Tambah Pengguna / Admin Daerah
            </button>
        </div>
    </div>
</div>

<!-- Alerts -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show text-sm shadow-sm" role="alert">
        <i class="fas fa-check-circle mr-1"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close btn-close" data-dismiss="alert" data-bs-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show text-sm shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle mr-1"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="close btn-close" data-dismiss="alert" data-bs-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show text-sm shadow-sm" role="alert">
        <h6 class="font-weight-bold mb-1"><i class="fas fa-exclamation-circle mr-1"></i> Terjadi Kesalahan Validasi:</h6>
        <ul class="mb-0 pl-3">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="close btn-close" data-dismiss="alert" data-bs-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-12">
        <div class="card card-outline card-teal shadow-sm">
            <div class="card-header border-0 d-flex justify-content-between align-items-center">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-id-badge text-teal mr-2"></i> Daftar Akun Terdaftar (<?= count($users) ?> Akun)
                </h3>
                <span class="badge badge-teal px-3 py-2 text-white font-weight-normal" style="background-color: #0d9488 !important; color: #ffffff !important;">
                    <i class="fas fa-user-lock mr-1"></i> RBAC & ABAC Active
                </span>
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
                                <th>Wilayah Scope (ABAC)</th>
                                <th>Status</th>
                                <th style="width: 170px;" class="text-center">Aksi & Otoritas</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><code>#<?= esc($u['id']) ?></code></td>
                                    <td class="font-weight-bold">
                                        <?= esc($u['name']) ?>
                                        <?php if ($u['id'] == session()->get('user_id')): ?>
                                            <span class="badge badge-secondary ml-1 small">Anda</span>
                                        <?php endif; ?>
                                    </td>
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
                                            <span class="badge badge-danger font-weight-bold">SUPER ADMIN</span>
                                        <?php elseif ($u['role'] === 'admin_daerah'): ?>
                                            <span class="badge badge-warning text-dark font-weight-bold">ADMIN DAERAH</span>
                                        <?php elseif ($u['role'] === 'provider'): ?>
                                            <span class="badge badge-teal text-white" style="background-color: #0d9488 !important;">PENYEDIA (OWNER)</span>
                                        <?php else: ?>
                                            <span class="badge badge-info">PENYEWA (TENANT)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($u['role'] === 'super_admin'): ?>
                                            <span class="badge badge-dark"><i class="fas fa-globe mr-1"></i> Nasional (Global)</span>
                                        <?php elseif ($u['role'] === 'admin_daerah'): ?>
                                            <div class="d-flex align-items-center">
                                                <span class="badge badge-warning text-dark font-weight-bold">
                                                    <i class="fas fa-location-dot mr-1"></i> <?= esc($u['region_name'] ?? 'Belum Ditugaskan') ?>
                                                </span>
                                                <button type="button" 
                                                        class="btn btn-xs btn-outline-secondary ml-1 ms-1" 
                                                        data-toggle="modal" 
                                                        data-bs-toggle="modal" 
                                                        data-target="#modalAssignRegion<?= $u['id'] ?>" 
                                                        data-bs-target="#modalAssignRegion<?= $u['id'] ?>"
                                                        onclick="openModalCustom('modalAssignRegion<?= $u['id'] ?>')"
                                                        title="Ubah Wilayah Tugas">
                                                    <i class="fas fa-pen-to-square"></i>
                                                </button>
                                            </div>
                                        <?php elseif ($u['role'] === 'provider'): ?>
                                            <span class="text-muted small">Multi-Region</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
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
                                        <div class="d-inline-flex align-items-center">
                                            <?php if ($u['id'] != 1): ?>
                                                <a href="<?= base_url('/admin/users/toggle/' . $u['id']) ?>" 
                                                   class="btn btn-xs <?= $u['status'] === 'active' ? 'btn-outline-warning' : 'btn-outline-success' ?> mr-1 me-1"
                                                   title="<?= $u['status'] === 'active' ? 'Tangguhkan Akun' : 'Aktifkan Akun' ?>">
                                                    <?= $u['status'] === 'active' ? '<i class="fas fa-pause mr-1"></i> Suspend' : '<i class="fas fa-play mr-1"></i> Aktif' ?>
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted font-italic small mr-1 me-1"><i class="fas fa-shield-halved mr-1"></i>Protected</span>
                                            <?php endif; ?>

                                            <?php if ($u['role'] === 'admin_daerah'): ?>
                                                <button type="button" 
                                                        class="btn btn-xs btn-outline-info" 
                                                        data-toggle="modal" 
                                                        data-bs-toggle="modal" 
                                                        data-target="#modalAssignRegion<?= $u['id'] ?>" 
                                                        data-bs-target="#modalAssignRegion<?= $u['id'] ?>"
                                                        onclick="openModalCustom('modalAssignRegion<?= $u['id'] ?>')"
                                                        title="Tugaskan Wilayah Operasional">
                                                    <i class="fas fa-map-location-dot mr-1"></i> Wilayah
                                                </button>
                                            <?php endif; ?>
                                        </div>
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

<!-- Modal Tambah Pengguna / Admin Daerah -->
<div class="modal fade" id="modalAddUser" tabindex="-1" role="dialog" aria-labelledby="modalAddUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content shadow border-0">
            <form action="<?= base_url('/admin/users/create') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-header bg-teal text-white" style="background-color: #0d9488 !important; color: #ffffff !important;">
                    <h5 class="modal-title font-weight-bold text-white" id="modalAddUserLabel">
                        <i class="fas fa-user-plus mr-2 me-2"></i> Tambah Pengguna / Admin Daerah Baru (IAM SA-02)
                    </h5>
                    <button type="button" class="close btn-close text-white" data-dismiss="modal" data-bs-dismiss="modal" onclick="closeModalCustom('modalAddUser')" aria-label="Close" style="color: #ffffff !important; opacity: 1;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info text-sm mb-3">
                        <i class="fas fa-info-circle mr-1"></i>
                        Super Administrator dapat mendaftarkan akun internal baru seperti <strong>Admin Daerah</strong> untuk kota/kabupaten tertentu di Indonesia, lengkap dengan batasan wilayah otoritas (ABAC).
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Contoh: Admin Daerah Kota Yogyakarta" required value="<?= old('name') ?>">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">Email Login <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="Contoh: admin.jogja@kosaja.id" required value="<?= old('email') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">Kata Sandi (Password) <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="font-weight-bold">Nomor WhatsApp Aktif</label>
                            <input type="text" name="whatsapp" class="form-control" placeholder="Contoh: 081234567890" value="<?= old('whatsapp') ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold">Gender (Kepatuhan Syariat) <span class="text-danger">*</span></label>
                            <select name="gender" class="form-control" required>
                                <option value="L" <?= old('gender') === 'L' ? 'selected' : '' ?>>Laki-laki (Ikhwan)</option>
                                <option value="P" <?= old('gender') === 'P' ? 'selected' : '' ?>>Perempuan (Akhwat)</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="font-weight-bold">Peran Akun (Role) <span class="text-danger">*</span></label>
                            <select name="role" id="inputRole" class="form-control font-weight-bold text-dark" required onchange="toggleRegionInput()">
                                <option value="admin_daerah" <?= old('role', 'admin_daerah') === 'admin_daerah' ? 'selected' : '' ?>>ADMIN DAERAH (Regional)</option>
                                <option value="super_admin" <?= old('role') === 'super_admin' ? 'selected' : '' ?>>SUPER ADMIN (Nasional)</option>
                                <option value="provider" <?= old('role') === 'provider' ? 'selected' : '' ?>>PENYEDIA (Owner Kos)</option>
                                <option value="tenant" <?= old('role') === 'tenant' ? 'selected' : '' ?>>PENYEWA (Tenant)</option>
                            </select>
                        </div>
                        <div class="col-md-4 form-group mb-3" id="regionInputGroup">
                            <label class="font-weight-bold">
                                Wilayah Tugas <span class="text-danger" id="regionRequiredStar">*</span>
                            </label>
                            <select name="region_id" id="inputRegionId" class="form-control">
                                <option value="">-- Pilih Wilayah Operasional --</option>
                                <?php foreach ($regions as $r): ?>
                                    <option value="<?= esc($r['id']) ?>" <?= (old('region_id') == $r['id']) ? 'selected' : '' ?>>
                                        <?= esc($r['name']) ?> (<?= strtoupper($r['level']) ?><?= ! empty($r['parent_name']) ? ' - ' . esc($r['parent_name']) : '' ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted d-block mt-1">Admin hanya dapat mengelola data pada wilayah ini & cabangnya.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" onclick="closeModalCustom('modalAddUser')">Batal</button>
                    <button type="submit" class="btn btn-teal text-white font-weight-bold" style="background-color: #0d9488 !important; color: #ffffff !important; border: 1px solid #0d9488 !important;">
                        <i class="fas fa-save mr-1 me-1"></i> Simpan & Daftarkan Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Penugasan Wilayah untuk Masing-masing Admin Daerah -->
<?php foreach ($users as $u): ?>
    <?php if ($u['role'] === 'admin_daerah'): ?>
        <div class="modal fade" id="modalAssignRegion<?= $u['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content shadow border-0">
                    <form action="<?= base_url('/admin/users/assign-region/' . $u['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title font-weight-bold">
                                <i class="fas fa-map-location-dot mr-2 me-2"></i> Penugasan Wilayah Tugas Operasional
                            </h5>
                            <button type="button" class="close btn-close text-dark" data-dismiss="modal" data-bs-dismiss="modal" onclick="closeModalCustom('modalAssignRegion<?= $u['id'] ?>')" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-light border small mb-3">
                                <div><strong>Nama Admin:</strong> <?= esc($u['name']) ?></div>
                                <div><strong>Email:</strong> <?= esc($u['email']) ?></div>
                                <div><strong>Wilayah Saat Ini:</strong> <span class="badge badge-warning text-dark font-weight-bold"><?= esc($u['region_name'] ?? 'Belum Ditugaskan') ?></span></div>
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold">Pilih Wilayah Tugas Operasional Baru <span class="text-danger">*</span></label>
                                <select name="region_id" class="form-control" required>
                                    <option value="">-- Lepas Penugasan (Tanpa Wilayah) --</option>
                                    <?php foreach ($regions as $r): ?>
                                        <option value="<?= esc($r['id']) ?>" <?= ((int)($u['region_id'] ?? 0) === (int)$r['id']) ? 'selected' : '' ?>>
                                            <?= esc($r['name']) ?> (<?= strtoupper($r['level']) ?><?= ! empty($r['parent_name']) ? ' - ' . esc($r['parent_name']) : '' ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted mt-1 d-block">
                                    Setelah disimpan, seluruh antrean moderasi listing, verifikasi KYC, dan laporan mediasi sengketa admin ini akan terisolasi secara otomatis ke wilayah ini.
                                </small>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal" onclick="closeModalCustom('modalAssignRegion<?= $u['id'] ?>')">Batal</button>
                            <button type="submit" class="btn btn-warning font-weight-bold text-dark">
                                <i class="fas fa-check mr-1 me-1"></i> Perbarui Penugasan Wilayah
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<script>
function openModalCustom(modalId) {
    var modalEl = document.getElementById(modalId);
    if (!modalEl) return;
    
    // 1. Try Bootstrap 5
    if (window.bootstrap && window.bootstrap.Modal) {
        try {
            var m = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            m.show();
            return;
        } catch(e) {}
    }
    
    // 2. Try jQuery / Bootstrap 4
    if (window.$ && typeof $('#' + modalId).modal === 'function') {
        try {
            $('#' + modalId).modal('show');
            return;
        } catch(e) {}
    }
    
    // 3. Fallback pure CSS
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
}

function closeModalCustom(modalId) {
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
}

function toggleRegionInput() {
    var roleEl = document.getElementById('inputRole');
    if (!roleEl) return;
    var role = roleEl.value;
    var group = document.getElementById('regionInputGroup');
    var star = document.getElementById('regionRequiredStar');
    var regionSelect = document.getElementById('inputRegionId');
    if (!group || !star || !regionSelect) return;
    
    if (role === 'admin_daerah') {
        group.style.display = 'block';
        star.style.display = 'inline';
        regionSelect.required = true;
    } else if (role === 'provider') {
        group.style.display = 'block';
        star.style.display = 'none';
        regionSelect.required = false;
    } else {
        group.style.display = 'none';
        regionSelect.required = false;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    toggleRegionInput();

    // Auto open modal jika parameter URL action=add_admin
    var urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('action') === 'add_admin') {
        var regionId = urlParams.get('region_id');
        if (regionId) {
            var select = document.getElementById('inputRegionId');
            if (select) {
                select.value = regionId;
            }
        }
        openModalCustom('modalAddUser');
    }
});
</script>
<?= $this->endSection() ?>
