<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Models\AssetGuideAcknowledgement;
use App\Models\MobileApplication;
use App\Models\WebApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetGuideAcknowledgementController extends Controller
{
    public function toggle(Request $request, string $assetType, string $assetId, string $guideId): JsonResponse|RedirectResponse
    {
        $morphType = $this->morphType($assetType);

        $existing = AssetGuideAcknowledgement::where([
            'asset_type' => $morphType,
            'asset_id' => $assetId,
            'guide_id' => $guideId,
        ])->first();

        if ($existing) {
            $existing->delete();
            $acknowledged = false;
        } else {
            AssetGuideAcknowledgement::create([
                'asset_type' => $morphType,
                'asset_id' => $assetId,
                'guide_id' => $guideId,
                'acknowledged_by' => Auth::id(),
                'acknowledged_at' => now(),
            ]);
            $acknowledged = true;
        }

        if ($request->expectsJson()) {
            return response()->json(['acknowledged' => $acknowledged]);
        }

        return redirect()->back();
    }

    private function morphType(string $type): string
    {
        return match ($type) {
            'web-application' => WebApplication::class,
            'mobile-application' => MobileApplication::class,
            default => abort(404),
        };
    }
}
