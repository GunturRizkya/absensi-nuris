@extends('layouts.app')

@section('content')
@php($stat = [
    ['Total Siswa'.($tahunAktif ? ' · '.$tahunAktif->tahun : ''), $totalSiswa, 'text-slate-800', 'bg-slate-100', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
    ['Hadir Hari Ini', $hari['hadir'], 'text-emerald-600', 'bg-emerald-50', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['Terlambat', $hari['terlambat'], 'text-amber-600', 'bg-amber-50', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
    ['Izin / Sakit / Alpa', $hari['izin'] + $hari['sakit'] + $hari['alpa'], 'text-rose-600', 'bg-rose-50', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z'],
    ['Izin Menunggu', $izinPending, 'text-blue-600', 'bg-blue-50', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
    ['Tanggal', $hariIni, 'text-slate-800', 'bg-slate-100', 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
])

<!-- Header -->
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <div>
        <h1 class="page-title">Dashboard Utama</h1>
        <p class="page-sub">Ringkasan kehadiran hari ini, {{ $hariIni }}.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        @if(auth()->user()->role === 'piket' || auth()->user()->role === 'admin')
            <a href="{{ route('piket.absensi.index') }}" class="btn-primary">Buka Absensi Gerbang</a>
        @endif
        @if(auth()->user()->bertugasSebagai('bk'))
            <a href="{{ route('bk.laporan.index') }}" class="btn-primary !bg-violet-600 hover:!bg-violet-700">Buka Laporan BK</a>
        @endif
    </div>
</div>

<!-- Statistik -->
<div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
    @foreach($stat as [$label, $nilai, $warna, $latar, $ikon])
        <div class="card p-5 hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl {{ $latar }} flex items-center justify-center mb-3">
                <svg class="w-5 h-5 {{ $warna }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ikon }}"/></svg>
            </div>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</p>
            <p class="text-2xl font-extrabold {{ $warna }} mt-1 tracking-tight">{{ $nilai }}</p>
        </div>
    @endforeach
</div>

<!-- Sapaan -->
<div class="card p-6">
    <h3 class="text-base font-bold text-slate-800">Selamat Datang di Sistem Absensi SMK</h3>
    <p class="text-slate-500 text-xs mt-1">Gunakan menu di sidebar untuk mengelola master data, jadwal pelajaran, atau melihat rekapitulasi presensi.</p>
</div>

@if(! empty($anak) && $anak->isNotEmpty())
    <div class="card p-6 mt-4">
        <h3 class="text-base font-bold text-slate-800 mb-4">Kehadiran Anak Hari Ini ({{ $hariIni }})</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($anak as $a)
                @php($absen = $a->absensiHarian->first())
                <div class="p-4 rounded-xl border border-slate-100 bg-slate-50/50">
                    <p class="font-bold text-slate-800 text-sm">{{ $a->nama_lengkap }}</p>
                    <p class="text-[11px] text-slate-400 mb-2">{{ $a->anggotaKelas->first()?->kelas?->nama_kelas ?? '-' }}</p>
                    @if($absen)
                        <span class="badge capitalize bg-emerald-50 text-emerald-600 border-emerald-200/60">{{ $absen->status }}{{ $absen->jam_masuk ? ' · '.substr($absen->jam_masuk, 0, 5) : '' }}</span>
                    @else
                        <span class="badge bg-slate-100 text-slate-500 border-transparent">belum ada record</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endif

@if(! empty($jadwalHariIni) && $jadwalHariIni->isNotEmpty())
    <div class="card p-6 mt-4">
        <h3 class="text-base font-bold text-slate-800 mb-4">Jadwal Mengajar Hari Ini</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-[11px] text-slate-400 uppercase tracking-wider">
                        <th class="py-2 pr-4 font-bold">Jam</th>
                        <th class="py-2 pr-4 font-bold">Kelas</th>
                        <th class="py-2 font-bold">Mata Pelajaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($jadwalHariIni as $j)
                        <tr>
                            <td class="py-2.5 pr-4 font-bold text-slate-800">{{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}</td>
                            <td class="py-2.5 pr-4">{{ $j->kelas?->nama_kelas ?? '-' }}</td>
                            <td class="py-2.5">{{ $j->mataPelajaran?->nama_mapel ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

@if(auth()->user()->isWaliKelas() && $pendingWali > 0)
    <div class="mt-4 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-xs font-bold text-amber-700 flex items-center justify-between gap-3">
        <span>{{ $pendingWali }} surat izin kelas Anda menunggu persetujuan.</span>
        <a href="{{ route('walikelas.surat-izin.index') }}" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-bold rounded-xl transition-colors shrink-0">Proses</a>
    </div>
@endif
@endsection
