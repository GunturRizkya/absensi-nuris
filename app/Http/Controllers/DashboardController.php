<?php

namespace App\Http\Controllers;

use App\Models\AbsensiHarian;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SuratIzin;
use App\Models\TahunAjaran;

class DashboardController extends Controller
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

    public function index()
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $hariIni = now()->toDateString();

        $totalSiswa = $tahunAktif
            ? Siswa::whereHas('anggotaKelas', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))->count()
            : Siswa::count();

        $hari = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0];
        if ($tahunAktif) {
            $cacah = AbsensiHarian::where('tahun_ajaran_id', $tahunAktif->id)
                ->where('tanggal', $hariIni)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            foreach ($hari as $status => $nol) {
                $hari[$status] = $cacah[$status] ?? 0;
            }
        }

        $izinPending = SuratIzin::where('status_persetujuan', 'pending')->count();

        // Orang tua: ringkas status hari ini per anak yang tertaut.
        $anak = [];
        if (auth()->user()->role === 'orang_tua') {
            $anak = auth()->user()->anak()
                ->with([
                    'anggotaKelas' => fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif?->id),
                    'anggotaKelas.kelas',
                    'absensiHarian' => fn ($q) => $q->where('tanggal', $hariIni),
                ])
                ->orderBy('nama_lengkap')
                ->get();
        }

        // Guru: jadwal mengajar hari ini.
        $jadwalHariIni = [];
        $guruId = auth()->user()->guru?->id;
        if ($guruId && $tahunAktif) {
            $jadwalHariIni = JadwalPelajaran::with(['kelas', 'mataPelajaran'])
                ->where('guru_id', $guruId)
                ->where('tahun_ajaran_id', $tahunAktif->id)
                ->where('hari', self::HARI[now()->format('l')] ?? '')
                ->orderBy('jam_mulai')
                ->get();
        }

        // Wali kelas: surat menunggu persetujuan di kelas perwaliannya.
        $pendingWali = 0;
        if (auth()->user()->isWaliKelas() && $guruId) {
            $pendingWali = SuratIzin::where('status_persetujuan', 'pending')
                ->whereHas('siswa.anggotaKelas', fn ($q) => $q->whereIn(
                    'kelas_id',
                    Kelas::where('wali_kelas_id', $guruId)->pluck('id')
                ))
                ->count();
        }

        return view('dashboard', compact('tahunAktif', 'totalSiswa', 'hari', 'izinPending', 'hariIni', 'anak', 'jadwalHariIni', 'pendingWali'));
    }
}
