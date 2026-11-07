<x-app-layout>
    <!-- Header -->
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="crumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-700">Data Guru</span>
                </nav>
                <h1 class="page-title">Manajemen Guru</h1>
                <p class="page-sub">Kelola data guru dan tautan akun loginnya.</p>
            </div>

            <div x-data="{ openModal: false }">
                <button @click="openModal = true" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Tambah Guru
                </button>

                <!-- Modal Tambah -->
                <div x-show="openModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
                    <div @click.outside="openModal = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-slate-800">Tambah Guru Baru</h3>
                            <button @click="openModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                        </div>

                        <form action="{{ route('admin.guru.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="label">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" required class="input">
                                </div>
                                <div>
                                    <label class="label">Foto <span class="font-medium text-slate-400">(JPG/PNG, maks 2 MB)</span></label>
                                    <input type="file" name="foto" accept=".jpg,.jpeg,.png" class="w-full text-xs text-slate-600 file:mr-3 file:px-3 file:py-2 file:text-xs file:font-bold file:bg-slate-100 file:hover:bg-slate-200 file:text-slate-700 file:rounded-xl file:border-0">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">NIP</label>
                                        <input type="text" name="nip" class="input">
                                    </div>
                                    <div>
                                        <label class="label">No. HP</label>
                                        <input type="text" name="no_hp" class="input">
                                    </div>
                                </div>
                                <div>
                                    <label class="label">Tautkan Akun Login <span class="font-medium text-slate-400">(opsional)</span></label>
                                    <select name="user_id" class="input">
                                        <option value="">— Tanpa akun —</option>
                                        @foreach($akunTersedia as $akun)
                                            <option value="{{ $akun->id }}">{{ $akun->name }} ({{ $akun->email }})</option>
                                        @endforeach
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

        <!-- Pencarian server -->
        <div class="card p-4 mb-4">
            <form action="{{ route('admin.guru.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-52 relative">
                    <label class="label">Cari nama / NIP</label>
                    <span class="absolute inset-y-0 left-0 top-5 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Contoh: Budi" class="input !pl-10 py-3">
                </div>
                <button type="submit" class="btn-primary">Tampilkan</button>
                @if($q)
                    <a href="{{ route('admin.guru.index') }}" class="btn-secondary">Reset</a>
                @endif
            </form>
        </div>

        <!-- Grid Kartu -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @forelse($guru as $item)
                <div class="card p-5 group hover:shadow-md transition-shadow" x-data="{ editModal: false }">
                    <div class="flex items-start gap-3.5">
                        @if($item->foto)
                            <img src="{{ asset($item->foto) }}" alt="Foto {{ $item->nama_lengkap }}" class="w-12 h-12 rounded-2xl object-cover shadow-sm shrink-0">
                        @else
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-violet-500 to-purple-500 text-white font-extrabold flex items-center justify-center text-sm shadow-sm shrink-0 tracking-wide">
                                {{ strtoupper(substr($item->nama_lengkap, 0, 2)) }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="font-extrabold text-slate-800 text-sm tracking-tight truncate">{{ $item->nama_lengkap }}</p>
                            <p class="text-[11px] font-medium text-slate-400">NIP {{ $item->nip ?? '-' }} · {{ $item->no_hp ?? '-' }}</p>
                            <div class="mt-2 flex flex-wrap gap-1.5">
                                @if($item->user)
                                    <span class="badge bg-blue-50 text-blue-600 border-blue-200/60">{{ $item->user->email }}</span>
                                @else
                                    <span class="badge bg-slate-100 text-slate-500 border-transparent">Tanpa akun</span>
                                @endif
                                @if($item->kelasWali)
                                    <span class="badge bg-violet-50 text-violet-600 border-violet-200/60">Wali {{ $item->kelasWali->nama_kelas }}</span>
                                @endif
                                @if($item->jadwal_count > 0)
                                    <span class="badge bg-slate-100 text-slate-500 border-transparent">{{ $item->jadwal_count }} jadwal</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 pt-3 border-t border-slate-100 flex justify-end gap-1.5 opacity-100 md:opacity-0 md:group-hover:opacity-100 focus-within:opacity-100 transition-opacity">
                        <button @click="editModal = true" class="btn-warn-soft">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </button>
                        <form action="{{ route('admin.guru.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data guru ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger-soft">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>

                        <!-- Modal Edit -->
                        <div x-show="editModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
                            <div @click.outside="editModal = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 text-left max-h-[90vh] overflow-y-auto">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-base font-bold text-slate-800">Edit Guru</h3>
                                    <button @click="editModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                                </div>
                                <form action="{{ route('admin.guru.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                                    @csrf @method('PUT')
                                    <div class="space-y-4">
                                        <div>
                                            <label class="label">Nama Lengkap</label>
                                            <input type="text" name="nama_lengkap" value="{{ $item->nama_lengkap }}" required class="input">
                                        </div>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="label">NIP</label>
                                                <input type="text" name="nip" value="{{ $item->nip }}" class="input">
                                            </div>
                                            <div>
                                                <label class="label">No. HP</label>
                                                <input type="text" name="no_hp" value="{{ $item->no_hp }}" class="input">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="label">Foto Baru <span class="font-medium text-slate-400">(kosongkan bila tetap)</span></label>
                                            <input type="file" name="foto" accept=".jpg,.jpeg,.png" class="w-full text-xs text-slate-600 file:mr-3 file:px-3 file:py-2 file:text-xs file:font-bold file:bg-slate-100 file:hover:bg-slate-200 file:text-slate-700 file:rounded-xl file:border-0">
                                        </div>
                                        <div>
                                            <label class="label">Tautkan Akun Login</label>
                                            <select name="user_id" class="input">
                                                <option value="">— Tanpa akun —</option>
                                                @if($item->user)
                                                    <option value="{{ $item->user->id }}" selected>{{ $item->user->name }} ({{ $item->user->email }})</option>
                                                @endif
                                                @foreach($akunTersedia as $akun)
                                                    <option value="{{ $akun->id }}">{{ $akun->name }} ({{ $akun->email }})</option>
                                                @endforeach
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
                    <p class="text-xs text-slate-400 font-medium">Belum ada data guru.</p>
                </div>
            @endforelse
        </div>
        <p class="mt-3 text-[11px] text-slate-400 font-medium">Menampilkan {{ $guru->count() }} dari {{ $guru->total() }} guru.</p>
        @if($guru->hasPages())
            <div class="mt-4">{{ $guru->links() }}</div>
        @endif
</x-app-layout>
