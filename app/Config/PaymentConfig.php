<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class PaymentConfig extends BaseConfig
{
    /**
     * Driver mode: 'mock' (instant local sandbox simulator) or 'midtrans'
     */
    public string $driver = 'mock';

    /**
     * Midtrans Server Key
     */
    public string $serverKey = '';

    /**
     * Midtrans Client Key
     */
    public string $clientKey = '';

    /**
     * Production or Sandbox mode
     */
    public bool $isProduction = false;

    /**
     * Snap URL
     */
    public string $snapUrl = 'https://app.sandbox.midtrans.com/snap/v2/vtweb/';

    public function __construct()
    {
        parent::__construct();

        $this->driver       = env('PAYMENT_DRIVER', 'mock');
        $this->serverKey    = env('MIDTRANS_SERVER_KEY', 'SB-Mid-server-demo-kosaja-testkey');
        $this->clientKey    = env('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-demo-kosaja-testkey');
        $this->isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);

        if ($this->isProduction) {
            $this->snapUrl = 'https://app.midtrans.com/snap/v2/vtweb/';
        }
    }
}
