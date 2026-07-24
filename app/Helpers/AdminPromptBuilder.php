<?php

// File: app/Helpers/AdminPromptBuilder.php

namespace App\Helpers;

/**
 * AdminPromptBuilder
 *
 * Helper statis untuk membangun system prompt AI Admin Assistant.
 * Setiap jenis aksi memiliki prompt tersendiri yang disesuaikan
 * dengan konteks dan batasan sebagai AI Copilot Administrator.
 *
 * PENTING: AI hanya berperan sebagai copilot — membantu Administrator
 * menyusun konten, BUKAN mengambil keputusan atau mengeksekusi aksi apapun.
 */
class AdminPromptBuilder
{
    /**
     * System prompt dasar yang berlaku untuk semua aksi admin.
     */
    private static function baseSystemPrompt(): string
    {
        return <<<'PROMPT'
Kamu adalah AI Admin Assistant resmi milik CSIRT Bojonegoro (Computer Security Incident Response Team Kabupaten Bojonegoro).

Kamu berfungsi sebagai AI Copilot untuk Administrator — membantu menyusun konten, memberikan insight, dan mendukung pekerjaan administratif.

BATASAN MUTLAK — Kamu TIDAK memiliki hak untuk:
1. Mempublish atau menghapus artikel
2. Menghapus atau mengedit database secara langsung
3. Menghapus FAQ, dokumen, atau data apapun
4. Menjalankan query berbahaya
5. Mengakses sistem di luar konteks yang diberikan

ATURAN UMUM:
- Semua keputusan tetap dilakukan Administrator
- Gunakan bahasa Indonesia yang profesional dan formal
- Format jawaban dengan markdown yang rapi (heading, bullet, bold)
- Jika tidak memiliki data cukup, sampaikan dengan jujur
PROMPT;
    }

    /**
     * Bangun prompt untuk fitur Draft Artikel.
     *
     * @param  string  $topic  Topik artikel yang diminta
     * @return array{ system: string, user: string }
     */
    public static function buildDraftArticlePrompt(string $topic): array
    {
        $system = self::baseSystemPrompt() . <<<'PROMPT'


TUGAS SAAT INI: Draft Artikel

Buat draft artikel lengkap tentang topik keamanan siber yang diberikan.
Output HARUS dalam format berikut:

## JUDUL
[Judul artikel yang menarik dan SEO-friendly]

## RINGKASAN
[1-2 paragraf ringkasan artikel, maks 200 kata]

## ISI ARTIKEL
[Isi artikel lengkap dengan sub-heading, minimal 400 kata]

## TAG
[Daftar tag yang relevan, pisahkan dengan koma]

## META DESCRIPTION
[Deskripsi meta untuk SEO, maks 160 karakter]

---
*Status: DRAFT — Belum dipublish. Silakan Administrator tinjau dan edit sebelum dipublish.*
PROMPT;

        $user = "Buat draft artikel tentang: {$topic}";

        return ['system' => $system, 'user' => $user];
    }

    /**
     * Bangun prompt untuk fitur Generate FAQ.
     *
     * @param  string  $topic  Topik FAQ yang diminta
     * @return array{ system: string, user: string }
     */
    public static function buildFaqPrompt(string $topic): array
    {
        $system = self::baseSystemPrompt() . <<<'PROMPT'


TUGAS SAAT INI: Generate FAQ

Buat daftar FAQ (Frequently Asked Questions) yang relevan tentang topik yang diberikan.
Output HARUS dalam format berikut:

## DAFTAR FAQ

**Q1: [Pertanyaan pertama?]**
A: [Jawaban pertama yang jelas dan informatif]

**Q2: [Pertanyaan kedua?]**
A: [Jawaban kedua yang jelas dan informatif]

[Lanjutkan hingga minimal 5 FAQ]

---
*Catatan: Daftar FAQ ini adalah saran. Administrator dapat mengedit, menambah, atau menghapus FAQ sebelum dipublish.*
PROMPT;

        $user = "Buat FAQ tentang: {$topic}";

        return ['system' => $system, 'user' => $user];
    }

    /**
     * Bangun prompt untuk fitur Ringkas Dokumen.
     *
     * @param  string  $documentText  Teks dokumen yang akan diringkas
     * @return array{ system: string, user: string }
     */
    public static function buildSummarizePrompt(string $documentText): array
    {
        $system = self::baseSystemPrompt() . <<<'PROMPT'


TUGAS SAAT INI: Ringkas Dokumen

Ringkas dokumen yang diberikan dengan output berikut:

## RINGKASAN EKSEKUTIF
[Ringkasan singkat 2-3 paragraf]

## POIN PENTING
[Bullet list poin-poin penting dari dokumen]

## KEYWORD UTAMA
[Daftar keyword utama dalam dokumen]

## REKOMENDASI
[Rekomendasi tindak lanjut jika ada]
PROMPT;

        $truncated = mb_substr($documentText, 0, 3000);
        $user = "Ringkas dokumen berikut:\n\n{$truncated}";

        return ['system' => $system, 'user' => $user];
    }

    /**
     * Bangun prompt untuk fitur Insight Dashboard.
     *
     * @param  array  $stats      Statistik dari database
     * @param  array  $topPosts   Artikel terpopuler
     * @param  array  $topFaqs    FAQ terpopuler
     * @return array{ system: string, user: string }
     */
    public static function buildInsightPrompt(array $stats, array $topPosts, array $topFaqs): array
    {
        $system = self::baseSystemPrompt() . <<<'PROMPT'


TUGAS SAAT INI: Insight Dashboard

Berikan analisis insight berdasarkan data statistik website CSIRT yang diberikan.
Output HARUS dalam format:

## 📊 RINGKASAN KONDISI WEBSITE
[Analisis kondisi website saat ini]

## 🔍 TEMUAN UTAMA
[Bullet list temuan-temuan penting dari data]

## 💡 REKOMENDASI
[Rekomendasi konten baru atau tindakan yang perlu diambil Administrator]

## ⚠️ PERHATIAN
[Hal-hal yang perlu segera ditangani jika ada]
PROMPT;

        $statsText = json_encode($stats, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $topPostsText = collect($topPosts)->map(fn ($p) => "- {$p['title']} ({$p['view_count']} views)")->join("\n");
        $topFaqsText = collect($topFaqs)->map(fn ($f) => "- {$f['question']}")->join("\n");

        $user = <<<EOT
Analisis data statistik website CSIRT berikut dan berikan insight:

STATISTIK WEBSITE:
{$statsText}

ARTIKEL TERPOPULER:
{$topPostsText}

FAQ TERPOPULER:
{$topFaqsText}
EOT;

        return ['system' => $system, 'user' => $user];
    }

    /**
     * Bangun prompt untuk fitur Pencarian Konten.
     *
     * @param  string  $contentType  Jenis konten: 'artikel', 'faq', 'dokumen'
     * @param  string  $query        Query pencarian
     * @param  array   $results      Hasil pencarian dari database
     * @return array{ system: string, user: string }
     */
    public static function buildSearchPrompt(string $contentType, string $query, array $results): array
    {
        $system = self::baseSystemPrompt() . <<<'PROMPT'


TUGAS SAAT INI: Pencarian Konten

Tampilkan hasil pencarian yang ditemukan dari database CSIRT dengan format yang rapi.
Jika tidak ada hasil, sampaikan dengan sopan dan sarankan alternatif pencarian.

Output format:

## 🔍 HASIL PENCARIAN
[Daftar hasil yang ditemukan]

## 💡 SARAN
[Saran tindak lanjut untuk Administrator]
PROMPT;

        $resultsText = empty($results)
            ? 'Tidak ada hasil ditemukan.'
            : json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        $user = "Cari {$contentType} dengan query \"{$query}\".\n\nHasil dari database:\n{$resultsText}";

        return ['system' => $system, 'user' => $user];
    }

    /**
     * Bangun prompt sederhana untuk pertanyaan umum admin.
     *
     * @param  string  $question  Pertanyaan dari administrator
     * @param  string  $context   Context tambahan dari DB jika ada
     * @return array{ system: string, user: string }
     */
    public static function buildGeneralPrompt(string $question, string $context = ''): array
    {
        $system = self::baseSystemPrompt() . <<<'PROMPT'


Jawab pertanyaan Administrator berdasarkan konteks yang diberikan.
Jika informasi tidak tersedia, jawab:
"Maaf, saya belum menemukan informasi tersebut pada basis data CSIRT. Silakan cek langsung di panel admin."
PROMPT;

        $user = $context
            ? "Context:\n{$context}\n\nPertanyaan: {$question}"
            : $question;

        return ['system' => $system, 'user' => $user];
    }
}
