<?php

namespace App\Jobs;

use App\Models\Competitor;
use App\Services\Billing\UsageLimitService;
use App\Services\Competitor\CompetitorMonitoringService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckCompetitorPricing implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 300;

    /**
     * Create a new job instance.
     */
    public function __construct(public Competitor $competitor)
    {
        // GP - 19-08-2026 code comment - CheckCompetitorPricing job constructor
    }

    /**
     * Execute the job.
     */
    public function handle(CompetitorMonitoringService $monitoringService, UsageLimitService $limitService): void
    {
        // GP - 19-08-2026 code comment - Scrape and compare competitor pricing
        
        Log::info("Executing pricing check for competitor ID: {$this->competitor->id} ({$this->competitor->name})");

        // Verify status
        if ($this->competitor->status === 'paused') {
            Log::info("Skipping competitor ID: {$this->competitor->id} because monitoring is paused.");
            return;
        }

        // Verify limits
        $workspace = $this->competitor->workspace;
        if (!$limitService->canPerformCheck($workspace)) {
            Log::warning("Workspace ID: {$workspace->id} has exceeded its monthly scraping check limit.");
            return;
        }

        // Execute check
        $result = $monitoringService->monitor($this->competitor);

        if (!$result['success']) {
            Log::error("Pricing check failed for competitor ID: {$this->competitor->id}: {$result['error']}");
            // Let the job fail and retry if applicable (e.g. timeout issues)
            throw new \Exception("Monitoring check failed: " . $result['error']);
        }

        // Dispatch alert rules runner if changes were found
        if ($result['changes_count'] > 0) {
            SendPriceChangeAlerts::dispatch($this->competitor);
        }
    }
}
