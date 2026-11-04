<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-alpa tiap malam waktu Jakarta (server memakai UTC).
Schedule::command('absensi:auto-alpa')->dailyAt('23:30')->timezone('Asia/Jakarta');
