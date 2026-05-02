<?php

// File: app/Http/Controllers/Admin/TaxonomyController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class TaxonomyController extends Controller
{
    // Menampilkan halaman utama untuk Kategori & Tag
    public function index()
    {
        return Inertia::render('Admin/Taxonomy/Index', [
            'categories' => Category::withCount('posts')->orderBy('name')->get(),
            'tags' => Tag::withCount('posts')->orderBy('name')->get(),
        ]);
    }

    // Menyimpan Kategori baru
    public function storeCategory(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255|unique:categories']);
        Category::create(['name' => $validated['name'], 'slug' => Str::slug($validated['name'])]);

        return back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    // Mengupdate Kategori
    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate(['name' => 'required|string|max:255|unique:categories,name,'.$category->id]);
        $category->update(['name' => $validated['name'], 'slug' => Str::slug($validated['name'])]);

        return back()->with('success', 'Kategori berhasil diperbarui.');
    }

    // Menghapus Kategori
    public function destroyCategory(Category $category)
    {
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }

    // Menyimpan Tag baru
    public function storeTag(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|max:255|unique:tags']);
        Tag::create(['name' => $validated['name'], 'slug' => Str::slug($validated['name'])]);

        return back()->with('success', 'Tag berhasil ditambahkan.');
    }

    // Mengupdate Tag
    public function updateTag(Request $request, Tag $tag)
    {
        $validated = $request->validate(['name' => 'required|string|max:255|unique:tags,name,'.$tag->id]);
        $tag->update(['name' => $validated['name'], 'slug' => Str::slug($validated['name'])]);

        return back()->with('success', 'Tag berhasil diperbarui.');
    }

    // Menghapus Tag
    public function destroyTag(Tag $tag)
    {
        $tag->delete();

        return back()->with('success', 'Tag berhasil dihapus.');
    }
}
