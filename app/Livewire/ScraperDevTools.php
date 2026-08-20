<?php

namespace App\Livewire;

use App\Jobs\CheckCompetitorPricing;
use App\Models\Competitor;
use App\Models\PriceChange;
use App\Models\PricingSnapshot;
use App\Services\Billing\UsageLimitService;
use App\Services\Competitor\CompetitorMonitoringService;
use App\Support\TenantContext;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class ScraperDevTools extends Component
{
    public array $consoleOutput = [];

    /**
     * Mount the component and clear the console.
     */
    public function mount(): void
    {
        // GP - 20-08-2026 code comment - Initialize Scraper DevTools component console
        $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] Scraper DevTools Console initialized. Ready for operations.";
    }

    /**
     * Scrape a specific competitor synchronously.
     */
    public function scrapeSync(int $competitorId): void
    {
        // GP - 20-08-2026 code comment - Execute a synchronous scraping check for a competitor
        $workspaceId = TenantContext::getWorkspaceId();

        $competitor = Competitor::where('workspace_id', $workspaceId)
            ->where('id', $competitorId)
            ->first();

        if (!$competitor) {
            $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] ERROR: Competitor ID {$competitorId} not found in this workspace.";
            return;
        }

        // Authorize action
        Gate::authorize('update', $competitor);

        $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] Starting synchronous check for {$competitor->name}...";

        try {
            $monitoringService = app(CompetitorMonitoringService::class);
            // Run monitor logic directly (sync)
            $result = $monitoringService->monitor($competitor);

            if ($result['success']) {
                $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] SUCCESS: {$competitor->name} checked successfully. Changes detected: {$result['changes_count']}";
            } else {
                $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] FAILED: {$competitor->name} check failed. Error: {$result['error']}";
            }
        } catch (\Exception $e) {
            Log::error("ScraperDevTools sync scrape exception: " . $e->getMessage());
            $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] EXCEPTION: " . $e->getMessage();
        }
    }

    /**
     * Dispatch a background queue job for a competitor.
     */
    public function queueJob(int $competitorId): void
    {
        // GP - 20-08-2026 code comment - Dispatch CheckCompetitorPricing background job to queue
        $workspaceId = TenantContext::getWorkspaceId();

        $competitor = Competitor::where('workspace_id', $workspaceId)
            ->where('id', $competitorId)
            ->first();

        if (!$competitor) {
            $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] ERROR: Competitor ID {$competitorId} not found in this workspace.";
            return;
        }

        // Authorize action
        Gate::authorize('update', $competitor);

        CheckCompetitorPricing::dispatch($competitor);
        
        $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] DISPATCHED: CheckCompetitorPricing job sent to queue database for {$competitor->name}.";
        session()->flash('success', "Dispatched checking job for {$competitor->name}.");
    }

    /**
     * Scrape all active competitors in the workspace synchronously.
     */
    public function scrapeAllSync(): void
    {
        // GP - 20-08-2026 code comment - Scrape all active competitors synchronously
        $workspaceId = TenantContext::getWorkspaceId();
        $competitors = Competitor::where('workspace_id', $workspaceId)->where('status', 'active')->get();

        if ($competitors->isEmpty()) {
            $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] WARNING: No active competitors found to scrape.";
            return;
        }

        $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] Bulk synchronous check initiated for " . $competitors->count() . " active competitors...";

        $monitoringService = app(CompetitorMonitoringService::class);
        foreach ($competitors as $competitor) {
            try {
                $result = $monitoringService->monitor($competitor);
                if ($result['success']) {
                    $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] SUCCESS [Sync]: {$competitor->name} checked. Changes detected: {$result['changes_count']}";
                } else {
                    $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] FAILED [Sync]: {$competitor->name} check failed: {$result['error']}";
                }
            } catch (\Exception $e) {
                $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] EXCEPTION [Sync] ({$competitor->name}): " . $e->getMessage();
            }
        }
    }

    /**
     * Dispatch queue jobs for all active competitors.
     */
    public function queueAll(): void
    {
        // GP - 20-08-2026 code comment - Queue pricing check jobs for all active competitors
        $workspaceId = TenantContext::getWorkspaceId();
        $competitors = Competitor::where('workspace_id', $workspaceId)->where('status', 'active')->get();

        if ($competitors->isEmpty()) {
            $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] WARNING: No active competitors found to queue.";
            return;
        }

        foreach ($competitors as $competitor) {
            CheckCompetitorPricing::dispatch($competitor);
        }

        $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] DISPATCHED [Bulk]: Queued check jobs for " . $competitors->count() . " active competitors.";
        session()->flash('success', "Queued check jobs for all active competitors.");
    }

    /**
     * Run the Laravel queue worker once synchronously.
     */
    public function processQueue(): void
    {
        // GP - 20-08-2026 code comment - Run artisan queue:work --once to process a single queued job
        $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] Initiating Artisan queue:work worker...";

        try {
            // Process the next queued job
            Artisan::call('queue:work', [
                '--once' => true
            ]);

            $output = trim(Artisan::output());
            if ($output) {
                $lines = explode("\n", $output);
                foreach ($lines as $line) {
                    $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] WORKER: " . trim($line);
                }
            } else {
                $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] WORKER: No jobs in queue or worker completed silently.";
            }
        } catch (\Exception $e) {
            $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] WORKER ERROR: " . $e->getMessage();
        }
    }

    /**
     * Clear console outputs log.
     */
    public function clearConsole(): void
    {
        // GP - 20-08-2026 code comment - Clear console output log list
        $this->consoleOutput = [];
        $this->consoleOutput[] = "[" . now()->format('H:i:s') . "] Console cleared.";
    }

    /**
     * Render the Livewire view with statistics.
     */
    public function render()
    {
        // GP - 20-08-2026 code comment - Query competitor metrics and render Scraper DevTools view
        $workspaceId = TenantContext::getWorkspaceId();

        $competitors = Competitor::where('workspace_id', $workspaceId)->get();
        $pendingJobsCount = DB::table('jobs')->count();

        $latestSnapshots = PricingSnapshot::where('workspace_id', $workspaceId)
            ->with('competitor')
            ->latest('captured_at')
            ->take(5)
            ->get();

        $latestChanges = PriceChange::where('workspace_id', $workspaceId)
            ->with(['competitor', 'plan'])
            ->latest('detected_at')
            ->take(5)
            ->get();

        return view('livewire.scraper-dev-tools', [
            'competitors' => $competitors,
            'pendingJobsCount' => $pendingJobsCount,
            'latestSnapshots' => $latestSnapshots,
            'latestChanges' => $latestChanges,
        ])->extends('layouts.app')->section('content');
    }
}
