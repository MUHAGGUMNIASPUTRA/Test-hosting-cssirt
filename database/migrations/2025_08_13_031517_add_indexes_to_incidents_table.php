<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  // We need to run some CREATE INDEX CONCURRENTLY statements (cannot be inside a transaction)
  public $withinTransaction = false;

  public function up(): void
  {
    // Regular BTREE indexes for frequent filters/sorts
    Schema::table('incidents', function (Blueprint $table) {
      $table->index('reporter_email', 'incidents_reporter_email_idx');
      $table->index('incident_type_id', 'incidents_incident_type_id_idx');
      $table->index('priority', 'incidents_priority_idx');
      $table->index('status', 'incidents_status_idx');
      $table->index('reported_at', 'incidents_reported_at_idx');
    });

    // Enable pg_trgm extension for fast ILIKE searches
    DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');

    // Trigram GIN indexes to accelerate ILIKE "%term%" searches used in admin index
    // Use CONCURRENTLY to avoid long locks on a live site
    DB::statement('CREATE INDEX IF NOT EXISTS incidents_case_id_trgm_idx     ON incidents USING gin (case_id gin_trgm_ops)');
    DB::statement('CREATE INDEX IF NOT EXISTS incidents_reporter_name_trgm_idx ON incidents USING gin (reporter_name gin_trgm_ops)');
    DB::statement('CREATE INDEX IF NOT EXISTS incidents_reporter_email_trgm_idx ON incidents USING gin (reporter_email gin_trgm_ops)');
    DB::statement('CREATE INDEX IF NOT EXISTS incidents_description_trgm_idx ON incidents USING gin (description gin_trgm_ops)');
  }

  public function down(): void
  {
    // Drop trigram GIN indexes
    DB::statement('DROP INDEX IF EXISTS incidents_case_id_trgm_idx');
    DB::statement('DROP INDEX IF EXISTS incidents_reporter_name_trgm_idx');
    DB::statement('DROP INDEX IF EXISTS incidents_reporter_email_trgm_idx');
    DB::statement('DROP INDEX IF EXISTS incidents_description_trgm_idx');

    // Drop BTREE indexes
    Schema::table('incidents', function (Blueprint $table) {
      $table->dropIndex('incidents_reporter_email_idx');
      $table->dropIndex('incidents_incident_type_id_idx');
      $table->dropIndex('incidents_priority_idx');
      $table->dropIndex('incidents_status_idx');
      $table->dropIndex('incidents_reported_at_idx');
    });

    // Keep the extension enabled (safe to leave installed)
  }
};
