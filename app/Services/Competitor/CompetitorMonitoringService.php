<?php

namespace App\Services\Competitor;

use App\Models\Competitor;
use App\Models\PriceChange;
use App\Models\PricingPlan;
use App\Models\PricingSnapshot;
use App\Services\Pricing\ChangeDetectionService;
use App\Services\Pricing\PricingExtractorService;
use App\Services\Scraping\ScrapingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CompetitorMonitoringService
{
    public function __construct(
        protected ScrapingService $scraper,
        protected PricingExtractorService $extractor,
        protected ChangeDetectionService $detector
    ) {
        // GP - 19-08-2026 code comment - CompetitorMonitoringService constructor
    }

    /**
     * Run check for a specific competitor.
     *
     * @param Competitor $competitor
     * @return array [success => bool, changes_count => int, error => string]
     */
    public function monitor(Competitor $competitor): array
    {
        // GP - 19-08-2026 code comment - Primary monitoring orchestration logic
        
        Log::info("Starting check for competitor: {$competitor->name} (ID: {$competitor->id})");

        // 1. Fetch content safely
        $scrapingResult = $this->scraper->fetchPricingPage($competitor->pricing_url);
        
        if (!$scrapingResult['success']) {
            DB::transaction(function () use ($competitor, $scrapingResult) {
                // Update checked status and set error state
                $competitor->update([
                    'last_checked_at' => Carbon::now(),
                    'next_check_at' => $this->calculateNextCheck($competitor->check_frequency),
                    'status' => 'error'
                ]);

                // Create alert details log if needed (health monitoring)
                Log::warning("Monitoring failed for competitor ID: {$competitor->id}. HTTP status: {$scrapingResult['status']}");
            });

            return [
                'success' => false,
                'changes_count' => 0,
                'error' => $scrapingResult['error']
            ];
        }

        $html = $scrapingResult['content'];
        $contentHash = md5($html);

        // Retrieve last snapshot
        $lastSnapshot = $competitor->snapshots()->latest('captured_at')->first();

        // 2. Check if content hash changed
        if ($lastSnapshot && $lastSnapshot->content_hash === $contentHash) {
            DB::transaction(function () use ($competitor) {
                $competitor->update([
                    'last_checked_at' => Carbon::now(),
                    'next_check_at' => $this->calculateNextCheck($competitor->check_frequency),
                    'status' => 'active' // Recover status if was in error state
                ]);
            });

            Log::info("No HTML content change detected for competitor: {$competitor->name}");
            return [
                'success' => true,
                'changes_count' => 0,
                'error' => ''
            ];
        }

        // 3. Extract pricing plans from new content
        $extractedPlans = $this->extractor->extract($html);
        if (empty($extractedPlans)) {
            // Parser confidence too low or page structures unreadable. Log but do not overwrite
            Log::warning("Pricing extraction returned empty plans list for {$competitor->name}. Skipping database updates.");
            return [
                'success' => true,
                'changes_count' => 0,
                'error' => 'No pricing plans could be extracted from HTML.'
            ];
        }

        $changesDetected = 0;

        // 4. Perform atomic writes
        DB::transaction(function () use ($competitor, $html, $contentHash, $scrapingResult, $extractedPlans, $lastSnapshot, &$changesDetected) {
            // Save Snapshot
            $snapshot = PricingSnapshot::create([
                'competitor_id' => $competitor->id,
                'workspace_id' => $competitor->workspace_id,
                'content_hash' => $contentHash,
                'raw_content' => $html,
                'normalized_data' => $extractedPlans,
                'http_status' => $scrapingResult['status'],
                'captured_at' => Carbon::now()
            ]);

            // Save Plans linked to Snapshot
            $savedPlans = [];
            foreach ($extractedPlans as $planData) {
                $plan = PricingPlan::create([
                    'competitor_id' => $competitor->id,
                    'snapshot_id' => $snapshot->id,
                    'name' => $planData['name'],
                    'slug' => $planData['slug'],
                    'monthly_price' => $planData['monthly_price'],
                    'annual_price' => $planData['annual_price'],
                    'currency' => $planData['currency'],
                    'billing_period' => $planData['billing_period'],
                    'description' => $planData['description'],
                    'user_limit' => $planData['user_limit'],
                    'features' => $planData['features']
                ]);
                $savedPlans[$plan->slug] = $plan;
            }

            // Compare and detect changes
            if ($lastSnapshot) {
                // Get active plan snapshots associated with previous snapshot
                $previousPlans = PricingPlan::where('snapshot_id', $lastSnapshot->id)->get()->toArray();
                
                $detectedChanges = $this->detector->detectChanges($previousPlans, $extractedPlans);

                foreach ($detectedChanges as $change) {
                    $planModel = $savedPlans[$change['plan_slug']] ?? null;

                    PriceChange::create([
                        'workspace_id' => $competitor->workspace_id,
                        'competitor_id' => $competitor->id,
                        'snapshot_id' => $snapshot->id,
                        'plan_id' => $planModel?->id,
                        'change_type' => $change['change_type'],
                        'field' => $change['field'],
                        'old_value' => $change['old_value'],
                        'new_value' => $change['new_value'],
                        'percentage_change' => $change['percentage_change'],
                        'severity' => $change['severity'],
                        'detected_at' => Carbon::now()
                    ]);

                    $changesDetected++;
                }
            }

            // Update checked times
            $competitor->update([
                'last_checked_at' => Carbon::now(),
                'next_check_at' => $this->calculateNextCheck($competitor->check_frequency),
                'status' => 'active'
            ]);
        });

        // 5. Trigger notifications if changes detected
        if ($changesDetected > 0) {
            Log::info("Detected {$changesDetected} price changes for competitor: {$competitor->name}");
            // Alert dispatcher job will be triggered here
            // dispatch(new SendPriceChangeAlerts($competitor->workspace_id));
        }

        return [
            'success' => true,
            'changes_count' => $changesDetected,
            'error' => ''
        ];
    }

    /**
     * Compute next schedule based on interval frequency.
     *
     * @param string $frequency
     * @return Carbon
     */
    private function calculateNextCheck(string $frequency): Carbon
    {
        // GP - 19-08-2026 code comment - Map scrape schedules to target dates
        
        $now = Carbon::now();
        return match (strtolower($frequency)) {
            'hourly' => $now->addHour(),
            '6 hours', 'every_6_hours' => $now->addHours(6),
            'weekly' => $now->addWeek(),
            default => $now->addDay(), // daily default
        };
    }
}
