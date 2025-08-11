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
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class IncidentController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request): Response
  {
    $query = Incident::with(['incidentType', 'assignedUser']);

    // Apply search filter
    if ($request->filled('search')) {
      $search = $request->get('search');
      $query->where(function ($q) use ($search) {
        $q->where('case_id', 'ilike', "%{$search}%")
          ->orWhere('reporter_name', 'ilike', "%{$search}%")
          ->orWhere('reporter_email', 'ilike', "%{$search}%")
          ->orWhere('description', 'ilike', "%{$search}%");
      });
    }

    // Apply category filter
    if ($request->filled('category')) {
      $query->where('incident_type_id', $request->get('category'));
    }

    // Apply priority filter
    if ($request->filled('priority')) {
      $query->where('priority', $request->get('priority'));
    }

    // Apply status filter
    if ($request->filled('status')) {
      $query->where('status', $request->get('status'));
    }

    return Inertia::render('Admin/Incidents/Index', [
      'incidents' => $query->latest('reported_at')->paginate(10)->withQueryString(),
      'filters' => $request->only(['search', 'status', 'priority']),
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(): Response
  {
    return Inertia::render('Admin/Incidents/Create', [
      'incidentTypes' => IncidentType::all(['id', 'name']),
      'staffUsers' => User::whereIn('role', ['admin', 'staff'])->get(['id', 'name']),
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
      'reporter_phone' => 'nullable|string|max:20',
      'incident_type_id' => 'required|exists:incident_types,id',
      'incident_at' => 'required|date',
      'description' => 'required|string',
      'status' => 'required|in:Baru,Diverifikasi,Dalam Penyelidikan,Selesai,Ditutup',
      'priority' => 'required|in:Rendah,Sedang,Tinggi,Kritikal',
      'assigned_to' => 'nullable|exists:users,id',
      'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip,doc,docx|max:2048', // Max 2MB
    ]);

    // Handle file upload
    $path = null;
    if ($request->hasFile('attachment')) {
      $path = $request->file('attachment')->store('incidents', 'public');
    }

    Incident::create(array_merge($validated, [
      'case_id' => 'CSIRT-BJN-' . now()->year . '-' . str_pad(Incident::count() + 1, 4, '0', STR_PAD_LEFT),
      'attachment' => $path,
      'reported_at' => now(),
    ]));

    return redirect()->route('admin.incidents.index')->with('success', 'Laporan insiden berhasil dibuat.');
  }

  /**
   * Display the specified resource.
   */
  public function show(Incident $incident): Response
  {
    // Pass staffUsers to the view for the assignment dropdown
    return Inertia::render('Admin/Incidents/Show', [
      'incident' => $incident->load([
        'incidentType',
        'assignedUser',
        'incidentLogs.user'
      ]),
      'staffUsers' => User::whereIn('role', ['admin', 'staff'])->get(['id', 'name']),
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Incident $incident): Response
  {
    $incident->file_size = $incident->fileSize();

    return Inertia::render('Admin/Incidents/Create', [
      'incident' => $incident,
      'incidentTypes' => IncidentType::all(['id', 'name']),
      'staffUsers' => User::whereIn('role', ['admin', 'staff'])->get(['id', 'name']),
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, Incident $incident)
  {
    $validated = $request->validate([
      'reporter_name' => 'required|string|max:255',
      'reporter_email' => 'required|email|max:255',
      'reporter_phone' => 'nullable|string|max:20',
      'incident_type_id' => 'required|exists:incident_types,id',
      'incident_at' => 'required|date',
      'description' => 'required|string',
      'status' => 'required|in:Baru,Diverifikasi,Dalam Penyelidikan,Selesai,Ditutup',
      'priority' => 'required|in:Rendah,Sedang,Tinggi,Kritikal',
      'assigned_to' => 'nullable|exists:users,id',
      'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip,doc,docx|max:2048', // Max 2MB
    ]);

    // Handle file upload
    if ($request->hasFile('attachment')) {
      // Delete old attachment if exists
      if ($incident->attachment && Storage::disk('public')->exists($incident->attachment)) {
        Storage::disk('public')->delete($incident->attachment);
      }

      $validated['attachment'] = $request->file('attachment')->store('incidents', 'public');
    }

    $this->logChanges($incident, $validated);
    $incident->update($validated);

    return redirect()->route('admin.incidents.show', $incident)->with('success', 'Insiden berhasil diperbarui.');
  }

  /**
   * Update the management status of an incident.
   */
  public function updateManagement(Request $request, Incident $incident)
  {
    $validated = $request->validate([
        'status' => 'required|in:Baru,Diverifikasi,Dalam Penyelidikan,Selesai,Ditutup',
        'priority' => 'required|in:Rendah,Sedang,Tinggi,Kritikal',
        'assigned_to' => 'nullable|exists:users,id',
    ]);

    // Panggil helper untuk mencatat perubahan sebelum di-update
    $this->logChanges($incident, $validated);

    $incident->update($validated);

    return back()->with('success', 'Status insiden berhasil diperbarui.');
  }

  /**
   * Helper method to log changes to an incident.
   */
  private function logChanges(Incident $incident, array $newData)
  {
    $changes = [];
    $originalData = $incident->getOriginal();

    // Cek perubahan status
    if ($originalData['status'] !== $newData['status']) {
        $changes[] = "Status diubah dari '{$originalData['status']}' menjadi '{$newData['status']}'.";
    }

    // Cek perubahan prioritas
    if ($originalData['priority'] !== $newData['priority']) {
        $changes[] = "Prioritas diubah dari '{$originalData['priority']}' menjadi '{$newData['priority']}'.";
    }

    // Cek perubahan penugasan
    if ($originalData['assigned_to'] != $newData['assigned_to']) {
        $oldAssignee = User::find($originalData['assigned_to']);
        $newAssignee = User::find($newData['assigned_to']);
        $oldAssigneeName = $oldAssignee ? $oldAssignee->name : 'Belum Ditugaskan';
        $newAssigneeName = $newAssignee ? $newAssignee->name : 'Belum Ditugaskan';
        $changes[] = "Insiden ditugaskan dari '{$oldAssigneeName}' ke '{$newAssigneeName}'.";
    }

    // Cek perubahan deskripsi (hanya jika ada di data baru)
    if (isset($newData['description']) && $originalData['description'] !== $newData['description']) {
        $changes[] = "Deskripsi insiden diperbarui.";
    }

    // Simpan semua perubahan ke log
    foreach ($changes as $message) {
        $incident->incidentLogs()->create([
            'log_message' => $message,
            'user_id' => Auth::id(),
        ]);
    }
  }

  /**
   * Store a new log for the specified incident.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Models\Incident  $incident
   * @return \Illuminate\Http\RedirectResponse
   */
  public function addLog(Request $request, Incident $incident)
  {
    $validated = $request->validate([
      'log_message' => 'required|string',
    ]);

    $incident->incidentLogs()->create([
      'log_message' => $validated['log_message'],
      'user_id' => Auth::id(), // Automatically associate with the logged-in user
    ]);

    return back()->with('success', 'Catatan berhasil ditambahkan.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Incident $incident)
  {
    try {
      $incident->delete();
      return back()->with('success', [
        'title' => 'Berhasil',
        'message' => 'Insiden berhasil dihapus.',
        'icon' => 'success',
      ])->withInput();
    } catch (\Exception $e) {
      return back()->with('error', [
        'title' => 'Gagal',
        'message' => 'Gagal menghapus insiden. Pastikan tidak ada data terkait yang menghalangi penghapusan.',
        'icon' => 'error',
      ])->withInput();
    }
  }
}
