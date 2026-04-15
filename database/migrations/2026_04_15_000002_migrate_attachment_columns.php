<?php

use App\Enums\AttachmentType;
use App\Models\Attachment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migrates legacy inline attachment columns to the unified `attachments` table.
 *
 * Tables affected:
 *  - incidents       : attachment (string) → attachment_id FK
 *  - incident_logs   : attachment + attachment_type (strings) → attachment_id FK
 *  - documents       : official_file_path (string) → official_attachment_id FK
 *  - posts           : image (string) → image_id FK
 *
 * For development: php artisan migrate:fresh --seed is sufficient.
 * For production: this migration copies existing data before dropping old columns.
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Add nullable FK columns ────────────────────────────────────────

        Schema::table('incidents', function (Blueprint $table) {
            $table->foreignId('attachment_id')->nullable()->constrained('attachments')->nullOnDelete()->after('attachment');
        });

        Schema::table('incident_logs', function (Blueprint $table) {
            $table->foreignId('attachment_id')->nullable()->constrained('attachments')->nullOnDelete()->after('attachment_type');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->foreignId('official_attachment_id')->nullable()->constrained('attachments')->nullOnDelete()->after('official_file_path');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('image_id')->nullable()->constrained('attachments')->nullOnDelete()->after('image');
        });

        // ── 2. Migrate existing data ──────────────────────────────────────────

        $this->migrateIncidents();
        $this->migrateIncidentLogs();
        $this->migrateDocuments();
        $this->migratePosts();

        // ── 3. Drop old columns ───────────────────────────────────────────────

        Schema::table('incidents', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });

        Schema::table('incident_logs', function (Blueprint $table) {
            $table->dropColumn(['attachment', 'attachment_type']);
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('official_file_path');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }

    public function down(): void
    {
        // Re-add old columns
        Schema::table('incidents', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('attachment_id');
        });

        Schema::table('incident_logs', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('attachment_id');
            $table->string('attachment_type')->nullable()->after('attachment');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->string('official_file_path')->nullable()->after('official_attachment_id');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->string('image')->nullable()->after('image_id');
        });

        // Drop FK columns (data not restored)
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attachment_id');
        });

        Schema::table('incident_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('attachment_id');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->dropConstrainedForeignId('official_attachment_id');
        });

        Schema::table('posts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('image_id');
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function migrateIncidents(): void
    {
        DB::table('incidents')
            ->whereNotNull('attachment')
            ->orderBy('id')
            ->each(function (object $row) {
                $attachmentId = $this->createFromString($row->attachment, 'local', 'incidents');
                DB::table('incidents')->where('id', $row->id)->update(['attachment_id' => $attachmentId]);
            });
    }

    private function migrateIncidentLogs(): void
    {
        DB::table('incident_logs')
            ->whereNotNull('attachment')
            ->orderBy('id')
            ->each(function (object $row) {
                $type = $row->attachment_type ?? (str_starts_with($row->attachment, 'http') ? 'link' : 'file');
                $attachmentId = $this->createFromString($row->attachment, 'public', 'incidents/logs', $type);
                DB::table('incident_logs')->where('id', $row->id)->update(['attachment_id' => $attachmentId]);
            });
    }

    private function migrateDocuments(): void
    {
        DB::table('documents')
            ->whereNotNull('official_file_path')
            ->orderBy('id')
            ->each(function (object $row) {
                $attachmentId = $this->createFromString($row->official_file_path, 'public', 'documents/official');
                DB::table('documents')->where('id', $row->id)->update(['official_attachment_id' => $attachmentId]);
            });
    }

    private function migratePosts(): void
    {
        DB::table('posts')
            ->whereNotNull('image')
            ->orderBy('id')
            ->each(function (object $row) {
                $attachmentId = $this->createFromString($row->image, 'public', 'posts');
                DB::table('posts')->where('id', $row->id)->update(['image_id' => $attachmentId]);
            });
    }

    /**
     * Create an Attachment record from a legacy string value.
     * Returns the new attachment ID.
     */
    private function createFromString(string $value, string $disk, string $directory, ?string $explicitType = null): int
    {
        $isLink = $explicitType === 'link' || str_starts_with($value, 'http://') || str_starts_with($value, 'https://');

        if ($isLink) {
            return DB::table('attachments')->insertGetId([
                'type' => AttachmentType::Link->value,
                'url' => $value,
                'filename' => basename(parse_url($value, PHP_URL_PATH) ?: $value),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return DB::table('attachments')->insertGetId([
            'type' => AttachmentType::File->value,
            'disk' => $disk,
            'path' => $value,
            'filename' => basename($value),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
};
