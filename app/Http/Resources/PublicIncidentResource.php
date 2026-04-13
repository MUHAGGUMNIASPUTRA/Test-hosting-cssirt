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
            $data['attachment'] = [
                'filename' => basename($this->attachment),
                'extension' => strtoupper(pathinfo($this->attachment, PATHINFO_EXTENSION)),
                'file_size' => method_exists($this->resource, 'fileSize') ? $this->resource->fileSize() : null,
                'download_url' => URL::signedRoute('incident.attachment.download', [
                    'caseId' => $this->case_id,
                    'email' => $this->reporter_email,
                ], now()->addMinutes(15)),
            ];
        }

        return $data;
    }
}
