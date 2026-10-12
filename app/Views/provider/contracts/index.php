<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-file-contract text-teal mr-2"></i>Kontrak Sewa Penyewa
                </h1>
                <p class="text-muted small mb-0">Daftar kontrak sewa aktif dan riwayat hunian pada properti Anda</p>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?= base_url('/provider/applications') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-inbox mr-1"></i> Pengajuan Sewa Masuk
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card card-outline card-teal shadow-sm">
            <div class="card-header border-0">
                <h3 class="card-title font-weight-bold">Semua Kontrak Penyewa</h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($contracts)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-file-signature fa-3x text-muted mb-3 opacity-50"></i>
                        <h5 class="text-secondary font-weight-bold">Belum Ada Kontrak Aktif</h5>
                        <p class="text-muted small mb-0">Kontrak resmi akan terbentuk otomatis setelah pembayaran sewa pertama penyewa berhasil.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>No. Kontrak</th>
                                    <th>Penyewa</th>
                                    <th>Kamar & Properti</th>
                                    <th>Masa Sewa</th>
                                    <th>Nilai Kontrak</th>
                                    <th>Status</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($contracts as $c): ?>
                                    <?php 
                                        $badgeClass = match($c['status']) {
                                            'active'     => 'badge-success',
                                            'completed'  => 'badge-secondary',
                                            'terminated' => 'badge-danger',
                                            default      => 'badge-light',
                                        };
                                        $statusLabel = match($c['status']) {
                                            'active'     => 'Aktif Berjalan',
                                            'completed'  => 'Selesai',
                                            'terminated' => 'Berhenti Awal',
                                            default      => ucfirst($c['status']),
                                        };
                                    ?>
                                    <tr>
                                        <td>
                                            <strong class="text-teal font-monospace">#CTR-<?= str_pad($c['id'], 5, '0', STR_PAD_LEFT) ?></strong>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark"><?= esc($c['tenant_name']) ?></div>
                                            <div class="small text-muted"><i class="fab fa-whatsapp text-success mr-1"></i><?= esc($c['tenant_whatsapp'] ?? '-') ?></div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark">Kamar <?= esc($c['unit_name']) ?></div>
                                            <div class="small text-muted"><?= esc($c['property_name']) ?></div>
                                        </td>
                                        <td>
                                            <div class="small font-weight-bold"><?= date('d M Y', strtotime($c['start_date'])) ?></div>
                                            <div class="small text-muted">s/d <?= date('d M Y', strtotime($c['end_date'])) ?></div>
                                        </td>
                                        <td>
                                            <strong class="text-dark">Rp <?= number_format($c['agreed_price'], 0, ',', '.') ?></strong>
                                            <div class="small text-muted text-capitalize"><?= str_replace('_', ' ', $c['payment_schedule']) ?></div>
                                        </td>
                                        <td>
                                            <span class="badge <?= $badgeClass ?> px-2 py-1">
                                                <?= esc($statusLabel) ?>
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?= base_url('/provider/contracts/' . $c['id']) ?>" class="btn btn-sm btn-outline-teal font-weight-bold">
                                                <i class="fas fa-file-alt mr-1"></i> Buka Kontrak
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
