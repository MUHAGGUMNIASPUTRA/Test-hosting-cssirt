<?php

// Tujuan: CRUD pegawai via dialog inline; reveal data sensitif khusus admin
// Caller: routes/web.php admin group
// Side Effects: DB write

namespace App\Http\Controllers\Admin\Assets;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Assets\StoreEmployeeRequest;
use App\Http\Requests\Admin\Assets\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Organization;
use App\Models\Position;
use App\Services\Assets\EmployeeService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmployeeController extends Controller
{
    public function __construct(private readonly EmployeeService $service) {}

    public function index(Request $request): Response
    {
        $query = Employee::with(['position.department.organization', 'organization'])->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'ilike', '%'.$request->search.'%');
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
        $positions = Position::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Admin/Assets/Employees/Index', [
            'employees' => $employees,
            'organizations' => $organizations,
            'positions' => $positions,
            'filters' => $request->only(['search', 'organization_id', 'status']),
        ]);
    }

    public function store(StoreEmployeeRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('admin.employees.index')
            ->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function edit(Employee $employee): Response
    {
        return Inertia::render('Admin/Assets/Employees/Index', [
            'employee' => $employee->load(['position.department', 'organization']),
            'organizations' => Organization::orderBy('name')->get(['id', 'name']),
            'positions' => Position::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee): RedirectResponse
    {
        $this->service->update($employee, $request->validated());

        return redirect()->route('admin.employees.index')
            ->with('success', 'Pegawai berhasil diperbarui.');
    }

    public function destroy(Employee $employee): RedirectResponse
    {
        $employee->delete();

        return redirect()->back()->with('success', 'Pegawai berhasil dihapus.');
    }

    public function reveal(Request $request, Employee $employee): JsonResponse
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Hanya admin yang dapat mengakses data sensitif.');
        }

        $request->validate(['password' => ['required', 'string']]);

        try {
            $data = $this->service->reveal($employee, $request->input('password'));

            return response()->json($data);
        } catch (AuthorizationException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }
}
