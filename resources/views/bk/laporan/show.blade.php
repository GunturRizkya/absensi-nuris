<x-app-layout>
    <!-- Breadcrumb & Header -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-400 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
            <span>/</span>
            <a href="{{ route('bk.laporan.index') }}" class="hover:text-blue-600">Laporan BK</a>
            <span>/</span>
            <span class="text-slate-700">{{ $siswa->nama_lengkap }}</span>
        </nav>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $siswa->nama_lengkap }}</h1>
        <p class="text-xs text-slate-400 font-medium mt-0.5">
            NISN {{ $siswa->nisn }} ·
            {{ $siswa->anggotaKelas->first()?->kelas?->nama_kelas ?? 'Belum ada kelas' }} ·
            Periode {{ $dari }} s/d {{ $sampai }}
        </p>
    </div>

    <!-- Filter Periode -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs p-4 mb-6">
        <form action="{{ route('bk.laporan.show', $siswa) }}" method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Dari</label>
                <input type="date" name="dari" value="{{ $dari }}" class="px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Sampai</label>
                <input type="date" name="sampai" value="{{ $sampai }}" class="px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">Tampilkan</button>
        </form>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200/70 p-4"><p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total</p><p class="text-xl font-bold text-slate-800">{{ $ringkasan['total'] }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200/70 p-4"><p class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider">Hadir</p><p class="text-xl font-bold text-emerald-600">{{ $ringkasan['hadir'] }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200/70 p-4"><p class="text-[11px] font-bold text-amber-500 uppercase tracking-wider">Terlambat</p><p class="text-xl font-bold text-amber-600">{{ $ringkasan['terlambat'] }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200/70 p-4"><p class="text-[11px] font-bold text-sky-500 uppercase tracking-wider">Izin</p><p class="text-xl font-bold text-sky-600">{{ $ringkasan['izin'] }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200/70 p-4"><p class="text-[11px] font-bold text-violet-500 uppercase tracking-wider">Sakit</p><p class="text-xl font-bold text-violet-600">{{ $ringkasan['sakit'] }}</p></div>
        <div class="bg-white rounded-2xl border border-slate-200/70 p-4"><p class="text-[11px] font-bold text-rose-500 uppercase tracking-wider">Alpa</p><p class="text-xl font-bold text-rose-600">{{ $ringkasan['alpa'] }}</p></div>
    </div>

    <!-- Riwayat Harian -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100"><h2 class="text-sm font-bold text-slate-800">Riwayat Absensi Harian</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Masuk / Pulang</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Metode</th>
                        <th class="px-6 py-3.5">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($riwayat as $r)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $r->tanggal }}</td>
                            <td class="px-6 py-4">{{ $r->jam_masuk ?? '-' }} / {{ $r->jam_pulang ?? '-' }}</td>
                            <td class="px-6 py-4 capitalize">{{ $r->status }}</td>
                            <td class="px-6 py-4">{{ $r->metode_masuk ?? '-' }}</td>
                            <td class="px-6 py-4 max-w-xs truncate" title="{{ $r->keterangan }}">{{ $r->keterangan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada riwayat pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $riwayat->appends(request()->query())->links() }}</div>
        @endif
    </div>

    <!-- Riwayat Per Mapel -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100"><h2 class="text-sm font-bold text-slate-800">Absensi Per Mata Pelajaran <span class="text-xs font-medium text-slate-400">(20 terakhir)</span></h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Tanggal</th>
                        <th class="px-6 py-3.5">Mapel</th>
                        <th class="px-6 py-3.5">Guru</th>
                        <th class="px-6 py-3.5">Status</th>
                        <th class="px-6 py-3.5">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($riwayatMapel as $m)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">{{ $m->tanggal }}</td>
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $m->jadwal?->mataPelajaran?->nama_mapel ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $m->jadwal?->guru?->nama_lengkap ?? ($m->pencatat?->nama_lengkap ?? '-') }}</td>
                            <td class="px-6 py-4 capitalize">{{ $m->status }}</td>
                            <td class="px-6 py-4 max-w-xs truncate" title="{{ $m->catatan }}">{{ $m->catatan ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Belum ada absensi mapel pada periode ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Surat Izin -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100"><h2 class="text-sm font-bold text-slate-800">Surat Izin / Sakit</h2></div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Periode</th>
                        <th class="px-6 py-3.5">Jenis</th>
                        <th class="px-6 py-3.5">Alasan</th>
                        <th class="px-6 py-3.5">Persetujuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($suratIzin as $izin)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">{{ $izin->tanggal_mulai }} s/d {{ $izin->tanggal_selesai }}</td>
                            <td class="px-6 py-4 capitalize">{{ $izin->jenis }}</td>
                            <td class="px-6 py-4 max-w-xs truncate" title="{{ $izin->alasan }}">{{ $izin->alasan }}</td>
                            <td class="px-6 py-4 capitalize">{{ $izin->status_persetujuan }}@if($izin->penyetuju) oleh {{ $izin->penyetuju->name }}@endif</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">Belum ada surat izin.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
