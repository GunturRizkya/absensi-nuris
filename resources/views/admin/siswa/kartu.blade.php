<x-app-layout>
    <div class="max-w-3xl mx-auto print:max-w-none">
        <!-- Header (hilang saat cetak) -->
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 print:hidden">
            <div>
                <nav class="crumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                    <span>/</span>
                    <a href="{{ route('admin.siswa.index') }}" class="hover:text-blue-600">Data Siswa</a>
                    <span>/</span>
                    <a href="{{ route('admin.siswa.show', $siswa->id) }}" class="hover:text-blue-600">{{ $siswa->nama_lengkap }}</a>
                    <span>/</span>
                    <span class="text-slate-700">Kartu Siswa</span>
                </nav>
                <h1 class="page-title">Kartu Pelajar</h1>
                <p class="page-sub">Pratinjau di bawah sesuai ukuran cetak kartu (CR80).</p>
            </div>
            <button onclick="window.print()" class="btn-primary shrink-0">Cetak Kartu</button>
        </div>

        <!-- Kartu: depan + belakang -->
        <div id="kartu-cetak" class="grid grid-cols-1 md:grid-cols-2 gap-5 print:grid-cols-2 print:gap-5">
            <!-- DEPAN -->
            <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-md bg-white print:shadow-none print:break-inside-avoid">
                <div class="bg-gradient-to-r from-blue-700 to-blue-500 px-5 py-3.5 flex items-center gap-3 print:bg-blue-700">
                    <div class="w-9 h-9 rounded-xl bg-white/20 ring-1 ring-white/30 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-white text-sm font-extrabold tracking-tight">SMK NURUL ISLAM</p>
                        <p class="text-blue-100 text-[10px] font-bold tracking-widest">KARTU TANDA PELAJAR</p>
                    </div>
                </div>
                <div class="p-5 flex gap-4">
                    @if($siswa->foto)
                        <img src="{{ asset($siswa->foto) }}" alt="Foto {{ $siswa->nama_lengkap }}" class="w-24 h-28 rounded-xl object-cover border border-slate-200 shrink-0">
                    @else
                        <div class="w-24 h-28 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white text-2xl font-extrabold flex items-center justify-center shrink-0">
                            {{ collect(explode(' ', $siswa->nama_lengkap))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('') }}
                        </div>
                    @endif
                    <dl class="min-w-0 text-xs space-y-1.5">
                        <div><dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nama</dt><dd class="font-extrabold text-slate-800 leading-snug">{{ $siswa->nama_lengkap }}</dd></div>
                        <div><dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">NISN</dt><dd class="font-mono font-bold text-slate-700">{{ $siswa->nisn }}</dd></div>
                        <div><dt class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kelas</dt><dd class="font-bold text-slate-700">{{ $siswa->anggotaKelas->first()?->kelas?->nama_kelas ?? '-' }}</dd></div>
                    </dl>
                </div>
                <div class="px-5 py-2.5 bg-slate-50 border-t border-slate-100">
                    <p class="text-[10px] text-slate-400 font-medium">Kartu ini wajib dibawa setiap hari sekolah.</p>
                </div>
            </div>

            <!-- BELAKANG -->
            <div class="rounded-2xl overflow-hidden border border-slate-200 shadow-md bg-white print:shadow-none print:break-inside-avoid">
                <div class="p-5 text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pindai untuk absensi</p>
                    <div class="inline-block mt-2 p-3 rounded-2xl border border-slate-200 [&>svg]:w-44 [&>svg]:h-44 [&>svg]:block">
                        {!! (new \SimpleSoftwareIO\QrCode\Generator)->size(220)->generate($siswa->qr_code ?: $siswa->nisn) !!}
                    </div>
                    <p class="mt-2 font-mono text-xs font-bold text-slate-700">{{ $siswa->qr_code ?: $siswa->nisn }}</p>
                </div>
                <div class="px-5 py-3 bg-blue-700 print:bg-blue-700">
                    <p class="text-white text-[11px] font-bold text-center tracking-wide">SMK NURUL ISLAM · CIANJUR</p>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page { size: A4 landscape; margin: 12mm; }
        }
    </style>
</x-app-layout>
