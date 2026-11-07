<x-app-layout>
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="mb-5 flex items-start justify-between gap-3">
            <div>
                <nav class="crumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-700">Surat Izin</span>
                </nav>
                <h1 class="page-title">Pengajuan Surat Izin</h1>
                <p class="page-sub">Berkas bukti maksimal 2 MB (JPG/PNG/PDF).</p>
            </div>
            <div x-data="{ openModal: false }" class="shrink-0">
                <button @click="openModal = true" class="btn-primary whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Ajukan
                </button>

                <div x-show="openModal" class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-slate-900/50 backdrop-blur-xs sm:px-4" x-cloak>
                    <div @click.outside="openModal = false" class="bg-white rounded-t-3xl sm:rounded-2xl w-full sm:max-w-md p-5 sm:p-6 shadow-xl max-h-[92vh] overflow-y-auto">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-slate-800">Surat Izin / Sakit Baru</h3>
                            <button @click="openModal = false" class="w-9 h-9 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-xl">&times;</button>
                        </div>
                        <form action="{{ route('orangtua.surat-izin.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="label">Anak</label>
                                    <select name="siswa_id" required class="input py-3">
                                        @foreach($anak as $a)
                                            <option value="{{ $a->id }}">{{ $a->nama_lengkap }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">Dari</label>
                                        <input type="date" name="tanggal_mulai" required class="input py-3">
                                    </div>
                                    <div>
                                        <label class="label">Sampai</label>
                                        <input type="date" name="tanggal_selesai" required class="input py-3">
                                    </div>
                                </div>
                                <div>
                                    <label class="label">Jenis</label>
                                    <select name="jenis" required class="input py-3">
                                        <option value="izin">Izin</option>
                                        <option value="sakit">Sakit</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="label">Alasan</label>
                                    <textarea name="alasan" required rows="3" class="input"></textarea>
                                </div>
                                <div>
                                    <label class="label">Bukti (opsional)</label>
                                    <input type="file" name="file_bukti" accept=".jpg,.jpeg,.png,.pdf" class="w-full text-xs text-slate-600 file:mr-3 file:px-4 file:py-2.5 file:text-xs file:font-bold file:bg-slate-100 file:hover:bg-slate-200 file:text-slate-700 file:rounded-xl file:border-0">
                                </div>
                            </div>
                            <div class="mt-6 grid grid-cols-2 gap-3">
                                <button type="button" @click="openModal = false" class="btn-secondary py-3">Batal</button>
                                <button type="submit" class="btn-primary py-3">Ajukan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center justify-between gap-3">
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 text-base leading-none px-1">&times;</button>
            </div>
        @endif

        <!-- Kartu Pengajuan -->
        <div class="space-y-2.5">
            @forelse($surat as $item)
                <div class="card px-4 py-3.5">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-slate-800">{{ $item->siswa->nama_lengkap ?? '-' }}</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">{{ $item->tanggal_mulai }} s/d {{ $item->tanggal_selesai }} · <span class="capitalize">{{ $item->jenis }}</span></p>
                        </div>
                        <span class="badge shrink-0 capitalize {{ $item->status_persetujuan === 'disetujui' ? 'bg-emerald-50 text-emerald-600 border-emerald-200/60' : ($item->status_persetujuan === 'ditolak' ? 'bg-rose-50 text-rose-600 border-rose-200/60' : 'bg-amber-50 text-amber-600 border-amber-200/60') }}">{{ $item->status_persetujuan }}</span>
                    </div>
                    @if($item->penyetuju)
                        <p class="text-[11px] text-slate-400 mt-1.5">Oleh {{ $item->penyetuju->name }}</p>
                    @endif
                </div>
            @empty
                <div class="card px-5 py-10 text-center text-xs text-slate-400 font-medium">Belum ada pengajuan.</div>
            @endforelse
        </div>
        @if($surat->hasPages())
            <div class="card mt-3 px-4 py-3">{{ $surat->links() }}</div>
        @endif
    </div>
</x-app-layout>
