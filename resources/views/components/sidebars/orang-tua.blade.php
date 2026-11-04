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
            <x-sidebar.link :href="route('orangtua.notifikasi.index')" :active="request()->routeIs('orangtua.notifikasi.*')" :badge="$belumDibaca ?? null">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                </x-slot:icon>
                Notifikasi
            </x-sidebar.link>
        </div>
    </div>

    <!-- ANAK SAYA -->
    <div>
        <p class="px-3 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Anak Saya</p>
        <div class="space-y-0.5">
            <x-sidebar.link :href="route('orangtua.riwayat.index')" :active="request()->routeIs('orangtua.riwayat.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </x-slot:icon>
                Riwayat Absensi
            </x-sidebar.link>
            <x-sidebar.link :href="route('orangtua.surat-izin.index')" :active="request()->routeIs('orangtua.surat-izin.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                </x-slot:icon>
                Ajukan Surat Izin
            </x-sidebar.link>
        </div>
    </div>
</nav>
