<?php

// filepath: app/Http/Controllers/Admin/FaqController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Faq\SaveFaqRequest;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Faq::latest();

        // Apply search filter
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('question', 'ilike', '%'.$request->search.'%')
                    ->orWhere('answer', 'ilike', '%'.$request->search.'%')
                    ->orWhere('category', 'ilike', '%'.$request->search.'%');
            });
        }

        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category', 'ilike', '%'.$request->category.'%');
        }

        // Apply status filter
        if ($request->filled('status')) {
            $isPublished = $request->status === 'published';
            $query->where('is_published', $isPublished);
        }

        $faqs = $query->orderBy('id')->paginate(10)->withQueryString();

        // Get unique categories for filter
        $categories = Faq::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->sort()
            ->values();

        return Inertia::render('Admin/Faq/Index', [
            'faqs' => $faqs,
            'categories' => $categories,
            'filters' => $request->only(['search', 'category', 'status']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SaveFaqRequest $request): RedirectResponse
    {
        Faq::create($request->validated());

        return redirect()->back()->with('success', 'FAQ berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SaveFaqRequest $request, Faq $faq): RedirectResponse
    {
        $faq->update($request->validated());

        return redirect()->back()->with('success', 'FAQ berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()->back()->with('success', 'FAQ berhasil dihapus.');
    }
}
