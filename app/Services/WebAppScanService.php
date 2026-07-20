<?php

namespace App\Services;

use App\Models\WebApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class WebAppScanService
{
    private const LOG = 'schedule';

    private const REQUEST_TIMEOUT = 6;

    private const SSL_TIMEOUT = 5;

    private const MAX_RETRIES = 1;

    private const RETRY_DELAY = 3;   // detik

    private const SSL_MAX_RETRIES = 1;

    private const SSL_RETRY_DELAY = 2;   // detik

    private const SSL_EXTRA_TIMEOUT = 5;

    private const SSL_EXTRA_RETRIES = 1;

    private const IDLE_INDICATORS = [
        'welcome to nginx', 'apache2 ubuntu default page',
        'apache http server test page', 'it works!',
        'apache2 debian default page', 'test page for the apache http server',
        'nginx default page', 'welcome to apache',
        'apache ubuntu default page', 'default server page',
        'coming soon', 'under construction', 'site maintenance',
        'not found', '404 not found', 'the requested url was not found',
        'page not found', 'file not found', 'directory not found',
    ];

    private const MEANINGFUL_INDICATORS = [
        '<html', '<body', '<div', '<form', '<table',
        'javascript', 'css', 'content', 'main', 'data',
        'sistem', 'aplikasi', 'dashboard', 'login', 'home',
    ];

    private function log(): \Psr\Log\LoggerInterface
    {
        return Log::channel(self::LOG);
    }

    public function run(Command $command): void
    {
        if (! function_exists('curl_init')) {
            $msg = 'Ekstensi PHP curl tidak aktif. Aktifkan extension=curl di php.ini (termasuk untuk CLI/php.ini yang dipakai Artisan), lalu restart web server.';
            $command->error($msg);
            $this->log()->error("[web-app:scan] {$msg}");

            return;
        }

        $command->info('Web App Scan dimulai: '.now()->format('Y-m-d H:i:s'));
        $this->log()->info('[web-app:scan] Scan dimulai.');

        $counts = ['updated' => 0, 'skipped' => 0, 'error' => 0];

        $apps = WebApplication::with([
            'networks' => fn ($q) => $q->where('is_primary', true)->with(['subdomain', 'ipAddress']),
        ])->get();

        if ($apps->isEmpty()) {
            $command->warn('Tidak ada aplikasi web di database.');
            $this->log()->warning('[web-app:scan] Tidak ada aplikasi web di database.');

            return;
        }

        $command->info("Ditemukan {$apps->count()} aplikasi.");
        $bar = $command->getOutput()->createProgressBar($apps->count());
        $bar->start();

        foreach ($apps as $app) {
            try {
                $url = $this->resolveUrl($app);

                if (! $url) {
                    $this->log()->warning("[web-app:scan] SKIP — {$app->name}: tidak ada URL/domain/IP.");
                    $counts['skipped']++;
                    $bar->advance();

                    continue;
                }

                // Cermin csv_processor.py: cek SSL dulu, lalu app status pakai ssl_boost
                $httpsStatus = $this->checkHttpsSupport($url);
                $appStatus = $this->checkAppStatus($url, $httpsStatus);

                $oldAppStatus = $this->enumVal($app->app_status);
                $oldHttpsStatus = $this->enumVal($app->https_status);

                $app->update([
                    'app_status' => $appStatus,
                    'https_status' => $httpsStatus,
                ]);

                if ($oldAppStatus !== $appStatus || $oldHttpsStatus !== $httpsStatus) {
                    $this->log()->info("[web-app:scan] CHANGED — {$app->name}", [
                        'url' => $url,
                        'app_status' => "{$oldAppStatus} → {$appStatus}",
                        'https_status' => "{$oldHttpsStatus} → {$httpsStatus}",
                    ]);
                }

                $counts['updated']++;

            } catch (\Throwable $e) {
                $this->log()->error("[web-app:scan] ERROR — {$app->name}: {$e->getMessage()}");
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

        $this->log()->info('[web-app:scan] Scan selesai.', $counts);
        $command->info('Scan selesai: '.now()->format('Y-m-d H:i:s'));
    }

    private function resolveUrl(WebApplication $app): ?string
    {
        $net = $app->networks->first();

        $domain = $this->cleanValue($net?->subdomain?->subdomain);
        $publicIp = $this->cleanValue($net?->ipAddress?->public_ip);
        $localIp = $this->cleanValue($net?->ipAddress?->private_ip);

        $raw = $domain !== '' ? $domain : ($publicIp !== '' ? $publicIp : $localIp);
        if ($raw === '') {
            return null;
        }

        return $this->validateAndCleanUrl($raw);
    }

    private function cleanValue(?string $val): string
    {
        if ($val === null) {
            return '';
        }
        $val = trim($val);
        $placeholders = ['-', '--', 'N/A', 'n/a', 'null', 'NULL', 'None', ''];

        return in_array($val, $placeholders, true) ? '' : $val;
    }

    private function validateAndCleanUrl(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = str_replace(' ', '', trim($url));
        if ($url === '') {
            return null;
        }

        if (! preg_match('#^https?://#i', $url)) {
            $url = "http://{$url}";
        }

        $parts = parse_url($url);
        if ($parts === false || empty($parts['host'])) {
            return null;
        }

        $host = trim($parts['host'], '.');
        if ($host === '') {
            return null;
        }

        $scheme = $parts['scheme'] ?? 'http';
        $port = isset($parts['port']) ? ":{$parts['port']}" : '';
        $path = $parts['path'] ?? '/';

        return "{$scheme}://{$host}{$port}{$path}";
    }

    private function withScheme(string $url, string $scheme): string
    {
        $parts = parse_url($url);
        if ($parts === false || empty($parts['host'])) {
            return $url;
        }

        $port = isset($parts['port']) ? ":{$parts['port']}" : '';
        $path = $parts['path'] ?? '/';
        $query = isset($parts['query']) ? "?{$parts['query']}" : '';

        return "{$scheme}://{$parts['host']}{$port}{$path}{$query}";
    }

    private static ?string $caBundlePath = null;

    private static bool $caBundleResolved = false;

    private function resolveCaBundlePath(): ?string
    {
        if (self::$caBundleResolved) {
            return self::$caBundlePath;
        }
        self::$caBundleResolved = true;

        if (class_exists(\Composer\CaBundle\CaBundle::class)) {
            try {
                self::$caBundlePath = \Composer\CaBundle\CaBundle::getSystemCaRootBundlePath();

                return self::$caBundlePath;
            } catch (\Throwable) {
                // lanjut ke fallback
            }
        }

        $iniCurlCainfo = trim((string) ini_get('curl.cainfo'));
        if ($iniCurlCainfo !== '' && is_file($iniCurlCainfo)) {
            self::$caBundlePath = $iniCurlCainfo;

            return self::$caBundlePath;
        }

        $iniOpensslCafile = trim((string) ini_get('openssl.cafile'));
        if ($iniOpensslCafile !== '' && is_file($iniOpensslCafile)) {
            self::$caBundlePath = $iniOpensslCafile;

            return self::$caBundlePath;
        }

        foreach ([
            '/etc/ssl/certs/ca-certificates.crt', // Debian/Ubuntu
            '/etc/pki/tls/certs/ca-bundle.crt',   // RHEL/CentOS
            '/etc/ssl/cert.pem',                  // Alpine/macOS
        ] as $path) {
            if (is_file($path)) {
                self::$caBundlePath = $path;

                return self::$caBundlePath;
            }
        }

        return self::$caBundlePath; // null — biarkan curl pakai default bawaannya sendiri
    }

    private function newCurlHandle(string $url, int $timeout, bool $verifySsl): \CurlHandle|false
    {
        $ch = curl_init($url);
        if ($ch === false) {
            return false;
        }

        $options = [
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_TIMEOUT => $timeout,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => $verifySsl,
            CURLOPT_SSL_VERIFYHOST => $verifySsl ? 2 : 0,
            CURLOPT_NOSIGNAL => true,
        ];

        if ($caBundle = $this->resolveCaBundlePath()) {
            $options[CURLOPT_CAINFO] = $caBundle;
        }

        curl_setopt_array($ch, $options);

        return $ch;
    }

    private function checkHttpsSupport(string $url): string
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (! $host) {
            return 'nonaktif';
        }

        $target = "https://{$host}/";

        for ($attempt = 0; $attempt < self::SSL_MAX_RETRIES + 1; $attempt++) {
            if ($attempt > 0) {
                sleep(self::SSL_RETRY_DELAY);
            }

            // Langkah 1: koneksi dengan verifikasi SSL penuh
            $ch = $this->newCurlHandle($target, self::SSL_TIMEOUT, true);
            if ($ch === false) {
                continue;
            }

            curl_setopt_array($ch, [
                CURLOPT_NOBODY => true,
                CURLOPT_FOLLOWLOCATION => false,
                CURLOPT_CERTINFO => true,
            ]);
            curl_exec($ch);
            $errno = curl_errno($ch);
            $certInfo = curl_getinfo($ch, CURLINFO_CERTINFO);
            curl_close($ch);

            if ($errno === 0) {
                return 'aktif'; // sertifikat valid dan trusted
            }

            // Langkah 2: cek masa berlaku sertifikat tanpa verifikasi
            $context = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);

            $client = @stream_socket_client(
                "ssl://{$host}:443",
                $errnoSock,
                $errstr,
                self::SSL_TIMEOUT,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if ($client === false) {
                continue; // gagal koneksi, coba retry
            }

            $params = stream_context_get_params($client);
            fclose($client);

            if (! isset($params['options']['ssl']['peer_certificate'])) {
                return 'nonaktif';
            }

            $cert = $params['options']['ssl']['peer_certificate'];
            $certData = openssl_x509_parse($cert);

            if (! isset($certData['validTo_time_t'])) {
                return 'nonaktif';
            }

            $expireTs = $certData['validTo_time_t'];

            return $expireTs < time() ? 'expired' : 'aktif';
        }

        return 'nonaktif';
    }

    private function checkAppStatus(string $url, string $httpsStatus = 'nonaktif'): string
    {
        // ssl_boost: tambah timeout & retry jika SSL valid/expired
        $sslBoost = in_array($httpsStatus, ['aktif', 'expired']);
        $effectiveTimeout = self::REQUEST_TIMEOUT + ($sslBoost ? self::SSL_EXTRA_TIMEOUT : 0);
        $effectiveRetries = self::MAX_RETRIES + ($sslBoost ? self::SSL_EXTRA_RETRIES : 0);

        $host = parse_url($url, PHP_URL_HOST);
        if (! $host) {
            return 'nonaktif';
        }

        $preferredScheme = $sslBoost ? 'https' : 'http';
        $alternateScheme = $sslBoost ? 'http' : 'https';

        $urlsToTry = array_values(array_unique([
            $this->withScheme($url, $preferredScheme),
            $this->withScheme($url, $alternateScheme),
        ]));

        $headers = [
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,*/*;q=0.8',
            'Accept-Language: id-ID,id;q=0.9,en-US;q=0.8,en;q=0.7',
            'Cache-Control: no-cache',
        ];

        foreach ($urlsToTry as $urlIndex => $testUrl) {
            $isPrimaryUrl = $urlIndex === 0;
            $attemptsForThisUrl = $isPrimaryUrl ? $effectiveRetries + 1 : 1;

            for ($attempt = 0; $attempt < $attemptsForThisUrl; $attempt++) {
                if ($attempt > 0) {
                    sleep(self::RETRY_DELAY);
                }

                $ch = $this->newCurlHandle($testUrl, $effectiveTimeout, false);
                if ($ch === false) {
                    continue;
                }

                curl_setopt_array($ch, [
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_ENCODING => '', // biar curl auto gzip/deflate/br, mirror requests di Python
                ]);

                $body = curl_exec($ch);
                $errno = curl_errno($ch);
                $statusCode = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
                curl_close($ch);

                if ($body === false || $errno !== 0) {
                    continue; // koneksi gagal, retry / lanjut ke skema lain
                }

                return $this->evaluateResponse($statusCode, strtolower($body));
            }
        }

        return 'nonaktif';
    }

    // ── evaluateResponse ──────────────────────────────────────────────────────
    // Cermin app_checker.py: _evaluate_response()

    private function evaluateResponse(int $code, string $body): string
    {
        if ($code === 200) {
            if ($this->hasIdleIndicator($body)) {
                return 'idle';
            }
            if ($this->hasMeaningfulContent($body)) {
                return 'aktif';
            }

            return 'idle';
        }

        if ($code === 404) {
            return 'idle';
        }
        if (in_array($code, [301, 302, 303, 307, 308])) {
            return 'aktif';
        }
        if ($code === 403) {
            return 'aktif';
        }
        if (in_array($code, [400, 401, 405, 406, 410])) {
            return 'idle';
        }
        if (in_array($code, [500, 502, 503, 504])) {
            return 'nonaktif';
        }

        return 'idle';
    }

    private function hasIdleIndicator(string $body): bool
    {
        foreach (self::IDLE_INDICATORS as $indicator) {
            if (str_contains($body, $indicator)) {
                return true;
            }
        }

        return false;
    }

    private function hasMeaningfulContent(string $body): bool
    {
        foreach (self::MEANINGFUL_INDICATORS as $indicator) {
            if (str_contains($body, $indicator)) {
                return true;
            }
        }

        return strlen($body) > 1000;
    }

    private function enumVal(mixed $val): string
    {
        return $val instanceof \BackedEnum ? $val->value : (string) ($val ?? 'nonaktif');
    }
}
