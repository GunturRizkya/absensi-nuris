<?php

namespace App\Http\Controllers\Piket;

use App\Http\Controllers\Controller;
use App\Models\AbsensiHarian;
use App\Models\PengaturanAbsensi;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Services\NotifikasiOrtu;
use App\Services\StatistikAbsensiHarian;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    /**
     * Scan ganda dalam jeda ini dianggap duplikat (bukan pulang).
     * Pulang manual operator selalu dicatat tanpa jeda.
     */
    private const JEDA_PULANG_MENIT = 10;

    public function __construct(
        private readonly StatistikAbsensiHarian $statistikAbsensi,
    ) {}

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
        $statistik = $this->statistikAbsensi->hitung($tahunAktif, $hariIni);

        $daftarSiswa = $tahunAktif
            ? Siswa::whereHas('anggotaKelas', fn ($q) => $q->where('tahun_ajaran_id', $tahunAktif->id))
                ->with(['anggotaKelas.kelas'])
                ->orderBy('nama_lengkap')
                ->get()
            : collect();

        return view('piket.absensi.index', compact('tahunAktif', 'hariIni', 'statistik', 'daftarSiswa'));
    }

    public function scan(Request $request)
    {
        $data = $request->validate([
            'kode' => 'required|string|max:255',
            'metode' => 'required|in:qr,rfid,fingerprint',
        ]);

        // Satu kolom cocokkan QR, kartu RFID, atau NISN.
        $siswa = Siswa::where('qr_code', $data['kode'])
            ->orWhere('card_number', $data['kode'])
            ->orWhere('nisn', $data['kode'])
            ->first();

        if (! $siswa) {
            return redirect()->back()->with('error', 'Kode tidak dikenali. Cek kartu/QR siswa.');
        }

        [$ok, $pesan, $hasil] = $this->catat($siswa, $data['metode'], null);

        // Kamera (fetch) minta JSON agar halaman tidak reload per scan.
        if ($request->wantsJson()) {
            return response()->json([
                'ok' => $ok,
                'hasil' => $hasil,
                'error' => $ok ? null : $pesan,
                'statistik' => $this->statistikAbsensi->hitung(
                    TahunAjaran::where('is_aktif', true)->first(),
                    now()->toDateString()
                ),
            ]);
        }

        return redirect()->back()->with($ok ? 'hasil' : 'error', $ok ? $hasil : $pesan);
    }

    public function manual(Request $request)
    {
        // OSIS hanya boleh scan; input manual khusus piket/admin.
        abort_if($request->user()->role === 'osis', 403, 'Hanya piket yang boleh input manual.');

        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'tipe' => 'required|in:masuk,pulang',
            'keterangan' => 'nullable|string',
        ]);

        [$ok, $pesan, $hasil] = $this->catat(Siswa::findOrFail($data['siswa_id']), 'manual', $data['keterangan'], $data['tipe']);

        return redirect()->back()->with($ok ? 'hasil' : 'error', $ok ? $hasil : $pesan);
    }

    /**
     * Umpan JSON untuk live feed (dipolling halaman gerbang tiap 10 detik).
     */
    public function feed(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();

        $log = $tahunAktif
            ? AbsensiHarian::with(['siswa', 'siswa.anggotaKelas.kelas'])
                ->where('tahun_ajaran_id', $tahunAktif->id)
                ->where('tanggal', now()->toDateString())
                ->latest('updated_at')
                ->limit(12)
                ->get()
                ->map(fn ($a) => [
                    'nama' => $a->siswa->nama_lengkap,
                    'kelas' => $a->siswa->anggotaKelas->first()?->kelas?->nama_kelas ?? '-',
                    'status' => $a->status,
                    'jam' => substr($a->jam_pulang ?: $a->jam_masuk ?: '', 0, 5),
                    'foto' => $a->siswa->foto ? asset($a->siswa->foto) : null,
                ])
            : [];

        return response()->json(['log' => $log]);
    }

    /**
     * @return array{bool, string, ?array}
     */
    private function catat(Siswa $siswa, string $metode, ?string $keterangan, ?string $tipePaksa = null): array
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        if (! $tahunAktif) {
            return [false, 'Belum ada tahun ajaran aktif.', null];
        }

        $hariIni = now()->toDateString();
        $namaHari = self::HARI[now()->format('l')] ?? null;

        $pengaturan = $namaHari
            ? PengaturanAbsensi::where('hari', $namaHari)->where('is_aktif', true)->first()
            : null;

        if (! $pengaturan) {
            return [false, "Tidak ada jadwal absensi hari ini ($namaHari).", null];
        }

        $absen = AbsensiHarian::firstOrNew([
            'siswa_id' => $siswa->id,
            'tahun_ajaran_id' => $tahunAktif->id,
            'tanggal' => $hariIni,
        ]);

        $sekarang = now()->format('H:i:s');
        $batasTepatWaktu = $pengaturan->jam_masuk instanceof \DateTimeInterface
            ? $pengaturan->jam_masuk->format('H:i:s')
            : (string) $pengaturan->jam_masuk;

        // Mode pulang: record masuk sudah ada dan jam pulang masih kosong.
        // Scan otomatis butuh jeda sejak jam masuk agar scan ganda tidak tercatat pulang.
        $cukupJeda = $absen->exists && ! empty($absen->jam_masuk)
            && Carbon::parse("{$hariIni} {$absen->jam_masuk}")->diffInMinutes(now()) >= self::JEDA_PULANG_MENIT;

        $mauPulang = $tipePaksa
            ? $tipePaksa === 'pulang'
            : ($absen->exists && empty($absen->jam_pulang) && $cukupJeda);

        if ($mauPulang) {
            if (! $absen->exists) {
                return [false, "Data masuk {$siswa->nama_lengkap} hari ini belum ada.", null];
            }

            $absen->jam_pulang = $sekarang;
            $absen->metode_pulang = $metode;
            if ($keterangan) {
                $absen->keterangan = $keterangan;
            }
            $absen->save();

            NotifikasiOrtu::catat($siswa->id, $hariIni, "Ananda {$siswa->nama_lengkap} pulang pukul ".substr($sekarang, 0, 5).'.');

            return [true, '', $this->hasil($siswa, 'pulang', $absen->status, $sekarang)];
        }

        // Mode masuk.
        if ($absen->exists && ! empty($absen->jam_masuk)) {
            return [true, '', $this->hasil($siswa, 'masuk (sudah tercatat)', $absen->status, $absen->jam_masuk)];
        }

        if ($sekarang > (string) $pengaturan->jam_tutup_absensi) {
            return [false, "Absensi masuk sudah ditutup pukul {$pengaturan->jam_tutup_absensi}.", null];
        }

        $batas = Carbon::parse($batasTepatWaktu)->addMinutes($pengaturan->toleransi_keterlambatan)->format('H:i:s');
        $status = $sekarang <= $batas ? 'hadir' : 'terlambat';

        $absen->jam_masuk = $sekarang;
        $absen->status = $status;
        $absen->metode_masuk = $metode;
        $absen->keterangan = $keterangan;
        $absen->save();

        NotifikasiOrtu::catat($siswa->id, $hariIni, "Ananda {$siswa->nama_lengkap} tercatat {$status} pukul ".substr($sekarang, 0, 5).'.');

        return [true, '', $this->hasil($siswa, 'masuk', $status, $sekarang)];
    }

    private function hasil(Siswa $siswa, string $tipe, string $status, string $jam): array
    {
        $siswa->loadMissing(['anggotaKelas.kelas']);

        return [
            'nama' => $siswa->nama_lengkap,
            'nisn' => $siswa->nisn,
            'kelas' => $siswa->anggotaKelas->first()?->kelas?->nama_kelas ?? '-',
            'tipe' => $tipe,
            'status' => $status,
            'jam' => substr($jam, 0, 5),
            'foto' => $siswa->foto ? asset($siswa->foto) : null,
        ];
    }
}
