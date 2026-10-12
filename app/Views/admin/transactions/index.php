<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-receipt text-primary mr-2"></i> Transaksi Finansial Global (SA-06)
            </h1>
            <p class="text-muted small mb-0">Arus kas seluruh platform, log tagihan sewa digital, dan pendapatan gateway fee nasional.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <span class="badge badge-primary px-3 py-2">
                <i class="fas fa-shield-halved mr-1"></i> Otoritas Super Admin
            </span>
        </div>
    </div>
</div>

<!-- Stats Row -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm border-0">
            <span class="info-box-icon bg-success text-white"><i class="fas fa-wallet"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Pembayaran Lunas</span>
                <span class="info-box-number text-success font-weight-bold" style="font-size: 1.3rem;">
                    Rp <?= number_format($stats['total_paid'], 0, ',', '.') ?>
                </span>
                <small class="text-muted"><?= esc($stats['count_paid']) ?> invoice terbayar</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm border-0">
            <span class="info-box-icon bg-info text-white"><i class="fas fa-hand-holding-dollar"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Fee Platform &amp; Gateway</span>
                <span class="info-box-number text-info font-weight-bold" style="font-size: 1.3rem;">
                    Rp <?= number_format($stats['total_fees'], 0, ',', '.') ?>
                </span>
                <small class="text-muted">Pendapatan operasional</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm border-0">
            <span class="info-box-icon bg-warning text-dark"><i class="fas fa-hourglass-half"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Tagihan Pending</span>
                <span class="info-box-number text-warning font-weight-bold" style="font-size: 1.3rem;">
                    <?= esc($stats['count_pending']) ?> Invoice
                </span>
                <small class="text-muted">Menunggu pembayaran</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-3">
        <div class="info-box shadow-sm border-0">
            <span class="info-box-icon bg-primary text-white"><i class="fas fa-building-columns"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Kanal Gateway</span>
                <span class="info-box-number text-primary font-weight-bold" style="font-size: 1.3rem;">
                    Virtual Account
                </span>
                <small class="text-muted">BCA, Mandiri, BNI, BRI</small>
            </div>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title font-weight-bold mb-0 text-dark">
            <i class="fas fa-list text-primary mr-2"></i> Log Transaksi Invoice Nasional
        </h5>
        <div>
            <a href="<?= base_url('/admin/transactions') ?>" class="btn btn-xs <?= empty($statusFilter) ? 'btn-primary font-weight-bold' : 'btn-outline-secondary' ?>">Semua</a>
            <a href="<?= base_url('/admin/transactions?status=paid') ?>" class="btn btn-xs <?= $statusFilter === 'paid' ? 'btn-success font-weight-bold' : 'btn-outline-secondary' ?>">Lunas</a>
            <a href="<?= base_url('/admin/transactions?status=pending') ?>" class="btn btn-xs <?= $statusFilter === 'pending' ? 'btn-warning font-weight-bold' : 'btn-outline-secondary' ?>">Pending</a>
            <a href="<?= base_url('/admin/transactions?status=overdue') ?>" class="btn btn-xs <?= $statusFilter === 'overdue' ? 'btn-danger font-weight-bold' : 'btn-outline-secondary' ?>">Overdue</a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (! empty($invoices)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th>Invoice / Kontrak</th>
                        <th>Penyewa</th>
                        <th>Mitra Provider</th>
                        <th>Sewa Pokok</th>
                        <th>Fee Platform</th>
                        <th>Total Gross</th>
                        <th>Status</th>
                        <th>Jatuh Tempo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td>
                            <div class="font-weight-bold text-dark"><?= esc($inv['invoice_number'] ?? ('#INV-' . $inv['id'])) ?></div>
                            <small class="text-muted"><i class="fas fa-file-contract mr-1"></i> Kontrak #<?= esc($inv['contract_id']) ?></small>
                        </td>
                        <td>
                            <div class="font-weight-bold text-dark"><?= esc($inv['tenant_name']) ?></div>
                            <small class="text-muted"><?= esc($inv['tenant_email']) ?></small>
                        </td>
                        <td>
                            <div class="small font-weight-bold text-dark"><?= esc($inv['provider_name']) ?></div>
                            <small class="text-muted"><?= esc($inv['property_name']) ?></small>
                        </td>
                        <td class="font-weight-bold">
                            Rp <?= number_format($inv['rental_amount'], 0, ',', '.') ?>
                        </td>
                        <td class="text-muted small">
                            Rp <?= number_format($inv['gateway_fee'], 0, ',', '.') ?>
                        </td>
                        <td class="font-weight-bold text-success">
                            Rp <?= number_format($inv['total_amount'], 0, ',', '.') ?>
                        </td>
                        <td>
                            <?php if ($inv['status'] === 'paid'): ?>
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle mr-1"></i> LUNAS</span>
                            <?php elseif ($inv['status'] === 'pending'): ?>
                                <span class="badge badge-warning px-2 py-1 text-dark">PENDING</span>
                            <?php elseif ($inv['status'] === 'overdue'): ?>
                                <span class="badge badge-danger px-2 py-1">OVERDUE</span>
                            <?php else: ?>
                                <span class="badge badge-secondary px-2 py-1"><?= strtoupper($inv['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="small text-muted">
                            <?= date('d M Y', strtotime($inv['due_date'])) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="p-5 text-center text-muted">
            <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
            <p class="mb-0">Tidak ada riwayat transaksi dengan filter yang dipilih.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
