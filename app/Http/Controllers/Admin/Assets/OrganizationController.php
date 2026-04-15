<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveOrganizationRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrganizationController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Organization::latest();

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%'.$request->search.'%');
        }

        $organizations = $query->paginate(15)->withQueryString();

        return Inertia::render('Admin/Assets/Organizations/Index', [
            'organizations' => $organizations,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(SaveOrganizationRequest $request): RedirectResponse
    {
        Organization::create($request->validated());

        return redirect()->back()->with('success', 'Organisasi berhasil ditambahkan.');
    }

    public function update(SaveOrganizationRequest $request, Organization $organization): RedirectResponse
    {
        $organization->update($request->validated());

        return redirect()->back()->with('success', 'Organisasi berhasil diperbarui.');
    }

    public function destroy(Organization $organization): RedirectResponse
    {
        $organization->delete();

        return redirect()->back()->with('success', 'Organisasi berhasil dihapus.');
    }
}
