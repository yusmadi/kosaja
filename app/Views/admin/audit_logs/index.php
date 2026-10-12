<?= $this->extend('layouts/adminlte') ?>

<?= $this->section('content') ?>
<div class="content-header p-0 mb-3">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold h3 text-dark">
                <i class="fas fa-clock-rotate-left text-teal mr-2"></i> Immutable Audit Trail (System Logs)
            </h1>
            <p class="text-muted small mb-0">Catatan kronologis permanen setiap mutasi finansial, pergantian status sewa, persetujuan listing, dan resolusi dispute.</p>
        </div>
        <div class="col-sm-6 text-sm-right mt-2 mt-sm-0">
            <a href="<?= base_url('/admin/dashboard') ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left mr-1"></i> Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body p-3">
        <form method="GET" action="<?= base_url('/admin/audit') ?>" class="row g-2 align-items-center">
            <div class="col-md-4">
                <label class="small text-muted font-weight-bold mb-1">Filter Aksi Sistem (Action):</label>
                <select name="action" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Jenis Aksi --</option>
                    <?php foreach ($actionList as $act): ?>
                        <option value="<?= esc($act) ?>" <?= ($selectedAction === $act) ? 'selected' : '' ?>><?= esc($act) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="small text-muted font-weight-bold mb-1">Objek Domain (Table):</label>
                <select name="object_type" class="form-control form-control-sm" onchange="this.form.submit()">
                    <option value="">-- Semua Objek --</option>
                    <option value="contracts" <?= ($selectedObjectType === 'contracts') ? 'selected' : '' ?>>contracts</option>
                    <option value="invoices" <?= ($selectedObjectType === 'invoices') ? 'selected' : '' ?>>invoices</option>
                    <option value="payments" <?= ($selectedObjectType === 'payments') ? 'selected' : '' ?>>payments</option>
                    <option value="disputes" <?= ($selectedObjectType === 'disputes') ? 'selected' : '' ?>>disputes</option>
                    <option value="refunds" <?= ($selectedObjectType === 'refunds') ? 'selected' : '' ?>>refunds</option>
                    <option value="listings" <?= ($selectedObjectType === 'listings') ? 'selected' : '' ?>>listings</option>
                    <option value="rental_applications" <?= ($selectedObjectType === 'rental_applications') ? 'selected' : '' ?>>rental_applications</option>
                </select>
            </div>
            <div class="col-md-2 mt-auto">
                <a href="<?= base_url('/admin/audit') ?>" class="btn btn-sm btn-light border btn-block">
                    <i class="fas fa-rotate-left mr-1"></i> Reset Filter
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Audit Logs Table Card -->
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <?php if (!empty($logs)): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle m-0 text-sm">
                    <thead class="bg-light">
                        <tr>
                            <th class="pl-3" style="width: 175px;">Waktu (WIB) & ID</th>
                            <th>Aktor (User)</th>
                            <th>Action / Aksi</th>
                            <th>Target Objek & Wilayah</th>
                            <th>Keterangan / Alasan</th>
                            <th class="text-right pr-3">Mutasi Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log): ?>
                            <tr>
                                <td class="pl-3">
                                    <strong class="d-block text-dark">#LOG-<?= $log['id'] ?></strong>
                                    <small class="text-muted"><i class="fas fa-clock mr-1 text-teal"></i> <?= \App\Models\AuditLogModel::formatJakartaTime($log['created_at'], 'd M Y H:i:s') ?></small>
                                </td>
                                <td>
                                    <?php if (!empty($log['actor_name'])): ?>
                                        <strong class="d-block text-dark"><?= esc($log['actor_name']) ?></strong>
                                        <span class="badge badge-light border text-uppercase" style="font-size: 0.65rem;"><?= esc($log['actor_role'] ?? 'user') ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary"><i class="fas fa-robot mr-1"></i> SYSTEM / CRON</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $actionClass = match($log['action']) {
                                        'PAYMENT_SUCCESS', 'INVOICE_PAYMENT_SUCCESS', 'CREATE_CONTRACT', 'APPROVE_LISTING', 'VERIFY_PROVIDER' => 'badge-success',
                                        'INVOICE_OVERDUE', 'PAYMENT_FAILED', 'REFUND_PROCESSED', 'REJECT_LISTING', 'REJECT_PROVIDER', 'SUSPEND_PROVIDER' => 'badge-danger',
                                        'DISPUTE_OPENED', 'DISPUTE_INVESTIGATING' => 'badge-warning text-dark',
                                        'DISPUTE_DECIDED' => 'badge-teal text-white',
                                        'USER_LOGIN', 'USER_REGISTER' => 'badge-primary',
                                        default => 'badge-info',
                                    };
                                    ?>
                                    <span class="badge <?= $actionClass ?> font-weight-bold">
                                        <?= esc($log['action']) ?>
                                    </span>
                                </td>
                                <td>
                                    <code><?= esc($log['object_type']) ?> #<?= $log['object_id'] ?></code>
                                    <?php if (! empty($log['region_name'])): ?>
                                        <div class="small text-muted mt-1">
                                            <i class="fas fa-map-marker-alt text-teal mr-1"></i> <?= esc($log['region_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="small text-muted"><?= esc($log['reason'] ?? '-') ?></span>
                                </td>
                                <td class="text-right pr-3">
                                    <?php if (!empty($log['previous_state']) || !empty($log['new_state'])): ?>
                                        <button type="button" class="btn btn-xs btn-outline-secondary" data-toggle="modal" data-target="#payloadModal-<?= $log['id'] ?>">
                                            <i class="fas fa-code mr-1"></i> Lihat JSON
                                        </button>

                                        <!-- Payload Modal -->
                                        <div class="modal fade text-left" id="payloadModal-<?= $log['id'] ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title font-weight-bold">
                                                            Mutasi State Objek #LOG-<?= $log['id'] ?> (<?= esc($log['action']) ?>)
                                                        </h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-xs">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <label class="font-weight-bold text-muted text-uppercase">Previous State (Sebelumnya):</label>
                                                                <pre class="bg-light p-2 rounded border" style="max-height: 250px; overflow-y: auto;"><code><?= esc(json_encode(json_decode($log['previous_state'] ?? '{}'), JSON_PRETTY_PRINT)) ?></code></pre>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="font-weight-bold text-muted text-uppercase text-teal">New State (Setelahnya):</label>
                                                                <pre class="bg-light p-2 rounded border" style="max-height: 250px; overflow-y: auto;"><code><?= esc(json_encode(json_decode($log['new_state'] ?? '{}'), JSON_PRETTY_PRINT)) ?></code></pre>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-clipboard-list fa-3x mb-3 text-secondary opacity-50"></i>
                <h5 class="font-weight-bold text-dark">Tidak Ada Log Audit</h5>
                <p class="small text-muted mb-0">Belum ada aktivitas yang tercatat untuk filter pencarian ini.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>
