<?php
// File: app/Http/Controllers/Admin/IncidentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentLog;
use App\Models\IncidentType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class IncidentController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(): Response
  {
    return Inertia::render('Admin/Incidents/Index', [
      'incidents' => Incident::with(['incidentType', 'assignedUser'])
        ->latest('reported_at')
        ->paginate(10),
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(): Response
  {
    return Inertia::render('Admin/Incidents/Create', [
      'incidentTypes' => IncidentType::all(['id', 'name']),
      'staffUsers' => User::where('role', 'staff')->orWhere('role', 'admin')->get(['id', 'name']),
    ]);
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'reporter_name' => 'required|string|max:255',
      'reporter_email' => 'required|email|max:255',
      'incident_type_id' => 'required|exists:incident_types,id',
      'incident_at' => 'required|date',
      'description' => 'required|string',
      'status' => 'required|in:Baru,Diverifikasi,Dalam Penyelidikan,Selesai,Ditutup',
      'priority' => 'required|in:Rendah,Sedang,Tinggi,Kritis',
      'assigned_to' => 'nullable|exists:users,id',
    ]);

    Incident::create(array_merge($validated, [
      'case_id' => 'CSIRT-BJN-' . now()->year . '-' . str_pad(Incident::count() + 1, 4, '0', STR_PAD_LEFT),
      'reported_at' => now(),
    ]));

    return redirect()->route('admin.incidents.index')->with('success', 'Laporan insiden berhasil dibuat.');
  }

  /**
   * Display the specified resource.
   */
  public function show(Incident $incident): Response
  {
    return Inertia::render('Admin/Incidents/Show', [
      'incident' => $incident->load([
        'incidentType',
        'assignedUser',
        'incidentLogs.user' // Eager load logs and the user who created the log
      ]),
    ]);
  }

  // Nanti kita tambahkan method edit(), update(), destroy() di sini
}
