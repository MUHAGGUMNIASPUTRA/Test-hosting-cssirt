<?php

namespace App\Http\Controllers;

use App\Http\Resources\FullIncidentResource;
use App\Http\Resources\PublicIncidentResource;
use App\Http\Traits\HandlesSeoRequests;
use App\Mail\IncidentConfirmationMail;
use App\Mail\IncidentReportMail;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Services\AttachmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class IncidentController extends Controller
{
    use HandlesSeoRequests;

    public function __construct(private readonly AttachmentService $attachmentService) {}

    public function create()
    {
        return $this->handleSeoRequest('Incidents/Create', [
            'incidentTypes' => IncidentType::orderBy('name')->get(['id', 'name', 'description', 'guide']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'reporter_name' => 'required|string|max:255',
            'reporter_email' => 'required|email|max:255',
            'reporter_phone' => 'nullable|string|max:20',
            'incident_type_id' => 'required|exists:incident_types,id',
            'incident_at' => 'required|date',
            'description' => 'required|string',
            'priority' => 'required|in:Rendah,Sedang,Tinggi,Kritikal',
            'attachment_type' => 'nullable|in:file,link',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip,doc,docx|max:5120',
            'attachment_links' => 'nullable|string|max:2000',
            'captcha_answer' => 'required',
            'captcha_expected' => 'required|string',
        ], [
            'captcha_answer.required' => 'Jawaban captcha wajib diisi.',
            'priority.required' => 'Prioritas tiket wajib dipilih.',
            'incident_type_id.required' => 'Kategori insiden wajib dipilih.',
            'incident_type_id.exists' => 'Kategori insiden tidak valid.',
            'incident_at.required' => 'Waktu kejadian wajib diisi.',
            'description.required' => 'Deskripsi insiden wajib diisi.',
            'reporter_name.required' => 'Nama pelapor wajib diisi.',
            'reporter_email.required' => 'Email pelapor wajib diisi.',
            'reporter_email.email' => 'Format email tidak valid.',
        ]);

        // Verify captcha
        if (strtolower(trim($validated['captcha_answer'])) !== strtolower(trim($validated['captcha_expected']))) {
            return back()->withErrors(['captcha_answer' => 'Jawaban captcha tidak sesuai.'])->withInput();
        }

        // Public submissions store files privately on 'local' disk so they are
        // not directly web-accessible. Downloads are served via signed route.
        $attachment = $this->attachmentService->resolve(
            $request->hasFile('attachment') ? $request->file('attachment') : null,
            $validated['attachment_type'] ?? null,
            $validated['attachment_links'] ?? null,
            null,
            'local',
            'incidents',
        );

        $incident = Incident::create([
            'case_id' => Incident::generateCaseId(),
            'access_token' => Str::random(64),
            'reporter_name' => $validated['reporter_name'],
            'reporter_email' => $validated['reporter_email'],
            'reporter_phone' => $validated['reporter_phone'] ?? null,
            'incident_type_id' => $validated['incident_type_id'],
            'description' => $validated['description'],
            'status' => 'Baru',
            'priority' => $validated['priority'],
            'attachment_id' => $attachment?->id,
            'incident_at' => $validated['incident_at'],
            'reported_at' => now(),
        ]);

        $incident->incidentLogs()->create([
            'log_message' => 'Tiket insiden dibuat',
            'is_public' => true,
            'user_id' => Auth::id(),
        ]);

        try {
            Mail::to(config('mail.csirt_email', 'ttis@bojonegorokab.go.id'))
                ->send(new IncidentReportMail($incident));

            Mail::to($validated['reporter_email'])
                ->send(new IncidentConfirmationMail($incident));
        } catch (\Exception $e) {
            Log::error('Incident email sending failed: '.$e->getMessage());
        }

        return back()->with('success', [
            'title' => 'Tiket Berhasil Dibuat!',
            'message' => "Tiket Anda telah berhasil dibuat dengan ID: {$incident->case_id}. Konfirmasi telah dikirim ke email Anda.",
            'case_id' => $incident->case_id,
        ]);
    }

    /**
     * Search for an incident by case ID and email.
     */
    public function search(Request $request)
    {
        $failCount = $request->session()->get('search_fail_count', 0);
        $captchaRequired = $failCount >= 3;

        $rules = [
            'case_id' => 'required|string',
            'email' => 'required|email',
        ];
        $messages = [
            'case_id.required' => 'ID Tiket wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ];

        if ($captchaRequired) {
            $rules['captcha_answer'] = 'required';
            $rules['captcha_expected'] = 'required|string';
            $messages['captcha_answer.required'] = 'Jawaban captcha wajib diisi.';
        }

        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            if ($captchaRequired) {
                return back()->withErrors($validator)->with('captcha_required', true)->withInput();
            }

            return back()->withErrors($validator)->withInput();
        }
        $validated = $validator->validated();

        if ($captchaRequired) {
            if (strtolower(trim($validated['captcha_answer'] ?? '')) !== strtolower(trim($validated['captcha_expected'] ?? ''))) {
                $request->session()->put('search_fail_count', $failCount + 1);

                return back()->withErrors([
                    'captcha' => 'Jawaban captcha tidak sesuai.',
                ])->with('captcha_required', true)->withInput();
            }
        }

        $incident = Incident::with(['incidentType', 'incidentLogs', 'incidentLogs.attachment', 'attachment'])
            ->where('case_id', $validated['case_id'])
            ->where('reporter_email', $validated['email'])
            ->first();

        if (! $incident) {
            $newCount = $failCount + 1;
            $request->session()->put('search_fail_count', $newCount);

            return back()->withErrors([
                'search' => 'Data tiket tidak ditemukan atau kombinasi tidak valid.',
            ])->with('captcha_required', $newCount >= 3)->withInput();
        }

        $request->session()->put('search_fail_count', 0);
        $resource = new PublicIncidentResource($incident->load('incidentType'));

        return back()->with('incident_found', $resource->toArray($request));
    }

    /**
     * Download a private attachment via signed URL.
     */
    public function downloadAttachment(Request $request, string $caseId)
    {
        $email = $request->query('email');
        $incident = Incident::with('attachment')
            ->where('case_id', $caseId)
            ->when($email, fn ($q) => $q->where('reporter_email', $email))
            ->firstOrFail();

        if (! $incident->attachment || ! $incident->attachment->isFile()) {
            abort(404);
        }

        $attachment = $incident->attachment;

        return response()->streamDownload(function () use ($attachment) {
            echo Storage::disk($attachment->disk)->get($attachment->path);
        }, $attachment->filename ?? basename($attachment->path));
    }

    /**
     * Show full incident details for a reporter using access token.
     */
    public function showWithToken(Request $request, string $caseId)
    {
        $token = $request->query('token');
        if (! $token) {
            abort(404);
        }

        $incident = Incident::with(['incidentType', 'incidentLogs', 'incidentLogs.attachment', 'attachment'])
            ->where('case_id', $caseId)
            ->where('access_token', $token)
            ->firstOrFail();

        $resource = new FullIncidentResource($incident);

        return $this->handleSeoRequest('Incidents/Show', [
            'incident' => $resource->toArray($request),
        ]);
    }
}
