<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-4">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-cash-register text-teal mr-2"></i> Pembayaran Tagihan Paket
            </h1>
            <p class="text-muted small mb-0">Invoice: <strong><?= esc($invoice['invoice_number']) ?></strong></p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/provider/subscription') ?>" class="btn btn-outline-secondary btn-sm font-weight-bold">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Katalog
            </a>
        </div>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="card border-0 shadow-sm" style="border-radius: 14px; overflow: hidden;">
            <div class="card-header bg-teal text-white p-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge badge-warning text-dark text-uppercase px-2 py-1 mb-1 font-weight-bold" style="font-size: 0.7rem;">
                            SANDBOX SIMULATOR & GATEWAY
                        </span>
                        <h4 class="font-weight-bold mb-0">Checkout Tagihan Paket</h4>
                    </div>
                    <div class="text-right">
                        <span class="small d-block text-white-50">Total Tagihan</span>
                        <h3 class="font-weight-bold mb-0">Rp <?= number_format($invoice['amount'], 0, ',', '.') ?></h3>
                    </div>
                </div>
            </div>

            <div class="card-body p-4">
                <!-- Rincian Paket -->
                <div class="p-3 bg-light rounded mb-4">
                    <h6 class="font-weight-bold text-dark mb-2">Rincian Paket yang Dipilih:</h6>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Nama Paket</span>
                        <strong class="text-dark"><?= esc($plan['name']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Tipe & Siklus</span>
                        <span class="text-dark"><?= ($plan['type'] === 'subscription') ? 'Langganan Bulanan (30 Hari)' : 'Listing Fee (15 Hari)' ?></span>
                    </div>
                    <div class="d-flex justify-content-between py-1 border-bottom">
                        <span class="text-muted">Kapasitas Slot Iklan</span>
                        <strong class="text-teal"><?= esc($plan['listing_capacity'] >= 999 ? 'Unlimited (>100)' : $plan['listing_capacity']) ?> Listing Aktif</strong>
                    </div>
                    <div class="d-flex justify-content-between py-1">
                        <span class="text-muted">Order ID</span>
                        <code class="text-dark"><?= esc($invoice['order_id']) ?></code>
                    </div>
                </div>

                <!-- Formulir Pembayaran -->
                <form action="<?= base_url('/provider/subscription/simulate-execute/' . $invoice['id']) ?>" method="POST" id="simForm">
                    <?= csrf_field() ?>

                    <h6 class="font-weight-bold text-dark mb-3">Pilih Metode Pembayaran:</h6>
                    
                    <div class="row mb-4">
                        <div class="col-sm-6 mb-2">
                            <label class="d-block p-3 border rounded cursor-pointer position-relative bg-white payment-option active-option" style="cursor: pointer;">
                                <input type="radio" name="payment_method" value="bsi_va" checked class="mr-2">
                                <strong>BSI Virtual Account</strong>
                                <span class="d-block text-muted small ml-4">Bank Syariah Indonesia</span>
                            </label>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="d-block p-3 border rounded cursor-pointer position-relative bg-white payment-option" style="cursor: pointer;">
                                <input type="radio" name="payment_method" value="bca_va" class="mr-2">
                                <strong>BCA Virtual Account</strong>
                                <span class="d-block text-muted small ml-4">Bank Central Asia</span>
                            </label>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="d-block p-3 border rounded cursor-pointer position-relative bg-white payment-option" style="cursor: pointer;">
                                <input type="radio" name="payment_method" value="mandiri_va" class="mr-2">
                                <strong>Mandiri Virtual Account</strong>
                                <span class="d-block text-muted small ml-4">Bank Mandiri</span>
                            </label>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="d-block p-3 border rounded cursor-pointer position-relative bg-white payment-option" style="cursor: pointer;">
                                <input type="radio" name="payment_method" value="qris" class="mr-2">
                                <strong>QRIS Instant</strong>
                                <span class="d-block text-muted small ml-4">GoPay / OVO / Dana / ShopeePay</span>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center pt-3 border-top">
                        <button type="submit" name="action" value="failed" class="btn btn-outline-danger btn-sm font-weight-bold mb-2 mb-sm-0">
                            <i class="fas fa-times mr-1"></i> Batalkan / Simulasikan Gagal
                        </button>
                        <button type="submit" name="action" value="success" class="btn btn-teal text-white font-weight-bold shadow px-4 py-2">
                            <i class="fas fa-check-circle mr-1"></i> Bayar Sekarang (Simulasi Lunas)
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<style>
    .payment-option:hover {
        border-color: #0d9488 !important;
        background-color: #f0fdf4 !important;
    }
</style>
<?= $this->endSection() ?>
