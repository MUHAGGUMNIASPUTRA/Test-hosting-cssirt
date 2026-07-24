<?php

// File: app/Http/Controllers/Api/AIAssistantController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AIAssistantRequest;
use App\Services\AIKnowledgeService;
use App\Services\AIProviderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * AIAssistantController
 *
 * Menangani endpoint POST /api/ai-assistant.
 * Mengintegrasikan AIKnowledgeService (RAG retrieval)
 * dengan AIProviderService (LLM generation).
 */
class AIAssistantController extends Controller
{
    public function __construct(
        private readonly AIKnowledgeService $knowledgeService,
        private readonly AIProviderService $providerService,
    ) {}

    /**
     * Terima pertanyaan user, cari context dari DB, kirim ke AI, kembalikan jawaban.
     *
     * POST /api/ai-assistant
     * Body: { "question": "..." }
     *
     * Response: {
     *   "answer": "...",
     *   "references": [{ "type": "faq", "label": "...", "url": "..." }],
     *   "has_result": true
     * }
     */
    public function ask(AIAssistantRequest $request): JsonResponse
    {
        $question = trim($request->validated('question'));

        try {
            // Step 1: RAG — Ambil context dan referensi dari database
            $retrieved = $this->knowledgeService->retrieve($question);

            // Step 2: Generate jawaban menggunakan AI provider
            $answer = $this->providerService->ask($question, $retrieved['context']);

            return response()->json([
                'answer'     => $answer,
                'references' => $retrieved['references'],
                'has_result' => $retrieved['has_result'],
            ]);

        } catch (\Exception $e) {
            Log::error('AIAssistantController error: ' . $e->getMessage(), [
                'question' => $question,
            ]);

            return response()->json([
                'answer'     => 'Maaf, terjadi kesalahan saat memproses pertanyaan Anda. Silakan coba lagi atau hubungi tim CSIRT secara langsung.',
                'references' => [],
                'has_result' => false,
            ], 200); // Tetap 200 agar frontend bisa menampilkan pesan error dengan baik
        }
    }
}
