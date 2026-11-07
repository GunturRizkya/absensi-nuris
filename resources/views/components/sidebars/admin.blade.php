<nav class="space-y-5">
    <!-- UTAMA -->
    <div>
        <p class="px-3 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Utama</p>
        <div class="space-y-0.5">
            <x-sidebar.link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </x-slot:icon>
                Dashboard
            </x-sidebar.link>
            <x-sidebar.link :href="route('piket.absensi.index')" :active="request()->routeIs('piket.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </x-slot:icon>
                Absensi Gerbang
            </x-sidebar.link>
        </div>
    </div>

    <!-- MASTER DATA -->
    <div>
        <p class="px-3 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Master Data</p>
        <div class="space-y-0.5">
            <x-sidebar.link :href="route('admin.jurusan.index')" :active="request()->routeIs('admin.jurusan.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </x-slot:icon>
                Data Jurusan
            </x-sidebar.link>
            <x-sidebar.link :href="route('admin.kelas.index')" :active="request()->routeIs('admin.kelas.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </x-slot:icon>
                Data Kelas
            </x-sidebar.link>
            <x-sidebar.link :href="route('admin.pengguna.index')" :active="request()->routeIs('admin.pengguna.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 13a6 6 0 00-12 0m12 0v-2a6 6 0 00-6-6m6 6H3"/></svg>
                </x-slot:icon>
                Data Pengguna
            </x-sidebar.link>
            <x-sidebar.link :href="route('admin.guru.index')" :active="request()->routeIs('admin.guru.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                </x-slot:icon>
                Data Guru
            </x-sidebar.link>
            
            <x-sidebar.link :href="route('admin.siswa.index')" :active="request()->routeIs('admin.siswa.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                    </svg>
                </x-slot:icon>
                Data Siswa
            </x-sidebar.link>

            {{-- Menu Hari Libur --}}
            <x-sidebar.link
                :href="route('admin.hari-libur.index')"
                :active="request()->routeIs('admin.hari-libur.*')">
            
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 012 2v14H3V6a2 2 0 012-2z"/>
                    </svg>
                </x-slot:icon>
                Hari Libur
            </x-sidebar.link>
        </div>
    </div>

    <!-- AKADEMIK & PRESENSI -->
    <div>
        <p class="px-3 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Akademik & Presensi</p>
        <div class="space-y-0.5">
            <x-sidebar.link :href="route('admin.mapel.index')" :active="request()->routeIs('admin.mapel.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25"/></svg>
                </x-slot:icon>
                Mata Pelajaran
            </x-sidebar.link>
            <x-sidebar.link :href="route('admin.jadwal.index')" :active="request()->routeIs('admin.jadwal.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                </x-slot:icon>
                Jadwal Pelajaran
            </x-sidebar.link>
            <x-sidebar.link :href="route('walikelas.surat-izin.index')" :active="request()->routeIs('walikelas.*')" :badge="$persetujuanPending ?? null">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </x-slot:icon>
                Persetujuan Izin
            </x-sidebar.link>
            <x-sidebar.link :href="route('admin.rekap.harian')" :active="request()->routeIs('admin.rekap.harian')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </x-slot:icon>
                Rekap Harian
            </x-sidebar.link>
            <x-sidebar.link :href="route('admin.rekap.bulanan')" :active="request()->routeIs('admin.rekap.bulanan')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </x-slot:icon>
                Rekap Bulanan
            </x-sidebar.link>
        </div>
    </div>

    <!-- PENGATURAN -->
    <div>
        <p class="px-3 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Pengaturan</p>
        <div class="space-y-0.5">
            <x-sidebar.link :href="route('admin.pengaturan-absensi.index')" :active="request()->routeIs('admin.pengaturan-absensi.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </x-slot:icon>
                Pengaturan Absensi
            </x-sidebar.link>
            <x-sidebar.link :href="route('admin.tahun-ajaran.index')" :active="request()->routeIs('admin.tahun-ajaran.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </x-slot:icon>
                Tahun Ajaran
            </x-sidebar.link>
        </div>
    </div>
</nav>
