<x-app-layout>
    <!-- Breadcrumb & Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Laporan BK</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Laporan Kehadiran BK</h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Rekapitulasi kehadiran siswa untuk pembinaan dan konseling.</p>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs p-4 mb-6">
        <form action="{{ route('bk.laporan.index') }}" method="GET" class="grid grid-cols-2 md:grid-cols-6 gap-3 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    @foreach($tahunAjaran as $ta)
                        <option value="{{ $ta->id }}" @selected($tahunAjaranId == $ta->id)>{{ $ta->tahun }} - Smt {{ $ta->semester }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Kelas</label>
                <select name="kelas_id" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" @selected($kelasId == $k->id)>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Dari</label>
                <input type="date" name="dari" value="{{ $dari }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Sampai</label>
                <input type="date" name="sampai" value="{{ $sampai }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Batas Alpa</label>
                <input type="number" name="batas_alpa" value="{{ $batasAlpa }}" min="1" max="31" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>
            <div>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">Tampilkan</button>
            </div>
        </form>
        <div class="mt-3 flex flex-wrap gap-2 text-[11px] font-bold">
            <a href="{{ route('bk.laporan.index', ['dari' => now()->toDateString(), 'sampai' => now()->toDateString(), 'kelas_id' => $kelasId, 'tahun_ajaran_id' => $tahunAjaranId]) }}" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-colors">Hari Ini</a>
            <a href="{{ route('bk.laporan.index', ['dari' => now()->subDays(6)->toDateString(), 'sampai' => now()->toDateString(), 'kelas_id' => $kelasId, 'tahun_ajaran_id' => $tahunAjaranId]) }}" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-colors">7 Hari</a>
            <a href="{{ route('bk.laporan.index', ['dari' => now()->startOfMonth()->toDateString(), 'sampai' => now()->toDateString(), 'kelas_id' => $kelasId, 'tahun_ajaran_id' => $tahunAjaranId]) }}" class="px-3 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-colors">Bulan Ini</a>
        </div>
        @error('sampai')
            <p class="mt-2 text-xs font-bold text-rose-600">{{ $message }}</p>
        @enderror
    </div>

    @unless($tahunAjaranId)
        <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold">Belum ada tahun ajaran aktif. Minta admin mengaktifkan tahun ajaran terlebih dahulu.</div>
    @endunless

    <!-- Ringkasan -->
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-3 mb-6">
        <div class="bg-white rounded-2xl border border-slate-200/70 p-4">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Siswa</p>
            <p class="text-xl font-bold text-slate-800">{{ $ringkasan['total_siswa'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/70 p-4">
            <p class="text-[11px] font-bold text-emerald-500 uppercase tracking-wider">Hadir</p>
            <p class="text-xl font-bold text-emerald-600">{{ $ringkasan['hadir'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/70 p-4">
            <p class="text-[11px] font-bold text-amber-500 uppercase tracking-wider">Terlambat</p>
            <p class="text-xl font-bold text-amber-600">{{ $ringkasan['terlambat'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/70 p-4">
            <p class="text-[11px] font-bold text-sky-500 uppercase tracking-wider">Izin</p>
            <p class="text-xl font-bold text-sky-600">{{ $ringkasan['izin'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/70 p-4">
            <p class="text-[11px] font-bold text-violet-500 uppercase tracking-wider">Sakit</p>
            <p class="text-xl font-bold text-violet-600">{{ $ringkasan['sakit'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200/70 p-4">
            <p class="text-[11px] font-bold text-rose-500 uppercase tracking-wider">Alpa</p>
            <p class="text-xl font-bold text-rose-600">{{ $ringkasan['alpa'] }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-rose-200 p-4">
            <p class="text-[11px] font-bold text-rose-400 uppercase tracking-wider">Perhatian BK</p>
            <p class="text-xl font-bold text-rose-600">{{ $perhatian->count() }}</p>
        </div>
    </div>

    <!-- Siswa Perhatian BK -->
    <div class="bg-white rounded-2xl border border-rose-200/70 shadow-2xs overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Siswa Perhatian BK <span class="text-xs font-medium text-slate-400">(alpa ≥ {{ $batasAlpa }}x pada periode)</span></h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-rose-50/50 border-b border-slate-200/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Nama</th>
                        <th class="px-6 py-3.5">Kelas</th>
                        <th class="px-6 py-3.5 text-center">Alpa</th>
                        <th class="px-6 py-3.5 text-center">Izin / Sakit</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($perhatian as $s)
                        <tr class="hover:bg-rose-50/40 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $s->nama_lengkap }}<span class="block text-[11px] font-medium text-slate-400">{{ $s->nisn }}</span></td>
                            <td class="px-6 py-4">{{ $s->anggotaKelas->first()?->kelas?->nama_kelas ?? '-' }}</td>
                            <td class="px-6 py-4 text-center font-bold text-rose-600">{{ $s->alpa_count }}x</td>
                            <td class="px-6 py-4 text-center">{{ $s->izin_count }} / {{ $s->sakit_count }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('bk.laporan.show', $s) }}" class="px-3 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold rounded-lg transition-colors text-[11px]">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Tidak ada siswa yang mencapai batas alpa. 🎉</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rekap Per Siswa -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Rekap Kehadiran Per Siswa</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Nama</th>
                        <th class="px-6 py-3.5">Kelas</th>
                        <th class="px-6 py-3.5 text-center">Hadir</th>
                        <th class="px-6 py-3.5 text-center">Terlambat</th>
                        <th class="px-6 py-3.5 text-center">Izin</th>
                        <th class="px-6 py-3.5 text-center">Sakit</th>
                        <th class="px-6 py-3.5 text-center">Alpa</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($rekap as $s)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $s->nama_lengkap }}<span class="block text-[11px] font-medium text-slate-400">{{ $s->nisn }}</span></td>
                            <td class="px-6 py-4">{{ $s->anggotaKelas->first()?->kelas?->nama_kelas ?? '-' }}</td>
                            <td class="px-6 py-4 text-center text-emerald-600 font-bold">{{ $s->hadir_count }}</td>
                            <td class="px-6 py-4 text-center text-amber-600 font-bold">{{ $s->terlambat_count }}</td>
                            <td class="px-6 py-4 text-center text-sky-600 font-bold">{{ $s->izin_count }}</td>
                            <td class="px-6 py-4 text-center text-violet-600 font-bold">{{ $s->sakit_count }}</td>
                            <td class="px-6 py-4 text-center text-rose-600 font-bold">{{ $s->alpa_count }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('bk.laporan.show', $s) }}" class="px-3 py-1 bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold rounded-lg transition-colors text-[11px]">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-6 py-8 text-center text-slate-400">Belum ada data siswa pada filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rekap->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $rekap->links() }}</div>
        @endif
    </div>

    <!-- Izin Menunggu Persetujuan -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100">
            <h2 class="text-sm font-bold text-slate-800">Surat Izin Menunggu Persetujuan</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Siswa</th>
                        <th class="px-6 py-3.5">Periode</th>
                        <th class="px-6 py-3.5">Jenis</th>
                        <th class="px-6 py-3.5">Alasan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($izinPending as $izin)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $izin->siswa->nama_lengkap ?? '-' }}</td>
                            <td class="px-6 py-4">{{ $izin->tanggal_mulai }} s/d {{ $izin->tanggal_selesai }}</td>
                            <td class="px-6 py-4 capitalize">{{ $izin->jenis }}</td>
                            <td class="px-6 py-4 max-w-xs truncate" title="{{ $izin->alasan }}">{{ $izin->alasan }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="px-6 py-8 text-center text-slate-400">Tidak ada surat izin yang menunggu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
