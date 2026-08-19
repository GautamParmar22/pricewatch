<?php

namespace App\Livewire;

use App\Models\Competitor;
use App\Models\PriceChange;
use App\Support\TenantContext;
use Livewire\Component;

class Dashboard extends Component
{
    /**
     * Render the dashboard component.
     */
    public function render()
    {
        // GP - 19-08-2026 code comment - Gather metrics and compile dashboard view

        // TenantContext is set by ScopeWorkspace middleware. Fall back to session if needed.
        $workspaceId = TenantContext::getWorkspaceId() ?? session('current_workspace_id');

        $activeComps = Competitor::where('workspace_id', $workspaceId)
            ->where('status', 'active')
            ->count();

        $totalChanges = PriceChange::where('workspace_id', $workspaceId)->count();

        $priceIncreases = PriceChange::where('workspace_id', $workspaceId)
            ->where('change_type', 'price_increased')
            ->count();

        $activeAlerts = PriceChange::where('workspace_id', $workspaceId)
            ->whereNull('reviewed_at')
            ->count();

        $recentChanges = PriceChange::where('workspace_id', $workspaceId)
            ->with(['competitor', 'plan'])
            ->latest('detected_at')
            ->take(5)
            ->get();

        $competitorOverview = Competitor::where('workspace_id', $workspaceId)
            ->take(4)
            ->get();
       
        return view('livewire.dashboard', [
            'activeCompetitors' => $activeComps,
            'totalChanges' => $totalChanges,
            'priceIncreases' => $priceIncreases,
            'activeAlerts' => $activeAlerts,
            'recentChanges' => $recentChanges,
            'competitorOverview' => $competitorOverview
        ])->extends('layouts.app')->section('content');
    }
}
