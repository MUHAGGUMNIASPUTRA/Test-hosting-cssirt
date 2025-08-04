<?php
// File: app/Http/Controllers/PostController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request): Response
  {
    $query = Post::with(['categories', 'tags'])
      ->latest();

    // Apply search filter
    if ($request->filled('search')) {
      $query->where('title', 'ilike', '%' . $request->search . '%')
        ->orWhere('excerpt', 'ilike', '%' . $request->search . '%');
    }

    // Apply status filter
    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    $posts = $query->paginate(10)->withQueryString();

    return Inertia::render('Admin/Posts/Index', [
      'posts' => $posts,
      'filters' => $request->only(['search', 'status']),
    ]);
  }

  /**
   * Show the form for creating a new post.
   *
   * @return \Inertia\Response
   */
  public function create(): Response
  {
    return Inertia::render('Admin/Posts/Create', [
      'categories' => Category::orderBy('name', 'asc')->get(['id', 'name']),
      'tags' => Tag::orderBy('name', 'asc')->get(['id', 'name']),
    ]);
  }

  /**
   * Store a newly created post in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'body' => 'required|string',
      'excerpt' => 'required|string|max:500',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
      'status' => 'required|in:Draft,Published',
      'categories' => 'required|array|min:1',
      'categories.*' => 'exists:categories,id',
      'tags' => 'nullable|array',
      'tags.*' => 'exists:tags,id',
    ]);

    $path = null;
    if ($request->hasFile('image')) {
      $path = $request->file('image')->store('posts', 'public');
    }

    $post = Post::create([
      'title' => $validated['title'],
      'slug' => Str::slug($validated['title']),
      'body' => $validated['body'],
      'excerpt' => $validated['excerpt'],
      'image' => $path,
      'status' => $validated['status'],
      'published_by' => Auth::user()->name,
      'published_at' => $validated['status'] === 'Published' ? now() : null,
    ]);

    $post->categories()->sync($validated['categories']);

    if (!empty($validated['tags'])) {
      $post->tags()->sync($validated['tags']);
    }

    // Redirect to the post list (we will create this page later)
    return redirect()->route('admin.posts.index')->with('success', 'Artikel berhasil dibuat.');
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Post $post): Response
  {
    return Inertia::render('Admin/Posts/Create', [
      'post' => $post->load(['categories', 'tags']),
      'categories' => Category::orderBy('name', 'asc')->get(['id', 'name']),
      'tags' => Tag::orderBy('name', 'asc')->get(['id', 'name']),
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Post $post)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'body' => 'required|string',
      'excerpt' => 'required|string|max:500',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
      'removeImage' => 'nullable|boolean',
      'status' => 'required|in:Draft,Published',
      'categories' => 'required|array|min:1',
      'categories.*' => 'exists:categories,id',
      'tags' => 'nullable|array',
      'tags.*' => 'exists:tags,id',
    ]);

    $path = $post->image;

    // Check if user wants to remove the current image
    if ($request->boolean('removeImage')) {
      // Delete old image if it exists
      if ($post->image) {
        Storage::disk('public')->delete($post->image);
      }
      $path = null;
    }

    if ($request->hasFile('image')) {
      // Delete old image if it exists
      if ($post->image) {
        Storage::disk('public')->delete($post->image);
      }
      $path = $request->file('image')->store('posts', 'public');
    }

    $post->update([
      'title' => $validated['title'],
      'slug' => Str::slug($validated['title']),
      'body' => $validated['body'],
      'excerpt' => $validated['excerpt'],
      'image' => $path,
      'status' => $validated['status'],
      'published_at' => ($post->status === 'Draft' && $validated['status'] === 'Published') ? now() : $post->published_at,
    ]);

    $post->categories()->sync($validated['categories']);
    $post->tags()->sync($validated['tags'] ?? []);

    return redirect()->route('admin.posts.index')->with('success', 'Artikel berhasil diperbarui.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Post $post)
  {
    if ($post->image) {
      Storage::disk('public')->delete($post->image);
    }
    $post->delete();
    return redirect()->route('admin.posts.index')->with('success', 'Artikel berhasil dihapus.');
  }
}
