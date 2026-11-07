<nav class="space-y-5">
    <!-- UTAMA -->
    <div>
        <p class="px-3 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Utama</p>
        <div class="space-y-0.5">
            <x-sidebar.link :href="route('osis.dashboard')" :active="request()->routeIs('osis.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                </x-slot:icon>
                Dashboard
            </x-sidebar.link>
            <x-sidebar.link :href="route('piket.absensi.index')" :active="request()->routeIs('piket.*')">
                <x-slot:icon>
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </x-slot:icon>
                Scan Gerbang
            </x-sidebar.link>
        </div>
    </div>
</nav>
