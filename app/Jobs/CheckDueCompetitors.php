<?php

namespace App\Jobs;

use App\Models\Competitor;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckDueCompetitors implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        // GP - 19-08-2026 code comment - CheckDueCompetitors job constructor
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // GP - 19-08-2026 code comment - Locate competitors due for pricing check
        
        Log::info("Running CheckDueCompetitors scheduler task...");

        // Fetch all active competitors whose next_check_at is due (or never checked)
        $dueCompetitors = Competitor::where('status', 'active')
            ->where(function ($query) {
                $query->where('next_check_at', '<=', Carbon::now())
                      ->orWhereNull('next_check_at');
            })
            ->get();

        Log::info("Found {$dueCompetitors->count()} competitors due for check.");

        foreach ($dueCompetitors as $competitor) {
            // Dispatch price checker job for each
            CheckCompetitorPricing::dispatch($competitor);
        }
    }
}
