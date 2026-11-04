<?php

namespace App\Http\Controllers\WaliKelas;

use App\Http\Controllers\Controller;
use App\Models\AbsensiHarian;
use App\Models\Kelas;
use App\Models\SuratIzin;
use App\Models\TahunAjaran;
use App\Services\NotifikasiOrtu;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class SuratIzinController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isAdmin = $user->role === 'admin';
        $guruId = $user->guru?->id;

        $filter = $request->validate([
            'status' => 'nullable|in:pending,disetujui,ditolak',
            'kelas_id' => 'nullable|exists:kelas,id',
        ]);

        $status = $filter['status'] ?? 'pending';
        $kelasId = $filter['kelas_id'] ?? null;

        $kelasOptions = $isAdmin
            ? Kelas::orderBy('nama_kelas')->get()
            : Kelas::where('wali_kelas_id', $guruId)->orderBy('nama_kelas')->get();

        $surat = SuratIzin::with(['siswa', 'siswa.anggotaKelas.kelas', 'penyetuju'])
            ->when(! $isAdmin, fn ($q) => $q->whereHas(
                'siswa.anggotaKelas',
                fn ($qq) => $qq->whereIn('kelas_id', $kelasOptions->pluck('id'))
            ))
            ->when($status, fn ($q) => $q->where('status_persetujuan', $status))
            ->when($kelasId, fn ($q) => $q->whereHas(
                'siswa.anggotaKelas',
                fn ($qq) => $qq->where('kelas_id', $kelasId)
            ))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $jumlahPending = SuratIzin::where('status_persetujuan', 'pending')
            ->when(! $isAdmin, fn ($q) => $q->whereHas(
                'siswa.anggotaKelas',
                fn ($qq) => $qq->whereIn('kelas_id', $kelasOptions->pluck('id'))
            ))
            ->count();

        return view('walikelas.surat_izin.index', compact('surat', 'kelasOptions', 'jumlahPending', 'status', 'kelasId'));
    }

    public function setujui(Request $request, $id)
    {
        $izin = $this->milikWali($request, $id);

        $izin->update([
            'status_persetujuan' => 'disetujui',
            'disetujui_oleh' => $request->user()->id,
            'tanggal_persetujuan' => now(),
        ]);

        $diperbarui = $this->terapkanKeAbsensi($izin);

        $izin->loadMissing('siswa');
        foreach (CarbonPeriod::create($izin->tanggal_mulai, $izin->tanggal_selesai) as $tanggal) {
            NotifikasiOrtu::catat(
                $izin->siswa_id,
                $tanggal->toDateString(),
                "Surat {$izin->jenis} Ananda {$izin->siswa->nama_lengkap} ({$izin->tanggal_mulai} s/d {$izin->tanggal_selesai}) disetujui."
            );
        }

        return redirect()->back()->with(
            'success',
            "Surat izin disetujui. $diperbarui hari absensi diperbarui menjadi {$izin->jenis}."
        );
    }

    public function tolak(Request $request, $id)
    {
        $izin = $this->milikWali($request, $id);

        $izin->update([
            'status_persetujuan' => 'ditolak',
            'disetujui_oleh' => $request->user()->id,
            'tanggal_persetujuan' => now(),
        ]);

        return redirect()->back()->with('success', 'Surat izin ditolak.');
    }

    /**
     * Wali hanya boleh memproses surat dari kelas perwaliannya (admin bebas).
     */
    private function milikWali(Request $request, $id): SuratIzin
    {
        $user = $request->user();
        $izin = SuratIzin::findOrFail($id);

        if ($user->role !== 'admin') {
            $boleh = $izin->siswa->anggotaKelas()
                ->whereIn('kelas_id', Kelas::where('wali_kelas_id', $user->guru?->id)->pluck('id'))
                ->exists();

            abort_unless($boleh, 403, 'Bukan siswa perwalian Anda.');
        }

        return $izin;
    }

    /**
     * Efek persetujuan: isi tanggal kosong dengan status izin/sakit,
     * naikkan status alpa. Kehadiran yang sudah tercatat tidak diubah.
     */
    private function terapkanKeAbsensi(SuratIzin $izin): int
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (! $tahunAktif) {
            return 0;
        }

        $diperbarui = 0;

        foreach (CarbonPeriod::create($izin->tanggal_mulai, $izin->tanggal_selesai) as $tanggal) {
            $absen = AbsensiHarian::firstOrNew([
                'siswa_id' => $izin->siswa_id,
                'tahun_ajaran_id' => $tahunAktif->id,
                'tanggal' => $tanggal->toDateString(),
            ]);

            if (! $absen->exists) {
                $absen->status = $izin->jenis;
                $absen->keterangan = 'Surat izin disetujui';
                $absen->save();
                $diperbarui++;
            } elseif ($absen->status === 'alpa') {
                $absen->status = $izin->jenis;
                $absen->keterangan = 'Surat izin disetujui';
                $absen->save();
                $diperbarui++;
            }
        }

        return $diperbarui;
    }
}
