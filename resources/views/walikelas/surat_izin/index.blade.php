<x-app-layout>
    <!-- Breadcrumb & Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="flex items-center space-x-2 text-xs font-semibold text-slate-400 mb-1">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Persetujuan Surat Izin</span>
            </nav>
            <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Persetujuan Surat Izin</h1>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Menyetujui otomatis memperbarui absensi harian menjadi izin/sakit.</p>
        </div>
        <div class="px-4 py-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold">
            {{ $jumlahPending }} menunggu persetujuan
        </div>
    </div>

    <!-- Alert -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs p-4 mb-6">
        <form action="{{ route('walikelas.surat-izin.index') }}" method="GET" class="flex flex-wrap gap-3 items-end">
            <div class="flex gap-2">
                @foreach(['pending' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'] as $nilai => $label)
                    <a href="{{ route('walikelas.surat-izin.index', ['status' => $nilai, 'kelas_id' => $kelasId]) }}" class="px-3 py-2 text-xs font-bold rounded-xl transition-colors {{ $status === $nilai ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">{{ $label }}</a>
                @endforeach
            </div>
            <div>
                <select name="kelas_id" onchange="this.form.submit()" class="px-3.5 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 bg-white font-bold text-slate-600">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasOptions as $k)
                        <option value="{{ $k->id }}" @selected($kelasId == $k->id)>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <input type="hidden" name="status" value="{{ $status }}">
        </form>
    </div>

    <!-- Tabel -->
    <div class="bg-white rounded-2xl border border-slate-200/70 shadow-2xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/70 border-b border-slate-200/70 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="px-6 py-3.5">Siswa / Kelas</th>
                        <th class="px-6 py-3.5">Periode</th>
                        <th class="px-6 py-3.5">Jenis / Alasan</th>
                        <th class="px-6 py-3.5">Bukti</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($surat as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $item->siswa->nama_lengkap ?? '-' }}<span class="block text-[11px] font-medium text-slate-400">{{ $item->siswa->anggotaKelas->first()?->kelas?->nama_kelas ?? '-' }}</span></td>
                            <td class="px-6 py-4">{{ $item->tanggal_mulai }} s/d {{ $item->tanggal_selesai }}</td>
                            <td class="px-6 py-4"><span class="capitalize font-bold">{{ $item->jenis }}</span><span class="block text-[11px] font-medium text-slate-400 max-w-xs truncate" title="{{ $item->alasan }}">{{ $item->alasan }}</span></td>
                            <td class="px-6 py-4">
                                @if($item->file_bukti)
                                    <a href="{{ asset($item->file_bukti) }}" target="_blank" class="text-blue-600 hover:underline font-bold">Lihat</a>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($item->status_persetujuan === 'pending')
                                    <form action="{{ route('walikelas.surat-izin.setujui', $item->id) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-3 py-1 bg-emerald-50 text-emerald-600 hover:bg-emerald-100 font-bold rounded-lg transition-colors text-[11px]">Setujui</button>
                                    </form>
                                    <form action="{{ route('walikelas.surat-izin.tolak', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak surat izin ini?')">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="px-3 py-1 bg-rose-50 text-rose-600 hover:bg-rose-100 font-bold rounded-lg transition-colors text-[11px]">Tolak</button>
                                    </form>
                                @else
                                    <span class="capitalize text-slate-400">{{ $item->status_persetujuan }}@if($item->penyetuju) oleh {{ $item->penyetuju->name }}@endif</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-8 text-center text-slate-400">Tidak ada surat pada filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($surat->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">{{ $surat->links() }}</div>
        @endif
    </div>
</x-app-layout>
