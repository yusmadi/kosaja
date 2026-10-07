<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table            = 'audit_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'actor_id',
        'action',
        'object_type',
        'object_id',
        'previous_state',
        'new_state',
        'reason',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Record an audit event
     */
    public function record(
        ?int $actorId,
        string $action,
        string $objectType,
        int $objectId,
        ?array $previousState = null,
        ?array $newState = null,
        ?string $reason = null
    ) {
        return $this->insert([
            'actor_id'       => $actorId,
            'action'         => $action,
            'object_type'    => $objectType,
            'object_id'      => $objectId,
            'previous_state' => $previousState ? json_encode($previousState) : null,
            'new_state'      => $newState ? json_encode($newState) : null,
            'reason'         => $reason,
            'created_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get recent logs with actor user details
     */
    public function getRecentLogs(int $limit = 50)
    {
        return $this->select('audit_logs.*, users.name as actor_name, users.email as actor_email, users.role as actor_role')
                    ->join('users', 'users.id = audit_logs.actor_id', 'left')
                    ->orderBy('audit_logs.id', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }
}
