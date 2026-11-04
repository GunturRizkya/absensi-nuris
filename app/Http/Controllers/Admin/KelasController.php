<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelas;
use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->validate([
            'q' => 'nullable|string|max:100',
            'jurusan_id' => 'nullable|exists:jurusan,id',
        ]);

        $q = $filter['q'] ?? null;
        $jurusanId = $filter['jurusan_id'] ?? null;

        $kelas = Kelas::with(['jurusan', 'waliKelas'])
            ->when($q, fn ($query) => $query->where('nama_kelas', 'like', "%$q%"))
            ->when($jurusanId, fn ($query) => $query->where('jurusan_id', $jurusanId))
            ->latest()
            ->paginate(12)
            ->withQueryString();
        $jurusan = Jurusan::all();
        $guru = Guru::orderBy('nama_lengkap')->get();

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $jumlahSiswa = AnggotaKelas::when($tahunAktif, fn ($query) => $query->where('tahun_ajaran_id', $tahunAktif->id))
            ->selectRaw('kelas_id, COUNT(*) as total')
            ->groupBy('kelas_id')
            ->pluck('total', 'kelas_id');

        return view('admin.kelas.index', compact('kelas', 'jurusan', 'guru', 'q', 'jurusanId', 'jumlahSiswa', 'tahunAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jurusan_id' => 'required|exists:jurusan,id',
            'nama_kelas' => 'required|string|max:50',
            'tingkat' => 'required|in:X,XI,XII',
            'wali_kelas_id' => 'nullable|exists:guru,id',
        ]);

        Kelas::create($request->all());

        return redirect()->back()->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $kelas = Kelas::findOrFail($id);

        $request->validate([
            'jurusan_id' => 'required|exists:jurusan,id',
            'nama_kelas' => 'required|string|max:50',
            'tingkat' => 'required|in:X,XI,XII',
            'wali_kelas_id' => 'nullable|exists:guru,id',
        ]);

        $kelas->update($request->all());

        return redirect()->back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);
        $kelas->delete();

        return redirect()->back()->with('success', 'Data kelas berhasil dihapus.');
    }
}
