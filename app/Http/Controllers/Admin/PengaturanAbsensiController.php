<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PengaturanAbsensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PengaturanAbsensiController extends Controller
{
    /**
     * Urutan hari sekolah, dipakai supaya kartu selalu tampil Senin → Sabtu.
     */
    private const URUTAN_HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public function index()
    {
        $pengaturan = PengaturanAbsensi::all()
            ->sortBy(fn ($item) => array_search($item->hari, self::URUTAN_HARI, true))
            ->values();

        return view('admin.pengaturan_absensi.index', compact('pengaturan'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jam_masuk' => ['required', 'date_format:H:i'],
            'toleransi_keterlambatan' => ['required', 'integer', 'min:0', 'max:180'],
            'jam_tutup_absensi' => ['required', 'date_format:H:i'],
            'jam_pulang' => ['required', 'date_format:H:i'],
            'durasi_absensi_pulang' => ['required', 'integer', 'min:0', 'max:600'],
        ], [
            'date_format' => 'Format :attribute harus JJ:MM.',
            'integer' => ':attribute harus berupa angka.',
            'min' => ':attribute tidak boleh kurang dari :min.',
            'max' => ':attribute tidak boleh lebih dari :max.',
        ], [
            'jam_masuk' => 'jam masuk',
            'toleransi_keterlambatan' => 'toleransi keterlambatan',
            'jam_tutup_absensi' => 'jam tutup absensi',
            'jam_pulang' => 'jam pulang',
            'durasi_absensi_pulang' => 'durasi absensi pulang',
        ]);

        if ($validator->fails()) {
            // edit_id dipakai view untuk membuka kembali modal hari yang gagal disimpan.
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('edit_id', $id);
        }

        $pengaturan = PengaturanAbsensi::findOrFail($id);
        $pengaturan->update($validator->validated());

        return redirect()
            ->route('admin.pengaturan-absensi.index')
            ->with('success', "Pengaturan hari {$pengaturan->hari} berhasil diperbarui.");
    }

    public function toggleAktif($id)
    {
        $pengaturan = PengaturanAbsensi::findOrFail($id);
        $pengaturan->update(['is_aktif' => ! $pengaturan->is_aktif]);

        return redirect()
            ->route('admin.pengaturan-absensi.index')
            ->with('success', $pengaturan->is_aktif
                ? "Absensi hari {$pengaturan->hari} diaktifkan kembali."
                : "Absensi hari {$pengaturan->hari} dinonaktifkan. Scan gerbang pada hari ini akan ditolak.");
    }
}
