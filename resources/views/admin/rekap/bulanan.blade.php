<x-app-layout>
    <!-- Breadcrumb & Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Rekap Bulanan</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Rekap Absensi Bulanan</h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Matriks kehadiran per siswa. H=hadir, T=terlambat, I=izin, S=sakit, A=alpa, -=belum ada record.</p>
        </div>
        <button onclick="window.print()" class="print:hidden inline-flex items-center justify-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shadow-sm transition-all">Cetak</button>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs p-4 mb-6 print:hidden">
        <form action="{{ route('admin.rekap.bulanan') }}" method="GET" class="grid grid-cols-2 md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Bulan</label>
                <input type="month" name="bulan" value="{{ $bulan }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
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
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">Tampilkan</button>
            </div>
        </form>
    </div>

    <!-- Matriks -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-4 py-3.5 sticky left-0 bg-slate-50/95 min-w-44">Nama</th>
                        @foreach($daftarTanggal as $tgl)
                            <th class="px-2 py-3.5 text-center min-w-9">{{ substr($tgl, 8, 2) }}</th>
                        @endforeach
                        <th class="px-2 py-3.5 text-center text-emerald-500">H</th>
                        <th class="px-2 py-3.5 text-center text-amber-500">T</th>
                        <th class="px-2 py-3.5 text-center text-sky-500">I</th>
                        <th class="px-2 py-3.5 text-center text-violet-500">S</th>
                        <th class="px-2 py-3.5 text-center text-rose-500">A</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($daftar as $s)
                        @php($peta = $s->absensiHarian->keyBy('tanggal'))
                        @php($jml = ['hadir' => 0, 'terlambat' => 0, 'izin' => 0, 'sakit' => 0, 'alpa' => 0])
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-4 py-3 font-bold text-slate-800 sticky left-0 bg-white min-w-44">{{ $s->nama_lengkap }}<span class="block text-[10px] font-medium text-slate-400">{{ $s->anggotaKelas->first()?->kelas?->nama_kelas ?? '-' }}</span></td>
                            @foreach($daftarTanggal as $tgl)
                                @php($st = $peta[$tgl]->status ?? null)
                                @if($st) @php($jml[$st]++) @endif
                                <td class="px-2 py-3 text-center font-bold
                                    {{ $st === 'hadir' ? 'text-emerald-600' : ($st === 'terlambat' ? 'text-amber-600' : ($st === 'izin' ? 'text-sky-600' : ($st === 'sakit' ? 'text-violet-600' : ($st === 'alpa' ? 'text-rose-600' : 'text-slate-300')))) }}">
                                    {{ $st ? ['hadir' => 'H', 'terlambat' => 'T', 'izin' => 'I', 'sakit' => 'S', 'alpa' => 'A'][$st] : '-' }}
                                </td>
                            @endforeach
                            <td class="px-2 py-3 text-center font-bold text-emerald-600">{{ $jml['hadir'] }}</td>
                            <td class="px-2 py-3 text-center font-bold text-amber-600">{{ $jml['terlambat'] }}</td>
                            <td class="px-2 py-3 text-center font-bold text-sky-600">{{ $jml['izin'] }}</td>
                            <td class="px-2 py-3 text-center font-bold text-violet-600">{{ $jml['sakit'] }}</td>
                            <td class="px-2 py-3 text-center font-bold text-rose-600">{{ $jml['alpa'] }}</td>
                        </tr>
                    @empty
                        <tr><td class="px-6 py-8 text-center text-slate-400">Belum ada data siswa pada filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($daftar->hasPages())
        <div class="mt-4">{{ $daftar->links() }}</div>
    @endif
</x-app-layout>
