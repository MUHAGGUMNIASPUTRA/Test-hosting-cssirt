<?php
// filepath: app/Http/Controllers/Admin/ServiceController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Service\SaveServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request): Response
  {
    $query = Service::latest();

    // Apply search filter
    if ($request->filled('search')) {
      $query->where('name', 'ilike', '%' . $request->search . '%')
        ->orWhere('short_description', 'ilike', '%' . $request->search . '%');
    }

    // Apply status filter
    if ($request->filled('status')) {
      $isActive = $request->status === 'active';
      $query->where('is_active', $isActive);
    }

    $services = $query->orderBy('name')->paginate(10)->withQueryString();

    return Inertia::render('Admin/Services/Index', [
      'services' => $services,
      'filters' => $request->only(['search', 'status']),
    ]);
  }

  /**
   * Show the form for creating a new resource.
   */
  public function create(): Response
  {
    return Inertia::render('Admin/Services/Create');
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(SaveServiceRequest $request): RedirectResponse
  {
    $data         = $request->validated();
    $data['slug'] = Str::slug($data['name']);

    Service::create($data);

    return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dibuat.');
  }

  /**
   * Display the specified resource.
   */
  public function show(Service $service): Response
  {
    return Inertia::render('Admin/Services/Show', [
      'service' => $service,
    ]);
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit(Service $service): Response
  {
    return Inertia::render('Admin/Services/Create', [
      'service' => $service,
    ]);
  }

  /**
   * Update the specified resource in storage.
   */
  public function update(SaveServiceRequest $request, Service $service): RedirectResponse
  {
    $data = $request->validated();

    if ($service->name !== $data['name']) {
      $data['slug'] = Str::slug($data['name']);
    }

    $service->update($data);

    return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil diperbarui.');
  }

  /**
   * Remove the specified resource from storage.
   */
  public function destroy(Service $service): RedirectResponse
  {
    $service->delete();
    return redirect()->route('admin.services.index')->with('success', 'Layanan berhasil dihapus.');
  }
}
