<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TahunAjaranController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->validate(['q' => 'nullable|string|max:100'])['q'] ?? null;

        $tahunAjaran = TahunAjaran::when($q, fn ($query) => $query->where('tahun', 'like', "%$q%"))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.tahun_ajaran.index', compact('tahunAjaran', 'q'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => [
                'required', 'string', 'max:20',
                Rule::unique('tahun_ajaran', 'tahun')->where(fn ($q) => $q->where('semester', $request->semester)),
            ],
            'semester' => 'required|in:1,2',
        ]);

        TahunAjaran::create([
            'tahun' => $request->tahun,
            'semester' => $request->semester,
            'is_aktif' => false,
        ]);

        return redirect()->back()->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function setAktif($id)
    {
        // Nonaktifkan semua tahun ajaran terlebih dahulu
        TahunAjaran::query()->update(['is_aktif' => false]);

        // Aktifkan tahun ajaran yang dipilih
        $tahun = TahunAjaran::findOrFail($id);
        $tahun->update(['is_aktif' => true]);

        return redirect()->back()->with('success', 'Tahun ajaran aktif berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $tahun = TahunAjaran::findOrFail($id);
        $tahun->delete();

        return redirect()->back()->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
