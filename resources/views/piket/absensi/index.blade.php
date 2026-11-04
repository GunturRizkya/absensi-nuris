<x-app-layout>
    <!-- Header -->
    <div class="mb-6">
        <nav class="crumb">
            <a href="{{ route('dashboard') }}" class="hover:text-blue-600">Dashboard</a>
            <span>/</span>
            <span class="text-slate-700">Absensi Gerbang</span>
        </nav>
        <h1 class="page-title">Absensi Gerbang Harian</h1>
        <p class="page-sub">Arahkan QR kartu pelajar ke kamera. {{ $hariIni }}</p>
    </div>

    @unless($tahunAktif)
        <div class="mb-5 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold">Belum ada tahun ajaran aktif. Minta admin mengaktifkannya terlebih dahulu.</div>
    @endunless

    <!-- Statistik hidup -->
    @php($stat = [
        ['Terdaftar', $statistik['total_siswa'], 'text-slate-800', 'bg-slate-100', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'stat-total-siswa'],
        ['Hadir', $statistik['hadir'], 'text-emerald-600', 'bg-emerald-50', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', 'stat-hadir'],
        ['Terlambat', $statistik['terlambat'], 'text-amber-600', 'bg-amber-50', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z', 'stat-terlambat'],
        ['Izin', $statistik['izin'], 'text-sky-600', 'bg-sky-50', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'stat-izin'],
        ['Sakit', $statistik['sakit'], 'text-violet-600', 'bg-violet-50', 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'stat-sakit'],
        ['Alpa', $statistik['alpa'], 'text-rose-600', 'bg-rose-50', 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z', 'stat-alpa'],
    ])
    <div class="grid grid-cols-3 md:grid-cols-6 gap-3 mb-5">
        @foreach($stat as [$label, $nilai, $warna, $latar, $ikon, $id])
            <div class="card p-4 hover:shadow-md transition-shadow">
                <div class="w-9 h-9 rounded-xl {{ $latar }} flex items-center justify-center mb-2.5">
                    <svg class="w-4 h-4 {{ $warna }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ikon }}"/></svg>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</p>
                <p id="{{ $id }}" class="text-xl font-extrabold {{ $warna }} tracking-tight">{{ $nilai }}</p>
            </div>
        @endforeach
    </div>

    <!-- Error -->
    @if(session('error'))
        <div class="mb-5 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold flex items-center justify-between gap-3">
            <span>{{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 text-base leading-none px-1">&times;</button>
        </div>
    @endif

    <!-- Bento 70/30 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Kiri: hasil + kamera + manual -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Kartu profil hasil -->
            <div class="card p-5 sm:p-6" id="hasil-scan">
                @if(session('hasil'))
                    @php($h = session('hasil'))
                    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                        @if($h['foto'] ?? null)
                            <img src="{{ $h['foto'] }}" alt="Foto {{ $h['nama'] }}" class="w-20 h-20 rounded-3xl object-cover shadow-md shrink-0">
                        @else
                            <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-blue-500 to-indigo-500 text-white text-2xl font-extrabold flex items-center justify-center shadow-md shrink-0">
                                {{ collect(explode(' ', $h['nama']))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('') }}
                            </div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="text-lg font-extrabold text-slate-800 tracking-tight">{{ $h['nama'] }}</p>
                            <p class="text-[11px] font-semibold text-slate-400">NISN {{ $h['nisn'] }} · {{ $h['kelas'] }}</p>
                            <div class="mt-2.5 flex flex-wrap items-center gap-2">
                                <span class="inline-flex items-center px-4 py-1.5 rounded-full text-sm font-extrabold capitalize {{ $h['status'] === 'hadir' ? 'bg-emerald-500 text-white' : ($h['status'] === 'terlambat' ? 'bg-amber-500 text-white' : 'bg-slate-500 text-white') }}">
                                    {{ $h['status'] }}
                                </span>
                                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Absen {{ $h['tipe'] }} · {{ $h['jam'] }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="flex items-center gap-4">
                        <div class="w-20 h-20 rounded-3xl bg-slate-100 border-2 border-dashed border-slate-200 flex items-center justify-center shrink-0">
                            <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <div>
                            <p class="font-bold text-slate-500 text-sm">Menunggu scan berikutnya…</p>
                            <p class="text-[11px] text-slate-400 mt-0.5">Arahkan QR kartu pelajar ke kamera di bawah.</p>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Kamera -->
            <div class="card p-5 sm:p-6" id="qr-scanner" data-endpoint="{{ route('piket.absensi.scan') }}">
                <div class="flex items-center justify-between gap-3 mb-1">
                    <h2 class="text-sm font-bold text-slate-800">Scan QR Kamera</h2>
                    <span class="badge bg-emerald-50 text-emerald-600 border-emerald-200/60"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1.5 animate-pulse"></span><span id="qr-status">Menyiapkan kamera…</span></span>
                </div>

                <div id="qr-reader" class="mt-3 overflow-hidden rounded-2xl border border-slate-200 bg-slate-900 aspect-[4/3]"></div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <select id="qr-kamera" class="input bg-white">
                        <option value="">Memuat daftar kamera…</option>
                    </select>
                    <div class="flex gap-2">
                        <button type="button" id="qr-mulai" class="btn-primary hidden flex-1">Nyalakan</button>
                        <button type="button" id="qr-stop" class="btn-secondary hidden flex-1">Matikan</button>
                    </div>
                </div>

                <details class="mt-4">
                    <summary class="text-xs font-bold text-slate-500 cursor-pointer hover:text-slate-700">Ketik kode manual / RFID / NISN</summary>
                    <form action="{{ route('piket.absensi.scan') }}" method="POST" class="mt-3 space-y-3">
                        @csrf
                        <input type="hidden" name="metode" value="qr">
                        <input type="text" name="kode" required autocomplete="off" placeholder="QR / UID Kartu / NISN" class="input input-lg font-mono">
                        <button type="submit" class="btn-primary w-full py-3.5 text-sm">Catat Kehadiran</button>
                    </form>
                </details>
            </div>

            <!-- Input manual & koreksi: khusus piket/admin, OSIS hanya scan -->
            @if(in_array(Auth::user()->role, ['piket', 'admin'], true))
            <!-- Input manual piket -->
            <div class="card p-5 sm:p-6" x-data="{ openManual: false }">                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Input Manual Piket</h2>
                        <p class="text-[11px] text-slate-400 font-medium">Untuk siswa yang lupa kartu.</p>
                    </div>
                    <button @click="openManual = !openManual" class="btn-secondary">Buka Form</button>
                </div>

                <div x-show="openManual" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="mt-4">
                    <form action="{{ route('piket.absensi.manual') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="sm:col-span-2">
                                <label class="label">Siswa</label>
                                <select name="siswa_id" required class="input">
                                    @foreach($daftarSiswa as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama_lengkap }} ({{ $s->nisn }}) — {{ $s->anggotaKelas->first()?->kelas?->nama_kelas ?? '-' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Jenis</label>
                                <select name="tipe" required class="input">
                                    <option value="masuk">Masuk</option>
                                    <option value="pulang">Pulang</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <label class="label">Keterangan (opsional)</label>
                            <input type="text" name="keterangan" class="input">
                        </div>
                        <button type="submit" class="btn-primary w-full mt-4 py-3.5">Catat Manual</button>
                    </form>
                </div>
            </div>

            <!-- Koreksi status -->
            <div class="card p-5 sm:p-6" x-data="{ openKoreksi: false }">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-800">Koreksi Status</h2>
                        <p class="text-[11px] text-slate-400 font-medium">Ubah status + wajib alasan, tercatat nama Anda.</p>
                    </div>
                    <button @click="openKoreksi = !openKoreksi" class="btn-secondary">Buka Form</button>
                </div>

                <div x-show="openKoreksi" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-cloak class="mt-4">
                    <form action="{{ route('piket.koreksi') }}" method="POST">
                        @csrf
                        <input type="hidden" name="tanggal" value="{{ $hariIni }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="sm:col-span-2">
                                <label class="label">Siswa</label>
                                <select name="siswa_id" required class="input">
                                    @foreach($daftarSiswa as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama_lengkap }} ({{ $s->nisn }}) — {{ $s->anggotaKelas->first()?->kelas?->nama_kelas ?? '-' }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="label">Status Baru</label>
                                <select name="status" required class="input">
                                    <option value="hadir">Hadir</option>
                                    <option value="terlambat">Terlambat</option>
                                    <option value="izin">Izin</option>
                                    <option value="sakit">Sakit</option>
                                    <option value="alpa">Alpa</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">Alasan (wajib)</label>
                                <input type="text" name="alasan" required maxlength="255" placeholder="Contoh: surat susulan" class="input">
                            </div>
                        </div>
                        <button type="submit" class="btn-primary w-full mt-4 py-3.5">Simpan Koreksi</button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Kanan: live feed -->
        <div class="card p-5 flex flex-col" id="live-feed" data-endpoint="{{ route('piket.absensi.feed') }}">
            <div class="flex items-center justify-between gap-2 mb-1">
                <h2 class="text-sm font-bold text-slate-800">Live Feed Hari Ini</h2>
                <span class="badge bg-blue-50 text-blue-600 border-blue-200/60"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5 animate-pulse"></span>Otomatis</span>
            </div>
            <p class="text-[11px] text-slate-400 font-medium mb-3">Diperbarui tiap 10 detik.</p>
            <div id="live-feed-list" class="space-y-2 overflow-y-auto max-h-[560px] pr-0.5">
                <p class="text-xs text-slate-400 font-medium text-center py-8">Memuat…</p>
            </div>
        </div>
    </div>

    <script>
    (function () {
        var box = document.getElementById('live-feed');
        if (!box) return;
        var list = document.getElementById('live-feed-list');
        var endpoint = box.dataset.endpoint;
        var warna = { hadir: 'bg-emerald-500', terlambat: 'bg-amber-500', izin: 'bg-sky-500', sakit: 'bg-violet-500', alpa: 'bg-rose-500' };

        function muat() {
            fetch(endpoint, { headers: { Accept: 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.log || !data.log.length) {
                        list.innerHTML = '<p class="text-xs text-slate-400 font-medium text-center py-8">Belum ada yang absen hari ini.</p>';
                        return;
                    }
                    list.innerHTML = data.log.map(function (e) {
                        var foto = e.foto
                            ? '<img src="' + e.foto + '" alt="" class="w-9 h-9 rounded-xl object-cover shrink-0">'
                            : '<span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-400 text-[10px] font-extrabold flex items-center justify-center shrink-0">' + e.nama.split(' ').slice(0, 2).map(function (w) { return w.charAt(0); }).join('') + '</span>';
                        return '<div class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50/40 transition-colors">'
                            + foto
                            + '<div class="min-w-0 flex-1"><p class="text-xs font-bold text-slate-800 truncate">' + e.nama + '</p>'
                            + '<p class="text-[10px] text-slate-400 font-medium">' + e.kelas + ' · ' + e.jam + '</p></div>'
                            + '<span class="w-2.5 h-2.5 rounded-full shrink-0 ' + (warna[e.status] || 'bg-slate-300') + '" title="' + e.status + '"></span></div>';
                    }).join('');
                })
                .catch(function () { /* biarkan data lama saat jaringan gagal */ });
        }

        muat();
        setInterval(muat, 10000);
    })();
    </script>
</x-app-layout>
