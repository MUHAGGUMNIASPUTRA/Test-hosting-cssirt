<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->text('nip')->nullable()->change();
            $table->text('nik')->nullable()->change();
            $table->text('phone')->nullable()->change();
            $table->text('email')->nullable()->change();
        });

        DB::table('employees')->get()->each(function ($employee) {
            DB::table('employees')->where('id', $employee->id)->update([
                'nip' => $employee->nip ? Crypt::encryptString($employee->nip) : null,
                'nik' => $employee->nik ? Crypt::encryptString($employee->nik) : null,
                'phone' => $employee->phone ? Crypt::encryptString($employee->phone) : null,
                'email' => $employee->email ? Crypt::encryptString($employee->email) : null,
            ]);
        });
    }

    public function down(): void
    {
        DB::table('employees')->get()->each(function ($employee) {
            DB::table('employees')->where('id', $employee->id)->update([
                'nip' => $employee->nip ? Crypt::decryptString($employee->nip) : null,
                'nik' => $employee->nik ? Crypt::decryptString($employee->nik) : null,
                'phone' => $employee->phone ? Crypt::decryptString($employee->phone) : null,
                'email' => $employee->email ? Crypt::decryptString($employee->email) : null,
            ]);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('nip', 255)->nullable()->change();
            $table->string('nik', 255)->nullable()->change();
            $table->string('phone', 50)->nullable()->change();
            $table->string('email', 255)->nullable()->change();
        });
    }
};
