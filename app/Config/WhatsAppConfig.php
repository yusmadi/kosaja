<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class WhatsAppConfig extends BaseConfig
{
    /**
     * Driver mode: 'simulator' (writes to log & notification table) or 'twilio' or 'meta'
     */
    public string $driver = 'simulator';

    /**
     * Twilio Credentials
     */
    public string $twilioSid = '';
    public string $twilioToken = '';
    public string $twilioFromNumber = 'whatsapp:+14155238886'; // Twilio sandbox standard

    /**
     * Meta Cloud API Credentials
     */
    public string $metaPhoneNumberId = '';
    public string $metaAccessToken = '';

    /**
     * Platform sender label
     */
    public string $senderName = 'KosAja Official Notification';

    public function __construct()
    {
        parent::__construct();

        $this->driver            = env('WHATSAPP_DRIVER', 'simulator');
        $this->twilioSid         = env('TWILIO_SID', '');
        $this->twilioToken       = env('TWILIO_TOKEN', '');
        $this->twilioFromNumber  = env('TWILIO_FROM_NUMBER', 'whatsapp:+14155238886');
        $this->metaPhoneNumberId = env('META_PHONE_NUMBER_ID', '');
        $this->metaAccessToken   = env('META_ACCESS_TOKEN', '');
    }
}
