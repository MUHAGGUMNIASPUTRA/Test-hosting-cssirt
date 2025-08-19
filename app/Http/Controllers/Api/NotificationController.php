<?php
// filepath: app/Http/Controllers/Api/NotificationController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
  /**
   * Get notifications for admin
   */
  public function getIncidentNotifications(): JsonResponse
  {
    // Get all unread incidents
    $unreadIncidents = Incident::unread()
      ->with(['incidentType', 'readBy'])
      ->orderBy('created_at', 'desc')
      ->get();

    // Get last 10 read incidents
    $readIncidents = Incident::read()
      ->with(['incidentType', 'readBy'])
      ->orderBy('created_at', 'desc')
      ->limit(10)
      ->get();

    return response()->json([
      'unread' => $unreadIncidents,
      'read' => $readIncidents,
      'unread_count' => $unreadIncidents->count()
    ]);
  }

  /**
   * Mark incident as read
   */
  public function markAsRead(Request $request, Incident $incident): JsonResponse
  {
    $incident->update([
      'is_read' => true,
      'read_by' => Auth::id(),
      'read_at' => now()
    ]);

    return response()->json(['success' => true]);
  }

  /**
   * Mark all incidents as read
   */
  public function markAllAsRead(Request $request): JsonResponse
  {
    Incident::unread()->update([
      'is_read' => true,
      'read_by' => Auth::id(),
      'read_at' => now()
    ]);

    return response()->json(['success' => true]);
  }
}
