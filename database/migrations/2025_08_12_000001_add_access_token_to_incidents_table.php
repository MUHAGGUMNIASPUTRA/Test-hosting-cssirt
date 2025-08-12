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
      $table->string('access_token', 128)->nullable()->unique()->after('case_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('incidents', function (Blueprint $table) {
      $table->dropUnique(['access_token']);
      $table->dropColumn('access_token');
    });
  }
};
