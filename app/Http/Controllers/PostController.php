<?php
// File: app/Http/Controllers/PostController.php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
  /**
   * Display a listing of the posts.
   */
  public function index(): Response
  {
    return Inertia::render('Posts/Index', [
      'posts' => Post::with('categories') // Eager load categories
        ->where('status', 'Published')
        ->latest('published_at')
        ->paginate(6)
        ->withQueryString(),
    ]);
  }

  /**
   * Display the specified post.
   *
   * @param  \App\Models\Post  $post
   * @return \Inertia\Response
   */
  public function show(Request $request, Post $post): Response
  {
    $post->increment('views_count');

    // Check if the current user/guest has already rated this post
    $hasRated = false;
    if (auth()->check()) {
      $hasRated = $post->ratings()->where('user_id', auth()->id())->exists();
    } else {
      $hasRated = $post->ratings()->where('ip_address', $request->ip())->exists();
    }

    $recentPosts = Post::where('status', 'Published')
      ->where('id', '!=', $post->id)
      ->latest('published_at')
      ->take(4)
      ->get();

    return Inertia::render('Posts/Show', [
      'post' => $post->load(['categories', 'tags']),
      'recentPosts' => $recentPosts,
      'hasRated' => $hasRated,
    ]);
  }
}
