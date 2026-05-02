<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveTechStackCategoryRequest;
use App\Models\TechStackCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TechStackCategoryController extends Controller
{
    public function index(Request $request): Response
    {
        $query = TechStackCategory::withCount('techStacks')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%'.$request->search.'%');
        }

        $categories = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Assets/TechStackCategories/Index', [
            'categories' => $categories,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(SaveTechStackCategoryRequest $request): RedirectResponse
    {
        TechStackCategory::create($request->validated());

        return redirect()->back()->with('success', 'Kategori tech stack berhasil ditambahkan.');
    }

    public function update(SaveTechStackCategoryRequest $request, TechStackCategory $techStackCategory): RedirectResponse
    {
        $techStackCategory->update($request->validated());

        return redirect()->back()->with('success', 'Kategori tech stack berhasil diperbarui.');
    }

    public function destroy(TechStackCategory $techStackCategory): RedirectResponse
    {
        $techStackCategory->delete();

        return redirect()->back()->with('success', 'Kategori tech stack berhasil dihapus.');
    }
}
