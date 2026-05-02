<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SeoService
{
    private string $ssrServerUrl;

    private array $seoComponentMapping;

    public function __construct(?string $ssrUrl = null)
    {
        $this->ssrServerUrl = $ssrUrl ?? config('app.ssr_url', 'http://localhost:13714');
        $this->seoComponentMapping = [
            'Landing' => 'SEOLanding',
            'Services/Index' => 'SEOServices',
            'Posts/Index' => 'SEOPosts',
            'Posts/Show' => 'SEOPostShow',
            'Categories/Show' => 'SEOCategoryShow',
            'Contact/Index' => 'SEOContact',
            'Faq/Index' => 'SEOFAQ',
            'Incidents/Create' => 'SEOIncident',
            'Profile/Index' => 'SEOProfile',
        ];
    }

    /**
     * Check if the current request is from a search engine crawler
     */
    public function isSearchEngineCrawler(Request $request): bool
    {
        $userAgent = $request->header('User-Agent', '');

        // Common search engine bot patterns
        $botPatterns = [
            'googlebot',
            'bingbot',
            'slurp',
            'duckduckbot',
            'baiduspider',
            'yandexbot',
            'facebookexternalhit',
            'twitterbot',
            'linkedinbot',
            'whatsapp',
            'telegrambot',
            'crawl',
            'spider',
            'bot',
            'meta',
            'fetch',
        ];

        foreach ($botPatterns as $pattern) {
            if (stripos($userAgent, $pattern) !== false) {
                return true;
            }
        }

        // Check for specific SEO testing parameter
        if ($request->has('_seo_test')) {
            return true;
        }

        return false;
    }

    /**
     * Get SEO component name for given component
     */
    public function getSeoComponent(string $component): ?string
    {
        return $this->seoComponentMapping[$component] ?? null;
    }

    /**
     * Render SEO version of the component
     */
    public function renderSeoVersion(string $component, array $props = []): string
    {
        try {
            // Prepare the page data in the format expected by Inertia SSR
            $pageData = [
                'component' => $component,
                'props' => $props,
                'url' => request()->url(),
                'version' => '1.0',
            ];

            Log::info('SeoService: Rendering SEO version', [
                'component' => $component,
                'props_keys' => array_keys($props),
                'ssr_url' => $this->ssrServerUrl,
            ]);

            // Make request to SSR server with POST to /render endpoint
            $response = Http::timeout(30)->post($this->ssrServerUrl.'/render', [
                'page' => $pageData,
            ]);

            if ($response->successful()) {
                $responseData = $response->json();

                // Check if we got HTML in the response
                if (isset($responseData['html'])) {
                    $html = $responseData['html'];

                    // Wrap with full HTML document structure
                    return $this->wrapWithDocumentStructure($html, $component, $props);
                }

                Log::warning('SeoService: SSR response missing HTML', [
                    'response' => $responseData,
                ]);
            } else {
                Log::warning('SeoService: SSR server returned error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
            }
        } catch (\Exception $e) {
            Log::error('SeoService: Failed to render SEO version', [
                'component' => $component,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        // Return fallback basic HTML if SSR fails
        return $this->createFallbackHtml($component, $props);
    }

    /**
     * Wrap the rendered HTML with proper document structure
     */
    private function wrapWithDocumentStructure(string $html, string $component, array $props): string
    {
        $title = $this->extractTitle($component, $props);
        $description = $this->extractDescription($component, $props);
        $canonicalUrl = request()->url();

        return "<!DOCTYPE html>
<html lang=\"id\">
<head>
  <meta charset=\"utf-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
  <title>{$title}</title>
  <meta name=\"description\" content=\"{$description}\">
  <meta name=\"robots\" content=\"index, follow\">
  <link rel=\"canonical\" href=\"{$canonicalUrl}\">

  <!-- Open Graph -->
  <meta property=\"og:title\" content=\"{$title}\">
  <meta property=\"og:description\" content=\"{$description}\">
  <meta property=\"og:url\" content=\"{$canonicalUrl}\">
  <meta property=\"og:type\" content=\"website\">
  <meta property=\"og:site_name\" content=\"CSIRT Bojonegoro\">

  <!-- Twitter Card -->
  <meta name=\"twitter:card\" content=\"summary_large_image\">
  <meta name=\"twitter:title\" content=\"{$title}\">
  <meta name=\"twitter:description\" content=\"{$description}\">

  <!-- Styles -->
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; line-height: 1.6; }
    .container { max-width: 1200px; margin: 0 auto; padding: 0 1rem; }
  </style>
</head>
<body>
  {$html}
</body>
</html>";
    }

    /**
     * Extract title from component and props
     */
    private function extractTitle(string $component, array $props): string
    {
        $baseTitle = 'CSIRT Bojonegoro - Computer Security Incident Response Team';

        switch ($component) {
            case 'Landing':
                return 'CSIRT Bojonegoro - Keamanan Siber Pemerintah Kabupaten Bojonegoro';
            case 'Services/Index':
                return 'Layanan CSIRT Bojonegoro - Keamanan Siber Profesional 24/7';
            case 'Posts/Index':
                return 'Artikel Keamanan Siber - CSIRT Bojonegoro';
            case 'Posts/Show':
                return ($props['post']['title'] ?? 'Artikel').' - CSIRT Bojonegoro';
            case 'Categories/Show':
                return 'Kategori: '.($props['category']['name'] ?? 'Artikel').' - CSIRT Bojonegoro';
            case 'Contact/Index':
                return 'Hubungi Kami - CSIRT Bojonegoro';
            case 'Faq/Index':
                return 'FAQ - Pertanyaan yang Sering Diajukan - CSIRT Bojonegoro';
            case 'Incidents/Create':
                return 'Lapor Insiden Keamanan Siber - CSIRT Bojonegoro';
            case 'Profile/Index':
                return 'Profil CSIRT Bojonegoro - Computer Security Incident Response Team';
            default:
                return $baseTitle;
        }
    }

    /**
     * Extract description from component and props
     */
    private function extractDescription(string $component, array $props): string
    {
        switch ($component) {
            case 'Landing':
                return 'CSIRT Bojonegoro adalah tim respons insiden keamanan siber yang melindungi aset digital Pemerintah Kabupaten Bojonegoro dengan layanan 24/7, monitoring keamanan, dan respons cepat terhadap ancaman siber.';
            case 'Services/Index':
                return 'Layanan keamanan siber profesional CSIRT Bojonegoro meliputi monitoring 24/7, respons insiden, penilaian kerentanan, pelatihan, forensik digital, dan konsultasi keamanan untuk pemerintahan.';
            case 'Posts/Index':
                return 'Kumpulan artikel, panduan, dan informasi terkini seputar keamanan siber, best practices, dan tips untuk melindungi sistem informasi dari ancaman cyber.';
            case 'Posts/Show':
                return $props['post']['excerpt'] ?? 'Artikel keamanan siber dari CSIRT Bojonegoro dengan informasi dan panduan untuk meningkatkan keamanan sistem informasi.';
            case 'Categories/Show':
                return 'Artikel dalam kategori '.($props['category']['name'] ?? 'keamanan siber').' - kumpulan informasi dan panduan terkait keamanan sistem informasi dari CSIRT Bojonegoro.';
            case 'Contact/Index':
                return 'Hubungi CSIRT Bojonegoro untuk layanan keamanan siber, konsultasi, pelaporan insiden, atau informasi lebih lanjut. Tim siap membantu 24/7.';
            case 'Faq/Index':
                return 'Pertanyaan yang sering diajukan seputar layanan CSIRT Bojonegoro, keamanan siber, pelaporan insiden, dan cara melindungi sistem informasi.';
            case 'Incidents/Create':
                return 'Laporkan insiden keamanan siber ke CSIRT Bojonegoro. Tim respons siap membantu 24/7 untuk menangani ancaman dan serangan siber secara profesional.';
            case 'Profile/Index':
                return 'Profil lengkap CSIRT Bojonegoro - Computer Security Incident Response Team Kabupaten Bojonegoro yang bertugas melindungi aset digital pemerintahan.';
            default:
                return 'CSIRT Bojonegoro - Computer Security Incident Response Team Kabupaten Bojonegoro, melindungi aset digital pemerintahan dengan layanan keamanan siber professional.';
        }
    }

    /**
     * Check if SSR server is available
     */
    public function isSSRServerAvailable(): bool
    {
        try {
            $response = Http::timeout(3)->get($this->ssrServerUrl);

            return $response->status() !== 500;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create a basic fallback HTML when SSR fails
     */
    private function createFallbackHtml(string $component, array $props): string
    {
        $title = $this->extractTitle($component, $props);
        $description = $this->extractDescription($component, $props);

        return "<!DOCTYPE html>
<html lang=\"id\">
<head>
  <meta charset=\"UTF-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
  <title>{$title}</title>
  <meta name=\"description\" content=\"{$description}\">
  <meta name=\"robots\" content=\"index, follow\">
</head>
<body>
  <h1>{$title}</h1>
  <p>{$description}</p>
  <p>Konten sedang dimuat...</p>
</body>
</html>";
    }
}
