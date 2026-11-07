<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Absensi Nuris') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-800 antialiased selection:bg-blue-500 selection:text-white">
    <div class="min-h-screen flex">

        <!-- Overlay drawer (mobile) -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-30 hidden md:hidden print:hidden"></div>

        <!-- SIDEBAR (WHITE SAAS) -->
        <aside id="sidebar" class="w-[270px] bg-white border-r border-slate-200/80 flex flex-col justify-between fixed inset-y-0 left-0 z-40 shadow-sm transform -translate-x-full md:translate-x-0 transition-transform duration-300 print:hidden">
            <div>
                <!-- Brand Header -->
                <div class="h-16 px-5 flex items-center border-b border-slate-100">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center shadow-md shadow-blue-500/20 shrink-0">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                            </svg>
                        </div>
                        <div class="leading-tight">
                            <span class="block text-sm font-extrabold text-slate-800 tracking-tight">SMK NURUL ISLAM</span>
                            <span class="mt-0.5 inline-flex px-2 py-px rounded-md bg-blue-50 text-[10px] font-bold text-blue-600 tracking-widest">{{ strtoupper(str_replace('_', ' ', Auth::user()->role)) }}</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Component per Role -->
                <div class="p-3 overflow-y-auto max-h-[calc(100vh-12rem)]">
                    @switch(Auth::user()->role)
                        @case('admin')
                            <x-sidebars.admin />
                            @break

                        @case('guru')
                        @case('wali_kelas')
                        @case('piket')
                            <x-sidebars.guru />
                            @break

                        @case('orang_tua')
                            <x-sidebars.orang-tua />
                            @break

                        @case('osis')
                            <x-sidebars.osis />
                            @break

                        @default
                            <x-sidebars.admin />
                    @endswitch
                </div>
            </div>

            <!-- Bottom User Profile Card -->
            <div class="p-3 border-t border-slate-100 bg-slate-50/60">
                <div class="flex items-center justify-between p-2 rounded-xl hover:bg-white hover:shadow-sm border border-transparent hover:border-slate-200/70 transition-all">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs shadow-sm shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="truncate max-w-[110px]">
                            <p class="text-xs font-bold text-slate-800 truncate">{{ Auth::user()->name }}</p>
                            <p class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-rose-600 p-1.5 rounded-lg hover:bg-rose-50 transition-colors" title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTAINER -->
        <div class="flex-1 md:pl-[270px] flex flex-col min-w-0">

            <!-- TOP NAVBAR -->
            <header class="h-16 bg-white/90 backdrop-blur border-b border-slate-200/80 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-20 shadow-xs print:hidden">
                <div class="flex items-center space-x-3">
                    <!-- Hamburger (mobile drawer) -->
                    <button id="btn-drawer" class="md:hidden p-2 -ml-2 text-slate-500 hover:text-blue-600 hover:bg-slate-100 rounded-lg transition-colors" aria-label="Buka menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <!--palette trigger (pengganti search palsu) -->
                    <button id="btn-palette" class="relative w-56 sm:w-72 flex items-center gap-2 pl-9 pr-10 py-2 text-xs bg-slate-100/70 border border-slate-200/80 rounded-xl text-slate-400 font-medium hover:border-blue-300 hover:bg-blue-50/50 transition-all text-left">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </span>
                        <span class="truncate">Cari menu…</span>
                        <kbd class="absolute inset-y-0 right-0 mr-2 my-auto h-fit px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 bg-white border border-slate-200 rounded shadow-2xs">⌘K</kbd>
                    </button>
                </div>

                <!-- Right Action Icons & User -->
                <div class="flex items-center space-x-2 sm:space-x-3">
                    <div class="hidden sm:flex items-center space-x-2 pl-2">
                        <div class="w-8 h-8 rounded-full bg-blue-600 text-white font-bold flex items-center justify-center text-xs ring-2 ring-blue-500/20 shrink-0">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <div class="text-left leading-none hidden lg:block">
                            <span class="block text-xs font-bold text-slate-700">{{ Auth::user()->name }}</span>
                            <span class="text-[10px] text-slate-400">{{ Auth::user()->email }}</span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- PAGE CONTENT -->
            <main class="flex-1 p-4 sm:p-8">
                @hasSection('content')
                    @yield('content')
                @else
                    {{ $slot ?? '' }}
                @endif
            </main>
        </div>
    </div>

    <!-- COMMAND PALETTE (navigasi cepat, dibangun dari menu sidebar) -->
    <div id="palette" class="fixed inset-0 z-50 hidden">
        <div id="palette-bg" class="absolute inset-0 bg-slate-900/50 backdrop-blur-xs"></div>
        <div class="relative max-w-md mx-auto mt-24 px-4">
            <div class="bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden">
                <div class="flex items-center gap-2 px-4 border-b border-slate-100">
                    <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <input id="palette-input" type="text" autocomplete="off" placeholder="Ketik nama menu…" class="w-full py-3.5 text-sm outline-none placeholder-slate-400">
                    <kbd class="px-1.5 py-0.5 text-[10px] font-semibold text-slate-400 bg-slate-100 rounded">esc</kbd>
                </div>
                <div id="palette-list" class="max-h-72 overflow-y-auto p-2"></div>
            </div>
        </div>
    </div>

    <script>
    (function () {
        var sidebar = document.getElementById('sidebar');
        var overlay = document.getElementById('sidebar-overlay');
        var palette = document.getElementById('palette');
        var paletteInput = document.getElementById('palette-input');
        var paletteList = document.getElementById('palette-list');

        /* ---- Drawer mobile ---- */
        function bukaDrawer(buka) {
            if (!sidebar || !overlay) return;
            sidebar.classList.toggle('-translate-x-full', !buka);
            overlay.classList.toggle('hidden', !buka);
            document.body.classList.toggle('overflow-hidden', buka);
        }
        document.getElementById('btn-drawer')?.addEventListener('click', function () { bukaDrawer(true); });
        overlay?.addEventListener('click', function () { bukaDrawer(false); });
        sidebar?.querySelectorAll('a').forEach(function (a) {
            a.addEventListener('click', function () { bukaDrawer(false); });
        });

        /* ---- Command palette: kumpulkan link dari sidebar ---- */
        var menu = [];
        sidebar?.querySelectorAll('a[href]').forEach(function (a) {
            var href = a.getAttribute('href');
            var teks = (a.textContent || '').trim().replace(/\s+/g, ' ');
            if (href && href !== '#' && teks) menu.push({ teks: teks, href: href });
        });

        function renderDaftar(filter) {
            var cocok = menu.filter(function (m) {
                return m.teks.toLowerCase().indexOf(filter.toLowerCase()) !== -1;
            }).slice(0, 8);
            paletteList.innerHTML = cocok.length
                ? cocok.map(function (m, i) {
                    return '<a href="' + m.href + '" data-i="' + i + '" class="palette-item flex items-center gap-2.5 px-3 py-2.5 rounded-xl text-sm font-medium text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">'
                        + '<span class="w-1.5 h-1.5 rounded-full bg-blue-400 shrink-0"></span><span>' + m.teks + '</span></a>';
                }).join('')
                : '<p class="px-3 py-6 text-center text-xs text-slate-400">Menu tidak ditemukan.</p>';
        }

        function bukaPalette(buka) {
            if (!palette) return;
            palette.classList.toggle('hidden', !buka);
            if (buka) {
                renderDaftar('');
                paletteInput.value = '';
                setTimeout(function () { paletteInput.focus(); }, 30);
            }
        }
        document.getElementById('btn-palette')?.addEventListener('click', function () { bukaPalette(true); });
        document.getElementById('palette-bg')?.addEventListener('click', function () { bukaPalette(false); });
        paletteInput?.addEventListener('input', function () { renderDaftar(paletteInput.value); });
        paletteInput?.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                var pertama = paletteList.querySelector('.palette-item');
                if (pertama) window.location.href = pertama.getAttribute('href');
            }
        });
        document.addEventListener('keydown', function (e) {
            if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                e.preventDefault();
                bukaPalette(palette.classList.contains('hidden'));
            }
            if (e.key === 'Escape') {
                bukaPalette(false);
                bukaDrawer(false);
            }
        });
    })();
    </script>
</body>
</html>
