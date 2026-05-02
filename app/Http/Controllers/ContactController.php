<?php

// File: app/Http/Controllers/ContactController.php

namespace App\Http\Controllers;

use App\Http\Traits\HandlesSeoRequests;
use App\Mail\ContactConfirmationMail;
use App\Mail\ContactFormMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    use HandlesSeoRequests;

    /**
     * Display the contact page.
     */
    public function index()
    {
        return $this->handleSeoRequest('Contact/Index');
    }

    /**
     * Handle contact form submission.
     */
    public function store(Request $request)
    {
        // Validate the form data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10',
            'type' => 'required|in:general,consultation,report,partnership',
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'subject.required' => 'Subjek wajib diisi.',
            'message.required' => 'Pesan wajib diisi.',
            'message.min' => 'Pesan minimal 10 karakter.',
            'type.in' => 'Jenis pesan tidak valid.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $validated = $validator->validated();

        try {
            // Send email to CSIRT team
            Mail::to(config('mail.csirt_email', 'ttis@bojonegorokab.go.id'))
                ->send(new ContactFormMail($validated));

            // Send confirmation email to user
            Mail::to($validated['email'])
                ->send(new ContactConfirmationMail($validated));

            return back()->with('success', [
                'title' => 'Pesan Berhasil Dikirim!',
                'message' => 'Terima kasih atas pesan Anda. Tim CSIRT akan merespons dalam 1x24 jam. Silakan periksa email Anda untuk konfirmasi.',
                'type' => 'success',
            ]);

        } catch (\Exception $e) {
            Log::error('Contact form error: '.$e->getMessage());

            return back()->with('error', [
                'title' => 'Gagal Mengirim Pesan',
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi kami melalui telepon.',
                'type' => 'error',
            ])->withInput();
        }
    }
}
