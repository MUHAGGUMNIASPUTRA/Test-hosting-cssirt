<?php
// File: app/Http/Controllers/PostController.php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesSeoRequests;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
  use HandlesSeoRequests;

  /**
   * Display a listing of the posts.
   */
  public function index(Request $request)
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

    return $this->handleSeoRequest('Posts/Index', [
      'posts' => $posts,
      'isFirstPage' => $isFirstPage,
    ]);
  }

  /**
   * Display the specified post.
   */
  public function show(Request $request, $slug)
  {
    $post = Post::with(['category', 'tags'])
      ->where('status', 'published')
      ->where('slug', $slug)
      ->firstOrFail();

    $hasRated = false;
    $user = $request->user();

    if ($user) {
      $hasRated = $post->ratings()->where('user_id', $user->id)->exists();
    } else {
      // For guest users, check by IP
      $hasRated = $post->ratings()->where('ip_address', $request->ip())->exists();
    }

    $postWithRating = $post;
    $postWithRating->has_rated = $hasRated;

    return $this->handleSeoRequest('Posts/Show', [
      'post' => $postWithRating
    ]);
  }
}
