<?php
// File: app/Mail/ContactFormMail.php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
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

    return $this->subject('Pesan Baru dari Website CSIRT Bojonegoro - ' . $this->contactData['subject'])
      ->replyTo($this->contactData['email'], $this->contactData['name'])
      ->view('emails.contact-form')
      ->with([
        'contactData' => $this->contactData,
        'typeLabel' => $typeLabels[$this->contactData['type']] ?? 'Lainnya'
      ]);
  }
}
