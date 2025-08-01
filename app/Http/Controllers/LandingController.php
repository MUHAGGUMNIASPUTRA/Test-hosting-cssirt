<?php
// File: app/Http/Controllers/LandingController.php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesSeoRequests;
use App\Models\Post;
use App\Models\Service;
use Inertia\Response;

class LandingController extends Controller
{
  use HandlesSeoRequests;
  /**
   * Display the landing page.
   */
  public function __invoke()
  {
    // Get the 3 latest published posts
    $posts = Post::where('status', 'Published')
      ->latest('published_at')
      ->take(3)
      ->get();

    // Get all active services
    $services = Service::where('is_active', true)->get();

    $props = [
      'posts' => $posts,
      'services' => $services,
    ];

    // Check for force SEO parameter (for testing)
    if (request('force_seo')) {
      return $this->forceSeoRender('Landing', $props);
    }

    // Pass the data to the Vue component with SEO handling
    return $this->handleSeoRequest('Landing', $props);
  }
}
