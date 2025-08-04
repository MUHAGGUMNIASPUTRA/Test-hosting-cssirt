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
      'posts' => [
        'total' => DB::table('posts')->count(),
        'published' => DB::table('posts')->where('status', 'Published')->count(),
        'draft' => DB::table('posts')->where('status', 'Draft')->count(),
      ],
      'services' => [
        'total' => DB::table('services')->count(),
        'active' => DB::table('services')->where('is_active', true)->count(),
      ],
      'documents' => [
        'total' => DB::table('documents')->count(),
      ],
      'users' => [
        'total' => DB::table('users')->count(),
        'active' => DB::table('users')->whereNotNull('created_at')->count(),
      ],
      'faqs' => [
        'total' => DB::table('faqs')->count(),
        'published' => DB::table('faqs')->where('is_published', true)->count(),
      ]
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

    // Get recent posts
    $recentPosts = DB::table('posts')
      ->select('id', 'title', 'status', 'views_count', 'created_at', 'published_at')
      ->orderBy('created_at', 'desc')
      ->limit(3)
      ->get();

    // Get recent users
    $recentUsers = DB::table('users')
      ->select('id', 'name', 'email', 'role', 'created_at')
      ->orderBy('created_at', 'desc')
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
      'recentPosts' => $recentPosts,
      'recentUsers' => $recentUsers,
      'systemAlerts' => $systemAlerts,
    ]);
  }
}
