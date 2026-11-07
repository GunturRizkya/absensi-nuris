<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Relasi akun orang tua (users.role = orang_tua) ke siswa agar bisa login & menerima notifikasi.
        Schema::create('orang_tua_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->string('hubungan', 20)->nullable(); // ayah / ibu / wali
            $table->timestamps();

            $table->unique(['user_id', 'siswa_id'], 'orang_tua_siswa_user_siswa_unique');
        });

        // Log notifikasi kehadiran ke orang tua (anti kirim ganda + bisa diaudit).
        Schema::create('notifikasi_kehadiran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('kanal', ['wa', 'sms', 'aplikasi'])->default('wa');
            $table->enum('status', ['pending', 'terkirim', 'gagal'])->default('pending');
            $table->text('pesan')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'tanggal', 'kanal'], 'notifikasi_siswa_tanggal_kanal_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi_kehadiran');
        Schema::dropIfExists('orang_tua_siswa');
    }
};
