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
        Schema::create('web_applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('stage', ['draft', 'pengajuan', 'pengujian', 'revisi', 'persiapan', 'diterima'])->default('draft');
            $table->enum('app_status', ['aktif', 'idle', 'nonaktif'])->default('aktif');
            $table->enum('https_status', ['aktif', 'expired', 'nonaktif'])->default('nonaktif');
            $table->foreignUuid('location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('provider_org_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->foreignUuid('owner_org_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->enum('owner_contact_type', ['auto', 'manual'])->default('auto');
            $table->foreignUuid('owner_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignUuid('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_applications');
    }
};
