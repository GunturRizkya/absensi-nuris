@extends('layouts.app')

@section('content')
@php
    $namaHari = [
        'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
    ];
    $kodeHari = [
        'Senin' => 'Sen', 'Selasa' => 'Sel', 'Rabu' => 'Rab',
        'Kamis' => 'Kam', 'Jumat' => 'Jum', 'Sabtu' => 'Sab', 'Minggu' => 'Min',
    ];

    $hariIni = $namaHari[now('Asia/Jakarta')->format('l')] ?? null;
    $editId = session('edit_id');

    $jumlahTotal = $pengaturan->count();
    $hariAktif = $pengaturan->where('is_aktif', true);
    $jumlahAktif = $hariAktif->count();
    $persenAktif = $jumlahTotal > 0 ? round($jumlahAktif / $jumlahTotal * 100) : 0;

    $modus = function (string $kolom) use ($hariAktif) {
        return $hariAktif->pluck($kolom)
            ->filter(fn ($nilai) => $nilai !== null && $nilai !== '')
            ->countBy()
            ->sortDesc()
            ->keys()
            ->first();
    };

    $jamMasukUmum = substr((string) $modus('jam_masuk'), 0, 5);
    $jamPulangUmum = substr((string) $modus('jam_pulang'), 0, 5);
    $toleransiUmum = $modus('toleransi_keterlambatan');
    $durasiPulangUmum = $modus('durasi_absensi_pulang');
@endphp

<div class="mx-auto max-w-7xl space-y-5">

    {{-- Judul halaman --}}
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <nav class="crumb">
                <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                <span>/</span>
                <span class="text-slate-700">Pengaturan Absensi</span>
            </nav>
            <h1 class="page-title">Pengaturan Jam Absensi</h1>
            <p class="page-sub">Atur jam masuk, jam pulang, dan toleransi keterlambatan untuk tiap hari sekolah.</p>
        </div>

        <span class="inline-flex items-center gap-2 self-start sm:self-auto rounded-2xl border border-blue-100 bg-blue-50/70 px-3.5 py-2 text-[11px] font-bold text-blue-700">
            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Zona waktu <span class="font-extrabold">Asia/Jakarta</span>
        </span>
    </div>

    {{-- Notifikasi --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="flex items-center justify-between gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-xs font-bold text-emerald-700">
            <span class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ session('success') }}
            </span>
            <button @click="show = false" class="px-1 text-base leading-none text-emerald-500 hover:text-emerald-700">&times;</button>
        </div>
    @endif

    @if ($errors->any())
        <div class="flex items-start gap-2.5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-xs font-bold text-rose-700">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
            <div>
                <p>Pengaturan belum tersimpan. Periksa kembali isian berikut:</p>
                <ul class="mt-1.5 list-inside list-disc space-y-0.5 font-semibold">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Ringkasan kebijakan --}}
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
        <div class="card p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Hari aktif</p>
                    <p class="text-lg font-extrabold leading-tight text-slate-800">
                        {{ $jumlahAktif }} <span class="text-xs font-bold text-slate-400">dari {{ $jumlahTotal }} hari</span>
                    </p>
                </div>
            </div>
            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-slate-100">
                <div class="h-full rounded-full bg-gradient-to-r from-blue-500 to-indigo-400" style="width: {{ $persenAktif }}%"></div>
            </div>
        </div>

        <div class="card p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jam masuk umum</p>
                    <p class="text-lg font-extrabold leading-tight text-slate-800">{{ $jamMasukUmum ?: '—' }}</p>
                </div>
            </div>
            <p class="mt-3 text-[11px] font-semibold text-slate-400">Toleransi {{ $toleransiUmum ?? 0 }} menit</p>
        </div>

        <div class="card p-4">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jam pulang umum</p>
                    <p class="text-lg font-extrabold leading-tight text-slate-800">{{ $jamPulangUmum ?: '—' }}</p>
                </div>
            </div>
            <p class="mt-3 text-[11px] font-semibold text-slate-400">Jendela absen pulang {{ $durasiPulangUmum ?? 0 }} menit</p>
        </div>
    </div>

    <div class="flex items-start gap-2.5 rounded-2xl border border-amber-100 bg-amber-50/70 px-4 py-3 text-[11px] font-semibold leading-relaxed text-amber-700">
        <svg class="mt-0.5 w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <p>Scan gerbang mengikuti pengaturan hari yang aktif. Hari nonaktif dianggap libur: scan ditolak dan penandaan alpa otomatis dilewati.</p>
    </div>

    {{-- Kartu per hari --}}
    @if ($pengaturan->isEmpty())
        <div class="card px-6 py-14 text-center">
            <p class="text-sm font-bold text-slate-700">Belum ada pengaturan jam absensi.</p>
            <p class="mt-1 text-xs font-medium text-slate-400">Jalankan seeder pengaturan untuk membuat jadwal Senin–Sabtu.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-2 xl:grid-cols-3">
            @foreach ($pengaturan as $item)
                @php
                    $sedangDiedit = (string) $editId === (string) $item->id;
                    $nilai = fn (string $field, $default) => $sedangDiedit ? old($field, $default) : $default;

                    $jamMasuk = substr($item->jam_masuk, 0, 5);
                    $jamTutup = substr($item->jam_tutup_absensi, 0, 5);
                    $jamPulang = substr($item->jam_pulang, 0, 5);
                    $batasTepat = \Carbon\Carbon::parse($item->jam_masuk)
                        ->addMinutes($item->toleransi_keterlambatan)
                        ->format('H:i');
                    $isHariIni = $hariIni === $item->hari;
                @endphp

                <div class="card flex flex-col transition-shadow hover:shadow-md {{ $isHariIni ? 'ring-2 ring-blue-500/50' : '' }}"
                     x-data="{
                        editHari: {{ $sedangDiedit ? 'true' : 'false' }},
                        masuk: '{{ $nilai('jam_masuk', $jamMasuk) }}',
                        tol: {{ (int) $nilai('toleransi_keterlambatan', $item->toleransi_keterlambatan) }},
                        get batas() {
                            const [jam, menit] = (this.masuk || '00:00').split(':').map(Number);
                            let total = (jam || 0) * 60 + (menit || 0) + (parseInt(this.tol) || 0);
                            total = ((total % 1440) + 1440) % 1440;
                            return String(Math.floor(total / 60)).padStart(2, '0') + ':' + String(total % 60).padStart(2, '0');
                        }
                     }">

                    {{-- Header kartu --}}
                    <div class="flex items-start justify-between gap-3 border-b border-slate-100 p-5 pb-4">
                        <div class="flex min-w-0 items-center gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl text-xs font-extrabold shadow-sm {{ $item->is_aktif ? 'bg-gradient-to-br from-blue-500 to-indigo-500 text-white' : 'bg-slate-100 text-slate-400' }}">
                                {{ $kodeHari[$item->hari] ?? substr($item->hari, 0, 3) }}
                            </div>
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <p class="text-sm font-extrabold text-slate-800">{{ $item->hari }}</p>
                                    @if ($isHariIni)
                                        <span class="badge border-blue-200/70 bg-blue-50 text-blue-600">Hari ini</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <span class="badge shrink-0 {{ $item->is_aktif ? 'border-emerald-200/60 bg-emerald-50 text-emerald-600' : 'border-transparent bg-slate-100 text-slate-500' }}">
                            <span class="mr-1.5 h-1.5 w-1.5 rounded-full {{ $item->is_aktif ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                            {{ $item->is_aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>

                    {{-- Isi kartu --}}
                    <div class="flex-1 p-5 pt-4">
                        <div class="flex items-center gap-2 rounded-xl bg-slate-50 px-3 py-2.5">
                            <div class="shrink-0 text-center">
                                <p class="text-sm font-extrabold leading-tight text-slate-800">{{ $jamMasuk }}</p>
                                <p class="text-[10px] font-bold text-slate-400">Masuk</p>
                            </div>
                            <div class="flex flex-1 items-center gap-1.5 px-1">
                                <span class="h-px flex-1 bg-slate-200"></span>
                                <span class="badge whitespace-nowrap border-amber-200/60 bg-amber-50 text-amber-600">+{{ $item->toleransi_keterlambatan }} mnt</span>
                                <span class="h-px flex-1 bg-slate-200"></span>
                            </div>
                            <div class="shrink-0 text-center">
                                <p class="text-sm font-extrabold leading-tight text-slate-800">{{ $jamPulang }}</p>
                                <p class="text-[10px] font-bold text-slate-400">Pulang</p>
                            </div>
                        </div>

                        <div class="mt-2 grid grid-cols-2 gap-2">
                            <div class="rounded-xl border border-slate-100 px-3 py-2">
                                <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Batas tepat waktu</p>
                                <p class="mt-0.5 text-xs font-extrabold text-slate-700">{{ $batasTepat }}</p>
                            </div>
                            <div class="rounded-xl border border-slate-100 px-3 py-2">
                                <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Tutup absen masuk</p>
                                <p class="mt-0.5 text-xs font-extrabold text-slate-700">{{ $jamTutup }}</p>
                            </div>
                        </div>

                        <p class="mt-2 flex items-center gap-1.5 px-1 text-[10px] font-semibold text-slate-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Jendela absen pulang {{ $item->durasi_absensi_pulang }} menit sejak jam pulang
                        </p>
                    </div>

                    {{-- Aksi --}}
                    <div class="flex items-center justify-between gap-1.5 border-t border-slate-100 bg-slate-50/50 px-4 py-3 sm:px-5">
                        <form action="{{ route('admin.pengaturan-absensi.toggle-aktif', $item->id) }}" method="POST" class="inline"
                              @if ($item->is_aktif) onsubmit="return confirm('Nonaktifkan absensi hari {{ $item->hari }}? Scan gerbang pada hari ini akan ditolak.')" @endif>
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-2 whitespace-nowrap rounded-lg text-[11px] font-bold transition-colors {{ $item->is_aktif ? 'text-emerald-600 hover:text-emerald-700' : 'text-slate-400 hover:text-slate-600' }}"
                                    title="{{ $item->is_aktif ? 'Nonaktifkan hari ini' : 'Aktifkan hari ini' }}">
                                <span class="relative inline-flex h-5 w-9 shrink-0 items-center rounded-full transition-colors {{ $item->is_aktif ? 'bg-emerald-500' : 'bg-slate-300' }}">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform {{ $item->is_aktif ? 'translate-x-[18px]' : 'translate-x-0.5' }}"></span>
                                </span>
                                {{ $item->is_aktif ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>

                        <button type="button" @click="editHari = true" class="btn-blue-soft shrink-0 whitespace-nowrap">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Ubah jam
                        </button>
                    </div>

                    {{-- Modal edit --}}
                    <div x-show="editHari"
                         x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                         @keydown.escape.window="editHari = false"
                         @click.self="editHari = false"
                         class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 px-0 backdrop-blur-sm sm:items-center sm:px-4"
                         x-cloak>
                        <div x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                             class="max-h-[92vh] w-full overflow-y-auto rounded-t-3xl border border-slate-100 bg-white shadow-2xl sm:max-w-lg sm:rounded-2xl">

                            <div class="flex items-start justify-between gap-3 border-b border-slate-100 px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-xs font-extrabold text-blue-600">
                                        {{ $kodeHari[$item->hari] ?? substr($item->hari, 0, 3) }}
                                    </div>
                                    <div>
                                        <h3 class="text-sm font-extrabold text-slate-800">Pengaturan hari {{ $item->hari }}</h3>
                                        <p class="mt-0.5 text-[11px] font-semibold text-slate-400">Berlaku untuk scan gerbang hari ini.</p>
                                    </div>
                                </div>
                                <button type="button" @click="editHari = false" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600" aria-label="Tutup">&times;</button>
                            </div>

                            <form action="{{ route('admin.pengaturan-absensi.update', $item->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="space-y-4 px-5 py-5">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="label" for="jam-masuk-{{ $item->id }}">Jam masuk</label>
                                            <input id="jam-masuk-{{ $item->id }}" type="time" name="jam_masuk" x-model="masuk"
                                                   value="{{ $nilai('jam_masuk', $jamMasuk) }}" required class="input">
                                            @if ($sedangDiedit)
                                                @error('jam_masuk')<p class="mt-1 text-[10px] font-bold text-rose-600">{{ $message }}</p>@enderror
                                            @endif
                                        </div>
                                        <div>
                                            <label class="label" for="jam-tutup-{{ $item->id }}">Tutup absen masuk</label>
                                            <input id="jam-tutup-{{ $item->id }}" type="time" name="jam_tutup_absensi"
                                                   value="{{ $nilai('jam_tutup_absensi', $jamTutup) }}" required class="input">
                                            @if ($sedangDiedit)
                                                @error('jam_tutup_absensi')<p class="mt-1 text-[10px] font-bold text-rose-600">{{ $message }}</p>@enderror
                                            @endif
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <label class="label" for="jam-pulang-{{ $item->id }}">Jam pulang</label>
                                            <input id="jam-pulang-{{ $item->id }}" type="time" name="jam_pulang"
                                                   value="{{ $nilai('jam_pulang', $jamPulang) }}" required class="input">
                                            @if ($sedangDiedit)
                                                @error('jam_pulang')<p class="mt-1 text-[10px] font-bold text-rose-600">{{ $message }}</p>@enderror
                                            @endif
                                        </div>
                                        <div>
                                            <label class="label" for="durasi-pulang-{{ $item->id }}">Jendela absen pulang</label>
                                            <div class="relative">
                                                <input id="durasi-pulang-{{ $item->id }}" type="number" name="durasi_absensi_pulang" min="0" max="600"
                                                       value="{{ $nilai('durasi_absensi_pulang', $item->durasi_absensi_pulang) }}" required class="input pr-12">
                                                <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[10px] font-bold text-slate-400">menit</span>
                                            </div>
                                            @if ($sedangDiedit)
                                                @error('durasi_absensi_pulang')<p class="mt-1 text-[10px] font-bold text-rose-600">{{ $message }}</p>@enderror
                                            @endif
                                        </div>
                                    </div>

                                    <div>
                                        <label class="label" for="toleransi-{{ $item->id }}">Toleransi keterlambatan</label>
                                        <div class="relative">
                                            <input id="toleransi-{{ $item->id }}" type="number" name="toleransi_keterlambatan" min="0" max="180"
                                                   x-model="tol" value="{{ $nilai('toleransi_keterlambatan', $item->toleransi_keterlambatan) }}" required class="input pr-12">
                                            <span class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-[10px] font-bold text-slate-400">menit</span>
                                        </div>
                                        @if ($sedangDiedit)
                                            @error('toleransi_keterlambatan')<p class="mt-1 text-[10px] font-bold text-rose-600">{{ $message }}</p>@enderror
                                        @endif
                                    </div>

                                    <div class="flex items-center justify-between gap-3 rounded-xl border border-blue-100 bg-blue-50/70 px-3.5 py-2.5">
                                        <span class="flex items-center gap-2 text-[11px] font-bold text-blue-700">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Batas tepat waktu
                                        </span>
                                        <span class="text-sm font-extrabold text-blue-700" x-text="batas">{{ $batasTepat }}</span>
                                    </div>
                                    <p class="px-1 text-[10px] font-semibold text-slate-400">Siswa yang scan setelah batas ini tercatat terlambat.</p>
                                </div>

                                <div class="flex justify-end gap-2 border-t border-slate-100 bg-slate-50/50 px-5 py-4">
                                    <button type="button" @click="editHari = false" class="btn-secondary">Batal</button>
                                    <button type="submit" class="btn-primary">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Simpan perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
