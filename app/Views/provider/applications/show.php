<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-clipboard-check text-teal mr-2"></i>Review Pengajuan Sewa
                </h1>
                <p class="text-muted small mb-0">Nomor Pengajuan: <strong>#APP-<?= str_pad($application['id'], 5, '0', STR_PAD_LEFT) ?></strong></p>
            </div>
            <div class="col-sm-6 text-sm-right">
                <a href="<?= base_url('/provider/applications') ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar Pengajuan
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle mr-2"></i><?= session()->getFlashdata('success') ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle mr-2"></i><?= session()->getFlashdata('error') ?>
                <button type="button" class="close" data-dismiss="alert">&times;</button>
            </div>
        <?php endif; ?>

        <?php 
            $status = $application['status'];
            $badgeClass = match($status) {
                'pending'   => 'badge-warning',
                'approved'  => 'badge-success',
                'rejected'  => 'badge-danger',
                'cancelled' => 'badge-secondary',
                'expired'   => 'badge-dark',
                default     => 'badge-light',
            };
            $statusLabel = match($status) {
                'pending'   => 'Menunggu Keputusan Pemilik',
                'approved'  => 'Disetujui (Reservasi Aktif)',
                'rejected'  => 'Ditolak',
                'cancelled' => 'Dibatalkan oleh Penyewa',
                'expired'   => 'Kedaluwarsa',
                default     => ucfirst($status),
            };
        ?>

        <!-- Application Status Header Card -->
        <div class="card card-outline <?= $status === 'approved' ? 'card-success' : ($status === 'rejected' ? 'card-danger' : 'card-warning') ?> shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge <?= $badgeClass ?> px-3 py-1 font-weight-bold" style="font-size: 0.9rem;">
                                <?= esc($statusLabel) ?>
                            </span>
                            <span class="text-muted small ml-2">Diajukan pada: <?= date('d F Y, H:i', strtotime($application['created_at'])) ?> WIB</span>
                        </div>
                        <h4 class="font-weight-bold text-dark mt-2 mb-1">
                            Permohonan Sewa Kamar <?= esc($application['unit_name']) ?> — <?= esc($application['property_name']) ?>
                        </h4>
                        <p class="text-secondary small mb-0">
                            Pemohon: <strong><?= esc($application['tenant_name']) ?></strong> (<?= $application['tenant_gender'] === 'L' ? 'Laki-laki / Putra' : 'Perempuan / Putri' ?>)
                        </p>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                        <?php if ($status === 'pending'): ?>
                            <button type="button" class="btn btn-danger btn-sm px-3 mr-2 font-weight-bold" data-toggle="modal" data-target="#rejectModal">
                                <i class="fas fa-times mr-1"></i> Tolak Pengajuan
                            </button>
                            <button type="button" class="btn btn-success btn-sm px-3 font-weight-bold shadow-sm" data-toggle="modal" data-target="#approveModal">
                                <i class="fas fa-check mr-1"></i> Setujui Pengajuan
                            </button>
                        <?php elseif ($status === 'approved' && !empty($reservation)): ?>
                            <div class="text-md-right">
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-lock mr-1"></i> Unit Telah Dikunci (Reserved)</span>
                                <div class="small text-muted mt-1">
                                    Batas Checkout: <strong><?= date('d M Y, H:i', strtotime($reservation['expires_at'])) ?></strong>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rejection Reason Card (If Rejected) -->
        <?php if ($status === 'rejected'): ?>
            <div class="card card-outline card-danger shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title font-weight-bold mb-0"><i class="fas fa-comment-slash mr-2"></i>Alasan Penolakan yang Diberikan</h5>
                </div>
                <div class="card-body">
                    <p class="text-dark mb-0 font-italic">
                        "<?= esc($application['rejection_reason']) ?>"
                    </p>
                </div>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Left Column: Applicant & Family Validation -->
            <div class="col-lg-6">
                <!-- Data Calon Penyewa -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white font-weight-bold">
                        <i class="fas fa-user-circle text-teal mr-2"></i>Data Profil Calon Penyewa
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" style="width: 40%;">Nama Lengkap:</td>
                                <td class="font-weight-bold text-dark"><?= esc($application['tenant_name']) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Kelamin:</td>
                                <td>
                                    <span class="badge <?= $application['tenant_gender'] === 'L' ? 'badge-primary' : 'badge-danger' ?>">
                                        <i class="fas <?= $application['tenant_gender'] === 'L' ? 'fa-mars' : 'fa-venus' ?> mr-1"></i>
                                        <?= $application['tenant_gender'] === 'L' ? 'Laki-laki (Putra)' : 'Perempuan (Putri)' ?>
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Kesesuaian Gender:</td>
                                <td>
                                    <?php 
                                        $target = $application['target_gender'];
                                        $gender = $application['tenant_gender'];
                                        $isMatch = ($target === 'campur_keluarga') || ($target === 'putra' && $gender === 'L') || ($target === 'putri' && $gender === 'P');
                                    ?>
                                    <?php if ($isMatch): ?>
                                        <span class="text-success font-weight-bold"><i class="fas fa-check-circle mr-1"></i>Cocok Sesuai Syariat Properti</span>
                                    <?php else: ?>
                                        <span class="text-danger font-weight-bold"><i class="fas fa-exclamation-triangle mr-1"></i>Tidak Cocok</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email Akun:</td>
                                <td><?= esc($application['tenant_email']) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nomor WhatsApp:</td>
                                <td>
                                    <?php if (!empty($application['tenant_whatsapp'])): ?>
                                        <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $application['tenant_whatsapp']) ?>?text=<?= urlencode("Halo {$application['tenant_name']}, saya pemilik {$application['property_name']} mengenai pengajuan sewa Anda.") ?>" 
                                           target="_blank" 
                                           class="btn btn-success btn-xs font-weight-bold">
                                            <i class="fab fa-whatsapp mr-1"></i> <?= esc($application['tenant_whatsapp']) ?> (Hubungi)
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>

                        <?php if (!empty($application['applicant_notes'])): ?>
                            <div class="mt-3 p-3 bg-light rounded border">
                                <span class="small font-weight-bold text-muted d-block mb-1">Catatan dari Pemohon:</span>
                                <div class="text-dark small">
                                    <?= nl2br(esc($application['applicant_notes'])) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Verifikasi Pasutri / Syarat Keluarga (Jika Properti Campur/Keluarga) -->
                <?php if ($application['target_gender'] === 'campur_keluarga'): ?>
                    <div class="card card-outline shadow-sm mb-4" style="border-top-color: #7c3aed;">
                        <div class="card-header bg-white font-weight-bold" style="color: #6b21a8;">
                            <i class="fas fa-people-roof mr-2"></i>Validasi Syarat Pasutri & Keluarga Sah
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                Karena properti ini berstatus hunian keluarga, pemilik diwajibkan memvalidasi dokumen legalitas (Buku Nikah / Kartu Keluarga) calon penghuni.
                            </p>

                            <table class="table table-sm table-borderless mb-3">
                                <tr>
                                    <td class="text-muted" style="width: 40%;">Status Dinyatakan:</td>
                                    <td>
                                        <span class="badge text-white px-2 py-1 font-weight-bold" style="background-color: #7c3aed;">
                                            <?= esc(ucfirst(str_replace('_', ' ', $application['marital_status'] ?? 'menikah'))) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status Validasi:</td>
                                    <td>
                                        <?php if ($application['is_family_verified']): ?>
                                            <span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>Telah Divalidasi oleh Anda</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning text-dark"><i class="fas fa-hourglass-half mr-1"></i>Menunggu Verifikasi</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>

                            <!-- Document Photo Preview -->
                            <div class="p-3 bg-light rounded border">
                                <span class="small font-weight-bold text-dark d-block mb-2">Lampiran Dokumen Buku Nikah / KK:</span>
                                <?php if (!empty($application['marriage_certificate_url'])): ?>
                                    <div class="text-center">
                                        <a href="<?= base_url(esc($application['marriage_certificate_url'])) ?>" target="_blank">
                                            <img src="<?= base_url(esc($application['marriage_certificate_url'])) ?>" 
                                                 alt="Dokumen Nikah" 
                                                 class="img-fluid rounded border shadow-sm" 
                                                 style="max-height: 220px; object-fit: contain;">
                                        </a>
                                        <div class="mt-2">
                                            <a href="<?= base_url(esc($application['marriage_certificate_url'])) ?>" target="_blank" class="btn btn-outline-primary btn-xs">
                                                <i class="fas fa-external-link-alt mr-1"></i> Buka Ukuran Penuh
                                            </a>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-light text-muted small border mb-0 text-center">
                                        <i class="fas fa-file-invoice mr-1"></i> Calon penyewa belum mengunggah dokumen digital. Anda dapat meminta penunjukan fisik Buku Nikah saat survei atau check-in serah terima kunci.
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Rencana Sewa & Kamar -->
            <div class="col-lg-6">
                <!-- Rincian Unit & Kamar -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white font-weight-bold">
                        <i class="fas fa-bed text-teal mr-2"></i>Rincian Unit & Jadwal Sewa
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded mb-3">
                            <img src="<?= base_url(esc($application['unit_photo'] ?? ($application['property_photo'] ?? 'assets/images/sample-kos-1.jpg'))) ?>" 
                                 alt="<?= esc($application['unit_name']) ?>" 
                                 class="rounded object-fit-cover shadow-sm mr-3" 
                                 style="width: 75px; height: 75px;">
                            <div>
                                <h5 class="font-weight-bold text-dark mb-1"><?= esc($application['unit_name']) ?></h5>
                                <div class="small text-muted mb-1"><?= esc($application['property_name']) ?></div>
                                <span class="badge badge-info">Status Kamar Saat Ini: <?= ucfirst($application['unit_status']) ?></span>
                            </div>
                        </div>

                        <table class="table table-sm table-borderless mb-0">
                            <tr>
                                <td class="text-muted" style="width: 45%;">Tanggal Masuk:</td>
                                <td class="font-weight-bold text-dark"><?= date('d F Y', strtotime($application['start_date'])) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Durasi Sewa:</td>
                                <td class="font-weight-bold text-dark"><?= esc($application['duration_months']) ?> Bulan</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Skema Pembayaran:</td>
                                <td class="font-weight-bold text-dark text-capitalize"><?= str_replace('_', ' ', esc($application['payment_schedule'])) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Nominal Sewa Termin 1:</td>
                                <td>
                                    <strong class="text-teal font-weight-bold" style="font-size: 1.15rem;">
                                        Rp <?= number_format($application['rental_amount'], 0, ',', '.') ?>
                                    </strong>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tarif Standar Bulanan:</td>
                                <td>Rp <?= number_format($application['price_monthly'], 0, ',', '.') ?> / bln</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- Reservasi Status Card (If already generated) -->
                <?php if ($status === 'approved' && !empty($reservation)): ?>
                    <div class="card card-outline card-success shadow-sm mb-4">
                        <div class="card-header bg-success text-white font-weight-bold">
                            <i class="fas fa-shield-alt mr-2"></i>Entitas Reservasi Aktif
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless mb-0">
                                <tr>
                                    <td class="text-muted">ID Reservasi:</td>
                                    <td class="font-weight-bold">#RES-<?= str_pad($reservation['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Status Reservasi:</td>
                                    <td><span class="badge badge-success text-uppercase"><?= esc($reservation['status']) ?></span></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Batas Waktu Expiration:</td>
                                    <td class="text-danger font-weight-bold">
                                        <i class="fas fa-clock mr-1"></i> <?= date('d M Y, H:i', strtotime($reservation['expires_at'])) ?> WIB
                                    </td>
                                </tr>
                            </table>
                            <div class="alert alert-light text-muted small border mb-0 mt-3">
                                Unit kamar saat ini terkunci khusus bagi penyewa ini. Apabila pembayaran tidak diselesaikan sebelum batas waktu, sistem otomatis mengembalikan kamar ke status AVAILABLE.
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Modal Reject Application -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="<?= base_url('/provider/applications/reject/' . $application['id']) ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title font-weight-bold" id="rejectModalLabel">
                    <i class="fas fa-times-circle mr-2"></i>Tolak Pengajuan Sewa
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary small mb-3">
                    Anda akan menolak pengajuan sewa dari <strong><?= esc($application['tenant_name']) ?></strong> untuk kamar <strong><?= esc($application['unit_name']) ?></strong>. Mohon berikan alasan yang transparan dan sopan.
                </p>

                <div class="form-group mb-0">
                    <label for="rejection_reason" class="font-weight-bold text-dark small">Alasan Penolakan <span class="text-danger">*</span></label>
                    <textarea class="form-control" 
                              id="rejection_reason" 
                              name="rejection_reason" 
                              rows="3" 
                              required 
                              minlength="5"
                              placeholder="Contoh: Kamar sedang dalam tahap perbaikan / tanggal mulai sewa terlalu jauh dari ketersediaan kamar."></textarea>
                    <small class="form-text text-muted">Alasan penolakan ini wajib diisi dan akan dibaca langsung oleh calon penyewa.</small>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batalkan</button>
                <button type="submit" class="btn btn-danger btn-sm font-weight-bold">
                    <i class="fas fa-times mr-1"></i> Konfirmasi Tolak Pengajuan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Approve Application -->
<div class="modal fade" id="approveModal" tabindex="-1" role="dialog" aria-labelledby="approveModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="<?= base_url('/provider/applications/approve/' . $application['id']) ?>" method="POST" class="modal-content border-0 shadow">
            <?= csrf_field() ?>
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold" id="approveModalLabel">
                    <i class="fas fa-check-circle mr-2"></i>Setujui Pengajuan Sewa
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4">
                <p class="text-secondary small mb-3">
                    Dengan menyetujui pengajuan ini:
                </p>
                <ul class="text-secondary small ps-3 mb-3">
                    <li>Kamar fisik <strong><?= esc($application['unit_name']) ?></strong> akan dikunci statusnya menjadi <strong>RESERVED</strong>.</li>
                    <li>Sistem membentuk entitas <strong>RESERVASI</strong> dengan batas pembayaran <strong><?= $reservationConfig->expirationWindowHours ?> Jam</strong>.</li>
                    <li>Penyewa dapat melanjutkan ke proses pembayaran checkout.</li>
                </ul>

                <?php if ($application['target_gender'] === 'campur_keluarga'): ?>
                    <div class="form-check p-3 rounded border mb-3" style="background-color: #faf5ff; border-color: #d8b4fe !important;">
                        <input class="form-check-input ml-0 mr-2" type="checkbox" id="confirm_family_check" name="confirm_family_check" value="1" required>
                        <label class="form-check-label small font-weight-bold text-dark" for="confirm_family_check">
                            Saya telah memverifikasi bukti/dokumen keabsahan hubungan keluarga / suami-istri pemohon ini. <span class="text-danger">*</span>
                        </label>
                    </div>
                <?php endif; ?>

                <div class="alert alert-info py-2 px-3 small mb-0">
                    <i class="fas fa-info-circle mr-1"></i> Apabila penyewa tidak menyelesaikan pembayaran dalam <?= $reservationConfig->expirationWindowHours ?> jam, kamar akan otomatis dibuka kembali.
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Batalkan</button>
                <button type="submit" class="btn btn-success btn-sm font-weight-bold shadow-sm">
                    <i class="fas fa-check mr-1"></i> Ya, Setujui & Kunci Unit
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
