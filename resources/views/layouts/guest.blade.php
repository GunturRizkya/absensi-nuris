<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Absensi Nuris') }} — Masuk</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased" style="font-family: 'Plus Jakarta Sans', sans-serif;">
        <div class="min-h-screen grid lg:grid-cols-2 bg-[#f8fafc]">
            <!-- Panel branding -->
            <div class="relative hidden lg:flex flex-col justify-between overflow-hidden bg-gradient-to-br from-blue-700 via-blue-600 to-blue-500 p-12 text-white">
                <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-white/10"></div>
                <div class="absolute -bottom-32 -left-16 w-[28rem] h-[28rem] rounded-full bg-blue-800/40"></div>

                <div class="relative flex items-center gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-white/15 ring-1 ring-white/25 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="font-extrabold tracking-tight">SMK NURUL ISLAM</p>
                        <p class="text-[11px] text-blue-100 tracking-widest">SISTEM ABSENSI SISWA</p>
                    </div>
                </div>

                <div class="relative">
                    <h1 class="text-4xl font-extrabold leading-tight tracking-tight">Absensi cepat,<br>rekap otomatis.</h1>
                    <p class="mt-3 text-blue-100 text-sm max-w-md">Scan QR di gerbang, catat per mapel, setujui izin, dan pantau kehadiran dari satu dasbor.</p>
                    <ul class="mt-8 space-y-4 text-sm font-medium">
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-white/15 ring-1 ring-white/25 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </span>
                            Scan QR / kartu di gerbang
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-white/15 ring-1 ring-white/25 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            </span>
                            Rekap harian & bulanan otomatis
                        </li>
                        <li class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-xl bg-white/15 ring-1 ring-white/25 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            </span>
                            Orang tua memantau dari HP
                        </li>
                    </ul>
                </div>

                <p class="relative text-[11px] text-blue-200">© {{ date('Y') }} SMK Nurul Islam</p>
            </div>

            <!-- Panel form -->
            <div class="flex flex-col items-center justify-center px-5 py-10 sm:px-10">
                <!-- Brand ringkas (mobile) -->
                <div class="lg:hidden flex items-center gap-2.5 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l9-5-9-5-9 5 9 5z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0112 20.055a11.952 11.952 0 01-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <p class="text-sm font-extrabold text-slate-800 tracking-tight">SMK NURUL ISLAM</p>
                        <p class="text-[10px] text-slate-400 tracking-widest">SISTEM ABSENSI SISWA</p>
                    </div>
                </div>

                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
