<?php

// File: app/Http/Controllers/FaqController.php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesSeoRequests;
use App\Services\FaqCacheService;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    use HandlesSeoRequests;

    /**
     * Display the FAQ page with cached data
     */
    public function index()
    {
        $faqs = FaqCacheService::getFaqs();
        $categories = FaqCacheService::getCategories();

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
        FaqCacheService::clearAll();

        return response()->json(['message' => 'FAQ cache cleared successfully']);
    }
}
