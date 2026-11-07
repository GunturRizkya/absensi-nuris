<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
   public function index()
{
    $hariLiburs = HariLibur::orderBy('tanggal_mulai')
        ->paginate(10);

    return view('admin.hari_libur.index', compact('hariLiburs'));
}
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_libur' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string|max:2000',
        ]);

        HariLibur::create($data);

        return redirect()
            ->route('admin.hari-libur.index')
            ->with('success', 'Hari libur berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama_libur' => 'required|string|max:255',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'nullable|string|max:2000',
        ]);

        $hariLibur = HariLibur::findOrFail($id);
        $hariLibur->update($data);

        return redirect()
            ->route('admin.hari-libur.index')
            ->with('success', 'Hari libur berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $hariLibur = HariLibur::findOrFail($id);
        $hariLibur->delete();

        return redirect()
            ->route('admin.hari-libur.index')
            ->with('success', 'Hari libur berhasil dihapus.');
    }
}