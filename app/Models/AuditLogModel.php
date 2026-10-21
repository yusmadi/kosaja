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
        'region_id',
        'action',
        'object_type',
        'object_id',
        'previous_state',
        'new_state',
        'reason',
        'created_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    /**
     * Record an audit event with dynamic region auto-detection and Jakarta WIB timezone
     */
    public function record(
        ?int $actorId,
        string $action,
        string $objectType,
        int $objectId,
        ?array $previousState = null,
        ?array $newState = null,
        ?string $reason = null,
        ?int $regionId = null
    ) {
        // Fallback actor from active session if null
        if ($actorId === null && session()->has('user_id')) {
            $actorId = (int) session()->get('user_id');
        }

        // Auto-detect region if not explicitly provided
        if ($regionId === null) {
            $regionId = $this->detectRegion($objectType, $objectId, $actorId);
        }

        $jakartaTime = (new \DateTime('now', new \DateTimeZone('Asia/Jakarta')))->format('Y-m-d H:i:s');

        return $this->insert([
            'actor_id'       => $actorId,
            'region_id'      => $regionId,
            'action'         => $action,
            'object_type'    => $objectType,
            'object_id'      => $objectId,
            'previous_state' => $previousState ? json_encode($previousState) : null,
            'new_state'      => $newState ? json_encode($newState) : null,
            'reason'         => $reason,
            'created_at'     => $jakartaTime,
        ]);
    }

    /**
     * Auto-detect region associated with the given domain object or actor
     */
    protected function detectRegion(string $objectType, int $objectId, ?int $actorId): ?int
    {
        $db = \Config\Database::connect();

        try {
            switch (strtolower($objectType)) {
                case 'property':
                case 'properties':
                    $prop = $db->table('properties')->select('region_id')->where('id', $objectId)->get()->getRowArray();
                    if (! empty($prop['region_id'])) return (int) $prop['region_id'];
                    break;

                case 'listing':
                case 'listings':
                    $row = $db->table('listings')
                        ->select('properties.region_id')
                        ->join('units', 'units.id = listings.unit_id')
                        ->join('properties', 'properties.id = units.property_id')
                        ->where('listings.id', $objectId)
                        ->get()->getRowArray();
                    if (! empty($row['region_id'])) return (int) $row['region_id'];
                    break;

                case 'unit':
                case 'units':
                    $row = $db->table('units')
                        ->select('properties.region_id')
                        ->join('properties', 'properties.id = units.property_id')
                        ->where('units.id', $objectId)
                        ->get()->getRowArray();
                    if (! empty($row['region_id'])) return (int) $row['region_id'];
                    break;

                case 'contract':
                case 'contracts':
                    $row = $db->table('contracts')->select('properties.region_id')
                        ->join('properties', 'properties.id = contracts.property_id')
                        ->where('contracts.id', $objectId)->get()->getRowArray();
                    if (! empty($row['region_id'])) return (int) $row['region_id'];
                    break;

                case 'dispute':
                case 'disputes':
                    $row = $db->table('disputes')->select('properties.region_id')
                        ->join('contracts', 'contracts.id = disputes.contract_id')
                        ->join('properties', 'properties.id = contracts.property_id')
                        ->where('disputes.id', $objectId)->get()->getRowArray();
                    if (! empty($row['region_id'])) return (int) $row['region_id'];
                    break;

                case 'report':
                case 'reports':
                    $row = $db->table('reports')->select('region_id')->where('id', $objectId)->get()->getRowArray();
                    if (! empty($row['region_id'])) return (int) $row['region_id'];
                    break;

                case 'rental_application':
                case 'rental_applications':
                    $row = $db->table('rental_applications')
                        ->select('properties.region_id')
                        ->join('listings', 'listings.id = rental_applications.listing_id')
                        ->join('units', 'units.id = listings.unit_id')
                        ->join('properties', 'properties.id = units.property_id')
                        ->where('rental_applications.id', $objectId)
                        ->get()->getRowArray();
                    if (! empty($row['region_id'])) return (int) $row['region_id'];
                    break;

                case 'reservation':
                case 'reservations':
                    $row = $db->table('reservations')
                        ->select('properties.region_id')
                        ->join('rental_applications', 'rental_applications.id = reservations.application_id')
                        ->join('units', 'units.id = rental_applications.unit_id')
                        ->join('properties', 'properties.id = units.property_id')
                        ->where('reservations.id', $objectId)
                        ->get()->getRowArray();
                    if (! empty($row['region_id'])) return (int) $row['region_id'];
                    break;

                case 'invoice':
                case 'invoices':
                    $row = $db->table('invoices')
                        ->select('properties.region_id')
                        ->join('contracts', 'contracts.id = invoices.contract_id')
                        ->join('properties', 'properties.id = contracts.property_id')
                        ->where('invoices.id', $objectId)
                        ->get()->getRowArray();
                    if (! empty($row['region_id'])) return (int) $row['region_id'];
                    break;

                case 'payment':
                case 'payments':
                    $row = $db->table('payments')
                        ->select('properties.region_id')
                        ->join('invoices', 'invoices.id = payments.invoice_id')
                        ->join('contracts', 'contracts.id = invoices.contract_id')
                        ->join('properties', 'properties.id = contracts.property_id')
                        ->where('payments.id', $objectId)
                        ->get()->getRowArray();
                    if (! empty($row['region_id'])) return (int) $row['region_id'];
                    break;

                case 'provider_profile':
                case 'provider_profiles':
                    $row = $db->table('provider_profiles')
                        ->select('properties.region_id')
                        ->join('properties', 'properties.provider_id = provider_profiles.user_id')
                        ->groupStart()
                            ->where('provider_profiles.id', $objectId)
                            ->orWhere('provider_profiles.user_id', $objectId)
                        ->groupEnd()
                        ->limit(1)
                        ->get()->getRowArray();
                    if (! empty($row['region_id'])) return (int) $row['region_id'];
                    break;

                case 'user':
                case 'users':
                    $user = $db->table('users')->select('region_id')->where('id', $objectId)->get()->getRowArray();
                    if (! empty($user['region_id'])) return (int) $user['region_id'];
                    break;
            }

            // Fallback: check actor's assigned region if available
            if ($actorId) {
                $user = $db->table('users')->select('region_id')->where('id', $actorId)->get()->getRowArray();
                if (! empty($user['region_id'])) {
                    return (int) $user['region_id'];
                }
            }
        } catch (\Throwable $e) {
            // Silently skip if query fails
        }

        return null;
    }

    /**
     * Get recent logs globally with actor user and region details
     */
    public function getRecentLogs(int $limit = 50)
    {
        return $this->select('audit_logs.*, users.name as actor_name, users.email as actor_email, users.role as actor_role, regions.name as region_name')
                    ->join('users', 'users.id = audit_logs.actor_id', 'left')
                    ->join('regions', 'regions.id = audit_logs.region_id', 'left')
                    ->orderBy('audit_logs.id', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get recent logs filtered for Regional Admin's scoped regions
     */
    public function getRecentLogsForRegion(array $scopedRegionIds, ?int $actorId = null, int $limit = 50)
    {
        $builder = $this->select('audit_logs.*, users.name as actor_name, users.email as actor_email, users.role as actor_role, regions.name as region_name')
                        ->join('users', 'users.id = audit_logs.actor_id', 'left')
                        ->join('regions', 'regions.id = audit_logs.region_id', 'left');

        if (! empty($scopedRegionIds)) {
            $builder->groupStart()
                    ->whereIn('audit_logs.region_id', $scopedRegionIds);

            if ($actorId) {
                $builder->orWhere('audit_logs.actor_id', $actorId);
            }

            $builder->orWhereIn('users.region_id', $scopedRegionIds);
            $builder->groupEnd();
        } elseif ($actorId) {
            $builder->where('audit_logs.actor_id', $actorId);
        }

        return $builder->orderBy('audit_logs.id', 'DESC')
                       ->limit($limit)
                       ->findAll();
    }

    /**
     * Format timestamp according to Jakarta (WIB) timezone
     */
    public static function formatJakartaTime(?string $datetime, string $format = 'd M Y H:i'): string
    {
        if (empty($datetime)) {
            return '-';
        }

        try {
            $dt = new \DateTime($datetime, new \DateTimeZone('Asia/Jakarta'));
            return $dt->format($format) . ' WIB';
        } catch (\Throwable $e) {
            return date($format, strtotime($datetime)) . ' WIB';
        }
    }
}
