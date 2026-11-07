<?php

namespace App\Services;

use App\Jobs\KirimNotifikasiWhatsApp;
use App\Models\NotifikasiKehadiran;
use App\Models\Siswa;

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

    /**
     * Notifikasi WhatsApp saat siswa absen masuk.
     */
    public static function waMasuk(Siswa $siswa, string $status, string $jam): void
    {
        $siswa->loadMissing(['anggotaKelas.kelas']);

        $label = match ($status) {
            'terlambat' => 'TERLAMBAT',
            'izin' => 'IZIN',
            'sakit' => 'SAKIT',
            default => 'HADIR',
        };

        $pesan = "*ABSENSI MASUK*\n"
            ."Ananda {$siswa->nama_lengkap}".self::kelas($siswa)."\n"
            ."Status: {$label}\n"
            ."Pukul: {$jam}\n"
            .'Tanggal: '.now()->format('d/m/Y')."\n\n"
            .'Pesan otomatis dari sekolah, tidak perlu dibalas.';

        self::antreWa($siswa, 'masuk', $pesan);
    }

    /**
     * Notifikasi WhatsApp saat siswa absen pulang.
     */
    public static function waPulang(Siswa $siswa, string $jam): void
    {
        $siswa->loadMissing(['anggotaKelas.kelas']);

        $pesan = "*ABSENSI PULANG*\n"
            ."Ananda {$siswa->nama_lengkap}".self::kelas($siswa)."\n"
            ."Pukul: {$jam}\n"
            .'Tanggal: '.now()->format('d/m/Y')."\n\n"
            .'Pesan otomatis dari sekolah, tidak perlu dibalas.';

        self::antreWa($siswa, 'pulang', $pesan);
    }

    /**
     * Antre pengiriman WA agar berjalan SETELAH response (scan tetap cepat).
     */
    private static function antreWa(Siswa $siswa, string $jenis, string $pesan): void
    {
        $nomor = WhatsAppNotifikasi::normalisasiNomor($siswa->no_hp_ortu);

        if ($nomor === null) {
            return;
        }

        KirimNotifikasiWhatsApp::dispatch(
            $siswa->id,
            now()->toDateString(),
            $jenis,
            $nomor,
            $pesan,
        )->afterResponse();
    }

    private static function kelas(Siswa $siswa): string
    {
        $kelas = $siswa->anggotaKelas->first()?->kelas?->nama_kelas;

        return $kelas ? " ({$kelas})" : '';
    }
}
