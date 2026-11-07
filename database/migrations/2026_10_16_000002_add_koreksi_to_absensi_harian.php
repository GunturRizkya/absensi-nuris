<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('absensi_harian', function (Blueprint $table) {
            // Jejak siapa terakhir mengoreksi manual record ini.
            $table->foreignId('dikoreksi_oleh')->nullable()->after('keterangan')
                ->constrained('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('absensi_harian', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dikoreksi_oleh');
        });
    }
};
