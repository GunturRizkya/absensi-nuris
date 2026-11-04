<?php

namespace App\Http\Controllers\OrangTua;

use App\Http\Controllers\Controller;
use App\Models\SuratIzin;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SuratIzinController extends Controller
{
    public function index(Request $request)
    {
        $anak = $request->user()->anak()->orderBy('nama_lengkap')->get();

        $surat = SuratIzin::with(['siswa', 'penyetuju'])
            ->whereIn('siswa_id', $anak->pluck('id'))
            ->latest()
            ->paginate(10);

        return view('orangtua.surat_izin.index', compact('anak', 'surat'));
    }

    public function store(Request $request)
    {
        $milik = $request->user()->anak()->pluck('siswa.id')->all();

        $data = $request->validate([
            'siswa_id' => ['required', 'exists:siswa,id', Rule::in($milik)],
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'jenis' => 'required|in:izin,sakit',
            'alasan' => 'required|string',
            // Disimpan di public/bukti-izin (tanpa symlink storage).
            'file_bukti' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        if ($request->hasFile('file_bukti')) {
            $file = $request->file('file_bukti');
            $nama = now()->format('YmdHis').'_'.$data['siswa_id'].'.'.$file->getClientOriginalExtension();
            $file->move(public_path('bukti-izin'), $nama);
            $data['file_bukti'] = 'bukti-izin/'.$nama;
        }

        SuratIzin::create($data + ['status_persetujuan' => 'pending']);

        return redirect()->back()->with('success', 'Surat berhasil diajukan, menunggu persetujuan wali kelas.');
    }
}
