<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notifikasi_kehadiran', function (Blueprint $table) {
            $table->dateTime('dibaca_pada')->nullable()->after('pesan');
        });
    }

    public function down(): void
    {
        Schema::table('notifikasi_kehadiran', function (Blueprint $table) {
            $table->dropColumn('dibaca_pada');
        });
    }
};
