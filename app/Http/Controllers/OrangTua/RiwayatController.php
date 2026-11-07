<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\AbsensiHarian;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $anak = $request->user()->anak()->orderBy('nama_lengkap')->get();

        $filter = $request->validate([
            'anak_id' => 'nullable|exists:siswa,id',
            'dari' => 'nullable|date',
            'sampai' => 'nullable|date|after_or_equal:dari',
        ]);

        $anakId = $filter['anak_id'] ?? $anak->first()?->id;
        $dari = $filter['dari'] ?? now()->startOfMonth()->toDateString();
        $sampai = $filter['sampai'] ?? now()->toDateString();

        // Pastikan hanya anak sendiri yang bisa dilihat.
        if ($anakId && ! $anak->contains('id', (int) $anakId)) {
            abort(403);
        }

        $riwayat = $anakId
            ? AbsensiHarian::where('siswa_id', $anakId)
                ->whereBetween('tanggal', [$dari, $sampai])
                ->orderByDesc('tanggal')
                ->paginate(31)
                ->withQueryString()
            : collect();

        return view('orangtua.riwayat.index', compact('anak', 'riwayat', 'anakId', 'dari', 'sampai'));
    }
}
