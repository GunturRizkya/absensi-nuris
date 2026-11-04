<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotifikasiKehadiran extends Model
{
    protected $table = 'notifikasi_kehadiran';

    protected $guarded = ['id'];

    protected $casts = [
        'dibaca_pada' => 'datetime',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
