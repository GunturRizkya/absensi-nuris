@extends('layouts.app')

@section('content')
<!-- Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <nav class="crumb">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
            <span>/</span>
            <span class="text-slate-700">Tahun Ajaran</span>
        </nav>
        <h1 class="page-title">Manajemen Tahun Ajaran</h1>
        <p class="page-sub">Kelola tahun ajaran dan semester aktif untuk sistem presensi sekolah.</p>
    </div>

    <div x-data="{ openModal: false }">
        <button @click="openModal = true" class="btn-primary">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
            Tambah Tahun Ajaran
        </button>

        <!-- Modal Tambah -->
        <div x-show="openModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
            <div @click.outside="openModal = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-base font-bold text-slate-800">Tambah Tahun Ajaran Baru</h3>
                    <button @click="openModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                </div>

                <form action="{{ route('admin.tahun-ajaran.store') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="label">Tahun Ajaran</label>
                            <input type="text" name="tahun" placeholder="Contoh: 2026/2027" required class="input">
                        </div>
                        <div>
                            <label class="label">Semester</label>
                            <select name="semester" required class="input">
                                <option value="1">Semester 1 (Ganjil)</option>
                                <option value="2">Semester 2 (Genap)</option>
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

<!-- Alert -->
@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center justify-between gap-3">
        <span>{{ session('success') }}</span>
        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 text-base leading-none px-1">&times;</button>
    </div>
@endif

<!-- Filter -->
<div class="card p-4 mb-5">
    <form action="{{ route('admin.tahun-ajaran.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-52">
            <label class="label">Cari tahun ajaran</label>
            <input type="text" name="q" value="{{ $q }}" placeholder="Contoh: 2026/2027" class="input">
        </div>
        <button type="submit" class="btn-primary">Tampilkan</button>
        @if($q)
            <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn-secondary">Reset</a>
        @endif
    </form>
</div>

<!-- Bento Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
    @forelse($tahunAjaran as $item)
        <div class="card p-5 flex flex-col hover:shadow-md transition-shadow {{ $item->is_aktif ? 'ring-2 ring-emerald-400/60' : '' }}">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xl font-extrabold text-slate-800 tracking-tight">{{ $item->tahun }}</p>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Semester {{ $item->semester }}</p>
                </div>
                @if($item->is_aktif)
                    <span class="badge bg-emerald-50 text-emerald-600 border-emerald-200/60 shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5"></span>Aktif
                    </span>
                @else
                    <span class="badge bg-slate-100 text-slate-500 border-transparent shrink-0">Nonaktif</span>
                @endif
            </div>
            <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-end gap-1.5">
                @unless($item->is_aktif)
                    <form action="{{ route('admin.tahun-ajaran.set-aktif', $item->id) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn-blue-soft">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Set Aktif
                        </button>
                    </form>
                    <form action="{{ route('admin.tahun-ajaran.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger-soft" title="Hapus">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </form>
                @endunless
            </div>
        </div>
    @empty
        <div class="card px-6 py-12 text-center sm:col-span-2 xl:col-span-3">
            <p class="text-xs text-slate-400 font-medium">Belum ada data tahun ajaran.</p>
        </div>
    @endforelse
</div>
@if($tahunAjaran->hasPages())
    <div class="mt-4">{{ $tahunAjaran->links() }}</div>
@endif
@endsection
