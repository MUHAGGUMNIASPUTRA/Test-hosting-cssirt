<?php

use App\Services\WebAppScanService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('web-app:scan', function () {
    $this->info('Web App Scan dimulai: ' . now()->format('Y-m-d H:i:s'));
    Log::info('[web-app:scan] Scan dimulai.');

    $counts = app(WebAppScanService::class)->run();

    $this->table(['Keterangan', 'Jumlah'], [
        ['✅ Berhasil diupdate', $counts['updated']],
        ['⚠️  Dilewati (no URL)', $counts['skipped']],
        ['❌ Error',              $counts['error']],
    ]);

    Log::info('[web-app:scan] Scan selesai.', $counts);
    $this->info('Scan selesai: ' . now()->format('Y-m-d H:i:s'));

})->purpose('Scan keaktifan & status HTTPS semua aplikasi web, lalu update database');

Schedule::command('web-app:scan')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->runInBackground();