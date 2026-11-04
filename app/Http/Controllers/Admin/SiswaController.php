<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbsensiHarian;
use App\Models\AbsensiMapel;
use App\Models\AnggotaKelas;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\SuratIzin;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $tahunAjaranId = $request->get('tahun_ajaran_id', $tahunAktif?->id);

        $filter = $request->validate([
            'tahun_ajaran_id' => 'nullable|exists:tahun_ajaran,id',
            'q' => 'nullable|string|max:100',
            'kelas_id' => 'nullable|exists:kelas,id',
            'tingkat' => 'nullable|in:X,XI,XII',
        ]);

        $tahunAjaranId = $filter['tahun_ajaran_id'] ?? $tahunAjaranId;
        $q = $filter['q'] ?? null;
        $kelasId = $filter['kelas_id'] ?? null;
        $tingkat = $filter['tingkat'] ?? null;

        $cakupan = fn ($query) => $query
            ->when($tahunAjaranId, fn ($q2) => $q2->where('tahun_ajaran_id', $tahunAjaranId))
            ->when($kelasId, fn ($q2) => $q2->where('kelas_id', $kelasId));

        $siswa = Siswa::with(['anggotaKelas' => fn ($q2) => $q2->where('tahun_ajaran_id', $tahunAjaranId), 'anggotaKelas.kelas'])
            ->when($tahunAjaranId || $kelasId, fn ($query) => $query->whereHas('anggotaKelas', $cakupan))
            ->when($tingkat, fn ($query) => $query->whereHas('anggotaKelas.kelas', fn ($q2) => $q2->where('tingkat', $tingkat)))
            ->when($q, fn ($query) => $query->where(
                fn ($w) => $w->where('nama_lengkap', 'like', "%$q%")->orWhere('nisn', 'like', "%$q%")
            ))
            ->orderBy('nama_lengkap')
            ->paginate(15)
            ->withQueryString();

        // Agregat kartu statistik (seluruh cakupan tahun ajaran).
        $dasarStat = Siswa::whereHas('anggotaKelas', fn ($query) => $query->where('tahun_ajaran_id', $tahunAjaranId));
        $stat = [
            'total' => (clone $dasarStat)->count(),
            'foto' => (clone $dasarStat)->whereNotNull('foto')->count(),
            'laki' => (clone $dasarStat)->where('jenis_kelamin', 'L')->count(),
            'perempuan' => (clone $dasarStat)->where('jenis_kelamin', 'P')->count(),
        ];
        $stat['tanpa_foto'] = $stat['total'] - $stat['foto'];

        return view('admin.siswa.index', [
            'siswa' => $siswa,
            'kelas' => Kelas::orderBy('nama_kelas')->get(),
            'tahunAjaran' => TahunAjaran::latest()->get(),
            'tahunAjaranId' => $tahunAjaranId,
            'q' => $q,
            'kelasId' => $kelasId,
            'tingkat' => $tingkat,
            'stat' => $stat,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nisn' => 'required|string|max:20|unique:siswa,nisn',
            'nama_lengkap' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp_ortu' => 'nullable|string|max:20',
            'qr_code' => 'nullable|string|max:255|unique:siswa,qr_code',
            'card_number' => 'nullable|string|max:255|unique:siswa,card_number',
            'fingerprint_id' => 'nullable|string|max:255|unique:siswa,fingerprint_id',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        // Default QR = NISN bila dikosongkan.
        $data['qr_code'] = ($data['qr_code'] ?? null) ?: $data['nisn'];
        $data['foto'] = $this->simpanFoto($request, null);

        $siswa = Siswa::create($data);

        AnggotaKelas::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => $data['kelas_id'],
            'tahun_ajaran_id' => $data['tahun_ajaran_id'],
        ]);

        return redirect()->back()->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $data = $request->validate([
            'nisn' => ['required', 'string', 'max:20', Rule::unique('siswa', 'nisn')->ignore($siswa->id)],
            'nama_lengkap' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'no_hp_ortu' => 'nullable|string|max:20',
            'qr_code' => ['nullable', 'string', 'max:255', Rule::unique('siswa', 'qr_code')->ignore($siswa->id)],
            'card_number' => ['nullable', 'string', 'max:255', Rule::unique('siswa', 'card_number')->ignore($siswa->id)],
            'fingerprint_id' => ['nullable', 'string', 'max:255', Rule::unique('siswa', 'fingerprint_id')->ignore($siswa->id)],
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'kelas_id' => 'required|exists:kelas,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
        ]);

        $data['qr_code'] = ($data['qr_code'] ?? null) ?: $data['nisn'];
        $data['foto'] = $this->simpanFoto($request, $siswa) ?? $siswa->foto;

        $siswa->update($data);

        AnggotaKelas::updateOrCreate(
            ['siswa_id' => $siswa->id, 'tahun_ajaran_id' => $data['tahun_ajaran_id']],
            ['kelas_id' => $data['kelas_id']]
        );

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $this->hapusFoto($siswa->foto);
        $siswa->delete();

        return redirect()->back()->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Kartu pelajar siap cetak (depan: biodata+foto, belakang: QR).
     */
    public function kartu($id)
    {
        $siswa = Siswa::with(['anggotaKelas.kelas'])->findOrFail($id);

        return view('admin.siswa.kartu', compact('siswa'));
    }

    public function show(Request $request, $id)
    {
        $siswa = Siswa::with(['anggotaKelas.kelas', 'anggotaKelas.tahunAjaran'])->findOrFail($id);

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $tahunAjaranId = $request->get('tahun_ajaran_id', $tahunAktif?->id);

        $ringkasan = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0, 'total' => 0];
        if ($tahunAjaranId) {
            $cacah = AbsensiHarian::where('siswa_id', $siswa->id)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            foreach (array_keys($ringkasan) as $status) {
                if ($status !== 'total') {
                    $ringkasan[$status] = $cacah[$status] ?? 0;
                    $ringkasan['total'] += $ringkasan[$status];
                }
            }
        }

        $riwayat = AbsensiHarian::where('siswa_id', $siswa->id)
            ->when($tahunAjaranId, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->orderByDesc('tanggal')
            ->paginate(15)
            ->withQueryString();

        $rekapBulanan = [];
        if ($tahunAjaranId) {
            $rekapBulanan = AbsensiHarian::where('siswa_id', $siswa->id)
                ->where('tahun_ajaran_id', $tahunAjaranId)
                ->selectRaw("DATE_FORMAT(tanggal, '%Y-%m') as bulan, status, COUNT(*) as total")
                ->groupBy('bulan', 'status')
                ->orderBy('bulan')
                ->get()
                ->groupBy('bulan');
        }

        $riwayatMapel = AbsensiMapel::with(['jadwal.mataPelajaran', 'jadwal.guru'])
            ->where('siswa_id', $siswa->id)
            ->orderByDesc('tanggal')
            ->limit(10)
            ->get();

        $suratIzin = SuratIzin::with('penyetuju')
            ->where('siswa_id', $siswa->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.siswa.show', compact(
            'siswa', 'ringkasan', 'riwayat', 'riwayatMapel', 'suratIzin',
            'tahunAktif', 'tahunAjaranId', 'rekapBulanan'
        ));
    }

    /**
     * Simpan upload foto ke public/foto-siswa (tanpa symlink storage).
     * Kembalikan path baru, atau null bila tidak ada upload.
     */
    private function simpanFoto(Request $request, ?Siswa $lama): ?string
    {
        if (! $request->hasFile('foto')) {
            return null;
        }

        $file = $request->file('foto');
        $nama = now()->format('YmdHis').'_'.bin2hex(random_bytes(4)).'.'.$file->getClientOriginalExtension();
        $file->move(public_path('foto-siswa'), $nama);

        if ($lama?->foto) {
            $this->hapusFoto($lama->foto);
        }

        return 'foto-siswa/'.$nama;
    }

    private function hapusFoto(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
