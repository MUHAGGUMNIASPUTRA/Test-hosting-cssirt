<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_guide_acknowledgements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('asset_type');
            $table->uuid('asset_id');
            $table->foreignUuid('guide_id')->constrained('virtual_asset_guides')->cascadeOnDelete();
            $table->foreignUuid('acknowledged_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('acknowledged_at');
            $table->timestamps();

            $table->unique(['asset_type', 'asset_id', 'guide_id']);
            $table->index(['asset_type', 'asset_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_guide_acknowledgements');
    }
};
