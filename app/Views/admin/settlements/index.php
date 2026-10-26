<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<!-- Header -->
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-7">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-money-bill-transfer text-teal mr-2"></i> 
                <?= $isSuperAdmin ? 'Otorisasi Pencairan Dana Mitra (SA-07)' : 'Verifikasi Pencairan Sewa Mitra (ADM-09)' ?>
            </h1>
            <p class="text-muted small mb-0">
                <?= $isSuperAdmin 
                    ? 'Pusat otorisasi transfer dana sewa dari rekening penampung (escrow) ke rekening bank resmi pemilik properti.' 
                    : 'Verifikasi faktual kesiapan kamar & kepatuhan sewa di wilayah tugas Anda sebelum diteruskan ke Super Admin.' ?>
            </p>
        </div>
        <div class="col-sm-5 text-sm-right mt-2 mt-sm-0">
            <span class="badge badge-primary px-3 py-2 mr-1 shadow-sm">
                <i class="fas fa-location-dot mr-1"></i> Scope: <?= esc($scopedLabel) ?>
            </span>
            <span class="badge <?= $isSuperAdmin ? 'badge-success' : 'badge-warning text-dark' ?> px-3 py-2 shadow-sm">
                <i class="fas <?= $isSuperAdmin ? 'fa-shield-halved' : 'fa-user-check' ?> mr-1"></i>
                <?= $isSuperAdmin ? 'Checker (Otorisator Finansial)' : 'Maker (Verifikator Lapangan)' ?>
            </span>
        </div>
    </div>
</div>

<!-- Flash Alerts -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show shadow-sm text-dark" role="alert">
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

<!-- Workflow Info Callout -->
<div class="card bg-gradient-light border shadow-sm mb-4">
    <div class="card-body p-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap">
            <div class="d-flex align-items-center mb-2 mb-md-0">
                <i class="fas fa-shield-halved fa-2x text-teal mr-3"></i>
                <div>
                    <strong class="text-dark d-block">Protokol Tata Kelola Finansial Dua Lapis (Maker-Checker)</strong>
                    <small class="text-muted">
                        Pemisahan kontrol antara pemeriksaan operasional lapangan dengan eksekusi mutasi kas platform.
                    </small>
                </div>
            </div>
            <div class="d-flex align-items-center flex-wrap gap-2 text-xs">
                <div class="badge badge-light border p-2 text-left mr-2">
                    <span class="font-weight-bold text-dark d-block">1. Tahap Verifikasi (Maker):</span>
                    <span class="text-muted">Admin Daerah konfirmasi check-in aman &amp; no dispute.</span>
                </div>
                <div class="text-muted mr-2"><i class="fas fa-arrow-right"></i></div>
                <div class="badge badge-light border p-2 text-left">
                    <span class="font-weight-bold text-dark d-block">2. Tahap Otorisasi (Checker):</span>
                    <span class="text-muted">Super Admin transfer dana dari escrow ke rekening mitra.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Settlement Stats -->
<div class="row">
    <!-- Menunggu Verifikasi Daerah -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box shadow-sm border-0" style="border-left: 4px solid #f59e0b !important;">
            <span class="info-box-icon bg-warning text-dark"><i class="fas fa-hourglass-half"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Menunggu Verifikasi Lapangan</span>
                <span class="info-box-number text-dark font-weight-bold" style="font-size: 1.25rem;">
                    <?= esc($stats['count_pending']) ?> Permohonan
                </span>
                <small class="text-muted">Rp <?= number_format($stats['total_pending'], 0, ',', '.') ?> (Maker Pending)</small>
            </div>
        </div>
    </div>

    <!-- Siap Dicairkan Super Admin -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box shadow-sm border-0" style="border-left: 4px solid #0d9488 !important;">
            <span class="info-box-icon bg-teal text-white"><i class="fas fa-file-circle-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Siap Otorisasi Transfer</span>
                <span class="info-box-number text-teal font-weight-bold" style="font-size: 1.25rem;">
                    <?= esc($stats['count_verified']) ?> Terverifikasi
                </span>
                <small class="text-teal font-weight-bold">Rp <?= number_format($stats['total_verified'], 0, ',', '.') ?> (Checker Ready)</small>
            </div>
        </div>
    </div>

    <!-- Total Dana Tersalurkan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box shadow-sm border-0" style="border-left: 4px solid #10b981 !important;">
            <span class="info-box-icon bg-success text-white"><i class="fas fa-check-double"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Dana Berhasil Tersalurkan</span>
                <span class="info-box-number text-success font-weight-bold" style="font-size: 1.25rem;">
                    Rp <?= number_format($stats['total_completed'], 0, ',', '.') ?>
                </span>
                <small class="text-muted">Telah ditransfer ke mitra</small>
            </div>
        </div>
    </div>

    <!-- Ditahan / Sengketa -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="info-box shadow-sm border-0" style="border-left: 4px solid #ef4444 !important;">
            <span class="info-box-icon bg-danger text-white"><i class="fas fa-hand-holding-dollar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Pencairan Ditahan</span>
                <span class="info-box-number text-danger font-weight-bold" style="font-size: 1.25rem;">
                    <?= esc($stats['count_rejected']) ?> Ditahan
                </span>
                <small class="text-muted">Menunggu investigasi masalah</small>
            </div>
        </div>
    </div>
</div>

<!-- Main Settlements Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap">
        <h5 class="card-title font-weight-bold mb-0 text-dark">
            <i class="fas fa-list text-teal mr-2"></i> Daftar Antrean &amp; Riwayat Pencairan Dana Sewa
        </h5>
        <div class="mt-2 mt-sm-0">
            <a href="<?= base_url('/admin/settlements') ?>" class="btn btn-xs <?= empty($statusFilter) ? 'btn-teal text-white font-weight-bold' : 'btn-outline-secondary' ?>">Semua</a>
            <a href="<?= base_url('/admin/settlements?status=pending') ?>" class="btn btn-xs <?= $statusFilter === 'pending' ? 'btn-warning text-dark font-weight-bold' : 'btn-outline-secondary' ?>">
                <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi (<?= esc($stats['count_pending']) ?>)
            </a>
            <a href="<?= base_url('/admin/settlements?status=verified') ?>" class="btn btn-xs <?= $statusFilter === 'verified' ? 'btn-info text-white font-weight-bold' : 'btn-outline-secondary' ?>">
                <i class="fas fa-check mr-1"></i> Siap Transfer (<?= esc($stats['count_verified']) ?>)
            </a>
            <a href="<?= base_url('/admin/settlements?status=completed') ?>" class="btn btn-xs <?= $statusFilter === 'completed' ? 'btn-success font-weight-bold' : 'btn-outline-secondary' ?>">
                <i class="fas fa-check-double mr-1"></i> Selesai Lunas
            </a>
            <a href="<?= base_url('/admin/settlements?status=rejected') ?>" class="btn btn-xs <?= $statusFilter === 'rejected' ? 'btn-danger font-weight-bold' : 'btn-outline-secondary' ?>">
                <i class="fas fa-ban mr-1"></i> Ditahan (<?= esc($stats['count_rejected']) ?>)
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (! empty($settlements)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th>Mitra &amp; Wilayah</th>
                        <th>Rekening Bank Tujuan</th>
                        <th>Hunian &amp; Kontrak</th>
                        <th>Rincian Finansial</th>
                        <th>Alur Persetujuan (Maker &amp; Checker)</th>
                        <th class="text-right">Tindakan Otorisasi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($settlements as $s): ?>
                    <tr>
                        <!-- Mitra & Wilayah -->
                        <td>
                            <div class="font-weight-bold text-dark"><?= esc($s['provider_name']) ?></div>
                            <small class="text-muted d-block"><?= esc($s['provider_email']) ?></small>
                            <span class="badge badge-light border text-teal mt-1">
                                <i class="fas fa-location-dot mr-1"></i> <?= esc($s['region_name'] ?? 'Wilayah Terdaftar') ?>
                            </span>
                        </td>

                        <!-- Rekening Bank Tujuan -->
                        <td>
                            <div class="font-weight-bold text-dark"><?= esc($s['bank_name']) ?></div>
                            <code class="text-dark font-weight-bold"><?= esc($s['bank_account_number']) ?></code>
                            <div class="small text-muted">a/n <?= esc($s['bank_account_holder']) ?></div>
                            <span class="badge badge-success px-1 py-0 mt-1" style="font-size: 0.68rem;">
                                <i class="fas fa-check-circle mr-1"></i> Rekening KYC Terdaftar
                            </span>
                        </td>

                        <!-- Properti & Kontrak -->
                        <td>
                            <div class="font-weight-bold text-dark"><?= esc($s['property_name'] ?? 'Unit Hunian Sewa') ?></div>
                            <span class="badge badge-light border text-muted">Kontrak #<?= esc($s['contract_id_ref'] ?? 'N/A') ?></span>
                            <?php if (! empty($s['rental_start_date'])): ?>
                                <small class="text-muted d-block mt-1">Mulai: <?= date('d M Y', strtotime($s['rental_start_date'])) ?></small>
                            <?php endif; ?>
                        </td>

                        <!-- Rincian Finansial -->
                        <td>
                            <div class="small text-muted">Bruto: Rp <?= number_format($s['gross_amount'], 0, ',', '.') ?></div>
                            <div class="small text-muted">Fee Platform: Rp <?= number_format($s['platform_fee'], 0, ',', '.') ?></div>
                            <div class="font-weight-bold text-teal" style="font-size: 1.1rem;">
                                Bersih: Rp <?= number_format($s['net_amount'], 0, ',', '.') ?>
                            </div>
                        </td>

                        <!-- Alur Persetujuan (Maker & Checker Info) -->
                        <td>
                            <?php if ($s['status'] === 'pending'): ?>
                                <span class="badge badge-warning px-2 py-1 text-dark d-inline-block mb-1">
                                    <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi Lapangan
                                </span>
                                <small class="text-muted d-block">Langkah 1: Menunggu pemeriksaan oleh Admin Daerah.</small>

                            <?php elseif ($s['status'] === 'verified'): ?>
                                <span class="badge badge-info px-2 py-1 text-white d-inline-block mb-1">
                                    <i class="fas fa-check mr-1"></i> Diverifikasi Admin Daerah
                                </span>
                                <div class="small text-dark font-weight-bold">
                                    Verifikator: <?= esc($s['verifier_name'] ?? 'Admin Daerah') ?>
                                </div>
                                <small class="text-muted d-block">
                                    <?= !empty($s['verified_at']) ? date('d/m/Y H:i', strtotime($s['verified_at'])) : '' ?>
                                </small>
                                <?php if (! empty($s['verification_notes'])): ?>
                                    <small class="text-muted font-italic d-block mt-1" title="<?= esc($s['verification_notes']) ?>">
                                        "<?= esc(mb_strimwidth($s['verification_notes'], 0, 45, '...')) ?>"
                                    </small>
                                <?php endif; ?>

                            <?php elseif ($s['status'] === 'completed'): ?>
                                <span class="badge badge-success px-2 py-1 d-inline-block mb-1">
                                    <i class="fas fa-check-double mr-1"></i> Tersalurkan Lunas
                                </span>
                                <?php if (! empty($s['payout_reference'])): ?>
                                    <code class="d-block text-success font-weight-bold"><?= esc($s['payout_reference']) ?></code>
                                <?php endif; ?>
                                <small class="text-muted d-block">
                                    Diotorisasi oleh <?= esc($s['processor_name'] ?? 'Super Admin') ?> (<?= date('d/m/Y', strtotime($s['processed_at'] ?? $s['updated_at'])) ?>)
                                </small>

                            <?php elseif ($s['status'] === 'rejected'): ?>
                                <span class="badge badge-danger px-2 py-1 d-inline-block mb-1">
                                    <i class="fas fa-ban mr-1"></i> Pencairan Ditahan
                                </span>
                                <?php if (! empty($s['rejection_reason'])): ?>
                                    <small class="text-danger font-weight-bold d-block">
                                        "<?= esc($s['rejection_reason']) ?>"
                                    </small>
                                <?php endif; ?>

                            <?php else: ?>
                                <span class="badge badge-secondary px-2 py-1"><?= strtoupper($s['status']) ?></span>
                            <?php endif; ?>
                        </td>

                        <!-- Tindakan Otorisasi -->
                        <td class="text-right">
                            <!-- PERAN 1: ADMIN DAERAH (MAKER) -->
                            <?php if (! $isSuperAdmin): ?>
                                <?php if ($s['status'] === 'pending'): ?>
                                    <button type="button" class="btn btn-xs btn-teal text-white font-weight-bold mb-1" data-toggle="modal" data-target="#modalVerify<?= $s['id'] ?>">
                                        <i class="fas fa-check mr-1"></i> Verifikasi Lapangan
                                    </button>
                                    <button type="button" class="btn btn-xs btn-outline-danger font-weight-bold mb-1" data-toggle="modal" data-target="#modalHold<?= $s['id'] ?>">
                                        <i class="fas fa-hand mr-1"></i> Tahan
                                    </button>
                                <?php elseif ($s['status'] === 'verified'): ?>
                                    <span class="badge badge-light border text-info">
                                        <i class="fas fa-hourglass-start mr-1"></i> Menunggu Super Admin
                                    </span>
                                <?php elseif ($s['status'] === 'completed'): ?>
                                    <span class="badge badge-light border text-success">
                                        <i class="fas fa-check-circle mr-1"></i> Telah Dicairkan
                                    </span>
                                <?php elseif ($s['status'] === 'rejected'): ?>
                                    <button type="button" class="btn btn-xs btn-outline-warning" data-toggle="modal" data-target="#modalVerify<?= $s['id'] ?>">
                                        <i class="fas fa-rotate-left mr-1"></i> Tinjau Ulang
                                    </button>
                                <?php endif; ?>

                            <!-- PERAN 2: SUPER ADMIN (CHECKER) -->
                            <?php else: ?>
                                <?php if ($s['status'] === 'verified'): ?>
                                    <button type="button" class="btn btn-xs btn-success font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalProcess<?= $s['id'] ?>">
                                        <i class="fas fa-money-bill-transfer mr-1"></i> Otorisasi Transfer
                                    </button>
                                <?php elseif ($s['status'] === 'pending'): ?>
                                    <span class="badge badge-light border text-warning font-weight-bold d-block mb-1">
                                        <i class="fas fa-user-clock mr-1"></i> Butuh Verifikasi Daerah
                                    </span>
                                    <!-- Super Admin Emergency Bypass Option -->
                                    <button type="button" class="btn btn-xs btn-outline-secondary" data-toggle="modal" data-target="#modalProcess<?= $s['id'] ?>" title="Otorisasi langsung apabila wilayah belum memiliki Admin Daerah">
                                        <i class="fas fa-bolt mr-1"></i> Bypass Otorisasi
                                    </button>
                                <?php elseif ($s['status'] === 'completed'): ?>
                                    <span class="badge badge-light border text-success">
                                        <i class="fas fa-circle-check mr-1"></i> Selesai
                                    </span>
                                <?php elseif ($s['status'] === 'rejected'): ?>
                                    <span class="badge badge-light border text-danger">
                                        <i class="fas fa-ban mr-1"></i> Ditahan Daerah
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <!-- MODAL 1: Verifikasi Lapangan (Maker - Admin Daerah) -->
                    <div class="modal fade" id="modalVerify<?= $s['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <form action="<?= base_url('/admin/settlements/verify/' . $s['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="modal-header bg-teal text-white">
                                        <h5 class="modal-title font-weight-bold">
                                            <i class="fas fa-clipboard-check mr-2"></i> Verifikasi Lapangan Pencairan Sewa #<?= $s['id'] ?>
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-left">
                                        <div class="alert alert-info border-0 p-3 mb-3 text-sm">
                                            <i class="fas fa-info-circle mr-1"></i> Sebagai <strong>Maker (Admin Daerah)</strong>, pastikan penyewa telah menempati unit kamar fisik dan tidak ada sengketa fasilitas sebelum merekomendasikan pencairan ini ke Super Admin.
                                        </div>

                                        <div class="p-3 bg-light rounded border mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Mitra Pemilik:</span>
                                                <strong><?= esc($s['provider_name']) ?></strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Rekening Tujuan:</span>
                                                <span><?= esc($s['bank_name']) ?> (<?= esc($s['bank_account_number']) ?>)</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Hunian / Kontrak:</span>
                                                <span><?= esc($s['property_name'] ?? 'Kos') ?> (Kontrak #<?= esc($s['contract_id_ref'] ?? 'N/A') ?>)</span>
                                            </div>
                                            <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                                <span class="font-weight-bold text-dark">Nominal Pencairan Bersih:</span>
                                                <strong class="text-teal" style="font-size: 1.15rem;">Rp <?= number_format($s['net_amount'], 0, ',', '.') ?></strong>
                                            </div>
                                        </div>

                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold text-dark">Catatan Konfirmasi Lapangan <span class="text-danger">*</span></label>
                                            <textarea name="verification_notes" class="form-control" rows="3" placeholder="Contoh: Kamar sudah diserahterimakan, penyewa sudah aktif check-in, tidak ada kendala sengketa. Layak dicairkan." required><?= esc($s['verification_notes'] ?? 'Kondisi hunian normal, penyewa telah menempati kamar fisik dengan aman, tidak ada laporan sengketa aktif. Rekomendasi pencairan dana sewa disetujui.') ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-teal text-white btn-sm font-weight-bold">
                                            <i class="fas fa-check mr-1"></i> Konfirmasi &amp; Rekomendasikan Cair
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL 2: Tahan Pencairan (Maker - Admin Daerah) -->
                    <div class="modal fade" id="modalHold<?= $s['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <form action="<?= base_url('/admin/settlements/hold/' . $s['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title font-weight-bold">
                                            <i class="fas fa-hand-holding-dollar mr-2"></i> Tahan Pencairan Dana #<?= $s['id'] ?>
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-left">
                                        <div class="alert alert-warning border-0 p-3 mb-3 text-sm text-dark">
                                            <i class="fas fa-exclamation-triangle mr-1"></i> Penahanan pencairan akan membekukan dana di escrow sampai kendala diselesaikan. Alasan penahanan akan dicatat pada log audit.
                                        </div>

                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold text-dark">Alasan Penahanan Pencairan <span class="text-danger">*</span></label>
                                            <textarea name="rejection_reason" class="form-control" rows="3" placeholder="Contoh: Terjadi kebocoran atap kamar dan fasilitas AC mati, penyewa mengajukan komplain resmi. Tahan pencairan hingga perbaikan selesai." required><?= esc($s['rejection_reason'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-danger btn-sm font-weight-bold">
                                            <i class="fas fa-ban mr-1"></i> Tahan Pencairan Dana
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- MODAL 3: Otorisasi & Eksekusi Transfer Bank (Checker - Super Admin) -->
                    <div class="modal fade" id="modalProcess<?= $s['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <form action="<?= base_url('/admin/settlements/process/' . $s['id']) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title font-weight-bold">
                                            <i class="fas fa-money-bill-transfer mr-2"></i> Otorisasi Transfer Pencairan #<?= $s['id'] ?>
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body text-left">
                                        <?php if ($s['status'] === 'verified'): ?>
                                            <div class="alert alert-success border-0 p-3 mb-3 text-sm">
                                                <i class="fas fa-check-circle mr-1"></i> <strong>Pemeriksaan Lapangan Selesai:</strong>
                                                Diverifikasi oleh <strong><?= esc($s['verifier_name'] ?? 'Admin Daerah') ?></strong>
                                                (<?= !empty($s['verified_at']) ? date('d/m/Y H:i', strtotime($s['verified_at'])) : '' ?>).
                                                <div class="font-italic mt-1">"<?= esc($s['verification_notes'] ?? 'Kondisi aman.') ?>"</div>
                                            </div>
                                        <?php else: ?>
                                            <div class="alert alert-warning border-0 p-3 mb-3 text-sm text-dark">
                                                <i class="fas fa-bolt mr-1"></i> <strong>Mode Bypass Eksekutif:</strong> 
                                                Permohonan ini belum melalui verifikasi Admin Daerah. Pastikan Anda telah mengonfirmasi transaksi secara independen sebelum transfer.
                                            </div>
                                        <?php endif; ?>

                                        <div class="p-3 bg-light rounded border mb-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Rekening Bank Tujuan:</span>
                                                <strong><?= esc($s['bank_name']) ?></strong>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Nomor Rekening:</span>
                                                <code class="text-dark font-weight-bold"><?= esc($s['bank_account_number']) ?></code>
                                            </div>
                                            <div class="d-flex justify-content-between mb-1">
                                                <span class="text-muted">Atas Nama Rekening:</span>
                                                <strong><?= esc($s['bank_account_holder']) ?></strong>
                                            </div>
                                            <div class="d-flex justify-content-between border-top pt-2 mt-2">
                                                <span class="font-weight-bold text-dark">Dana yang Harus Ditransfer:</span>
                                                <strong class="text-success" style="font-size: 1.25rem;">
                                                    Rp <?= number_format($s['net_amount'], 0, ',', '.') ?>
                                                </strong>
                                            </div>
                                        </div>

                                        <div class="form-group mb-3">
                                            <label class="font-weight-bold text-dark">Nomor Referensi Transfer Bank</label>
                                            <input type="text" name="payout_reference" class="form-control" value="PAYOUT-<?= date('Ymd') ?>-<?= sprintf('%03d', $s['id']) ?>" required>
                                            <small class="text-muted">Masukkan nomor transaksi bank / referensi mutasi pengiriman dana.</small>
                                        </div>

                                        <div class="form-group mb-0">
                                            <label class="font-weight-bold text-dark">Catatan Finansial</label>
                                            <input type="text" name="notes" class="form-control" value="Pencairan dana sewa telah diotorisasi dan ditransfer ke rekening resmi mitra.">
                                        </div>
                                    </div>
                                    <div class="modal-footer bg-light">
                                        <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-success btn-sm font-weight-bold">
                                            <i class="fas fa-check-double mr-1"></i> Otorisasi &amp; Tandai Lunas
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="p-5 text-center text-muted">
            <i class="fas fa-money-bill-transfer fa-3x text-muted mb-3"></i>
            <h6 class="font-weight-bold text-dark">Tidak Ada Transaksi Settlement</h6>
            <p class="mb-0 text-sm">Tidak ada permohonan pencairan dana sewa dengan kriteria atau status ini pada wilayah Anda.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
