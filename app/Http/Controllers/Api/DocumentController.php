<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function __construct(private readonly DocumentService $documentService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = [
            'search' => $request->input('search'),
            'areas' => $request->input('areas', []),
        ];

        $paginator = $this->documentService->list($filters);

        return response()->json([
            'status' => 'success',
            'data' => $paginator,
        ]);
    }
}
