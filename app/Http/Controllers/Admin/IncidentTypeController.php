<?php
// filepath: app/Http/Controllers/Admin/IncidentTypeController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IncidentType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class IncidentTypeController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request): Response
  {
    $query = IncidentType::withCount('incidents');

    // Apply search filter
    if ($request->filled('search')) {
      $search = $request->get('search');
      $query->where(function ($q) use ($search) {
        $q->where('name', 'ilike', "%{$search}%")
          ->orWhere('description', 'ilike', "%{$search}%");
      });
    }

    return Inertia::render('Admin/IncidentTypes/Index', [
      'incidentTypes' => $query->orderBy('name')->paginate(10)->withQueryString(),
      'filters' => $request->only(['search']),
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(): Response
  {
    return Inertia::render('Admin/IncidentTypes/Create');
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:incident_types,name',
      'description' => 'nullable|string',
    ], [
      'name.required' => 'Nama kategori wajib diisi.',
      'name.unique' => 'Nama kategori sudah digunakan.',
    ]);

    $validated['slug'] = Str::slug($validated['name']);
    IncidentType::create($validated);

    return redirect()
      ->route('admin.incident-types.index')
      ->with('success', 'Kategori insiden berhasil dibuat.');
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(IncidentType $incidentType): Response
  {
    return Inertia::render('Admin/IncidentTypes/Create', [
      'incidentType' => $incidentType->load('incidents:id,case_id,incident_type_id'),
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, IncidentType $incidentType)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:incident_types,name,' . $incidentType->id,
      'description' => 'nullable|string',
    ], [
      'name.required' => 'Nama kategori wajib diisi.',
      'name.unique' => 'Nama kategori sudah digunakan.',
    ]);

    $validated['slug'] = Str::slug($validated['name']);
    $incidentType->update($validated);

    return redirect()
      ->route('admin.incident-types.index')
      ->with('success', 'Kategori insiden berhasil diperbarui.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(IncidentType $incidentType)
  {
    // Check if incident type has associated incidents
    if ($incidentType->incidents()->count() > 0) {
      return back()->with('error', [
        'title' => 'Gagal Menghapus',
        'message' => 'Kategori tidak dapat dihapus karena masih digunakan dalam ' . $incidentType->incidents()->count() . ' insiden.',
        'icon' => 'error',
      ]);
    }

    try {
      $incidentType->delete();

      return back()->with('success', [
        'title' => 'Berhasil',
        'message' => 'Kategori insiden berhasil dihapus.',
        'icon' => 'success',
      ]);
    } catch (\Exception $e) {
      return back()->with('error', [
        'title' => 'Gagal',
        'message' => 'Gagal menghapus kategori insiden.',
        'icon' => 'error',
      ]);
    }
  }
}
