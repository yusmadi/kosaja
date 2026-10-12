<?php

namespace App\Libraries;

use Config\Database;

class AdminScopeService
{
    protected $db;
    protected $session;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->session = session();
    }

    /**
     * Check if currently logged in as Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->session->get('role') === 'super_admin';
    }

    /**
     * Check if currently logged in as Admin Daerah/Kota
     */
    public function isAdminDaerah(): bool
    {
        return $this->session->get('role') === 'admin_daerah';
    }

    /**
     * Get the region ID assigned to current admin
     */
    public function getAdminRegionId(): ?int
    {
        $userId = $this->session->get('user_id');
        if (! $userId) {
            return null;
        }

        $user = $this->db->table('users')->where('id', $userId)->get()->getRowArray();
        return ! empty($user['region_id']) ? (int) $user['region_id'] : null;
    }

    /**
     * Get list of region IDs within this admin's scope.
     * Returns NULL if Super Admin (meaning unrestricted / global).
     * Returns array of IDs (parent + operational children) if Admin Daerah.
     */
    public function getScopedRegionIds(): ?array
    {
        if ($this->isSuperAdmin()) {
            return null; // Global unrestricted access
        }

        $regionId = $this->getAdminRegionId();
        if (! $regionId) {
            return [];
        }

        $children = $this->db->table('regions')->where('parent_id', $regionId)->get()->getResultArray();
        $ids = [$regionId];
        foreach ($children as $child) {
            $ids[] = (int) $child['id'];
        }

        return $ids;
    }

    /**
     * Get human-readable scope label
     */
    public function getScopedRegionLabel(): string
    {
        if ($this->isSuperAdmin()) {
            return 'Nasional (Seluruh Wilayah Indonesia)';
        }

        $regionId = $this->getAdminRegionId();
        if (! $regionId) {
            return 'Wilayah Regional (Belum Ditugaskan)';
        }

        $region = $this->db->table('regions')->where('id', $regionId)->get()->getRowArray();

        if ($region) {
            return $region['name'];
        }

        return 'Wilayah Regional';
    }

    /**
     * Get names of operational child clusters/areas under this admin's region
     *
     * @return string[]
     */
    public function getScopedClusterNames(): array
    {
        if ($this->isSuperAdmin()) {
            return [];
        }

        $regionId = $this->getAdminRegionId();
        if (! $regionId) {
            return [];
        }

        $children = $this->db->table('regions')
            ->where('parent_id', $regionId)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        $names = [];
        foreach ($children as $child) {
            $cleanName = trim(preg_replace('/\s*\(.*?\)/', '', $child['name']));
            $names[] = ! empty($cleanName) ? $cleanName : $child['name'];
        }

        return $names;
    }

    /**
     * Get formatted label of clusters (e.g. "Depok & Mlati", "Kuta Alam", or empty string if no sub-clusters)
     */
    public function getScopedClustersLabel(): string
    {
        $clusters = $this->getScopedClusterNames();
        if (empty($clusters)) {
            return '';
        }

        if (count($clusters) === 1) {
            return $clusters[0];
        }

        $last = array_pop($clusters);
        return implode(', ', $clusters) . ' & ' . $last;
    }
}
