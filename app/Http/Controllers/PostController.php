<?php
// File: app/Http/Controllers/PostController.php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesSeoRequests;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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

    $query = Post::with('categories')
      ->where('status', 'Published');

    // Apply search filter
    if ($request->filled('search')) {
      $searchTerm = $request->search;
      $query->where(function($q) use ($searchTerm) {
        $q->where('title', 'ilike', '%' . $searchTerm . '%')
          ->orWhere('excerpt', 'ilike', '%' . $searchTerm . '%')
          ->orWhere('body', 'ilike', '%' . $searchTerm . '%')
          ->orWhereHas('categories', function($categoryQuery) use ($searchTerm) {
            $categoryQuery->where('name', 'ilike', '%' . $searchTerm . '%');
          })
          ->orWhereHas('tags', function($tagQuery) use ($searchTerm) {
            $tagQuery->where('name', 'ilike', '%' . $searchTerm . '%');
          });
      });
      $perPage = 6;
    }

    $posts = $query->latest('published_at')
      ->paginate($perPage)
      ->withQueryString();

    return $this->handleSeoRequest('Posts/Index', [
      'posts' => $posts,
      'isFirstPage' => $isFirstPage,
      'filters' => $request->only(['search']),
    ]);
  }

  /**
   * Display the specified post.
   */
  public function show(Request $request, Post $post): Response
  {
    $post->increment('views_count');

    // Check if the current user/guest has already rated this post
    $hasRated = false;
    $user = $request->user();

    if ($user) {
      $hasRated = $post->ratings()->where('user_id', $user->id)->exists();
    } else {
      $hasRated = $post->ratings()->where('ip_address', $request->ip())->exists();
    }

    $popularPosts = Post::where('status', 'Published')
      ->where('id', '!=', $post->id)
      ->orderBy('views_count', 'desc')
      ->take(4)
      ->get();

    return Inertia::render('Posts/Show', [
      'post' => $post->load(['categories', 'tags']),
      'popularPosts' => $popularPosts,
      'hasRated' => $hasRated,
    ]);
  }
}
