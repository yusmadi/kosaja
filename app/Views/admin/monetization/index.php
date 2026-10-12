<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-coins text-warning mr-2"></i> Kebijakan Monetisasi Platform (SA-10)
            </h1>
            <p class="text-muted small mb-0">Pengaturan tarif listing fee, masa gratis promosi mitra (free period), dan paket langganan berulang.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <span class="badge badge-primary px-3 py-2">
                <i class="fas fa-shield-halved mr-1"></i> Otoritas Super Admin
            </span>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Core Pricing Rules -->
    <div class="col-lg-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-sliders text-primary mr-2"></i> Parameter Tarif Utama (PRD 29, 30, 31)
                </h5>
            </div>
            <div class="card-body">
                <form action="<?= base_url('/admin/monetization/update') ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">Durasi Masa Bebas Biaya (Free Period):</label>
                        <div class="input-group">
                            <input type="number" name="free_period_months" class="form-control" value="<?= esc($policies['free_period_months']) ?>" required min="1" max="24">
                            <div class="input-group-append">
                                <span class="input-group-text">Bulan sejak aktivasi</span>
                            </div>
                        </div>
                        <small class="text-muted">Mitra baru dibebaskan dari biaya listing selama 30 hari atau hingga transaksi sewa pertama lunas (PRD 29 FR-PM-001).</small>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-dark">Biaya Iklan per Listing (Listing Fee):</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="number" name="listing_fee" class="form-control" value="<?= esc($policies['listing_fee']) ?>" required min="0" step="1000">
                            <div class="input-group-append">
                                <span class="input-group-text">/ listing / bulan</span>
                            </div>
                        </div>
                        <small class="text-muted">Berlaku setelah masa free period berakhir (PRD 30 &amp; 31).</small>
                    </div>

                    <div class="form-group mb-4">
                        <label class="font-weight-bold small text-dark">Biaya Layanan Gerbang Pembayaran (Gateway Fee):</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Rp</span>
                            </div>
                            <input type="number" name="gateway_fee" class="form-control" value="<?= esc($policies['gateway_fee']) ?>" required min="0" step="500">
                            <div class="input-group-append">
                                <span class="input-group-text">/ transaksi sewa</span>
                            </div>
                        </div>
                        <small class="text-muted">Dibebankan pada faktur invoice digital penyewa saat checkout.</small>
                    </div>

                    <button type="submit" class="btn btn-primary font-weight-bold">
                        <i class="fas fa-save mr-1"></i> Simpan Kebijakan Tarif Platform
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Right Column: Subscription Tiers -->
    <div class="col-lg-6 mb-3">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-boxes-stacked text-warning mr-2"></i> Skema Paket Kuota Langganan Mitra (PRD 32)
                </h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php foreach ($policies['subscription_tiers'] as $tier): ?>
                    <li class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="font-weight-bold text-dark mb-0"><?= esc($tier['name']) ?></h6>
                            <span class="badge badge-success px-2 py-1 font-weight-bold">
                                <?= $tier['price'] == 0 ? 'GRATIS' : 'Rp ' . number_format($tier['price'], 0, ',', '.') . ' / bln' ?>
                            </span>
                        </div>
                        <div class="small text-muted mb-1"><?= esc($tier['features']) ?></div>
                        <span class="badge badge-light border text-muted">
                            <i class="fas fa-door-open mr-1"></i> Kuota Maks: <?= esc($tier['quota']) ?> Listing
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <div class="p-3 bg-light border-top">
                    <small class="text-muted">
                        <i class="fas fa-info-circle text-info mr-1"></i>
                        Skema langganan otomatis diperbarui setiap bulan bagi mitra yang telah melampaui masa promosi gratis onboarding.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
