<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class ReservationConfig extends BaseConfig
{
    /**
     * Reservation Expiration Window in hours.
     * Default: 24 hours from provider approval.
     */
    public int $expirationWindowHours = 24;

    /**
     * Third-party payment gateway transaction fee (IDR).
     * Charged to the tenant transparently per PRD FR-PAY-002 & FR-PAY-003.
     */
    public float $gatewayFee = 4500.00;

    /**
     * Description of the gateway fee.
     */
    public string $gatewayFeeDescription = 'Biaya pemrosesan sistem pembayaran digital (Payment Gateway) pihak ketiga. Bukan komisi calo atau markup sewa platform.';
}
