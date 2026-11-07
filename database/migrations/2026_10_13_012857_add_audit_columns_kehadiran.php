<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Jejak siapa mencatat absensi per mapel dan lewat metode apa (mendukung input manual guru).
        Schema::table('absensi_mapel', function (Blueprint $table) {
            $table->foreignId('dicatat_oleh')->nullable()->after('siswa_id')
                ->constrained('guru')->onDelete('set null');
            $table->enum('metode', ['qr', 'rfid', 'fingerprint', 'manual'])->nullable()->after('status');
            $table->time('jam_absen')->nullable()->after('tanggal');
        });

        // Metode absensi pulang agar simetris dengan metode_masuk.
        Schema::table('absensi_harian', function (Blueprint $table) {
            $table->enum('metode_pulang', ['qr', 'rfid', 'fingerprint', 'manual'])->nullable()->after('metode_masuk');
        });

        // Pelacakan siapa menyetujui/menolak surat izin dan kapan.
        Schema::table('surat_izin', function (Blueprint $table) {
            $table->foreignId('disetujui_oleh')->nullable()->after('status_persetujuan')
                ->constrained('users')->onDelete('set null');
            $table->dateTime('tanggal_persetujuan')->nullable()->after('disetujui_oleh');
        });
    }

    public function down(): void
    {
        Schema::table('surat_izin', function (Blueprint $table) {
            $table->dropConstrainedForeignId('disetujui_oleh');
            $table->dropColumn('tanggal_persetujuan');
        });

        Schema::table('absensi_harian', function (Blueprint $table) {
            $table->dropColumn('metode_pulang');
        });

        Schema::table('absensi_mapel', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dicatat_oleh');
            $table->dropColumn(['metode', 'jam_absen']);
        });
    }
};
