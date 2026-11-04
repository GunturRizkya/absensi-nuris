<?php

namespace App\Console\Commands;

use App\Services\AutoAlpa;
use Illuminate\Console\Command;

class AutoAlpaCommand extends Command
{
    protected $signature = 'absensi:auto-alpa
        {--tanggal= : Tanggal YYYY-MM-DD (default hari ini)}
        {--force : Jalankan walau jam tutup absensi belum lewat}';

    protected $description = 'Tandai alpa siswa yang belum absen (dijadwalkan tiap malam)';

    public function handle(): int
    {
        $hasil = AutoAlpa::jalankan(
            $this->option('tanggal') ?: now()->toDateString(),
            (bool) $this->option('force')
        );

        $this->info($hasil['pesan']);

        return self::SUCCESS;
    }
}
