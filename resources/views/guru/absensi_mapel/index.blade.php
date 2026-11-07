<x-app-layout>
    <!-- Breadcrumb & Header -->
    <div class="mb-6">
        <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-400 mb-1">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
            <span>/</span>
            <span class="text-slate-700">Absensi Mapel</span>
        </nav>
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Absensi Mata Pelajaran</h1>
        <p class="text-xs text-slate-400 font-medium mt-0.5">Jadwal mengajar Anda hari {{ $namaHari }}, {{ $tanggal }}.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    @unless($guruId)
        <div class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold">Akun Anda belum ditautkan ke data guru. Minta admin menautkannya di Data Guru.</div>
    @endunless

    <!-- Filter Tanggal -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs p-4 mb-6">
        <form action="{{ route('guru.absensi-mapel.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">Tampilkan Jadwal</button>
        </form>
    </div>

    <!-- Daftar Jadwal -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($jadwal as $j)
            <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="font-bold text-slate-800">{{ $j->mataPelajaran?->nama_mapel ?? '-' }}</p>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $j->kelas?->nama_kelas ?? '-' }} · {{ substr($j->jam_mulai, 0, 5) }} – {{ substr($j->jam_selesai, 0, 5) }}</p>
                    </div>
                    @if($j->terisi > 0)
                        <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200/60">Terisi {{ $j->terisi }}</span>
                    @else
                        <span class="shrink-0 inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200/60">Belum diisi</span>
                    @endif
                </div>
                <a href="{{ route('guru.absensi-mapel.form', ['jadwal' => $j->id, 'tanggal' => $tanggal]) }}" class="mt-4 block text-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl transition-colors">Isi Absensi</a>
            </div>
        @empty
            <div class="md:col-span-2 bg-white rounded-2xl border border-slate-200/70 p-8 text-center text-xs text-slate-400 font-medium">Tidak ada jadwal mengajar pada tanggal ini.</div>
        @endforelse
    </div>
</x-app-layout>
