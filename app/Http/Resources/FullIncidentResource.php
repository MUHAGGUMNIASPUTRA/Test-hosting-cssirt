<?php
// filepath: app/Http/Resources/FullIncidentResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class FullIncidentResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @return array
   */
  public function toArray($request)
  {
    return [
      'case_id' => $this->case_id,
      'status' => $this->status,
      'priority' => $this->priority,
      'reported_at' => $this->reported_at,
      'incident_at' => $this->incident_at,
      'incident_type' => $this->whenLoaded('incidentType', function () {
        return [
          'name' => optional($this->incidentType)->name,
        ];
      }),
      'description' => $this->description,
      'attachment' => $this->when($this->attachment, function () {
        return [
          'filename' => basename($this->attachment),
          'extension' => strtoupper(pathinfo($this->attachment, PATHINFO_EXTENSION)),
          'file_size' => method_exists($this->resource, 'fileSize') ? $this->resource->fileSize() : null,
          'download_url' => URL::signedRoute('incident.attachment.download', [
            'caseId' => $this->case_id,
          ], now()->addMinutes(15)),
        ];
      }),
      'logs' => $this->whenLoaded('incidentLogs', function () {
        return $this->incidentLogs->map(function ($log) {
          return [
            'message' => $log->log_message,
            'created_at' => $log->created_at,
          ];
        });
      }),
    ];
  }
}
