<?php
// File: app/Services/FaqCacheService.php

namespace App\Services;

use App\Models\Faq;
use Illuminate\Support\Facades\Cache;

class FaqCacheService
{
  const CACHE_DURATION = 60 * 60 * 24; // 24 hours
  const FAQ_CACHE_KEY = 'faqs.published';
  const FAQ_CATEGORIES_CACHE_KEY = 'faq.categories';

  /**
   * Get cached FAQ data
   */
  public static function getFaqs()
  {
    return Cache::remember(self::FAQ_CACHE_KEY, self::CACHE_DURATION, function () {
      return Faq::published()
        ->orderBy('id')
        ->get()
        ->groupBy('category');
    });
  }

  /**
   * Get cached categories
   */
  public static function getCategories()
  {
    return Cache::remember(self::FAQ_CATEGORIES_CACHE_KEY, self::CACHE_DURATION, function () {
      return Faq::published()
        ->select('category', 'id')
        ->distinct()
        ->orderBy('id')
        ->pluck('category')
        ->toArray();
    });
  }

  /**
   * Clear all FAQ caches
   */
  public static function clearAll()
  {
    Cache::forget(self::FAQ_CACHE_KEY);
    Cache::forget(self::FAQ_CATEGORIES_CACHE_KEY);
  }

  /**
   * Refresh cache
   */
  public static function refresh()
  {
    self::clearAll();
    self::getFaqs();
    self::getCategories();
  }
}
