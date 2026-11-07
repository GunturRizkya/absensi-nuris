<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Satu siswa hanya punya satu baris absensi harian per tanggal (anti double-scan).
        Schema::table('absensi_harian', function (Blueprint $table) {
            $table->unique(['siswa_id', 'tanggal'], 'absensi_harian_siswa_tanggal_unique');
        });

        // Satu siswa hanya punya satu baris absensi per jadwal per tanggal.
        Schema::table('absensi_mapel', function (Blueprint $table) {
            $table->unique(['jadwal_pelajaran_id', 'siswa_id', 'tanggal'], 'absensi_mapel_jadwal_siswa_tanggal_unique');
        });

        // Satu siswa hanya terdaftar di satu kelas per tahun ajaran.
        Schema::table('anggota_kelas', function (Blueprint $table) {
            $table->unique(['siswa_id', 'tahun_ajaran_id'], 'anggota_kelas_siswa_tahun_unique');
        });

        // ID sidik jari harus unik antar siswa (NULL tetap boleh ganda di MySQL).
        Schema::table('siswa', function (Blueprint $table) {
            $table->unique('fingerprint_id', 'siswa_fingerprint_unique');
        });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropUnique('siswa_fingerprint_unique');
        });

        Schema::table('anggota_kelas', function (Blueprint $table) {
            $table->dropUnique('anggota_kelas_siswa_tahun_unique');
        });

        Schema::table('absensi_mapel', function (Blueprint $table) {
            $table->dropUnique('absensi_mapel_jadwal_siswa_tanggal_unique');
        });

        Schema::table('absensi_harian', function (Blueprint $table) {
            $table->dropUnique('absensi_harian_siswa_tanggal_unique');
        });
    }
};
