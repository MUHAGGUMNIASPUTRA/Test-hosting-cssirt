<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up(): void
  {
    Schema::create('document_areas', function (Blueprint $table) {
      $table->id();
      $table->string('name')->unique();
      $table->string('slug')->unique();
      $table->text('description')->nullable();
      $table->timestamps();
    });

    Schema::table('documents', function (Blueprint $table) {
      $table->foreignId('document_area_id')
        ->nullable()
        ->after('id')
        ->constrained('document_areas')
        ->nullOnDelete();

      // Make existing file_path nullable (was used for single file/link)
      $table->string('file_path')->nullable()->change();

      // New: File Dokumen Sah (PDF — upload atau link, wajib diisi)
      $table->string('official_file_path')->nullable()->after('file_path');

      // Visibilitas publik
      $table->boolean('is_public')->default(false)->after('published_at');
    });
  }

  public function down(): void
  {
    Schema::table('documents', function (Blueprint $table) {
      $table->dropForeign(['document_area_id']);
      $table->dropColumn(['document_area_id', 'official_file_path', 'is_public']);
      $table->string('file_path')->nullable(false)->change();
    });

    Schema::dropIfExists('document_areas');
  }
};
