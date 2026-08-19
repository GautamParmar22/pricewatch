<?php

namespace App\Livewire;

use App\Services\Billing\UsageLimitService;
use App\Support\TenantContext;
use Livewire\Component;

class BillingManager extends Component
{
    /**
     * Switch pricing plan subscription.
     */
    public function selectPlan(string $planName, UsageLimitService $limitService)
    {
        // GP - 19-08-2026 code comment - Switch subscription plans locally
        
        $workspace = TenantContext::getWorkspace();
        
        // Simulating upgrading Cashier subscription
        // Normally: $workspace->newSubscription('default', $priceId)->create($paymentMethod);
        
        session()->flash('success', "Plan successfully updated to the {$planName} Plan!");
    }

    /**
     * Render billing quotas page.
     */
    public function render(UsageLimitService $limitService)
    {
        // GP - 19-08-2026 code comment - Load billing page parameters
        
        $workspace = TenantContext::getWorkspace();
        $plan = $limitService->getWorkspacePlan($workspace);
        $quotas = $limitService->getPlanQuotas($plan);

        // Competitors count
        $competitorsCount = $workspace->competitors()->count();
        $competitorsPercent = min(100, ($competitorsCount / $quotas['competitors']) * 100);

        // Monthly checks count
        $checksCount = $workspace->snapshots()
            ->where('captured_at', '>=', now()->startOfMonth())
            ->count();
        $checksPercent = min(100, ($checksCount / $quotas['checks']) * 100);

        // Team members count
        $usersCount = $workspace->users()->count();
        $usersPercent = min(100, ($usersCount / $quotas['users']) * 100);

        return view('livewire.billing-manager', [
            'planName' => $plan,
            'price' => $plan === 'Starter' ? 19 : ($plan === 'Pro' ? 99 : 49),
            'competitorsCount' => $competitorsCount,
            'competitorsLimit' => $quotas['competitors'],
            'competitorsPercent' => $competitorsPercent,
            'checksCount' => $checksCount,
            'checksLimit' => $quotas['checks'],
            'checksPercent' => $checksPercent,
            'usersCount' => $usersCount,
            'usersLimit' => $quotas['users'],
            'usersPercent' => $usersPercent
        ])->extends('layouts.app')->section('content');
    }
}
