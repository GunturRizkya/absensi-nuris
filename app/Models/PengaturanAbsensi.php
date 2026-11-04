<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanAbsensi extends Model
{
    protected $table = 'pengaturan_absensi';

    protected $fillable = [
        'hari', 'jam_masuk', 'toleransi_keterlambatan',
        'jam_tutup_absensi', 'jam_pulang', 'durasi_absensi_pulang', 'is_aktif',
    ];
}
