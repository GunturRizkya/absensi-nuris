<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->validate(['q' => 'nullable|string|max:100'])['q'] ?? null;

        $guru = Guru::with(['user', 'kelasWali'])
            ->when($q, fn ($query) => $query->where(
                fn ($w) => $w->where('nama_lengkap', 'like', "%$q%")->orWhere('nip', 'like', "%$q%")
            ))
            ->withCount('jadwal')
            ->orderBy('nama_lengkap')
            ->paginate(12)
            ->withQueryString();

        // Akun yang bisa ditautkan: role guru/wali_kelas yang belum punya data guru.
        $akunTersedia = User::whereIn('role', ['guru', 'wali_kelas'])
            ->whereNotIn('id', Guru::whereNotNull('user_id')->pluck('user_id'))
            ->orderBy('name')
            ->get();

        return view('admin.guru.index', compact('guru', 'akunTersedia', 'q'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'nip' => 'nullable|string|max:30|unique:guru,nip',
            'no_hp' => 'nullable|string|max:20',
            'user_id' => 'nullable|exists:users,id|unique:guru,user_id',
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data['foto'] = $this->simpanFoto($request, null);

        Guru::create($data);

        return redirect()->back()->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $guru = Guru::findOrFail($id);

        $data = $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'nip' => ['nullable', 'string', 'max:30', Rule::unique('guru', 'nip')->ignore($guru->id)],
            'no_hp' => 'nullable|string|max:20',
            'user_id' => ['nullable', 'exists:users,id', Rule::unique('guru', 'user_id')->ignore($guru->id)],
            'foto' => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data['foto'] = $this->simpanFoto($request, $guru) ?? $guru->foto;

        $guru->update($data);

        return redirect()->back()->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $this->hapusFoto($guru->foto);
        $guru->delete();

        return redirect()->back()->with('success', 'Data guru berhasil dihapus.');
    }

    private function simpanFoto(Request $request, ?Guru $lama): ?string
    {
        if (! $request->hasFile('foto')) {
            return null;
        }

        $file = $request->file('foto');
        $nama = now()->format('YmdHis').'_'.bin2hex(random_bytes(4)).'.'.$file->getClientOriginalExtension();
        $file->move(public_path('foto-siswa'), $nama);

        if ($lama?->foto) {
            $this->hapusFoto($lama->foto);
        }

        return 'foto-siswa/'.$nama;
    }

    private function hapusFoto(?string $path): void
    {
        if ($path && file_exists(public_path($path))) {
            @unlink(public_path($path));
        }
    }
}
