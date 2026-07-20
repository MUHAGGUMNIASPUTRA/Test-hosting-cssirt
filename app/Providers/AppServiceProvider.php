<?php

// filepath: app/Providers/AppServiceProvider.php

namespace App\Providers;

use App\Services\SeoService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
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

        // Custom rate limiters for public endpoints
        RateLimiter::for('incident-create', function (Request $request) {
            $key = $request->ip().'|'.substr((string) $request->userAgent(), 0, 100);

            return [
                Limit::perMinute(10)->by($key)->response(function () {
                    return redirect()->back()->withErrors([
                        'form' => 'Terlalu banyak percobaan membuat tiket. Silakan coba lagi nanti.',
                    ]);
                }),
                Limit::perDay(100)->by($key),
            ];
        });

        RateLimiter::for('incident-search', function (Request $request) {
            $key = $request->ip().'|'.substr((string) $request->userAgent(), 0, 100);

            return [
                Limit::perMinute(20)->by($key)->response(function () {
                    return redirect()->back()->withErrors([
                        'search' => 'Terlalu banyak permintaan pencarian. Silakan coba lagi nanti.',
                    ]);
                }),
                Limit::perHour(200)->by($key),
            ];
        });

        RateLimiter::for('incident-download', function (Request $request) {
            $key = $request->ip().'|'.substr((string) $request->userAgent(), 0, 100);

            return [
                Limit::perMinute(30)->by($key),
                Limit::perHour(300)->by($key),
            ];
        });

        RateLimiter::for('login', function (Request $request) {
            return [
                Limit::perMinute(20)->by($request->ip()),
                Limit::perHour(100)->by($request->ip()),
            ];
        });

        RateLimiter::for('register', function (Request $request) {
            return [
                Limit::perMinute(3)->by($request->ip()),
                Limit::perDay(10)->by($request->ip()),
            ];
        });

        RateLimiter::for('admin-incident-mutation', function (Request $request) {
            return Limit::perMinute(30)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
