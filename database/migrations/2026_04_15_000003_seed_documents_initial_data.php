<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $filePath = 'guides/csirt.pdf';
        $fullPath = storage_path("app/public/{$filePath}");

        $attachmentId = '44444444-0000-0000-0000-000000000001';

        DB::table('attachments')->insert([
            'id' => $attachmentId,
            'type' => 'file',
            'disk' => 'public',
            'path' => $filePath,
            'url' => null,
            'filename' => 'csirt.pdf',
            'file_size' => file_exists($fullPath) ? filesize($fullPath) : null,
            'mime_type' => 'application/pdf',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $areaId = DB::table('document_areas')->where('slug', 'suplemen')->value('id');

        DB::table('documents')->insert([
            'id' => '55555555-0000-0000-0000-000000000001',
            'title' => 'RFC 2350 CSIRT Bojonegoro',
            'slug' => Str::slug('RFC 2350 CSIRT Bojonegoro').'-'.Str::random(4),
            'description' => null,
            'draft_file_path' => null,
            'official_attachment_id' => $attachmentId,
            'reference_number' => null,
            'stage' => 'Final',
            'version' => 'RFC2350',
            'published_at' => $now,
            'is_public' => true,
            'document_area_id' => $areaId,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        $document = DB::table('documents')->where('version', 'RFC2350')->first();

        if ($document) {
            DB::table('attachments')->where('id', $document->official_attachment_id)->delete();
            DB::table('documents')->where('id', $document->id)->delete();
        }
    }
};
