<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\AbsensiHarian;
use App\Models\Siswa;
use App\Models\TahunAjaran;

/**
 * Menghitung ringkasan kehadiran harian untuk satu tahun ajaran.
 */
class StatistikAbsensiHarian
{
    /**
     * @return array{hadir:int, terlambat:int, izin:int, sakit:int, alpa:int, total_siswa:int, sudah_absen:int}
     */
    public function hitung(?TahunAjaran $tahunAktif, string $tanggal): array
    {
        $ringkasan = [
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'total_siswa' => 0,
            'sudah_absen' => 0,
        ];

        if (! $tahunAktif) {
            return $ringkasan;
        }

        $ringkasan['total_siswa'] = Siswa::whereHas(
            'anggotaKelas',
            fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id)
        )->count();

        $cacah = AbsensiHarian::query()
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->where('tanggal', $tanggal)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        foreach (['hadir', 'terlambat', 'izin', 'sakit', 'alpa'] as $status) {
            $ringkasan[$status] = $cacah[$status] ?? 0;
        }

        $ringkasan['sudah_absen'] = AbsensiHarian::query()
            ->where('tahun_ajaran_id', $tahunAktif->id)
            ->where('tanggal', $tanggal)
            ->distinct('siswa_id')
            ->count('siswa_id');

        return $ringkasan;
    }
}
