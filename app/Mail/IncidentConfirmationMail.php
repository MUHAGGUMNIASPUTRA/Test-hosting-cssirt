<?php
// filepath: app/Mail/IncidentConfirmationMail.php

namespace App\Mail;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IncidentConfirmationMail extends Mailable
{
  use Queueable, SerializesModels;

  public $incident;

  public function __construct(Incident $incident)
  {
    $this->incident = $incident;
  }

  public function build()
  {
    return $this->subject("Konfirmasi Tiket - {$this->incident->case_id}")
                ->view('emails.incident-confirmation')
                ->with([
                  'incident' => $this->incident,
                ]);
  }
}
