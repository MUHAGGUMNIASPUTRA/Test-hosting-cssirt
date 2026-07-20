<?php

// filepath: app/Http/Resources/PublicIncidentResource.php

namespace App\Http\Resources;

use App\Models\Attachment;
use App\Models\Incident;
use App\Models\IncidentType;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\URL;

/**
 * @property Incident $resource
 * @property string $case_id
 * @property string $status
 * @property string $priority
 * @property string $reported_at
 * @property IncidentType $incidentType
 * @property Attachment|null $attachment
 * @property string $reporter_email
 * @property Collection $incidentLogs
 */
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

        // Attachment: use unified AttachmentResource shape, then add signed download URL for
        // private-disk files (local disk = not directly accessible via /storage/).
        if ($this->attachment) {
            $attachmentData = (new AttachmentResource($this->attachment))->toArray($request);

            if ($this->attachment->isFile() && $this->attachment->disk === 'local') {
                $attachmentData['url'] = URL::signedRoute('incident.attachment.download', [
                    'caseId' => $this->case_id,
                    'email' => $this->reporter_email,
                ], now()->addMinutes(15));
            }

            $data['attachment'] = $attachmentData;
        }

        $data['logs'] = $this->whenLoaded('incidentLogs', /** @phpstan-ignore-next-line */ function () use ($request) {
            return $this->incidentLogs
                ->filter(fn ($log) => $log->is_public)
                ->values()
                ->map(function ($log) use ($request) {
                    $isEdited = $log->updated_at->gt($log->created_at);

                    return [
                        'message' => $log->log_message,
                        'created_at' => $log->created_at,
                        'is_edited' => $isEdited,
                        'edited_at' => $isEdited ? $log->updated_at : null,
                        'attachment' => $log->attachment
                            ? (new AttachmentResource($log->attachment))->toArray($request)
                            : null,
                    ];
                });
        });

        return $data;
    }
}
