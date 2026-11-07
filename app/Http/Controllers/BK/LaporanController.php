<?php

namespace App\Http\Controllers\BK;

use App\Http\Controllers\Controller;
use App\Models\AbsensiHarian;
use App\Models\AbsensiMapel;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SuratIzin;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    private const STATUS = ['hadir', 'terlambat', 'izin', 'sakit', 'alpa'];

    public function index(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();

        $filter = $request->validate([
            'tahun_ajaran_id' => ['nullable', 'exists:tahun_ajaran,id'],
            'kelas_id' => ['nullable', 'exists:kelas,id'],
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
            'batas_alpa' => ['nullable', 'integer', 'min:1', 'max:31'],
        ]);

        $tahunAjaranId = $filter['tahun_ajaran_id'] ?? $tahunAktif?->id;
        $kelasId = $filter['kelas_id'] ?? null;
        $dari = $filter['dari'] ?? now()->startOfMonth()->toDateString();
        $sampai = $filter['sampai'] ?? now()->toDateString();
        $batasAlpa = $filter['batas_alpa'] ?? 3;

        $counts = ['absensiHarian as total_hadir' => fn ($q) => $q->whereBetween('tanggal', [$dari, $sampai])];
        foreach (self::STATUS as $status) {
            $counts["absensiHarian as {$status}_count"] = fn ($q) => $q
                ->where('status', $status)
                ->whereBetween('tanggal', [$dari, $sampai]);
        }

        $dasar = fn () => Siswa::query()
            ->whereHas('anggotaKelas', fn ($q) => $q
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId)))
            ->with(['anggotaKelas' => fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId), 'anggotaKelas.kelas'])
            ->withCount($counts)
            ->orderBy('nama_lengkap');

        // Agregat dihitung dari seluruh filter; tabelnya dipaginasi.
        $semua = $dasar()->get();

        $rekap = $dasar()->paginate(15)->withQueryString();

        // Siswa perhatian BK: alpa mencapai batas dalam periode.
        $perhatian = $semua->filter(fn ($s) => $s->alpa_count >= $batasAlpa)->values();

        $ringkasan = ['total_siswa' => $semua->count(), 'total_hadir' => $semua->sum('total_hadir')];
        foreach (self::STATUS as $status) {
            $ringkasan[$status] = $semua->sum("{$status}_count");
        }

        $izinPending = SuratIzin::with('siswa')
            ->where('status_persetujuan', 'pending')
            ->when($tahunAjaranId, function ($q) use ($tahunAjaranId, $kelasId) {
                $q->whereHas('siswa.anggotaKelas', fn ($qq) => $qq
                    ->where('tahun_ajaran_id', $tahunAjaranId)
                    ->when($kelasId, fn ($qq) => $qq->where('kelas_id', $kelasId)));
            })
            ->latest()
            ->limit(10)
            ->get();

        return view('bk.laporan.index', [
            'rekap' => $rekap,
            'perhatian' => $perhatian,
            'ringkasan' => $ringkasan,
            'izinPending' => $izinPending,
            'kelas' => Kelas::with('jurusan')->orderBy('nama_kelas')->get(),
            'tahunAjaran' => TahunAjaran::latest()->get(),
            'tahunAktif' => $tahunAktif,
            'tahunAjaranId' => $tahunAjaranId,
            'kelasId' => $kelasId,
            'dari' => $dari,
            'sampai' => $sampai,
            'batasAlpa' => $batasAlpa,
        ]);
    }

    public function show(Request $request, Siswa $siswa)
    {
        $filter = $request->validate([
            'dari' => ['nullable', 'date'],
            'sampai' => ['nullable', 'date', 'after_or_equal:dari'],
        ]);

        $dari = $filter['dari'] ?? now()->subDays(30)->toDateString();
        $sampai = $filter['sampai'] ?? now()->toDateString();

        $siswa->load(['anggotaKelas.kelas', 'anggotaKelas.tahunAjaran']);

        $ringkasan = ['total' => 0];
        foreach (self::STATUS as $status) {
            $ringkasan[$status] = (clone $siswa)->absensiHarian()
                ->where('status', $status)
                ->whereBetween('tanggal', [$dari, $sampai])
                ->count();
            $ringkasan['total'] += $ringkasan[$status];
        }

        $riwayat = AbsensiHarian::where('siswa_id', $siswa->id)
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderByDesc('tanggal')
            ->paginate(31);

        $riwayatMapel = AbsensiMapel::with(['jadwal.mataPelajaran', 'jadwal.guru', 'pencatat'])
            ->where('siswa_id', $siswa->id)
            ->whereBetween('tanggal', [$dari, $sampai])
            ->orderByDesc('tanggal')
            ->limit(20)
            ->get();

        $suratIzin = SuratIzin::with('penyetuju')
            ->where('siswa_id', $siswa->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('bk.laporan.show', compact('siswa', 'ringkasan', 'riwayat', 'riwayatMapel', 'suratIzin', 'dari', 'sampai'));
    }
}
