<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanAbsensi;
use Illuminate\Http\Request;

class PengaturanAbsensiController extends Controller
{
    public function index()
    {
        $pengaturan = PengaturanAbsensi::all();

        return view('admin.pengaturan_absensi.index', compact('pengaturan'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jam_masuk' => 'required',
            'toleransi_keterlambatan' => 'required|numeric',
            'jam_tutup_absensi' => 'required',
            'jam_pulang' => 'required',
            'durasi_absensi_pulang' => 'required|numeric',
        ]);

        $pengaturan = PengaturanAbsensi::findOrFail($id);
        $pengaturan->update($request->all());

        return redirect()->back()->with('success', 'Pengaturan jam absensi berhasil diperbarui.');
    }
}
