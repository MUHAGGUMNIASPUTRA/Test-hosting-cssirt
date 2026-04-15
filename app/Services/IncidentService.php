<?php

namespace App\Services;

use App\Models\Attachment;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\User;
use Carbon\Carbon;
use Fruitcake\LaravelDebugbar\Facades\Debugbar;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class IncidentService
{
    public function __construct(private readonly AttachmentService $attachmentService) {}

    /**
     * Get global statistics for all incidents.
     *
     * @return array{total: int, in_progress: int, critical: int, completed: int}
     */
    public function getGlobalStats(): array
    {
        $stats = DB::table('incidents')
            ->select([
                DB::raw('COUNT(*) as total'),
                DB::raw("COUNT(CASE WHEN status IN ('Baru', 'Diverifikasi', 'Dalam Penyelidikan') THEN 1 END) as in_progress"),
                DB::raw("COUNT(CASE WHEN priority = 'Kritikal' THEN 1 END) as critical"),
                DB::raw("COUNT(CASE WHEN status = 'Selesai' THEN 1 END) as completed"),
            ])
            ->first();

        return [
            'total' => (int) $stats->total,
            'in_progress' => (int) $stats->in_progress,
            'critical' => (int) $stats->critical,
            'completed' => (int) $stats->completed,
        ];
    }

    /**
     * Create a new incident and log the creation.
     */
    public function create(array $validated, ?UploadedFile $file, int $actorId, string $disk = 'public', string $directory = 'attachments'): Incident
    {
        $attachment = $this->attachmentService->resolve(
            $file,
            $validated['attachment_type'] ?? null,
            $validated['attachment_links'] ?? null,
            null,
            $disk,
            $directory,
        );

        $incident = Incident::create([
            'case_id' => Incident::generateCaseId(),
            'access_token' => \Illuminate\Support\Str::random(64),
            'reporter_name' => $validated['reporter_name'],
            'reporter_email' => $validated['reporter_email'],
            'reporter_phone' => $validated['reporter_phone'] ?? null,
            'incident_type_id' => $validated['incident_type_id'],
            'incident_at' => $validated['incident_at'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'assigned_to' => $validated['assigned_to'] ?? null,
            'attachment_id' => $attachment?->id,
            'reported_at' => now(),
        ]);

        $incident->incidentLogs()->create([
            'log_message' => 'Tiket insiden dibuat',
            'user_id' => $actorId,
        ]);

        return $incident;
    }

    /**
     * Update an existing incident and log all changes.
     */
    public function update(
        Incident $incident,
        array $validated,
        ?UploadedFile $file,
        int $actorId,
        string $disk = 'public',
        string $directory = 'attachments',
    ): void {
        $attachment = $this->attachmentService->resolve(
            $file,
            $validated['attachment_type'] ?? null,
            $validated['attachment_links'] ?? null,
            $incident->attachment,
            $disk,
            $directory,
        );

        $coreData = [
            'reporter_name' => $validated['reporter_name'],
            'reporter_email' => $validated['reporter_email'],
            'reporter_phone' => $validated['reporter_phone'] ?? null,
            'incident_type_id' => $validated['incident_type_id'],
            'incident_at' => $validated['incident_at'],
            'description' => $validated['description'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'assigned_to' => $validated['assigned_to'] ?? null,
            'attachment_id' => $attachment?->id,
        ];

        $this->logChanges($incident, $coreData, $actorId, $attachment);
        $incident->update($coreData);
    }

    /**
     * Update management fields only (status, priority, assigned_to) and log changes.
     */
    public function updateManagement(Incident $incident, array $validated, int $actorId): void
    {
        $this->logChanges($incident, $validated, $actorId);
        $incident->update($validated);
    }

    /**
     * Log changes to an incident compared to its current state.
     */
    public function logChanges(Incident $incident, array $newData, int $actorId, ?Attachment $newAttachment = null): void
    {
        $changes = [];
        $isPublic = false;
        $originalData = $incident->getOriginal();
        $normalized = $this->normalizeDataForComparison($originalData, $newData);

        // Prefetch reference names to avoid N+1
        $typeIds = collect([$originalData['incident_type_id'] ?? null, $newData['incident_type_id'] ?? null])->filter()->unique()->values();
        $typesById = $typeIds->isEmpty() ? collect() : IncidentType::whereIn('id', $typeIds)->get(['id', 'name'])->keyBy('id');

        $userIds = collect([$originalData['assigned_to'] ?? null, $newData['assigned_to'] ?? null])->filter()->unique()->values();
        $usersById = $userIds->isEmpty() ? collect() : User::whereIn('id', $userIds)->get(['id', 'name'])->keyBy('id');

        foreach ($newData as $key => $value) {
            if ($normalized['original'][$key] === $normalized['new'][$key]) {
                continue;
            }

            Debugbar::info([
                "Field '{$key}' changed:",
                'original' => $normalized['original'][$key],
                'new' => $normalized['new'][$key],
            ]);

            switch ($key) {
                case 'reporter_name':
                    $changes[] = "Nama pelapor diubah dari '{$originalData[$key]}' menjadi '{$value}'.";
                    break;
                case 'reporter_email':
                    $changes[] = "Email pelapor diubah dari '{$originalData[$key]}' menjadi '{$value}'.";
                    break;
                case 'reporter_phone':
                    $oldPhone = $originalData[$key] ?: 'Tidak ada';
                    $newPhone = $value ?: 'Tidak ada';
                    $changes[] = "Nomor telepon pelapor diubah dari '{$oldPhone}' menjadi '{$newPhone}'.";
                    break;
                case 'incident_type_id':
                    $oldType = $originalData[$key] ? optional($typesById->get((int) $originalData[$key]))->name : 'Tidak ada';
                    $newType = $value ? optional($typesById->get((int) $value))->name : 'Tidak ada';
                    $changes[] = "Kategori insiden diubah dari '{$oldType}' menjadi '{$newType}'.";
                    break;
                case 'description':
                    $changes[] = 'Deskripsi insiden diperbarui.';
                    break;
                case 'status':
                    $isPublic = true;
                    $changes[] = "Status diubah dari '{$originalData[$key]->value}' menjadi '{$value}'.";
                    break;
                case 'priority':
                    $isPublic = true;
                    $changes[] = "Prioritas diubah dari '{$originalData[$key]->value}' menjadi '{$value}'.";
                    break;
                case 'assigned_to':
                    $oldName = $originalData[$key] ? (optional($usersById->get((int) $originalData[$key]))->name ?? 'Belum Ditugaskan') : 'Belum Ditugaskan';
                    $newName = $value ? (optional($usersById->get((int) $value))->name ?? 'Belum Ditugaskan') : 'Belum Ditugaskan';
                    $changes[] = "Insiden ditugaskan dari '{$oldName}' ke '{$newName}'.";
                    break;
                case 'attachment_id':
                    $changes[] = $newAttachment
                        ? 'Lampiran insiden diperbarui.'
                        : 'Lampiran insiden dihapus.';
                    break;
            }
        }

        $incident->incidentLogs()->create([
            'log_message' => implode("\n", $changes),
            'user_id' => $actorId,
            'is_public' => $isPublic,
        ]);
    }

    /**
     * Normalize field values for accurate comparison between original and new data.
     *
     * @return array{original: array<string, mixed>, new: array<string, mixed>}
     */
    private function normalizeDataForComparison(array $originalData, array $newData): array
    {
        $normalized = ['original' => [], 'new' => []];

        foreach ($newData as $key => $value) {
            if ($key === 'incident_at') {
                $normalized['original'][$key] = $originalData[$key] ? Carbon::parse($originalData[$key])->format('Y-m-d H:i:s') : null;
                $normalized['new'][$key] = $value ? Carbon::parse($value)->format('Y-m-d H:i:s') : null;
            } elseif ($key === 'incident_type_id' || $key === 'assigned_to' || $key === 'attachment_id') {
                $normalized['original'][$key] = (string) ($originalData[$key] ?? '');
                $normalized['new'][$key] = (string) ($value ?? '');
            } elseif ($key === 'status' || $key === 'priority') {
                $normalized['original'][$key] = (string) ($originalData[$key]->value ?? '');
                $normalized['new'][$key] = (string) ($value ?? '');
            } else {
                $normalized['original'][$key] = $originalData[$key] ?? null;
                $normalized['new'][$key] = $value;
            }
        }

        return $normalized;
    }
}
