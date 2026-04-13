<?php

namespace App\Http\Controllers\Admin;

use App\Enums\IncidentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Incident\AddLogRequest;
use App\Http\Requests\Admin\Incident\StoreIncidentRequest;
use App\Http\Requests\Admin\Incident\UpdateIncidentRequest;
use App\Http\Requests\Admin\Incident\UpdateManagementRequest;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\User;
use App\Services\IncidentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class IncidentController extends Controller
{
    public function __construct(private readonly IncidentService $incidentService) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Incident::with(['incidentType', 'assignedUser']);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('case_id', 'ilike', "%{$search}%")
                  ->orWhere('reporter_name', 'ilike', "%{$search}%")
                  ->orWhere('reporter_email', 'ilike', "%{$search}%")
                  ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('incident_type_id', $request->get('category'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->get('priority'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        return Inertia::render('Admin/Incidents/Index', [
            'incidents' => $query->latest('reported_at')->paginate(10)->withQueryString(),
            'filters'   => $request->only(['search', 'status', 'priority', 'category']),
            'stats'     => $this->incidentService->getGlobalStats(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Incidents/Create', [
            'incidentTypes' => IncidentType::orderBy('name')->get(['id', 'name', 'description', 'guide']),
            'staffUsers'    => User::whereIn('role', ['admin', 'staff'])->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        $this->incidentService->create(
            $request->validated(),
            $request->file('attachment'),
            Auth::id()
        );

        return redirect()->route('admin.incidents.index')
            ->with('success', 'Laporan insiden berhasil dibuat.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Incident $incident): Response
    {
        if (!$incident->is_read) {
            $incident->update([
                'is_read' => true,
                'read_by' => Auth::id(),
                'read_at' => now(),
            ]);
        }

        return Inertia::render('Admin/Incidents/Show', [
            'incident'   => $incident->load(['incidentType', 'assignedUser', 'incidentLogs.user']),
            'staffUsers' => User::whereIn('role', ['admin', 'staff'])->get(['id', 'name']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Incident $incident): RedirectResponse|Response
    {
        if ($incident->status === IncidentStatus::Ditutup) {
            return back()->with('error', 'Insiden sudah ditutup dan tidak dapat diubah.');
        }

        $incident->file_size = $incident->fileSize();

        return Inertia::render('Admin/Incidents/Create', [
            'incident'      => $incident,
            'incidentTypes' => IncidentType::all(['id', 'name', 'description', 'guide']),
            'staffUsers'    => User::whereIn('role', ['admin', 'staff'])->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIncidentRequest $request, Incident $incident): RedirectResponse
    {
        if ($incident->status === IncidentStatus::Ditutup) {
            return back()->with('error', 'Insiden sudah ditutup dan tidak dapat diubah.');
        }

        $this->incidentService->update(
            $incident,
            $request->validated(),
            $request->file('attachment'),
            Auth::id()
        );

        return redirect()->route('admin.incidents.show', $incident)
            ->with('success', 'Insiden berhasil diperbarui.');
    }

    /**
     * Update the management status of an incident.
     */
    public function updateManagement(UpdateManagementRequest $request, Incident $incident): RedirectResponse
    {
        if ($incident->status === IncidentStatus::Ditutup) {
            return back()->with('error', 'Insiden sudah ditutup dan tidak dapat diubah.');
        }

        $this->incidentService->updateManagement($incident, $request->validated(), Auth::id());

        return back()->with('success', 'Status insiden berhasil diperbarui.');
    }

    /**
     * Store a new log for the specified incident.
     */
    public function addLog(AddLogRequest $request, Incident $incident): RedirectResponse
    {
        if ($incident->status === IncidentStatus::Ditutup) {
            return back()->with('error', 'Tidak dapat menambahkan log pada insiden yang sudah ditutup.');
        }

        $incident->incidentLogs()->create([
            'log_message' => $request->validated('log_message'),
            'user_id'     => Auth::id(),
        ]);

        return back()->with('success', 'Catatan berhasil ditambahkan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Incident $incident): RedirectResponse
    {
        try {
            $incident->delete();
            return back()->with('success', [
                'title'   => 'Berhasil',
                'message' => 'Insiden berhasil dihapus.',
                'icon'    => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with('error', [
                'title'   => 'Gagal',
                'message' => 'Gagal menghapus insiden. Pastikan tidak ada data terkait yang menghalangi penghapusan.',
                'icon'    => 'error',
            ]);
        }
    }
}
