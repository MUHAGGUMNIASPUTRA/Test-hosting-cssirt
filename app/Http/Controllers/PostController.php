<?php
// File: app/Http/Controllers/PostController.php

namespace App\Http\Controllers;

use App\Models\Post;
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
        ->paginate(9)
        ->withQueryString(),
    ]);
  }

  /**
   * Display the specified post.
   *
   * @param  \App\Models\Post  $post
   * @return \Inertia\Response
   */
  public function show(Post $post): Response
  {
    // Increment the views_count
    $post->increment('views_count');

    // Get 4 recent posts, excluding the current one
    $recentPosts = Post::where('status', 'Published')
      ->where('id', '!=', $post->id)
      ->latest('published_at')
      ->take(4)
      ->get();

    return Inertia::render('Posts/Show', [
      'post' => $post->load(['categories', 'tags']),
      'recentPosts' => $recentPosts,
    ]);
  }
}
