<?php
// File: app/Http/Controllers/IncidentController.php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesSeoRequests;
use App\Models\Incident;
use App\Models\IncidentType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\IncidentReportMail;
use App\Mail\IncidentConfirmationMail;

class IncidentController extends Controller
{
  use HandlesSeoRequests;

  /**
   * Show the form for creating a new incident report.
   */
  public function create()
  {
    return $this->handleSeoRequest('Incidents/Create', [
      'incidentTypes' => IncidentType::orderBy('name')->get(['id', 'name']),
    ]);
  }

  /**
   * Store a newly created incident report in storage.
   */
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
      'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,zip,doc,docx|max:2048', // Max 2MB
      'captcha_answer' => 'required|string',
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

    // Handle file upload
    $path = null;
    if ($request->hasFile('attachment')) {
      $path = $request->file('attachment')->store('incidents', 'public');
    }

    // Create incident with auto-generated case ID
    $incident = Incident::create([
      'case_id' => 'CSIRT-BJN-' . now()->year . '-' . str_pad(Incident::count() + 1, 4, '0', STR_PAD_LEFT),
      'reporter_name' => $validated['reporter_name'],
      'reporter_email' => $validated['reporter_email'],
      'reporter_phone' => $validated['reporter_phone'],
      'incident_type_id' => $validated['incident_type_id'],
      'description' => $validated['description'],
      'status' => 'Baru',
      'priority' => $validated['priority'],
      'attachment' => $path,
      'incident_at' => $validated['incident_at'],
      'reported_at' => now(),
    ]);

    try {
      // Send email to CSIRT team
      Mail::to(config('mail.csirt_email', 'ttis@bojonegorokab.go.id'))
        ->send(new IncidentReportMail($incident));

      // Send confirmation email to reporter
      Mail::to($validated['reporter_email'])
        ->send(new IncidentConfirmationMail($incident));

    } catch (\Exception $e) {
      Log::error('Incident email sending failed: ' . $e->getMessage());
      // Don't fail the request if email fails
    }

    return back()->with('success', [
      'title' => 'Tiket Berhasil Dibuat!',
      'message' => "Tiket Anda telah berhasil dibuat dengan ID: {$incident->case_id}. Konfirmasi telah dikirim ke email Anda.",
      'case_id' => $incident->case_id,
    ]);
  }

  /**
   * Search for an incident by case ID and email
   */
  public function search(Request $request)
  {
    $validated = $request->validate([
      'case_id' => 'required|string',
      'email' => 'required|email',
    ], [
      'case_id.required' => 'ID Tiket wajib diisi.',
      'email.required' => 'Email wajib diisi.',
      'email.email' => 'Format email tidak valid.',
    ]);

    $incident = Incident::with([
      'incidentType',
      'assignedUser',
      'incidentLogs' => function($query) {
        $query->with('user')->latest();
      }
    ])
      ->where('case_id', $validated['case_id'])
      ->where('reporter_email', $validated['email'])
      ->first();

    if (!$incident) {
      return back()->withErrors([
        'search' => 'Tiket tidak ditemukan. Pastikan ID Tiket dan email yang Anda masukkan benar.'
      ])->withInput();
    }

    // Add file size to the incident data
    $incidentData = $incident->toArray();
    if ($incident->attachment) {
      $incidentData['attachment_file_size'] = $incident->fileSize();
      $incidentData['attachment_filename'] = basename($incident->attachment);
      $incidentData['attachment_extension'] = strtoupper(pathinfo($incident->attachment, PATHINFO_EXTENSION));
    }

    return back()->with('incident_found', $incidentData);
  }
}
