<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Drop existing FKs to redefine with proper ON DELETE behavior
            $table->dropForeign('incidents_incident_type_id_foreign');
            $table->dropForeign('incidents_assigned_to_foreign');

            // Ensure indexes exist on FK columns (helps joins and enforcement)
            $table->index('incident_type_id', 'incidents_incident_type_id_idx_fk'); // if not already
            $table->index('assigned_to', 'incidents_assigned_to_idx_fk'); // if not already

            // Recreate FK: Types cannot be deleted when used (RESTRICT)
            $table->foreign('incident_type_id', 'incidents_incident_type_id_foreign')
                ->references('id')
                ->on('incident_types')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            // Recreate FK: If a user is deleted, unassign the ticket (SET NULL)
            $table->foreign('assigned_to', 'incidents_assigned_to_foreign')
                ->references('id')
                ->on('users')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table) {
            // Drop our adjusted FKs
            $table->dropForeign('incidents_incident_type_id_foreign');
            $table->dropForeign('incidents_assigned_to_foreign');

            // Restore original behavior: NO ACTION (default)
            $table->foreign('incident_type_id', 'incidents_incident_type_id_foreign')
                ->references('id')
                ->on('incident_types')
                ->noActionOnDelete()
                ->noActionOnUpdate();

            $table->foreign('assigned_to', 'incidents_assigned_to_foreign')
                ->references('id')
                ->on('users')
                ->noActionOnDelete()
                ->noActionOnUpdate();

            // Optional: drop helper indexes we added for FKs
            $table->dropIndex('incidents_incident_type_id_idx_fk');
            $table->dropIndex('incidents_assigned_to_idx_fk');
        });
    }
};
