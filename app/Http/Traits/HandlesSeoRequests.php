<?php

namespace App\Http\Traits;

use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Inertia\Inertia;

trait HandlesSeoRequests
{
    /**
     * Handle SEO request or return regular Inertia response
     */
    protected function handleSeoRequest(string $component, array $props = []): mixed
    {
        $seoService = app(SeoService::class);
        $request = request();

        // Check if this is a crawler request or force_seo parameter
        if ($seoService->isSearchEngineCrawler($request) || $request->has('force_seo')) {
            // Don't cache pages with CSRF-protected forms (they contain tokens tied to sessions)
            $noCacheForms = ['Incidents/Create'];
            if (! in_array($component, $noCacheForms)) {
                // Try to render SEO version
                $seoHtml = $seoService->renderSeoVersion($component, $props);

                if ($seoHtml) {
                    return response($seoHtml)
                        ->header('Content-Type', 'text/html; charset=utf-8')
                        ->header('X-Robots-Tag', 'index, follow')
                        ->header('Cache-Control', 'public, max-age=3600');
                }
            }

            // Fallback to regular response if SEO fails or component shouldn't be cached
        }

        // Return regular Inertia response
        return Inertia::render($component, $props);
    }

    /**
     * Force SEO rendering (for testing)
     */
    protected function forceSeoRender(string $component, array $props = []): Response
    {
        $seoService = app(SeoService::class);
        $seoHtml = $seoService->renderSeoVersion($component, $props);

        if (! $seoHtml) {
            abort(500, 'SEO rendering failed');
        }

        return response($seoHtml)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('X-SEO-Rendered', 'true');
    }
}
