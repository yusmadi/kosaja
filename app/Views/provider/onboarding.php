<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-id-card text-teal mr-2"></i> Verifikasi Identitas & Rekening (KYC)
            </h1>
            <p class="text-muted small mb-0">Lengkapi identitas KTP dan nomor rekening bank untuk pencairan hasil sewa properti Anda.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/provider/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Dasbor
            </a>
        </div>
    </div>
</div>

<!-- Flash alerts -->
<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle mr-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <strong><i class="fas fa-exclamation-triangle mr-2"></i> Terjadi kesalahan input:</strong>
        <ul class="mb-0 mt-1 pl-3">
            <?php foreach (session()->getFlashdata('errors') as $err): ?>
                <li><?= esc($err) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<!-- Status Banner -->
<?php $status = $profile['verification_status'] ?? 'pending'; ?>
<?php if ($status === 'verified'): ?>
    <div class="alert alert-success border-0 shadow-sm d-flex align-items-center mb-4">
        <i class="fas fa-certificate fa-3x mr-3 text-success"></i>
        <div>
            <h5 class="font-weight-bold mb-1">Identitas Anda Terverifikasi (Verified)</h5>
            <p class="mb-0 text-sm">
                Akun Anda telah resmi disetujui oleh tim KosAja. Seluruh penerimaan sewa akan disalurkan ke rekening bank terdaftar.
            </p>
        </div>
    </div>
<?php elseif ($status === 'rejected'): ?>
    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center mb-4">
        <i class="fas fa-times-circle fa-3x mr-3 text-danger"></i>
        <div>
            <h5 class="font-weight-bold mb-1">Verifikasi Memerlukan Perbaikan</h5>
            <p class="mb-1 text-sm font-weight-bold text-danger">
                Alasan Penolakan: "<?= esc($profile['rejection_reason'] ?? 'Foto KTP buram / nomor rekening tidak cocok') ?>"
            </p>
            <p class="mb-0 text-sm text-dark">
                Silakan perbaiki foto identitas atau perbarui nomor rekening Anda di bawah ini, lalu klik Simpan untuk mengajukan verifikasi ulang.
            </p>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4" style="background-color: #fef3c7; color: #92400e;">
        <i class="fas fa-hourglass-half fa-3x mr-3 text-warning"></i>
        <div>
            <h5 class="font-weight-bold mb-1">Status Verifikasi: Menunggu Peninjauan Admin (Pending)</h5>
            <p class="mb-0 text-sm">
                Dokumen Anda sedang dalam antrean verifikasi tim kami (biasanya 1x24 jam). Sesuai panduan platform, <strong>Anda tetap dapat mulai mendaftarkan properti dan kamar fisik Anda sekarang juga</strong> sebelum verifikasi selesai.
            </p>
        </div>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Form KYC -->
    <div class="col-lg-8">
        <div class="card card-outline card-teal shadow-sm">
            <div class="card-header">
                <h3 class="card-title font-weight-bold">
                    <i class="fas fa-user-shield text-teal mr-2"></i> Formulir Data Identitas & Settlement
                </h3>
            </div>
            <form action="<?= base_url('/provider/verification/update') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Nama Lengkap Pemilik</label>
                            <input type="text" class="form-control" value="<?= esc($user['name'] ?? '') ?>" readonly disabled>
                            <small class="text-muted">Sesuai data akun terdaftar.</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Nomor WhatsApp</label>
                            <input type="text" class="form-control" value="<?= esc($user['whatsapp'] ?? '') ?>" readonly disabled>
                            <small class="text-muted">Digunakan untuk notifikasi dan kontak darurat.</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Email</label>
                            <input type="email" class="form-control" value="<?= esc($user['email'] ?? '') ?>" readonly disabled>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label font-weight-bold">Jenis Kelamin</label>
                            <input type="text" class="form-control" value="<?= ($user['gender'] ?? '') === 'L' ? 'Laki-laki (Ikhwan)' : 'Perempuan (Akhwat)' ?>" readonly disabled>
                        </div>
                    </div>

                    <hr class="my-3">

                    <h5 class="font-weight-bold text-teal mb-3">
                        <i class="fas fa-building-columns mr-1"></i> Data Rekening Settlement (Pencairan Dana)
                    </h5>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="bank_name" class="form-label font-weight-bold">Nama Bank Penerima <span class="text-danger">*</span></label>
                            <select name="bank_name" id="bank_name" class="form-control" required>
                                <option value="">-- Pilih Bank Rekening --</option>
                                <?php
                                $bankOptions = [
                                    'BSI (Bank Syariah Indonesia)',
                                    'BCA Syariah',
                                    'Bank Muamalat',
                                    'Bank Mandiri',
                                    'Bank Central Asia (BCA)',
                                    'Bank Rakyat Indonesia (BRI)',
                                    'Bank Negara Indonesia (BNI)',
                                    'Bank CIMB Niaga',
                                    'Bank Permata',
                                    'Bank Jago / Jago Syariah',
                                ];
                                $currentBank = old('bank_name', $profile['bank_name'] ?? 'BSI (Bank Syariah Indonesia)');
                                ?>
                                <?php foreach ($bankOptions as $bank): ?>
                                    <option value="<?= esc($bank) ?>" <?= $currentBank === $bank ? 'selected' : '' ?>>
                                        <?= esc($bank) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small class="text-muted">Rekomendasi: Bank Syariah untuk mendukung transaksi berkah.</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="bank_account_number" class="form-label font-weight-bold">Nomor Rekening Bank <span class="text-danger">*</span></label>
                            <input type="text" name="bank_account_number" id="bank_account_number" class="form-control font-weight-bold" placeholder="Contoh: 7123456789" value="<?= esc(old('bank_account_number', $profile['bank_account_number'] ?? '')) ?>" required>
                            <small class="text-muted">Pastikan nomor rekening aktif dan atas nama pemilik sendiri.</small>
                        </div>
                    </div>

                    <hr class="my-3">

                    <h5 class="font-weight-bold text-teal mb-3">
                        <i class="fas fa-camera mr-1"></i> Unggah Foto KTP / Identitas Resmi
                    </h5>

                    <div class="form-group mb-3">
                        <label for="id_card_photo" class="form-label font-weight-bold">
                            Foto e-KTP / Kartu Identitas <?= empty($profile['id_card_photo']) ? '<span class="text-danger">*</span>' : '' ?>
                        </label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="id_card_photo" name="id_card_photo" accept="image/jpeg,image/png,image/webp">
                            <label class="custom-file-label" for="id_card_photo">Pilih file foto KTP (JPG, PNG, WebP max 5MB)...</label>
                        </div>
                        <small class="text-muted d-block mt-1">
                            Foto harus jelas, tidak buram, dan teks NIK serta nama terbaca utuh. Data disimpan aman sesuai standar keamanan data.
                        </small>
                    </div>

                    <?php if (!empty($profile['id_card_photo'])): ?>
                        <div class="p-3 bg-light rounded border mb-3">
                            <span class="font-weight-bold text-dark d-block mb-2">Foto KTP yang Terunggah:</span>
                            <div class="d-flex align-items-center">
                                <img src="<?= \App\Libraries\StorageService::getUrl($profile['id_card_photo']) ?>" alt="KTP Provider" class="img-thumbnail mr-3 shadow-sm" style="max-height: 120px; max-width: 200px; object-fit: cover;">
                                <div>
                                    <span class="badge badge-success mb-1"><i class="fas fa-check mr-1"></i> Tersimpan di server</span>
                                    <p class="text-muted text-xs mb-0">Unggah file baru di atas jika ingin mengganti dokumen KTP.</p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white border-top text-right">
                    <button type="submit" class="btn btn-teal text-white font-weight-bold px-4 shadow-sm">
                        <i class="fas fa-save mr-1"></i> Simpan Data Verifikasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Informasi Program & Dukungan -->
    <div class="col-lg-4">
        <!-- Benefit Card -->
        <div class="card shadow-sm border-0 mb-4 bg-gradient-teal text-white">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <i class="fas fa-shield-halved fa-2x mr-3 text-warning"></i>
                    <div>
                        <h5 class="font-weight-bold mb-0">Mitra Terpercaya</h5>
                        <small class="text-white-50">Jaminan Keamanan Ekosistem</small>
                    </div>
                </div>
                <p class="text-sm mb-3">
                    Mitra yang telah terverifikasi akan mendapatkan lencana <strong>Verified Provider</strong> pada setiap listing iklan, meningkatkan kepercayaan calon penyewa hingga 80%.
                </p>
                <div class="border-top border-white-50 pt-3">
                    <div class="d-flex justify-content-between text-sm">
                        <span>Bebas Biaya Listing:</span>
                        <strong>6 Bulan Pertama</strong>
                    </div>
                    <div class="d-flex justify-content-between text-sm mt-1">
                        <span>Berlaku Hingga:</span>
                        <strong><?= date('d M Y', strtotime($profile['free_period_end'] ?? '+6 months')) ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Syarat Kepatuhan Syariat -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h6 class="font-weight-bold mb-0 text-dark">
                    <i class="fas fa-scale-balanced text-teal mr-2"></i> Ketentuan Syariat Hunian
                </h6>
            </div>
            <div class="card-body text-sm">
                <ul class="pl-3 mb-0 text-muted">
                    <li class="mb-2">
                        <strong>Pemisahan Gender Tegas:</strong> Kos/kontrakan wajib ditentukan apakah khusus Putra, Putri, atau Campur Keluarga.
                    </li>
                    <li class="mb-2">
                        <strong>Hunian Keluarga:</strong> Untuk hunian keluarga / pasutri, sistem mewajibkan verifikasi hubungan sah (Buku Nikah / KK).
                    </li>
                    <li>
                        <strong>Lingkungan Positif:</strong> Menolak aktivitas perjudian, miras, dan kemaksiatan di area hunian sewa.
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->section('scripts') ?>
<script>
    // Custom file input label update
    document.addEventListener('DOMContentLoaded', function() {
        var fileInput = document.getElementById('id_card_photo');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                var fileName = e.target.files[0] ? e.target.files[0].name : 'Pilih file foto KTP...';
                var nextSibling = e.target.nextElementSibling;
                if (nextSibling) {
                    nextSibling.innerText = fileName;
                }
            });
        }
    });
</script>
<?= $this->endSection() ?>
<?= $this->endSection() ?>
