<x-app-layout>
    <div x-data="{ tab: '{{ in_array($hariAktif, $daftarHari) ? $hariAktif : ($daftarHari[0] ?? 'Senin') }}' }">
        <!-- Header -->
        <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <nav class="crumb">
                    <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                    <span>/</span>
                    <span class="text-slate-700">Jadwal Pelajaran</span>
                </nav>
                <h1 class="page-title">Manajemen Jadwal Pelajaran</h1>
                <p class="page-sub">Acuan pencocokan absensi per mata pelajaran.</p>
            </div>

            <div x-data="{ openModal: false }">
                <button @click="openModal = true" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    Tambah Jadwal
                </button>

                <!-- Modal Tambah -->
                <div x-show="openModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
                    <div @click.outside="openModal = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-slate-800">Tambah Jadwal Baru</h3>
                            <button @click="openModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                        </div>

                        <form action="{{ route('admin.jadwal.store') }}" method="POST">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label class="label">Tahun Ajaran</label>
                                    <select name="tahun_ajaran_id" required class="input">
                                        @foreach($tahunAjaran as $ta)
                                            <option value="{{ $ta->id }}" @selected($tahunAjaranId == $ta->id)>{{ $ta->tahun }} - Smt {{ $ta->semester }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">Kelas</label>
                                        <select name="kelas_id" required class="input">
                                            @foreach($kelas as $k)
                                                <option value="{{ $k->id }}" @selected($kelasId == $k->id)>{{ $k->nama_kelas }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="label">Hari</label>
                                        <select name="hari" required class="input">
                                            @foreach($daftarHari as $h)
                                                <option value="{{ $h }}">{{ $h }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="label">Mata Pelajaran</label>
                                    <select name="mata_pelajaran_id" required class="input">
                                        @foreach($mapel as $m)
                                            <option value="{{ $m->id }}">{{ $m->nama_mapel }} ({{ $m->kode_mapel }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="label">Guru Pengampu</label>
                                    <select name="guru_id" required class="input">
                                        @foreach($guru as $g)
                                            <option value="{{ $g->id }}">{{ $g->nama_lengkap }}</option>
                                        @endforeach
                                    </select>
                                    @if($guru->isEmpty())
                                        <p class="mt-1 text-[11px] font-bold text-amber-600">Belum ada data guru — tambahkan dulu di Data Guru.</p>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">Jam Mulai</label>
                                        <input type="time" name="jam_mulai" required class="input">
                                    </div>
                                    <div>
                                        <label class="label">Jam Selesai</label>
                                        <input type="time" name="jam_selesai" required class="input">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-2">
                                <button type="button" @click="openModal = false" class="btn-secondary">Batal</button>
                                <button type="submit" class="btn-primary">Simpan Data</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center justify-between gap-3">
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 text-base leading-none px-1">&times;</button>
            </div>
        @endif

        <!-- Filter konteks -->
        <div class="card p-4 mb-4">
            <form action="{{ route('admin.jadwal.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end">
                <div>
                    <label class="label">Tahun Ajaran</label>
                    <select name="tahun_ajaran_id" class="input">
                        @foreach($tahunAjaran as $ta)
                            <option value="{{ $ta->id }}" @selected($tahunAjaranId == $ta->id)>{{ $ta->tahun }} - Smt {{ $ta->semester }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Kelas</label>
                    <select name="kelas_id" class="input">
                        <option value="">Semua Kelas</option>
                        @foreach($kelas as $k)
                            <option value="{{ $k->id }}" @selected($kelasId == $k->id)>{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-primary">Tampilkan</button>
            </form>
        </div>

        <!-- Tabulasi Hari -->
        <div class="flex gap-1.5 overflow-x-auto pb-1 mb-4">
            @foreach($daftarHari as $h)
                <button @click="tab = '{{ $h }}'" :class="tab === '{{ $h }}' ? 'bg-blue-600 text-white shadow-sm shadow-blue-500/20' : 'bg-white text-slate-500 border border-slate-200/80 hover:text-blue-600'" class="shrink-0 px-4 py-2 text-xs font-bold rounded-xl transition-all">
                    {{ $h }}
                    <span class="ml-1.5 text-[10px] opacity-70">{{ $jadwal->where('hari', $h)->count() }}</span>
                </button>
            @endforeach
        </div>

        @foreach($daftarHari as $h)
            <div x-show="tab === '{{ $h }}'" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($jadwal->where('hari', $h)->groupBy('kelas_id') as $jadwalKelas)
                    @php($pertama = $jadwalKelas->first())
                    <div class="card overflow-hidden">
                        <div class="px-5 py-3.5 bg-blue-50/60 border-b border-blue-100/60 flex items-center justify-between gap-3">
                            <p class="font-extrabold text-slate-800 text-sm tracking-tight">{{ $pertama->kelas?->nama_kelas ?? '-' }}</p>
                            <span class="badge bg-blue-600 text-white border-transparent shrink-0">{{ $jadwalKelas->count() }} jam</span>
                        </div>
                        <div class="divide-y divide-slate-100">
                            @foreach($jadwalKelas->sortBy('jam_mulai') as $item)
                                <div class="px-5 py-3 flex items-center gap-3 group" x-data="{ editModal: false }">
                                    <div class="text-center shrink-0 w-14">
                                        <p class="text-xs font-extrabold text-blue-600">{{ substr($item->jam_mulai, 0, 5) }}</p>
                                        <p class="text-[10px] font-medium text-slate-400">{{ substr($item->jam_selesai, 0, 5) }}</p>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-bold text-slate-800 truncate">{{ $item->mataPelajaran?->nama_mapel ?? '-' }}</p>
                                        <p class="text-[11px] text-slate-400 truncate">{{ $item->guru?->nama_lengkap ?? '-' }}</p>
                                    </div>
                                    <div class="flex gap-1 opacity-100 md:opacity-0 md:group-hover:opacity-100 focus-within:opacity-100 transition-opacity shrink-0">
                                        <button @click="editModal = true" class="w-8 h-8 flex items-center justify-center text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form action="{{ route('admin.jadwal.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="w-8 h-8 flex items-center justify-center text-slate-300 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>

                                    <!-- Modal Edit -->
                                    <div x-show="editModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
                                        <div @click.outside="editModal = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-xl border border-slate-100 text-left max-h-[90vh] overflow-y-auto">
                                            <div class="flex items-center justify-between mb-4">
                                                <h3 class="text-base font-bold text-slate-800">Edit Jadwal</h3>
                                                <button @click="editModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                                            </div>
                                            <form action="{{ route('admin.jadwal.update', $item->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="space-y-4">
                                                    <input type="hidden" name="tahun_ajaran_id" value="{{ $item->tahun_ajaran_id }}">
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="label">Kelas</label>
                                                            <select name="kelas_id" required class="input">
                                                                @foreach($kelas as $k)
                                                                    <option value="{{ $k->id }}" @selected($item->kelas_id == $k->id)>{{ $k->nama_kelas }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="label">Hari</label>
                                                            <select name="hari" required class="input">
                                                                @foreach($daftarHari as $dh)
                                                                    <option value="{{ $dh }}" @selected($item->hari == $dh)>{{ $dh }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <label class="label">Mata Pelajaran</label>
                                                        <select name="mata_pelajaran_id" required class="input">
                                                            @foreach($mapel as $m)
                                                                <option value="{{ $m->id }}" @selected($item->mata_pelajaran_id == $m->id)>{{ $m->nama_mapel }} ({{ $m->kode_mapel }})</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="label">Guru Pengampu</label>
                                                        <select name="guru_id" required class="input">
                                                            @foreach($guru as $g)
                                                                <option value="{{ $g->id }}" @selected($item->guru_id == $g->id)>{{ $g->nama_lengkap }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="grid grid-cols-2 gap-3">
                                                        <div>
                                                            <label class="label">Jam Mulai</label>
                                                            <input type="time" name="jam_mulai" value="{{ substr($item->jam_mulai, 0, 5) }}" required class="input">
                                                        </div>
                                                        <div>
                                                            <label class="label">Jam Selesai</label>
                                                            <input type="time" name="jam_selesai" value="{{ substr($item->jam_selesai, 0, 5) }}" required class="input">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-6 flex justify-end gap-2">
                                                    <button type="button" @click="editModal = false" class="btn-secondary">Batal</button>
                                                    <button type="submit" class="btn-primary">Update Data</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="card px-6 py-10 text-center md:col-span-2">
                        <p class="text-xs text-slate-400 font-medium">Tidak ada jadwal hari {{ $h }} pada filter ini.</p>
                    </div>
                @endforelse
            </div>
        @endforeach
    </div>
</x-app-layout>
