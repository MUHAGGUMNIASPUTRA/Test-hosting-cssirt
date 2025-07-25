<?php
// File: app/Http/Controllers/CategoryController.php

namespace App\Http\Controllers;

use App\Models\Category;
use Inertia\Inertia;
use Inertia\Response;

class CategoryController extends Controller
{
  /**
   * Display a listing of posts for a specific category.
   */
  public function show(Category $category): Response
  {
    return Inertia::render('Categories/Show', [
      'category' => $category,
      'posts' => $category->posts()
        ->with('categories') // <-- INI PERBAIKANNYA: Eager load the categories relationship
        ->where('status', 'Published')
        ->latest('published_at')
        ->paginate(9)
        ->withQueryString(),
    ]);
  }
}
