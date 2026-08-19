<?php

namespace App\Services\Pricing;

use App\Models\PricingPlan;
use Illuminate\Support\Str;

class ChangeDetectionService
{
    /**
     * Compare pricing snapshots and identify differences.
     *
     * @param array $previousPlans Array of standard Eloquent plans or raw plan arrays
     * @param array $currentPlans Array of raw extracted plan arrays
     * @return array List of changes (e.g. [['plan_name' => 'Pro', 'change_type' => 'price_increased', ...]])
     */
    public function detectChanges(array $previousPlans, array $currentPlans): array
    {
        // GP - 19-08-2026 code comment - Price change detection logic
        
        $changes = [];
        
        // Map previous plans by slug/name for easy matching
        $prevMap = [];
        foreach ($previousPlans as $p) {
            $slug = is_array($p) ? ($p['slug'] ?? Str::slug($p['name'])) : $p->slug;
            $prevMap[$slug] = $p;
        }

        // Map current plans by slug/name
        $currMap = [];
        foreach ($currentPlans as $c) {
            $slug = $c['slug'] ?? Str::slug($c['name']);
            $currMap[$slug] = $c;
        }

        // 1. Check for modified plans and added plans
        foreach ($currentPlans as $curr) {
            $slug = $curr['slug'] ?? Str::slug($curr['name']);
            
            if (!isset($prevMap[$slug])) {
                // Plan added
                $changes[] = [
                    'plan_name' => $curr['name'],
                    'plan_slug' => $slug,
                    'change_type' => 'plan_added',
                    'field' => 'plan',
                    'old_value' => null,
                    'new_value' => $curr['monthly_price'] ? "\${$curr['monthly_price']}" : 'Custom',
                    'percentage_change' => null,
                    'severity' => 'medium',
                    'notes' => "New plan '{$curr['name']}' was added to pricing tiers."
                ];
                continue;
            }

            $prev = $prevMap[$slug];
            $prevMonthly = is_array($prev) ? ($prev['monthly_price'] ?? null) : $prev->monthly_price;
            $currMonthly = $curr['monthly_price'] ?? null;

            // Check price differences
            if ($prevMonthly !== null && $currMonthly !== null && $prevMonthly != $currMonthly) {
                $diff = $currMonthly - $prevMonthly;
                $pct = ($prevMonthly > 0) ? ($diff / $prevMonthly) * 100 : 0;
                $changeType = ($diff > 0) ? 'price_increased' : 'price_decreased';
                $severity = ($pct > 15 && $diff > 0) ? 'high' : (($diff > 0) ? 'medium' : 'low');

                $changes[] = [
                    'plan_name' => $curr['name'],
                    'plan_slug' => $slug,
                    'change_type' => $changeType,
                    'field' => 'monthly_price',
                    'old_value' => "\${$prevMonthly}",
                    'new_value' => "\${$currMonthly}",
                    'percentage_change' => $pct,
                    'severity' => $severity,
                    'notes' => "Plan '{$curr['name']}' monthly price changed from \${$prevMonthly} to \${$currMonthly}."
                ];
            }

            // Check limit changes
            $prevLimit = is_array($prev) ? ($prev['user_limit'] ?? null) : $prev->user_limit;
            $currLimit = $curr['user_limit'] ?? null;

            if ($prevLimit !== $currLimit) {
                $changeType = 'limit_changed';
                $severity = ($prevLimit > $currLimit && $currLimit !== null) ? 'high' : 'medium'; // Limit reduction is severe
                
                $oldVal = $prevLimit === 999999 ? 'Unlimited' : ($prevLimit ?? '—');
                $newVal = $currLimit === 999999 ? 'Unlimited' : ($currLimit ?? '—');

                $changes[] = [
                    'plan_name' => $curr['name'],
                    'plan_slug' => $slug,
                    'change_type' => $changeType,
                    'field' => 'user_limit',
                    'old_value' => (string) $oldVal,
                    'new_value' => (string) $newVal,
                    'percentage_change' => null,
                    'severity' => $severity,
                    'notes' => "Plan '{$curr['name']}' user limit changed from {$oldVal} to {$newVal}."
                ];
            }

            // Check features count/lists changes
            $prevFeatures = is_array($prev) ? ($prev['features'] ?? []) : ($prev->features ?? []);
            $currFeatures = $curr['features'] ?? [];
            
            sort($prevFeatures);
            sort($currFeatures);
            
            if ($prevFeatures !== $currFeatures) {
                $changes[] = [
                    'plan_name' => $curr['name'],
                    'plan_slug' => $slug,
                    'change_type' => 'feature_changed',
                    'field' => 'features',
                    'old_value' => count($prevFeatures) . ' features',
                    'new_value' => count($currFeatures) . ' features',
                    'percentage_change' => null,
                    'severity' => 'low',
                    'notes' => "Plan '{$curr['name']}' features items list changed."
                ];
            }
        }

        // 2. Check for removed plans
        foreach ($prevMap as $slug => $prev) {
            if (!isset($currMap[$slug])) {
                $name = is_array($prev) ? $prev['name'] : $prev->name;
                $price = is_array($prev) ? ($prev['monthly_price'] ?? null) : $prev->monthly_price;
                
                $changes[] = [
                    'plan_name' => $name,
                    'plan_slug' => $slug,
                    'change_type' => 'plan_removed',
                    'field' => 'plan',
                    'old_value' => $price ? "\${$price}" : 'Custom',
                    'new_value' => null,
                    'percentage_change' => null,
                    'severity' => 'high',
                    'notes' => "Plan '{$name}' was removed from pricing options."
                ];
            }
        }

        return $changes;
    }
}
