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
        Schema::table('incidents', function (Blueprint $table) {
            $table->boolean('is_read')->default(false);
            $table->uuid('read_by')->nullable();
            $table->timestamp('read_at')->nullable();

            $table->foreign('read_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['is_read', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['read_by']);
            $table->dropIndex(['is_read', 'created_at']);
            $table->dropColumn(['is_read', 'read_by', 'read_at']);
        });
    }
};
