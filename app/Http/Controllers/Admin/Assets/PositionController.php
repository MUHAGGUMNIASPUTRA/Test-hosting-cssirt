<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\SavePositionRequest;
use App\Models\Department;
use App\Models\Organization;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PositionController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Position::with(['department.organization'])->latest();

        if ($request->filled('search')) {
            $query->where('name', 'ilike', '%'.$request->search.'%');
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        $positions = $query->paginate(15)->withQueryString();
        $organizations = Organization::orderBy('name')->get(['id', 'name']);
        $departments = Department::with('organization')->orderBy('name')->get(['id', 'name', 'organization_id']);

        return Inertia::render('Admin/Assets/Positions/Index', [
            'positions' => $positions,
            'organizations' => $organizations,
            'departments' => $departments,
            'filters' => $request->only(['search', 'department_id']),
        ]);
    }

    public function store(SavePositionRequest $request): RedirectResponse
    {
        Position::create($request->validated());

        return redirect()->back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function update(SavePositionRequest $request, Position $position): RedirectResponse
    {
        $position->update($request->validated());

        return redirect()->back()->with('success', 'Jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position): RedirectResponse
    {
        $position->delete();

        return redirect()->back()->with('success', 'Jabatan berhasil dihapus.');
    }
}
