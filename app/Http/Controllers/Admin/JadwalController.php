<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public const HARI = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

    public function index(Request $request)
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $tahunAjaranId = $request->get('tahun_ajaran_id', $tahunAktif?->id);
        $kelasId = $request->get('kelas_id');
        $hari = $request->get('hari');

        $jadwal = JadwalPelajaran::with(['kelas', 'mataPelajaran', 'guru'])
            ->when($tahunAjaranId, fn ($q) => $q->where('tahun_ajaran_id', $tahunAjaranId))
            ->when($kelasId, fn ($q) => $q->where('kelas_id', $kelasId))
            ->when($hari, fn ($q) => $q->where('hari', $hari))
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();

        return view('admin.jadwal.index', [
            'jadwal' => $jadwal,
            'kelas' => Kelas::orderBy('nama_kelas')->get(),
            'mapel' => MataPelajaran::orderBy('nama_mapel')->get(),
            'guru' => Guru::orderBy('nama_lengkap')->get(),
            'tahunAjaran' => TahunAjaran::latest()->get(),
            'tahunAjaranId' => $tahunAjaranId,
            'kelasId' => $kelasId,
            'hariAktif' => $hari,
            'daftarHari' => self::HARI,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:guru,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        JadwalPelajaran::create($data);

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $jadwal = JadwalPelajaran::findOrFail($id);

        $data = $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'guru_id' => 'required|exists:guru,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ]);

        $jadwal->update($data);

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        JadwalPelajaran::findOrFail($id)->delete();

        return redirect()->back()->with('success', 'Jadwal pelajaran berhasil dihapus.');
    }
}
