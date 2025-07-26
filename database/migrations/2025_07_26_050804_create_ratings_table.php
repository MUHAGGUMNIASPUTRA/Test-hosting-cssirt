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
    Schema::create('ratings', function (Blueprint $table) {
      $table->id();
      $table->foreignId('post_id')->constrained()->cascadeOnDelete();
      $table->foreignId('user_id')->nullable()->cascadeOnDelete();
      $table->ipAddress('ip_address')->nullable()->after('user_id');
      $table->tinyInteger('rating'); // Saving vote (1, 2, 3, 4, 5)
      $table->timestamps();

      // Each user can only rate a post once
      $table->unique(['post_id', 'user_id']);
    });

    Schema::table('posts', function (Blueprint $table) {
      $table->unsignedInteger('ratings_count')->default(0)->after('rating');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('ratings');
    Schema::table('posts', function (Blueprint $table) {
      $table->dropColumn('ratings_count');
    });
  }
};
