<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-scale-balanced text-teal mr-2"></i> Tinjau Kasus Dispute #DSP-<?= $dispute['id'] ?>
            </h1>
            <p class="text-muted small mb-0">Periksa detail aduan, bukti, kontrak terkait, dan tetapkan keputusan resolusi yang mengikat.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/admin/disputes') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3">
        <i class="fas fa-check-circle mr-2"></i> <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3">
        <i class="fas fa-exclamation-triangle mr-2"></i> <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<div class="row">
    <!-- Dispute Information -->
    <div class="col-lg-7">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-circle-info text-teal mr-2"></i> Rincian Keluhan & Pelapor
                </h5>
                <span class="badge <?= $dispute['status'] === 'resolved' ? 'badge-success' : ($dispute['status'] === 'investigating' ? 'badge-primary' : 'badge-warning text-dark') ?> text-uppercase px-2 py-1">
                    <?= $dispute['status'] ?>
                </span>
            </div>
            <div class="card-body">
                <h4 class="font-weight-bold text-dark mb-2"><?= esc($dispute['title']) ?></h4>
                <div class="text-muted small mb-3">
                    <i class="fas fa-calendar mr-1"></i> Diajukan: <?= date('d M Y H:i', strtotime($dispute['created_at'])) ?>
                </div>

                <div class="p-3 bg-light rounded mb-4">
                    <label class="text-muted text-uppercase font-weight-bold text-xs d-block mb-1">Deskripsi Permasalahan oleh Penyewa:</label>
                    <p class="mb-0 text-dark" style="white-space: pre-line;"><?= esc($dispute['reason']) ?></p>
                </div>

                <div class="row g-3">
                    <div class="col-sm-6 mb-3">
                        <div class="border rounded p-3 h-100">
                            <span class="text-muted text-xs font-weight-bold text-uppercase d-block mb-1">
                                <i class="fas fa-user mr-1 text-primary"></i> Pelapor (Penyewa)
                            </span>
                            <strong class="d-block text-dark"><?= esc($dispute['raised_by_name']) ?></strong>
                            <small class="text-muted d-block"><?= esc($dispute['raised_by_email']) ?></small>
                            <small class="text-muted d-block"><i class="fab fa-whatsapp text-success mr-1"></i> <?= esc($dispute['raised_by_whatsapp']) ?></small>
                        </div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <div class="border rounded p-3 h-100">
                            <span class="text-muted text-xs font-weight-bold text-uppercase d-block mb-1">
                                <i class="fas fa-store mr-1 text-teal"></i> Terlapor (Mitra Pemilik)
                            </span>
                            <strong class="d-block text-dark"><?= esc($dispute['against_user_name']) ?></strong>
                            <small class="text-muted d-block"><?= esc($dispute['against_user_email']) ?></small>
                            <small class="text-muted d-block"><i class="fab fa-whatsapp text-success mr-1"></i> <?= esc($dispute['against_user_whatsapp']) ?></small>
                        </div>
                    </div>
                </div>

                <div class="border rounded p-3">
                    <span class="text-muted text-xs font-weight-bold text-uppercase d-block mb-1">
                        <i class="fas fa-file-contract mr-1 text-secondary"></i> Kontrak Terkait (#CTR-<?= $dispute['contract_id'] ?>)
                    </span>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Hunian:</span>
                        <strong><?= esc($dispute['property_name']) ?> — Kamar <?= esc($dispute['unit_name']) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Periode Sewa:</span>
                        <span><?= date('d M Y', strtotime($dispute['start_date'])) ?> s/d <?= date('d M Y', strtotime($dispute['end_date'])) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Sewa Bulanan:</span>
                        <strong class="text-teal">Rp <?= number_format($dispute['agreed_price'], 0, ',', '.') ?> / bln</strong>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($refund)): ?>
            <div class="card shadow-sm border-0 border-left border-success mb-4" style="border-left: 5px solid #28a745 !important;">
                <div class="card-body">
                    <h5 class="font-weight-bold text-success mb-2">
                        <i class="fas fa-check-circle mr-1"></i> Pengembalian Dana Telah Diproses (Refund)
                    </h5>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Nominal Refund:</span>
                        <strong class="text-dark">Rp <?= number_format($refund['amount'], 0, ',', '.') ?> (<?= strtoupper($refund['refund_type']) ?>)</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Tanggal Proses:</span>
                        <span><?= date('d M Y H:i', strtotime($refund['processed_at'])) ?></span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Status Refund:</span>
                        <span class="badge badge-success text-uppercase"><?= $refund['status'] ?></span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Actions & Decisions Card -->
    <div class="col-lg-5">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title font-weight-bold mb-0 text-dark">
                    <i class="fas fa-gavel text-teal mr-2"></i> Tindakan & Putusan Backoffice
                </h5>
            </div>
            <div class="card-body">
                <?php if (! empty($dispute['is_escalated'])): ?>
                    <div class="alert alert-danger border-0 shadow-sm mb-3">
                        <div class="font-weight-bold"><i class="fas fa-triangle-exclamation mr-1"></i> Kasus Dieskalasi ke Super Admin</div>
                        <small class="d-block mt-1"><?= esc($dispute['escalation_notes']) ?></small>
                        <small class="text-white-50 d-block mt-1">Dieskalasi pada: <?= date('d M Y H:i', strtotime($dispute['escalated_at'])) ?></small>
                    </div>
                <?php endif; ?>

                <?php if ($dispute['status'] === 'opened'): ?>
                    <div class="alert alert-warning border-0 small mb-3">
                        <i class="fas fa-info-circle mr-1"></i> Dispute ini baru diajukan dan belum diinvestigasi. Ubah status untuk memulai mediasi.
                    </div>
                    <form action="<?= base_url('/admin/disputes/investigate/' . $dispute['id']) ?>" method="POST" class="mb-3">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-primary btn-block font-weight-bold">
                            <i class="fas fa-magnifying-glass mr-1"></i> Mulai Proses Investigasi
                        </button>
                    </form>
                <?php endif; ?>

                <?php if (session()->get('role') === 'admin_daerah' && empty($dispute['is_escalated']) && $dispute['status'] !== 'resolved'): ?>
                    <div class="card bg-light border-warning border mb-3">
                        <div class="card-body p-3">
                            <h6 class="font-weight-bold text-dark mb-1"><i class="fas fa-arrow-up text-warning mr-1"></i> Eskalasi ke Super Admin (PRD 5.3)</h6>
                            <p class="small text-muted mb-2">Jika kasus kompleks atau memerlukan wewenang platform pusat:</p>
                            <form action="<?= base_url('/admin/disputes/escalate/' . $dispute['id']) ?>" method="POST">
                                <?= csrf_field() ?>
                                <textarea name="escalation_notes" class="form-control form-control-sm mb-2" rows="2" placeholder="Alasan eskalasi ke pusat..." required></textarea>
                                <button type="submit" class="btn btn-sm btn-outline-danger btn-block font-weight-bold">
                                    <i class="fas fa-paper-plane mr-1"></i> Eskalasi ke Super Admin
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($dispute['status'] !== 'resolved'): ?>
                    <form action="<?= base_url('/admin/disputes/decide/' . $dispute['id']) ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-sm">Pilih Keputusan Final Resolusi:</label>
                            <select name="decision" class="form-control" id="decisionSelect" required>
                                <option value="">-- Pilih Putusan --</option>
                                <option value="no_refund">NO REFUND (Keluhan Ditolak / Diselesaikan Tanpa Dana)</option>
                                <option value="partial_refund">PARTIAL REFUND (Pengembalian Sebagian Biaya)</option>
                                <option value="full_refund">FULL REFUND (Pengembalian Dana Penuh)</option>
                            </select>
                        </div>

                        <div class="form-group mb-3" id="refundSection" style="display: none;">
                            <label class="font-weight-bold text-sm">Nominal Pengembalian Dana (Rp):</label>
                            <input type="number" name="refund_amount" id="refundAmount" class="form-control" placeholder="Contoh: 500000" min="0" max="<?= $dispute['agreed_price'] ?>" value="<?= $dispute['agreed_price'] ?>">
                            <small class="text-muted">Maksimal: Rp <?= number_format($dispute['agreed_price'], 0, ',', '.') ?> (Sesuai tarif termin sewa).</small>
                        </div>

                        <div class="form-group mb-3">
                            <label class="font-weight-bold text-sm">Catatan Pertimbangan / Alasan Keputusan:</label>
                            <textarea name="decision_notes" class="form-control" rows="4" placeholder="Jelaskan dasar hukum/kebijakan pengambilan keputusan ini..." required></textarea>
                            <small class="text-muted">Catatan ini akan dicatat ke Immutable Audit Trail dan dikirim via WhatsApp ke penyewa.</small>
                        </div>

                        <button type="submit" class="btn btn-teal text-white btn-block font-weight-bold" onclick="return confirm('Apakah Anda yakin ingin menetapkan keputusan final ini? Tindakan ini akan dicatat secara permanen di Audit Trail.');">
                            <i class="fas fa-stamp mr-1"></i> Tetapkan Keputusan & Eksekusi
                        </button>
                    </form>
                <?php else: ?>
                    <div class="p-3 bg-light rounded border">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Status:</span>
                            <span class="badge badge-success">RESOLVED</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Keputusan:</span>
                            <strong class="text-teal"><?= strtoupper(str_replace('_', ' ', $dispute['decision'])) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Ditetapkan Oleh:</span>
                            <span><?= esc($dispute['decided_by_name'] ?? 'Admin') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <span class="text-muted">Tanggal Putusan:</span>
                            <span><?= date('d M Y H:i', strtotime($dispute['decided_at'])) ?></span>
                        </div>
                        <label class="text-muted text-xs font-weight-bold text-uppercase d-block mb-1">Catatan Putusan:</label>
                        <p class="mb-0 text-dark small bg-white p-2 rounded border">"<?= esc($dispute['decision_notes']) ?>"</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const decisionSelect = document.getElementById('decisionSelect');
    const refundSection  = document.getElementById('refundSection');
    
    if (decisionSelect) {
        decisionSelect.addEventListener('change', function() {
            if (this.value === 'partial_refund' || this.value === 'full_refund') {
                refundSection.style.display = 'block';
            } else {
                refundSection.style.display = 'none';
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
