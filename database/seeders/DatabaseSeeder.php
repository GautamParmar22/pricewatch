<?php

namespace Database\Seeders;

use App\Models\AlertDestination;
use App\Models\AlertRule;
use App\Models\Competitor;
use App\Models\PriceChange;
use App\Models\PricingPlan;
use App\Models\PricingSnapshot;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // GP - 19-08-2026 code comment - Seed B2B pricing intelligence workspace, competitors, snapshots and changes logs
        
        // 1. Create Workspace
        $owner = User::create([
            'name' => 'Gautam Parmar',
            'email' => 'gautam@parextech.com',
            'password' => bcrypt('123456'),
        ]);

        $member1 = User::create([
            'name' => 'Yash Bodar',
            'email' => 'yash@parextech.com',
            'password' => bcrypt('123456'),
        ]);

        $member2 = User::create([
            'name' => 'Michael Biehn',
            'email' => 'michael@pricewatch.com',
            'password' => bcrypt('password123'),
        ]);

        $workspace = Workspace::create([
            'name' => 'PriceWatch HQ',
            'slug' => 'pricewatch-hq',
            'owner_id' => $owner->id,
            'timezone' => 'UTC'
        ]);

        // Associate users
        $workspace->users()->attach($owner->id, ['role' => 'owner']);
        $workspace->users()->attach($member1->id, ['role' => 'admin']);
        $workspace->users()->attach($member2->id, ['role' => 'member']);

        // 2. Alert rules and destinations
        AlertRule::create([
            'workspace_id' => $workspace->id,
            'name' => 'Notify on any Price Change',
            'change_types' => ['price_increased', 'price_decreased'],
            'minimum_percentage_change' => 0.00,
            'competitor_ids' => null,
            'channels' => ['email', 'slack'],
            'enabled' => true
        ]);

        AlertRule::create([
            'workspace_id' => $workspace->id,
            'name' => 'Notify on New Plan Tiers',
            'change_types' => ['plan_added'],
            'minimum_percentage_change' => 0.00,
            'competitor_ids' => null,
            'channels' => ['email'],
            'enabled' => true
        ]);

        AlertRule::create([
            'workspace_id' => $workspace->id,
            'name' => 'Critical alerts (> 20% shift)',
            'change_types' => ['price_increased', 'price_decreased'],
            'minimum_percentage_change' => 20.00,
            'competitor_ids' => null,
            'channels' => ['email', 'slack', 'webhook'],
            'enabled' => true
        ]);

        AlertDestination::create([
            'workspace_id' => $workspace->id,
            'name' => 'Owner Primary Email',
            'type' => 'email',
            'configuration' => ['email_address' => 'owner@pricewatch.com'],
            'enabled' => true,
            'verified_at' => now()
        ]);

        AlertDestination::create([
            'workspace_id' => $workspace->id,
            'name' => 'Company Slack Hook',
            'type' => 'slack',
            'configuration' => ['webhook_url' => 'https://hooks.slack.com/services/T00000000/B00000000/XXXXXXXXXXXXXXXXXXXXXXXX'],
            'enabled' => true,
            'verified_at' => now()
        ]);

        // 3. 10 Competitors
        $competitorNames = [
            'Stripe' => ['url' => 'stripe.com', 'freq' => 'hourly'],
            'Paddle' => ['url' => 'paddle.com', 'freq' => 'hourly'],
            'Lemon Squeezy' => ['url' => 'lemonsqueezy.com', 'freq' => 'daily'],
            'Chargebee' => ['url' => 'chargebee.com', 'freq' => 'daily'],
            'Recurly' => ['url' => 'recurly.com', 'freq' => 'daily'],
            'Braintree' => ['url' => 'braintree.com', 'freq' => 'weekly'],
            'Shopify' => ['url' => 'shopify.com', 'freq' => 'daily'],
            'Gumroad' => ['url' => 'gumroad.com', 'freq' => 'weekly'],
            'Zuora' => ['url' => 'zuora.com', 'freq' => 'weekly'],
            'FastSpring' => ['url' => 'fastspring.com', 'freq' => 'weekly'],
        ];

        $competitorModels = [];
        foreach ($competitorNames as $name => $meta) {
            $competitorModels[$name] = Competitor::create([
                'workspace_id' => $workspace->id,
                'name' => $name,
                'slug' => Str::slug($name),
                'website_url' => "https://{$meta['url']}",
                'pricing_url' => "https://{$meta['url']}/pricing",
                'status' => 'active',
                'check_frequency' => $meta['freq'],
                'last_checked_at' => now()->subMinutes(rand(10, 180)),
                'next_check_at' => now()->addMinutes(rand(10, 300)),
                'created_by' => $owner->id
            ]);
        }

        // 4. Create baseline and current snapshots with historical changes
        $startDate = Carbon::create(2026, 8, 1);
        $totalChangesCount = 0;

        foreach ($competitorModels as $name => $comp) {
            
            // Create a baseline snapshot
            $baselineSnap = PricingSnapshot::create([
                'workspace_id' => $workspace->id,
                'competitor_id' => $comp->id,
                'content_hash' => md5($name . '_baseline'),
                'raw_content' => 'Baseline HTML content for ' . $name,
                'normalized_data' => [],
                'http_status' => 200,
                'captured_at' => $startDate->copy()->addDays(rand(1, 5)),
            ]);

            // Add plans to baseline
            $plansData = [];
            if ($name === 'Stripe') {
                $plansData = [
                    ['name' => 'Standard', 'price' => 0.0, 'annual' => 0.0, 'users' => 999999, 'features' => ['Pay-as-you-go', 'Global cards', 'API logs']],
                    ['name' => 'Professional', 'price' => 49.0, 'annual' => 490.0, 'users' => 5, 'features' => ['Advanced Fraud protect', 'Custom receipts', 'Priority SLA']],
                ];
            } elseif ($name === 'Paddle') {
                $plansData = [
                    ['name' => 'Checkout', 'price' => 0.0, 'annual' => 0.0, 'users' => 999999, 'features' => ['Global taxes resolved', 'Checkout modals']],
                    ['name' => 'Growth', 'price' => 79.0, 'annual' => 790.0, 'users' => 10, 'features' => ['SaaS metrics analytics', 'Multi-currency invoicing']],
                ];
            } elseif ($name === 'Lemon Squeezy') {
                $plansData = [
                    ['name' => 'Basic', 'price' => 32.0, 'annual' => 320.0, 'users' => 1, 'features' => ['Merchant of record', 'License keys']],
                ];
            } else {
                $plansData = [
                    ['name' => 'Starter', 'price' => 19.0, 'annual' => 190.0, 'users' => 3, 'features' => ['Core billing features']],
                    ['name' => 'Enterprise', 'price' => null, 'annual' => null, 'users' => 999999, 'features' => ['Dedicated servers', 'Custom contract']],
                ];
            }

            $baselinePlans = [];
            foreach ($plansData as $pd) {
                $baselinePlans[$pd['name']] = PricingPlan::create([
                    'competitor_id' => $comp->id,
                    'snapshot_id' => $baselineSnap->id,
                    'name' => $pd['name'],
                    'slug' => Str::slug($pd['name']),
                    'monthly_price' => $pd['price'],
                    'annual_price' => $pd['annual'],
                    'currency' => 'USD',
                    'user_limit' => $pd['users'],
                    'features' => $pd['features']
                ]);
            }

            // Create current snapshot reflecting shifts
            $currentSnap = PricingSnapshot::create([
                'workspace_id' => $workspace->id,
                'competitor_id' => $comp->id,
                'content_hash' => md5($name . '_current'),
                'raw_content' => 'Current HTML content for ' . $name,
                'normalized_data' => [],
                'http_status' => 200,
                'captured_at' => Carbon::create(2026, 8, 19, 10, 0, 0),
            ]);

            // Add plans to current snapshot, triggering historical changes
            foreach ($plansData as $pd) {
                
                $currentPrice = $pd['price'];
                $hasChange = false;
                $oldPriceVal = null;
                $changeType = null;
                $percent = 0;

                // Let's inject changes for Stripe, Paddle and Lemon Squeezy explicitly
                if ($name === 'Stripe' && $pd['name'] === 'Professional') {
                    $currentPrice = 59.0;
                    $hasChange = true;
                    $oldPriceVal = '$49.00';
                    $changeType = 'price_increased';
                    $percent = 20;
                } elseif ($name === 'Paddle' && $pd['name'] === 'Growth') {
                    $currentPrice = 89.0;
                    $hasChange = true;
                    $oldPriceVal = '$79.00';
                    $changeType = 'price_increased';
                    $percent = 13;
                } elseif ($name === 'Lemon Squeezy' && $pd['name'] === 'Basic') {
                    $currentPrice = 29.0;
                    $hasChange = true;
                    $oldPriceVal = '$32.00';
                    $changeType = 'price_decreased';
                    $percent = -9;
                }

                $newPlan = PricingPlan::create([
                    'competitor_id' => $comp->id,
                    'snapshot_id' => $currentSnap->id,
                    'name' => $pd['name'],
                    'slug' => Str::slug($pd['name']),
                    'monthly_price' => $currentPrice,
                    'annual_price' => $currentPrice ? $currentPrice * 10 : null,
                    'currency' => 'USD',
                    'user_limit' => $pd['users'],
                    'features' => $pd['features']
                ]);

                if ($hasChange) {
                    PriceChange::create([
                        'workspace_id' => $workspace->id,
                        'competitor_id' => $comp->id,
                        'snapshot_id' => $currentSnap->id,
                        'plan_id' => $newPlan->id,
                        'change_type' => $changeType,
                        'field' => 'monthly_price',
                        'old_value' => $oldPriceVal,
                        'new_value' => '$' . number_format($currentPrice, 2),
                        'percentage_change' => $percent,
                        'severity' => abs($percent) >= 20 ? 'high' : 'medium',
                        'detected_at' => Carbon::create(2026, 8, 19, 9, 30, 0),
                        'reviewed_at' => null
                    ]);
                    $totalChangesCount++;
                }
            }
        }

        // Generate additional mock changes to hit 30+ changes requirement
        for ($i = $totalChangesCount; $i < 35; $i++) {
            $randomComp = $competitorModels[array_rand($competitorModels)];
            $percent = rand(5, 30) * (rand(0, 1) ? 1 : -1);
            $oldPrice = rand(15, 120);
            $newPrice = round($oldPrice * (1 + $percent / 100), 2);
            
            $compSnap = PricingSnapshot::where('competitor_id', $randomComp->id)->first();

            PriceChange::create([
                'workspace_id' => $workspace->id,
                'competitor_id' => $randomComp->id,
                'snapshot_id' => $compSnap->id,
                'plan_id' => null,
                'change_type' => $percent > 0 ? 'price_increased' : 'price_decreased',
                'field' => 'monthly_price',
                'old_value' => '$' . number_format($oldPrice, 2),
                'new_value' => '$' . number_format($newPrice, 2),
                'percentage_change' => $percent,
                'severity' => abs($percent) >= 20 ? 'high' : (abs($percent) >= 10 ? 'medium' : 'low'),
                'detected_at' => Carbon::create(2026, 8, rand(1, 18), rand(0, 23), rand(0, 59)),
                'reviewed_at' => rand(0, 1) ? Carbon::now()->subHours(rand(1, 48)) : null,
                'reviewed_by' => rand(0, 1) ? $owner->id : null
            ]);
        }
    }
}
