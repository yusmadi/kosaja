<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportModel extends Model
{
    protected $table            = 'reports';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'reporter_id',
        'object_type',
        'object_id',
        'region_id',
        'title',
        'reason',
        'evidence_url',
        'status',
        'admin_notes',
        'resolved_by_id',
        'resolved_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get reports with reporter and regional information
     */
    public function getReportsWithDetails(?string $status = null, ?int $regionId = null)
    {
        $builder = $this->db->table('reports')
            ->select('reports.*, 
                      users.name as reporter_name, users.email as reporter_email, users.whatsapp as reporter_whatsapp,
                      regions.name as region_name,
                      resolvers.name as resolver_name')
            ->join('users', 'users.id = reports.reporter_id', 'left')
            ->join('regions', 'regions.id = reports.region_id', 'left')
            ->join('users as resolvers', 'resolvers.id = reports.resolved_by_id', 'left')
            ->orderBy('reports.id', 'DESC');

        if (! empty($status)) {
            $builder->where('reports.status', $status);
        }

        if (! empty($regionId)) {
            // Include child regions (operational regions under this city/regency)
            $childRegionIds = $this->getChildRegionIds($regionId);
            $builder->whereIn('reports.region_id', array_merge([$regionId], $childRegionIds));
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Helper to get descendant region IDs
     */
    protected function getChildRegionIds(int $regionId): array
    {
        $children = $this->db->table('regions')->where('parent_id', $regionId)->get()->getResultArray();
        $ids = [];
        foreach ($children as $c) {
            $ids[] = (int) $c['id'];
        }
        return $ids;
    }
}
