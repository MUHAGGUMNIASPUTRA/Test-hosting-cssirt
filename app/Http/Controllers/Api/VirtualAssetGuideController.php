<?php

// Tujuan: Return list of VirtualAssetGuides untuk document picker
// Caller: VirtualAssetGuides/Create.vue component
// Side Effects: Database read only

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VirtualAssetGuide;
use Illuminate\Http\Request;

class VirtualAssetGuideController extends Controller
{
    public function index(Request $request)
    {
        $query = VirtualAssetGuide::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%'.$request->search.'%');
        }

        $guides = $query->orderBy('name')->get(['id', 'name', 'description', 'type']);

        return response()->json([
            'status' => 'success',
            'data' => $guides,
        ]);
    }
}
