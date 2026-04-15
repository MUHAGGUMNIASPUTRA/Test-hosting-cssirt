<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveLocationRequest;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LocationController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Location::with('organization')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%'.$request->search.'%');
        }

        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        $locations = $query->paginate(15)->withQueryString();
        $organizations = Organization::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Assets/Locations/Index', [
            'locations' => $locations,
            'organizations' => $organizations,
            'filters' => $request->only(['search', 'organization_id']),
        ]);
    }

    public function store(SaveLocationRequest $request): RedirectResponse
    {
        Location::create($request->validated());

        return redirect()->back()->with('success', 'Lokasi berhasil ditambahkan.');
    }

    public function update(SaveLocationRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());

        return redirect()->back()->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();

        return redirect()->back()->with('success', 'Lokasi berhasil dihapus.');
    }
}
