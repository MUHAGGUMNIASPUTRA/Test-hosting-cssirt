<?php

// File: app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesSeoRequests;
use App\Models\Category;

class CategoryController extends Controller
{
    use HandlesSeoRequests;

    /**
     * Display a listing of posts for a specific category.
     */
    public function show(Category $category)
    {
        return $this->handleSeoRequest('Categories/Show', [
            'category' => $category,
            'posts' => $category->posts()
                ->with('categories')
                ->where('status', 'Published')
                ->latest('published_at')
                ->paginate(6)
                ->withQueryString(),
        ]);
    }
}
