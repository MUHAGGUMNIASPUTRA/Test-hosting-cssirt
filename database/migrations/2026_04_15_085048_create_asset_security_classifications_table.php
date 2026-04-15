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
        Schema::create('asset_security_classifications', function (Blueprint $table) {
            $table->id();
            $table->string('asset_type');
            $table->uuid('asset_id');
            $table->tinyInteger('confidentiality')->unsigned()->default(1);
            $table->tinyInteger('integrity')->unsigned()->default(1);
            $table->tinyInteger('availability')->unsigned()->default(1);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['asset_type', 'asset_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_security_classifications');
    }
};
