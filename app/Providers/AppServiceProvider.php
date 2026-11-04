<?php

namespace App\Providers;

use App\Models\Kelas;
use App\Models\NotifikasiKehadiran;
use App\Models\SuratIzin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Badge jumlah notifikasi belum dibaca di sidebar orang tua.
        View::composer('components.sidebars.orang-tua', function ($view) {
            $belumDibaca = 0;

            if (Auth::check() && Auth::user()->role === 'orang_tua') {
                $belumDibaca = NotifikasiKehadiran::whereIn(
                    'siswa_id',
                    Auth::user()->anak()->pluck('siswa.id')
                )
                    ->where('kanal', 'aplikasi')
                    ->whereNull('dibaca_pada')
                    ->count();
            }

            $view->with('belumDibaca', $belumDibaca);
        });

        // Badge surat menunggu persetujuan (admin: semua, wali: kelasnya).
        View::composer(['components.sidebars.admin', 'components.sidebars.guru'], function ($view) {
            $pending = 0;

            if (Auth::check()) {
                $user = Auth::user();
                $query = SuratIzin::where('status_persetujuan', 'pending');

                if ($user->role !== 'admin' && $user->guru) {
                    $query->whereHas('siswa.anggotaKelas', fn ($q) => $q->whereIn(
                        'kelas_id',
                        Kelas::where('wali_kelas_id', $user->guru->id)->pluck('id')
                    ));
                }

                $pending = (int) $query->count();
            }

            $view->with('persetujuanPending', $pending);
        });
    }
}
