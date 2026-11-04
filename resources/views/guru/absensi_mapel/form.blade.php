<x-app-layout>
    <!-- Breadcrumb & Header -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-400 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
            <span>/</span>
            <a href="{{ route('guru.absensi-mapel.index', ['tanggal' => $tanggal]) }}" class="hover:text-blue-600">Absensi Mapel</a>
            <span>/</span>
            <span class="text-slate-700">{{ $jadwal->mataPelajaran?->nama_mapel }}</span>
        </nav>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">{{ $jadwal->mataPelajaran?->nama_mapel ?? '-' }}</h1>
        <p class="text-xs text-slate-400 font-medium mt-0.5">{{ $jadwal->kelas?->nama_kelas ?? '-' }} · {{ $jadwal->hari }}, {{ $tanggal }} · {{ substr($jadwal->jam_mulai, 0, 5) }} – {{ substr($jadwal->jam_selesai, 0, 5) }}</p>
    </div>

    <form action="{{ route('guru.absensi-mapel.simpan', $jadwal->id) }}" method="POST">
        @csrf
        <input type="hidden" name="tanggal" value="{{ $tanggal }}">

        <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/70 border-b border-slate-200/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="px-6 py-3.5">Nama</th>
                            <th class="px-4 py-3.5 text-center">Hadir</th>
                            <th class="px-4 py-3.5 text-center">Izin</th>
                            <th class="px-4 py-3.5 text-center">Sakit</th>
                            <th class="px-4 py-3.5 text-center">Alpa</th>
                            <th class="px-4 py-3.5 text-center">Cabut</th>
                            <th class="px-6 py-3.5">Catatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                        @forelse($siswa as $s)
                            @php($lama = $terisi[$s->id] ?? null)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-6 py-3 font-bold text-slate-800">{{ $s->nama_lengkap }}<span class="block text-[11px] font-medium text-slate-400">{{ $s->nisn }}</span></td>
                                @foreach(['hadir', 'izin', 'sakit', 'alpa', 'cabut'] as $st)
                                    <td class="px-4 py-3 text-center">
                                        <input type="radio" name="status[{{ $s->id }}]" value="{{ $st }}" @checked(($lama?->status ?? 'hadir') === $st) class="w-4 h-4 accent-blue-600 cursor-pointer">
                                    </td>
                                @endforeach
                                <td class="px-6 py-3">
                                    <input type="text" name="catatan[{{ $s->id }}]" value="{{ $lama?->catatan }}" placeholder="—" class="w-full px-2.5 py-1.5 text-xs border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-6 py-8 text-center text-slate-400">Tidak ada siswa di kelas ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($siswa->isNotEmpty())
            <div class="mt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-sm transition-colors">Simpan Absensi ({{ $siswa->count() }} siswa)</button>
            </div>
        @endif
    </form>
</x-app-layout>
