<?php

namespace Tests\Unit;

use App\Services\Pricing\PricingExtractorService;
use Tests\TestCase;

class PricingExtractorTest extends TestCase
{
    /**
     * Test generic DOM parsing and pricing card extraction.
     */
    public function test_pricing_extraction_from_html(): void
    {
        // GP - 19-08-2026 code comment - Test pricing extractor parser logic
        
        $html = '
        <html>
        <body>
            <div class="pricing-card">
                <h2>Pro Plan</h2>
                <div class="price">$49/month</div>
                <div class="description">Ideal for scaling teams.</div>
                <ul>
                    <li>10 active users</li>
                    <li>Advanced API logs</li>
                    <li>24/7 Priority support</li>
                </ul>
            </div>
            <div class="pricing-card">
                <h2>Enterprise</h2>
                <div class="price">Contact sales</div>
                <div class="description">Custom security parameters.</div>
                <ul>
                    <li>SSO auth</li>
                    <li>Dedicated nodes</li>
                </ul>
            </div>
        </body>
        </html>
        ';

        $extractor = new PricingExtractorService();
        $plans = $extractor->extract($html);

        $this->assertCount(2, $plans);

        // Assert Pro Plan
        $pro = collect($plans)->firstWhere('slug', 'pro-plan');
        $this->assertNotNull($pro);
        $this->assertEquals(49.00, $pro['monthly_price']);
        $this->assertEquals('USD', $pro['currency']);
        $this->assertContains('10 active users', $pro['features']);

        // Assert Enterprise Plan
        $enterprise = collect($plans)->firstWhere('slug', 'enterprise');
        $this->assertNotNull($enterprise);
        $this->assertNull($enterprise['monthly_price']);
        $this->assertContains('SSO auth', $enterprise['features']);
    }
}
