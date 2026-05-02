<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('web_app_networks', function (Blueprint $table) {
            $table->foreignUuid('ip_address_id')->nullable()->after('public_ip')
                ->constrained('ip_addresses')->nullOnDelete();
            $table->foreignUuid('subdomain_id')->nullable()->after('ip_address_id')
                ->constrained('subdomains')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('web_app_networks', function (Blueprint $table) {
            $table->dropForeign(['ip_address_id']);
            $table->dropForeign(['subdomain_id']);
            $table->dropColumn(['ip_address_id', 'subdomain_id']);
        });
    }
};
