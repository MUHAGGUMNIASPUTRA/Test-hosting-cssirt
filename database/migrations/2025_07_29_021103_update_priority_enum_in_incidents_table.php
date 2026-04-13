<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // For PostgreSQL, modifying an ENUM (which is a CHECK constraint in Laravel)
        // is safer with raw SQL statements because the ->change() method can cause syntax errors.
        // The default constraint name follows the pattern: {table}_{column}_check
        DB::statement('ALTER TABLE incidents DROP CONSTRAINT incidents_priority_check');
        DB::statement("ALTER TABLE incidents ADD CONSTRAINT incidents_priority_check CHECK (priority IN ('Rendah', 'Sedang', 'Tinggi', 'Kritikal'))");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to the original check constraint
        DB::statement('ALTER TABLE incidents DROP CONSTRAINT incidents_priority_check');
        DB::statement("ALTER TABLE incidents ADD CONSTRAINT incidents_priority_check CHECK (priority IN ('Rendah', 'Sedang', 'Tinggi', 'Kritis'))");
    }
};
