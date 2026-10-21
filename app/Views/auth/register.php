<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-body p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="d-inline-flex align-items-center justify-content-center p-3 rounded-circle bg-light mb-3">
                            <i class="bi bi-person-plus-fill text-teal fs-2"></i>
                        </div>
                        <h2 class="h4 fw-bold">Daftar Akun KosAja</h2>
                        <p class="text-muted small">Pilih peran akun Anda dan isi formulir pendaftaran di bawah ini</p>
                    </div>

                    <!-- Flash Messages -->
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('errors')): ?>
                        <div class="alert alert-danger small">
                            <i class="bi bi-exclamation-circle me-1"></i> <strong>Periksa kembali input Anda:</strong>
                            <ul class="mb-0 ps-3 mt-1">
                                <?php foreach (session()->getFlashdata('errors') as $err): ?>
                                    <li><?= esc($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('/register') ?>" method="POST" id="registerForm">
                        <?= csrf_field() ?>

                        <!-- Role Selection -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-muted text-uppercase mb-2">Daftar Sebagai:</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="role" id="roleTenant" value="tenant" <?= (old('role', $defaultRole ?? 'tenant') === 'tenant') ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-secondary w-100 p-3 text-start rounded-3 h-100" for="roleTenant">
                                        <div class="fw-bold mb-1"><i class="bi bi-person-fill me-1 text-teal"></i> PENYEWA</div>
                                        <div class="small text-muted" style="font-size: 0.75rem;">Mencari kamar kos atau rumah kontrakan.</div>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="role" id="roleProvider" value="provider" <?= (old('role', $defaultRole ?? 'tenant') === 'provider') ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-secondary w-100 p-3 text-start rounded-3 h-100" for="roleProvider">
                                        <div class="fw-bold mb-1"><i class="bi bi-building me-1 text-teal"></i> PENYEDIA</div>
                                        <div class="small text-muted" style="font-size: 0.75rem;">Pemilik kos atau pengelola properti.</div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="mb-3">
                            <label for="name" class="form-label small fw-bold text-muted text-uppercase">Nama Lengkap</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Sesuai KTP" value="<?= old('name') ?>" required>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label small fw-bold text-muted text-uppercase">Alamat Email</label>
                                <input type="email" name="email" id="email" class="form-control" placeholder="nama@email.com" value="<?= old('email') ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="whatsapp" class="form-label small fw-bold text-muted text-uppercase">Nomor WhatsApp</label>
                                <input type="text" name="whatsapp" id="whatsapp" class="form-control" placeholder="08xxxxxxxxxx" value="<?= old('whatsapp') ?>" required>
                            </div>
                        </div>

                        <!-- Gender for Syariat Compliance -->
                        <div class="mb-3 p-3 bg-light rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label small fw-bold text-muted text-uppercase mb-0">
                                    Jenis Kelamin <span class="text-danger">*</span>
                                </label>
                                <span class="badge bg-teal bg-opacity-10 text-teal small" style="font-size: 0.7rem;">
                                    <i class="bi bi-shield-check me-1"></i> Kepatuhan Syariat
                                </span>
                            </div>
                            <p class="text-muted small mb-2" style="font-size: 0.78rem;">
                                KosAja menerapkan aturan penempatan kos putra, kos putri, dan keluarga. Penentuan gender wajib valid sesuai identitas KTP.
                            </p>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="genderL" value="L" <?= (old('gender') === 'L') ? 'checked' : '' ?> required>
                                    <label class="form-check-label fw-semibold" for="genderL">
                                        <i class="bi bi-gender-male text-primary me-1"></i> Laki-laki
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" id="genderP" value="P" <?= (old('gender') === 'P') ? 'checked' : '' ?> required>
                                    <label class="form-check-label fw-semibold" for="genderP">
                                        <i class="bi bi-gender-female text-danger me-1"></i> Perempuan
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Provider-specific fields -->
                        <div id="providerFields" style="display: none;">
                            <!-- Wilayah Operasional Kos -->
                            <div class="mb-3">
                                <label for="provider_region_id" class="form-label small fw-bold text-muted text-uppercase">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> Wilayah Operasional Kos <span class="text-danger">*</span>
                                </label>
                                <select name="region_id" id="provider_region_id" class="form-select">
                                    <option value="">-- Pilih Wilayah Operasional (Kabupaten / Kota) --</option>
                                    <?php if (!empty($regions)): ?>
                                        <?php foreach ($regions as $r): ?>
                                            <?php $isSelectable = ($r['level'] !== 'province'); ?>
                                            <option value="<?= $r['id'] ?>" <?= old('region_id') == $r['id'] ? 'selected' : '' ?> <?= ! $isSelectable ? 'disabled class="fw-bold bg-light text-muted"' : '' ?>>
                                                <?= str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $r['depth'] ?? 0) ?>
                                                <?= ($r['depth'] ?? 0) > 0 ? '↳ ' : '' ?><?= esc($r['name']) ?> <?= ! $isSelectable ? '(Provinsi - Pilih Kota/Kabupaten)' : '(' . ucfirst($r['level']) . ')' ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text small text-muted">
                                    <i class="bi bi-shield-check text-teal me-1"></i> <strong>Aturan Wilayah:</strong> Akun penyedia hanya dapat mendaftarkan kos di wilayah ini. Verifikasi akun Anda akan diproses langsung oleh <strong>Admin Daerah</strong> wilayah tersebut.
                                </div>
                            </div>

                            <div class="p-3 bg-teal bg-opacity-10 rounded-3 border border-teal mb-3">
                                <div class="fw-bold small text-teal mb-1"><i class="bi bi-gift-fill me-1"></i> Promo Bebas Listing Fee (30 Hari / Transaksi Sewa Pertama)!</div>
                                <div class="small text-muted" style="font-size: 0.8rem;">Rekening ini digunakan untuk penyaluran *settlement* sewa langsung dari penyewa.</div>
                                <div class="row g-2 mt-2">
                                    <div class="col-md-6">
                                        <label for="bank_name" class="form-label small fw-bold text-muted text-uppercase">Nama Bank</label>
                                        <input type="text" name="bank_name" id="bank_name" class="form-control form-control-sm" placeholder="Misal: BSI, BCA, Mandiri" value="<?= old('bank_name') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="bank_account_number" class="form-label small fw-bold text-muted text-uppercase">Nomor Rekening</label>
                                        <input type="text" name="bank_account_number" id="bank_account_number" class="form-control form-control-sm" placeholder="Nomor rekening" value="<?= old('bank_account_number') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Password -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label small fw-bold text-muted text-uppercase">Password</label>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Minimal 6 karakter" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password_confirm" class="form-label small fw-bold text-muted text-uppercase">Konfirmasi Password</label>
                                <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Ulangi password" required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-kosaja-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-person-check-fill me-1"></i> Buat Akun Sekarang
                        </button>
                    </form>

                    <div class="text-center mt-4 pt-3 border-top">
                        <p class="small text-muted mb-0">
                            Sudah memiliki akun? <a href="<?= base_url('/login') ?>" class="text-teal fw-bold text-decoration-none">Masuk di sini</a>
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
    function toggleProviderFields() {
        if ($('#roleProvider').is(':checked')) {
            $('#providerFields').slideDown(200);
            $('#provider_region_id').attr('required', true);
        } else {
            $('#providerFields').slideUp(200);
            $('#provider_region_id').removeAttr('required');
        }
    }

    $('input[name="role"]').on('change', toggleProviderFields);
    toggleProviderFields();
</script>
<?= $this->endSection() ?>
