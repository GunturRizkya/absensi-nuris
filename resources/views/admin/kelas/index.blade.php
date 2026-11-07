@extends('layouts.app')

@section('content')
<!-- Header -->
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="crumb">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Data Kelas</span>
            </nav>
            <h1 class="page-title">Pengaturan Kelas</h1>
            <p class="page-sub">Kelola rombongan belajar berdasarkan tingkat dan jurusan.</p>
        </div>

        <div x-data="{ openModal: false }">
            <button @click="openModal = true" class="btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                Tambah Kelas
            </button>

            <!-- Modal Tambah -->
            <div x-show="openModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
                <div @click.outside="openModal = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-800">Tambah Kelas Baru</h3>
                        <button @click="openModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                    </div>
                    <form action="{{ route('admin.kelas.store') }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <div>
                                <label class="label">Nama Kelas</label>
                                <input type="text" name="nama_kelas" placeholder="Contoh: XII RPL 1" required class="input">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="label">Tingkat</label>
                                    <select name="tingkat" required class="input bg-white">
                                        <option value="X">X (Sepuluh)</option>
                                        <option value="XI">XI (Sebelas)</option>
                                        <option value="XII">XII (Duabelas)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="label">Jurusan</label>
                                    <select name="jurusan_id" required class="input bg-white">
                                        @foreach($jurusan as $j)
                                            <option value="{{ $j->id }}">{{ $j->nama_jurusan }} ({{ $j->kode_jurusan }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="label">Wali Kelas <span class="font-medium text-slate-400">(opsional)</span></label>
                                <select name="wali_kelas_id" class="input bg-white">
                                    <option value="">— Tanpa wali kelas —</option>
                                    @foreach($guru as $g)
                                        <option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>
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

    <!-- Filter -->
    <div class="card p-4 mb-4">
        <form action="{{ route('admin.kelas.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-end">
            <div class="sm:col-span-2 relative">
                <label class="label">Cari nama kelas</label>
                <span class="absolute inset-y-0 left-0 top-5 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="q" value="{{ $q }}" placeholder="Contoh: XII RPL 1" class="input !pl-10">
            </div>
            <div>
                <label class="label">Jurusan</label>
                <select name="jurusan_id" class="input bg-white">
                    <option value="">Semua Jurusan</option>
                    @foreach($jurusan as $j)
                        <option value="{{ $j->id }}" @selected($jurusanId == $j->id)>{{ $j->nama_jurusan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 sm:col-span-2">
                <button type="submit" class="btn-primary flex-1">Tampilkan</button>
                @if($q || $jurusanId)
                    <a href="{{ route('admin.kelas.index') }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Bento Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse($kelas as $item)
            <div class="card p-5 group hover:shadow-md transition-shadow">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white font-extrabold flex items-center justify-center text-sm shadow-sm shrink-0">
                            {{ $item->tingkat }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-extrabold text-slate-800 text-sm tracking-tight truncate">{{ $item->nama_kelas }}</p>
                            <p class="text-[11px] font-medium text-slate-400 truncate">{{ $item->jurusan->nama_jurusan ?? '-' }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <div class="rounded-xl bg-slate-50 px-3 py-2.5 flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <div>
                            <p class="text-base font-extrabold text-slate-800 leading-none">{{ $jumlahSiswa[$item->id] ?? 0 }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Siswa</p>
                        </div>
                    </div>
                    <div class="rounded-xl bg-slate-50 px-3 py-2.5 flex items-center gap-2.5 min-w-0">
                        <svg class="w-4 h-4 text-violet-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <div class="min-w-0">
                            <p class="text-xs font-extrabold text-slate-800 leading-tight truncate">{{ $item->waliKelas->nama_lengkap ?? 'Tanpa wali' }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Wali Kelas</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex justify-end gap-1.5 opacity-100 md:opacity-0 md:group-hover:opacity-100 focus-within:opacity-100 transition-opacity" x-data="{ editModal: false }">
                    <button @click="editModal = true" class="btn-warn-soft">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </button>
                    <form action="{{ route('admin.kelas.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')">
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
                                <h3 class="text-base font-bold text-slate-800">Edit Kelas</h3>
                                <button @click="editModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                            </div>
                            <form action="{{ route('admin.kelas.update', $item->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="space-y-4">
                                    <div>
                                        <label class="label">Nama Kelas</label>
                                        <input type="text" name="nama_kelas" value="{{ $item->nama_kelas }}" required class="input">
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="label">Tingkat</label>
                                            <select name="tingkat" required class="input bg-white">
                                                <option value="X" @selected($item->tingkat === 'X')>X (Sepuluh)</option>
                                                <option value="XI" @selected($item->tingkat === 'XI')>XI (Sebelas)</option>
                                                <option value="XII" @selected($item->tingkat === 'XII')>XII (Duabelas)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="label">Jurusan</label>
                                            <select name="jurusan_id" required class="input bg-white">
                                                @foreach($jurusan as $j)
                                                    <option value="{{ $j->id }}" @selected($item->jurusan_id == $j->id)>{{ $j->nama_jurusan }} ({{ $j->kode_jurusan }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div>
                                        <label class="label">Wali Kelas</label>
                                        <select name="wali_kelas_id" class="input bg-white">
                                            <option value="">— Tanpa wali kelas —</option>
                                            @foreach($guru as $g)
                                                <option value="{{ $g->id }}" @selected($item->wali_kelas_id == $g->id)>{{ $g->nama_lengkap }}</option>
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
            <div class="card px-6 py-12 text-center sm:col-span-2 xl:col-span-3">
                <p class="text-xs text-slate-400 font-medium">Belum ada data kelas tersimpan.</p>
            </div>
        @endforelse
    </div>
    <p class="mt-3 text-[11px] text-slate-400 font-medium">Menampilkan {{ $kelas->count() }} dari {{ $kelas->total() }} kelas{{ $tahunAktif ? ' · jumlah siswa tahun '.$tahunAktif->tahun : '' }}.</p>
    @if($kelas->hasPages())
        <div class="mt-4">{{ $kelas->links() }}</div>
    @endif
@endsection
