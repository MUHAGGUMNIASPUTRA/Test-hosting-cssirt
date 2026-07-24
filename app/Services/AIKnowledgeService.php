<?php

// File: app/Services/AIKnowledgeService.php

namespace App\Services;

use App\Models\Document;
use App\Models\Faq;
use App\Models\Post;
use App\Models\Service;
use Illuminate\Support\Str;

/**
 * AIKnowledgeService
 *
 * Bertanggung jawab untuk mencari dan menyusun context dari database
 * berdasarkan pertanyaan user. Implementasi RAG (Retrieval-Augmented Generation)
 * sederhana menggunakan keyword matching dengan prioritas sumber.
 */
class AIKnowledgeService
{
    /**
     * Jumlah maksimal item per sumber
     */
    private const MAX_PER_SOURCE = 3;

    /**
     * Panjang maksimal teks body yang disertakan dalam context
     */
    private const MAX_BODY_LENGTH = 800;

    /**
     * Ambil context dan referensi berdasarkan pertanyaan user.
     *
     * @param  string  $question
     * @return array{ context: string, references: array, has_result: bool }
     */
    public function retrieve(string $question): array
    {
        $keywords = $this->extractKeywords($question);
        $contextParts = [];
        $references = [];

        // 1. FAQ (Prioritas tertinggi)
        $faqResult = $this->searchFaq($keywords);
        if (! empty($faqResult['items'])) {
            $contextParts[] = $faqResult['context'];
            foreach ($faqResult['references'] as $ref) {
                $references[] = $ref;
            }
        }

        // 2. Artikel / Posts
        $postResult = $this->searchPosts($keywords);
        if (! empty($postResult['items'])) {
            $contextParts[] = $postResult['context'];
            foreach ($postResult['references'] as $ref) {
                $references[] = $ref;
            }
        }

        // 3. Dokumen Panduan
        $documentResult = $this->searchDocuments($keywords);
        if (! empty($documentResult['items'])) {
            $contextParts[] = $documentResult['context'];
            foreach ($documentResult['references'] as $ref) {
                $references[] = $ref;
            }
        }

        // 4. RFC2350
        $rfc2350Result = $this->searchRfc2350($keywords);
        if (! empty($rfc2350Result['items'])) {
            $contextParts[] = $rfc2350Result['context'];
            foreach ($rfc2350Result['references'] as $ref) {
                $references[] = $ref;
            }
        }

        // 5. Layanan / Services
        $serviceResult = $this->searchServices($keywords);
        if (! empty($serviceResult['items'])) {
            $contextParts[] = $serviceResult['context'];
            foreach ($serviceResult['references'] as $ref) {
                $references[] = $ref;
            }
        }

        // 6. Informasi Kontak (statis)
        $contactResult = $this->getContactInfo($keywords);
        if (! empty($contactResult['context'])) {
            $contextParts[] = $contactResult['context'];
            foreach ($contactResult['references'] as $ref) {
                $references[] = $ref;
            }
        }

        $context = implode("\n\n---\n\n", $contextParts);
        $hasResult = ! empty($contextParts);

        return [
            'context'    => $context,
            'references' => $references,
            'has_result' => $hasResult,
        ];
    }

    /**
     * Ekstrak kata kunci penting dari pertanyaan user.
     * Menghapus stop words umum bahasa Indonesia dan Inggris.
     */
    private function extractKeywords(string $question): array
    {
        $stopWords = [
            'yang', 'dan', 'atau', 'dengan', 'untuk', 'pada', 'di', 'ke', 'dari',
            'ini', 'itu', 'adalah', 'ada', 'tidak', 'bisa', 'dapat', 'akan',
            'apa', 'bagaimana', 'kapan', 'dimana', 'siapa', 'mengapa', 'berapa',
            'cara', 'tolong', 'mohon', 'bantu', 'saya', 'kami', 'kita', 'mereka',
            'the', 'a', 'an', 'is', 'are', 'was', 'were', 'be', 'been',
            'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would',
            'what', 'how', 'when', 'where', 'who', 'why', 'which',
        ];

        $words = preg_split('/\s+/', strtolower(trim($question)));
        $keywords = array_filter($words, function ($word) use ($stopWords) {
            $clean = preg_replace('/[^a-z0-9]/', '', $word);

            return strlen($clean) >= 3 && ! in_array($clean, $stopWords);
        });

        return array_values(array_unique($keywords));
    }

    /**
     * Bangun LIKE query untuk pencarian multi-keyword.
     */
    private function buildSearchQuery($query, array $columns, array $keywords)
    {
        return $query->where(function ($q) use ($columns, $keywords) {
            foreach ($keywords as $keyword) {
                $q->orWhere(function ($inner) use ($columns, $keyword) {
                    foreach ($columns as $column) {
                        $inner->orWhere($column, 'ilike', "%{$keyword}%");
                    }
                });
            }
        });
    }

    /**
     * Cari pada tabel FAQ.
     */
    private function searchFaq(array $keywords): array
    {
        if (empty($keywords)) {
            return ['items' => [], 'context' => '', 'references' => []];
        }

        $query = Faq::where('is_published', true);
        $query = $this->buildSearchQuery($query, ['question', 'answer'], $keywords);
        $faqs = $query->select('id', 'question', 'answer', 'category')
            ->limit(self::MAX_PER_SOURCE)
            ->get();

        if ($faqs->isEmpty()) {
            return ['items' => [], 'context' => '', 'references' => []];
        }

        $contextLines = ["[SUMBER: FAQ CSIRT Bojonegoro]"];
        foreach ($faqs as $faq) {
            $contextLines[] = "T: {$faq->question}";
            $contextLines[] = "J: {$faq->answer}";
            if ($faq->category) {
                $contextLines[] = "Kategori: {$faq->category}";
            }
            $contextLines[] = '';
        }

        return [
            'items'      => $faqs->toArray(),
            'context'    => implode("\n", $contextLines),
            'references' => [
                [
                    'type'  => 'faq',
                    'label' => 'FAQ CSIRT Bojonegoro',
                    'url'   => '/faq',
                ],
            ],
        ];
    }

    /**
     * Cari pada tabel Posts / Artikel.
     */
    private function searchPosts(array $keywords): array
    {
        if (empty($keywords)) {
            return ['items' => [], 'context' => '', 'references' => []];
        }

        $query = Post::where('status', 'published');
        $query = $this->buildSearchQuery($query, ['title', 'excerpt', 'body'], $keywords);
        $posts = $query->select('id', 'title', 'slug', 'excerpt', 'body')
            ->latest('published_at')
            ->limit(self::MAX_PER_SOURCE)
            ->get();

        if ($posts->isEmpty()) {
            return ['items' => [], 'context' => '', 'references' => []];
        }

        $contextLines = ["[SUMBER: ARTIKEL CSIRT Bojonegoro]"];
        $references = [];

        foreach ($posts as $post) {
            $body = strip_tags($post->body ?? '');
            $body = Str::limit($body, self::MAX_BODY_LENGTH);
            $excerpt = strip_tags($post->excerpt ?? '');

            $contextLines[] = "Judul: {$post->title}";
            if ($excerpt) {
                $contextLines[] = "Ringkasan: {$excerpt}";
            }
            if ($body) {
                $contextLines[] = "Isi: {$body}";
            }
            $contextLines[] = '';

            $references[] = [
                'type'  => 'article',
                'label' => "Artikel: {$post->title}",
                'url'   => "/posts/{$post->slug}",
            ];
        }

        return [
            'items'      => $posts->toArray(),
            'context'    => implode("\n", $contextLines),
            'references' => $references,
        ];
    }

    /**
     * Cari pada tabel Documents (Panduan, bukan RFC2350).
     */
    private function searchDocuments(array $keywords): array
    {
        if (empty($keywords)) {
            return ['items' => [], 'context' => '', 'references' => []];
        }

        $query = Document::published()
            ->where('is_public', true)
            ->whereNull('version'); // Exclude RFC2350

        $query = $this->buildSearchQuery($query, ['title', 'description'], $keywords);
        $documents = $query->select('id', 'title', 'slug', 'description', 'reference_number', 'version')
            ->limit(self::MAX_PER_SOURCE)
            ->get();

        if ($documents->isEmpty()) {
            return ['items' => [], 'context' => '', 'references' => []];
        }

        $contextLines = ["[SUMBER: DOKUMEN PANDUAN CSIRT Bojonegoro]"];
        $references = [];

        foreach ($documents as $doc) {
            $contextLines[] = "Judul: {$doc->title}";
            if ($doc->reference_number) {
                $contextLines[] = "Nomor Referensi: {$doc->reference_number}";
            }
            if ($doc->description) {
                $contextLines[] = "Deskripsi: {$doc->description}";
            }
            $contextLines[] = '';

            $references[] = [
                'type'  => 'document',
                'label' => "Panduan: {$doc->title}",
                'url'   => "/documents/{$doc->slug}/view",
            ];
        }

        return [
            'items'      => $documents->toArray(),
            'context'    => implode("\n", $contextLines),
            'references' => $references,
        ];
    }

    /**
     * Cari dokumen RFC2350.
     */
    private function searchRfc2350(array $keywords): array
    {
        // Hanya sertakan RFC2350 jika ada keyword yang relevan
        $rfc2350Keywords = ['rfc', 'rfc2350', 'kebijakan', 'policy', 'cirt', 'csirt', 'profil', 'organisasi'];
        $relevant = array_intersect($keywords, $rfc2350Keywords);

        if (empty($relevant) && ! empty($keywords)) {
            return ['items' => [], 'context' => '', 'references' => []];
        }

        $document = Document::where('version', 'RFC2350')->first();

        if (! $document) {
            return ['items' => [], 'context' => '', 'references' => []];
        }

        $contextLines = [
            "[SUMBER: RFC2350 - Dokumen Resmi CSIRT Bojonegoro]",
            "Judul: {$document->title}",
        ];

        if ($document->description) {
            $contextLines[] = "Deskripsi: {$document->description}";
        }

        return [
            'items'   => [$document->toArray()],
            'context' => implode("\n", $contextLines),
            'references' => [
                [
                    'type'  => 'rfc2350',
                    'label' => 'RFC2350 CSIRT Bojonegoro',
                    'url'   => '/rfc2350',
                ],
            ],
        ];
    }

    /**
     * Cari pada tabel Services / Layanan.
     */
    private function searchServices(array $keywords): array
    {
        if (empty($keywords)) {
            return ['items' => [], 'context' => '', 'references' => []];
        }

        $query = Service::where('is_active', true);
        $query = $this->buildSearchQuery($query, ['name', 'short_description', 'full_description'], $keywords);
        $services = $query->select('id', 'name', 'slug', 'short_description', 'full_description')
            ->limit(self::MAX_PER_SOURCE)
            ->get();

        if ($services->isEmpty()) {
            return ['items' => [], 'context' => '', 'references' => []];
        }

        $contextLines = ["[SUMBER: LAYANAN CSIRT Bojonegoro]"];

        foreach ($services as $service) {
            $contextLines[] = "Layanan: {$service->name}";
            if ($service->short_description) {
                $contextLines[] = "Deskripsi Singkat: {$service->short_description}";
            }
            if ($service->full_description) {
                $fullDesc = strip_tags($service->full_description);
                $contextLines[] = "Deskripsi Lengkap: " . Str::limit($fullDesc, self::MAX_BODY_LENGTH);
            }
            $contextLines[] = '';
        }

        return [
            'items'   => $services->toArray(),
            'context' => implode("\n", $contextLines),
            'references' => [
                [
                    'type'  => 'service',
                    'label' => 'Layanan CSIRT Bojonegoro',
                    'url'   => '/services',
                ],
            ],
        ];
    }

    /**
     * Dapatkan informasi kontak statis CSIRT Bojonegoro.
     * Selalu disertakan jika pertanyaan mengandung keyword kontak.
     */
    private function getContactInfo(array $keywords): array
    {
        $contactKeywords = [
            'kontak', 'contact', 'hubungi', 'telepon', 'phone', 'email', 'alamat',
            'address', 'jam', 'operasional', 'hours', 'darurat', 'emergency',
            'lapor', 'report', 'whatsapp', 'wa',
        ];

        $relevant = array_intersect($keywords, $contactKeywords);

        if (empty($relevant) && ! empty($keywords)) {
            return ['context' => '', 'references' => []];
        }

        $contextLines = [
            "[SUMBER: INFORMASI KONTAK CSIRT Bojonegoro]",
            "Organisasi: CSIRT Kabupaten Bojonegoro",
            "Instansi: Dinas Komunikasi dan Informatika (Diskominfo) Kabupaten Bojonegoro",
            "Alamat: Jl. P. Mas Tumapel No. 1, Bojonegoro, Jawa Timur 62115",
            "Telepon Darurat: 0353-881826 (24/7)",
            "Website: https://csirt.bojonegorokab.go.id",
            "Jam Operasional: Senin–Jumat 08.00–16.00 WIB (darurat 24/7)",
            "Layanan Pelaporan Insiden: Tersedia 24 jam setiap hari",
        ];

        return [
            'context' => implode("\n", $contextLines),
            'references' => [
                [
                    'type'  => 'contact',
                    'label' => 'Kontak CSIRT Bojonegoro',
                    'url'   => '/contact',
                ],
            ],
        ];
    }
}
