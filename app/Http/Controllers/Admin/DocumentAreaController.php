<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DocumentArea;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class DocumentAreaController extends Controller
{
  public function index(Request $request): Response
  {
    $query = DocumentArea::withCount('documents');

    if ($request->filled('search')) {
      $search = $request->get('search');
      $query->where(function ($q) use ($search) {
        $q->where('name', 'ilike', "%{$search}%")
          ->orWhere('description', 'ilike', "%{$search}%");
      });
    }

    return Inertia::render('Admin/DocumentAreas/Index', [
      'documentAreas' => $query->orderBy('name')->paginate(10)->withQueryString(),
      'filters' => $request->only(['search']),
    ]);
  }

  public function create(): Response
  {
    return Inertia::render('Admin/DocumentAreas/Create');
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:document_areas,name',
      'description' => 'nullable|string',
    ], [
      'name.required' => 'Nama area dokumen wajib diisi.',
      'name.unique' => 'Nama area dokumen sudah digunakan.',
    ]);

    $validated['slug'] = Str::slug($validated['name']);
    DocumentArea::create($validated);

    return redirect()
      ->route('admin.document-areas.index')
      ->with('success', 'Area dokumen berhasil dibuat.');
  }

  public function edit(DocumentArea $documentArea): Response
  {
    return Inertia::render('Admin/DocumentAreas/Create', [
      'documentArea' => $documentArea->loadCount('documents'),
    ]);
  }

  public function update(Request $request, DocumentArea $documentArea)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:document_areas,name,' . $documentArea->id,
      'description' => 'nullable|string',
    ], [
      'name.required' => 'Nama area dokumen wajib diisi.',
      'name.unique' => 'Nama area dokumen sudah digunakan.',
    ]);

    $validated['slug'] = Str::slug($validated['name']);
    $documentArea->update($validated);

    return redirect()
      ->route('admin.document-areas.index')
      ->with('success', 'Area dokumen berhasil diperbarui.');
  }

  public function destroy(DocumentArea $documentArea)
  {
    if ($documentArea->documents()->count() > 0) {
      return back()->with('error', [
        'title' => 'Gagal Menghapus',
        'message' => 'Area dokumen tidak dapat dihapus karena masih digunakan dalam ' . $documentArea->documents()->count() . ' dokumen.',
        'icon' => 'error',
      ]);
    }

    try {
      $documentArea->delete();

      return back()->with('success', [
        'title' => 'Berhasil',
        'message' => 'Area dokumen berhasil dihapus.',
        'icon' => 'success',
      ]);
    } catch (\Exception $e) {
      return back()->with('error', [
        'title' => 'Gagal',
        'message' => 'Gagal menghapus area dokumen.',
        'icon' => 'error',
      ]);
    }
  }
}
