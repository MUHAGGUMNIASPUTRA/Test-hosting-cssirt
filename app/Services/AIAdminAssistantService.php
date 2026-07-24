<?php

// File: app/Services/AIAdminAssistantService.php

namespace App\Services;

use App\Helpers\AdminPromptBuilder;
use App\Models\Document;
use App\Models\Faq;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * AIAdminAssistantService
 *
 * Service untuk AI Admin Assistant — fitur AI Copilot khusus Administrator.
 * Menggunakan AIProviderService (existing) untuk memanggil LLM,
 * dan AdminPromptBuilder untuk membangun prompt per jenis aksi.
 *
 * Fitur yang tersedia:
 * - Draft Artikel
 * - Generate FAQ
 * - Ringkas Dokumen
 * - Insight Dashboard
 * - Statistik Website
 * - Cari Artikel / FAQ / Dokumen
 * - Pertanyaan umum admin
 */
class AIAdminAssistantService
{
    public function __construct(
        private readonly AIProviderService $providerService,
    ) {}

    /**
     * Dispatch aksi berdasarkan action type.
     *
     * @param  string  $action  Jenis aksi: draft_article, generate_faq, summarize, dll.
     * @param  string  $prompt  Prompt/input dari Administrator
     * @return array{ answer: string, action: string, metadata: array }
     */
    public function handle(string $action, string $prompt): array
    {
        return match ($action) {
            'draft_article'      => $this->draftArticle($prompt),
            'generate_faq'       => $this->generateFaq($prompt),
            'summarize'          => $this->summarizeDocument($prompt),
            'dashboard_insight'  => $this->getDashboardInsight(),
            'statistics'         => $this->getStatistics(),
            'search_article'     => $this->searchContent('artikel', $prompt, 'post'),
            'search_faq'         => $this->searchContent('FAQ', $prompt, 'faq'),
            'search_document'    => $this->searchContent('dokumen', $prompt, 'document'),
            default              => $this->handleGeneral($prompt),
        };
    }

    /**
     * 1. Draft Artikel
     * Menghasilkan draft artikel tentang topik yang diberikan.
     */
    public function draftArticle(string $topic): array
    {
        $prompts = AdminPromptBuilder::buildDraftArticlePrompt($topic);

        $answer = $this->callAI($prompts['system'], $prompts['user']);

        return [
            'answer'   => $answer,
            'action'   => 'draft_article',
            'metadata' => [
                'topic' => $topic,
                'note'  => 'Draft artikel belum dipublish. Tinjau dan edit di panel Artikel.',
            ],
        ];
    }

    /**
     * 2. Generate FAQ
     * Menghasilkan daftar FAQ tentang topik yang diberikan.
     */
    public function generateFaq(string $topic): array
    {
        $prompts = AdminPromptBuilder::buildFaqPrompt($topic);

        $answer = $this->callAI($prompts['system'], $prompts['user']);

        return [
            'answer'   => $answer,
            'action'   => 'generate_faq',
            'metadata' => [
                'topic' => $topic,
                'note'  => 'Daftar FAQ ini adalah saran. Tambahkan manual di panel FAQ.',
            ],
        ];
    }

    /**
     * 3. Ringkas Dokumen
     * Meringkas teks dokumen yang diberikan.
     */
    public function summarizeDocument(string $documentText): array
    {
        if (empty(trim($documentText))) {
            return [
                'answer'   => 'Silakan masukkan teks dokumen yang ingin diringkas setelah perintah "Ringkas dokumen berikut:".',
                'action'   => 'summarize',
                'metadata' => [],
            ];
        }

        $prompts = AdminPromptBuilder::buildSummarizePrompt($documentText);

        $answer = $this->callAI($prompts['system'], $prompts['user']);

        return [
            'answer'   => $answer,
            'action'   => 'summarize',
            'metadata' => [
                'original_length' => mb_strlen($documentText),
            ],
        ];
    }

    /**
     * 4. Insight Dashboard
     * Membaca statistik website dan menghasilkan insight.
     */
    public function getDashboardInsight(): array
    {
        $stats = $this->collectStats();
        $topPosts = $this->getTopPosts();
        $topFaqs  = $this->getTopFaqs();

        $prompts = AdminPromptBuilder::buildInsightPrompt($stats, $topPosts, $topFaqs);

        $answer = $this->callAI($prompts['system'], $prompts['user']);

        return [
            'answer'   => $answer,
            'action'   => 'dashboard_insight',
            'metadata' => [
                'stats_snapshot' => $stats,
            ],
        ];
    }

    /**
     * 5. Statistik Website
     * Mengembalikan statistik real-time dari database tanpa memanggil AI.
     */
    public function getStatistics(): array
    {
        $stats = $this->collectStats();

        $lines = [
            '## 📊 Statistik Website CSIRT',
            '',
            '### Konten',
            "- **Artikel**: {$stats['posts']['total']} total, {$stats['posts']['published']} dipublish",
            "- **FAQ**: {$stats['faqs']['total']} total, {$stats['faqs']['published']} dipublish",
            "- **Dokumen**: {$stats['documents']['total']} total",
            '',
            '### Insiden',
            "- **Total Insiden**: {$stats['incidents']['total']}",
            "- **Insiden Bulan Ini**: {$stats['incidents']['this_month']}",
            "- **Insiden Terbuka**: {$stats['incidents']['open']}",
            "- **Kritikal**: {$stats['incidents']['critical']}",
            '',
            '### Aset',
            "- **Web Aplikasi**: {$stats['web_applications']['total']} ({$stats['web_applications']['active']} aktif)",
            "- **Mobile Aplikasi**: {$stats['mobile_applications']['total']} ({$stats['mobile_applications']['active']} aktif)",
            "- **Lisensi**: {$stats['licenses']['total']} ({$stats['licenses']['expiring_soon']} akan kadaluarsa)",
            "- **Aset Fisik**: {$stats['physical_assets']['total']}",
            "- **Aset Informasi**: {$stats['information_assets']['total']}",
            '',
            '*Data diambil langsung dari database pada ' . now()->format('d M Y, H:i') . ' WIB*',
        ];

        return [
            'answer'   => implode("\n", $lines),
            'action'   => 'statistics',
            'metadata' => $stats,
        ];
    }

    /**
     * 6/7/8. Cari Konten (Artikel / FAQ / Dokumen)
     *
     * @param  string  $contentType  Label tampilan: 'artikel', 'FAQ', 'dokumen'
     * @param  string  $query        Query pencarian
     * @param  string  $modelType    Tipe model: 'post', 'faq', 'document'
     */
    public function searchContent(string $contentType, string $query, string $modelType): array
    {
        $results = match ($modelType) {
            'post'     => $this->searchPosts($query),
            'faq'      => $this->searchFaqs($query),
            'document' => $this->searchDocuments($query),
            default    => [],
        };

        if (empty($results)) {
            return [
                'answer'   => "Maaf, saya belum menemukan {$contentType} yang relevan dengan \"{$query}\" pada basis data CSIRT.",
                'action'   => "search_{$modelType}",
                'metadata' => ['query' => $query, 'count' => 0],
            ];
        }

        $prompts = AdminPromptBuilder::buildSearchPrompt($contentType, $query, $results);
        $answer  = $this->callAI($prompts['system'], $prompts['user']);

        return [
            'answer'   => $answer,
            'action'   => "search_{$modelType}",
            'metadata' => [
                'query'   => $query,
                'count'   => count($results),
                'results' => $results,
            ],
        ];
    }

    /**
     * Pertanyaan umum admin yang tidak masuk kategori aksi spesifik.
     */
    public function handleGeneral(string $question): array
    {
        $prompts = AdminPromptBuilder::buildGeneralPrompt($question);

        $answer = $this->callAI($prompts['system'], $prompts['user']);

        return [
            'answer'   => $answer,
            'action'   => 'general',
            'metadata' => [],
        ];
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Panggil AI provider melalui AIProviderService.
     * Mengirim system prompt dan user message secara terpisah.
     *
     * Fallback ke pesan default jika provider error.
     */
    private function callAI(string $systemPrompt, string $userMessage): string
    {
        $apiKey = config('services.ai_provider.key');
        $apiUrl = config('services.ai_provider.url');
        $model  = config('services.ai_provider.model');

        if (empty($apiKey) || $apiKey === 'your-api-key-here') {
            return $this->fallbackResponse($userMessage);
        }

        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Content-Type'  => 'application/json',
            ])
                ->timeout(45)
                ->post($apiUrl, [
                    'model'       => $model,
                    'messages'    => [
                        ['role' => 'system', 'content' => $systemPrompt],
                        ['role' => 'user',   'content' => $userMessage],
                    ],
                    'max_tokens'  => 2000,
                    'temperature' => 0.3,
                ]);

            if (! $response->successful()) {
                throw new \RuntimeException("AI API error: {$response->status()}");
            }

            $data   = $response->json();
            $answer = $data['choices'][0]['message']['content'] ?? null;

            if (empty($answer)) {
                throw new \RuntimeException('Empty AI response.');
            }

            return trim($answer);

        } catch (\Exception $e) {
            Log::error('AIAdminAssistantService::callAI error: ' . $e->getMessage());

            return $this->fallbackResponse($userMessage);
        }
    }

    /**
     * Fallback jika AI provider tidak tersedia.
     */
    private function fallbackResponse(string $input): string
    {
        return <<<EOT
⚠️ **AI Provider tidak terkonfigurasi**

Untuk menggunakan fitur AI Admin Assistant, harap konfigurasikan variabel berikut di file `.env`:

```
AI_PROVIDER_KEY=your-api-key-here
AI_PROVIDER_URL=https://api.groq.com/openai/v1/chat/completions
AI_MODEL=llama-3.1-8b-instant
```

Setelah dikonfigurasi, jalankan `php artisan config:clear` dan coba kembali.

*Permintaan Anda: "{$input}"*
EOT;
    }

    /**
     * Kumpulkan statistik dari database.
     */
    private function collectStats(): array
    {
        return [
            'posts' => [
                'total'     => DB::table('posts')->count(),
                'published' => DB::table('posts')->where('status', 'published')->count(),
                'draft'     => DB::table('posts')->where('status', 'draft')->count(),
            ],
            'faqs' => [
                'total'     => DB::table('faqs')->count(),
                'published' => DB::table('faqs')->where('is_published', true)->count(),
            ],
            'documents' => [
                'total' => DB::table('documents')->count(),
            ],
            'incidents' => [
                'total'      => DB::table('incidents')->count(),
                'this_month' => DB::table('incidents')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'open'     => DB::table('incidents')
                    ->whereIn('status', ['Baru', 'Diverifikasi', 'Dalam Penyelidikan'])
                    ->count(),
                'resolved' => DB::table('incidents')
                    ->whereIn('status', ['Selesai', 'Ditutup'])
                    ->count(),
                'critical' => DB::table('incidents')
                    ->where('priority', 'Kritikal')
                    ->whereIn('status', ['Baru', 'Diverifikasi', 'Dalam Penyelidikan'])
                    ->count(),
            ],
            'web_applications' => [
                'total'  => DB::table('web_applications')->count(),
                'active' => DB::table('web_applications')->where('app_status', 'aktif')->count(),
            ],
            'mobile_applications' => [
                'total'  => DB::table('mobile_applications')->count(),
                'active' => DB::table('mobile_applications')->where('app_status', 'aktif')->count(),
            ],
            'licenses' => [
                'total'         => DB::table('licenses')->count(),
                'active'        => DB::table('licenses')->where('is_active', true)->count(),
                'expiring_soon' => DB::table('licenses')
                    ->where('is_active', true)
                    ->whereNotNull('expired_at')
                    ->whereBetween('expired_at', [now(), now()->addDays(30)])
                    ->count(),
            ],
            'physical_assets' => [
                'total' => DB::table('physical_assets')->count(),
            ],
            'information_assets' => [
                'total' => DB::table('information_assets')->count(),
            ],
        ];
    }

    /**
     * Ambil artikel terpopuler berdasarkan view_count atau ratings.
     */
    private function getTopPosts(): array
    {
        return Post::where('status', 'published')
            ->select('id', 'title', 'slug')
            ->withCount('ratings')
            ->orderByDesc('ratings_count')
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'title'      => $p->title,
                'slug'       => $p->slug,
                'view_count' => $p->ratings_count,
            ])
            ->toArray();
    }

    /**
     * Ambil FAQ yang paling banyak (berdasarkan urutan).
     */
    private function getTopFaqs(): array
    {
        return Faq::where('is_published', true)
            ->select('id', 'question', 'category')
            ->orderBy('id')
            ->limit(5)
            ->get()
            ->map(fn ($f) => [
                'question' => $f->question,
                'category' => $f->category,
            ])
            ->toArray();
    }

    /**
     * Cari artikel di database.
     */
    private function searchPosts(string $query): array
    {
        return Post::where('status', 'published')
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                  ->orWhere('excerpt', 'ilike', "%{$query}%");
            })
            ->select('id', 'title', 'slug', 'excerpt', 'status')
            ->latest('published_at')
            ->limit(5)
            ->get()
            ->map(fn ($p) => [
                'id'      => $p->id,
                'title'   => $p->title,
                'slug'    => $p->slug,
                'excerpt' => Str::limit(strip_tags($p->excerpt ?? ''), 120),
                'url'     => "/admin/posts/{$p->id}/edit",
            ])
            ->toArray();
    }

    /**
     * Cari FAQ di database.
     */
    private function searchFaqs(string $query): array
    {
        return Faq::where(function ($q) use ($query) {
            $q->where('question', 'ilike', "%{$query}%")
              ->orWhere('answer', 'ilike', "%{$query}%");
        })
            ->select('id', 'question', 'answer', 'category', 'is_published')
            ->orderBy('id')
            ->limit(5)
            ->get()
            ->map(fn ($f) => [
                'id'           => $f->id,
                'question'     => $f->question,
                'answer'       => Str::limit($f->answer, 150),
                'category'     => $f->category,
                'is_published' => $f->is_published,
            ])
            ->toArray();
    }

    /**
     * Cari dokumen di database.
     */
    private function searchDocuments(string $query): array
    {
        return Document::where(function ($q) use ($query) {
            $q->where('title', 'ilike', "%{$query}%")
              ->orWhere('description', 'ilike', "%{$query}%");
        })
            ->select('id', 'title', 'slug', 'description', 'reference_number')
            ->limit(5)
            ->get()
            ->map(fn ($d) => [
                'id'               => $d->id,
                'title'            => $d->title,
                'slug'             => $d->slug,
                'description'      => Str::limit($d->description ?? '', 150),
                'reference_number' => $d->reference_number,
                'url'              => "/admin/documents/{$d->id}/edit",
            ])
            ->toArray();
    }
}
