<x-guest-layout>
    <div class="bg-white rounded-3xl border border-slate-200/70 shadow-xl shadow-blue-900/5 p-6 sm:p-8">
        <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Selamat Datang</h1>
        <p class="text-xs text-slate-400 font-medium mt-1 mb-6">Masuk untuk mengelola absensi sekolah.</p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="label">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@sekolah.sch.id" class="input py-3">
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <label for="password" class="label">Password</label>
                <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" class="input py-3">
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <!-- Remember Me -->
            <div class="mt-4 flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-blue-600 shadow-sm focus:ring-blue-500/30" name="remember">
                    <span class="ms-2 text-xs font-medium text-slate-500">Ingat saya</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-bold text-blue-600 hover:text-blue-700" href="{{ route('password.request') }}">
                        Lupa password?
                    </a>
                @endif
            </div>

            <button type="submit" class="btn-primary w-full py-3 mt-6 text-sm">
                Masuk
            </button>
        </form>
    </div>
    <p class="mt-5 text-center text-[11px] text-slate-400">Akun dibuat oleh admin sekolah. Hubungi tata usaha bila belum punya akun.</p>
</x-guest-layout>
