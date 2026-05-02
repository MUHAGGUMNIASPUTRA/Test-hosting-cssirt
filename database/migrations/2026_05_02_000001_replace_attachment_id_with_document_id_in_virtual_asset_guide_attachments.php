<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('virtual_asset_guide_attachments', function (Blueprint $table) {
            $table->dropForeign(['attachment_id']);
            $table->dropColumn('attachment_id');
            $table->foreignUuid('document_id')
                ->after('virtual_asset_guide_id')
                ->constrained()
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('virtual_asset_guide_attachments', function (Blueprint $table) {
            $table->dropForeign(['document_id']);
            $table->dropColumn('document_id');
            $table->foreignUuid('attachment_id')
                ->after('virtual_asset_guide_id')
                ->constrained('attachments')
                ->cascadeOnDelete();
        });
    }
};
