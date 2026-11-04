<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AbsensiMapel extends Model
{
    use HasFactory;

    // Tambahkan baris ini
    protected $table = 'absensi_mapel';

    protected $guarded = ['id'];

    public function jadwal()
    {
        return $this->belongsTo(JadwalPelajaran::class, 'jadwal_pelajaran_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function pencatat()
    {
        return $this->belongsTo(Guru::class, 'dicatat_oleh');
    }
}
