<?php

namespace App\Libraries;

use Config\PaymentConfig;

class PaymentGatewayService
{
    protected PaymentConfig $config;

    public function __construct()
    {
        $this->config = config('PaymentConfig') ?? new PaymentConfig();
    }

    /**
     * Create checkout payment transaction
     *
     * @param array $reservation
     * @param string $paymentMethod
     * @return array
     */
    public function createPaymentTransaction(array $reservation, string $paymentMethod = 'bank_transfer_va'): array
    {
        $orderId = 'KOS-' . $reservation['id'] . '-' . date('ymdHis');
        $rentalAmount = (float) $reservation['rental_amount'];
        $gatewayFee   = (float) ($reservation['gateway_fee'] > 0 ? $reservation['gateway_fee'] : 4500);
        $totalAmount  = $rentalAmount + $gatewayFee;

        // If driver is midtrans and credentials provided
        if ($this->config->driver === 'midtrans' && !empty($this->config->serverKey) && !str_contains($this->config->serverKey, 'demo')) {
            $midtransResult = $this->createMidtransSnap($orderId, $reservation, $rentalAmount, $gatewayFee, $totalAmount);
            if ($midtransResult['success']) {
                return $midtransResult;
            }
        }

        // Mock Sandbox Simulator Driver
        $vaNumber = match($paymentMethod) {
            'bsi_va' => '7123' . str_pad($reservation['id'], 6, '0', STR_PAD_LEFT) . rand(10, 99),
            'bca_va' => '8077' . str_pad($reservation['id'], 6, '0', STR_PAD_LEFT) . rand(10, 99),
            default  => '9988' . str_pad($reservation['id'], 6, '0', STR_PAD_LEFT) . rand(10, 99),
        };

        return [
            'success'        => true,
            'driver'         => 'mock',
            'order_id'       => $orderId,
            'payment_method' => $paymentMethod,
            'rental_amount'  => $rentalAmount,
            'gateway_fee'    => $gatewayFee,
            'gross_amount'   => $totalAmount,
            'va_number'      => $vaNumber,
            'snap_token'     => 'mock-snap-token-' . bin2hex(random_bytes(8)),
            'redirect_url'   => base_url('/checkout/simulation/' . $reservation['id'] . '?order_id=' . $orderId . '&method=' . $paymentMethod),
            'message'        => 'Transaksi pembayaran siap disimulasikan.',
        ];
    }

    /**
     * Parse and validate incoming webhook notification payload
     *
     * @param array $payload
     * @return array [order_id, status (SUCCESS|PENDING|FAILED), amount, payment_method, trx_id]
     */
    public function parseWebhookPayload(array $payload): array
    {
        // Check if Midtrans webhook format
        if (isset($payload['transaction_status'])) {
            $orderId   = $payload['order_id'] ?? '';
            $statusRaw = $payload['transaction_status'];
            $fraud     = $payload['fraud_status'] ?? 'accept';
            $amount    = (float) ($payload['gross_amount'] ?? 0);
            $trxId     = $payload['transaction_id'] ?? ('TRX-' . time());
            $payType   = $payload['payment_type'] ?? 'midtrans';

            $normalizedStatus = 'PENDING';
            if ($statusRaw === 'capture') {
                $normalizedStatus = ($fraud === 'challenge') ? 'PENDING' : 'SUCCESS';
            } elseif ($statusRaw === 'settlement') {
                $normalizedStatus = 'SUCCESS';
            } elseif (in_array($statusRaw, ['cancel', 'deny', 'expire'])) {
                $normalizedStatus = 'FAILED';
            }

            return [
                'success'        => true,
                'order_id'       => $orderId,
                'status'         => $normalizedStatus,
                'amount'         => $amount,
                'payment_method' => $payType,
                'gateway_trx_id' => $trxId,
            ];
        }

        // Generic / Mock Webhook Format
        $orderId = $payload['order_id'] ?? '';
        $status  = strtoupper($payload['status'] ?? 'SUCCESS');
        $amount  = (float) ($payload['amount'] ?? 0);
        $payType = $payload['payment_method'] ?? 'bank_transfer_va';
        $trxId   = $payload['gateway_trx_id'] ?? ('SIM-' . strtoupper(bin2hex(random_bytes(5))));

        return [
            'success'        => true,
            'order_id'       => $orderId,
            'status'         => in_array($status, ['SUCCESS', 'PAID', 'SETTLEMENT']) ? 'SUCCESS' : ($status === 'PENDING' ? 'PENDING' : 'FAILED'),
            'amount'         => $amount,
            'payment_method' => $payType,
            'gateway_trx_id' => $trxId,
        ];
    }

    /**
     * Call Midtrans Snap API
     */
    protected function createMidtransSnap(string $orderId, array $res, float $rental, float $fee, float $total): array
    {
        $snapEndpoint = 'https://app.sandbox.midtrans.com/snap/v1/transactions';
        if ($this->config->isProduction) {
            $snapEndpoint = 'https://app.midtrans.com/snap/v1/transactions';
        }

        $payload = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int) round($total),
            ],
            'item_details' => [
                [
                    'id'       => 'RENTAL-' . $res['unit_id'],
                    'price'    => (int) round($rental),
                    'quantity' => 1,
                    'name'     => 'Sewa Kamar ' . substr($res['unit_name'], 0, 30),
                ],
                [
                    'id'       => 'GATEWAY-FEE',
                    'price'    => (int) round($fee),
                    'quantity' => 1,
                    'name'     => 'Biaya Payment Gateway',
                ],
            ],
            'customer_details' => [
                'first_name' => $res['tenant_name'],
                'email'      => $res['tenant_email'],
                'phone'      => $res['tenant_whatsapp'] ?? '08123456789',
            ],
        ];

        $ch = curl_init($snapEndpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Authorization: Basic ' . base64_encode($this->config->serverKey . ':'),
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $json = json_decode($response, true);
        if ($httpCode >= 200 && $httpCode < 300 && isset($json['token'])) {
            return [
                'success'      => true,
                'driver'       => 'midtrans',
                'order_id'     => $orderId,
                'gross_amount' => $total,
                'snap_token'   => $json['token'],
                'redirect_url' => $json['redirect_url'],
            ];
        }

        return ['success' => false, 'error' => $response];
    }
}
