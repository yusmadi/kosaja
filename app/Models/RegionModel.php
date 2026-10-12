<?php

namespace App\Models;

use CodeIgniter\Model;

class RegionModel extends Model
{
    protected $table            = 'regions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'parent_id',
        'name',
        'level',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'name'  => 'required|min_length[2]|max_length[255]',
        'level' => 'required|in_list[province,city,operational]',
    ];

    /**
     * Get hierarchy with parent info, sorted hierarchically:
     * - Provinces / root regions sorted alphabetically by name (A-Z)
     * - Direct children (e.g. Kab/Kota under Aceh) sorted alphabetically under parent (A-Z)
     * - Sub-children (e.g. Klaster Operasional under Kab/Kota) sorted alphabetically under parent (A-Z)
     *
     * @return array
     */
    public function getHierarchy(): array
    {
        $raw = $this->select('regions.*, p.name as parent_name')
                    ->join('regions p', 'p.id = regions.parent_id', 'left')
                    ->findAll();

        if (empty($raw)) {
            return [];
        }

        $byId = [];
        $childrenByParent = [];

        foreach ($raw as $r) {
            $byId[$r['id']] = $r;
            $parentId = !empty($r['parent_id']) ? (int) $r['parent_id'] : 0;
            $childrenByParent[$parentId][] = $r;
        }

        // Roots are regions without parent_id OR whose parent_id does not exist in the dataset
        $roots = [];
        foreach ($raw as $r) {
            $parentId = !empty($r['parent_id']) ? (int) $r['parent_id'] : 0;
            if ($parentId === 0 || !isset($byId[$parentId])) {
                $roots[] = $r;
            }
        }

        // Sort root regions alphabetically by name (case-insensitive)
        usort($roots, function ($a, $b) {
            return strnatcasecmp($a['name'], $b['name']);
        });

        $result = [];
        $visited = [];

        $buildTree = function ($node, $depth = 0) use (&$buildTree, &$result, &$visited, &$childrenByParent) {
            if (isset($visited[$node['id']])) {
                return;
            }
            $visited[$node['id']] = true;
            $node['depth'] = $depth;
            $result[] = $node;

            $nodeId = (int) $node['id'];
            if (isset($childrenByParent[$nodeId])) {
                $children = $childrenByParent[$nodeId];
                // Sort children alphabetically by name (case-insensitive)
                usort($children, function ($a, $b) {
                    return strnatcasecmp($a['name'], $b['name']);
                });

                foreach ($children as $child) {
                    $buildTree($child, $depth + 1);
                }
            }
        };

        foreach ($roots as $root) {
            $buildTree($root, 0);
        }

        // Failsafe: if any orphaned node was not visited, append it
        if (count($result) < count($raw)) {
            foreach ($raw as $r) {
                if (!isset($visited[$r['id']])) {
                    $r['depth'] = 0;
                    $result[] = $r;
                }
            }
        }

        return $result;
    }
}
