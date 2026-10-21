<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="bg-light py-4 border-bottom">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-teal text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('/checkout/' . $reservation['id']) ?>" class="text-teal text-decoration-none">Checkout</a></li>
                <li class="breadcrumb-item active" aria-current="page">Simulator Pembayaran Gateway</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Payment Gateway Sandbox Simulator</h1>
                <p class="text-muted small mb-0">Uji coba simulasi pembayaran digital resmi tanpa uang nyata (Sandbox Development Mode)</p>
            </div>
            <div>
                <span class="badge bg-warning text-dark px-3 py-2 fs-6 shadow-sm">
                    <i class="bi bi-cpu me-1"></i> Sandbox Testing
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow rounded-4 overflow-hidden">
                <!-- Header -->
                <div class="card-header bg-teal text-white p-4 text-center">
                    <span class="badge bg-white text-teal px-3 py-1 mb-2 text-uppercase fw-bold" style="font-size: 0.75rem;">
                        Secure Payment Gateway
                    </span>
                    <h2 class="h4 fw-bold mb-1">Instruksi Pembayaran Digital</h2>
                    <p class="text-white-50 small mb-0">Order Reference: <code><?= esc($orderId) ?></code></p>
                </div>

                <div class="card-body p-4 p-md-5">
                    <!-- Virtual Account Box -->
                    <div class="text-center p-4 bg-light rounded-4 border mb-4">
                        <span class="text-muted small text-uppercase fw-semibold d-block mb-1">
                            Nomor Virtual Account (<?= strtoupper(str_replace('_', ' ', $paymentMethod)) ?>)
                        </span>
                        <div class="h3 fw-bold text-dark letter-spacing-1 mb-2">
                            <?= esc($vaNumber) ?>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1">
                            <i class="bi bi-shield-check me-1"></i> Atas Nama: KosAja Escrow (<?= esc($reservation['tenant_name']) ?>)
                        </span>
                    </div>

                    <!-- Financial Breakdown Review -->
                    <div class="p-3 bg-white rounded-3 border mb-4 small">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Unit Hunian:</span>
                            <strong class="text-dark"><?= esc($reservation['unit_name']) ?> — <?= esc($reservation['property_name']) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Biaya Sewa (Rental Amount):</span>
                            <span>Rp <?= number_format($rentalAmount, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Biaya Sistem (Gateway Fee):</span>
                            <span>Rp <?= number_format($gatewayFee, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between pt-2 border-top align-items-center">
                            <strong class="text-dark">Total Tagihan:</strong>
                            <strong class="h5 fw-bold text-teal mb-0">Rp <?= number_format($totalAmount, 0, ',', '.') ?></strong>
                        </div>
                    </div>

                    <!-- Simulator Triggers -->
                    <div class="p-4 bg-light rounded-4 border border-teal mb-4">
                        <h3 class="h6 fw-bold text-teal mb-2"><i class="bi bi-sliders me-2"></i>Panel Eksekusi Webhook Gateway</h3>
                        <p class="text-muted small mb-3">
                            Klik tombol di bawah ini untuk mensimulasikan respons webhook otomatis dari penyedia payment gateway (Midtrans/Xendit) ke server KosAja:
                        </p>

                        <div class="d-grid gap-2">
                            <form action="<?= base_url('/checkout/simulation-execute/' . $reservation['id']) ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="order_id" value="<?= esc($orderId) ?>">
                                <input type="hidden" name="amount" value="<?= $totalAmount ?>">
                                <input type="hidden" name="payment_method" value="<?= esc($paymentMethod) ?>">
                                <input type="hidden" name="outcome" value="SUCCESS">
                                <input type="hidden" name="status" value="SUCCESS">
                                <button type="submit" class="btn btn-success w-100 py-3 fw-bold shadow-sm">
                                    <i class="bi bi-check2-circle me-1"></i> Simulasikan Pembayaran BERHASIL (Success Settlement)
                                </button>
                            </form>

                            <form action="<?= base_url('/checkout/simulation-execute/' . $reservation['id']) ?>" method="POST">
                                <?= csrf_field() ?>
                                <input type="hidden" name="order_id" value="<?= esc($orderId) ?>">
                                <input type="hidden" name="amount" value="<?= $totalAmount ?>">
                                <input type="hidden" name="payment_method" value="<?= esc($paymentMethod) ?>">
                                <input type="hidden" name="outcome" value="FAILED">
                                <input type="hidden" name="status" value="FAILED">
                                <button type="submit" class="btn btn-outline-danger w-100 py-2 small">
                                    <i class="bi bi-x-circle me-1"></i> Simulasikan Pembayaran GAGAL / EXPIRED
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="text-center">
                        <a href="<?= base_url('/checkout/' . $reservation['id']) ?>" class="small text-muted text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i> Kembali ke Rincian Checkout
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
