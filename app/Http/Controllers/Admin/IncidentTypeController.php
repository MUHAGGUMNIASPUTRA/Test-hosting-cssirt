<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\IncidentType\SaveIncidentTypeRequest;
use App\Models\IncidentType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class IncidentTypeController extends Controller
{
    public function index(Request $request): Response
    {
        $query = IncidentType::withCount('incidents');

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

    public function create(): Response
    {
        return Inertia::render('Admin/IncidentTypes/Create');
    }

    public function store(SaveIncidentTypeRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        IncidentType::create($data);

        return redirect()
            ->route('admin.incident-types.index')
            ->with('success', 'Jenis insiden berhasil dibuat.');
    }

    public function edit(IncidentType $incidentType): Response
    {
        return Inertia::render('Admin/IncidentTypes/Create', [
            'incidentType' => $incidentType->load('incidents:id,case_id,incident_type_id'),
        ]);
    }

    public function update(SaveIncidentTypeRequest $request, IncidentType $incidentType): RedirectResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);
        $incidentType->update($data);

        return redirect()
            ->route('admin.incident-types.index')
            ->with('success', 'Jenis insiden berhasil diperbarui.');
    }

    public function destroy(IncidentType $incidentType)
    {
        if ($incidentType->incidents()->count() > 0) {
            return back()->with('error', [
                'title' => 'Gagal Menghapus',
                'message' => 'Jenis insiden tidak dapat dihapus karena masih digunakan dalam '.$incidentType->incidents()->count().' insiden.',
                'icon' => 'error',
            ]);
        }

        try {
            $incidentType->delete();

            return back()->with('success', [
                'title' => 'Berhasil',
                'message' => 'Jenis insiden berhasil dihapus.',
                'icon' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Gagal',
                'message' => 'Gagal menghapus jenis insiden.',
                'icon' => 'error',
            ]);
        }
    }
}
