<?php

// File: app/Http/Controllers/FaqController.php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesSeoRequests;
use App\Models\Faq;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class FaqController extends Controller
{
    use HandlesSeoRequests;

    /**
     * Display the FAQ page with cached data
     */
    public function index()
    {
        // Cache FAQ data for 24 hours since it rarely changes
        $faqs = Cache::remember('faqs.published', 60 * 60 * 24, function () {
            return Faq::where('is_published', true)
                ->orderBy('id')
                ->get()
                ->groupBy('category');
        });

        // Get all categories for navigation
        $categories = Cache::remember('faq.categories', 60 * 60 * 24, function () {
            return Faq::where('is_published', true)
                ->select('category')
                ->selectRaw('MIN(id) as min_id')
                ->groupBy('category')
                ->orderBy('min_id')
                ->pluck('category')
                ->toArray();
        });

        return $this->handleSeoRequest('Faq/Index', [
            'faqs' => $faqs,
            'categories' => $categories,
        ]);
    }

    /**
     * Search FAQs
     */
    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return response()->json([]);
        }

        $results = Faq::where('is_published', true)
            ->where(function ($q) use ($query) {
                $q->where('question', 'like', "%{$query}%")
                    ->orWhere('answer', 'like', "%{$query}%");
            })
            ->select('id', 'question', 'answer', 'category')
            ->orderBy('id')
            ->limit(10)
            ->get();

        return response()->json($results);
    }

    /**
     * Clear FAQ cache (useful for admin operations)
     */
    public function clearCache()
    {
        Cache::forget('faqs.published');
        Cache::forget('faq.categories');

        return response()->json(['message' => 'FAQ cache cleared successfully']);
    }
}
