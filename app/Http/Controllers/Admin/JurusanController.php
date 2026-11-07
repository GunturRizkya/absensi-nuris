<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AnggotaKelas;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class JurusanController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->validate(['q' => 'nullable|string|max:100'])['q'] ?? null;

        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();

        $jurusan = Jurusan::withCount(['kelas', 'mapel'])
            ->when($q, fn ($query) => $query->where(
                fn ($w) => $w->where('kode_jurusan', 'like', "%$q%")->orWhere('nama_jurusan', 'like', "%$q%")
            ))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $jumlahSiswa = AnggotaKelas::join('kelas', 'kelas.id', '=', 'anggota_kelas.kelas_id')
            ->when($tahunAktif, fn ($query) => $query->where('anggota_kelas.tahun_ajaran_id', $tahunAktif->id))
            ->selectRaw('kelas.jurusan_id, COUNT(*) as total')
            ->groupBy('kelas.jurusan_id')
            ->pluck('total', 'jurusan_id');

        return view('admin.jurusan.index', compact('jurusan', 'jumlahSiswa', 'tahunAktif', 'q'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_jurusan' => 'required|string|max:20|unique:jurusan,kode_jurusan',
            'nama_jurusan' => 'required|string|max:100',
        ]);

        Jurusan::create($request->all());

        return redirect()->back()->with('success', 'Data jurusan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $jurusan = Jurusan::findOrFail($id);

        $request->validate([
            'kode_jurusan' => 'required|string|max:20|unique:jurusan,kode_jurusan,'.$jurusan->id,
            'nama_jurusan' => 'required|string|max:100',
        ]);

        $jurusan->update($request->all());

        return redirect()->back()->with('success', 'Data jurusan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $jurusan = Jurusan::findOrFail($id);
        $jurusan->delete();

        return redirect()->back()->with('success', 'Data jurusan berhasil dihapus.');
    }
}
