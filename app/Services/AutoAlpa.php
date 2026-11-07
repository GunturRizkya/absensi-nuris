<?php

namespace App\Services;

use App\Models\AbsensiHarian;
use App\Models\PengaturanAbsensi;
use App\Models\Siswa;
use App\Models\SuratIzin;
use App\Models\TahunAjaran;
use Carbon\Carbon;

class AutoAlpa
{
    private const HARI = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu',
    ];

    /**
     * Tandai alpa siswa yang belum punya record absensi pada tanggal tsb.
     * Siswa bersurat disetujui langsung berstatus izin/sakit.
     *
     * @return array{dibuat: array<string, int>, pesan: string}
     */
    public static function jalankan(string $tanggal, bool $paksa = false): array
    {
        $kosong = ['dibuat' => [], 'pesan' => ''];

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (! $tahunAktif) {
            $kosong['pesan'] = 'Belum ada tahun ajaran aktif.';

            return $kosong;
        }

        $namaHari = self::HARI[Carbon::parse($tanggal)->format('l')] ?? null;
        $pengaturan = $namaHari
            ? PengaturanAbsensi::where('hari', $namaHari)->where('is_aktif', true)->first()
            : null;

        if (! $pengaturan) {
            $kosong['pesan'] = "Tidak ada jadwal absensi ($namaHari), dilewati.";

            return $kosong;
        }

        if (! $paksa && $tanggal === now()->toDateString() && now()->format('H:i:s') < (string) $pengaturan->jam_tutup_absensi) {
            $kosong['pesan'] = "Jam tutup absensi ({$pengaturan->jam_tutup_absensi}) belum lewat.";

            return $kosong;
        }

        $sudahAda = AbsensiHarian::where('tahun_ajaran_id', $tahunAktif->id)
            ->where('tanggal', $tanggal)
            ->pluck('id', 'siswa_id');

        $bersurat = SuratIzin::where('status_persetujuan', 'disetujui')
            ->where('tanggal_mulai', '<=', $tanggal)
            ->where('tanggal_selesai', '>=', $tanggal)
            ->pluck('jenis', 'siswa_id');

        $dibuat = ['alpa' => 0, 'izin' => 0, 'sakit' => 0];

        Siswa::whereHas('anggotaKelas', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
            ->chunkById(200, function ($daftar) use ($sudahAda, $bersurat, $tahunAktif, $tanggal, &$dibuat) {
                foreach ($daftar as $siswa) {
                    if (isset($sudahAda[$siswa->id])) {
                        continue;
                    }
                    $status = $bersurat[$siswa->id] ?? 'alpa';
                    AbsensiHarian::create([
                        'siswa_id' => $siswa->id,
                        'tahun_ajaran_id' => $tahunAktif->id,
                        'tanggal' => $tanggal,
                        'status' => $status,
                        'keterangan' => 'Otomatis oleh sistem',
                    ]);
                    NotifikasiOrtu::catat($siswa->id, $tanggal, "Ananda {$siswa->nama_lengkap} tercatat {$status} pada {$tanggal} (otomatis).");
                    $dibuat[$status]++;
                }
            });

        $total = array_sum($dibuat);
        $kosong['dibuat'] = $dibuat;
        $kosong['pesan'] = "Selesai: $total record (alpa {$dibuat['alpa']}, izin {$dibuat['izin']}, sakit {$dibuat['sakit']}).";

        return $kosong;
    }
}
