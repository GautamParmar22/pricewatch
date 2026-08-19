<?php

namespace App\Http\Controllers;

use App\Models\Competitor;
use App\Models\PricingPlan;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompetitorController extends Controller
{
    /**
     * Display a list of competitors.
     */
    public function index()
    { 
        // GP - 19-08-2026 code comment - Delegate listing to Livewire CompetitorList component
        return view('competitors.index');
    }

    /**
     * Show competitor details.
     */
    public function show(int $id)
    {
        // GP - 19-08-2026 code comment - Show detailed competitor insights
        
        $workspaceId = TenantContext::getWorkspaceId();
        
        $competitor = Competitor::where('workspace_id', $workspaceId)
            ->where('id', $id)
            ->firstOrFail();

        // Authorize action
        Gate::authorize('view', $competitor);

        // Fetch latest snapshot plans
        $latestSnapshot = $competitor->snapshots()->latest('captured_at')->first();
        
        $plans = [];
        if ($latestSnapshot) {
            $plans = PricingPlan::where('snapshot_id', $latestSnapshot->id)->get();
        }

        // Fetch recent price change timeline logs
        $changes = $competitor->changes()->latest('detected_at')->take(10)->get();

        return view('competitors.show', [
            'competitor' => $competitor,
            'plans' => $plans,
            'changes' => $changes
        ]);
    }

    /**
     * Delete competitor tracker.
     */
    public function destroy(int $id)
    {
        // GP - 19-08-2026 code comment - Safely delete competitor tracker
        
        $workspaceId = TenantContext::getWorkspaceId();
        
        $competitor = Competitor::where('workspace_id', $workspaceId)
            ->where('id', $id)
            ->firstOrFail();

        // Authorize action
        Gate::authorize('delete', $competitor);

        $competitor->delete();

        return redirect()->route('competitors.index')->with('success', 'Competitor deleted successfully.');
    }
}
