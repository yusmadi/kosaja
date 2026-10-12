<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationLogModel extends Model
{
    protected $table            = 'notification_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'recipient_phone',
        'recipient_name',
        'recipient_role',
        'event_type',
        'message',
        'status',
        'channel',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Record notification event
     */
    public function record(string $phone, ?string $name, ?string $role, string $eventType, string $message, string $status = 'simulated', string $channel = 'whatsapp')
    {
        return $this->insert([
            'recipient_phone' => $phone,
            'recipient_name'  => $name,
            'recipient_role'  => $role,
            'event_type'      => $eventType,
            'message'         => $message,
            'status'          => $status,
            'channel'         => $channel,
            'created_at'      => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get recent notifications
     */
    public function getRecent(int $limit = 30)
    {
        return $this->orderBy('id', 'DESC')->limit($limit)->findAll();
    }
}
