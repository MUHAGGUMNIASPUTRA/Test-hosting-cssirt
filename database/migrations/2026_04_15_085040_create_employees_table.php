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
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('nip')->nullable()->comment('Masked: 5 digit awal + * + 3 digit akhir');
            $table->string('nik')->nullable()->comment('Masked: 5 digit awal + * + 3 digit akhir');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->foreignUuid('position_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedSmallInteger('year_joined')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
