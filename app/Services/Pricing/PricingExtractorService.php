<?php

namespace App\Services\Pricing;

use Illuminate\Support\Str;

class PricingExtractorService implements PricingExtractorInterface
{
    /**
     * Parse HTML pricing page content and extract plans.
     *
     * @param string $html
     * @return array
     */
    public function extract(string $html): array
    {
        // GP - 19-08-2026 code comment - Extract pricing details from public HTML
        
        $plans = [];
        
        if (empty($html)) {
            return $plans;
        }

        // Clean up head and scripts to prevent matching false-positive numbers
        $html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);
        $html = preg_replace('/<head\b[^>]*>(.*?)<\/head>/is', '', $html);

        // Native DOM Document parser
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html);
        $xpath = new \DOMXPath($dom);

        // Common plan naming indicators
        $tierNames = ['free', 'starter', 'basic', 'pro', 'professional', 'growth', 'business', 'enterprise', 'startup', 'agency', 'team', 'developer'];

        // Let's find all headers (h2, h3, h4) representing pricing cards
        $headers = $xpath->query('//h1 | //h2 | //h3 | //h4 | //h5 | //strong | //span[contains(@class, "title") or contains(@class, "name")]');
        
        foreach ($headers as $header) {
            $text = trim($header->nodeValue);
            $cleanText = strtolower($text);

            // If header name matches common tier names, let's look for pricing around it
            if (in_array($cleanText, $tierNames) || (strlen($text) > 2 && strlen($text) < 25 && preg_match('/^(free|basic|pro|growth|enterprise|business|starter|team|premium)/i', $text))) {
                
                // Let's navigate up to find container parent element
                $parent = $header->parentNode;
                $pricingCardHtml = '';
                
                // Traverse up to 4 levels to find a plan card context
                for ($i = 0; $i < 4; $i++) {
                    if ($parent) {
                        $class = $parent->hasAttribute('class') ? strtolower($parent->getAttribute('class')) : '';
                        if (str_contains($class, 'card') || str_contains($class, 'tier') || str_contains($class, 'plan') || str_contains($class, 'pricing')) {
                            break;
                        }
                        $parent = $parent->parentNode;
                    }
                }

                $parentContext = $parent ?: $header->parentNode;
                $contextText = $parentContext ? $parentContext->nodeValue : '';
                
                // Search for prices in the parent container
                $price = $this->extractPrice($contextText);
                $features = $this->extractFeatures($parentContext, $xpath);
                $billing = $this->extractBillingPeriod($contextText);

                $plans[] = [
                    'name' => ucfirst($text),
                    'slug' => Str::slug($text),
                    'monthly_price' => $price['monthly'] ?? null,
                    'annual_price' => $price['annual'] ?? null,
                    'currency' => $price['currency'] ?? 'USD',
                    'billing_period' => $billing,
                    'description' => $price['is_custom'] ? 'Contact sales for custom pricing.' : "Standard {$text} tier pricing.",
                    'user_limit' => $this->extractUserLimits($contextText),
                    'features' => $features
                ];
            }
        }

        // De-duplicate plans by slug/name
        $uniquePlans = [];
        foreach ($plans as $plan) {
            if (!isset($uniquePlans[$plan['slug']])) {
                $uniquePlans[$plan['slug']] = $plan;
            }
        }

        return array_values($uniquePlans);
    }

    /**
     * Extract prices from text context.
     *
     * @param string $text
     * @return array [monthly, annual, currency, is_custom]
     */
    private function extractPrice(string $text): array
    {
        // GP - 19-08-2026 code comment - Extract price from text
        
        $result = [
            'monthly' => null,
            'annual' => null,
            'currency' => 'USD',
            'is_custom' => false
        ];

        if (preg_match('/(contact sales|custom pricing|talk to sales|let\'s talk|contact us|get in touch|request a quote)/i', $text)) {
            $result['is_custom'] = true;
            return $result;
        }

        // Match currency signs
        if (str_contains($text, '€')) $result['currency'] = 'EUR';
        elseif (str_contains($text, '£')) $result['currency'] = 'GBP';

        // Match price patterns like $49, $99/mo, $990/year, etc.
        // Let's find all instances of digits following a currency symbol or word
        if (preg_match_all('/(?:[\$\€\£]|usd|eur|gbp)\s*(\d{1,5}(?:\.\d{2})?)/i', $text, $matches)) {
            $prices = array_map('floatval', $matches[1]);
            
            // Check context for billing words (annual/yearly vs monthly)
            foreach ($prices as $p) {
                if ($p > 150) {
                    $result['annual'] = $p;
                } else {
                    $result['monthly'] = $p;
                }
            }
        }

        // Fallback: match simple digits
        if ($result['monthly'] === null && $result['annual'] === null) {
            if (preg_match('/(?:^|\s)(\d{1,3})(?:\/mo|per month|\s+a month)/i', $text, $match)) {
                $result['monthly'] = floatval($match[1]);
            }
        }

        // Fill annual if empty using monthly multiplier
        if ($result['monthly'] !== null && $result['annual'] === null) {
            $result['annual'] = $result['monthly'] * 10; // Assume 2 months free discount
        }

        return $result;
    }

    /**
     * Extract plan features from DOM element.
     *
     * @param \DOMNode|null $contextNode
     * @param \DOMXPath $xpath
     * @return array
     */
    private function extractFeatures(?\DOMNode $contextNode, \DOMXPath $xpath): array
    {
        // GP - 19-08-2026 code comment - Extract lists of features from plans cards
        
        $features = [];
        if (!$contextNode) {
            return $features;
        }

        // Query list items within this card element
        $listItems = $xpath->query('.//li', $contextNode);
        foreach ($listItems as $item) {
            $val = trim($item->nodeValue);
            if (!empty($val) && strlen($val) < 100) {
                $features[] = $val;
            }
        }

        // If no list items, try splitting lines of text
        if (empty($features)) {
            $lines = explode("\n", $contextNode->nodeValue);
            foreach ($lines as $line) {
                $line = trim($line);
                if (strlen($line) > 5 && strlen($line) < 60 && !str_starts_with($line, '$') && !is_numeric($line)) {
                    $features[] = $line;
                }
            }
        }

        return array_slice($features, 0, 8); // Max 8 features for summary
    }

    /**
     * Extract billing period from context.
     *
     * @param string $text
     * @return string
     */
    private function extractBillingPeriod(string $text): string
    {
        // GP - 19-08-2026 code comment - Parse billing schedule type
        
        $text = strtolower($text);
        if (str_contains($text, 'annual') || str_contains($text, 'yearly') || str_contains($text, '/yr')) {
            return 'annual';
        }
        return 'monthly';
    }

    /**
     * Extract user limit from card text.
     *
     * @param string $text
     * @return int|null
     */
    private function extractUserLimits(string $text): ?int
    {
        // GP - 19-08-2026 code comment - Parse user seat limit numbers
        
        if (preg_match('/(\d+)\s*(?:active\s*)?users/i', $text, $match)) {
            return intval($match[1]);
        }
        if (preg_match('/limit(?:ed)?\s*to\s*(\d+)/i', $text, $match)) {
            return intval($match[1]);
        }
        if (str_contains(strtolower($text), 'unlimited users') || str_contains(strtolower($text), 'unlimited seats')) {
            return 999999; // Unlimited
        }
        return null;
    }
}
