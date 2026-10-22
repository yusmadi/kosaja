<?php

namespace App\Models;

use CodeIgniter\Model;

class ProviderSubscriptionModel extends Model
{
    protected $table            = 'provider_subscriptions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'provider_id',
        'plan_id',
        'start_date',
        'end_date',
        'status',
        'capacity_limit',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get the active subscription for a provider
     */
    public function getActiveSubscription(int $providerId): ?array
    {
        $today = date('Y-m-d');

        return $this->select('provider_subscriptions.*, subscription_plans.name as plan_name, subscription_plans.slug as plan_slug, subscription_plans.type as plan_type, subscription_plans.billing_cycle, subscription_plans.price as plan_price')
            ->join('subscription_plans', 'subscription_plans.id = provider_subscriptions.plan_id')
            ->where('provider_subscriptions.provider_id', $providerId)
            ->where('provider_subscriptions.status', 'active')
            ->where('provider_subscriptions.end_date >=', $today)
            ->orderBy('provider_subscriptions.end_date', 'DESC')
            ->first();
    }

    /**
     * Get effective listing quota and billing status for a provider
     */
    public function getEffectiveQuota(int $providerId): array
    {
        $today = date('Y-m-d');
        $activeSub = $this->getActiveSubscription($providerId);

        // Get provider profile for free period check
        $profileModel = new ProviderProfileModel();
        $profile = $profileModel->getByUserId($providerId);
        $freePeriodEnd = $profile['free_period_end'] ?? null;

        // 1. If provider has an active paid subscription/listing fee
        if ($activeSub) {
            $daysRemaining = (int) ceil((strtotime($activeSub['end_date']) - strtotime($today)) / 86400);

            return [
                'has_active_package' => true,
                'package_source'     => 'paid',
                'plan_name'          => $activeSub['plan_name'],
                'plan_slug'          => $activeSub['plan_slug'],
                'plan_type'          => $activeSub['plan_type'],
                'billing_cycle'      => $activeSub['billing_cycle'],
                'capacity_limit'     => (int) $activeSub['capacity_limit'],
                'start_date'         => $activeSub['start_date'],
                'end_date'           => $activeSub['end_date'],
                'days_remaining'     => max(0, $daysRemaining),
                'is_in_grace_period' => false,
                'status'             => 'active',
                'subscription_id'    => $activeSub['id'],
            ];
        }

        // 2. Check if within initial Free Period (30 days or shortened transition)
        $isFreeActive = false;
        $isGracePeriod = false;
        $freeDaysRemaining = 0;

        if (! empty($freePeriodEnd)) {
            $freeDaysRemaining = (int) ceil((strtotime($freePeriodEnd) - strtotime($today)) / 86400);
            if ($freeDaysRemaining >= 0) {
                $isFreeActive = true;
                // If remaining days <= 3, consider it in grace/transition period
                if ($freeDaysRemaining <= 3) {
                    $isGracePeriod = true;
                }
            }
        }

        if ($isFreeActive) {
            return [
                'has_active_package' => true,
                'package_source'     => 'free_period',
                'plan_name'          => 'Free Starter Period',
                'plan_slug'          => 'free-starter',
                'plan_type'          => 'free_trial',
                'billing_cycle'      => 'initial_free',
                'capacity_limit'     => 3,
                'start_date'         => null,
                'end_date'           => $freePeriodEnd,
                'days_remaining'     => max(0, $freeDaysRemaining),
                'is_in_grace_period' => $isGracePeriod,
                'status'             => $isGracePeriod ? 'grace_period' : 'active',
                'subscription_id'    => null,
            ];
        }

        // 3. Expired without active package (Capacity = 0 for new non-rented listings)
        return [
            'has_active_package' => false,
            'package_source'     => 'expired',
            'plan_name'          => 'Masa Aktif Berakhir',
            'plan_slug'          => 'none',
            'plan_type'          => 'none',
            'billing_cycle'      => 'none',
            'capacity_limit'     => 0,
            'start_date'         => null,
            'end_date'           => $freePeriodEnd,
            'days_remaining'     => 0,
            'is_in_grace_period' => false,
            'status'             => 'expired',
            'subscription_id'    => null,
        ];
    }

    /**
     * Activate or extend subscription after payment success
     */
    public function activatePlan(int $providerId, int $planId): array
    {
        $planModel = new SubscriptionPlanModel();
        $plan = $planModel->find($planId);
        if (! $plan) {
            throw new \RuntimeException('Subscription plan not found: ' . $planId);
        }

        $today = date('Y-m-d');
        $activeSub = $this->getActiveSubscription($providerId);

        // Determine start date: if currently active with same/compatible plan, extend from end_date
        $startDate = $today;
        if ($activeSub && $activeSub['end_date'] >= $today) {
            $startDate = date('Y-m-d', strtotime($activeSub['end_date'] . ' +1 day'));
        }

        $endDate = date('Y-m-d', strtotime($startDate . ' +' . (int) $plan['duration_days'] . ' days'));

        $subId = $this->insert([
            'provider_id'    => $providerId,
            'plan_id'        => $planId,
            'start_date'     => $startDate,
            'end_date'       => $endDate,
            'status'         => 'active',
            'capacity_limit' => (int) $plan['listing_capacity'],
        ]);

        return [
            'subscription_id' => $subId,
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'capacity_limit'  => (int) $plan['listing_capacity'],
        ];
    }
}
