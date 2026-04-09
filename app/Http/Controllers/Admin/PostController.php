<?php

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
  public function index(Request $request): Response
  {
    $query = Post::with(['categories', 'tags'])
      ->withCount('ratings');

    if ($request->filled('search')) {
      $query->where('title', 'ilike', '%' . $request->search . '%');
    }

    if ($request->filled('status')) {
      $query->where('status', $request->status);
    }

    return Inertia::render('Admin/Posts/Index', [
      'posts' => $query->latest()->paginate(10)->withQueryString(),
      'filters' => $request->only(['search', 'status']),
    ]);
  }

  public function create(): Response
  {
    return Inertia::render('Admin/Posts/Create', [
      'categories' => Category::orderBy('name', 'asc')->get(['id', 'name']),
      'tags' => Tag::orderBy('name', 'asc')->get(['id', 'name']),
    ]);
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'body' => 'required|string',
      'excerpt' => 'required|string|max:500',
      'image_type' => 'nullable|in:file,link',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
      'image_url' => 'nullable|string|max:1000',
      'status' => 'required|in:Draft,Published',
      'categories' => 'required|array|min:1',
      'categories.*' => 'exists:categories,id',
      'tags' => 'nullable|array',
      'tags.*' => 'exists:tags,id',
    ]);

    $path = null;
    if (($validated['image_type'] ?? 'file') === 'file' && $request->hasFile('image')) {
      $path = $request->file('image')->store('posts', 'public');
    } elseif (($validated['image_type'] ?? null) === 'link' && !empty($validated['image_url'])) {
      $path = $validated['image_url'];
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

    return redirect()->route('admin.posts.index')->with('success', 'Artikel berhasil dibuat.');
  }

  public function edit(Post $post): Response
  {
    return Inertia::render('Admin/Posts/Create', [
      'post' => $post->load(['categories', 'tags']),
      'categories' => Category::orderBy('name', 'asc')->get(['id', 'name']),
      'tags' => Tag::orderBy('name', 'asc')->get(['id', 'name']),
    ]);
  }

  public function update(Request $request, Post $post)
  {
    $validated = $request->validate([
      'title' => 'required|string|max:255',
      'body' => 'required|string',
      'excerpt' => 'required|string|max:500',
      'image_type' => 'nullable|in:file,link',
      'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
      'image_url' => 'nullable|string|max:1000',
      'status' => 'required|in:Draft,Published',
      'categories' => 'required|array|min:1',
      'categories.*' => 'exists:categories,id',
      'tags' => 'nullable|array',
      'tags.*' => 'exists:tags,id',
    ]);

    $path = $post->image;
    if (($validated['image_type'] ?? 'file') === 'file' && $request->hasFile('image')) {
      // Delete old stored file if it exists (not a URL)
      if ($post->image && !str_starts_with($post->image, 'http')) {
        Storage::disk('public')->delete($post->image);
      }
      $path = $request->file('image')->store('posts', 'public');
    } elseif (($validated['image_type'] ?? null) === 'link') {
      $path = !empty($validated['image_url']) ? $validated['image_url'] : null;
    }

    $post->update([
      'title' => $validated['title'],
      'slug' => Str::slug($validated['title']),
      'body' => $validated['body'],
      'excerpt' => $validated['excerpt'],
      'image' => $path,
      'status' => $validated['status'],
      'published_by' => Auth::user()->name,
      'published_at' => $validated['status'] === 'Published' ? ($post->published_at ?? now()) : null,
    ]);

    $post->categories()->sync($validated['categories']);

    if (!empty($validated['tags'])) {
      $post->tags()->sync($validated['tags']);
    }

    return redirect()->route('admin.posts.index')->with('success', 'Artikel berhasil diperbarui.');
  }

  public function destroy(Post $post)
  {
    if ($post->image && !str_starts_with($post->image, 'http')) {
      Storage::disk('public')->delete($post->image);
    }

    $post->categories()->detach();
    $post->tags()->detach();
    $post->delete();

    return redirect()->route('admin.posts.index')->with('success', 'Artikel berhasil dihapus.');
  }
}
