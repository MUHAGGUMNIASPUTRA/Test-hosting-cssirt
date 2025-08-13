<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
  // Needed for CREATE INDEX CONCURRENTLY
  public $withinTransaction = false;

  public function up(): void
  {
    // Enable pg_trgm (safe if already installed)
    DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');

    // BTREE indexes (filters/sorts) - idempotent and non-blocking
    DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS incidents_reporter_email_idx   ON incidents (reporter_email)');
    DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS incidents_incident_type_id_idx ON incidents (incident_type_id)');
    DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS incidents_priority_idx         ON incidents (priority)');
    DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS incidents_status_idx           ON incidents (status)');
    DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS incidents_reported_at_idx      ON incidents (reported_at)');

    // Trigram GIN indexes for ILIKE searches
    DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS incidents_case_id_trgm_idx         ON incidents USING gin (case_id gin_trgm_ops)');
    DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS incidents_reporter_name_trgm_idx   ON incidents USING gin (reporter_name gin_trgm_ops)');
    DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS incidents_reporter_email_trgm_idx  ON incidents USING gin (reporter_email gin_trgm_ops)');
    DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS incidents_description_trgm_idx     ON incidents USING gin (description gin_trgm_ops)');
  }

  public function down(): void
  {
    // Drop GIN indexes
    DB::statement('DROP INDEX CONCURRENTLY IF EXISTS incidents_case_id_trgm_idx');
    DB::statement('DROP INDEX CONCURRENTLY IF EXISTS incidents_reporter_name_trgm_idx');
    DB::statement('DROP INDEX CONCURRENTLY IF EXISTS incidents_reporter_email_trgm_idx');
    DB::statement('DROP INDEX CONCURRENTLY IF EXISTS incidents_description_trgm_idx');

    // Drop BTREE indexes
    DB::statement('DROP INDEX CONCURRENTLY IF EXISTS incidents_reporter_email_idx');
    DB::statement('DROP INDEX CONCURRENTLY IF EXISTS incidents_incident_type_id_idx');
    DB::statement('DROP INDEX CONCURRENTLY IF EXISTS incidents_priority_idx');
    DB::statement('DROP INDEX CONCURRENTLY IF EXISTS incidents_status_idx');
    DB::statement('DROP INDEX CONCURRENTLY IF EXISTS incidents_reported_at_idx');
  }
};
