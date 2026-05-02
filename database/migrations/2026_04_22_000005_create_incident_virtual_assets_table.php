<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_virtual_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('incident_id')->constrained()->cascadeOnDelete();
            $table->string('assetable_type');
            $table->uuid('assetable_id');
            $table->timestamps();

            $table->index(['assetable_type', 'assetable_id']);
            $table->unique(['incident_id', 'assetable_type', 'assetable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_virtual_assets');
    }
};
