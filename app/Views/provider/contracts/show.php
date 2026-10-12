<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark font-weight-bold">
                    <i class="fas fa-file-contract text-teal mr-2"></i>Dokumen Kontrak Sewa Penyewa
                </h1>
                <p class="text-muted small mb-0">Nomor Registrasi: <strong>KOS/CTR/<?= date('Y', strtotime($contract['created_at'])) ?>/<?= str_pad($contract['id'], 5, '0', STR_PAD_LEFT) ?></strong></p>
            </div>
            <div class="col-sm-6 text-sm-right">
                <button type="button" class="btn btn-outline-secondary btn-sm mr-1" onclick="window.print()">
                    <i class="fas fa-print mr-1"></i> Cetak / Simpan PDF
                </button>
                <a href="<?= base_url('/provider/contracts') ?>" class="btn btn-outline-teal btn-sm">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar
                </a>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Contract Summary Card -->
        <div class="card card-outline card-success shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <span class="badge badge-success px-3 py-1 font-weight-bold mb-2">
                            <i class="fas fa-check-circle mr-1"></i> SEWA AKTIF BERJALAN
                        </span>
                        <h4 class="font-weight-bold text-dark mb-1">
                            Kamar <?= esc($contract['unit_name']) ?> — <?= esc($contract['property_name']) ?>
                        </h4>
                        <p class="text-secondary small mb-0">
                            Penyewa Aktif: <strong><?= esc($contract['tenant_name']) ?></strong> (<?= $contract['tenant_gender'] === 'L' ? 'Putra' : 'Putri' ?>) • WhatsApp: <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $contract['tenant_whatsapp'] ?? '') ?>" target="_blank" class="text-success font-weight-bold"><i class="fab fa-whatsapp"></i> <?= esc($contract['tenant_whatsapp'] ?? '-') ?></a>
                        </p>
                    </div>
                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                        <span class="text-muted small d-block">Nilai Kontrak Sewa:</span>
                        <strong class="text-teal font-weight-bold h4 mb-0">Rp <?= number_format($contract['agreed_price'], 0, ',', '.') ?></strong>
                        <div class="small text-muted text-capitalize"><?= str_replace('_', ' ', $contract['payment_schedule']) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Official Agreement Clauses Sheet -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white font-weight-bold">
                <i class="fas fa-scroll text-teal mr-2"></i>Klausul Perjanjian Sewa Digital Resmi
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded border h-100">
                            <span class="badge badge-teal text-white mb-2">PIHAK PERTAMA (PEMILIK)</span>
                            <div class="small">
                                <div><strong>Nama:</strong> <?= esc($contract['provider_name']) ?></div>
                                <div><strong>Email:</strong> <?= esc($contract['provider_email']) ?></div>
                                <div><strong>WhatsApp:</strong> <?= esc($contract['provider_whatsapp'] ?? '-') ?></div>
                                <div><strong>Properti:</strong> <?= esc($contract['property_name']) ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3 mt-md-0">
                        <div class="p-3 bg-light rounded border h-100">
                            <span class="badge badge-secondary text-white mb-2">PIHAK KEDUA (PENYEWA)</span>
                            <div class="small">
                                <div><strong>Nama:</strong> <?= esc($contract['tenant_name']) ?></div>
                                <div><strong>Email:</strong> <?= esc($contract['tenant_email']) ?></div>
                                <div><strong>WhatsApp:</strong> <?= esc($contract['tenant_whatsapp'] ?? '-') ?></div>
                                <div><strong>Gender:</strong> <?= $contract['tenant_gender'] === 'L' ? 'Laki-laki (Putra)' : 'Perempuan (Putri)' ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="small text-secondary" style="line-height: 1.8;">
                    <div class="mb-3">
                        <strong class="text-dark">PASAL 1 — OBJEK SEWA:</strong>
                        Unit kamar fisik <strong><?= esc($contract['unit_name']) ?></strong> (Lantai <?= esc($contract['unit_floor'] ?? '1') ?>) di <?= esc($contract['property_name']) ?>, beralamat di <?= esc($contract['property_address']) ?>.
                    </div>
                    <div class="mb-3">
                        <strong class="text-dark">PASAL 2 — JANGKA WAKTU:</strong>
                        Berlaku dari <strong><?= date('d F Y', strtotime($contract['start_date'])) ?></strong> sampai dengan <strong><?= date('d F Y', strtotime($contract['end_date'])) ?></strong>.
                    </div>
                    <div class="mb-3">
                        <strong class="text-dark">PASAL 3 — TATA TERTIB HUNIAN:</strong>
                        Penyewa wajib mematuhi aturan hunian (Kategori: <strong><?= strtoupper(esc($contract['target_gender'])) ?></strong>), tidak membawa tamu lawan jenis bukan mahram ke dalam kamar, serta menjaga ketenteraman dan kebersihan lingkungan bersama.
                    </div>
                </div>

                <!-- Invoice Breakdown -->
                <div class="mt-4 pt-3 border-top">
                    <h6 class="font-weight-bold text-dark mb-2"><i class="fas fa-file-invoice-dollar mr-2 text-teal"></i>Tagihan & Pembayaran Pertama</h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0 small">
                            <thead class="bg-light">
                                <tr>
                                    <th>No. Invoice</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Nominal Sewa</th>
                                    <th>Gateway Fee</th>
                                    <th>Total Bayar</th>
                                    <th>Status Tagihan</th>
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
                                            <td class="font-weight-bold">Rp <?= number_format($inv['total_amount'], 0, ',', '.') ?></td>
                                            <td><span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>LUNAS (PAID)</span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>
