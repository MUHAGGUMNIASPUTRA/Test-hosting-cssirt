<?php

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\StoreEmployeeRequest;
use App\Http\Requests\Admin\Assets\UpdateEmployeeRequest;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Position;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Employee::with(['position.department.organization', 'organization'])->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ilike', '%'.$request->search.'%')
                    ->orWhere('email', 'ilike', '%'.$request->search.'%');
            });
        }

        if ($request->filled('organization_id')) {
            $query->where('organization_id', $request->organization_id);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'aktif');
        }

        $employees = $query->paginate(15)->withQueryString();
        $organizations = Organization::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Assets/Employees/Index', [
            'employees' => $employees,
            'organizations' => $organizations,
            'filters' => $request->only(['search', 'organization_id', 'status']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Assets/Employees/Create', [
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
            'departments' => Department::with('organization')->orderBy('name')->get(['id', 'name', 'organization_id']),
            'positions' => Position::with('department')->orderBy('name')->get(['id', 'name', 'department_id']),
        ]);
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        Employee::create($request->validated());

        return redirect()->route('admin.employees.index')
            ->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit(Employee $employee): Response
    {
        return Inertia::render('Admin/Assets/Employees/Create', [
            'employee' => $employee->load(['position.department', 'organization']),
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
            'departments' => Department::with('organization')->orderBy('name')->get(['id', 'name', 'organization_id']),
            'positions' => Position::with('department')->orderBy('name')->get(['id', 'name', 'department_id']),
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $employee->update($request->validated());

        return redirect()->route('admin.employees.index')
            ->with('success', 'Pegawai berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->back()->with('success', 'Pegawai berhasil dihapus.');
    }
}
