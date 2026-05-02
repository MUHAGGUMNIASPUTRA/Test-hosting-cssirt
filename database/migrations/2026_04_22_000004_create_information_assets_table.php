<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('information_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('document_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('storage_format', ['file_dokumen', 'cetak', 'keduanya']);
            $table->foreignUuid('location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('owner_org_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('information_assets');
    }
};
