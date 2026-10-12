<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-scale-balanced text-teal mr-2"></i> Manajemen Dispute & Komplain
            </h1>
            <p class="text-muted small mb-0">Tinjau keluhan sewa dari penyewa atau penyedia, proses investigasi, dan tentukan kebijakan refund.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/admin/refunds') ?>" class="btn btn-outline-teal btn-sm font-weight-bold">
                <i class="fas fa-hand-holding-dollar mr-1"></i> Riwayat Pengembalian Dana (Refunds)
            </a>
        </div>
    </div>
</div>

<!-- Filter Pills -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body p-2 d-flex flex-wrap gap-2">
        <a href="<?= base_url('/admin/disputes') ?>" class="btn btn-sm <?= empty($selectedStatus) ? 'btn-teal text-white' : 'btn-light border' ?> mr-2">
            Semua (<?= $counts['all'] ?>)
        </a>
        <a href="<?= base_url('/admin/disputes?status=opened') ?>" class="btn btn-sm <?= ($selectedStatus === 'opened') ? 'btn-warning text-dark' : 'btn-light border' ?> mr-2">
            <i class="fas fa-circle-exclamation mr-1"></i> Baru Masuk (<?= $counts['opened'] ?>)
        </a>
        <a href="<?= base_url('/admin/disputes?status=investigating') ?>" class="btn btn-sm <?= ($selectedStatus === 'investigating') ? 'btn-primary' : 'btn-light border' ?> mr-2">
            <i class="fas fa-magnifying-glass mr-1"></i> Investigasi (<?= $counts['investigating'] ?>)
        </a>
        <a href="<?= base_url('/admin/disputes?status=resolved') ?>" class="btn btn-sm <?= ($selectedStatus === 'resolved') ? 'btn-success' : 'btn-light border' ?> mr-2">
            <i class="fas fa-circle-check mr-1"></i> Selesai Diputuskan (<?= $counts['resolved'] ?>)
        </a>
        <a href="<?= base_url('/admin/disputes?status=escalated') ?>" class="btn btn-sm <?= ($selectedStatus === 'escalated') ? 'btn-danger font-weight-bold text-white' : 'btn-light border' ?>">
            <i class="fas fa-arrow-up mr-1"></i> Dieskalasi ke Pusat (<?= $counts['escalated'] ?>)
        </a>
    </div>
</div>

<!-- Disputes List Card -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <?php if (!empty($disputes)): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle m-0 text-sm">
                    <thead class="bg-light">
                        <tr>
                            <th class="pl-3">ID & Judul</th>
                            <th>Pelapor (Penyewa)</th>
                            <th>Terlapor (Penyedia)</th>
                            <th>Properti & Kamar</th>
                            <th>Status</th>
                            <th>Keputusan</th>
                            <th class="text-right pr-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($disputes as $d): ?>
                            <tr>
                                <td class="pl-3">
                                    <strong class="d-block text-dark">#DSP-<?= $d['id'] ?>: <?= esc($d['title']) ?></strong>
                                    <small class="text-muted"><i class="fas fa-clock mr-1"></i> <?= date('d M Y H:i', strtotime($d['created_at'])) ?></small>
                                </td>
                                <td>
                                    <strong><?= esc($d['raised_by_name']) ?></strong>
                                    <small class="d-block text-muted"><?= esc($d['raised_by_whatsapp']) ?></small>
                                </td>
                                <td>
                                    <strong><?= esc($d['against_user_name']) ?></strong>
                                    <small class="d-block text-muted"><?= esc($d['against_user_whatsapp']) ?></small>
                                </td>
                                <td>
                                    <div><?= esc($d['property_name']) ?></div>
                                    <small class="text-muted">Kamar: <?= esc($d['unit_name']) ?></small>
                                </td>
                                <td>
                                    <?php if ($d['status'] === 'opened'): ?>
                                        <span class="badge badge-warning text-dark"><i class="fas fa-bell mr-1"></i> OPENED</span>
                                    <?php elseif ($d['status'] === 'investigating'): ?>
                                        <span class="badge badge-primary"><i class="fas fa-magnifying-glass mr-1"></i> INVESTIGATING</span>
                                    <?php elseif ($d['status'] === 'resolved'): ?>
                                        <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i> RESOLVED</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><?= strtoupper($d['status']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($d['decision'] === 'full_refund'): ?>
                                        <span class="badge badge-danger">FULL REFUND</span>
                                    <?php elseif ($d['decision'] === 'partial_refund'): ?>
                                        <span class="badge badge-warning text-dark">PARTIAL REFUND</span>
                                    <?php elseif ($d['decision'] === 'no_refund'): ?>
                                        <span class="badge badge-secondary">NO REFUND</span>
                                    <?php else: ?>
                                        <span class="text-muted small">Menunggu</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right pr-3">
                                    <a href="<?= base_url('/admin/disputes/' . $d['id']) ?>" class="btn btn-xs btn-teal text-white font-weight-bold">
                                        <i class="fas fa-eye mr-1"></i> Tinjau & Putuskan
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-shield-cat fa-3x mb-3 text-secondary opacity-50"></i>
                <h5 class="font-weight-bold text-dark">Tidak Ada Dispute</h5>
                <p class="small text-muted mb-0">Belum ada laporan komplain sewa yang masuk pada kategori ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
