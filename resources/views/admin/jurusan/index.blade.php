<x-app-layout>
    <!-- Header -->
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="crumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-700">Data Jurusan</span>
                </nav>
                <h1 class="page-title">Manajemen Jurusan SMK</h1>
                <p class="page-sub">Kelola daftar program keahlian/jurusan yang tersedia di sekolah.</p>
            </div>

            <div x-data="{ openModal: false }">
                <button @click="openModal = true" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Tambah Jurusan
                </button>

                <!-- Modal Tambah -->
                <div x-show="openModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
                    <div @click.outside="openModal = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-slate-800">Tambah Jurusan Baru</h3>
                            <button @click="openModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                        </div>
                        <form action="{{ route('admin.jurusan.store') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="label">Kode Jurusan</label>
                                    <input type="text" name="kode_jurusan" placeholder="Contoh: RPL" required class="input uppercase">
                                </div>
                                <div>
                                    <label class="label">Nama Jurusan</label>
                                    <input type="text" name="nama_jurusan" placeholder="Contoh: Rekayasa Perangkat Lunak" required class="input">
                                </div>
                            </div>
                            <div class="mt-6 flex justify-end gap-2">
                                <button type="button" @click="openModal = false" class="btn-secondary">Batal</button>
                                <button type="submit" class="btn-primary">Simpan</button>
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
            <form action="{{ route('admin.jurusan.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-52 relative">
                    <label class="label">Cari kode / nama</label>
                    <span class="absolute inset-y-0 left-0 top-5 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Contoh: RPL" class="input !pl-10 py-3">
                </div>
                <button type="submit" class="btn-primary">Tampilkan</button>
                @if($q)
                    <a href="{{ route('admin.jurusan.index') }}" class="btn-secondary">Reset</a>
                @endif
            </form>
        </div>

        <!-- Bento Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
            @forelse($jurusan as $item)
                <div class="card p-5 group hover:shadow-md transition-shadow">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white font-extrabold flex items-center justify-center text-xs shadow-sm shrink-0 tracking-wide">
                                {{ strtoupper(substr($item->kode_jurusan, 0, 3)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="font-extrabold text-slate-800 text-sm tracking-tight">{{ $item->kode_jurusan }}</p>
                                <p class="text-[11px] font-medium text-slate-400 truncate">{{ $item->nama_jurusan }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                        <div class="rounded-xl bg-slate-50 px-2 py-2.5">
                            <p class="text-base font-extrabold text-slate-800">{{ $item->kelas_count }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-2 py-2.5">
                            <p class="text-base font-extrabold text-slate-800">{{ $item->mapel_count }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Mapel</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 px-2 py-2.5">
                            <p class="text-base font-extrabold text-blue-600">{{ $jumlahSiswa[$item->id] ?? 0 }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Siswa</p>
                        </div>
                    </div>
                    <div class="mt-4 pt-3 border-t border-slate-100 flex justify-end gap-1.5 opacity-100 md:opacity-0 md:group-hover:opacity-100 focus-within:opacity-100 transition-opacity" x-data="{ editModal: false }">
                        <button @click="editModal = true" class="btn-warn-soft">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </button>
                        <form action="{{ route('admin.jurusan.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus jurusan ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-danger-soft">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Hapus
                            </button>
                        </form>

                        <!-- Modal Edit -->
                        <div x-show="editModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
                            <div @click.outside="editModal = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 text-left">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-base font-bold text-slate-800">Edit Jurusan</h3>
                                    <button @click="editModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                                </div>
                                <form action="{{ route('admin.jurusan.update', $item->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="space-y-4">
                                        <div>
                                            <label class="label">Kode Jurusan</label>
                                            <input type="text" name="kode_jurusan" value="{{ $item->kode_jurusan }}" required class="input uppercase">
                                        </div>
                                        <div>
                                            <label class="label">Nama Jurusan</label>
                                            <input type="text" name="nama_jurusan" value="{{ $item->nama_jurusan }}" required class="input">
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
                <div class="card px-6 py-12 text-center sm:col-span-2 xl:col-span-3">
                    <p class="text-xs text-slate-400 font-medium">Belum ada data jurusan.</p>
                </div>
            @endforelse
        </div>
        <p class="mt-3 text-[11px] text-slate-400 font-medium">Menampilkan {{ $jurusan->count() }} dari {{ $jurusan->total() }} jurusan{{ $tahunAktif ? ' · jumlah siswa tahun '.$tahunAktif->tahun : '' }}.</p>
        @if($jurusan->hasPages())
            <div class="mt-4">{{ $jurusan->links() }}</div>
        @endif
</x-app-layout>
