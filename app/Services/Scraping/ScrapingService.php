<?php

namespace App\Services\Scraping;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScrapingService
{
    /**
     * Fetch the pricing page content safely.
     *
     * @param string $url
     * @return array [success => bool, content => string, status => int, error => string]
     */
    public function fetchPricingPage(string $url): array
    {
        // GP - 19-08-2026 code comment - Safely scrape competitor pricing pages
        
        // 1. SSRF Validation
        if (!$this->isUrlSafe($url)) {
            Log::warning("Blocked potential SSRF attack attempt to URL: {$url}");
            return [
                'success' => false,
                'content' => '',
                'status' => 400,
                'error' => 'SSRF validation failed: The specified URL points to a restricted network range.'
            ];
        }

        try {
            $userAgent = config('app.pricewatch_user_agent', 'PriceWatch Bot/1.0 (+https://pricewatch.co)');
            
            // 2. Fetch page using Laravel HTTP Client
            $response = Http::withHeaders([
                'User-Agent' => $userAgent,
                'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            ])
            ->timeout(15) // Connect + response timeout
            ->connectTimeout(5)
            ->withoutRedirecting() // Do not automatically follow redirects to prevent SSRF redirect-chain bypass
            ->get($url);

            // Handle redirect checks manually
            if ($response->redirect()) {
                $redirectUrl = $response->header('Location');
                
                // Resolve relative redirect URL to absolute
                if ($redirectUrl && !preg_match('/^https?:\/\//i', $redirectUrl)) {
                    $originalParts = parse_url($url);
                    $scheme = $originalParts['scheme'] ?? 'https';
                    $host = $originalParts['host'] ?? '';
                    $port = isset($originalParts['port']) ? ':' . $originalParts['port'] : '';
                    
                    if (str_starts_with($redirectUrl, '/')) {
                        $redirectUrl = "{$scheme}://{$host}{$port}{$redirectUrl}";
                    } else {
                        $path = $originalParts['path'] ?? '/';
                        $dir = dirname($path);
                        $dir = $dir === '\\' || $dir === '/' ? '/' : $dir . '/';
                        $redirectUrl = "{$scheme}://{$host}{$port}{$dir}{$redirectUrl}";
                    }
                }
                
                // Validate redirect URL safety
                if (!$redirectUrl || !$this->isUrlSafe($redirectUrl)) {
                    return [
                        'success' => false,
                        'content' => '',
                        'status' => 400,
                        'error' => 'Redirect blocked: The redirect destination is invalid or points to a private network.'
                    ];
                }

                // Follow redirect once (max 1 redirect for safety)
                $response = Http::withHeaders(['User-Agent' => $userAgent])
                    ->timeout(10)
                    ->connectTimeout(5)
                    ->withoutRedirecting()
                    ->get($redirectUrl);
            }

            if ($response->successful()) {
                return [
                    'success' => true,
                    'content' => $response->body(),
                    'status' => $response->status(),
                    'error' => ''
                ];
            }

            return [
                'success' => false,
                'content' => '',
                'status' => $response->status(),
                'error' => "HTTP status error: {$response->status()}"
            ];

        } catch (\Exception $e) {
            Log::error("Scraping failed for URL {$url}: " . $e->getMessage());
            return [
                'success' => false,
                'content' => '',
                'status' => 500,
                'error' => 'Network request failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate URL scheme and IP address to prevent SSRF vulnerabilities.
     *
     * @param string $url
     * @return bool
     */
    public function isUrlSafe(string $url): bool
    {
        // GP - 19-08-2026 code comment - SSRF url validator
        
        $parts = parse_url($url);
        
        // Only allow HTTP and HTTPS
        if (!isset($parts['scheme']) || !in_array(strtolower($parts['scheme']), ['http', 'https'])) {
            return false;
        }

        $host = $parts['host'] ?? null;
        if (!$host) {
            return false;
        }

        // DNS Lookup to resolve all IP addresses for the host
        $ips = @gethostbynamel($host);
        if (!$ips) {
            // Check if host is direct IP
            if (filter_var($host, FILTER_VALIDATE_IP)) {
                $ips = [$host];
            } else {
                return false;
            }
        }

        foreach ($ips as $ip) {
            if (!$this->isIpSafe($ip)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a resolved IP address is public and safe.
     *
     * @param string $ip
     * @return bool
     */
    private function isIpSafe(string $ip): bool
    {
        // GP - 19-08-2026 code comment - SSRF IP checker
        
        // filter_var returns the ip if valid, or false if not.
        // FILTER_FLAG_NO_PRIV_RANGE: Block private class networks (10.0.0.0/8, 172.16.0.0/12, 192.168.0.0/16)
        // FILTER_FLAG_NO_RES_RANGE: Block loopback and reserved ranges (127.0.0.0/8, 0.0.0.0/8, fc00::/7 etc)
        $validated = filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );

        return $validated !== false;
    }
}
