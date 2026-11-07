<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrangTuaSiswa;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public const ROLES = ['admin', 'guru', 'wali_kelas', 'piket', 'orang_tua', 'osis'];

    public const PENUGASAN = ['bk'];

    public function index(Request $request)
    {
        $filter = $request->validate([
            'q' => 'nullable|string|max:100',
            'role' => ['nullable', Rule::in(self::ROLES)],
        ]);

        $q = $filter['q'] ?? null;
        $roleAktif = $filter['role'] ?? null;

        $pengguna = User::with(['guru', 'orangTuaSiswa.siswa'])
            ->when($q, fn ($query) => $query->where(
                fn ($w) => $w->where('name', 'like', "%$q%")
                    ->orWhere('username', 'like', "%$q%")
                    ->orWhere('email', 'like', "%$q%")
            ))
            ->when($roleAktif, fn ($query) => $query->where('role', $roleAktif))
            ->latest()
            ->paginate(12)
            ->withQueryString();
        $semuaSiswa = Siswa::orderBy('nama_lengkap')->get(['id', 'nisn', 'nama_lengkap']);

        return view('admin.pengguna.index', compact('pengguna', 'semuaSiswa', 'q', 'roleAktif'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'nullable|string|max:255|unique:users,username',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(self::ROLES)],
            'penugasan' => ['nullable', 'string', 'max:30', Rule::in(self::PENUGASAN)],
        ]);

        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['nullable', 'string', 'max:255', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(self::ROLES)],
            'penugasan' => ['nullable', 'string', 'max:30', Rule::in(self::PENUGASAN)],
        ]);

        // Admin tidak boleh mencabut role admin miliknya sendiri.
        if ($user->is(auth()->user()) && $data['role'] !== 'admin') {
            return redirect()->back()->with('error', 'Anda tidak dapat mencabut role admin milik sendiri.');
        }

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->back()->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->is(auth()->user())) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus.');
    }

    /**
     * Tautkan akun orang tua ke seorang siswa.
     */
    public function tautkanAnak(Request $request, $id)
    {
        $ortu = User::findOrFail($id);

        if ($ortu->role !== 'orang_tua') {
            return redirect()->back()->with('error', 'Hanya akun orang tua yang bisa ditautkan ke siswa.');
        }

        $data = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'hubungan' => 'nullable|string|max:20',
        ]);

        $sudah = OrangTuaSiswa::where('user_id', $ortu->id)
            ->where('siswa_id', $data['siswa_id'])
            ->exists();

        if ($sudah) {
            return redirect()->back()->with('error', 'Siswa tersebut sudah tertaut ke akun ini.');
        }

        OrangTuaSiswa::create([
            'user_id' => $ortu->id,
            'siswa_id' => $data['siswa_id'],
            'hubungan' => $data['hubungan'] ?: null,
        ]);

        return redirect()->back()->with('success', 'Anak berhasil ditautkan ke akun orang tua.');
    }

    /**
     * Lepas tautan akun orang tua - siswa.
     */
    public function lepasAnak($pivotId)
    {
        OrangTuaSiswa::findOrFail($pivotId)->delete();

        return redirect()->back()->with('success', 'Tautan anak berhasil dilepas.');
    }
}
