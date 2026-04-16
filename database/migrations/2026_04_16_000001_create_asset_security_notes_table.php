<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_security_notes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('asset_type');
            $table->uuid('asset_id');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->foreignId('attachment_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index(['asset_type', 'asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_security_notes');
    }
};
