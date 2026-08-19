<?php

namespace App\Services\Pricing;

interface PricingExtractorInterface
{
    /**
     * Parse HTML pricing page content and extract plans.
     *
     * @param string $html
     * @return array Array of plans (e.g. [['name' => 'Pro', 'monthly_price' => 49.00, ...]])
     */
    public function extract(string $html): array;
}
