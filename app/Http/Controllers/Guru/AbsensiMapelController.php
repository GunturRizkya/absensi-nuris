<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\AbsensiMapel;
use App\Models\AnggotaKelas;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiMapelController extends Controller
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

    private const STATUS = ['hadir', 'izin', 'sakit', 'alpa', 'cabut'];

    public function index(Request $request)
    {
        $guruId = $request->user()->guru?->id;

        $filter = $request->validate([
            'tanggal' => 'nullable|date',
        ]);

        $tanggal = $filter['tanggal'] ?? now()->toDateString();
        $namaHari = self::HARI[Carbon::parse($tanggal)->format('l')] ?? '';

        $jadwal = $guruId
            ? JadwalPelajaran::with(['kelas', 'mataPelajaran', 'tahunAjaran'])
                ->where('guru_id', $guruId)
                ->where('hari', $namaHari)
                ->orderBy('jam_mulai')
                ->get()
                ->each(fn ($j) => $j->terisi = AbsensiMapel::where('jadwal_pelajaran_id', $j->id)
                    ->where('tanggal', $tanggal)
                    ->count())
            : collect();

        return view('guru.absensi_mapel.index', compact('jadwal', 'tanggal', 'namaHari', 'guruId'));
    }

    public function form(Request $request, JadwalPelajaran $jadwal)
    {
        $this->cekMilik($request, $jadwal);

        $filter = $request->validate(['tanggal' => 'nullable|date']);
        $tanggal = $filter['tanggal'] ?? now()->toDateString();

        $jadwal->load(['kelas', 'mataPelajaran']);

        $siswa = $jadwal->kelas
            ? AnggotaKelas::with('siswa')
                ->where('kelas_id', $jadwal->kelas_id)
                ->where('tahun_ajaran_id', $jadwal->tahun_ajaran_id)
                ->get()
                ->map(fn ($a) => $a->siswa)
                ->filter()
                ->sortBy('nama_lengkap')
                ->values()
            : collect();

        $terisi = AbsensiMapel::where('jadwal_pelajaran_id', $jadwal->id)
            ->where('tanggal', $tanggal)
            ->get()
            ->keyBy('siswa_id');

        return view('guru.absensi_mapel.form', compact('jadwal', 'tanggal', 'siswa', 'terisi'));
    }

    public function simpan(Request $request, JadwalPelajaran $jadwal)
    {
        $guru = $this->cekMilik($request, $jadwal);

        $data = $request->validate([
            'tanggal' => 'required|date',
            'status' => 'required|array',
            'status.*' => 'required|in:hadir,izin,sakit,alpa,cabut',
            'catatan' => 'nullable|array',
            'catatan.*' => 'nullable|string|max:255',
        ]);

        $boleh = $jadwal->kelas
            ? AnggotaKelas::where('kelas_id', $jadwal->kelas_id)
                ->where('tahun_ajaran_id', $jadwal->tahun_ajaran_id)
                ->pluck('siswa_id')
                ->all()
            : [];

        $tersimpan = 0;
        foreach ($data['status'] as $siswaId => $status) {
            if (! in_array((int) $siswaId, $boleh, true)) {
                continue;
            }
            AbsensiMapel::updateOrCreate(
                ['jadwal_pelajaran_id' => $jadwal->id, 'siswa_id' => $siswaId, 'tanggal' => $data['tanggal']],
                [
                    'status' => $status,
                    'catatan' => $data['catatan'][$siswaId] ?? null,
                    'dicatat_oleh' => $guru?->id,
                    'metode' => 'manual',
                    'jam_absen' => now()->format('H:i:s'),
                ]
            );
            $tersimpan++;
        }

        return redirect()
            ->route('guru.absensi-mapel.index', ['tanggal' => $data['tanggal']])
            ->with('success', "Absensi tersimpan untuk $tersimpan siswa.");
    }

    /**
     * Jadwal hanya boleh diisi guru pengampunya (admin bebas).
     *
     * @return Guru|null
     */
    private function cekMilik(Request $request, JadwalPelajaran $jadwal)
    {
        $user = $request->user();
        $guru = $user->guru;

        if ($user->role !== 'admin' && (! $guru || $jadwal->guru_id !== $guru->id)) {
            abort(403, 'Bukan jadwal mengajar Anda.');
        }

        return $guru;
    }
}
