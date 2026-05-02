<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attachments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('type', ['file', 'link']);
            // File-only fields
            $table->string('disk')->nullable();        // 'local' | 'public'
            $table->string('path')->nullable();        // relative storage path
            $table->bigInteger('file_size')->nullable(); // bytes
            $table->string('mime_type')->nullable();
            // Link-only field
            $table->text('url')->nullable();
            // Shared
            $table->string('filename')->nullable();    // original filename for display
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attachments');
    }
};
