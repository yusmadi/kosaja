<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-hand-holding-dollar text-teal mr-2"></i> Riwayat Pengembalian Dana (Refunds)
            </h1>
            <p class="text-muted small mb-0">Semua eksekusi pengembalian dana sewa yang disetujui dari investigasi dispute.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/admin/disputes') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-scale-balanced mr-1"></i> Kembali ke Kasus Dispute
            </a>
        </div>
    </div>
</div>

<div class="row mb-3">
    <div class="col-sm-6 col-lg-4">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-teal text-white"><i class="fas fa-money-bill-transfer"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Realisasi Refund</span>
                <span class="info-box-number text-teal font-weight-bold">Rp <?= number_format($totalAmount, 0, ',', '.') ?></span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="info-box shadow-sm">
            <span class="info-box-icon bg-success text-white"><i class="fas fa-check-double"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Total Transaksi Refund</span>
                <span class="info-box-number font-weight-bold"><?= count($refunds) ?> Transaksi</span>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <?php if (!empty($refunds)): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle m-0 text-sm">
                    <thead class="bg-light">
                        <tr>
                            <th class="pl-3">ID Refund</th>
                            <th>Penerima (Penyewa)</th>
                            <th>Nominal Refund</th>
                            <th>Tipe Refund</th>
                            <th>Alasan / Dasar Pertimbangan</th>
                            <th>Diproses Oleh</th>
                            <th class="text-right pr-3">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($refunds as $rf): ?>
                            <tr>
                                <td class="pl-3 font-weight-bold">
                                    #RF-<?= $rf['id'] ?>
                                    <?php if ($rf['dispute_id']): ?>
                                        <a href="<?= base_url('/admin/disputes/' . $rf['dispute_id']) ?>" class="badge badge-light border d-block mt-1">
                                            #DSP-<?= $rf['dispute_id'] ?>
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= esc($rf['tenant_name']) ?></strong>
                                    <small class="text-muted d-block"><?= esc($rf['tenant_email']) ?></small>
                                </td>
                                <td class="font-weight-bold text-success">
                                    Rp <?= number_format($rf['amount'], 0, ',', '.') ?>
                                </td>
                                <td>
                                    <span class="badge <?= $rf['refund_type'] === 'full' ? 'badge-danger' : 'badge-warning text-dark' ?> text-uppercase">
                                        <?= $rf['refund_type'] ?> REFUND
                                    </span>
                                </td>
                                <td>
                                    <span class="small text-muted text-truncate d-inline-block" style="max-width: 250px;">
                                        <?= esc($rf['reason']) ?>
                                    </span>
                                </td>
                                <td><?= esc($rf['processed_by_name'] ?? 'Admin') ?></td>
                                <td class="text-right pr-3 text-muted">
                                    <?= date('d M Y H:i', strtotime($rf['processed_at'])) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-hand-holding-dollar fa-3x mb-3 text-secondary opacity-50"></i>
                <h5 class="font-weight-bold text-dark">Belum Ada Pengembalian Dana</h5>
                <p class="small text-muted mb-0">Belum ada transaksi refund yang pernah dieksekusi.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
