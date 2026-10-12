<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-sliders text-info mr-2"></i> Konfigurasi Sistem Platform (SA-11)
            </h1>
            <p class="text-muted small mb-0">Parameter teknis, gateway pembayaran, SLA otomatisasi worker, dan keamanan operasional.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <span class="badge badge-primary px-3 py-2">
                <i class="fas fa-shield-halved mr-1"></i> Otoritas Super Admin
            </span>
        </div>
    </div>
</div>

<div class="row">
    <!-- Operational Parameters -->
    <div class="col-lg-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-stopwatch text-primary mr-2"></i> Parameter Waktu &amp; SLA Transaksi
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/admin/configuration/update') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">Masa Tenggang Tagihan (Grace Period):</label>
                        <div class="input-group">
                            <input type="number" name="grace_period_days" class="form-control" value="<?= esc($config['grace_period_days']) ?>" required min="1" max="14">
                            <div class="input-group-append">
                                <span class="input-group-text">Hari sebelum OVERDUE</span>
                            </div>
                        </div>
                        <small class="text-muted">Penyewa diberi toleransi 3 hari setelah jatuh tempo sebelum status menjadi Overdue (PRD 23).</small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold small text-dark">Batas Waktu Pembayaran Reservasi (Expiry Window):</label>
                        <div class="input-group">
                            <input type="number" name="reservation_expiry" class="form-control" value="<?= esc($config['reservation_expiry']) ?>" required min="1" max="72">
                            <div class="input-group-append">
                                <span class="input-group-text">Jam (Maksimal Kunci Unit)</span>
                            </div>
                        </div>
                        <small class="text-muted">Kamar akan otomatis dilepas kembali ke publik jika lewat batas waktu ini (PRD 17).</small>
                    </div>

                    <button type="submit" class="btn btn-primary font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Parameter Operasional
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Integrations & Environment Status -->
    <div class="col-lg-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-plug text-success mr-2"></i> Status Integrasi Eksternal
                </h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped align-middle mb-0">
                    <tbody>
                        <tr>
                            <td class="font-weight-bold text-dark" style="width: 40%;">Lingkungan (Environment)</td>
                            <td><span class="badge badge-success text-uppercase"><?= esc($config['environment']) ?></span></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-dark">Payment Gateway</td>
                            <td>
                                <span class="font-weight-bold text-primary"><?= esc($config['payment_gateway']) ?></span>
                                <small class="d-block text-muted">Virtual Account &amp; E-Wallet Engine</small>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-dark">Server Key</td>
                            <td><code class="text-muted"><?= esc($config['gateway_server_key']) ?></code></td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-dark">WhatsApp Dispatcher</td>
                            <td>
                                <span class="badge badge-info px-2 py-1"><i class="fas fa-check-circle mr-1"></i> Aktif</span>
                                <small class="d-block text-muted"><?= esc($config['wa_service']) ?></small>
                            </td>
                        </tr>
                        <tr>
                            <td class="font-weight-bold text-dark">Retensi Audit Trail</td>
                            <td><span class="badge badge-secondary"><?= esc($config['audit_retention']) ?></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
