<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-clock text-teal mr-2"></i>Reservasi Unit Kamar
                </h1>
                <p class="text-muted small mb-0">Daftar kamar yang sedang dikunci untuk proses pembayaran penyewa (Expiration Window)</p>
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
            <div class="card-header">
                <h3 class="card-title font-weight-bold">Daftar Reservasi Kamar</h3>
            </div>
            <div class="card-body p-0">
                <?php if (empty($reservations)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3 opacity-50"></i>
                        <h5 class="text-secondary font-weight-bold">Belum Ada Reservasi Aktif</h5>
                        <p class="text-muted small mb-0">Reservasi otomatis dibentuk saat Anda menyetujui pengajuan sewa masuk.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>ID Reservasi</th>
                                    <th>Kamar & Properti</th>
                                    <th>Penyewa</th>
                                    <th>Nominal Sewa</th>
                                    <th>Batas Expiration</th>
                                    <th>Status</th>
                                    <th class="text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reservations as $res): ?>
                                    <?php 
                                        $statusBadge = match($res['status']) {
                                            'active'    => 'badge-success',
                                            'converted' => 'badge-primary',
                                            'expired'   => 'badge-dark',
                                            'cancelled' => 'badge-secondary',
                                            default     => 'badge-light',
                                        };
                                        $statusLabel = match($res['status']) {
                                            'active'    => 'Aktif (Terkunci)',
                                            'converted' => 'Terkonversi ke Kontrak',
                                            'expired'   => 'Kedaluwarsa',
                                            'cancelled' => 'Dibatalkan',
                                            default     => ucfirst($res['status']),
                                        };
                                    ?>
                                    <tr>
                                        <td>
                                            <strong class="text-teal">#RES-<?= str_pad($res['id'], 5, '0', STR_PAD_LEFT) ?></strong>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark"><?= esc($res['unit_name']) ?></div>
                                            <div class="small text-muted"><?= esc($res['property_name']) ?></div>
                                        </td>
                                        <td>
                                            <div class="font-weight-bold text-dark"><?= esc($res['tenant_name']) ?></div>
                                            <div class="small text-muted"><i class="fab fa-whatsapp text-success mr-1"></i><?= esc($res['tenant_whatsapp'] ?? '-') ?></div>
                                        </td>
                                        <td>
                                            <strong class="text-dark">Rp <?= number_format($res['rental_amount'], 0, ',', '.') ?></strong>
                                        </td>
                                        <td>
                                            <span class="text-danger font-weight-bold small">
                                                <i class="fas fa-clock mr-1"></i> <?= date('d M Y, H:i', strtotime($res['expires_at'])) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $statusBadge ?> px-2 py-1">
                                                <?= esc($statusLabel) ?>
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?= base_url('/provider/applications/' . $res['application_id']) ?>" class="btn btn-sm btn-outline-teal">
                                                Detail Pengajuan
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
