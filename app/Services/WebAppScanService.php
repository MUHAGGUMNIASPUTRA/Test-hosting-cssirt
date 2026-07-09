<?php

namespace App\Services;

use App\Models\WebApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebAppScanService
{
    private const TIMEOUT = 10;

    private const IDLE_INDICATORS = [
        'welcome to nginx', 'apache2 ubuntu default page',
        'apache http server test page', 'it works!',
        'apache2 debian default page', 'nginx default page',
        'welcome to apache', 'default server page',
        'coming soon', 'under construction', 'site maintenance',
    ];

    private const CODE_MAP = [
        301 => 'aktif', 302 => 'aktif', 303 => 'aktif',
        307 => 'aktif', 308 => 'aktif', 403 => 'aktif',
        404 => 'idle',  400 => 'idle',  401 => 'idle',
        405 => 'idle',  406 => 'idle',  410 => 'idle',
        500 => 'nonaktif', 502 => 'nonaktif',
        503 => 'nonaktif', 504 => 'nonaktif',
    ];

    public function run(Command $command): void
    {
        $command->info('Web App Scan dimulai: ' . now()->format('Y-m-d H:i:s'));
        Log::info('[web-app:scan] Scan dimulai.');

        $counts = ['updated' => 0, 'skipped' => 0, 'error' => 0];

        $apps = WebApplication::with([
            'networks' => fn ($q) => $q->where('is_primary', true)->with(['subdomain', 'ipAddress']),
        ])->get();

        if ($apps->isEmpty()) {
            $command->warn('Tidak ada aplikasi web di database.');
            Log::warning('[web-app:scan] Tidak ada aplikasi web di database.');
            return;
        }

        $command->info("Ditemukan {$apps->count()} aplikasi.");
        $bar = $command->getOutput()->createProgressBar($apps->count());
        $bar->start();

        foreach ($apps as $app) {
            try {
                $url = $this->resolveUrl($app);

                if (! $url) {
                    Log::warning("[web-app:scan] SKIP — {$app->name}: tidak ada URL/domain/IP.");
                    $counts['skipped']++;
                    $bar->advance();
                    continue;
                }

                [$newAppStatus, $newHttpsStatus] = $this->scan($url);

                $oldAppStatus   = $this->enumVal($app->app_status);
                $oldHttpsStatus = $this->enumVal($app->https_status);

                $app->update([
                    'app_status'   => $newAppStatus,
                    'https_status' => $newHttpsStatus,
                ]);

                if ($oldAppStatus !== $newAppStatus || $oldHttpsStatus !== $newHttpsStatus) {
                    Log::info("[web-app:scan] CHANGED — {$app->name}", [
                        'url'          => $url,
                        'app_status'   => "{$oldAppStatus} → {$newAppStatus}",
                        'https_status' => "{$oldHttpsStatus} → {$newHttpsStatus}",
                    ]);
                }

                $counts['updated']++;

            } catch (\Throwable $e) {
                Log::error("[web-app:scan] ERROR — {$app->name}: {$e->getMessage()}");
                $counts['error']++;
            }

            $bar->advance();
        }

        $bar->finish();
        $command->newLine(2);

        $command->table(['Keterangan', 'Jumlah'], [
            ['✅ Berhasil diupdate', $counts['updated']],
            ['⚠️  Dilewati (no URL)', $counts['skipped']],
            ['❌ Error',              $counts['error']],
        ]);

        Log::info('[web-app:scan] Scan selesai.', $counts);
        $command->info('Scan selesai: ' . now()->format('Y-m-d H:i:s'));
    }

    private function resolveUrl(WebApplication $app): ?string
    {
        $net = $app->networks->first();

        if ($subdomain = $net?->subdomain?->subdomain) {
            return str_starts_with($subdomain, 'http') ? $subdomain : "https://{$subdomain}";
        }
        if ($pub  = $net?->ipAddress?->public_ip)  return "http://{$pub}";
        if ($priv = $net?->ipAddress?->private_ip) return "http://{$priv}";

        return null;
    }

    private function scan(string $url): array
    {
        $isHttps     = str_starts_with($url, 'https://');
        $appStatus   = 'nonaktif';
        $httpsStatus = 'nonaktif';

        try {
            $response = Http::timeout(self::TIMEOUT)
                ->withoutVerifying()
                ->withHeaders(['User-Agent' => 'Mozilla/5.0', 'Accept' => 'text/html,*/*'])
                ->get($url);

            $code = $response->status();
            $body = strtolower($response->body());

            if ($code === 200) {
                $appStatus = $this->evaluateBody($body);
            } elseif (isset(self::CODE_MAP[$code])) {
                $appStatus = self::CODE_MAP[$code];
            } else {
                $appStatus = $code >= 200 && $code < 400 ? 'aktif' : 'nonaktif';
            }

            if ($isHttps) {
                $httpsStatus = $this->checkSsl($url);
            }

        } catch (\Illuminate\Http\Client\ConnectionException) {
            $appStatus   = 'nonaktif';
            $httpsStatus = 'nonaktif';
        }

        return [$appStatus, $httpsStatus];
    }

    private function evaluateBody(string $body): string
    {
        foreach (self::IDLE_INDICATORS as $indicator) {
            if (str_contains($body, $indicator)) return 'idle';
        }

        return (str_contains($body, '<html') || str_contains($body, 'login')
            || str_contains($body, 'dashboard') || strlen($body) > 1000)
            ? 'aktif' : 'idle';
    }

    private function checkSsl(string $url): string
    {
        try {
            Http::timeout(self::TIMEOUT)->get($url);
            return 'aktif';
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $msg = strtolower($e->getMessage());
            return str_contains($msg, 'expired') ? 'expired' : 'nonaktif';
        }
    }

    private function enumVal(mixed $val): string
    {
        return $val instanceof \BackedEnum ? $val->value : (string) ($val ?? 'nonaktif');
    }
}