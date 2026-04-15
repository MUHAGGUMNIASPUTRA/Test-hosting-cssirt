<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SaveDepartmentRequest;
use App\Models\Department;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DepartmentController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Department::with('organization')->latest();

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%'.$request->search.'%');
        }

        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        $departments = $query->paginate(15)->withQueryString();
        $organizations = Organization::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Assets/Departments/Index', [
            'departments' => $departments,
            'organizations' => $organizations,
            'filters' => $request->only(['search', 'organization_id']),
        ]);
    }

    public function store(SaveDepartmentRequest $request): RedirectResponse
    {
        Department::create($request->validated());

        return redirect()->back()->with('success', 'Bidang berhasil ditambahkan.');
    }

    public function update(SaveDepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->validated());

        return redirect()->back()->with('success', 'Bidang berhasil diperbarui.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();

        return redirect()->back()->with('success', 'Bidang berhasil dihapus.');
    }
}
