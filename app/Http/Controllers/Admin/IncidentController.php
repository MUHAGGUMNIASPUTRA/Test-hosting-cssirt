<?php

// Tujuan: CRUD insiden admin beserta log, manajemen status, dan aset virtual terdampak
// Caller: routes/web.php admin group
// Side Effects: DB write, storage I/O (attachment)

namespace App\Http\Controllers\Admin;

use App\Enums\IncidentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Incident\AddLogRequest;
use App\Http\Requests\Admin\Incident\StoreIncidentRequest;
use App\Http\Requests\Admin\Incident\UpdateIncidentRequest;
use App\Http\Requests\Admin\Incident\UpdateLogRequest;
use App\Http\Requests\Admin\Incident\UpdateManagementRequest;
use App\Models\Incident;
use App\Models\IncidentLog;
use App\Models\IncidentType;
use App\Models\User;
use App\Services\AttachmentService;
use App\Services\IncidentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class IncidentController extends Controller
{
    public function __construct(
        private readonly IncidentService $incidentService,
        private readonly AttachmentService $attachmentService,
    ) {}

    public function index(Request $request): Response
    {
        $query = Incident::with(['incidentType', 'assignedUser', 'webApplications', 'mobileApplications']);

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

        if ($request->filled('assigned_to')) {
            if ($request->get('assigned_to') === 'none') {
                $query->whereNull('assigned_to');
            } else {
                $query->where('assigned_to', $request->get('assigned_to'));
            }
        }

        return Inertia::render('Admin/Incidents/Index', [
            'incidents' => $query->latest('reported_at')->paginate(10)->withQueryString(),
            'filters' => $request->only(['search', 'status', 'priority', 'category', 'assigned_to']),
            'stats' => $this->incidentService->getGlobalStats(),
            'incidentTypes' => IncidentType::orderBy('sort_order')->get(['id', 'name']),
            'staffUsers' => User::whereIn('role', ['admin', 'staff'])->get(['id', 'name']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Incidents/Create', [
            'incidentTypes' => IncidentType::orderBy('sort_order')->get(['id', 'name', 'description', 'guide']),
            'staffUsers' => User::whereIn('role', ['admin', 'staff'])->get(['id', 'name']),
        ]);
    }

    public function store(StoreIncidentRequest $request): RedirectResponse
    {
        $this->incidentService->create(
            $request->validated(),
            $request->file('attachment'),
            Auth::id(),
        );

        return redirect()->route('admin.incidents.index')
            ->with('success', 'Laporan insiden berhasil dibuat.');
    }

    public function show(Incident $incident): Response
    {
        if (! $incident->is_read) {
            $incident->update([
                'is_read' => true,
                'read_by' => Auth::id(),
                'read_at' => now(),
            ]);
        }

        return Inertia::render('Admin/Incidents/Show', [
            'incident' => $incident->load([
                'incidentType', 'assignedUser',
                'incidentLogs.user', 'incidentLogs.attachment', 'attachment',
                'webApplications', 'mobileApplications',
            ]),
            'staffUsers' => User::whereIn('role', ['admin', 'staff'])->get(['id', 'name']),
        ]);
    }

    public function edit(Incident $incident): RedirectResponse|Response
    {
        if ($incident->status === IncidentStatus::Ditutup) {
            return back()->with('error', 'Insiden sudah ditutup dan tidak dapat diubah.');
        }

        return Inertia::render('Admin/Incidents/Create', [
            'incident' => $incident->load(['attachment', 'webApplications', 'mobileApplications']),
            'incidentTypes' => IncidentType::orderBy('sort_order')->get(['id', 'name', 'description', 'guide']),
            'staffUsers' => User::whereIn('role', ['admin', 'staff'])->get(['id', 'name']),
        ]);
    }

    public function update(UpdateIncidentRequest $request, Incident $incident): RedirectResponse
    {
        if ($incident->status === IncidentStatus::Ditutup) {
            return back()->with('error', 'Insiden sudah ditutup dan tidak dapat diubah.');
        }

        $incident->loadMissing('attachment');

        $this->incidentService->update(
            $incident,
            $request->validated(),
            $request->file('attachment'),
            Auth::id(),
        );

        return redirect()->route('admin.incidents.show', $incident)
            ->with('success', 'Insiden berhasil diperbarui.');
    }

    public function updateManagement(UpdateManagementRequest $request, Incident $incident): RedirectResponse
    {
        if ($incident->status === IncidentStatus::Ditutup) {
            return back()->with('error', 'Insiden sudah ditutup dan tidak dapat diubah.');
        }

        $this->incidentService->updateManagement($incident, $request->validated(), Auth::id());

        return back()->with('success', 'Status insiden berhasil diperbarui.');
    }

    public function addLog(AddLogRequest $request, Incident $incident): RedirectResponse
    {
        if ($incident->status === IncidentStatus::Ditutup) {
            return back()->with('error', 'Tidak dapat menambahkan log pada insiden yang sudah ditutup.');
        }

        $validated = $request->validated();

        $attachment = $this->attachmentService->resolve(
            $request->hasFile('attachment') ? $request->file('attachment') : null,
            $validated['attachment_type'] ?? null,
            $validated['attachment_link'] ?? null,
            null,
            'public',
            'incidents/logs',
        );

        $incident->incidentLogs()->create([
            'log_message' => $validated['log_message'],
            'user_id' => Auth::id(),
            'is_public' => (bool) ($validated['is_public'] ?? false),
            'attachment_id' => $attachment?->id,
        ]);

        return back()->with('success', 'Catatan berhasil ditambahkan.');
    }

    public function updateLog(UpdateLogRequest $request, Incident $incident, IncidentLog $log): RedirectResponse
    {
        abort_if($log->incident_id !== $incident->id, 404);

        $validated = $request->validated();
        $log->loadMissing('attachment');

        $newAttachment = $log->attachment;

        if (($validated['attachment_type'] ?? null) === 'none') {
            $this->attachmentService->delete($log->attachment);
            $newAttachment = null;
        } else {
            $newAttachment = $this->attachmentService->resolve(
                $request->hasFile('attachment') ? $request->file('attachment') : null,
                $validated['attachment_type'] ?? null,
                $validated['attachment_link'] ?? null,
                $log->attachment,
                'public',
                'incidents/logs',
            );
        }

        $log->update([
            'log_message' => $validated['log_message'],
            'is_public' => (bool) ($validated['is_public'] ?? false),
            'attachment_id' => $newAttachment?->id,
        ]);

        return back()->with('success', 'Catatan berhasil diperbarui.');
    }

    public function destroyLog(Incident $incident, IncidentLog $log): RedirectResponse
    {
        abort_if($log->incident_id !== $incident->id, 404);

        $log->loadMissing('attachment');
        $this->attachmentService->delete($log->attachment);
        $log->delete();

        return back()->with('success', 'Catatan berhasil dihapus.');
    }

    public function destroy(Incident $incident): RedirectResponse
    {
        try {
            $incident->loadMissing('attachment');
            $this->attachmentService->delete($incident->attachment);
            $incident->delete();

            return back()->with('success', [
                'title' => 'Berhasil',
                'message' => 'Insiden berhasil dihapus.',
                'icon' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with('error', [
                'title' => 'Gagal',
                'message' => 'Gagal menghapus insiden. Pastikan tidak ada data terkait yang menghalangi penghapusan.',
                'icon' => 'error',
            ]);
        }
    }
}
