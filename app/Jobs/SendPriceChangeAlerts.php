<?php

namespace App\Jobs;

use App\Models\AlertDestination;
use App\Models\Competitor;
use App\Models\NotificationLog;
use App\Models\PriceChange;
use App\Notifications\PriceChangeNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendPriceChangeAlerts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Competitor $competitor)
    {
        // GP - 19-08-2026 code comment - SendPriceChangeAlerts job constructor
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // GP - 19-08-2026 code comment - Alert rules match evaluator and channel dispatcher
        
        $workspace = $this->competitor->workspace;
        
        // Retrieve latest unreviewed changes for this competitor
        $changes = PriceChange::where('competitor_id', $this->competitor->id)
            ->whereNull('reviewed_at')
            ->get();

        if ($changes->isEmpty()) {
            return;
        }

        // Fetch active alert rules for this workspace
        $rules = $workspace->alertRules()->where('enabled', true)->get();
        if ($rules->isEmpty()) {
            return;
        }

        // Fetch configured and active alert destinations
        $destinations = $workspace->alertDestinations()->where('enabled', true)->get();

        foreach ($changes as $change) {
            foreach ($rules as $rule) {
                // Verify if rule applies to this competitor (if scoped)
                $scopedCompetitors = $rule->competitor_ids;
                if (!empty($scopedCompetitors) && !in_array($this->competitor->id, $scopedCompetitors)) {
                    continue;
                }

                // Verify if rule applies to this change type
                $activeChangeTypes = $rule->change_types ?? [];
                if (!in_array($change->change_type, $activeChangeTypes)) {
                    continue;
                }

                // Verify minimum percentage threshold (if applicable to price changes)
                if (in_array($change->change_type, ['price_increased', 'price_decreased'])) {
                    if (abs($change->percentage_change) < $rule->minimum_percentage_change) {
                        continue;
                    }
                }

                // Rule triggers! Dispatch alerts to Destinations
                foreach ($destinations as $dest) {
                    if ($dest->type === 'email') {
                        $this->sendEmailAlert($dest, $change);
                    } elseif ($dest->type === 'slack') {
                        $this->sendSlackAlert($dest, $change);
                    } elseif ($dest->type === 'webhook') {
                        $this->sendWebhookAlert($dest, $change);
                    }
                }
            }
        }
    }

    /**
     * Send email alert notification.
     */
    private function sendEmailAlert(AlertDestination $dest, PriceChange $change): void
    {
        // GP - 19-08-2026 code comment - Dispatch email notifications
        
        try {
            $email = $dest->configuration['email'] ?? null;
            if (!$email) return;

            Notification::route('mail', $email)->notify(new PriceChangeNotification($change));

            NotificationLog::create([
                'workspace_id' => $change->workspace_id,
                'price_change_id' => $change->id,
                'channel' => 'email',
                'status' => 'sent',
                'recipient' => $email,
                'sent_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send email alert: " . $e->getMessage());
            NotificationLog::create([
                'workspace_id' => $change->workspace_id,
                'price_change_id' => $change->id,
                'channel' => 'email',
                'status' => 'failed',
                'recipient' => $dest->configuration['email'] ?? 'unknown',
                'error_message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send slack webhook alert notification.
     */
    private function sendSlackAlert(AlertDestination $dest, PriceChange $change): void
    {
        // GP - 19-08-2026 code comment - Dispatch Slack alerts
        
        try {
            $slackUrl = $dest->configuration['webhook_url'] ?? null;
            if (!$slackUrl) return;

            $message = [
                'text' => "🚨 *Pricing change detected for {$change->competitor->name}*",
                'attachments' => [
                    [
                        'color' => $change->severity === 'high' ? '#EF4444' : '#6366F1',
                        'fields' => [
                            ['title' => 'Plan Name', 'value' => $change->plan?->name ?? '—', 'short' => true],
                            ['title' => 'Change Type', 'value' => ucwords(str_replace('_', ' ', $change->change_type)), 'short' => true],
                            ['title' => 'Before', 'value' => $change->old_value ?? '—', 'short' => true],
                            ['title' => 'After', 'value' => $change->new_value ?? '—', 'short' => true],
                            ['title' => 'Details', 'value' => "Plan pricing adjusted from {$change->old_value} to {$change->new_value} ({$change->percentage_change}%).", 'short' => false]
                        ]
                    ]
                ]
            ];

            Http::post($slackUrl, $message);

            NotificationLog::create([
                'workspace_id' => $change->workspace_id,
                'price_change_id' => $change->id,
                'channel' => 'slack',
                'status' => 'sent',
                'recipient' => $slackUrl,
                'sent_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send Slack webhook alert: " . $e->getMessage());
            NotificationLog::create([
                'workspace_id' => $change->workspace_id,
                'price_change_id' => $change->id,
                'channel' => 'slack',
                'status' => 'failed',
                'recipient' => $dest->configuration['webhook_url'] ?? 'unknown',
                'error_message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Send signed webhook notification payload.
     */
    private function sendWebhookAlert(AlertDestination $dest, PriceChange $change): void
    {
        // GP - 19-08-2026 code comment - Dispatch HMAC-signed webhook alert payloads
        
        try {
            $webhookUrl = $dest->configuration['endpoint_url'] ?? null;
            $secret = $dest->configuration['secret_key'] ?? 'pricewatch_default_secret';
            if (!$webhookUrl) return;

            $payload = [
                'event' => 'price_changed',
                'competitor' => $change->competitor->name,
                'plan' => $change->plan?->name ?? '—',
                'change_type' => $change->change_type,
                'old_price' => $change->old_value,
                'new_price' => $change->new_value,
                'percentage_change' => $change->percentage_change,
                'detected_at' => $change->detected_at->toIso8601String()
            ];

            $jsonPayload = json_encode($payload);
            $signature = hash_hmac('sha256', $jsonPayload, $secret);

            // Execute POST request with security headers
            Http::withHeaders([
                'X-PriceWatch-Signature' => $signature,
                'Content-Type' => 'application/json'
            ])
            ->timeout(10)
            ->post($webhookUrl, $payload);

            NotificationLog::create([
                'workspace_id' => $change->workspace_id,
                'price_change_id' => $change->id,
                'channel' => 'webhook',
                'status' => 'sent',
                'recipient' => $webhookUrl,
                'sent_at' => now()
            ]);
        } catch (\Exception $e) {
            Log::error("Failed to send webhook alert: " . $e->getMessage());
            NotificationLog::create([
                'workspace_id' => $change->workspace_id,
                'price_change_id' => $change->id,
                'channel' => 'webhook',
                'status' => 'failed',
                'recipient' => $dest->configuration['endpoint_url'] ?? 'unknown',
                'error_message' => $e->getMessage()
            ]);
        }
    }
}
