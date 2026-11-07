<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use App\Services\StatistikAbsensiHarian;
use Illuminate\View\View;

class OsisController extends Controller
{
    public function __construct(
        private readonly StatistikAbsensiHarian $statistik,
    ) {}

    public function index(): View
    {
        $tahunAktif = TahunAjaran::where('is_aktif', true)->first();
        $hariIni = now()->toDateString();

        $ringkasan = $this->statistik->hitung($tahunAktif, $hariIni);
        $ringkasan['belum_absen'] = max($ringkasan['total_siswa'] - $ringkasan['sudah_absen'], 0);

        return view('osis.index', compact('tahunAktif', 'hariIni', 'ringkasan'));
    }
}
