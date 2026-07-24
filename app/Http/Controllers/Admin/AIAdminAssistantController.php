<?php

// File: app/Http/Controllers/Admin/AIAdminAssistantController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AIAdminAssistantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * AIAdminAssistantController
 *
 * Menangani endpoint POST /api/admin/ai-assistant.
 * Hanya dapat diakses oleh user yang sudah login (auth + staff middleware).
 *
 * Request body:
 *   {
 *     "action":  "draft_article" | "generate_faq" | "summarize" | "dashboard_insight" | ...
 *     "prompt":  "Topik atau teks input dari Administrator"
 *   }
 *
 * Response:
 *   {
 *     "answer":   "...",
 *     "action":   "draft_article",
 *     "metadata": { ... }
 *   }
 */
class AIAdminAssistantController extends Controller
{
    /**
     * Daftar action yang valid.
     */
    private const VALID_ACTIONS = [
        'draft_article',
        'generate_faq',
        'summarize',
        'dashboard_insight',
        'statistics',
        'search_article',
        'search_faq',
        'search_document',
        'general',
    ];

    public function __construct(
        private readonly AIAdminAssistantService $assistantService,
    ) {}

    /**
     * Terima request dari AI Admin Assistant panel dan proses dengan service.
     *
     * POST /api/admin/ai-assistant
     */
    public function ask(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'string', 'in:' . implode(',', self::VALID_ACTIONS)],
            'prompt' => ['nullable', 'string', 'max:5000'],
        ]);

        $action = $validated['action'];
        $prompt = trim($validated['prompt'] ?? '');

        try {
            $result = $this->assistantService->handle($action, $prompt);

            return response()->json([
                'answer'   => $result['answer'],
                'action'   => $result['action'],
                'metadata' => $result['metadata'] ?? [],
                'success'  => true,
            ]);

        } catch (\Exception $e) {
            Log::error('AIAdminAssistantController error: ' . $e->getMessage(), [
                'action'  => $action,
                'user_id' => $request->user()?->id,
            ]);

            return response()->json([
                'answer'  => 'Maaf, terjadi kesalahan saat memproses permintaan Anda. Silakan coba lagi.',
                'action'  => $action,
                'metadata' => [],
                'success'  => false,
            ]);
        }
    }
}
