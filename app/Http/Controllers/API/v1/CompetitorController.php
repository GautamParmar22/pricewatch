<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CompetitorResource;
use App\Http\Resources\PriceChangeResource;
use App\Jobs\CheckCompetitorPricing;
use App\Models\Competitor;
use App\Services\Billing\UsageLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class CompetitorController extends Controller
{
    /**
     * List all competitors for workspace.
     */
    public function index(Request $request)
    {
        // GP - 19-08-2026 code comment - List competitors API
        
        $workspace = $request->user()->workspaces()->first();
        if (!$workspace) {
            return response()->json(['error' => 'No active workspace found.'], 404);
        }

        $competitors = $workspace->competitors;

        return CompetitorResource::collection($competitors);
    }

    /**
     * Store new competitor.
     */
    public function store(Request $request, UsageLimitService $limitService)
    {
        // GP - 19-08-2026 code comment - Create competitor API
        
        $workspace = $request->user()->workspaces()->first();
        if (!$workspace) {
            return response()->json(['error' => 'No active workspace found.'], 404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'website_url' => ['required', 'url', 'max:255'],
            'pricing_url' => ['required', 'url', 'max:255'],
            'check_frequency' => ['required', 'string', 'in:hourly,every_6_hours,daily,weekly'],
        ]);

        // 1. Quota check
        if (!$limitService->canAddCompetitor($workspace)) {
            return response()->json(['error' => 'Competitor limit reached for current billing subscription.'], 403);
        }

        // 2. Duplicate checking
        $exists = Competitor::where('workspace_id', $workspace->id)
            ->where('pricing_url', $request->pricing_url)
            ->exists();

        if ($exists) {
            return response()->json(['error' => 'Pricing URL is already monitored.'], 422);
        }

        // 3. Create
        $competitor = Competitor::create([
            'workspace_id' => $workspace->id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'website_url' => $request->website_url,
            'pricing_url' => $request->pricing_url,
            'check_frequency' => $request->check_frequency,
            'created_by' => $request->user()->id
        ]);

        // 4. Dispatch job
        CheckCompetitorPricing::dispatch($competitor);

        return new CompetitorResource($competitor);
    }

    /**
     * Show competitor.
     */
    public function show(Request $request, int $id)
    {
        // GP - 19-08-2026 code comment - Show competitor API
        
        $workspace = $request->user()->workspaces()->first();
        $competitor = Competitor::where('workspace_id', $workspace?->id)
            ->where('id', $id)
            ->firstOrFail();

        Gate::authorize('view', $competitor);

        return new CompetitorResource($competitor);
    }

    /**
     * Update competitor.
     */
    public function update(Request $request, int $id)
    {
        // GP - 19-08-2026 code comment - Update competitor API
        
        $workspace = $request->user()->workspaces()->first();
        $competitor = Competitor::where('workspace_id', $workspace?->id)
            ->where('id', $id)
            ->firstOrFail();

        Gate::authorize('update', $competitor);

        $request->validate([
            'name' => ['string', 'max:255'],
            'check_frequency' => ['string', 'in:hourly,every_6_hours,daily,weekly'],
            'status' => ['string', 'in:active,paused,error'],
        ]);

        $competitor->update($request->only(['name', 'check_frequency', 'status']));

        return new CompetitorResource($competitor);
    }

    /**
     * Destroy competitor.
     */
    public function destroy(Request $request, int $id)
    {
        // GP - 19-08-2026 code comment - Delete competitor API
        
        $workspace = $request->user()->workspaces()->first();
        $competitor = Competitor::where('workspace_id', $workspace?->id)
            ->where('id', $id)
            ->firstOrFail();

        Gate::authorize('delete', $competitor);

        $competitor->delete();

        return response()->json(['success' => true, 'message' => 'Competitor monitoring deleted.']);
    }

    /**
     * Trigger checking scan immediately.
     */
    public function checkNow(Request $request, int $id)
    {
        // GP - 19-08-2026 code comment - Trigger scrape job API
        
        $workspace = $request->user()->workspaces()->first();
        $competitor = Competitor::where('workspace_id', $workspace?->id)
            ->where('id', $id)
            ->firstOrFail();

        Gate::authorize('update', $competitor);

        CheckCompetitorPricing::dispatch($competitor);

        return response()->json(['success' => true, 'message' => 'Crawl checking job dispatched.']);
    }

    /**
     * Return historical changes logs.
     */
    public function priceHistory(Request $request, int $id)
    {
        // GP - 19-08-2026 code comment - Retrieve history logs API
        
        $workspace = $request->user()->workspaces()->first();
        $competitor = Competitor::where('workspace_id', $workspace?->id)
            ->where('id', $id)
            ->firstOrFail();

        Gate::authorize('view', $competitor);

        $changes = $competitor->changes()->latest('detected_at')->get();

        return PriceChangeResource::collection($changes);
    }
}
