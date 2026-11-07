<x-app-layout>
    <!-- Header -->
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="crumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-700">Data Pengguna</span>
                </nav>
                <h1 class="page-title">Manajemen Pengguna</h1>
                <p class="page-sub">Kelola akun login, role, dan penugasan khusus seperti BK.</p>
            </div>

            <div x-data="{ openModal: false }">
                <button @click="openModal = true" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Tambah Pengguna
                </button>

                <!-- Modal Tambah -->
                <div x-show="openModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
                    <div @click.outside="openModal = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-slate-800">Tambah Pengguna Baru</h3>
                            <button @click="openModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                        </div>

                        <form action="{{ route('admin.pengguna.store') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="label">Nama Lengkap</label>
                                    <input type="text" name="name" required class="input">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">Username</label>
                                        <input type="text" name="username" class="input">
                                    </div>
                                    <div>
                                        <label class="label">Role</label>
                                        <select name="role" required class="input">
                                            <option value="admin">Administrator</option>
                                            <option value="guru" selected>Guru</option>
                                            <option value="wali_kelas">Wali Kelas</option>
                                            <option value="piket">Guru Piket</option>
                                            <option value="orang_tua">Orang Tua</option>
                                            <option value="osis">OSIS</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="label">Email</label>
                                    <input type="email" name="email" required class="input">
                                </div>
                                <div>
                                    <label class="label">Password</label>
                                    <input type="password" name="password" required minlength="8" class="input">
                                </div>
                                <div>
                                    <label class="label">Penugasan <span class="font-medium text-slate-400">(menambah hak akses)</span></label>
                                    <select name="penugasan" class="input">
                                        <option value="">— Tanpa penugasan —</option>
                                        <option value="bk">BK – Bimbingan Konseling (akses halaman guru + BK)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-2">
                                <button type="button" @click="openModal = false" class="btn-secondary">Batal</button>
                                <button type="submit" class="btn-primary">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center justify-between gap-3">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 text-base leading-none px-1">&times;</button>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center justify-between gap-3">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-base leading-none px-1">&times;</button>
            </div>
        @endif

        <!-- Filter -->
        <div class="card p-4 mb-4">
            <form action="{{ route('admin.pengguna.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
                <div class="sm:col-span-2 relative">
                    <label class="label">Cari nama / username / email</label>
                    <span class="absolute inset-y-0 left-0 top-5 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Contoh: Budi" class="input !pl-10">
                </div>
                <div>
                    <label class="label">Role</label>
                    <select name="role" class="input bg-white">
                        <option value="">Semua Role</option>
                        <option value="admin" @selected($roleAktif === 'admin')>Administrator</option>
                        <option value="guru" @selected($roleAktif === 'guru')>Guru</option>
                        <option value="wali_kelas" @selected($roleAktif === 'wali_kelas')>Wali Kelas</option>
                        <option value="piket" @selected($roleAktif === 'piket')>Guru Piket</option>
                        <option value="orang_tua" @selected($roleAktif === 'orang_tua')>Orang Tua</option>
                        <option value="osis" @selected($roleAktif === 'osis')>OSIS</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="btn-primary flex-1">Tampilkan</button>
                    @if($q || $roleAktif)
                        <a href="{{ route('admin.pengguna.index') }}" class="btn-secondary">Reset</a>
                    @endif
                </div>
            </form>
            <p class="mt-2.5 text-[11px] text-slate-400 font-medium">Menampilkan {{ $pengguna->count() }} dari {{ $pengguna->total() }} akun.</p>
        </div>

        <!-- Grid Kartu -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @forelse($pengguna as $item)
                <div class="card p-5 group hover:shadow-md transition-shadow" x-data="{ editModal: false }">
                    <div class="flex items-start gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white font-extrabold flex items-center justify-center text-sm shadow-sm shrink-0 tracking-wide">
                            {{ strtoupper(substr($item->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="font-extrabold text-slate-800 text-sm tracking-tight truncate">{{ $item->name }}</p>
                            <p class="text-[11px] font-medium text-slate-400 truncate">{{ $item->email }}{{ $item->username ? ' · @'.$item->username : '' }}</p>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                <span class="badge bg-blue-50 text-blue-600 border-blue-200/60 capitalize">{{ str_replace('_', ' ', $item->role) }}</span>
                                @if($item->penugasan)
                                    <span class="badge bg-violet-50 text-violet-600 border-violet-200/60 uppercase">{{ $item->penugasan }}</span>
                                @endif
                                @if($item->guru)
                                    <span class="badge bg-slate-100 text-slate-500 border-transparent">Tertaut guru</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($item->role === 'orang_tua')
                        <div class="mt-3 pt-3 border-t border-slate-100 space-y-1.5">
                            @foreach($item->orangTuaSiswa as $tautan)
                                <div class="flex items-center justify-between gap-2 px-2.5 py-1.5 rounded-xl bg-violet-50/70 border border-violet-100">
                                    <span class="text-[11px] font-bold text-violet-700 truncate">{{ $tautan->siswa->nama_lengkap ?? '-' }}@if($tautan->hubungan) <span class="font-medium text-violet-400">({{ $tautan->hubungan }})</span>@endif</span>
                                    <form action="{{ route('admin.pengguna.lepas-anak', $tautan->id) }}" method="POST" onsubmit="return confirm('Lepas tautan anak ini?')" class="shrink-0">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-violet-400 hover:text-rose-600 font-bold text-base leading-none px-1" title="Lepas">&times;</button>
                                    </form>
                                </div>
                            @endforeach
                            <form action="{{ route('admin.pengguna.tautkan-anak', $item->id) }}" method="POST" class="flex gap-1.5">
                                @csrf
                                <select name="siswa_id" required class="flex-1 min-w-0 px-2.5 py-2 text-[11px] font-medium border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white">
                                    <option value="">— Tautkan anak —</option>
                                    @foreach($semuaSiswa as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama_lengkap }} ({{ $s->nisn }})</option>
                                    @endforeach
                                </select>
                                <select name="hubungan" class="px-2 py-2 text-[11px] border border-slate-200 rounded-xl bg-white">
                                    <option value="">—</option>
                                    <option value="ayah">Ayah</option>
                                    <option value="ibu">Ibu</option>
                                    <option value="wali">Wali</option>
                                </select>
                                <button type="submit" class="px-3 py-2 text-[11px] font-bold text-white bg-violet-500 hover:bg-violet-600 rounded-xl transition-colors shrink-0">+</button>
                            </form>
                        </div>
                    @endif

                    <div class="mt-3 pt-3 border-t border-slate-100 flex justify-end gap-1.5 opacity-100 md:opacity-0 md:group-hover:opacity-100 focus-within:opacity-100 transition-opacity">
                        <button @click="editModal = true" class="btn-warn-soft">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </button>
                        @if($item->id !== auth()->id())
                            <form action="{{ route('admin.pengguna.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-danger-soft">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    Hapus
                                </button>
                            </form>
                        @endif

                        <!-- Modal Edit -->
                        <div x-show="editModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
                            <div @click.outside="editModal = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 text-left max-h-[90vh] overflow-y-auto">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-base font-bold text-slate-800">Edit Pengguna</h3>
                                    <button @click="editModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                                </div>
                                <form action="{{ route('admin.pengguna.update', $item->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="space-y-4">
                                        <div>
                                            <label class="label">Nama Lengkap</label>
                                            <input type="text" name="name" value="{{ $item->name }}" required class="input">
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="label">Username</label>
                                                <input type="text" name="username" value="{{ $item->username }}" class="input">
                                            </div>
                                            <div>
                                                <label class="label">Role</label>
                                                <select name="role" required class="input">
                                                    <option value="admin" @selected($item->role === 'admin')>Administrator</option>
                                                    <option value="guru" @selected($item->role === 'guru')>Guru</option>
                                                    <option value="wali_kelas" @selected($item->role === 'wali_kelas')>Wali Kelas</option>
                                                    <option value="piket" @selected($item->role === 'piket')>Guru Piket</option>
                                                    <option value="orang_tua" @selected($item->role === 'orang_tua')>Orang Tua</option>
                                                    <option value="osis" @selected($item->role === 'osis')>OSIS</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="label">Email</label>
                                            <input type="email" name="email" value="{{ $item->email }}" required class="input">
                                        </div>
                                        <div>
                                            <label class="label">Password Baru <span class="font-medium text-slate-400">(kosongkan jika tidak diubah)</span></label>
                                            <input type="password" name="password" minlength="8" class="input">
                                        </div>
                                        <div>
                                            <label class="label">Penugasan</label>
                                            <select name="penugasan" class="input">
                                                <option value="">— Tanpa penugasan —</option>
                                                <option value="bk" @selected(strtolower(trim($item->penugasan ?? '')) === 'bk')>BK – Bimbingan Konseling (akses halaman guru + BK)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mt-6 flex justify-end gap-2">
                                        <button type="button" @click="editModal = false" class="btn-secondary">Batal</button>
                                        <button type="submit" class="btn-primary">Update Data</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="card px-6 py-12 text-center lg:col-span-2">
                    <p class="text-xs text-slate-400 font-medium">Belum ada data pengguna.</p>
                </div>
            @endforelse
        </div>
        @if($pengguna->hasPages())
            <div class="mt-4">{{ $pengguna->links() }}</div>
        @endif
</x-app-layout>
