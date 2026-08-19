<?php

namespace Tests\Unit;

use App\Services\Scraping\ScrapingService;
use Tests\TestCase;

class ScrapingServiceTest extends TestCase
{
    /**
     * Test that private IP ranges and loopback domains are detected as unsafe (SSRF protection).
     */
    public function test_loopback_and_private_ips_blocked(): void
    {
        // GP - 19-08-2026 code comment - Test loopback and private IP blocks
        
        $scraper = new ScrapingService();

        // Safe URL
        $this->assertTrue($scraper->isUrlSafe('https://stripe.com/pricing'));
        $this->assertTrue($scraper->isUrlSafe('https://paddle.com/pricing'));

        // Unsafe loopbacks
        $this->assertFalse($scraper->isUrlSafe('http://127.0.0.1/pricing'));
        $this->assertFalse($scraper->isUrlSafe('http://localhost/pricing'));
        $this->assertFalse($scraper->isUrlSafe('http://[::1]/pricing'));

        // Unsafe private networks
        $this->assertFalse($scraper->isUrlSafe('http://192.168.1.1/pricing'));
        $this->assertFalse($scraper->isUrlSafe('http://10.0.0.1/pricing'));
        $this->assertFalse($scraper->isUrlSafe('http://172.16.0.1/pricing'));
        
        // Link-local
        $this->assertFalse($scraper->isUrlSafe('http://169.254.169.254/latest/meta-data/'));
    }
}
