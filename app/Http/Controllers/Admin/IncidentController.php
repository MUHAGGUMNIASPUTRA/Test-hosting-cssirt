<?php
// filepath: app/Http/Controllers/Admin/IncidentController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Incident;
use App\Models\IncidentLog;
use App\Models\IncidentType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
      'incidentTypes' => IncidentType::orderBy('name')->get(['id', 'name']),
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
      $path = $request->file('attachment')->store('incidents', 'local');
    }

    $incident = Incident::create(array_merge($validated, [
      'case_id' => Incident::generateCaseId(),
      'access_token' => Str::random(64),
      'attachment' => $path,
      'reported_at' => now(),
    ]));

    // Log creation by the current admin/staff user
    $incident->incidentLogs()->create([
      'log_message' => 'Tiket insiden dibuat',
      'user_id' => Auth::id(),
    ]);

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

      $validated['attachment'] = $request->file('attachment')->store('incidents', 'local');
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
   * Helper method to normalize data for comparison
   */
  private function normalizeDataForComparison($originalData, $newData)
  {
    $normalized = [];

    foreach ($newData as $key => $value) {
      if ($key === 'incident_at') {
        // Normalize datetime to Y-m-d H:i:s format
        $normalized['original'][$key] = $originalData[$key] ? Carbon::parse($originalData[$key])->format('Y-m-d H:i:s') : null;
        $normalized['new'][$key] = $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
      } elseif ($key === 'incident_type_id' || $key === 'assigned_to') {
        // Normalize IDs to string
        $normalized['original'][$key] = (string)($originalData[$key] ?? '');
        $normalized['new'][$key] = (string)($value ?? '');
      } else {
        // Regular comparison
        $normalized['original'][$key] = $originalData[$key] ?? null;
        $normalized['new'][$key] = $value;
      }
    }

    return $normalized;
  }

  /**
   * Helper method to log changes to an incident.
   */
  private function logChanges(Incident $incident, array $newData)
  {
    $changes = [];
    $originalData = $incident->getOriginal();

    // Normalize data for proper comparison
    $normalized = $this->normalizeDataForComparison($originalData, $newData);

    // Prefetch reference names to avoid N+1
    $typeIds = collect([$originalData['incident_type_id'] ?? null, $newData['incident_type_id'] ?? null])->filter()->unique()->values();
    $typesById = $typeIds->isEmpty() ? collect() : IncidentType::whereIn('id', $typeIds)->get(['id', 'name'])->keyBy('id');

    $userIds = collect([$originalData['assigned_to'] ?? null, $newData['assigned_to'] ?? null])->filter()->unique()->values();
    $usersById = $userIds->isEmpty() ? collect() : User::whereIn('id', $userIds)->get(['id', 'name'])->keyBy('id');

    // Now use normalized data for comparison
    foreach ($newData as $key => $value) {
      if ($normalized['original'][$key] !== $normalized['new'][$key]) {
        // Handle each field's change message
        switch ($key) {
          case 'reporter_name':
            $changes[] = "Nama pelapor diubah dari '{$originalData[$key]}' menjadi '{$value}'.";
            break;
          case 'reporter_email':
            $changes[] = "Email pelapor diubah dari '{$originalData[$key]}' menjadi '{$value}'.";
            break;
          case 'reporter_phone':
            $oldPhone = $originalData[$key] ?: 'Tidak ada';
            $newPhone = $value ?: 'Tidak ada';
            $changes[] = "Nomor telepon pelapor diubah dari '{$oldPhone}' menjadi '{$newPhone}'.";
            break;
          case 'incident_type_id':
            $oldType = $originalData[$key] ? optional($typesById->get((int)$originalData[$key]))->name : 'Tidak ada';
            $newType = $value ? optional($typesById->get((int)$value))->name : 'Tidak ada';
            $changes[] = "Kategori insiden diubah dari '{$oldType}' menjadi '{$newType}'.";
            break;
          // case 'incident_at':
          //   $oldDate = $originalData[$key] ? Carbon::parse($originalData[$key])->format('d/m/Y H:i') : 'Tidak ada';
          //   $newDate = $value ? Carbon::parse($value)->format('d/m/Y H:i') : 'Tidak ada';
          //   $changes[] = "Waktu kejadian diubah dari '{$oldDate}' menjadi '{$newDate}'.";
          //   break;
          case 'description':
            $changes[] = "Deskripsi insiden diperbarui.";
            break;
          case 'status':
            $changes[] = "Status diubah dari '{$originalData[$key]}' menjadi '{$value}'.";
            break;
          case 'priority':
            $changes[] = "Prioritas diubah dari '{$originalData[$key]}' menjadi '{$value}'.";
            break;
          case 'assigned_to':
            $oldAssigneeName = $originalData[$key] ? (optional($usersById->get((int)$originalData[$key]))->name ?? 'Belum Ditugaskan') : 'Belum Ditugaskan';
            $newAssigneeName = $value ? (optional($usersById->get((int)$value))->name ?? 'Belum Ditugaskan') : 'Belum Ditugaskan';
            $changes[] = "Insiden ditugaskan dari '{$oldAssigneeName}' ke '{$newAssigneeName}'.";
            break;
          case 'attachment':
            if ($value) {
              $changes[] = "Lampiran insiden diperbarui.";
            } else {
              $changes[] = "Lampiran insiden dihapus.";
              // Delete old attachment if exists
              if ($incident->attachment && Storage::disk('public')->exists($incident->attachment)) {
                Storage::disk('public')->delete($incident->attachment);
              }
            }
        }
      }
    }

    // Create incident log entries for each change
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
