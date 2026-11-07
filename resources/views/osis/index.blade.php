<x-app-layout>
    <!-- Header -->
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="page-title">Dashboard OSIS</h1>
            <p class="page-sub">Ringkasan absensi gerbang hari ini, {{ $hariIni }}.</p>
        </div>
        <a href="{{ route('piket.absensi.index') }}" class="btn-primary !py-3 !text-sm shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Mulai Scan Gerbang
        </a>
    </div>

    @unless($tahunAktif)
        <div class="mb-5 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-700 text-xs font-bold">
            Belum ada tahun ajaran aktif. Minta admin mengaktifkannya terlebih dahulu.
        </div>
    @endunless

    <!-- Statistik -->
    @php($stat = [
        ['Siswa Terdaftar', $ringkasan['total_siswa'], 'text-slate-800', 'bg-slate-100', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z'],
        ['Sudah Absen', $ringkasan['sudah_absen'], 'text-emerald-600', 'bg-emerald-50', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Belum Absen', $ringkasan['belum_absen'], 'text-amber-600', 'bg-amber-50', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Hadir', $ringkasan['hadir'], 'text-emerald-600', 'bg-emerald-50', 'M5 13l4 4L19 7'],
        ['Terlambat', $ringkasan['terlambat'], 'text-amber-600', 'bg-amber-50', 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['Izin / Sakit / Alpa', $ringkasan['izin'] + $ringkasan['sakit'] + $ringkasan['alpa'], 'text-rose-600', 'bg-rose-50', 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
    ])
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3 mb-5">
        @foreach($stat as [$label, $nilai, $warna, $latar, $ikon])
            <div class="card p-4 hover:shadow-md transition-shadow">
                <div class="w-9 h-9 rounded-xl {{ $latar }} flex items-center justify-center mb-2.5">
                    <svg class="w-4 h-4 {{ $warna }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $ikon }}"/></svg>
                </div>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $label }}</p>
                <p class="text-xl font-extrabold {{ $warna }} tracking-tight">{{ $nilai }}</p>
            </div>
        @endforeach
    </div>

    <!-- Bento: cara pakai + live feed -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2 space-y-4">
            <!-- Cara pakai -->
            <div class="card p-5 sm:p-6">
                <h2 class="text-sm font-bold text-slate-800 mb-3">Cara Melakukan Absensi</h2>
                <ol class="space-y-2.5 text-xs text-slate-600">
                    <li class="flex gap-2.5"><span class="w-5 h-5 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-extrabold flex items-center justify-center shrink-0">1</span>Pastikan Anda login dengan akun OSIS.</li>
                    <li class="flex gap-2.5"><span class="w-5 h-5 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-extrabold flex items-center justify-center shrink-0">2</span>Tekan tombol <b>Mulai Scan Gerbang</b> di kanan atas.</li>
                    <li class="flex gap-2.5"><span class="w-5 h-5 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-extrabold flex items-center justify-center shrink-0">3</span>Arahkan kamera HP ke QR code kartu pelajar siswa.</li>
                    <li class="flex gap-2.5"><span class="w-5 h-5 rounded-lg bg-blue-50 text-blue-600 text-[10px] font-extrabold flex items-center justify-center shrink-0">4</span>Hasil scan langsung muncul dan tercatat otomatis.</li>
                </ol>
                <div class="mt-4 p-3.5 rounded-xl bg-slate-50 border border-slate-100 text-[11px] text-slate-500 font-medium">
                    Catatan: OSIS hanya bisa memindai kartu. Input manual dan koreksi status hanya untuk piket/admin.
                </div>
            </div>

            <!-- CTA scan -->
            <a href="{{ route('piket.absensi.index') }}" class="card p-5 sm:p-6 flex items-center justify-between gap-4 hover:shadow-md hover:border-blue-200 transition-all group">
                <div>
                    <p class="font-extrabold text-slate-800">Siap memindai kartu siswa?</p>
                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">Buka halaman scan gerbang dan nyalakan kamera HP Anda.</p>
                </div>
                <span class="btn-primary shrink-0 group-hover:scale-105 transition-transform">Buka Scan</span>
            </a>
        </div>

        <!-- Live feed -->
        <div class="card p-5 flex flex-col" id="live-feed" data-endpoint="{{ route('piket.absensi.feed') }}">
            <div class="flex items-center justify-between gap-2 mb-1">
                <h2 class="text-sm font-bold text-slate-800">Live Feed Hari Ini</h2>
                <span class="badge bg-blue-50 text-blue-600 border-blue-200/60"><span class="w-1.5 h-1.5 rounded-full bg-blue-500 mr-1.5 animate-pulse"></span>Otomatis</span>
            </div>
            <p class="text-[11px] text-slate-400 font-medium mb-3">Diperbarui tiap 10 detik.</p>
            <div id="live-feed-list" class="space-y-2 overflow-y-auto max-h-[420px] pr-0.5">
                @for ($i = 0; $i < 4; $i++)
                    <div class="flex items-center gap-2.5 p-2.5 rounded-xl border border-slate-100">
                        <div class="w-9 h-9 rounded-xl bg-slate-100 animate-pulse"></div>
                        <div class="flex-1 space-y-1.5">
                            <div class="h-2.5 rounded bg-slate-100 animate-pulse"></div>
                            <div class="h-2 rounded bg-slate-100 animate-pulse w-2/3"></div>
                        </div>
                    </div>
                @endfor
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
