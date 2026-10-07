<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-5 my-md-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-light mb-3">
                            <i class="bi bi-houses-fill text-teal fs-2"></i>
                        </div>
                        <h2 class="h4 fw-bold">Selamat Datang di KosAja</h2>
                        <p class="text-muted small">Silakan masuk ke akun Anda</p>
                    </div>

                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show small" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger small">
                            <ul class="mb-0 ps-3">
                                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                    <li><?= esc($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('/login') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="email" class="form-label small fw-bold text-muted text-uppercase">Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                                <input type="email" name="email" id="email" class="form-control border-start-0 ps-0" placeholder="nama@email.com" value="<?= old('email') ?>" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <label for="password" class="form-label small fw-bold text-muted text-uppercase">Password</label>
                                <a href="#" class="small text-muted text-decoration-none">Lupa password?</a>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-lock text-muted"></i></span>
                                <input type="password" name="password" id="password" class="form-control border-start-0 ps-0" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div class="form-check mb-4">
                            <input class="form-check-input" type="checkbox" id="rememberMe">
                            <label class="form-check-label small text-muted" for="rememberMe">
                                Ingat saya di perangkat ini
                            </label>
                        </div>

                        <button type="submit" class="btn btn-kosaja-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                        </button>
                    </form>

                    <!-- Quick Demo Accounts for Testing -->
                    <div class="mt-4 pt-4 border-top text-center">
                        <p class="small text-muted fw-bold mb-2">Akun Uji Coba Cepat (Sprint Demo):</p>
                        <div class="d-flex flex-wrap justify-content-center gap-1">
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 btn-fill-demo" data-email="admin@kosaja.id" data-pass="password123">
                                <i class="bi bi-shield-lock text-danger"></i> Admin
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 btn-fill-demo" data-email="ahmad@owner.kosaja.id" data-pass="password123">
                                <i class="bi bi-shop text-primary"></i> Penyedia (Owner)
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 btn-fill-demo" data-email="budi@gmail.com" data-pass="password123">
                                <i class="bi bi-person text-success"></i> Penyewa
                            </button>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <p class="small text-muted mb-0">
                            Belum memiliki akun? <a href="<?= base_url('/register') ?>" class="text-teal fw-bold text-decoration-none">Daftar Sekarang</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    $('.btn-fill-demo').on('click', function() {
        $('#email').val($(this).data('email'));
        $('#password').val($(this).data('pass'));
    });
</script>
<?= $this->endSection() ?>
