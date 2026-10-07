<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <!-- Header Card -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #0f766e 100%); color: #ffffff;">
                <div class="card-body p-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white text-teal d-flex align-items-center justify-content-center shadow" style="width: 70px; height: 70px; font-size: 2rem;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div>
                            <h1 class="h4 fw-bold mb-1 text-white"><?= esc($user['name']) ?></h1>
                            <div class="d-flex flex-wrap gap-2 align-items-center">
                                <span class="badge bg-light text-dark text-uppercase small">
                                    Role: <?= esc($user['role']) ?>
                                </span>
                                <span class="badge bg-white bg-opacity-25 text-white small">
                                    <i class="bi <?= $user['gender'] === 'L' ? 'bi-gender-male' : 'bi-gender-female' ?> me-1"></i>
                                    <?= $user['gender'] === 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                </span>
                                <?php if ($user['role'] === 'provider'): ?>
                                    <span class="badge bg-warning text-dark small">
                                        Status KYC: <?= esc($providerProfile['verification_status'] ?? 'pending') ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div>
                        <?php if (in_array($user['role'], ['super_admin', 'admin_daerah'])): ?>
                            <a href="<?= base_url('/admin/dashboard') ?>" class="btn btn-light btn-sm fw-bold">
                                <i class="bi bi-shield-lock me-1 text-danger"></i> Ke Backoffice Admin
                            </a>
                        <?php elseif ($user['role'] === 'provider'): ?>
                            <a href="<?= base_url('/provider/dashboard') ?>" class="btn btn-light btn-sm fw-bold">
                                <i class="bi bi-speedometer2 me-1 text-teal"></i> Ke Dasbor Penyedia
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Flash Alerts -->
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
                    <i class="bi bi-exclamation-circle me-1"></i> <strong>Periksa data Anda:</strong>
                    <ul class="mb-0 ps-3 mt-1">
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Profile Form -->
                <div class="col-md-7">
                    <div class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3"><i class="bi bi-person-gear text-teal me-2"></i> Pengaturan Informasi Dasar</h2>

                            <form action="<?= base_url('/profile/update') ?>" method="POST">
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted text-uppercase">Alamat Email (Akun)</label>
                                    <input type="email" class="form-control bg-light" value="<?= esc($user['email']) ?>" readonly>
                                    <div class="form-text small">Email digunakan sebagai identitas akun dan tidak dapat diubah.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label small fw-bold text-muted text-uppercase">Nama Lengkap</label>
                                    <input type="text" name="name" id="name" class="form-control" value="<?= old('name', $user['name']) ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="whatsapp" class="form-label small fw-bold text-muted text-uppercase">Nomor WhatsApp Aktif</label>
                                    <input type="text" name="whatsapp" id="whatsapp" class="form-control" value="<?= old('whatsapp', $user['whatsapp']) ?>" required>
                                </div>

                                <!-- Syariat Gender Field -->
                                <div class="mb-3 p-3 bg-light rounded-3 border">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-0">
                                            Jenis Kelamin <span class="text-danger">*</span>
                                        </label>
                                        <span class="badge bg-teal text-white small" style="font-size: 0.65rem;">Wajib Syariat</span>
                                    </div>
                                    <p class="text-muted small mb-2" style="font-size: 0.75rem;">
                                        Penetapan gender mengunci hak booking kamar (Putra/Putri) sesuai syariat.
                                    </p>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="genderL" value="L" <?= (old('gender', $user['gender']) === 'L') ? 'checked' : '' ?> required>
                                            <label class="form-check-label fw-semibold" for="genderL">
                                                <i class="bi bi-gender-male text-primary me-1"></i> Laki-laki
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="gender" id="genderP" value="P" <?= (old('gender', $user['gender']) === 'P') ? 'checked' : '' ?> required>
                                            <label class="form-check-label fw-semibold" for="genderP">
                                                <i class="bi bi-gender-female text-danger me-1"></i> Perempuan
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <?php if ($user['role'] === 'provider'): ?>
                                    <!-- Bank Information -->
                                    <div class="p-3 bg-teal bg-opacity-10 rounded-3 border border-teal mb-3">
                                        <h6 class="fw-bold text-teal mb-2"><i class="bi bi-bank me-1"></i> Data Rekening Settlement</h6>
                                        <div class="mb-2">
                                            <label for="bank_name" class="form-label small fw-bold text-muted text-uppercase">Nama Bank</label>
                                            <input type="text" name="bank_name" id="bank_name" class="form-control form-control-sm" value="<?= old('bank_name', $providerProfile['bank_name'] ?? '') ?>" placeholder="Misal: BSI, Mandiri, BCA">
                                        </div>
                                        <div>
                                            <label for="bank_account_number" class="form-label small fw-bold text-muted text-uppercase">Nomor Rekening</label>
                                            <input type="text" name="bank_account_number" id="bank_account_number" class="form-control form-control-sm" value="<?= old('bank_account_number', $providerProfile['bank_account_number'] ?? '') ?>" placeholder="Nomor rekening bank">
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <button type="submit" class="btn btn-kosaja-primary w-100 py-2">
                                    <i class="bi bi-check2-circle me-1"></i> Simpan Perubahan Profil
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Password & Security Card -->
                <div class="col-md-5">
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h2 class="h5 fw-bold mb-3"><i class="bi bi-shield-lock text-teal me-2"></i> Keamanan Password</h2>

                            <form action="<?= base_url('/profile/update-password') ?>" method="POST">
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label for="current_password" class="form-label small fw-bold text-muted text-uppercase">Password Saat Ini</label>
                                    <input type="password" name="current_password" id="current_password" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label small fw-bold text-muted text-uppercase">Password Baru</label>
                                    <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Minimal 6 karakter" required>
                                </div>

                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label small fw-bold text-muted text-uppercase">Ulangi Password Baru</label>
                                    <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                                </div>

                                <button type="submit" class="btn btn-outline-secondary w-100 py-2">
                                    <i class="bi bi-key me-1"></i> Ubah Password
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Role Badge Card -->
                    <div class="card border-0 shadow-sm rounded-4 bg-light">
                        <div class="card-body p-3 small text-muted">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-info-circle-fill text-teal"></i>
                                <strong>Pemisahan Akses (RBAC)</strong>
                            </div>
                            Sistem KosAja menerapkan pembatasan hak akses berbasis peran secara ketat. Anda saat ini login dengan hak akses: <strong><?= strtoupper(esc($user['role'])) ?></strong>.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
