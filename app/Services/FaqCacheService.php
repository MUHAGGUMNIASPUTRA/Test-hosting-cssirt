<?php
// File: app/Services/FaqCacheService.php

namespace App\Services;

use App\Models\Faq;
use Illuminate\Support\Facades\Cache;

class FaqCacheService
{
  public const int    CACHE_DURATION           = 60 * 60 * 24; // 24 hours
  public const string FAQ_CACHE_KEY            = 'faqs.published';
  public const string FAQ_CATEGORIES_CACHE_KEY = 'faq.categories';

  /**
   * Get cached FAQ data
   */
  /** @return \Illuminate\Support\Collection<string, \Illuminate\Support\Collection> */
  public static function getFaqs(): \Illuminate\Support\Collection
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
   *
   * @return string[]
   */
  public static function getCategories(): array
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
  public static function clearAll(): void
  {
    Cache::forget(self::FAQ_CACHE_KEY);
    Cache::forget(self::FAQ_CATEGORIES_CACHE_KEY);
  }

  /**
   * Refresh cache
   */
  public static function refresh(): void
  {
    self::clearAll();
    self::getFaqs();
    self::getCategories();
  }
}
