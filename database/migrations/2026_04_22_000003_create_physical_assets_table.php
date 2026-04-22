<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('physical_assets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('asset_code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('specifications')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->foreignUuid('attachment_id')->nullable()->constrained('attachments')->nullOnDelete();
            $table->foreignUuid('location_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignUuid('owner_org_id')->nullable()->constrained('organizations')->nullOnDelete();
            $table->enum('owner_contact_type', ['auto', 'manual'])->default('auto');
            $table->foreignUuid('owner_employee_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('physical_assets');
    }
};
