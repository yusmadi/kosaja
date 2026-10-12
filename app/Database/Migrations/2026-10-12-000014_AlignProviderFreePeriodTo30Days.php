<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlignProviderFreePeriodTo30Days extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // 1. Get all provider profiles
        $profiles = $db->table('provider_profiles')->get()->getResultArray();
        
        foreach ($profiles as $profile) {
            $userId = (int) $profile['user_id'];
            $createdAt = !empty($profile['created_at']) ? $profile['created_at'] : date('Y-m-d H:i:s');
            
            // Check if this provider already has an active rental contract with a paid invoice
            $hasPaidRental = $db->table('contracts')
                ->join('invoices', 'invoices.contract_id = contracts.id')
                ->where('contracts.provider_id', $userId)
                ->where('invoices.status', 'paid')
                ->countAllResults() > 0;
                
            if ($hasPaidRental) {
                // If already completed a rental transaction, grant 3-day grace period from now (FR-PM-004)
                $newEnd = date('Y-m-d', strtotime('+3 days'));
            } else {
                // Otherwise calculate 30 calendar days from creation (FR-PM-001)
                $candidateEnd = date('Y-m-d', strtotime($createdAt . ' +30 days'));
                $maxAllowed = date('Y-m-d', strtotime('+30 days'));
                $newEnd = ($candidateEnd > $maxAllowed) ? $maxAllowed : $candidateEnd;
            }
            
            $db->table('provider_profiles')
                ->where('id', $profile['id'])
                ->update(['free_period_end' => $newEnd]);
        }
    }

    public function down()
    {
        // Reversible if needed
    }
}
