<?php

namespace Database\Seeders;

use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\PengaturanAbsensi;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seeder Tahun Ajaran
        $tahunAjaran = TahunAjaran::create([
            'tahun' => '2025/2026',
            'semester' => '1', // Pakai string '1' (sesuai ENUM di migrasi)
            'is_aktif' => true,
        ]);

        // 2. Seeder Pengaturan Absensi Harian (Jam Masuk, Toleransi, Pulang)
        PengaturanAbsensi::create([
            'hari' => 'Senin', // Atau sesuaikan dengan enum/string di tabelmu (misal: 'Senin' atau '1')
            'jam_masuk' => '07:00:00',
            'toleransi_keterlambatan' => 15, // Dalam menit
            'jam_tutup_absensi' => '08:00:00',
            'jam_pulang' => '15:00:00',
            'durasi_absensi_pulang' => 60, // Dalam menit (misal interval absen pulang)
            'is_aktif' => true,
        ]);

        // 3. Seeder Jurusan SMK
        $rpl = Jurusan::create([
            'kode_jurusan' => 'RPL',
            'nama_jurusan' => 'Rekayasa Perangkat Lunak',
        ]);

        $tkj = Jurusan::create([
            'kode_jurusan' => 'TKJ',
            'nama_jurusan' => 'Teknik Komputer dan Jaringan',
        ]);

        // 4. Seeder Kelas
        Kelas::create([
            'jurusan_id' => $rpl->id,
            'nama_kelas' => 'XII RPL 1',
            'tingkat' => 'XII', // Sesuaikan dengan enum ('X', 'XI', 'XII', 'XIII')
        ]);
        
        Kelas::create([
            'jurusan_id' => $tkj->id,
            'nama_kelas' => 'XII TKJ 1',
            'tingkat' => 'XII', // Sesuaikan dengan enum ('X', 'XI', 'XII', 'XIII')
        ]);
    }
}