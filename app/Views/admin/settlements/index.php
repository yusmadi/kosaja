<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-money-bill-transfer text-teal mr-2"></i> Settlement Dana Mitra (SA-07)
            </h1>
            <p class="text-muted small mb-0">Pencairan dana sewa dari escrow platform ke rekening bank resmi mitra pemilik properti.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <span class="badge badge-primary px-3 py-2">
                <i class="fas fa-shield-halved mr-1"></i> Otoritas Super Admin
            </span>
        </div>
    </div>
</div>

<!-- Settlement Stats -->
<div class="row">
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box shadow-sm border-0">
            <span class="info-box-icon bg-teal text-white"><i class="fas fa-check-double"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Total Dana Tersalurkan</span>
                <span class="info-box-number text-teal font-weight-bold" style="font-size: 1.3rem;">
                    Rp <?= number_format($stats['total_completed'], 0, ',', '.') ?>
                </span>
                <small class="text-muted">Settlement berhasil ditransfer</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box shadow-sm border-0">
            <span class="info-box-icon bg-warning text-dark"><i class="fas fa-hourglass-start"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Menunggu Pencairan</span>
                <span class="info-box-number text-warning font-weight-bold" style="font-size: 1.3rem;">
                    Rp <?= number_format($stats['total_pending'], 0, ',', '.') ?>
                </span>
                <small class="text-muted"><?= esc($stats['count_pending']) ?> permohonan antrean</small>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4">
        <div class="info-box shadow-sm border-0">
            <span class="info-box-icon bg-success text-white"><i class="fas fa-shield-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text text-muted">Protokol Keamanan</span>
                <span class="info-box-number text-success font-weight-bold" style="font-size: 1.3rem;">
                    Rekening KYC Sah
                </span>
                <small class="text-muted">Hanya ke rekening terverifikasi</small>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title font-weight-bold mb-0 text-dark">
            <i class="fas fa-list text-teal mr-2"></i> Daftar Antrean &amp; Riwayat Settlement
        </h5>
        <div>
            <a href="<?= base_url('/admin/settlements') ?>" class="btn btn-xs <?= empty($statusFilter) ? 'btn-teal text-white font-weight-bold' : 'btn-outline-secondary' ?>" style="<?= empty($statusFilter) ? 'background-color: #0d9488;' : '' ?>">Semua</a>
            <a href="<?= base_url('/admin/settlements?status=pending') ?>" class="btn btn-xs <?= $statusFilter === 'pending' ? 'btn-warning font-weight-bold' : 'btn-outline-secondary' ?>">Pending</a>
            <a href="<?= base_url('/admin/settlements?status=completed') ?>" class="btn btn-xs <?= $statusFilter === 'completed' ? 'btn-success font-weight-bold' : 'btn-outline-secondary' ?>">Selesai</a>
        </div>
    </div>
    <div class="card-body p-0">
        <?php if (! empty($settlements)): ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th>Mitra Pemilik</th>
                        <th>Rekening Bank Tujuan</th>
                        <th>Kontrak Sewa</th>
                        <th>Jumlah Bruto</th>
                        <th>Biaya Platform</th>
                        <th>Pencairan Bersih</th>
                        <th>Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($settlements as $s): ?>
                    <tr>
                        <td>
                            <div class="font-weight-bold text-dark"><?= esc($s['provider_name']) ?></div>
                            <small class="text-muted"><?= esc($s['provider_email']) ?></small>
                        </td>
                        <td>
                            <div class="font-weight-bold text-dark"><?= esc($s['bank_name']) ?></div>
                            <small class="text-muted"><?= esc($s['bank_account_number']) ?> a/n <?= esc($s['bank_account_holder']) ?></small>
                        </td>
                        <td>
                            <span class="badge badge-light border">Kontrak #<?= esc($s['contract_id_ref'] ?? 'N/A') ?></span>
                        </td>
                        <td class="font-weight-bold">
                            Rp <?= number_format($s['gross_amount'], 0, ',', '.') ?>
                        </td>
                        <td class="text-muted small">
                            Rp <?= number_format($s['platform_fee'], 0, ',', '.') ?>
                        </td>
                        <td class="font-weight-bold text-success" style="font-size: 1.05rem;">
                            Rp <?= number_format($s['net_amount'], 0, ',', '.') ?>
                        </td>
                        <td>
                            <?php if ($s['status'] === 'completed'): ?>
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check mr-1"></i> TERSALURKAN</span>
                                <?php if (! empty($s['payout_reference'])): ?>
                                    <small class="d-block text-muted font-italic"><?= esc($s['payout_reference']) ?></small>
                                <?php endif; ?>
                            <?php elseif ($s['status'] === 'pending'): ?>
                                <span class="badge badge-warning px-2 py-1 text-dark">MENUNGGU OTORISASI</span>
                            <?php else: ?>
                                <span class="badge badge-secondary px-2 py-1"><?= strtoupper($s['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-right">
                            <?php if ($s['status'] === 'pending'): ?>
                                <form action="<?= base_url('/admin/settlements/process/' . $s['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Otorisasi pencairan dana Rp <?= number_format($s['net_amount'], 0, ',', '.') ?> ke mitra <?= esc($s['provider_name']) ?>?');">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="btn btn-xs btn-success font-weight-bold">
                                        <i class="fas fa-check mr-1"></i> Otorisasi Transfer
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="badge badge-light border text-muted">Diproses: <?= date('d/m/Y', strtotime($s['processed_at'] ?? $s['updated_at'])) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="p-5 text-center text-muted">
            <i class="fas fa-money-bill-transfer fa-3x text-muted mb-3"></i>
            <p class="mb-0">Tidak ada transaksi settlement dengan filter ini.</p>
        </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
