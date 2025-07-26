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
  public function index(Request $request): Response
  {
    $page = $request->get('page', 1);
    $isFirstPage = $page == 1;

    // For first page: get 7 posts (1 featured + 6 regular)
    // For other pages: get 6 posts (all regular)
    $perPage = $isFirstPage ? 7 : 6;

    $posts = Post::with('categories')
      ->where('status', 'Published')
      ->latest('published_at')
      ->paginate($perPage)
      ->withQueryString();

    return Inertia::render('Posts/Index', [
      'posts' => $posts,
      'isFirstPage' => $isFirstPage,
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
