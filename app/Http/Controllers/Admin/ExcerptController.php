<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExcerptController extends Controller
{
  public function generate(Request $request)
  {
    $request->validate([
      'title' => 'required|string|max:255',
      'body' => 'required|string'
    ]);

    try {
      $apiKey = config('services.google_ai.api_key');

      if (!$apiKey) {
        return response()->json([
          'success' => false,
          'message' => 'Google AI API key not configured'
        ], 500);
      }

      // Clean the body content (remove HTML tags)
      $cleanBody = strip_tags($request->body);
      $cleanBody = html_entity_decode($cleanBody);

      // Limit content length to avoid API limits
      $contentToSummarize = substr($cleanBody, 0, 4000);

      $prompt = "Buatlah ringkasan singkat dalam bahasa Indonesia untuk artikel berikut ini. Ringkasan harus:\n" .
                "- Maksimal 35 kata\n" .
                "- Menjelaskan inti dari artikel\n" .
                "- Menggunakan bahasa yang mudah dipahami\n" .
                "- Menarik untuk dibaca\n\n" .
                "Judul: {$request->title}\n\n" .
                "Isi Artikel: {$contentToSummarize}\n\n" .
                "Ringkasan:";

      $response = Http::timeout(30)
        ->withHeaders([
          'Content-Type' => 'application/json',
        ])
        ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key={$apiKey}", [
          'contents' => [
            [
              'parts' => [
                [
                  'text' => $prompt
                ]
              ]
            ]
          ],
          'generationConfig' => [
            'temperature' => 0.7,
            'topK' => 40,
            'topP' => 0.95,
            'maxOutputTokens' => 100,
          ]
        ]);

      if ($response->successful()) {
          $data = $response->json();

          if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
            $excerpt = trim($data['candidates'][0]['content']['parts'][0]['text']);

            // Clean up the response
            $excerpt = preg_replace('/^(Ringkasan:|Summary:)/i', '', $excerpt);
            $excerpt = trim($excerpt);

            // Validate word count
            $wordCount = str_word_count($excerpt);

            return response()->json([
              'success' => true,
              'excerpt' => $excerpt,
              'word_count' => $wordCount,
              'message' => 'Ringkasan berhasil dibuat'
            ]);
          } else {
            Log::error('Unexpected Google AI response structure', ['response' => $data]);

            return response()->json([
              'success' => false,
              'message' => 'Format respons dari AI tidak sesuai'
            ], 500);
          }
      } else {
        Log::error('Google AI API Error', [
          'status' => $response->status(),
          'body' => $response->body()
        ]);

        return response()->json([
          'success' => false,
          'message' => 'Gagal menghubungi layanan AI: ' . $response->body()
        ], $response->status());
      }

    } catch (\Exception $e) {
      Log::error('Excerpt generation error', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);

      return response()->json([
        'success' => false,
        'message' => 'Terjadi kesalahan saat membuat ringkasan: ' . $e->getMessage()
      ], 500);
    }
  }
}
