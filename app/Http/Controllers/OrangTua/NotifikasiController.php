<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\NotifikasiKehadiran;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $anakIds = $request->user()->anak()->pluck('siswa.id');

        $notifikasi = NotifikasiKehadiran::with('siswa')
            ->whereIn('siswa_id', $anakIds)
            ->where('kanal', 'aplikasi')
            ->orderByRaw('dibaca_pada IS NOT NULL, tanggal DESC, id DESC')
            ->paginate(20);

        $belumDibaca = NotifikasiKehadiran::whereIn('siswa_id', $anakIds)
            ->where('kanal', 'aplikasi')
            ->whereNull('dibaca_pada')
            ->count();

        return view('orangtua.notifikasi.index', compact('notifikasi', 'belumDibaca'));
    }

    public function baca(Request $request, $id)
    {
        $anakIds = $request->user()->anak()->pluck('siswa.id');

        $notif = NotifikasiKehadiran::whereIn('siswa_id', $anakIds)->findOrFail($id);
        $notif->update(['dibaca_pada' => now()]);

        return redirect()->back();
    }

    public function bacaSemua(Request $request)
    {
        $anakIds = $request->user()->anak()->pluck('siswa.id');

        NotifikasiKehadiran::whereIn('siswa_id', $anakIds)
            ->where('kanal', 'aplikasi')
            ->whereNull('dibaca_pada')
            ->update(['dibaca_pada' => now()]);

        return redirect()->back()->with('success', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
