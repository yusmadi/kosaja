<?= $this->extend('layouts/public') ?>

<?= $this->section('content') ?>
<div class="bg-light py-4 border-bottom">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small text-muted">
                        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>" class="text-decoration-none text-muted">Beranda</a></li>
                        <li class="breadcrumb-item active text-dark font-weight-bold" aria-current="page">Dasbor Penyewa</li>
                    </ol>
                </nav>
                <h1 class="h3 fw-bold text-dark mb-0">
                    <i class="bi bi-speedometer2 text-teal me-2"></i> Dasbor Penyewa Saya
                </h1>
                <p class="text-secondary small mb-0">Pantau kamar sewa aktif, bayar tagihan sewa bulanan, dan kelola kontrak digital Anda.</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('/contracts') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="bi bi-file-earmark-check me-1"></i> Kontrak Saya
                </a>
                <a href="<?= base_url('/search') ?>" class="btn btn-kosaja-primary btn-sm rounded-pill px-3">
                    <i class="bi bi-search me-1"></i> Cari Kamar Lain
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container py-4">
    <!-- Flash Notifications -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Overview Stats Banner -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-primary bg-opacity-10 text-primary p-3 me-3 fs-4">
                        <i class="bi bi-house-check"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Hunian Aktif</span>
                        <h4 class="fw-bold mb-0 text-dark"><?= $stats['active_contracts_count'] ?> <small class="text-muted fs-6">Kamar</small></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-danger bg-opacity-10 text-danger p-3 me-3 fs-4">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Tagihan Belum Lunas</span>
                        <h4 class="fw-bold mb-0 <?= $stats['unpaid_invoices_count'] > 0 ? 'text-danger' : 'text-success' ?>">
                            <?= $stats['unpaid_invoices_count'] ?> <small class="text-muted fs-6">Tagihan</small>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-warning bg-opacity-10 text-warning p-3 me-3 fs-4">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Total Nominal Tagihan</span>
                        <h5 class="fw-bold mb-0 text-dark">Rp <?= number_format($stats['total_due_amount'], 0, ',', '.') ?></h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                <div class="d-flex align-items-center">
                    <div class="rounded-3 bg-success bg-opacity-10 text-success p-3 me-3 fs-4">
                        <i class="bi bi-check2-all"></i>
                    </div>
                    <div>
                        <span class="text-muted small d-block">Tagihan Lunas</span>
                        <h4 class="fw-bold mb-0 text-success"><?= $stats['paid_invoices_count'] ?> <small class="text-muted fs-6">Riwayat</small></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Invoices Section (CRITICAL FOR SPRINT 7) -->
    <div class="card border-0 shadow-sm rounded-3 mb-4" id="invoices">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-credit-card-2-front text-teal me-2"></i> Tagihan Aktif & Jatuh Tempo
            </h5>
            <span class="badge bg-teal-subtle text-teal rounded-pill px-3 py-2 fw-semibold">
                <?= count($activeInvoices) ?> Tagihan Menunggu
            </span>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($activeInvoices)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="ps-4">No. Invoice</th>
                                <th>Properti & Kamar</th>
                                <th>Jatuh Tempo</th>
                                <th>Nominal</th>
                                <th>Status Tagihan</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activeInvoices as $inv): ?>
                                <tr>
                                    <td class="ps-4 fw-bold text-dark">
                                        #INV-<?= $inv['id'] ?>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= esc($inv['property_name']) ?></div>
                                        <small class="text-muted">Kamar: <?= esc($inv['unit_name']) ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold <?= ($inv['status'] === 'overdue' || $inv['is_in_grace_period']) ? 'text-danger' : 'text-dark' ?>">
                                            <i class="bi bi-calendar-event me-1"></i> <?= date('d M Y', strtotime($inv['due_date'])) ?>
                                        </div>
                                        <?php if ($inv['is_in_grace_period']): ?>
                                            <span class="badge bg-warning text-dark small mt-1">
                                                <i class="bi bi-clock-history me-1"></i> Grace Period: sisa <?= $inv['grace_days_left'] ?> hari
                                            </span>
                                        <?php elseif ($inv['status'] === 'overdue'): ?>
                                            <span class="badge bg-danger small mt-1">
                                                <i class="bi bi-exclamation-octagon-fill me-1"></i> Terlambat (Overdue)
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">Rp <?= number_format($inv['total_amount'], 0, ',', '.') ?></div>
                                        <small class="text-muted">Sewa: Rp <?= number_format($inv['rental_amount'], 0, ',', '.') ?> + Fee: Rp <?= number_format($inv['gateway_fee'], 0, ',', '.') ?></small>
                                    </td>
                                    <td>
                                        <?php if ($inv['status'] === 'overdue'): ?>
                                            <span class="badge bg-danger rounded-pill px-3 py-2 fw-semibold">OVERDUE</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-semibold">PENDING</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button type="button" class="btn btn-kosaja-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#payModal-<?= $inv['id'] ?>">
                                            <i class="bi bi-credit-card me-1"></i> Bayar Sekarang
                                        </button>
                                    </td>
                                </tr>

                                <!-- Payment Modal for Invoice -->
                                <div class="modal fade" id="payModal-<?= $inv['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold">Konfirmasi Pembayaran Tagihan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="<?= base_url('/tenant/invoices/pay/' . $inv['id']) ?>" method="POST">
                                                <?= csrf_field() ?>
                                                <div class="modal-body py-3">
                                                    <div class="p-3 bg-light rounded-3 mb-3">
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted small">Nomor Tagihan:</span>
                                                            <strong class="text-dark">#INV-<?= $inv['id'] ?></strong>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted small">Kamar & Properti:</span>
                                                            <span class="text-dark fw-semibold"><?= esc($inv['unit_name']) ?> (<?= esc($inv['property_name']) ?>)</span>
                                                        </div>
                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span class="text-muted small">Jatuh Tempo:</span>
                                                            <span class="text-dark"><?= date('d M Y', strtotime($inv['due_date'])) ?></span>
                                                        </div>
                                                        <hr class="my-2">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span class="fw-bold">Total Pembayaran:</span>
                                                            <h5 class="fw-bold text-teal mb-0">Rp <?= number_format($inv['total_amount'], 0, ',', '.') ?></h5>
                                                        </div>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold text-muted">Pilih Metode Pembayaran Digital:</label>
                                                        <div class="form-check p-3 border rounded-3 mb-2 d-flex align-items-center">
                                                            <input class="form-check-input me-3" type="radio" name="payment_method" id="payMethodBsi-<?= $inv['id'] ?>" value="bsi_va" checked>
                                                            <label class="form-check-label flex-grow-1" for="payMethodBsi-<?= $inv['id'] ?>">
                                                                <strong class="d-block text-dark">BSI Virtual Account (Syariah)</strong>
                                                                <small class="text-muted">Verifikasi instan otomatis bebas riba</small>
                                                            </label>
                                                        </div>
                                                        <div class="form-check p-3 border rounded-3 d-flex align-items-center">
                                                            <input class="form-check-input me-3" type="radio" name="payment_method" id="payMethodQris-<?= $inv['id'] ?>" value="qris">
                                                            <label class="form-check-label flex-grow-1" for="payMethodQris-<?= $inv['id'] ?>">
                                                                <strong class="d-block text-dark">QRIS Pembayaran Digital</strong>
                                                                <small class="text-muted">Scan QR dari m-Banking atau e-Wallet</small>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-kosaja-primary rounded-pill px-4">
                                                        <i class="bi bi-shield-lock-fill me-1"></i> Konfirmasi & Bayar Lunas
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-check2-circle text-teal display-4 mb-2"></i>
                    <h6 class="fw-bold text-dark mt-2">Semua Tagihan Anda Lunas!</h6>
                    <p class="small text-muted mb-0">Tidak ada tagihan tertunda atau tunggakan sewa yang harus dibayarkan saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Active Rentals & Contracts -->
    <div class="row g-4 mb-4">
        <!-- Kontrak Aktif -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-building-check text-teal me-2"></i> Hunian & Kontrak Aktif Saya
                    </h5>
                    <a href="<?= base_url('/contracts') ?>" class="text-teal text-decoration-none small fw-semibold">
                        Semua Kontrak <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($activeContracts)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($activeContracts as $c): ?>
                                <div class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark"><?= esc($c['property_name']) ?></h6>
                                            <span class="text-muted small">Kamar: <strong><?= esc($c['unit_name']) ?></strong> • <?= esc($c['unit_size'] ?? '-') ?></span>
                                        </div>
                                        <span class="badge bg-success rounded-pill px-3 py-1">KONTRAK AKTIF</span>
                                    </div>
                                    <div class="p-2 bg-light rounded-3 mb-2 small text-secondary">
                                        <div class="d-flex justify-content-between mb-1">
                                            <span>Masa Sewa:</span>
                                            <strong class="text-dark"><?= date('d M Y', strtotime($c['start_date'])) ?> s/d <?= date('d M Y', strtotime($c['end_date'])) ?></strong>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>Sewa Bulanan:</span>
                                            <strong class="text-dark">Rp <?= number_format($c['agreed_price'], 0, ',', '.') ?> / bln</strong>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small">
                                            <i class="bi bi-person-badge me-1"></i> Pemilik: <?= esc($c['provider_name']) ?>
                                        </span>
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#disputeModal-<?= $c['id'] ?>">
                                                <i class="bi bi-flag me-1"></i> Lapor Komplain
                                            </button>
                                            <a href="<?= base_url('/contracts/' . $c['id']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                                <i class="bi bi-file-text me-1"></i> Detail Kontrak
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dispute Modal -->
                                <div class="modal fade" id="disputeModal-<?= $c['id'] ?>" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow">
                                            <div class="modal-header border-0 pb-0">
                                                <h5 class="modal-title fw-bold text-danger">
                                                    <i class="bi bi-shield-exclamation me-1"></i> Ajukan Dispute / Komplain
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="<?= base_url('/tenant/disputes/create') ?>" method="POST">
                                                <?= csrf_field() ?>
                                                <input type="hidden" name="contract_id" value="<?= $c['id'] ?>">
                                                <div class="modal-body py-3">
                                                    <p class="text-muted small mb-3">
                                                        Laporan komplain Anda akan langsung ditinjau secara adil oleh Admin Backoffice KosAja.
                                                    </p>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Kamar & Properti:</label>
                                                        <input type="text" class="form-control form-control-sm bg-light" value="<?= esc($c['property_name']) ?> - <?= esc($c['unit_name']) ?>" readonly>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Judul Masalah / Komplain:</label>
                                                        <input type="text" name="title" class="form-control" placeholder="Contoh: Fasilitas AC Rusak / Air Tidak Mengalir" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label small fw-bold">Penjelasan Lengkap Permasalahan:</label>
                                                        <textarea name="reason" class="form-control" rows="4" placeholder="Jelaskan detail kendala yang Anda alami dan solusi yang diharapkan (misal: perbaikan atau penyesuaian biaya)..." required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 pt-0">
                                                    <button type="button" class="btn btn-light rounded-pill px-3" data-bs-dismiss="modal">Tutup</button>
                                                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                                                        Kirim Laporan Komplain
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-house-dash display-4 opacity-50 mb-2"></i>
                            <p class="small text-muted mb-0">Belum ada kontrak hunian yang aktif saat ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Dispute & Komplain History -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-shield-check text-teal me-2"></i> Status Komplain & Dispute
                    </h5>
                    <span class="badge bg-light text-muted border rounded-pill px-2"><?= count($disputes) ?> Kasus</span>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($disputes)): ?>
                        <div class="list-group list-group-flush">
                            <?php foreach ($disputes as $d): ?>
                                <div class="list-group-item p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-dark small">#DSP-<?= $d['id'] ?>: <?= esc($d['title']) ?></strong>
                                        <span class="badge <?= $d['status'] === 'resolved' ? 'bg-success' : ($d['status'] === 'investigating' ? 'bg-primary' : 'bg-warning text-dark') ?> rounded-pill small">
                                            <?= strtoupper($d['status']) ?>
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-2 text-truncate"><?= esc($d['reason']) ?></p>
                                    <?php if ($d['decision'] !== 'none'): ?>
                                        <div class="p-2 bg-light rounded small border">
                                            <span class="text-muted">Keputusan Admin:</span>
                                            <strong class="text-teal d-block"><?= strtoupper(str_replace('_', ' ', $d['decision'])) ?></strong>
                                            <?php if (!empty($d['decision_notes'])): ?>
                                                <small class="text-muted d-block mt-1">"<?= esc($d['decision_notes']) ?>"</small>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-shield-heart display-4 text-teal opacity-50 mb-2"></i>
                            <p class="small text-muted mb-0">Tidak ada dispute atau masalah aktif. Semua sewa berjalan lancar!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Pembayaran Tagihan Lunas -->
    <?php if (!empty($paidInvoices)): ?>
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-journal-check text-success me-2"></i> Riwayat Tagihan Lunas
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-sm">
                        <thead class="bg-light text-muted small">
                            <tr>
                                <th class="ps-4">No. Invoice</th>
                                <th>Hunian</th>
                                <th>Periode / Jatuh Tempo</th>
                                <th>Total Dibayar</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paidInvoices as $pi): ?>
                                <tr>
                                    <td class="ps-4 fw-bold">#INV-<?= $pi['id'] ?></td>
                                    <td><?= esc($pi['property_name']) ?> (<?= esc($pi['unit_name']) ?>)</td>
                                    <td><?= date('d M Y', strtotime($pi['due_date'])) ?></td>
                                    <td class="fw-bold text-success">Rp <?= number_format($pi['total_amount'], 0, ',', '.') ?></td>
                                    <td><span class="badge bg-success rounded-pill px-3 py-1">LUNAS</span></td>
                                    <td class="text-end pe-4">
                                        <a href="<?= base_url('/contracts/' . $pi['contract_id']) ?>" class="btn btn-outline-secondary btn-xs rounded-pill">
                                            Lihat Kontrak
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
