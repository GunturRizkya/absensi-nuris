<x-app-layout>
    <!-- Breadcrumb & Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Rekap Harian</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Rekap Absensi Harian</h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Status kehadiran seluruh siswa pada tanggal terpilih.</p>
        </div>
        <div class="flex flex-wrap gap-2 print:hidden">
            <button onclick="window.print()" class="inline-flex items-center justify-center px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-xl shadow-sm transition-all">Cetak</button>
            <form action="{{ route('admin.rekap.auto-alpa') }}" method="POST" onsubmit="return confirm('Tandai alpa semua yang belum absen pada tanggal ini?')">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold rounded-xl shadow-sm transition-all">Jalankan Auto-Alpa</button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs p-4 mb-6 print:hidden">
        <form action="{{ route('admin.rekap.harian') }}" method="GET" class="grid grid-cols-2 md:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-600 mb-1">Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
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

    <!-- Ringkasan -->
    <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-6">
        @php($kartu = [
            'Hadir' => [$ringkasan['hadir'], 'text-emerald-500', 'text-emerald-600'],
            'Terlambat' => [$ringkasan['terlambat'], 'text-amber-500', 'text-amber-600'],
            'Izin' => [$ringkasan['izin'], 'text-sky-500', 'text-sky-600'],
            'Sakit' => [$ringkasan['sakit'], 'text-violet-500', 'text-violet-600'],
            'Alpa' => [$ringkasan['alpa'], 'text-rose-500', 'text-rose-600'],
            'Belum Absen' => [$ringkasan['belum'], 'text-slate-400', 'text-slate-500'],
        ])
        @foreach($kartu as $label => [$nilai, $t, $n])
            <div class="bg-white rounded-2xl border border-slate-200/70 p-4">
                <p class="text-[11px] font-bold {{ $t }} uppercase tracking-wider">{{ $label }}</p>
                <p class="text-xl font-bold {{ $n }}">{{ $nilai }}</p>
            </div>
        @endforeach
    </div>

    <!-- Tabel -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Nama / Kelas</th>
                        <th class="px-6 py-3.5 text-center">Status</th>
                        <th class="px-6 py-3.5">Masuk / Pulang</th>
                        <th class="px-6 py-3.5">Metode</th>
                        <th class="px-6 py-3.5">Keterangan</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($daftar as $s)
                        @php($a = $s->absensiHarian->first())
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $s->nama_lengkap }}<span class="block text-[11px] font-medium text-slate-400">{{ $s->anggotaKelas->first()?->kelas?->nama_kelas ?? '-' }}</span></td>
                            <td class="px-6 py-4 text-center">
                                @if($a)
                                    <span class="capitalize font-bold">{{ $a->status }}</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-500">belum absen</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $a?->jam_masuk ? substr($a->jam_masuk, 0, 5) : '-' }} / {{ $a?->jam_pulang ? substr($a->jam_pulang, 0, 5) : '-' }}</td>
                            <td class="px-6 py-4">{{ $a?->metode_masuk ?? '-' }}</td>
                            <td class="px-6 py-4 max-w-xs truncate" title="{{ $a?->keterangan }}">{{ $a?->keterangan ?? '-' }}@if($a?->korektor)<span class="block text-[10px] text-slate-400">dikoreksi oleh {{ $a->korektor->name }}</span>@endif</td>
                            <td class="px-6 py-4 text-right" x-data="{ koreksiModal: false }">
                                <button @click="koreksiModal = true" class="px-3 py-1 bg-amber-50 text-amber-600 hover:bg-amber-100 font-bold rounded-lg transition-colors text-[11px]">Koreksi</button>

                                <!-- Modal Koreksi -->
                                <div x-show="koreksiModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
                                    <div @click.outside="koreksiModal = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 text-left">
                                        <div class="flex items-center justify-between mb-1">
                                            <h3 class="text-base font-bold text-slate-800">Koreksi Absensi</h3>
                                            <button @click="koreksiModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                                        </div>
                                        <p class="text-xs text-slate-400 font-medium mb-4">{{ $s->nama_lengkap }} · {{ $tanggal }}@if($a) · saat ini: <span class="capitalize font-bold">{{ $a->status }}</span>@endif</p>
                                        <form action="{{ route('admin.rekap.koreksi') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="siswa_id" value="{{ $s->id }}">
                                            <input type="hidden" name="tanggal" value="{{ $tanggal }}">
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="label">Status Baru</label>
                                                    <select name="status" required class="input">
                                                        @foreach(['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpa' => 'Alpa'] as $nilai => $label)
                                                            <option value="{{ $nilai }}" @selected($a?->status === $nilai)>{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="label">Alasan Koreksi (wajib)</label>
                                                    <input type="text" name="alasan" required maxlength="255" placeholder="Contoh: surat susulan dari ortu" class="input">
                                                </div>
                                            </div>
                                            <div class="mt-6 flex justify-end gap-2">
                                                <button type="button" @click="koreksiModal = false" class="btn-secondary">Batal</button>
                                                <button type="submit" class="btn-primary">Simpan Koreksi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-6 py-8 text-center text-slate-400">Belum ada data siswa pada filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($daftar->hasPages())
        <div class="mt-4">{{ $daftar->links() }}</div>
    @endif
</x-app-layout>
