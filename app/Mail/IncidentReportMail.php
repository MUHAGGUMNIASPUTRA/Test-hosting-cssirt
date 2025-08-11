<?php
// filepath: app/Mail/IncidentReportMail.php

namespace App\Mail;

use App\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class IncidentReportMail extends Mailable
{
  use Queueable, SerializesModels;

  public $incident;

  public function __construct(Incident $incident)
  {
    $this->incident = $incident;
  }

  public function build()
  {
    return $this->subject("Laporan Insiden Baru - {$this->incident->case_id}")
                ->view('emails.incident-report')
                ->with([
                  'incident' => $this->incident,
                ]);
  }
}
