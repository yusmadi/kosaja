<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Kuitansi Pembayaran — KosAja') ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #0d9488;
            --primary-dark: #0f766e;
            --secondary: #64748b;
            --success: #16a34a;
            --warning: #f59e0b;
            --danger: #dc2626;
            --dark: #0f172a;
            --light-bg: #f8fafc;
            --border: #e2e8f0;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            color: #334155;
            padding: 30px 15px;
            font-size: 14px;
            line-height: 1.5;
        }

        .receipt-container {
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            overflow: hidden;
            border: 1px solid var(--border);
        }

        .top-action-bar {
            max-width: 800px;
            margin: 0 auto 15px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: var(--primary);
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .btn-outline {
            background-color: transparent;
            color: #475569;
            border: 1px solid #cbd5e1;
        }

        .btn-outline:hover {
            background-color: #f8fafc;
            color: var(--dark);
        }

        .receipt-header {
            padding: 35px 40px;
            background: linear-gradient(to right, #f8fafc, #ffffff);
            border-bottom: 2px dashed var(--border);
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .brand-title {
            font-size: 24px;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.5px;
        }

        .brand-title span {
            color: #f59e0b;
        }

        .brand-tagline {
            color: var(--secondary);
            font-size: 12px;
            margin-top: 4px;
        }

        .invoice-title-block {
            text-align: right;
        }

        .invoice-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--dark);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .invoice-number {
            font-family: monospace;
            font-size: 14px;
            font-weight: 600;
            color: var(--primary-dark);
            margin-top: 4px;
        }

        .receipt-meta {
            padding: 25px 40px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            background-color: #ffffff;
            border-bottom: 1px solid var(--border);
        }

        .meta-col p {
            margin-bottom: 6px;
            font-size: 13px;
        }

        .meta-label {
            color: var(--secondary);
            width: 140px;
            display: inline-block;
        }

        .meta-value {
            font-weight: 600;
            color: var(--dark);
        }

        .badge-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-paid {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #b45309;
            border: 1px solid #fde68a;
        }

        .receipt-body {
            padding: 30px 40px;
        }

        .table-receipt {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }

        .table-receipt th {
            text-align: left;
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 16px;
            border-top: 1px solid var(--border);
            border-bottom: 1px solid var(--border);
        }

        .table-receipt td {
            padding: 16px;
            border-bottom: 1px solid var(--border);
            vertical-align: top;
        }

        .item-name {
            font-weight: 700;
            color: var(--dark);
            font-size: 15px;
            margin-bottom: 4px;
        }

        .item-desc {
            color: var(--secondary);
            font-size: 12px;
        }

        .total-summary-card {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .summary-box {
            width: 320px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 6px 0;
            font-size: 13px;
            color: #475569;
        }

        .summary-row.total-row {
            border-top: 2px solid var(--border);
            padding-top: 12px;
            margin-top: 8px;
            font-size: 16px;
            font-weight: 800;
            color: var(--primary);
        }

        .receipt-footer {
            padding: 25px 40px;
            background-color: #f8fafc;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stamp-box {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            border: 2px dashed #16a34a;
            border-radius: 8px;
            color: #16a34a;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 1px;
            transform: rotate(-3deg);
        }

        .footer-note {
            font-size: 11px;
            color: #94a3b8;
            max-width: 450px;
            text-align: right;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .top-action-bar {
                display: none;
            }
            .receipt-container {
                box-shadow: none;
                border: none;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="top-action-bar">
        <a href="<?= base_url('/provider/subscription') ?>" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>
        <button onclick="window.print()" class="btn btn-primary">
            <i class="fas fa-print"></i> Cetak / Simpan PDF
        </button>
    </div>

    <div class="receipt-container">
        <!-- HEADER -->
        <div class="receipt-header">
            <div>
                <div class="brand-title">Kos<span>Aja</span></div>
                <div class="brand-tagline">Platform Ekosistem Hunian Kos & Manajemen Properti</div>
                <div style="font-size: 11px; color: #94a3b8; margin-top: 6px;">
                    PT KosAja Solusi Digital Indonesia &bull; support@kosaja.id
                </div>
            </div>
            <div class="invoice-title-block">
                <div class="invoice-title">Kuitansi Pembayaran</div>
                <div class="invoice-number"><?= esc($invoice['invoice_number']) ?></div>
                <div style="margin-top: 6px;">
                    <?php if ($invoice['status'] === 'paid'): ?>
                        <span class="badge-status badge-paid">
                            <i class="fas fa-check-circle"></i> LUNAS
                        </span>
                    <?php elseif ($invoice['status'] === 'pending'): ?>
                        <span class="badge-status badge-pending">
                            <i class="fas fa-clock"></i> MENUNGGU PEMBAYARAN
                        </span>
                    <?php else: ?>
                        <span class="badge-status" style="background: #e2e8f0; color: #475569;">
                            <?= strtoupper(esc($invoice['status'])) ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- METADATA -->
        <div class="receipt-meta">
            <div class="meta-col">
                <p><span class="meta-label">Ditagihkan Kepada:</span> <span class="meta-value"><?= esc($provider['name'] ?? 'Mitra Penyedia Kos') ?></span></p>
                <p><span class="meta-label">Email:</span> <span class="meta-value"><?= esc($provider['email'] ?? '-') ?></span></p>
                <p><span class="meta-label">WhatsApp / Telp:</span> <span class="meta-value"><?= esc($provider['whatsapp'] ?? '-') ?></span></p>
            </div>
            <div class="meta-col">
                <p><span class="meta-label">Order ID:</span> <span class="meta-value" style="font-family: monospace;"><?= esc($invoice['order_id']) ?></span></p>
                <p><span class="meta-label">Tanggal Terbit:</span> <span class="meta-value"><?= date('d F Y, H:i', strtotime($invoice['created_at'])) ?> WIB</span></p>
                <p><span class="meta-label">Jatuh Tempo:</span> <span class="meta-value"><?= date('d F Y', strtotime($invoice['due_date'])) ?></span></p>
                <?php if (!empty($payment['paid_at'])): ?>
                    <p><span class="meta-label">Tanggal Lunas:</span> <span class="meta-value text-success" style="color: #16a34a;"><?= date('d F Y, H:i', strtotime($payment['paid_at'])) ?> WIB</span></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- BODY / TABLE -->
        <div class="receipt-body">
            <table class="table-receipt">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Rincian Layanan</th>
                        <th>Siklus & Durasi</th>
                        <th>Kapasitas Slot</th>
                        <th style="text-align: right;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="width: 40px; color: var(--secondary);">1</td>
                        <td>
                            <div class="item-name"><?= esc($plan['name'] ?? 'Paket Layanan KosAja') ?></div>
                            <div class="item-desc">
                                <?= ($plan['type'] === 'subscription') ? 'Paket Berlangganan Bulanan Mitra Penyedia Kos' : 'Paket Kuota Listing Tambahan Fleksibel 15 Hari' ?>
                            </div>
                        </td>
                        <td>
                            <strong><?= ($plan['billing_cycle'] === 'monthly') ? '30 Hari Kalender' : '15 Hari Kalender' ?></strong>
                        </td>
                        <td>
                            <strong style="color: var(--primary);">
                                +<?= esc($plan['listing_capacity'] >= 999 ? 'Unlimited' : $plan['listing_capacity']) ?> Listing Aktif
                            </strong>
                        </td>
                        <td style="text-align: right; font-weight: 700; color: var(--dark);">
                            Rp <?= number_format($invoice['amount'], 0, ',', '.') ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="total-summary-card">
                <div class="summary-box">
                    <div class="summary-row">
                        <span>Subtotal Biaya Paket:</span>
                        <span>Rp <?= number_format($invoice['amount'], 0, ',', '.') ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Biaya Administrasi / Gateway:</span>
                        <span>Rp 0</span>
                    </div>
                    <div class="summary-row total-row">
                        <span>Total Pembayaran:</span>
                        <span>Rp <?= number_format($invoice['amount'], 0, ',', '.') ?></span>
                    </div>
                    <div class="summary-row" style="margin-top: 6px; font-size: 11px;">
                        <span>Metode Pembayaran:</span>
                        <span style="font-weight: 700; text-transform: uppercase;">
                            <?= esc(str_replace('_', ' ', $invoice['payment_method'] ?? 'Transfer Digital (VA/QRIS)')) ?>
                        </span>
                    </div>
                    <?php if (!empty($payment['gateway_trx_id'])): ?>
                        <div class="summary-row" style="font-size: 11px;">
                            <span>Ref. Transaksi Gateway:</span>
                            <span style="font-family: monospace;"><?= esc($payment['gateway_trx_id']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="receipt-footer">
            <div>
                <?php if ($invoice['status'] === 'paid'): ?>
                    <div class="stamp-box">
                        <i class="fas fa-check-double"></i> LUNAS TERVERIFIKASI
                    </div>
                <?php else: ?>
                    <div style="font-size: 12px; font-weight: 600; color: #b45309;">
                        <i class="fas fa-info-circle mr-1"></i> Menunggu proses pembayaran mitra.
                    </div>
                <?php endif; ?>
            </div>
            <div class="footer-note">
                Kuitansi ini diterbitkan secara otomatis oleh sistem KosAja Billing Engine dan merupakan bukti transaksi sah. Simpan dokumen ini untuk arsip administrasi dan pembukuan Anda.
            </div>
        </div>
    </div>

</body>
</html>
