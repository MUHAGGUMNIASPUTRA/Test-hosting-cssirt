<?php

// File: app/Http/Controllers/Admin/DashboardController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        // Get statistics from database
        $stats = [
            'incidents' => [
                'total' => DB::table('incidents')->count(),
                'thisMonth' => DB::table('incidents')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'open' => DB::table('incidents')
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
            'services' => [
                'total' => DB::table('services')->count(),
                'active' => DB::table('services')->where('is_active', true)->count(),
            ],
            'documents' => [
                'total' => DB::table('documents')->count(),
            ],
            'faqs' => [
                'total' => DB::table('faqs')->count(),
                'published' => DB::table('faqs')->where('is_published', true)->count(),
            ],
            'webApplications' => [
                'total' => DB::table('web_applications')->count(),
                'active' => DB::table('web_applications')->where('app_status', 'aktif')->count(),
            ],
            'mobileApplications' => [
                'total' => DB::table('mobile_applications')->count(),
                'active' => DB::table('mobile_applications')->where('app_status', 'aktif')->count(),
            ],
            'licenses' => [
                'total' => DB::table('licenses')->count(),
                'active' => DB::table('licenses')->where('is_active', true)->count(),
                'expiringSoon' => DB::table('licenses')
                    ->where('is_active', true)
                    ->whereNotNull('expired_at')
                    ->whereBetween('expired_at', [now(), now()->addDays(30)])
                    ->count(),
            ],
            'physicalAssets' => [
                'total' => DB::table('physical_assets')->count(),
            ],
            'informationAssets' => [
                'total' => DB::table('information_assets')->count(),
            ],
        ];

        // Get recent incidents
        $recentIncidents = DB::table('incidents')
            ->join('incident_types', 'incidents.incident_type_id', '=', 'incident_types.id')
            ->select(
                'incidents.*',
                'incident_types.name as type_name'
            )
            ->orderBy('incidents.reported_at', 'desc')
            ->limit(5)
            ->get();

        // Get system alerts (announcements)
        $systemAlerts = DB::table('announcements')
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentIncidents' => $recentIncidents,
            'systemAlerts' => $systemAlerts,
        ]);
    }
}
