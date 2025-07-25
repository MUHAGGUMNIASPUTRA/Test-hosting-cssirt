<?php
// File: app/Http/Controllers/IncidentController.php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\IncidentType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class IncidentController extends Controller
{
  /**
   * Show the form for creating a new incident report.
   */
  public function create(): Response
  {
    return Inertia::render('Incidents/Create', [
      'incidentTypes' => IncidentType::all(['id', 'name']),
    ]);
  }

  /**
   * Store a newly created incident report in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'reporter_name' => 'required|string|max:255',
      'reporter_email' => 'required|email|max:255',
      'reporter_phone' => 'nullable|string|max:20',
      'incident_type_id' => 'required|exists:incident_types,id',
      'incident_at' => 'required|date',
      'description' => 'required|string',
      'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip|max:5120', // Max 5MB
    ]);

    $path = null;
    if ($request->hasFile('attachment')) {
      $path = $request->file('attachment')->store('attachments', 'public');
    }

    Incident::create([
      'case_id' => 'CSIRT-BJN-' . now()->year . '-' . str_pad(Incident::count() + 1, 4, '0', STR_PAD_LEFT),
      'reporter_name' => $validated['reporter_name'],
      'reporter_email' => $validated['reporter_email'],
      'reporter_phone' => $validated['reporter_phone'],
      'incident_type_id' => $validated['incident_type_id'],
      'description' => $validated['description'],
      'attachment' => $path,
      'incident_at' => $validated['incident_at'],
      'reported_at' => now(),
    ]);

    return redirect()->route('incident.create')->with('success', 'Laporan Anda telah berhasil dikirim. Terima kasih.');
  }
}
