<?php

namespace App\Providers;

use App\Services\SeoService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->singleton(SeoService::class, function ($app) {
      return new SeoService(config('app.ssr_url'));
    });
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    Vite::prefetch(concurrency: 3);
  }
}
