@extends('layouts.app')

@section('content')
<!-- Header -->
<div class="mb-6">
    <nav class="crumb">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <span class="text-slate-700">Pengaturan Jam Absensi</span>
    </nav>
    <h1 class="page-title">Pengaturan Jam & Toleransi Gerbang</h1>
    <p class="page-sub">Atur batasan jam masuk, jam pulang, serta toleransi keterlambatan siswa secara presisi.</p>
</div>

<!-- Alert -->
@if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center justify-between gap-3">
        <span>{{ session('success') }}</span>
        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 text-base leading-none px-1">&times;</button>
    </div>
@endif

<!-- Bento Grid Hari -->
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @foreach($pengaturan as $item)
        <div class="card p-5 hover:shadow-md transition-shadow" x-data="{ editHari: false }">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl {{ $item->is_aktif ? 'bg-gradient-to-br from-blue-500 to-indigo-500' : 'bg-slate-200' }} text-white font-extrabold flex items-center justify-center text-sm shadow-sm shrink-0">
                        {{ substr($item->hari, 0, 2) }}
                    </div>
                    <div>
                        <p class="font-bold text-slate-800 text-sm">{{ $item->hari }}</p>
                        <p class="text-[11px] font-semibold text-slate-400">Masuk {{ substr($item->jam_masuk, 0, 5) }} · Pulang {{ substr($item->jam_pulang, 0, 5) }}</p>
                    </div>
                </div>
                <span class="badge shrink-0 {{ $item->is_aktif ? 'bg-emerald-50 text-emerald-600 border-emerald-200/60' : 'bg-slate-100 text-slate-500 border-transparent' }}">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $item->is_aktif ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>{{ $item->is_aktif ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <!-- Mini timeline -->
            <div class="mt-4 space-y-2 text-xs font-medium text-slate-600">
                <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50">
                    <span class="text-slate-400 font-bold">Toleransi terlambat</span>
                    <span class="font-bold text-slate-800">+{{ $item->toleransi_keterlambatan }} mnt</span>
                </div>
                <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50">
                    <span class="text-slate-400 font-bold">Tutup absen masuk</span>
                    <span class="font-bold text-slate-800">{{ substr($item->jam_tutup_absensi, 0, 5) }}</span>
                </div>
                <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50">
                    <span class="text-slate-400 font-bold">Jendela absen pulang</span>
                    <span class="font-bold text-slate-800">{{ $item->durasi_absensi_pulang }} mnt</span>
                </div>
            </div>

            <button @click="editHari = true" class="btn-secondary w-full mt-4">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Ubah Pengaturan
            </button>

            <!-- Modal Edit -->
            <div x-show="editHari" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
                <div @click.outside="editHari = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-slate-800">Pengaturan Hari {{ $item->hari }}</h3>
                        <button @click="editHari = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                    </div>
                    <form action="{{ route('admin.pengaturan-absensi.update', $item->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="label">Jam Masuk</label>
                                    <input type="time" name="jam_masuk" value="{{ substr($item->jam_masuk, 0, 5) }}" required class="input">
                                </div>
                                <div>
                                    <label class="label">Toleransi (mnt)</label>
                                    <input type="number" name="toleransi_keterlambatan" value="{{ $item->toleransi_keterlambatan }}" required min="0" class="input">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="label">Jam Tutup Absen</label>
                                    <input type="time" name="jam_tutup_absensi" value="{{ substr($item->jam_tutup_absensi, 0, 5) }}" required class="input">
                                </div>
                                <div>
                                    <label class="label">Jam Pulang</label>
                                    <input type="time" name="jam_pulang" value="{{ substr($item->jam_pulang, 0, 5) }}" required class="input">
                                </div>
                            </div>
                            <div>
                                <label class="label">Durasi Absen Pulang (mnt)</label>
                                <input type="number" name="durasi_absensi_pulang" value="{{ $item->durasi_absensi_pulang }}" required min="0" class="input">
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end gap-2">
                            <button type="button" @click="editHari = false" class="btn-secondary">Batal</button>
                            <button type="submit" class="btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection
