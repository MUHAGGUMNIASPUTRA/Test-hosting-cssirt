<?php

// Tujuan: Endpoint API untuk mencari daftar aset virtual (web + mobile) di form insiden
// Caller: Incident Create/Edit form (Vue fetch via axios)
// Side Effects: none

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileApplication;
use App\Models\WebApplication;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class VirtualAssetController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        $limit = 20;

        $webApps = WebApplication::select(['id', 'name', 'app_status'])
            ->when($search, fn ($q) => $q->where('name', 'ilike', "%{$search}%"))
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'asset_type' => 'web-application',
                'asset_type_label' => 'Aplikasi Web',
                'status' => $item->app_status?->value,
            ]);

        $mobileApps = MobileApplication::select(['id', 'name', 'app_status'])
            ->when($search, fn ($q) => $q->where('name', 'ilike', "%{$search}%"))
            ->orderBy('name')
            ->limit($limit)
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'asset_type' => 'mobile-application',
                'asset_type_label' => 'Aplikasi Mobile',
                'status' => $item->app_status?->value,
            ]);

        return response()->json($webApps->merge($mobileApps)->values());
    }
}
