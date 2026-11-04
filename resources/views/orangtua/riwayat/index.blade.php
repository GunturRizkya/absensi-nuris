<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-5">
            <nav class="crumb">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Riwayat Absensi</span>
            </nav>
            <h1 class="page-title">Riwayat Absensi Anak</h1>
            <p class="page-sub">H=hadir, T=terlambat, I=izin, S=sakit, A=alpa.</p>
        </div>

        <!-- Filter -->
        <div class="card p-4 mb-4">
            <form action="{{ route('orangtua.riwayat.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div class="sm:col-span-2">
                    <label class="label">Anak</label>
                    <select name="anak_id" class="input">
                        @foreach($anak as $a)
                            <option value="{{ $a->id }}" @selected($anakId == $a->id)>{{ $a->nama_lengkap }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Dari</label>
                    <input type="date" name="dari" value="{{ $dari }}" class="input">
                </div>
                <div>
                    <label class="label">Sampai</label>
                    <input type="date" name="sampai" value="{{ $sampai }}" class="input">
                </div>
                <div class="sm:col-span-2">
                    <button type="submit" class="btn-primary w-full py-3">Tampilkan</button>
                </div>
            </form>
        </div>

        <!-- Kartu Riwayat -->
        <div class="space-y-2.5">
            @forelse($riwayat as $r)
                <div class="card px-4 py-3.5 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-sm font-extrabold shrink-0
                        {{ $r->status === 'hadir' ? 'bg-emerald-50 text-emerald-600' : ($r->status === 'terlambat' ? 'bg-amber-50 text-amber-600' : ($r->status === 'izin' ? 'bg-sky-50 text-sky-600' : ($r->status === 'sakit' ? 'bg-violet-50 text-violet-600' : 'bg-rose-50 text-rose-600'))) }}">
                        {{ ['hadir' => 'H', 'terlambat' => 'T', 'izin' => 'I', 'sakit' => 'S', 'alpa' => 'A'][$r->status] ?? '?' }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-slate-800 capitalize">{{ $r->status }}</p>
                        <p class="text-[11px] text-slate-400">{{ $r->tanggal }}{{ $r->jam_masuk ? ' · masuk '.substr($r->jam_masuk, 0, 5) : '' }}{{ $r->jam_pulang ? ' · pulang '.substr($r->jam_pulang, 0, 5) : '' }}</p>
                        @if($r->keterangan)
                            <p class="text-[11px] text-slate-500 mt-0.5 truncate">{{ $r->keterangan }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="card px-5 py-10 text-center text-xs text-slate-400 font-medium">Belum ada riwayat pada periode ini.</div>
            @endforelse
        </div>
        @if($riwayat instanceof \Illuminate\Pagination\LengthAwarePaginator && $riwayat->hasPages())
            <div class="card mt-3 px-4 py-3">{{ $riwayat->links() }}</div>
        @endif
    </div>
</x-app-layout>
