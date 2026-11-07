<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-5 flex items-start justify-between gap-3">
            <div>
                <nav class="crumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-700">Notifikasi</span>
                </nav>
                <h1 class="page-title">Notifikasi Kehadiran</h1>
                <p class="page-sub">Kabar kehadiran anak langsung dari sekolah.</p>
            </div>
            @if($belumDibaca > 0)
                <form action="{{ route('orangtua.notifikasi.baca-semua') }}" method="POST" class="shrink-0">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn-primary whitespace-nowrap">Baca Semua ({{ $belumDibaca }})</button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center justify-between gap-3">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-base leading-none px-1">&times;</button>
            </div>
        @endif

        <!-- Daftar -->
        <div class="card overflow-hidden">
            <div class="divide-y divide-slate-100">
                @forelse($notifikasi as $n)
                    <div class="px-4 sm:px-5 py-4 {{ $n->dibaca_pada ? '' : 'bg-blue-50/60' }}">
                        <div class="flex gap-3">
                            <div class="mt-1.5 w-2.5 h-2.5 rounded-full shrink-0 {{ $n->dibaca_pada ? 'bg-slate-200' : 'bg-blue-500' }}"></div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-bold text-slate-800">{{ $n->siswa->nama_lengkap ?? '-' }}</p>
                                <p class="text-[13px] text-slate-600 mt-1 leading-relaxed">{{ $n->pesan }}</p>
                                <p class="text-[11px] text-slate-400 mt-1.5">{{ $n->tanggal }}</p>
                                @unless($n->dibaca_pada)
                                    <form action="{{ route('orangtua.notifikasi.baca', $n->id) }}" method="POST" class="mt-2.5">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-blue-soft w-full sm:w-auto py-2">Tandai Sudah Dibaca</button>
                                    </form>
                                @endunless
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-10 text-center text-xs text-slate-400 font-medium">Belum ada notifikasi.</p>
                @endforelse
            </div>
            @if($notifikasi->hasPages())
                <div class="px-4 py-3 border-t border-slate-100">{{ $notifikasi->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
