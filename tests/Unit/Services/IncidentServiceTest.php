<?php

namespace Tests\Unit\Services;

use App\Enums\IncidentPriority;
use App\Enums\IncidentStatus;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Models\User;
use App\Services\IncidentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Unit tests for IncidentService.
 *
 * Requires a PostgreSQL test database (the project uses PostgreSQL-specific
 * migrations). Configure DB_DATABASE in .env.testing before running.
 *
 * Run: php artisan test --filter IncidentServiceTest
 */
class IncidentServiceTest extends TestCase
{
    use RefreshDatabase;

    private IncidentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new IncidentService;
    }

    // -------------------------------------------------------------------------
    // resolveAttachment — pure logic, no DB needed
    // -------------------------------------------------------------------------

    public function test_resolve_attachment_returns_null_when_type_is_link_and_no_link_provided(): void
    {
        $result = $this->service->resolveAttachment(null, 'link', null, 'existing/path.pdf');

        $this->assertNull($result);
    }

    public function test_resolve_attachment_returns_link_when_type_is_link_with_value(): void
    {
        $result = $this->service->resolveAttachment(null, 'link', 'https://example.com/file.pdf', null);

        $this->assertSame('https://example.com/file.pdf', $result);
    }

    public function test_resolve_attachment_keeps_existing_when_type_is_file_and_no_new_file(): void
    {
        $result = $this->service->resolveAttachment(null, 'file', null, 'attachments/old.pdf');

        $this->assertSame('attachments/old.pdf', $result);
    }

    public function test_resolve_attachment_keeps_existing_when_type_is_null(): void
    {
        // null type defaults to 'file' behavior
        $result = $this->service->resolveAttachment(null, null, null, 'attachments/old.pdf');

        $this->assertSame('attachments/old.pdf', $result);
    }

    public function test_resolve_attachment_stores_file_and_returns_path(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('report.pdf', 100, 'application/pdf');
        $result = $this->service->resolveAttachment($file, 'file', null, null);

        $this->assertNotNull($result);
        $this->assertStringStartsWith('attachments/', $result);
        Storage::disk('public')->assertExists($result);
    }

    // -------------------------------------------------------------------------
    // getGlobalStats — needs DB
    // -------------------------------------------------------------------------

    public function test_get_global_stats_returns_correct_shape_on_empty_db(): void
    {
        $stats = $this->service->getGlobalStats();

        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('in_progress', $stats);
        $this->assertArrayHasKey('critical', $stats);
        $this->assertArrayHasKey('completed', $stats);

        $this->assertSame(0, $stats['total']);
        $this->assertSame(0, $stats['in_progress']);
        $this->assertSame(0, $stats['critical']);
        $this->assertSame(0, $stats['completed']);
    }

    public function test_get_global_stats_counts_correctly(): void
    {
        $user = User::factory()->create();
        $type = IncidentType::create(['name' => 'Phishing', 'slug' => 'phishing', 'description' => '']);

        $base = [
            'reporter_name' => 'Pelapor',
            'reporter_email' => 'pelapor@test.com',
            'incident_type_id' => $type->id,
            'incident_at' => now(),
            'description' => 'Test',
            'reported_at' => now(),
            'access_token' => 'tok1',
        ];

        // 2 in-progress (Baru)
        Incident::create(array_merge($base, [
            'case_id' => 'CSIRT-2026-04-001',
            'status' => IncidentStatus::Baru->value,
            'priority' => IncidentPriority::Sedang->value,
        ]));
        Incident::create(array_merge($base, [
            'case_id' => 'CSIRT-2026-04-002',
            'status' => IncidentStatus::Diverifikasi->value,
            'priority' => IncidentPriority::Kritikal->value,
            'access_token' => 'tok2',
        ]));
        // 1 completed (Selesai)
        Incident::create(array_merge($base, [
            'case_id' => 'CSIRT-2026-04-003',
            'status' => IncidentStatus::Selesai->value,
            'priority' => IncidentPriority::Rendah->value,
            'access_token' => 'tok3',
        ]));

        $stats = $this->service->getGlobalStats();

        $this->assertSame(3, $stats['total']);
        $this->assertSame(2, $stats['in_progress']); // Baru + Diverifikasi
        $this->assertSame(1, $stats['critical']);     // Kritikal
        $this->assertSame(1, $stats['completed']);    // Selesai
    }

    // -------------------------------------------------------------------------
    // create — needs DB
    // -------------------------------------------------------------------------

    public function test_create_generates_incident_and_initial_log(): void
    {
        $user = User::factory()->create();
        $type = IncidentType::create(['name' => 'Malware', 'slug' => 'malware', 'description' => '']);

        $validated = [
            'reporter_name' => 'Budi',
            'reporter_email' => 'budi@test.com',
            'reporter_phone' => '08123456789',
            'incident_type_id' => $type->id,
            'incident_at' => now()->toDateTimeString(),
            'description' => 'Komputer terinfeksi malware',
            'status' => IncidentStatus::Baru->value,
            'priority' => IncidentPriority::Tinggi->value,
            'attachment_type' => 'link',
            'attachment_links' => 'https://screenshot.example.com',
        ];

        $incident = $this->service->create($validated, null, $user->id);

        $this->assertInstanceOf(Incident::class, $incident);
        $this->assertDatabaseHas('incidents', [
            'reporter_name' => 'Budi',
            'reporter_email' => 'budi@test.com',
            'attachment' => 'https://screenshot.example.com',
        ]);

        // Initial log should be created
        $this->assertDatabaseHas('incident_logs', [
            'incident_id' => $incident->id,
            'user_id' => $user->id,
            'log_message' => 'Tiket insiden dibuat',
        ]);
    }

    // -------------------------------------------------------------------------
    // logChanges — needs DB
    // -------------------------------------------------------------------------

    public function test_log_changes_creates_log_entry_for_status_change(): void
    {
        $user = User::factory()->create();
        $type = IncidentType::create(['name' => 'DDoS', 'slug' => 'ddos', 'description' => '']);
        $incident = Incident::create([
            'case_id' => 'CSIRT-2026-04-TEST',
            'access_token' => 'tokentest',
            'reporter_name' => 'Ali',
            'reporter_email' => 'ali@test.com',
            'incident_type_id' => $type->id,
            'incident_at' => now(),
            'description' => 'Test DDoS',
            'status' => IncidentStatus::Baru->value,
            'priority' => IncidentPriority::Sedang->value,
            'reported_at' => now(),
        ]);

        // Reload fresh from DB so getOriginal() returns DB values
        $incident = Incident::find($incident->id);

        $this->service->logChanges($incident, [
            'status' => IncidentStatus::Selesai->value,
        ], $user->id);

        $this->assertDatabaseHas('incident_logs', [
            'incident_id' => $incident->id,
            'user_id' => $user->id,
            'log_message' => "Status diubah dari 'Baru' menjadi 'Selesai'.",
        ]);
    }

    public function test_log_changes_marks_log_public_when_status_changes(): void
    {
        $user = User::factory()->create();
        $type = IncidentType::create(['name' => 'Ransomware', 'slug' => 'ransomware', 'description' => '']);
        $incident = Incident::create([
            'case_id' => 'CSIRT-2026-04-PUB1',
            'access_token' => 'tokenpub1',
            'reporter_name' => 'Rina',
            'reporter_email' => 'rina@test.com',
            'incident_type_id' => $type->id,
            'incident_at' => now(),
            'description' => 'Test public status',
            'status' => IncidentStatus::Baru->value,
            'priority' => IncidentPriority::Sedang->value,
            'reported_at' => now(),
        ]);

        $incident = Incident::find($incident->id);

        $this->service->logChanges($incident, [
            'status' => IncidentStatus::DalamPenyelidikan->value,
        ], $user->id);

        $this->assertDatabaseHas('incident_logs', [
            'incident_id' => $incident->id,
            'user_id' => $user->id,
            'is_public' => true,
        ]);
    }

    public function test_log_changes_marks_log_public_when_priority_changes(): void
    {
        $user = User::factory()->create();
        $type = IncidentType::create(['name' => 'Brute Force', 'slug' => 'brute-force', 'description' => '']);
        $incident = Incident::create([
            'case_id' => 'CSIRT-2026-04-PUB2',
            'access_token' => 'tokenpub2',
            'reporter_name' => 'Deni',
            'reporter_email' => 'deni@test.com',
            'incident_type_id' => $type->id,
            'incident_at' => now(),
            'description' => 'Test public priority',
            'status' => IncidentStatus::Baru->value,
            'priority' => IncidentPriority::Rendah->value,
            'reported_at' => now(),
        ]);

        $incident = Incident::find($incident->id);

        $this->service->logChanges($incident, [
            'priority' => IncidentPriority::Kritikal->value,
        ], $user->id);

        $this->assertDatabaseHas('incident_logs', [
            'incident_id' => $incident->id,
            'user_id' => $user->id,
            'is_public' => true,
        ]);
    }

    public function test_log_changes_does_not_mark_log_public_for_non_status_priority_changes(): void
    {
        $user = User::factory()->create();
        $type = IncidentType::create(['name' => 'Spam', 'slug' => 'spam', 'description' => '']);
        $incident = Incident::create([
            'case_id' => 'CSIRT-2026-04-PRIV',
            'access_token' => 'tokenpriv',
            'reporter_name' => 'Tono',
            'reporter_email' => 'tono@test.com',
            'incident_type_id' => $type->id,
            'incident_at' => now(),
            'description' => 'Test not public',
            'status' => IncidentStatus::Baru->value,
            'priority' => IncidentPriority::Sedang->value,
            'reported_at' => now(),
        ]);

        $incident = Incident::find($incident->id);

        $this->service->logChanges($incident, [
            'reporter_name' => 'Tono Updated',
        ], $user->id);

        $this->assertDatabaseHas('incident_logs', [
            'incident_id' => $incident->id,
            'user_id' => $user->id,
            'is_public' => false,
        ]);
    }

    public function test_log_changes_skips_unchanged_fields(): void
    {
        $user = User::factory()->create();
        $type = IncidentType::create(['name' => 'Defacement', 'slug' => 'defacement', 'description' => '']);
        $incident = Incident::create([
            'case_id' => 'CSIRT-2026-04-SKP',
            'access_token' => 'tokenskip',
            'reporter_name' => 'Siti',
            'reporter_email' => 'siti@test.com',
            'incident_type_id' => $type->id,
            'incident_at' => now(),
            'description' => 'Test',
            'status' => IncidentStatus::Baru->value,
            'priority' => IncidentPriority::Rendah->value,
            'reported_at' => now(),
        ]);

        $incident = Incident::find($incident->id);
        $logCountBefore = $incident->incidentLogs()->count();

        // Pass same values — nothing should be logged
        $this->service->logChanges($incident, [
            'status' => IncidentStatus::Baru->value,   // unchanged
            'priority' => IncidentPriority::Rendah->value, // unchanged
        ], $user->id);

        $this->assertSame($logCountBefore, $incident->fresh()->incidentLogs()->count());
    }
}
