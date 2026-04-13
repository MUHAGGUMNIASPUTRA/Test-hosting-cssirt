<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Post\StorePostRequest;
use App\Http\Requests\Admin\Post\UpdatePostRequest;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\PostService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function __construct(private readonly PostService $postService) {}

    public function index(Request $request): Response
    {
        $query = Post::with(['categories', 'tags'])->withCount('ratings');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('title', 'ilike', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        return Inertia::render('Admin/Posts/Index', [
            'posts'   => $query->latest()->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Posts/Create', [
            'categories' => Category::orderBy('name', 'asc')->get(['id', 'name']),
            'tags'       => Tag::orderBy('name', 'asc')->get(['id', 'name']),
        ]);
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $this->postService->create(
            $request->validated(),
            $request->file('image'),
            Auth::user()->name
        );

        return redirect()->route('admin.posts.index')
            ->with('success', 'Artikel berhasil dibuat.');
    }

    public function edit(Post $post): Response
    {
        return Inertia::render('Admin/Posts/Create', [
            'post'       => $post->load(['categories', 'tags']),
            'categories' => Category::orderBy('name', 'asc')->get(['id', 'name']),
            'tags'       => Tag::orderBy('name', 'asc')->get(['id', 'name']),
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->postService->update(
            $post,
            $request->validated(),
            $request->file('image'),
            Auth::user()->name
        );

        return redirect()->route('admin.posts.index')
            ->with('success', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->postService->deleteWithAssets($post);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Artikel berhasil dihapus.');
    }
}
