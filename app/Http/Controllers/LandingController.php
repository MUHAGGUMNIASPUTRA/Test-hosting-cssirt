<?php
// File: app/Http/Controllers/LandingController.php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class LandingController extends Controller
{
  /**
   * Display the landing page.
   *
   * @return \Inertia\Response
   */
  public function __invoke(): Response
  {
    // Get the 3 latest published posts
    $posts = Post::where('status', 'Published')
      ->latest('published_at')
      ->take(3)
      ->get();

    // Get all active services
    $services = Service::where('is_active', true)->get();

    // Pass the data to the Vue component
    return Inertia::render('Landing', [
      'posts' => $posts,
      'services' => $services,
    ]);
  }
}
