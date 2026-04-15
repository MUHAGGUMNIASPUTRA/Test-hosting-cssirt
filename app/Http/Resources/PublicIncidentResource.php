<?php

// filepath: app/Http/Resources/PublicIncidentResource.php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class PublicIncidentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array
     */
    public function toArray($request)
    {
        $data = [
            'case_id' => $this->case_id,
            'status' => $this->status,
            'priority' => $this->priority,
            'reported_at' => $this->reported_at,
            'incident_type' => $this->whenLoaded('incidentType', function () {
                return [
                    'name' => optional($this->incidentType)->name,
                ];
            }),
        ];

        if ($this->attachment) {
            $isLink = str_starts_with($this->attachment, 'http://') || str_starts_with($this->attachment, 'https://');

            if ($isLink) {
                $data['attachment'] = [
                    'type' => 'link',
                    'url' => $this->attachment,
                    'filename' => basename($this->attachment),
                ];
            } else {
                $data['attachment'] = [
                    'type' => 'file',
                    'filename' => basename($this->attachment),
                    'extension' => strtoupper(pathinfo($this->attachment, PATHINFO_EXTENSION)),
                    'file_size' => method_exists($this->resource, 'fileSize') ? $this->resource->fileSize() : null,
                    'download_url' => URL::signedRoute('incident.attachment.download', [
                        'caseId' => $this->case_id,
                        'email' => $this->reporter_email,
                    ], now()->addMinutes(15)),
                ];
            }
        }

        $data['logs'] = $this->whenLoaded('incidentLogs', function () {
            return $this->incidentLogs
                ->filter(fn ($log) => $log->is_public)
                ->values()
                ->map(function ($log) {
                    $isEdited = $log->updated_at->gt($log->created_at);

                    return [
                        'message' => $log->log_message,
                        'created_at' => $log->created_at,
                        'is_edited' => $isEdited,
                        'edited_at' => $isEdited ? $log->updated_at : null,
                        'attachment' => $log->attachment,
                        'attachment_type' => $log->attachment_type,
                    ];
                });
        });

        return $data;
    }
}
