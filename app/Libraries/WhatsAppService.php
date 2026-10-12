<?php

namespace App\Libraries;

use Config\WhatsAppConfig;
use App\Models\NotificationLogModel;

class WhatsAppService
{
    protected WhatsAppConfig $config;
    protected NotificationLogModel $logModel;

    public function __construct()
    {
        $this->config   = config('WhatsAppConfig') ?? new WhatsAppConfig();
        $this->logModel = new NotificationLogModel();
    }

    /**
     * Send or simulate a WhatsApp message
     *
     * @param string $phone
     * @param string $message
     * @param string|null $recipientName
     * @param string|null $recipientRole
     * @param string $eventType
     * @return array
     */
    public function send(
        string $phone,
        string $message,
        ?string $recipientName = null,
        ?string $recipientRole = null,
        string $eventType = 'general'
    ): array {
        $normalizedPhone = $this->normalizePhoneNumber($phone);
        $driver = $this->config->driver;
        $status = 'simulated';

        // Add brand header
        $fullMessage = "*KosAja Notification*\n\n" . trim($message) . "\n\n_Pesan otomatis sistem KosAja. Harap tidak membalas langsung ke nomor ini._";

        if ($driver === 'twilio' && !empty($this->config->twilioSid) && !empty($this->config->twilioToken)) {
            $twilioResult = $this->sendViaTwilio($normalizedPhone, $fullMessage);
            $status = $twilioResult['success'] ? 'sent' : 'failed';
        } elseif ($driver === 'meta' && !empty($this->config->metaAccessToken) && !empty($this->config->metaPhoneNumberId)) {
            $metaResult = $this->sendViaMeta($normalizedPhone, $fullMessage);
            $status = $metaResult['success'] ? 'sent' : 'failed';
        } else {
            // Simulated local driver
            $status = 'simulated';
        }

        // Record to notification log table
        $logId = $this->logModel->record(
            $normalizedPhone,
            $recipientName,
            $recipientRole,
            $eventType,
            $fullMessage,
            $status,
            'whatsapp'
        );

        return [
            'success'   => ($status !== 'failed'),
            'status'    => $status,
            'log_id'    => $logId,
            'wa_url'    => 'https://wa.me/' . $normalizedPhone . '?text=' . urlencode($fullMessage),
            'message'   => $fullMessage,
            'recipient' => $normalizedPhone,
        ];
    }

    /**
     * Send Notification when Application is Approved
     */
    public function sendApplicationApprovedNotification(array $application, array $reservation): array
    {
        $expiresAtStr = date('d M Y, H:i', strtotime($reservation['expires_at']));
        $checkoutUrl  = base_url('/checkout/' . $reservation['id']);

        $msg = "Halo *{$application['tenant_name']}*,\n\n"
             . "Kabar gembira! Pengajuan sewa Anda untuk kamar *{$application['unit_name']}* di *{$application['property_name']}* telah *DISETUJUI* oleh pemilik.\n\n"
             . "🔒 Unit kamar saat ini telah dikunci khusus untuk Anda (*RESERVED*).\n"
             . "⏰ Batas Waktu Pembayaran: *{$expiresAtStr} WIB*.\n\n"
             . "Silakan selesaikan proses checkout dan pembayaran sewa melalui tautan berikut:\n"
             . "👉 {$checkoutUrl}\n\n"
             . "Terima kasih telah memilih hunian di KosAja!";

        return $this->send(
            $application['tenant_whatsapp'] ?? '',
            $msg,
            $application['tenant_name'],
            'tenant',
            'application_approved'
        );
    }

    /**
     * Send Notification when Application is Rejected
     */
    public function sendApplicationRejectedNotification(array $application, string $reason): array
    {
        $searchUrl = base_url('/search');

        $msg = "Halo *{$application['tenant_name']}*,\n\n"
             . "Terima kasih telah mengajukan sewa untuk kamar *{$application['unit_name']}* di *{$application['property_name']}*.\n\n"
             . "Mohon maaf, permohonan Anda saat ini *BELUM DAPAT DITERIMA* oleh pemilik hunian dengan alasan:\n"
             . "💬 \"_{$reason}_\"\n\n"
             . "Anda dapat mencari alternatif hunian lain yang siap huni di KosAja:\n"
             . "👉 {$searchUrl}\n\n"
             . "Semoga lekas menemukan hunian yang cocok!";

        return $this->send(
            $application['tenant_whatsapp'] ?? '',
            $msg,
            $application['tenant_name'],
            'tenant',
            'application_rejected'
        );
    }

    /**
     * Send Notification when Payment Succeeded and Contract is Created
     */
    public function sendPaymentSuccessNotifications(array $contract, array $invoice, array $payment): array
    {
        $totalPaidFormatted = 'Rp ' . number_format($payment['amount_paid'], 0, ',', '.');
        $contractUrl        = base_url('/contracts/' . $contract['id']);

        // 1. To Tenant
        $tenantMsg = "Alhamdulillah! Pembayaran sewa Anda sebesar *{$totalPaidFormatted}* telah *BERHASIL DITERIMA*.\n\n"
                   . "📄 Kontrak Sewa Resmi *#CTR-{$contract['id']}* kini telah *AKTIF*.\n"
                   . "🏠 Properti: *{$contract['property_name']}* — Kamar *{$contract['unit_name']}*\n"
                   . "📅 Periode Sewa: *" . date('d M Y', strtotime($contract['start_date'])) . " s/d " . date('d M Y', strtotime($contract['end_date'])) . "*\n\n"
                   . "Surat perjanjian sewa digital Anda dapat diakses dan diunduh di:\n"
                   . "👉 {$contractUrl}\n\n"
                   . "Selamat menempati hunian baru Anda bersama KosAja!";

        $tenantResult = $this->send(
            $contract['tenant_whatsapp'] ?? '',
            $tenantMsg,
            $contract['tenant_name'],
            'tenant',
            'payment_success'
        );

        // 2. To Provider
        $rentalFormatted = 'Rp ' . number_format($invoice['rental_amount'], 0, ',', '.');
        $providerMsg = "Halo *{$contract['provider_name']}*,\n\n"
                     . "Penyewa baru atas nama *{$contract['tenant_name']}* telah berhasil menyelesaikan pembayaran sewa pertama sebesar *{$rentalFormatted}* untuk kamar *{$contract['unit_name']}* di *{$contract['property_name']}*.\n\n"
                     . "✅ Status Unit: *TERISI (OCCUPIED / ACTIVE RENTAL)*\n"
                     . "📄 Kontrak Aktif: *#CTR-{$contract['id']}*\n\n"
                     . "Silakan pantau rincian kontrak dan jadwal serah terima kunci di Dasbor KosAja:\n"
                     . "👉 {$contractUrl}";

        $providerResult = $this->send(
            $contract['provider_whatsapp'] ?? '',
            $providerMsg,
            $contract['provider_name'],
            'provider',
            'tenant_rental_active'
        );

        return [
            'tenant_notification'   => $tenantResult,
            'provider_notification' => $providerResult,
        ];
    }

    /**
     * Send WhatsApp Reminder H-3 Before Invoice Due Date
     */
    public function sendInvoiceReminderH3Notification(array $invoice): array
    {
        $dueDateFormatted = date('d M Y', strtotime($invoice['due_date']));
        $totalFormatted   = 'Rp ' . number_format($invoice['total_amount'], 0, ',', '.');
        $payUrl           = base_url('/tenant/dashboard#invoices');

        $msg = "Halo *{$invoice['tenant_name']}*,\n\n"
             . "Pengingat dari KosAja: Tagihan sewa kamar Anda untuk *{$invoice['property_name']}* (Kamar *{$invoice['unit_name']}*) akan *JATUH TEMPO DALAM 3 HARI (H-3)* pada tanggal *{$dueDateFormatted}*.\n\n"
             . "💰 Jumlah Tagihan: *{$totalFormatted}*\n"
             . "📅 Batas Waktu: *{$dueDateFormatted}*\n\n"
             . "Untuk kenyamanan tinggal Anda, silakan lakukan pembayaran tagihan tepat waktu melalui tautan dasbor berikut:\n"
             . "👉 {$payUrl}\n\n"
             . "Terima kasih atas kerja sama Anda!";

        return $this->send(
            $invoice['tenant_whatsapp'] ?? '',
            $msg,
            $invoice['tenant_name'],
            'tenant',
            'invoice_reminder_h3'
        );
    }

    /**
     * Send WhatsApp Warning during 3-day Grace Period
     */
    public function sendInvoiceGracePeriodNotification(array $invoice, int $daysRemainingInGrace = 2): array
    {
        $dueDateFormatted = date('d M Y', strtotime($invoice['due_date']));
        $totalFormatted   = 'Rp ' . number_format($invoice['total_amount'], 0, ',', '.');
        $payUrl           = base_url('/tenant/dashboard#invoices');

        $msg = "⚠️ *Pemberitahuan Grace Period Tagihan KosAja*\n\n"
             . "Halo *{$invoice['tenant_name']}*,\n\n"
             . "Tagihan sewa Anda sebesar *{$totalFormatted}* telah melewati tanggal jatuh tempo ({$dueDateFormatted}).\n"
             . "Saat ini Anda berada dalam *Masa Tenggang (Grace Period 3 Hari)* dengan sisa waktu *{$daysRemainingInGrace} hari* sebelum tagihan berstatus OVERDUE.\n\n"
             . "Segera selesaikan pembayaran agar hunian tetap nyaman:\n"
             . "👉 {$payUrl}";

        return $this->send(
            $invoice['tenant_whatsapp'] ?? '',
            $msg,
            $invoice['tenant_name'],
            'tenant',
            'invoice_grace_period_warning'
        );
    }

    /**
     * Send WhatsApp Alert when Invoice is Overdue (after 3-day grace period)
     */
    public function sendInvoiceOverdueNotification(array $invoice): array
    {
        $dueDateFormatted = date('d M Y', strtotime($invoice['due_date']));
        $totalFormatted   = 'Rp ' . number_format($invoice['total_amount'], 0, ',', '.');
        $payUrl           = base_url('/tenant/dashboard#invoices');

        $msg = "🚨 *PEMBERITAHUAN TAGIHAN OVERDUE*\n\n"
             . "Halo *{$invoice['tenant_name']}*,\n\n"
             . "Masa tenggang (Grace Period 3 hari) untuk tagihan sewa kamar *{$invoice['unit_name']}* di *{$invoice['property_name']}* telah berakhir.\n\n"
             . "Status tagihan Anda kini resmi menjadi *OVERDUE (TUNGGAKAN)*.\n"
             . "Nominal: *{$totalFormatted}*\n"
             . "Jatuh Tempo Semula: *{$dueDateFormatted}*\n\n"
             . "Mohon segera lakukan pelunasan hari ini guna menghindari penghentian sewa sepihak oleh pengelola:\n"
             . "👉 {$payUrl}";

        return $this->send(
            $invoice['tenant_whatsapp'] ?? '',
            $msg,
            $invoice['tenant_name'],
            'tenant',
            'invoice_overdue'
        );
    }

    /**
     * Send WhatsApp Notification for Dispute Resolution Decision & Refund
     */
    public function sendDisputeDecisionNotification(array $dispute, ?array $refund = null): array
    {
        $decisionText = match($dispute['decision']) {
            'full_refund'    => 'Pengembalian Dana Penuh (Full Refund)',
            'partial_refund' => 'Pengembalian Dana Sebagian (Partial Refund)',
            'no_refund'      => 'Dispute Ditolak Tanpa Refund (No Refund)',
            default          => 'Penyelesaian Tanpa Dana'
        };

        $msg = "⚖️ *Keputusan Dispute & Komplain KosAja*\n\n"
             . "Halo *{$dispute['raised_by_name']}*,\n\n"
             . "Laporan dispute Anda terkait kontrak *#CTR-{$dispute['contract_id']}* ({$dispute['property_name']} - {$dispute['unit_name']}) telah ditinjau dan diputuskan oleh Administrator Backoffice:\n\n"
             . "📋 *Status:* RESOLVED\n"
             . "🏛️ *Keputusan:* *{$decisionText}*\n"
             . "💬 *Catatan Admin:* \"_{$dispute['decision_notes']}_\"\n";

        if ($refund && !empty($refund['amount'])) {
            $refundAmountFormatted = 'Rp ' . number_format($refund['amount'], 0, ',', '.');
            $msg .= "\n💵 *Nominal Refund:* *{$refundAmountFormatted}*\n"
                  . "Dana telah diproses kembali ke metode pembayaran Anda.\n";
        }

        $msg .= "\nTerima kasih telah mempercayakan kenyamanan dan keadilan sewa di KosAja.";

        return $this->send(
            $dispute['raised_by_whatsapp'] ?? '',
            $msg,
            $dispute['raised_by_name'],
            'tenant',
            'dispute_resolved'
        );
    }


    /**
     * Helper to normalize Indonesian telephone numbers to international format (62...)
     */
    protected function normalizePhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (!str_starts_with($cleaned, '62') && !empty($cleaned)) {
            $cleaned = '62' . $cleaned;
        }
        return $cleaned;
    }

    /**
     * Dispatch via Twilio REST API
     */
    protected function sendViaTwilio(string $toPhone, string $message): array
    {
        $url = "https://api.twilio.com/2010-04-01/Accounts/{$this->config->twilioSid}/Messages.json";
        $data = [
            'From' => $this->config->twilioFromNumber,
            'To'   => 'whatsapp:+' . $toPhone,
            'Body' => $message,
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, "{$this->config->twilioSid}:{$this->config->twilioToken}");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['success' => ($httpCode >= 200 && $httpCode < 300), 'response' => $response];
    }

    /**
     * Dispatch via Meta WhatsApp Cloud API
     */
    protected function sendViaMeta(string $toPhone, string $message): array
    {
        $url = "https://graph.facebook.com/v20.0/{$this->config->metaPhoneNumberId}/messages";
        $headers = [
            'Authorization: Bearer ' . $this->config->metaAccessToken,
            'Content-Type: application/json',
        ];
        $payload = [
            'messaging_product' => 'whatsapp',
            'to'                => $toPhone,
            'type'              => 'text',
            'text'              => ['body' => $message],
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ['success' => ($httpCode >= 200 && $httpCode < 300), 'response' => $response];
    }
}
