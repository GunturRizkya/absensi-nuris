<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiHarian;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\AutoAlpa;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function harian(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();

        $filter = $request->validate([
            'tanggal' => 'nullable|date',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $tanggal = $filter['tanggal'] ?? now()->toDateString();
        $kelasId = $filter['kelas_id'] ?? null;
        $tahunAjaranId = $tahunAktif?->id;

        $daftar = Siswa::query()
            ->whereHas('anggotaKelas', fn ($q) => $q
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId)))
            ->with([
                'anggotaKelas' => fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId),
                'anggotaKelas.kelas',
                'absensiHarian' => fn ($q) => $q->where('tanggal', $tanggal),
                'absensiHarian.korektor',
            ])
            ->orderBy('nama_lengkap')
            ->paginate(20)
            ->withQueryString();

        $ringkasan = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'belum' => 0];

        // Ringkasan dihitung dari seluruh filter (bukan halaman aktif).
        $semuaStatus = AbsensiHarian::where('tahun_ajaran_id', $tahunAjaranId)
            ->where('tanggal', $tanggal)
            ->whereIn('siswa_id', Siswa::whereHas('anggotaKelas', fn ($q) => $q
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId)))->pluck('id'))
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $totalSiswa = Siswa::whereHas('anggotaKelas', fn ($q) => $q
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId)))->count();

        foreach (['hadir', 'terlambat', 'izin', 'sakit', 'alpa'] as $status) {
            $ringkasan[$status] = $semuaStatus[$status] ?? 0;
        }
        $ringkasan['belum'] = $totalSiswa - array_sum([$ringkasan['hadir'], $ringkasan['terlambat'], $ringkasan['izin'], $ringkasan['sakit'], $ringkasan['alpa']]);

        return view('admin.rekap.harian', [
            'daftar' => $daftar,
            'ringkasan' => $ringkasan,
            'kelas' => Kelas::orderBy('nama_kelas')->get(),
            'tanggal' => $tanggal,
            'kelasId' => $kelasId,
        ]);
    }

    public function bulanan(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();

        $filter = $request->validate([
            'bulan' => 'nullable|date_format:Y-m',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $bulan = $filter['bulan'] ?? now()->format('Y-m');
        $kelasId = $filter['kelas_id'] ?? null;
        $tahunAjaranId = $tahunAktif?->id;

        $awal = Carbon::createFromFormat('Y-m-d', "$bulan-01");
        $daftarTanggal = [];
        for ($d = $awal->copy(); $d->month === $awal->month; $d->addDay()) {
            $daftarTanggal[] = $d->toDateString();
        }

        $daftar = Siswa::query()
            ->whereHas('anggotaKelas', fn ($q) => $q
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId)))
            ->with([
                'anggotaKelas' => fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId),
                'anggotaKelas.kelas',
                'absensiHarian' => fn ($q) => $q->whereBetween('tanggal', [$daftarTanggal[0], end($daftarTanggal)]),
            ])
            ->orderBy('nama_lengkap')
            ->paginate(20)
            ->withQueryString();

        return view('admin.rekap.bulanan', [
            'daftar' => $daftar,
            'daftarTanggal' => $daftarTanggal,
            'kelas' => Kelas::orderBy('nama_kelas')->get(),
            'bulan' => $bulan,
            'kelasId' => $kelasId,
        ]);
    }

    public function autoAlpa(Request $request)
    {
        $data = $request->validate(['tanggal' => 'nullable|date']);

        $hasil = AutoAlpa::jalankan($data['tanggal'] ?? now()->toDateString(), true);

        return redirect()->back()->with('success', $hasil['pesan']);
    }

    /**
     * Koreksi manual status absensi harian (admin & piket).
     * Membuat record bila belum ada; selalu mencatat pengoreksi + alasan.
     */
    public function koreksi(Request $request)
    {
        // Koreksi butuh alasan + nama pengoreksi: hanya admin/piket.
        abort_unless(in_array($request->user()->role, ['admin', 'piket'], true), 403);

        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:hadir,terlambat,izin,sakit,alpa',
            'alasan' => 'required|string|max:255',
        ]);

        $tahunAktif = TahunAjaran::where('is_aktif', true)->firstOrFail();

        $absen = AbsensiHarian::firstOrNew([
            'siswa_id' => $data['siswa_id'],
            'tahun_ajaran_id' => $tahunAktif->id,
            'tanggal' => $data['tanggal'],
        ]);

        $absen->status = $data['status'];
        $absen->keterangan = $data['alasan'];
        $absen->dikoreksi_oleh = $request->user()->id;
        $absen->save();

        $nama = $absen->siswa->nama_lengkap;

        return redirect()->back()->with('success', "Absensi $nama pada {$data['tanggal']} dikoreksi menjadi {$data['status']}.");
    }
}
