<?php

namespace App\Services;

use App\Models\NotifikasiKehadiran;

class NotifikasiOrtu
{
    /**
     * Catat notifikasi aplikasi untuk orang tua (satu baris per siswa+tanggal,
     * pesan selalu mencerminkan kejadian terakhir hari itu).
     */
    public static function catat(int $siswaId, string $tanggal, string $pesan): void
    {
        NotifikasiKehadiran::updateOrCreate(
            ['siswa_id' => $siswaId, 'tanggal' => $tanggal, 'kanal' => 'aplikasi'],
            ['status' => 'terkirim', 'pesan' => $pesan]
        );
    }
}
