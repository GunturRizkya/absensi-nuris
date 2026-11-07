@extends('layouts.app')

@section('content')
<!-- Header -->
<div class="mb-6">
    <nav class="crumb">
        <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
        <span>/</span>
        <a href="{{ route('admin.siswa.index') }}" class="hover:text-blue-600">Data Siswa</a>
        <span>/</span>
        <span class="text-slate-700">{{ $siswa->nama_lengkap }}</span>
    </nav>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="page-title">{{ $siswa->nama_lengkap }}</h1>
            <p class="page-sub">NISN {{ $siswa->nisn }} · {{ $siswa->anggotaKelas->first()?->kelas?->nama_kelas ?? 'Belum ada kelas' }}</p>
        </div>
        <div class="flex gap-2 shrink-0">
            <a href="{{ route('admin.siswa.kartu', $siswa->id) }}" target="_blank" class="btn-secondary">Cetak Kartu</a>
            <a href="{{ route('admin.siswa.index', ['tahun_ajaran_id' => $tahunAjaranId]) }}" class="btn-secondary">Kembali</a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <!-- Biodata + QR -->
    <div class="space-y-4">
        <div class="card p-6 text-center">
            @if($siswa->foto)
                <img src="{{ asset($siswa->foto) }}" alt="Foto {{ $siswa->nama_lengkap }}" class="w-28 h-28 rounded-3xl object-cover mx-auto shadow-md">
            @else
                <div class="w-28 h-28 rounded-3xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white text-3xl font-extrabold flex items-center justify-center mx-auto shadow-md">
                    {{ collect(explode(' ', $siswa->nama_lengkap))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('') }}
                </div>
            @endif
            <p class="mt-3 font-extrabold text-slate-800">{{ $siswa->nama_lengkap }}</p>
            <p class="text-[11px] font-semibold text-slate-400">NISN {{ $siswa->nisn }}</p>
            <div class="mt-3 flex flex-wrap justify-center gap-1.5">
                <span class="badge bg-blue-50 text-blue-600 border-blue-200/60">{{ $siswa->anggotaKelas->first()?->kelas?->nama_kelas ?? '-' }}</span>
                <span class="badge bg-slate-100 text-slate-500 border-transparent">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
            </div>
            <dl class="mt-4 pt-4 border-t border-slate-100 text-left text-xs space-y-2.5">
                <div class="flex justify-between gap-3"><dt class="text-slate-400 font-bold">HP Ortu</dt><dd class="font-bold text-slate-700">{{ $siswa->no_hp_ortu ?? '-' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-400 font-bold">Kartu RFID</dt><dd class="font-mono font-bold text-slate-700">{{ $siswa->card_number ?? '-' }}</dd></div>
                <div class="flex justify-between gap-3"><dt class="text-slate-400 font-bold">Sidik Jari</dt><dd class="font-mono font-bold text-slate-700">{{ $siswa->fingerprint_id ?? '-' }}</dd></div>
            </dl>
        </div>

        <div class="card p-6 text-center">
            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-3">QR Kartu Pelajar</p>
            <div class="inline-block p-3 rounded-2xl border border-slate-200 bg-white [&>svg]:w-44 [&>svg]:h-44 [&>svg]:block">
                {!! (new \SimpleSoftwareIO\QrCode\Generator)->size(220)->generate($siswa->qr_code ?: $siswa->nisn) !!}
            </div>
            <p class="mt-3 font-mono text-xs font-bold text-slate-600">{{ $siswa->qr_code ?: $siswa->nisn }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Pindai dengan kamera halaman gerbang</p>
        </div>
    </div>

    <!-- Statistik + Riwayat -->
    <div class="lg:col-span-2 space-y-4">
        <div class="grid grid-cols-3 sm:grid-cols-6 gap-3">
            @php($kartu = [
                'Hadir' => [$ringkasan['hadir'], 'text-emerald-600', 'bg-emerald-50'],
                'Terlambat' => [$ringkasan['terlambat'], 'text-amber-600', 'bg-amber-50'],
                'Izin' => [$ringkasan['izin'], 'text-sky-600', 'bg-sky-50'],
                'Sakit' => [$ringkasan['sakit'], 'text-violet-600', 'bg-violet-50'],
                'Alpa' => [$ringkasan['alpa'], 'text-rose-600', 'bg-rose-50'],
                'Total' => [$ringkasan['total'], 'text-slate-800', 'bg-slate-100'],
            ])
            @foreach($kartu as $label => [$nilai, $t, $latar])
                <div class="card p-4 text-center">
                    <p class="text-xl font-extrabold {{ $t }}">{{ $nilai }}</p>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">{{ $label }}</p>
                </div>
            @endforeach
        </div>
        <p class="text-[11px] text-slate-400 font-medium -mt-1">Periode: tahun ajaran {{ $tahunAktif?->tahun ?? '-' }} semester {{ $tahunAktif?->semester ?? '-' }}</p>

        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Rekap Bulanan</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-5 py-3">Bulan</th>
                            <th class="px-3 py-3 text-center text-emerald-500">H</th>
                            <th class="px-3 py-3 text-center text-amber-500">T</th>
                            <th class="px-3 py-3 text-center text-sky-500">I</th>
                            <th class="px-3 py-3 text-center text-violet-500">S</th>
                            <th class="px-3 py-3 text-center text-rose-500">A</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-bold text-slate-700">
                        @forelse($rekapBulanan as $bulan => $baris)
                            @php($peta = $baris->pluck('total', 'status'))
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3">{{ $bulan }}</td>
                                <td class="px-3 py-3 text-center text-emerald-600">{{ $peta['hadir'] ?? 0 }}</td>
                                <td class="px-3 py-3 text-center text-amber-600">{{ $peta['terlambat'] ?? 0 }}</td>
                                <td class="px-3 py-3 text-center text-sky-600">{{ $peta['izin'] ?? 0 }}</td>
                                <td class="px-3 py-3 text-center text-violet-600">{{ $peta['sakit'] ?? 0 }}</td>
                                <td class="px-3 py-3 text-center text-rose-600">{{ $peta['alpa'] ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-5 py-6 text-center text-slate-400 font-medium">Belum ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-800">Riwayat Absensi Harian</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3 text-center">Status</th>
                            <th class="px-5 py-3">Masuk / Pulang</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                        @forelse($riwayat as $r)
                            <tr class="hover:bg-slate-50/50">
                                <td class="px-5 py-3 font-bold">{{ $r->tanggal }}</td>
                                <td class="px-5 py-3 text-center capitalize font-bold">{{ $r->status }}</td>
                                <td class="px-5 py-3">{{ $r->jam_masuk ? substr($r->jam_masuk, 0, 5) : '-' }} / {{ $r->jam_pulang ? substr($r->jam_pulang, 0, 5) : '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-5 py-8 text-center text-slate-400">Belum ada riwayat.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($riwayat->hasPages())
                <div class="px-5 py-3 border-t border-slate-100">{{ $riwayat->links() }}</div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800">Absensi Mapel Terakhir</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($riwayatMapel as $m)
                        <div class="px-5 py-3 text-xs">
                            <p class="font-bold text-slate-800">{{ $m->jadwal?->mataPelajaran?->nama_mapel ?? '-' }} <span class="font-medium text-slate-400">· {{ $m->tanggal }}</span></p>
                            <p class="text-slate-500 capitalize mt-0.5">{{ $m->status }} · {{ $m->jadwal?->guru?->nama_lengkap ?? '-' }}</p>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-center text-xs text-slate-400">Belum ada.</p>
                    @endforelse
                </div>
            </div>
            <div class="card overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-800">Surat Izin Terakhir</h2>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($suratIzin as $z)
                        <div class="px-5 py-3 text-xs">
                            <p class="font-bold text-slate-800 capitalize">{{ $z->jenis }} <span class="font-medium text-slate-400">· {{ $z->tanggal_mulai }} s/d {{ $z->tanggal_selesai }}</span></p>
                            <p class="text-slate-500 capitalize mt-0.5">{{ $z->status_persetujuan }}</p>
                        </div>
                    @empty
                        <p class="px-5 py-6 text-center text-xs text-slate-400">Belum ada.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
