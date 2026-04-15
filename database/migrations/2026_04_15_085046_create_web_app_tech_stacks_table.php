<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('web_app_tech_stacks', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('web_application_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('tech_stack_id')->constrained()->cascadeOnDelete();
            $table->string('version')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_app_tech_stacks');
    }
};
