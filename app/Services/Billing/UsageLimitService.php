<?php

namespace App\Services\Billing;

use App\Models\Workspace;

class UsageLimitService
{
    /**
     * Get plan configuration quotas.
     *
     * @param string $planName
     * @return array
     */
    public function getPlanQuotas(string $planName): array
    {
        // GP - 19-08-2026 code comment - Map plan names to active usage quotas
        
        return match (ucfirst($planName)) {
            'Starter' => [
                'competitors' => 10,
                'checks' => 10000,
                'users' => 3
            ],
            'Pro' => [
                'competitors' => 100,
                'checks' => 100000,
                'users' => 15
            ],
            'Agency' => [
                'competitors' => 500,
                'checks' => 500000,
                'users' => 50
            ],
            default => [ // Growth plan default
                'competitors' => 25,
                'checks' => 25000,
                'users' => 10
            ]
        };
    }

    /**
     * Check if a workspace has exceeded its competitors limit.
     *
     * @param Workspace $workspace
     * @return bool
     */
    public function canAddCompetitor(Workspace $workspace): bool
    {
        // GP - 19-08-2026 code comment - Verify competitor capacity
        
        $plan = $this->getWorkspacePlan($workspace);
        $quotas = $this->getPlanQuotas($plan);

        return $workspace->competitors()->count() < $quotas['competitors'];
    }

    /**
     * Check if a workspace has exceeded its monthly check limit.
     *
     * @param Workspace $workspace
     * @return bool
     */
    public function canPerformCheck(Workspace $workspace): bool
    {
        // GP - 19-08-2026 code comment - Verify crawl volume capacity
        
        $plan = $this->getWorkspacePlan($workspace);
        $quotas = $this->getPlanQuotas($plan);

        // Fetch checks count from the current month
        $checksThisMonth = $workspace->snapshots()
            ->where('captured_at', '>=', now()->startOfMonth())
            ->count();

        return $checksThisMonth < $quotas['checks'];
    }

    /**
     * Check if a workspace has exceeded user seats limit.
     *
     * @param Workspace $workspace
     * @return bool
     */
    public function canAddUser(Workspace $workspace): bool
    {
        // GP - 19-08-2026 code comment - Verify team seat capacity
        
        $plan = $this->getWorkspacePlan($workspace);
        $quotas = $this->getPlanQuotas($plan);

        return $workspace->users()->count() < $quotas['users'];
    }

    /**
     * Resolve the current plan name for a workspace.
     *
     * @param Workspace $workspace
     * @return string
     */
    public function getWorkspacePlan(Workspace $workspace): string
    {
        // GP - 19-08-2026 code comment - Resolve workspace plan subscription
        
        // If Cashier subscription is active, map stripe price ID or name
        $subscription = $workspace->subscription('default');
        if ($subscription && $subscription->active()) {
            // Cashier maps subscription name directly
            return $subscription->name;
        }

        // Default local plan fallback
        return 'Growth';
    }
}
