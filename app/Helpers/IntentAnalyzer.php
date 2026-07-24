<?php

// File: app/Helpers/IntentAnalyzer.php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * IntentAnalyzer
 *
 * Helper statis untuk menganalisis intent dari query pencarian user.
 *
 * Cara kerja:
 * 1. Jika AI Provider dikonfigurasi → kirim query ke LLM untuk ekstraksi intent + keywords
 * 2. Jika tidak ada AI (fallback) → gunakan kamus sinonim cybersecurity statis
 *
 * Contoh:
 *   Input: "Saya kena ransomware"
 *   Output: { intent: "ransomware", keywords: ["ransomware", "enkripsi data", "recovery"] }
 *
 *   Input: "email mencurigakan"
 *   Output: { intent: "phishing", keywords: ["phishing", "email palsu", "social engineering"] }
 */
class IntentAnalyzer
{
    /**
     * System prompt untuk ekstraksi intent & keyword dari query cybersecurity.
     */
    private const INTENT_SYSTEM_PROMPT = <<<'PROMPT'
Kamu adalah AI spesialis analisis intent pencarian keamanan siber untuk website CSIRT Bojonegoro.

Tugasmu: Analisis query pencarian user dan ekstrak intent serta keyword yang relevan.

ATURAN:
1. Tentukan INTENT utama (1-3 kata, bahasa Indonesia atau Inggris teknis)
2. Hasilkan KEYWORDS untuk pencarian database (array 3-6 kata)
3. Pahami sinonim & konteks cybersecurity Indonesia
4. HANYA output JSON, tidak ada teks lain

SINONIM YANG HARUS DIPAHAMI:
- "lupa password" / "tidak bisa login" → intent: "password reset", keywords: ["password", "reset", "login", "akun"]
- "website diretas" / "website tidak bisa diakses" / "tampilan berubah" → intent: "defacement", keywords: ["defacement", "hacking", "peretasan", "website"]
- "email mencurigakan" / "email palsu" / "link aneh" → intent: "phishing", keywords: ["phishing", "email", "social engineering", "penipuan"]
- "QRIS palsu" / "pembayaran digital palsu" → intent: "QRIS scam", keywords: ["QRIS", "scam", "penipuan", "pembayaran"]
- "terkena virus" / "laptop lambat aneh" / "file hilang" → intent: "malware", keywords: ["malware", "virus", "trojan", "infeksi"]
- "kena ransomware" / "file terenkripsi" / "minta tebusan" → intent: "ransomware", keywords: ["ransomware", "enkripsi", "recovery", "backup"]
- "OTP dicuri" / "kode verifikasi diambil" → intent: "OTP phishing", keywords: ["OTP", "social engineering", "verifikasi", "phishing"]
- "akun diretas" / "akun dicuri" → intent: "account takeover", keywords: ["akun", "hacking", "password", "social engineering"]
- "lapor insiden" / "cara lapor" → intent: "pelaporan insiden", keywords: ["lapor", "insiden", "CSIRT", "prosedur"]
- "keamanan jaringan" / "network" → intent: "network security", keywords: ["jaringan", "network", "firewall", "keamanan"]

OUTPUT FORMAT (JSON):
{
  "intent": "intent utama dalam 1-3 kata",
  "keywords": ["keyword1", "keyword2", "keyword3"],
  "confidence": 0.9
}
PROMPT;

    /**
     * Kamus sinonim statis untuk fallback tanpa AI.
     * Format: keyword_trigger → [intent, [keywords...]]
     */
    private static array $synonymMap = [
        // Password & Akun
        'password'   => ['password reset', ['password', 'reset', 'login', 'akun']],
        'lupa'       => ['password reset', ['password', 'reset', 'login']],
        'login'      => ['password reset', ['login', 'password', 'akun']],
        'akun'       => ['account security', ['akun', 'password', 'keamanan']],

        // Phishing
        'phishing'   => ['phishing', ['phishing', 'email', 'social engineering']],
        'email'      => ['phishing', ['phishing', 'email', 'penipuan']],
        'palsu'      => ['phishing', ['phishing', 'penipuan', 'social engineering']],
        'mencurigakan' => ['phishing', ['phishing', 'email', 'penipuan', 'social engineering']],
        'link'       => ['phishing', ['phishing', 'link', 'email']],

        // Malware & Virus
        'virus'      => ['malware', ['malware', 'virus', 'trojan', 'infeksi']],
        'malware'    => ['malware', ['malware', 'virus', 'trojan']],
        'trojan'     => ['malware', ['trojan', 'malware', 'virus']],
        'lambat'     => ['malware', ['malware', 'virus', 'kinerja']],
        'infeksi'    => ['malware', ['malware', 'virus', 'infeksi']],

        // Ransomware
        'ransomware' => ['ransomware', ['ransomware', 'enkripsi', 'recovery', 'backup']],
        'enkripsi'   => ['ransomware', ['ransomware', 'enkripsi', 'recovery']],
        'tebusan'    => ['ransomware', ['ransomware', 'tebusan', 'recovery']],
        'terkunci'   => ['ransomware', ['ransomware', 'file', 'recovery']],

        // Defacement / Hacking
        'diretas'    => ['defacement', ['defacement', 'hacking', 'peretasan', 'website']],
        'hacked'     => ['hacking', ['hacking', 'defacement', 'peretasan']],
        'defacement' => ['defacement', ['defacement', 'website', 'peretasan']],
        'website'    => ['website security', ['website', 'keamanan', 'defacement']],

        // QRIS & Penipuan Digital
        'qris'       => ['QRIS scam', ['QRIS', 'scam', 'penipuan', 'pembayaran']],
        'penipuan'   => ['penipuan digital', ['penipuan', 'scam', 'phishing']],
        'scam'       => ['scam', ['scam', 'penipuan', 'phishing']],

        // OTP & Social Engineering
        'otp'        => ['OTP phishing', ['OTP', 'social engineering', 'verifikasi']],
        'verifikasi' => ['social engineering', ['verifikasi', 'OTP', 'phishing']],
        'social'     => ['social engineering', ['social engineering', 'phishing', 'OTP']],

        // Laporan & CSIRT
        'lapor'      => ['pelaporan insiden', ['lapor', 'insiden', 'CSIRT', 'prosedur']],
        'insiden'    => ['pelaporan insiden', ['insiden', 'lapor', 'CSIRT']],
        'csirt'      => ['CSIRT', ['CSIRT', 'layanan', 'kontak']],
        'layanan'    => ['layanan CSIRT', ['layanan', 'CSIRT', 'keamanan']],

        // Jaringan
        'jaringan'   => ['network security', ['jaringan', 'network', 'firewall']],
        'network'    => ['network security', ['jaringan', 'network', 'keamanan']],
        'firewall'   => ['network security', ['firewall', 'jaringan', 'keamanan']],

        // Backup & Recovery
        'backup'     => ['backup', ['backup', 'recovery', 'data', 'pemulihan']],
        'recovery'   => ['recovery', ['recovery', 'backup', 'pemulihan', 'data']],
        'data'       => ['keamanan data', ['data', 'backup', 'keamanan']],
    ];

    /**
     * Analisis query dan kembalikan intent + keywords.
     *
     * @param  string  $query  Query pencarian dari user
     * @return array{ intent: string, keywords: array<string>, confidence: float, used_ai: bool }
     */
    public static function analyze(string $query): array
    {
        $apiKey = config('services.ai_provider.key');
        $apiUrl = config('services.ai_provider.url');
        $model  = config('services.ai_provider.model');

        // Gunakan AI jika tersedia
        if (! empty($apiKey) && $apiKey !== 'your-api-key-here') {
            $result = self::analyzeWithAI($query, $apiKey, $apiUrl, $model);
            if ($result !== null) {
                return array_merge($result, ['used_ai' => true]);
            }
        }

        // Fallback ke kamus sinonim
        return array_merge(self::analyzeWithFallback($query), ['used_ai' => false]);
    }

    /**
     * Analisis menggunakan LLM (OpenAI-compatible API).
     *
     * @return array|null null jika gagal
     */
    private static function analyzeWithAI(string $query, string $apiKey, string $apiUrl, string $model): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])
                ->timeout(10)
                ->post($apiUrl, [
                    'model'       => $model,
                    'messages'    => [
                        ['role' => 'system', 'content' => self::INTENT_SYSTEM_PROMPT],
                        ['role' => 'user',   'content' => "Analisis query ini: \"{$query}\""],
                    ],
                    'max_tokens'  => 200,
                    'temperature' => 0.1,
                    'response_format' => ['type' => 'json_object'],
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException("AI error: {$response->status()}");
            }

            $data    = $response->json();
            $content = $data['choices'][0]['message']['content'] ?? null;

            if (empty($content)) {
                throw new \RuntimeException('Empty AI response');
            }

            $parsed = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE || empty($parsed['intent'])) {
                throw new \RuntimeException('Invalid JSON from AI');
            }

            return [
                'intent'     => $parsed['intent'] ?? 'pencarian umum',
                'keywords'   => array_values(array_filter((array) ($parsed['keywords'] ?? []))),
                'confidence' => (float) ($parsed['confidence'] ?? 0.8),
            ];

        } catch (\Exception $e) {
            Log::warning("IntentAnalyzer AI failed, using fallback: {$e->getMessage()}");

            return null;
        }
    }

    /**
     * Analisis menggunakan kamus sinonim statis (tanpa AI).
     */
    private static function analyzeWithFallback(string $query): array
    {
        $lower = strtolower($query);
        $words = preg_split('/\s+/', $lower);

        $intentMatch  = null;
        $keywordsMatch = [];

        // Cari kata kunci pertama yang cocok di kamus
        foreach ($words as $word) {
            $clean = preg_replace('/[^a-z0-9]/', '', $word);
            if (isset(self::$synonymMap[$clean])) {
                [$intentMatch, $keywordsMatch] = self::$synonymMap[$clean];
                break;
            }
        }

        // Jika tidak ditemukan di kamus, gunakan kata dari query langsung
        if ($intentMatch === null) {
            $meaningful = array_filter($words, fn ($w) => strlen($w) >= 3);
            $keywordsMatch = array_values(array_slice($meaningful, 0, 5));
            $intentMatch   = implode(' ', array_slice($keywordsMatch, 0, 2)) ?: 'pencarian umum';
        }

        return [
            'intent'     => $intentMatch,
            'keywords'   => $keywordsMatch,
            'confidence' => 0.6,
        ];
    }
}
