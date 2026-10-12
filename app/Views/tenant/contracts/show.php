<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="bg-light py-4 border-bottom d-print-none">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 small">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-teal text-decoration-none">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('/contracts') ?>" class="text-teal text-decoration-none">Kontrak Sewa</a></li>
                <li class="breadcrumb-item active" aria-current="page">#CTR-<?= str_pad($contract['id'], 5, '0', STR_PAD_LEFT) ?></li>
            </ol>
        </nav>
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Surat Perjanjian Sewa Digital</h1>
                <p class="text-muted small mb-0">Nomor Registrasi Kontrak: <strong>KOS/CTR/<?= date('Y', strtotime($contract['created_at'])) ?>/<?= str_pad($contract['id'], 5, '0', STR_PAD_LEFT) ?></strong></p>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> Cetak Dokumen / Simpan PDF
                </button>
                <span class="badge bg-success px-3 py-2 fs-6 shadow-sm">
                    <i class="bi bi-shield-check me-1"></i> Kontrak Resmi Aktif
                </span>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 d-print-none" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Printable Digital Contract Paper -->
            <div class="card border shadow-sm rounded-4 p-4 p-md-5 bg-white contract-paper">
                <!-- Letterhead -->
                <div class="text-center pb-4 mb-4 border-bottom">
                    <div class="d-flex align-items-center justify-content-center gap-2 mb-2">
                        <i class="bi bi-house-heart-fill text-teal fs-2"></i>
                        <span class="h3 fw-bold text-dark mb-0 letter-spacing-1">Kos<span class="text-teal">Aja</span></span>
                    </div>
                    <h2 class="h5 fw-bold text-uppercase text-dark mb-1">SURAT PERJANJIAN SEWA MENYEWA HUNIAN</h2>
                    <p class="text-muted small mb-0">
                        Nomor Kontrak: <strong>KOS/CTR/<?= date('Y', strtotime($contract['created_at'])) ?>/<?= str_pad($contract['id'], 5, '0', STR_PAD_LEFT) ?></strong>
                    </p>
                </div>

                <!-- Pembukaan -->
                <p class="small text-secondary mb-4" style="line-height: 1.8;">
                    Pada hari ini, terhitung sejak tanggal <strong><?= date('d F Y', strtotime($contract['created_at'])) ?></strong>, telah dibuat dan disepakati perjanjian sewa menyewa hunian secara digital dan sah melalui platform <strong>KosAja</strong> oleh dan antara pihak-pihak di bawah ini:
                </p>

                <!-- Identitas Para Pihak -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <span class="badge bg-teal text-white mb-2">PIHAK PERTAMA (PEMILIK / PENGELOLA)</span>
                            <div class="small">
                                <div class="mb-1"><strong>Nama:</strong> <?= esc($contract['provider_name']) ?></div>
                                <div class="mb-1"><strong>Email:</strong> <?= esc($contract['provider_email']) ?></div>
                                <div class="mb-1"><strong>WhatsApp:</strong> <?= esc($contract['provider_whatsapp'] ?? '-') ?></div>
                                <div><strong>Properti:</strong> <?= esc($contract['property_name']) ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 border h-100">
                            <span class="badge bg-secondary text-white mb-2">PIHAK KEDUA (PENYEWA)</span>
                            <div class="small">
                                <div class="mb-1"><strong>Nama:</strong> <?= esc($contract['tenant_name']) ?></div>
                                <div class="mb-1"><strong>Email:</strong> <?= esc($contract['tenant_email']) ?></div>
                                <div class="mb-1"><strong>WhatsApp:</strong> <?= esc($contract['tenant_whatsapp'] ?? '-') ?></div>
                                <div><strong>Gender:</strong> <?= $contract['tenant_gender'] === 'L' ? 'Laki-laki (Putra)' : 'Perempuan (Putri)' ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pasal-Pasal Perjanjian -->
                <div class="contract-clauses small text-secondary" style="line-height: 1.8;">
                    <!-- Pasal 1 -->
                    <div class="mb-4">
                        <h3 class="h6 fw-bold text-dark mb-1">PASAL 1 — OBJEK SEWA</h3>
                        <p class="mb-0">
                            PIHAK PERTAMA menyewakan kepada PIHAK KEDUA sebuah unit hunian kamar: <strong>Kamar <?= esc($contract['unit_name']) ?></strong> (Lantai <?= esc($contract['unit_floor'] ?? '1') ?>, Ukuran <?= esc($contract['unit_size'] ?? '-') ?>) yang beralamat di <strong><?= esc($contract['property_address']) ?></strong>, Wilayah <?= esc($contract['region_name'] ?? 'Sleman') ?>.
                        </p>
                    </div>

                    <!-- Pasal 2 -->
                    <div class="mb-4">
                        <h3 class="h6 fw-bold text-dark mb-1">PASAL 2 — JANGKA WAKTU SEWA</h3>
                        <p class="mb-0">
                            Perjanjian sewa ini berlaku terhitung mulai tanggal <strong><?= date('d F Y', strtotime($contract['start_date'])) ?></strong> dan berakhir pada tanggal <strong><?= date('d F Y', strtotime($contract['end_date'])) ?></strong>.
                        </p>
                    </div>

                    <!-- Pasal 3 -->
                    <div class="mb-4">
                        <h3 class="h6 fw-bold text-dark mb-1">PASAL 3 — NILAI SEWA & SKEMA PEMBAYARAN</h3>
                        <p class="mb-1">
                            Nilai sewa yang disepakati untuk unit tersebut adalah sebesar <strong>Rp <?= number_format($contract['agreed_price'], 0, ',', '.') ?></strong> dengan skema pembayaran <strong><?= strtoupper(str_replace('_', ' ', $contract['payment_schedule'])) ?></strong>.
                        </p>
                        <p class="mb-0">
                            Pembayaran termin awal telah diverifikasi dan diselesaikan secara lunas melalui sistem platform KosAja.
                        </p>
                    </div>

                    <!-- Pasal 4 -->
                    <div class="mb-4">
                        <h3 class="h6 fw-bold text-dark mb-1">PASAL 4 — TATA TERTIB & KEPATUHAN SYARIAT</h3>
                        <ol class="ps-3 mb-0">
                            <li>PIHAK KEDUA berkewajiban mematuhi norma adab, kesopanan, dan ketentuan syariat hunian (Kategori: <strong><?= strtoupper(esc($contract['target_gender'])) ?></strong>).</li>
                            <li>Dilarang membawa lawan jenis yang bukan mahram/pasangan sah ke dalam kamar tidur pribadi.</li>
                            <li>Bagi hunian keluarga/pasutri, wajib bersedia memperlihatkan dokumen Buku Nikah asli saat verifikasi serah terima kunci.</li>
                            <li>Menjaga ketenteraman, kebersihan, serta keamanan fasilitas bersama.</li>
                        </ol>
                    </div>
                </div>

                <!-- Rincian Pembayaran Terverifikasi -->
                <div class="mt-4 pt-4 border-top">
                    <h3 class="h6 fw-bold text-dark mb-3"><i class="bi bi-receipt me-2 text-teal"></i>Riwayat Pembayaran Termin Pertama</h3>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm small mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Nomor Tagihan</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Porsi Sewa</th>
                                    <th>Gateway Fee</th>
                                    <th>Total Dibayar</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($invoices)): ?>
                                    <?php foreach ($invoices as $inv): ?>
                                        <tr>
                                            <td class="font-monospace">#INV-<?= str_pad($inv['id'], 5, '0', STR_PAD_LEFT) ?></td>
                                            <td><?= date('d M Y', strtotime($inv['due_date'])) ?></td>
                                            <td>Rp <?= number_format($inv['rental_amount'], 0, ',', '.') ?></td>
                                            <td>Rp <?= number_format($inv['gateway_fee'], 0, ',', '.') ?></td>
                                            <td class="fw-bold text-dark">Rp <?= number_format($inv['total_amount'], 0, ',', '.') ?></td>
                                            <td>
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> LUNAS (PAID)</span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tanda Tangan Digital & Escrow Seal -->
                <div class="row mt-5 pt-4 border-top text-center">
                    <div class="col-6">
                        <span class="small text-muted d-block mb-4">PIHAK PERTAMA</span>
                        <div class="digital-seal mx-auto mb-2 text-success">
                            <i class="bi bi-patch-check-fill fs-3"></i>
                            <div class="small fw-bold">TERVERIFIKASI PLATFORM</div>
                        </div>
                        <strong class="text-dark small d-block"><?= esc($contract['provider_name']) ?></strong>
                        <small class="text-muted">(Pemilik Properti)</small>
                    </div>
                    <div class="col-6">
                        <span class="small text-muted d-block mb-4">PIHAK KEDUA</span>
                        <div class="digital-seal mx-auto mb-2 text-teal">
                            <i class="bi bi-patch-check-fill fs-3"></i>
                            <div class="small fw-bold">PEMBAYARAN SAH</div>
                        </div>
                        <strong class="text-dark small d-block"><?= esc($contract['tenant_name']) ?></strong>
                        <small class="text-muted">(Penyewa Hunian)</small>
                    </div>
                </div>

                <!-- Footer Disclaimer -->
                <div class="text-center text-muted small mt-5 pt-3 border-top" style="font-size: 0.72rem;">
                    Dokumen ini digenerate secara otomatis oleh sistem KosAja dan memiliki kekuatan bukti kesepakatan sewa menyewa digital resmi antara Pemilik dan Penyewa.
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        body {
            background-color: #fff !important;
        }
        .contract-paper {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }
    }
</style>
<?= $this->endSection() ?>
