<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-user-check text-teal mr-2"></i> Verifikasi Identitas Mitra Provider (KYC)
            </h1>
            <p class="text-muted small mb-0">Tinjau kelengkapan dokumen KTP dan data rekening bank pemilik sebelum listing dapat aktif secara komersial.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/admin/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Overview
            </a>
        </div>
    </div>
</div>

<!-- Flash alerts -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i> <?= session()->getFlashdata('warning') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-times-circle mr-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Scope Alert Banner -->
<div class="alert alert-light border shadow-sm mb-3 d-flex flex-wrap align-items-center justify-content-between">
    <div>
        <i class="fas fa-shield-halved text-teal mr-2"></i>
        <strong>Otoritas Wilayah Penugasan:</strong>
        <span class="badge badge-teal text-white ml-1"><?= esc($scopedLabel) ?></span>
    </div>
    <small class="text-muted mt-1 mt-sm-0">
        <?= $isSuperAdmin ? 'Super Admin memiliki wewenang penuh memantau & memoderasi seluruh wilayah Indonesia.' : 'Admin Daerah berwenang memeriksa dan memverifikasi dokumen mitra di wilayah tugas ini.' ?>
    </small>
</div>

<!-- Filter Tabs -->
<div class="mb-3">
    <div class="btn-group shadow-sm">
        <a href="<?= base_url('/admin/providers?status=all') ?>" class="btn btn-sm <?= ($statusFilter === 'all') ? 'btn-teal text-white' : 'btn-light border' ?>">
            Semua Mitra <span class="badge badge-light ml-1"><?= $counts['all'] ?></span>
        </a>
        <a href="<?= base_url('/admin/providers?status=pending') ?>" class="btn btn-sm <?= ($statusFilter === 'pending') ? 'btn-warning text-dark font-weight-bold' : 'btn-light border' ?>">
            <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi <span class="badge badge-danger ml-1"><?= $counts['pending'] ?></span>
        </a>
        <a href="<?= base_url('/admin/providers?status=verified') ?>" class="btn btn-sm <?= ($statusFilter === 'verified') ? 'btn-success' : 'btn-light border' ?>">
            <i class="fas fa-check mr-1"></i> Terverifikasi <span class="badge <?= ($statusFilter === 'verified') ? 'badge-light text-success' : 'badge-secondary' ?> ml-1"><?= $counts['verified'] ?></span>
        </a>
        <a href="<?= base_url('/admin/providers?status=rejected') ?>" class="btn btn-sm <?= ($statusFilter === 'rejected') ? 'btn-danger' : 'btn-light border' ?>">
            <i class="fas fa-times mr-1"></i> Ditolak <span class="badge <?= ($statusFilter === 'rejected') ? 'badge-light text-danger' : 'badge-secondary' ?> ml-1"><?= $counts['rejected'] ?></span>
        </a>
        <?php if (!empty($counts['suspended'])): ?>
        <a href="<?= base_url('/admin/providers?status=suspended') ?>" class="btn btn-sm <?= ($statusFilter === 'suspended') ? 'btn-dark text-white' : 'btn-light border' ?>">
            <i class="fas fa-ban mr-1"></i> Ditangguhkan <span class="badge <?= ($statusFilter === 'suspended') ? 'badge-light text-dark' : 'badge-secondary' ?> ml-1"><?= $counts['suspended'] ?></span>
        </a>
        <?php endif; ?>
    </div>
</div>

<div class="card card-outline card-teal shadow-sm">
    <div class="card-header border-0">
        <h3 class="card-title font-weight-bold">
            <i class="fas fa-users-viewfinder text-teal mr-2"></i> Daftar Mitra Penyedia (<?= count($providers) ?> Data)
        </h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover m-0 text-sm align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>Mitra & Kontak</th>
                        <th>Wilayah Operasional</th>
                        <th>Status KYC</th>
                        <th>Dokumen KTP</th>
                        <th>Rekening Bank</th>
                        <th>Inventaris</th>
                        <th class="text-center" style="width: 200px;">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($providers)): ?>
                        <?php foreach ($providers as $p): ?>
                            <tr>
                                <td>
                                    <div class="font-weight-bold text-dark"><?= esc($p['name']) ?></div>
                                    <small class="text-muted d-block"><i class="fas fa-envelope mr-1"></i> <?= esc($p['email']) ?></small>
                                    <small class="text-muted d-block"><i class="fab fa-whatsapp text-success mr-1"></i> <?= esc($p['whatsapp'] ?? '-') ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-light border text-dark font-weight-bold">
                                        <i class="fas fa-map-marker-alt text-danger mr-1"></i> <?= esc($p['region_name'] ?? 'Belum Ditugaskan') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php $st = $p['verification_status'] ?? 'pending'; ?>
                                    <?php if ($st === 'verified'): ?>
                                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> Terverifikasi</span>
                                    <?php elseif ($st === 'rejected'): ?>
                                        <span class="badge badge-danger"><i class="fas fa-times-circle mr-1"></i> Ditolak</span>
                                        <?php if (!empty($p['rejection_reason'])): ?>
                                            <small class="d-block text-danger mt-1" style="max-width: 180px;">"<?= esc($p['rejection_reason']) ?>"</small>
                                        <?php endif; ?>
                                    <?php elseif ($st === 'suspended'): ?>
                                        <span class="badge badge-dark">Ditangguhkan</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning text-dark"><i class="fas fa-hourglass-start mr-1"></i> Menunggu Review</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($p['id_card_photo'])): ?>
                                        <a href="<?= \App\Libraries\StorageService::getUrl($p['id_card_photo']) ?>" target="_blank" class="btn btn-xs btn-outline-info">
                                            <i class="fas fa-image mr-1"></i> Lihat KTP
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted text-xs"><i class="fas fa-ban mr-1"></i> Belum unggah</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="font-weight-bold text-dark"><?= esc($p['bank_name'] ?? 'BSI') ?></div>
                                    <small class="text-muted"><?= esc($p['bank_account_number'] ?? '-') ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-light border"><?= $p['total_properties'] ?> Properti</span>
                                    <span class="badge badge-light border"><?= $p['total_units'] ?> Unit Kamar</span>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($p['profile_id'])): ?>
                                        <?php if ($p['verification_status'] !== 'verified'): ?>
                                            <form action="<?= base_url('/admin/providers/verify/' . $p['profile_id']) ?>" method="post" class="d-inline form-verify" data-provider-name="<?= esc($p['name']) ?>">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-xs btn-success font-weight-bold" title="Setujui Verifikasi">
                                                    <i class="fas fa-check mr-1"></i> Setujui
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($p['verification_status'] !== 'rejected'): ?>
                                            <button type="button" class="btn btn-xs btn-outline-danger" data-toggle="modal" data-target="#rejectModal<?= $p['profile_id'] ?>" title="Tolak Pengajuan">
                                                <i class="fas fa-times mr-1"></i> Tolak
                                            </button>

                                            <!-- Modal Tolak -->
                                            <div class="modal fade" id="rejectModal<?= $p['profile_id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content text-left">
                                                        <form action="<?= base_url('/admin/providers/reject/' . $p['profile_id']) ?>" method="post">
                                                            <?= csrf_field() ?>
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Tolak Verifikasi Mitra</h5>
                                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                                    <span aria-hidden="true">&times;</span>
                                                                </button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <p class="text-sm text-dark">
                                                                    Anda akan menolak pengajuan verifikasi dari <strong><?= esc($p['name']) ?></strong>. Mohon berikan alasan penolakan secara spesifik agar mitra dapat memperbaiki:
                                                                </p>
                                                                <div class="form-group mb-0">
                                                                    <label class="font-weight-bold text-sm">Alasan Penolakan <span class="text-danger">*</span></label>
                                                                    <textarea name="reason" class="form-control" rows="3" placeholder="Contoh: Foto KTP terpotong/buram, atau nomor rekening tidak sesuai nama KTP." required></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-danger btn-sm font-weight-bold">Konfirmasi Tolak</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <?php if ($p['verification_status'] === 'verified'): ?>
                                            <form action="<?= base_url('/admin/providers/suspend/' . $p['profile_id']) ?>" method="post" class="d-inline form-suspend" data-provider-name="<?= esc($p['name']) ?>">
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-xs btn-outline-dark" title="Suspend Mitra">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted text-xs">Profil belum dibuat</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                Tidak ada data penyedia dengan status yang dipilih.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // SweetAlert2 Confirmation for "Setujui"
    document.querySelectorAll('.form-verify').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (form.dataset.confirmed === 'true') {
                return;
            }
            e.preventDefault();
            var providerName = form.getAttribute('data-provider-name') || 'mitra ini';

            Swal.fire({
                title: 'Setujui Verifikasi Mitra?',
                html: 'Apakah Anda yakin ingin menyetujui verifikasi identitas KYC untuk <strong>' + providerName + '</strong>?<br><small class="text-muted d-block mt-2">Setelah disetujui, mitra dapat mengaktifkan listing properti dan menerima reservasi.</small>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d9488',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-check mr-1"></i> Ya, Setujui',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: false,
                customClass: {
                    confirmButton: 'btn btn-teal px-3 py-2 mx-1',
                    cancelButton: 'btn btn-secondary px-3 py-2 mx-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            });
        });
    });

    // SweetAlert2 Confirmation for "Suspend Mitra"
    document.querySelectorAll('.form-suspend').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            if (form.dataset.confirmed === 'true') {
                return;
            }
            e.preventDefault();
            var providerName = form.getAttribute('data-provider-name') || 'mitra ini';

            Swal.fire({
                title: 'Tangguhkan Status Mitra?',
                html: 'Apakah Anda yakin ingin menangguhkan akun mitra <strong>' + providerName + '</strong>?<br><small class="text-muted d-block mt-2">Akses operasional dan listing properti milik mitra akan dinonaktifkan sementara.</small>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-ban mr-1"></i> Ya, Tangguhkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                customClass: {
                    confirmButton: 'btn btn-danger px-3 py-2 mx-1',
                    cancelButton: 'btn btn-secondary px-3 py-2 mx-1'
                },
                buttonsStyling: false
            }).then(function(result) {
                if (result.isConfirmed) {
                    form.dataset.confirmed = 'true';
                    form.submit();
                }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
