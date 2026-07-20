<?php

use App\Services\WebAppScanService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('web-app:scan', function () {
    app(WebAppScanService::class)->run($this);
})->purpose('Scan keaktifan & status HTTPS semua aplikasi web, lalu update database');

Schedule::command('web-app:scan')
    ->dailyAt('00:00')
    ->withoutOverlapping()
    ->runInBackground();
