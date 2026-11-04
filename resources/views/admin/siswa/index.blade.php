@extends('layouts.app')

@section('content')
<div x-data="{ mode: 'grid' }">
    <!-- Header -->
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <nav class="crumb">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Data Siswa</span>
            </nav>
            <h1 class="page-title">Data Siswa</h1>
            <p class="page-sub">Kelola data siswa, statistik per kelas, dan kartu pelajar siap scan.</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Toggle grid / list -->
            <div class="flex p-1 rounded-xl bg-white border border-slate-200/80">
                <button @click="mode = 'grid'" :class="mode === 'grid' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-400 hover:text-slate-600'" class="w-9 h-9 flex items-center justify-center rounded-lg transition-all" title="Tampilan kartu">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                </button>
                <button @click="mode = 'list'" :class="mode === 'list' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-400 hover:text-slate-600'" class="w-9 h-9 flex items-center justify-center rounded-lg transition-all" title="Tampilan baris">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
            </div>
            <div x-data="{ openModal: false }">
                <button @click="openModal = true" class="btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                    <span class="hidden sm:inline">Tambah Siswa</span><span class="sm:hidden">Tambah</span>
                </button>

                <!-- Modal Tambah -->
                <div x-show="openModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
                    <div @click.outside="openModal = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-slate-800">Tambah Siswa Baru</h3>
                            <button @click="openModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                        </div>

                        <form action="{{ route('admin.siswa.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">NISN</label>
                                        <input type="text" name="nisn" required class="input">
                                    </div>
                                    <div>
                                        <label class="label">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" required class="input">
                                            <option value="L">Laki-laki</option>
                                            <option value="P">Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="label">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" required class="input">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">No. HP Orang Tua</label>
                                        <input type="text" name="no_hp_ortu" class="input">
                                    </div>
                                    <div>
                                        <label class="label">Foto <span class="font-medium text-slate-400">(maks 2 MB)</span></label>
                                        <input type="file" name="foto" accept=".jpg,.jpeg,.png" class="w-full text-xs text-slate-600 file:mr-3 file:px-3 file:py-2 file:text-xs file:font-bold file:bg-slate-100 file:hover:bg-slate-200 file:text-slate-700 file:rounded-xl file:border-0">
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="label">QR <span class="font-medium text-slate-400">(default NISN)</span></label>
                                        <input type="text" name="qr_code" class="input">
                                    </div>
                                    <div>
                                        <label class="label">Kartu (RFID)</label>
                                        <input type="text" name="card_number" class="input">
                                    </div>
                                    <div>
                                        <label class="label">Sidik Jari</label>
                                        <input type="text" name="fingerprint_id" class="input">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">Tahun Ajaran</label>
                                        <select name="tahun_ajaran_id" required class="input">
                                            @foreach($tahunAjaran as $ta)
                                                <option value="{{ $ta->id }}" @selected($tahunAjaranId == $ta->id)>{{ $ta->tahun }} - Smt {{ $ta->semester }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="label">Kelas</label>
                                        <select name="kelas_id" required class="input">
                                            @foreach($kelas as $k)
                                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                            @endforeach
                                        </select>
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
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition class="mb-5 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold flex items-center justify-between gap-3">
            <span>{{ session('success') }}</span>
            <button @click="show = false" class="text-emerald-500 hover:text-emerald-700 text-base leading-none px-1">&times;</button>
        </div>
    @endif

    <!-- Kartu statistik -->
    <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 sm:gap-4 mb-4">
        <div class="card p-4 sm:p-5 flex items-center justify-between gap-3">
            <div>
                <p class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Siswa</p>
                <p class="text-2xl font-extrabold text-slate-800 tracking-tight">{{ $stat['total'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
        </div>
        <div class="card p-4 sm:p-5 flex items-center justify-between gap-3">
            <div>
                <p class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Ada Foto</p>
                <p class="text-2xl font-extrabold text-emerald-600 tracking-tight">{{ $stat['foto'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div class="card p-4 sm:p-5 flex items-center justify-between gap-3">
            <div>
                <p class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Belum Ada Foto</p>
                <p class="text-2xl font-extrabold text-rose-500 tracking-tight">{{ $stat['tanpa_foto'] }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
        </div>
        <div class="card p-4 sm:p-5 flex items-center justify-between gap-3">
            <div>
                <p class="text-[10px] sm:text-[11px] font-bold text-slate-400 uppercase tracking-wider">Laki / Perempuan</p>
                <p class="text-2xl font-extrabold tracking-tight"><span class="text-blue-600">{{ $stat['laki'] }} L</span><span class="text-slate-300 text-lg"> / </span><span class="text-pink-500">{{ $stat['perempuan'] }} P</span></p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="card p-4 mb-4">
        <form action="{{ route('admin.siswa.index') }}" method="GET" class="grid grid-cols-2 md:grid-cols-6 gap-3 items-end">
            <div class="col-span-2">
                <label class="label">Cari NISN / Nama</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Cari NISN, NIS, Nama…" class="input !pl-10">
                </div>
            </div>
            <div>
                <label class="label">Tahun Ajaran</label>
                <select name="tahun_ajaran_id" class="input bg-white">
                    @foreach($tahunAjaran as $ta)
                        <option value="{{ $ta->id }}" @selected($tahunAjaranId == $ta->id)>{{ $ta->tahun }} - Smt {{ $ta->semester }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Tingkat</label>
                <select name="tingkat" class="input bg-white">
                    <option value="">Semua Tingkat</option>
                    <option value="X" @selected($tingkat === 'X')>X</option>
                    <option value="XI" @selected($tingkat === 'XI')>XI</option>
                    <option value="XII" @selected($tingkat === 'XII')>XII</option>
                </select>
            </div>
            <div>
                <label class="label">Kelas</label>
                <select name="kelas_id" class="input bg-white">
                    <option value="">Semua Kelas</option>
                    @foreach($kelas as $k)
                        <option value="{{ $k->id }}" @selected($kelasId == $k->id)>{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex gap-2 col-span-2 md:col-span-1">
                <button type="submit" class="btn-primary flex-1">Tampilkan</button>
                @if($q || $kelasId || $tingkat)
                    <a href="{{ route('admin.siswa.index', ['tahun_ajaran_id' => $tahunAjaranId]) }}" class="btn-secondary">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- MODE KARTU -->
    <div x-show="mode === 'grid'" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 sm:gap-4">
        @forelse($siswa as $item)
            <div class="card p-4 sm:p-5 hover:shadow-md transition-shadow" x-data="{ editModal: false }">
                <div class="flex items-start gap-3">
                    @if($item->foto)
                        <img src="{{ asset($item->foto) }}" alt="Foto {{ $item->nama_lengkap }}" class="w-12 h-12 rounded-2xl object-cover shadow-sm shrink-0">
                    @else
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white font-extrabold flex items-center justify-center text-sm shadow-sm shrink-0 tracking-wide">
                            {{ collect(explode(' ', $item->nama_lengkap))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('') }}
                        </div>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="font-bold text-slate-800 text-sm leading-snug">{{ $item->nama_lengkap }}</p>
                        <p class="text-[11px] text-slate-400 font-medium mt-0.5">NISN: {{ $item->nisn }}</p>
                    </div>
                    <span class="badge shrink-0 {{ $item->jenis_kelamin === 'P' ? 'bg-pink-50 text-pink-500 border-pink-200/60' : 'bg-blue-50 text-blue-500 border-blue-200/60' }}">{{ $item->jenis_kelamin }}</span>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between gap-3">
                    <p class="text-xs text-slate-400 font-medium">Kelas: <span class="font-bold text-slate-700">{{ $item->anggotaKelas->first()?->kelas?->nama_kelas ?? '-' }}</span></p>
                </div>
                <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between gap-2">
                    @if($item->foto)
                        <span class="badge bg-emerald-50 text-emerald-600 border-emerald-200/60">Foto Siap</span>
                    @else
                        <span class="badge bg-amber-50 text-amber-600 border-amber-200/60">Belum Ada Foto</span>
                    @endif
                    <div class="flex items-center gap-1">
                        <a href="{{ route('admin.siswa.show', ['id' => $item->id, 'tahun_ajaran_id' => $tahunAjaranId]) }}" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </a>
                        <a href="{{ route('admin.siswa.kartu', $item->id) }}" target="_blank" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-violet-600 hover:bg-violet-50 rounded-lg transition-colors" title="Kartu">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/></svg>
                        </a>
                        <button @click="editModal = true" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </button>
                        <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus siswa ini beserta riwayat absensinya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Modal Edit -->
                <div x-show="editModal" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-xs px-4" x-cloak>
                    <div @click.outside="editModal = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95 translate-y-2" x-transition:enter-end="opacity-100 scale-100 translate-y-0" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-xl border border-slate-100 text-left max-h-[90vh] overflow-y-auto">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-bold text-slate-800">Edit Siswa</h3>
                            <button @click="editModal = false" class="w-8 h-8 flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-full text-lg">&times;</button>
                        </div>
                        <form action="{{ route('admin.siswa.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf @method('PUT')
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">NISN</label>
                                        <input type="text" name="nisn" value="{{ $item->nisn }}" required class="input">
                                    </div>
                                    <div>
                                        <label class="label">Jenis Kelamin</label>
                                        <select name="jenis_kelamin" required class="input">
                                            <option value="L" @selected($item->jenis_kelamin === 'L')>Laki-laki</option>
                                            <option value="P" @selected($item->jenis_kelamin === 'P')>Perempuan</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="label">Nama Lengkap</label>
                                    <input type="text" name="nama_lengkap" value="{{ $item->nama_lengkap }}" required class="input">
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">No. HP Orang Tua</label>
                                        <input type="text" name="no_hp_ortu" value="{{ $item->no_hp_ortu }}" class="input">
                                    </div>
                                    <div>
                                        <label class="label">Foto Baru <span class="font-medium text-slate-400">(kosongkan bila tetap)</span></label>
                                        <input type="file" name="foto" accept=".jpg,.jpeg,.png" class="w-full text-xs text-slate-600 file:mr-3 file:px-3 file:py-2 file:text-xs file:font-bold file:bg-slate-100 file:hover:bg-slate-200 file:text-slate-700 file:rounded-xl file:border-0">
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-3">
                                    <div>
                                        <label class="label">QR Code</label>
                                        <input type="text" name="qr_code" value="{{ $item->qr_code }}" class="input">
                                    </div>
                                    <div>
                                        <label class="label">Kartu (RFID)</label>
                                        <input type="text" name="card_number" value="{{ $item->card_number }}" class="input">
                                    </div>
                                    <div>
                                        <label class="label">Sidik Jari</label>
                                        <input type="text" name="fingerprint_id" value="{{ $item->fingerprint_id }}" class="input">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="label">Tahun Ajaran</label>
                                        <select name="tahun_ajaran_id" required class="input">
                                            @foreach($tahunAjaran as $ta)
                                                <option value="{{ $ta->id }}" @selected($tahunAjaranId == $ta->id)>{{ $ta->tahun }} - Smt {{ $ta->semester }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="label">Kelas</label>
                                        <select name="kelas_id" required class="input">
                                            @foreach($kelas as $k)
                                                <option value="{{ $k->id }}" @selected($item->anggotaKelas->first()?->kelas_id == $k->id)>{{ $k->nama_kelas }}</option>
                                            @endforeach
                                        </select>
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
        @empty
            <div class="card px-6 py-12 text-center sm:col-span-2 xl:col-span-3">
                <p class="text-xs text-slate-400 font-medium">Belum ada data siswa pada filter ini.</p>
            </div>
        @endforelse
    </div>

    <!-- MODE BARIS -->
    <div x-show="mode === 'list'" x-cloak class="space-y-2.5">
        @foreach($siswa as $item)
            <div class="card px-4 sm:px-5 py-3 flex flex-col sm:flex-row sm:items-center gap-3 hover:shadow-md transition-shadow">
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <span class="font-mono text-[11px] font-bold text-slate-400 w-24 shrink-0 hidden sm:block">{{ $item->nisn }}</span>
                    <p class="font-bold text-slate-800 text-sm truncate">{{ $item->nama_lengkap }}<span class="sm:hidden block text-[11px] font-medium text-slate-400">{{ $item->nisn }}</span></p>
                </div>
                <div class="flex items-center gap-1.5">
                    <a href="{{ route('admin.siswa.show', ['id' => $item->id, 'tahun_ajaran_id' => $tahunAjaranId]) }}" class="btn-blue-soft">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        Detail
                    </a>
                    <a href="{{ route('admin.siswa.kartu', $item->id) }}" target="_blank" class="btn-blue-soft !bg-violet-50 !text-violet-600 hover:!bg-violet-100">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2"/></svg>
                        Kartu
                    </a>
                    <form action="{{ route('admin.siswa.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus siswa ini beserta riwayat absensinya?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger-soft" title="Hapus">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span class="hidden sm:inline">Hapus</span>
                        </button>
                    </form>
                    <span class="text-[11px] text-slate-400 font-medium sm:hidden">Edit via mode kartu</span>
                </div>
            </div>
        @endforeach
    </div>

    @if($siswa->hasPages())
        <div class="mt-4">{{ $siswa->links() }}</div>
    @endif
</div>
@endsection
