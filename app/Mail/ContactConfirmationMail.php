<?php
// File: app/Mail/ContactConfirmationMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactConfirmationMail extends Mailable
{
  use Queueable, SerializesModels;

  public $contactData;

  /**
   * Create a new message instance.
   */
  public function __construct($contactData)
  {
    $this->contactData = $contactData;
  }

  /**
   * Build the message.
   */
  public function build()
  {
    $typeLabels = [
      'general' => 'Informasi Umum',
      'consultation' => 'Konsultasi Keamanan',
      'report' => 'Laporan Non-Darurat',
      'partnership' => 'Kerjasama'
    ];

    return $this->subject('Konfirmasi Pesan Anda - CSIRT Bojonegoro')
      ->view('emails.contact-confirmation')
      ->with([
        'contactData' => $this->contactData,
        'typeLabel' => $typeLabels[$this->contactData['type']] ?? 'Lainnya'
      ]);
  }
}
