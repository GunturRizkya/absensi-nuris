<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jurusan;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MapelController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->validate([
            'q' => 'nullable|string|max:100',
            'jurusan_id' => 'nullable|string',
        ]);

        $q = $filter['q'] ?? null;
        $jurusanId = $filter['jurusan_id'] ?? null;

        $mapel = MataPelajaran::with('jurusan')
            ->when($q, fn ($query) => $query->where(
                fn ($w) => $w->where('kode_mapel', 'like', "%$q%")->orWhere('nama_mapel', 'like', "%$q%")
            ))
            ->when($jurusanId === 'umum', fn ($query) => $query->whereNull('jurusan_id'))
            ->when($jurusanId && $jurusanId !== 'umum', fn ($query) => $query->where('jurusan_id', $jurusanId))
            ->orderBy('nama_mapel')
            ->paginate(12)
            ->withQueryString();
        $jurusan = Jurusan::orderBy('nama_jurusan')->get();

        return view('admin.mapel.index', compact('mapel', 'jurusan', 'q', 'jurusanId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_mapel' => 'required|string|max:20|unique:mata_pelajaran,kode_mapel',
            'nama_mapel' => 'required|string|max:100',
            'jurusan_id' => 'nullable|exists:jurusan,id',
        ]);

        MataPelajaran::create($data);

        return redirect()->back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $mapel = MataPelajaran::findOrFail($id);

        $data = $request->validate([
            'kode_mapel' => ['required', 'string', 'max:20', Rule::unique('mata_pelajaran', 'kode_mapel')->ignore($mapel->id)],
            'nama_mapel' => 'required|string|max:100',
            'jurusan_id' => 'nullable|exists:jurusan,id',
        ]);

        $mapel->update($data);

        return redirect()->back()->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        MataPelajaran::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}
